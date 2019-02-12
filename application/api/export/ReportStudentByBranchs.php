<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\Student;


class ReportStudentByBranchs extends Export
{
	protected $columns = [
        ['field'=>'branch_name','title'=>'校区名称','width'=>20],
        ['field'=>'student_num','title'=>'校区人数','width'=>20],
	];

	protected function get_title()
	{
		$title = '校区人数统计表';
		return $title;
	}

	protected function get_student_number($bid)
	{
		$w['bid'] = $bid;
		$w['status'] = 1;
		return Student::where($w)->count();
	}

	protected function get_data()
	{
		$model = new Student;
		$fields = ['bid'];
		$group = 'bid';
		$bids = $this->params['bids'];
		$w['bid'] = ['in',$bids];
		$data = $model->where($w)->field('bid')->group($group)->order('bid asc')->getSearchResult([],[],false);
		// print_r($data);exit;

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['branch_name'] = get_branch_name($v['bid']);
			$data['list'][$k]['student_num'] = $this->get_student_number($v['bid']);
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
	}
}