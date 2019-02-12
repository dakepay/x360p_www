<?php
namespace app\ftapi\model;

use think\Exception;

class Material extends Base
{

    public function materialStoreQty()
    {
        return $this->hasMany('MaterialStoreQty', 'mt_id', 'mt_id');
    }

}