<?php 

namespace app\api\controller;

use think\Request;
use app\api\model\ReportBranchPerformanceSummary;
use app\api\model\OrderPaymentHistory;
use app\api\model\StudentLessonHour;
use app\api\model\EmployeeReceipt;

class ReportBranchPerformanceSummarys extends Base
{
	public function get_list(Request $request)
	{
        set_time_limit(0);
        ini_set("memory_limit","512M");
		$input = $request->get();
		$model = new ReportBranchPerformanceSummary();
		$ret = $model->getDaySectionReport($input,true);
		if(!$ret){
			return $this->sendError(400,$model->getError());
		}
		return $this->sendSuccess($ret);
	}

    protected function convert_type($key)
    {
        $map = [0=>'班课',1=>'1对1',2=>'1对多',3=>'研学旅行团'];
        if(key_exists($key,$map)){
            return $map[$key];
        }
        return '-';
    }

    public function get_detail(Request $request,$id = 0)
    {
    	$id = input('id/d');
    	
    	$data = ReportBranchPerformanceSummary::get($id);
    	if(empty($data)) $ret = [];

        $input = $request->get();

    	$start_date = $input['start_date'];
		$end_date   = $input['end_date'];

		$start_ts = strtotime($start_date.' 00:00:00');
        $end_ts   = strtotime($end_date.' 23:59:59');

        $start_int_day = format_int_day($start_date);
        $end_int_day   = format_int_day($end_date);

        $params['between_ts'] = [$start_ts,$end_ts];
        $params['between_int_day'] = [$start_int_day,$end_int_day];

        $w['bid'] = $data['bid'];

        switch ($input['type']) {
            case 'amount':
                $model = new OrderPaymentHistory;
                $w['paid_time'] = ['between',$params['between_ts']];
                $w['amount'] = ['gt',0];
                if(isset($input['dept_id'])){
                    unset($w['bid']);
                    $bids = get_bids_by_dpt_id($input['dept_id']);
                    $w['bid'] = ['in',$bids];
                    // print_r($w);exit;
                    $ret = $model->where($w)->getSearchResult($input);
                }else{
                    $ret = $model->where($w)->getSearchResult($input);
                }  
                foreach ($ret['list'] as &$row) {
                    $employee_receipt = EmployeeReceipt::get(['oid'=>$row['oid']]);
                    $row['sid'] = get_student_name($employee_receipt['sid']);
                    $row['eid'] = get_teacher_name($employee_receipt['eid']);
                    $row['create_time'] = date('Y-m-d',strtotime($row['create_time']));
                }
                $ret['columns'] = OrderPaymentHistory::$detail_fields;
                break;
            case 'lesson_amount':
                $model = new StudentLessonHour;
                $w['int_day'] = ['between',$params['between_int_day']];
                if(isset($input['dept_id'])){
                    unset($w['bid']);
                    $bids = get_bids_by_dpt_id($input['dept_id']);
                    $w['bid'] = ['in',$bids];
                    $ret = $model->where($w)->getSearchResult($input);
                }else{
                    $ret = $model->where($w)->getSearchResult($input);
                }
                foreach ($ret['list'] as &$row) {
                    // $row['lesson_type'] = $this->convert_type($row['lesson_type']);
                    $row['sid'] = get_student_name($row['sid']);
                }
                $ret['columns'] = StudentLessonHour::$detail_fields;
                break;
            case 'refund_amount':
                $model = new EmployeeReceipt;
                $w['amount']   = ['lt',0];
                $w['receipt_time'] = ['between',$params['between_ts']];

                if(isset($input['dept_id'])){
                    unset($w['bid']);
                    $bids = get_bids_by_dpt_id($input['dept_id']);
                    $w['bid'] = ['in',$bids];
                    $ret = $model->where($w)->getSearchResult($input);
                }else{
                    $ret = $model->where($w)->getSearchResult($input);
                }
                foreach ($ret['list'] as &$row) {
                    $row['sid'] = get_student_name($row['sid']);
                    $row['receipt_time'] = date('Y-m-d',strtotime($row['receipt_time']));
                }
                $ret['columns'] =  EmployeeReceipt::$detail_refund_fields;
                break;
            case 'cut_amount':
                $model = new StudentLessonHour;
                $w['int_day'] = ['between',$params['between_int_day']];
                $w['consume_type'] = 3;
                if(isset($input['dept_id'])){
                    unset($w['bid']);
                    $bids = get_bids_by_dpt_id($input['dept_id']);
                    $w['bid'] = ['in',$bids];
                    $ret = $model->where($w)->getSearchResult($input);
                }else{
                    $ret = $model->where($w)->getSearchResult($input);
                }
                foreach ($ret['list'] as &$row) {
                    $row['sid'] = get_student_name($row['sid']);
                    $row['int_day'] = date('Y-m-d',strtotime($row['int_day']));
                }
                $ret['columns'] =  StudentLessonHour::$detail_cut_fields;
                break;
            default:
 
                break;
        }

        foreach ($ret['list'] as &$row) {
        	$row['bid'] = get_branch_name($row['bid']);
        }


        return $this->sendSuccess($ret);


    }



}