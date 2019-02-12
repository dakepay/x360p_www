<?php
/**
 * Author: luo
 * Time: 2018/5/29 11:21
 */

namespace app\sapi\model;


class StudySituation extends Base
{

    protected $type = ['content' => 'json'];

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];


    public function lessonBuySuit()
    {
        return $this->hasOne('LessonBuySuit', 'lbs_id', 'lbs_id');
    }

    public function studySituationItem()
    {
        $this->hasMany('StudySituationItem', 'ss_id', 'ss_id');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')
            ->field('sid,bid,student_name,sex,photo_url,birth_time,school_grade,school_class,school_id,first_tel,sno,card_no');
    }

    public function customer()
    {
        return $this->hasOne('Customer', 'cu_id', 'cu_id')
            ->field('cu_id,bid,name,sex,birth_time,school_grade,school_class,school_id,first_tel');
    }

    public function questionnaire()
    {
        return $this->hasOne('Questionnaire', 'qid', 'qid');
    }

    public function createEmployee()
    {
        return $this->hasOne('Employee', 'eid', 'create_eid')->field('eid,ename,uid,mobile,photo_url');
    }

}