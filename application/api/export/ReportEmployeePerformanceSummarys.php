<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportEmployeePerformanceSummary;

class ReportEmployeePerformanceSummarys extends Export
{

	protected $columns = [

	    ['field'=>'bid','title'=>'校区','width'=>20],
	    ['field'=>'eid','title'=>'员工姓名','width'=>20],
	    ['field'=>'performance_nums','title'=>'签单数','width'=>20],
	    ['field'=>'performance_amount','title'=>'签单业绩','width'=>20],
	    ['field'=>'refund_nums','title'=>'退单数','width'=>20],
	    ['field'=>'refund_amount','title'=>'退单金额','width'=>20],
	    
	    ['field'=>'edu_lesson_amount','title'=>'学管师业绩','width'=>20],
	    ['field'=>'edu_lesson_hours','title'=>'课时数','width'=>20],
	    ['field'=>'teach_lesson_amount','title'=>'老师业绩','width'=>20],
	    ['field'=>'teach_lesson_hours','title'=>'课时数','width'=>20],
	    ['field'=>'second_lesson_amount','title'=>'助教业绩','width'=>20],
	    ['field'=>'second_lesson_hours','title'=>'课时数','width'=>20],

	];

	protected function get_title()
	{
        $params = $this->params;
        $title = sprintf('人员业绩(%s~%s)',$params['start_date'],$params['end_date']);
        return $title;
	}

	protected function get_columns()
	{
		$input = $this->params;
		$arr = $this->columns;
		if($input['type'] == 1){
            unset($arr[6]);unset($arr[7]);unset($arr[8]);unset($arr[9]);unset($arr[10]);unset($arr[11]);
            $arr = array_values($arr);
		}elseif($input['type'] == 2){
			unset($arr[2]);unset($arr[3]);unset($arr[4]);unset($arr[5]);
			$arr = array_values($arr);
		}
		return $arr;
	}

	protected function get_data()
	{

		$model = new ReportEmployeePerformanceSummary;
		$w = [];

		$input = $this->params;
		if(isset($input['bid']) && $input['bid'] == -1){
		    unset($input['bid']);
        }
		unset($input['type']);
		$w['start_int_day'] = format_int_day($input['start_date']);
        $w['end_int_day']   = format_int_day($input['end_date']);

        $ret = $model->where($w)->order('bid ASC')->getSearchResult($input,[],false);
        foreach ($ret['list'] as &$row) {
        	$row['bid'] = get_branch_name($row['bid']);
        	$row['eid'] = get_teacher_name($row['eid']);
        }


        if(!empty($ret['list'])){
        	return collection($ret['list'])->toArray();
        }
        return [];
	}


}