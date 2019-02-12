<?php
/** 
 * Author: luo
 * Time: 2017-10-18 15:02
**/


namespace app\sapi\model;

class StudentMoneyHistory extends Base
{

    const BUSINESS_TYPE_TRANSFORM = 1;  //结转
    const BUSINESS_TYPE_REFUND = 2;     //退款
    const BUSINESS_TYPE_RECHARGE = 3;   //充值
    const BUSINESS_TYPE_ORDER = 4;      //下单
    const BUSINESS_TYPE_SUPPLEMENT = 5; //订单续费

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

}