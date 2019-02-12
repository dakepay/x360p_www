<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\StudentLesson;

class ReportStudentByUnassigns extends Export
{
	protected $columns = [
        ['field'=>'lid','title'=>'课程名称','width'=>20],
        ['field'=>'sid','title'=>'学员姓名','width'=>20],
        ['field'=>'sno','title'=>'学号','width'=>20],
        ['field'=>'first_tel','title'=>'手机号','width'=>20],
        ['field'=>'remain_lesson_hours','title'=>'剩余课时数','width'=>20],
	];

	protected function get_title(){
		$title = '未分班学员统计';
		return $title;
	}

	protected function get_data()
	{
		$model = new StudentLesson;
        
        $w['ac_status'] = 0;
        $w['lesson_type'] = 0;
        $w['lesson_status'] = ['in',['0','1']];
		$data = $model->where($w)->getSearchResult($this->params,[],false);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['lid'] = get_lesson_name($v['lid']);
			$sinfo = get_student_info($v['sid']);
			$data['list'][$k]['sid'] = $sinfo['student_name'];
			$data['list'][$k]['sno'] = $sinfo['sno'];
			$data['list'][$k]['first_tel'] = $sinfo['first_tel'];
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
	}
}