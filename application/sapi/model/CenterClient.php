<?php
namespace app\sapi\model;

class CenterClient extends Center
{
	protected $table = 'pro_client';

	protected $hidden = ['account_price','add_renew_amount','branch_num_limit','contact','eid','init_amount','init_renew_amount','is_init_pay','is_org_open','tel','student_num_limit'];
}