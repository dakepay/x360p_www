<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\StudentLeave;


class StudentLeaves extends Export
{
    protected $res_name = 'student_leave';

    protected $columns = [
        ['field'=>'sid','title'=>'请假人','width'=>20],
        ['field'=>'int_day','title'=>'上课时间','width'=>20],
        ['field'=>'create_time','title'=>'请假时间','width'=>20],
        ['field'=>'reason','title'=>'请假原因','width'=>40],
    ];

    protected function get_title(){
        $title = '请假记录';
        return $title;
    }

    public function get_data()
    {
        $model = new StudentLeave();

        // print_r($this->params);exit;


        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['sid'] = get_student_name($v['sid']);
            $list[$k]['reason'] = $v['reason']?$v['reason']:'未填写';
            $list[$k]['int_day']   = int_day_to_date_str($v['int_day']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}