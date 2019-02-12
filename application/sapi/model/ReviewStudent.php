<?php
/**
 * Author: luo
 * Time: 2018-01-04 15:00
**/

namespace app\sapi\model;

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


    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function review()
    {
        return $this->hasOne('Review', 'rvw_id', 'rvw_id');
    }

    public function lesson()
    {
        return $this->hasOne('Lesson', 'lid', 'lid')->field('lid,lesson_name,short_desc');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'eid', 'eid')->field('eid,ename,photo_url');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid');
    }



}