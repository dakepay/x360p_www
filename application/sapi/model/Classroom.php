<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/8
 * Time: 16:06
 */
namespace app\sapi\model;

class Classroom extends Base
{
    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];
    protected $type = [
        'seat_config' => 'array',
    ];


}