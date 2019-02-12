<?php

namespace app\api\validate;

use think\Validate;
use app\api\model\Franchisee;

class FranchiseeValidate extends Validate
{
	protected $rule = [
        'org_name|加盟商名称' => 'require|min:4',
        'status|运营状态' => 'require',
        'org_address|机构地址' => 'require',
        // 'business_license|营业执照号' => 'require|min:4',
    ];

    protected $message = [
        
        
    ];

    protected $scene = [
        
    ];

}