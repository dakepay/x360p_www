<?php
/**
 * luo
 */
namespace app\sapi\validate;

use think\Validate;

class Comment extends Validate
{
    // 验证规则
    protected $rule = [
        ['app_name', 'require'],
        ['app_content_id', 'require|number'],
        ['content', 'require']
    ];

    protected $message = [
    ];



}