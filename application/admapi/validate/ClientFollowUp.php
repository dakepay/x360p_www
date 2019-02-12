<?php
/**
 * Author: luo
 * Time: 2017-12-09 15:21
**/

namespace app\admapi\validate;

use think\Validate;

class ClientFollowUp extends Validate
{
    // 验证规则
    protected $rule = [
        ['cid|客户ID', 'require'],
    ];

    protected $scene = [
    ];

}