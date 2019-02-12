<?php 

namespace app\api\controller;
use think\Request;
use app\api\model\ReportStudentByLesson;
use app\api\model\StudentLesson;
use app\api\model\Student;



class ReportStudentByLessons extends Base
{

	public function get_list(Request $request)
	{
        $input = $request->get();
        $model = new ReportStudentByLesson;

        $w = [];
        $w['status'] = 1;
        if(!empty($input['lid'])){
        	$w[] = ['exp', "find_in_set({$input['lid']},lids)"];
        }
        if(!empty($input['student_name'])){
        	$student_name = $input['student_name'];
        	$w['student_name'] = ['like','%'.$student_name.'%'];
        }
        if(!empty($input['sno'])){
        	$sno = $input['sno'];
        	$w['sno'] = ['like','%'.$sno.'%'];
        }
        if(!empty($input['first_tel'])){
        	$first_tel = $input['first_tel'];
        	$w['first_tel'] = ['like','%'.$first_tel.'%'];
        }

        $fields = ['sno','student_name','bid','sid','lids','first_tel'];
        $data = $model->where($w)->field($fields)->order('sid asc')->getSearchResult($input);

        // print_r($data);exit;

        foreach ($data['list'] as $k => $v) { 
                $data['list'][$k]['lids'] = explode(',',$data['list'][$k]['lids']);
        }


        if(empty($data)){
        	$model = new StudentLesson;
		$sids = $model->column('sid');
		$sids = array_unique($sids);
		$ret = ReportStudentByLesson::buildReport($sids);
		if($ret === false){
			return $this->sendError(400,$ret);
		}
		$data = $model->where($w)->field($fields)->order('sid asc')->getSearchResult();
                foreach ($data['list'] as $k => $v) {
                        $data['list'][$k]['lids'] = explode(',',$data['list'][$k]['lids']);    
                }


        }
        return $this->sendSuccess($data);
	}


	public function post(Request $request)
	{
		$model = new StudentLesson;
                $bids = $request->header('x-bid');
                $bids = explode(',',$bids);
                $w['og_id'] = gvar('og_id');
                $w['bid'] = ['in',$bids];
		$sids = $model->where($w)->column('sid');
		$sids = array_unique($sids);

                // 添加数据前 清空之前的数据
                $old_sids = (new student)->where($w)->column('sid');
                $m_rsbl = new ReportStudentByLesson;
                foreach ($old_sids as $sid) {
                        $student = ReportStudentByLesson::get(['sid'=>$sid]);
                        if(!empty($student)){
                            $m_rsbl->deleteOldData($student);
                        }
                }

		$ret = ReportStudentByLesson::buildReport($sids);
		if($ret === false){
			return $this->sendError(400,$ret);
		}
		return $this->sendSuccess();
	}


}