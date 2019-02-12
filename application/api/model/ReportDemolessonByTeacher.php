<?php

namespace app\api\model;

class ReportDemolessonByTeacher extends Base
{

	public static function buildReport($input)
	{
		$cids = $input['cids'];
		foreach ($cids as $cid) {
			$cinfo = get_class_info($cid);
			$data = [];
			$data['og_id'] = gvar('og_id');
			$data['bid'] = $cinfo['bid'];
			$data['lid'] = $cinfo['lid'];
			$data['eid'] = $cinfo['teach_eid'];
			$data['cid'] = $cinfo['cid'];
			$data['int_day'] = int_day($cinfo['create_time']);
           
            $w_s['cid'] = $cid;
            $w_s['status'] = 1;
            $sids = ClassStudent::where($w_s)->column('sid');
			$data['sids'] = count($sids);

			$w_t['is_demo_transfered'] = 1;
            $w_t['sid'] = ['in',$sids];
			$data['transfered_sids'] = count(Student::where($w_t)->column('sid'));

			$w['cid'] = $cid;
			$model = new self();
			$exist_data = ReportDemolessonByTeacher::where($w)->find();
			if($exist_data){
                $where['id'] = $exist_data['id'];
                $model->save($data,$where);
			}else{
				$model->isUpdate(false)->save($data);
			}


		}

		return true;
	}
	
}