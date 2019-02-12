<?php

namespace app\api\controller;

use think\Request;
use app\api\model\ReportEmployeePerformanceSummary;
use app\api\model\EmployeeLessonHour;
use app\api\model\EmployeeReceipt;

class ReportEmployeePerformanceSummarys extends Base
{
	public function get_list(Request $request)
	{
		$input = $request->get();

		$model = new ReportEmployeePerformanceSummary;
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

    /**
     * 获取每条业绩详情
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function get_detail(Request $request,$id=0)
	{
		$id = input('id/d');

        $input = $request->get();

		$data = ReportEmployeePerformanceSummary::get($id);
		if(empty($data)) $ret = [];
		
		$start_date = $data['start_int_day'];
		$end_date   = $data['end_int_day'];

		$start_ts = strtotime($start_date.' 00:00:01');
        $end_ts   = strtotime($end_date.' 23:59:59');

        $params['between_ts'] = [$start_ts,$end_ts];
        $params['between_int_day'] = [$start_date,$end_date];


        $m_elh = new EmployeeLessonHour;
        $w_elh['int_day'] = ['between',$params['between_int_day']];

        $m_er = new EmployeeReceipt;
        $w_er['receipt_time'] = ['between',$params['between_ts']];

        switch ($input['type']) {
        	case 'performance_amount':  // 签单业绩
        		$w_er['eid']     = $data['eid'];
                $w_er['amount']   = ['gt',0];
                $ret = $m_er->where($w_er)->getSearchResult($input);
                foreach ($ret['list'] as &$row) {
                    $row['sid'] = get_student_name($row['sid']);
                    $row['sale_role_did'] = get_did_value($row['sale_role_did']);
                    $row['receipt_time'] = date('Y-m-d',strtotime($row['receipt_time']));
                }
                $ret['columns'] = EmployeeReceipt::$detail_fields;
        		break;
            case 'refund_amount':  // 退单金额
                $w_er['eid']     = $data['eid'];
                $w_er['amount']   = ['lt',0];
                $ret = $m_er->where($w_er)->getSearchResult($input);
                foreach ($ret['list'] as &$row) {
                    $row['sid'] = get_student_name($row['sid']);
                    $row['sale_role_did'] = get_did_value($row['sale_role_did']);
                    $row['receipt_time'] = date('Y-m-d',strtotime($row['receipt_time']));
                }
                $ret['columns'] = EmployeeReceipt::$detail_refund_fields;
                break;
        	case 'second_lesson_amount':  // 助教业绩
        		$w_elh[] = ['exp',"find_in_set({$data['eid']},second_eids)"];
        		$ret = $m_elh->where($w_elh)->getSearchResult($input);
                foreach ($ret['list'] as &$row) {
                    $row['lesson_type'] = $this->convert_type($row['lesson_type']);
                    $row['time_section'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'-'.int_hour_to_hour_str($row['int_end_hour']); 
                }
        		$ret['columns'] = EmployeeLessonHour::$detail_fields;
        		break;
        	case 'edu_lesson_amount':  // 学管师业绩
        		$w_elh['edu_eid'] = $data['eid'];
        		$ret = $m_elh->where($w_elh)->getSearchResult($input);
                foreach ($ret['list'] as &$row) {
                    $row['lesson_type'] = $this->convert_type($row['lesson_type']);
                    $row['time_section'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'-'.int_hour_to_hour_str($row['int_end_hour']); 
                }
        		$ret['columns'] = EmployeeLessonHour::$detail_fields;
        		break;
        	case 'teach_lesson_amount':  // 老师业绩
        		$w_elh['eid'] = $data['eid'];
        		$ret = $m_elh->where($w_elh)->getSearchResult($input);
                foreach ($ret['list'] as &$row) {
                    $row['lesson_type'] = $this->convert_type($row['lesson_type']);
                    $row['time_section'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'-'.int_hour_to_hour_str($row['int_end_hour']); 
                }
        		$ret['columns'] = EmployeeLessonHour::$detail_fields;
        		break;
        	
        	default:
        		# code...
        		break;
        }

        foreach ($ret['list'] as &$row) {
        	$row['eid'] = get_teacher_name($data['eid']);
            $row['bid'] = get_branch_name($row['bid']);
        }


		return $this->sendSuccess($ret);
	}






}