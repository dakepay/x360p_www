<?php
/**
 * Author: luo
 * Time: 2017-11-03 17:42
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class OrderReceiptBillItem extends Base
{

    public function orderPaymentHistory()
    {
        return $this->hasMany('OrderPaymentHistory', 'orb_id', 'orb_id');
    }

    public function orderItem()
    {
        return $this->hasOne('OrderItem', 'oi_id', 'oi_id');
    }

    public function orderReceiptBill()
    {
        return $this->hasOne('OrderReceiptBill', 'orb_id', 'orb_id');
    }

    public function createOneItem($data)
    {
        $rs = (new self())->allowField(true)->isUpdate(false)->save($data);
        if(false === $rs) return $this->user_error('创建收据项目失败');

        return true;
    }

    //报废收据项目
    public function delOneBillItem($orbi_id, $bill_item = null,$force = false)
    {
        if(is_null($bill_item)) {
            $bill_item = $this->find($orbi_id);
        }
        if(!$force) {
            $rs = $this->canDelBillItem($orbi_id, $bill_item);
            if ($rs === false) return false;
        }
        try {
            $this->startTrans();
            $is_supplement = $this->isSupplement($bill_item['oid']);

            $m_order = new Order();
            $m_order_item = new OrderItem();
            $m_or = new OrderRefund();
            if (!$is_supplement) {
                //--1.1-- 不是补缴
                $order_item = $m_order_item->find($bill_item['oi_id']);


                //--1.2-- 如果item是物品
                if ($order_item['gtype'] == OrderItem::GTYPE_GOODS) {
                    $m_or = new OrderRefund();
                    $rs = $m_or->refundMaterial($order_item, $order_item['nums']);
                    if($rs === false) throw new FailResult($m_or->getErrorMsg());
                }elseif($order_item['gtype'] == OrderItem::GTYPE_LESSON){
                    if(!$force) {
                        $rs = $m_order_item->delStudentLessonOfItem($order_item, false);
                        if ($rs === false) throw new FailResult($m_order_item->getErrorMsg());
                    }
                }

                //--1.3-- 更新订单item
                $order_item->paid_amount = 0;
                $rs = $order_item->allowField('paid_amount,sl_id')->save();
                if($rs === false) throw new FailResult('更新订单项目失败');
                
                //--1.4-- 更新订单
                $order = $m_order->find($order_item['oid']);
                $order->paid_amount = $order->paid_amount - $bill_item['paid_amount'];
                $order->money_paid_amount = $order->money_paid_amount - $bill_item['money_paid_amount'];
                $order->balance_paid_amount = $order->balance_paid_amount - $bill_item['balance_paid_amount'];
                $order->unpaid_amount = $order->unpaid_amount + $bill_item['paid_amount'];
                $order->order_status = Order::ORDER_STATUS_PLACE_ORDER;
                $order->is_submit = 0;
                $order->pay_status = Order::PAY_STATUS_NO;
                $order->ac_status = Order::AC_STATUS_NO;
                $rs = $order->save();
                if($rs === false) throw new FailResult('更新订单失败');
                
            } else {
                //--2.1-- 如果收据是补缴
                $sl_id = 0;
                $order_item = $m_order_item->find($bill_item['oi_id']);
                if ($order_item['paid_amount'] == $bill_item['paid_amount']) {
                    //--2.2-- 如果是由保存订单补缴
                    if ($order_item['gtype'] == OrderItem::GTYPE_LESSON) {
                        $rs = $m_order_item->delStudentLessonOfItem($order_item);
                        if($rs === false) throw new FailResult($m_order_item->getErrorMsg());

                    } elseif ($order_item['gtype'] == OrderItem::GTYPE_GOODS) {
                        $rs = $m_or->refundMaterial($order_item, $order_item['nums']);
                        if($rs === false) throw new FailResult($m_or->getErrorMsg());

                    }
                } else {
                    //--2.3-- 如果不是初次补缴
                    if ($order_item['gtype'] == OrderItem::GTYPE_LESSON) {
                        $lesson = Lesson::get(['lid' => $order_item['lid']]);
                        //--2.4-- 减少课次
                        $m_sl = new StudentLesson();
                        if (!empty($lesson) || !empty($order_item['cid'])) {
                            $sl_id = $order_item['sl_id'];
                            $student_lesson = $m_sl->find($order_item['sl_id']);
                            $order_item = $m_sl::calcLessonTimesAndHour($lesson, $order_item, $order_item['nums'], null, $order_item['cid']);
                            $order_item['lesson_amount'] = $order_item['subtotal'];//还回金额
                            $rs = $m_sl->handleStudentLessonHours($student_lesson, $order_item, StudentLesson::HANDLE_STUDENT_LESSON_HOURS_DELETE_BILL);
                            if($rs === false) throw new FailResult($m_sl->getErrorMsg());
                        }
                    } elseif ($order_item['gtype'] == OrderItem::GTYPE_GOODS) {
                        $rs = $m_or->refundMaterial($order_item, $order_item['nums']);
                        if($rs === false) throw new FailResult($m_or->getErrorMsg());

                    }

                }

                //--2.5-- 更新订单item
                $order_item->paid_amount = $order_item->paid_amount - $bill_item['paid_amount'];
                $rs = $order_item->allowField('paid_amount,sl_id')->save();
                if($rs === false) throw new FailResult('更新订单项目失败');

                //--2.6-- 更新订单数据,订单的相关状态在OrderReceiptBill->delOneBill() 中处理
                $order = $m_order->find($order_item['oid']);
                $order->paid_amount = $order->paid_amount - $bill_item['paid_amount'];
                $order->money_paid_amount = $order->money_paid_amount - $bill_item['money_paid_amount'];
                $order->balance_paid_amount = $order->balance_paid_amount - $bill_item['balance_paid_amount'];
                $order->unpaid_amount = $order->unpaid_amount + $bill_item['paid_amount'];

                $rs = $order->save();
                if($rs === false) throw new FailResult('更新订单失败');
                
            }

            $rs = $bill_item->delete();
            if($rs === false) throw new FailResult('删除收据项目失败');

            //订单项目如果没有收据了，把订单项目也删除
            $bill_item_num = $this->where('oi_id', $bill_item['oi_id'])->count();
            if($bill_item_num == 0) {
                //删除前先处理预充值的返还
                $m_oi = $m_order_item->where('oi_id',$bill_item['oi_id'])->find();
                if($m_oi->gtype == OrderItem::GTYPE_DEBIT){
                    $rs = $m_oi->rollbackDebit();
                    if(!$rs){
                        throw new FailResult($m_oi->getError());
                    }
                }
                $rs = $m_oi->delete();
                if($rs === false) throw new FailResult($m_oi->getError());
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

    //收据项目是否能删除
    public function canDelBillItem($orbi_id, $bill_item = null)
    {
        if(is_null($bill_item)) {
            $bill_item = $this->find($orbi_id);
        }
        if(empty($bill_item)) return $this->user_error('没有收据项目');

        $attendance_item = StudentLessonHour::get(['oi_id' => $bill_item['oi_id']]);
        if(!empty($attendance_item)) return $this->user_error('收据下面的订单有课耗记录，请先处理');

        $transfer_item = OrderTransferItem::get(['oi_id' => $bill_item['oi_id']]);
        if(!empty($transfer_item) && input('force', 0) != 1) {
            return $this->user_error('收据下面的订单有结转，是否处理结转，强制删除订单？', self::CODE_HAVE_RELATED_DATA);
        } else {
            $m_oti = new OrderTransferItem();
            $rs = $m_oti->delOrderTransferItem($bill_item['oi_id']);
            if($rs === false) throw new FailResult($m_oti->getErrorMsg());
        }

        $refund_item = OrderRefundItem::get(['oi_id' => $bill_item['oi_id']]);
        if(!empty($refund_item) && input('force', 0) != 1) {
            return $this->user_error('收据下面的订单有退款，是否处理退款，强制删除订单？', self::CODE_HAVE_RELATED_DATA);
        } else {
            $m_ori = new OrderRefundItem();
            $rs = $m_ori->delOrderRefundItem($bill_item['oi_id']);
            if($rs === false) return $this->user_error($m_ori->getErrorMsg());
        }

        return true;
    }

    //判断是否补缴
    public function isSupplement($oid)
    {
        $bill_items = OrderReceiptBillItem::all(['oid' => $oid]);
        if(count($bill_items) <= 1) {
            $is_supplement = false;
        } else {
            //有多个收据表示是补缴
            $orb_ids = array_unique(array_column($bill_items, 'orb_id'));
            if(count($orb_ids) > 1) {
                $is_supplement = true;
            } else {
                $is_supplement = false;
            }
        }

        return $is_supplement;
    }



    /**
     * 修改缴费金额
     * @param $orbi_id
     * @param $amount
     * 报废收据:
    1. 关联的业绩记录金额需要修改。
    2. 已经产生的课耗单价需要修改等。
    2. 如果有产生现金收入，那么记一笔的tally记录也需要修改
     */
    public function updatAamount($orbi_id,$amount)
    {
        $bill_item = $this->where('orbi_id',$orbi_id)->find();
        if (empty($bill_item)){
            return $this->user_error('收据不存在或已删除！');
        }
        if ($amount <= 0){
            return $this->user_error('金额必须大于零！');
        }

        $order_info = get_order_info($bill_item['oid']);
        if (empty($order_info)){
            return $this->user_error('订单不存在或已删除！');
        }

        $update_amount = $amount - $bill_item['paid_amount'];
        if ($update_amount == 0){
            return $this->user_error('订单金额为改变！');
        }

        $this->startTrans();
        try {
            //  修改订单金额
            $mOrder = new Order();
            $result = $mOrder->updatePaidAmount($bill_item['oid'],$update_amount);
            if (false === $result){
                return $this->user_error($mOrder->getError());
            }

            // 修改课耗单价等
            $mOrderItem = new OrderItem();
            $result = $mOrderItem->updatePaidAmount($bill_item['oi_id'],$update_amount);
            if (false === $result){
                return $this->user_error($mOrderItem->getError());
            }

            //  修改帐户流水金额
            $mOph = new OrderPaymentHistory();
            $result = $mOph->updateAmount($bill_item['orb_id'],$update_amount);
            if (false === $result){
                return $this->user_error($mOph->getError());
            }

            //  修改订单收据表主表金额
            $mOrb = new OrderReceiptBill();
            $result = $mOrb->updatePaidAmount($bill_item['orb_id'],$update_amount);
            if (false === $result){
                return $this->user_error($mOrb->getError());
            }

            //  修改订单销售业绩
            $mOrderPerformance = new OrderPerformance();
            $result = $mOrderPerformance->updatePerformanceAmount($bill_item['oid'],$update_amount);
            if (false === $result){
                return $this->user_error($mOrderPerformance->getError());
            }

            //  修改员工回款记录
            $mEmployeeReceipt = new EmployeeReceipt();
            $result = $mEmployeeReceipt->updateReceiptAmount($bill_item['oid'],$update_amount);
            if (false === $result){
                return $this->user_error($mEmployeeReceipt->getError());
            }

            //  修改订单收据表条目金额
            $update_orb['paid_amount'] = $amount;
            $w_orb['orbi_id'] = $orbi_id;
            $result = $this->save($update_orb,$w_orb);
            if (false === $result){
                return $this->sql_save_error('order_receipt_item_bill');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

}