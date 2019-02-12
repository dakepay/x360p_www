<?php
/**
 * Author: luo
 * Time: 2017-10-24 10:00
**/


namespace app\api\validate;

use think\Validate;

class ClassAttendance extends Validate
{
    protected $rule = [
        ['ca_id|排课ID', 'require|number'],
        ['int_day|上课日期', 'require|number'],
        ['class_student_nums|班级人数', 'require|number']
    ];

    protected $scene = [
    ];
}