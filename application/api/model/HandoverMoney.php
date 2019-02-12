<?php
/**
 * Author: luo
 * Time: 2018/1/27 17:50
 */

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class HandoverMoney extends Base
{

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $type = ['ack_time' => 'timestamp'];

    public function setAckTimeAttr($value, $data)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function getAckTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i:s', $value) : 0;
    }

    public function handoverWork()
    {
        return $this->hasMany('HandoverWork', 'hm_id', 'hm_id');
    }

    //增加一次缴费
    public function addOneHandoverMoney($data)
    {
        if(!isset($data['hw_ids']) || empty($data['hw_ids'])) {
            return $this->user_error('param error');
        }

        $m_hw = new HandoverWork();
        $inc_amount = $m_hw->where('hw_id', 'in', $data['hw_ids'])->sum('money_inc_amount');
        $dec_amount = $m_hw->where('hw_id', 'in', $data['hw_ids'])->sum('money_dec_amount');
        $cash_inc_amount = $m_hw->where('hw_id', 'in', $data['hw_ids'])->sum('cash_inc_amount');
        $cash_dec_amount = $m_hw->where('hw_id', 'in', $data['hw_ids'])->sum('cash_dec_amount');
        $data['amount'] = $inc_amount - $dec_amount;
        $data['cash_amount'] = $cash_inc_amount - $cash_dec_amount;

        try {
            $this->startTrans();
            $rs = $this->data($data)->allowField(true)->isUpdate(false)->save();
            if ($rs === false) throw new FailResult('缴费记录失败');

            $hm_id = $this->getAttr('hm_id');
            $rs = $m_hw->where('hw_id', 'in', $data['hw_ids'])->update(['hm_id' => $hm_id]);
            if ($rs === false) throw new FailResult('更新交班缴费失败');
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

    //确认交款
    public function ack($hm_id, $handover_money = null)
    {
        if(is_null($handover_money)) {
            $handover_money = $this->find($hm_id);
        }
        if(empty($handover_money) || $handover_money['ack_eid'] > 0) return $this->user_error('没有交款记录或者已经确认');

        $handover_money->ack_time = time();
        $employee = gvar('user.employee');
        $eid = !empty($employee) ? $employee['eid'] : (gvar('uid') == 1 ? 1 : 0 );
        $handover_money->ack_eid = $eid;
        $rs = $handover_money->save();
        if($rs === false) return $this->user_error('确认失败');

        return true;
    }

}