<?php
/** 
 * Author: luo
 * Time: 2017-10-12 09:24
**/

namespace app\api\validate;

use think\Validate;

class CourseArrange extends Validate
{
    protected $rule = [
        ['lid|课程ID', 'require|number'],
        ['teach_eid|老师ID', 'require|number'],
        ['cr_id|教室ID', 'number'],
        ['int_day|上课日期', 'require'],
        ['int_start_hour|上课开始时间', 'require'],
        ['int_end_hour|上课结束时间', 'require'],
        ['name|排课名称','requireIf:is_trial,1']
    ];

    protected $message = [

    ];

    protected $scene = [

    ];
}