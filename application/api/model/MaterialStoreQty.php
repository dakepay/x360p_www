<?php
/**
 * Author: luo
 * Time: 2017-11-24 10:42
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class MaterialStoreQty extends Base
{
    public function store()
    {
        return $this->hasOne('MaterialStore', 'ms_id', 'ms_id')
            ->field('ms_id,name');
    }

    public function material()
    {
        return $this->hasOne('Material', 'mt_id', 'mt_id');
    }

    //仓库物品数量增加
    public function incMaterialNum($ms_id, $mt_id, $num)
    {
        $this->startTrans();
        try {

            $msq = $this->where('ms_id', $ms_id)->where('mt_id', $mt_id)->find();
            if(empty($msq)) {
                $rs = $this->data([])->allowField(true)->isUpdate(false)->save([
                    'ms_id' => $ms_id,
                    'mt_id' => $mt_id,
                ]);
                if(!$rs) exception('仓库物品数量关联失败');
            }

            //$rs = $this->where('ms_id', $ms_id)->where('mt_id', $mt_id)
            //    ->setInc('num', $num);
            //if(!$rs) exception('仓库物品数量增加失败');
            //
            //$rs = (new Material())->where('mt_id', $mt_id)->setInc('num', $num);
            //if(!$rs) exception('物品总数量增加失败');

            $rs = $this->updateMaterialNum($mt_id, $ms_id);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function updateMaterialNum($mt_id, $ms_id)
    {

        try {
            $this->startTrans();

            $m_mh = new MaterialHistory();
            $in_num_of_one_store = $m_mh->where('ms_id', $ms_id)->where('mt_id', $mt_id)
                ->where('type', MaterialHistory::TYPE_IN)->sum('num');
            $out_num_of_one_store = $m_mh->where('ms_id', $ms_id)->where('mt_id', $mt_id)
                ->where('type', MaterialHistory::TYPE_OUT)->sum('num');
            $total_num_of_one_store = $in_num_of_one_store - $out_num_of_one_store;

            $m_msq = new MaterialStoreQty();
            $rs = $m_msq->where(['mt_id' => $mt_id, 'ms_id' => $ms_id])->update(['num' => $total_num_of_one_store]);
            if($rs === false) throw new FailResult($m_msq->getErrorMsg());

            $m_material = new Material();
            $children_total_num = $m_material->where('parent_id', $mt_id)->sum('num');
            $total_num = $m_msq->where('mt_id', $mt_id)->sum('num');
            $rs = $m_material->where('mt_id', $mt_id)->update(['num' => $total_num + $children_total_num]);
            if($rs === false) throw new FailResult($m_material->getErrorMsg());

            $rs = $this->updateParentNum($mt_id);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }


        return true;
    }

    public function updateParentNum($self_mt_id)
    {
        $m_material = m('material');
        $material = $m_material->where('mt_id', $self_mt_id)->field('parent_id')->find();
        if(empty($material) || $material['parent_id'] <= 0) return true;

        $parent_material = $m_material->find($material['parent_id']);
        if(empty($parent_material)) return true;

        $m_msq = m('MaterialStoreQty');
        $children_total_num = $m_material->where('parent_id', $material['parent_id'])->sum('num');
        $parent_material_num = $m_msq->where('mt_id', $parent_material['mt_id'])->sum('num');
        $parent_material->num = $children_total_num + $parent_material_num;
        $rs = $parent_material->isUpdate(true)->save();
        if($rs === false) throw new FailResult($material->getError());

        if($parent_material['parent_id'] > 0) {
            return $this->updateParentNum($parent_material['mt_id']);
        }

        return true;
    }

    //仓库物品数量减少
    public function decMaterialNum($ms_id, $mt_id, $num)
    {
        $this->startTrans();
        try {

            //$msq = $m_msq->where('ms_id', $data['ms_id'])->where('mt_id', $data['mt_id'])->find();
            //if (empty($msq) || $msq->num < $data['num']) throw new FailResult('仓库物品数量不够');

            $msq = $this->where('ms_id', $ms_id)->where('mt_id', $mt_id)->find();
            if(empty($msq)) {
                $rs = $this->data([])->allowField(true)->isUpdate(false)->save([
                    'ms_id' => $ms_id,
                    'mt_id' => $mt_id,
                ]);
                if(!$rs) exception('仓库物品数量关联失败');
            }

            //$material = Material::get(['mt_id' => $mt_id]);
            //(new MaterialStoreQty())->where('ms_id', $ms_id)->where('mt_id', $mt_id)
            //    ->setDec('num', $num);
            //$material->setDec('num', $num);
            $rs = $this->updateMaterialNum($mt_id, $ms_id);
            if($rs === false) throw new FailResult($this->getErrorMsg());

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

    public function isEnoughMaterial($ms_id, $mt_id, $num)
    {
        $msq = MaterialStoreQty::get(['ms_id' => $ms_id, 'mt_id' => $mt_id]);
        if(empty($msq)) return false;

        if($msq->num < $num ) return false;

        return true;
    }

}