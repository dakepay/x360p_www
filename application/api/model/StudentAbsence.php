<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/9
 * Time: 19:46
 */

namespace app\api\model;

use think\Exception;

class StudentAbsence extends Base
{
    const STATUS_UNARRANGE = 0;/*未安排*/
    const STATUS_ARRANGED  = 1;/*已安排*/
    const STATUS_CLOSE     = 2;/*已补课结束*/

    const ABSENCE_TYPE_ATTENDANCE = 1; # 考勤产生的缺勤
    const ABSENCE_TYPE_CANCEL_COURSE = 2; # 取消排课产生的缺勤

    protected $append = ['makeup'];

    public function getMakeupAttr($value,$data){
        if(!isset($data['status'])){
            return null;
        }
        if($data['status'] == 0){
            return null;
        }

        $w['sa_id'] = $data['sa_id'];
        $ma_info = get_ma_info($w);

        if(!$ma_info && $data['slv_id'] > 0){
            $w = [];
            $w['slv_id'] = $data['slv_id'];
            $ma_info = get_ma_info($w);
        }

        if(!$ma_info){
            return null;
        }

        return $ma_info;
    }

    public function courseArrange()
    {
        return $this->belongsTo('CourseArrange', 'ca_id', 'ca_id');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid');
    }

    public function studentLesson()
    {
        return $this->belongsTo('StudentLesson', 'sl_id', 'sl_id');
    }
    
    public function studentAttendance()
    {
        return $this->belongsTo('StudentAttendance', 'satt_id', 'satt_id');
    }

    public function makeup()
    {
        return $this->hasOne('MakeupArrange', 'sa_id', 'sa_id');
    }

    public static function autoCreateAbsence(StudentAttendance $attendance, $leave = null)
    {
        if ($leave && $leave instanceof StudentLeave) {
            $model = self::get(['slv_id' => $leave['slv_id']]);
        }
        if (empty($model)) {
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
            if (!empty($leave) && $leave instanceof StudentLeave) {
                $data['slv_id']   = $leave['slv_id'];
                $data['is_leave'] = 1;
            } else {
                $data['is_leave'] = 0;
                $data['slv_id']   = 0;
            }
            $model->allowField(true)->isUpdate(false)->save($data);
        }
        return $model;
    }

    public function addOneAbsence($data)
    {
        if(empty($data['sid']) || empty($data['ca_id'])) return $this->user_error('sid or ca_id error');

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return false;

        return true;
    }

    /**
     * 在补课记录未考勤的状态下，撤销缺勤记录对应的补课
     */
    public function cancelMakeup()
    {
        $status = $this->getData('status');
        if (empty($status)) {
            return $this->user_error('还没有安排补课,无法撤销!');
        }
        if ($status == 2) {
            return $this->user_error('已考勤，无法撤销!');
        }
        $w_ma['sa_id'] = $this->getData('sa_id');
        $ma_info = get_ma_info($w_ma);
        $ca_id = 0;
        if($ma_info){
            $ca_id = $ma_info['ca_id'];
        }

        $sid = $this->getData('sid');
        
        $this->startTrans();
        try {

            if($ca_id > 0) {
                $w_cas['sid']   = $sid;
                $w_cas['ca_id'] = $ca_id;
                $w_cas['is_makeup'] = 1;
                $cas_info = get_cas_info($w_cas);

                $m_cas = new CourseArrangeStudent();
                if ($cas_info) {
                    $w_cas_delete['cas_id'] = $cas_info['cas_id'];
                    $result = $m_cas->where($w_cas_delete)->delete(true);
                    if (false === $result) {
                        $this->rollback();
                        return $this->sql_delete_error('course_arragne_student');
                    }
                }

                $m_ma = new MakeupArrange();
                $w_ma_delete['ma_id'] = $ma_info['ma_id'];
                $result = $m_ma->where($w_ma_delete)->delete(true);
                if (false === $result) {
                    $this->rollback();
                    return $this->sql_delete_error('makeup_arrange');
                }
            }

            $result = $this->data('status', 0)->save();
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('student_absence');
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 修改补课状态
     * @param $sa_id
     * @param $data
     */
    public function updateStatus($sa_id,$data)
    {
        $w['sa_id'] = $sa_id;
        $student_absence = $this->where($w)->find();
        if (empty($student_absence)){
            return $this->user_error('缺勤记录不存在');
        }
        $result = $this->allowField(true)->save($data,$w);

        if (false === $result){
            return $this->sql_save_error('student_absence');
        }

        return true;
    }
}