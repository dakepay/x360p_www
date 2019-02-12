<?php
namespace app\sapi\validate;

use think\Validate;

class Advice extends Validate
{
	// 验证规则
    protected $rule = [
        ['sid|学生id', 'require'],
        ['content|内容', 'require'],
    ];

    protected $scene = [
    ];

}