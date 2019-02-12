<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\Student;

class Loss extends Export
{
    protected $res_name = 'student';

    protected $columns = [
        ['field'=>'sid','title'=>'姓名','width'=>20],
        ['field'=>'birth_time','title'=>'出生日期','width'=>20],
        ['field'=>'first_tel','title'=>'手机号','width'=>20],
        ['field'=>'last_attendance_time','title'=>'最后考勤时间','width'=>20],
    ];

    protected function get_title(){
        $title = '流失预警学员';
        return $title;
    }

    public function get_data()
    {
        $model = new Student();
        $time = time() - 30*24*60*60;
        $result = $model->where('last_attendance_time','lt',$time)->where('last_attendance_time','gt','0')->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['sid'] = get_student_name($v['sid']);
            // $list[$k]['status'] = $this->convert_status($v['status']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];
    }
}