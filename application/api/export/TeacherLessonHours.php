<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\EmployeeLessonHour;

class TeacherLessonHours extends Export
{
     
    protected $columns = [
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'edu_eid','title'=>'班主任','width'=>20],

        ['field'=>'eid','title'=>'老师','width'=>20],
        ['field'=>'second_eid','title'=>'助教','width'=>20],
        ['field'=>'lesson_type','title'=>'课程类型','width'=>20],

        ['field'=>'lid','title'=>'课程','width'=>20],
        ['field'=>'sj_id','title'=>'科目','width'=>20],
        ['field'=>'grade','title'=>'年级','width'=>20],
        ['field'=>'student_nums','title'=>'出勤人数','width'=>20],
        ['field'=>'int_day','title'=>'考勤日期','width'=>20],
        ['field'=>'section','title'=>'考勤时段','width'=>20],
        ['field'=>'lesson_hours','title'=>'课时数','width'=>20],
        ['field'=>'total_lesson_hours','title'=>'总课时数','width'=>20],
        ['field'=>'total_lesson_amount','title'=>'总课时金额','width'=>20],

    ];


    protected function get_title(){
        $title = '班主任产出';
        return $title;
    }

    protected function get_columns(){
        $arr = $this->columns;
        if(!user_config('params.enable_grade')){
            unset($arr[6]);
            $arr = array_values($arr);
        }
        return $arr;
    }

    protected function convert_type($value)
    {
        $map = [0=>'班课', 1=>'1对1', 2=>'1对多' ,3=>'研学旅行团'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function get_section($day,$start,$end)
    {
    	if($day){
    		return '周'.int_day_to_week($day).' '.int_hour_to_hour_str($start).'~'.int_hour_to_hour_str($end);
    	}else{
    		return '-';
    	}
    }


    public function get_data()
    {
        $model = new EmployeeLessonHour();
        $data = $model->getSearchResult($this->params,[],false);

        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['bid'] = get_branch_name($v['bid']);
        	$data['list'][$k]['lid'] = get_lesson_name($v['lid']);
        	$data['list'][$k]['sj_id'] = get_subject_name($v['sj_id']);
        	$data['list'][$k]['eid'] = get_teacher_name($v['eid']);
        	$data['list'][$k]['second_eid'] = get_teacher_name($v['second_eid']);
        	$data['list'][$k]['edu_eid'] = get_teacher_name($v['edu_eid']);
        	$data['list'][$k]['lesson_type'] = $this->convert_type($v['lesson_type']);
        	$data['list'][$k]['grade'] = get_grade_title($v['grade']);
        	$data['list'][$k]['int_day'] = int_day_to_date_str($v['int_day']);
        	$data['list'][$k]['section'] = $this->get_section($v['int_day'],$v['int_start_hour'],$v['int_end_hour']);
        }

      

        if (!empty($data['list'])) {
            return collection($data['list'])->toArray();
        }
        return [];

    }



}