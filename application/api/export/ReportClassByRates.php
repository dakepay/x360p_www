<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\Classes;

class ReportClassByRates extends Export
{
	protected $columns = [
        ['field'=>'bid','title'=>'校区名称','width'=>20],
		['field'=>'cid','title'=>'班级名称','width'=>20],
		['field'=>'lid','title'=>'课程名称','width'=>20],
		['field'=>'teach_eid','title'=>'上课老师','width'=>20],
		['field'=>'plan_student_nums','title'=>'预招人数','width'=>20],
		['field'=>'student_nums','title'=>'实际人数','width'=>20],
		['field'=>'nums_rate','title'=>'满班率','width'=>20],
	];

	protected function get_title()
	{
		$title = '满班率';
		return $title;
	}

	protected function get_data()
	{
		$model = new Classes;
		$w = [];
		if(!empty($this->params['start_date'])){
			$w['create_time'] = ['between',[strtotime($this->params['start_date']),strtotime($this->params['end_date'])]];
		}

        $data = $model->where($w)->order('cid asc')->getSearchResult($this->params,[],false);

        foreach ($data['list'] as $k => $v) {
        	$data['list'][$k]['bid'] = get_branch_name($v['bid']);
        	$data['list'][$k]['cid'] = get_class_name($v['cid']);
        	$data['list'][$k]['lid'] = get_lesson_name($v['lid']);
        	$data['list'][$k]['teach_eid'] = get_teacher_name($v['teach_eid']);
        }

        if(!empty($data['list'])){
        	return collection($data['list'])->toArray();
        }
        return [];
	}
}