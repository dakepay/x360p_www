<?php

namespace app\api\model;

class ReportClassByName extends Base
{

	public static function buildReport($sids)
	{
		// print_r($cids);exit;
		foreach ($sids as $k => $sid) {
            
            $w_c['sid'] = $sid;
			$cids = ClassStudent::where($w_c)->column('cid');

			foreach ($cids as $k1 => $cid) {
				$cinfo = get_class_info($cid);
				$sinfo = get_student_info($sid);
				$data = [];
				$data['og_id'] = gvar('og_id');
				$data['sid'] = $sid;
				$data['cid'] = $cid;
				$data['bid'] = $cinfo['bid'];
				$data['teach_eid'] = $cinfo['teach_eid'];
				$data['status'] = (new ClassStudent)->where(['sid'=>$sid,'cid'=>$cid])->value('status');
				$data['lid'] = $cinfo['lid'];
				$int_day = $cinfo['create_time'];
			    $data['int_day'] = date('Ymd',$int_day);

			    $w['cid'] = $cid;
			    $w['sid'] = $sid;
				$model = new ReportClassByName;
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
		

	}
}