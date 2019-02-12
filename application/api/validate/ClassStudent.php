<?php
/**
 * Author: luo
 * Time: 2017-10-20 10:39
**/

namespace app\api\validate;

use think\Validate;

class ClassStudent extends Validate
{
    // 验证规则
    protected $rule = [
        ['bid|校区ID', 'require|number'],
        ['cid|班级ID', 'require|number'],
        ['sid|学生ID', 'require|number'],
    ];

    protected $message = [
    ];

    protected $scene = [
    ];
}