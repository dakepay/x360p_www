<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\StudentLessonHour;


class StudentLessonHours extends Export
{
    protected $res_name = 'student_lesson_hour';

    protected $columns = [
        ['field'=>'sid','title'=>'学员姓名','width'=>20],
        ['field'=>'lid','title'=>'课程','width'=>20],
        ['field'=>'cid','title'=>'班级','width'=>20],
        ['field'=>'grade','title'=>'年级','width'=>20],
        ['field'=>'cr_id','title'=>'教室','width'=>20],
        
        ['field'=>'sj_id','title'=>'科目','width'=>20],
        ['field'=>'int_day','title'=>'日期','width'=>20],

        ['field'=>'week','title'=>'星期','width'=>20],
        ['field'=>'section','title'=>'时段','width'=>20],

        // ['field'=>'section_time','title'=>'考勤时段','width'=>20],

        ['field'=>'lesson_hours','title'=>'课时数','width'=>20],
        ['field'=>'status','title'=>'扣课时状态','width'=>20],
        ['field'=>'lesson_amount','title'=>'课时金额','width'=>20],
        ['field'=>'desc','title'=>'课消说明','width'=>60],

        ['field'=>'lesson_type','title'=>'课程类型','width'=>20],
        ['field'=>'eid','title'=>'老师','width'=>20],
        ['field'=>'lesson_minutes','title'=>'课时长(分钟)','width'=>20],

    ];

    protected function get_title(){
        $title = '课耗管理表';
        return $title;
    }

    protected function get_columns(){
        $arr = $this->columns;
        if(!user_config('params.enable_grade')){
            unset($arr[3]);
            $arr = array_values($arr);
        }
        return $arr;
    }

    protected function convert_status($is_pay){
        if($is_pay==0){
            return '未扣';
        }else if($is_pay==1){
            return '已扣';
        }
    }

    protected function convert_type($value)
    {
        $map = [0=>'班课', 1=>'1对1', 2=>'1对多' ,3=>'研学旅行团'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function get_desc($type,$start,$end,$day,$cid,$eid){
        if($type==1){
            return '考勤课消:星期'.int_day_to_week($day).' '.int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end).'/'.get_class_name($cid).'/'.get_teacher_name($eid);
        }else if($type==2){
            return '登记课消:星期'.int_day_to_week($day).' '.int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end).'/'.get_class_name($cid).'/'.get_teacher_name($eid);
        }
    }

    protected function get_section_time($start,$end,$day){
        return '星期'.int_day_to_week($day).' '.int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end);
    }

    protected function get_week($day){
        return '星期'.int_day_to_week($day);
    }

    protected function get_section($start,$end){
        return int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end);
    }

    public function get_data()
    {
        $model = new StudentLessonHour();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['status'] = $this->convert_status($v['is_pay']);
            $list[$k]['sj_id']   = get_subject_name($v['sj_id']);
            $list[$k]['int_day']   = int_day_to_date_str($v['int_day']);
            $list[$k]['sid']   = get_student_name($v['sid']);
            $list[$k]['lid']   = get_lesson_name($v['lid']);
            $list[$k]['cid']   = get_class_name($v['cid']);
            $list[$k]['desc'] = $this->get_desc($v['change_type'],$v['int_start_hour'],$v['int_end_hour'],$v['int_day'],$v['cid'],$v['eid']);
            $list[$k]['eid'] = get_teacher_name($v['eid']);
            $list[$k]['lesson_type'] = $this->convert_type($v['lesson_type']);
            $list[$k]['section_time'] = $this->get_section_time($v['int_start_hour'],$v['int_end_hour'],$v['int_day']);
            $list[$k]['week'] = $this->get_week($v['int_day']);
            $list[$k]['section'] = $this->get_section($v['int_start_hour'],$v['int_end_hour']);
            $list[$k]['grade'] = get_grade_title($v['grade']);
            $list[$k]['cr_id'] = get_class_room($v['cr_id']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}