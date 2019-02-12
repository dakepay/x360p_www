<?php
/**
 * Author: luo
 * Time: 2017-10-30 15:40
**/

namespace app\api\validate;

use think\Validate;
class InputTemplate extends Validate
{
    protected $rule = [
        ['name|模板名称', 'require'],
        ['template|模板内容', 'require'],
    ];

    protected $message = [
    ];

    protected $scene = [
    ];


}