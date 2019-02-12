<?php
/**
 * Author: luo
 * Time: 2017-12-19 17:18
 **/

namespace app\sapi\model;

use app\api\model\ClassSchedule;
use app\common\exception\FailResult;
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

    const LESSON_TYPE_CLASS = 0;    # 班课
    const LESSON_TYPE_ONE2ONE = 1;  # 一对一
    const LESSON_TYPE_ONE2MANY = 2; # 一对多

    protected $attendance_fail_report = [];

    //protected $is_trial_type = ['yes' => 1, 'no' => 0]; //试听课程

    public function setIntDayAttr($value, $data)
    {
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
    public function studentLeave()
    {
        return $this->belongsTo('StudentLeave', 'ca_id', 'ca_id');
    }


    public function oneClass()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }

    public function students()
    {
        return $this->belongsToMany('Student', 'course_arrange_student', 'sid', 'ca_id');
    }

    public function teacher()
    {
        return $this->hasOne('Employee', 'eid', 'teach_eid');
    }

    public function coursePrepare()
    {
        return $this->belongsTo('CoursePrepare', 'ca_id', 'ca_id');
    }

    /**
     * @desc  家长申请排课
     * @author luo
     */
    public function addOneCourse($data)
    {
        $rule = [
            'sid' => 'require',
            'cid' => 'require',
            'int_day' => 'require',
            'int_start_hour' => 'require',
            'int_end_hour' => 'require',
        ];

        $validate = validate()->rule($rule);
        $rs = $validate->check($data);
        if($rs !== true) {
            return $this->user_error($validate->getError());
        }

        $class = Classes::get($data['cid']);
        if($class['class_type'] == Classes::CLASS_TYPE_NORMAL ) return $this->user_error('选择的班级是标准班级，无法约课');

        $data['int_day'] = format_int_day($data['int_day']);
        $data['int_start_hour'] = format_int_day($data['int_start_hour']);
        $data['int_end_hour'] = format_int_day($data['int_end_hour']);

        $class_schedule = ClassSchedule::get(
            ['cid' => $data['cid'], 'int_start_hour' => $data['int_start_hour'], 'int_end_hour' => $data['int_end_hour']]
        );

        if(empty($class_schedule)) return $this->user_error('班级没有这个时间段的排课计划');

        if(!empty($class->getData('end_lesson_time'))) {
            if(strtotime($data['int_day']) > $class->getData('end_lesson_time')) return $this->user_error('选择日期超出了班级的结束日期');
        }

        try {
            $this->startTrans();

            $w_ca = [
                'int_day'        => $data['int_day'],
                'int_start_hour' => $data['int_start_hour'],
                'cid'            => $data['cid'],
            ];
            $old_course_arrange = $this->where($w_ca)->find();
            if(empty($old_course_arrange)) {
                $data = array_merge($class->toArray(), $class_schedule->toArray(), $data);
                $data['create_type'] = 1;
                $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
                if($rs === false) throw new FailResult($this->getErrorMsg());

                $ca_id = $this->ca_id;
            } else {
                $ca_id = $old_course_arrange['ca_id'];
            }

            $m_cas = new \app\api\model\CourseArrangeStudent();
            $course_arrange = \app\api\model\CourseArrange::get($ca_id);
            $rs = $m_cas->addOneArrangeStudent($course_arrange, $data['sid']);
            if($rs === false) throw new FailResult($m_cas->getErrorMsg());

            if(!empty($data['sa_id'])) {
                $m_ma = new \app\api\model\MakeupArrange();
                $rs = $m_ma->addMakeUpStudentsFromAbsence($course_arrange, [$data['sa_id']]);
                if($rs === false) throw new FailResult($m_ma->getErrorMsg());
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $ca_id;
    }


}