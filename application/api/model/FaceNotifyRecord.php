<?php

namespace app\api\model;

use think\Exception;
use think\Log;
use app\api\model\CourseArrange;

class FaceNotifyRecord extends Base
{

    /**
     * 刷卡
     * @param  [type] $card_no [description]
     * @return [type]          [description]
     */
    public function swipeFace($fcr_id){

        try {
            $m_student = Student::GetByFaceId($fcr_id);
        }catch(\Exception $e){
            return $this->user_error($e->getMessage());
        }
        if(!$m_student){
            return $this->user_error('学员信息不存在!');
        }
        if($m_student->isQuit()){
            return $this->user_error('学员信息已失效,学员已退学!');
        }
        if($m_student->isSuspend()){
            return $this->user_error('学员在休学状态,不能刷脸考勤!');
        }

        $student_info = [
            'sid'           => $m_student->sid,
            'student_name'  => $m_student->student_name,
            'nick_name'     => $m_student->nick_name,
            'avatar'        => $m_student->photo_url,
            'photo_url'     => $m_student->photo_url
        ];
        request()->bind('student',$student_info);
        //先获得学生当前的排课记录
        $now_time = time();
        $handler = '';
        $m_instance = null;
        $business_type = 0; //1,刷卡考勤 2,到校通知 3,离校通知 4,课程签到

        //先记录刷脸记录
        $this->startTrans();

        try{
            $scr = [];
            $student_data = $m_student->getData();
            $now_int_day  = int_day($now_time);
            $now_int_hour = int_hour($now_time);

            array_copy($scr,$student_data,['og_id','bid','sid','sid','face_id']);

            $scr['int_day']  = $now_int_day;
            $scr['int_hour'] = $now_int_hour;

            $result = $this->save($scr);

            if(!$result){
                $this->rollback();
                return $this->sql_add_error('face_notify_record');
            }

            $today_course_arrange_list = $m_student->getTodayCourseArranges();

            $current_att_ca = $this->filter_current_att_ca($today_course_arrange_list);

            if($current_att_ca){
                $handler = 'attendance';//刷脸考勤
                $m_instance = new CourseArrange($current_att_ca);
            }

            //再查看有没有按时间计费的课程
            if($handler == ''){
                $m_student_lesson = $m_student->getDateExpireLessonHour();
                if($m_student_lesson){
                    $handler = 'lesson_sign';//课程签到
                    $m_instance = $m_student_lesson;
                }
            }
            //自动处理到离校通知
            if($handler == ''){
                //自动刷卡到离校通知
                $today_course_arrange_times = count($today_course_arrange_list);
                if($today_course_arrange_times > 0){
                    $handler = 'school_sign';//到离校
                    $m_instance = $m_student;
                }
            }

            if($handler != ''){
                $method = 'swipe_'.$handler;
                if(!method_exists($this, $method)){
                    exception('刷脸逻辑还未实现:'.$method);
                }
                $result = call_user_func_array([$this,$method],[$m_instance,$student_info]);
                if(!$result){
                    $this->rollback();
                }
                $this->commit();
                return $result;
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        $return_msg = $this->get_return_message($today_course_arrange_list);

        return $this->user_error($return_msg);

    }

    /**
     * 刷脸考勤
     * @param  [type] $m_cs [description]
     * @return [type]       [返回考勤的课程信息]
     */
    protected function swipe_attendance($m_ca,$student_info){
        $result = $m_ca->regOneStudentAtt($student_info['sid'],StudentAttendance::ATT_WAY_FACE);
        if(!$result){
            return $this->user_error($m_ca->getError());
        }

        $this->business_type = 1;

        $this->save();

        $student_attendance_info = $m_ca->getLessonInfo($student_info['sid']);
        $ret['student'] = $student_info;
        $ret['student']['last_attendance_time'] = date('Y-m-d H:i',time());
        $ret['msg'] = '刷脸考勤成功';

        $ret = array_merge($ret,$student_attendance_info);

        return $ret;
    }

    /**
     * 课程签到
     * @param  [type] $m_sl [description]
     * @return [type]       [返回课程信息]
     */
    protected function swipe_lesson_sign($m_sl){
        $this->business_type = 4;
        $this->sl_id         = $m_sl->sl_id;
        $this->save();

        $sign_lesson_info = $m_sl->getLessonInfo();

        $ret['student'] = get_student_info($m_sl->sid);
        $ret['student']['last_attendance_time'] = date('Y-m-d H:i',time());
        $ret['msg'] = '考勤签到成功';

        $ret = array_merge($ret,$sign_lesson_info);
        return $ret;
    }

    /**
     * 刷卡到离校通知
     * @param  [type] $m_s [description]
     * @return [type]      [description]
     */
    protected function swipe_school_sign($m_s,$student_info){
        $input['sid'] = $student_info['sid'];
        $input['swipe'] = 1;

        $result = $this->m_student_attend_school_log->addOneLog($input);

        if(!$result){
            return $this->user_error($this->m_student_attend_school_log->getError());
        }
        if($result['action'] == 'leave'){
            $business_type = 2;
        }else if($result['action'] == 'attend'){
            $business_type = 3;
        }

        $this->business_type = $business_type;
        $this->save();

        return $result;

    }

    /**
     * 过滤当前时间的排课
     * @param  [type] $ca_list [description]
     * @return [type]          [description]
     */
    protected function filter_current_att_ca($ca_list){
        $now_int_hour = int_hour(time());
        $cs_config = user_config('params.class_attendance');
        $before_min = $cs_config['min_before_class'];
        $after_min  = $cs_config['min_after_class'];

        $current_ca = null;

        foreach($ca_list as $ca){
            if($now_int_hour < $ca['int_start_hour']){
                $diff_minutes = cacu_minutes($now_int_hour,$ca['int_start_hour']);
                if($diff_minutes <= $before_min){
                    $current_ca = $ca;
                    break;
                }
            }elseif($now_int_hour <= $ca['int_end_hour']){
                $current_ca = $ca;
                break;
            }else{
                $diff_minutes = cacu_minutes($ca['int_end_hour'],$now_int_hour);
                if($diff_minutes <= $after_min){
                    $current_ca = $ca;
                    break;
                }
            }
        }

        return $current_ca;
    }

    /**
     * [get_return_message description]
     * @param  [type] $cs_list [description]
     * @return [type]          [description]
     */
    protected function get_return_message($tcal_list){
        $now_time = time();
        $now_int_hour = int_hour($now_time);
        $future_cas_nums = 0;
        if($tcal_list){
            foreach($tcal_list as $cas){
                if($cas->int_start_hour > $now_int_hour){
                    $future_cas_nums++;
                }
            }
        }
        if($future_cas_nums > 0){
            return '还没有到刷脸时间!';
        }
        return '今天没有排课!';
    }


}