<?php
/**
 * Author: luo
 * Time: 2018-04-10 11:15
**/

namespace app\api\validate;

use think\Validate;

class StudentExamScore extends Validate
{
    // 验证规则
    protected $rule = [
        ['se_id|考试ID', 'require|number'],
        ['sid', 'require|number'],
    ];

    protected $message = [
    ];

    protected $scene = [
    ];

}