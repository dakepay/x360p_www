<?php
/** 
 * Author: luo
 * Time: 2017-11-03 17:39
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class OrderReceiptBill extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected function setPaidTimeAttr($value)
    {
        if(is_numeric($value)){
            return $value;
        }
        return strtotime($value);
    }

    protected function getPaidTimeAttr($value)
    {
        return $value ? date('Y-m-d', $value) : $value;
    }

    public function orderPaymentHistory()
    {
        return $this->hasMany('OrderPaymentHistory', 'orb_id', 'orb_id')->field('orb_id,amount,aa_id');
    }

    public function orderReceiptBillItem()
    {
        return $this->hasMany('OrderReceiptBillItem', 'orb_id', 'orb_id');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'uid', 'create_uid')->field('eid,uid,ename');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,sno');
    }

    public function employeeReceipt()
    {
        return $this->hasMany('EmployeeReceipt', 'orb_id', 'orb_id');
    }

    public function getContractNoAttr($value,$data){
        $bid_no = str_pad($data['bid'],3,'0',STR_PAD_LEFT);
        $business_no = str_pad($data['orb_id'],8,'0',STR_PAD_LEFT);
        return sprintf("%s-%s",$bid_no,$business_no);
    }

    /**
     * 获取打印数据
     * @param $orb_no
     * @param int $bill_type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function makePrintData($orb_no,$bill_type = 1)
    {
        $bill = $this->where('orb_no|orb_id', $orb_no)->findOrFail();

        $sys_data = $this->getSysData($bill);
        $diy = get_print_vars($bill['bid']);
        $bs_data = $this->getBsData($bill);
        $bm_data = $this->getBmData($bill);
        $item = [];
        if (is_array($bm_data) && isset($bm_data[0])) {
            $item = $bm_data[0];
        }
        if($bill_type == 1) {
            $print_data = [
                'diy' => $diy,
                'sys' => $sys_data,
                'bs' => $bs_data,
                'bm' => $bm_data,
                'item' => $item
            ];
        }else{
            $student_data = get_student_info($bill['sid']);
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

            $op_info['name'] = '';

            $op_info['paid_time']   = $bs_data['pay_date'];

            $w_e['uid'] = $bs_data['create_uid'];
            $employee_info = get_employee_info($w_e);
            if($employee_info){
                $op_info['name'] = $employee_info['ename'];
            }else{
                $user_info = get_user_info($w_e);
                $op_info['name'] = !empty($user_info['name'])?$user_info['name']:$user_info['account'];
            }

            $lesson_price = new \stdClass();

            $w_lp['dtype'] = 1;
            $w_lp['is_delete'] = 0;
            $w_lp['og_id'] = gvar('og_id');

            $lp_list = db('lesson_price_define')->where($w_lp)->select();

            if($lp_list){
                $lesson_price = [];
                $bid = $bill['bid'];
                $com_id = get_dept_id_by_bid($bid);
                foreach($lp_list as $lp){
                    $arr_bids = explode(',',$lp['bids']);
                    $arr_dept_ids = explode(',',$lp['dept_ids']);
                    if(in_array($com_id,$arr_dept_ids) || in_array($bid,$arr_bids)){
                        $lesson_price['d'.$lp['sj_id']] = $lp;
                    }
                }
            }

            $w_lp['dtype'] = 2;

            $lp_list = db('lesson_price_define')->where($w_lp)->select();

            if($lp_list){
                if(is_object($lesson_price)){
                    $lesson_price = [];
                }
                $bid = $bill['bid'];
                $com_id = get_dept_id_by_bid($bid);
                foreach($lp_list as $lp){
                    $arr_bids = explode(',',$lp['bids']);
                    $arr_dept_ids = explode(',',$lp['dept_ids']);
                    if(in_array($com_id,$arr_dept_ids) || in_array($bid,$arr_bids)){
                        $lesson_price['l'.$lp['product_level_did']] = $lp;
                    }
                }
            }

            $order_info = get_order_info($bm_data[0]['oid']);

            $order['order_no'] = $order_info['order_no'];

            $print_data = [
                'diy' => $diy,
                'sys' => $sys_data,
                'order' => $bs_data,
                'student'   => $student_data,
                'items' => $bm_data,
                'item' => $item,
                'op'    => $op_info,
                'lp'    => $lesson_price
            ];
        }

        return $print_data;
    }

    //票据打印数据
    protected function getSysData(OrderReceiptBill $bill)
    {
        $org_name = '';  //TODO
        $branch = Branch::get(['bid' => $bill->bid]);
        return ['org_name' => $org_name, 'branch_name' => $branch->branch_name];
    }

    //票据打印数据
    protected function getBsData(OrderReceiptBill $bill)
    {
        $bs_data = [];

        $student = Student::get(['sid' => $bill->sid]);
        $bs_data['sid']  = $bill->sid;
        $bs_data['student_name'] = $student->student_name;
        $bs_data['card_no'] = $student->card_no;
        $bs_data['sno'] = $student->sno;
        $bs_data['first_tel'] = $student->first_tel;

        $bs_data['pay_date'] = $bill->paid_time;
        $bs_data['create_date'] = $bill->create_time;
        $bs_data['receipt_no'] = $bill->orb_no;
        $bs_data['contract_no'] = $bill->contract_no;
        $user = (new User())->where('uid', $bill->create_uid)->field('name,uid')->find();
        $bs_data['op_name'] = $user['name'];
        $bs_data['origin_amount'] = $this->getBillOriginAmount($bill);
        $bs_data['order_reduce_amount'] = $this->getBillReduceAmount($bill);
        $bs_data['balance_paid_amount'] = $bill->balance_paid_amount;
        $bs_data['pay_amount'] = $bill->amount;
        $bs_data['pay_remain_amount'] = $this->getBillUnpaidAmount($bill);
        $bs_data['pay_amount_b'] = number2chinese($bill->amount, true);
        $bs_data['pay_remark'] = $this->getBillPayRemark($bill);
        $bs_data['account'] = $student->first_tel;

        $bs_data['create_uid'] = $bill->create_uid;
        try {
            $bs_data['qrcode'] = $student->getWechatQrcode();
        } catch (\Exception $e) {
            $bs_data['qrcode'] = '';
        }

        return $bs_data;
    }

    //票据打印数据
    protected function getBmData(OrderReceiptBill $bill)
    {
        $bm_data = [];

        $items = OrderReceiptBillItem::all(['orb_id' => $bill->orb_id]);
        foreach($items as $per_item) {
            $item = OrderItem::get(['oi_id' => $per_item['oi_id']]);
            $tmp = $item->toArray();

            $tmp['year'] = date('Y',$bill->getData('paid_time'));
            $tm['season']  = '';
            if($item->gtype == OrderItem::GTYPE_LESSON) {
                $tmp['lesson_name'] = '';
                $tmp['class_name']  = '';
                if($tmp['lid'] > 0){
                    $lesson_info = get_lesson_info($tmp['lid']);
                    $tmp['lesson_name'] = $lesson_info['lesson_name'];
                    $tmp['season'] = get_season_name($lesson_info['season']);
                    $tmp['year'] = $lesson_info['year'];
                }

                if($tmp['cid'] > 0){
                    $class_info = get_class_info($tmp['cid']);
                    $tmp['class_name'] = $class_info['class_name'];
                    $tmp['season'] = get_season_name($class_info['season']);
                    $tmp['year'] = $class_info['year'];
                }

                if(!$tmp['year'] == 0){
                    $tmp['year'] = date('Y',$bill->getData('paid_time'));
                }
            }elseif($item->gtype == OrderItem::GTYPE_GOODS) {
                $material = $item->material;
                $tmp['lesson_name'] = $material['name'];

            }elseif($item->gtype == OrderItem::GTYPE_PAYITEM){
                $tmp['lesson_name'] = $item->item_name;
            }

            $tmp['unpay_amount'] = $item->getUnpaidAmount($item);
            $tmp['nums_number'] = $tmp['nums'];
            if($tmp['present_lesson_hours'] > 0){
                $tmp['nums'] = sprintf("%s(赠%s)",$tmp['nums'],$tmp['present_lesson_hours']);
            }
            $tmp['price'] = format_currency($tmp['price']);
            $tmp['origin_price']    = format_currency($tmp['origin_price']);

            array_push($bm_data, $tmp);
        }

        return $bm_data;
    }

    //获取收据相关的订单应收合计
    public function getBillOriginAmount($bill)
    {
        $oids = (new OrderReceiptBillItem())->where('orb_id', $bill->orb_id)->column('oid');
        if(empty($oids)) return 0;
        $oids = array_unique($oids);

        $origin_amount = (new Order())->where('oid', 'in', $oids)->sum('origin_amount');
        $discount_amount = (new Order())->where('oid','in',$oids)->sum('order_discount_amount');

        return $origin_amount - $discount_amount;
    }

    public function getBillReduceAmount($bill)
    {
        $oids = (new OrderReceiptBillItem())->where('orb_id', $bill->orb_id)->column('oid');
        if(empty($oids)) return 0;
        $oids = array_unique($oids);

        $reduce_amount = (new Order())->where('oid', 'in', $oids)->sum('order_reduced_amount');
        return $reduce_amount;
    }

    public function getBillPayRemark($bill)
    {
        $oids = (new OrderReceiptBillItem())->where('orb_id', $bill->orb_id)->column('oid');
        if(empty($oids)) return '';
        $oids = array_unique($oids);

       $remark_list = (new Order())->where('oid', 'in', $oids)->column('remark');
       return !empty($remark_list) ? implode(',', $remark_list) : '';
    }

    public function getBillUnpaidAmount($bill)
    {
        $oids = (new OrderReceiptBillItem())->where('orb_id', $bill->orb_id)->column('oid');
        if(empty($oids)) return 0;
        $oids = array_unique($oids);

        if(count($oids) <= 1) {
            $m_order = new Order();
            $order = $m_order->find(array_values($oids));
            if(empty($order)) return 0;
            return abs($order['paid_amount'] - $order['order_amount']);
        } else {
            return $bill['unpaid_amount'] > 0 ? $bill['unpaid_amount'] : $bill['student_money'];
        }
    }

    //创建一个收据
    public function createOneBill($bill_data)
    {
        $this->startTrans();
        try{
            if(!isset($bill_data['amount']) || $bill_data['amount'] < 0) return $this->user_error('创建收据金额不正确');
            $bill_data['orb_no'] = $this->makeOrbNo();

            if(!empty($bill_data['user_receipt_no']) || $bill_data['user_receipt_no'] != ''){
                $bill_data['orb_no'] = $bill_data['user_receipt_no'];
            }
            $rs = (new self())->allowField(true)->isUpdate(false)->save($bill_data);
            if(false === $rs) return $this->user_error('创建收据失败');

            $orb_id = $this->getLastInsID();

            $this->commit();
        } catch(\Exception $e) {
            $this->rollback();
            return $this->user_error(['msg' => $e->getMessage(), 'trace' => $e->getTrace()]);
        }

        return $orb_id;
    }

    public function makeOrbNo()
    {
        $str = strtoupper(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'),0, 3));
        return $str.date('YmdHis');
    }

    public static function getOrbNoByOrbId($orb_id)
    {
        $bill = (new self())->field('orb_no')->find($orb_id);
        if(empty($bill)) return '';

        return isset($bill['orb_no']) ? $bill['orb_no'] : '';
    }

    /*
     * 报废收据:
     *  - 订单
     *  - 订单项目
     *  - student_lesson
     *  - 分班
     *  - 物品
     *  - 订单归属人
     *  - 电子钱包预存
     *  - 收据
     *  - 收款记录
     *  - 归属人业绩
     */
    public function delOneBill($orb_id,$force = false)
    {
        $bill = $this->where('orb_id',$orb_id)->find();
        $rs = $this->canDelBill($orb_id, $bill);
        if($rs === false) return false;

        try {
            $this->startTrans();
            $m_student = new Student();
            $student = $m_student->find($bill['sid']);

            //--1--  处理所有收据项目
            $bill_items = OrderReceiptBillItem::all(['orb_id' => $bill['orb_id']]);
            $oids = array_column($bill_items, 'oid');
            foreach ($bill_items as $bill_item) {
                $rs = $bill_item->delOneBillItem($bill_item['orbi_id'], $bill_item,$force);
                if ($rs === false) throw new FailResult($bill_item->getErrorMsg(), $bill_item->get_error_code());
            }

            //--2-- 处理回款业绩
            $m_er = new EmployeeReceipt();
            $rs = $m_er->where('orb_id', $orb_id)->delete();
            if($rs === false) throw new FailResult('处理回款业绩失败');

            //--3-- 帐户退款
            $m_oph = new OrderPaymentHistory();
            $payment_list = $m_oph->where('orb_id', $orb_id)->where('orb_id', $orb_id)->select();
            $m_tally = new Tally();
            foreach ($payment_list as $payment) {
                /*
                2018-09-19 干掉
                $tally_data['relate_id'] = $orb_id;
                $tally_data['type'] = Tally::TALLY_TYPE_PAYOUT;
                $tally_data['cate'] = Tally::CATE_PAYOUT;
                $tally_data['aa_id'] = $payment['aa_id'];
                $tally_data['amount'] = $payment['amount'];
                $tally_data['remark'] = '报废收据，减少收入';
                $tally_data['sid'] = $student['sid'];
                $rs = $m_tally->createOneTally($tally_data);
                if($rs === false) throw new FailResult($m_tally->getErrorMsg());
                */
                $w_tly = [];
                $w_tly['relate_id'] = $payment['oph_id'];
                $w_tly['type'] = Tally::TALLY_TYPE_INCOME;

                $m_tally = new Tally();
                $tally = $m_tally->where($w_tly)->find();
                if($tally){
                    $result = $tally->delBusinessTally();
                    if(false === $result){
                        throw new FailuResult($tally->getErrorMsg());
                    }
                }

                $payment->delete();
            }

            //--4-- 如果有用电子钱包付款
            if($bill['balance_paid_amount'] > 0) {
                $money_change_data = [
                    'business_type' => StudentMoneyHistory::BUSINESS_TYPE_ADD,
                    'business_id'   => $bill['orb_id'],
                    'money'         => $bill['balance_paid_amount'],
                    'remark'        => '报效收据，退回电子钱包余额部分',
                ];
                $rs = $m_student->changeMoney($student, $money_change_data);
                if($rs === false) throw new FailResult($m_student->getErrorMsg());
            }

            //--5-- 如果有超额预存, 只有__报名缴费__才有超额预存
            if($bill['student_money'] > 0) {

                $money_change_data = [
                    'business_type' => StudentMoneyHistory::BUSINESS_TYPE_DEC,
                    'business_id'   => $bill['orb_id'],
                    'money'         => -$bill['student_money'],
                    'remark'        => '报效收据，减少超额充值电子钱包部分',
                ];
                $rs = $m_student->changeMoney($student, $money_change_data);
                if($rs === false) throw new FailResult($m_student->getErrorMsg());
                $m_order = new Order();
                $m_order->where('oid', 'in', $oids)->setDec('paid_amount', $bill['student_money']);
                $m_order->where('oid', 'in', $oids)->update(['pay_status' => Order::PAY_STATUS_NO]);
            }

            //--6-- 更新订单相关状态
            $m_order = new Order();
            $orders = $m_order->where('oid', 'in', $oids)->select();
            $m_order_item = new OrderItem();
            $is_order_delete = false;
            foreach($orders as $order) {
                $order_item_num = $m_order_item->where('oid', $order['oid'])->count();
                if($order_item_num <= 0) {
                    $rs = $order->delete();
                    if($rs === false) throw new FailResult($order->getErrorMsg());
                    $is_order_delete = true;
                } else {
                    Order::updateRedundantField($order['oid'], $order);
                }
            }

            if($is_order_delete){
                //删除关联的订单赠送课程记录
                $w_slo['oid'] = $order['oid'];
                $w_slo['op_type'] = 4;
                $m_slo = new StudentLessonOperate();

                $result = $m_slo->where($w_slo)->delete(true);      //硬删除
                if(false === $result){
                    throw new FailResult($m_slo->getError());
                }
            }

            //--7-- 删除订单业绩
            $m_op = new OrderPerformance();
            $rs = $m_op->where('oid', 'in', $oids)->delete();
            if($rs === false) throw new FailResult($m_op->getErrorMsg());

            $rs = $bill->delete();
            if($rs === false) throw new FailResult('删除收据失败');

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

    //收据是否可以删除
    public function canDelBill($orb_id, $bill = null)
    {
        if(is_null($bill)) {
            $bill = $this->find($orb_id);
        }
        if(empty($bill)) return $this->user_error('收据不存在');

        if($bill['hw_id'] > 0) return $this->user_error('已经交班');

        $m_orbi = new OrderReceiptBillItem();
        $oi_ids = $m_orbi->where('orb_id', $orb_id)->column('oi_id');
        $orb_ids = $m_orbi->where('oi_id', 'in', $oi_ids)->column('orb_id');
        $orb_ids = array_unique($orb_ids);
        rsort($orb_ids);
        if(array_search($orb_id, $orb_ids) > 0) return $this->user_error('此收据订单后续有收据，作废不了');

        return true;
    }


    public function setIsDemoAttr($value,$data){
        if($value == 1){
            return $value;
        }
        $is_demo = 0;
        if(isset($data['oid']) && !empty($data['oid'])) {
            $order_info = get_order_info($data['oid']);
            $is_demo = !empty($order_info) ? $order_info['is_demo'] : 0;
        }
        return $is_demo;
    }

    /**
     * 订单收据号修改
     */
    public function updateReceipt($orb_id,$input){

        $order = $this->get($orb_id);
        if ($order['user_receipt_no'] == $input['user_receipt_no']){
            return true;
        }

        $legal = $this->is_contract_and_receipt_legal($input['user_receipt_no']);
        if (!$legal) {
            return $this->user_error('收据号不合法');
        }

        $user_receipt_no = $this->is_user_receipt_no_exists($orb_id,$input['user_receipt_no']);

        if ($user_receipt_no){
            return $this->user_error('收据号已存在');
        }

        $w = ['orb_id' => $orb_id];
        $allow_update_filed = ['user_receipt_no'];
        $update = [];
        foreach($allow_update_filed as $field) {
            if (isset($input[$field])) {
                $update['user_receipt_no'] = $input[$field];
                $update['orb_no'] = $input[$field];
            }
        }
        $result = $this->where($w)->update($update);
        return $result;
    }

    /**
     * 用户定义收据号是否存在
     * @param $user_contract_no
     */
    public function is_user_receipt_no_exists($orb_id,$user_receipt_no){
        $w['user_receipt_no|orb_no'] = $user_receipt_no;
        $ex_order = $this->where($w)->find();

        if(!$ex_order){
            return false;
        }

        if($ex_order['orb_id'] == $orb_id){
            return false;
        }

        return true;
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
     * 修改订单收据号收款金额
     * @param $orb_id
     * @param $update_amount
     * @return bool
     */
    public function updatePaidAmount($orb_id,$update_amount)
    {
        $receipt_bill = $this->where('orb_id',$orb_id)->find();
        if (empty($receipt_bill)){
            return $this->user_error('订单收据不存在！');
        }

        $update['amount'] = $receipt_bill['amount'] + $update_amount;
        $w['orb_id'] = $orb_id;
        $result = $this->save($update,$w);
        if (false === $result){
            return $this->sql_save_error('order_receipt_bill');
        }

        return true;
    }

}