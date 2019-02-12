<?php
/**
 * Author: luo
 * Time: 2017-10-18 19:27
**/

namespace app\sapi\model;

class OrderRefund extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,sno');
    }

    public function orderRefundHistory()
    {
        return $this->hasMany('OrderRefundHistory', 'or_id', 'or_id');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'uid', 'create_uid')->field('eid,uid,ename');
    }



}