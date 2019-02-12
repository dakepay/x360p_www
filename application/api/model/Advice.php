<?php
/** 
 * Author: luo
 * Time: 2018-01-15 12:28
**/

namespace app\api\model;

class Advice extends Base
{
    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name');
    }

}