<?php
/**
 * Author: luo
 * Time: 2017-12-13 10:56
**/

namespace app\admapi\validate;

use think\Validate;

class CustomerFollowUp extends Validate
{
    // 验证规则
    protected $rule = [
        ['cu_id|客户ID', 'require'],
    ];

    protected $scene = [
    ];

}