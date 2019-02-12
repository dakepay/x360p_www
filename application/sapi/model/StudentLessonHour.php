<?php
/** 
 * Author: luo
 * Time: 2017-10-26 10:11
**/

namespace app\sapi\model;

class StudentLessonHour extends Base
{

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function lesson()
    {
        return $this->hasOne('Lesson', 'lid', 'lid')->field('lid,lesson_name,short_desc');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

}