<?php
/**
 * Author: luo
 * Time: 2017-12-13 10:35
**/

namespace app\admapi\validate;

use think\Validate;

class Customer extends Validate
{
    // 验证规则
    protected $rule = [
        ['name|客户名称', 'require'],
    ];

    protected $scene = [
    ];

}