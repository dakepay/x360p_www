<?php

namespace app\ftapi\model;

class StudentAttendance extends Base
{

    public function courseArrange()
    {
        return $this->hasOne('CourseArrange', 'ca_id', 'ca_id');
    }

    public function student(){
        return $this->hasOne('Student','sid','sid');
    }

//    public function getSidAttr($value,$data)
//    {
//        return get_student_info($value);
//    }

}