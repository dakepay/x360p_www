<?php
/** 
 * Author: luo
 * Time: 2017-11-10 17:21
**/

namespace app\api\model;

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

    public function createOneStore($data)
    {
        $this->startTrans();
        try {
            //--1-- 创建仓库
            $rs = $this->data([])->validate()->allowField(true)->isUpdate(false)->save($data);
            if (!$rs) throw new FailResult($this->getErrorMsg());

            $ms_id = $this->getLastInsID();

            //--2-- 关联校区
            $rs = $this->updateBranchStore($data['bids'], $ms_id);
            if(!$rs) exception($this->getErrorMsg());

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function updateBranchStore($bids, $ms_id)
    {
        if (isset($bids) && !empty($bids) && is_array($bids)) {
            $bids = array_filter($bids, 'is_numeric');
            $rs = (new Branch())->where('bid', 'in', $bids)->update(['ms_id' => $ms_id]);
            if($rs === false) return $this->user_error('更新校区仓库失败');
        }

        return true;
    }

    //删除仓库
    public function delOneStore(MaterialStore $store)
    {
        $this->startTrans();
        try {
            $rs = (new Branch())->where('ms_id', $store->ms_id)->update(['ms_id' => 0]);
            if($rs === false) exception('校区删除仓库失败');

            $rs = $store->delete();
            if(!$rs) exception('仓库删除失败');

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //编辑仓库
    public function updateStore(MaterialStore $store, $data)
    {
        $this->startTrans();
        try {

            //--1-- 更新仓库
            $rs = $store->allowField(true)->save($data);
            if ($rs === false) exception('仓库编辑失败');

            //--2-- 更新仓库的校区
            (new Branch())->where('ms_id', $store->ms_id)->update(['ms_id' => 0]);
            if (isset($data['bids'])) {
                $rs = $this->updateBranchStore($data['bids'], $store->ms_id);
                if (!$rs) exception($this->getErrorMsg());
            }
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        return true;
    }

}