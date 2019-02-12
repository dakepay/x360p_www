<?php
/**
 * Author: luo
 * Time: 2018/1/27 16:17
 */

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class HandoverWork extends Base
{

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $insert = ['submit_time'];

    protected $type = [
        'submit_time' => 'timestamp',
        'ack_time' => 'timestamp',
    ];

    public function setSubmitTimeAttr()
    {
        return time();
    }

    public function getSubmitTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i:s', $value) : 0;
    }

    public function getAckTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i:s', $value) : 0;
    }

    public function orderReceiptBill()
    {
        return $this->hasMany('OrderReceiptBill', 'hw_id', 'hw_id');
    }

    public function orderRefund()
    {
        return $this->hasMany('OrderRefund', 'hw_id', 'hw_id');
    }

    /*
     * @desc 增加一次交班
     * @step:
     *  - 可能只是他自己收到的缴费情况
     *  - 可能把所有交给他自己的班，交班
     */
    public function addOneHandoverWork($data)
    {
        if(!isset($data['eid'])) return $this->user_error('没有交班人');
        $uid = Employee::getUidByEid($data['eid']);
        $m_orb = new OrderReceiptBill();
        $m_or = new OrderRefund();

        //--1.1-- 如果没有提交所有收据id,则以全部未交班收据交班
        if(!isset($data['orb_ids'])) {
            $data['orb_ids'] = $m_orb->where(['hw_id' => 0, 'create_uid' => $uid])->column('orb_id');
        }

        //--1.2-- 如果没有提交所有退款id,则以全部未交班退款交班
        if(!isset($data['or_ids'])) {
            $data['or_ids'] = $m_or->where(['hw_id' => 0, 'create_uid' => $uid])->column('or_id');
        }

        if(empty($data['orb_ids']) && empty($data['or_ids']) && !isset($data['last_hw_id'])) {
            return $this->user_error('既没有相关收据，也没有上一次交班人，交班不了');
        }

        //--1.3-- 计算增加的金额
        if(isset($data['orb_ids']) && is_array($data['orb_ids']) && !empty($data['orb_ids'])) {
            $data['orb_ids'] = array_unique(array_filter($data['orb_ids']));
            $data['money_inc_amount'] = $m_orb->where('orb_id', 'in', $data['orb_ids'])->sum('money_paid_amount');
            $data['cash_inc_amount'] = $this->calCashAmountByOrbIds($data['orb_ids']);
        }

        //--1.4-- 计算减少的金额
        if(isset($data['or_ids']) && is_array($data['or_ids']) && !empty($data['or_ids'])) {
            $data['or_ids'] = array_unique(array_filter($data['or_ids']));
            $data['money_dec_amount'] = $m_or->where('or_id', 'in', $data['or_ids'])->sum('refund_amount');
            $data['cash_dec_amount'] = $this->calCashAmountByOrIds($data['or_ids']);
        }

        $data = $this->getStartTimeAndEndTime($data['orb_ids'], $data['or_ids'], $data);
        try {
            $this->startTrans();

            $rs = $this->data($data)->allowField(true)->isUpdate(false)->save();
            if ($rs === false) throw new FailResult('交班失败');

            $hw_id = $this->getAttr('hw_id');
            if (isset($data['orb_ids']) && is_array($data['orb_ids'])) {
                $rs = $m_orb->where('orb_id', 'in', $data['orb_ids'])->update(['hw_id' => $hw_id]);
                if($rs === false) throw new FailResult('更新收据交班信息失败');
            }
            if (isset($data['or_ids']) && is_array($data['or_ids'])) {
                $rs = $m_or->where('or_id', 'in', $data['or_ids'])->update(['hw_id' => $hw_id]);
                if($rs === false) throw new FailResult('更新退款交班信息失败');
            }

            if(isset($data['hw_ids']) && is_array($data['hw_ids']) && !empty($data['hw_ids'])) {
                $data['hw_ids'] = array_unique(array_filter($data['hw_ids']));
                $rs = $this->where('hw_id', 'in', $data['hw_ids'])->update(['to_hw_id' => $hw_id]);
                if($rs === false) throw new FailResult('更新交班去向失败');
            }

            //生成待办记录
            $this->addBacklogOfHandover($data);

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

    public function getStartTimeAndEndTime($orb_ids, $or_ids, $data)
    {
        $receipt_start_time = 0;
        $receipt_end_time = 0;
        $refund_start_time = 0;
        $refund_end_time = 0;

        if(!empty($orb_ids)) {
            sort($orb_ids);
            $m_orb = new OrderReceiptBill();
            $bill = $m_orb->where('orb_id', array_shift($orb_ids))->find();
            $receipt_start_time = $bill->getData('create_time');
            if(!empty($orb_ids)) {
                $bill = $m_orb->where('orb_id', array_pop($orb_ids))->find();
                $receipt_end_time = $bill->getData('create_time');
            }
        }

        if(!empty($or_ids)) {
            sort($or_ids);
            $m_or = new OrderRefund();
            $bill = $m_or->where('or_id', array_shift($or_ids))->find();
            $refund_start_time = $bill->getData('create_time');
            if(!empty($or_ids)) {
                $bill = $m_or->where('or_id', array_pop($or_ids))->find();
                $refund_end_time = $bill->getData('create_time');
            }
        }

        $data['start_time'] = $receipt_start_time >= $refund_start_time ? $receipt_start_time : $refund_start_time;
        $data['end_time'] = $receipt_end_time >= $refund_end_time ? $receipt_start_time : $refund_end_time;

        return $data;
    }

    //交班生成一条待办
    public function addBacklogOfHandover($data)
    {
        if(!isset($data['to_eid'])) return true;
        $backlog_data = [
            'create_uid' => Employee::getUidByEid($data['to_eid']),
            'int_day' => date('Ymd', time()),
            'desc' => '您有一条交班待确认信息',
        ];
        Backlog::create($backlog_data);
    }

    //撤消交班
    public function delHandoverWork($hw_id, $handover_work = null)
    {
        if(is_null($handover_work)) {
            $handover_work = $this->find($hw_id);
            if(empty($handover_work)) return $this->user_error('没有交班记录');
        }

        if($handover_work->getData('ack_time') > 0) return $this->user_error('交班已经确认，撤消不了');

        try {
            $this->startTrans();
            $m_orb = new OrderReceiptBill();
            $rs = $m_orb->where('hw_id', $hw_id)->update(['hw_id' => 0]);
            if($rs === false) throw new Exception('撤消收据交班失败');

            $m_or = new OrderRefund();
            $rs = $m_or->where('hw_id', $hw_id)->update(['hw_id' => 0]);
            if($rs === false) throw new Exception('撤消退款交班失败');

            $rs = $handover_work->delete();
            if($rs === false) throw new Exception('撤消交班失败');

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        }

        return true;
    }


    //根据收据id计算收据中的现金部分金额
    public function calCashAmountByOrbIds($orb_ids)
    {
        $orb_ids = array_unique(array_filter($orb_ids));
        if(empty($orb_ids)) return 0;

        $m_oph = new OrderPaymentHistory();
        $amount = $m_oph->alias('o')->join('accounting_account a', 'a.aa_id = o.aa_id')
            ->where('a.type', AccountingAccount::TYPE_CASH)->where('o.orb_id', 'in', $orb_ids)
            ->sum('o.amount');
        return $amount;
    }

    //根据退款id计算退款中的现金部分金额
    public function calCashAmountByOrIds($or_ids)
    {
        $or_ids = array_unique(array_filter($or_ids));
        if(empty($or_ids)) return 0;

        $orh = new OrderRefundHistory();
        $amount = $orh->alias('o')->join('accounting_account a', 'a.aa_id = o.aa_id')
            ->where('a.type', AccountingAccount::TYPE_CASH)->where('o.or_id', 'in', $or_ids)
            ->sum('o.amount');
        return $amount;
    }



}