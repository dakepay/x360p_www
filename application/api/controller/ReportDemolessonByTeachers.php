<?php

namespace app\api\controller;

use think\Request;
use app\api\model\ReportDemolessonByTeacher;
use app\api\model\Classes;

class ReportDemolessonByTeachers extends Base
{
	protected function get_rate($sids,$transfered_sids)
	{
		$rates = '';
		if($sids==0){
			return '0%';
		}else{
			$rates = round($transfered_sids/$sids*100,2).'%';
		}
		return $rates;
	}

	public function get_list(Request $request)
	{
		$input = $request->get();

		$model = new ReportDemolessonByTeacher;
		$w = [];
		if(!empty($input['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		}

		$data = $model->where($w)->getSearchResult($input);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['cid'] = get_class_name($v['cid']);
			$data['list'][$k]['rate'] = $this->get_rate($v['sids'],$v['transfered_sids']);
		}

		return $this->sendSuccess($data);
	}



	public function teacher(Request $request)
	{
		$input = $request->get();
		$model = new ReportDemolessonByTeacher;

		$w = [];
		if(!empty($input['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		}
		$fields = ['eid'];
		$group = 'eid';

		$data = $model->where($w)->field($fields)->group($group)->order('eid asc')->getSearchResult($input);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['cids'] = count($model->where('eid',$v['eid'])->column('cid'));
			$data['list'][$k]['lids'] = count(array_unique($model->where('eid',$v['eid'])->column('lid')));
			$data['list'][$k]['sids'] = $model->where('eid',$v['eid'])->sum('sids');
			$data['list'][$k]['transfered_sids'] = $model->where('eid',$v['eid'])->sum('transfered_sids');
			$data['list'][$k]['rate'] = $this->get_rate($data['list'][$k]['sids'],$data['list'][$k]['transfered_sids']);
		}

		return $this->sendSuccess($data);

	}


	public function post(Request $request)
	{
        $model = new Classes;

        $w['og_id'] = gvar('og_id');
        $w['is_demo'] = 1;
        $w['status'] = ['in',['0','1']];
        $input['cids'] = $model->where($w)->column('cid');

        $res = ReportDemolessonByTeacher::buildReport($input);

        if($res === false){
        	return $this->sendError(400,$ret);
        }

        return $this->sendSuccess();


	}

}