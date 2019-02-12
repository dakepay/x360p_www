<?php
/**
 * Author: luo
 * Time: 2017-10-12 09:41
 **/

namespace app\sapi\model;

use app\common\exception\FailResult;
use think\Exception;

class CourseArrangeStudent extends Base
{

    public function getLessonHourAttr($value,$data){
        $default_lesson_hour = 1.00;
        if(isset($data['consume_lesson_hour']) && $data['consume_lesson_hour'] > 0){
            return $data['consume_lesson_hour'];
        }
        $minutes     = cacu_minutes($data['int_start_hour'],$data['int_end_hour']);
        if(isset($data['lid']) && $data['lid'] > 0){
            $lid = $data['lid'];
            $lesson_info = get_lesson_info($lid);

            if($minutes == $lesson_info['unit_lesson_minutes']){
                return $lesson_info['unit_lesson_hours'];
            }

            $lesson_per_lesson_hour_minutes = $lesson_info['unit_lesson_minutes'] / $lesson_info['unit_lesson_hours'];

            $lesson_hour = cacu_lesson_hours($data['int_start_hour'],$data['int_end_hour'],$lesson_per_lesson_hour_minutes);

            if($lesson_hour < 0){
                $lesson_hour = $default_lesson_hour;
            }

            return $lesson_hour;
        }elseif($data['cid'] > 0){
            $class_info = get_class_info($data['cid']);
            $per_lesson_hour_minutes = $class_info['per_lesson_hour_minutes'];
            if($per_lesson_hour_minutes == 0){
                return $this->m_classes->getConsumeLessonHour($data['cid']);
            }
            if($minutes == $per_lesson_hour_minutes){
                if($class_info['consume_lesson_hour'] == 0){
                    $lesson_hour = $default_lesson_hour;
                }else{
                    $lesson_hour = $class_info['consume_lesson_hour'];
                }

                return $lesson_hour;
            }

            $lesson_hour = cacu_lesson_hours($data['int_start_hour'],$data['int_end_hour'],$per_lesson_hour_minutes);

            if($lesson_hour < 0){
                $lesson_hour = $default_lesson_hour;
            }

            return $lesson_hour;

        }

        return $default_lesson_hour;
    }

    public function setIntStartHourAttr($value)
    {
        return format_int_hour($value);
    }

    public function setIntEndHourAttr($value)
    {
        return format_int_hour($value);
    }


    public function getIntStartHourAttr($value,$data)
    {
        // return date('H:s',strtotime($value));
        return $value;
    }

    public function getIntEndHourAttr($value,$data)
    {
        // return date('H:s',strtotime($value));
        return $value;
    }

    /**
     * @author luo
     * @param CourseArrange $course
     * @param $sid
     */
    public function addOneArrangeStudent(CourseArrange $course, $sid, array $data = [])
    {
        $ca_info = $course->toArray();
        array_copy($data, $ca_info, ['ca_id', 'cid', 'lid', 'int_day', 'int_start_hour', 'int_end_hour', 'sj_id', 'grade', 'sg_id']);

        $data['sid'] = $sid;
        $data['cu_id'] = empty($data['cu_id']) ? 0 : $data['cu_id'];
        $data['is_trial'] = !empty($data['is_trial']) ? $data['is_trial'] : (empty($data['cu_id']) ? 0 : 1);

        //判断学员是否重复
        if($sid > 0) {
            $w_ex['sid'] = $sid;
            array_copy($w_ex, $data, ['int_day', 'int_start_hour']);
            $w_ex['ca_id'] = ['NEQ', $course->ca_id];

            $ex_cas = get_cas_info($w_ex);
            if($ex_cas) {
                $msg = sprintf('学员:%s在时间段%s %s已经有排课记录存在!', get_student_name($sid), int_day_to_date_str($data['int_day']), int_hour_to_hour_str($data['int_start_hour']));
                return $this->user_error($msg);
            }
        }

        if(empty($data['sid']) && empty($data['cu_id'])) return $this->user_error('学生或者客户id必须有一个');

        $old = $this->where(['ca_id' => $course->ca_id, 'sid' => $sid, 'cu_id' => $data['cu_id']])->find();
        if(!empty($old)) {
            $rs = $old->allowField(true)->isUpdate(true)->save($data);
            if($rs === false) return $old->getError();
            return true;
        }

        try {
            $this->startTrans();
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $m_sl = new \app\api\model\StudentLesson();
            $rs = $m_sl->updateArrange($data['sid'], $data['lid']);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //删除一条记录
    public function delOneRow(CourseArrange $course, $sid = 0, $cu_id = 0)
    {
        if($sid == 0 && $cu_id == 0) return $this->user_error('sid,cu_id不能同时为0');
        if(empty($course)) return $this->user_error('排课不能为空');

        $w_cas = ['sid' => $sid, 'cu_id' => $cu_id, 'is_attendance' => 0];
        $row = $this->where('ca_id', $course->ca_id)->where($w_cas)->find();
        if(empty($row)) return true;
        if($row['is_attendance'] == 1) return $this->user_error('已经考勤不能删除');

        try {
            $this->startTrans();
            if($row['is_trial'] > 0) {
                $trial = \app\api\model\TrialListenArrange::get($w_cas);
                if(!empty($trial)) {
                    if($trial['is_attendance']) throw new FailResult('试听已经考勤不能删除');
                    $rs = $trial->delete();
                    if($rs === false) throw new FailResult($trial->getErrorMsg());
                }
            }

            if($row['is_makeup'] > 0 && $sid > 0) {
                $makeup = \app\api\model\MakeupArrange::get(['sid' => $sid]);
                if(!empty($makeup)) {
                    $rs = $makeup->delete();
                    if($rs === false) throw new FailResult($makeup->getErrorMsg());
                }
            }

            $m_sl = new \app\api\model\StudentLesson();
            $rs = $m_sl->updateArrange($sid, $course->lid);
            if($rs === false) throw new FailResult($m_sl->getErrorMsg());

            $rs = $row->delete();
            if($rs === false) throw new FailResult($row->getError());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }


    public function oneClass()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }

}