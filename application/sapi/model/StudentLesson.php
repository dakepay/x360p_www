<?php
/**
 * Author: luo
 * Time: 2017-12-23 11:45
**/

namespace app\sapi\model;

use think\Exception;

class StudentLesson extends Base
{
    const AC_STATUS_NO = 0; //未分班
    const AC_STATUS_SOME = 1; //部分分班
    const AC_STATUS_ALL = 2; //完成分班

    const LESSON_STATUS_NO = 0; //未开始上课
    const LESSON_STATUS_ING = 1; //上课中
    const LESSON_STATUS_DONE = 2; //已经结课

    public $type = [
        'last_attendance_time' => 'timestamp',
    ];

    public function setExpireTimeAttr($value)
    {
        return $value ? strtotime($value) : 0;
    }

    public function getExpireTimeAttr($value)
    {
        return $value ? date('Y-m-d', $value) : 0;
    }

    public function getLastAttendanceTimeAttr($value)
    {
        return $value && is_numeric($value) ? date('Y-md H:i', $value) : $value;
    }

    public function getSjIdsAttr($value,$data)
    {
        if(empty($value)){
            return [];
        }

        if(is_array($value)) return $value;

        $value = explode(',', $value);
        $value = array_map(function($id){
            return intval($id);
        }, $value);

        return $value;
    }

    public function student()
    {
        return $this->hasOne('Student','sid','sid')
            ->field('sid,bid,student_name,nick_name,sex,photo_url,birth_time,first_family_name,card_no,sno');
    }

    public function lesson()
    {
        return $this->hasOne('Lesson', 'lid', 'lid')->field('lid,price_type,lesson_name,unit_lesson_hours,lesson_cover_picture');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid');
    }

    public function orderItems()
    {
        return $this->hasMany('OrderItem', 'sl_id', 'sl_id')->order('create_time', 'asc');
    }

}