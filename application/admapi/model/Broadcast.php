<?php

namespace app\admapi\model;

class Broadcast extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function setDptIdsAttr($value)
    {
        is_array($value) && $value = implode(',', $value);

        return $value;
    }

    public function  getDptIdsAttr($value)
    {
        return split_int_array($value);
    }

    public function user()
    {
        return $this->hasOne('User', 'uid', 'create_uid')->field('uid,name');
    }
    public function updateBroadcast($input){
        if(empty($input['bc_id'])){
            return $this->user_error('bc_id error!');
        }
        $area = $this->get($input['bc_id']);
        if(empty($area)){
            return $this->user_error('公告不存在!');
        }
        $update_area['bc_id'] = $input['bc_id'];

        $update = [];
        $update['type'] = $input['type'];
        $update['title'] = $input['title'];
        $update['desc'] = $input['desc'];
        $update['update_time'] = int_day(time());


        $rs = $this->save($update,$update_area);

        if(false === $rs){
            return $this->sql_save_error('Broadcast');
        }
        return true;
    }


}