<?php
/** 
 * Author: luo
 * Time: 2017-10-14 11:07
**/

namespace app\api\validate;

use think\Validate;

class Order extends Base
{
    protected $rule = [
        ['order_no|订单编号', 'require']
    ];

    protected $scene = [
    ];
}