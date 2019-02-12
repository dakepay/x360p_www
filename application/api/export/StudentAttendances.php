<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\StudentAttendance;


class StudentAttendances extends Export
{
    protected $res_name = 'student_attendance';

    protected $columns = [
        ['field'=>'sid','title'=>'学员','width'=>20],
        ['field'=>'status','title'=>'出勤状态','width'=>20],
        ['field'=>'is_consume','title'=>'课时','width'=>20],
        ['field'=>'lid','title'=>'课程','width'=>20],
        ['field'=>'cid','title'=>'班级','width'=>20],
        ['field'=>'lesson_type','title'=>' 课程类型','width'=>20],
        ['field'=>'eid','title'=>'上课老师','width'=>20],
        ['field'=>'int_day','title'=>'授课日期','width'=>30],
        ['field'=>'in_time','title'=>'出勤时间','width'=>20],
        // ['field'=>'is_attendance','title'=>'上课时段','width'=>20],
    ];

    protected function get_title(){
        $title = '学员考勤记录';
        return $title;
    }

    protected function convert_type($value)
    {
        $map = ['班课', '1对1', '1对多','研学旅行团'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function convert_status($is_in,$is_leave)
    {
        if($is_in===0 && $is_leave===1){
            return '缺勤 请假';
        }else if($is_in===0){
            return '缺勤';
        }else if($is_leave===1){
            return '请假';
        }
        return '正常';
    }

    protected function get_int_day($day,$start,$end){
        return int_day_to_date_str($day).' '.int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end);
    }

    public function get_data()
    {
        $model = new StudentAttendance();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['status'] = $this->convert_status($v['is_in'],$v['is_leave']);
            $list[$k]['sid']    = get_student_name($v['sid']);
            $list[$k]['is_consume'] = $list[$k]['is_consume']==true?'已扣课时':'未扣课时';
            $list[$k]['lid']            = get_lesson_name($v['lid']);
            $list[$k]['cid']            = get_class_name($v['cid']);
            $list[$k]['lesson_type']    = $this->convert_type($v['lesson_type']);
            $list[$k]['eid']            = get_teacher_name($v['eid']);
            // $list[$k]['int_day']        = int_day_to_date_str($v['int_day']);
            $list[$k]['int_day'] = $this->get_int_day($v['int_day'],$v['int_start_hour'],$v['int_end_hour']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}