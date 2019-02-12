<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportStudentSummary;

class ReportStudentSummarys extends Export
{
	protected $columns = [

	    ['field'=>'bid','title'=>'校区','width'=>20],
	    ['field'=>'course_arrange_times','title'=>'排课次数','width'=>20],
	    ['field'=>'course_arrange_student_times','title'=>'排课人次数','width'=>20],
	    ['field'=>'course_arrange_student_nums','title'=>'排课人数','width'=>20],
	    ['field'=>'student_attendance_times','title'=>'出勤人次数','width'=>20],
	    ['field'=>'student_attendance_nums','title'=>'出勤人数','width'=>20],
	    ['field'=>'student_leave_times','title'=>'请假人次数','width'=>20],
	    ['field'=>'student_leave_nums','title'=>'请假人数','width'=>20],

	];

	protected function get_title()
	{
        $params = $this->params;
        $title = sprintf('(%s~%s)',$params['start_date'],$params['end_date']);
        return $title;
	}


	protected function get_columns()
	{
		$input = $this->params;
		$arr = $this->columns;
		$input['type'] = isset($input['type']) ? $input['type'] : 2;
		if($input['type'] == 2){
            $arr[0] = ['field'=>'dept_id','title'=>'分公司','width'=>20];
		}
		return $arr;
	}


	protected function get_data()
	{
		$input = $this->params;
		// print_r($input);exit;
		$model = new ReportStudentSummary;
		$w = [];

		$w['start_int_day'] = format_int_day($input['start_date']);
        $w['end_int_day']   = format_int_day($input['end_date']);

        $input['type'] = isset($input['type']) ? $input['type'] : 2;

        if($input['type'] == 2){
        	unset($input['type']);
        	$group = 'dept_id';
        	$ret = $model->where($w)->group($group)->getSearchResult($input,[],false);
        	foreach ($ret['list'] as &$row) {
        		$w['dept_id'] = $row['dept_id'];
        		$row['dept_id'] = $row['dept_id'] ? get_department_name($row['dept_id']) : '总部';
        		$row['course_arrange_times'] = $model->where($w)->sum('course_arrange_times');
        		$row['course_arrange_student_times'] = $model->where($w)->sum('course_arrange_student_times');
        		$row['course_arrange_student_nums'] = $model->where($w)->sum('course_arrange_student_nums');
        		$row['student_attendance_times'] = $model->where($w)->sum('student_attendance_times');
        		$row['student_attendance_nums'] = $model->where($w)->sum('student_attendance_nums');
        		$row['student_leave_times'] = $model->where($w)->sum('student_leave_times');
        		$row['student_leave_nums'] = $model->where($w)->sum('student_leave_nums');
        	}
        }else{
        	unset($input['type']);
        	$ret = $model->where($w)->getSearchResult($input,[],false);
	        foreach ($ret['list'] as &$row) {
	        	$row['bid'] = get_branch_name($row['bid']);
	        }
        }

        

        if(!empty($ret['list'])){
        	return collection($ret['list'])->toArray();
        }
        return [];

	}


}