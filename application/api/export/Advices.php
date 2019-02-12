<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\Advice;


class Advices extends Export
{
    protected $res_name = 'advice';

    protected $columns = [

        ['field'=>'sid','title'=>'姓名','width'=>20],
        ['field'=>'content','title'=>'投诉内容','width'=>60],
        ['field'=>'create_time','title'=>'投诉时间','width'=>20],
    ];

    protected function get_title(){
        $title = '投诉建议';
        return $title;
    }

    public function get_data()
    {
        $model = new Advice();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['sid'] = get_student_name($v['sid']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}