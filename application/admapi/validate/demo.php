<?php
/**
 * Author: luo
 * Time: 2017-12-04 17:55
**/

namespace app\admapi\validate;

use think\Validate;

class Demo extends Validate
{
    // 验证规则
    protected $rule = [
        ['name|名称', 'require'],
    ];

    protected $scene = [
    ];

}