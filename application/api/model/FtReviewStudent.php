<?php
namespace app\api\model;


class FtReviewStudent extends Base
{

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url');
    }


}