<?php
/** 
 * Author: luo
 * Time: 2017-10-14 10:28
**/

namespace app\api\model;

use app\common\exception\FailResult;
use app\common\Wechat;
use think\Exception;
use think\Hook;

class Order extends Base
{
    const AC_STATUS_NO = 0; //未分班
    const AC_STATUS_SOME = 1; //部分分班
    const AC_STATUS_ALL = 2; //完成分班

    const PAY_STATUS_NO = 0;    //未付款
    const PAY_STATUS_SOME = 1;  //部分付款
    const PAY_STATUS_ALL = 2;   //全部付款

    const ORDER_STATUS_PLACE_ORDER = 0;
    const ORDER_STATUS_PAID = 1;
    const ORDER_STATUS_ASSIGN_CLASS = 2;
    const ORDER_STATUS_APPLIED_REFUND = 10;
    const ORDER_STATUS_REFUNDED = 11;

    const REFUND_STATUS_NO = 0;
    const REFUND_STATUS_ING = 1;
    const REFUND_STATUS_DONE = 2;

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];
    protected $insert = ['order_amount', 'money_pay_amount', 'money_paid_amount', 'unpaid_amount', 'pay_status'];

    protected function setOrderAmountAttr($value, $data)
    {
        $data['order_reduced_amount'] = isset($data['order_reduced_amount']) ? (float)$data['order_reduced_amount'] : 0;
        $data['order_discount_amount'] = isset($data['order_discount_amount']) ? (float)$data['order_discount_amount'] : 0;
        $value = isset($data['origin_amount']) ? (float)$data['origin_amount'] - $data['order_discount_amount'] - $data['order_reduced_amount'] : 0;
        return $value;
    }

    protected function setMoneyPayAmountAttr($value, $data)
    {
        if(isset($data['order_amount']) && isset($data['balance_paid_amount'])) {
            return $data['order_amount'] - $data['balance_paid_amount'];
        }
        return $value ? $value : 0;
    }

    protected function setMoneyPaidAmountAttr($value, $data)
    {
        if(isset($data['paid_amount'])) {
            if($data['paid_amount'] == 0) return 0;
            $data['balance_paid_amount'] = isset($data['balance_paid_amount']) ? $data['balance_paid_amount'] : 0;
            $data['money_paid_amount'] = $data['paid_amount'] - $data['balance_paid_amount'];
            return $data['money_paid_amount'];
        }

        return $value ? $value : 0;
    }

    protected function setUnpaidAmountAttr($value, $data)
    {
        if(isset($data['order_amount']) && isset($data['paid_amount'])) {
            return $data['order_amount'] - $data['paid_amount'] <= 0 ? 0 : $data['order_amount'] - $data['paid_amount'];
        }
        return $value ? $value : 0;
    }

    protected function setPaidTimeAttr($value)
    {
        if(is_numeric($value)){
            return $value;
        }
        return $value ? strtotime($value) : $value;
    }

    protected function getPaidTimeAttr($value)
    {
        return $value ? date('Y-m-d', $value) : 0;
    }

    public function getGoods()
    {
        $data = [];
        $order_items = $this->getAttr('orderItems');
        foreach ($order_items as $item) {
            array_push($data, $item['goods']);
        }
        return $data;
    }

    public function orderItems()
    {
        return $this->hasMany('OrderItem', 'oid', 'oid');
    }


    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')
            ->field('bid,sid,student_name,money,sex,photo_url,birth_time,birth_year');
    }

    public function employee()
    {
        return $this->hasMany('OrderPerformance', 'eid', 'oid');
    }

    public function orderPerformance()
    {
        return $this->hasMany('OrderPerformance', 'oid', 'oid');
    }

    public function studentDebitCard()
    {
        return $this->hasOne('StudentDebitCard', 'sdc_id', 'sdc_id');
    }


    //生成订单编号
    protected function makeOrderNo()
    {
        do {
            $order_no = makeOrderNo();
        } while ($this->where('order_no', $order_no)->count() === 1);
        return $order_no;
    }

    //更新冗余字段
    public static function updateRedundantField($oid, $order = null)
    {
        if(is_null($order)) {
            $order = Order::get(['oid' => $oid]);
        }
        $order->order_amount = $order->origin_amount - $order->order_discount_amount - $order->order_reduced_amount;
        $order->paid_amount = $order->balance_paid_amount + $order->money_paid_amount;
        //结转退费也会减少未付款，更新要注意, 只有未付款大于订单金额减去订单支付金额
        if($order->paid_amount > 0 && $order->unpaid_amount > ($order->order_amount - $order->paid_amount)) {
            $order->unpaid_amount = ($order->order_amount - $order->paid_amount) >= 0
                ? ($order->order_amount - $order->paid_amount) : 0;
        }

        if($order->paid_amount + $order->order_discount_amount + $order->order_reduced_amount >= $order->order_amount) {
            $order->pay_status = self::PAY_STATUS_ALL;
        } elseif($order->paid_amount + $order->order_discount_amount + $order->order_reduced_amount > 0) {
            $order->pay_status = self::PAY_STATUS_SOME;
        } else {
            $order->pay_status = self::PAY_STATUS_NO;
        }

        if($order->paid_amount + $order->order_discount_amount + $order->order_reduced_amount > 0 && $order->order_status == Order::ORDER_STATUS_PLACE_ORDER) {
            $order->order_status = Order::ORDER_STATUS_PAID;
        }

        //如果付款等于0，且订单状态是已付状态，改为下单状态，主要是撤销收据用到。
        if($order->paid_amount + $order->order_discount_amount + $order->order_reduced_amount <= 0 && $order->order_status == Order::ORDER_STATUS_PAID) {
            $order->order_status = Order::ORDER_STATUS_PLACE_ORDER;
        }

        $rs = $order->save();
        if($rs === false) return false;

        return true;
    }


    //更新退款状态
    public function updateRefundStatus($oid, $order_status, $refund_status)
    {
        $data = [
            'order_status' => $order_status,
            'refund_status' => $refund_status
        ];

        $rs = $this->where('oid', $oid)->update($data);
        if($rs === false) return $this->user_error('更新退款状态失败');

        return true;
    }

    //更新分班状态
    public static function updateAcStatus($oid, $status)
    {
        $model = new self();
        $rs = $model->where('oid', $oid)->update(['ac_status' => $status]);
        if($rs === false) $model->user_error('更新订单分班状态失败');

        return true;
    }


    /**
     * 删除订单
     * @param Order $order
     * @param bool $force 是否强制删除
     * @return bool
     */
    public function deleteOrder(Order $order,$force = false)
    {
        try {
            $this->startTrans();

            $sid = $order['sid'];

            if ($order['order_status'] !== 0) {
                $m_orbi = new OrderReceiptBillItem();
                $orb_ids = $m_orbi->where('oid', $order->oid)->order('orb_id asc')->column('orb_id');
                $orb_ids = array_unique($orb_ids);

                $m_orb = new OrderReceiptBill();
                foreach ($orb_ids as $tmp_orb_id) {
                    $rs = $m_orb->delOneBill($tmp_orb_id, $force);
                    if ($rs === false) throw new FailResult($m_orb->getErrorMsg(), $m_orb->error_code);
                }

            } else {   //未付款订单
                $oid = $order->oid;

                $m_oi = new OrderItem();
                $order_items = $m_oi->where('oid', $oid)->field('oi_id')->select();


                foreach ($order_items as $item) {
                    $rs = $m_oi->delOneItem($item['oi_id']);
                    if ($rs === false) throw new FailResult($m_oi->getErrorMsg());
                }

                $rs = (new OrderPerformance())->where('oid', $oid)->delete();
                if ($rs === false) throw new FailResult('删除订单相关业绩失败');

                $rs = $order->delete();
                if ($rs === false) throw new FailResult('删除订单失败');

            }

            $mStudent = new Student();

            $result = $mStudent->refreshCustomerSignup($sid);

            if (!$result) {
                $this->rollback();
                return $this->user_error($mStudent->getError());
            }

        }catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;
    }

    public static function getOrderNo($oid) {
        $order = (new self())->where('oid', $oid)->field('order_no')->find();

        return !empty($order) && isset($order['order_no']) ? $order['order_no'] : '';
    }


    public function addSalesman($oid, $salesman_data)
    {
        $order = $this->find($oid);
        if(empty($order)) return $this->user_error('订单不存在');
        if (!empty($salesman_data)) {
            try {
                $this->startTrans();
                $order_performance_model = new OrderPerformance();
                foreach ($salesman_data as $salesman) {
                    $data = [];
                    $data['oid'] = $oid;
                    $data['bid'] = $order->bid;
                    $data['eid'] = $salesman['eid'];
                    $data['sale_role_did'] = isset($salesman['sale_role_did']) ? $salesman['sale_role_did'] : 0;
                    $data['amount'] = $order->order_amount;

                    $rs = $order_performance_model->where('oid', $oid)->where('eid', $salesman['eid'])->find();
                    if(!empty($rs)) continue;

                    $rs = $order_performance_model->createOnePerformance($data);
                    if (!$rs) exception(400, $order_performance_model->getErrorMsg());
                }
                $this->commit();
            } catch (Exception $e) {
                $this->rollback();
                return $this->user_error($e->getMessage());
            }
        }

        return true;
    }


    /**
     * 获取订单的微信模板消息的数据
     */
    public function getTplDetail($format = false)
    {
        $order_items = $this->getAttr('order_items');
        $order_info = [];
        foreach ($order_items as $key => $item) {
            $order_info[$key]['nums'] = $item['nums'];
            if ($item['gtype'] == OrderItem::GTYPE_LESSON) {
                //课程
                $order_info[$key]['unit'] = $item['nums_unit_text'];
                $order_info[$key]['name'] = $item['lesson']['lesson_name'];
            } elseif ($item['gtype'] == OrderItem::GTYPE_GOODS) {
                //物品
                $order_info[$key]['name'] = $item['material']['name'];
                $order_info[$key]['unit'] = $item['material']['unit'];
            } elseif ($item['gtype'] == OrderItem::GTYPE_DEBIT) {
                //储值或储值卡
                $order_info[$key]['unit'] = '元';
                $order_info[$key]['name'] = '储值';
            }
            $order_info[$key]['format'] = sprintf("\t%s\t×%s\t%s", $order_info[$key]['name'], $order_info[$key]['nums'], $order_info[$key]['unit']);
        }
        if ($format) {
            unset($item);
            $str = "\n";
            foreach ($order_info as $item) {
                $str .= $item['format'] . "\n";
            }
            return rtrim($str);
        } else {
            return $order_info;
        }
    }

  
    /**
     * 下单操作
     * 报名和缴费
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function createOrder($input){
        $need_fields = ['student','order'];
        if(!$this->checkInputParam($input,$need_fields)){
            return false;
        }
        $ret               = [];
        $input_student     = $input['student'];              # 学生
        $input_order       = $input['order'];                  # 订单基本数据
        $input_order_items = $input['order']['items'];    
        $input_salesman    = isset($input['salesman']) && !empty($input['salesman']) ? $input['salesman']:[];
        $input_paid_list   = $input['order']['payment'];
        $input_order_tpl   = isset($input['template_data']) ? $input['template_data'] : [];

        $orb_id = 0;
        $oid    = 0;
        $this->startTrans();
        try {
            //处理订单模板
            if(!empty($input_order_tpl)){
                $input_order_tpl['template'] = isset($input_order_tpl['template']) && !empty($input_order_tpl['template'])
                                        ? $input_order_tpl['template'] : $input_order_items;
                $input_order_tpl['type']     = InputTemplate::TYPE_ORDER;

                $result = $this->m_input_template->createOneTemplate($input_order_tpl);

                if(!$result){
                    $this->rollback();
                    return $this->user_error($this->m_input_template->getError());
                }
            }
            //处理学员信息
            if(!isset($input_student['sid']) || $input_student['sid'] == 0){
                if(isset($input_student['sid'])){
                    unset($input_student['sid']);
                }
                $sid = $this->m_student->createOneStudent($input_student);
                if(!$sid){
                    $this->rollback();
                    return $this->user_error($this->m_student->getError());
                }
                $input_student['sid'] = $sid;
            }

            //判断是否有体验课存在
            $has_demo_lesson = false;
            $unp_amount = 0.00;
            $mCs = new ClassStudent();
            foreach($input_order_items as $ioi){
                if($ioi['gtype'] == 0){
                    //判断班级是否满员
                    if(isset($ioi['cid']) && $ioi['cid'] > 0){
                        $class_info = get_class_info($ioi['cid']);
                        $w_cs['cid'] = $ioi['cid'];
                        $w_cs['sid'] = ['NEQ',$input_student['sid']];
                        $w_cs['status'] = 1;

                        $cs_count = $mCs->where($w_cs)->count();
                        if($cs_count >= $class_info['plan_student_nums']){
                            $msg = sprintf("班级:%s 已经满员,额定人数:%s,现有人数:%s",$class_info['class_name'],$class_info['plan_student_nums'],$cs_count);
                            $this->rollback();
                            return $this->user_error($msg);
                        }

                        if($class_info['is_demo'] == 1){
                            $has_demo_lesson = true;
                            break;
                        }
                    }
                    if(isset($ioi['lid']) && $ioi['lid'] > 0){  // 报的课程 
                        $lesson_info = get_lesson_info($ioi['lid']);
                        if($lesson_info['is_demo'] == 1){
                            $has_demo_lesson = true;
                            break;
                        }
                    }
                }elseif($ioi['gtype'] == 3){
                    if(!isset($ioi['pi_id'])){
                        $this->rollback();
                        return $this->user_error('订单条目缺少参数pi_id!');
                    }
                    $pi_info = get_pi_info($ioi['pi_id']);
                    if(!$pi_info['is_performance']){
                        $unp_amount += $ioi['subtotal'];
                    }
                }
            }

            //判断用户定义合同号是否已存在是否合法
            if (isset($input_order['user_contract_no']) && !empty($input_order['user_contract_no'])) {
                $legal = $this->is_contract_and_receipt_legal($input_order['user_contract_no']);
                if (!$legal) {
                    $this->rollback();
                    return $this->user_error('合同号不合法');
                }

                $user_contract_no = $this->is_user_contract_no_exists($input_order['user_contract_no'],0);
                if ($user_contract_no) {
                    $this->rollback();
                    return $this->user_error('合同号已存在');
                }
            }


            //创建订单信息
            $input_order['sid'] = $input_student['sid'];
            if($has_demo_lesson){
                $input_order['is_demo'] = 1;
            }
            $input_order['unp_amount'] = $unp_amount;
            $oid = $this->createOrderMain($input_order);
            if(!$oid){
                $this->rollback();
                return false;
            }

            //创建订单条目
            $result = $this->createOrderItems($input_order_items,$oid);

            if(!$result){
                $this->rollback();
                return false;
            }
            //创建订单业绩
            $result = $this->createOrderPerformance($input_salesman,$oid);
            if(!$result){
                $this->rollback();
                return false;
            }

            //处理付款
            if (isset($input_order['is_submit']) && $input_order['is_submit'] === 1) {
                $orb_id = $this->confirmPayment($input_paid_list,$input_order_items,$input_order,$oid);        //确认付款
                if(!$orb_id){
                    $this->rollback();
                    return false;
                }

            }

            //更新市场名单状态
            $result = $this->updateMarketClueDeal($input_student['sid'],1);
            if(!$result){
                $this->rollback();
                return false;
            }

            // 更新客户名单状态 仅客户名单转正式学员 报名有效
            $result = $this->updateCustomer($input_student['sid'],$input_order['order_amount']);
            if($result !== true){
                $this->rollback();
                return false;
            }
            

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        $this->oid     = $oid;
        $ret['items']  = $input_order_items;
        $ret['orb_id'] = $orb_id;
        $ret['oid']    = $oid;

        return $ret;

    }

    /**
     * 更新客户信息
     * @param  [type] $sid [description]
     * @return [type]      [description]
     */
    public function updateCustomer($sid,$amount)
    {
        $m_customer = new Customer();
        $customer = $m_customer->where('sid',$sid)->find();
        if(!empty($customer) && $customer['is_reg'] == 0){
            $customer->is_reg = 1;
            $customer->save();
        }
        return true;
    }

    /**
     * 创建订单主表记录
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function createOrderMain($data) {
        $data['order_no'] = $this->makeOrderNo();
        $data['user_contract_no'] = isset($data['user_contract_no'])?$data['user_contract_no']:'';
        if(isset($data['is_import']) && $data['is_import'] == 1){  // 导入的订单
            if($data['items'][0]['cid']){
                $data['order_status'] = 2;  //已分班
            }else{
                $data['order_status'] = 1;  //已支付
            }
            $data['pay_status']  = Order::PAY_STATUS_ALL;
            $data['paid_amount'] = $data['paid_amount'];
            $data['unpaid_amount'] = 0.00;  // 暂不支持部分付款
            $data['is_submit'] = 1;  // 提交订单
        }else{
            $data['order_status'] = 0;
            $data['pay_status']  = 0;
            $data['paid_amount'] = 0.00;
            $data['unpaid_amount'] = $data['order_amount'];
            $data['is_submit'] = 0;
        }

        if (!empty($data['user_contract_no']) || $data['user_contract_no'] != ''){
            $data['order_no'] = $data['user_contract_no'];
        }

        if($data['order_discount_amount'] < 0) return $this->user_error('折扣单价高于原价了或者折扣出现负数了。');
        $result = $this->data([])->validate()->isUpdate(false)->allowField(true)->save($data);

        if(!$result){
            return $this->sql_add_error('order');
        }
        $oid = $this->oid;

        return $oid;
    }


    /**
     * 创建订单条目
     * @param  [type]  $input_order_items [description]
     * @param  integer $oid               [description]
     * @return [type]                     [description]
     */
    public function createOrderItems(&$input_order_items,$oid = 0){
        if(!$oid){
            $oid = $this->getData('oid');
        }

        $order_info = get_order_info($oid);

        $sid = $order_info['sid'];

        $this->startTrans();
        try{
            foreach ($input_order_items as $k => $item) {
                $m_order_item = new OrderItem();
                if(isset($item['oi_id'])){
                    unset($item['oi_id']);
                }
                $item['oid'] = $oid;
                $item['sid'] = $sid;
                $item['start_int_day'] = int_day($order_info['paid_time']);


                $result = $m_order_item->createOrderItem($item);
                if(!$result){
                    $this->rollback();
                    return $this->user_error($m_order_item->getError());
                }
                $item['oi_id'] = $m_order_item->oi_id;

                $input_order_items[$k] = $item;
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 创建订单业绩
     * @param  [type]  &$input_salesman [description]
     * @param  integer $oid             [description]
     * @return [type]                   [description]
     */
    public function createOrderPerformance(&$input_salesman,$oid = 0){
        if(!$oid){
            $oid = $this->getData('oid');
        }
        $order_info = get_order_info($oid);

        $order_amount = $order_info['order_amount'];
        $unp_amount   = $order_info['unp_amount'];

        $performance_amount = $order_amount - $unp_amount;

        if (!empty($input_salesman)) {
            $this->startTrans();
            try{
                foreach ($input_salesman as $salesman) {
                    $data = [];
                    $data['oid'] = $oid;
                    $data['sid'] = $order_info['sid'];
                    $data['eid'] = $salesman['eid'];
                    $data['sale_role_did'] = isset($salesman['sale_role_did']) ? $salesman['sale_role_did'] : 0;
                    $data['amount'] = $performance_amount;
                    $data['unp_amount'] = $unp_amount;

                    $result = $this->m_order_performance->createOnePerformance($data);
                    if(!$result){
                        $this->rollback();
                        return $this->user_error($this->m_order_performance->getError());
                    }
                }
            }catch(Exception $e){
                $this->rollback();
                return $this->exception_error($e);
                
            }
            $this->commit();
        }
        return true;
    }

    protected function get_detail_payment_info($input_order_items){
        $payment_info = [];

        foreach($input_order_items as $ioi){
            array_push($payment_info,[
                'oi_id' => $ioi['oi_id'],
                'paid_amount'   => $ioi['paid_amount']
            ]);
        }

        return $payment_info;
    }

    /**
     * 订单添加付款记录
     * @param [type]  $input_paid_list [description]
     * @param [type]  $input_pay_items [description]
     * @param integer $oid             [description]
     */
    public function addPayment($input_paid_list,$input_order_items,$input_order,$oid = 0){
        if(!$oid){
            $oid = $this->getData('oid');
        }
        $order_info = get_order_info($oid);
        if(!$order_info){
            return $this->user_error('订单不存在:'.$oid);
        }
        $input_order['user_receipt_no'] = isset($input_order['user_receipt_no'])?$input_order['user_receipt_no']:'';
         //已付金额
        $paid_amount = $order_info['paid_amount'];
        $orb_id = 0;
        $this->startTrans();
        try {
            $balance_paid_amount = 0;
            if(isset($input_order['balance_paid_amount']) && $input_order['balance_paid_amount'] > 0){     //如果使用余额付款
                $balance_paid_amount = $this->useBalancePayorder($oid, $input_order['balance_paid_amount']);
                if(false === $balance_paid_amount){
                    $this->rollback();
                    return false;
                }
                $paid_amount += $balance_paid_amount;
            }

            $money_paid_amount = 0.00;

            if(!empty($input_paid_list)){
                foreach($input_paid_list as $paid){
                    $money_paid_amount += $paid['pay_amount'];
                }

                $paid_amount += $money_paid_amount;

            }

            $sum_payment_info = [
                'balance_paid_amount' => $balance_paid_amount,
                'money_paid_amount'   => $money_paid_amount,
                'paid_amount'         => $paid_amount,
                'user_receipt_no'     => $input_order['user_receipt_no'],
            ];

            $detail_payment_info = $this->get_detail_payment_info($input_order_items);


            if (!empty($input_order['user_receipt_no']) && $input_order['user_receipt_no'] != '') {
                $legal = $this->is_contract_and_receipt_legal($input_order['user_receipt_no']);
                if (!$legal) {
                    $this->rollback();
                    return $this->user_error('收据号不合法');
                }

                $user_receipt_no = $this->is_user_receipt_no_exists($input_order['user_receipt_no']);
                if ($user_receipt_no) {
                    $this->rollback();
                    return $this->user_error('收据号已存在');
                }
            }

            //生成收据
            $orb_id = $this->createOrderReceiptBill($sum_payment_info,$detail_payment_info,$oid);
            if(!$orb_id){
                $this->rollback();
                return false;
            }

            //生成订单付款记录
            if(!empty($input_paid_list)){
                $m_order_payment_history = new OrderPaymentHistory();
                foreach($input_paid_list as $paid){
                    if((float)$paid['pay_amount'] < 0 ){
                        continue;
                    }
                    $paid_history_data = [];
                    array_copy($paid_history_data,$paid,['aa_id']);
                    $paid_history_data['oid'] = $oid;
                    $paid_history_data['orb_id'] = $orb_id;
                    $paid_history_data['amount'] = $paid['pay_amount'];
                    $paid_history_data['og_id'] = $order_info['og_id'];
                    $paid_history_data['bid']   = $order_info['bid'];
                    $paid_history_data['paid_time'] = $order_info['paid_time'];
                    $paid_history_data['opo_id'] = isset($paid['opo_id'])?$paid['opo_id']:0;
                    $paid_history_data['remark'] = Tally::setRemark('payment',['orb_id'=>$orb_id]);
                    $paid_history_data['sid'] = $order_info['sid'];


                    $result = $m_order_payment_history->createOneHistory($paid_history_data);
                    if(!$result){
                        exception($m_order_payment_history->getError());
                    }
                }
            }

            //超出部分预存
            if($paid_amount > $order_info['order_amount']){
                $result = $this->orderChargeMoney($paid_amount,$order_info['oid']);
                if(!$result){
                    $this->rollback();
                    return false;
                }
            }
            
            //计算收款业绩
            $result = $this->createEmployeeReceipt($orb_id,$money_paid_amount,$oid);

            if(!$result){
                $this->rollback();
                return false;
            }

            //更新订单已付款金额及付款状态
            $update_order = [];
            $update_order['is_submit']    = 1;
            $update_order['paid_amount']  = $paid_amount;
            $update_order['unpaid_amount'] = min_val($order_info['order_amount'] - $update_order['paid_amount']);

            $update_order['order_status'] = 1;
            $update_order['pay_status']   = $paid_amount >= $order_info['order_amount'] ? self::PAY_STATUS_ALL:self::PAY_STATUS_SOME;
            


            if($update_order['pay_status'] == self::PAY_STATUS_ALL && $order_info['is_debit'] == 0){ // 缴清 更新student表in_time = 0的数据
                $paid_time = isset($input_order['paid_time']) ? strtotime($input_order['paid_time']) : $order_info['paid_time'];
                $m_student = Student::get(['sid'=>$order_info['sid']]);
                if($m_student->getData('in_time') == 0 || $m_student->in_time == '1970-01-01'){
                    $m_student->in_time = $paid_time;
                    $res = $m_student->save();
                    if($res === false){
                        return $this->save_sql_error('student');
                    }
                }

                // 更新一下order 表的paid_time
                $m_order = Order::get($oid);
                $m_order->paid_time = $paid_time;
                $res = $m_order->save();
                if($res === false){
                    return $this->save_sql_error('order');
                }
            }
            

            //20180930 添加付款金额统计
            $update_order['money_paid_amount'] = $order_info['money_paid_amount'] + $sum_payment_info['money_paid_amount'];
            $update_order['balance_paid_amount'] = $order_info['balance_paid_amount'] + $sum_payment_info['balance_paid_amount'];
            $w_order_update = [];
            $w_order_update['oid'] = $oid;
            $result = $this->isUpdate(true)->save($update_order,$w_order_update);

            $hook_data = [
                'sid' => $order_info['sid'],
                'amount' => $money_paid_amount,         //必须是非余额付款金额
                'hook_action' => 'order_pay'
            ];
            Hook::listen('handle_credit', $hook_data);

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('order');
            }
        }catch(Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return $orb_id; 
    }
    
    /**
     * 确认付款
     * @param  [type]  $input_paid_list [description]
     * @param  integer $oid             [description]
     * @return [type]                   [description]
     */
    public function confirmPayment($input_paid_list,$input_order_items,$input_order,$oid = 0){
        if(!$oid){
            $oid = $this->getData('oid');
        }
        $order_info = get_order_info($oid);
        if(!$order_info){
            return $this->user_error('订单不存在:'.$oid);
        }
       
        $this->startTrans();
        try {
            $orb_id = $this->addPayment($input_paid_list,$input_order_items,$input_order,$oid);

            if(!$orb_id){
                $this->rollback();
                return false;
            }

            //创建订单相关业务数据：课时、商品
            $result = $this->createOrderBusinessData($oid);
            if(!$result){
                $this->rollback();
                return false;
            }

            //更新学员首次报名业绩
            $result = $this->updateCustomerSignupAmount($oid);
            if(!$result){
                $this->rollback();
                return false;
            }

            //更新学员体验课学员状态
            $result = $this->updateStudentIsDemoField($oid);
            if(!$result){
                $this->rollback();
                return false;
            }
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return $orb_id;
    }

    /**
     * 用余额付订单款
     * @param  [type] $oid [description]
     * @return [type]      [description]
     */
    public function useBalancePayorder($oid = 0, $balance_paid_amount){
        if(!$oid){
            $oid = $this->getData('oid');
        }
        $order_info = get_order_info($oid);
        if(!$order_info){
            return $this->user_error('订单不存在:'.$oid);
        }

        $paid_amount = 0.00;

        $w_s['sid'] = $order_info['sid'];
        $m_student = $this->m_student->where($w_s)->find();
        if(!$m_student){
            return $this->user_error('订单关联学员ID不存在!');
        }

        $w_smh['sid']           = $order_info['sid'];
        $w_smh['business_type'] = StudentMoneyHistory::BUSINESS_TYPE_ORDER;
        $w_smh['business_id']   = $oid;

        //$smh = $this->m_student_money_history->where($w_smh)->find();       //一个订单一次只处理一次余额支付
        //if($smh){
        //    return $paid_amount;
        //}

        if($m_student->money < $balance_paid_amount){
            return $this->user_error('电子钱包余额不足!');
        }

        $money_change_data = [];
        $money_change_data['money'] = -$balance_paid_amount;
        array_copy($money_change_data,$w_smh,['business_type','business_id']);

        //处理储蓄卡
        if(!empty($order_info['sdc_id'])) {
            $student_debit_card = StudentDebitCard::get($order_info['sdc_id']);
            if(!empty($student_debit_card) && $student_debit_card['remain_amount'] >= 0) {
                $dec_amount = $balance_paid_amount >= $student_debit_card['remain_amount'] ? $student_debit_card['remain_amount'] : $balance_paid_amount;
                $student_debit_card->remain_amount = $student_debit_card->remain_amount - $dec_amount;
                if($student_debit_card->remain_amount <= 0) {
                    $student_debit_card->is_used = 2;
                } else {
                    $student_debit_card->is_used = 1;
                }
                $student_debit_card->save();

                $money_change_data['money'] = -$dec_amount;
                $money_change_data['sdc_id'] = $order_info['sdc_id'];
                $result = $this->m_student->changeMoney($m_student,$money_change_data);
                if(!$result){
                    return $this->user_error($this->m_student->getError());
                }
            }
        }

        if(isset($dec_amount) && $dec_amount <= $balance_paid_amount) {
            $money_change_data['money'] = -($balance_paid_amount - $dec_amount);
        }

        if($money_change_data['money'] != 0) {
            $result = $this->m_student->changeMoney($m_student,$money_change_data);
            if(!$result){
                return $this->user_error($this->m_student->getError());
            }
        }

        return $balance_paid_amount;
    }

    /**
     * 通过订单超出金额部分充值预付款
     * @param  integer $oid [description]
     * @return [type]       [description]
     */
    public function orderChargeMoney($paid_amount,$oid = 0){
        if(!$oid){
            $oid = $this->getData('oid');
        }


        $order_info = get_order_info($oid);
        if(!$order_info){
            return $this->user_error('订单不存在:'.$oid);
        }

        $w_s['sid'] = $order_info['sid'];
        $m_student = $this->m_student->where($w_s)->find();
        if(!$m_student){
            return $this->user_error('订单关联学员ID不存在!');
        }

        $charge_amount = 0.00;

        $w_smh['sid']           = $order_info['sid'];
        $w_smh['business_type'] = StudentMoneyHistory::BUSINESS_TYPE_RECHARGE;
        $w_smh['business_id']   = $oid;

        $smh = $this->m_student_money_history->where($w_smh)->find();       //一个订单一次只处理一次余额支付

        if($smh){
            return $charge_amount;
        }

        $charge_amount = $paid_amount - $order_info['order_amount'];

        $money_change_data = [];
        $money_change_data['money'] = $charge_amount;

        array_copy($money_change_data,$w_smh,['business_type','business_id']);

        $result = $this->m_student->changeMoney($m_student,$money_change_data);

        if(!$result){
            return $this->user_error($this->m_student->getError());
        }

        return $charge_amount;
    }

    /**
     * 创建员工业绩
     * @param  [type]  $orb_id      [收据ID]
     * @param  [type]  $paid_amount [本次付款金额]
     * @param  integer $oid         [订单ID]
     * @return [type]               [description]
     */
    public function createEmployeeReceipt($orb_id,$paid_amount,$oid = 0){
        if(!$oid){
            $oid = $this->getData('oid');
        }
        $order_info = get_order_info($oid);
        if(!$order_info){
            return $this->user_error('订单不存在:'.$oid);
        }

        $need_cacu_unp = false;

        $unp_amount = 0.00;

        if($paid_amount == $order_info['money_pay_amount']){
            $unp_amount = $order_info['unp_amount'];
        }else{
            $need_cacu_unp = true;
        }

        $w_op['oid'] = $order_info['oid'];

        $order_performance_list = $this->m_order_performance->where($w_op)->select();

        $mEmployeeReceipt = new EmployeeReceipt();
        $this->startTrans();
        try{
            if($order_performance_list){
                foreach($order_performance_list as $opl){
                    $opl_data = $opl->getData();
                    $erc_data = [];
                    array_copy($erc_data,$opl_data,['og_id','bid','eid','sale_role_did','oid']);
                    $erc_data['orb_id'] = $orb_id;
                    $erc_data['amount'] = $paid_amount;
                    $erc_data['sid'] = $order_info['sid'];
                    $erc_data['unp_amount'] = $unp_amount;


                    if($need_cacu_unp){
                        $w_erc_unp = [];
                        $w_erc_unp['eid'] = $opl_data['eid'];
                        $w_erc_unp['oid'] = $opl_data['oid'];

                        $ex_unp_amount = $mEmployeeReceipt->where($w_erc_unp)->sum('unp_amount');

                        if($ex_unp_amount){
                            $erc_data['unp_amount'] = $order_info['unp_amount'];
                        }
                    }

                    if($erc_data['unp_amount'] > 0 ){
                        $erc_data['amount'] = min_val($paid_amount - $erc_data['unp_amount']);
                    }

                    $w_erc = [];
                    $w_erc['eid']    = $opl_data['eid'];
                    $w_erc['orb_id'] = $orb_id;

                    $ex = $mEmployeeReceipt->where($w_erc)->find();
                    if(!$ex){
                        $result = $mEmployeeReceipt->createOneReceipt($erc_data);
                        if(!$result){
                            exception($mEmployeeReceipt->getError());
                        }
                    }
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 创建收据
     * @param  integer $oid [description]
     * @return [type]       [description]
     */
    public function createOrderReceiptBill($sum_payment_info,$detail_payment_info,$oid = 0){
        if($oid == 0){
            $oid = $this->getData('oid');
        }

        $balance_paid_amount = $sum_payment_info['balance_paid_amount'];
        $money_paid_amount   = $sum_payment_info['money_paid_amount'];
        $paid_amount         = $sum_payment_info['paid_amount'];
        $user_receipt_no         = $sum_payment_info['user_receipt_no'];

        $order_info = get_order_info($oid);
        if(!$order_info){
            return $this->user_error('订单不存在:'.$oid);
        }


        $unpaid_amount = $order_info['order_amount'] - $paid_amount;

        if($unpaid_amount < 0){
            $unpaid_amount = 0;
        }

        $student_amount = $paid_amount - $order_info['order_amount'];

        if($student_amount < 0){
            $student_amount = 0;
        }

        $bill_data  = [];
        array_copy($bill_data,$order_info,['og_id','bid','sid','oid']);

        $bill_data['amount'] = $money_paid_amount + $balance_paid_amount;

        $bill_data['balance_paid_amount'] = $balance_paid_amount;
        $bill_data['money_paid_amount']   = $money_paid_amount;
        $bill_data['unpaid_amount']       = $unpaid_amount;
        $bill_data['student_amount']      = $student_amount;
        $bill_data['paid_time']           = $order_info['paid_time'];       //20180915添加
	$bill_data['user_receipt_no']      = $user_receipt_no;
	
        $this->startTrans();

        try{
            $mRrderReceiptBill = new OrderReceiptBill();
            $orb_id = $mRrderReceiptBill->createOneBill($bill_data);
            if (!$orb_id){
                return $this->user_error($mRrderReceiptBill->getError());
            }

            $mOrderReceiptBillItem = new OrderReceiptBillItem();
            foreach($detail_payment_info as $dpi){
                $bill_data_item = [];
                $order_item = get_order_item_info($dpi['oi_id'],false);

                array_copy($bill_data_item,$order_item,['og_id','oid','bid','sid','oi_id','paid_amount']);
                $bill_data_item['orb_id'] = $orb_id;
                $bill_data_item['paid_amount'] = $dpi['paid_amount'];
            
                $result = $mOrderReceiptBillItem->createOneItem($bill_data_item);
                if(false === $result){
                    return $this->user_error($mOrderReceiptBillItem->getError());
                }

                //更新订单条目已付款
                $update_oi = [];
                $update_oi['paid_amount'] = $order_item['paid_amount']+$dpi['paid_amount'];

                $w_update_oi = [];
                $w_update_oi['oi_id'] = $dpi['oi_id'];

                $result = (new OrderItem)->save($update_oi,$w_update_oi);

                if(false === $result){
                    return  $this->sql_save_error('order_item');
                }
            }
           
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return $orb_id;
    }

    /**
     * 更新客户报名金额
     * @param  integer $oid [description]
     * @return [type]       [description]
     */
    public function updateCustomerSignupAmount($oid = 0){
        if($oid == 0){
            $oid = $this->getData('oid');
        }

        $order_info = get_order_info($oid);
        if(!$order_info){
            return $this->user_error('订单不存在:'.$oid);
        }

        $sid = $order_info['sid'];

        $signup_amount = $order_info['money_pay_amount'];

        $w_cu['sid'] = $sid;
        $w_cu['signup_amount'] = 0;
        $update_cu['signup_amount'] = $signup_amount;
        $update_cu['signup_int_day'] = int_day($order_info['paid_time']);

        $this->startTrans();
        try {

            $result = $this->m_customer->isUpdate(true)->save($update_cu, $w_cu);

            if (false === $result) {
                $this->rollback();
                return $this->sql_save_error('customer');
            }

            $w_mcl['sid'] = $sid;
            $update_mcl['deal_amount'] = $signup_amount;

            $result = $this->m_market_clue->isUpdate(true)->data([])->save($update_mcl, $w_mcl);
            if (false === $result) {
                $this->rollback();
                return $this->sal_save_error('market_clue');
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return true;

    }

    /**
     * 创建订单相关业务数据 学员课时、商品处理
     * @param  integer $oid [description]
     * @return [type]       [description]
     */
    public function createOrderBusinessData($oid = 0){
        if($oid == 0){
            $oid = $this->getData('oid');
        }

        $order_info = get_order_info($oid);
        if(!$order_info){
            return $this->user_error('订单不存在:'.$oid);
        }

        $this->startTrans();
        try{
         
            $w_oi = [];
            $w_oi['oid'] = $oid;
            $order_items = $this->m_order_item->where($w_oi)->select();

            foreach($order_items as $oi){
                $result = $oi->createBusinessData();
                if(!$result){
                    return $this->user_error($oi->getError());
                }
            }

            if(!isset($order_info['is_debit']) || $order_info['is_debit'] == 0) {
                //更新学员的剩余课时数和总课时数统计
                $result = $this->m_student->updateLessonHours($order_info['sid']);
                if (!$result) {
                    $this->rollback();
                    return $this->user_error($this->m_student->getError());
                }
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;
    }

    /**
     * 订单结算
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function pay($input){
        $need_fields = ['order'];
        if(!$this->checkInputParam($input,$need_fields)){
            return false;
        }
        $ret               = [];
        $input_order       = $input['order'];                  # 订单基本数据
        $input_order_items = $input['order']['items'];
        $input_paid_list_origin  = $input['order']['payment'];
        $input_order_tpl   = isset($input['template_data']) ? $input['template_data'] : [];

        $input_paid_list = [];
        //过滤支付方式
        foreach($input_paid_list_origin as $k=>$r){
            if($r['pay_amount'] !== ''){
                array_push($input_paid_list,$r);
            }
        }

        if(!isset($input_order['oid']) || empty($input_order['oid'])){
            return $this->user_error('缺少订单ID参数!');
        }

        $order_info = get_order_info($input_order['oid']);

        if(!$order_info){
            return $this->user_error('订单信息有误,订单不存在或已经被删除!');
        }
        $orb_id = 0;
        $oid    = $input_order['oid'];


        //判断如果订单已经交费完成，就不允许操作了,重复提交情况。
        if($order_info['pay_status'] == 2){
            $w_orb['oid'] = $oid;
            $mOrb = new OrderReceiptBill();
            $m_orb = $mOrb->where($w_orb)->order('orb_id DESC')->find();
            $ret['orb_id'] = $m_orb->orb_id;
            $ret['oid']    = $oid;
            return $ret;
        }

        if(empty($input_paid_list)){
            if($order_info['order_amount'] > 0){
                $ret['orb_id'] = 0;
                $ret['oid']    = $oid;
                return $ret;
                //return $this->user_error('缺少付款信息参数!');
            }
        }

        $this->data($order_info);
        unset($order_info);

        $this->startTrans();
        try {
            //处理订单模板
            if(!empty($input_order_tpl)){
                $input_order_tpl['template'] = isset($input_order_tpl['template']) && !empty($input_order_tpl['template'])
                                        ? $input_order_tpl['template'] : $input_order_items;
                $input_order_tpl['type']     = InputTemplate::TYPE_ORDER;

                $result = $this->m_input_template->createOneTemplate($input_order_tpl);

                if(!$result){
                    $this->rollback();
                    return $this->user_error($this->m_input_template->getError());
                }
            }

            $orb_id = $this->confirmPayment($input_paid_list,$input_order_items,$input_order,$oid);        //确认付款
            if(!$orb_id){
                $this->rollback();
                return false;
            }

            // 添加一个订单缴费 操作日志
            StudentLog::addPayOrderLog($oid);

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        $ret['orb_id'] = $orb_id;
        $ret['oid']    = $oid;

        return $ret;
    }

    /**
     * 订单补缴款
     * @param  [type] $sid      [description]
     * @param  [type] $items    [description]
     * @param  [type] $payments [description]
     * @return [type]           [description]
     */
    public function supplementPay($sid, $items, $payments ,$paid_time)
    {
        if(empty($items)){
            return $this->user_error('缺少参数:items');
        }
        $oid = $items[0]['oid'];
        $input_paid_list   = $payments;
        $input_order_items = [];

        $balance_paid_amount = 0;
        foreach($items as $item){
            if(isset($item['balance_paid_amount']) && $item['balance_paid_amount'] > 0) {
                $balance_paid_amount += $item['balance_paid_amount'];
            }
            if($item['oid'] == $oid){
                $new_order_item = [];
                array_copy($new_order_item,$item,['oi_id','money_pay_amount','balance_paid_amount','paid_amount']);
                array_push($input_order_items,$new_order_item);
            }
        }
	
	    $input_order['balance_paid_amount'] = $balance_paid_amount;
        $input_order['paid_time'] = $paid_time;


        return $this->addPayment($input_paid_list,$input_order_items,$input_order,$oid);

    }

    /**
     * 更新市场名单成交状态
     * @param $sid
     * @param $status
     * @return bool
     */
    public function updateMarketClueDeal($sid,$status = 1){
        $m_mc = new MarketClue();
        $w_update_mc['sid'] = $sid;
        $update_mc['is_deal'] = $status;

        $this->startTrans();

        $result = $m_mc->save($update_mc,$w_update_mc);

        // 更新市场渠道 
        $market_clue = MarketClue::get(['sid'=>$sid]);
        if($market_clue){
            MarketClue::UpdateNumOfChannel($market_clue['mc_id']);
        }

        if(false === $result){
            $this->rollback();
            return $this->sql_save_error('market_clue');
        }

        $this->commit();
        return true;
    }

    /**
     * 更新学员是否体验课学员状态
     * @param $oid
     */
    public function updateStudentIsDemoField($oid){
        $order_info = get_order_info($oid);
        $sid = $order_info['sid'];

        $student_info = get_student_info($sid);
        $m_student = new Student($student_info);
        $m_student->isUpdate(true);

        $is_demo = intval($order_info['is_demo']);
        $member_config = user_config('params.member');

        $this->startTrans();
        try {
            // 如果学员状态为结课状态 更新学员状态
            if($student_info['status'] == Student::STATUS_FINISH){
                $m_student->status = Student::STATUS_NORMAL;
                $ret = $m_student->save();
                if(false === $ret){
                    $this->rollback();
                    return $this->save_sql_error('student');
                }
            }

            if ($is_demo) {

               if($student_info['is_demo'] == 0 && $student_info['is_demo_transfered'] == 0) {

                   $m_student->is_demo = 1;

                   if ($member_config['enable'] == 1) {
                       $m_student->vip_level = 0;
                   }

                   $result = $m_student->save();
                   if (false === $result) {
                       $this->rollback();
                       $this->sql_save_error('student');
                       return false;
                   }

               }
            }else{
                    $has_lesson_oi = 0;
                    $w_oi['oid'] = $oid;
                    $oi_list = get_table_list('order_item', $w_oi);
                    foreach($oi_list as $oi){
                        if($oi['gtype'] == 0){
                            $has_lesson_oi++;
                        }
                    }
                    if($has_lesson_oi) {
                        $dth['sid'] = $sid;
                        $dth['int_day'] = int_day(time());
                        $dth['sign_amount'] = $order_info['order_amount'];
                        $dth['bid'] = $student_info['bid'];

                        $from_demo_class = $this->find_demo_class($sid);

                        if ($from_demo_class) {
                            $dth['from_cid'] = $from_demo_class['cid'];
                            $dth['teach_eid'] = $from_demo_class['teach_eid'];
                            $dth['second_eid'] = $from_demo_class['second_eid'];
                            $dth['edu_eid'] = $from_demo_class['edu_eid'];
                        }
                        //查找来源体验课班级
                        $m_dth = model('demo_transfer_history');
                        $result = $m_dth->save($dth);
                        if (!$result) {
                            $this->rollback();
                            $this->sql_add_error('demo_transfer_history');
                            return false;
                        }
                        $m_student->is_demo = 0;
                        $m_student->is_demo_transfered = 1;

                        if ($member_config['enable'] == 1) {
                            //获得累计订单金额
                            $total_amount = $this->getStudentTotalOrderAmount($sid);
                            $m_student->vip_level = get_vip_level_by_amount($member_config, $total_amount);
                        }

                        $result = $m_student->save();
                        if (false === $result) {
                            $this->rollback();
                            $this->sql_save_error('student');
                            return false;
                        }
                    }
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 查找学员的体验课班级
     * @param $sid
     */
    protected function find_demo_class($sid){
        //todo;
        $cs_list = table('class_student')
            ->alias('cs')
            ->join('class c','cs.cid = c.cid','left')
            ->where('cs.sid',$sid)
            ->where('c.is_demo',1)
            ->where('c.is_delete',0)
            ->order('cs.in_time DESC')
            ->limit('0,1')
            ->select();

        if($cs_list){
            return $cs_list[0];
        }

        return false;

    }

    /**
     * 获得学员的累计订单金额
     * @param $sid
     */
    protected function getStudentTotalOrderAmount($sid){
        $w_o['sid'] = $sid;
        $w_o['order_status'] = ['LT',10];

        $amount = Order::where($w_o)->sum('paid_amount');
        if(!$amount){
            $amount = 0.00;
        }
        return $amount;
    }

    /**
     * 创建打印课表数据
     * @param $oid
     */
    public function makeLessonPrintData($oid){
        $ret = [];
        $order_info = get_order_info($oid);
        $sid = $order_info['sid'];
        $bid = $order_info['bid'];

        $sdc = [];
        $dc  = [];

        $student_data = get_student_info($sid);

        if(intval(date('Ymd',$student_data['create_time'])) == intval(int_day(time()))){
            $student_data['is_new'] = 1;
        }else{
            $student_data['is_new'] = 0;
        }

        $student_data['sex'] = get_sex($student_data['sex']);
        $student_data['school_name'] = '';
        if($student_data['school_id'] > 0){
            $ps_info = get_public_school_info($student_data['school_id']);
            $student_data['school_name'] = $ps_info['school_name'];
        }
        $student_data['first_family_rel'] = get_family_rel($student_data['first_family_rel']);

        $sys_data = user_config('params');
        $sys_data = [
            'org_name' => $sys_data['org_name'],
            'sysname' => $sys_data['sysname'],
            'address' => $sys_data['address'],
            'mobile' => $sys_data['mobile'],
        ];
        $sys_data['branch_name'] = get_branch_name($bid);

        if($order_info['sdc_id'] > 0){
            $sdc = get_sdc_info($order_info['sdc_id']);
            $dc  = get_dc_info($sdc['dc_id']);
        }

        //课程列表
        $lesson_list = [];
        $w_oi['gtype'] = 0;
        $w_oi['oid']   = $oid;

        $oi_list = get_table_list('order_item',$w_oi);

        $max_row = 6;
        $have_row = 0;

        foreach($oi_list as $oi){
            $litem = [];
            $litem['blank']  = false;
            $litem['lesson_name'] = '';
            $litem['class_name'] = '';
            $litem['teacher'] = '';
            $litem['lesson'] = [];
            $litem['class']  = [];
            $litem['sl']     = [];
            $litem['amount'] = $oi['subtotal'];
            $litem['origin_amount'] = $oi['origin_amount'];

            $sl_info = get_sl_info($oi['sl_id']);
            $litem['sl'] = $sl_info;
            if($oi['lid'] > 0) {
                $lesson = get_lesson_info($oi['lid']);
                $litem['lesson_name'] = $lesson['lesson_name'];
                $litem['lesson'] = $lesson;
            }
            if($oi['cid']>0){
                $class = get_class_info($oi['cid']);
                $cs_list = get_table_list('class_schedule',['cid'=>$class['cid']]);
                $schedules_text = [];
                if($cs_list){
                    foreach($cs_list as $cs){
                        array_push($schedules_text,$this->get_schedule_text($cs));
                    }
                }
                $class['schedules_text'] = $schedules_text;
                $class['schedules'] = $cs_list;
                $teacher = get_employee_info($class['teach_eid']);
                $litem['teacher'] = $teacher['ename'];
                $litem['class_name'] = $class['class_name'];
                $litem['class'] = $class;
            }
            array_push($lesson_list,$litem);
            $have_row++;
        }

        while($have_row < $max_row){
            array_push($lesson_list,[
                'blank'     => true,
                'lesson'    => [],
                'class'     => [],
                'sl'        => [],
                'amount'    => '',
                'origin_amount'=>''
            ]);
            $have_row++;
        }

        $ret['lessons'] = $lesson_list;
        $ret['student'] = $student_data;
        $ret['order']   = $order_info;
        $ret['sys']     = $sys_data;
        $ret['sdc']     = $sdc;
        $ret['dc']      = $dc;
        $ret['diy']     = get_print_vars($bid);
        return $ret;
    }

    protected function get_schedule_text($cs){
        $week_text = [
          1=>'一',
          2=>'二',
          3=>'三',
          4=>'四',
          5=>'五',
          6=>'六',
          7=>'日'
        ];

        $format = '周%s %s~%s';

        return sprintf($format,
            $week_text[$cs['week_day']],
            int_hour_to_hour_str($cs['int_start_hour']),
            int_hour_to_hour_str($cs['int_end_hour'])
        );
    }



    /**
     * 订单收据号修改
     */
    public function updatContract($oid,$input){
        $order = $this->get($oid);
        if ($order['user_contract_no'] == $input['user_contract_no']){
            return true;
        }

        $legal = $this->is_contract_and_receipt_legal($input['user_contract_no']);
        if (!$legal) {
            return $this->user_error('合同号不合法');
        }

        $user_receipt_no = $this->is_user_contract_no_exists($input['user_contract_no'],$oid);
        if ($user_receipt_no){
            return $this->user_error('合同号已存在');
        }

        $w = ['oid' => $oid];
        $allow_update_filed = ['user_contract_no'];
        $update = [];
        foreach($allow_update_filed as $field) {
            if (isset($input[$field])) {
                $update['user_contract_no'] = $input[$field];
                $update['order_no'] = $input[$field];
            }
        }
        $result = $this->where($w)->update($update);

        return $result;
    }


    /**
     * 用户定义合同号是否存在
     * @param $user_contract_no
     */
    protected function is_user_contract_no_exists($user_contract_no,$oid=0){
        $w['user_contract_no|order_no'] = $user_contract_no;
        $ex_order = $this->where($w)->find();
        if(!$ex_order){
            return false;
        }
        if ($oid > 0){
            if($ex_order['oid'] == $oid){
                return false;
            }
        }
        return true;
    }

    /**
     * 用户定义收据号是否存在
     * @param $user_contract_no
     */
    protected function is_user_receipt_no_exists($user_receipt_no){
        $orb_model = new OrderReceiptBill();
        $w['user_receipt_no|orb_no'] = $user_receipt_no;
        $result = $orb_model->where($w)
            ->find();
        if ($result){
            return true;
        }
        return false;
    }

    /**
     * 用户定义合同号收据号是否合法
     * @param $user_contract_no
     */
    protected function is_contract_and_receipt_legal($string){
        $result = preg_match('/^\w{4,24}$/',$string);
        if ($result){
            return true;
        }
        return false;
    }


    /**
     * 修改付款金额
     * @param $oid
     * @param $update_amount
     */
    public function updatePaidAmount($oid,$update_amount)
    {
        $order = $this->where('oid',$oid)->find();
        if (empty($order)){
            return $this->user_error('订单不存在！');
        }

        $update['paid_amount'] = $order['paid_amount'] + $update_amount;
        $w['oid'] = $oid;
        $result = $this->save($update,$w);
        if (false === $result){
            return $this->sql_save_error('order');
        }

        return true;
    }
}