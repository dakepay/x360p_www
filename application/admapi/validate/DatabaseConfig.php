<?php
/**
 * Author: luo
 * Time: 2017-12-06 19:31
**/

namespace app\admapi\validate;

use think\Validate;

class DatabaseConfig extends Validate
{
    // 验证规则
    protected $rule = [
        ['cid|客户ID', 'require|number'],
        ['host|二级域名', 'require|unique:client'],
        ['hostname|数据库地址', 'require'],
        ['database|数据库名', 'require'],
        ['username|数据库用户名', 'require'],
        ['password|数据库密码', 'require'],
    ];

    protected $message = [
    ];

}