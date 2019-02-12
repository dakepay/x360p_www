<?php
/**
 * Author: luo
 * Time: 2017-12-29 10:57
**/

namespace app\api\model;


class ReviewStudent extends Base
{

//    protected $type = [
//        'detail' => 'json',
//    ];

    public function setDetailAttr($value, $data)
    {
        if ($data['review_style'] == 1){
            $value = json_encode($value);
        }
        return $value;
    }

    public function getDetailAttr($value, $data)
    {
        if ($data['review_style'] == 1){
            $value = json_decode($value,true);
        }
        return $value;
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url');
    }


}