<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/9
 * Time: 17:16
 */

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;
use think\Hook;

class StudentLeave extends Base
{
    public static $detail_fields = [
        ['type'=>'index','width'=>60,'align'=>'center'],
        ['title'=>'校区','key'=>'bid','align'=>'center'],
        ['title'=>'学员姓名','key'=>'sid','align'=>'center'],
        ['title'=>'上课时间','key'=>'time_section','align'=>'center','width'=>90],
        ['title'=>'请假时间','key'=>'create_time','align'=>'center','width'=>90],
        // ['title'=>'类型','key'=>'leave_type','align'=>'center'],
        ['title'=>'原因','key'=>'reason','align'=>'center'],
    ];

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $append = ['makeup'];


    public function getMakeupAttr($value,$data){
        if(!isset($data['ma_id'])){
            return null;
        }
        if($data['ma_id'] == 0){
            return null;
        }
        $ma_info  = get_ma_info($data['ma_id']);
        if(!$ma_info){
            return null;
        }
        if($ma_info['satt_id'] > 0){
            $satt_info = get_satt_info($ma_info['satt_id']);
            $ma_info['attendance'] = $satt_info;
        }

        return $ma_info;
    }

    public function student()
    {
        return $this->belongsTo('Student', 'sid', 'sid');
    }

    public function absence()
    {
        return $this->hasOne('StudentAbsence', 'slv_id', 'slv_id');
    }

    public function courseArrange()
    {
        return $this->belongsTo('CourseArrange', 'ca_id', 'ca_id');
    }

    /**
     * 根据排课学员详细创建请假记录
     * @param $cas
     */
    public function createByCas($cas){
        $w_slv = [];
        array_copy($w_slv,$cas,['sid','int_day','int_start_hour','int_end_hour']);

        $slv_info = get_slv_info($w_slv);

        if($slv_info){
           $update_slv = [];
           $update_slv['satt_id'] = $cas['satt_id'];

           $result = $this->m_student_leave->save($update_slv,['slv_id'=>$slv_info['slv_id']]);

           if(false === $result){
               return $this->sql_save_error('student_leave');
           }
        }else {
            $ca_info = get_ca_info($cas['ca_id']);
            $slv_info = [];
            array_copy($slv_info, $ca_info, ['bid', 'lesson_type', 'sj_id','grade', 'lid', 'cid', 'ca_id', 'int_day', 'int_start_hour', 'int_end_hour']);
            array_copy($slv_info, $cas, ['sid', 'satt_id']);
            $slv_info['reason'] = $cas['remark'];

            $result = $this->m_student_leave->data([])->isUpdate(false)->save($slv_info);

            if(!$result){
                return $this->sql_add_error('student_leave');
            }
            //请假扣积分
            $hook_data = [
                'hook_action' => 'leave',
                'sid' => $slv_info['sid'],
            ];
            Hook::listen('handle_credit', $hook_data);
        }

        return true;

    }

    public static function checkIsLeave(StudentAttendance $attendance)
    {
        $w = [];
        $w['sid'] = $attendance['sid'];
        if (!empty($attendance['ca_id'])) {/*排课考勤*/
            $w['ca_id'] = $attendance['ca_id'];
        } else {/*自由考勤*/
            $w['int_day']        = $attendance['int_day'];
            $w['int_start_hour'] = $attendance['int_start_hour'];
            $w['int_end_hour']   = $attendance['int_end_hour'];
        }
        return self::get($w);
    }
    
    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name,teach_eid');
    }

    public static function autoCreateLeave(StudentAttendance $attendance)
    {
        $model = self::checkIsLeave($attendance);
        if ($model) {
            return $model;
        }
        $model = new self();
        $table_field = self::getTableInfo()['fields'];
        $omit_field  = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];
        foreach ($table_field as $key => $value) {
            if (in_array($value, $omit_field)) {
                unset($table_field[$key]);
            }
        }
        $data = [];
        foreach ($table_field as $field) {
            if (isset($attendance[$field])) {
                $data[$field] = $attendance[$field];
            }
        }
        if (!empty($attendance['remark'])) {
            $data['reason'] = $attendance['remark'];

            //0:其他,1:病假,2:事假
            if ($data['reason'] == '病假') {
                $data['leave_type'] = 1;
            } elseif ($data['reason'] == '事假') {
                $data['leave_type'] = 2;
            } else {
                $data['leave_type'] = 0;
            }
        }
        $model->isUpdate(false)->allowField(true)->save($data);
        return $model;
    }

    /**
     * 批量请假
     * @param $ca_ids
     * @param $student
     * @param $data
     */
    public function addBatchLeave($ca_ids,$student,$data){
        $this->startTrans();
        try {

            foreach ($ca_ids as $ca_id) {
                $rs = $this->createOneLeave($ca_id, $student['sid'], $data);
                if (!$rs) throw new FailResult($this->getErrorMsg());
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        $this->commit();
        return true;
    }

    public function createOneLeave($ca_id,$sid,$data){

        $w_ex = [];
        $w_ex['ca_id'] = $ca_id;
        $w_ex['sid']   = $sid;
        $is_exist = self::get($w_ex);
        if ($is_exist) {
            return true;
        }
        $ca_info = get_ca_info($ca_id);
        if(!$ca_info){
            return $this->user_error('排课记录ID:'.$ca_id.',不存在!');
        }

        $leave_int_day = $ca_info['int_day'];
        $now_int_day   = int_day(time());

        $slv = [];
        $slv['sid']   = $sid;
        $slv['ca_id'] = $ca_id;

        array_copy($slv,$ca_info,['og_id','bid','lid','sj_id','grade','cid','lesson_type','int_day','int_start_hour','int_end_hour']);
        array_copy($slv,$data,['leave_type','reason']);

        $student_lesson = StudentLesson::GetStudentLessonByCa($sid,$ca_info);
        $slv['sl_id'] = $student_lesson['sl_id'];

        $this->startTrans();
        try {
            $mCas = new CourseArrangeStudent();

            //补请假
            $w_cas['sid'] = $sid;
            $w_cas['ca_id'] = $ca_id;

            $cas_info = get_cas_info($w_cas);
            $new_cas = [];

            if (!$cas_info) {
                //补学员排课记录
                $new_cas['sid'] = $sid;
                array_copy($new_cas, $ca_info, ['og_id', 'ca_id', 'lid', 'sj_id', 'grade','cid', 'sg_id', 'int_day', 'int_start_hour', 'int_end_hour']);
                $new_cas['is_in'] = 0;
                $new_cas['is_leave'] = 1;

                $result = $mCas->data([])->isUpdate(false)->save($new_cas);

                if (!$result) {
                    $this->rollback();
                    return $this->sql_add_error('course_arragne');
                }
                $cas_id = $mCas->cas_id;

                $cas_info = $new_cas;
                $cas_info['cas_id'] = $cas_id;
            }

            if ($cas_info && $cas_info['is_in'] == 1) {
                $student_info = get_student_info($sid);
                $msg = sprintf("学员%s在%s %s的考勤已经登记，并且是正常出勤状态，请先撤销学员当次考勤记录再进行补请假操作!",
                    int_day_to_date_str($cas_info['int_day']),
                    int_hour_to_hour_str($cas_info['int_start_hour']),
                    $student_info['student_name']
                );
                $this->rollback();
                return $this->user_error($msg);
            }

            if(empty($new_cas)){
                $update_cas = [];
                $update_cas['is_leave'] = 1;
                $update_cas['is_in']    = 0;
                $w_update_cas['cas_id'] = $cas_info['cas_id'];

                $result = $mCas->data([])->save($update_cas,$w_update_cas);
                if (!$result) {
                    $this->rollback();
                    return $this->sql_add_error('course_arrange_student');
                }
            }



            //写入请假记录
            $result = $this->data([])->allowField(true)->isUpdate(false)->save($slv);
            if(!$result){
                $this->rollback();
                return $this->sql_add_error('student_leave');
            }

            $hook_data = [
                'hook_action' => 'leave',
                'sid' => $sid,
            ];
            Hook::listen('handle_credit', $hook_data);

            if ($leave_int_day <= $now_int_day) {
                //判断排课记录是否有考勤
                $w_catt['ca_id'] = $ca_id;
                $catt_info = get_catt_info($w_catt);
                $catt_fields = ['og_id','bid','catt_id','ca_id','int_day','int_start_hour','int_end_hour',
                    'eid','second_eid','lid','lesson_type','cid','sj_id','sg_id'];
                if($catt_info){
                    //补考勤记录
                    $satt = [];
                    $satt['sid'] = $sid;

                    array_copy($satt,$catt_info,$catt_fields);

                    $satt['sl_id'] = $slv['sl_id'];
                    $satt['is_leave'] = 1;
                    $satt['is_in'] = 0;

                    $satt['remark'] = '补请假';

                    $result = $this->m_student_attendance->data([])->isUpdate(false)->save($satt);

                    if(!$result){
                        $this->rollback();
                        return $this->sql_add_error('student_attendance');
                    }

                    $satt_id = $this->m_student_attendance->satt_id;

                    //补缺勤记录
                    $sa_info = [];
                    $sa_info['sid'] = $sid;
                    array_copy($sa_info,$satt,$catt_fields);
                    array_copy($sa_info,$satt,['satt_id','is_leave','remark']);

                    $result = $this->m_student_absence->data([])->isUpdate(false)->save($sa_info);
                    if(!$result){
                        $this->rollback();
                        return $this->sql_add_error('student_absence');
                    }




                    $update_cas['satt_id'] = $satt_id;
                    $update_cas['is_attendance'] = 1;

                    $w_update_cas['cas_id'] = $cas_info['cas_id'];

                    $result = $this->m_course_arrange_student->data([])->save($update_cas,$w_update_cas);

                    if(false === $result){
                        $this->rollback();
                        return $this->sql_save_error('course_arragne_student');
                    }

                    array_copy($cas_info,$update_cas,['satt_id','is_attendance']);

                    $m_catt = new ClassAttendance($catt_info);

                    $m_catt->isUpdate(true)->updateCattCountFields();
                }
            }

            // 添加一条请假操作日志
            StudentLog::addLeaveLog($data,$sid,$ca_id);

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    public function countLeaveOfDay($day)
    {
        $day = format_int_day($day);
        $num = $this->scope('bid')->where('int_day', $day)->count();
        return $num;
    }


    public function delLeave(){
        $slv_info = $this->getData();

        $w_cas['sid'] = $slv_info['sid'];
        $w_cas['ca_id'] = $slv_info['ca_id'];

        $m_cas = new CourseArrangeStudent();
        $cas = $m_cas->where($w_cas)->find();

        $this->startTrans();
        try{

            if(!empty($cas)) {
                $cas->is_leave = 0;
                if($cas->is_attendance == 0){
                    $cas->is_in = -1;
                }

                $result = $cas->save();

                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('course_arrange_student');
                }
            }

           $result = $this->delete();

           if(false === $result){
               $this->rollback();
               return $this->sql_delete_error('student_leave');
           }

           // 添加一条 学员 请假撤销日志
           StudentLog::addStudentLeaveDeleteLog($slv_info['ca_id'],$slv_info['sid']);

        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

}