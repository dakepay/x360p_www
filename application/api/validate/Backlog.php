<?php
/**
 * luo
 */
namespace app\api\validate;

use think\Validate;

class Backlog extends Validate
{
    // 验证规则
    protected $rule = [
        ['desc|待办事项', 'require'],
    ];

    protected $message = [
    ];



}