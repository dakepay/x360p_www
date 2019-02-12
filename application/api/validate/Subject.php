<?php
/**
 * luo
 */
namespace app\api\validate;

use think\Validate;

class Subject extends Validate
{
    // 验证规则
    protected $rule = [
        ['subject_name|科目名称', 'require'],
    ];

    protected $message = [

    ];

    protected $scene = [

    ];
}