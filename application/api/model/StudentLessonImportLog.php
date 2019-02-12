<?php
/**
 * Author: luo
 * Time: 2018/5/19 14:49
 */

namespace app\api\model;


class StudentLessonImportLog extends Base
{

    //protected $skip_og_id_condition = true;

    protected $append = ['create_employee_name'];


    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function getSjIdsAttr($value)
    {
        return !empty($value) && is_string($value) ? explode(',', $value) : $value;
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name');
    }

    public function createEmployee()
    {
        return $this->hasOne('Employee', 'uid', 'create_uid')->field('eid,ename,uid');
    }

    public function addLog($data)
    {
        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return false;

        return true;
    }

    /**
     * 删除导入操作
     * @param int $slil_id
     * @return bool
     */
    public function delImportLog($slil_id = 0)
    {
        if($slil_id == 0){
            $slil_info = $this->getData();
        }else{
            $slil_info = get_row_info($slil_id,'student_lesson_import_log','slil_id',true);
        }

        if(empty($slil_info)){
            return $this->input_param_error('slil_id');
        }

        $this->startTrans();
        try{
            $mStudentLesson = new StudentLesson();
            $w_sl['sl_id']  = $slil_info['sl_id'];
            $m_sl = $mStudentLesson->where($w_sl)->find();
            if($m_sl){
                $result = $m_sl->reduceImportLessonHours($slil_info['lesson_hours'],$slil_info['unit_lesson_hour_amount']);
                if(!$result){
                    $this->rollback();
                    return $this->user_error($m_sl->getError());
                }
            }

            $result = $this->where('slil_id',$slil_info['slil_id'])->delete();

            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('student_lesson_import_log');
            }

            $mStudent = new Student();
            $mStudent->updateLessonHours($slil_info['sid']);

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

}