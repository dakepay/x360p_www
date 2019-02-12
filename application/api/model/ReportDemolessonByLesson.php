<?php

namespace app\api\model;

class ReportDemolessonByLesson extends Base
{

	public function getSidsAttr($value,$data)
	{
		if(!empty($value)){
			return count(explode(',',$value));
		}
		return '0';
	}

	public function getTransferedSidsAttr($value,$data)
	{
		if(!empty($value)){
			return count(explode(',',$value));
		}
		return '0';
	}

	public static function buildReport($post)
	{
		$lids = $post['lids'];

		foreach ($lids as $lid) {
			$linfo = get_lesson_info($lid);
			$data = [];
			$data['og_id'] = gvar('og_id');
			$linfo = get_lesson_info($lid);
			$data['bids'] = $linfo['bids'];
			$data['lid'] = $lid;
			$data['int_day'] = int_day($linfo['create_time']);

			$cids = Classes::where('lid',$lid)->column('cid');
			$data['cids'] = count($cids);

			$sids = []; 
			foreach ($cids as $cid) {
				$w_s['cid'] = $cid;
				$w_s['status'] = 1;
				$sid = ClassStudent::where($w_s)->column('sid');
				$sids = array_merge($sids,$sid);
			}
			// $data['sids'] = count($sids);
			$data['sids'] = implode(',',$sids);
			
            
            $w_t['is_demo_transfered'] = 1;
            $w_t['sid'] = ['in',$sids];
			$transfered_sids = Student::where($w_t)->column('sid');
			// $data['transfered_sids'] = count($transfered_sids);
			$data['transfered_sids'] = implode(',',$transfered_sids);


			$w_exist['lid'] = $lid;
			$w_exist['og_id'] = gvar('og_id');
			$model = new self();
			$exist_data = $model->where($w_exist)->find();

			if($exist_data){
				$where['id'] = $exist_data['id'];
				$model->allowField(true)->save($data,$where);
			}else{
				$model->isUpdate(false)->save($data);
			}
			
		}

		return true;
	}

	public function deleteLessons($lid)
	{
		$res = (new ReportDemolessonByLesson)->where('lid',$lid)->delete();
		return $this;
	}
}