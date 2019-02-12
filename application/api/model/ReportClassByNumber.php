<?php
namespace app\api\model;

class ReportClassByNumber extends Base
{
	public static function buildReport($cids)
	{
		foreach ($cids as $k => $cid) {
			$data = [];
			$data['og_id'] = gvar('og_id');
			$data['bid'] = Classes::where('cid',$cid)->value('bid');
			$data['cid'] = $cid;
			
			$cinfo = get_class_info($cid);
			$data['lid'] = $cinfo['lid'];
			$data['sj_id'] = $cinfo['sj_id'];
			$data['teach_eid'] = $cinfo['teach_eid'];
			$int_day = $cinfo['create_time'];
			$data['status'] = $cinfo['status'];
			$data['int_day'] = date('Ymd',$int_day);
            
            $w_n['cid'] = $cid;
            $w_n['status'] = 1;
			$data['student_num'] = ClassStudent::where($w_n)->count();

			$w['cid'] = $cid;
			$model = new ReportClassByNumber;
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


	public function deleteOldData(ReportClassByNumber $class)
	{
		$res = $class->delete();
		return $this;
	}


}