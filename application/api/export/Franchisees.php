<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\Franchisee;

class Franchisees extends Export
{
	protected $columns = [
        ['title'=>'加盟商名称','field'=>'org_name','width'=>20],
        ['title'=>'联系电话','field'=>'mobile','width'=>20],
        ['title'=>'地址','field'=>'org_address','width'=>40],
        ['title'=>'运营状态','field'=>'status','width'=>20],
        ['title'=>'店面性质','field'=>'address_did','width'=>20],
        ['title'=>'装修费用','field'=>'decorate_fee','width'=>20],
        ['title'=>'是否总部装修','field'=>'is_head_decorate','width'=>20],
        ['title'=>'主体变更','field'=>'is_owner_change','width'=>20],
        ['title'=>'营业执照号','field'=>'business_license','width'=>30],
        ['title'=>'授权铜牌是否下发','field'=>'is_authorize_dispatch','width'=>30],
        ['title'=>'企业邮箱','field'=>'org_email','width'=>20],
        ['title'=>'销售员工','field'=>'sale_eid','width'=>20],
        ['title'=>'督导员工','field'=>'service_eid','width'=>20],
        ['title'=>'是否签约','field'=>'is_sign','width'=>20],
        ['title'=>'合同开始时间','field'=>'contract_start_int_day','width'=>20],
        ['title'=>'合同结束时间','field'=>'contract_end_int_day','width'=>20],
        ['title'=>'开业时间','field'=>'open_int_day','width'=>20],
	];

	protected function get_title()
	{
		$title = '加盟商表';
		return $title;
	}

	protected function convert_status($key)
	{
		$map = [0=>'未选址',1=>'筹备期',2=>'预售期',3=>'正常营业',4=>'停业',5=>'已解约'];
		if(key_exists($key,$map)){
			return $map[$key];
		}
		return '-';
	}

	protected function get_data()
	{
		$input = $this->params;
		unset($input['bid']);
		$model = new Franchisee;
		$w = [];
		$ret = $model->where($w)->getSearchResult($input,[],false);
		foreach ($ret['list'] as &$item) {
			$item['status'] = $this->convert_status($item['status']);
			$item['address_did'] = get_did_value($item['address_did']);
			$item['is_head_decorate'] = $item['is_head_decorate'] ? '是' : '否';
			$item['is_owner_change'] = $item['is_owner_change'] ? '已完成' : '未完成';
			$item['is_authorize_dispatch'] = $item['is_authorize_dispatch'] ? '是' : '否';
			$item['sale_eid'] = get_teacher_name($item['sale_eid']);
			$item['service_eid'] = get_teacher_name($item['service_eid']);
            $item['is_sign'] = $item['is_sign'] ? '已签约' : '未签约';
		}
		// echo $model->getLastSql();exit;

		if(!empty($ret['list'])){
			return collection($ret['list'])->toArray();
		}

		return [];
	}

}