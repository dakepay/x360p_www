<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportStudentByRemainlessonhour;

class ReportStudentByRemainlessonhours extends Export
{
	protected $columns = [
        ['field'=>'sid','title'=>'学员姓名','width'=>20],
        ['field'=>'bid','title'=>'所属校区','width'=>20],
        ['field'=>'lesson_hour','title'=>'学员总课时','width'=>20],
        ['field'=>'remain_lesson_hour','title'=>'剩余课时','width'=>20],
        ['field'=>'remain_money','title'=>'剩余课时金额','width'=>20]
	];

	protected function get_title()
	{
		$title = '剩余课时报表';
		return $title;
	}

	protected function get_data()
	{
		$model = new ReportStudentByRemainlessonhour;

		$w = [];
		$w['status'] = 1;
		if(!empty($this->parmas['cid'])){
			$cid = $this->params['cid'];
			$w[] = ['exp',"find_in_set({$cid},cids)"];
		}
		if(!empty($this->parmas['lid'])){
			$lid = $this->params['lid'];
			$w[] = ['exp',"find_in_set({$lid},lids)"];
		}

		$data = $model->where($w)->order('sid asc')->getSearchResult($this->params,[],false);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['sid'] = get_student_name($v['sid']);
			$data['list'][$k]['bid'] = get_branch_name($v['bid']);
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
	}
}