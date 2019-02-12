<?php

namespace app\ftapi\model;

class UserBase extends Base
{
    protected $name = 'user';

    protected $hidden = ['salt', 'password', 'is_admin'];

    public function employee()
    {
        return $this->hasOne('Employee','uid','uid','LEFT');
    }
}
