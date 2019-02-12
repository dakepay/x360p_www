<?php
/** 
 * Author: luo
 * Time: 2017-12-13 17:50
**/

namespace app\admapi\validate;

use think\Validate;

class Holiday extends Validate
{
    // 验证规则
    protected $rule = [
        ['name|名称', 'require'],
        ['day|放假日期', 'require|number'],
        ['year|年份', 'require|number'],
    ];

    protected $scene = [
    ];

}