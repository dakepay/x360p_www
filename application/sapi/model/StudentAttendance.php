<?php
/**
 * Author: luo
 * Time: 2017-10-23 19:28
 **/

namespace app\sapi\model;

class StudentAttendance extends Base
{

    public function courseArrange()
    {
        return $this->hasOne('CourseArrange', 'ca_id', 'ca_id');
    }

}