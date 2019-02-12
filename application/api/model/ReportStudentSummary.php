<?php 

namespace app\api\model;

use app\common\Report;

class ReportStudentSummary extends Report
{
	protected $report_name = '在读学员统计表';
	protected $report_table_name = 'report_student_summary';


	/**
     * 获取查询报表
     * @param $input
     */
    public function getDaySectionReport($input,$pagenation = false){
        if(!isset($input['start_date'])){
            $ds = current_week_ds();
            $input['start_date'] = $ds[0];
            $input['end_date']   = $ds[1];
        }
        $w['start_int_day'] = format_int_day($input['start_date']);
        $w['end_int_day']   = format_int_day($input['end_date']);

        $day_diff = int_day_diff($w['start_int_day'],$w['end_int_day']);

        if($day_diff > 31){
            return $this->user_error('查询时间间隔不可超过1个月');
        }
        $og_id = gvar('og_id');
        $user  = gvar('user');

        $request_bids = isset($user['employee'])?$user['employee']['bids']:[];

        $query_bids = [];
        $build_bids = [];
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
        
        $model = new self();
        $ret = $model->skipBid()->where($w)->getSearchResult($input,false);

        if(!isset($ret['total'])) {
            $ret['total'] = count($ret['list']);

        }
        if(!isset($ret['params'])){
            $ret['params'] = $input;

        }
        $result_bids = [];
        if(!$ret || isset($input['refresh']) && $input['refresh'] == 1){
            $ret = [];
            $build_bids = $query_bids;
        }else{
            if(count($ret['list']) < count($query_bids)){
                $result_bids = array_column($ret['list'],'bid');
                $build_bids = array_values(array_diff($query_bids,$result_bids));
            }
        }
        /*
        print_r($w);
        echo($model->getLastSql());
        print_r($input);
        print_r($query_bids);
        print_r($ret);
        print_r($build_bids);
        exit;
        */
        if(!empty($build_bids)){
            foreach($build_bids as $bid){
               $this->buildDaySectionReport($input['start_date'],$input['end_date'],$bid);
            }
            $ret = $model->where($w)->getSearchResult($input,false);
            if(!isset($ret['total'])) {
                $ret['total'] = count($ret['list']);

            }
            if(!isset($ret['params'])){
                $ret['params'] = $input;

            }
        }

        $ret['params'] = $input;

        $enable_company = user_config('params.enable_company');
        if($enable_company){
            $ret['list1'] = $this->getCompanyList($ret['list']);

        }else{
            $ret['list1'] = [];
        }

        if(!isset($ret['params']['pagesize'])){
            $ret['params']['pagesize'] = 100;
        }


        if(isset($ret['params']) && isset($ret['params']['page'])){
            $ret['page'] = $ret['params']['page'];
            $ret['pagesize'] = $ret['params']['pagesize'];
        }


        return $ret;

    }

	protected $report_fields = [
        'course_arrange_times' => ['title'=>'排课次数','type'=>Report::FTYPE_INT],
        'course_arrange_student_times' => ['title'=>'排课人次数','type'=>Report::FTYPE_INT],
        'course_arrange_student_nums' => ['title'=>'排课人数','type'=>Report::FTYPE_INT],
        'student_attendance_times' => ['title'=>'出勤人次数','type'=>Report::FTYPE_INT],
        'student_attendance_nums' => ['title'=>'出勤人数','type'=>Report::FTYPE_INT],
        'student_leave_times' => ['title'=>'请假人次数','type'=>Report::FTYPE_INT],
        'student_leave_nums' => ['title'=>'请假人数','type'=>Report::FTYPE_INT],
	];


	/**
     * 生成报表前段
     * @param  [type] &$params [description]
     * @return [type]          [description]
     */
    protected function build_day_section_report_before(&$params){
    	$this->count_course_arrange($params);
    	$this->count_student_attendance($params);
        $this->count_student_leave($params);
    }
    
    /**
     * 统计排课次数 排课人次数 排课人数
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function count_course_arrange($params)
    {
    	// print_r($params);exit;
    	$course_arrange_times = 0;
    	$course_arrange_student_times = 0;
    	$course_arrange_student_nums = 0;

    	$w_ca['bid'] = $params['bid'];
        $w_ca['int_day'] = ['between',$params['between_int_day']];
        $sids = [];
        foreach (get_all_rows('course_arrange',$w_ca) as $ca) {
        	$course_arrange_times ++;
        	$w_cas['ca_id'] = $ca['ca_id'];
        	$sid = (new CourseArrangeStudent)->where('ca_id',$ca['ca_id'])->column('sid');
        	$sids = array_merge($sids,$sid);
        	$course_arrange_student_times = count($sids);
        	$course_arrange_student_nums = count(array_unique($sids));
        }
        $this->bid_row_field_value['course_arrange_times'] = $course_arrange_times;
        $this->bid_row_field_value['course_arrange_student_times'] = $course_arrange_student_times;
        $this->bid_row_field_value['course_arrange_student_nums'] = $course_arrange_student_nums;

    }

    /**
     * 统计出勤人次数 出勤人数
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function count_student_attendance($params)
    {
    	$student_attendance_times = 0;
    	$student_attendance_nums = 0;

    	$w_sa['bid'] = $params['bid'];
    	$w_sa['int_day'] = ['between',$params['between_int_day']];

    	$sids = (new StudentAttendance)->where($w_sa)->column('sid');
    	$student_attendance_times = count($sids);
    	$student_attendance_nums = count(array_unique($sids));

    	$this->bid_row_field_value['student_attendance_times'] = $student_attendance_times;
    	$this->bid_row_field_value['student_attendance_nums'] = $student_attendance_nums;

    }


    /**
     * 统计请假人次数 请假人数
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function count_student_leave($params)
    {
    	$student_leave_times = 0;
    	$student_leave_nums = 0;

    	$w_sl['bid'] = $params['bid'];
    	$w_sl['int_day'] = ['between',$params['between_int_day']];

    	$sids = (new StudentLeave)->where($w_sl)->column('sid');
    	$student_leave_times = count($sids);
    	$student_leave_nums = count(array_unique($sids));

    	$this->bid_row_field_value['student_leave_times'] = $student_leave_times;
    	$this->bid_row_field_value['student_leave_nums'] = $student_leave_nums;

    }








}