<?php
namespace app\api\controller;

use think\Request;
use app\api\model\CustomerLog;
use app\api\model\Customer;

class CustomerLogs extends Base
{
    protected function convert_op_type($op_type)
    {
    	$map = [1=>'客户分配',2=>'转为学员',3=>'跟单',4=>'编辑',5=>'添加',6=>'删除',7=>'导入'];
    	if(key_exists($op_type,$map)){
    		return $map[$op_type];
    	}
    	return '-';
    }

    protected function convert_field($field)
    {
        $map = ['name'=>'客户姓名','birth_time'=>'出生日期','first_tel'=>'联系电话','nick_name'=>'昵称','home_address'=>'家庭住址','school_id'=>'公立学校','school_grade'=>'年级','school_class'=>'班级','first_family_rel'=>'第一关系','first_family_name'=>'第一关系姓名','second_family_rel'=>'第二关系','second_family_name'=>'第二关系','from_did'=>'招生来源','intention_level'=>'意向级别','customer_status_did'=>'客户状态','referer_sid'=>'介绍人','follow_eid'=>'主责任人','bid'=>'校区','get_time'=>'获取时间','sex'=>'性别','remark'=>'备注'];
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
                    case 'birth_time':
                        $old_value = date('Y-m-d',$item['old_value']);
                        $new_value = date('Y-m-d',$item['new_value']);
                        break;
                    case 'school_id':
                        $old_value = get_school_name($item['old_value']);
                        $new_value = get_school_name($item['new_value']);
                        break;
                    case 'school_grade':
                        $old_value = get_grade_title($item['old_value']);
                        $new_value = get_grade_title($item['new_value']);
                        break;
                    case 'first_family_rel':
                        $old_value = get_family_rel($item['old_value']);
                        $new_value = get_family_rel($item['new_value']);
                        break;
                    case 'second_family_rel':
                        $old_value = get_family_rel($item['old_value']);
                        $new_value = get_family_rel($item['new_value']);
                        break;
                    case 'from_did':
                        $old_value = get_did_value($item['old_value']);
                        $new_value = get_did_value($item['new_value']);
                        break;
                    case 'customer_status_did':
                        $old_value = get_did_value($item['old_value']);
                        $new_value = get_did_value($item['new_value']);
                        break;
                    case 'referer_sid':
                        $old_value = get_student_name($item['old_value']);
                        $new_value = get_student_name($item['new_value']);
                        break;
                    case 'follow_eid':
                        $old_value = get_employee_name($item['old_value']);
                        $new_value = get_employee_name($item['new_value']);
                        break;
                    case 'bid':
                        $old_value = get_branch_name($item['old_value']);
                        $new_value = get_branch_name($item['new_value']);
                        break;
                    case 'get_time':
                        $old_value = date('Y-m-d',$item['old_value']);
                        $new_value = date('Y-m-d',$item['new_value']);
                        break;
                    case 'sex':
                        $old_value = get_sex($item['old_value']);
                        $new_value = get_sex($item['new_value']);
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


    protected function get_customer_withtrashed_name($cu_id)
    {
        $customer = Customer::withTrashed()->where('cu_id',$cu_id)->find();
        if(empty($customer)){
        	return '-';
        }
        return $customer->name;
    }

	public function get_list(Request $request)
	{
		$mCustomerLog = new CustomerLog;
		$input = $request->param();
		$ret = $mCustomerLog->getSearchResult($input);
		foreach ($ret['list'] as &$item) {
			$item['create_uid'] = get_user_name($item['create_uid']);
			$item['cu_id'] = $this->get_customer_withtrashed_name($item['cu_id']);
			$item['op_type'] = $this->convert_op_type($item['op_type']);
			$item['remark'] = $this->get_remark($item['content']);
		}

		return $this->sendSuccess($ret);
	}

}