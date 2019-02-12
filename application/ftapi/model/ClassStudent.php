<?php

namespace app\ftapi\model;

use app\common\exception\FailResult;
use think\Db;
use think\Exception;

class ClassStudent extends Base
{

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid');
    }

}