<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportClassByName;
use app\api\model\CourseArrange;

class ReportClassByNames extends Export
{
	protected $columns = [
        ['field'=>'bid','title'=>'校区名称','width'=>20],
        ['field'=>'cid','title'=>'班级名称','width'=>20],
        ['field'=>'lid','title'=>'课程名称','width'=>20],
        ['field'=>'section_time','title'=>'上课时间','width'=>20],
        ['field'=>'sid','title'=>'学生姓名','width'=>20],
        ['field'=>'teach_eid','title'=>'上课老师','width'=>20],
	];

	protected function get_title(){
		$title = '班级花名册';
		return $title;
	}

	protected function get_section_time($cid)
	{
		$ret = CourseArrange::where('cid',$cid)->find();
		$start = $ret['int_start_hour'];
		$end = $ret['int_end_hour'];
		return int_hour_to_hour_str($start).' - '.int_hour_to_hour_str($end);
	}

	protected function get_data()
	{
		$model = new ReportClassByName;
        $w = [];
		if(!empty($this->params['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($this->params['start_date'])),date('Ymd',strtotime($this->params['end_date']))]];
		}

		$data = $model->where($w)->order('sid asc')->getSearchResult($this->params,[],false);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['bid'] = get_branch_name($v['bid']);
			$data['list'][$k]['cid'] = get_class_name($v['cid']);
			$data['list'][$k]['lid'] = get_lesson_name($v['lid']);
			$data['list'][$k]['sid'] = get_student_name($v['sid']);
			$data['list'][$k]['teach_eid'] = get_teacher_name($v['teach_eid']);
			$data['list'][$k]['section_time'] = $this->get_section_time($v['cid']);
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}

		return [];


	}
}