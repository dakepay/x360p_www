<?php
namespace app\api\export;

use app\common\Export;
use app\api\model\MarketClueLog;
use app\api\model\MarketClue;

class MarketLogs extends Export
{
	protected $columns = [
        ['field'=>'create_uid','title'=>'操作人','width'=>20],
        ['field'=>'bid','title'=>'操作校区','width'=>20],
        ['field'=>'mcl_id','title'=>'市场名单','width'=>20],
        ['field'=>'op_type','title'=>'操作类型','width'=>20],
        ['field'=>'desc','title'=>'操作内容','width'=>40],
        ['field'=>'create_time','title'=>'操作时间','width'=>20],
        ['field'=>'remark','title'=>'备注','width'=>30],
	];

	protected function get_title()
	{
		$input = $this->params;
		$branch = get_branch_name($input['bid']);
		$title = $branch.'市场名单操作日志';
		return $title;
	}

	protected function convert_op_type($op_type)
	{
		$map = [1=>'添加',2=>'导入',3=>'编辑',4=>'删除',5=>'转为客户',6=>'分配'];
		if(key_exists($op_type,$map)){
			return $map[$op_type];
		}
		return $op_type;
	}

	protected function convert_valid($value)
	{
		$map = ['待确认','有效','无效'];
		if(key_exists($value,$map)){
			return $map[$value];
		}
		return '-';
	}

	protected function convert_field($field)
    {
        $map = ['is_valid'=>'有效性','name'=>'姓名','sex'=>'性别','tel'=>'电话号码','family_rel'=>'关系','email'=>'邮箱','school_grade'=>'年级','birth_time'=>'出生日期','mc_id'=>'来源渠道','from_did'=>'招生来源','remark'=>'备注','cu_assigned_eid'=>'销售负责人','assigned_eid'=>'市场负责人'];
        if(key_exists($field,$map)){
            return $map[$field];
        }
        return $field;
    }

	protected function get_remark($content)
    {
    	$keys = $content ? array_keys($content[0]) : [];
    	if(!empty($content) && in_array('field',$keys) && in_array('old_value',$keys) && in_array('new_value',$keys)){
    		foreach ($content as $item) {
                $field = $this->convert_field($item['field']);
                $old_value = $item['old_value'] ?: '-';
                $new_value = $item['new_value'];
                switch ($item['field']) {
                    case 'is_valid':
                        $old_value = $this->convert_valid($item['old_value']);
                        $new_value = $this->convert_valid($item['new_value']);
                        break;
                    case 'sex':
                        $old_value = get_sex($item['old_value']);
                        $new_value = get_sex($item['new_value']);
                        break;
                    case 'family_rel':
                        $old_value = get_family_rel($item['old_value']);
                        $new_value = get_family_rel($item['new_value']);
                        break;
                    case 'school_grade':
                        $old_value = get_grade_title($item['old_value']);
                        $new_value = get_grade_title($item['new_value']);
                        break;
                    case 'birth_time':
                        $old_value = $item['old_value'] ? date('Y-m-d',$item['old_value']) : '-';
                        $new_value = $item['new_value'] ? date('Y-m-d',$item['new_value']) : '-';
                        break;
                    case 'mc_id':
                        $old_value = get_mc_name($item['old_value']);
                        $new_value = get_mc_name($item['new_value']);
                        break;
                    case 'from_did':
                        $old_value = get_did_value($item['old_value']);
                        $new_value = get_did_value($item['new_value']);
                        break;
                    case 'cu_assigned_eid':
                        $old_value = get_employee_name($item['old_value']);
                        $new_value = get_employee_name($item['new_value']);
                        break;
                    case 'assigned_eid':
                        $old_value = get_employee_name($item['old_value']);
                        $new_value = get_employee_name($item['new_value']);
                        break;
                    default:
                        break;
                }

                $remark[] = '【'.$field.'】 编辑之前：'.$old_value.' ；编辑之后：'.$new_value;
            }
            return implode(' ',$remark);
    	}
    	return '-';
    }

    protected function get_withtrashed_mcl_name($mcl_id)
    {
        $mcl = MarketClue::withTrashed()->where('mcl_id',$mcl_id)->find();
        if(!empty($mcl)){
        	return $mcl['name'];
        }
        return '-';
    }

	public function get_data()
	{
		$input = $this->params;
        $pagenation = $this->pagenation;
		$mMarketClueLog = new MarketClueLog;
		$w = [];
		$ret = $mMarketClueLog->where($w)->getSearchResult($input,$pagenation);
		foreach ($ret['list'] as &$item) {
			$item['bid'] = get_branch_name($item['bid']);
			$item['create_uid'] = get_user_name($item['create_uid']);
			$item['mcl_id'] = $this->get_withtrashed_mcl_name($item['mcl_id']);
			$item['op_type'] = $this->convert_op_type($item['op_type']);
			$item['remark'] = $this->get_remark($item['content']);
		}
        if($pagenation){
            return $ret;
        }
		if(!empty($ret['list'])){
			return collection($ret['list'])->toArray();
		}
		return [];
	}


}