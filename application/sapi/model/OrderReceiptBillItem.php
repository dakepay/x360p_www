<?php
/**
 * Author: luo
 * Time: 2017-11-03 17:42
**/

namespace app\sapi\model;

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


}