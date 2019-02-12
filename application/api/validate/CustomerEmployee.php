<?php
/** 
 * Author: luo
 * Time: 2017-10-11
**/

namespace app\api\validate;

use think\Validate;

class CustomerEmployee extends Validate
{
    // 验证规则
    protected $rule = [
        ['cu_id|客户ID', 'require|number'],
        ['eid|员工ID', 'require|number'],
    ];

    protected $message = [
    ];

}