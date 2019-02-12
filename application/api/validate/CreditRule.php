<?php
/** 
 * Author: luo
 * Time: 2018-06-14 15:04
**/

namespace app\api\validate;

use think\Validate;

class CreditRule extends Validate
{
    protected $rule = [
        ['type|操作类型', 'require|number'],
        ['credit|积分', 'require'],
    ];

    protected $scene = [
    ];
}