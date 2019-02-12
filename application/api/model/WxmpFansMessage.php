<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2018/1/30
 * Time: 16:03
 */

namespace app\api\model;

class WxmpFansMessage extends Base
{
    protected $type = [
        'data_json' => 'json',
        'files_info' => 'json',
    ];

    protected $skip_og_id_condition = true;
}