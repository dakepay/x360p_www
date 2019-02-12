<?php

namespace app\vipapi\model;

use think\Db;

class Employee extends Base{
	protected $table = 'pro_employee';

	public function User(){
		return $this->hasOne('EmployeeUser','uid','uid')->field('avatar,mobile,name,email');
	}
}