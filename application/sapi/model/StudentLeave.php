<?php
/**
 * Author: luo
 * Time: 2017-12-20 09:58
**/

namespace app\sapi\model;

use think\Exception;

class StudentLeave extends Base
{
    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

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

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name,teach_eid');
    }

    public function createOneLeave(CourseArrange $course, Student $student, $data)
    {
        //$w = [];
        //$w['ca_id'] = $course['ca_id'];
        //$w['sid']   = $student['sid'];
        //$is_exist = self::get($w);
        //if ($is_exist) {
        //    return $this->user_error('这节课已经请过假');
        //}
        //
        //$course = $course->toArray();
        //$data = array_merge($course, $data);
        //$data['sid'] = $student['sid'];
        //
        //$w = [];
        //$w['lid'] = $course['lid'];
        //$w['sid'] = $student['sid'];
        //$data['sl_id'] = StudentLesson::get($w)['sl_id'] || 0;
        //
        //$rs = $this->data([])->allowField(true)->save($data);
        //return $rs;

        $w = [];
        $ca_id = $course['ca_id'];
        $w['ca_id'] = $ca_id;
        $w['sid']   = $student['sid'];
        $is_exist = self::get($w);
        if ($is_exist) {
            return $this->user_error('这节课已经请过假');
        }

        $course = $course->toArray();
        $data = array_merge($course, $data);
        $data['sid'] = $student['sid'];

        $w = [];
        $w['lid'] = $course['lid'];
        $w['sid'] = $student['sid'];
        $data['sl_id'] = StudentLesson::get($w)['sl_id'] || 0;
        $this->startTrans();
        try{
            $result = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if(!$result){
                $this->rollback();
                return $this->sql_add_error('student_leave');
            }

            $update_cas['is_leave'] = 1;
            $w_update_cas['ca_id'] = $ca_id;
            $w_update_cas['sid'] = $student['sid'];

            $result = $this->m_course_arrange_student->save($update_cas,$w_update_cas);

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('course_arrange_student');
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        $this->commit();

        return true;
    }


}