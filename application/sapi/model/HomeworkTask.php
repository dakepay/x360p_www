<?php
/**
 * Author: luo
 * Time: 2018/3/26 19:52
 */

namespace app\sapi\model;


class HomeworkTask extends Base
{

    const LESSON_TYPE_CLASS = 0; # 班课作业
    const LESSON_TYPE_ONE = 1;   # 一对一作业
    const LESSON_TYPE_MANY = 2;   # 一对多作业

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];


    public function setSidsAttr($value)
    {
        return !empty($value) && is_array($value) ? implode(',', $value) : $value;
    }

    public function setDeadlineAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function getSidsAttr($value)
    {
        return is_string($value) ? explode(',', $value) : $value;
    }

    //作业的多学员信息
    public function getStudentsAttr($value, $data)
    {
        $students = [];
        if(isset($data['sids']) && !empty($data['sids'])) {
            $sids = is_array($data['sids']) ? $data['sids'] : explode(',', $data['sids']);
            $students = (new Student())->where('sid', 'in', $sids)->field('sid,bid,student_name,sex,photo_url')->select();
        }
        return $students;
    }

    public function getStudentsCountAttr($value, $data)
    {
        $sids = [];
        if($data['lesson_type'] == self::LESSON_TYPE_CLASS) {
            $m_cs = new ClassStudent();
            $sids = $m_cs->where('status', ClassStudent::STATUS_NORMAL)->column('sid');
        } elseif ($data['lesson_type'] == self::LESSON_TYPE_ONE) {
            $sids = $data['sid'] > 0 ? [$data['sid']] : [];
        } elseif ($data['lesson_type'] == self::LESSON_TYPE_MANY) {
            $sids = is_array($data['sids']) ? $data['sids'] : explode(',', $data['sids']);
        }

        return count($sids);
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,bid,student_name,sex,photo_url');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,bid,class_name,lid');
    }

    public function homeworkAttachment()
    {
        return $this->hasMany('HomeworkAttachment', 'ht_id', 'ht_id');
    }

    public function homeworkComplete()
    {
        return $this->hasMany('HomeworkComplete', 'ht_id', 'ht_id');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'eid', 'eid')->field('eid,ename,uid,mobile,photo_url');
    }

}