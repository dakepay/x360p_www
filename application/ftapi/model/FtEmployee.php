<?php
namespace app\ftapi\model;


use think\Exception;
use think\Db;

class FtEmployee extends Base
{

    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function employee(){
        return $this->hasOne('Employee','eid','eid');
    }

}