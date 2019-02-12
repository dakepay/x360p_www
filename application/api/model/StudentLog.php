<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2018/1/2
 * Time: 11:42
 */

namespace app\api\model;

class StudentLog extends Base
{
    const OP_BACK_TO_CUSTOMER = 1; /*回流为客户*/
    const OP_TRANSFER_BRANCH = 2; /*转校区*/

    const OP_STUDENT_INSERT = 3; #添加学员
    const OP_STUDENT_IMPORT = 4; #导入学员
    const OP_STUDENT_DELETE = 5; #删除学员

    const OP_CLOSE = 10;/*结课*/
    const OP_STOP = 20;/*停课*/
    const OP_RECOVER = 21;/*复课*/
    const OP_SUSPENSION = 30;/*休学*/
    const OP_BACK = 31;/*复学*/
    const OP_QUIT = 90;/*退学*/
    const OP_ENROL = 91;/*入学*/

    const OP_LEAVE = 40; # 请假
    const OP_LEAVE_DELETE = 41; # 撤销请假
    const OP_TRANSFER_CLASS = 50; #转班
    const OP_STUDENT_EDIT = 51;  #编辑
    const OP_PAY_ORDER = 52; #缴费
    const OP_ORDER_REFUND = 53; #退费
    const OP_TRANSFER = 54;  #结转
    const OP_UNTRANSFER = 61;  #撤销结转
    const OP_TRANSHOURS = 55; #转让课时
    const OP_TRANSMONEYS = 58; #转让金额
    const OP_SERVICE = 56; # 服务记录
    const OP_SERVICE_TASK = 57; #服务安排
    const OP_ASSIGN_TEACHER = 59; # 分配班主任
    const OP_EDIT_STUDENT_AVATAR = 60; #编辑头像

    protected $type = [
        'extra_param' => 'json',
    ];

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function cls()
    {
        return $this->belongsTo('Classes', 'cid', 'cid')->field(['cid', 'class_name']);
    }

    public function createUser()
    {
        return $this->belongsTo('UserBase', 'create_uid', 'uid')->field(['uid', 'name']);
    }
    
    // 添加学员日志
    public static function addStudentInsertLog($sid)
    {
        $student = get_student_info($sid);
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_STUDENT_INSERT;
        $desc = config('format_string.student_insert');

        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];

        return StudentLog::create($data);
    }
    
    // 学员删除日志
    public static function addStudentDeleteLog($sid)
    {
        $student = get_student_info($sid);
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $desc = config('format_string.student_delete');
        $data['op_type'] = StudentLog::OP_STUDENT_DELETE;

        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];

        return StudentLog::create($data);
    }
    
    /**
     * 学员导入日志
     * @param [type] $sid [description]
     */
    public static function addStudentImportLog($sid)
    {
        $student = get_student_info($sid);
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $desc = config('format_string.student_import');
        $data['op_type'] = StudentLog::OP_STUDENT_IMPORT;

        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];

        return StudentLog::create($data);

    }


    public function addTransferBranchLog($sid,$to_bid)
    {
        $sinfo = get_student_info($sid);
        $data = [];
        $data['og_id'] = $sinfo['og_id'];
        $data['bid']   = $sinfo['bid'];
        $data['sid']   = $sid;
        $data['op_type'] = StudentLog::OP_TRANSFER_BRANCH;

        $format = config('format_string.student_transfer_branch');
        $log_data['from_branch_name'] = get_branch_name($sinfo['bid']);
        $log_data['to_branch_name']   = get_branch_name($to_bid);

        $data['desc'] = tpl_replace($format,$log_data);

        $param['from_bid'] = $sinfo['bid'];
        $param['to_bid']   = $to_bid;
        $param['sid']      = $sid;

        $data['extra_param'] = [];

        $this->startTrans();

        $result = $this->save($data);

        try {
            if (!$result) {
                $this->rollback();
                return $this->sql_add_error('student_log');
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 添加一条学生的停课日志
     * @param StudentLesson $studentLesson
     * @param array $info
     */
    public static function addStopLog(StudentLesson $studentLesson, array $info)
    {
        $data = [];
        $data['og_id'] = $studentLesson['og_id'];
        $data['bid']   = $studentLesson['bid'];
        $data['sid']   = $studentLesson['sid'];
        $data['op_type'] = StudentLog::OP_STOP;

        $desc = config('format_string.student_stop_lesson');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $studentLesson['student']['student_name'];
        $temp['lesson']  = $studentLesson['lesson']['lesson_name'];
        array_copy($temp,$info,['stop_time','recover_time','stop_remark']);
        $data['desc'] = str_replace(array_keys($temp), $temp, $desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }

    /**
     * 添加一条学生的复课操作日志
     * @param StudentLesson $studentLesson
     */
    public static function addRecoverLog(StudentLesson $studentLesson,String $recover_int_day)
    {
        $data = [];
        $data['og_id'] = $studentLesson['og_id'];
        $data['bid']   = $studentLesson['bid'];
        $data['sid']   = $studentLesson['sid'];
        $data['op_type'] = StudentLog::OP_RECOVER;

        $desc = config('format_string.student_recover_lesson');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $studentLesson['student']['student_name'];
        $temp['lesson']  = $studentLesson['lesson']['lesson_name'];
        $temp['time'] = date('Y-m-d',strtotime($recover_int_day));
        $data['desc'] = str_replace(array_keys($temp), $temp, $desc);

        $data['extra_param'] = [];
        return StudentLog::create($data);
    }

    /**
     * 添加一条学员休学日志
     * @param Student $student [description]
     */
    public static function addSuspendLog(array $extra_param,Student $student)
    {
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);

        $data['op_type'] = StudentLog::OP_SUSPENSION;
        $desc = config('format_string.student_suspend');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        array_copy($temp,$extra_param,['suspend_date','back_date','suspend_reason']);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);

        $data['extra_param'] = [];

        return StudentLog::create($data);
    }
    
    /**
     * 添加一条转让课时操作记录
     * @param array   $extra_param [description]
     * @param Student $student     [description]
     */
    public static function addTransHoursLog(array $extra_param,Student $student)
    {
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_TRANSHOURS;
        $desc = config('format_string.student_transhours');
        $temp['name'] = request()->user['name'];
        $temp['from_sid'] = get_student_name($extra_param['from_sid']);
        $temp['lesson_hours'] = $extra_param['lesson_hours'];
        $temp['lid'] = get_lesson_name($extra_param['lid']);
        $temp['to_sid'] = get_student_name($extra_param['to_sid']);
        $temp['remark'] = $extra_param['remark'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }

    /**
     * 添加一条转让金额的操作记录
     * @param array   $extra_param [description]
     * @param Student $student     [description]
     */
    public static function addTransMoneysLog(array $extra_param,Student $student)
    {
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_TRANSMONEYS;
        $desc = config('format_string.student_transmoneys');
        $temp['from_sid'] = get_student_name($extra_param['from_sid']);
        $temp['amount'] = $extra_param['amount'];
        $temp['to_sid'] = get_student_name($extra_param['to_sid']);
        $temp['remark'] = $extra_param['remark'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }
    
    /**
     * 添加一条请假日志
     * @param [type] $sid [description]
     */
    public static function addLeaveLog(array $extra_param,$sid,$ca_id)
    {
        $student = Student::get($sid);
        $ca_info = CourseArrange::get($ca_id);
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_LEAVE;
        $desc = config('format_string.student_leave');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $temp['time'] = int_day_to_date_str($ca_info['int_day']).' '.int_hour_to_hour_str($ca_info['int_start_hour']).'-'.int_hour_to_hour_str($ca_info['int_end_hour']);
        $temp['lid'] = get_lesson_name($ca_info['lid']);
        $temp['type'] = get_did_value($extra_param['leave_type']);
        $temp['reason'] = $extra_param['reason'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }


    public static function addStudentLeaveDeleteLog($ca_id,$sid)
    {
        $student = Student::get($sid);
        $ca_info = CourseArrange::get($ca_id);
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_LEAVE_DELETE;
        $desc = config('format_string.student_leave_delete');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $temp['time'] = int_day_to_date_str($ca_info['int_day']).' '.int_hour_to_hour_str($ca_info['int_start_hour']).'-'.int_hour_to_hour_str($ca_info['int_end_hour']);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);

        $data['extra_param'] = [];

        return StudentLog::create($data);
    }
    
    /**
     * 添加一条学员信息编辑日志
     * @param Stduent $student [description]
     */
    public static function addStudentEditLog(array $extra_param,$sid)
    {
        $student = Student::get($sid);
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_STUDENT_EDIT;
        $desc = config('format_string.student_edit');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = $extra_param;
        return StudentLog::create($data);
    }

    /**
     * 添加一条转班记录
     * @param [type] $sid      [description]
     * @param [type] $from_cid [description]
     * @param [type] $to_cid   [description]
     */
    public static function addTransferClassLog($sid,$from_cid,$to_cid)
    {
        $student = Student::get($sid);
        $data = [];
        array_copy($data,$student,['og_id','sid','bid']);
        $data['op_type'] = StudentLog::OP_TRANSFER_CLASS;
        $desc = config('format_string.student_transfer_class');
        $temp['name'] = request()->user['name'];
        $temp['from_cid'] = get_class_name($from_cid);
        $temp['to_cid'] = get_class_name($to_cid);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);

        $data['extra_param'] = [];

        return StudentLog::create($data);
    }
    
    /**
     * 添加一条订单缴费记录
     * @param [type] $oid [description]
     */
    public static function addPayOrderLog($oid)
    {
        $oinfo = get_order_info($oid);
        $student = Student::get($oinfo['sid']);
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_PAY_ORDER;
        $desc = config('format_string.student_pay_order');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $temp['order_no'] = $oinfo['order_no'];
        $temp['amount'] = $oinfo['order_amount'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);

    }

    /**
     * 添加一条学生的复学操作日志
     * @param Student $student
     * @param $extra_param
     */
    public static function addBackLog(Student $student)
    {
        $data = [];
        $data['og_id'] = $student['og_id'];
        $data['bid'] = $student['bid'];
        $data['sid'] = $student['sid'];
        $data['op_type'] = StudentLog::OP_BACK;

        $desc = config('format_string.student_back');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $data['desc'] = str_replace(array_keys($temp), $temp, $desc);

        $data['extra_param'] = [];
        return StudentLog::create($data);
    }

    /**
     * 添加一条学生的结课操作日志
     * @param StudentLesson $studentLesson
     */
    public static function addCloseLog(StudentLesson $studentLesson)
    {
        $data = [];
        $data['og_id'] = $studentLesson['og_id'];
        $data['bid']   = $studentLesson['bid'];
        $data['sid']   = $studentLesson['sid'];
        $data['sl_id'] = $studentLesson['sl_id'];
        $data['op_type'] = StudentLog::OP_CLOSE;

        $desc = config('format_string.student_close_lesson');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $studentLesson['student']['student_name'];
        $temp['lesson']  = $studentLesson['lesson']['lesson_name'];
        $data['desc'] = str_replace(array_keys($temp), $temp, $desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }

    /**
     * 添加一条学生的退学操作日志
     * @param Student $student
     */
    public static function addQuitLog(array $info,Student $student)
    {
        $data = [];
        $data['og_id'] = $student['og_id'];
        $data['bid']   = $student['bid'];
        $data['sid']   = $student['sid'];
        $data['op_type'] = StudentLog::OP_QUIT;

        $desc = config('format_string.student_quit');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $temp['reason'] = get_did_value($info['quit_reason']);
        $temp['remark'] = $info['remark'];
        $data['desc'] = str_replace(array_keys($temp), $temp, $desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }

    /**
     * 添加一条学生的入学操作日志
     * @param Student $student
     */
    public static function addEnrolLog(Student $student)
    {
        $data = [];
        $data['og_id'] = $student['og_id'];
        $data['bid']   = $student['bid'];
        $data['sid']   = $student['sid'];
        $data['op_type'] = StudentLog::OP_ENROL;

        $desc = config('format_string.student_enrol');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $data['desc'] = str_replace(array_keys($temp), $temp, $desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }

    /**
     * 添加一条退学学生的回流操作日志
     * @param Student $student
     */
    public static function addBackToCustomer(Student $student)
    {
        $data = [];
        $data['og_id'] = $student['og_id'];
        $data['bid']   = $student['bid'];
        $data['sid']   = $student['sid'];
        $data['op_type'] = StudentLog::OP_BACK_TO_CUSTOMER;

        $desc = config('format_string.student_back_to_customer');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $data['desc'] = str_replace(array_keys($temp), $temp, $desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }
    
    /**
     * 添加一条 学员退费操作记录
     * @param Student $student [description]
     */
    public static function addStuentRefundLog(Student $student,$amount,$time)
    {
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_ORDER_REFUND;
        $desc = config('format_string.student_order_refund');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $temp['time'] = date('Y-m-d',$time);
        $temp['amount'] = $amount;
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }
    
    /**
     * 添加一条学员 课程结转记录
     * @param Student $student [description]
     */
    public static function addStudentTransferLog(Student $student,$amount,$cut_amount,$op_type = 0)
    {
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = $op_type;
        $desc = config('format_string.student_transfer');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $temp['amount'] = $amount;
        $temp['cut'] = $cut_amount;
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }

    /**
     * 添加一条学员 服务操作记录
     * @param array $data [description]
     */
    public static function addServiceLog(array $extra_param)
    {
        $sid = $extra_param['sid'];
        $eid = $extra_param['eid'];
        $st_did = $extra_param['st_did'];
        $student = Student::get($sid);
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_SERVICE;
        $desc = config('format_string.student_service');
        $temp['name'] = get_employee_name($eid);
        $temp['time'] = int_day_to_date_str($extra_param['int_day']).' '.int_hour_to_hour_str($extra_param['int_hour']);
        $temp['student'] = $student['student_name'];
        $temp['st_did'] = get_did_value($st_did);
        $temp['content'] = $extra_param['content'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }

    public static function addServiceTaskLog(array $extra_param)
    {
        $sid = $extra_param['sid'];
        $eid = $extra_param['own_eid'];
        $st_did = $extra_param['st_did'];
        $student = Student::get($sid);
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_SERVICE_TASK;
        $desc = config('format_string.student_service_task');
        $temp['name'] = get_employee_name($eid);
        $temp['time'] = int_day_to_date_str($extra_param['int_day']).' '.int_hour_to_hour_str($extra_param['int_hour']);
        $temp['student'] = $student['student_name'];
        $temp['st_did'] = get_did_value($st_did);
        $temp['remark'] = $extra_param['remark'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }
    
    /**
     * 添加一条学员分配班主任日志
     * @param array $info [description]
     */
    public static function addAssignTeacherLog(array $info)
    {
        $data = [];
        $student = get_student_info($info['sid']);
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_ASSIGN_TEACHER;
        $desc = config('format_string.student_assign_teacher');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $temp['teacher'] = get_employee_name($info['eid']);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];
        return StudentLog::create($data);
    }
    
    /**
     * 添加一条编辑学员头像日志
     * @param Student $student [description]
     */
    public static function addEditStudentAvatarLog(Student $student)
    {
        $data = [];
        array_copy($data,$student,['og_id','bid','sid']);
        $data['op_type'] = StudentLog::OP_EDIT_STUDENT_AVATAR;
        $desc = config('format_string.student_edit_avatar');
        $temp['name'] = request()->user['name'];
        $temp['student'] = $student['student_name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['extra_param'] = [];
        StudentLog::create($data);
    }

}