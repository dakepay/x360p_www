<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\MakeupArrange;


class MakeupArranges extends Export
{
    protected $res_name = 'makeup_arrange';

    protected $columns = [
        ['field'=>'sid','title'=>'学员姓名','width'=>20],
        ['field'=>'lid','title'=>'课程/班级','width'=>20],
        ['field'=>'int_day','title'=>'上课时间','width'=>20],
        ['field'=>'eid','title'=>'上课老师','width'=>40],
        ['field'=>'status','title'=>'状态','width'=>20],
    ];

    protected function get_title(){
        $title = '补课记录';
        return $title;
    }

    protected function get_status($satt_id){
        $attendance = m('student_attendance')->where('satt_id',$satt_id)->find();
        if($satt_id==0){
            return '待补课考勤';
        }else if($attendance['is_in']==0 && $attendance['is_leave']!=1){
            return '缺课未到';
        }else if($attendance['is_leave']==1){
            return '请假未到';
        }

        return '已补课';
    }

    protected function convert_leave($value)
    {
        $map = [0=>'未请假', 1=>'有请假'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }


    public function get_data()
    {
        $model = new MakeupArrange();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['sid'] = get_student_name($v['sid']);
            $list[$k]['lid'] = get_lesson_name($v['lid']);
            $list[$k]['int_day']        = int_day_to_date_str($v['int_day']);
            $list[$k]['eid'] = get_teacher_name($v['eid']);
            $list[$k]['status'] = $this->get_satt_status($v['satt_id']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}