<?php 

namespace app\api\model;

class ReportStudentByTeacher extends Base
{
	public static function buildReport($eids)
	{
		// print_r($eids);exit;
		foreach ($eids as $k => $teach_eid) {
 
            $einfo = get_employee_info($teach_eid);
            $bids = $einfo['bids'];
            $bids = explode(',',$bids);

            foreach ($bids as &$bid) {
            	$data = [];
				$data['og_id'] = gvar('og_id');
				$data['teach_eid'] = $teach_eid;
				$data['bids'] = $bid;
				$data['is_on_job'] = $einfo['is_on_job'];

				$w_c['teach_eid'] = $teach_eid;
	            $w_c['status'] = ['in',['0','1']];
	            $w_c['bid'] = $bid;
	            $class_nums = Classes::where($w_c)->count();
				$data['class_nums'] = $class_nums;

				$cids = Classes::where($w_c)->column('cid');
				$w_s['cid'] = ['in',$cids];
				$w_s['status'] = 1;
				$data['class_student_nums'] = ClassStudent::where($w_s)->count();

				$w_a['lesson_type'] = 1;
				$w_a['is_trial'] = 0;
				$w_a['is_cancel'] = 0;
				$w_a['teach_eid'] = $teach_eid;
				$w_a['bid'] = $bid;

				(new CourseArrangeStudent)->where(['sid'=>0,'cu_id'=>0])->delete();

				$ca_ids = CourseArrange::where($w_a)->column('ca_id');
				$w_cas['ca_id'] = ['in',$ca_ids];
				$sids = (new CourseArrangeStudent)->where($w_cas)->column('sid');
				$sids = array_unique($sids);
				$data['onetoone_student_nums'] = count($sids);


				$w_as['lesson_type'] = 2;
				$w_as['is_trial'] = 0;
				$w_as['is_cancel'] = 0;
				$w_as['teach_eid'] = $teach_eid;
				$w_as['bid'] = $bid;

				$ca_ids = CourseArrange::where($w_as)->column('ca_id');

				$w_cas['ca_id'] = ['in',$ca_ids];
				$sids = (new CourseArrangeStudent)->where($w_cas)->column('sid');
				$sids = array_unique($sids);
				$data['onetomore_student_nums'] = count($sids);

				

				$w['teach_eid'] = $teach_eid;
				$w['bids'] = $bid;
				$model = new ReportStudentByTeacher;
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


	public function deleteOldData(ReportStudentByTeacher $rsbt)
	{
		$res = $rsbt->delete(true);
		return $this;
	}


}