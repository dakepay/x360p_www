<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ServiceRecord;
use app\api\model\Dictionary;

class ReportServiceByCustoms extends Export
{
	protected $columns = [
        ['field'=>'st_did','title'=>'服务类型','width'=>20],
        ['field'=>'service_nums','title'=>'服务次数','width'=>20],
        ['field'=>'student_nums','title'=>'服务人数','width'=>20],
	];

	protected function get_title()
	{
		$title = '自定义服务';
		return $title;
	}

	protected function get_student_nums($sr_ids)
	{
		$model = new ServiceRecord;
		$sum = 0;
		foreach ($sr_ids as $sr_id) {
			$w['sr_id'] = $sr_id;
			$cid = $model->where($w)->value('cid');
			$int_day = $model->where($w)->value('int_day');
			if($cid==0){
				$sum += 1;
			}else{
				$num = get_class_student_num($int_day,$cid);
				$sum += $num;
			}
		}
		return $sum;
	}

	protected function get_data()
	{
		$model = new ServiceRecord;
		$group = 'st_did';
		$fields = 'st_did';

		$w_dict['pid'] = 23;
		$w_dict['is_system'] = 0;
		$w_dict['og_id'] = gvar('og_id');
		$dids = (new Dictionary)->where($w_dict)->column('did');

		$w = [];
		$w['st_did'] = ['in',$dids];
		if(!empty($this->params['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($this->params['start_date'])),date('Ymd',strtotime($this->params['end_date']))]];
		}
		$data = $model->field($fields)->where($w)->group($group)->order('st_did asc')->getSearchResult($this->params,[],false);
		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['st_did'] = get_did_value($v['st_did']);
			$w_n['st_did'] = $v['st_did'];
			$w_n['bid'] = $this->params['bid'];
			$sr_ids = $model->where($w_n)->column('sr_id');
			$data['list'][$k]['service_nums'] = count($sr_ids);
			$data['list'][$k]['student_nums'] = $this->get_student_nums($sr_ids);
		}

		if($data['list']){
			return collection($data['list'])->toArray();
		}
		return [];
	}
}