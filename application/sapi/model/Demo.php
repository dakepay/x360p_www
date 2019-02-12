<?php
/** 
 * Author: luo
 * Time: 2017-10-11 11:09
**/

namespace app\sapi\model;
use think\Exception;
use app\common\exception\FailResult;

class Demo extends Base
{
    protected $readonly = ['lid'];

    protected $insert = ['on_time'];

    protected $type = [
        'on_time' => 'timestamp:Y-m-d',
    ];
    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];



}