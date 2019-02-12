<?php

namespace app\vipapi\model;

use think\Db;

class EmployeeUser extends Base{
	protected $table = 'pro_user';

	public function getAvatarAttr($value,$data){
		if(empty($value)){
			return 'http://s1.xiao360.com/common_img/avatar.jpg';
		}
		return $value;
	}
}