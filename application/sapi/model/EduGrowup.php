<?php
/**
 * Author: luo
 * Time: 2018/6/22 17:59
 */

namespace app\sapi\model;


class EduGrowup extends Base
{

    public function getAbilityIdsAttr($value)
    {
        return !empty($value) && is_string($value) ? explode(',', $value) : [];
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function eduGrowupItem()
    {
        return $this->hasMany('EduGrowupItem', 'eg_id', 'eg_id');
    }

    public function eduGrowupPic()
    {
        return $this->hasMany('EduGrowupPic', 'eg_id', 'eg_id');
    }

}