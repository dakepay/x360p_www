<?php
/**
 * Author: luo
 * Time: 2018/7/17 9:44
 */

namespace app\sapi\model;


class HomeworkPublish extends Base
{
    protected $type = [
        'publish_time' => 'timestamp'
    ];

    public function setPublishTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function homeworkComplete()
    {
        return $this->hasOne('HomeworkComplete', 'hc_id', 'hc_id');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

}