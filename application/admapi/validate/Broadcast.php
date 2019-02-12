<?php
/**
 * Author: luo
 * Time: 2018-01-23 11:24
**/

namespace app\admapi\validate;

use think\Validate;

class Broadcast extends Validate
{
    // 验证规则
    protected $rule = [
        ['title|标题', 'require'],
        ['desc|描述', 'require'],
    ];

    protected $scene = [
    ];

}