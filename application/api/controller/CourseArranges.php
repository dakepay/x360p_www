<?php
/**
 * Author: luo
 * Time: 2017-10-11 18:54
**/

namespace app\api\controller;

use app\api\model\Classroom;
use app\api\model\Classes;
use app\api\model\ClassStudent;
use app\api\model\CourseArrange;
use app\api\model\CourseArrangeStudent;
use app\api\model\CourseRemindLog;
use app\api\model\CourseAutoPlanLog;
use app\api\model\Employee;
use app\api\model\EmployeeStudent;
use app\api\model\Lesson;
use app\api\model\Student;
use app\api\model\StudentAbsence;
use app\api\model\StudentAttendSchoolLog;
use app\api\model\StudentLeave;
use app\api\model\StudentLesson;
use app\api\model\Subject;
use app\common\db\Query;
use DateInterval;
use DatePeriod;
use DateTime;
use think\Exception;
use think\Request;
use app\api\model\CourseArrange as CourseArrangeModel;
use app\api\model\Classes as ClassesModel;

class CourseArranges extends Base
{

    public function get_list(Request $request)
    {
        /** @var Query $model */
        $model = new CourseArrangeModel();
        $input = $request->param();
        $with = isset($input['with']) ?$input['with']:[];
        $with = is_string($with) ? explode(',', $with) : $with;
        if(($key = array_search('students', $with)) !== false) {
            $with_students = true;
            unset($with[$key]);
            $input['with'] = implode(',', $with);
        }

        $skip_bid = false;

        if(isset($input['teach_eid']) ){
            $login_user = gvar('user');
            $login_employee = $login_user['employee'];
            $login_eid = $login_employee['eid'];


            if($login_eid == $input['teach_eid'] ){

                $eid = $input['teach_eid'];
                $where_str = "find_in_set({$eid}, second_eids) or teach_eid = {$eid}";

                $m_classes = new ClassesModel();
                $my_cls_list = $m_classes->where('edu_eid',$login_eid)->whereOr('second_eid',$login_eid)->where('status','LT',2)->select();
                if($my_cls_list){
                    $cids = [];
                    foreach($my_cls_list as $c){
                        array_push($cids,$c['cid']);
                    }
                    $where_str .= " or cid in (".implode(',',$cids).")";
                    //$input['cid'] = '[IN,'.implode(',',$cids).']';
                }
                unset($input['teach_eid']);
                $model->where($where_str);

            }
            $skip_bid = true;
        }



        if (isset($input['out_time']) && isset($input['out_time']) == 1){
            $model->where('is_attendance','LT','2');
        }

        if(isset($input['sid'])) {
            //$cids = (new ClassStudent())->where('sid', $input['sid'])->column('cid');
            //$cids = array_unique($cids);
            //$where = !empty($cids) ? sprintf('ca.cid in (%s) or ', implode(',', $cids)) : '';
            $model->refreshStudentArrange($input['sid']);
            $where = '';
            $where .= 'cas.sid = ' . $input['sid'];
            $where .= ' and cas.delete_time is NULL';

            $m_ca = new CourseArrange();
            $fields = $m_ca->getTableFields();
            $input_where = [];
            if(!empty($input)) {
                foreach($input as $key => $val) {
                    if(in_array($key, $fields) && $key != 'sid') {
                        $input['ca.'.$key] = $val;
                        unset($input[$key]);
                    }
                }
            }

            $sort = input('order_sort', 'asc');

            $ret = $model->alias('ca')->join('course_arrange_student cas', 'ca.ca_id = cas.ca_id', 'left')
                ->where($where)->where($input_where)
                ->field('ca.*')
                ->order('ca.int_day',$sort)
                
                ->getSearchResult($input);

        } else {
            $ret = $model->withSum('consume_lesson_hour')->skipBid($skip_bid)->getSearchResult($input);
        }

        foreach($ret['list'] as &$course) {

            //是否返回排课的班级学员信息
            if(!empty($with_students) && !empty($course)) {
                $course['students'] = $model->getAttObjects($course['ca_id'],false,false);
            }
            if($course['is_attendance'] > 0){
                $w_catt['ca_id'] = $course['ca_id'];
                $catt_info = get_catt_info($w_catt);
                $course['consume_lesson_hour'] = $catt_info['consume_lesson_hour'];
            }

            $course['reason'] = empty($course['reason']) || is_null($course['reason']) ? '' : $course['reason'];
        
        }

        return $this->sendSuccess($ret);
    }

    /**
     * 获取临时班级排课
     * @param  Request $request [description]
     * @return [type]           [get]
     */
    public function tmp_course_arranges(Request $request)
    {
        $input = $request->param();
        $model = new CourseArrangeModel;
        $bid = $request->header('x-bid');
 
        $w_class['class_type'] = Classes::CLASS_TYPE_TMP;
        $w_class['bid'] = $bid;
        $w_class['og_id'] = gvar('og_id');
        if(isset($input['class_name'])){
            $w_class['class_name'] = ['like','%'.$input['class_name'].'%'];
        }

        $cids = (new Classes)->where($w_class)->column('cid');
        if(empty($cids)) return $this->sendSuccess();

        $w = [];
        $w['cid'] = ['in',$cids];

        $ret = $model->where($w)->getSearchResult($input);
        foreach ($ret['list'] as &$row) {
            $row['course_arrange_student'] = get_table_list('course_arrange_student',$w);
	    $row['student_num'] = 0;
	    if($row['course_arrange_student']){
	    	$row['student_num'] = count($row['course_arrange_student']);
	    }
        }

        return $this->sendSuccess($ret);

    }


    public function get_detail(Request $request, $id = 0)
    {
        $ca_id = $id;
        $m_ca = new CourseArrange();
        $get = $request->get();
        $with = !isset($get['with']) ? [] : (is_string($get['with']) ? explode(',', $get['with']) : $get['with']);
        if(($key = array_search('students', $with)) !== false) {
            $with_students = true;
            unset($with[$key]);
        }

        /** @var CourseArrange $course */
        $course = $m_ca->with($with)->find($ca_id);

        //是否返回排课的班级学员信息
        if(!empty($with_students) && !empty($course)) {
            $course['students'] = $course->getAttObjects();
        }

        return $this->sendSuccess($course);
    }

    /**
     * @param Request $request
     * @desc  增加排课，可以选取多个班级
     * @url   /api/course_arranges/
     * @method POST
     * @return
     */
    public function post(Request $request) {
        $input  = $request->post();
        $course = new CourseArrangeModel();

        //--1-- 多个班级，批量排课
        if (!empty($input['cids'])) {
            $cids = array_unique(array_filter($input['cids']));
            $rs = $course->batAutoCreateClassArrange($cids);

        //--2-- 一对一、一对多排课
        } elseif (isset($input['lesson_type']) && !empty(intval($input['lesson_type']))) {
            $rs = $course->addCourseOfStu($input);

        //--3-- 一个班级排课
        } elseif (!empty($input['cid']) && is_numeric($input['cid'])) {
            $class = ClassesModel::get($input['cid']);
            if(!$class) {
                return $this->sendError(400, '班级不存在或已删除');
            }
            $rs = $course->addCourseOfClass($class, $input);

        //--4-- 创建试听排课
        } elseif (isset($input['is_trial']) && $input['is_trial'] == 1) {
            $rs = $course->createOneCourse($input);

        } else {
            return $this->sendError(400, '参数不匹配!');
        }

        if(!$rs) return $this->sendError(400, $course->getErrorMsg());

        return $this->sendSuccess('操作成功');
    }

    /**
     * 规律排课
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function law_course_arranges(Request $request)
    {
        $loop_arranges = $request->post();
        $course = new CourseArrangeModel;

        $res = $course->addLawCourseArrange($loop_arranges);

        $data = [
            'success' => $res['success'],
            'fail'    => $res['fail'],
        ];
        
        // 如果班级排课成功数大于班级规定的排课次数  则更新班级排课次数
        if($loop_arranges[0]['cid']){
            $cid = $loop_arranges[0]['cid'];
            $class_info = Classes::get($cid);
            if($data['success'] > $class_info['lesson_times']){
                $class_info->lesson_times = $data['success'];
                $class_info->save();
            }
        }
        
        return $this->sendSuccess($data);
    }

    /*{
        "cr_id":5,
        "lesson_type":0,
        "lid":0,
        "sj_id":0,
        "sid":0,
        "sids":[],
        "grade":0,
        "consume_lesson_hour":0,
        "end_type":2,  
        "cid":66,
        "start_date":"2018-10-31",
        "end_date":"2018-11-30",
        "int_start_hour":"15:00",
        "int_end_hour":"16:00",
        "teach_eid":10008,
        "ignore_class_ec":0,
        "ignore_class_cc":0,
        "second_eids":[],
        "arrange_times":5,
        "loop_type":5,
        "arrange_weeks":[2,3,4]
    }*/

    /**
     * 一次性检测排课冲突
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function check_arranges(Request $request){
        $input = $request->post();

        $loop_arranges = $this->build_loop_arranges($input);

        $error = [];
        foreach ($loop_arranges as $per_arrange) {
            $error[] = $this->check_arrange_item($per_arrange);
        }

        return $this->sendSuccess($error);
    }
    
    /**
     * 一次性提交规律排课
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function law_course_arrange(Request $request)
    {
        $input = $request->post();
        $loop_arranges = $this->build_loop_arranges($input);
        $error = [];
        foreach ($loop_arranges as $k => $per_arrange) {
            $error[$k] = $this->check_arrange_item($per_arrange);
            if($error[$k]['is_error'] === true){
                unset($loop_arranges[$k]);
            }
        }
        array_values($loop_arranges);
        // print_r($loop_arranges);exit;

        $course = new CourseArrangeModel;
        $res = $course->addLawCourseArrange($loop_arranges);

        $data = [
            'success' => $res['success'],
            'fail'    => $res['fail'],
        ];
        return $this->sendSuccess($data);
    }


    protected function build_loop_arranges($input)
    {
        # $end_type  1:按排课次数  2：按结束日期
        # loop_type  1:每天 2：隔天 3：每周 4：单周 5：双周
        $loop_arranges = [];
        $is_skip_holiday = 1;
        $start_loop_time = isset($input['start_date']) ? strtotime($input['start_date']) : time();

        $input['end_type'] = isset($input['end_type']) ? $input['end_type'] : 1;
        $input['loop_type'] = isset($input['loop_type']) ? $input['loop_type'] : 1;
        if($input['end_type'] == 1){
            $arrange_times = isset($input['arrange_times']) ? $input['arrange_times'] : 1;
        }elseif($input['end_type'] == 2){
            $end_loop_time = isset($input['end_date']) ? strtotime($input['end_date']) : $start_loop_time;
        }
        $holidays = [];
        if($is_skip_holiday){
            $holidays = getBranchHoliday();
        }
        $arranged_times = 0;
        
        # 1:星期一 2：星期二 3：星期三 4：星期四 5：星期五 6：星期六 0：星期天
        $arrange_weeks = isset($input['arrange_weeks']) ? $input['arrange_weeks'] : [1];
        $arrange_int_days = [];

        if($input['end_type'] == 1){  // 排课次数排课
            switch ($input['loop_type']) {
                case '1':  // 每天循环
                    $loop_time = $start_loop_time;
                    $time_step = 24*60*60;
                    while($arranged_times < $arrange_times){
                        $int_day = date('Ymd',$loop_time);
                        if($is_skip_holiday && in_array($int_day,$holidays)){
                            $loop_time += $time_step;
                            continue;
                        }
                        $arrange_int_days[] = date('Y-m-d',$loop_time);
                        $arranged_times++;
                        $loop_time += $time_step;
                    }
                    break;
                case '2':  // 隔天循环
                    $loop_time = $start_loop_time;
                    $time_step = 24*60*60*2;
                    while($arranged_times < $arrange_times){
                        $int_day = date('Ymd',$loop_time);
                        if($is_skip_holiday && in_array($int_day,$holidays)){
                            $loop_time += $time_step;
                            continue;
                        }
                        $arrange_int_days[] = date('Y-m-d',$loop_time);
                        $arranged_times++;
                        $loop_time += $time_step;
                    }
                    break;
                case '3':  // 每周循环
                    $loop_time = $start_loop_time;
                    $time_step = 24*60*60;
                    while($arranged_times < $arrange_times){
                        $int_day = date('Ymd',$loop_time);

                        if($is_skip_holiday && in_array($int_day,$holidays)){
                            $loop_time += $time_step;
                        }
                        if(in_array(date('w',$loop_time),$arrange_weeks)){
                            $arrange_int_days[] = date('Y-m-d',$loop_time);
                            $arranged_times++;
                        }
                        $loop_time += $time_step;
                    }
                    break;
                case '4': // 单周循环
                    $loop_time = $start_loop_time;
                    $time_step = 24*60*60;
                    while($arranged_times < $arrange_times){
                        $int_day = date('Ymd',$loop_time);

                        if($is_skip_holiday && in_array($int_day,$holidays)){
                            $loop_time += $time_step;
                        }
                        if(in_array(date('w',$loop_time),$arrange_weeks) && date('W',$loop_time)%2 == 1){
                            $arrange_int_days[] = date('Y-m-d',$loop_time);
                            $arranged_times++;
                        }
                        $loop_time += $time_step;
                    }
                    break;
                case '5': // 双周周循环
                    $loop_time = $start_loop_time;
                    $time_step = 24*60*60;
                    while($arranged_times < $arrange_times){
                        $int_day = date('Ymd',$loop_time);

                        if($is_skip_holiday && in_array($int_day,$holidays)){
                            $loop_time += $time_step;
                        }
                        if(in_array(date('w',$loop_time),$arrange_weeks) && date('W',$loop_time)%2 == 0){
                            $arrange_int_days[] = date('Y-m-d',$loop_time);
                            $arranged_times++;
                        }
                        $loop_time += $time_step;
                    }
                    break;
                default:
                    break;
            }
            
        }elseif($input['end_type'] == 2){ // 安结束日期排课
            switch ($input['loop_type']) {
                case '3':  // 每周循环
                    $loop_time = $start_loop_time;
                    $time_step = 24*60*60;
                    while($loop_time < $end_loop_time){
                        $int_day = date('Ymd',$loop_time);
                        if($is_skip_holiday && in_array($int_day,$holidays)){
                            $loop_time += $time_step;
                        }
                        if(in_array(date('w',$loop_time),$arrange_weeks)){
                            $arrange_int_days[] = date('Y-m-d',$loop_time);
                        }
                        $loop_time += $time_step;
                    }
                    break;
                case '4': // 单周循环
                    $loop_time = $start_loop_time;
                    $time_step = 24*60*60;
                    while($loop_time < $end_loop_time){
                        $int_day = date('Ymd',$loop_time);
                        if($is_skip_holiday && in_array($int_day,$holidays)){
                            $loop_time += $time_step;
                        }
                        if(in_array(date('w',$loop_time),$arrange_weeks) && date('W',$loop_time)%2 == 1){
                            $arrange_int_days[] = date('Y-m-d',$loop_time);
                        }
                        $loop_time += $time_step;
                    }
                    break;
                case '5': // 双周循环
                    // echo 'success';exit;
                    $loop_time = $start_loop_time;
                    $time_step = 24*60*60;
                    while($loop_time < $end_loop_time){
                        $int_day = date('Ymd',$loop_time);
                        if($is_skip_holiday && in_array($int_day,$holidays)){
                            $loop_time += $time_step;
                        }
                        if(in_array(date('w',$loop_time),$arrange_weeks) && date('W',$loop_time)%2 == 0){
                            $arrange_int_days[] = date('Y-m-d',$loop_time);
                        }
                        $loop_time += $time_step;
                    }
                    break;
                default:
                    break;
            }
            
        }

        foreach ($arrange_int_days as $int_day) {
            $loop_arrange_item = $this->build_loop_arrange_item($input,$int_day);
            array_push($loop_arranges,$loop_arrange_item);
        }

        return $loop_arranges;
    }


    protected function build_loop_arrange_item($input,$int_day){
        $item = [
            'lid'                 => isset($input['lid']) ? $input['lid'] : 0,
            'cid'                 => isset($input['cid']) ? $input['cid'] : 0,
            'sid'                 => isset($input['sid']) ? $input['sid'] : 0,
            'sids'                => isset($input['sids']) ? $input['sids'] : [],
            'grade'               => isset($input['grade']) ? intval($input['grade']) : 0,
            'cr_id'               => intval($input['cr_id']),
            'sj_id'               => isset($input['sj_id']) ? $input['sj_id'] : 0,
            'int_day'             => $int_day,
            'teach_eid'           => intval($input['teach_eid']),
            'second_eids'         => intval($input['second_eids']),
            'lesson_type'         => isset($input['lesson_type']) ? $input['lesson_type'] : 0,
            'int_end_hour'        => $input['int_end_hour'],
            'int_start_hour'      => $input['int_start_hour'],
            'ignore_class_ec'     => isset($input['ignore_class_ec']) ? $input['ignore_class_ec'] : 0,
            'ignore_class_cc'     => isset($input['ignore_class_cc']) ? $input['ignore_class_cc'] : 0,
            'consume_lesson_hour' => isset($input['consume_lesson_hour']) ? $input['consume_lesson_hour'] : 0,
        ];
        if(isset($input['consume_source_type'])){
            $item['consume_source_type'] = intval($input['consume_source_type']);
        }
        if(isset($input['consume_lesson_amount'])){
            $item['consume_lesson_amount'] = floatval($input['consume_lesson_amount']);
        }
        return $item;
    }

    protected function check_arrange_item($input)
    {
        $m_ca = new CourseArrangeModel;
        $data           = [];
        $error_fields   = [];
        $error_students = [];
        $error_message  = [];
        $int_day = format_int_day($input['int_day']);
        $int_start_hour = format_int_day($input['int_start_hour']);
        $int_end_hour = format_int_day($input['int_end_hour']);
        $w['int_day']        = $int_day;
        $w['int_start_hour'] = ['elt',$int_start_hour];
        $w['int_end_hour']   = ['egt',$int_end_hour];

        //是否忽略老师 教室冲突
        $input['ignore_class_ec'] = isset($input['ignore_class_ec']) ? $input['ignore_class_ec'] : 0;
        $input['ignore_class_cc'] = isset($input['ignore_class_cc']) ? $input['ignore_class_cc'] : 0;

        // 检查老师是否冲突
        if($input['ignore_class_ec'] == 0){
            $w['teach_eid'] = $input['teach_eid'];
            $exist_data = $m_ca->where($w)->find();
            if(!empty($exist_data)){
                $error_fields = array_merge($error_fields,["teach_eid"]);
            }
            unset($w['teach_eid']);
        }

        if(isset($input['second_eids']) && !empty($input['second_eids'])){
            $input['second_eid'] = $input['second_eids'][0];
        }

        // 检测教室是否冲突
        if($input['ignore_class_cc'] == 0){ 
            $w['cr_id'] = isset($input['cr_id'])?intval($input['cr_id']):0;
            $w['bid'] = request()->header('x-bid');
            $exist_data = $m_ca->where($w)->find();
            if(!empty($exist_data)){
                $error_fields = array_merge($error_fields,["cr_id"]);
            }
            unset($w['cr_id']);
            unset($w['bid']);
        }
        
        // 检测排课日期是否为节假日
        $holiday = getBranchHoliday();
        if(in_array($w['int_day'],$holiday)){
            $error_message[0] = sprintf('【%s】是节假日',$input['int_day']);
        }

        // 2 班级排课
        if($input['lesson_type'] == 0){
            $class = ClassesModel::get($input['cid']);

            // 检测班级是否冲突
            $w['cid'] = $input['cid'];
            $w['is_cancel'] = 0;
            $exist_data = $m_ca->where($w)->find();
            if(!empty($exist_data)){
                $error_fields = array_merge($error_fields,["cid"]);
            }

            // 检测排课次数是否大于班级设定的课时数
            $w_exceed['cid'] = $class->cid;
            $w_exceed['is_cancel'] = 0;
            $count = $m_ca->where($w_exceed)->count();
            $input['chapter_index'] = intval($count) + 1;
            if (isset($input['chapter_index']) && ($input['chapter_index'] > $class->lesson_times)) {
                $error_message[0] = sprintf('【%s】排课次数已满',get_class_name($class->cid));
                $error_fields = [];
            }

            // 检测班级学员 是否有 一对一 一对多排课
            $w['cid'] = 0;
            $sids = (new ClassStudent)->where('cid',$class->cid)->column('sid');
            foreach ($sids as $sid) {
                $w['sid'] = $sid;
                $exist_data = (new CourseArrangeStudent)->where($w)->find();
                if(!empty($exist_data)){
                    $error_students[] = (new Student)->where('sid',$sid)->find();
                }
            }
        }

        // 3 一对一 一对多排课
        if( isset($input['lesson_type']) && !empty(intval($input['lesson_type'])) ){
            if(isset($w['cr_id'])){
                unset($w['cr_id']);  
            }
            if($input['sid'] > 0){  //一对一排课
                // 检测排课次数是否超出
                $w_cas['sid'] = $input['sid'];
                $w_cas['lid'] = $input['lid'];
                $cas_list = model('course_arrange_student')->where($w_cas)->select();
                $arranged_lesson_hours = 0;
                foreach($cas_list as $cas){
                    $arranged_lesson_hours += $cas->lesson_hour;
                }
                $total_lesson_hours = model('student_lesson')->where($w_cas)->sum('lesson_hours');
                $consume_lesson_hour = floatval($input['consume_lesson_hour']);
                if(($arranged_lesson_hours + $consume_lesson_hour) >= $total_lesson_hours){
                    $error_message[0] = sprintf('【%s】排课次数已满',get_student_name($input['sid']));
                    $error_fields = [];
                }
                // 检测 学员是否在同时段存在排课 
                $w['sid'] = $input['sid'];
                $exist_data = (new CourseArrangeStudent)->where($w)->find();
                if(!empty($exist_data)){
                    $error_students[0] = (new Student)->where('sid',$input['sid'])->find();
                }
            }
            if(!empty($input['sids'])){   // 一对多排课
                foreach ($input['sids'] as $sid) {
                    // 检测学员排课课时是否超出
                    $w_cas['sid'] = $sid;
                    $w_cas['lid'] = $input['lid'];
                    $cas_list = model('course_arrange_student')->where($w_cas)->select();
                    $arranged_lesson_hours = 0;
                    foreach($cas_list as $cas){
                        $arranged_lesson_hours += $cas->lesson_hour;
                    }
                    $total_lesson_hours = model('student_lesson')->where($w_cas)->sum('lesson_hours');
                    $consume_lesson_hour = floatval($input['consume_lesson_hour']);
                    if($arranged_lesson_hours >= $total_lesson_hours){
                        return false;
                    }
                    // 检测学员在同时段是否有排课
                    $w['sid'] = $sid;
                    $exist_data = (new CourseArrangeStudent)->where($w)->find();
                    if(!empty($exist_data)){
                        $error_students[] = (new Student)->where('sid',$sid)->find();
                    }
                }
            }
        }

        $is_error = (empty($error_fields) && empty($error_students) && empty($error_message)) ? false : true;

        $data = [
            'int_day'            =>  $input['int_day'],
            'error_fields'       =>  $error_fields,
            'error_message'      =>  $error_message,
            'error_students'     =>  $error_students,
            'lesson_type'        =>  $input['lesson_type'],
            'is_error'           =>  $is_error,
        ];
        if(empty($data['error_students'])){
            unset($data['error_students']);
        }
        if(empty($data['error_message'])){
            unset($data['error_message']);
        }

        return $data;
    }

    
    /**
     * [规律排课 提交前 检查冲突]
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function check_conflict(Request $request)
    {
        $input = $request->post();
        // print_r($input);exit;
        $m_ca = new CourseArrangeModel;
        $data           = [];
        $error_fields   = [];
        $error_students = [];
        $error_message  = [];
        $int_day = format_int_day($input['int_day']);
        $int_start_hour = format_int_day($input['int_start_hour']);
        $int_end_hour = format_int_day($input['int_end_hour']);
        $w['int_day']        = $int_day;
        $w['int_start_hour'] = ['elt',$int_start_hour];
        $w['int_end_hour']   = ['egt',$int_end_hour];

        //是否忽略老师 教室冲突
        $input['ignore_class_ec'] = isset($input['ignore_class_ec']) ? $input['ignore_class_ec'] : 0;
        $input['ignore_class_cc'] = isset($input['ignore_class_cc']) ? $input['ignore_class_cc'] : 0;

        // 检查老师是否冲突
        if($input['ignore_class_ec'] == 0){
            $w['teach_eid'] = $input['teach_eid'];
            $exist_data = $m_ca->where($w)->find();
            if(!empty($exist_data)){
                $error_fields = array_merge($error_fields,["teach_eid"]);
            }
            unset($w['teach_eid']);
        }

        if(isset($input['second_eids']) && !empty($input['second_eids'])){
            $input['second_eid'] = $input['second_eids'][0];
        }

        // 检测教室是否冲突
        if($input['ignore_class_cc'] == 0){ 
            $w['cr_id'] = isset($input['cr_id'])?intval($input['cr_id']):0;
            $w['bid'] = $request->header('x-bid');
            $exist_data = $m_ca->where($w)->find();
            if(!empty($exist_data)){
                $error_fields = array_merge($error_fields,["cr_id"]);
            }
            unset($w['cr_id']);
            unset($w['bid']);
        }
        
        // 检测排课日期是否为节假日
        $holiday = getBranchHoliday();
        if(in_array($w['int_day'],$holiday)){
            $error_message[0] = sprintf('【%s】是节假日',$input['int_day']);
        }

        // 2 班级排课
        if($input['lesson_type'] == 0){
            $class = ClassesModel::get($input['cid']);

            // 检测班级是否冲突
            $w['cid'] = $input['cid'];
            $w['is_cancel'] = 0;
            $exist_data = $m_ca->where($w)->find();
            if(!empty($exist_data)){
                $error_fields = array_merge($error_fields,["cid"]);
            }
            
            // 不检测排课次数大于班级规定的排课次数，如果排课次数大于班级规定的次数则更新班级的排课次数
            // 检测排课次数是否大于班级设定的课时数
            /*$w_exceed['cid'] = $class->cid;
            $w_exceed['is_cancel'] = 0;
            $count = $m_ca->where($w_exceed)->count();
            $input['chapter_index'] = intval($count) + 1;
            if (isset($input['chapter_index']) && ($input['chapter_index'] > $class->lesson_times)) {
                $error_message[0] = sprintf('【%s】排课次数已满',get_class_name($class->cid));
                $error_fields = [];
            }*/

            // 检测班级学员 在同一时间是否有其他排课
            // $w['cid'] = 0;
            unset($w['cid']);
            $sids = (new ClassStudent)->where(['cid'=>$class->cid,'status'=>1])->column('sid');
            $sids = array_unique($sids);
            foreach ($sids as $sid) {
                $w['sid'] = $sid;
                $exist_data = (new CourseArrangeStudent)->where($w)->find();
                if(!empty($exist_data)){
                    $sinfo = get_student_info($sid);
                    $error_students[] = sprintf('【%s】在【%s %s~%s】时段已有排课！',$sinfo['student_name'],int_day_to_date_str($int_day),int_hour_to_hour_str($int_start_hour),int_hour_to_hour_str($int_end_hour));
                    // $error_students[] = (new Student)->where('sid',$sid)->field('sid,student_name')->find();
                }
            }
        }

        // 3 一对一 一对多排课
        if( isset($input['lesson_type']) && !empty(intval($input['lesson_type'])) ){
            if(isset($w['cr_id'])){
    	    	unset($w['cr_id']);  
    	    }
            if($input['sid'] > 0){  //一对一排课
                // 检测排课次数是否超出
                $w_cas['sid'] = $input['sid'];
                $w_cas['lid'] = $input['lid'];
                $cas_list = model('course_arrange_student')->where($w_cas)->select();
                $arranged_lesson_hours = 0;
                foreach($cas_list as $cas){
                    $arranged_lesson_hours += $cas->lesson_hour;
                }
                $total_lesson_hours = model('student_lesson')->where($w_cas)->sum('lesson_hours');
                $consume_lesson_hour = floatval($input['consume_lesson_hour']);
                if(($arranged_lesson_hours + $consume_lesson_hour) > $total_lesson_hours){
                    $error_message[0] = sprintf('【%s】剩余课时不足',get_student_name($input['sid']));
                    $error_fields = [];
                }
                // 检测 学员是否在同时段存在排课 
                $w['sid'] = $input['sid'];
                $exist_data = (new CourseArrangeStudent)->where($w)->find();
                if(!empty($exist_data)){
                    $sinfo = get_student_info($input['sid']);
                    $error_students[] = sprintf('【%s】在【%s %s~%s】时段已有排课！',$sinfo['student_name'],int_day_to_date_str($int_day),int_hour_to_hour_str($int_start_hour),int_hour_to_hour_str($int_end_hour));
                    // $error_students[0] = (new Student)->where('sid',$input['sid'])->find();
                }
            }
            if(!empty($input['sids'])){   // 一对多排课
                foreach ($input['sids'] as $sid) {
                    // 检测学员排课课时是否超出
                    $w_cas['sid'] = $sid;
                    $w_cas['lid'] = $input['lid'];
                    $cas_list = model('course_arrange_student')->where($w_cas)->select();
                    $arranged_lesson_hours = 0;
                    foreach($cas_list as $cas){
                        $arranged_lesson_hours += $cas->lesson_hour;
                    }
                    $total_lesson_hours = model('student_lesson')->where($w_cas)->sum('lesson_hours');
                    $consume_lesson_hour = floatval($input['consume_lesson_hour']);
                    if($arranged_lesson_hours >= $total_lesson_hours){
                        return false;
                    }
                    // 检测学员在同时段是否有排课
                    $w['sid'] = $sid;
                    $exist_data = (new CourseArrangeStudent)->where($w)->find();
                    if(!empty($exist_data)){
                        $sinfo = get_student_info($sid);
                        $error_students[] = sprintf('【%s】在【%s %s~%s】时段已有排课！',$sinfo['student_name'],int_day_to_date_str($int_day),int_hour_to_hour_str($int_start_hour),int_hour_to_hour_str($int_end_hour));
                        //$error_students[] = (new Student)->where('sid',$sid)->find();
                    }
                }
            }
        }

        // 检测多助教冲突
	/*
        if($input['ignore_class_ec'] == 0 && isset($input['second_eids']) && !empty($input['second_eids'])){
            foreach ($input['second_eids'] as $per_second_eid) {
                $w[] = ['exp',"find_in_set({per_second_eid},$second_eids)"];
                $exist_data = $m_ca->where($w)->find();
                $second = get_teacher_name($per_second_eid);
                if(!empty($exist_data)){    
                    $error_message[] = sprintf('助教【%s】出现排课冲突',$second);
                }
            }
        }*/

        $is_error = (empty($error_fields) && empty($error_students) && empty($error_message)) ? false : true;

        $data = [
            'error_fields'       =>  $error_fields,
            'error_message'      =>  $error_message,
            'error_students'     =>  $error_students,
            'lesson_type'        =>  $input['lesson_type'],
            'is_error'           =>  $is_error,
        ];
        if(empty($data['error_students'])){
            unset($data['error_students']);
        }
        if(empty($data['error_message'])){
            unset($data['error_message']);
        }
        return $this->sendSuccess($data);
    }



    /**
     * @desc  判断课程时间是否存在
     * @author luo
     * @method POST
     */
    public function query_course(Request $request)
    {
        $rule = [
            'teach_eid|上课老师id' => 'require|number',
            'cr_id|教室id' => 'require|number',
            'int_day|具体日期' => 'require|number',
            'int_start_hour|开始时间' => 'require|number',
            'int_end_hour|结束时间' => 'require|number',
        ];
        $input = $request->post();
        if(empty($input)) return $this->sendError(400, 'param error');
        $ajust_ca_ids = array_column($input,'ca_id');
        $mCourseArrange = new CourseArrange();
        foreach($input as $row) {
            $result = $this->validate($row, $rule);
            if($result !== true) return $this->sendError(400, $result);
            $ca_id = isset($row['ca_id']) ? $row['ca_id'] : 0;
            $rs = $mCourseArrange->canArrangeCourse($row, $ca_id,$ajust_ca_ids);
            if($rs !== true) return $this->sendError(400, $mCourseArrange->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  编辑排课
     * @author luo
     * @method PUT
     */
    public function put(Request $request)
    {
        $id = input('id/d');
        /** @var CourseArrange $course */
        $course = CourseArrange::get(['ca_id' => $id]);
        if($course->is_attendance !== CourseArrange::IS_ATTENDANCE_NO) {
            return $this->sendError(400, '此排课已经考勤，不能编辑');
        }elseif($course->is_cancel === CourseArrange::IS_CANCEL){
            return $this->sendError(400, '此排课已取消，不能编辑');
        }
        
        $put = $request->put();
        
        $put['second_eid'] = 0;
        if(isset($put['second_eids']) && !empty($put['second_eids'])){
            $put['second_eid'] = $put['second_eids'][0];
        }

        $put['ca_id'] = $course['ca_id'];
        $rs = $course->updateCourse([$put]);
        if($rs === false) return $this->sendError(400, $course->getError());

        return $this->sendSuccess();
    }

    /**
     * @desc  取消排课
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function do_cancel_course(Request $request)
    {
        $post = $request->post();
        $m_ca = new CourseArrange();
        $rs = $m_ca->cancelCourse($post);
        if($rs === false) return $this->sendError(400, $m_ca->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * @desc  一对一、一对多课程添加,删除学生
     * @author luo
     * @url   /api/lessons/:id/
     * @method GET
     */
    public function update_students(Request $request)
    {
        $post = $request->post();
        $rule = [
            'add_sids' => 'array',
            'del_sids' => 'array',
            'ca_id'    => 'number',
        ];
        $rs = $this->validate($post, $rule);
        if($rs !== true) return $this->sendError(400, $rs);

        $add_sids = isset($post['add_sids']) ? $post['add_sids'] : [];
        $del_sids = isset($post['del_sids']) ? $post['del_sids'] : [];

        /** @var CourseArrange $course */
        $course = CourseArrange::get($post['ca_id']);
        if(empty($course)) {
            return $this->sendError(400, '排课不存在');
        }

        if($course['is_attendance'] != CourseArrange::IS_ATTENDANCE_NO || $course['lesson_type'] != CourseArrange::LESSON_TYPE_ONE2MANY) {
            return $this->sendError(400, '排课已经考勤或者不是一对多排课，无法编辑学员');
        }

        $rs = $course->updateStudents($add_sids, $del_sids);
        if($rs === false) return $this->sendError(400, $course->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  修改课程
     * @author luo
     * @method POST
     */
    public function update_course(Request $request)
    {
        $input = $request->post();
        $m_ca = new CourseArrange();
        $rs = $m_ca->updateCourse($input);
        if($rs === false) return $this->sendError(400, $m_ca->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  删除排课
     * @author luo
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $ca_id = input('id/d');
        $mCourseArrange = new CourseArrange();
        /** @var CourseArrange $course */
        $course = $mCourseArrange->where('ca_id',$ca_id)->find();
        if(!$course){
            return $this->sendSuccess();
        }
        if($course->is_attendance !== CourseArrange::IS_ATTENDANCE_NO) {
            return $this->sendError(400, '此排课已经考勤，不能删除');
        }

        $result = $mCourseArrange->deleteOneCourse($course);
        if(false === $result){
            return $this->sendError(400, $mCourseArrange->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  删除多个课程
     * @author luo
     * @method POST
     */
    public function delete_batch(Request $request)
    {
        $post = $request->post();
        $ca_ids = $post['ca_ids'];
        if(empty($ca_ids) || !is_array($ca_ids)) return $this->sendError('参数错误');
        
        $m_ca = new CourseArrange();
        $rs = $m_ca->deleteBatch($ca_ids);
        if($rs === false) return $this->sendError(400, $m_ca->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc 一对多课程删除学员
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete_students(Request $request)
    {

        $post = $request->post();
        if(empty($post['ca_id']) || empty($post['sids']) || !is_array($post['sids'])) {
            return $this->sendError(400, '参数错误');
        }

        $m_cas = new CourseArrangeStudent();
        $rs = $m_cas->deleteStudentByCaId($post['sids'], $post['ca_id']);
        if($rs === false) return $this->sendError(400, $m_cas->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function get_list_students(Request $request)
    {
        $ca_id = $request->param('id');
        $ca_model = CourseArrange::get($ca_id);
        if (empty($ca_model)) {
            return $this->sendError(400, 'resource not found');
        }
        $ret = [];
        $ret['arrange_students'] = $ca_model->getArrangeStudents();
        $ret['trial_students']   = $ca_model->getTrialStudents();
        $ret['makeup_students']  = $ca_model->getMakeupStudents();
        return $this->sendSuccess($ret);
    }

    /**
     * @desc 获取排课的考勤对象
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     * @throws \think\exception\DbException
     */
    public function get_list_attobjects(Request $request)
    {
        $ca_id = $request->param('id');

        $m_ca  = CourseArrange::get($ca_id);

        if(!$m_ca){
            return $this->sendError(400,'排课ID不存在!');
        }

        $att_objects = $m_ca->getAttObjects(0,true);

        return $this->sendSuccess($att_objects);
    }

    /**
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     * @throws \think\exception\DbException
     */
    public function get_list_services(Request $request)
    {
        $ca_id = $request->param('id');
        $m_ca  = CourseArrange::get($ca_id);
        if(!$m_ca){
            return $this->sendError(400,'排课ID不存在!');
        }
        $services = $m_ca->getServices();
        return $this->sendSuccess($services);
    }

    /**
     * @desc  根据条件计算排课课时数
     * @author luo
     * @param Request $request
     */
    public function consume_lesson_hour(Request $request)
    {
        $cid = input('cid', 0);
        $lid = input('lid', 0);
        $int_start_hour = input('int_start_hour');
        $int_end_hour = input('int_end_hour');

        if(empty($cid) && empty($lid)) {
            return $this->sendError(400, '班级或者课程错误');
        }

        if(empty($int_start_hour) || empty($int_end_hour)) {
            return $this->sendError(400, '参数错误');
        }

        $info = [];

        if($cid > 0) {
            $class = \app\api\model\Classes::get($cid);
            if(empty($class)) return $this->sendError(400, '班级不存在');
            $info = array_merge($info, $class->toArray());
        } elseif($lid > 0) {
            $lesson = get_lesson_info($lid);
            if(empty($lesson)) return $this->sendError(400, '课程不存在');
            $info = array_merge($info, $lesson);
        }

        $m_ca = new CourseArrange();
        $info['int_start_hour'] = $int_start_hour;
        $info['int_end_hour'] = $int_end_hour;
        $consume_lesson_hour = $m_ca->getConsumeLessonHour($info);

        return $this->sendSuccess(['consume_lesson_hour' => $consume_lesson_hour]);
    }

    /**
     * @desc  方法描述
     * @author luo
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method GET
     */
    public function today_course(Request $request)
    {
        $int_day = input('int_day');
        $get = $request->get();
        $m_ca = new CourseArrange();
        if(is_int($int_day)) {
            $courses = $m_ca->with('oneClass')->autoWhere($get)->where('int_day', $int_day)
                ->order('int_start_hour', 'asc')->select();
        } else {
            $courses = $courses = $m_ca->with('oneClass')->order('int_day desc,int_start_hour asc')
                ->autoWhere($get)->select();
        }

        if($request->isMobile())  return $this->sendSuccess($courses);
        foreach($courses as &$per_course) {

            if($per_course['lesson_type'] == CourseArrange::LESSON_TYPE_CLASS) {
                $tmp_sids = (new ClassStudent())->where('cid', $per_course['cid'])
                    ->where('status', ClassStudent::STATUS_NORMAL)->column('sid');
            } else {
                $tmp_sids = (new CourseArrangeStudent())->where('ca_id', $per_course['ca_id'])->column('sid');
            }

            $students = (new Student())->where('sid', 'in', $tmp_sids)
                ->field('sid,student_name,photo_url')->select();
            foreach($students as &$per_student) {
                $attend_log = (new StudentAttendSchoolLog())->where('sid', $per_student['sid'])
                    ->where('int_day', $per_course['int_day'])->find();
                $per_student['student_attend_school_log'] = $attend_log;
            }
            $per_course['students'] = $students;

        }

        return $this->sendSuccess($courses);
    }

    /**
     * @desc  手机端老师的课程日期
     * @author luo
     * @method GET
     */
    public function get_course_day(Request $request) {
        $input = $request->get();
        $get['order_field'] = 'int_day';
        $get['roder_sort'] = 'desc';
        $mCourseArrange = new CourseArrange();
        $where_str = '';
        if(isset($input['teach_eid'])){
            $eid = $input['teach_eid'];
            $where_str = "find_in_set({$eid}, second_eids) or teach_eid = {$eid}";
            unset($input['teach_eid']);
        }
        $ret = $mCourseArrange
            ->field('int_day')
            ->where($where_str)
            ->getSearchResult($input);
        $ret['list'] = array_column($ret['list'], 'int_day');
        $ret['list'] = array_values(array_unique($ret['list']));
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  课前提醒
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function students(Request $request)
    {
        $input = $request->get();
        $m_ca = new CourseArrange();
        if(isset($input['teach_eid']) ){
            $login_user = gvar('user');
            $login_employee = $login_user['employee'];
            $login_eid = $login_employee['eid'];
            $rids = $login_employee['rids'];

            if($login_eid == $input['teach_eid'] && !in_array(1,$rids)){

                $m_classes = new ClassesModel();

                $my_cls_list = $m_classes->where('edu_eid',$login_eid)->whereOr('second_eid',$login_eid)->where('status','LT',2)->select();

                if($my_cls_list){
                    $cids = [];
                    foreach($my_cls_list as $c){
                        array_push($cids,$c['cid']);
                    }
                    $input['cid'] = '[IN,'.implode(',',$cids).']';
                    unset($input['teach_eid']);
                }

            }
        }
        $courses = $m_ca->with('oneClass')->autoWhere($input)->select();

        foreach($courses as &$per_course) {

            $cas_list = $m_ca->getAttObjects($per_course['ca_id'],true,false);
            //todo:后期优化
            /*
            if(empty($cas_list)){
                $cas_list = $m_ca->getAttObjects($per_course['ca_id'],true,false);
            }*/

            $students = [];
            if(!empty($cas_list)){
                foreach($cas_list as $cas){
                    if(!empty($cas['student'])){
                        $w_crl = [];
                        $w_crl['sid'] = $cas['sid'];
                        $w_crl['ca_id'] = $per_course['ca_id'];
                        $cas['student']['course_remind_log'] = model('course_remind_log')->where($w_crl)->select();

                        $students[] = $cas['student'];
                    }
                }
            }

            $per_course['students'] = $students;
        }

        return $this->sendSuccess($courses);
    }

    /**
     * @desc  课前提醒
     * @author luo
     * @url   /api/lessons/:id/
     * @method POST
     */
    public function remind_course()
    {
        $data = input('data/a');
        if(empty($data)) return $this->sendError(400, 'param error');

        $m_cag = new CourseArrange();
        $result = $m_cag->remind_course($data);

        return $this->sendSuccess($result);
    }

    /**
     * @desc  自动课前提醒
     * @url   /api/lessons/:id/
     * @method POST
     */
    public function auto_remind_course(Request $request){
        $input = input();
        $mCourseArrange = new CourseArrange();
        $user = gvar('user');
        $rs = $mCourseArrange->setAutoPushRemindCourseTask($user['og_id'], $user['bid'],$input);
        if ($rs === false) $this->sendError('自动推送失败');

        return $this->sendSuccess();
    }

    public function test(Request $request){
        $input = input();

        $AutoPushCourseRemind = new \app\common\job\AutoPushCourseRemind();
        $rs = $AutoPushCourseRemind->doAutoPushCourseRemindJob($input['data']);
        if(!$rs) return $this->sendError(400);

        return $this->sendSuccess($rs);
    }


    /**
     * 登记考勤 新
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     * @throws \think\exception\DbException
     */
    public function post_regatts(Request $request)
    {
        $ca_id    = $request->param('id');
        $m_ca     = CourseArrange::get($ca_id);
        if (empty($m_ca)) {
            return $this->sendError(404, 'resource not found');
        }
        $bid = $m_ca->bid;
        $request->bind('bid', $bid);
        $request->header(['x-bid',$bid]);

        $input = $request->post();

        $rule = [
            'teach_eid|老师'        => 'require|number',
            'students|考勤学生'      => 'require|array',
            'lesson_remark|考勤备注' => 'max:255',
            'is_push|是否推送给家长'  => 'boolean',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $result = $m_ca->regAttendance($input);
        if (!$result) {
            return $this->sendError(400, $m_ca->getError());
        }
        return $this->sendSuccess('ok');
    }

    /**
     * 批量登记考勤
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function post_dayatts(Request $request)
    {
        $input = input();

        $result  = $this->m_course_arrange->reversalRegAtt($input);

        if(!$result){
            return $this->sendError(400,$this->m_course_arrange->getError());
        }

        return $this->sendSuccess($result);

    }

    /**
     * 登记自定义服务
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function post_services(Request $request)
    {
        $input = $request->post();
        $ca_id = input('id/d');

        $mca = CourseArrange::get($ca_id);
        if(!$mca){
            return $this->sendError(400,'排课不存在!');
        }
        $result = $mca->regService($input);
        if(!$result){
            return $this->sendError(400,$mca->getError());
        }
        return $this->sendSuccess($result);
    }

    /**
     * @desc  老师周课表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function employee_course_by_week(Request $request)
    {
        $int_day = input('int_day', date('Ymd', time()));
        $int_day_time = strtotime($int_day);
        $year = date('Y', $int_day_time);
        $week = date('W', $int_day_time);
        $week_time_arr = weekday($year, $week, 2);

        $week_time_arr = array_map('format_int_day', $week_time_arr);
        $start_day_obj = new DateTime($week_time_arr['start']);
        $end_day_obj = new DateTime(($week_time_arr['end'] + 1));
        $interval = new DateInterval('P1D');
        $range = new DatePeriod($start_day_obj, $interval, $end_day_obj);
        $get = $request->get();
        $with_count = [];
        if(isset($get['with_count']) && !empty($get['with_count'])) {
            $with_count = explode(',', $get['with_count']);
            //if(($key = array_search('student_leave', $with_count_arr)) !== false) {
            //    $with_count[] = 'student_leave';
            //}
        }
        $input = $get;
        $m_employee = new Employee();
        $w_employee = [];


        if(isset($input['teach_eid']) && !empty($input['teach_eid'])){
            $w_employee['eid'] = $input['teach_eid'];
        }

        $x_bid = !empty($input['bid']) ? $input['bid'] : $request->header('x-bid');
        $x_bid = explode(',', $x_bid)[0];

        if ($x_bid) {
            $w_employee[]= ['exp', "find_in_set({$x_bid},bids)"];
            $input['bid'] = -1;
        }
        $ret = $m_employee->where($w_employee)->field('eid,ename,photo_url')
            ->where('find_in_set(1,rids)')->getSearchResult($input);

        $m_ca = new CourseArrange();
        $w_ca['int_day'] = ['between', array_values($week_time_arr)];

        foreach($ret['list'] as &$employee) {
            $course_arranges = $m_ca->where('teach_eid', $employee['eid'])->where($w_ca)
                ->order('int_day asc, int_start_hour asc')
                ->with(['one_class','students','review','textbook','textbook_section'])->withCount($with_count)->select();
            //按照7天排列好
            foreach($range as $dt) {
                $employee['course_arrange'][$dt->format('Ymd')] = [];
            }
            foreach($course_arranges as $course) {
                $employee['course_arrange'][format_int_day($course['int_day'])][] = $course;
            }
        }

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  教室周课表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function classroom_course_by_week(Request $request)
    {
        $int_day = input('int_day', date('Ymd', time()));
        $year = date('Y', time());
        $week = date('W', strtotime($int_day));
        $week_time_arr = weekday($year, $week, 2);
        $week_time_arr = array_map('format_int_day', $week_time_arr);
        $start_day_obj = new DateTime($week_time_arr['start']);
        $end_day_obj = new DateTime(($week_time_arr['end'] + 1));
        $interval = new DateInterval('P1D');
        $range = new DatePeriod($start_day_obj, $interval, $end_day_obj);
        $get = $request->get();
        $with_count = [];
        if(isset($get['with_count']) && !empty($get['with_count'])) {
            $with_count = explode(',', $get['with_count']);
            //if(($key = array_search('student_leave', $with_count_arr)) !== false) {
            //    $with_count[] = 'student_leave';
            //}
        }

        $m_classroom = new Classroom();
        $w_classroom = [];
        if(isset($get['cr_id']) && !empty($get['cr_id'])){
            $w_classroom['cr_id'] = $get['cr_id'];
        }
        $ret = $m_classroom->where($w_classroom)->getSearchResult($get);

        $m_ca = new CourseArrange();
        $w_ca['int_day'] = ['between', array_values($week_time_arr)];

        if(isset($get['teach_eid']) && !empty($get['teach_eid'])){
            $w_ca['teach_eid'] = $get['teach_eid'];
        }

        foreach($ret['list'] as &$classroom) {
            $course_arranges = $m_ca->where('cr_id', $classroom['cr_id'])->where($w_ca)
                ->order('int_day asc, int_start_hour asc')
                ->with(['one_class','students','review','textbook','textbook_section'])->withCount($with_count)->select();
            $classroom['course_arrange'] = [];
            foreach($range as $dt) {
                $classroom['course_arrange'][$dt->format('Ymd')] = [];
            }
            foreach($course_arranges as $course) {
                $classroom['course_arrange'][format_int_day($course['int_day'])][] = $course;
            }
        }

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  学生周课表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function student_course_by_week(Request $request)
    {
        $get = $request->get();
        $w_student = [];
        if(!empty($get['my'])) {
            $eid = \app\api\model\User::getEidByUid(gvar('uid'));
            $m_es = new EmployeeStudent();
            $w_es = [];
            if(!empty($get['sid'])) $w_es['sid'] = $get['sid'];
            $ret = $m_es->where($w_es)->where('eid', $eid)->order('sid desc')->distinct('sid')->field('sid')->getSearchResult();
            $w_student['sid'] = empty($ret['list']) ? ['in', [-1]] : ['in', array_column($ret['list'], 'sid')];
        }

        $with_count = [];
        if(isset($get['with_count']) && !empty($get['with_count'])) {
            $with_count = explode(',', $get['with_count']);
            //if(($key = array_search('student_leave', $with_count_arr)) !== false) {
            //    $with_count[] = 'student_leave';
            //}
        }

        $m_student = new Student();
        $ret = $m_student->where($w_student)->field('sid,student_name,photo_url')
            ->where('status', Student::STATUS_NORMAL)->getSearchResult($get);

        $int_day = input('int_day', date('Ymd', time()));
        $year = date('Y', time());
        $week = date('W', strtotime($int_day));
        $week_time_arr = weekday($year, $week, 2);
        $week_time_arr = array_map('format_int_day', $week_time_arr);
        $start_day_obj = new DateTime($week_time_arr['start']);
        $end_day_obj = new DateTime(($week_time_arr['end'] + 1));
        $interval = new DateInterval('P1D');
        $range = new DatePeriod($start_day_obj, $interval, $end_day_obj);

        $m_cs = new ClassStudent();
        $m_ca = new CourseArrange();
        $m_sl = new StudentLeave();
        $m_sa = new StudentAbsence();

        $w_ca_teacher = [];
        if(isset($get['teach_eid']) && !empty($get['teach_eid'])){
            $w_ca_teacher['ca.teach_eid'] = $get['teach_eid'];
        }
        foreach($ret['list'] as &$student) {
            //$cids = $m_cs->where('sid', $student['sid'])->where('status', ClassStudent::STATUS_NORMAL)
            //    ->column('cid');
            //$cids = is_array($cids) && !empty($cids) ? implode(',', $cids) : $cids;
            $w_ca = "cas.sid = {$student['sid']}";
            //if(!empty($cids)) $w_ca .= " or ca.cid in ({$cids})";

            $course_arranges = $m_ca->alias('ca')->join('course_arrange_student cas', 'cas.ca_id = ca.ca_id', 'left')
               ->where($w_ca)->where($w_ca_teacher)->distinct('ca.ca_id')
                ->where('ca.int_day', 'between', array_values($week_time_arr))->field('ca.*')->select();

            foreach($range as $dt) {
                $student['course_arrange'][$dt->format('Ymd')] = [];
            }
            foreach($course_arranges as $course) {
                $course['one_class'] = $course->one_class;
                $course['students'] = $course->students;
                $course['review'] = $course->review;
                $course['textbook'] = $course->textbook;
                $course['textbook_section'] = $course->textbook_section;

                if(array_search('student_leave', $with_count) !== false) {
                    $course['student_leave_count'] = $m_sl->where('ca_id', $course->ca_id)->count();
                }
                if(array_search('student_absence', $with_count) !== false) {
                    $course['student_absence_count'] = $m_sa->where('ca_id', $course->ca_id)->count();
                }
                $student['course_arrange'][format_int_day($course['int_day'])][] = $course;
            }
        }

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  班级的周课表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function class_course_by_week(Request $request)
    {
        $get = $request->get();
        $w_class = [];
        $m_class = new \app\api\model\Classes();
        if(!empty($get['my'])) {
            $eid = \app\api\model\User::getEidByUid(gvar('uid'));
            $cids = $m_class->where('teach_eid', $eid)->column('cid');
            $w_class['cid'] = empty($cids) ? ['in', [-1]] : ['in', $cids];
        }

        $with_count = [];
        if(isset($get['with_count']) && !empty($get['with_count'])) {
            $with_count = explode(',', $get['with_count']);
            //if(($key = array_search('student_leave', $with_count_arr)) !== false) {
            //    $with_count[] = 'student_leave';
            //}
        }

        $ret = $m_class->where($w_class)->getSearchResult($get);

        $int_day = input('int_day', date('Ymd', time()));
        $year = date('Y', time());
        $week = date('W', strtotime($int_day));
        $week_time_arr = weekday($year, $week, 2);
        $week_time_arr = array_map('format_int_day', $week_time_arr);
        $start_day_obj = new DateTime($week_time_arr['start']);
        $end_day_obj = new DateTime(($week_time_arr['end'] + 1));
        $interval = new DateInterval('P1D');
        $range = new DatePeriod($start_day_obj, $interval, $end_day_obj);

        $w_ca_teacher = [];

        if(isset($get['teach_eid']) && !empty($get['teach_eid'])){
            $w_ca_teacher['teach_eid'] = $get['teach_eid'];
        }

        $m_ca = new CourseArrange();
        foreach($ret['list'] as &$class) {

            $course_arranges = $m_ca->where('cid', $class['cid'])->where('int_day', 'between', array_values($week_time_arr))
                ->where($w_ca_teacher)->with(['review','textbook','textbook_section'])->withCount($with_count)->select();

            foreach($range as $dt) {
                $class['course_arrange'][$dt->format('Ymd')] = [];
            }
            foreach($course_arranges as $course) {
                $class['course_arrange'][format_int_day($course['int_day'])][] = $course;
            }
        }

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  时间段周课表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function time_course_by_week(Request $request)
    {
        $int_day = input('int_day', date('Ymd', time()));
        $year = date('Y', time());
        $week = date('W', strtotime($int_day));
        $week_time_arr = weekday($year, $week, 2);
        $week_time_arr = array_map('format_int_day', $week_time_arr);

        $m_ca = new CourseArrange();
        $w_ca['int_day'] = ['between', array_values($week_time_arr)];
        $w_ca['bid'] = $request->bid;




        $get = $request->get();

        $with_count = [];
        if(isset($get['with_count']) && !empty($get['with_count'])) {
            $with_count = explode(',', $get['with_count']);
            //if(($key = array_search('student_leave', $with_count_arr)) !== false) {
            //    $with_count[] = 'student_leave';
            //}
        }

        if(isset($get['teach_eid']) && !empty($get['teach_eid'])){
            $w_ca['teach_eid'] = $get['teach_eid'];
        }


        $course_arranges = $m_ca->where($w_ca)
            ->order('int_day asc, int_start_hour asc')
            ->with(['one_class','students','review','textbook','textbook_section'])->withCount($with_count)->select();

        $ret['list'] = $course_arranges;

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  课程周课表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function lesson_course_by_week(Request $request)
    {
        $int_day = input('int_day', date('Ymd', time()));
        $year = date('Y', time());
        $week = date('W', strtotime($int_day));
        $week_time_arr = weekday($year, $week, 2);
        $week_time_arr = array_map('format_int_day', $week_time_arr);
        $start_day_obj = new DateTime($week_time_arr['start']);
        $end_day_obj = new DateTime(($week_time_arr['end'] + 1));
        $interval = new DateInterval('P1D');
        $range = new DatePeriod($start_day_obj, $interval, $end_day_obj);
        $get = $request->get();
        $with_count = [];
        if(isset($get['with_count']) && !empty($get['with_count'])) {
            $with_count = explode(',', $get['with_count']);
        }

        $m_lesson = new Lesson();

        $ret = $m_lesson->field('lid,bids,lesson_name')->getSearchResult($get);

        $m_ca = new CourseArrange();
        $w_ca['int_day'] = ['between', array_values($week_time_arr)];

        foreach($ret['list'] as &$lesson) {
            $course_arranges = $m_ca->where('lid', $lesson['lid'])->where($w_ca)
                ->order('int_day asc, int_start_hour asc')
                ->with(['one_class','students','review'])->withCount($with_count)->select();
            //按照7天排列好
            foreach($range as $dt) {
                $lesson['course_arrange'][$dt->format('Ymd')] = [];
            }
            foreach($course_arranges as $course) {
                $lesson['course_arrange'][format_int_day($course['int_day'])][] = $course;
            }
        }

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  科目周课表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function subject_course_by_week(Request $request)
    {
        $int_day = input('int_day', date('Ymd', time()));
        $year = date('Y', time());
        $week = date('W', strtotime($int_day));
        $week_time_arr = weekday($year, $week, 2);
        $week_time_arr = array_map('format_int_day', $week_time_arr);
        $start_day_obj = new DateTime($week_time_arr['start']);
        $end_day_obj = new DateTime(($week_time_arr['end'] + 1));
        $interval = new DateInterval('P1D');
        $range = new DatePeriod($start_day_obj, $interval, $end_day_obj);
        $get = $request->get();
        $with_count = [];
        if(isset($get['with_count']) && !empty($get['with_count'])) {
            $with_count = explode(',', $get['with_count']);
        }

        $m_subject = new Subject();
        $ret = $m_subject->field('sj_id,subject_name')->getSearchResult($get);

        $m_ca = new CourseArrange();
        $w_ca['int_day'] = ['between', array_values($week_time_arr)];

        foreach($ret['list'] as &$subject) {
            $course_arranges = $m_ca->where('sj_id', $subject['sj_id'])->where($w_ca)
                ->order('int_day asc, int_start_hour asc')
                ->with(['one_class','students','review'])->withCount($with_count)->select();
            //按照7天排列好
            foreach($range as $dt) {
                $subject['course_arrange'][$dt->format('Ymd')] = [];
            }
            foreach($course_arranges as $course) {
                $subject['course_arrange'][format_int_day($course['int_day'])][] = $course;
            }
        }

        return $this->sendSuccess($ret);
    }

}