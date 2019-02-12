<?php
/**
 * Author: luo
 * Time: 2018/3/29 11:49
 */

namespace app\sapi\model;


class HomeworkReply extends Base
{

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function homeworkAttachment()
    {
        return $this->hasMany('HomeworkAttachment', 'hr_id', 'hr_id');
    }

}