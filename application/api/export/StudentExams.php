<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\StudentExam;


class StudentExams extends Export
{
    protected $res_name = 'student_exam';

    protected $columns = [

        ['field'=>'exam_name','title'=>'名称','width'=>20],
        ['field'=>'exam_type_did','title'=>'类别','width'=>20],
        ['field'=>'exam_int_day','title'=>'考试日期','width'=>20],
        // ['field'=>'exam_subject_dids','title'=>'考试科目','width'=>20],
        ['field'=>'number','title'=>'班级数','width'=>20],
        ['field'=>'total','title'=>'考试人数','width'=>20],
        ['field'=>'maximum','title'=>'最高分','width'=>20],
        ['field'=>'minimum','title'=>'最低分','width'=>20],
        ['field'=>'average','title'=>'平均分','width'=>20],
    ];

    protected function get_title(){
        $title = '成绩查询';
        return $title;
    }

    protected function getdictionaryname($id){
        $dictionary = m('dictionary')->where('did',$id)->find();
        return $dictionary->name;
    }

    protected function gettotal($id){
        return m('student_exam_score')->where('se_id',$id)->count();
    }

    protected function getnumber($id){
        $num =  m('student_exam_score')->where('se_id',$id)->distinct(true)->field('cid')->select();
        return count($num);
    }

    protected function getmaximum($id){
        return m('student_exam_score')->where('se_id',$id)->max('total_score');
    }

    protected function getminimum($id){
        return m('student_exam_score')->where('se_id',$id)->min('total_score');
    }

    protected function getaverage($id){
        return m('student_exam_score')->where('se_id',$id)->avg('total_score');
    }

    public function get_data()
    {
        $model = new StudentExam();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            // $list[$k]['sid'] = get_student_name($v['sid']);
            $list[$k]['exam_type_did'] = $this->getdictionaryname($v['exam_type_did']);
            $list[$k]['exam_int_day'] = int_day_to_date_str($v['exam_int_day']);
            $list[$k]['total'] = $this->gettotal($v['se_id']);
            $list[$k]['maximum'] = $this->getmaximum($v['se_id']);
            $list[$k]['minimum'] = $this->getminimum($v['se_id']);
            $list[$k]['average'] = $this->getaverage($v['se_id']);
            $list[$k]['number'] = $this->getnumber($v['se_id']);

        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}