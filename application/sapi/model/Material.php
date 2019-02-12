<?php
/**
 * Author: luo
 * Time: 2017-11-24 10:37
**/

namespace app\sapi\model;

use think\Exception;

class Material extends Base
{

    public function materialStoreQty()
    {
        return $this->hasMany('MaterialStoreQty', 'mt_id', 'mt_id');
    }

}