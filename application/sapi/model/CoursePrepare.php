<?php
/**
 * Author: luo
 * Time: 2018/4/9 9:35
 */

namespace app\sapi\model;

use app\common\exception\FailResult;
use think\Exception;

class CoursePrepare extends Base
{

    public function getSidsAttr($value)
    {
        return is_string($value) ? explode(',', $value) : $value;
    }

    public function coursePrepareAttachment()
    {
        return $this->hasMany('CoursePrepareAttachment', 'cp_id', 'cp_id');
    }

    public function courseArrange()
    {
        return $this->hasOne('CourseArrange', 'ca_id', 'ca_id')
            ->field('ca_id,name,cid,teach_eid,second_eid,lid,sj_id,cr_id,int_day,int_start_hour,int_end_hour,is_attendance,is_trial,is_makeup');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,sex,student_name,photo_url');
    }

}