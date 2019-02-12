<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportStudentLessonClass;
use think\Request;

class StudentByBids extends Export
{
    protected $res_name = 'report_student_lesson_class';

    protected $columns = [

       ['field'=>'school_id','title'=>'学校名称','width'=>20],
       ['field'=>'student_num','title'=>'学生人数','width'=>20],


    ];

    protected function get_title(){
    	$title = '学生分布表';
    	return $title;
    }

    protected function get_student_num($school_id){
        $number =  m('report_student_lesson_class')->where('school_id',$school_id)->count();
        return $number;
    }

    public function get_data(){

        // $bids = request()->header('x-bid');
        // $this->params['bid'] = ['in',$bids];
        unset($this->params['bid']); 
        // print_r($this->params);exit;
        $fields = ['school_id']; 
        $model = new ReportStudentLessonClass; 
        $data = $model->field($fields)->group('school_id')->getSearchResult($this->params,[],false);

        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['student_num'] = $this->get_student_num($v['school_id']);
            $data['list'][$k]['school_id'] = get_school_name($v['school_id']);
        }

        if (!empty($data['list'])) {
            return collection($data['list'])->toArray();
        }
        return [];
    }


}