<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\StudentAbsence;


class StudentAbsences extends Export
{
    protected $res_name = 'student_absence';

    protected $columns = [
        ['field'=>'sid','title'=>'学员姓名','width'=>20],
        ['field'=>'lesson_type','title'=>'类型','width'=>20],
        ['field'=>'lesson_name','title'=>'课程','width'=>30],
        ['field'=>'class_name','title'=>'班级','width'=>30],

        ['field'=>'int_day','title'=>'缺课时间','width'=>20],
        ['field'=>'eid','title'=>'上课老师','width'=>40],
        ['field'=>'is_leave','title'=>'是否请假','width'=>20],
        ['field'=>'is_consume','title'=>'扣课时','width'=>20],
        ['field'=>'remark','title'=>'缺勤原因','width'=>20],
        ['field'=>'status','title'=>'补课安排','width'=>40],
    ];

    protected function get_title(){
        $title = '缺课记录';
        return $title;
    }

    protected function convert_leave($value)
    {
        $map = [0=>'未请假', 1=>'有请假'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function convert_consume($value)
    {
        $map = [0=>'未知', 1=>'已扣'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function convert_status($value)
    {
        $map = [0=>'未补课', 1=>'已安排',2=>'已补课'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function convert_lesson_type($value)
    {
        $map = [0=>'班课', 1=>'一对一',2=>'一对多'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function get_class_name($cid){
        if($cid == 0){
            return '-';
        }
        return get_class_name($cid);
    }

    protected function get_lesson_name($lid){
        if($lid == 0){
            return '-';

        }
        return get_lesson_name($lid);
    }

    public function get_data()
    {
        $model = new StudentAbsence();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['sid'] = get_student_name($v['sid']);
            $list[$k]['lesson_type'] = $this->convert_lesson_type($v['lesson_type']);
            $list[$k]['int_day'] = int_day_to_date_str($v['int_day']);
            $list[$k]['eid']   = get_teacher_name($v['eid']);
            $list[$k]['is_leave'] = $this->convert_leave($v['is_leave']);
            $list[$k]['is_consume'] = $this->convert_consume($v['is_consume']);
            $list[$k]['status'] = $this->convert_status($v['status']);
            $list[$k]['lesson_name'] = $this->get_lesson_name($v['lid']);
            $list[$k]['class_name'] = $this->get_class_name($v['cid']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}