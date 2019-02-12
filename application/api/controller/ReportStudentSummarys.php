<?php

namespace app\api\controller;
use think\Request;
use app\api\model\CourseArrange;
use app\api\model\CourseArrangeStudent;
use app\api\model\StudentAttendance;
use app\api\model\StudentLeave;

use app\api\model\ReportStudentSummary;

class ReportStudentSummarys extends Base
{
	public function get_list(Request $request)
	{
		$input = $request->get();
		$model = new ReportStudentSummary;
		$ret = $model->getDaySectionReport($input);
		if(!$ret){
			return $this->sendError(400,$model->getError());		
		}
		return $this->sendSuccess($ret);
	}

	protected function convert_type($key)
	{
		$map = [0=>'班课',1=>'一对一',2=>'一对多'];
		if(key_exists($key,$map)){
			return $map[$key];
		}
		return '-';
	}

	protected function convert_attendance($key)
	{
		$map = [0=>'未考勤',1=>'部分考勤',2=>'全部考勤'];
		if(key_exists($key,$map)){
			return $map[$key];
		}
		return '-';
	}

    /**
     * 获取详情
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	protected function get_detail(Request $request,$id=0)
	{
		$id = input('id/d');
		// print_r($id);exit;
		$input = $request->get();

		$data = ReportStudentSummary::get($id);
		if(empty($data)) $ret = [];

		$start_date = isset($input['start_date']) ? format_int_day($input['start_date']) : $data['start_int_day'];
		$end_date   = isset($input['end_date']) ? format_int_day($input['end_date']) : $data['end_int_day'];

		$start_ts = strtotime($start_date.' 00:00:01');
        $end_ts   = strtotime($end_date.' 23:59:59');

        $params['between_ts'] = [$start_ts,$end_ts];
        $params['between_int_day'] = [$start_date,$end_date];

		$m_ca  = new CourseArrange;
		$m_cas = new CourseArrangeStudent;
		$m_sa  = new StudentAttendance;
		$m_sl  = new StudentLeave;

		$w['int_day'] = ['between',$params['between_int_day']];
		$w['bid']     = $data['bid'];
        
        if(isset($input['dept_id'])){
            unset($w['bid']);
            $bids = get_bids_by_dpt_id($input['dept_id']);
            $w['bid'] = ['in',$bids];
            $ca_ids = $m_ca->where($w)->column('ca_id');
        }else{
        	$ca_ids = $m_ca->where($w)->column('ca_id');
        }

		switch ($input['type']) {
			case 'course_arrange_times':
		
                $ret = $m_ca->where($w)->getSearchResult($input);
      
				foreach ($ret['list'] as &$row) {
					$row['bid'] = get_branch_name($row['bid']);
					$row['lesson_type'] = $this->convert_type($row['lesson_type']);
					$row['teach_eid'] = get_teacher_name($row['teach_eid']);
					$row['cr_id'] = get_class_room($row['cr_id']);
					$row['time_section'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'-'.int_hour_to_hour_str($row['int_end_hour']); 
					$row['is_attendance'] = $this->convert_attendance($row['is_attendance']);
				}
				$ret['columns'] = CourseArrange::$detail_fields;
				break;
			case 'course_arrange_student_times':
			    $w_cas['ca_id'] = ['in',$ca_ids];
                $ret = $m_cas->where($w_cas)->order('sid asc')->getSearchResult($input);
                foreach ($ret['list'] as &$row) {
                	$row['student'] = $row['sid'] ? get_student_name($row['sid']) : get_customer_name($row['cu_id']);
                	$row['time_section'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'-'.int_hour_to_hour_str($row['int_end_hour']); 
                	$row['is_leave'] = $row['is_leave'] ? '已请假' : '未请假';
                	$row['is_attendance'] = $row['is_attendance'] ? '已上课' : '未上课';

                }
                $ret['columns'] = CourseArrangeStudent::$detail_fields;
			    break;
			 case 'student_attendance_times':
			
                $ret = $m_sa->where($w)->getSearchResult($input);
             
			    foreach ($ret['list'] as &$row) {
			    	$row['bid'] = get_branch_name($row['bid']);
			    	$row['sid'] = get_student_name($row['sid']);
			    	$row['is_in'] = $row['is_in'] ? '出勤' : '缺勤';
			    	$row['in_time'] = date('m-d H:i',strtotime($row['in_time']));
			    }
                $ret['columns'] = StudentAttendance::$detail_fields;
			    break;
			case 'student_leave_times':
			    
                $ret = $m_sl->where($w)->getSearchResult($input);
        
			    foreach ($ret['list'] as &$row) {
			    	$row['bid'] = get_branch_name($row['bid']);
			    	$row['sid'] = get_student_name($row['sid']);
			    	$row['time_section'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'-'.int_hour_to_hour_str($row['int_end_hour']); 
			    }
                $ret['columns'] = StudentLeave::$detail_fields;
			    break;
			
			default:
				# code...
				break;
		}

		return $this->sendSuccess($ret);

	}

}