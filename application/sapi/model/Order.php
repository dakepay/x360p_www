<?php
/** 
 * Author: luo
 * Time: 2017-10-14 10:28
**/

namespace app\sapi\model;

use app\common\exception\FailResult;
use app\common\Wechat;
use think\Exception;

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
        $data['order_reduced_amount'] = isset($data['order_reduced_amount']) ? $data['order_reduced_amount'] : 0;
        $data['order_discount_amount'] = isset($data['order_discount_amount']) ? $data['order_discount_amount'] : 0;
        $value = isset($data['origin_amount']) ? $data['origin_amount'] - $data['order_discount_amount'] - $data['order_reduced_amount'] : 0;
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
        return $value ? strtotime($value) : $value;
    }

    protected function getPaidTimeAttr($value)
    {
        return $value ? date('Y-m-d', $value) : 0;
    }

    protected function setPayStatusAttr($value, $data)
    {
        $order_amount = isset($data['order_amount']) ? $data['order_amount'] : 0;
        $paid_amount = isset($data['paid_amount']) ? $data['paid_amount'] : 0;

        if($paid_amount == 0) return self::PAY_STATUS_NO;
        if($paid_amount >= $order_amount) return self::PAY_STATUS_ALL;

        return self::PAY_STATUS_SOME;
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

    public function invoice()
    {
        return $this->hasOne('InvoiceApply', 'oid', 'oid');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')
            ->field('sid,student_name,money,sex,photo_url,birth_time,birth_year');
    }

    public function employee()
    {
        return $this->hasMany('OrderPerformance', 'eid', 'oid');
    }


}