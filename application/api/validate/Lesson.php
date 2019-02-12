<?php
/**
 * luo
 */
namespace app\api\validate;

use think\Validate;

class Lesson extends Validate
{
    // 验证规则
    protected $rule = [
        ['lesson_name|课程名称', 'require'],
        //['ability|课程能力属性', 'require|array'],
        //['fit_age|适合年龄段', 'require|array'],
        //['fit_grade|适合年级段', 'require|array'],
        //['short_desc|简短介绍', 'require'],
        //['public_content|宣传介绍', 'require'],
        ['chapter_nums|章节数量', 'require'],
        ['unit_price|课时单价', 'float'],
        ['unit_lesson_minutes|单节课时长(分钟)', 'require|float'],
        ['unit_lesson_hours|单节课扣课时数量', 'require'],
        ['sale_price|课程售价', 'require|float'],
    ];

    protected $scene = [

    ];
}