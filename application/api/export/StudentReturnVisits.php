<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\StudentReturnVisit;


class StudentReturnVisits extends Export
{
    protected $res_name = 'student_return_visit';

    protected $columns = [

        ['field'=>'sid','title'=>'姓名','width'=>20],
        ['field'=>'is_connect','title'=>'是否有效','width'=>20],
        ['field'=>'followup_did','title'=>'回访方式','width'=>20],
        ['field'=>'eid','title'=>'回访人','width'=>20],
        ['field'=>'int_day','title'=>'回访日期','width'=>20],
        ['field'=>'content','title'=>'回访内容','width'=>60],
    ];

    protected function convert_connect($id){
        if($id==0){
            return '无效';
        }else if($id==1){
            return '有效';
        }
    }

    protected function convert_did($id){
        $dictionary = m('dictionary')->where('did',$id)->find();
        if($dictionary){
            return $dictionary->name;
        }
        return '-';
    }

    protected function get_title(){
        $title = '学员回访';
        return $title;
    }

    public function get_data()
    {
        $model = new StudentReturnVisit();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['eid'] = get_teacher_name($v['eid']);
            $list[$k]['sid'] = get_student_name($v['sid']);
            $list[$k]['is_connect'] = $this->convert_connect($v['is_connect']);
            $list[$k]['int_day'] = int_day_to_date_str($v['int_day']);
            $list[$k]['followup_did'] = $this->convert_did($v['followup_did']);

        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}