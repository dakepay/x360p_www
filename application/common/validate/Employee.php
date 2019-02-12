<?php
/**
 * luo
 */
namespace app\common\validate;

use think\Validate;

class Employee extends Validate
{
    // 验证规则
    protected $rule = [
        ['ename|员工姓名', 'require'],
        //['bids|员工所属校区', 'require|array'],
        ['rids|员工所属角色', 'array'],
        ['sex|性别', 'in:0,1,2'],
        ['mobile|手机号码', 'unique:employee'],
        ['email|邮箱地址', 'email|unique:employee'],
        ['birth_time|出生日期', 'date'],
    ];

    protected $message = [

    ];

    protected $scene = [

    ];

}