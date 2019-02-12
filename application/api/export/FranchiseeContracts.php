<?php
namespace app\api\export;

use app\common\Export;
use app\api\model\FranchiseeContract;

class FranchiseeContracts extends Export
{
	protected $columns = [
	    ['title'=>'加盟商名称','field'=>'org_name','width'=>20],
        ['title'=>'合同号','field'=>'contract_no','width'=>20],
        ['title'=>'合同开始日期','field'=>'contract_start_int_day','width'=>20],
        ['title'=>'合同结束日期','field'=>'contract_end_int_day','width'=>20],
        ['title'=>'开业时间','field'=>'open_int_day','width'=>20],
        ['title'=>'区域性质','field'=>'region_level','width'=>20],
        ['title'=>'特许经营费','field'=>'join_fee1','width'=>20],
        ['title'=>'履约保证金','field'=>'join_fee2','width'=>20],
        ['title'=>'年度使用费','field'=>'join_fee3','width'=>20],
        ['title'=>'教育商品款','field'=>'join_fee4','width'=>20],
        ['title'=>'合同总金额','field'=>'contract_amount','width'=>20],
        ['title'=>'全款到账日期','field'=>'all_pay_int_day','width'=>20],
        ['title'=>'合同特殊约定','field'=>'content','width'=>50],
        ['title'=>'签约员工','field'=>'sign_eid','width'=>20],
	];

	protected function get_title()
	{
		$title = '加盟商合同表';
		return $title;
	}

	protected function convert_level($key)
	{
		$map = [0=>'-',1=>'一类',2=>'二类',3=>'三类',4=>'四类',5=>'五类'];
		if(key_exists($key,$map)){
			return $map[$key];
		}
		return '-';
	}

	protected function get_data()
	{
		$input = $this->params;
		$model = new FranchiseeContract;

		$w = [];
		$ret = $model->where($w)->getSearchResult($input,[],false);
		foreach ($ret['list'] as &$row) {
			$row['org_name'] = get_franchisee_name($row['fc_id']);
			$row['region_level'] = $this->convert_level($row['region_level']);
			$row['sign_eid'] = get_teacher_name($row['sign_eid']);
		}


		if(!empty($ret['list'])){
			return collection($ret['list'])->toArray();
		}

		return [];
	}


}