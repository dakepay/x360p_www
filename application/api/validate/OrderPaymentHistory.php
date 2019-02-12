<?php
/** 
 * Author: luo
 * Time: 2017-10-14 11:07
**/

namespace app\api\validate;

use think\Validate;

class OrderPaymentHistory extends Validate
{
    protected $rule = [
        ['amount|收款金额', 'require|number']
    ];

    protected $scene = [
    ];
}