<?php

namespace app\api\validate;

use think\Validate;
use app\api\model\FranchiseePerson;

class FranchiseePersonValidate extends Validate
{
	protected $rule = [
        ['name|姓名','require'],
        ['mobile|手机号码','require|number'],
        // ['address|通讯地址','require'],
        
    ];

    protected $message = [
        // 'address.require'  =>  '通讯地址必须',
    ];

    protected $scene = [
        
    ];

}