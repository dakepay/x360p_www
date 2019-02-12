<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\Customer;

class ReportCustomerByIntentions extends Export
{
	protected $columns = [
        ['field'=>'intention_level','title'=>'意向级别','width'=>20],
        ['field'=>'subtotal','title'=>'客户数量','width'=>20],
        ['field'=>'rate','title'=>'比例','width'=>20],
        ['field'=>'transfer_nums','title'=>'转化成功数','width'=>20],
        ['field'=>'transfer_rates','title'=>'转化率','width'=>20],
	];

	protected function get_title()
	{
		$title = '客户意向分析表';
		return $title;
	}

	protected function get_data()
	{
		$model = new Customer;

		$group = 'intention_level';
		$fields = ['intention_level'];
		$fields['count(cu_id)'] = 'subtotal';
		$fields['sum(is_reg)']  = 'transfer_nums';
        $w = [];
		if(!empty($this->params['start_date'])){
			$w['create_time'] = ['between',[strtotime($this->params['start_date']),strtotime($this->params['end_date'])]];
		}

		$data = $model->where($w)->group($group)->field($fields)->order('intention_level asc')->getSearchResult($this->params,[],false);
        $total = $model->where($w)->count();
		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['intention_level'] = $v['intention_level'];
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