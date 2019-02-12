<?php
/** 
 * Author: luo
 * Time: 2017-10-11 11:09
**/

namespace app\api\model;

class MobileLoginLog extends Base
{

    public $type = [
        'login_time' => 'timestamp'
    ];

    public function user()
    {
        return $this->hasOne('User', 'uid', 'uid')->field('uid,name');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name');
    }

}