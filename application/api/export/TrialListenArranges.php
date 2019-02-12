<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\TrialListenArrange;

class TrialListenArranges extends Export
{
    protected $res_name = 'trial_listen_arrange';

    protected $columns = [
        ['field'=>'name','title'=>'学员姓名','width'=>20],
        ['field'=>'cid','title'=>'班级名称','width'=>20],
        ['field'=>'listen_type','title'=>'听课类型','width'=>20],
        ['field'=>'eid','title'=>'上课老师','width'=>20],
        ['field'=>'classroom','title'=>'上课教室','width'=>20],
        ['field'=>'int_day','title'=>'试听日期','width'=>20],

        ['field'=>'int_hour','title'=>'上课时间','width'=>20],
        ['field'=>'status','title'=>'试听状态','width'=>20],
    ];

    protected function get_title(){
        $title = '试听记录';
        return $title;
    }

    protected function get_customer_name($sid,$cu_id){
        if($sid==0){
            $type = '意向学员';
            $name = m('customer')->where('cu_id',$cu_id)->find();
            return $type.' '.$name->name;
        }else if($cu_id==0){
            $type = '正式学员';
            $name = m('student')->where('sid',$sid)->find();
            return $type.' '.$name->student_name;
        }
    }

    protected function convert_type($id){
        if($id==0){
            return '跟班试听';
        }else if($id==1){
            return '排班试听';
        }
    }

    protected function convert_hour($start,$end){
        return int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end);
    }

    protected function get_listen_status($is_attendance,$attendance_status){
        if($is_attendance==1){
            if($attendance_status==1){
                return '已试听';
            }else{
                return '缺勤';
            }
        }else{
            return '待试听';
        }
    }

    protected function convert_class_name($cid,$ca_id){
        if($cid==0){
            $name = m('course_arrange')->where('ca_id',$ca_id)->find();
            return $name->name;
        }else{
            return get_class_name($cid);
        }
    }

    protected function find_class_room($ca_id){
        $cr = m('course_arrange')->where('ca_id',$ca_id)->find();
        $name = m('classroom')->where('cr_id',$cr['cr_id'])->find();
        return $name->room_name;
    }

    public function get_data()
    {
        $model = new TrialListenArrange();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {

            $list[$k]['name'] = $this->get_customer_name($v['sid'],$v['cu_id']);
            $list[$k]['cid'] = $this->convert_class_name($v['cid'],$v['ca_id']);

            $list[$k]['listen_type'] = $this->convert_type($v['listen_type']);
            $list[$k]['eid'] = get_teacher_name($v['eid']);

            $list[$k]['int_day'] = int_day_to_date_str($v['int_day']);

            $list[$k]['int_hour'] = $this->convert_hour($v['int_start_hour'],$v['int_end_hour']);

            $list[$k]['status'] = $this->get_listen_status($v['is_attendance'],$v['attendance_status']);

            $list[$k]['classroom'] = $this->find_class_room($v['ca_id']);


        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];
    }
}