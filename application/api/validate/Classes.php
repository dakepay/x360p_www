<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/21
 * Time: 16:50
 */

namespace app\api\validate;

use think\Validate;
use app\api\model\Classes as ClassesModel;
class Classes extends Validate
{
    protected $rule = [
        ['bid|校区ID', 'require|number'],
        ['class_name|班级名称', 'require'],
        ['sj_id|科目','require'],
        ['lid|课程ID', 'require|number'],
        ['teach_eid|老师ID', 'require|number'],
        ['plan_student_nums|计划招生人数', 'number'],
        ['student_nums|学员人数', 'number'],
        ['lesson_times|上课次数', 'number'],
        ['year|年份', 'number'],
        ['season|季节', 'alpha'],
        ['start_lesson_time|开课时间日期', 'date']
    ];

    protected $message = [
        'lesson_times.require' => '排课次数必须',
        'start_lesson_time.require' => '开课日期必须',
        'end_lesson_time.require' => '结课日期必须'
    ];

    protected $scene = [
        'post' => ['bid', 'class_name', 'sj_id', 'teach_eid'],
        'edit' => ['class_name' => ['require']],
    ];


}