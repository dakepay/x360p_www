<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/9/20
 * Time: 19:13
 */
namespace app\api\model;

class ClassLog extends Base
{
    /*
     * 事件类型，1：创建班级，2：编辑班级，
        3：学生加入班级，4：学生退出班级，
        5：班级状态status更改，6：排课操作，
        7：考勤操作,8:升班操作，
        9：结课操作,10:该班级学生停课，
        11：该班级学生复课
        12：该班级学生结课
    */
    const TYPE_STUDENT_STOP    = 10;//学生停课
    const TYPE_STUDENT_RECOVER = 11;//学生复课
    const TYPE_STUDENT_CLOSE   = 12;//学生结课

    const OP_CREATE_CLASS = 1; #创建班级
    const OP_EDIT_CLASS = 2; #编辑班级
    const OP_DELETE_CLASS = 3; #删除班级
    const OP_CLASS_STUDENT_INSERT = 4; #学员加入班级
    const OP_CLASS_STUDENT_DELETE = 5; #学员退出班级
    const OP_CLASS_CLOSE = 6; #班级结课
    const OP_CLASS_ARRANGE = 7; #班级排课
    const OP_DELETE_CLASS_ARRANGE = 8; #删除班级排课
    const OP_CLASS_UPGRADE = 9;  #班级升班
    const OP_CLASS_SERVICE = 10; #服务记录
    const OP_CLASS_SERVICE_TASK = 11; #服务安排
    const OP_CLASS_ATTENDANCE = 12; #班级考勤
    const OP_CLASS_IMPORT = 13; #导入班级

    const OP_ADD_CLASS_SCHEDULE = 14; #创建班级排课计划
    const OP_DELETE_CLASS_SCHEDULE = 15; #删除班级排课计划
    const OP_EDIT_CLASS_SCHEDULE = 16; #修改班级排课计划

    protected $type = [
        'content' => 'json',
    ];

    protected $hidden = [];

    public function cls()
    {
        return $this->belongsTo('Classes', 'cid', 'cid')->field(['cid', 'class_name']);
    }

    public function student()
    {
        return $this->belongsTo('Student', 'sid', 'sid')->field(['sid', 'student_name']);
    }

    public function createUser()
    {
        return $this->belongsTo('UserBase', 'create_uid', 'uid')->field(['uid', 'name']);
    }
    
    /**
     * 添加一条创建班级日志
     * @param Classes $class [description]
     */
    public static function addCreateClassLog(Classes $class)
    {
        $data = [];
        array_copy($data,$class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_CREATE_CLASS;

        $desc = config('format_string.class_insert');
        $temp['name'] = request()->user['name'];
        $temp['class'] = $class['class_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return ClassLog::create($data);
    }
    
    /**
     * 添加一天班级导入日志
     * @param [type] $cid [description]
     */
    public static function addImportClassLog($cid)
    {
        $class = get_class_info($cid);
        $data = [];
        array_copy($data,$class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_CLASS_IMPORT;

        $desc = config('format_string.class_import');
        $temp['name'] = request()->user['name'];
        $temp['class'] = $class['class_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return ClassLog::create($data);

    }
    
    /**
     * 添加一条编辑班级日志
     * @param Classes $class [description]
     */
    public static function addEditClassLog(array $class_data,array $content,Classes $class)
    {
        $data = [];
        array_copy($data,$class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_EDIT_CLASS;

        $desc = config('format_string.class_update');
        $temp['name'] = request()->user['name'];
        $new_class_name = $class_data['class_name'];
        $old_class_name = $class['class_name'];
        $temp['class'] = ($new_class_name == $old_class_name) ? $new_class_name : $old_class_name;
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);

        $data['content'] = $content;

        return ClassLog::create($data);
    }

    /**
     * 添加一条删除班级 操作记录
     * @param Classes $class [description]
     */
    public static function addDeleteClassLog(Classes $class)
    {
        $data = [];
        array_copy($data,$class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_DELETE_CLASS;

        $desc = config('format_string.class_delete');
        $temp['name'] = request()->user['name'];
        $temp['class'] = $class['class_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);

        return ClassLog::create($data);
    }
    
    /**
     * 添加一条班级加入学员记录
     * @param Classes $class [description]
     * @param [type]  $sid   [description]
     */
    public static function addClassStudentInsertLog(Classes $class,$sid)
    {
        $data = [];
        array($data,$class,['og_id','bid','cid']);
        $data['cid'] = $class['cid'];
        $data['event_type'] = ClassLog::OP_CLASS_STUDENT_INSERT;

        $desc = config('format_string.class_student_insert');
        $temp['name'] = request()->user['name'];
        $temp['student'] = get_student_name($sid);
        $temp['class'] = $class['class_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return ClassLog::create($data);
    }


    /**
     * 添加一条学员退班日志
     * @param array $class_student [description]
     */
    public static function addClassStudentDeleteLog($class_student)
    {
        $class = get_class_info($class_student['cid']);
        $sid = $class_student['sid'];
        $data = [];
        array_copy($data,$class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_CLASS_STUDENT_DELETE;

        $desc = config('format_string.class_student_delete');
        $temp['name'] = request()->user['name'];
        $temp['student'] = get_student_name($sid);
        $temp['class'] = $class['class_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return ClassLog::create($data);
    }

    /**
     * 添加一条班级结课的日志
     * @param Classes $cls
     */
    public static function addClassCloseLog(Classes $class)
    {
        $data = [];
        array_copy($data,$class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_CLASS_CLOSE;

        $desc = config('format_string.class_close');
        $temp['name'] = request()->user['name'];
        $temp['class'] = $class['class_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return ClassLog::create($data);
    }
    
    /**
     * 添加一条班级排课日志
     * @param Classes $class [description]
     */
    public static function addClassArrangeLog(Classes $class,array $input)
    {
        $data = [];
        array_copy($data,$class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_CLASS_ARRANGE;

        $desc = config('format_string.class_arrange');
        $temp['name'] = request()->user['name'];
        $temp['time'] = int_day_to_date_str($input['int_day']). ' '.$input['int_start_hour'].'-'.$input['int_end_hour'];
        $temp['class'] = $class['class_name'];
        $temp['cr_id'] = get_class_room($input['cr_id']);
        $temp['teach_eid'] = get_employee_name($input['teach_eid']);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return ClassLog::create($data);
    }
    
    /**
     * 添加一条删除班级排课日志
     * @param array $course [description]
     */
    public static function addDeleteClassArrangeLog(array $course)
    {
        $class = Classes::get($course['cid']);
        // array_copy($data,$class,['og_id','bid','cid']);
        $int_day = $course['int_day'];
        $int_start_hour = $course['int_start_hour'];
        $int_end_hour = $course['int_end_hour'];
        $data = [];
        $data['event_type'] = ClassLog::OP_DELETE_CLASS_ARRANGE;
        $data['cid'] = $course['cid'];
        $desc = config('format_string.delete_class_arrange');
        $temp['name'] = request()->user['name'];
        $temp['time'] = int_day_to_date_str($int_day). ' '.int_hour_to_hour_str($int_start_hour).'-'.int_hour_to_hour_str($int_end_hour);
        $temp['class'] = $class['class_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return ClassLog::create($data);
    }

    /**
     * 添加一条班级升班日志
     * @param Classes $new_class [description]
     * @param Classes $old_class [description]
     */
    public static function addClassUpgradeLog(Classes $new_class,Classes $old_class)
    {
        $data = [];
        array_copy($data,$old_class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_CLASS_UPGRADE;

        $desc = config('format_string.class_upgrade');
        $temp['name'] = request()->user['name'];
        $temp['old'] = $old_class['class_name'];
        $temp['new'] = $new_class['class_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return ClassLog::create($data);
    }


    /**
     * 添加一条班级服务日志
     * @param array $info [description]
     */
    public static function addClassServiceLog(array $info)
    {
        $class = get_class_info($info['cid']);
        $data = [];
        array_copy($data,$class,['og-id','bid','cid']);
        $data['event_type'] = ClassLog::OP_CLASS_SERVICE;

        $desc = config('format_string.class_service');
        $temp['name'] = get_employee_name($info['eid']);
        $temp['time'] = int_day_to_date_str($info['int_day']).' '.int_hour_to_hour_str($info['int_hour']);
        $temp['class'] = $class['class_name'];
        $temp['st_did'] = get_did_value($info['st_did']);
        $temp['content'] = $info['content'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return ClassLog::create($data);
    }

    /**
     * 添加一条 班级 服务安排记录
     * @param array $info [description]
     */
    public static function addClassServiceTaskLog(array $info)
    {
        $class = get_class_info($info['cid']);
        $data = [];
        array_copy($data,$class,['og-id','bid','cid']);
        $data['event_type'] = ClassLog::OP_CLASS_SERVICE_TASK;

        $desc = config('format_string.class_service_task');
        $temp['name'] = get_employee_name($info['own_eid']);
        $temp['time'] = int_day_to_date_str($info['int_day']).' '.int_hour_to_hour_str($info['int_hour']);
        $temp['class'] = $class['class_name'];
        $temp['st_did'] = get_did_value($info['st_did']);
        $temp['remark'] = $info['remark'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return ClassLog::create($data);
    }


    /**
     * 添加一条班级考勤日志
     * @param array $info [description]
     */
    public static function addClassAttendance(array $info)
    {
        $class = get_class_info($info['cid']);
        $data = [];
        array_copy($data,$class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_CLASS_ATTENDANCE;
        $desc = config('format_string.class_attendance');
        $temp['name'] = request()->user['name'];
        $temp['class'] = $class['class_name'];
        $temp['time'] = int_day_to_date_str($info['int_day']).' '.int_hour_to_hour_str($info['int_start_hour']).'-'.int_hour_to_hour_str($info['int_end_hour']);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        ClassLog::create($data);
    }
    
    /**
     * 添加一条班级排课计划日志
     * @param [type] $input [description]
     */
    public static function addClassScheduleLog($input,$cid)
    {
        $class = get_class_info($cid);
        $week_map = array(
            '1' => '星期一',
            '2' => '星期二',
            '3' => '星期三',
            '4' => '星期四',
            '5' => '星期五',
            '6' => '星期六',
            '7' => '星期日',
        );
        $data = [];
        array_copy($data,$class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_ADD_CLASS_SCHEDULE;
        $desc = config('format_string.add_class_schedule');
        $temp['name'] = request()->user['name'];
        $temp['class'] = $class['class_name'];
        $temp['week'] = $week_map[$input['week_day']];
        $temp['start'] = $input['int_start_hour'];
        $temp['end'] = $input['int_end_hour'];
        $temp['cr_id'] = get_class_room($input['cr_id']);
        $temp['eid'] = get_employee_name($input['eid']);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        ClassLog::create($data);
    }
    
    /**
     * 添加一条 删除班级排课计划日志
     * @param  [type] $input [description]
     * @param  [type] $cid   [description]
     * @return [type]        [description]
     */
    public static function deleteClassScheduleLog($input,$cid)
    {
        $class = get_class_info($cid);
        $week_map = array(
            '1' => '星期一',
            '2' => '星期二',
            '3' => '星期三',
            '4' => '星期四',
            '5' => '星期五',
            '6' => '星期六',
            '7' => '星期日',
        );
        $data = [];
        array_copy($data,$class,['og_id','bid','cid']);
        $data['event_type'] = ClassLog::OP_DELETE_CLASS_SCHEDULE;
        $desc = config('format_string.delete_class_schedule');
        $temp['name'] = request()->user['name'];
        $temp['class'] = $class['class_name'];
        $temp['week'] = $week_map[$input['week_day']];
        $temp['start'] = $input['int_start_hour'];
        $temp['end'] = $input['int_end_hour'];
        $temp['cr_id'] = get_class_room($input['cr_id']);
        $temp['eid'] = get_employee_name($input['eid']);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        ClassLog::create($data);
    }

    
    /**
     * 编辑班级排课计划
     * @param  [type] $input [description]
     * @param  [type] $cid   [description]
     * @return [type]        [description]
     */
    public static function editClassScheduleLog($input,$cid)
    {

    }

    


































    /**
     * 添加一条学生的停课日志
     * @param ClassStudent $classStudent
     * @param $info
     * @return $this
     */
    public static function addStudentStopLog(ClassStudent $classStudent, $info)
    {
        $data = [];
        $data['og_id'] = $classStudent['og_id'];
        $data['bid'] = $classStudent['bid'];
        $data['cid'] = $classStudent['cid'];
        $data['sid'] = $classStudent['sid'];
        $data['event_type'] = ClassLog::TYPE_STUDENT_STOP;
        $data['content'] = $info;

        $temp['name'] = request()->user['name'];
        $temp['student_name'] = $classStudent['Student']['student_name'];
        $temp['lesson_name']  = $classStudent['student_lesson']['lesson']['lesson_name']; //todo 异常
        $desc = config('format_string.class_student_stop');
        $data['desc']  = str_replace(array_keys($temp), $temp, $desc);
        return ClassLog::create($data);
    }
    /**
     * 添加一条学生的复课日志
     * @param ClassStudent $classStudent
     * @return $this
     */
    public static function addStudentRecoverLog(ClassStudent $classStudent)
    {
        $data = [];
        $data['og_id'] = $classStudent['og_id'];
        $data['bid'] = $classStudent['bid'];
        $data['cid'] = $classStudent['cid'];
        $data['sid'] = $classStudent['sid'];
        $data['event_type'] = ClassLog::TYPE_STUDENT_RECOVER;
        $data['content'] = [];

        $temp['name'] = request()->user['name'];
        $temp['student_name'] = $classStudent['Student']['student_name'];
        $temp['lesson_name']  = $classStudent['student_lesson']['lesson']['lesson_name']; //todo 异常
        $desc = config('format_string.class_student_recover');
        $data['desc'] = str_replace(array_keys($temp), $temp, $desc);
        return ClassLog::create($data);
    }

    

    public static function addCourseDeleteLog(CourseArrange $course_arrange)
    {
        $data['cid'] = $course_arrange['cid'];
        $data['event_type'] = 6;
        $data['og_id'] = $course_arrange['og_id'];
        $data['bid'] = $course_arrange['bid'];
        $data['content'] = $course_arrange->toArray();
        $data['desc'] = "删除" . int_day_to_date_str($course_arrange->getData('int_day')) . "排课";

        return ClassLog::create($data);
    }



}