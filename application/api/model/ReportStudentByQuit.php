<?php 

namespace app\api\model;

class ReportStudentByQuit extends Base
{

	public static function buildReport($sids)
	{
		foreach ($sids as $k => $sid) {
			$data = [];
			$data['og_id'] = gvar('og_id');
			$sinfo = get_student_info($sid);
			$data['bid'] = $sinfo['bid'];
			$data['sid'] = $sid;
			$data['quit_reason'] = $sinfo['quit_reason'];
			$data['quit_time'] = StudentLog::where('sid',$sid)->value('create_time');

			$w_sid['sid'] = $sid;
            $w_sid['is_end'] = 0;
			$cids = ClassStudent::where($w_sid)->column('cid');
			$data['cids'] = implode(',',array_unique($cids));

			$w_lid['sid'] = $sid;
			$lids = StudentLesson::where($w_lid)->column('lid');
			$data['lids'] = implode(',',array_unique($lids));

			$model = new ReportStudentByQuit;
			$exist_data = $model->where('sid',$sid)->find();

			if($exist_data){
				$w_ex = [];
	            $w_ex['id'] = $exist_data['id'];
	            $model->save($data,$w_ex); 
			}else{
				$model->isUpdate(false)->save($data);
			}
		}
	}

}