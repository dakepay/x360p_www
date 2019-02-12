<?php

namespace app\api\controller;
use think\Request;
use app\api\model\ReportStudentLessonClass;
use app\api\model\Student;

class ReportStudentLessonClasses extends Base
{
	protected function get_student_num($cid,$lid,$school_id){
		$w = [];
		if(!empty($cid)){
            $w[] = ['exp', "find_in_set({$cid},cids)"];
        }
        if(!empty($lid)){
        	$w[] = ['exp', "find_in_set({$lid},lids)"];
        }

        $w['school_id'] = $school_id;
        $w['status'] = 1;
        
        $ret = ReportStudentLessonClass::where($w)->getSearchResult();

        return $ret['total'];
	}

	protected function get_bid_student_num($cid,$lid,$school_id,$bid){
		$w = [];
		if(!empty($cid)){
            $w[] = ['exp', "find_in_set({$cid},cids)"];
        }
        if(!empty($lid)){
        	$w[] = ['exp', "find_in_set({$lid},lids)"];
        }
        $w['school_id'] = $school_id;
        $w['bid'] = $bid;
        $w['status'] = 1;
        $ret = ReportStudentLessonClass::where($w)->getSearchResult();
        return $ret['total'];
	}

	public function get_list(Request $request)
	{

		$input = $request->get();
		$model = new ReportStudentLessonClass;

		$w = [];
        if(!empty($input['cid'])){
            $w[] = ['exp', "find_in_set({$input['cid']},cids)"];
        }
        if(!empty($input['lid'])){
        	$w[] = ['exp', "find_in_set({$input['lid']},lids)"];
        }
        if(!empty($input['school_id'])){
        	$w['school_id'] = $input['school_id'];
        }
        $fields = ['school_id'];
		
        if(!empty($input['cid'])){
        	$input['cid'] = $input['cid'];
        }else{
            $input['cid'] = '';
        }
        if(!empty($input['lid'])){
        	$input['lid'] = $input['lid'];
        }else{
            $input['lid'] = '';
        }

        $bids = request()->header('x-bid');
        $bids = explode(',',$bids);
        
        $data = $model->where($w)->group('school_id')->field($fields)->order('school_id asc')->getSearchResult($input);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['student_num'] = $this->get_student_num($input['cid'],$input['lid'],$v['school_id']);
			$data['list'][$k]['school_id'] = get_school_name($v['school_id']) ? get_school_name($v['school_id']) : '学校已删除';
			$data['list'][$k]['sc_id'] = $v['school_id'];
			foreach ($bids as $k1 => $bid) {
				$data['list'][$k]['student_num_bid'.$bid] = $this->get_bid_student_num($input['cid'],$input['lid'],$v['school_id'],$bid);
			}
		}
        
        if(empty($data)){
        	$model = new Student;
			$sids = $model->column('sid');
			$ret = ReportStudentLessonClass::buildReport($sids);
			if($ret === false){
				return $this->sendError(400,$ret);
			}
			$data = $model->where($w)->group('school_id')->field($fields)->order('school_id asc')->getSearchResult($input);
			foreach ($data['list'] as $k => $v) {
				$data['list'][$k]['student_num'] = $this->get_student_num($input['cid'],$input['lid'],$v['school_id']);
					foreach ($bids as $k1 => $bid) {
					$data['list'][$k]['student_num_bid'.$bid] = $this->get_bid_student_num($input['cid'],$input['lid'],$v['school_id'],$bid);
				}
			}
        }
		
		return $this->sendSuccess($data);
	}

	protected function convert_status($key)
    {
        $map = [1=>'正常',20=>'停课',30=>'休学',90=>'退学',100=>'封存'];
        if(key_exists($key,$map)){
                return $map[$key];
        }
        return '-';
    }


	public function get_detail(Request $request,$id = 0)
	{
		// echo 'success';exit;
		$school_id = input('id/d');
		// print_r($school_id);exit;
		$input = $request->get();

		$w['school_id'] = $school_id;
		$w['status'] = 1;

		$model = new Student;
		$ret = $model->where($w)->field('sid,bid,student_name,first_tel,status,school_id')->getSearchResult($input);

		foreach ($ret['list'] as &$row) {
               $row['bid'] = get_branch_name($row['bid']);
               $row['status'] = $this->convert_status($row['status']);
        }

		$ret['columns'] = Student::$detail_fields;

		return $this->sendSuccess($ret);
	}


	public function post(Request $request)
	{
		$model = new Student;
		$bids = $request->header('x-bid');
		$bids = explode(',',$bids);
		$w['og_id'] = gvar('og_id');
		$w['bid'] = ['in',$bids];
		$sids = $model->where($w)->column('sid');
		//添加数据之前 清空之前的数据
		foreach ($sids as $sid) {
			$m_rslc = new ReportStudentLessonClass;
			$m_rslc->deleteOldDate($sid);
		}

		$ret = ReportStudentLessonClass::buildReport($sids);
		if($ret === false){
			return $this->sendError(400,$ret);
		}
		return $this->sendSuccess();
	}


}