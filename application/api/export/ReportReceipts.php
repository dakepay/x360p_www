<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\EmployeeReceipt;

class ReportReceipts extends Export
{
	protected $columns = [
        ['field'=>'eid','title'=>'姓名','width'=>20],
        ['field'=>'sale_role_did','title'=>'回款角色','width'=>20],
        ['field'=>'amount','title'=>'业绩金额','width'=>20],
        ['field'=>'receipt_time','title'=>'日期','width'=>20],
	];

	protected function get_title()
	{
		$title = '回款明细表';
		return $title;
	}

	protected function get_data()
	{
		$model = new EmployeeReceipt;

		$fields = ['eid','sale_role_did','amount','receipt_time'];
		$data = $model->field($fields)->getSearchResult($this->params,[],false);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['eid'] = get_teacher_name($v['eid']);
			$data['list'][$k]['sale_role_did'] = get_did_value($v['sale_role_did']);
			$data['list'][$k]['receipt_time'] = date('Y-m-d',strtotime($v['receipt_time']));
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
	}
}