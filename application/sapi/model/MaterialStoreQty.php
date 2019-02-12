<?php
/**
 * Author: luo
 * Time: 2017-11-24 10:42
**/

namespace app\sapi\model;

use app\common\exception\FailResult;
use think\Exception;

class MaterialStoreQty extends Base
{
    public function store()
    {
        return $this->hasOne('MaterialStore', 'ms_id', 'ms_id')
            ->field('ms_id,name');
    }


}