<?php
/**
 * Author: luo
 * Time: 2017-10-11 11:09
**/

namespace app\api\model;

use app\common\exception\FailResult;
use app\common\Wechat;
use think\Exception;

class CourseArrange extends Base
{
    const ATTENDANCE_NORMAL = 0;
    const ATTENDANCE_STUDENT_TRIAL = 1;
    const ATTENDANCE_CUSTOMER_TRIAL = 2;
    const ATTENDANCE_STUDENT_MAKEUP = 3;

    const IS_ATTENDANCE_NO = 0;   # 未考勤
    const IS_ATTENDANCE_SOME = 1; # 部分考勤
    const IS_ATTENDANCE_YES = 2;  # 全部考勤

    const IS_CANCEL = 1;   #取消de排课

    const LESSON_TYPE_CLASS = 0;    # 班课
    const LESSON_TYPE_ONE2ONE = 1;  # 一对一
    const LESSON_TYPE_ONE2MANY = 2; # 一对多

    protected $attendance_fail_report = [];

    public static $detail_fields = [
        ['type'=>'index','width'=>60,'align'=>'center'],
        ['title'=>'校区','key'=>'bid','align'=>'center'],
        ['title'=>'类型','key'=>'lesson_type','align'=>'center'],
        ['title'=>'老师','key'=>'teach_eid','align'=>'center'],
        ['title'=>'教室','key'=>'cr_id','align'=>'center'],
        ['title'=>'时间段','key'=>'time_section','align'=>'center','width'=>170],
        ['title'=>'是否考勤','key'=>'is_attendance','align'=>'center'],
    ];

    //protected $is_trial_type = ['yes' => 1, 'no' => 0]; //试听课程

    protected $append = ['disable_attendance','course_name'];

    public function setIntDayAttr($value,$data){
        return format_int_day($value);
    }

    protected function setIntStartHourAttr($value, $data)
    {
        return format_int_hour($value);
    }

    protected function setIntEndHourAttr($value, $data)
    {
        return format_int_hour($value);
    }

    public function getCourseNameAttr($value,$data){
        $course_name = get_course_name_by_row($data);

        return $course_name;
    }

    public function getSjIdAttr($value,$data){
        $sj_id = $value;
        if($value == 0){
            if($data['lid'] > 0){
                $lesson = $this->m_lesson->where('lid',$data['lid'])->cache(1)->find();
                if($lesson){
                    if($lesson->sj_id > 0){
                        $sj_id = $lesson->sj_id;

                    }elseif(!empty($lesson->sj_ids)){
                        $sj_id = $lesson->sj_ids[0];
                    }

                    if($sj_id > 0 && $data['ca_id'] > 0){
                        $this->m_course_arrange->isUpdate(true)->save(['sj_id'=>$sj_id],['ca_id'=>$data['ca_id']]);
                    }
                }
            }
        }
        return $sj_id;
    }

    //与班级考勤是一对一关系
    public function classAttendance()
    {
        return $this->hasOne('ClassAttendance', 'ca_id', 'ca_id');
    }

    public function studentAttendance()
    {
        return $this->hasMany('StudentAttendance', 'ca_id', 'ca_id');
    }

    //与课程是一对一关系
    public function lesson()
    {
        return $this->hasOne('Lesson', 'lid', 'lid');
    }

    /**
     * 获取该次排课的请假记录
     */
    public function getStudentLeaveAttr()
    {
        return $this->hasMany('StudentLeave', 'ca_id', 'ca_id');
    }

    public function getConsumeLessonHourAttr($value,$data)
    {
        if($value == 0){
            $value = $this->getConsumeLessonHour();
        }
        return $value;
    }

    public function oneClass()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }

    public function courseArrangeStudent()
    {
        return $this->belongsToMany('Student','course_arrange_student','sid','ca_id');
    }

    public function students()
    {
        return $this->belongsToMany('Student','course_arrange_student','sid','ca_id');
    }

    public function studentLeave()
    {
        return $this->hasMany('StudentLeave', 'ca_id', 'ca_id')->field('sid,reason,satt_id,ma_id');
    }

    public function studentAbsence()
    {
        return $this->hasMany('StudentAbsence','ca_id', 'ca_id');
    }

    //课程点评
    public function review()
    {
        return $this->hasOne('Review', 'ca_id', 'ca_id');
    }

    public function coursePrepare()
    {
        return $this->belongsTo('CoursePrepare', 'ca_id', 'ca_id');
    }

    public function Textbook()
    {
        return $this->hasOne('textbook', 'tb_id', 'tb_id');
    }

    public function TextbookSection()
    {
        return $this->hasOne('textbook_section', 'tbs_id', 'tbs_id');
    }

    protected function init_cainfo($ca_id){
        if($ca_id == 0){
            $ca_info = $this->getData();
            if(!isset($ca_info['ca_id'])){
                return $this->user_error('模型未实例化!');
            }
            $ca_id = $ca_info['ca_id'];
        }else{
            $ca_info = get_ca_info($ca_id);
            if(!$ca_info){
                return $this->user_error('排课ID不存在!');
            }
            $this->data($ca_info);
        }
        return $ca_info;
    }

    /**
     * 获得排课对象
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCourseArrangeObject(){
        $ca_info = $this->getData();
        $ret = '';
        if($ca_info['lesson_type'] == 0){
            if($ca_info['is_trial']){
                $ret = $ca_info['name'];
            }else{
                $ret = '班级:'.get_class_name($ca_info['cid']);
            }

        }else{
            $w_cas['ca_id'] = $ca_info['ca_id'];
            $mCas = new CourseArrangeStudent();
            $cas_list = $mCas->where($w_cas)->select();

            $arr_s = [];
            if($cas_list){
                foreach($cas_list as $cas){
                    array_push($arr_s,get_student_name($cas['sid']));
                }
            }

            $ret = implode(',',$arr_s);
        }
        return $ret;
    }

    /**
     * 获取该次排课的正式学员和补课学员
     * @return mixed
     */
    public function getStudentList()
    {
        $ca_info  = $this->getData();
        if(empty($ca_info)) return $this->user_error('排课数据错误');
        $m_cas = new CourseArrangeStudent();
        $sids = $m_cas->where('ca_id', $this->getData('ca_id'))->column('sid');
        $cu_ids = $m_cas->where('ca_id', $this->getData('ca_id'))->column('cu_id');

        $list = [];
        if(!empty($sids)) {
            $students = (new Student())->where('sid', 'in', array_unique($sids))->select();
            $list = array_merge($list, $students);
        }
        if(!empty($cu_ids)) {
            $customers = (new Customer())->where('cu_id', 'in', array_unique($cu_ids))->select();
            $list = array_merge($list, $customers);
        }

        return $list;
    }

    /**
     * 获取班课的考勤id
     * @return int
     */
    public function getCattIdAttr()
    {
        $ca_id = $this->getData('ca_id');
        $class_atd = ClassAttendance::get(['ca_id' => $ca_id]);
        if (empty($class_atd)) {
            return $class_atd['catt_id'];
        }
        return 0;
    }

    /**
     * 创建循环排课条目
     * @param $input
     * @param $int_day
     * @return array
     */
    private function build_loop_arrange_item($input,$int_day){
        $grade = 0;

        if(isset($input['grade'])){
            $grade = intval($input['grade']);
        }

        $item = [
            'cr_id' => $input['cr_id'],
            'int_day' => $int_day,
            'int_start_hour' => $input['int_start_hour'],
            'int_end_hour' => $input['int_end_hour'],
            'teach_eid' => $input['teach_eid'],
            'second_eid' => $input['second_eid'],
            'lesson_type' => $input['lesson_type'],
            'lid' => $input['lid'],
            'sj_id' => $input['sj_id'],
            'grade' => $grade,
            'consume_lesson_hour' => isset($input['consume_lesson_hour']) ? $input['consume_lesson_hour'] : 0,

            'ignore_class_ec' => isset($input['ignore_class_ec']) ? $input['ignore_class_ec'] : 0,
            'ignore_class_cc' => isset($input['ignore_class_cc']) ? $input['ignore_class_cc'] : 0,
            'tb_id' =>  $input['tb_id'],
            'tbs_id' =>  $input['tbs_id']
        ];
        if(isset($input['consume_source_type'])){
            $item['consume_source_type'] = intval($input['consume_source_type']);
        }
        if(isset($input['consume_lesson_amount'])){
            $item['consume_lesson_amount'] = floatval($input['consume_lesson_amount']);
        }
        return $item;
    }

    //一对一、一对多学员课程排课
    public function addCourseOfStu($input)
    {
        $is_loop = isset($input['isloop']) ? $input['isloop'] : 0;
        // $loop_arranges = isset($input['loop_arranges']) && ! empty($input['loop_arranges']) ? $input['loop_arranges'] : [];E
        $loop_arranges = [];

        $loop_times = isset($input['loop_times']) ? $input['loop_times'] : 1;
        $is_skip_holiday = 1;
        
        // 虚构一个循环排课类型   0：按周排课  1：单周排课   2：双周排课
        $input['loop_type'] = isset($input['loop_type'])?intval($input['loop_type']):0;
        $week = date('W',strtotime($input['int_day']));

        $students = $input['students'];
        if(empty($students)) return $this->user_error('学员不能为空');

        $this->startTrans();
        try {
            //--1-- 如果不是循环排课，则单独添加一个排课
            if (!$is_loop) {
                $loop_arranges = [];
                $loop_arranges[] = $this->build_loop_arrange_item($input,$input['int_day']);

            }else{
                if(empty($loop_arrranges)){
                    $holidays = [];
                    if($is_skip_holiday){
                        $holidays = getBranchHoliday();
                    }
                    $looped_times = 0;
                    $start_loop_time = strtotime($input['int_day']);
                    $week = date('W',$start_loop_time);
                    if($input['loop_type'] == 0){
                        $time_step = 604800;
                    }else{
                        $time_step = 2*604800;
                        if(
                            ($input['loop_type'] == 1 && $week % 2 == 0 ) ||
                            ($input['loop_type'] == 2 && $week % 2 == 1 )
                        ){
                            $start_loop_time += 604800;
                        }
                    }
                    $loop_time = $start_loop_time;
                    while($looped_times < $loop_times){
                        $int_day = date('Ymd',$loop_time);
                        if ($is_skip_holiday && in_array($int_day,$holidays)) {
                            $loop_time += $time_step;
                            continue;
                        }
                        $loop_arrange_item = $this->build_loop_arrange_item($input,$int_day);
                        array_push($loop_arranges,$loop_arrange_item);
                        $looped_times++;
                        $loop_time += $time_step;
                    }

                }
            }
           
            $m_cas = new CourseArrangeStudent();
            foreach ($loop_arranges as $per_arrange) {
                $ca_id = $this->createOneCourse($per_arrange);
                if(!$ca_id) throw new FailResult($this->getErrorMsg());

                $course = $this->find($ca_id);
                foreach($students as $per_stu) {
                    if(!$per_stu['sid']){
                        continue;
                    }
                    $rs = $m_cas->addOneArrangeStudent($course, $per_stu['sid']);
                    if($rs === false) throw new FailResult($m_cas->getErrorMsg());

                }
            }

            // 建立老师与学员之间的关系
            $sids = array_column($students,'sid');
            if(count($sids) == 1){  // 一对一
                $type = EmployeeStudent::TYPE_ONE;
            }else{  // 一对多
                $type = EmployeeStudent::TYPE_MANY;
            }
            $lid = $input['lid'];
            foreach ($sids as $sid) {
                $info = array(
                    'sid' => $sid,
                    'rid' => EmployeeStudent::EMPLOYEE_TEACHER,
                    'eid' => $input['teach_eid']
                );
                EmployeeStudent::addEmployeeStudentRelationship($info,$type,$lid);
            }

            // 建立助教与学员之间的关系
            if(!empty($input['second_eids'])){
                foreach ($input['second_eids'] as $eid) {
                    foreach ($sids as $sid) {
                        $info = array(
                            'sid' => $sid,
                            'rid' => EmployeeStudent::EMPLOYEE_TA,
                            'eid' => $eid
                        );
                        EmployeeStudent::addEmployeeStudentRelationship($info,$type,$lid);
                    }
                }
            }

        } catch (Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return true;

    }


    /**
     * 获得排课冲突错误提示
     * @param $eid
     * @param $ca
     */
    protected function get_conflict_error($id,$ca,$type){
        $format = "%s在%s的时间段%s~%s已经有排课存在,课程/班级为:%s";
        if($type == 'teacher'){
            $einfo = get_employee_info($id);
            $name = $einfo['ename'];
        }else{
            $cinfo = get_classroom_info($id);
            $name = $cinfo['room_name'];
        }

        $int_day = int_day_to_date_str($ca['int_day']);
        $int_start_hour = int_hour_to_hour_str($ca['int_start_hour']);
        $int_end_hour = int_hour_to_hour_str($ca['int_end_hour']);
        $course_name = get_course_name_by_row($ca);

        return sprintf($format,$name,$int_day,$int_start_hour,$int_end_hour,$course_name);
    }

    /**
     * @desc  添加一个排课
     * @author luo
     */
    public function createOneCourse($data)
    {
        if(!isset($data['teach_eid']) || $data['teach_eid'] <= 0) return $this->user_error('老师id不能为0');
        $data['int_day'] = format_int_day($data['int_day']);
        $data['int_start_hour'] = format_int_day($data['int_start_hour']);
        $data['int_end_hour'] = format_int_day($data['int_end_hour']);

        $w_ca = [];
        $w_ca['int_day']   = format_int_day($data['int_day']);
        $w_ca['teach_eid'] = $data['teach_eid'];

        $data['ignore_class_ec'] = isset($data['ignore_class_ec']) ? $data['ignore_class_ec'] : 0;
        $data['ignore_class_cc'] = isset($data['ignore_class_cc']) ? $data['ignore_class_cc'] : 0;


        $ca_list = get_table_list('course_arrange',$w_ca,[],'`int_start_hour` ASC');
        //判断是否有重复

        if($ca_list && $data['ignore_class_ec'] == 0){
            foreach($ca_list as $ca){
                if(is_timesection_in_timesection($data,$ca)){
                    $error = $this->get_conflict_error($data['teach_eid'],$ca,'teacher');
                    return $this->user_error($error);
                }
            }
        }

        $ignore_classroom_conflict = false;
        if(isset($data['lid']) && $data['lid'] > 0) {
            $lesson = get_lesson_info($data['lid']);
            if($data['ignore_class_cc'] == 0) {
                $ignore_classroom_conflict = true;
            }
        }
        //--2-- 判断教室是否有排课
        if(!$ignore_classroom_conflict && $data['cr_id'] > 0){
            $w_ca = [];
            $w_ca['int_day'] = $data['int_day'];
            $w_ca['cr_id']   = $data['cr_id'];

            $ca_list = get_table_list('course_arrange',$w_ca,[],'int_start_hour ASC');

            if($ca_list){
                foreach($ca_list as $ca){
                    if(is_timesection_in_timesection($data,$ca)){
                        $error = $this->get_conflict_error($data['cr_id'],$ca,'classroom');
                        return $this->user_error($error);
                    }
                }
            }

        }

        $result = $this->data([])->validate(true)->allowField(true)->isUpdate(false)->save($data);
        if(false === $result){
            return $this->sql_add_error('course_arrange');
        }

        $ca_id = $this->getAttr('ca_id');

        return $ca_id;
    }

    /**
     * @desc  添加课程
     * @author luo
     * @param array $course_data 课程数据
     * @param array $students_data 课程安排学生数据
     */
    public function createCourseAndTrial($course_data, $students_data) {

        $this->startTrans();
        try {

            //--1-- 添加课程信息
            $save_rs = $this->createOneCourse($course_data);
            if(!$save_rs){
                return $this->user_error($this->getError());
            }

            $course_arrange_id = $this->ca_id;

            //--2-- 添加试听安排记录
            if(isset($course_data['is_trial']) && $course_data['is_trial'] == 1) {
                if(!empty($students_data)) {
                    foreach($students_data as $per_student) {
                        $m_tla = new TrialListenArrange();
                        $result = $m_tla->createOneTrial($course_arrange_id, $per_student);
                        if(false === $result){
                            return $this->user_error($m_tla->getError());
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return $course_arrange_id;
    }

    /**
     * @desc  添加批量课程
     * @author luo
     */
    public function addBatchCourse2($cids, $exclude_holiday = 0) {

        $classes = (new Classes)->where('cid', 'in', $cids)->select();
        if(empty($classes)) {
            $this->user_error('没有相关班级');
            return false;
        }

        $this->startTrans();
        try {
            //循环班级创建排课
            foreach($classes as $class) {

                /***计算时间相差天数*start***/
                $begin_date = new \DateTime($class->start_lesson_time);
                $end_date = clone $begin_date;
                $end_date->add(new \DateInterval('P400D'));
                //$end_date = new \DateTime($class->end_lesson_time);
                $date_diff_obj = date_diff($begin_date, $end_date);
                if($date_diff_obj->days > 400) {
                    $this->user_error($class->class_name.'开始时间与结束时间不能超过400天');
                    return false;
                }
                /***计算时间相差天数*end***/

                $schedules = ClassSchedule::where('cid', $class->cid)->order('week_day asc, int_start_hour asc')
                    ->select();
                if(empty($schedules)) {
                    $this->user_error($class->class_name.'还没有排班记录');
                    return false;
                }

                $attended_course_num = CourseArrange::where(['cid' => $class->cid, 'is_attendance' => 1])->count();
                if($attended_course_num) {
                    $this->user_error($class->class_name.'已经有出勤排课');
                    return false;
                }

                CourseArrange::destroy(['cid' => $class->cid]);
                $class->arrange_times = 0;
                $rs = (new Classes)->where('cid', $class->cid)->update(['arrange_times' => 0]);
                if($rs === false) exception('排课次数清零失败');

                /****处理排班数据，用于后续使用* start****/
                $tmp_key = 0;
                $week_day_arr = [];
                $schdules_lesson_hour_arr = [];
                foreach($schedules as $per_schdule) {
                    //用于循环日期时增加天数
                    $week_day_arr[] = (int)$per_schdule->week_day;

                    //用于插入排课时的小时
                    $schdules_lesson_hour_arr[$tmp_key]['int_start_hour'] = $per_schdule->int_start_hour;
                    $schdules_lesson_hour_arr[$tmp_key]['int_end_hour'] = $per_schdule->int_end_hour;

                    //教室id
                    $schdules_lesson_hour_arr[$tmp_key]['cr_id'] = $per_schdule->cr_id != 0
                        ? $per_schdule->cr_id : $class->cr_id;

                    $tmp_key++;
                }

                $first_week_day = $min_week_day = min($week_day_arr);

                $day_offset_arr = [];
                $schedule_num = count($week_day_arr);
                for($i = 1; $i < $schedule_num; $i++) {
                    $day_offset_arr[] = $week_day_arr[$i] - $week_day_arr[$i-1];
                }
                /****处理排班数据，用于后续使用* end****/


                $interval_spec = 'P1W';
                $week_begin = intval($begin_date->format('w'));

                if ($week_begin == 0) { $week_begin = 7; }

                if ($first_week_day >= $week_begin) {
                    $range = $first_week_day - $week_begin;
                    $week_offset = new \DateInterval('P' . $range . 'D');
                } else {
                    $range = 7 - $week_begin + $first_week_day;
                    $week_offset = new \DateInterval('P' . $range . 'D');
                }
                $begin_date = $begin_date->add($week_offset);

                $interval = new \DateInterval($interval_spec);
                $date_range = new \DatePeriod($begin_date, $interval ,$end_date);

                //插入排课表的数据
                $data = [];
                $data['bid'] = $class->bid;
                $data['cid'] = $class->cid;
                $data['teach_eid'] = $class->teach_eid;
                $data['second_eid'] = $class->second_eid;
                $data['lid'] = $class->lid;
                $data['season'] = $class->season;

                $chapter_index = 0;

                //假期是否排课
                if($exclude_holiday) {
                    $holiday = [];
                } else {
                    $holiday = model('holiday')
                        ->where('year', $begin_date->format('y'))
                        ->column('int_day');
                }

                //循环日期，写入排课
                foreach($date_range as $per_date) {
                    $chapter_index++;
                    if ($class->lesson_times < $chapter_index) {
                        break;
                    }

                    $data['int_day'] = $per_date->format('Ymd');
                    //假期不排课
                    if (in_array($data['int_day'], $holiday)) {
                        continue;
                    }

                    $data['chapter_index'] = $chapter_index;
                    $data['int_start_hour'] = $schdules_lesson_hour_arr[0]['int_start_hour'];
                    $data['int_end_hour'] = $schdules_lesson_hour_arr[0]['int_end_hour'];
                    $data['cr_id'] = $schdules_lesson_hour_arr[0]['cr_id'];
                    unset($this->data['ca_id']);
                    $result = $this->addOneCourseOfClass($class, $data);

                    if (!$result) {
                        $this->user_error($this->getErrorMsg());
                        return false;
                    }

                    //一个星期的后续几天如果有排班，也要相应排课
                    $tmp_hour_key = 1;
                    foreach($day_offset_arr as $offset_day) {
                        $chapter_index++;
                        if ($class->lesson_times < $chapter_index) {
                            break;
                        }
                        $offset = new \DateInterval('P'.$offset_day.'D');
                        $per_date->add($offset);
                        $data['int_day'] = $per_date->format('Ymd');

                        if (in_array($data['int_day'], $holiday)) {
                            continue;
                        }

                        $data['chapter_index'] = $chapter_index;
                        $data['int_start_hour'] = $schdules_lesson_hour_arr[$tmp_hour_key]['int_start_hour'];
                        $data['int_end_hour'] = $schdules_lesson_hour_arr[$tmp_hour_key]['int_end_hour'];
                        $data['cr_id'] = $schdules_lesson_hour_arr[$tmp_hour_key]['cr_id'];
                        $tmp_hour_key++;

                        unset($this->data['ca_id']);
                        $result = $this->addOneCourseOfClass($class, $data);

                        if (!$result) {
                            $this->user_error($this->getErrorMsg());
                            return false;
                        }

                    }

                }

            }

            $this->commit();
        } catch(\ErrorException $e) {
            $this->rollback();
            $this->user_error('操作失败');
            return false;
        }

        return true;
    }

    /**
     * @desc  班级批量添加课程
     * @author luo
     * @param $cids
     * @param int $exclude_holiday
     */
    public function addBatchCourse($cids, $exclude_holiday = 0) {

        $classes = (new Classes)->where('cid', 'in', $cids)->select();
        if(empty($classes)) {
            $this->user_error('没有相关班级');
            return false;
        }

        $this->startTrans();
        try {
            //循环班级创建排课
            foreach($classes as $class) {

                /***计算时间相差天数*start***/
                $begin_date = new \DateTime($class->start_lesson_time);
                $end_date = clone $begin_date;
                $end_date->add(new \DateInterval('P400D'));
                //$end_date = new \DateTime($class->end_lesson_time);
                $date_diff_obj = date_diff($begin_date, $end_date);
                if($date_diff_obj->days > 400) {
                    $this->user_error($class->class_name.'开始时间与结束时间不能超过400天');
                    return false;
                }
                /***计算时间相差天数*end***/

                $schedules = ClassSchedule::where('cid', $class->cid)->order('week_day asc, int_start_hour asc')
                    ->select();
                if(empty($schedules)) {
                    $this->user_error($class->class_name.'还没有排班记录');
                    return false;
                }

                $attended_course_num = CourseArrange::where(['cid' => $class->cid, 'is_attendance' =>['gt', 0]])->count();
                if($attended_course_num) {
                    $this->user_error($class->class_name.'已经有出勤排课');
                    return false;
                }

                $m_cas = new CourseArrangeStudent();
                $course_student = $m_cas->where('cid', $class->cid)->where('is_trial|is_makeup', 1)->count();
                if($course_student > 1) {
                    throw new FailResult('之前的排课有试听或者补课的学员，无法重新自动排课');
                }
                $m_cas->where('cid', $class->cid)->delete();

                CourseArrange::destroy(['cid' => $class->cid]);
                $class->arrange_times = 0;
                $rs = (new Classes)->where('cid', $class->cid)->update(['arrange_times' => 0]);
                if($rs === false) exception('排课次数清零失败');

                /****处理排班数据，用于后续使用* start****/
                $week_day_arr = [];
                foreach($schedules as $per_schedule) {
                    $tmp_data = [
                        'int_start_hour' => $per_schedule->int_start_hour,
                        'int_end_hour'  => $per_schedule->int_end_hour,
                        'cr_id'         => $per_schedule->cr_id > 0 ? $per_schedule->cr_id : $class->cr_id,
                        'consume_lesson_hour' => $per_schedule->consume_lesson_hour,
                    ];

                    $week_day_arr[$per_schedule->week_day][] = $tmp_data;
                }
                /****处理排班数据，用于后续使用* end****/


                $interval_spec = 'P1D';
                $interval = new \DateInterval($interval_spec);
                $date_range = new \DatePeriod($begin_date, $interval ,$end_date);

                //插入排课表的数据
                $data = [];
                $data['bid'] = $class->bid;
                $data['cid'] = $class->cid;
                $data['teach_eid'] = $class->teach_eid;
                $data['second_eid'] = $class->second_eid;
                $data['lid'] = $class->lid;
                $data['season'] = $class->season;


                //假期是否排课
                if($exclude_holiday) {
                    $holiday = [];
                } else {
                    $holiday = $this->m_holiday->getHolidays($class->bid,$begin_date->getTimestamp(),$end_date->getTimestamp());
                    $holiday = array_map('format_int_day', $holiday);
                }

                //循环日期，写入排课
                $chapter_index = 0;
                foreach($date_range as $per_date) {
                    $this_loop_weekday = $per_date->format('w');
                    $this_loop_weekday = $this_loop_weekday == 0 ? 7 : $this_loop_weekday;
                    if(!in_array($this_loop_weekday, array_keys($week_day_arr))) {
                        continue;
                    }
                    $data['int_day'] = $per_date->format('Ymd');
                    //假期不排课
                    if (in_array($data['int_day'], $holiday)) {
                        continue;
                    }

                    foreach($week_day_arr[$this_loop_weekday] as $week_day_row) {
                        $chapter_index++;
                        if ($class->lesson_times < $chapter_index) {
                            break;
                        }
                        $data['chapter_index'] = $chapter_index;
                        $data['int_start_hour'] = $week_day_row['int_start_hour'];
                        $data['int_end_hour'] = $week_day_row['int_end_hour'];
                        $data['cr_id'] = $week_day_row['cr_id'];
                        $data['consume_lesson_hour'] = $week_day_row['consume_lesson_hour'];
                        unset($this->data['ca_id']);
                        $result = $this->addOneCourseOfClass($class, $data);

                        if (!$result) {
                            $this->user_error($this->getErrorMsg());
                            return false;
                        }

                    }

                }

            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }


    private function build_class_loop_arrange_item($input,$int_day){
        $item =  [
            'cr_id'          => intval($input['cr_id']),
            'int_day'        => $int_day,
            'int_start_hour' => $input['int_start_hour'],
            'int_end_hour'   => $input['int_end_hour'],
            'teach_eid'      => intval($input['teach_eid']),
            'second_eid'     => intval($input['second_eid']),
            'second_eids'     => $input['second_eids'],
            'consume_lesson_hour' => isset($input['consume_lesson_hour']) ? $input['consume_lesson_hour'] : 0,
            'ignore_class_ec' => isset($input['ignore_class_ec']) ? $input['ignore_class_ec'] : 0,
            'ignore_class_cc' => isset($input['ignore_class_cc']) ? $input['ignore_class_cc'] : 0,
            'tb_id' =>  $input['tb_id'],
            'tbs_id' =>  $input['tbs_id']
        ];
        if(isset($input['consume_source_type'])){
            $item['consume_source_type'] = intval($input['consume_source_type']);
        }
        if(isset($input['consume_lesson_amount'])){
            $item['consume_lesson_amount'] = floatval($input['consume_lesson_amount']);
        }
        return $item;
    }

    /*
     * desc: 班级手动排课
     * 1. 循环排课
     * 2. 预排课
     */
    public function addCourseOfClass(Classes $class, $input) {

        // 虚构一个循环排课类型   0:按周循环  1:单周循环  2:双周循环$
        $input['loop_type'] = isset($input['loop_type'])?intval($input['loop_type']):0;

        $is_loop = isset($input['isloop']) ? $input['isloop'] : 0;
        // $loop_arranges = isset($input['loop_arranges']) && !empty($input['loop_arranges']) ? $input['loop_arranges'] : [];
        $loop_arranges = [];
        $loop_times = isset($input['loop_times']) ? $input['loop_times'] : 1;
        $is_skip_holiday = 1;
        $teach_eid = intval($input['teach_eid']);



        if(!$teach_eid){
            return $this->input_param_error('teach_eid');
        }

        $this->startTrans();
        try {
            //--1-- 如果不是循环排课，则单独添加一个排课
            if (!$is_loop) {
                $loop_arranges[] = $this->build_class_loop_arrange_item($input,format_int_day($input['int_day']));

            }else{
                if(empty($loop_arranges)){
                    $holidays = [];
                    if($is_skip_holiday){
                        $holidays = getBranchHoliday();
                    }

                    $looped_times = 0;
                    $start_loop_time = strtotime($input['int_day']);
                    $week = date('W',$start_loop_time);
                    if($input['loop_type'] == 0){
                        $time_step = 604800;
                    }else{
                        $time_step = 2*604800;
                        if(
                            ($input['loop_type'] == 1 && $week % 2 == 0 ) ||
                            ($input['loop_type'] == 2 && $week % 2 == 1 )
                        ){
                            $start_loop_time += 604800;
                        }
                    }
                    $loop_time = $start_loop_time;
                    while($looped_times < $loop_times){
                        $int_day = date('Ymd',$loop_time);
                        if ($is_skip_holiday && in_array($int_day,$holidays)) {
                            $loop_time += $time_step;
                            continue;
                        }
                        $loop_arrange_item = $this->build_class_loop_arrange_item($input,$int_day);
                        array_push($loop_arranges,$loop_arrange_item);
                        $looped_times++;
                        $loop_time += $time_step;
                    }
                }
            }

//            $lesson_times = count($loop_arranges);
            foreach ($loop_arranges as $per_arrange) {
                $rs = $this->addOneCourseOfClass($class, $per_arrange);
                if(!$rs) throw new FailResult($this->getErrorMsg());
            }

            Classes::UpdateArrangeTimes($class['cid']);

        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    //班级添加一个课程
    public function addOneCourseOfClass(Classes $class, $input, $is_force = 0, $lesson_times = 0)
    {
//        $cid = $class->cid;
//        $count = $this->where('cid', $cid)->count();
//        if (!$is_force){
//            if ($class->lesson_times < $lesson_times + $count){
//                return $this->user_error('课程设定上课次数低于排课次数，是否更改', self::CODE_HAVE_RELATED_DATA);
//            }

//            --1-- 数据判断
            $cid = $class->cid;
            $count = $this->where('cid', $cid)->count();
            $input['chapter_index'] = intval($count) + 1;
            if (isset($input['chapter_index']) && ($input['chapter_index'] > $class->lesson_times)) {
            $this->error = '排课不能高于班级设定的课次';
            return false;
            }

//            $this->startTrans();
//            try {
//                $class->lesson_times = $lesson_times;
//                $rs = $class->save();
//                if(!$rs){
//                    return $this->user_error('lesson_times error');
//                }
//            } catch (Exception $e) {
//                $this->rollback();
//                return $this->exception_error($e);
//            }
//            $this->commit();
//            return true;
//        }


        $this->startTrans();
        try {
            //--2-- 添加课程
            $class_info = $class->toArray();
            $data = [];
            array_copy($data,$class_info,['bid','cid','lid','sj_id','grade','teach_eid','second_eid']);

            if(isset($input['sj_id'])){
                $data['sj_id'] = $input['sj_id'];
            }
            if(isset($input['teach_eid'])){
                $data['teach_eid'] = $input['teach_eid'];
            }
            if(isset($input['second_eid'])){
                $data['second_eid'] = $input['second_eid'];
            }
            if(isset($input['second_eids'])){
                $data['second_eids'] = $input['second_eids'];
            }

            $data = array_merge($input, $data);
            $rs = $this->canArrangeCourse($data);
            if ($rs === false) throw new FailResult($this->getErrorMsg());
            if(empty($data['sj_id'])) throw new FailResult('科目id错误');
            $this->data([])->isUpdate(false)->allowField(true)->validate(true)->save($data);
            

            
            //--3-- 更新班级数据

            $end_course = $this->where('cid', $class->cid)->order('int_day desc')->field('int_day')->find();
            $class->setInc('arrange_times');
            $class->where('cid', $cid)->update(['end_lesson_time' => strtotime($end_course['int_day'])]);
            $this->commit();

            //队列添加 排课学生
            $queue_data = [];
            $queue_data['class'] = 'AddCourseArrangeStudents';
            $queue_data['ca_id'] = $this->getAttr('ca_id');
            if($queue_data['ca_id']){
                queue_push('Base',$queue_data);
                //queue_push('AddCourseArrangeStudents',$queue_data);
            }

            // 添加 班级排课日志
            ClassLog::addClassArrangeLog($class,$input);
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }
  
    /**
     * 添加规律排课
     * @param [type] $data [description]
     */
    public function addLawCourseArrange($loop_arranges)
    {  
        $ret = ['success'=>0,'fail'=>0];
        //添加排课
        foreach ($loop_arranges as $input) {
            
            if(isset($input['second_eids']) && !empty($input['second_eids'])){
                $input['second_eid'] = $input['second_eids'][0];
            }

            if($input['cid'] && $input['lesson_type'] == 0){   // 班级排课
                $nums = $this->addLawCourseArrangesOfClass($input);
            }
            if($input['sid'] && $input['lesson_type'] == 1){   //一对一排课
                $nums = $this->addLawCourseArrangesOfStu($input);
            }
            if(!empty($input['sids']) && $input['lesson_type'] == 2){  // 一对多排课
                $nums = $this->addLawCourseArrangesOfStus($input);
            }

            $ret['success'] += $nums['success'];
            $ret['fail'] += $nums['fail'];
        }
        return $ret;
    }
    
    /**
     * 班级规律排课
     * @param [type] $input [description]
     * @param [type] $num   [description]
     */
    public function addLawCourseArrangesOfClass($input)
    {
        $data = [];
        $num = [
            'success' => 0,
            'fail'    => 0, 
        ];
        $this->startTrans();
        try{
            $class = Classes::get($input['cid']);
            $cid = $class->cid;
            $class_info = $class->toArray();
            array_copy($data,$class_info,['bid','cid','lid','sj_id','grade','teach_eid','second_eid']);
            if(isset($input['sj_id'])){
                $data['sj_id'] = $input['sj_id'];
            }
            if(isset($input['teach_eid'])){
                $data['teach_eid'] = $input['teach_eid'];
            }

            $data['cr_id'] = isset($input['cr_id']) ? $input['cr_id'] : $class_info['cr_id'];

            if(isset($input['second_eid'])){
                $data['second_eid'] = $input['second_eid'];
            }
            $data = array_merge($input, $data);
            $rs = $this->canArrangeLawCourse($data);
            if ($rs === false){
                $num['fail'] += 1;
            }else{
                $num['success'] += 1;
                $this->data([])->isUpdate(false)->allowField(true)->validate(true)->save($data);
                //队列添加 排课学生
                $queue_data = [];
                $queue_data['class'] = 'AddCourseArrangeStudents';
                $queue_data['ca_id'] = $this->getAttr('ca_id');
                if($queue_data['ca_id']){
                    queue_push('Base',$queue_data);
                }
                // 更新班级数据
                $end_course = $this->where('cid', $cid)->order('int_day desc')->field('int_day')->find();
                $class->setInc('arrange_times');
                $class->where('cid', $cid)->update(['end_lesson_time' => strtotime($end_course['int_day'])]);
            }

            // 添加一条班级排课 日志
            ClassLog::addClassArrangeLog($class,$data);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        return $num;
    }
    
    /**
     * 一对一规律排课
     * @param [type] $input [description]
     * @param [type] $num   [description]
     */
    public function addLawCourseArrangesOfStu($input)
    {
        $data = [];
        $num = [
            'success' => 0,
            'fail'    => 0, 
        ];
        $m_cas = new CourseArrangeStudent;
        $this->startTrans();
        try{
            $sid = $input['sid'];
            $student_info = Student::get($sid);
            array_copy($data,$student_info,['bid']);
            $data = array_merge($input,$data);
            $rs = $this->canArrangeLawCourse($data);
            if($rs === false){
                $num['fail'] += 1;
            }else{
                $num['success'] += 1;
                $this->data([])->isUpdate(false)->allowField(true)->validate(true)->save($data);
                // 添加排课学生
                $ca_id = $this->getAttr('ca_id');
                $course = $this->find($ca_id);
                $m_cas->addOneArrangeStudent($course, $sid);
            }

            //建立老师与学员之间的关系
            $info = array(
                'sid' => $sid,
                'rid' => EmployeeStudent::EMPLOYEE_TEACHER,
                'eid' => $input['teach_eid']
            );
            $type = EmployeeStudent::TYPE_ONE;
            $lid  = $input['lid'];
            EmployeeStudent::addEmployeeStudentRelationship($info,$type,$lid);


            //建立助教与学员之间的关系
            if(!empty($input['second_eids'])){
                foreach ($input['second_eids'] as $eid) {
                    $info_s = array(
                        'sid' => $sid,
                        'rid' => EmployeeStudent::EMPLOYEE_TA,
                        'eid' => $eid
                    );
                    EmployeeStudent::addEmployeeStudentRelationship($info_s,$type,$lid);
                }
            }
            
        }catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return $num;
    }

    /**
     * 一对多规律排课
     * @param [type] $input [description]
     * @param [type] $num   [description]
     */
    public function addLawCourseArrangesOfStus($input)
    {
        $data = [];
        $num = [
            'success' => 0,
            'fail'    => 0, 
        ];
        $m_cas = new CourseArrangeStudent;
        $this->startTrans();
        try{
            $sid = $input['sids'][0];
            $student_info = Student::get($sid);
            array_copy($data,$student_info,['bid']);
            $data = array_merge($input,$data);

            $rs = $this->canArrangeLawCourse($data);
            if($rs === false){
                $num['fail'] += 1;
            }else{
                $num['success'] += 1;
                $this->data([])->isUpdate(false)->allowField(true)->validate(true)->save($data);
                // 添加排课学生
                $ca_id = $this->getAttr('ca_id');
                $course = $this->find($ca_id);
                foreach ($input['sids'] as $s) {
                    if(!$s) continue;
                    $m_cas->addOneArrangeStudent($course, $s);
                }
            }

            // 建立老师与学员之间的关系
            foreach ($input['sids'] as $sid) {
                $info = array(
                    'sid' => $sid,
                    'rid' => EmployeeStudent::EMPLOYEE_TEACHER,
                    'eid' => $input['teach_eid']
                );
                $type = EmployeeStudent::TYPE_MANY;
                $lid  = $input['lid'];
                EmployeeStudent::addEmployeeStudentRelationship($info,$type,$lid);
            }

            // 建立助教与学员之间的关系
            if(!empty($input['second_eids'])){
                foreach ($input['second_eids'] as $eid) {
                    foreach ($input['sids'] as $sid) {
                        $info = array(
                            'sid' => $sid,
                            'rid' => EmployeeStudent::EMPLOYEE_TA,
                            'eid' => $eid
                        );
                        EmployeeStudent::addEmployeeStudentRelationship($info,$type,$lid);
                    }
                }
            }
            

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return $num;

    }


    /**
     * 写入数据库之前 检测是否冲突
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function canArrangeLawCourse($input)
    {
        $m_ca = new self();
        
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
                return false;
            }
            unset($w['teach_eid']);
        }
        
        // 检测助教是是否冲突
        /*if($input['second_eid'] && $input['ignore_class_ec'] == 0){
            $w['second_eid'] = $input['second_eid'];
            $exist_data = $m_ca->where($w)->find();
            if(!empty($exist_data)){
                return false;
            }
            unset($w['second_eid']);
        }*/
        
        // 检测教室是否冲突
        if($input['ignore_class_cc'] == 0 && isset($input['cr_id'])){
            $w['cr_id'] = $input['cr_id'];
            $w['bid'] = request()->header('x-bid');
            $exist_data = $m_ca->where($w)->find();
            if(!empty($exist_data)){
                return false;
            }
            unset($w['cr_id']);
            unset($w['bid']);
        }
        
        // 检测排课日期是否为节假日
        $holiday = getBranchHoliday();
        if(in_array($w['int_day'],$holiday)){
            return false;
        }

        // 2 班级排课
        if($input['lesson_type'] == 0){
            $class = Classes::get($input['cid']);
            // 检测班级是否冲突
            $w['cid'] = $input['cid'];
            $w['is_cancel'] = 0;
            $exist_data = $m_ca->where($w)->find();
            if(!empty($exist_data)){
                return false;
            }
            // 检测排课次数是否大于班级设定的课时数
            /*$w_exceed['cid'] = $class->cid;
            $w_exceed['is_cancel'] = 0;
            $count = $m_ca->where($w_exceed)->count();
            $input['chapter_index'] = intval($count) + 1;
            if (isset($input['chapter_index']) && ($input['chapter_index'] > $class->lesson_times)) {
                return false;
            }*/

            // 检测班级学员 在同一时间是否有其他排课
            // $w['cid'] = 0;
            unset($w['cid']);
            $sids = (new ClassStudent)->where(['cid'=>$class->cid,'status'=>1])->column('sid');
            foreach ($sids as $sid) {
                $w['sid'] = $sid;
                $exist_data = (new CourseArrangeStudent)->where($w)->find();
                if(!empty($exist_data)){
                    return false;
                }
            }
        }

        // 3 一对一 一对多排课
        if( $input['lesson_type'] == 1 || $input['lesson_type'] == 2 ){ 
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
                if($arranged_lesson_hours >= $total_lesson_hours){
                    return false;
                }

                // 检测 学员是否在同时段存在排课 
                $w['sid'] = $input['sid'];
                $exist_data = (new CourseArrangeStudent)->where($w)->find();
                if(!empty($exist_data)){
                    return false;
                }
            }
            if(!empty($input['sids'])){   // 一对多排课
                foreach ($input['sids'] as $sid) {
                    // 检测排课次数是否超出
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
                        return false;
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
                if(!empty($exist_data)){
                    return false;
                }
            }
        }*/

        return true;
    }


    /**
     * 是否可以排课
     * @param $new_ca_info
     * @param int $ca_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function canArrangeCourse($new_ca_info, $ca_id = 0,$ajust_ca_ids = [])
    {
        $int_day = format_int_day($new_ca_info['int_day']);
        $int_start_hour = format_int_hour($new_ca_info['int_start_hour']);
        $int_end_hour = format_int_hour($new_ca_info['int_end_hour']);
        $teach_eid = isset($new_ca_info['teach_eid']) ? $new_ca_info['teach_eid'] : 0;
        $cr_id = isset($new_ca_info['cr_id']) ? $new_ca_info['cr_id'] : 0;
        $cid = isset($new_ca_info['cid']) ? $new_ca_info['cid'] : 0;
        $sid = isset($new_ca_info['sid']) ? $new_ca_info['sid'] : 0;
        $bid = isset($new_ca_info['bid']) ? $new_ca_info['bid'] : request()->header('x-bid');
        if($teach_eid == 0 && $cr_id == 0 && $cid == 0) return true;

        $new_ca_info['ignore_class_ec'] = isset($new_ca_info['ignore_class_ec']) ? $new_ca_info['ignore_class_ec'] : 0;
        $new_ca_info['ignore_class_cc'] = isset($new_ca_info['ignore_class_cc']) ? $new_ca_info['ignore_class_cc'] : 0;

        $where = [];
        $where['int_day'] = $int_day;
        $where['int_start_hour'] = ['elt', $int_start_hour];
        $where['int_end_hour'] = ['gt', $int_start_hour];

        if($ca_id > 0){
            $where['ca_id'] = ['NEQ',$ca_id];
        }

        if($teach_eid > 0 && $new_ca_info['ignore_class_ec'] == 0) {
            $where['teach_eid'] = $teach_eid;
            $ex_ca = $this->where($where)->find();
            if($ex_ca){
                $ca_object = $ex_ca->getCourseArrangeObject();
                $msg = sprintf('%s号 %s %s-%s，老师:%s已经有排课,授课对象是%s',
                    int_day_to_date_str($int_day),
                    $this->getChiWeekdayByDay($int_day),
                    int_hour_to_hour_str($int_start_hour),
                    int_hour_to_hour_str($int_end_hour),
                    get_employee_name($teach_eid),
                    $ca_object);
                return $this->user_error($msg);
            }
            unset($where['teach_eid']);
        }

        if($cr_id > 0 && $new_ca_info['ignore_class_cc'] == 0) {
            $where['cr_id'] = $cr_id;
            $where['bid']   = $bid;

            $ex_ca = $this->where($where)->find();
            if($ex_ca){
                $classroom = get_classroom_info($cr_id);
                $room_name = $classroom['room_name'];
                $ca_object = $ex_ca->getCourseArrangeObject();
                $msg = sprintf('%s号 %s %s-%s，教室:%s已经有排课,授课对象是:%s',
                    int_day_to_date_str($int_day),
                    $this->getChiWeekdayByDay($int_day),
                    int_hour_to_hour_str($int_start_hour),
                    int_hour_to_hour_str($int_end_hour),
                    $room_name,
                    $ca_object
                );
                return $this->user_error($msg);
            }
            unset($where['teach_eid']);
            unset($where['bid']);
        }

        if($cid > 0) {
            $where['cid'] = $cid;
            $ex_ca = $this->where($where)->find();
            if($ex_ca){
                if(!empty($ajust_ca_ids) && !in_array($ex_ca['ca_id'],$ajust_ca_ids)) {
                    $msg = sprintf('%s号 %s %s-%s，班级:%s已经有排课',
                        int_day_to_date_str($int_day),
                        $this->getChiWeekdayByDay($int_day),
                        int_hour_to_hour_str($int_start_hour),
                        int_hour_to_hour_str($int_end_hour),
                        get_class_name($cid)
                    );
                    return $this->user_error($msg);
                }
            }
            unset($where['cid']);
        }

        if($sid > 0) {
            $where['sid'] = $sid;
            $mCas = new CourseArrangeStudent();
            $ex_cas = $mCas->where($where)->find();
            if($ex_cas){
                $msg = sprintf('%s号 %s %s-%s，学生:%s 已经有排课存在',
                    int_day_to_date_str($int_day),
                    $this->getChiWeekdayByDay($int_day),
                    int_hour_to_hour_str($int_start_hour),
                    int_hour_to_hour_str($int_end_hour),
                    get_student_name($sid)
                );
                return $this->user_error($msg);
            }
            unset($where['sid']);
        }

        return true;
    }

    /**
     * 批量更新排课信息
     * @param array $data
     * @return bool
     */
    public function updateCourse(array $data)
    {
        $first_ca = $data[0];
        if(!$first_ca || !isset($first_ca['ca_id'])){
            return $this->user_error('parameter error!');
        }
        $old_first_ca_info = get_ca_info($first_ca['ca_id']);
        if(!$old_first_ca_info){
            return $this->user_error('parameter error:ca_id does not exists!');
        }
        if($first_ca['int_day'] != $old_first_ca_info['int_day']) {
            if ($first_ca['int_day'] > $old_first_ca_info['int_day']) {
                //后移，那么按后排序
                $data = list_sort_by($data, 'int_day', 'desc');
            } elseif ($first_ca['int_day'] < $old_first_ca_info['int_day']) {
                //前移
                $data = list_sort_by($data, 'int_day', 'asc');
            }
        }
        $this->startTrans();
        try {
            foreach ($data as $row) {
                $result = $this->updateOneCourse($row);
                if(false === $result){
                    $this->rollback();
                    return false;
                }
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 更新单条排课记录
     * @param $row
     * @return bool
     */
    public function updateOneCourse($row)
    {
        if (!isset($row['ca_id'])) {
            return $this->user_error('排课参数传递错误:缺少ca_id!');
        }

        if (isset($row['int_day']) && !empty($row['int_day'])) {
            $row['int_day'] = format_int_day($row['int_day']);
        }

        $m_ca = $this->findOrFail($row['ca_id']);

        $old_ca_info = $m_ca->getData();

        $new_ca_info = array_merge($old_ca_info, $row);

        $result = $m_ca->canArrangeCourse($new_ca_info, $row['ca_id']);
        if (false === $result) {
            return $this->user_error($m_ca->getError());
        }

        $this->startTrans();
        try {
            $allow_field = 'name,teach_eid,second_eid,second_eids,cr_id,int_day,int_start_hour,int_end_hour,consume_lesson_hour,consume_source_type,consume_lesson_amount';
            $w_ca_update['ca_id'] = $row['ca_id'];
            $result = $m_ca->allowField($allow_field)
                ->isUpdate(true)
                ->save($new_ca_info, $w_ca_update);
            if (false === $result) {
                $this->rollback();
                return $this->user_error($m_ca->getError());
            }

            $update_data = [];
            $ajust_date = false;
            $fields = ['int_day', 'int_start_hour', 'int_end_hour'];

            foreach ($fields as $f) {
                if ($old_ca_info[$f] != $new_ca_info[$f]) {
                    $update_data[$f] = $new_ca_info[$f];
                }
            }

            $w_update['ca_id'] = $row['ca_id'];
            if (!empty($update_data)) {
                //更新试听安排
                $result = TrialListenArrange::update($update_data, $w_update);
                if (false === $result) {
                    $this->rollback();
                    return $this->user_error('更新试听安排时间失败');
                }

                //更新补课安排
                $result = MakeupArrange::update($update_data, $w_update);
                if (false === $result) {
                    $this->rollback();
                    return $this->user_error('更新补课安排时间失败');
                }

                // 关联更新 course_prepare表的数据
                $mCoursePrepare = new CoursePrepare();
                $m_cp = $mCoursePrepare->where('ca_id', $row['ca_id'])->find();

                if ($m_cp) {
                    $result = CoursePrepare::update($update_data, $w_update);
                    if (false === $result) {
                        $this->rollback();
                        return $this->user_error('更新备课服务失败');
                    }
                }
            } else {
                $ajust_date = true;
            }
            if (isset($row['consume_source_type'])) {
                $update_data['consume_source_type'] = $row['consume_source_type'];
            }
            if (isset($row['consume_lesson_amount'])) {
                $update_data['consume_lesson_amount'] = $row['consume_lesson_amount'];
            }
            if (!empty($update_data)) {
                $result = CourseArrangeStudent::update($update_data, $w_update);
                if (false === $result) {
                    $this->rollback();
                    return $this->user_error('更新学生课程时间失败');
                }
            }

            if ($ajust_date) {
                $push_alert = false;
                //小于1周的时间调整就发通知
                if (isset($update_data['int_day'])) {
                    $diff_day = int_day_diff($old_ca_info['int_day'], $new_ca_info['int_day']);
                    if (abs($diff_day) <= 7) {
                        $push_alert = true;
                    }
                } else {
                    $int_start_hour = intval(format_int_hour($row['int_start_hour']));
                    $int_end_hour = intval(format_int_hour($row['int_end_hour']));
                    if ($old_ca_info['int_start_hour'] != $int_start_hour || $old_ca_info['int_end_hour'] != $int_end_hour) {
                        $push_alert = true;
                    }
                }

                if ($push_alert) {
                    $this->pushAlterClassTime($old_ca_info, $row);
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * 推送排课调整提醒
     * @param $old_data
     * @param $new_data
     * @return bool
     */
    public function pushAlterClassTime($old_data,$new_data)
    {
        $mMessage = new Message();
        $mClass   = new Classes();
        try {
            $old_time = int_day_to_date_str($old_data['int_day']). ' ' .int_hour_to_hour_str($old_data['int_start_hour']). '-' .int_hour_to_hour_str($old_data['int_end_hour']);
            $new_time = int_day_to_date_str($new_data['int_day']). ' ' .$new_data['int_start_hour']. '-' .$new_data['int_end_hour'];

            $task_data['ca_id'] = $new_data['ca_id'];
            $task_data['subject'] = '上课时间调整通知';
            $class_name  = get_course_name_by_row($new_data);

            $task_data['alter_reason'] = '您有一节课 '.$class_name.' 由： '.$old_time.' 调整到： '.$new_time;
            $task_data['class_name'] = $class_name;
            $task_data['class_time'] = $new_time;

            $student_list = $mClass->getStudents($new_data['cid']);
            foreach ($student_list as $student){
                $task_data['sid'] = $student['sid'];
                $rs = $mMessage->sendTplMsg('alter_class_time',$task_data ,[],2);
                if($rs === false) return $this->user_error($mMessage->getError());
            }
        } catch(\Exception $e) {
            log_write($e->getFile() . ' ' . $e->getLine() . ' '. $e->getMessage(), 'error');
        }

        return true;
    }



    //根据日期转化为中文星期几
    public function getChiWeekdayByDay($day)
    {
        $weekday = intval(date('w', strtotime($day)));
        $chi_weekday = '';
        switch ($weekday) {
            case 0:
                $chi_weekday = '星期日';
                break;
            case 1:
                $chi_weekday = '星期一';
                break;
            case 2:
                $chi_weekday = '星期二';
                break;
            case 3:
                $chi_weekday = '星期三';
                break;
            case 4:
                $chi_weekday = '星期四';
                break;
            case 5:
                $chi_weekday = '星期五';
                break;
            case 6:
                $chi_weekday = '星期六';
                break;
        }
        return $chi_weekday;
    }
    
    /**
     * 根据ca_id 删除 请假记录
     * @param  [type] $ca_id [description]
     * @return [type]        [description]
     */
    public function deleteStudentLeave($ca_id)
    {
        if(empty($ca_id))  return $this->input_param_error('ca_id',1);  // 缺少输入参数
        
        $this->startTrans();
        try{
            $m_sl = new StudentLeave;
            $res = $m_sl->where('ca_id',$ca_id)->delete();
            if($res === false){
                return $this->sql_delete_error('student_leave');
            }
            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        return true;
    }

    //删除一个排课
    public function deleteOneCourse(CourseArrange $course)
    {
        if($course->is_attendance !== CourseArrange::IS_ATTENDANCE_NO) {
            return $this->user_error('此排课已经考勤，不能删除');
        }

        //如果排课有请假记录，同时删除请假记录
        $m_sl = new StudentLeave;
        $student_leave = $m_sl->where('ca_id',$course->ca_id)->find();
        if(!empty($student_leave)){
            // return $this->user_error('排课有关联的请假记录，请先删除请假记录');
            $result = $this->deleteStudentLeave($course->ca_id);
            if(false ===  $result){
                return $this->user_error('请假记录删除失败');
            }
        }

        /*$m_sl = new StudentLeave();
        $student_leave = $m_sl->where('ca_id', $course->ca_id)->find();
        if(!empty($student_leave)) return $this->user_error('排课有关联的请假记录，请先删除请假记录');*/
        $this->startTrans();
        try {
            $mCas = new CourseArrangeStudent();
            $result = $mCas->deleteByCaId($course->ca_id);
            if (false === $result){
		        $this->rollback();
                return $this->user_error($mCas->getError());
            }

            //--1-- 如果排课是班课
            $course_info = $course->getData();
            $course->delete();

            if ($course_info['lesson_type'] == self::LESSON_TYPE_CLASS) {
                $cid = $course_info['cid'];
                $list = $this->where('cid', $cid)->order('int_day', 'asc')
                    ->order('int_start_hour', 'asc')->select();
                $index = 1;
                foreach ($list as $per_ca) {
                    $rs = $this->where('ca_id', $per_ca['ca_id'])->update(['chapter_index' => $index]);
                    if ($rs === false){
		    	        $this->rollback();
                        return $this->user_error('更新班级下的其他课程序号失败');
                    }
                    $index++;
                }
                Classes::UpdateArrangeTimes($cid);
                if ($rs === false){
		            $this->rollback();
                    return $this->user_error('更新班级已排课次失败');
                }
                //添加一条删除班级排课 日志
                ClassLog::addDeleteClassArrangeLog($course_info);
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    //删除多个排课
    public function deleteBatch(array $ca_ids)
    {
        if(empty($ca_ids)) return $this->user_error('ca_ids不能为空');

        try {
            $this->startTrans();

            foreach ($ca_ids as $ca_id) {
                /** @var CourseArrange $course */
                $course = $this->find($ca_id);
                if (empty($course) || $course['is_attendance'] != self::IS_ATTENDANCE_NO) {
                    return $this->user_error('课程不存在或者已经考勤');
                }

                $rs = $course->deleteOneCourse($course);
                if ($rs === false) return $this->user_error($course->getErrorMsg());
            }

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public static function getCourseArrangeSjId($ca_id)
    {
        $course = CourseArrange::get(['ca_id' => $ca_id]);
        if(isset($course->sj_id)) return $course->sj_id;

        $class = Classes::get(['cid' => $course->cid]);

        $sj_id = $class->sj_id ? $class->sj_id : 0;
        return $sj_id;
    }

    public function checkIsStudentTrial($student)
    {
        if (is_numeric($student)) {
            $sid = $student;
        } elseif ($student instanceof Student) {
            $sid = $student['sid'];
        } else {
            throw new Exception('invalid param student');
        }

        $w = [];
        $w['sid'] = $sid;
        $w['ca_id'] = $this->getData('ca_id');
        return TrialListenArrange::get($w);
    }

    public function checkIsCustomerTrial($customer)
    {
        if (is_numeric($customer)) {
            $cu_id = $customer;
        } elseif ($customer instanceof Customer) {
            $cu_id = $customer['cu_id'];
        } else {
            throw new Exception('invalid param $customer');
        }

        $w = [];
        $w['cu_id'] = $cu_id;
        $w['ca_id'] = $this->getData('ca_id');
        return TrialListenArrange::get($w);
    }

    /**
     * 通过sid来确定该同学有没有在该次排课请假
     * @param $sid int
     */
    public function checkIsLeave($sid)
    {
        $ca_id = $this->getData('ca_id');
        $w = [];
        $w['ca_id'] = $ca_id;
        $w['sid']   = $sid;
        $model = StudentLeave::get($w);
        return $model;
    }

    public function checkIsSuspend($sid)
    {
        $lid = $this->getData('lid');
        $int_day = $this->getData('int_day');
        $int_start_hour = $this->getData('int_start_hour');
        $timestamp = int_to_time($int_day, $int_start_hour);
        $w = [];
        $w['lid'] = $lid;
        $w['sid'] = $sid;
        $model = StudentSuspend::where($w)->where('begin_time', '>', $timestamp)->where('end_time', '<', $timestamp)->find();
        return $model;
    }

    public function checkIsLater($timestamp)
    {
        $int_start_hour = $this->getData('int_start_hour');
        $atd_int_hour = intval(date('Hi', $timestamp));
        return $atd_int_hour <= $int_start_hour;
    }

    public function getDisableAttendanceAttr($value,$data){
        if(!isset($data['int_day'])){
            return 0;
        }
        $int_day = $data['int_day'];
        $now_int_day = intval(date('Ymd',time()));
        $result = 0;
        if($int_day > $now_int_day){
            $result = 1;
        }
        return $result;
    }

    public function getNeedNumsAttr()
    {
        $normal_num = count($this->getStudentList(false));
//        $leave_num  = $this->getAttr('leave_nums');
        $makeup_num = $this->getAttr('makeup_nums');
        $trial_num  = $this->getAttr('trial_nums');
        return $normal_num + $makeup_num + $trial_num;
    }

    public function getLeaveNumsAttr()
    {
        $ca_id = $this->getData('ca_id');
        return StudentLeave::where(['ca_id' => $ca_id])->count();
    }

    public function getMakeupNumsAttr()
    {
        $ca_id = $this->getData('ca_id');
        return MakeupArrange::where(['ca_id' => $ca_id])->count();
    }

    public function getTrialNumsAttr()
    {
        $ca_id = $this->getData('ca_id');
        return TrialListenArrange::where(['ca_id' => $ca_id])->count();
    }

    /**
     * 确定一个学生是否与当前排课有关系
     * @param Student $student
     * @return bool
     */
//    public function checkValidStudent($student, $include_makeup = true)
//    {
//        $student_list = $this->getStudentList($include_makeup);
//        if (empty($student_list)) {
//            throw new Exception('该排课没有关联的学生');
//        }
//        $sids = array_column(collection($student_list)->toArray(), 'sid');
//        return in_array($student['sid'], $sids);
//    }

    /**
     * 获取该次排课关联的试听记录
     * @return \think\model\relation\HasMany
     */
    public function trialListenArranges()
    {
        return $this->hasMany('TrialListenArrange', 'ca_id', 'ca_id');
    }

    /**
     * 获取该次排课关联的补课记录
     * @return \think\model\relation\HasMany
     */
    public function makeupArranges()
    {
        return $this->hasMany('MakeupArrange', 'ca_id', 'ca_id');
    }

    public function getArrangeStudents()
    {
        $lesson_type = $this->getData('lesson_type');

        if($lesson_type == Lesson::LESSON_TYPE_CLASS){
            $result = $this->createClassArrangeStudents();
            if(!$result){
                return false;
            }
        }
        
        $student_list = $this->getAttr('students');
        
        if (is_null($student_list)) {
            return [];
        }
        foreach ($student_list as $st) {
            if (!$st instanceof Student) {
                throw new Exception('pass');
            }
            $st['student_lesson'] = $st->getStudentLessonBySjId($this->getAttr('sj_id'));
            if ($leave_record = $this->checkIsLeave($st['sid'])) {
                $st['is_leave']     = true;
                $st['leave_record'] = $leave_record;
            } else {
                $st['is_leave'] = false;
            }

            if ($attendance_record = $this->checkIsAttendance($st['sid'])) {
                $st['is_attendance']     = true;
                $st['attendance'] = $attendance_record;
            } else {
                $st['is_attendance'] = false;
            }

        }
        if ($student_list) {
            usort($student_list, function ($a, $b) {
                if ($a['is_attendance'] == $b['is_attendance']) {
                    return 0;
                }
                return $a['is_attendance'] == true ? 1 : -1;
            });
        }
        return $student_list;
    }


    public function checkIsAttendance($sid)
    {
        $ca_id = $this->getData('ca_id');
        $w = [];
        $w['ca_id'] = $ca_id;
        $w['sid']   = $sid;
        return StudentAttendance::get($w);
    }

    public function getTrialStudents()
    {
        $trial_list = $this->getAttr('trial_listen_arranges');
        foreach ($trial_list as $item) {
            if (!$item instanceof TrialListenArrange) {
                throw new Exception('pass');
            }
            if ($item['is_student']) {
                $item->append(['student']);
            } else {
                $item->append(['customer']);
            }
        }
        return $trial_list;
    }

    public function getMakeupStudents()
    {
        $makeup_list = $this->getAttr('makeup_arranges');
        foreach ($makeup_list as $item) {
            if (!$item instanceof MakeupArrange) {
                throw new Exception('pass');
            }
            $item->append(['student_lesson', 'student', 'absence', 'leave']);
            $w = [];
            $w['ca_id'] = $this->getData('ca_id');
            $w['sid']   = $item['sid'];
            $item->status = $item->student->status;
            $item->attendance = StudentAttendance::get($w);
        }
        return $makeup_list;
    }



    /**
     * 试听[学员]考勤
     * @param array $atd_info
     */
    public function student_trial_attendance(array $atd_info)
    {
        if (empty($atd_info['sid'])) {
            throw new \InvalidArgumentException('param invalid');
        }

        $trial_record = $this->checkIsStudentTrial($atd_info['sid']);
        if (empty($trial_record)) {
            throw new \InvalidArgumentException('没有查询到匹配的学员试听记录');
        }
        if (empty($trial_record['is_attendance'])) {
            $atd_info['cls_attendance']->setInc('trial_nums');
        }

        $temp = [];
        $temp['catt_id'] = $atd_info['catt_id'];
        $temp['is_attendance'] = 1;
        if (!empty($atd_info['is_in'])) {
            if (empty($trial_record['attendance_status'])) {
                $atd_info['cls_attendance']->setInc('in_nums');
            }
            $temp['attendance_status'] = 1;
        }

        if (!empty($atd_info['teach_eid'])) {
            $temp['eid'] = $atd_info['teach_eid'];
        }
        $trial_record->allowField(true)->save($temp);
    }

    /**
     * 试听[意向客户]考勤
     * @param array $atd_info
     */
    public function customer_trial_attendance(array $atd_info)
    {
        if (empty($atd_info['cu_id'])) {
            throw new \InvalidArgumentException('param invalid');
        }
        $customer_trial_record = $this->checkIsCustomerTrial($atd_info['cu_id']);
        if (empty($customer_trial_record['is_attendance'])) {
            $atd_info['cls_attendance']->setInc('trial_nums');
        }
        if (empty($customer_trial_record)) {
            throw new \InvalidArgumentException('没有查询到匹配的意向客户试听记录');
        }

        $temp = [];
        $temp['catt_id'] = $atd_info['catt_id'];
        $temp['is_attendance'] = 1;
        if (!empty($atd_info['is_in'])) {
            if (empty($customer_trial_record['attendance_status'])) {
                $atd_info['cls_attendance']->setInc('in_nums');
            }
            $temp['attendance_status'] = 1;
        }

        if (!empty($atd_info['teach_eid'])) {
            $temp['eid'] = $atd_info['teach_eid'];
        }
        $customer_trial_record->allowField(true)->save($temp);
    }

    /**
     * 获取登记考勤的结果
     * @return array
     */
    public function getAttendanceReport()
    {
        return $this->attendance_fail_report;
    }

    //计算某天的排课
    public function countCourseOfDay($day, $is_attendance = null, $lesson_type = null)
    {
        $day = format_int_day($day);
        $where['int_day'] = $day;

        if(!is_null($is_attendance)) {
            if(is_array($is_attendance)) {
                $is_attendance = implode(',', array_filter($is_attendance));
                $where['is_attendance'] = ['in', $is_attendance];
            } else {
                $where['is_attendance'] = $is_attendance;
            }
        }
        !is_null($lesson_type) && $where['lesson_type'] = $lesson_type;

        $num = $this->scope('bid')->where($where)->count();
        return $num;
    }

    //课前微信通知
    public function wechat_tpl_notify($sid)
    {
        $wechat = Wechat::getInstance($this);
        $message['appid'] = $wechat->appid;
        $scene = 'before_class_push';
        $default_template_setting = config('tplmsg')[$scene];
        $message['url'] = $default_template_setting['weixin']['url'];//todo  替换[host]和业务[id]

        if ($wechat->default) {
            $message['template_id'] = $default_template_setting['weixin']['template_id'];
        } else {
            $w = [];
            $w['appid'] = $message['appid'];
            $w['scene'] = $scene;
            $target_tpl = WxmpTemplate::get($w);
            if (empty($target_tpl)) {
                //该公众号还没有成功设置该模板.
                return $this->user_error('该公众号还没有成功设置模板');
            }
            $message['template_id'] = $target_tpl['template_id'];
        }
        $wechat_template_config = user_config('wechat_template');


        $user_template_setting = isset($wechat_template_config[$scene]) ? $wechat_template_config[$scene] : null;
        if (empty($user_template_setting)) {
            //客户如果没有设置公众号的模板消息的first字段、remark字段和颜色的设置，则使用系统默认的公众号的设置
            $user_template_setting = $default_template_setting;
        }


        $sinfo = get_student_info($sid);
        $temp = [];
        $temp['student_name'] = $sinfo['student_name'];
        $temp['lesson_name']  = $this->getAttr('lesson')['lesson_name'];
        $temp['school_time'] = int_day_to_date_str($this->getAttr('int_day')) . ' ' .
            int_hour_to_hour_str($this->getAttr('int_start_hour'))
            . '-' . int_hour_to_hour_str($this->getAttr('int_end_hour'));
        $sys_info = user_config('params');
        //$temp['address'] = !empty($sys_info) && isset($sys_info['cfg_value']['address']) ? $sys_info['cfg_value']['address'] : '';
        $temp['address'] = get_branch_name($this->bid);
        $temp['mobile'] = !empty($sys_info) && isset($sys_info['mobile']) ? $sys_info['mobile'] : '';

        $search  = array_values($user_template_setting['tpl_fields']);
        $replace = array_values($temp);

        $data = $user_template_setting['weixin']['data'];
        foreach ($data as &$subject) {
            $subject = str_replace($search, $replace, $subject);
        }
        $sms_message = str_replace($search, $replace, $user_template_setting['sms']['tpl']);
        $message['data'] = $data;

        $w_us['sid'] = $sid;
        $w_us['is_delete'] = 0;

        $us_list = db('user_student')->where($w_us)->select();

        if (empty($us_list)) {
            return $this->user_error('学员未开启学习管家账号!');
        }

        $inner_message = [];
        $inner_message['og_id'] = $sinfo['og_id'];
        $inner_message['bid'] = $sinfo['bid'];
        $inner_message['sid'] = $sid;
        $inner_message['business_type'] = $scene;
        $inner_message['business_id'] = $this->getAttr('ca_id');
        $inner_message['title']   = $default_template_setting['message']['title'];
        $inner_message['content'] = str_replace($search, $replace, $default_template_setting['message']['content']);
        $inner_message['url']     = $message['url'];
        foreach ($us_list as $us) {
            $user = get_user_info($us['uid']);
            $inner_message['uid'] = $user['uid'];
            Message::create($inner_message);
            if ($user['mobile'] && $user_template_setting['sms_switch']) {
                queue_push('SendSmsMsg', [$user['mobile'], $sms_message]);
            }
            if ($user['openid'] && $user_template_setting['weixin_switch']) {
                $w = [];
                $w['openid'] = $user['openid'];
                $w['subscribe'] = WxmpFans::SUBSCRIBE;
                if (WxmpFans::get($w)) {
                    $message['openid'] = $user['openid'];
                    queue_push('SendWxTplMsg', $message);
                }
            }
        }

        return true;
    }



    /**
     * 删除一个指定的排课
     * @param  integer $ca_id [description]
     * @return [type]         [description]
     */
    public function delOne($ca_id = 0)
    {
        if($ca_id == 0){
            $ca_info = $this->getData();
            if(!isset($ca_info['ca_id'])){
                return $this->user_error('缺少参数:ca_id');
            }
            $ca_id   = $ca_info['ca_id'];
        }

        $ca_info = get_ca_info($ca_id);

        if(!$ca_info){
            return true;
        }


        if($ca_info['is_attendance'] == 1) {
            return $this->user_error('此排课已经考勤，不能删除');
        }

        $this->startTrans();
        try{
            //删除排课下面的学生
            $result = $this->m_course_arrange_student->deleteByCaId($ca_id);
            if(false === $result){
                $this->rollback();
                return $this->m_course_arrange_stduent->getError();
            }
            //删除排课关联的试听记录
            $result = $this->m_trial_listen_arrange->deleteByCaId($ca_id);
            if(false === $result){
                $this->rollback();
                return $this->m_trial_listen_arrange->getError();
            }
            //删除排课关联的补课记录
            $result = $this->m_makeup_arrange->deleteByCaId($ca_id);
            if(false === $result){
                $this->rollback();
                return $this->m_makeup_arrange->getError();
            }
            //删除本排课记录
            $w_del['ca_id'] = $ca_id;
            $result = $this->m_course_arrange->where($w_del)->delete();
            if(false === $result){
                $this->rollback();
                return $this->delete_error('course_arrange');
            }
            if($ca_info['lesson_type'] == 0){
                $result = $this->m_classes->updateArrange($ca_info['cid']);
                if(false === $result){
                    $this->rollback();
                    return $this->user_error($this->m_classes->getError());
                }
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    //一对一、一对多课程再添加学生
    public function addStudents(array $sids)
    {
        $ca_info = $this->getData();
        if(!isset($ca_info['ca_id'])) return $this->user_error('排课数据错误 ');

        $m_csa = new CourseArrangeStudent();
        try {
            $this->startTrans();
            foreach ($sids as $sid) {
                $rs = $m_csa->addOneArrangeStudent($this, $sid);
                if ($rs === false) throw new FailResult($m_csa->getErrorMsg());
            }
            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        }

        return true;
    }


    //排课添加学生，或者删除学生
    public function updateStudents($add_sids = [], $del_sids = [])
    {
        $ca_info = $this->getData();
        if(!isset($ca_info['ca_id'])) return $this->user_error('排课数据不存在');

        try {
            $this->startTrans();
            //增加学员
            if (!empty($add_sids) && is_array($add_sids)) {
                $rs = $this->addStudents($add_sids);
                if($rs === false) throw new FailResult($this->getErrorMsg());

            }

            //移除学员
            if (!empty($del_sids) && is_array($del_sids)) {
                $m_cas = new CourseArrangeStudent();
                $rs = $m_cas->deleteStudentByCaId($del_sids, $this->ca_id);
                if($rs === false) throw new FailResult($m_cas->getErrorMsg());
            }

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    /**
     * @desc 获取考勤对象
     * @param int $ca_id
     * @param bollean $refresh
     */
    public function getAttObjects($ca_id = 0,$refresh = false,$get_sl = true){
        $ca_info = $this->init_cainfo($ca_id);

        if(!$ca_info){
            return false;
        }

        $ca_id = $ca_info['ca_id'];

        $is_class_ca = $ca_info['lesson_type'] == Lesson::LESSON_TYPE_CLASS;


        if($ca_info['is_trial'] == 1) {
            $this->createTrialArrangeStudents();
        }elseif($ca_info['is_makeup'] == 1){
            $this->createMakeupArrangeStudents();
        }else{
            if($is_class_ca && $refresh){
                $this->createClassArrangeStudents($refresh);
            }
        }



        $w_cas['ca_id'] = $ca_id;

        $cas_list = get_table_list('course_arrange_student',$w_cas);

        if(!$cas_list){
            $cas_list = [];
        }

        //判断是否有试听或补课
        $has_makeup = 0;
        $has_trial  = 0;
        $student_fields = ['sid','student_name','nick_name','sex','photo_url','status','money'];
        $customer_fields = ['cu_id','name','nick_name','sex'];

        $sid_maps = [];

        foreach($cas_list as $k=>$cas){
            if($cas['sid'] == 0 && $cas['cu_id'] == 0){
                //删除无效记录
                db('course_arrange_student')->where('cas_id',$cas['cas_id'])->delete();
                unset($cas_list[$k]);
                continue;
            }
            if($cas['sid'] > 0 && isset($sid_maps[$cas['sid']])){
                //删除重复学员
                db('course_arrange_student')->where('cas_id',$cas['cas_id'])->delete();
                unset($cas_list[$k]);
                continue;
            }
            if($cas['is_makeup'] == 1){
                $has_makeup++;
            }
            if($cas['is_trial'] == 1) {
                $has_trial++;
            }
            if($cas['sid'] > 0){
                $sid_maps[$cas['sid']] = $cas;
            }

        }

        if(!$has_trial && $is_class_ca && $refresh){
            $trial_cas_list = $this->refreshTrialStudents();
            if(!empty($trial_cas_list)){
                foreach($trial_cas_list as $cas){
                    array_push($cas_list,$cas);
                }
            }
        }

        if(!$has_makeup && $is_class_ca && $refresh){
            $makeup_cas_list = $this->refreshMakeupStudents();
            if(!empty($makeup_cas_list)){
                foreach($makeup_cas_list as $cas){
                    array_push($cas_list,$cas);
                }
            }
        }

        foreach($cas_list as $k=>$cas){
            $student  = [];
            $customer = [];
            $attendance = null;
            $leave = null;

            if($cas['sid'] > 0){
                $s_info = get_student_info($cas['sid']);
                array_copy($student,$s_info,$student_fields);
                if($get_sl) {
                    $student['student_lesson'] = StudentLesson::GetStudentLessonByCa($cas['sid'], $ca_info);
                }
            }
            if($cas['cu_id'] > 0){
                $cu_info = get_customer_info($cas['cu_id']);
                array_copy($customer,$cu_info,$customer_fields);
            }
            if($cas['satt_id'] > 0){
                $attendance = get_satt_info($cas['satt_id']);
                $attendance['in_time'] = date('Y-m-d H:i',$attendance['in_time']);
            }
            if($cas['is_leave'] == 1){
                $w_slv['ca_id'] = $ca_id;
                $w_slv['sid']   = $cas['sid'];
                $leave = get_slv_info($w_slv);
                if($leave){
                    if(empty($leave['reason'])){
                        $leave['reason'] = get_did_value($leave['leave_type']);
                    }
                    $cas_list[$k]['remark'] = $leave['reason'];

                }
                $cas_list[$k]['is_in']  = 0;
            }

            $cas_list[$k]['student']  = $student;
            $cas_list[$k]['customer'] = $customer;
            $cas_list[$k]['attendance'] = $attendance;
            $cas_list[$k]['leave'] = $leave;
        }

        return $cas_list;
    }

    /**
     * 更新试听学员列表
     */
    public function refreshTrialStudents($ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return false;
        }
        $cas_list = [];
        $w_tla['ca_id'] = $ca_id;

        $tla_list = get_table_list('trial_listen_arrange',$w_tla);

        if(!$tla_list){
            return $cas_list;
        }

        $cas_fields = ['og_id','ca_id', 'lid', 'cid','sj_id','sg_id','int_day', 'int_start_hour', 'int_end_hour'];

        $this->startTrans();
        try {
            foreach ($tla_list as $tla) {
                $w_ex_cas = [];
                $w_ex_cas['ca_id'] = $tla['ca_id'];

                $cas = [];
                $cas['is_trial'] = 1;

                if ($tla['is_student']) {
                    $cas['sid'] = $tla['sid'];
                    $w_ex_cas['sid'] = $tla['sid'];
                } else {
                    $cas['cu_id'] = $tla['cu_id'];
                    $w_ex_cas['cu_id'] = $tla['cu_id'];
                }
                array_copy($cas, $ca_info, $cas_fields);

                $ex_cas = get_cas_info($w_ex_cas,false);
                if ($ex_cas) {
                    array_push($cas_list,$ex_cas);
                    continue;       //如果已经存在排课学员，跳过
                }

                $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                if (!$result) {
                    $this->rollback();
                    return $this->sql_add_error('course_arrange_student');
                }
                $cas_id = $this->m_course_arrange_student->cas_id;

                $cas_info = get_cas_info($cas_id);

                array_push($cas_list,$cas_info);
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return $cas_list;
    }

    /**
     * @param $ca_id
     */
    public function refreshMakeupStudents($ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return false;
        }
        $cas_list = [];
        $w_ma['ca_id'] = $ca_id;

        $ma_list = get_table_list('makeup_arrange',$w_ma);

        if(!$ma_list){
            return $ma_list;
        }

        $cas_fields = ['og_id','ca_id', 'lid', 'cid','sj_id','sg_id','int_day', 'int_start_hour', 'int_end_hour'];

        $this->startTrans();
        try {
            foreach ($ma_list as $ma) {
                $w_ex_cas = [];
                $w_ex_cas['ca_id'] = $ma['ca_id'];

                $cas = [];
                $cas['is_makeup'] = 1;
                $cas['sid'] = $ma['sid'];
                array_copy($cas, $ca_info, $cas_fields);

                $ex_cas = get_cas_info($w_ex_cas,false);
                if ($ex_cas) {
                    array_push($cas_list,$ex_cas);
                    continue;       //如果已经存在排课学员，跳过
                }

                $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                if (!$result) {
                    $this->rollback();
                    return $this->sql_add_error('course_arrange_student');
                }
                $cas_id = $this->m_course_arrange_student->cas_id;

                $cas_info = get_cas_info($cas_id);

                array_push($cas_list,$cas_info);
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return $cas_list;
    }

    /**
     * 创建补课学员记录
     * @return bool
     * @throws Exception
     */
    protected function createMakeupArrangeStudents(){
        $ca_info = $this->getData();
        $ca_id   = $ca_info['ca_id'];
        $w_cas['ca_id'] = $ca_id;
        $ex_cas = $this->m_course_arrange_student->where($w_cas)->find();

        //如果班级已经存在排课学生，就不创建了
        if($ex_cas){
            $now_int_day = int_day(time());
            if($now_int_day > $ca_info['int_day']){
                $w_cas['is_in'] = ['>',-1];
                $ex_cas = $this->m_course_arrange_student->where($w_cas)->find();

                if($ex_cas){
                    return true;
                }
            }else{
                return true;
            }
        }

        if($ca_info['is_attendance'] == 2){     //如果已经登记过考勤的，直接从Student_attendance表获取记录
            $w_satt = [];
            $w_satt['ca_id'] = $ca_id;
            $satt_list = get_table_list('student_attendance',$w_satt);
            if($satt_list){
                $cas_fields = ['og_id','sid','is_in','is_consume','ca_id', 'lid', 'cid','sj_id','sg_id','int_day', 'int_start_hour', 'int_end_hour'];
                $this->startTrans();
                try{
                    foreach($satt_list as $satt_info){
                        $w_ex_cas = [];
                        $w_ex_cas['ca_id'] = $satt_info['ca_id'];

                        $w_ex_cas['sid'] = $satt_info['sid'];


                        $ex_cas = get_cas_info($w_ex_cas,false);
                        if ($ex_cas) {
                            continue;       //如果已经存在排课学员，跳过
                        }

                        $cas = [];

                        array_copy($cas, $satt_info, $cas_fields);
                        $cas['is_attendance'] = 1;
                        $cas['is_makeup'] = 1;


                        $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                        if (!$result) {
                            $this->rollback();
                            return $this->sql_add_error('course_arrange_student');
                        }

                    }
                }catch(Exception $e){
                    $this->rollback();
                    return $this->exception_error($e);
                }

                $this->commit();
            }
        }else{
            $w_ma['ca_id'] = $ca_id;

            $ma_list = get_table_list('makeup_arrange',$w_ma);

            if($ma_list){
                $cas_fields = ['og_id','ca_id', 'lid', 'cid','sj_id','sg_id','int_day', 'int_start_hour', 'int_end_hour'];
                $ma_fields = ['sid','satt_id'];
                $this->startTrans();
                try{
                    foreach($ma_list as $ma){
                        $w_ex_cas = [];
                        $w_ex_cas['ca_id'] = $ma['ca_id'];

                        $w_ex_cas['sid'] = $ma['sid'];


                        $ex_cas = get_cas_info($w_ex_cas,false);
                        if ($ex_cas) {
                            continue;       //如果已经存在排课学员，跳过
                        }
                        $cas = [];
                        $cas['is_makeup'] = 1;
                        array_copy($cas, $ca_info, $cas_fields);
                        array_copy($cas,$ma,$ma_fields);

                        if($ma['satt_id'] > 0){
                            $satt_info = get_satt_info($ma['satt_id']);
                            if($satt_info) {
                                $cas['is_in'] = $satt_info['is_in'];
                                $cas['is_consume'] = $satt_info['is_consume'];
                            }
                            $cas['is_attendance'] = 1;
                        }



                        $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                        if (!$result) {
                            $this->rollback();
                            return $this->sql_add_error('course_arrange_student');
                        }


                    }
                }catch(Exception $e){
                    $this->rollback();
                    return $this->exception_error($e);
                }
                $this->commit();
            }
        }


        return true;
    }

    /**
     * 创建试听排课的学员记录
     * @return bool
     */
    protected function createTrialArrangeStudents(){
        $ca_info = $this->getData();
        $ca_id   = $ca_info['ca_id'];
        $w_cas['ca_id'] = $ca_id;
        $ex_cas = $this->m_course_arrange_student->where($w_cas)->find();

        //如果班级已经存在排课学生，就不创建了
        if($ex_cas){
            $now_int_day = int_day(time());
            if($now_int_day > $ca_info['int_day']){
                $w_cas['is_in'] = ['>',-1];
                $ex_cas = $this->m_course_arrange_student->where($w_cas)->find();

                if($ex_cas){
                    return true;
                }
            }else{
                return true;
            }
        }

        $w_tla['ca_id'] = $ca_id;

        $tla_list = get_table_list('trial_listen_arrange',$w_tla);

        if($tla_list){
            $cas_fields = ['og_id','ca_id', 'lid', 'cid','sj_id','sg_id','int_day', 'int_start_hour', 'int_end_hour'];
            $tla_fields = ['cu_id','sid'];
            $this->startTrans();
            try{
                foreach($tla_list as $tla){
                    $w_ex_cas = [];
                    $w_ex_cas['ca_id'] = $tla['ca_id'];
                    if($tla['is_student'] == 1){
                        $w_ex_cas['sid'] = $tla['sid'];
                    }else{
                        $w_ex_cas['cu_id'] = $tla['cu_id'];
                    }

                    $ex_cas = get_cas_info($w_ex_cas,false);
                    if ($ex_cas) {
                        continue;       //如果已经存在排课学员，跳过
                    }
                    $cas = [];
                    $cas['is_trial'] = 1;
                    array_copy($cas, $ca_info, $cas_fields);
                    array_copy($cas,$tla,$tla_fields);

                    if($tla['is_attendance'] == 1){
                        $cas['is_in'] = $tla['attendance_status'];
                        $cas['is_attendance'] = 1;

                        if($tla['sid'] > 0){
                            $w_satt['sid'] = $tla['sid'];
                            $w_satt['ca_id'] = $tla['ca_id'];

                            $satt_info = get_satt_info($w_satt);

                            if($satt_info){
                                $cas['satt_id'] = $satt_info['satt_id'];
                            }
                        }
                    }



                    $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                    if (!$result) {
                        $this->rollback();
                        return $this->sql_add_error('course_arrange_student');
                    }


                }
            }catch(Exception $e){
                $this->rollback();
                return $this->exception_error($e);
            }
            $this->commit();
        }
        return true;
    }

    /**
     * 创建班级排课的学员记录
     * @return [type] [description]
     */
    protected function createClassArrangeStudents($refresh = false){
        $ca_info = $this->getData();
        $ca_id   = $ca_info['ca_id'];
        if(!$refresh) {
            $mCas = new CourseArrangeStudent();
            $w_cas['ca_id'] = $ca_id;
            $ex_cas = $mCas->where($w_cas)->find();
            //如果班级已经存在排课学生，就不创建了
            if ($ex_cas) {
                $now_int_day = int_day(time());
                if ($now_int_day > $ca_info['int_day']) {
                    $w_cas['is_in'] = ['>', -1];
                    $ex_cas = $mCas->where($w_cas)->find();

                    if ($ex_cas) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
            return true;
        }
        $course_arrange_int_day = $ca_info['int_day'];
        $now_int_day = int_day(time());


        $course_arrange_time = strtotime(int_day_to_date_str($course_arrange_int_day).' 23:59:59');

        $w_cs['cid']     = $ca_info['cid'];
        $w_cs['is_end']  = 0;
        $w_cs['status']  = ['LT',2];
        $w_cs['in_time'] = ['LT',$course_arrange_time];


        $cs_list = get_table_list('class_student',$w_cs);




        if($cs_list){
            $cas_fields = ['og_id','bid','ca_id', 'lid', 'cid','sj_id','sg_id','grade','int_day', 'int_start_hour', 'int_end_hour',
                'consume_source_type','consume_lesson_amount'];
            $satt_fields = ['sid','satt_id','is_in','is_leave','is_makeup','is_consume'];
            $day_fields = ['int_day','int_start_hour','int_end_hour'];
            //获得当天的请假记录
            $slv_map = $this->get_student_leave_map($ca_id);

            $this->startTrans();
            try {
                foreach ($cs_list as $cs) {
                    //先判断是否已经考过勤的
                    if($now_int_day > $course_arrange_int_day){
                        $w_satt = [];
                        $w_satt['sid'] = $cs['sid'];
                        array_copy($w_satt,$ca_info,$day_fields);

                        $satt_info = get_satt_info($w_satt,false);


                        if($satt_info){
                            $cas = [];
                            array_copy($cas,$ca_info,$cas_fields);
                            array_copy($cas,$satt_info,$satt_fields);

                            $cas['is_attendance'] = 1;

                            $w_cas_ex = [];
                            $w_cas_ex['sid'] = $cs['sid'];
                            array_copy($w_cas_ex,$cas,$day_fields);

                            $mCas = new CourseArrangeStudent();

                            $ex_cas = $mCas->where($w_cas_ex)->find();
                            if($ex_cas){
                                $update_cas = [];
                                $update_cas['is_attendance'] = 1;
                                array_copy($update_cas,$cas,$satt_fields);
                                $w_cas_update['cas_id'] = $ex_cas['cas_id'];

                                $result = $mCas->save($update_cas,$w_cas_update);
                                if (false === $result) {
                                    $this->rollback();
                                    return $this->sql_save_error('course_arrange_student');
                                }
                            }else{
                                $result = $mCas->data([])->isUpdate(false)->save($cas);
                                if (!$result) {
                                    $this->rollback();
                                    return $this->sql_add_error('course_arrange_student');
                                }
                            }
                            continue;
                        }
                    }
                    /*
                    if ($cs['out_time'] > 0 && $cs['out_time'] > $cs['in_time'] && $cs['out_time'] < $course_arrange_time) {      //排除掉在排课日期退班的学员

                        continue;
                    }
                    */
                    $cas = [];
                    array_copy($cas, $ca_info, $cas_fields);

                    $cas['sid'] = $cs['sid'];

                    $w_ex_cas = [];
                    $w_ex_cas['ca_id'] = $cas['ca_id'];
                    $w_ex_cas['sid'] = $cs['sid'];

                    $ex_cas = get_cas_info($w_ex_cas,false,false);
                    if ($ex_cas) {

                        continue;       //如果已经存在排课学员，跳过
                    }


                    if (isset($slv_map[$cs['sid']])) {
                        $cas['is_leave'] = 1;
                        $cas['remark'] = $slv_map[$cs['sid']]['reason'];
                    }

                   
                    $mCas = new CourseArrangeStudent();

                    $result = $mCas->save($cas);
                   
                    if (!$result) {
                        $this->rollback();
                        return $this->sql_add_error('course_arrange_student');
                    }
                }
            }catch(\Exception $e){

                $this->rollback();
                return $this->exception_error($e);
            }
            $this->commit();
        }

        return true;
    }

    /**
     * @param $ca_id
     */
    protected function get_student_leave_map($ca_id){
        $w_slv = [];
        $w_slv['ca_id'] = $ca_id;

        $slv_list = get_table_list('student_leave',$w_slv);

        $slv_map = [];

        if($slv_list) {
            foreach ($slv_list as $slv) {
                $slv_map[$slv['sid']] = $slv;
            }
        }
        return $slv_map;
    }

    /**
     * 批量创建班级的排课记录
     * @param  [type] $cids            [班级ID，多个]
     * @param  [type] $exclude_holiday [是否排除节假日]
     * @return [type]                  [description]
     */
    public function batAutoCreateClassArrange($cids,$exclude_holiday = 1){
        if(is_numeric($cids)){
            $cids = [$cids];
        }

        $this->startTrans();
        try{
            foreach($cids as $cid){
                $result = $this->autoCreateClassArrange($cid,$exclude_holiday);
                if(!$result){
                    $this->rollback();
                    return false;
                }
                Classes::UpdateArrangeTimes($cid);
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;

    }

    /**
     * 创建单个班级的排课记录
     * @param  [type] $cid             [description]
     * @param  [type] $exclude_holiday [description]
     * @return [type]                  [description]
     */
    public function autoCreateClassArrange($cid,$exclude_holiday = 1){
        //获得班级信息
        $w['cid'] = $cid;
        $mClasses = new Classes();
        $class = $mClasses->where($w)->cache(1)->find();

        if(!$class){
            return $this->input_param_error('cid',1);
        }

        //获得节假日数据
        $holiday_list = [];

        /***计算时间相差天数*end***/

        $schedules = $class->getArrangeSchedules();
        

        if(empty($schedules)) {
            return $this->user_error($class->class_name.'还没有设置排课计划');
        }

        $start_arrange_time = $class->getData('start_lesson_time');

        if($start_arrange_time == 0){
            return $this->user_error($class->class_name.'没有设置开课日期');
        }

        //获得班级已经排课的列表
        $w_cca['cid'] = $class->cid;
        $w_cca['is_cancel'] = 0;
        $class_course_arranges = get_table_list('course_arrange',$w_cca);

        if(!$class_course_arranges){
            $class_arrange_times = 0;
        }else{
            $class_arrange_times   = count($class_course_arranges);
        }

        $class_need_arrange_times = $class->lesson_times - $class_arrange_times;

        if($class_need_arrange_times <= 0){
            return $this->user_error($class->class_name.'的所有课次已经排课完毕!请更新班级上课次数或删除已经排过的课');
        }

        //开始排课日期
        $max_auto_arrange_days = 400 ;                      //最大自动排课天数
        $end_arrange_time   = strtotime("+$max_auto_arrange_days days",$start_arrange_time);

        if($exclude_holiday){
            $mHoliday = new Holiday();
            $holiday_list = $mHoliday->getHolidays($class->bid,$start_arrange_time,$end_arrange_time);
        }

        //当前排课的时间戳
        $current_arrange_time = $start_arrange_time;
        //已排课次数
        $arranged_times       = 0 ;

        //周排课计划map
        $week_day_schedule_map = [];

        foreach($schedules as $schedule){
            $week_day = $schedule['week_day'];
            if(!isset($week_day_schedule_map[$week_day])){
                $week_day_schedule_map[$week_day] = [];
            }
            array_push($week_day_schedule_map[$week_day],[
                'int_start_hour'    => format_int_hour($schedule['int_start_hour']),
                'int_end_hour'      => format_int_hour($schedule['int_end_hour']),
                'eid'               => $schedule['eid'],
                'cr_id'             => $schedule['cr_id'],
                'consume_lesson_hour'   => $schedule['consume_lesson_hour'],
                'second_eids' => $schedule['second_eids']
            ]);
        }
        $this->startTrans();



        try{

            while($current_arrange_time < $end_arrange_time){

                $current_week_day  = date('N',$current_arrange_time);       //当前星期几
                $current_week_nums = date('W',$current_arrange_time);       //当前第几周
                $current_int_day   = date('Ymd',$current_arrange_time);     //当前日期天

                if(in_array($current_int_day,$holiday_list)){               //如果在节假日就跳过
                    $current_arrange_time += 86400;
                    continue;
                }

                if(!isset($week_day_schedule_map[$current_week_day])){      //如果当天日期不在排课计划日期里面就跳过
                    $current_arrange_time += 86400;
                    continue;
                }

                $current_schedules = $week_day_schedule_map[$current_week_day];

                foreach($current_schedules as $cs){
                    if($arranged_times >= $class_need_arrange_times){
                        break;
                    }
                    $result = $this->add_class_course_arrange_by_schedule($current_int_day,$cid,$cs);
                    if(false === $result){
                        $this->rollback();
                        return false;

                    }
                    if($result > 0){
                        $arranged_times++;
                    }
                    
                }

                $current_arrange_time += 86400;
            }

            $class->arrange_times = $arranged_times + $class_arrange_times;
            $result = $class->save();
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('classes');
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        //更新班级排课次数
        
        return true;

    }

    /**
     * 根据排课计划添加一次班级排课
     * @param [type] $cid      [description]
     * @param [type] $schedule [description]
     */
    protected function add_class_course_arrange_by_schedule($int_day,$cid,$schedule){
        $data = [];
        $data['int_day'] = $int_day;
        $w['cid'] = $cid;
        $class = $this->m_classes->where($w)->cache(1)->find();
        $class_data = $class->toArray();

        array_copy($data,$schedule,['int_start_hour','int_end_hour','consume_lesson_hour']);

        $w_ex_ca['cid'] = $cid;

        array_copy($w_ex_ca,$data,['int_start_hour','int_end_hour','int_day']);

        $ex_ca = $this->m_course_arrange->where($w_ex_ca)->find();
       
        if($ex_ca){

            return 0;
        }

        array_copy($data,$class_data,['teach_eid','second_eid','cr_id','og_id','bid','cid','lid','sj_id']);
        if($schedule['eid'] && $schedule['eid'] != $class->teach_eid){
            $data['teach_eid'] = $schedule['eid'];
        }
        if($schedule['cr_id'] && $schedule['cr_id'] != $class->cr_id){
            $data['cr_id'] = $schedule['cr_id'];
        }
        if(isset($schedule['second_eids']) && $schedule['second_eids']){
            $data['second_eids'] = $schedule['second_eids'];
        }

        $result = $this->canArrangeCourse($data);
        if (false === $result){
            return false;
        }

        $result = $this->data([])->isUpdate(false)->save($data);

        if(!$result){
            return $this->sql_add_error('course_arrange');
        }

        $result = $this->ca_id;
        
        return $result;
    }

    /**
     * 获得单课时分钟数
     * @param  array  $ca_info [description]
     * @return [type]          [description]
     */
    public function getPerLessonHourMinutes($ca_info = []){
        if(empty($ca_info)){
            $ca_info = $this->getData();
        }

        $per_lesson_hour_minutes = 0;

        try{
            if($ca_info['cid'] > 0){//班级
                $w_class['cid'] = $ca_info['cid'];
                $m_class = $this->m_classes->where($w_class)->cache(1)->find();
                if($m_class->per_lesson_hour_minutes > 0){
                    $per_lesson_hour_minutes = $m_class->per_lesson_hour_minutes;
                }
            }
            if($per_lesson_hour_minutes == 0 && $ca_info['lid'] > 0){//课程
                $w_lesson['lid'] = $ca_info['lid'];
                $m_lesson = $this->m_lesson->where($w_lesson)->cache(1)->find();
                if($m_lesson->per_lesson_hour_minutes){
                    $per_lesson_hour_minutes = $m_lesson->per_lesson_hour_minutes;
                }
            }

            if($per_lesson_hour_minutes == 0 && $ca_info['sg_id'] > 0){//科目级别
                $w_sg['sg_id'] = $ca_info['sg_id'];
                $m_subject_grade = $this->m_subject_grade->where($w_sg)->cache(1)->find();
                if($m_subject_grade->per_lesson_hour_minutes){
                    $per_lesson_hour_minutes = $m_subject_grade->per_lesson_hour_minutes;
                }
            }

            if($per_lesson_hour_minutes == 0){
                $w_sj['sj_id'] = $ca_info['sj_id'];
                $m_subject = $this->m_subject->where($w_sj)->cache(1)->find();
                if($m_subject->per_lesson_hour_minutes){
                    $per_lesson_hour_minutes = $m_subject->per_lesson_hour_minutes;
                }
            }
            
        }catch(Exception $e){
            $per_lesson_hour_minutes = 0;
        }

        if($per_lesson_hour_minutes == 0){
            $per_lesson_hour_minutes = user_config('params.per_lesson_hour_minutes');
        }

        return $per_lesson_hour_minutes;
    }

    /**
     * 获得消耗课时数量
     * @param  [type] $ca_info [description]
     * @return [type]          [description]
     */
    public function getConsumeLessonHour($ca_info = []){
        if(empty($ca_info)){
            $ca_info = $this->getData();
        }

        $ignore_time_long_clh = user_config('params.ignore_time_long_clh');

        
        if($ignore_time_long_clh){
            $lesson_hours = 1.00;
            if($ca_info['lid'] > 0){
                $lesson_info = get_lesson_info($ca_info['lid']);
                if($lesson_info['unit_lesson_hours'] > 0){
                    $lesson_hours = $lesson_info['unit_lesson_hours'];
                }
            }

        }else{
            $per_lesson_hour_minutes = $this->getPerLessonHourMinutes($ca_info);
            $lesson_hours = cacu_lesson_hours($ca_info['int_start_hour'],$ca_info['int_end_hour'],$per_lesson_hour_minutes);
        }

        return $lesson_hours;
    }

    /**
     * 按照排课记录登记考勤
     * @param  [type] &$input [description]
     * @return [type]         [description]
     */
    public function regAttendance(&$input){
        /*
        1,数据校验
        2，创建class_attendance
        3，批量处理student_attendance
        4，更新class_attendance
         */
        $need_fields = ['students','teach_eid','consume_lesson_hour','is_push'];

        if(!$this->checkInputParam($input,$need_fields)){

            return false;
        }

        if(!isset($input['lesson_remark'])){
            $input['lesson_remark'] = '';
        }
        $students = $input['students'];
        // 将考过勤（试听学员已登记试听）的学员过滤掉
        foreach ($students as $k => $per_student) {
            $course_arrange_student = CourseArrangeStudent::get($per_student['cas_id']);
            if($course_arrange_student->is_in == 1){
                unset($students[$k]);
            }
        }
        
        $params = [];

        array_copy($params,$input,['teach_eid','consume_lesson_hour','lesson_remark','is_push','consume_source_type','consume_lesson_amount']);

        if(isset($input['att_way'])){
            $params['att_way'] = $input['att_way'];
        }else{
            $params['att_way'] = 0;
        }


        return $this->attendance($students,$params);
    }



    /**
     * 依据排课登记一个学员考勤记录
     * @param  [type] $sid [description]
     * @param  [int] $att_way 1:刷卡，2:点名,3:自由登记,4:刷脸
     * @return [type]      [description]
     */
    public function regOneStudentAtt($sid,$att_way = 1){
        $_REQUEST['is_push'] = $_POST['is_push'] = 1;
        $params['att_way']   = $att_way;       //刷卡考勤
        $params['is_push']   = 1;              //推送消息

        $students = [];

        array_push($students,[
            'sid'               =>$sid,
            'is_in'             =>1,
            'is_leave'          =>false,
            'attendance_status' =>1,
            'is_consume'        => 1,
        ]);

        return $this->attendance($students,$params); 
    }

    /**
     * 获得考勤记录模型实例
     * @param $params
     * @param int $ca_id
     * @return bool
     */
    public function getCatt($params,$ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return false;
        }
        $ca_id = $ca_info['ca_id'];
        $w_catt = [];
        $w_catt['ca_id'] = $ca_id;

        $mClassAttendance = new ClassAttendance();

        $m_catt = $mClassAttendance->where($w_catt)->find();

        /*
         * 2018-11-30 注释掉，当一个老师在同一个时间段有多个排课的时候，会导致登记的学员考勤记录班级不准确, 柏金瀚遇到
        if(!$m_catt){

            $w_catt = [];

            if(isset($params['teach_eid']) && $params['teach_eid'] != $ca_info['teach_eid']){
                $w_catt['eid'] = $params['teach_eid'];
            }else{
                $w_catt['eid'] = $ca_info['teach_eid'];
            }
            array_copy($w_catt,$ca_info,['int_day','int_start_hour','int_end_hour']);

            $m_catt = $mClassAttendance->where($w_catt)->find();



            if($m_catt){

                $m_catt->ca_id = $ca_id;
            }
        }*/


        if(!$m_catt){
            $m_catt = $mClassAttendance->createCattByCa($ca_info,$params);
            if(!$m_catt){
                exception($mClassAttendance->getError());
            }
        }else{
            //update lesson_remark
            if(isset($params['lesson_remark']) && !empty($params['lesson_remark']) && $params['lesson_remark'] != $m_catt->lesson_remark){
                $m_catt->lesson_remark = safe_str($params['lesson_remark']);
                $m_catt->save();
            }
        }

        return $m_catt;

    }


    /**
     * 通用考勤
     * @param  [type] $students [description]
     * @param  [type] $prams    [description]
     * @param  array  $ca_info  [description]
     * @return [type]           [description]
     */
    public function attendance($students,$params,$ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return false;
        }

        //先循环检查一遍是否有选择出勤状态
        $has_in_status = 0;
        foreach($students as $s){
            if($s['is_in'] != -1){
                $has_in_status++;
                break;
            }
        }
        if($ca_info['is_trial'] == 0 && $has_in_status == 0){
            return $this->user_error('您还未选择任何学员的出勤状态，请选择至少一名学员的出勤状态登记考勤!');
        }

        //限制登记考勤的日期
        if(!$this->checkRegParamsPermit($ca_id)){
            return false;
        }
        $ca_id = $ca_info['ca_id'];

        $ret = true;

        $this->startTrans();

        try
        {
            $m_catt = $this->getCatt($params);

            //循环处理正常考勤学员
            $catt_info = $m_catt->getData();

            $do_map = [];
            $do_cache_key = '';

            $satt_id = 0;
            foreach($students as $s){
                if($s['is_in'] == -1){
                    continue;
                }
                if(isset($s['sid']) && $s['sid'] > 0) {
                    $do_cache_key = 's-'.$s['sid'];
                }elseif(isset($s['cu_id']) && $s['cu_id'] > 0){
                    $do_cache_key = 'cu-'.$s['cu_id'];
                }
                if (isset($do_map[$do_cache_key])) {
                    continue;
                }
                $mCourseArrangeStudent = new CourseArrangeStudent();
                //处理cas记录
                if(!isset($s['cas_id'])){
                    $m_cas = $mCourseArrangeStudent->getBySidAndCaId($s['sid'],$ca_id);
                }else{
                    $m_cas = $mCourseArrangeStudent->where('cas_id',$s['cas_id'])->find();
                }
                if(!$m_cas){
                    exception('输入参数有误:students');
                }

                $satt_id = $m_cas->updateAttStatus($s,$catt_info,$params);
                if(!$satt_id){
                    exception($m_cas->getError());
                }

                $do_map[$do_cache_key] = 1;
            }

            //处理完学员考勤登记以后更新班级考勤记录
            $result = $m_catt->updateCattCountFields();

            if(!$result){
                exceptiion($m_catt->getError());
            }



            //创建教师课耗
            $result = $m_catt->createEmployeeLessonHours();

            if(!$result){
                exception($m_catt->getError());
            }

            //更新排课状态
            if($m_catt->need_nums <= $m_catt->in_nums + $m_catt->absence_nums){
                $update_ca['is_attendance'] = 2;
            }elseif($m_catt->in_nums + $m_catt->absence_nums > 0){
                $update_ca['is_attendance'] = 1;
            }else{
                $update_ca['is_attendance'] = 0;
            }

            $w_ca_update['ca_id'] = $ca_id;

            $result = $this->save($update_ca,$w_ca_update);

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('course_arrange');
            }

            if(isset($params['att_way']) && $params['att_way'] == 1){
                $ret = $satt_id;
            }
            
            // 添加一条班级考勤日志
            if($ca_info['cid']>0){
                ClassLog::addClassAttendance($ca_info);
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return $ret;

    }

    /**
     * 获得指定学生的课时情况
     * @param  [type] $sid [description]
     * @return [type]      [description]
     */
    public function getLessonInfo($sid){
        $ca_info = $this->getData();
        $lesson_type = $ca_info['lesson_type'];
        $sj_id = $ca_info['sj_id'];
        $w_sj['sj_id'] = $sj_id;
        $sj_info = $this->m_subject->where($w_sj)->find();
        $ret['subject'] = $sj_info['subject_name'];
        if($lesson_type == 0){
            $w_c['cid'] = $ca_info['cid'];
            $m_class = $this->m_classes->where($w_c)->find();
            $ret['class_name'] = $m_class->class_name;
        }else{
            $ret['class_name'] = '';
        }

        $student_lesson_hour = $this->m_student->getStudentLessonBySjId($sj_id,$sid);

        $ret = array_merge($ret,$student_lesson_hour);

        return $ret;
    }

    /**
     * 反选登记某日考勤记录
     * @param $input
     * @return bool
     */
    public function reversalRegAtt($input){
        $need_fields = ['int_day','absences'];
        if(!$this->checkInputParam($input,$need_fields)){
            return false;
        }
        $now_int_hour = int_hour(time());
        $now_int_day  = int_day(time());
        $int_day  = format_int_day($input['int_day']);
        $absences = $input['absences'];

        if(!$absences || !is_array($absences)){
            $absences = [];
        }

        $w_ca['int_day'] = $int_day;

        $ca_list = $this->where($w_ca)->order('int_start_hour ASC')->select();

        if(!$ca_list){
            $err_msg = sprintf("%s当天没有排课，无法批量登记考勤!",$int_day);
            return $this->sendError($err_msg);
        }

        $this->startTrans();

        try {
            foreach ($ca_list as $ca) {

                if($ca['int_day'] == $now_int_day && $ca['int_start_hour'] > $now_int_hour){
                    continue;
                }
                $result = $ca->reversalAttendance($absences);
                if(!$result){
                    $this->rollback();
                    return $this->user_error($ca->getError());
                }
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return true;
    }

    /**
     * 反选登记当堂考勤
     * @param array $absences
     * @return bool
     */
    public function reversalAttendance($absences = [],$ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return false;
        }
        $ca_id = $ca_info['ca_id'];

        $input = $this->prepareRegAttsData($ca_info,$absences);

        return $this->regAttendance($input);
    }

    /**
     * 获取反选考勤预备数据
     * @param $ca_info
     * @param array $absences
     */
    protected function prepareRegAttsData($ca_info,$absences = []){
        $ret = [
            'teach_eid' =>$ca_info['teach_eid'],
            'consume_lesson_hour'=>$ca_info['consume_lesson_hour'],
            'lesson_remark' => '',
            'is_push' => false,
            'students'=>[]
        ];
        $students = $this->getAttObjects($ca_info['ca_id'],true);

        $absences_map = [];

        foreach($absences as $abs){
            $absences_map[$abs['sid']] = $abs;
        }

        foreach($students as $k=>$s){
            if(isset($absences_map[$s['sid']])){
                $am = $absences_map[$s['sid']];
                $students[$k]['is_in'] = 0;
                $students[$k]['is_leave'] = $am['is_leave'];
                $students[$k]['is_consume'] = isset($am['is_consume'])?$am['is_consume']:0;
            }else{
                $students[$k]['is_in'] = 1;
                $students[$k]['is_consume'] = 1;
                $students[$k]['is_leave'] = 0;
            }
        }

        $ret['students'] = $students;

        return $ret;

    }

    /**
     * 刷新学员排课记录
     * @param $sid
     */
    public function refreshStudentArrange($sid){
        $w_cs['sid'] = $sid;
        $w_cs['status'] = 1;
        $w_cs['is_end'] = 0;

        $m_cs = new ClassStudent();

        $cs_list = get_table_list('class_student',$w_cs);

        if(empty($cs_list)){
            return true;
        }

        foreach($cs_list as $cs){
            $this->refreshStudentAtClassArrange($sid,$cs['cid'],$cs['in_time']);
        }

        return true;
    }

    /**
     * 刷新学员在班级的排课记录
     * @param $sid
     * @param $cid
     * @param $in_time
     * @return bool
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    public function refreshStudentAtClassArrange($sid,$cid,$in_time){
        $w_ca['cid'] = $cid;
        $int_day = int_day($in_time);
        $w_ca['int_day'] = ['EGT',$int_day];


        $ca_list = get_table_list('course_arrange',$w_ca);


        if(empty($ca_list)){
            return true;
        }
        $w_cas['sid'] = $sid;
        $w_cas['cid'] = $cid;

        //删除在入班之前的排课学员记录

        $all_cas_list = get_table_list('course_arrange_student',$w_cas);
        $ca_id_map = [];
        $cas_list  = [];

        foreach($all_cas_list as $k=>$cas){
            if(isset($ca_id_map[$cas['ca_id']])){
                db('course_arrange_student')->where('cas_id',$cas['cas_id'])->delete();
                continue;
            }
            if($cas['is_in'] == -1 && $cas['int_day'] < $int_day ){
                db('course_arrange_student')->where('cas_id',$cas['cas_id'])->delete();
                continue;
            }
            $ca_id_map[$cas['ca_id']] = $cas;
            array_push($cas_list,$cas);
        }

        $ca_count  = count($ca_list);
        $cas_count = count($cas_list);
        if($ca_count <= $cas_count){
            return true;
        }


        $cas_fields = ['og_id','ca_id','cid','lid','sj_id','sg_id','int_day','int_start_hour','int_end_hour'];
        $m_cas = new CourseArrangeStudent();
        foreach($ca_list as $ca){
            if(isset($ca_id_map[$ca['ca_id']])){
                continue;
            }
            $new_cas = [];
            $new_cas['sid'] = $sid;
            array_copy($new_cas,$ca,$cas_fields);
            $m_cas->data([])->isUpdate(false)->save($new_cas);
        }
        return true;

    }

    /**
     * 清楚学员在班级的排课记录,出班时调用
     * @param $sid
     * @param $cid
     * @param int $out_time
     */
    public function clearStudentAtClassArrange($sid,$cid){
        $w_cas['sid'] = $sid;
        $w_cas['cid'] = $cid;
        $w_cas['is_in'] = -1;

        db('course_arrange_student')->where($w_cas)->delete();

        return true;

    }

    //取消排课
    public function cancelCourse($post)
    {
        if(empty($post['ca_id'])) return $this->user_error('ca_id错误');

        $course = $this->find($post['ca_id']);
        if($course['is_attendance'] != self::IS_ATTENDANCE_NO) return $this->user_error('排课已经考勤');

        //如果排课有请假记录，同时删除请假记录
        $m_sl = new StudentLeave;
        $student_leave = $m_sl->where('ca_id',$post['ca_id'])->find();
        if(!empty($student_leave)){
            // return $this->user_error('排课有关联的请假记录，请先删除请假记录');
            $res = $this->deleteStudentLeave($post['ca_id']);
            if($res !== true){
                return $this->user_error('请假记录删除失败');
            }
        }

        $post['is_cancel'] = 1;
        $this->startTrans();
        try {
            $rs = $course->allowField('is_cancel,reason')->isUpdate(true)->save($post);
            if ($rs === false) {
                $this->rollback();
                return $this->user_error($course->getError());
            }

            $w_cas_update['ca_id'] = $post['ca_id'];
            $update_cas['is_cancel'] = 1;

            $m_cas = new CourseArrangeStudent();
            $result = $m_cas->save($update_cas,$w_cas_update);
            
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('course_arrange_student');
            }

    

            if(isset($post['create_sa']) && intval($post['create_sa']) == 1) {
                $m_sa = new StudentAbsence();
                $sids = $m_cas->where('ca_id', $post['ca_id'])->column('sid');
                $sids = array_unique($sids);
                $student_absence_data = $course->toArray();
                foreach ($sids as $per_sid) {
                    $student_absence_data['sid'] = $per_sid;
                    $student_absence_data['absence_type'] = StudentAbsence::ABSENCE_TYPE_CANCEL_COURSE;
                    $student_absence_data['eid'] = $student_absence_data['teach_eid'] ?$student_absence_data['teach_eid']: 0;
                    $student_absence_data['remark'] = '取消排课:' . $post['reason'];
                    $rs = $m_sa->addOneAbsence($student_absence_data);
                    if ($rs === false) throw new FailResult($m_sa->getErrorMsg());
                }
            }

            if ($course['cid'] > 0) {
                $w_csd['cid'] = $course['cid'];
                $csd_list = get_table_list('class_schedule',$w_csd);
                if($csd_list) {
                    $result = $this->autoCreateClassArrange($course['cid']);
                    if (!$result) {
                        $this->rollback();
                        return false;
                    }
                }else{
                    $m_class = new Classes();
                    $rs = $m_class->updateArrange($course['cid']);
                    if ($rs === false){
                        $this->rollback();
                        return $this->user_error($m_class->getError());
                    }
                }
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 更新教师ID
     * @param $class_info
     * @param $teach_eid
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function UpdateTeachEidOfClass($class_info, $teach_eid)
    {
        if($teach_eid <= 0 || empty($class_info)) return true;
        $cid = $class_info['cid'];

        $self = new self();
        $where = [
            'is_attendance' => self::IS_ATTENDANCE_NO,
            'cid'           => $cid,
        ];
        $course_list = $self->where($where)->order('int_day asc')->select();
        $msg = [];
        /** @var CourseArrange $course */
        foreach($course_list as $course) {
            //如果排课老师不是班级的老师，可能已经调过老师，则不更换
            if($course['teach_eid'] == $teach_eid || $course['teach_eid'] != $class_info['teach_eid']) continue;

            $condition_field = ['int_day', 'int_start_hour', 'int_end_hour', 'teach_eid', 'cr_id', 'cid'];
            array_copy($new_ca_info, $course, $condition_field);
            $rs = $self->canArrangeCourse($new_ca_info, $course['ca_id']);
            if($rs !== true) {
                $msg[] = $rs;
                continue;
            }

            $course->teach_eid = $teach_eid;
            $rs = $course->allowfield('teach_eid')->save();
            if($rs === false) {
                $msg[] = sprintf('%s号的排课更换老师失败，原因：%s', $course['int_day'], $course->getError());
            }

        }

        return empty($msg) ? true : $msg;
    }


    /**
     * 更新科目ID
     * @param $class_info
     * @param $sj_id
     * @return bool|false|int
     */
    public static function UpdateUnattendanceSjid($class_info,$sj_id)
    {

        if($sj_id <=0 || empty($class_info)) return true;
        $cid = $class_info['cid'];

        $self = new self();
        $where = [
            'cid'           => $cid,
        ];

        $update['sj_id'] = $sj_id;

        $result = $self->save($update,$where);

        return $result;
    }


    public static function UpdateLidOfClass($class_info, $lid)
    {

        $cid = $class_info['cid'];

        $self = new self();
        $where = [
            'cid'           => $cid,
        ];
        $update['lid'] = $lid;

        $result = $self->save($update,$where);

        return $result;
    }


    public function checkRegParamsPermit($ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return true;
        }
        $now_int_day = int_day(time());

        if($ca_info['int_day'] < $now_int_day){
            $catt_params = user_config('params.class_attendance');
            if($catt_params['allow_reg_history'] === 0 ){
                return $this->user_error('系统设置为不允许登记历史考勤!');
            }
            if($catt_params['allow_reg_history'] === 1){
                if($catt_params['reg_history_pass_days'] > 0){
                    $days = int_day_diff($ca_info['int_day'],$now_int_day);
                    if($days > $catt_params['reg_history_pass_days']){
                        $msg = sprintf('系统设置为允许考勤的天数为%s天,当前考勤已过%s天',$catt_params['reg_history_pass_days'],$days);
                        return $this->user_error($msg);
                    }
                }elseif($catt_params['reg_history_pass_months'] > 0){
                    $months = int_month_diff($ca_info['int_day'],$now_int_day);
                    if($months >= $catt_params['reg_history_pass_months']){
                        $msg = sprintf('系统设置为允许考勤的月数为%s个月内,当前考勤已过%s个月',$catt_params['reg_history_pass_months'],$months);
                        return $this->user_error($msg);
                    }
                }
            }
        }
        return true;
    }

    /**
     * 获得学期阶段
     * @param int $ca_id
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTermPeriod($ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return true;
        }

        $period = '';
        $lesson_type = $ca_info['lesson_type'];

        if($ca_info['is_trial'] == 1 || $ca_info['is_makeup'] == 1){
            return '';
        }

        if($lesson_type == 0){
            //班课
            $class_info = get_class_info($ca_info['cid']);
            if(!$class_info){
                return $period;
            }

            $lesson_times = $class_info['lesson_times'];

            $w_ca['cid'] = $ca_info['cid'];
            $w_ca['is_cancel'] = 0;

            $ca_list = $this->where($w_ca)->order('int_day ASC,int_start_hour ASC')->select();

            $cur_index = 1;
            $index = 0;
            foreach($ca_list as $ca){
                $index++;
                if($ca['ca_id'] == $ca_info['ca_id']){
                    $cur_index = $index;
                    break;
                }
            }

            if($cur_index == 1){
                $period = 'first';
            }elseif($cur_index == $lesson_times){
                $period = 'last';
            }else{
                if($lesson_times % 2 == 0){
                    $middle_index = $lesson_times / 2;
                }else{
                    $middle_index = round($lesson_times / 2);
                }
                if($middle_index == $cur_index){
                    $period = 'middle';
                }
            }
        }

        return $period;

    }

    /**
     * 获得排课关联服务列表
     * @param int $ca_id
     */
    public function getServices($ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return true;
        }
        $now_time = time();
        $now_int_hour = int_hour($now_time);
        $classroom_period = 'in';       //课堂时期
        if($now_int_hour < $ca_info['int_start_hour']){
            $classroom_period = 'before';
        }elseif($now_int_hour > $ca_info['int_start_hour']){
            $classroom_period = 'after';
        }
        //学期时期
        $term_period = $this->getTermPeriod($ca_id);

        $ret['period'] = [
            'classroom' => $classroom_period,
            'term'      => $term_period
        ];

        $service_config = user_config('service_standard');

        $services = [];

        foreach($service_config as $st=>$configs){
            foreach($configs as $p=>$list){
                if(!empty($list)){
                    foreach($list as $k=>$l){
                        $item = array_merge($l,[
                           'type'=>$st,
                           'period' => $p
                        ]);
                        $this->check_service_item($item,$ca_id);
                        array_push($services,$item);
                    }
                }
            }
        }

        $ret['services'] = $services;

        return $ret;

    }

    protected function check_service_item(&$item,$ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return true;
        }
        $item['status'] = 0;
        $ca_id = $ca_info['ca_id'];
        $w['ca_id'] = $ca_id;

        $ws = [];
        $ws['int_day'] = $ca_info['int_day'];
        $cas_list = get_table_list('course_arrange_student',$w);
        if($cas_list){
            $s_ids = [];
            foreach($cas_list as $cas){
                $s_ids[] = $cas['sid'];
            }
            $ws['sid'] = ['IN',$s_ids];
        }
        if($item['system'] == 1){
            switch($item['name']){
                case 'remind':
                    $crl_list = model('course_remind_log')->where($w)->select();
                    if($crl_list){
                        $item['status'] = 1;
                        $item['business_data'] = $crl_list;
                    }
                    break;
                case 'prepare':
                    $cp_list = model('course_prepare')->where($w)->select();
                    if($cp_list){
                        $item['status'] = 1;
                        $item['business_data'] = $cp_list;
                    }
                    break;
                case 'school_arrive':
                    if(isset($ws['sid'])) {
                        $sasl_list = model('student_attend_school_log')->where($ws)->select();
                        if($sasl_list){
                            $item['status'] = 1;
                            $item['business_data'] = $sasl_list;
                        }
                    }
                    break;
                case 'attendance':
                    $catt = model('class_attendance')->where($w)->find();
                    if($catt){
                        $item['status'] = 1;
                        $item['business_data'] = $catt;
                    }
                    break;
                case 'review':
                    $review = model('review')->where($w)->find();
                    if($review){
                        $item['status'] = 1;
                        $item['business_data'] = $review;
                    }
                    break;
                case 'homework':
                    $hw = model('homework_task')->where($w)->find();
                    if($hw){
                        $item['status'] = 1;
                        $item['business_data'] = $hw;
                    }
                    break;
                case 'return_visit':
                    if(isset($ws['sid'])){
                        unset($ws['int_day']);
                        $srv_list = model('student_return_visit')->where($ws)->select();
                        if($srv_list){
                            $item['status'] = 1;
                            $item['business_data'] = $srv_list;
                        }
                    }
                    break;
                default:
                    break;
            }
        }else{
            $w_sr['ca_id'] = $ca_id;
            $w_sr['st_did'] = $this->_get_service_did($item);
            $sr_info = model('service_record')->where($w_sr)->find();
            if($sr_info){
                $item['status'] = 1;
                $item['business_data'] = $sr_info;
                $item['files'] = $sr_info->service_record_file();
            }else{
                $item['files'] = [];
            }
        }
        return $item;
    }

    private function _get_service_did($item){
        if(!isset($item['name']) || strpos($item['name'],'did_') === false){
            return 0;
        }
        $arr = explode('_',$item['name']);
        return intval($arr[1]);
    }

    /**
     * 登记服务
     * @param $input
     * @param int $ca_id
     */
    public function regService($input,$ca_id = 0){

    }

    /**
     * @desc  课前提醒
     * @url   /api/lessons/:id/
     * @method POST
     */
    public function remind_course($data)
    {
        $ret = [];

        $m_sl = new StudentLeave();
        foreach($data as $row) {
            //课前发送微信通知
            $course = CourseArrange::get($row['ca_id']);
            if(empty($course)) return $this->user_error(400, '课程不存在');

            foreach($row['sids'] as $per_sid) {
                $is_leave = $m_sl->where('sid', $per_sid)->where('ca_id', $row['ca_id'])->find();
                if(!empty($is_leave)) {
                    log_write('已经请假了，不推送课前提醒', 'error');
                    continue;
                }

                try {
                    $result = $course->wechat_tpl_notify($per_sid);
                    if(false === $result){
                        exception($course->getError());
                    }
                    $m_crl = new CourseRemindLog();
                    $result = $m_crl->addOneLog(['sid' => $per_sid, 'ca_id' => $course['ca_id']]);
                    if(false === $result){
                        exception($m_crl->getError());
                    }
                    add_service_record('course_arrange_remind', ['sid' => $per_sid, 'st_did' => 222]);
                } catch (\Exception $e) {
                    $sinfo = get_student_info($per_sid);
                    $tmp = [
                        'sid' => $per_sid,
                        'student' => $sinfo,
                        'msg' =>  '发送失败:'.$e->getMessage()
                    ];
                    array_push($ret, $tmp);
                }
            }
        }

        return $ret;
    }

    /**
     *  获取一个校区某天所有排课信息
     * @param $day
     * @param $bid
     * @return array
     */
    public function autoOneDayCourseArrange($day,$bid)
    {
        $w['int_day'] = int_day_add(int_day(time()),$day);
        $w['bid'] = $bid;
        $courses = $this->where($w)->select();
        $data = [];
        foreach ($courses as $k => $per_course) {
            $cas_list = $this->getAttObjects($per_course['ca_id'], true, false);
            $students = [];
            if (!empty($cas_list)) {
                foreach ($cas_list as $cas) {
                    if (!empty($cas['student'])) {
                        $students[] = $cas['sid'];
                    }
                }
            }
            $data[$k]['ca_id'] = $per_course['ca_id'];
            $data[$k]['sids'] = $students;
        }

        return $data;
    }

    /**
     *  获取一个校区某天所有排课信息
     * @param $day
     * @param $bid
     * @return array
     */
    public function autoOneDayCourseTeachers($day,$bid){
        $w['int_day'] = int_day_add(int_day(time()),$day);
        $w['bid'] = $bid;
        $course_list = $this->where($w)->select();
        return $course_list;
    }

    /**
     *  自动推送老师课前提醒
     * @param $data
     */
    public function AutoRemindTeacher($data){
        
        $mMessage = new Message();
        foreach ($data as $k => $row){
            try {
                $task_data['ca_id'] = $row['ca_id'];
                $task_data['subject'] = '老师课前提醒';
                $time = int_day_to_date_str($row['int_day']) . ' ' . int_hour_to_hour_str($row['int_start_hour']) . '-' . int_hour_to_hour_str($row['int_end_hour']);
                $lesson_name  = get_lesson_name($row['lid']);
                $task_data['content'] = '您有一节'.$lesson_name.'课于：'.$time.'开始,请提前准备';
                $employee_info = get_employee_info($row['teach_eid']);
                $task_data['uid'] = $employee_info['uid'];

                $rs = $mMessage->sendTplMsg('remind_teacher',$task_data ,[],2);
                if($rs === false) return $this->user_error($mMessage->getErrorMsg());

            } catch(\Exception $e) {
                log_write($e->getFile() . ' ' . $e->getLine() . ' '. $e->getMessage(), 'error');
            }
        }

        return true;
    }



}