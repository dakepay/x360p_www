<?php
/**
 * Author: luo
 * Time: 2017-12-26 14:36
**/

namespace app\sapi\model;

class Advice extends Base
{
    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function adviceReply()
    {
        return $this->hasMany('AdviceReply', 'aid', 'aid');
    }

}