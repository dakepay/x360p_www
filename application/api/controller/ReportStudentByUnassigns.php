<?php

namespace app\api\controller;
use think\Request;
use app\api\model\StudentLesson;
use app\api\model\Student;

class ReportStudentByUnassigns extends Base
{
	public function get_list(Request $request)
	{
		$input = $request->get();
		$model = new StudentLesson;

		$w_c['ac_status'] = 0;
		$w_c['lesson_type'] = 0;
		$w_c['lesson_status'] = ['in',['0','1']];
		$w_c['is_stop'] = 0;
        
        $fields = ['lid','sid','remain_lesson_hours'];
		$data = $model->where($w_c)->field($fields)->getSearchResult($input);

		foreach ($data['list'] as $k => $v) {
			$sinfo = get_student_info($v['sid']);
			$data['list'][$k]['sno'] = $sinfo['sno'];
			$data['list'][$k]['student_name'] = $sinfo['student_name'];
			$data['list'][$k]['first_tel'] = $sinfo['first_tel'];
		}
		return $this->sendSuccess($data);
	}
}