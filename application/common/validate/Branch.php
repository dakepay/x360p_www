<?php
/**
 * luo
 */
namespace app\common\validate;

use think\Validate;

class Branch extends Validate
{
    // 验证规则
    protected $rule = [
        ['branch_name|校区名称', 'require|unique:branch,branch_name^big_area_id'],
        ['branch_type|校区类型', 'in:1,2'],
    ];

    protected $message = [
        'branch_type.in' => '校区类型必须为 直营 或 加盟',
    ];



}