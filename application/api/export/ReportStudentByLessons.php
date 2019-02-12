<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportStudentByLesson;

class ReportStudentByLessons extends Export
{
	protected $columns = [
        ['field'=>'student_name','title'=>'姓名','width'=>20],
        ['field'=>'sno','title'=>'学号','width'=>20],
        ['field'=>'first_tel','title'=>'手机号码','width'=>20],
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'lesson_num','title'=>'在读课程数','width'=>20],
        ['field'=>'lids','title'=>'课程名称','width'=>60],
	];

	protected function get_title()
	{
		$title = '课程人数统计表';
		return $title;
	}

    protected function get_lesson_names($lids)
    {
    	$arr = explode(',',$lids);
    	$ret = '';
    	foreach ($arr as $k => $v) {
    		$ret = get_lesson_name($v).' 、'.$ret;
    	}
    	return rtrim($ret,"、");
    }


    protected function get_lesson_num($lids)
    {
    	$arr = explode(',',$lids);
    	return count($arr);
    }

    protected function get_data()
    {
    	$model = new ReportStudentByLesson;
    	$fields = ['student_name','sno','first_tel','bid','lids'];
        $w['status'] = 1;
    	$data = $model->field($fields)->where($w)->order('sid asc')->getSearchResult($this->params,[],false);

    	foreach ($data['list'] as $k => $v) {
    		$data['list'][$k]['bid'] = get_branch_name($v['bid']);
    		$data['list'][$k]['lesson_num'] = $this->get_lesson_num($v['lids']);
    		$data['list'][$k]['lids'] = $this->get_lesson_names($v['lids']);
    	}

    	if(!empty($data['list'])){
    		return collection($data['list'])->toArray();
    	}

    	return [];
    }

}