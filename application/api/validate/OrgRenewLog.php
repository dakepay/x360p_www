<?php
/**
 * Author: luo
 * Time: 2017-12-14 11:41
**/

namespace app\admapi\validate;

use think\Validate;

class OrgRenewLog extends Validate
{
    // 验证规则
    protected $rule = [
        ['og_id|加盟商ID', 'require'],
        ['new_day|新的到期时间', 'require'],
    ];

    protected $scene = [
    ];

}