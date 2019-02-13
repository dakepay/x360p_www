<?php
/**
 * Author: luo
 * Time: 2017-11-18 11:01
**/

namespace app\api\controller;

use app\api\model\CourseArrange;
use app\api\model\CourseArrangeStudent;
use app\api\model\Employee;
use app\api\model\EmployeeLessonHour;
use app\api\model\EmployeeReceipt;
use app\api\model\EmployeeStudent;
use app\api\model\Student;
use app\api\model\StudentAttendance;
use app\api\model\StudentLeave;
use app\api\model\StudentLessonHour;
use think\Request;
use app\api\model\StudentLesson;
use app\api\model\Classes;
use app\api\model\CustomerFollowUp;
use app\api\model\TrialListenArrange;
use think\Db;

class Dashboard extends Base
{

    private $expire_time = 5; # 缓存时间，单位：秒

    /**
     * @desc  学生课时预警
     * @author luo
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method GET
     */
    public function student_lesson_warn(Request $request)
    {
        $input = $request->param();
        $model = new StudentLesson();
        $input['remain_lesson_hours'] = 1;
        $ret = $model->with(['student','lesson'])->getSearchResult($input);
        $es = new EmployeeStudent();
        foreach($ret['list'] as &$row) {
            if(!empty($row['student'])) {
                $employee_student = $es->where('sid', $row['student']['sid'])->find();
                if(empty($employee_student)) {
                    $row['student']['ename'] = '';
                } else {
                    $employee = Employee::get($employee_student['eid']);
                    $row['student']['ename'] = empty($employee) ? '' : $employee['ename'];
                }
            }
        }
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  学生请假预警
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function student_leave_warn(Request $request)
    {
        $page = input('page', 1);
        $pagesize = input('pagesize', 10);
        $leave_num = input('leave_num', 4);
        $bid = $request->bid;
        $m_sl = new StudentLeave();
        $total = $m_sl->where('bid', $bid)->group('sid')->having("leave_num >= {$leave_num}")->field('sid,count(sid) as leave_num')
            ->count('sid');
        $list = $m_sl->where('bid', $bid)->group('sid')->having("leave_num >= {$leave_num}")
            ->order('leave_num desc')->field('sid,count(sid) as leave_num')
            ->page($page, $pagesize)->select();
        $m_student = new Student();
        foreach($list as $row) {
            $row['student'] = $m_student->where('sid', $row['sid'])->field('sid,student_name,first_tel')->find();
        }

        $ret = [
            'total' => $total,
            'list' => $list,
            'page' => $page,
            'pagesize' => $pagesize
        ];
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  班级课次预警
     * @author luo
     * @method GET
     */
    public function class_times_warn(Request $request)
    {
        $input = $request->param();
        $input['arrange_times'] = ['gt',0];
        $model = new Classes();
        $ret = $model->with('lesson')->where('lesson_times = arrange_times + 1')->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  学员流失预警
     * @author luo
     * @method GET
     */
    public function student_lost_warn(Request $request)
    {
        $input = $request->param();
        $model = new Student();
        $ret = $model->where('last_attendance_time > 0')
            ->where('last_attendance_time', '<', strtotime('30 day ago'))
            ->getSearchResult($input);

        $ret['list'] = array_map(function($val){
            $val['create_time'] = date('Y-m-d', strtotime($val['create_time']));
            $val['lost_day'] = floor((time()-strtotime($val['last_attendance_time'])) / (24 * 3600));
            return $val;
        },$ret['list']);

        $es = new EmployeeStudent();
        foreach($ret['list'] as &$row) {
            $employee_student = $es->where('sid', $row['sid'])->find();
            if(empty($employee_student)) {
                $row['ename'] = '';
            } else {
                $employee = Employee::get($employee_student['eid']);
                $row['ename'] = empty($employee) ? '' : $employee['ename'];
            }
        }

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  今日试听提醒
     * @author luo
     */
    public function today_trial(Request $request)
    {
        $input = $request->param();

        $model = new \app\api\model\TrialListenArrange();
        $ret = $model->where('int_day', date('Ymd', time()))->with(['customer', 'student'])->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  生日提醒
     * @author luo
     * @method GET
     */
    public function birthday_remind(Request $request)
    {
        $input = $request->get();
        $bid = $request->bid;
        if(isset($input['month'])){
            $month = intval($input['month']);
            $day   = [];
        }else{
            $month = date('m', time());
            //$day = date('d', time());
            $input_birth_day = input('birth_day');
            if(is_numeric($input_birth_day)) {
                $day = ['birth_day' => (int)input('birth_day')];
            } elseif(is_string($input_birth_day)) {
                $input_birth_day_arr = explode(',', $input_birth_day);
                if(count($input_birth_day_arr) == 2) {
                    sort($input_birth_day_arr);
                    $day = ['birth_day' => ['between', $input_birth_day]];
                } else {
                    $day = ['birth_day' => $input_birth_day_arr[0]];
                }
            } else {
                $day = ['birth_day' => date('d', time())];
            }
        }

        $type = isset($input['type'])?$input['type']:'student';

        $w['og_id'] = gvar('og_id');
        $w['birth_month'] = $month;

        if(!empty($day)){
            $w = array_merge($w,$day);
        }
        if($type == 'student'){
            $w['bid'] = $bid;
            if(isset($input['name'])){
                $w['student_name'] = ['LIKE',"%".$input['name']."%"];
            }

            $mStudent = new Student();
            $result = $mStudent->where($w)->getSearchResult($input,true);

        }else{
            if(isset($input['name'])){
                $w['ename'] = ['LIKE',"%".$input['name']."%"];
            }

            $mEmployee = new Employee();
            $result = $mEmployee->where($w)->where("find_in_set({$bid}, bids)")->getSearchResult($input,true);
        }


        if(isset($result['list']) && !empty($result['list'])){
            foreach($result['list'] as $k=>$r){
                $result['list'][$k]['age'] = $r['birth_time'] ? date('Y', time()) - date('Y', strtotime($r['birth_time'])) : 0;
            }
        }
        return $this->sendSuccess($result);
    }

    /**
     * @desc  近七日学生课耗相关统计
     * @author luo
     * @method GET
     */
    public function student_stats(Request $request)
    {
        $client = gvar('client');
        $cid = $client['cid'];
        $rkey = sprintf("dashboard_student_stats_%s_%s",$cid,gvar('og_id'));
        $stats = redis()->get($rkey);
        if(!empty($stats)) return $this->sendSuccess($stats);

        $lesson_stats = [];
        for($i = 0; $i <= 6; $i++) {
            $day = date('Ymd', strtotime($i.' day ago'));
            $lesson_stats[$i]['day'] = $day;
            $lesson_stats[$i]['lesson_num'] = (new StudentLesson())->countNumOfDay($day);
        }

        $attendance_stats = [];
        $s_a_model = new StudentAttendance();
        for($i = 0; $i <= 6; $i++) {
            $day = date('Ymd', strtotime($i.' day ago'));
            $attendance_num = $s_a_model->countAttendanceOfDay($day);
            $should_num = $s_a_model->countShouldAttendanceOfDay($day);

            $attendance_stats[$i] = [
                'day' => $day,
                'rate' => $attendance_num ? round($attendance_num / $should_num, 2) : 0,
                'attendance_num' => $attendance_num,
                'should_num' => $should_num,
            ];
        }

        $consume_stats = [];
        $cs_model = new StudentLessonHour();
        for($i = 0; $i <= 6; $i++) {
            $day = date('Ymd', strtotime($i.' day ago'));
            $num = $cs_model->countConsumeOfDay($day);

            $consume_stats[$i] = [
                'day' => $day,
                'num' => $num,
            ];
        }

        $stats = [
            'lesson_stats' => array_reverse($lesson_stats),
            'attendance_stats' => array_reverse($attendance_stats),
            'consume_stats' => array_reverse($consume_stats),
        ];

        redis()->set($rkey, $stats, $this->expire_time);

        return $this->sendSuccess($stats);

    }

    /**
     * @desc  考勤看板的数据
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function today_course(Request $request)
    {
        $data = redis()->get('dashboard_attendance_data');
        if(!empty($data)) return $this->sendSuccess($data);

        $att_data = [];

        $today = date('Ymd', time());

        $atd_model = new StudentAttendance();
        $att_data['student_num'] = $atd_model->countShouldAttendanceOfDay($today);
        $att_data['student_attendance_num'] = $atd_model->countAttendanceOfDay($today);
        $att_data['student_leave_num'] = (new StudentLeave())->countLeaveOfDay($today);
        $att_data['attendance_rate'] = $att_data['student_attendance_num']
            ? round(($att_data['student_attendance_num'] / $att_data['student_num']),2) : 0;

        $ca_model = new CourseArrange();
        $att_data['course_num'] = $ca_model->countCourseOfDay($today);
        $is_attendance = [CourseArrange::IS_ATTENDANCE_YES, CourseArrange::IS_ATTENDANCE_SOME];
        $att_data['course_attendance_num'] = $ca_model
            ->countCourseOfDay($today, $is_attendance);

        $att_data['class_num'] = $ca_model->countCourseOfDay($today, null, CourseArrange::LESSON_TYPE_CLASS);
        $att_data['class_attendance_num'] =
            $ca_model->countCourseOfDay($today, $is_attendance, CourseArrange::LESSON_TYPE_CLASS);

        $course_list = $ca_model->with('one_class')->where('bid', $request->bid)
            ->where('int_day', $today)->order('int_start_hour asc')->select();

        $data = [
            'attendance' => $att_data,
            'course'     => $course_list ? $course_list : [],
        ];

        redis()->set('dashboard_attendance_data', $data, $this->expire_time);

        return $this->sendSuccess($data);
    }

    /**
     *  未考勤学员列表
     * @param Request $request
     */
    public function today_no_attendance(Request $request)
    {
        $today = date('Ymd', time());

        $sql = 'select * from x360p_course_arrange_student where ca_id in (select ca_id from x360p_course_arrange where int_day = '.$today.')';


        $student_list = DB::query($sql);
        foreach ($student_list as $k => $student){
            if ($student['is_in'] == 1){
                unset($student_list[$k]);
            }
        }
        $student_list = array_values($student_list);

        return $this->sendSuccess($student_list);
    }

    /**
     * @desc    咨询师工作台数据汇总
     * @param Request $request
     * @method GET
     */
    public function cc(Request $request){
        $input = input();
        $mPerformance = new EmployeeReceipt();
        $data['list'] = [];
        $where = '';

        if ($input['type'] == 'month'){
            $time = time() - 30 * 86400;
            $where = ' where create_time > '.$time;
        }elseif($input['type'] == 'year'){
            $time = time() - 30 * 12 * 86400;
            $where = ' where create_time > '.$time;
        }elseif($input['type'] == 'week'){
            $time = time() - 7 * 86400;
            $where = ' where create_time > '.$time;
        }

        if (!empty($input['bid'])){
            $where .= ' and bid = '.$input['bid'];
        }

        $sql = 'select eid,sum(amount) as total from x360p_employee_receipt'.$where.' group by eid  order by total desc';
        $data['list'] = $mPerformance->query($sql);
        $employee = gvar('user.employee');

        $mCfu = new CustomerFollowUp();
        $mTla = new TrialListenArrange();
        $data['today_follow_up'] = $mCfu->getTodayFollowUp($employee['eid']);
        $data['today_promise'] = $mCfu->getTodayPromise($employee['eid']);
        $data['recent_contact'] = $mCfu->where(['eid'=>$employee['eid'],'system_op_type' => 0])->order(['create_time'=>'desc'])->limit(5)->select();
        $data['trial_listen'] = $mTla->where(['create_uid'=>$employee['eid'],'is_arrive' => 1])->order(['create_time'=>'desc'])->limit(5)->select();

        foreach ($data['recent_contact'] as $k => $v){
            $data['recent_contact'][$k]['customer'] = get_customer_info($v['cu_id']);
        }
        $data['recent_assigned'] = $mCfu->where(['eid'=>$employee['eid']])->where('system_op_type','GT',0)->order(['create_time'=>'desc'])->limit(5)->select();
        foreach ($data['recent_assigned'] as $k => $v){
            $data['recent_assigned'][$k]['customer'] = get_customer_info($v['cu_id']);
        }
        foreach ($data['trial_listen'] as $k => $v){
            $data['trial_listen'][$k]['customer'] = get_customer_info($v['cu_id']);
            $data['trial_listen'][$k]['student'] = get_student_info($v['sid']);
        }


        $data['today_trial_listen'] = $mTla->getTodayTrialListen($employee['eid']);

        if (!empty($data['list'])){
            foreach ($data['list'] as $k => $v){
                $data['list'][$k]['name'] = get_employee_name($v['eid']);
            }
        }else{
            return $this->sendSuccess($data);
        }
        return $this->sendSuccess($data);
    }

    /**
     * 考勤预警
     * @param Request $request
     */
    public function attendance_warning(Request $request)
    {
        $input = input();
        $bid = auto_bid();
        $where = 'where is_delete = 0 and bid = '.$bid;

        if (isset($input['int_day'])){
            $where .= ' and int_day = '.format_int_day($input['int_day']);
        }else{
            $int_day = int_day(time());
            $where .= ' and int_day < ' . $int_day;
        }

        $result = [];
        //  历史所有排课
        $sql_attendance_total = 'select count(*) as num from x360p_course_arrange ' . $where;
        $result['attendance_total'] = DB::query($sql_attendance_total)[0]['num'];

        //  历史排课全勤
        $sql_attendance_full = 'select count(*) as num from x360p_course_arrange ' . $where . ' and is_attendance = 2 ';
        $result['attendance_full'] = DB::query($sql_attendance_full)[0]['num'];

        //  历史排课部分考勤
        $sql_attendance_part = 'select count(*) as num from x360p_course_arrange  ' . $where . ' and is_attendance = 1';
        $result['attendance_part'] = DB::query($sql_attendance_part)[0]['num'];

        //  历史排课未考勤
        $sql_attendance_no = 'select count(*) as num from x360p_course_arrange ' . $where . ' and is_attendance = 0 ';
        $result['attendance_no'] = DB::query($sql_attendance_no)[0]['num'];

        return $this->sendSuccess(['list' => $result]);
    }

    /**
     * @desc  员工业绩排行
     * @method POST
     */
    public function receipt_ranking(Request $request){

        $input = $request->get('type');
        $mEmployeeReceipt = new EmployeeReceipt();
        $data['list'] = [];
        if ($input == 'month'){
            $create_time = time() - 30 * 86400;
            $sql = 'select eid,sum(amount) as total from x360p_employee_receipt where create_time > '.$create_time.' group by eid  order by total desc';
            $data['list'] = $mEmployeeReceipt->query($sql);
        }elseif($input == 'year'){
            $create_time = time() - 30 * 12 * 86400;
            $sql = 'select eid,sum(amount) as total from x360p_employee_receipt where create_time > '.$create_time.' group by eid  order by total desc';
            $data['list'] = $mEmployeeReceipt->query($sql);
        }
        if ($data['list']){
            foreach ($data['list'] as $k => $v){
                $data['list'][$k]['name'] = get_employee_name($v['eid']);
            }
        }else{
            return $this->sendSuccess($data);
        }
        return $this->sendSuccess($data);
    }

    /**
     * 学管师课时排行
     * @param Request $request
     */
    public function class_hour_ranking(Request $request)
    {
        $input = $request->get('type');
        $mElh = new EmployeeLessonHour();
        $data['list'] = [];
        if ($input == 'month'){
            $create_time = time() - 30 * 86400;
            $sql = 'select eid,sum(lesson_hours) as total from x360p_employee_lesson_hour where create_time > '.$create_time.' group by eid  order by total desc';
            $data['list'] = $mElh->query($sql);
        }elseif($input == 'year'){
            $create_time = time() - 30 * 12 * 86400;
            $sql = 'select eid,sum(lesson_hours) as total from x360p_employee_lesson_hour where create_time > '.$create_time.' group by eid  order by total desc';
            $data['list'] = $mElh->query($sql);
        }
        if ($data['list']){
            foreach ($data['list'] as $k => $v){
                $data['list'][$k]['name'] = get_employee_name($v['eid']);
            }
        }else{
            return $this->sendSuccess($data);
        }
        return $this->sendSuccess($data);
    }


}