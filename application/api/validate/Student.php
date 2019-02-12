<?php
/**
 * Author: luo
 * Time: 2017-11-01 09:04
**/

namespace app\api\validate;

use think\Validate;

class Student extends Validate
{
    // 验证规则
    protected $rule = [
        ['student_name|学生姓名', 'require'],
        ['first_tel|联系电话', 'require|unique:student,first_tel^student_name'],
        ['second_tel|第二联系人电话', 'different:first_tel', '第二联系人电话与第一联系人电话不能重复'],
        ['card_no|考勤卡号', 'unique:student'],
    ];

    protected $scene = [
        'edit' => ['card_no'],
    ];
}