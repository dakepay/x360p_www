<?php

namespace app\api\controller;
use think\Request;
use app\api\model\ReportStudentByQuit;
use app\api\model\Student;

class ReportStudentByQuits extends Base
{ 
    protected function get_student_tel($sid){
	    $student = get_student_info($sid);
	    if(!$student){
	        return '-';
	    }
	    return $student['first_tel'];
	}

	protected function get_eid($sid){
	    $student = get_student_info($sid);
	    if(!$student){
	        return '-';
	    }
	    return $student['eid'];
	}

	protected function get_quit_reason($did){
	    $dictionary = get_dict_info($did);
	    if(!$dictionary){
	        return '-';
	    }
	    return $dictionary['title'];
	}

	public function get_list(Request $request)
	{
		$input = $request->get();
		$model = new ReportStudentByQuit;

		$w = [];
		if(!empty($input['cid'])){
			$w[] = ['exp',"find_in_set({$input['cid']},cids)"];
		}
		if(!empty($input['lid'])){
			$w[] = ['exp',"find_in_set({$input['lid']},lids)"];
		}

		if(!empty($input['start_date'])){
			$start_ts = strtotime($input['start_date'].'00:00:00');
			$end_ts = strtotime($input['end_date'].'23:59:59');
			$w['quit_time'] = ['between',[$start_ts,$end_ts]];
		}
		if(!empty($quit_reason)){
			$w['quit_reason'] = $input['quit_reason'];
		}
        
        $fields = ['bid','sid','cids','lids','quit_time','quit_reason'];
		$data = $model->field($fields)->where($w)->order('sid asc')->getSearchResult($input);
		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['eid'] = $this->get_eid($v['sid']);
			$data['list'][$k]['sid'] = get_student_name($v['sid']);
			$data['list'][$k]['first_tel'] = $this->get_student_tel($v['sid']);
			$data['list'][$k]['quit_reason'] = $this->get_quit_reason($v['quit_reason']);
			$data['list'][$k]['quit_time'] = date('Y-m-d',$v['quit_time']);
		}

		if(empty($data)){
			$model = new Student;
	        $sids = $model->where('status',90)->column('sid');
	        $ret = ReportStudentByQuit::buildReport($sids);
	        if($ret === false){
	        	return $this->sendError(400,$ret);
	        }
	        $data = $model->field($fields)->where($w)->order('sid asc')->getSearchResult();
	        foreach ($data['list'] as $k => $v) {
                $data['list'][$k]['eid'] = $this->get_eid($v['sid']);
                $data['list'][$k]['sid'] = get_student_name($v['sid']);
				$data['list'][$k]['first_tel'] = $this->get_student_tel($v['sid']);
				$data['list'][$k]['quit_reason'] = $this->get_quit_reason($v['quit_reason']);
				$data['list'][$k]['quit_time'] = date('Y-m-d',$v['quit_time']);
			}
		}

		return $this->sendSuccess($data);
	}


	public function post(Request $request)
	{
        $model = new Student;
        $w['status'] = 90;
        $w['og_id'] = gvar('og_id');
        $sids = $model->where($w)->column('sid');
        $ret = ReportStudentByQuit::buildReport($sids);
        if($ret === false){
        	return $this->sendError(400,$ret);
        }
        return $this->sendSuccess();
	}

}