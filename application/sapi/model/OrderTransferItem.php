<?php
/** 
 * Author: luo
 * Time: 2017-10-18 18:26
**/

namespace app\sapi\model;

class OrderTransferItem extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function orderItem()
    {
        return $this->hasOne('OrderItem', 'oi_id', 'oi_id');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'uid', 'create_uid')->field('eid,uid,ename');
    }

}