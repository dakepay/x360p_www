<?php
/**
 * Author: luo
 * Time: 2017-12-26 20:02
**/

namespace app\sapi\model;

class StudentCreditHistory extends Base
{

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    const TYPE_INC = 1; # 增加积分
    const TYPE_DEC = 2; # 减少积分

    const CATE_STUDY = 1; # 学习积分
    const CATE_CONSUME = 2; # 消费积分

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name');
    }

    public function creditRule()
    {
        return $this->hasOne('CreditRule', 'cru_id', 'cru_id');
    }



}