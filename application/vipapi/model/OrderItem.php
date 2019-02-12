<?php
namespace app\vipapi\model;

class OrderItem extends Base{
	public function goods(){
		return $this->hasOne('Goods','vg_id','gid');
	}
}