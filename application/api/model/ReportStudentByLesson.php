<?php
namespace app\api\model;



class ReportStudentByLesson extends Base
{
	public static function buildReport($sids)
	{
		foreach ($sids as $k => $v) {
			$sid = $v;
			$data = [];
			$data['og_id'] = gvar('og_id');
			$sinfo = get_student_info($sid);

			$data['bid'] = $sinfo['bid'];
			$data['sid'] = $sid;
			$data['student_name'] = $sinfo['student_name'];
			$data['status'] = $sinfo['status'];
			$data['sno'] = $sinfo['sno'];
			$data['first_tel'] = $sinfo['first_tel'];

			$w_lid['sid'] = $sid;
			$w_lid['lesson_status'] = ['in',['0','1']];
			$lids = StudentLesson::where($w_lid)->column('lid');
			$data['lids'] = implode(',',$lids);

			$w['sid'] = $sid;
			$model = new ReportStudentByLesson;
			$exist_data = $model->where($w)->find();

			if($exist_data){
				$w_ex = [];
	            $w_ex['id'] = $exist_data['id'];
	            $model->save($data,$w_ex);
			}else{
				$model->isUpdate(false)->save($data);
			}

		}
	}


	public function deleteOldData(ReportStudentByLesson $rsbl)
	{
		$res = $rsbl->delete();
		return $this;
	}




}