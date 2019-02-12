<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/30
 * Time: 17:54
 */
namespace app\api\validate;

use think\Validate;

class LessonMaterial extends Validate
{
    protected $rule = [
        ['lid|课程ID', 'require|number'],
        ['mt_id|物品ID', 'require|number'],
        ['default_num|数量', 'number'],
        ['num|数量', 'number'],
    ];

    protected $scene = [
    ];
}