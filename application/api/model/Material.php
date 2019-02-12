<?php
/**
 * Author: luo
 * Time: 2017-11-24 10:37
**/

namespace app\api\model;

use think\Exception;

class Material extends Base
{

    public function materialStoreQty()
    {
        return $this->hasMany('MaterialStoreQty', 'mt_id', 'mt_id');
    }

    public function delMaterial(Material $material)
    {
        $this->startTrans();
        try {
            $rs = MaterialStoreQty::destroy(['mt_id' => $material->mt_id]);
            if($rs === false) exception('删除物品库存失败');

            $rs = $material->delete();
            if($rs === false) exception('删除物品失败');

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

}