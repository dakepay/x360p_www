<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/10
 * Time: 12:04
 */

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class MakeupArrange extends Base
{

    const MAKEUP_TYPE_INSERT_COURSE = 0; # 跟班补课
    const MAKEUP_TYPE_NEW_COURSE = 1; # 开课补课

    protected $append = ['absence'];

    public function getAbsenceAttr($value,$data){
        if(!isset($data['sa_id']) && !isset($data['slv_id'])){
            return null;
        }
        $absence = ['leave'=>null,'absence'=>''];

        if($data['slv_id'] > 0){
            $slv_info = get_slv_info($data['slv_id']);
            $absence['leave'] = $slv_info;
        }
        if($data['sa_id'] > 0){
            $sa_info = get_sa_info($data['sa_id']);
            $absence['absence'] = $sa_info;
        }

        return $absence;
    }

    public function student()
    {
        return $this->belongsTo('Student', 'sid', 'sid');
    }

    public function studentLesson()
    {
        return $this->belongsTo('StudentLesson', 'sl_id', 'sl_id');
    }

    public function absence()
    {
        return $this->belongsTo('StudentAbsence', 'sa_id', 'sa_id');
    }

    public function leave()
    {
        return $this->belongsTo('StudentLeave', 'slv_id', 'slv_id');
    }

    public function courseArrange()
    {
        return $this->belongsTo('CourseArrange', 'ca_id', 'ca_id');
    }

    public function oneClass()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }

    public function attendance()
    {
        return $this->belongsTo('StudentAttendance', 'satt_id', 'satt_id');
    }

    /**
     * 根据排课ID删除补课记录
     * @param  [type] $ca_id [description]
     * @return [type]        [description]
     */
    public function deleteByCaId($ca_id)
    {
        $w_ma['ca_id'] = $ca_id;

        $ma_list = $this->where($w_ma)->select();

        if(!$ma_list) {
            return true;
        }

        $this->startTrans();
        foreach($ma_list as $ma) {
            if($ma['sa_id'] > 0) {
                $result = $this->m_student_absence->data([])->save(['status' => 0], ['sa_id' => $ma['sa_id']]);
                if(false === $result) {
                    $this->rollback();
                    return $this->sql_save_error('student_absence');
                }

                $result = $ma->delete();

                if(false === $result) {
                    $this->rollback();
                    return $this->sql_delete_error('makeup_arrange');
                }
            }
        }
        $this->commit();
        return true;
    }

    /**
     * @desc 查看补课记录是否存在
     * @param array $atd_info
     */
    public static function checkExistMakeupArrange(array $atd_info)
    {
        $w = [];
        $w['sid'] = $atd_info['sid'];
        if(!empty($atd_info['ca_id'])) {
            $w['ca_id'] = $atd_info['ca_id'];
        } else {
            $w['int_day'] = $atd_info['int_day'];
            $w['int_start_hour'] = $atd_info['int_start_hour'];
            $w['int_end_hour'] = $atd_info['int_end_hour'];
        }
        return self::get($w);
    }

    public function addMakeUpStudents(array $course, array $sa_ids = [], array $slv_ids = [])
    {
        if(empty($sa_ids) && empty($slv_ids)) return $this->user_error('缺少缺勤id或者请假id');

        $this->startTrans();
        try {
            $data = [];
            if(!empty($course['ca_id'])) {
                $data['makeup_type'] = 0;/*跟班补课*/
                /*根据已有排课添加补课记录*/
                $m_course = CourseArrange::get($course['ca_id']);
                if(empty($m_course)){
                    return $this->user_error('排课不存在');
                }
            } else {
                $data['makeup_type'] = 1;/*排班补课*/
                /*新添加一条排课，然后给新排课添加补课记录*/
                $m_course = new CourseArrange();
                $ca_id = $m_course->createOneCourse($course);
                if(!$ca_id) {
                    $this->rollback();
                    return $this->user_error($m_course->getError());
                } else {
                    $m_course = CourseArrange::get($ca_id);
                }
            }

            if(!empty($sa_ids)) {
                $rs = $this->addMakeUpStudentsFromAbsence($m_course, $sa_ids);
                if($rs === false){
	    	    $this->rollback();
                    return $this->user_error($this->getError());
                }
            }

            if(!empty($slv_ids)) {
                $rs = $this->addMakeUpStudentsFromLeave($m_course, $slv_ids);
                if($rs === false){
		    $this->rollback();
                    return $this->user_error($this->getError());
                }
            }

        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    //给缺勤学生增加补课
    public function addMakeUpStudentsFromAbsence($course, $sa_ids)
    {
        if(!($course instanceof CourseArrange)) {
            $course = CourseArrange::get($course['ca_id']);
        }
	$this->startTrans();
        try {
            
            $absence_list = StudentAbsence::all(['sa_id' => ['in', $sa_ids]]);
            $m_cas = new CourseArrangeStudent();
            foreach($absence_list as $absence) {
                if(empty($absence)) continue;
                if($course['lid'] > 0 && $absence['lid'] > 0 && $absence['lid'] !== $course['lid']) {
                    $this->rollback();
		            return $this->user_error('补课的课程和缺课的课程必须一致！');
                }
                if($absence['status']) {
		            $this->rollback();
                    return $this->user_error('该次缺勤已安排补课或已补课结束！');
                }

                $w = [];
                $w['sid'] = $absence['sid'];
                $w['ca_id'] = $course['ca_id'];
                if(self::get($w)) {
                    /*一个学生的多个补课记录不能安排到同一个排课*/
                    $msg = sprintf('该次排课已经存在一条学生：%s的补课记录,不能重复添加！', $absence['student']['student_name']);
                    $this->rollback();
		            return $this->user_error($msg);
                }

                $temp = $absence->toArray();
                $temp = array_merge($temp, $course->toArray());
                $temp['makeup_type'] = $temp['cid'] > 0 ? self::MAKEUP_TYPE_INSERT_COURSE : self::MAKEUP_TYPE_NEW_COURSE;
                $temp['catt_id'] = 0;
                $temp['satt_id'] = 0;
                $rs = $this->data([])->allowField(true)->isUpdate(false)->save($temp);
                if($rs === false){
		            $this->rollback();
                    return $this->sql_add_error('makeup_arrange');
                }

                $rs = $m_cas->addOneArrangeStudent($course, $absence['sid'], ['is_makeup' => 1]);
                if($rs === false){
	                $this->rollback();
                    return $this->user_error($m_cas->getError());
                }

                /*更新缺课记录的状态为：已安排补课*/
                $w_abs['sa_id'] = $absence['sa_id'];
                $update_abs = [
                    'status' => 1,
                    'ma_ca_id' =>  $course['ca_id']
                ];
                $result = $absence->save($update_abs,$w_abs);
                if (false === $result){
                    return $this->sql_save_error('student_absence');
                }
            }

        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    //请假补课
    public function addMakeUpStudentsFromLeave($course, array $slv_ids)
    {
        if(empty($slv_ids)) return $this->user_error('请假id不能为空');
        if(!($course instanceof CourseArrange)) {
            $course = CourseArrange::get($course['ca_id']);
        }

        try {
            $this->startTrans();
            $m_cas = new CourseArrangeStudent();
            $leave_list = StudentLeave::all(['slv_id' => ['in', $slv_ids]]);
            foreach($leave_list as $leave) {
                if(empty($leave)) continue;
                if($leave['lid'] > 0 && $leave['lid'] !== $course['lid']) {
                    throw new FailResult('补课的课程和缺课的课程必须一致！');
                }
                if($leave['ma_id'] > 0) {
                    throw new FailResult($leave['student']['student_name'] . '请假已安排补课');
                }

                $w = [];
                $w['sid'] = $leave['sid'];
                $w['ca_id'] = $course['ca_id'];
                if(self::get($w)) {
                    /*一个学生的多个补课记录不能安排到同一个排课*/
                    $msg = sprintf('该次排课已经存在一条学生：%s的补课记录,不能重复添加！', $leave['student']['student_name']);
                    throw new FailResult($msg);
                }
                //如果排课里面已经存在学生，就不允许再安排
                if($m_cas->where($w)->find()) {
                    $msg = sprintf('该次排课已经存在一条学生：%s的上课安排记录,不能重复添加！', $leave['student']['student_name']);
                    throw new FailResult($msg);
                }

                $temp = $leave->toArray();
                $temp = array_merge($temp, $course->toArray());
                $temp['makeup_type'] = $temp['cid'] > 0 ? self::MAKEUP_TYPE_INSERT_COURSE : self::MAKEUP_TYPE_NEW_COURSE;
                $temp['catt_id'] = 0;
                $temp['satt_id'] = 0;
                $rs = $this->data([])->allowField(true)->isUpdate(false)->save($temp);
                if($rs === false) return false;

                $rs = $m_cas->addOneArrangeStudent($course, $leave['sid'], ['is_makeup' => 1]);
                if($rs === false) throw new FailResult($m_cas->getErrorMsg());

                /*更新缺课记录的状态为：已安排补课*/
                $leave->data('ma_id', $this->ma_id)->save();
            }
            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //删除一条补课
    public function delOne()
    {
        $data = $this->getData();
        if(empty($data)) return $this->user_error('补课数据错误');

        if($data['catt_id'] > 0 || $data['satt_id'] > 0) return $this->user_error('已经考勤删除不了');

        try {
            $this->startTrans();
            $sa_id = $this->getData('sa_id');
            if($sa_id > 0) {
                $mStudentAbsence = new StudentAbsence();
                $rs = $mStudentAbsence->save(['status' => 0], ['sa_id' => $sa_id]);
                if($rs === false){
                    return $this->sql_save_error('student_absence');
                }
            }

            if($data['slv_id'] > 0) {
                $mStudentLeave = new StudentLeave();
                $rs = $mStudentLeave->where('slv_id', $data['slv_id'])->update(['ma_id' => 0]);
                if($rs === false) throw new FailResult('更新请假记录的补课安排失败');
            }

            if($data['ca_id'] > 0 && $data['sid'] > 0) {
                $course = CourseArrange::get($data['ca_id']);
                if(!empty($course)) {
                    $m_cas = new CourseArrangeStudent();
                    $rs = $m_cas->delOneRow($course, $data['sid']);
                    if($rs === false) throw new FailResult($m_cas->getError());
                }
            }

            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getError());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

}