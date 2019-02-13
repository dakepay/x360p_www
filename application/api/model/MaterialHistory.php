<?php
/**
 * Author: luo
 * Time: 2017-11-24 12:10
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class MaterialHistory extends Base
{

    const TYPE_IN = 1;  # 进库
    const TYPE_OUT = 2; # 出库

    const CATE_STOCK = 1;   # 进货
    const CATE_USE = 2;     # 领用
    const CATE_ALLOCATE = 3;# 调拔
    const CATE_DAMAGE = 4;  # 损坏
    const CATE_ORDER = 5;   # 报名下单
    const OUT_SALE = 5;   # 对外出售

    protected $skip_og_id_condition = true;

    public function setIntDayAttr($value)
    {
        return $value ? format_int_day($value) : date('Ymd', time());
    }

    public function material()
    {
        return $this->belongsTo('Material','mt_id','mt_id');
    }

    public function materialStore()
    {
        return $this->hasOne('MaterialStore', 'ms_id', 'ms_id');
    }

    public function toMaterialStore()
    {
        return $this->hasOne('MaterialStore', 'ms_id', 'to_ms_id');
    }

    //增加一个物品进出库记录
    public function addOneHis($data)
    {
        $rs = $this->validateData($data, 'MaterialHistory');
        if(!$rs) return $this->user_error($this->getErrorMsg());

        $store = MaterialStore::get($data['ms_id']);
        if(empty($store)) return $this->user_error('仓库ID不存在或已经被删除！'.$data['ms_id']);

        $this->startTrans();
        try {
            //--1-- 添加记录
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if (!$rs) throw new FailResult('添加进出记录失败');
            $mh_id = $this->mh_id;

            //--2-- 仓库物品数量处理、物品总数量处理
            if ($data['type'] == self::TYPE_IN) {   # 增加库存
                $m_msq = new MaterialStoreQty();
                $rs = $m_msq->incMaterialNum($data['ms_id'], $data['mt_id'], $data['num']);
                if (!$rs) throw new FailResult($m_msq->getError());
            }

            if ($data['type'] == self::TYPE_OUT) {  # 减少库存
                $m_msq = new MaterialStoreQty();

                $rs = $m_msq->decMaterialNum($data['ms_id'], $data['mt_id'], $data['num']);
                if (!$rs) throw new FailResult($m_msq->getError());
            }

            //--3-- 调拔要增加一条对方进出库记录
            if (isset($data['cate']) && $data['cate'] == self::CATE_ALLOCATE) {
                if (!isset($data['to_ms_id'])) throw new FailResult('调拔对方错误');
                $this->where('mh_id', $mh_id)->update(['remark' => '调拔出仓库']);
                $this->addInvolvedHis($mh_id);
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    //调拔相关的对方进出库记录
    public function addInvolvedHis($mh_id)
    {
        $his = $this->find($mh_id);
        $data = [
            'mt_id' => $his['mt_id'],
            'ms_id' => $his['to_ms_id'],
            'to_ms_id' => $his['ms_id'],
            'num' => $his['num'],
            'eid' => $his['eid'],
            'type' => $his['type'] === self::TYPE_OUT ? self::TYPE_IN : self::TYPE_IN,
            'cate' => $his['cate'],
            'remark' => '调拔入的仓库',
            'int_day' => $his['int_day']
        ];

        $this->startTrans();
        try {

            //--1-- 添加记录
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if (!$rs) exception('添加调拔记录失败');

            //--2-- 处理物品库存
            if ($data['type'] == self::TYPE_IN) {   # 进库
                $m_msq = new MaterialStoreQty();
                $rs = $m_msq->incMaterialNum($data['ms_id'], $data['mt_id'], $data['num']);
                if(!$rs) exception($m_msq->getErrorMsg());
            }

            if ($data['type'] == self::TYPE_OUT) {  # 出库
                $m_msq = new MaterialStoreQty();

                //$msq = $m_msq->where('ms_id', $data['ms_id'])->where('mt_id', $data['mt_id'])->find();
                //if (empty($msq) || $msq->num < $data['num']) throw new FailResult('仓库物品数量不够');

                $rs = $m_msq->decMaterialNum($data['ms_id'], $data['mt_id'], $data['num']);
                if(!$rs) exception($m_msq->getErrorMsg());
            }
            
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //物品批量进出仓
    public function addBatchHisOfMaterial($data)
    {
        if(!isset($data['data'])) return $this->user_error('缺少仓库及数量');

        $ms_id_data = $data['data']; # [{ms_id:1,num:2}] 各个仓库的物品数量
        unset($data['data']);
        $his_data = $data;

        $this->startTrans();
        try {
            foreach ($ms_id_data as $row) {
                $his_data = array_merge($his_data, $row);
                $rs = $this->addOneHis($his_data);
                if (!$rs) {
                    $this->rollback();
                    return $this->user_error($this->getErrorMsg());
                }
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

    /*
     * 1. 删除出入库记录
     * 2. 数量回退
     */
    public function delOneHis($mh_id)
    {
        $this->startTrans();
        try {
            $mOrderItem = new OrderItem();
            $w_o = [
              'gtype' => OrderItem::GTYPE_GOODS,
                'gid' => $mh_id
            ];
            $m_oi_list = $mOrderItem->where($w_o)->select();
            if ($m_oi_list){
                return $this->user_error('该物品已售出，不能删除');
            }

            $history = $this->find($mh_id);
            $history->delete();
            $m_msq = new MaterialStoreQty();
            if ($history['type'] == MaterialHistory::TYPE_IN) {
                $result = $m_msq->decMaterialNum($history->ms_id, $history->mt_id, $history->num);
                if (false === $result){
                    $this->rollback();
                    return $this->user_error('仓库物品数量复原失败');
                }
            }
            if ($history['type'] == MaterialHistory::TYPE_OUT) {
                $result = $m_msq->incMaterialNum($history->ms_id, $history->mt_id, $history->num);
                if (false === $result){
                    $this->rollback();
                    return $this->user_error('仓库物品数量复原失败');
                }
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

}