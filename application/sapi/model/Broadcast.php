<?php
/**
 * Author: luo
 * Time: 2017-11-24 17:22
**/

namespace app\sapi\model;

class Broadcast extends Base
{

    const TYPE_INTERNAL = 1;
    const TYPE_EXTERNAL = 2;

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function setDptIdsAttr($value)
    {
        is_array($value) && $value = implode(',', $value);

        return $value;
    }

    public function  getDptIdsAttr($value)
    {
        return split_int_array($value);
    }





}