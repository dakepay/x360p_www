<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/8
 * Time: 16:00
 */
namespace app\api\validate;

use think\Validate;

class Classroom extends Validate
{
    // 验证规则
    protected $rule = [
        ['bid|校区ID', 'require|number'],
        ['room_name|教室名称', 'require'],
        ['seat_nums|教室座位数', 'require|number'],
        ['trial_listen_nums_limit|试听最多允许人数', 'number'],
    ];

    protected $message = [

    ];

    protected $scene = [

    ];
}