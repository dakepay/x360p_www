<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportBranchPerformanceSummary;

class ReportBranchPerformanceSummarys extends Export
{
    protected $report_model = null;

    public function __init(){
        set_time_limit(0);
        ini_set("memory_limit","516M");
    }

	protected $columns = [
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'nums','title'=>'签单数','width'=>20],
        ['field'=>'amount','title'=>'签单金额','width'=>20],
        ['field'=>'refund_amount','title'=>'退单金额','width'=>20],
        ['field'=>'only_amount','title'=>'净金额','width'=>20],
        ['field'=>'lesson_hours','title'=>'确收课时','width'=>20],
        ['field'=>'lesson_amount','title'=>'确收金额','width'=>20],
        ['field'=>'refund_nums','title'=>'退单数量','width'=>20],
        ['field'=>'cut_amount','title'=>'违约金额','width'=>20],
	];


	protected function get_title()
	{
		$title = '校区业绩表';
		return $title;
	}


	protected function get_columns()
	{
		$input = $this->params;
		$arr = $this->columns;
		if($input['type'] == 2){
            $arr[0] = ['field'=>'dept_id','title'=>'分公司','width'=>20];
		}
		return $arr;
	}


	protected function get_data()
	{

		$model = new ReportBranchPerformanceSummary;

        $this->report_model = $model;

		$w = [];

		$input = $this->params;
		unset($input['bid']);
		$w['start_int_day'] = format_int_day($input['start_date']);
        $w['end_int_day']   = format_int_day($input['end_date']);

        $og_id = gvar('og_id');
        $user  = gvar('user');


        if($input['type'] == 2){
        	unset($input['type']);
        	$com_ids = $user['employee']['com_ids'];
        	$w['dept_id'] = ['IN',$com_ids];
        	$group = 'dept_id';
        	$ret = $model->where($w)->group($group)->getSearchResult($input,[],false);
        	foreach ($ret['list'] as &$row) {
        		$w['dept_id'] = $row['dept_id'];
        		$row['dept_id'] = $row['dept_id'] ? get_department_name($row['dept_id']) : '总部';
        		$row['only_amount'] = $row['amount'] - $row['refund_amount'];
        	}
        }else{
        	unset($input['type']);

            $request_bids = isset($user['employee'])?$user['employee']['bids']:[];

            if(!$request_bids){
                $request_bids = [];
            }

            $query_bids = $request_bids;

            if(isset($input['bid'])){
                if($input['bid'] == -1){
                    $query_bids = [];
                }else{
                    $query_bids = explode(',',$input['bid']);
                }
            }

            if(empty($query_bids)){
                $w_branch['og_id'] = $og_id;
                $branch_list = get_table_list('branch',$w_branch);
                $query_bids  = array_column($branch_list,'bid');
            }

            $w['bid'] = ['in',$query_bids];

        	$ret = $model->where($w)->getSearchResult($input,[],false);
	        foreach ($ret['list'] as &$row) {
	        	$row['bid'] = get_branch_name($row['bid']);
	        	$row['only_amount'] = $row['amount'] - $row['refund_amount'];
	        }
        }
        

        if(!empty($ret['list'])){
        	return collection($ret['list'])->toArray();
        }
        return [];
	}


}