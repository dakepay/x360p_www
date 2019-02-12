<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/30
 * Time: 17:53
 */

namespace app\api\model;

class Goods extends Base
{
    protected $readonly = ['lid'];

    protected $insert = ['on_time'];

    protected $type = [
        'on_time' => 'timestamp:Y-m-d',
        'off_time' => 'timestamp:Y-m-d',
        'order_limit_time' => 'timestamp:Y-m-d',
    ];
    protected $hidden = ['on_time', 'off_time', 'create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function setOnTimeAttr($value)
    {
        return time();
    }

    public function lesson()
    {
        return $this->belongsTo('Lesson', 'lid', 'lid');
    }
}