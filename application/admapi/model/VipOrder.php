<?php
namespace app\admapi\model;

class VipOrder extends Base{
    const CREAT_FROM_USER = 0;
    const CREAT_FROM_ADMIN = 1;

	protected $hidden = ['is_delete', 'delete_time', 'delete_uid'];
	public function client(){
		return $this->hasOne('Client', 'cid', 'cid');
	}
}