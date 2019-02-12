<?php
namespace app\admapi\model;


class WebcallCallLog extends Base
{

    protected $hidden = ['create_time', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function client(){
        return $this->hasOne('Client','cid','cid');
    }


}