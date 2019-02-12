<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\EmployeeLessonHour;


class EmployeeLessonHours extends Export
{
    protected $res_name = 'employee_lesson_hour';

    protected $columns = [
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'eid','title'=>'教师姓名','width'=>20],
        ['field'=>'lesson_type','title'=>'课程类型','width'=>20],
        ['field'=>'lid','title'=>'课程','width'=>20],
        ['field'=>'class_name','title'=>'班级','width'=>20],
        ['field'=>'sj_id','title'=>'科目','width'=>20],
        ['field'=>'student_nums','title'=>'出勤人数','width'=>20],
        ['field'=>'int_day','title'=>'考勤日期','width'=>20],
        ['field'=>'section','title'=>'考勤时段','width'=>20],
        ['field'=>'lesson_hours','title'=>'课时数','width'=>20],
        ['field'=>'total_lesson_hours','title'=>'总课时数','width'=>20],
        ['field'=>'total_lesson_amount','title'=>'总课时金额','width'=>20],
    ];

    protected function get_title(){
        $title = '教师产出';
        return $title;
    }

    protected function convert_hour($start,$end,$day){
        return '星期'.int_day_to_week($day).' '.int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end);
    }

    protected function get_lesson_type_name($lesson_type){
        $map = [
            0   => '班课',
            1   => '1对1',
            2   => '1对多',
            3   => '研学旅行团'
        ];

        if(isset($map[$lesson_type])){
            return $map[$lesson_type];
        }
        return $lesson_type;
    }

    public function get_data()
    {
        $model = new EmployeeLessonHour();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['bid'] = get_branch_name($v['bid']);
            $list[$k]['sj_id'] = get_subject_name($v['sj_id']);
            $list[$k]['lid'] = get_lesson_name($v['lid']);
            $list[$k]['class_name'] = get_class_name($v['cid']);
            $list[$k]['lesson_type'] = $this->get_lesson_type_name($v['lesson_type']);
            $list[$k]['int_day']        = int_day_to_date_str($v['int_day']);
            $list[$k]['eid'] = get_teacher_name($v['eid']);
            $list[$k]['section'] = $this->convert_hour($v['int_start_hour'],$v['int_end_hour'],$v['int_day']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }


}