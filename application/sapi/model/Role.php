<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/21
 * Time: 9:30
 */
namespace app\sapi\model;

use think\Db;

class Role extends Base
{
    public function employee()
    {
        return $this->belongsToMany('Employee', 'EmployeeRole', 'eid', 'rid');
    }

    public function getPersAttr($value, $data)
    {
        return explode(',', trim($value, ',')) ;
    }

    public function setPersAttr($value)
    {
        return join(',', $value);
    }

    public function getMobilePersAttr($value, $data)
    {
        return explode(',', trim($value, ',')) ;
    }

    public function setMobilePersAttr($value)
    {
        return join(',', $value);
    }
}