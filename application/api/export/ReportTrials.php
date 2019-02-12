<?php

namespace app\api\export;

use app\api\model\ReportTrial;
use app\common\Export;

class ReportTrials extends Export
{
	protected $columns = [
        ['field'=>'student_name','title'=>'学员姓名','width'=>20],
        ['field'=>'status','title'=>'状态','width'=>20],
        ['field'=>'teach_eid','title'=>'试听课老师','width'=>20],
        ['field'=>'sign_amount','title'=>'报读金额','width'=>20],
        ['field'=>'sign_time','title'=>'报读日期','width'=>20],
        ['field'=>'lid','title'=>'报读课程','width'=>20],
        ['field'=>'receive_amount','title'=>'实收费金额','width'=>20],
        ['field'=>'eid','title'=>'收款人','width'=>20],
	];

	protected function get_title()
	{
		$title = '试听报读统计表';
		return $title;
	}

	protected function convert_status($key)
	{
		$map = [0=>'未报读',1=>'已报读'];
		if(key_exists($key,$map)){
			return $map[$key];
		}
		return '-';
	}
    
    protected function get_data(){

    	$model = new ReportTrial;
    	$input = $this->params;
    	$w = [];
    	if(!empty($input['start_date'])){
    		$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
    	}
    	if(!empty($input['student_name'])){
    		$w['student_name'] = ['like','%'.$input['student_name'].'%'];
    	}

    	$data = $model->where($w)->getSearchResult($input);
    	foreach ($data['list'] as $k => $v) {
    		$data['list'][$k]['eid'] = get_teacher_name($v['eid']);
    		if($v['sign_time']){
    			$data['list'][$k]['sign_time'] = date('Y-m-d',$v['sign_time']);
    		}else{
    			$data['list'][$k]['sign_time'] = '-';
    		}
    		$data['list'][$k]['teach_eid'] = get_teacher_name($v['teach_eid']);
    		$data['list'][$k]['lid'] = get_lesson_name($v['lid']);
    		$data['list'][$k]['status'] = $this->convert_status($v['status']);
    	}

    	if($data['list']){
    		return collection($data['list'])->toArray();
    	}

    	return [];


    }



}