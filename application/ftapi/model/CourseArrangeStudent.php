<?php
namespace app\ftapi\model;

use app\common\exception\FailResult;
use think\Exception;

class CourseArrangeStudent extends Base
{

    public function oneClass()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }

    public function student(){
        return $this->hasOne('Student','sid','sid');
    }

    public function lesson(){
        return $this->hasOne('Lesson','lid','lid');
    }

    public function getIntStartHourAttr($value,$data)
    {
         return date('H:s',strtotime($value));
    }

    public function getIntEndHourAttr($value,$data)
    {
         return date('H:s',strtotime($value));
    }

    public function getIntDayAttr($value,$data){
        return int_day_to_date_str($value);
    }



}