<?php
/** 
 * Author: luo
 * Time: 2017-10-12 09:41
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class CourseArrangeStudent extends Base
{

    public static $detail_fields = [
        ['type'=>'index','width'=>60,'align'=>'center'],
        ['title'=>'学员姓名','key'=>'student','align'=>'center'],
        ['title'=>'时间段','key'=>'time_section','align'=>'center','width'=>170],
        ['title'=>'是否请假','key'=>'is_leave','align'=>'center'],
        ['title'=>'是否上课','key'=>'is_attendance','align'=>'center'],
    ];

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

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url,sex,first_tel');
    }

    public function customer()
    {
        return $this->hasOne('Customer', 'cu_id', 'cu_id')->field('cu_id,name,sex,first_tel');
    }

    public function courseArrange()
    {
        return $this->hasOne('CourseArrange', 'ca_id', 'ca_id');
    }

    /**
     * 根据学员ID和排课ID获取排课学员记录对象实例
     * @param $sid
     * @param $ca_id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBySidAndCaId($sid,$ca_id){
        $w_cas['sid']   = $sid;
        $w_cas['ca_id'] = $ca_id;

        $cas_info = $this->where($w_cas)->find();

        if(!$cas_info){
            //补cas_info记录
            $cas_fields = ['og_id','ca_id', 'lid', 'cid','sj_id','sg_id','grade','int_day', 'int_start_hour', 'int_end_hour'];
            $ca_info = get_ca_info($ca_id);
            $new_cas = [];
            $new_cas['sid'] = $sid;
            array_copy($new_cas,$ca_info,$cas_fields);
            $new_cas['is_in'] = -1;


            $result = $this->save($new_cas);

           
            $w_cas = [];
            $w_cas['cas_id'] = $this->cas_id;
            $cas_info = $this->where($w_cas)->find();
        }

        return $cas_info;
    }

    /**
     * @param $input
     * @param $catt_info
     * @param $params
     */
    public function updateAttStatus($input,$catt_info = [],$params = []){
        $cas_info = $this->getData();

        if($cas_info['is_attendance'] && $cas_info['satt_id'] > 0){
            return $cas_info['satt_id'];
        }

        $update_cas = [];
        array_copy($update_cas,$input,['is_in','is_leave','remark','is_consume','consume_source_type','consume_lesson_amount']);

        if($input['is_consume']) {
            //这段代码先屏蔽，以后如果开启 登记考勤时 可以满足不同的学员 同一次课 扣不同课时时再开启 2018-07-30
            /*
            if (isset($input['consume_lesson_hour']) && $input['consume_lesson_hour'] > 0) {
                $update_cas['consume_lesson_hour'] = $input['consume_lesson_hour'];
            } else {
                $update_cas['consume_lesson_hour'] = $catt_info['consume_lesson_hour'];
            }
            */
            $update_cas['consume_lesson_hour'] = $catt_info['consume_lesson_hour'];
        }
        if(isset($update_cas['consume_source_type']) && $catt_info['consume_source_type'] != $update_cas['consume_source_type']){
            $update_cas['consume_source_type'] = $catt_info['consume_source_type'];
        }

        if($catt_info['consume_source_type'] == 2 && isset($input['is_reset']) && $input['is_reset']){        //如果是自定义消费金额
            $update_cas['consume_lesson_amount'] = floatval($input['reset_val']);
            $update_cas['is_reset'] = 1;
        }

        $new_cas = array_merge($cas_info,$update_cas);

        $this->startTrans();

        $ret = true;

        try {

            $mStudentAttendance = new StudentAttendance();

            $extra_consume = [];

            if(isset($input['extra_consume'])){
                $extra_consume = $input['extra_consume'];
            }

            $satt = $mStudentAttendance->regSAttByCatt($new_cas, $catt_info, $params,$extra_consume);

            if (!$satt) {
                $this->rollback();
                return $this->user_error($mStudentAttendance->getError());
            }

            if(isset($update_cas['is_reset'])){
                unset($update_cas['is_reset']);
            }

            $update_cas['is_attendance'] = 1;
            if(!is_bool($satt)){
                $update_cas['satt_id'] = $satt['satt_id'];
                if(isset($satt['has_extra_consume'])){
                    $update_cas['has_extra_consume'] = $satt['has_extra_consume'];
                }
                $ret = $satt['satt_id'];
            }

            $result = $this->data($update_cas)->save();

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('course_arrange_student');
            }

            //请假补登
            $new_cas = array_merge($new_cas,$update_cas);

            if($new_cas['is_leave']){
                $m_student_leave = new StudentLeave();
                $result = $m_student_leave->createByCas($new_cas);
                if(!$result){
                    $this->rollback();
                    return $this->user_error($m_student_leave->getError());
                }
            }

        }catch (\Exception $e){

            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return $ret;

    }

    public function addStudents(CourseArrange $course, array $list)
    {
        try {
            $this->startTrans();

            $m_ma = new MakeupArrange();
            $m_ta = new TrialListenArrange();
            foreach ($list as $row) {
                $sid = isset($row['sid']) ? $row['sid'] : 0;
                $slv_id = isset($row['slv_id']) ? $row['slv_id'] : 0;
                $sa_id = isset($row['sa_id']) ? $row['sa_id'] : 0;
                $cu_id = isset($row['cu_id']) ? $row['cu_id'] : 0;

                if ($sid <= 0 && $slv_id <= 0 && $sa_id <= 0 && $cu_id <= 0) {
                    throw new FailResult('参数不正确');
                }

                if ($slv_id > 0) {
                    $rs = $m_ma->addMakeUpStudentsFromLeave($course, [$slv_id]);
                    if ($rs === false) throw new FailResult($m_ma->getErrorMsg());
                }

                if ($sa_id > 0) {
                    $rs = $m_ma->addMakeUpStudentsFromAbsence($course, [$sa_id]);
                    if ($rs === false) throw new FailResult($m_ma->getErrorMsg());
                }

                if ($sid > 0) {
                    if($course->is_trial) {
                        $rs = $m_ta->createOneTrial($course->ca_id, ['sid' => $sid]);
                        if($rs === false) throw new FailResult($m_ta->getErrorMsg());
                    } else {
                        $rs = $this->addOneArrangeStudent($course, $sid);
                        if ($rs === false) throw new FailResult($this->getErrorMsg());
                    }
                }

                if ($cu_id > 0 && $sid <= 0) {
                    if($course->is_trial) {
                        $rs = $m_ta->createOneTrial($course->ca_id, ['cu_id' => $cu_id]);
                        if($rs === false) throw new FailResult($m_ta->getErrorMsg());
                    } else {
                        $rs = $this->addOneArrangeStudent($course, $sid, ['cu_id' => $cu_id]);
                        if ($rs === false) throw new FailResult($this->getErrorMsg());
                    }
                }

            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    /**
     * @author luo
     * @param CourseArrange $course
     * @param $sid
     */
    public function addOneArrangeStudent(CourseArrange $course, $sid, array $data = []) {
        $ca_info = $course->toArray();
        array_copy($data,$ca_info,['ca_id','cid','lid','int_day','int_start_hour','int_end_hour','sj_id','grade','sg_id','consume_lesson_hour']);

        $data['sid']            = $sid;
        $data['cu_id']          = empty($data['cu_id']) ? 0 : $data['cu_id'];
        $data['is_trial']       = !empty($data['is_trial']) ? $data['is_trial'] : (empty($data['cu_id']) ? 0 : 1);

        //判断学员是否重复
        if($sid > 0){
            $w_ex['sid'] = $sid;
            array_copy($w_ex,$data,['int_day','int_start_hour']);
            $w_ex['ca_id'] = ['NEQ',$course->ca_id];

            $ex_cas = get_cas_info($w_ex);
            if($ex_cas){
                $msg = sprintf('学员:%s在时间段%s %s已经有排课记录存在!',get_student_name($sid),int_day_to_date_str($data['int_day']),int_hour_to_hour_str($data['int_start_hour']));
                return $this->user_error($msg);
            }
        }



        if(empty($data['sid']) && empty($data['cu_id'])) return $this->user_error('学生或者客户id必须有一个');
        $old = $this->where(['ca_id' => $course->ca_id, 'sid' => $sid, 'cu_id' => $data['cu_id']])->find();

        $this->startTrans();
        try {
            if(!empty($old)) {
                $rs = $old->allowField(true)->isUpdate(true)->save($data);
                if($rs === false){
                    return $this->sql_save_error('course_arrange_student');
                }
                return true;
            }

            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if($rs === false){
                return $this->sql_add_error('course_arrange_student');
            }
            
            //$rs = $this->updateRemainArrangeTimes($data['sid'], $data['lid'], -1);
            $mStudentLesson = new StudentLesson();
            $rs = $mStudentLesson->updateArrange($data['sid'], $data['lid']);
            if($rs === false){
                return $this->user_error($mStudentLesson->getError());
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return $rs;
    }

    //更新学员未排课次数
    //public function updateRemainArrangeTimes($sid, $lid, $num = 1)
    //{
    //    if($sid <= 0 || $lid <= 0) return true;
    //
    //    $lesson = get_lesson_info($lid, true);
    //    if(!empty($lesson) && ($lesson['lesson_type'] == Lesson::LESSON_TYPE_ONE_TO_ONE
    //            || $lesson['lesson_type'] == Lesson::LESSON_TYPE_ONE_TO_MULTI)) {
    //        /** @var StudentLesson $student_lesson */
    //        $student_lesson = StudentLesson::get(['sid' => $sid, 'lid' => $lid]);
    //        if (!empty($student_lesson) && $student_lesson['remain_arrange_times'] > 0) {
    //            $student_lesson->remain_arrange_times = $student_lesson->remain_arrange_times + $num;
    //            $rs = $student_lesson->allowField('remain_arrange_times')->isUpdate(true)->save();
    //            if ($rs === false) return $this->user_error('减少学员未排课次失败');
    //        }
    //    }
    //
    //    return true;
    //}

    /*
     * 1. 删除课程安排的学生
     * 2. 增加学生的未安排课程次数
     */
    public function deleteByCaId($ca_id)
    {
        $ca_info = get_ca_info($ca_id);
        if(!$ca_info){
            return true;
        }
        $lesson_type = $ca_info['lesson_type'];

        $cas_list = $this->where('ca_id',$ca_id)->select();

        if(!$cas_list){
            return true;
        }

        $mStudentAbsence = new StudentAbsence();
        $mTla = new TrialListenArrange();
        $mMakeupArrange = new MakeupArrange();

        $this->startTrans();
        try{
            foreach ($cas_list as $k =>  $cas){
                if ($cas['is_makeup'] == 1){
                    //删除关联的缺课记录
                    $w_sa['ma_ca_id'] = $cas['ca_id'];
                    $w_sa['sid'] = $cas['sid'];
                    $student_absence = $mStudentAbsence->where($w_sa)->find();

                    if ($student_absence['status'] == 1){
                        $update_sa['status'] = 0;
                        $update_sa['ma_ca_id'] = 0;

                        $result = $mStudentAbsence->updateStatus($student_absence['sa_id'],$update_sa);
                        if(false === $result){
                            $this->rollback();
                            return $this->user_error($mStudentAbsence->getError());
                        }
                    }
                }

                $w_trial_listen_arrange = [
                    'sid' => $cas['sid'],
                    'cu_id' => $cas['cu_id'],
                    'ca_id' => $cas['ca_id']
                ];
                $result = $mTla->where($w_trial_listen_arrange)->delete();
                if(false === $result){
                    $this->rollback();
                    return $this->user_error($mTla->getError());
                }

                $w_make_up = [
                    'sid' => $cas['sid'],
                    'ca_id' => $cas['ca_id']
                ];
                $result = $mMakeupArrange->where($w_make_up)->delete();
                if(false === $result){
                    $this->rollback();
                    return $this->user_error($mMakeupArrange->getError());
                }


                $cas_info = $cas->getData();
                $result = $cas->delete();
                if(false === $result){
                    $this->rollback();
                    return $this->sql_delete_error('course_arrange_student');
                }

                if(!$cas_info['is_trial']){
                    if($lesson_type != 0){
                        $mStudentLesson = new StudentLesson();
                        $result = $mStudentLesson->updateArrange($cas_info['sid'],$cas_info['lid']);
                        if(false === $result){
                            $this->rollback();
                            return $this->user_error($mStudentLesson->getError());
                        }
                    }
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * @desc  删除课程安排的学生
     * @author luo
     * @param array $sids
     * @param $ca_id
     * @param bool $handle_student_lesson 是否处理相关购买的课时数
     */
    public function deleteStudentByCaId(array $sids, $ca_id)
    {
        if(empty($sids) || !is_array($sids)) return $this->user_error('学生id错误');

        $this->startTrans();
        try {
            $course = CourseArrange::get($ca_id);
            foreach($sids as $sid) {
                $rs = $this->delOneRow($course, $sid);
                if($rs === false) throw new FailResult($this->getErrorMsg());
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    //删除一条记录
    public function delOneRow(CourseArrange $course, $sid = 0, $cu_id = 0)
    {
        if($sid == 0 && $cu_id == 0) return $this->user_error('sid,cu_id不能同时为0');
        if(empty($course)) return $this->user_error('排课不能为空');


        $w_cas = ['sid' => $sid, 'cu_id' => $cu_id];
        $row = $this->where('ca_id', $course->ca_id)->where($w_cas)->find();
        if(empty($row)) return true;
        if($row['is_trial'] != 1 && $row['is_attendance'] == 1){
            return $this->user_error('已经考勤不能删除');
        }

        $this->startTrans();
        try {
            if ($row['is_trial'] > 0) {
                $w_cas['ca_id'] = $course->ca_id;
                $trial = TrialListenArrange::get($w_cas);
                if (!empty($trial)) {
                    if ($trial['is_attendance']) return $this->user_error('试听已经考勤不能删除');
                    $result = $trial->delete();
                    if (false === $result){
                        $this->rollback();
                        return $this->sql_delete_error('trial_listen_arrange');
                    }
                }
            }

            if ($row['is_makeup'] > 0 && $sid > 0) {
                $makeup = MakeupArrange::get(['sid' => $sid, 'ca_id' => $course->ca_id]);
                if (!empty($makeup)) {
                    $result = $makeup->delete();
                    if (false === $result){
                        $this->rollback();
                        return $this->sql_delete_error('makeup_arrange');
                    }
                    $mStudentAbsence = new StudentAbsence();
                    $w_sa['sa_id'] = $makeup['sa_id'];
                    $student_absence = $mStudentAbsence->where($w_sa)->find();
                    if (!empty($student_absence) && $student_absence['status'] == 1){
                        $update_sa['status'] = 0;
                        $update_sa['ma_ca_id'] = 0;

                        $result = $mStudentAbsence->updateStatus($student_absence['sa_id'],$update_sa);
                        if(false === $result){
                            return $this->user_error($mStudentAbsence->getError());
                        }
                    }
                }
            }

            $mStudentLesson = new StudentLesson();
            $result = $mStudentLesson->updateArrange($sid, $course->lid);
            if(false === $result){
                $this->rollback();
                return $this->user_error($mStudentLesson->getError());
            }

            $result = $row->delete();
            if (false === $result){
                $this->rollback();
                return $this->sql_delete_error('course_arrange_student');
            }

            //删除该学员在该班级的所有后面未考勤的排课
            if($row['cid'] > 0 && $row['sid'] > 0){
                $w_cs = [];
                $w_cs['cid'] = $row['cid'];
                $w_cs['sid'] = $row['sid'];
                $w_cs['status'] = 1;
                $w_cs['is_end'] = 0;
                $ex_cs = get_cs_info($w_cs);
                if(!$ex_cs){
                    $w_cas=[];
                    $w_cas['sid'] = $row['sid'];
                    $w_cas['cid'] = $row['cid'];
                    $w_cas['is_in'] = -1;

                    $result = $this->where($w_cas)->delete(true);
                    if(false === $result){
                        $this->rollback();
                        return $this->sql_delete_error('course_arrange_student');
                    }
                }
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    //删除排课里面的多个学员
    public function delList($ca_id, array $list)
    {
        try {
            $this->startTrans();
            $course = CourseArrange::get($ca_id);
            foreach($list as $row) {
                $sid = isset($row['sid']) ?$row['sid']: 0;
                $cu_id = isset($row['cu_id']) ?$row['cu_id']: 0;
                $rs = $this->delOneRow($course, $sid, $cu_id);
                if($rs === false){
                    return $this->user_error($this->getError());
                }
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    //把一个班的学生加入排课学生中
    public static function AddStudentOfClass($course)
    {
        $self = new self();

        if(!($course instanceof CourseArrange)) {
            $course = CourseArrange::get($course);
        }
        if(empty($course)) return $self->user_error('课程不存在');
        if($course['cid'] <= 0) return true;

        $sids = (new ClassStudent())->where('cid', $course['cid'])
            ->where('status', ClassStudent::STATUS_NORMAL)->column('sid');
        $sids = array_unique($sids);
        if(empty($sids)) return true;

        try {
            $self->startTrans();

            foreach ($sids as $sid) {
                $rs = $self->addOneArrangeStudent($course, $sid);
                if($rs === false) throw new FailResult($self->getErrorMsg());
            }

            $self->commit();
        } catch (Exception $e) {
            $self->rollback();
            return $self->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

}