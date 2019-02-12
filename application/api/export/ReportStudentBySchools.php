<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportStudentLessonClass;

class ReportStudentBySchools extends Export
{
    protected $columns = [
        ['field'=>'school_name','title'=>'学校名称','width'=>20],
        ['field'=>'student_num','title'=>'总人数','width'=>20],
    ];

	protected function get_title()
	{
		$title = '学校人数统计表';
		return $title;
	}

	protected function get_columns()
	{
		$bids = $this->params['bids'];
		$bids = explode(',',$bids);
		$arr = [];
		foreach ($bids as $k => $v) {
			$arr[$k] = ['field'=>'branch_name_'.$v,'title'=>'','width'=>20];
			$arr[$k]['title'] = get_branch_name($v);
		}
		$arr1 = $this->columns;
		$this->columns = array_merge($arr1,$arr);
		return $this->columns;
	}

	protected function get_branch_number($school_id,$bid){
		$w['school_id'] = $school_id;
		$w['bid'] = $bid;
		return ReportStudentLessonClass::where($w)->count();
	}

	protected function get_data()
	{
		$model = new ReportStudentLessonClass;
		$bids = $this->params['bids'];
		$bids = explode(',',$bids);
		
		$group = 'school_id';
		$data = $model->field('school_id')->group($group)->order('school_id asc')->getSearchResult($this->params,[],false);

		// print_r($data);exit;
		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['school_name'] = get_school_name($v['school_id']);
			$total_number = ReportStudentLessonClass::where('school_id',$v['school_id'])->count();
			$data['list'][$k]['student_num'] = $total_number;
			foreach ($bids as $k1 => $v1) {
				$data['list'][$k]['branch_name_'.$v1] = $this->get_branch_number($v['school_id'],$v1);
			}
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];


	}



}