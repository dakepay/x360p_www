<?php
/** 
 * Author: luo
 * Time: 2017-10-14 11:07
**/

namespace app\api\validate;

class OrderItem extends Base
{

    protected $validate_class_name = '订单下面的项目';

    protected $rule = [
        ['oid|订单id', 'require|number'],
        ['sid|学生id', 'require|number'],
        ['nums|商品数量', 'require|number'],
    ];

    protected $scene = [
    ];

}