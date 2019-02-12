<?php
/**
 * Author: luo
 * Time: 2018/6/30 12:11
 */

namespace app\sapi\model;


class MakeupArrange extends Base
{
    const MAKEUP_TYPE_INSERT_COURSE = 0; # 跟班补课
    const MAKEUP_TYPE_NEW_COURSE = 1; # 开课补课

    //protected $append = ['absence'];
    //
    //public function getAbsenceAttr($value,$data){
    //    if(!isset($data['sa_id']) && !isset($data['slv_id'])){
    //        return null;
    //    }
    //    $absence = ['leave'=>null,'absence'=>''];
    //
    //    if($data['slv_id'] > 0){
    //        $slv_info = get_slv_info($data['slv_id']);
    //        $absence['leave'] = $slv_info;
    //    }
    //    if($data['sa_id'] > 0){
    //        $sa_info = get_sa_info($data['sa_id']);
    //        $absence['absence'] = $sa_info;
    //    }
    //
    //    return $absence;
    //}

    public function courseArrange()
    {
        return $this->belongsTo('CourseArrange', 'ca_id', 'ca_id');
    }

    public function oneClass()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }

    public function absence()
    {
        return $this->belongsTo('StudentAbsence', 'sa_id', 'sa_id');
    }

    public function leave()
    {
        return $this->belongsTo('StudentLeave', 'slv_id', 'slv_id');
    }

}