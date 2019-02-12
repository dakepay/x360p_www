<?php

namespace app\api\controller;
use think\Request;
use app\api\model\ReportClassByNumber;
use app\api\model\Classes;
use app\api\model\ClassStudent;


class ReportClassByNumbers extends Base
{
	public function get_list(Request $request)
	{
		$input = $request->get();
		$model = new ReportClassByNumber;
		$w = [];
		$w['status'] = ['in',['0','1']];

		if(!empty($input['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		}

        $fields = ['bid','cid','lid','teach_eid','student_num'];
		$data = $model->where($w)->field($fields)->order('bid asc')->getSearchResult($input);
		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['branch_name'] = get_branch_name($v['bid']);
			$data['list'][$k]['lesson_name'] = get_lesson_name($v['lid']);
			$data['list'][$k]['teach_name'] = get_teacher_name($v['teach_eid']);
            $cinfo = get_class_info($v['cid']);
            $data['list'][$k]['class_type'] = $cinfo['class_type'];
            $data['list'][$k]['class_name'] = $cinfo['class_name'];
			$data['list'][$k]['sids'] = ClassStudent::where('cid',$v['cid'])->column('sid');
		}

		if(empty($data)){
			$model = new Classes;
			$cids = $model->column('cid');
			$ret = ReportClassByNumber::buildReport($cids);
			if($ret===false){
				return $this->sendError(400,$ret);
			}
			$data = $model->where($w)->field($fields)->order('cid asc')->getSearchResult();
			foreach ($data['list'] as $k => $v) {
				$data['list'][$k]['branch_name'] = get_branch_name($v['bid']);
				$data['list'][$k]['lesson_name'] = get_lesson_name($v['lid']);
				$data['list'][$k]['teach_name'] = get_teacher_name($v['teach_eid']);
				$cinfo = get_class_info($v['cid']);
                $data['list'][$k]['class_type'] = $cinfo['class_type'];
		$data['list'][$k]['class_name'] = $cinfo['class_name'];
			}
		}

		return $this->sendSuccess($data);
	}



	public function post(Request $request)
	{
		$model = new Classes;
		$w['og_id'] = gvar('og_id');
		$bids = $request->header('x-bid');
		$bids = explode(',',$bids);
		$w['bid'] = ['in',$bids];
		$w['status'] = ['in',[0,1]];
		$cids = $model->where($w)->column('cid');
		unset($w['status']);

		$m_rcbn = new ReportClassByNumber;
		$m_rcbn->where($w)->delete();

		// delete old Data
		/*foreach ($cids as $cid) {
			$m_rcbn = new ReportClassByNumber;
			$class = $m_rcbn->where('cid',$cid)->find();
			if(!empty($class)){
				$m_rcbn->deleteOldData($class);
			}
		}exit;*/

		$ret = ReportClassByNumber::buildReport($cids);
		if($ret===false){
			return $this->sendError(400,$ret);
		}
		return $this->sendSuccess();
	}
}