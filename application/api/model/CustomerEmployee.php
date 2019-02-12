<?php
/** 
 * Author: luo
 * Time: 2017-10-11 11:09
**/

namespace app\api\model;

class CustomerEmployee extends Base
{
    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    //public function createCustomerEmployee($data) {
    //    $rs = $this->allowField(true)->save($data);
    //    return $rs;
    //}

}