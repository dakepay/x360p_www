<?php
/**
 * Author: luo
 * Time: 2017-11-24 12:23
**/

namespace app\api\validate;

use think\Validate;

class MaterialHistory extends Validate
{
    // 验证规则
    protected $rule = [
        ['num|数量', 'require|egt:1'],
        ['ms_id|仓库ID', 'require|number'],
        ['mt_id|物品ID', 'require|number'],
        ['type|进出库类型', 'require|number'],
    ];

    protected $scene = [

    ];
}