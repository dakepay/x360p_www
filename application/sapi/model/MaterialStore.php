<?php
/** 
 * Author: luo
 * Time: 2017-11-10 17:21
**/

namespace app\aspi\model;

use app\common\exception\FailResult;
use think\Exception;

class MaterialStore extends Base
{

    public function setBidsAttr($value)
    {
        is_array($value) && $value = implode(',', $value);

        return $value;
    }

    public function getBidsAttr($value)
    {
        return split_int_array($value);
    }

    public function branch()
    {
        return $this->hasMany('Branch', 'ms_id', 'ms_id')->field('ms_id, bid, branch_name');
    }

    public function materialStoreQty()
    {
        return $this->hasMany('MaterialStoreQty', 'ms_id', 'ms_id');
    }


}