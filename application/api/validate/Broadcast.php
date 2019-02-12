<?php
/** 
 * Author: luo
 * Time: 2017-11-23 18:15
**/

namespace app\api\validate;

use think\Validate;

class Broadcast extends Validate
{
    // 验证规则
    protected $rule = [
        ['title|标题', 'require'],
    ];

    protected $message = [
    ];

}