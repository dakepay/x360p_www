<?php
/**
 * Author: luo
 * Time: 2018/3/22 16:17
 */

namespace app\api\model;


class ActionLog extends Base
{
    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function user()
    {
        return $this->hasOne('User', 'uid', 'uid')->field('uid,account,name');
    }

    public function org()
    {
        return $this->hasOne('Org', 'og_id', 'og_id')->field('og_id,org_name');
    }

}