<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/21
 * Time: 10:21
 */

namespace app\api\validate;

use think\Validate;

class Role extends Validate
{
    // 验证规则
    protected $rule = [
        ['role_name|角色名称', 'require|unique:role'],
        ['role_desc|角色描述', 'require'],
    ];

    protected $scene = [
    ];
}