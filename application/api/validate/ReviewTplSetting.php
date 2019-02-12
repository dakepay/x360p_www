<?php
/**
 * luo
 */
namespace app\api\validate;

use think\Validate;

class ReviewTplSetting extends Validate
{
    // 验证规则
    protected $rule = [
        ['name|模板名称', 'require'],
    ];

    protected $scene = [

    ];
}