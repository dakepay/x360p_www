<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\ClassAttendance;


class ClassAttendances extends Export
{
    protected $res_name = 'class_attendance';

    protected $columns = [
        ['field'=>'cid','title'=>'考勤对象','width'=>20],
        ['field'=>'lid','title'=>'课程','width'=>20],
        ['field'=>'need_nums','title'=>'应到人数','width'=>20],
        ['field'=>'in_nums','title'=>'实到人数','width'=>20],
        ['field'=>'sj_id','title'=>'科目','width'=>20],
        ['field'=>'eid','title'=>'上课老师','width'=>20],
        ['field'=>'int_day','title'=>'考勤日期','width'=>20],
        ['field'=>'section','title'=>'上课时段','width'=>20],
    ];

    protected function get_title(){
        $title = '考勤记录';
        return $title;
    }

    protected function convert_attendance($value)
    {
        $map = ['未考勤', '部分考勤', '已考勤'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function convert_hour($start,$end,$day){
        return '星期'.int_day_to_week($day).' '.int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end);
    }

    public function get_data()
    {
        $model = new ClassAttendance();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['cid']            = get_class_name($v['cid']);
            $list[$k]['sj_id']          = get_subject_name($v['sj_id']);
            $list[$k]['eid']            = get_teacher_name($v['eid']);
            $list[$k]['lid']            = get_lesson_name($v['lid']);
            $list[$k]['int_day']        = int_day_to_date_str($v['int_day']);
            $list[$k]['section']        = $this->convert_hour($v['int_start_hour'],$v['int_end_hour'],$v['int_day']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}