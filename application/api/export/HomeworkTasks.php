<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\HomeworkTask;


class HomeworkTasks extends Export
{
    protected $res_name = 'homework_task';

    protected $columns = [

        ['field'=>'lid','title'=>'课程','width'=>20],
        ['field'=>'obj','title'=>'作业对象','width'=>40],
        ['field'=>'complete','title'=>'完成情况','width'=>20],
        ['field'=>'eid','title'=>'老师','width'=>20],
        ['field'=>'push_status','title'=>'推送','width'=>20],
        ['field'=>'create_time','title'=>'发布时间','width'=>20],
        ['field'=>'deadline','title'=>'截止日期','width'=>20],
        ['field'=>'remark','title'=>'备注','width'=>20],

    ];

    protected function get_title(){
        $title = '作业服务';
        return $title;
    }

    protected function convert_push($value)
    {
        $map = [0=>'待推送', 1=>'已推送'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function get_obj($lesson_type,$sid,$sids,$cid){
        if($lesson_type==0){
            return '班课 '.get_class_name($cid);
        }else if($lesson_type==1){
            $name = m('student')->where('sid',$sid)->find();
            return '一对一 '.$name->student_name;
        }else if($lesson_type==2){
            $names = m('student')->where('sid','in',$sids)->column('student_name');
            $students = collection($names)->toArray();
            return '一对多 '.implode(' ',$students);
        }
    }

    protected function get_complete($lesson_type,$sid,$sids,$cid,$ht_id){
        if($lesson_type==0){
            $class = m('class')->where('cid',$cid)->find();
            $b = $class->student_nums;
            $a = m('homework_complete')->where('ht_id',$ht_id)->count();
            return $a.'/'.$b;
        }else if($lesson_type==1){
            $a = m('homework_complete')->where('ht_id',$ht_id)->count();
            return $a.'/1';
        }else if($lesson_type==2){
            $b = count($sids);
            $a = m('homework_complete')->where('ht_id',$ht_id)->count();
            return $a.'/'.$b;
        }
    }

    public function get_data()
    {
        $model = new HomeworkTask();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['lid'] = get_lesson_name($v['lid']);
            $list[$k]['eid'] = get_teacher_name($v['eid']);
            $list[$k]['push_status'] = $this->convert_push($v['push_status']);
            $list[$k]['deadline'] = int_day_to_date_str($v['deadline']);
            $list[$k]['obj'] = $this->get_obj($v['lesson_type'],$v['sid'],$v['sids'],$v['cid']);
            $list[$k]['complete'] = $this->get_complete($v['lesson_type'],$v['sid'],$v['sids'],$v['cid'],$v['ht_id']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}