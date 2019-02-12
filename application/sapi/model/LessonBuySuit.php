<?php
/**
 * Author: luo
 * Time: 2018/6/4 17:19
 */

namespace app\sapi\model;


class LessonBuySuit extends Base
{
    protected $type = ['define' => 'json'];
    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url,first_tel');
    }

    public function customer()
    {
        return $this->hasOne('Customer', 'cu_id', 'cu_id')->field('cu_id,name,first_tel');
    }


}