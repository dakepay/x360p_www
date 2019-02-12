<?php

namespace app\api\controller;

use think\Request;
use app\api\model\ServiceRecord;
use app\api\model\Dictionary;

class ReportServiceByCustoms extends Base
{
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


	public function get_list(Request $request)
	{
		$input = $request->get(); 
		$bid = request()->header('x-bid');
		$model = new ServiceRecord;

		$w_dict['pid'] = 23;
		$w_dict['is_system'] = 0;
		$w_dict['og_id'] = gvar('og_id');
		$dids = (new Dictionary)->where($w_dict)->column('did');

		$w = [];
		$group = 'st_did';
		$fields = ['st_did'];
		if(!empty($input['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		}
        $w['st_did'] = ['in',$dids];
		$data = $model->where($w)->group($group)->field($fields)->order('st_did asc')->getSearchResult($input);

		foreach ($data['list'] as $k => $v) {
			$w_e['st_did'] = $v['st_did'];
			$w_e['bid'] = $bid;
			$w_e['og_id'] = gvar('og_id');
			$sr_ids = $model->where($w_e)->column('sr_id');
			$data['list'][$k]['sr_ids'] = $sr_ids;
			$data['list'][$k]['student_nums'] = $this->get_student_nums($sr_ids);
			$data['list'][$k]['type'] = get_did_value($v['st_did']);
		}

		return $this->sendSuccess($data);
	}
}