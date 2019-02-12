<?php

namespace app\api\model;

class ReportStudentLessonClass extends Base
{

	public static function buildReport($sids){

		foreach ($sids as $k => $v) {

			$sid = $v;
			$data = [];
			$data['og_id'] = gvar('og_id');
			$sinfo = get_student_info($sid);

			$data['bid'] = $sinfo['bid'];
			$data['school_id'] = $sinfo['school_id'];
			$data['sid'] = $sid;
			$data['status'] = $sinfo['status'];
            
            $w_sid['sid'] = $sid;
            $w_sid['is_end'] = 0;
			$cids = ClassStudent::where($w_sid)->column('cid');
			$data['cids'] = implode(',',array_unique($cids));

			$w_lid['sid'] = $sid;
			$lids = StudentLesson::where($w_lid)->column('lid');
			$data['lids'] = implode(',',array_unique($lids));

			$model = new ReportStudentLessonClass;
			$exist_data = $model->where($w_lid)->find();

			if($exist_data){
				$w_ex = [];
	            $w_ex['id'] = $exist_data['id'];
	            $model->save($data,$w_ex);  
			}else{
				$model->isUpdate(false)->save($data);
			}

			// return $data;

		}
	}


	public function deleteOldDate($sid)
	{
		$res = (new ReportStudentLessonClass)->where('sid',$sid)->delete();
		return $this;
	}

}

