<?php 

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportClassByNumber;

class ReportClassByNumbers extends Export
{
	protected $columns = [
        ['field'=>'bid','title'=>'校区名称','width'=>20],
        ['field'=>'lid','title'=>'课程名称','width'=>20],
        ['field'=>'cid','title'=>'班级名称','width'=>20],
        ['field'=>'teach_eid','title'=>'上课老师','width'=>20],
        ['field'=>'student_num','title'=>'班级人数','width'=>20],
	];

	protected function get_title()
	{
		$title = '班级人数';
		return $title;
	}

	protected function get_data()
	{
		$model = new ReportClassByNumber;
        $w = [];
		if(!empty($this->params['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($this->params['start_date'])),date('Ymd',strtotime($this->params['end_date']))]];
		}

		$data = $model->order('cid asc')->where($w)->getSearchResult($this->params,[],false);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['bid'] = get_branch_name($v['bid']);
			$data['list'][$k]['lid'] = get_lesson_name($v['lid']);
			$data['list'][$k]['cid'] = get_class_name($v['cid']);
			$data['list'][$k]['teach_eid'] = get_teacher_name($v['teach_eid']);
		}


		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}

		return [];
	}
}