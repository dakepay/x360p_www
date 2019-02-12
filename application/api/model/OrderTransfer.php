<?php
/**
 * Author: luo
 * Time: 2017-10-18 17:36
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class OrderTransfer extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function orderTransferItem()
    {
        return $this->hasMany('OrderTransferItem', 'ot_id', 'ot_id');
    }

    public function orderCutAmount()
    {
        return $this->hasMany('OrderCutAmount', 'ot_id', 'ot_id');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'uid', 'create_uid')->field('eid,uid,ename');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid');
    }

    /*
     * 1. 计算各项结转次数、金额
     * 2. 增加各项课耗、减少剩余课次数
     * 3. 计算总的结转次数、总额
     * 4. 减少未付款、增加电子钱包, 减少未付款指的是订单下面的item未付款，不是针对整个订单
     * 4. 记录结转
     */
    public function transfer($student_data, $items, $cut_items = []) {

        $student_model = new Student();
        $student = $student_model->where('sid', $student_data['sid'])->findOrFail();

        $this->startTrans();
        try {
            $order_update_info = [];        //订单更新数据
            $balance_update_amount = 0;     //电子钱包更新金额
            $total_transfer_amount = 0;     // 结转总额
            $total_cut_amount = array_sum(array_column($cut_items, 'amount'));    //扣款总额

            //--1-- 订单项目
            $order_item_model = new OrderItem();
            foreach ($items as $key => $per_item) {

                if($per_item['transfer_num'] <= 0) {
                    unset($items[$key]);
                    continue;
                }
                $order_item_info = $order_item_model->where('oi_id', $per_item['oi_id'])->findOrFail();
                if($order_item_info['paid_amount'] <= 0 || $per_item['transfer_num'] > $order_item_info['nums']) {
                    unset($items[$key]);
                    continue;
                }

                $order_transfer_amount = OrderTransferItem::getTransferAmountByOiId($per_item['oi_id']);
                $order_refund_amount = OrderRefundItem::getRefundAmountByOiId($per_item['oi_id']);

                //--1.1-- 循环体的临时数据
                $tmp_oid = $order_item_info['oid'];
                # 项目未付款 = 小计 - 已付 - 结转 - 退款 #
                $tmp_unpaid_amount = $order_item_info['subtotal'] - $order_item_info['paid_amount']
                                    - $order_transfer_amount - $order_refund_amount;
                $tmp_unpaid_amount = $tmp_unpaid_amount >= 0 ? $tmp_unpaid_amount : 0;
                $tmp_transfer_num = $per_item['transfer_num'];
                $tmp_transfer_price = $per_item['transfer_price'];
                $cacu_tmp_transfer_amount = $tmp_transfer_price * $tmp_transfer_num;
                $tmp_transfer_amount = $per_item['amount'];

                $format_cacu_tmp_transfer_amount = format_currency($cacu_tmp_transfer_amount);
                $format_tmp_transfer_amount = format_currency($tmp_transfer_amount);

                if($format_cacu_tmp_transfer_amount != $format_tmp_transfer_amount){
                    $items[$key]['transfer_price'] = format_currency($per_item['amount'] / $per_item['transfer_num']);
                }
                # 需要扣减未付款部分 #
                $tmp_reduce_unpaid_amount = $tmp_transfer_amount >= $tmp_unpaid_amount ? $tmp_unpaid_amount : $tmp_transfer_amount;
                # 需要增加电子账户部分 #
                $tmp_add_balance_amount = $tmp_transfer_amount >= $tmp_unpaid_amount ? $tmp_transfer_amount - $tmp_unpaid_amount : 0;

                $total_transfer_amount += $tmp_transfer_amount;
                $balance_update_amount += $tmp_add_balance_amount;

                //--1.2-- 总订单结转金额
                if(isset($order_update_info[$tmp_oid]['reduce_unpaid_amount'])) {
                    $order_update_info[$tmp_oid]['reduce_unpaid_amount'] += $tmp_reduce_unpaid_amount;
                } else {
                    $order_update_info[$tmp_oid]['reduce_unpaid_amount'] = $tmp_reduce_unpaid_amount;
                    $order_update_info[$tmp_oid]['oid'] = $tmp_oid;
                }

                //--1.3-- 计算结转课时数据
                $student_lesson_model = new StudentLesson();
                if($order_item_info['sl_id'] > 0) {
                    $student_lesson = $student_lesson_model->find($order_item_info['sl_id']);
                    $lesson = Lesson::get(['lid' => $student_lesson->lid]);
                    $lesson_dec_data = [];
                    $lesson_dec_data['lesson_amount'] = $tmp_transfer_amount;
                    $lesson_dec_data = $student_lesson_model->calcLessonTimesAndHour($lesson, $lesson_dec_data, $tmp_transfer_num, $order_item_info['nums_unit'], $order_item_info['cid']);

                    if(isset($per_item['transfer_present_nums'])){
                        $lesson_dec_data['remain_lesson_hours'] += floatval($per_item['transfer_present_nums']);
                        $lesson_dec_data['lesson_hours'] += floatval($per_item['transfer_present_nums']);
                    }

                    //--1.4-- 减少课时
                    $rs = $student_lesson_model->handleStudentLessonHours($student_lesson, $lesson_dec_data, StudentLesson::HANDLE_STUDENT_LESSON_HOURS_TRANSFER);
                    if(!$rs) throw new FailResult($student_lesson_model->getErrorMsg());

                    //记录结转课时
                    if($lesson_dec_data['lesson_hours'] > 0) {
                        $operate_data = [
                            'oid'           => $order_item_info['oid'],
                            'oi_id'         => $order_item_info['oi_id'],
                            'unit_price'    => $per_item['transfer_price'],
                            'lesson_amount' => $tmp_transfer_amount,
                            'sl_id'         => $student_lesson['sl_id'],
                            'lesson_hours'  => $lesson_dec_data['lesson_hours'],
                            'op_type'       => StudentLessonOperate::OP_TYPE_TRANSFER
                        ];
                        $m_slo = new StudentLessonOperate();
                        $rs = $m_slo->addOperation($operate_data);
                        if($rs === false) throw new FailResult($m_slo->getErrorMsg());
                    }

                    //--1.5-- 如果课时为0，则退出班级
                    $rs = $student_lesson_model->exitClassWhenBuyLessonEqualZero($student_lesson->sl_id);
                    if(!$rs) throw new FailResult($student_lesson_model->getErrorMsg());

                }

                //--1.4-- 物品入库
                if($order_item_info['gid'] > 0) {
                    $rs = $this->transferMaterial($order_item_info, $tmp_transfer_num);
                    if($rs === false) throw new FailResult($this->getErrorMsg());
                }
            }

            //--2-- 订单结转减少未付款
            foreach($order_update_info as $per_order) {
                $tmp_oid = $per_order['oid'];
                $tmp_reduce_unpaid_amount = $per_order['reduce_unpaid_amount'];

                $order = Order::get(['oid' => $tmp_oid]);
                $order->where('oid', $tmp_oid)->setDec('unpaid_amount', $tmp_reduce_unpaid_amount);

            }

            $balance_update_amount -= $total_cut_amount;
            //--3-- 记录结转
            $data = [
                'bid'             => $student->bid,
                'sid'             => $student->sid,
                'transfer_amount' => $total_transfer_amount,
                'balance_amount' =>  $balance_update_amount,
            ];
            $ot_id = $this->createOneTransfer($data);
            if (!$ot_id) throw new FailResult('添加结转记录失败');

            //--4-- 各个项目结转记录
            $transfer_item_model = new OrderTransferItem();
            foreach ($items as $per_item) {
                if($per_item['transfer_num'] <= 0) continue;
                $order_item_info = $order_item_model->where('oi_id', $per_item['oi_id'])->findOrFail();
                $transfer_price = $per_item['transfer_price'];
                $transfer_present_nums = isset($per_item['transfer_present_nums'])?floatval($per_item['transfer_present_nums']):0.00;
                $data = [
                    'ot_id'      => $ot_id,
                    'oi_id'      => $order_item_info->oi_id,
                    'nums'       => $per_item['transfer_num'],
                    'present_nums'  => $transfer_present_nums,
                    'unit_price' => $transfer_price,
                    'amount'     => $per_item['amount'],
                ];
                $transfer_item_model->createTransferItem($data);

                if($transfer_present_nums > 0){
                    $order_item_info->deduct_present_lesson_hours = $order_item_info->deduct_present_lesson_hours + $transfer_present_nums;
                    $order_item_info->save();
                }
            }

            //--5-- 扣款
            $cut_amount = 0;
            foreach($cut_items as $per_cut) {
                $cut_data = [
                    'type' => OrderCutAmount::CUT_TYPE_TRANSFER,
                    'ot_id' => $ot_id,
                ];
                $cut_data = array_merge($per_cut, $cut_data);
                $rs = (new OrderCutAmount())->createOneCut($cut_data);
                if(!$rs) throw new FailResult('增加扣款记录失败');

                $cut_amount += $per_cut['amount'];
            }

            //--5-- 退款到电子钱包
            if($balance_update_amount > 0) {
                $money_change_data = [
                    'business_type' => StudentMoneyHistory::BUSINESS_TYPE_TRANSFORM,
                    'business_id'   => isset($ot_id) ? $ot_id : 0,
                    'money'         => $balance_update_amount,
                    'remark'        => '订单结转,结转单号',
                ];

                $rs = $student_model->changeMoney($student, $money_change_data);
                if(!$rs) ($student_model->getError());
            }

            $student->updateLessonHours($student->sid);

            // 添加一条 学员结转 操作日志
            StudentLog::addStudentTransferLog($student,$total_transfer_amount,$cut_amount,StudentLog::OP_TRANSFER);

        
        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();

        return true;
    }

    public function transferMaterial($order_item_info, $transfer_num)
    {
        if(!isset($order_item_info['gid']) || !isset($order_item_info['bid'])) {
            throw new FailResult('物品结转参数不对');
        }

        $mh_data = [
            'mt_id' => $order_item_info['gid'],
            'ms_id' => Branch::getMsId($order_item_info['bid']),
            'num' => $transfer_num,
            'int_day' => date('Ymd', time()),
            'type' => MaterialHistory::TYPE_IN,
            'remark' => '订单结转'.Order::getOrderNo(isset($order_item_info['oid']) ? $order_item_info['oid'] : 0),
        ];
        $m_mh = new MaterialHistory();
        $rs = $m_mh->addOneHis($mh_data);
        if($rs === false) throw new FailResult($m_mh->getErrorMsg());

        return true;
    }

    public function createOneTransfer($data)
    {
        $data['bill_no'] = $this->makeBillNo();
        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return $this->user_error('添加结转记录失败');

        $ot_id = $this->getLastInsID();

        return $ot_id;
    }

    public function makeBillNo()
    {
        $str = strtoupper(substr(str_shuffle('ABCDEFGHIJKLNMOPQRSTUVWXYZ'),0, 3));
        return $str.date('YmdHis');
    }

    public static function getBillNoByOtId($ot_id)
    {
        $info = (new self())->field('bill_no')->find($ot_id);
        return isset($info['bill_no']) ? $info['bill_no'] : '';
    }

    //删除结转，主要用于删除订单时候
    public function delOrderTransfer($ot_id)
    {
        $order_transfer = $this->find($ot_id);
        if(empty($order_transfer)) return true;

        try {
            $this->startTrans();

            //增加电子钱包余额
            if($order_transfer['balance_amount'] > 0) {
                $student = Student::get($order_transfer['sid']);
                $money_change_data = [
                    'business_type' => StudentMoneyHistory::BUSINESS_TYPE_TRANSFORM,
                    'business_id'   => $ot_id,
                    'money'         => -$order_transfer['balance_amount'],
                    'remark'        => '取消结转',
                ];
                $rs = $student->changeMoney($student, $money_change_data);
                if(!$rs) throw new FailResult($student->getErrorMsg());
            }

            //删除结转扣款
            (new OrderCutAmount())->where('ot_id', $ot_id)->delete();

            $order_transfer->delete();

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }


    /**
     * 撤销订单结转
     * @param $ot_id
     * @return bool
     */
    public function undoOrderTransfer($ot_id)
    {
        $order_transfer = $this->find($ot_id);
        if (empty($order_transfer)){
            return $this->user_error('转接记录不存在');
        }

        $mOti = new OrderTransferItem();
        $mOca = new OrderCutAmount();
        $mStudent = new Student();
        $this->startTrans();
        try {
            $result = $mOti->undoOrderTransferItem($ot_id);
            if (false === $result) {
                return $this->user_error($mOti->getError());
            }

            $cut_amount = $mOca->undoCutAmount($ot_id);
            if (false === $result) {
                return $this->user_error($mOca->getError());
            }

            $student = $mStudent->get($order_transfer['sid']);
            if($order_transfer['balance_amount'] > 0) {
                $money_change_data = [
                    'business_type' => StudentMoneyHistory::BUSINESS_TYPE_UNTRANSFORM,
                    'business_id'   => isset($ot_id) ? $ot_id : 0,
                    'money'         => -$order_transfer['balance_amount'],
                    'remark'        => '订单撤销结转,结转单号:'.$order_transfer['bill_no'],
                ];
                $result = $mStudent->changeMoney($student, $money_change_data);
                if(false === $result) ($mStudent->getError());
            }

            $student->updateLessonHours($student->sid);
            $order_transfer->delete();
            // 添加一条 学员结转 操作日志
            StudentLog::addStudentTransferLog($student,$order_transfer['transfer_amount'],$cut_amount,StudentLog::OP_UNTRANSFER);
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;


    }

}