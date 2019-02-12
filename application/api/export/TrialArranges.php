<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\CourseArrange;

class TrialArranges extends Export
{
    protected $res_name = 'course_arrange';

    protected $columns = [
        ['field'=>'name','title'=>'试听班名称','width'=>20],
        ['field'=>'int_day','title'=>'日期','width'=>20],
        ['field'=>'time_section','title'=>'时段','width'=>20],
        ['field'=>'teach_eid','title'=>'老师','width'=>20],
        ['field'=>'sj_id','title'=>'科目','width'=>20],
        ['field'=>'cr_id','title'=>'教室','width'=>20],
        ['field'=>'is_attendance','title'=>'考勤','width'=>20],
    ];

    protected function get_title(){
        $title = '试听排课';
        return $title;
    }

    protected function convert_hour($start,$end,$day){
        return '星期'.int_day_to_week($day).' '.int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end);
    }

    protected function get_class_room($cr_id){
        $class = m('classroom')->where('cr_id',$cr_id)->find();
        return $class->room_name;
    }

    protected function convert_attendance($value)
    {
        $map = [0=>'未考勤',1=>'部分考勤',2=>'全部考勤'];
        if(key_exists($value,$map)){
            return $map[$value];
        }
        return '';
    }

    public function get_data()
    {
        $model = new CourseArrange();
        $result = $model->where('is_trial','1')->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['int_day'] = int_day_to_date_str($v['int_day']);
            $list[$k]['time_section'] = $this->convert_hour($v['int_start_hour'],$v['int_end_hour'],$v['int_day']);
            $list[$k]['teach_eid'] = get_teacher_name($v['teach_eid']);
            $list[$k]['sj_id'] = get_subject_name($v['sj_id']);
            $list[$k]['cr_id'] = $this->get_class_room($v['cr_id']);
            $list[$k]['is_attendance'] = $this->convert_attendance($v['is_attendance']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];
    }
}