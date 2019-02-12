<?php
/**
 * Author: luo
 * Time: 2017-12-04 17:55
**/

namespace app\admapi\validate;

use think\Validate;

class Client extends Validate
{
    // 验证规则
    protected $rule = [
        ['client_name|客户名称', 'require'],
        ['host|二级域名', 'unique:client|length:2,12'],
        ['expire_day|到期日', 'date']
    ];

    protected $scene = [
        'renew' => ['expire_day' => 'require|date'],
    ];

}