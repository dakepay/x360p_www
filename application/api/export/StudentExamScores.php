<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\StudentExamScore;


class StudentExamScores extends Export
{
    protected $res_name = 'student_exam_score';

    protected $columns = [

        ['field'=>'cid','title'=>'班级','width'=>20],
        ['field'=>'name','title'=>'姓名','width'=>20],
        ['field'=>'number','title'=>'学号','width'=>20],
        ['field'=>'tel','title'=>'电话','width'=>20],
        ['field'=>'total_score','title'=>'成绩','width'=>20],
        ['field'=>'remark','title'=>'备注','width'=>60],
    ];

    protected function get_title(){
        $title = '学员成绩列表';
        return $title;
    }

    protected function get_number($id){
        $number = m('student')->where('sid',$id)->find();
        return $number->sno;
    }

    protected function get_tel($id){
        $tel = m('student')->where('sid',$id)->find();
        return $tel->first_tel;
    }

    public function get_data()
    {
        $model = new StudentExamScore();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['cid'] = get_class_name($v['cid']);
            $list[$k]['name'] = get_student_name($v['sid']);
            $list[$k]['number'] = $this->get_number($v['sid']);
            $list[$k]['tel'] = $this->get_tel($v['sid']);

        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}