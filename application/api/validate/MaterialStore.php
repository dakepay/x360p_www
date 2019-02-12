<?php
/**
 * Author: luo
 * Time: 2017-11-24 12:23
**/

namespace app\api\validate;

use think\Validate;

class MaterialStore extends Validate
{
    // 验证规则
    protected $rule = [
        ['name|仓库名称', 'require'],
    ];

    protected $scene = [

    ];
}