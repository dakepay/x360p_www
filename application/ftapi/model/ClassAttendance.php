<?php

namespace app\ftapi\model;

use think\Exception;
use think\Model;

class ClassAttendance extends Base
{
    protected $append = ['course_name'];

    public function ftReview() {
        return $this->hasOne('FtReview', 'catt_id', 'catt_id');
    }

    public function CourseArrange()
    {
        return $this->belongsTo('CourseArrange', 'ca_id', 'ca_id');
    }
//
//    public function c()
//    {
//        return $this->hasMany('Student', 'sid', 'ca_id');
//    }

    public function students()
    {
        return $this->hasMany('StudentAttendance', 'catt_id', 'catt_id');
    }

    public function lesson(){
        return $this->hasOne('Lesson','lid','lid');
    }

    public function oneClass()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }

//    public function getIntDayAttr($value,$data){
//        return int_day_to_date_str($value);
//    }
//
//    public function getIntStartHourAttr($value,$data)
//    {
//        return date('H:i:s',strtotime($value));
//    }
//
//    public function getIntEndHourAttr($value,$data)
//    {
//        return date('H:i:s',strtotime($value));
//    }

    public function getCourseNameAttr($value,$data){
        $course_name = get_course_name_by_row($data);

        return $course_name;
    }



}