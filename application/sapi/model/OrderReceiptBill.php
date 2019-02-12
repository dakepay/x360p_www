<?php
/** 
 * Author: luo
 * Time: 2017-11-03 17:39
**/

namespace app\sapi\model;

use think\Exception;

class OrderReceiptBill extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

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

}