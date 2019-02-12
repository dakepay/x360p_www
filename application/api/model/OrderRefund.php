<?php
/**
 * Author: luo
 * Time: 2017-10-18 19:27
 **/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;
use app\api\model\OrderRefundItem;
use app\api\model\OrderRefundHistory;

class OrderRefund extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,sno');
    }

    public function orderCutAmount()
    {
        return $this->hasMany('OrderCutAmount', 'or_id', 'or_id');
    }

    public function orderRefundHistory()
    {
        return $this->hasMany('OrderRefundHistory', 'or_id', 'or_id');
    }

    public function orderRefundItem()
    {
        return $this->hasMany('OrderRefundItem', 'or_id', 'or_id');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'uid', 'create_uid')->field('eid,uid,ename');
    }

    public function employeeReceipts()
    {
        return $this->hasMany('EmployeeReceipt','or_id','or_id');
    }

    /**
     * 退款处理
     * @param $student_data
     * @param $items
     * @param $accounts
     * @param $cut_items
     * @param int $refund_balance_amount
     * @param array $salesman
     * @param string $remark
     * @param int $refund_int_day
     * @return bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 1. 增加项目课耗、减少剩余课次
     * 2. 扣减订单未付款，更新订单退款状态
     * 3. 总退款记录
     * 4. 各项目退款记录
     * 5. 退款历史
     */
    public function refund($student_data, $items, $accounts, $cut_items, $refund_balance_amount = 0, $salesman = [], $remark = '',$refund_int_day = 0)
    {
        $mStudent = new Student();
        $student = $mStudent->where('sid', $student_data['sid'])->findOrFail();
        if ($student->money < $refund_balance_amount) return $this->user_error('电子钱包余额不够退款');
        if($refund_int_day == 0){
            $refund_int_day = int_day(time());
        }
        $refund_time = int_day_to_timestamp($refund_int_day);

        $this->startTrans();
        try {

            $need_update_student_lesson_hour = false;
            $order_update_info = [];        //订单更新数据
            $refund_cash_amount = 0;        //扣减未付款外，实际的退款金额
            $total_refund_amount = 0;       // 退款总额
            $total_cut_amount = array_sum(array_column($cut_items, 'amount'));    //扣款总额

            //--1-- 订单项目
            $mOrderItem = new OrderItem();
            $oids = [];
            $oid  = 0;
            if (!empty($items)) {
                foreach ($items as $key => $per_item) {
                    $oids[] = $per_item['oid'];
                    if ($per_item['refund_num'] <= 0) {
                        unset($items[$key]);
                        continue;
                    }
                    $order_item_info = $mOrderItem->where('oi_id', $per_item['oi_id'])->findOrFail();
                    if ($order_item_info['paid_amount'] < 0) {
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
                    $tmp_unpaid_amount = $tmp_unpaid_amount > 0 ? $tmp_unpaid_amount : 0;
                    $tmp_refund_num = $per_item['refund_num'];
                    $tmp_refund_price = $per_item['refund_price'];
                    $cacu_tmp_refund_amount = $tmp_refund_price * $tmp_refund_num;
                    $tmp_refund_amount = $per_item['amount'];

                    $format_cacu_tmp_refund_amount = format_currency($cacu_tmp_refund_amount);
                    $format_tmp_refund_amount = format_currency($tmp_refund_amount);

                    if ($format_cacu_tmp_refund_amount != $format_tmp_refund_amount) {
                        $items[$key]['refund_price'] = format_currency($per_item['amount'] / $per_item['refund_num']);
                    }
                    # 需要扣减未付款部分 #
                    $tmp_reduce_unpaid_amount = $tmp_refund_amount >= $tmp_unpaid_amount ? $tmp_unpaid_amount : $tmp_refund_amount;
                    # 实际退款金额 #
                    $tmp_refund_cash_amount = $tmp_refund_amount >= $tmp_unpaid_amount ? $tmp_refund_amount - $tmp_unpaid_amount : 0;

                    $total_refund_amount += $tmp_refund_amount;
                    $refund_cash_amount += $tmp_refund_cash_amount;

                    //--1.2-- 总订单扣减未付款金额
                    if (isset($order_update_info[$tmp_oid]['reduce_unpaid_amount'])) {
                        $order_update_info[$tmp_oid]['reduce_unpaid_amount'] += $tmp_reduce_unpaid_amount;
                    } else {
                        $order_update_info[$tmp_oid]['reduce_unpaid_amount'] = $tmp_reduce_unpaid_amount;
                        $order_update_info[$tmp_oid]['oid'] = $tmp_oid;
                    }

                    //--1.3-- 计算退款课时数据
                    if ($order_item_info['sl_id'] > 0) {
                        $mStudentLesson = new StudentLesson();
                        $student_lesson = $mStudentLesson->find($order_item_info['sl_id']);
                        if (empty($student_lesson)) throw new FailResult('student_lesson不存在');
                        $lesson = Lesson::get(['lid' => $student_lesson->lid]);
                        //$lesson_dec_data = $student_lesson_model->calcLessonTimesAndHour($lesson, [], $tmp_refund_num);
                        $lesson_dec_data = [];
                        $lesson_dec_data['lesson_amount'] = $tmp_refund_amount;
                        $mStudentLesson->calcLessonTimesAndHour($lesson, $lesson_dec_data, $tmp_refund_num, $order_item_info['nums_unit'], $order_item_info['cid']);
                        if (isset($per_item['refund_present_nums'])) {
                            $lesson_dec_data['remain_lesson_hours'] += floatval($per_item['refund_present_nums']);
                            $lesson_dec_data['lesson_hours'] += floatval($per_item['refund_present_nums']);
                        }
                        //--1.4-- 减少课时
                        $rs = $mStudentLesson->handleStudentLessonHours($student_lesson, $lesson_dec_data, StudentLesson::HANDLE_STUDENT_LESSON_HOURS_REFUND);
                        if (!$rs) throw new FailResult($mStudentLesson->getErrorMsg());

                        //记录退款课时
                        if ($lesson_dec_data['lesson_hours'] > 0) {
                            $operate_data = [
                                'oid' => $order_item_info['oid'],
                                'oi_id' => $order_item_info['oi_id'],
                                'unit_price' => $per_item['refund_price'],
                                'lesson_amount' => $tmp_refund_amount,
                                'sl_id' => $student_lesson['sl_id'],
                                'lesson_hours' => $lesson_dec_data['lesson_hours'],
                                'op_type' => StudentLessonOperate::OP_TYPE_REFUND
                            ];
                            $m_slo = new StudentLessonOperate();
                            $rs = $m_slo->addOperation($operate_data);
                            if ($rs === false) throw new FailResult($m_slo->getErrorMsg());
                        }

                        //--1.5-- 如果课时为0，则退出班级
                        $rs = $mStudentLesson->exitClassWhenBuyLessonEqualZero($student_lesson->sl_id, $student_lesson);
                        if (!$rs) throw new FailResult($mStudentLesson->getErrorMsg());

                        $need_update_student_lesson_hour = true;
                    }

                    //--1.4-- 物品入库
                    if ($order_item_info['gid'] > 0) {
                        $rs = $this->refundMaterial($order_item_info, $tmp_refund_num,$refund_int_day);
                        if ($rs === false) throw new FailResult($this->getErrorMsg());
                    }
                }

                //--2-- 订单退款减少未付款
                foreach ($order_update_info as $per_order) {
                    $tmp_oid = $per_order['oid'];
                    $tmp_reduce_unpaid_amount = $per_order['reduce_unpaid_amount'];

                    $order = Order::get(['oid' => $tmp_oid]);
                    $rs = $order->updateRefundStatus($tmp_oid, Order::ORDER_STATUS_REFUNDED, Order::REFUND_STATUS_DONE);
                    if ($rs === false) throw new FailResult('退款状态失败');

                    if ($tmp_reduce_unpaid_amount <= 0) continue;
                    $order->setDec('unpaid_amount', $tmp_reduce_unpaid_amount);
                }
            }

            if(!empty($oids)) {
                $oids = array_unique($oids);
                if (count($oids) > 1) {
                    $this->rollback();
                    return $this->user_error('退费订单需要一个一个退');
                }
                $oid = $oids[0];
            }

            //--3-- 添加总退款记录
            $refund_amount = $refund_cash_amount + $refund_balance_amount - $total_cut_amount;
            $refund_data = [
                'bid' => $student->bid,
                'oid' => $oid,
                'sid' => $student->sid,
                'need_refund_amount' => $total_refund_amount,
                'refund_balance_amount' => $refund_balance_amount,
                'cut_amount' => $total_cut_amount,
                'refund_amount' => $refund_amount,
                'refund_int_day'    => $refund_int_day,
                'remark' => $remark,
            ];
            $or_id = $this->createOneRefund($refund_data);
            if (!$or_id){
                $this->rollback();
                return false;
            }

            //--4-- 添加各个项目退款记录

            if (!empty($items)) {
                $mOrderRefundItem = new OrderRefundItem();
                foreach ($items as $item) {
                    $m_oi = $mOrderItem->where('oi_id', $item['oi_id'])->findOrFail();
                    $refund_present_nums = isset($item['refund_present_nums']) ? floatval($item['refund_present_nums']) : 0.00;
                    $data = [
                        'or_id' => $or_id,
                        'oi_id' => $m_oi->oi_id,
                        'nums' => $item['refund_num'],
                        'present_nums' => $refund_present_nums,
                        'unit_price' => $item['refund_price'],
                        'amount' => $item['amount'],
                    ];
                    $result = $mOrderRefundItem->createRefundItem($data);
                    if(!$result){
                        $this->rollback();
                        return $this->sql_save_error('order_refund_item');
                    }
                    if ($refund_present_nums > 0) {
                        $m_oi['deduct_present_lesson_hours'] = $m_oi['deduct_present_lesson_hours'] + $refund_present_nums;
                        $m_oi->save();
                    }
                }
            }

            //--4.1-- 业绩归属人退款记录
            if (!empty($salesman) && is_array($salesman)) {
                $mEmployeeReceipt = new EmployeeReceipt();
                foreach ($salesman as $man) {
                    if (!isset($man['eid'])) throw new FailResult('业绩相关员工id错误');
                    $receipt_data = [
                        'eid' => $man['eid'],
                        'sid' => $student->sid,
                        'sale_role_did' => $man['sale_role_did'],
                        'or_id' => $or_id,
                        'amount' => -($refund_cash_amount + $refund_balance_amount),
                        'receipt_time'  => $refund_time
                    ];
                    $rs = $mEmployeeReceipt->createOneReceipt($receipt_data);
                    if(false === $rs){
                        $this->rollback();
                        return $this->user_error($mEmployeeReceipt->getError());
                    }
                }
            }

            //--5-- 减少电子钱包余额
            if ($refund_balance_amount > 0) {
                $money_change_data = [
                    'business_type' => StudentMoneyHistory::BUSINESS_TYPE_REFUND,
                    'business_id' => $or_id,
                    'money' => -$refund_balance_amount,
                    'remark' => '订单退款:' . $remark,
                ];
                $rs = $mStudent->changeMoney($student, $money_change_data);
                if (!$rs) throw new FailResult($mStudent->getErrorMsg());
            }

            //--6-- 记录退款历史
            $mOrh = new OrderRefundHistory();
            foreach ($accounts as $account) {
                if ($account['amount'] > 0) {
                    $refund_history_data = [
                        'bid' => $student->bid,
                        'or_id' => $or_id,
                        'oid' => $oid,
                        'sid' => $student['sid'],
                        'aa_id' => $account['aa_id'],
                        'amount' => $account['amount'],
                        'pay_time' => $refund_time
                    ];
                    $rs = $mOrh->createRefundHistory($refund_history_data);
                    if (!$rs) throw new FailResult($mOrh->getError());
                }
            }

            //--7-- 扣款
            foreach ($cut_items as $per_cut) {
                $cut_data = [
                    'type' => OrderCutAmount::CUT_TYPE_TRANSFER,
                    'or_id' => $or_id,
                    'sid' => $student->sid,
                    'cut_int_day' => $refund_int_day
                ];
                $cut_data = array_merge($per_cut, $cut_data);
                $rs = (new OrderCutAmount())->createOneCut($cut_data);
                if (!$rs) throw new FailResult('增加扣款记录失败');

                if($rs['amount'] > 0) {     //大于0才记录课时产出业绩
                    $refund_data = [
                        'lesson_amount' => $rs['amount'],
                        'change_type' => StudentLessonHour::CHANGE_TYPE_REFUND,
                        'consume_type' => 3,
                        'int_day' => $refund_int_day,
                    ];
                    array_copy($refund_data, $rs, ['sid', 'og_id', 'bid']);

                    $res = (new StudentLessonHour)->createOneRefund($refund_data);
                    if (!$res) throw new FailResult('添加扣款转化失败');

                    $oca_info = OrderCutAmount::get($rs['oca_id']);
                    $oca_info['slh_id'] = $res->slh_id;
                    $result = $oca_info->save();
                    if (false === $result) {
                        $this->rollback();
                        return $this->sql_save_error('order_cut_amount');
                    }
                }

            }

            //--9-- 更新学员的课时
            if ($need_update_student_lesson_hour) {
                $student->updateLessonHours($student->sid);
            }

            // 添加一条 退费操作日志
            StudentLog::addStuentRefundLog($student,$total_refund_amount,$refund_time);


        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return $or_id;
    }

    /**
     * 撤销退款
     * 1. 处理业务数据（课时，物品，储值，杂费）
     * 2. 处理财务数据()
     * 3. 处理退款记录
     */

    public function undoRefund($or_id)
    {
        $w_or['or_id'] = $or_id;
        $or_m = $this->where($w_or)->find();
        if (!$or_m) {
            return $this->user_error('退费记录不存在');
        }

        //学生信息
        $mStudent = new Student();
        $student = $mStudent->where('sid', $or_m['sid'])->findOrFail();


        //订单退费记录信息
        $mOri = new OrderRefundItem();
        $ori_list = $mOri->where($w_or)->select();
        //订单付款记录信息
        $mOrh = new OrderRefundHistory();
        $orh = $mOrh->where($w_or)->find();

        $mStudentLesson = new StudentLesson();
        $refund_balance_amount = $or_m['refund_balance_amount'];    //  退电子钱包金额

        $mOrder = new Order();

        $this->startTrans();
        try {
            if (!empty($ori_list)) {
                foreach ($ori_list as $ori_m) {
                    $nums = $ori_m['nums'];   //  退费课时数量
                    // 订单项目
                    $mOi = new OrderItem();
                    $order_item_info = $mOi->where('oi_id', $ori_m['oi_id'])->find();
                    if($order_item_info) {
                        //  学员课时撤回
                        if ($order_item_info['gtype'] == 0) {
                            $student_lesson = $mStudentLesson->where('sl_id',$order_item_info['sl_id'])->find();
                            if (empty($student_lesson)){
                                return $this->user_error('student_lesson记录不存在');
                            }
                            $result = $mStudentLesson->backLesson($student_lesson, $nums);
                            if (false === $result){
                                return $this->user_error($mStudentLesson->getError());
                            }

                            $result = $mStudent->backLesson($student, $nums);
                            if (false === $result){
                                return $this->user_error($mStudent->getError());
                            }
                        }

                        //  物品撤销退费
                        if ($order_item_info['gtype'] == 1) {
                            $result = $this->undoMaterial($order_item_info, $nums);
                            if (false === $result){
                                return $this->user_error($this->getError());
                            }
                        }

                        // 订单状态修改
                        $result = $mOrder->updateRefundStatus($order_item_info['oid'], Order::ORDER_STATUS_PAID, Order::REFUND_STATUS_NO);
                        if (false === $result){
                            return $this->user_error($mOrder->getError());
                        }
                    }
                }
            }


            //  学员财务数据撤回
            if ($refund_balance_amount > 0) {
                $money_change_data = [
                    'business_type' => StudentMoneyHistory::BUSINESS_TYPE_ADD,
                    'business_id' => $or_id,
                    'money' => $refund_balance_amount,
                    'remark' => '用户订单退款撤回',
                ];
                $result = $mStudent->changeMoney($student, $money_change_data);
                if (false === $result){
                    return $this->user_error($mStudent->getError());
                }
            }

            // 帐户退款
            $w_tly = [];
            $w_tly['relate_id'] = $orh['orh_id'];
            $w_tly['type'] = Tally::TALLY_TYPE_PAYOUT;
            $m_tally = new Tally();
            $tally = $m_tally->where($w_tly)->find();
            if ($tally) {
                $result = $tally->delBusinessTally();
                if (false === $result) {
                    return $this->user_error($tally->getError());
                }
            }
            $result = $m_tally->where($w_tly)->delete();
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('tally');
            }
            $w_or['or_id'] = $or_id;
            //删除扣款记录
            $mOca = new OrderCutAmount();
            $oca = $mOca->where($w_or)->find();

            if($oca){
                $mSlh = new StudentLessonHour();
                $w_slh['slh_id'] = $oca['slh_id'];

                $result = $mSlh->where($w_slh)->delete();
                if(false === $result){
                    $this->rollback();
                    return $this->sql_delete_error('student_lesson_hour');
                }

                $result = $oca->delete();

                if(false === $result){
                    $this->rollback();
                    return $this->sql_delete_error('order_cut_amount');
                }

            }
            //删除退款相关业绩
            $mEmployeeReceipt = new EmployeeReceipt();
            $result = $mEmployeeReceipt->where($w_or)->delete();
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('employee_receipt');
            }

            $result = $mOrh->where($w_or)->delete();
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('order_refund_history');
            }

            $result = $mOri->where($w_or)->delete();
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('order_refund_item');
            }

            $result = $or_m->delete();
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('order_refund');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    //  物品入库
    public function refundMaterial($order_item_info, $refund_num,$refund_int_day = 0)
    {
        if($refund_int_day == 0){
            $refund_int_day = int_day(time());
        }
        if (!isset($order_item_info['gid']) || !isset($order_item_info['bid'])) {
            throw new FailResult('物品参数错误');
        }
        $mh_data = [
            'mt_id' => $order_item_info['gid'],
            'ms_id' => Branch::getMsId($order_item_info['bid']),
            'int_day' => $refund_int_day,
            'num' => $refund_num,
            'type' => MaterialHistory::TYPE_IN,
            'remark' => '订单退回撤回' . Order::getOrderNo(isset($order_item_info['oid']) ? $order_item_info['oid'] : 0)
        ];
        $m_mh = new MaterialHistory();
        $rs = $m_mh->addOneHis($mh_data);
        if ($rs === false) throw new FailResult($m_mh->getErrorMsg());

        return true;
    }

    //  物品出库
    public function undoMaterial($order_item_info, $refund_num)
    {
        if (!isset($order_item_info['gid']) || !isset($order_item_info['bid'])) {
            throw new FailResult('物品退费参数错误');
        }

        $mh_data = [
            'mt_id' => $order_item_info['gid'],
            'ms_id' => Branch::getMsId($order_item_info['bid']),
            'int_day' => date('Ymd', time()),
            'num' => $refund_num,
            'type' => MaterialHistory::TYPE_OUT,
            'remark' => '订单退回撤销' . Order::getOrderNo(isset($order_item_info['oid']) ? $order_item_info['oid'] : 0)
        ];
        $m_mh = new MaterialHistory();
        $rs = $m_mh->addOneHis($mh_data);
        if ($rs === false) throw new FailResult($m_mh->getErrorMsg());

        return true;
    }


    public function createOneRefund($data)
    {
        $data['bill_no'] = $this->makeBillNo();
        $result = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if(!$result){
            return $this->sql_add_error('order_refund');
        }
        $or_id = $this->getAttr('or_id');
        return $or_id;
    }

    public function makeBillNo()
    {
        $str = strtoupper(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3));
        return $str . date('YmdHis');
    }

    public function makePrintData($number)
    {
        $refund = $this->where('or_id|bill_no', $number)->findOrFail();

        $sys_data = $this->getSysData($refund);
        $bs_data = $this->getBsData($refund);
        $bm_data = $this->getBmData($refund);

        $item = [];

        if(is_array($bm_data) && isset($bm_data[0])){
            $item = $bm_data[0];
        }

        $diy = get_print_vars($refund['bid']);

        $print_data = [
            'diy' => $diy,
            'sys' => $sys_data,
            'bs' => $bs_data,
            'bm' => $bm_data,
            'item'  => $item
        ];
        return $print_data;
    }

    //票据打印数据
    protected function getSysData(OrderRefund $refund)
    {
        $org_name = '';
        $branch = Branch::get(['bid' => $refund->bid]);
        return ['org_name' => $org_name, 'branch_name' => $branch->branch_name];
    }

    //票据打印数据
    protected function getBsData(OrderRefund $refund)
    {
        $bs_data = [];

        $student = Student::get(['sid' => $refund->sid]);
        $bs_data['sid'] = $refund->sid;
        $bs_data['student_name'] = $student->student_name;
        $bs_data['card_no'] = $student->card_no;
        $bs_data['sno'] = $student->sno;
        $bs_data['first_tel'] = $student->first_tel;

        $bs_data['pay_date'] = int_day_to_date_str($refund->refund_int_day);
        $bs_data['receipt_no'] = $refund->bill_no;
        $user = (new User())->where('uid', $refund->create_uid)->field('name,uid')->find();
        $bs_data['op_name'] = $user['name'];
        $bs_data['need_refund_amount'] = $refund->need_refund_amount;
        $bs_data['cut_amount'] = $refund->cut_amount;
        $bs_data['refund_balance_amount'] = $refund->refund_balance_amount;
        $bs_data['refund_amount'] = $refund->refund_amount;
        $bs_data['refund_amount_b'] = number2chinese($bs_data['refund_amount'], true);
        $bs_data['refund_remark'] = $refund->remark;

        try {
            $bs_data['qrcode'] = $student->getWechatQrcode();
        } catch (\Exception $e) {
            $bs_data['qrcode'] = '';
        }

        return $bs_data;
    }

    //票据打印数据
    protected function getBmData(OrderRefund $refund)
    {
        $bm_data = [];

        $items = OrderRefundItem::all(['or_id' => $refund->or_id]);
        foreach ($items as $per_item) {
            $item = OrderItem::get(['oi_id' => $per_item['oi_id']]);
            $tmp = $item->toArray();

            if ($item->gtype == OrderItem::GTYPE_LESSON) {
                $lid = $item->student_lesson->lid;
                $lesson = (new Lesson())->where('lid', $lid)->field('lesson_name')->find();
                $tmp['lesson_name'] = $lesson['lesson_name'];

                $cid = $item->student_lesson->cid;
                $class = (new Classes())->where('cid', $cid)->field('class_name')->find();
                $tmp['class_name'] = $class['class_name'];
            } elseif ($item->gtype == OrderItem::GTYPE_GOODS) {
                $material = $item->material;
                $tmp['lesson_name'] = $material['name'];

            } elseif ($item->gtype == OrderItem::GTYPE_PAYITEM) {
                $tmp['lesson_name'] = $item->item_name;
            }

            $tmp['nums'] = $item['nums'];
            $tmp['refund_nums'] = $per_item['nums'];   //退款数量
            $tmp['refund_unit_price'] = $per_item['unit_price'];
            $tmp['refund_amount'] = $per_item['amount'];

            array_push($bm_data, $tmp);
        }

        return $bm_data;
    }

    public static function getBillNoByOrId($or_id)
    {
        $refund = (new self())->field('bill_no')->find($or_id);
        if (empty($refund)) return '';

        return $refund['bill_no'];
    }

    //删除退款
    public function delOrderRefund($or_id)
    {
        $order_refund = $this->find($or_id);
        if (empty($order_refund)) return true;

        try {
            $this->startTrans();

            //增加电子钱包余额
            if ($order_refund['refund_balance_amount'] > 0) {
                $student = Student::get($order_refund['sid']);
                $money_change_data = [
                    'business_type' => StudentMoneyHistory::BUSINESS_TYPE_REFUND,
                    'business_id' => $or_id,
                    'money' => $order_refund['refund_balance_amount'],
                    'remark' => '取消退款',
                ];
                $rs = $student->changeMoney($student, $money_change_data);
                if (!$rs) throw new FailResult($student->getErrorMsg());
            }

            //删除退款相关业绩
            (new EmployeeReceipt())->where('or_id', $or_id)->delete();
            (new OrderCutAmount())->where('or_id', $or_id)->delete();

            $m_order_refund_history = new OrderRefundHistory();
            $refund_history_list = $m_order_refund_history->where('or_id', $or_id)->select();
            foreach ($refund_history_list as $row) {
                $tally_data = [
                    'bid' => $row['bid'],
                    'or_id' => $or_id,
                    'sid' => $order_refund['sid'],
                    'aa_id' => $row['aa_id'],
                    'amount' => $row['amount'],
                    'type' => Tally::TALLY_TYPE_INCOME,
                    'relate_id' => $row['orh_id'],
                    'remark' => '取消退款'
                ];
                $rs = (new Tally())->createOneTally($tally_data);
                if (!$rs) return $this->user_error('取消退款流水记录失败');
            }

            $rs = $order_refund->delete();
            if ($rs === false) throw new FailResult($order_refund->getError());

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

}