<?php

namespace app\api\controller;
use think\Request;
use app\api\model\ReportClassByName;
use app\api\model\Classes;
use app\api\model\Student;
use app\api\model\CourseArrange;
use app\api\model\ClassStudent;


class ReportClassByNames extends Base
{
	protected function get_section_time($cid){
		$ret = CourseArrange::where('cid',$cid)->find();
		$start = $ret['int_start_hour'];
		$end = $ret['int_end_hour'];
		return int_hour_to_hour_str($start).' - '.int_hour_to_hour_str($end);
	}
	

	public function get_list(Request $request)
	{
		$input = $request->get();
		$model = new ReportClassByName;
		$w = [];
		$w['status'] = 1;

		if(!empty($input['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		}

        $fields = ['bid','lid','cid','sid','teach_eid'];
		$data = $model->where($w)->field($fields)->order('sid asc')->getSearchResult($input);
		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['branch_name'] = get_branch_name($v['bid']);
			$data['list'][$k]['lesson_name'] = get_lesson_name($v['lid']);
			$data['list'][$k]['student_name'] = get_student_name($v['sid']);
			$data['list'][$k]['teach_eid'] = get_teacher_name($v['teach_eid']);
			$data['list'][$k]['section_time'] = $this->get_section_time($v['cid']);
			$cinfo = get_class_info($v['cid']);
            $data['list'][$k]['class_type'] = $cinfo['class_type'];
	    $data['list'][$k]['class_name'] = $cinfo['class_name'];
		}

		if(empty($data)){
			$model = new Classes;
			$cids = $model->column('cid');
			$ret = ReportClassByName::buildReport($cids);
			if($ret===false){
				return $this->sendError(400,$ret);
			}
			$data = $model->where($w)->field($fields)->order('sid asc')->getSearchResult();
			foreach ($data['list'] as $k => $v) {
				$data['list'][$k]['branch_name'] = get_branch_name($v['bid']);
				$data['list'][$k]['lesson_name'] = get_lesson_name($v['lid']);
				$data['list'][$k]['student_name'] = get_student_name($v['sid']);
				$data['list'][$k]['teach_eid'] = get_teacher_name($v['teach_eid']);
				$data['list'][$k]['section_time'] = $this->get_section_time($v['cid']);
				$cinfo = get_class_info($v['cid']);
                $data['list'][$k]['class_type'] = $cinfo['class_type'];
		$data['list'][$k]['class_name'] = $cinfo['class_name'];
			}
		}

		return $this->sendSuccess($data);
	}


	public function post(Request $request)
	{
		$model = new ClassStudent;
		$bids = $request->header('x-bid');
		$bids = explode(',',$bids);
		$w['bid'] = ['in',$bids];
		$w['og_id'] = gvar('og_id');
		$w['status'] = 1;
		$sids = $model->where($w)->column('sid');
		$sids = array_unique($sids);

		(new ReportClassByName)->where($w)->delete();

		$ret = ReportClassByName::buildReport($sids);
		if($ret===false){
			return $this->sendError(400,$ret);
		}
		return $this->sendSuccess();
	}
}