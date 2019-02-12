<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\StudentArtwork;


class StudentArtworks extends Export
{
    protected $res_name = 'student_artwork';

    protected $columns = [

        ['field'=>'sid','title'=>'学员','width'=>20],
        ['field'=>'art_name','title'=>'作品名','width'=>20],
        ['field'=>'art_desc','title'=>'作品简介','width'=>60],
        ['field'=>'eid','title'=>'指导老师','width'=>20],
        ['field'=>'create_time','title'=>'发布时间','width'=>20],
    ];

    protected function get_title(){
        $title = '作品服务';
        return $title;
    }

    public function get_data()
    {
        $model = new StudentArtwork();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['eid'] = get_teacher_name($v['eid']);
            $list[$k]['sid'] = get_student_name($v['sid']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}