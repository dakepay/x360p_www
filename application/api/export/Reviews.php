<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\Review;

class Reviews extends Export
{
	protected $columns = [
        ['field'=>'eid','title'=>'授课老师','width'=>20],
        ['field'=>'teach_obj','title'=>'授课对象','width'=>60],
        ['field'=>'create_time','title'=>'课评时间','width'=>25],
        ['field'=>'attendance_time','title'=>'考勤时间','width'=>25],
        ['field'=>'lesson_content','title'=>'课堂内容','width'=>40],
        ['field'=>'next_task','title'=>'下次课内容','width'=>40],
	];

	protected function get_title()
	{
		$title = '课评服务记录';
		return $title;
	}

	protected function get_teach_obj($lesson_type,$cid,$review_student = [])
	{
		switch ($lesson_type) {
			case '0':
				$type = get_class_name($cid);
				break;
		    case '1': 
		        $type = '一对一';
		        break;
		    case '2':
		        $type = '一对多';
		        break;
			default:
				break;
		}
		$student = [];
		foreach ($review_student as $per_student) {
			$student[] = $per_student['student']['student_name'];
		}
		$student = implode(',',$student);
		return $type.': '.$student;
	}

	protected function get_data()
	{
		$input = $this->params;
		$m_review = new Review;

		$ret = $m_review->with(['reviewStudent.student'])->getSearchResult($input,[],false);

		foreach ($ret['list'] as &$row) {
			$row['eid'] = get_teacher_name($row['eid']);
			$row['teach_obj'] = $this->get_teach_obj($row['lesson_type'],$row['cid'],$row['review_student']);
			$row['attendance_time'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'~'.int_hour_to_hour_str($row['int_end_hour']);
			$row['lesson_content'] = isset($row['content']['lesson_content']) ? $row['content']['lesson_content'] : '';
			$row['next_task'] = isset($row['content']['next_task']) ? $row['content']['next_task'] : '';
		}

		if(!empty($ret['list'])){
			return collection($ret['list'])->toArray();
		}

		return [];

	}


}