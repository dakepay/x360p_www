<?php

namespace app\ftapi\model;

class Classroom extends Base
{
    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];
    protected $type = [
        'seat_config' => 'array',
    ];


}