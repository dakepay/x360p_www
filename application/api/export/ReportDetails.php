<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\EmployeeReceipt;
use app\api\model\EmployeeLessonHour;
use app\api\model\OrderPaymentHistory;
use app\api\model\StudentLessonHour;
use app\api\model\ReportEmployeePerformanceSummary;
use app\api\model\ReportBranchPerformanceSummary;
use app\api\model\ReportStudentSummary;
use app\api\model\CourseArrange;
use app\api\model\CourseArrangeStudent;
use app\api\model\StudentAttendance;
use app\api\model\StudentLeave;
use app\api\model\Student;
use app\api\model\Classes;
use app\api\model\ClassStudent;

class ReportDetails extends Export
{

	protected $columns = [];
    protected $title = '明细表';


	protected function get_title()
	{
        $input = $this->params;
        $title = $this->title;
        switch ($input['table']) {
            case 'class_student':
                $teacher = get_teacher_name($input['eid']);
                $title = $teacher.'老师所在班级人数统计表';
                break;
            case 'class':
                $teacher = get_teacher_name($input['eid']);
                $title = $teacher.'老师所在班级统计表';
                break;
            case 'student':
                $teacher = isset($input['eid']) ? get_teacher_name($input['eid']) : '';
                $stype = isset($input['stype']) ? $input['stype'] : '';
                if($stype == 1){
                    $title = $teacher.'老师一对一学员统计表';
                }elseif($stype == 2){
                    $title = $teacher.'老师一对多学员统计表';
                }else{
                    $branch_name = get_branch_name($input['branch_id']);
                    if(isset($input['school_id'])){
                        $title = $branch_name.'中来自'.$input['school_id'].'学员统计表';
                    }elseif(isset($input['status'])){
                        $status = $this->convert_status($input['status']);
                        $title = $branch_name.$status.'学员人数统计表';
                    }
                }
                break;
            case 'course_arrange':
                $time = sprintf('%s~%s',$input['start_date'],$input['end_date']);
                if(isset($input['dept_id'])){
                    $dept_name = $input['dept_id'] ? get_department_name($input['dept_id']) : '总部';
                    $title = $dept_name.$time.'排课表';
                }else{
                    $rss = ReportStudentSummary::get($input['id']);
                    $branch_name = get_branch_name($rss['bid']);
                    $title = $branch_name.$time.'排课表';
                }  
                break;
            case 'course_arrange_student':
                $time = sprintf('%s~%s',$input['start_date'],$input['end_date']);
                if(isset($input['dept_id'])){
                    $dept_name = $input['dept_id'] ? get_department_name($input['dept_id']) : '总部';
                    $title = $dept_name.$time.'排课学员';
                }else{
                    $rss = ReportStudentSummary::get($input['id']);
                    $branch_name = get_branch_name($rss['bid']);
                    $title = $branch_name.$time.'排课学员';
                } 
                break;
            case 'student_attendance':
                $time = sprintf('%s~%s',$input['start_date'],$input['end_date']);
                if(isset($input['dept_id'])){
                    $dept_name = $input['dept_id'] ? get_department_name($input['dept_id']) : '总部';
                    $title = $dept_name.$time.'出勤学员';
                }else{
                    $rss = ReportStudentSummary::get($input['id']);
                    $branch_name = get_branch_name($rss['bid']);
                    $title = $branch_name.$time.'出勤学员';
                } 
                break;
            case 'student_leave':
                $time = sprintf('%s~%s',$input['start_date'],$input['end_date']);
                if(isset($input['dept_id'])){
                    $dept_name = $input['dept_id'] ? get_department_name($input['dept_id']) : '总部';
                    $title = $dept_name.$time.'请假学员';
                }else{
                    $rss = ReportStudentSummary::get($input['id']);
                    $branch_name = get_branch_name($rss['bid']);
                    $title = $branch_name.$time.'请假学员';
                } 
                break;
            case 'employee_receipt':
                $teacher = get_teacher_name($input['eid']);
                $time = sprintf('%s~%s',$input['start_date'],$input['end_date']);
                $title = $teacher.$time.'签单明细表';
                break;
            case 'employee_refund_receipt':
                $time = sprintf('%s~%s',$input['start_date'],$input['end_date']);
                if(isset($input['dept_id'])){
                    $dept_name = $input['dept_id'] ? get_department_name($input['dept_id']) : '总部';
                    $title = $time.$dept_name.'退款金额';
                }else{
                    $rbph = ReportBranchPerformanceSummary::get($input['id']);
                    $branch_name = get_branch_name($rbph['bid']);
                    $title = $time.$branch_name.'退款金额';
                }
                break;
            case 'cut_amount':
                $time = sprintf('%s~%s',$input['start_date'],$input['end_date']);
                if(isset($input['dept_id'])){
                    $dept_name = $input['dept_id'] ? get_department_name($input['dept_id']) : '总部';
                    $title = $time.$dept_name.'违约金额';
                }else{
                    $rbph = ReportBranchPerformanceSummary::get($input['id']);
                    $branch_name = get_branch_name($rbph['bid']);
                    $title = $time.$branch_name.'违约金额';
                }
                break;
            case 'employee_lesson_hour':
                $teacher = get_teacher_name($input['eid']);
                $time = sprintf('%s~%s',$input['start_date'],$input['end_date']);
                $title = $time.$teacher.'业绩';
                break;
            case 'order_payment_history':
                $time = sprintf('%s~%s',$input['start_date'],$input['end_date']);
                if(isset($input['dept_id'])){
                    $dept_name = $input['dept_id'] ? get_department_name($input['dept_id']) : '总部';
                    $title = $time.$dept_name.'签单金额';
                }else{
                    $rbph = ReportBranchPerformanceSummary::get($input['id']);
                    $branch_name = get_branch_name($rbph['bid']);
                    $title = $time.$branch_name.'签单金额';
                }
                break;
            case 'student_lesson_hour':
                $time = sprintf('%s~%s',$input['start_date'],$input['end_date']);
                if(isset($input['dept_id'])){
                    $dept_name = $input['dept_id'] ? get_department_name($input['dept_id']) : '总部';
                    $title = $time.$dept_name.'确收金额';
                }else{
                    $rbph = ReportBranchPerformanceSummary::get($input['id']);
                    $branch_name = get_branch_name($rbph['bid']);
                    $title = $time.$branch_name.'确收金额';
                }
                break;
            default:
                # code...
                break;
        }
		return $title;
	}

	protected function get_columns()
	{
		$input = $this->params;

		$arr = $this->columns;
		switch ($input['table']) {
			case 'employee_receipt':
				$arr = [
                    ['field'=>'bid','title'=>'校区','width'=>20],
                    ['field'=>'eid','title'=>'员工姓名','width'=>20],
                    ['field'=>'sale_role_did','title'=>'签约角色','width'=>20],
                    ['field'=>'sid','学员姓名','title'=>'学员姓名','width'=>20],
                    ['field'=>'receipt_time','title'=>'签约时间','width'=>20],
                    ['field'=>'amount','title'=>'签约金额','width'=>20],
				];
				break;
            case 'employee_refund_receipt':
                $arr = [
                    ['field'=>'bid','title'=>'校区','width'=>20],
                    ['field'=>'sid','学员姓名','title'=>'学员姓名','width'=>20],
                    ['field'=>'receipt_time','title'=>'退款时间','width'=>20],
                    ['field'=>'amount','title'=>'退款金额','width'=>20],
                ];
                break;
            case 'cut_amount':
                $arr = [
                    ['field'=>'bid','title'=>'校区','width'=>20],
                    ['field'=>'sid','学员姓名','title'=>'学员姓名','width'=>20],
                    ['field'=>'lesson_amount','title'=>'扣款金额','width'=>20],
                    ['field'=>'int_day','title'=>'扣款时间','width'=>20],
                ];
                break;
			case 'employee_lesson_hour':
			    $arr = [
                    ['field'=>'bid','title'=>'校区','width'=>20],
                    ['field'=>'eid','title'=>'员工姓名','width'=>20],
                    ['field'=>'time_section','title'=>'上课时段','width'=>30],
                    ['field'=>'lesson_type','title'=>'课程类型','width'=>20],
                    ['field'=>'total_lesson_hours','title'=>'总课时数','width'=>20],
                    ['field'=>'total_lesson_amount','title'=>'总课时金额','width'=>20],
				];
				break;
			case 'order_payment_history':
                $arr = [
                    ['field'=>'bid','title'=>'校区','width'=>20],
                    ['field'=>'create_time','title'=>'签约时间','width'=>20],
                    ['field'=>'sid','title'=>'学员姓名','width'=>20],
                    ['field'=>'eid','title'=>'签约员工','width'=>20],
                    ['field'=>'amount','title'=>'签约金额','width'=>20],
                ];
			    break;
			case 'student_lesson_hour':
                $arr = [
                    ['field'=>'bid','title'=>'校区','width'=>20],
                    ['field'=>'sid','title'=>'学员姓名','width'=>20],
                    ['field'=>'lesson_hours','title'=>'课时数','width'=>20],
                    ['field'=>'lesson_amount','title'=>'课时金额','width'=>20],
                ];
			    break;
			case 'course_arrange':
			    $arr = [
                    ['field'=>'bid','title'=>'校区','width'=>20],
                    ['field'=>'lesson_type','title'=>'类型','width'=>20],
                    ['field'=>'teach_eid','title'=>'老师','width'=>20],
                    ['field'=>'cr_id','title'=>'教室','width'=>20],
                    ['field'=>'time_section','title'=>'时间段','width'=>20],
                    ['field'=>'is_attendance','title'=>'是否考勤','width'=>20],
			    ];
			    break;
			case 'course_arrange_student':
			    $arr = [
                    ['field'=>'student','title'=>'学员姓名','width'=>20],
                    ['field'=>'time_section','title'=>'时间段','width'=>20],
                    ['field'=>'is_leave','title'=>'是否请假','width'=>20],
                    ['field'=>'is_attendance','title'=>'是否上课','width'=>20],
			    ];
			    break;
			case 'student_attendance':
			    $arr = [
                    ['field'=>'bid','title'=>'校区','width'=>20],
                    ['field'=>'sid','title'=>'学员姓名','width'=>20],
                    ['field'=>'is_in','title'=>'是否出勤','width'=>20],
                    ['field'=>'in_time','title'=>'出勤时间','width'=>20],
			    ];
			    break;
			case 'student_leave':
                $arr = [
                    ['field'=>'bid','title'=>'校区','width'=>20],
                    ['field'=>'sid','title'=>'学员姓名','width'=>20],
                    ['field'=>'time_section','title'=>'上课时间','width'=>20],
                    ['field'=>'create_time','title'=>'请假时间','width'=>20],
                    ['field'=>'reason','title'=>'请假原因','width'=>20],
                ];
			    break;
			case 'student':
                $arr = [
                    ['field'=>'student_name','title'=>'学员姓名','width'=>20],
                    ['field'=>'bid','title'=>'校区','width'=>20],
                    ['field'=>'first_tel','title'=>'联系电话','width'=>20],
                    ['field'=>'status','title'=>'状态','width'=>20],
                ];
			    break;
			case 'class':
			    $arr = [
                    ['field'=>'class_name','title'=>'班级名称','width'=>20],
                    ['field'=>'class_type','title'=>'类型','width'=>20],
                    ['field'=>'teach_eid','title'=>'上课老师','width'=>20],
                    ['field'=>'plan_student_nums','title'=>'预招人数','width'=>20],
                    ['field'=>'student_nums','title'=>'班级人数','width'=>20],
			    ];
			    break;
		    case 'class_student':
                $arr = [
                    ['field'=>'sid','title'=>'学员姓名','width'=>20],
	                ['field'=>'bid','title'=>'校区','width'=>20],
	                ['field'=>'first_tel','title'=>'联系电话','width'=>20],
	                ['field'=>'cid','title'=>'班级','width'=>20],
	                ['field'=>'status','title'=>'状态','width'=>20],
                ];
			    break;
			default:
				# code...
				break;
		}


		return $arr;
	}

	protected function convert_type($key)
	{
		$map = [0=>'班课',1=>'1对1',2=>'1对多',3=>'研学旅行团'];
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

	protected function convert_class_type($key)
	{
		$map = [0=>'标准班级',1=>'临时班级',2=>'活动班级'];
		if(key_exists($key,$map)){
			return $map[$key];
		}
		return '-';
	}

	protected function convert_status($key)
    {
        $map = [1=>'正常',20=>'停课',30=>'休学',50=>'结课',90=>'退学',100=>'封存'];
        if(key_exists($key,$map)){
            return $map[$key];
        }
        return '-';
    }

    protected function convert_class_status($key)
    {
    	$map = [0=>'停课',1=>'正常',2=>'转出',9=>'结课'];
    	if(key_exists($key,$map)){
    		return $map[$key];
    	}
    	return '-';
    }

	protected function get_data()
	{

		$input = $this->params;
		$bid = $input['bid'];

		unset($input['bid']);

        $start_ts = isset($input['start_date']) ? strtotime($input['start_date'].' 00:00:01') : '';
    	$end_ts   = isset($input['end_date']) ? strtotime($input['end_date'].' 23:59:59') : '';

    	$start_int_day = isset($input['start_date']) ? format_int_day($input['start_date']) : '';
    	$end_int_day = isset($input['end_date']) ? format_int_day($input['end_date']) : '';

    	$params['between_ts'] = [$start_ts,$end_ts];
    	$params['between_int_day'] = [$start_int_day,$end_int_day];


        switch ($input['table']) {
			case 'employee_lesson_hour':
			    $model = new EmployeeLessonHour;
			    $w['int_day'] = ['between',$params['between_int_day']];
			    if($input['type'] == 'teach_lesson_amount'){
			    	$w['eid'] = $input['eid'];
			    }elseif($input['type'] == 'second_lesson_amount'){
			    	$w[] = ['exp',"find_in_set({$input['eid']},second_eids)"];
			    }elseif($input['type'] == 'edu_lesson_amount'){
			    	$w['edu_eid'] = $input['eid'];
			    }
			    $ret = $model->where($w)->getSearchResult();
				foreach ($ret['list'] as &$row) {
					$row['bid'] = get_branch_name($row['bid']);
					$row['time_section'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'-'.int_hour_to_hour_str($row['int_end_hour']); 
					$row['lesson_type'] = $this->convert_type($row['lesson_type']);
					$row['eid'] = get_teacher_name($input['eid']);
				}
				break;
			case 'employee_receipt':
			    $model = new EmployeeReceipt;
			    $w['receipt_time'] = ['between',$params['between_ts']];
			    $w['eid'] = $input['eid'];
                $w['amount'] = ['gt',0];
                $ret = $model->where($w)->getSearchResult([],false);
                foreach ($ret['list'] as &$row) {
					$row['bid'] = get_branch_name($row['bid']);
					$row['eid'] = get_teacher_name($input['eid']);
					$row['sid'] = get_student_name($row['sid']);
					$row['sale_role_did'] = get_did_value($row['sale_role_did']);
					$row['receipt_time'] = date('Y-m-d',strtotime($row['receipt_time']));
				}
			    break;
            case 'employee_refund_receipt':
                $model = new EmployeeReceipt;
                $w['receipt_time'] = ['between',$params['between_ts']];
                $w['amount'] = ['lt',0];
                $rbph = ReportBranchPerformanceSummary::get($input['id']);
                $w['bid'] = $rbph['bid'];
                if(isset($input['dept_id'])){
                    unset($w['bid']);
                    $bids = get_bids_by_dpt_id($input['dept_id']);
                    $w['bid'] = ['in',$bids];
                    $ret = $model->where($w)->getSearchResult([],false);
                }else{
                    $ret = $model->where($w)->getSearchResult([],false);
                }
                foreach ($ret['list'] as &$row) {
                    $row['bid'] = get_branch_name($row['bid']);
                    $row['sid'] = get_student_name($row['sid']);
                    $row['receipt_time'] = date('Y-m-d',strtotime($row['receipt_time']));
                }
                break;
            case 'cut_amount':
                $model = new StudentLessonHour;
                $w['int_day'] = ['between',$params['between_int_day']];
                $w['consume_type'] = 3;
                $rbph = ReportBranchPerformanceSummary::get($input['id']);
                $w['bid'] = $rbph['bid'];
                if(isset($input['dept_id'])){
                    unset($w['bid']);
                    $bids = get_bids_by_dpt_id($input['dept_id']);
                    $w['bid'] = ['in',$bids];
                    $ret = $model->where($w)->getSearchResult([],false);
                }else{
                    $ret = $model->where($w)->getSearchResult([],false);
                }
                foreach ($ret['list'] as &$row) {
                    $row['bid'] = get_branch_name($row['bid']);
                    $row['sid'] = get_student_name($row['sid']);
                    $row['int_day'] = date('Y-m-d',strtotime($row['int_day']));
                }
                break;
			case 'order_payment_history':
			    $model = new OrderPaymentHistory;
			    $w['create_time'] = ['between',$params['between_ts']];
                $rbph = ReportBranchPerformanceSummary::get($input['id']);
                $w['bid'] = $rbph['bid'];
                if(isset($input['dept_id'])){
                	unset($w['bid']);
                	$bids = get_bids_by_dpt_id($input['dept_id']);
                    $w['bid'] = ['in',$bids];
                    $ret = $model->where($w)->getSearchResult([],false);
                }else{
                	$ret = $model->where($w)->getSearchResult([],false);
                }
                foreach ($ret['list'] as &$row) {
                	$row['bid'] = get_branch_name($row['bid']);
                    $employee_receipt = EmployeeReceipt::get(['oid'=>$row['oid']]);
                    $row['sid'] = get_student_name($employee_receipt['sid']);
                    $row['eid'] = get_teacher_name($employee_receipt['eid']);
                	$row['create_time'] = date('Y-m-d',strtotime($row['create_time']));
                }
			    break;
			case 'student_lesson_hour':
			    $model = new StudentLessonHour;
			    $w['int_day'] = ['between',$params['between_int_day']];
			    $rbph = ReportBranchPerformanceSummary::get($input['id']);
                $w['bid'] = $rbph['bid'];
                if(isset($input['dept_id'])){
                	unset($w['bid']);
                	$bids = get_bids_by_dpt_id($input['dept_id']);
                    $w['bid'] = ['in',$bids];
                    $ret = $model->where($w)->getSearchResult([],false);
                }else{
                	$ret = $model->where($w)->getSearchResult([],false);
                }
                foreach ($ret['list'] as &$row) {
                	$row['bid'] = get_branch_name($row['bid']);
                	$row['sid'] = get_student_name($row['sid']);
                }
                break;
            case 'course_arrange':
                $model = new CourseArrange;
                $w['int_day'] = ['between',$params['between_int_day']];
                $rss = ReportStudentSummary::get($input['id']);
                $w['bid'] = $rss['bid'];
                if(isset($input['dept_id'])){
                	unset($w['bid']);
                	$bids = get_bids_by_dpt_id($input['dept_id']);
                	$w['bid'] = ['in',$bids];
                	$ret = $model->where($w)->getSearchResult([],false);
                }else{
                	$ret = $model->where($w)->getSearchResult([],false);
                }
                foreach ($ret['list'] as &$row) {
                	$row['bid'] = get_branch_name($row['bid']);
                	$row['lesson_type'] = $this->convert_type($row['lesson_type']);
                	$row['teach_eid'] = get_teacher_name($row['teach_eid']);
                	$row['cr_id'] = get_class_room($row['cr_id']);
                	$row['time_section'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'-'.int_hour_to_hour_str($row['int_end_hour']); 
                	$row['is_attendance'] = $this->convert_attendance($row['is_attendance']);
                }
                break;
            case 'course_arrange_student':
                $model = new CourseArrangeStudent;
                $w['int_day'] = ['between',$params['between_int_day']];
                $rss = ReportStudentSummary::get($input['id']);
                $w['bid'] = $rss['bid']; 
                if(isset($input['dept_id'])){
		            unset($w['bid']);
		            $bids = get_bids_by_dpt_id($input['dept_id']);
		            $w['bid'] = ['in',$bids];
		            $ca_ids = (new CourseArrange)->where($w)->column('ca_id');
		        }else{
		        	$ca_ids = (new CourseArrange)->where($w)->column('ca_id');
		        }

		        $w_ca['ca_id'] = ['in',$ca_ids];
                $ret = $model->where($w_ca)->getSearchResult([],false);
                
                foreach ($ret['list'] as &$row) {
                	$row['student'] = $row['sid'] ? get_student_name($row['sid']) : get_customer_name($row['cu_id']);
                	$row['time_section'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'-'.int_hour_to_hour_str($row['int_end_hour']); 
                	$row['is_leave'] = $row['is_leave'] ? '已请假' : '未请假';
                	$row['is_attendance'] = $row['is_attendance'] ? '已上课' : '未上课';

                }
                break;
            case 'student_attendance':
                $model = new StudentAttendance;
                $w['int_day'] = ['between',$params['between_int_day']];
                $rss = ReportStudentSummary::get($input['id']);
                $w['bid'] = $rss['bid'];
                if(isset($input['dept_id'])){
                	unset($w['bid']);
                	$bids = get_bids_by_dpt_id($input['dept_id']);
                	$w['bid'] = ['in',$bids];
                	$ret = $model->where($w)->getSearchResult([],false);
                }else{
                	$ret = $model->where($w)->getSearchResult([],false);
                }
                foreach ($ret['list'] as &$row) {
			    	$row['bid'] = get_branch_name($row['bid']);
			    	$row['sid'] = get_student_name($row['sid']);
			    	$row['is_in'] = $row['is_in'] ? '出勤' : '缺勤';
			    	$row['in_time'] = date('m-d H:i',strtotime($row['in_time']));
			    }
                break;
            case 'student_leave':
                $model = new StudentLeave;
                $w['int_day'] = ['between',$params['between_int_day']];
                $rss = ReportStudentSummary::get($input['id']);
                $w['bid'] = $rss['bid'];
                if(isset($input['dept_id'])){
                	unset($w['bid']);
                	$bids = get_bids_by_dpt_id($input['dept_id']);
                	$w['bid'] = ['in',$bids];
                	$ret = $model->where($w)->getSearchResult([],false);
                }else{
                	$ret = $model->where($w)->getSearchResult([],false);
                }
                foreach ($ret['list'] as &$row) {
			    	$row['bid'] = get_branch_name($row['bid']);
			    	$row['sid'] = get_student_name($row['sid']);
			    	$row['time_section'] = int_day_to_date_str($row['int_day']).' '.int_hour_to_hour_str($row['int_start_hour']).'-'.int_hour_to_hour_str($row['int_end_hour']); 
			    }
                break;
            case 'student':
                $model = new Student;
                $w_student['bid'] = isset($input['branch_id']) ? $input['branch_id'] : '';
                if(isset($input['school_id'])){
                	$w_student['status'] = 1;
                	$school_id = m('public_school')->where('school_name',$input['school_id'])->value('ps_id');
                	$input['school_id'] = $school_id;
                }
                if(isset($input['eid'])){
                    if($input['stype'] == 1){
                        $w['lesson_type'] = 1;
                    }else{
                    	$w['lesson_type'] = 2;
                    }

                	$w['teach_eid'] = $input['eid'];
	                $w['is_trial'] = 0;
	                $w['is_cancel'] = 0;
	                $res = (new CourseArrange)->where($w)->getSearchResult($input);
	                $ca_ids = array_column($res['list'],'ca_id');
	                $w_cas['ca_id'] = ['in',$ca_ids];
	                $rets = (new CourseArrangeStudent)->where($w_cas)->getSearchResult();
	                $sids = array_column($rets['list'],'sid');
	                $sids = array_unique($sids);
	                $w_s['sid'] = ['in',$sids];
	                $w_s['bid'] = $bid;
	                $ret = (new Student)->where($w_s)->getSearchResult([],false);
                }else{
                	$ret = $model->where($w_student)->getSearchResult($input,[],false);
                }
                
                foreach ($ret['list'] as &$row) {
                	$row['bid'] = get_branch_name($row['bid']);
                	$row['status'] = $this->convert_status($row['status']);
                }
                break;
            case 'class':
                $model = new Classes;
                $input['bid'] = $bid;
                $w_class['teach_eid'] = $input['eid'];
                $w_class['status'] = ['in',['0','1']];
                $ret = $model->where($w_class)->getSearchResult($input,[],false);
                foreach ($ret['list'] as &$row) {
                	$row['class_type'] = $this->convert_class_type($row['class_type']);
                	$row['teach_eid'] = get_teacher_name($row['teach_eid']);
                }
                break;
                
            case 'class_student':
                $w_cs['bid'] = $bid;
                $w_cs['status'] = ['in',['0','1']];
                $w_cs['teach_eid'] = $input['eid'];
                $cids = (new Classes)->where($w_cs)->column('cid');
                $model = new ClassStudent;
                $w_c['cid'] = ['in',$cids];
                $w_c['status'] = 1;
                $ret = $model->where($w_c)->getSearchResult([],false);
                foreach ($ret['list'] as &$row) {
                	$sinfo = get_student_info($row['sid']);
                	$row['sid'] = $sinfo['student_name'];
                	$row['bid'] = get_branch_name($sinfo['bid']);
                	$row['first_tel'] = $sinfo['first_tel'];
                	$row['cid'] = get_class_name($row['cid']);
                	$row['status'] = $this->convert_class_status($row['status']);
                }

                break;
			default:
				# code...
				break;
		}

        if(!empty($ret['list'])){
        	return collection($ret['list'])->toArray();
        }
        return [];
	}


}