<?php
/**
 * Author: luo
 * Time: 2018/6/30 10:58
 */

namespace app\sapi\model;


class StudentAbsence extends Base
{

    const STATUS_UNARRANGE = 0;/*未安排*/
    const STATUS_ARRANGED  = 1;/*已安排*/
    const STATUS_CLOSE     = 2;/*已补课结束*/

    public function courseArrange()
    {
        return $this->belongsTo('CourseArrange', 'ca_id', 'ca_id');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

}