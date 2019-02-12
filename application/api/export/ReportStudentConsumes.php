<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\StudentLessonHour;

class ReportStudentConsumes extends Export
{
	protected $columns = [
        ['field'=>'sid','title'=>'学员','width'=>20],
        ['field'=>'sno','title'=>'学号','width'=>20],
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'sum_lesson_hours','title'=>'课时数','width'=>20],
        ['field'=>'sum_total_lesson_amount','title'=>'课时金额','width'=>20],
 
	];

	protected function get_title()
	{
		$input = $this->params;
	    $time = sprintf('%s_%s',format_int_day($input['start_date']),format_int_day($input['end_date']));
	    $title = $time.'课耗确收表(按学员)';
	    return $title;
	}

	protected function get_data()
	{
		$input = $this->params;
        $model = new StudentLessonHour();

        $w = [];
        if (!empty($input['start_date'])) {
            $w['int_day'] = ['between', [date('Ymd', strtotime($input['start_date'])), date('Ymd', strtotime($input['end_date']))]];
        }
        if (!empty($input['group'])) {
            $group = explode(',', $input['group']);
        } else {
            $group = [];
        }

        $fields = $group;
        $fields["sum(lesson_hours)"]        = 'sum_lesson_hours';
        $fields["sum(lesson_amount)"]       = 'sum_total_lesson_amount';
        $fields["count(slh_id)"]            = 'sum_lesson_times';
        $with = [];
        if (in_array('sid', $group)) {
            $with['student'] = function($query) {
                $query->field(['sid', 'bid', 'sno', 'student_name']);
            };
        }

        $model->where($w)
            ->group(join(',', $group))
            ->field($fields)
            ->with($with);
        if (!empty($input['order_field'])) {
            $model->order($input['order_field'], $input['order_sort']);
        } else {
            $input['order_field'] = 'sum_lesson_hours';
            $input['order_sort']  = 'desc';
        }
        $ret = $model->getSearchResult($input,[],false);
        foreach ($ret['list'] as &$item) {
            $sinfo = get_student_info($item['sid']);
            $item['sid'] = $sinfo['student_name'];
            $item['sno'] = $sinfo['sno'];
            $item['bid'] = get_branch_name($sinfo['bid']);
        }

        if(!empty($ret['list'])){
        	return collection($ret['list'])->toArray();
        }
        return [];
	}


}