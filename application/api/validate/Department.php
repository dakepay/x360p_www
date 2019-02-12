<?php
/**
 * luo
 */
namespace app\api\validate;

use think\Validate;

class Department extends Validate
{
    // 验证规则
    protected $rule = [
        ['pid|上级部门ID', 'require|number'],
        ['dpt_type|部门类型', 'require|number'],
        ['dpt_name|部门名称', 'require|unique:department,dpt_name^pid'],
    ];

    protected $message = [
        'branch_type.in' => '校区类型必须为 直营 或 加盟',
    ];

}