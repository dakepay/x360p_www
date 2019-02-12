<?php
namespace app\ftapi\model;

class Student extends Base
{

    public $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected function getBirthTimeAttr($value)
    {
        return $value !== 0 ? date('Y-m-d', $value) : $value;
    }


}