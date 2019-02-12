<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportStudentByTeacher;

class ReportStudentByTeachers extends Export
{
	protected $columns = [
        ['field'=>'teach_eid','title'=>'老师名称','width'=>20],
        ['field'=>'class_nums','title'=>'班级数','width'=>20],
        ['field'=>'class_student_nums','title'=>'班级人数','width'=>20],
        ['field'=>'onetoone_student_nums','title'=>'1对1人数','width'=>20],
        ['field'=>'onetomore_student_nums','title'=>'1对多人数','width'=>20],
	];

	protected function get_title()
	{
		$title = '老师学员统计';
		return $title;
	}

	protected function get_data()
	{
		$model = new ReportStudentByTeacher;

		$w['is_on_job'] = 1;
		$bid = $this->params['bid'];
		$w[] = ['exp', "find_in_set({$bid},bids)"];
		$data = $model->where($w)->getSearchResult($this->params,[],false);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['teach_eid'] = get_teacher_name($v['teach_eid']);
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
	}
}