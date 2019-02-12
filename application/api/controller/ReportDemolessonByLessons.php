<?php

namespace app\api\controller;

use think\Request;
use app\api\model\ReportDemolessonByLesson;
use app\api\model\Lesson;

class ReportDemolessonByLessons extends Base
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
		$model = new ReportDemolessonByLesson;
		$w = [];
		if(!empty($input['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		}

		$data = $model->where($w)->getSearchResult($input);
        foreach ($data['list'] as $k => $v) {
        	$data['list'][$k]['rate'] = $this->get_rate($v['sids'],$v['transfered_sids']);
        }

		return $this->sendSuccess($data);

	}


	public function post(Request $request)
	{
		$m_lesson = new Lesson;
		$w['og_id']  = gvar('og_id');
		$w['is_demo'] = 1;
		$post['lids'] = $m_lesson->where($w)->column('lid');

		// delete old Data
		if(!empty($post['lids'])){
			$model = new ReportDemolessonByLesson;
			foreach ($post['lids'] as $lid) {
				$model->deleteLessons($lid);
			}
		}

		$res = ReportDemolessonByLesson::buildReport($post);
		if($res === false){
			return $this->sendError(400,$ret);
		}

		return $this->sendSuccess();

	}
}