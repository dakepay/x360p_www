<?php

namespace app\api\validate;

use think\Validate;

class FranchiseeContractValidate extends Validate
{
	protected $rule = [
        ['org_name|加盟商名称','require'],
        ['org_address|机构地址','require'],
        // ['decorate|装修费用','float'],
        ['sale_eid|销售员工ID','require|number'],
        ['service_eid|督导员工ID','require|number'],
        
    ];

    protected $message = [
        
        
    ];

    protected $scene = [
        
    ];
}