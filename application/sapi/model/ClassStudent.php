<?php
/**
 * Author: luo
 * Time: 2017-12-22 10:14
**/

namespace app\sapi\model;

use app\common\exception\FailResult;
use think\Db;
use think\Exception;

class ClassStudent extends Base
{
    const STATUS_NORMAL = 1;    /*正常*/
    const STATUS_STOP   = 0;    /*停课*/
    const STATUS_CLASS_TRANSFER = 2;/*转班*/
    const STATUS_CLOSE = 9;     /*已结课*/

    public $in_way = ['order' => 1, 'assign' => 2, 'dss' => 3];

    protected $type = [
        'in_time' => 'timestamp',
    ];

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid');
    }

}