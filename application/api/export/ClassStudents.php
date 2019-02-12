<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/9/6
 * Time: 12:15
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\ClassStudent;

class ClassStudents extends Export
{

    protected $columns = [
        ['title'=>'姓名','field'=>'sid','width'=>20],
        ['title'=>'生日','field'=>'birth_time','width'=>40],
        ['title'=>'状态','field'=>'status','width'=>20],
        ['field'=>'school_grade','title'=>'年级','width'=>20],
        ['title'=>'剩余课时','field'=>'sl_id','width'=>20],
        ['title'=>'余额','field'=>'money','width'=>40],
        ['title'=>'总课时','field'=>'student_lesson_hours','width'=>40],
        ['title'=>'入班日期','field'=>'in_time','width'=>20],
    ];

    protected $cls;

    protected function get_title(){
        $class_name = $this->cls['class_name'];
        $title = $class_name . '班级学员名册';
        return $title;
    }

    protected function convert_status($value)
    {
        $map = [0=>'停课', 1=>'正常', 2=>'转出',9=>'结课'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function get_remain($sl_id){
        $remain = '0';
        if($sl_id > 0){
            $sl_info = get_sl_info($sl_id);
            if($sl_info){
                $remain = $sl_info['remain_lesson_hours'];
            }
        }
        return $remain;
    }

    public function get_birth_time($birth_time)
    {
        if($birth_time == 0){
            return '-';
        }else{
            return date('Y-m-d',$birth_time);
        }
    }

    public function get_data()
    {   
        // print_r($this->params);exit;

        $model = new ClassStudent();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        // echo $model->getLastSql();exit;\
        foreach ($list as $k => $v) {
            $list[$k]['sid'] = get_student_name($v['sid']);
            $student_info = get_student_info($v['sid']);
            $list[$k]['birth_time'] = $this->get_birth_time($student_info['birth_time']);
            $list[$k]['school_grade'] = get_grade_name($student_info['school_grade']);
            $list[$k]['status'] = $this->convert_status($v['status']);
            $list[$k]['sl_id'] = $this->get_remain($v['sl_id']);
            $list[$k]['money'] = $student_info['money'];
            $list[$k]['student_lesson_hours'] = $student_info['student_lesson_hours'];
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];


    }
}