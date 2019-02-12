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

class Drops extends Export
{
    protected $res_name = 'student';

    protected $columns = [
        ['field'=>'sid','title'=>'姓名','width'=>20],
        ['field'=>'birth_time','title'=>'出生日期','width'=>20],
        ['field'=>'status','title'=>'状态','width'=>20],
        ['field'=>'quit_reason','title'=>'退学原因','width'=>20],
        ['field'=>'first_tel','title'=>'手机号','width'=>20],
    ];

    protected function get_title(){
        $title = '流失学员';
        return $title;
    }

    protected function convert_status($value)
    {
        $map = [1=>'正常',20=>'停课',30=>'休学',90=>'退学',100=>'封存'];
        if(key_exists($value,$map)){
            return $map[$value];
        }
        return '';
    }

    public function get_data()
    {
        $model = new Student();
        $result = $model->where('status',90)->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['sid'] = get_student_name($v['sid']);
            $list[$k]['status'] = $this->convert_status($v['status']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];
    }
}