<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/8
 * Time: 16:55
 */
namespace app\api\validate;

use think\Validate;

class TimeSection extends Validate
{
    // 验证规则
    protected $rule = [
        ['bid|校区ID', 'require|number'],
        ['season|季节', 'require'],
        ['time_index|时间段序号', 'require|number'],
        ['int_start_hour|开始时间', 'require'],
        ['int_end_hour|结束时间', 'require'],
    ];

    protected $message = [

    ];

    protected $scene = [
    ];
}