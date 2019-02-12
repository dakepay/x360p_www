<?php
/**
 * Author: luo
 * Time: 2017-11-23 11:49
**/

namespace app\api\validate;

use think\Validate;
class Tally extends Validate
{
    protected $rule = [
        ['type|进出账类型', 'require'],
        ['aa_id|账户ID', 'require'],
        ['amount|金额', 'require'],
    ];

    protected $message = [
    ];

    protected $scene = [
    ];


}