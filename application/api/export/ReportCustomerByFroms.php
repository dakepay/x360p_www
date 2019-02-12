<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\Customer;

class ReportCustomerByFroms extends Export
{
	protected $columns = [
        ['field'=>'from_did','title'=>'招生来源','width'=>20],
        ['field'=>'subtotal','title'=>'客户数量','width'=>20],
        ['field'=>'rate','title'=>'比例','width'=>20],
        ['field'=>'transfer_nums','title'=>'转化成功数','width'=>20],
        ['field'=>'transfer_rates','title'=>'转化率','width'=>20],
	];

	protected function get_title()
	{
		$title = '客户来源分析表';
		return $title;
	}

	protected function get_data()
	{
		$model = new Customer;

		$group = 'from_did';
		$fields = ['from_did'];
		$fields['count(cu_id)'] = 'subtotal';
		$fields['sum(is_reg)']  = 'transfer_nums';
        $w = [];
		if(!empty($this->params['start_date'])){
			$w['create_time'] = ['between',[strtotime($this->params['start_date']),strtotime($this->params['end_date'],true)]];
		}

		$data = $model->where($w)->group($group)->field($fields)->order('from_did asc')->getSearchResult($this->params,[],false);

        $total = $model->where($w)->count();
		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['from_did'] = get_did_value($v['from_did']) ? get_did_value($v['from_did']) : '未指定';
			$rate = sprintf('%.4f',$v['subtotal']/$total)*100;
			$data['list'][$k]['rate'] = $rate.'%';
			$transfer_rates = sprintf('%.4f', $v['transfer_nums']/$v['subtotal'])*100;
			$data['list'][$k]['transfer_rates'] = $transfer_rates.'%';
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
	}
}