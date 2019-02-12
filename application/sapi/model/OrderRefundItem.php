<?php
/** 
 * Author: luo
 * Time: 2017-10-18 19:39
**/

namespace app\sapi\model;

class OrderRefundItem extends Base
{

    public function orderItem()
    {
        return $this->hasOne('OrderItem', 'oi_id', 'oi_id');
    }

    public function orderRefund()
    {
        return $this->hasOne('OrderRefund', 'or_id', 'or_id');
    }

}