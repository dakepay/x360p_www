<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportStudentByClass;
use app\api\model\Classes;
use app\api\model\ClassStudent;

class ReportStudentByClasses extends Export
{
	protected $columns = [
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'class_name','title'=>'班级名称','width'=>20],
        ['field'=>'initial_student_num','title'=>'月初人数','width'=>20],
        ['field'=>'in_student_num','title'=>'本月转入','width'=>20],
        ['field'=>'out_student_num','title'=>'本月转出','width'=>20],
        ['field'=>'final_student_num','title'=>'月末人数','width'=>20],
	];

	protected function get_title()
	{
		$title = '班级人数统计表';
		return $title;
	}

	protected function get_branch_names($cid)
	{
		$bid = Classes::where('cid',$cid)->value('bid');
		return get_branch_name($bid);
	}

	protected function get_data()
	{
		$model = new ReportStudentByClass;
		$fields = ['cid'];
		$group = 'cid';
        
        $bids = $this->params['bid']; 
        $cids = ltrim($bids,'[In,');
        $cids = rtrim($cids,']');
        $x_bids = explode(',',$cids);
        $w_cids = Classes::where('bid','in',$x_bids)->column('cid');
        $w['cid'] = ['in',$w_cids];

		$data = $model->field($fields)->where($w)->group($group)->order('cid asc')->getSearchResult($this->params,[],false);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['bid'] = $this->get_branch_names($v['cid']);
			$data['list'][$k]['class_name'] = get_class_name($v['cid']);

			$w_d['int_day'] = ['between',[date('Ymd',strtotime($this->params['start_date'])),date('Ymd',strtotime($this->params['end_date']))]];
			$w_d['cid'] = $v['cid'];
            $in_student_num = $model->where($w_d)->sum('in_student_num');
			$data['list'][$k]['in_student_num'] = $in_student_num;
			$out_student_num = $model->where($w_d)->sum('out_student_num');
			$data['list'][$k]['out_student_num'] = $out_student_num;

			$w_i['status'] = 1;
			$w_i['cid'] = $v['cid'];
			$w_i['in_time'] = ['lt',strtotime($this->params['start_date'])];

			$initial_student_num = ClassStudent::where($w_i)->count();
			$data['list'][$k]['initial_student_num'] = $initial_student_num + $data['list'][$k]['out_student_num'];
			$data['list'][$k]['final_student_num'] = $initial_student_num + $data['list'][$k]['out_student_num'] + $data['list'][$k]['in_student_num'];
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
        return [];
	}
}