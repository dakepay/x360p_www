<?php
/**
 * Author: luo
 * Time: 2018/4/13 17:22
 */

namespace app\sapi\model;

class StudentExamScore extends Base
{

    public function studentExamSubjectScore()
    {
        return $this->hasMany('StudentExamSubjectScore', 'ses_id', 'ses_id');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url,sex');
    }

    public function studentExam()
    {
        return $this->hasOne('StudentExam', 'se_id', 'se_id');
    }


}