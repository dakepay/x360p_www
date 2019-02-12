<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\CoursePrepare;


class CoursePrepares extends Export
{
    protected $res_name = 'course_prepare';

    protected $columns = [
        ['field'=>'title','title'=>'备课标题','width'=>20],
        ['field'=>'lid','title'=>'课程','width'=>20],
        ['field'=>'lesson_type','title'=>'授课类型','width'=>20],

        // ['field'=>'mc_id','title'=>'授课对象','width'=>20],
        
        ['field'=>'int_day','title'=>'上课日期','width'=>20],
        ['field'=>'time_section','title'=>'上课时段','width'=>20],
        ['field'=>'sj_id','title'=>'科目','width'=>20],
        ['field'=>'is_push','title'=>'是否推送','width'=>20],
    ];

    protected function get_title(){
        $title = '备课服务';
        return $title;
    }

    protected function convert_type($value)
    {
        $map = [0=>'班课', 1=>'1对1', 2=>'1对多'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function convert_push($value)
    {
        $map = [0=>'待推送', 1=>'已推送'];
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
        $model = new CoursePrepare();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['lid'] = get_lesson_name($v['lid']);
            $list[$k]['lesson_type'] = $this->convert_type($v['lesson_type']);
            $list[$k]['int_day'] = int_day_to_date_str($v['int_day']);
            $list[$k]['time_section'] = $this->convert_hour($v['int_start_hour'],$v['int_end_hour'],$v['int_day']);
            $list[$k]['sj_id'] = get_subject_name($v['sj_id']);

            $list[$k]['is_push'] = $this->convert_push($v['is_push']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}