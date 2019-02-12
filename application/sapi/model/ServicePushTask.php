<?php
/**
 * Author: luo
 * Time: 2018/6/23 9:53
 */

namespace app\sapi\model;


class ServicePushTask extends Base
{

    protected function getPushTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i', $value) : $value;
    }

    public function filePackage()
    {
        return $this->hasOne('FilePackage', 'fp_id', 'rel_id');
    }



}