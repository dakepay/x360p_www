<?php
/**
 * luo
 */
namespace app\api\validate;

use think\Validate;

class Comment extends Validate
{
    // 验证规则
    protected $rule = [
        ['app_name', 'require'],
        ['app_content_id', 'require|number'],
    ];

    protected $message = [
    ];



}