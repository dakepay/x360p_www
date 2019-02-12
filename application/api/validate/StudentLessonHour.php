<?php
/** 
 * Author: luo
 * Time: 2017-10-26 10:40
**/

namespace app\api\validate;

use think\Validate;

class StudentLessonHour extends Validate
{
    // 验证规则
    protected $rule = [
        ['sid|学生ID', 'require|number'],
    ];

    protected $scene = [

    ];
}