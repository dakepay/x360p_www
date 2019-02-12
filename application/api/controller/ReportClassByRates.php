<?php

namespace app\api\controller;
use think\Request;
use app\api\model\Classes;


class ReportClassByRates extends Base
{
	public function get_list(Request $request){
		
		$input = $request->get();
		$model = new Classes;
		$w = [];
		$w['status'] = ['in',['0','1']];

		if(!empty($input['start_date'])){
			$w['create_time'] = ['between',[strtotime($input['start_date']),strtotime($input['end_date'])]];
		}

		$fields = ['bid','cid','lid','teach_eid','edu_eid','plan_student_nums','student_nums','nums_rate'];
		$data = $model->where($w)->field($fields)->order('cid asc')->getSearchResult($input);
		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['branch_name'] = get_branch_name($v['bid']);
			$data['list'][$k]['class_name'] = get_class_name($v['cid']);
			$data['list'][$k]['lesson_name'] = get_lesson_name($v['lid']);
			$data['list'][$k]['teach_name'] = get_teacher_name($v['teach_eid']);
			$data['list'][$k]['edu_name'] = get_teacher_name($v['edu_eid']);
			$cinfo = get_class_info($v['cid']);
            $data['list'][$k]['class_type'] = $cinfo['class_type'];
		}

		return $this->sendSuccess($data);
	}
}