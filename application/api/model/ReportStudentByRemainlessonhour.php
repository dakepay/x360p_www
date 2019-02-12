<?php

namespace app\api\model;

class ReportStudentByRemainlessonhour extends Base
{

	public static function buildReport($sids)
	{
		// print_r($sids);exit;
		foreach ($sids as $sid) {
			$data = [];
			$sinfo = get_student_info($sid);
			$data['og_id'] = gvar('og_id');
			$data['bid'] = $sinfo['bid'];
			$data['sid'] = $sid;
			$data['status'] = $sinfo['status'];

            $w_c['sid'] = $sid;
            $w_c['status'] = 1;
            $cids = ClassStudent::where($w_c)->column('cid');

			$data['cids'] = implode(',',$cids);
            
            $w_l['sid'] = $sid;
            $w_l['lesson_status'] = ['in',['0','1']];
            $lids = StudentLesson::where($w_l)->column('lid');
			$data['lids'] = implode(',',$lids);
            
            $lesson_hour = StudentLesson::where($w_l)->sum('lesson_hours');
			$data['lesson_hour'] = $lesson_hour;
			$data['remain_lesson_hour'] = StudentLesson::where($w_l)->sum('remain_lesson_hours');
			$data['remain_money'] = StudentLesson::where($w_l)->sum('remain_lesson_amount');

			$w['sid'] = $sid;
			$model = new ReportStudentByRemainlessonhour;
			$exist_data = $model->where($w)->find();
			if($exist_data){
				$where['id'] = $exist_data['id'];
	            $model->save($data,$where);
			}else{
				$model->isUpdate(false)->save($data);
			}
		}
	}
}