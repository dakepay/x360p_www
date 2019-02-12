<?php
/** 
 * Author: luo
 * Time: 2017-10-11
**/


namespace app\api\validate;

use think\Validate;

class Customer extends Validate
{
    // 验证规则
    protected $rule = [
        ['name|学员姓名', 'require'],
        ['sex|性别', 'in:0,1,2']
    ];

    protected $message = [
    ];

}