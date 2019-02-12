<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/21
 * Time: 16:50
 */

namespace app\api\validate;

use think\Validate;
class StudentExam extends Validate
{
    protected $rule = [
        ['exam_name|考试名称', 'require'],
        ['exam_int_day|考试日期', 'require'],
    ];

    protected $message = [
    ];

    protected $scene = [

    ];


}