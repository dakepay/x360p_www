<?php

namespace app\ftapi\model;


class Customer extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];
    protected $type = [
        'next_follow_time' => 'timestamp',
        'last_follow_time' => 'timestamp',
    ];

}