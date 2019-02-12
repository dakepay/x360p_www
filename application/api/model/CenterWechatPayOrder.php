<?php
/** 
 * Author: luo
 * Time: 2018-02-28 10:50
**/

namespace app\api\model;

class CenterWechatPayOrder extends Base
{
    protected $connection = 'db_center';

    const STATUS_DEFAULT = 0; # 默认状态，初始下单
    const STATUS_SUCCESS = 1; # 支付成功
    const STATUS_FAIL = 2; # 支付失败

    protected $skip_og_id_condition = true;
}