<?php
/**
 * Author: luo
 * Time: 2017-10-24 15:47
**/

namespace app\api\model;

use app\common\Wechat;
use think\Exception;
use think\Log;

class SwipingCardRecord extends Base
{
    const BUSINESS_TYPE_ATTEND_CLASS  = 1;
    const BUSINESS_TYPE_LEAVE_SCHOOL  = 2;
    const BUSINESS_TYPE_ARRIVE_SCHOOL = 3;
    const BUSINESS_TYPE_LESSON_HOUR_REG = 4;        //按时间计费课程签到

    protected $hidden = ['create_uid','update_time', 'is_delete', 'delete_time', 'delete_uid'];
    public function student()
    {
        return $this->belongsTo('Student', 'sid', 'sid');
    }

    public function branch()
    {
        return $this->belongsTo('Branch', 'bid', 'bid');
    }

    /**
     * 刷卡
     * @param  [type] $card_no [description]
     * @return [type]          [description]
     */
    public function swipeCard($card_no){
        try {
            $m_student = Student::GetByCardNo($card_no);
        }catch(\Exception $e){
            return $this->user_error($e->getMessage());
        }
        if(!$m_student){
            return $this->user_error('学员卡号不存在!');
        }
        if($m_student->isQuit()){
            return $this->user_error('学员卡号已失效,学员已退学!');
        }
        if($m_student->isSuspend()){
            return $this->user_error('学员在休学状态,不能刷卡考勤!');
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

        //先记录刷卡记录
        $this->startTrans();

        try{
            $scr = [];
            $student_data = $m_student->getData();
            $now_int_day  = int_day($now_time);
            $now_int_hour = int_hour($now_time);

            array_copy($scr,$student_data,['og_id','bid','sid','sid','card_no']);

            $scr['int_day']  = $now_int_day;
            $scr['int_hour'] = $now_int_hour;

            $result = $this->save($scr);

            if(!$result){
                $this->rollback();
                return $this->sql_add_error('swiping_card_record');
            }

            $today_course_arrange_list = $m_student->getTodayCourseArranges();

            $current_att_ca = $this->filter_current_att_ca($today_course_arrange_list);

            if($current_att_ca){
                $handler = 'attendance';//刷卡考勤
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
                    exception('刷卡逻辑还未实现:'.$method);
                }
                $result = call_user_func_array([$this,$method],[$m_instance,$student_info]);
                if(!$result){
                    $this->rollback();
                }
                $this->commit();
                return $result;
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        $return_msg = $this->get_return_message($today_course_arrange_list);

        return $this->user_error($return_msg);
    }

    /**
     * 刷卡考勤
     * @param  [type] $m_cs [description]
     * @return [type]       [返回考勤的课程信息]
     */
    protected function swipe_attendance(CourseArrange $m_ca,$student_info){
        $satt_id = $m_ca->regOneStudentAtt($student_info['sid']);
        if(!$satt_id){
            return $this->user_error($m_ca->getError());
        }

        $this->business_type = 1;

        $this->save();

        $student_attendance_info = $m_ca->getLessonInfo($student_info['sid']);
        $ret['student'] = $student_info;
        $ret['student']['last_attendance_time'] = date('Y-m-d H:i',time());
        $ret['msg'] = '刷卡考勤成功';
        $ret['satt_id'] = $satt_id;

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
    protected function get_return_message($cs_list){
        $now_time = time();
        $now_int_hour = int_hour($now_time);
        $future_cs_nums = 0;
        if($cs_list){
            foreach($cs_list as $cs){
                if($cs->int_start_hour > $now_int_hour){
                    $future_cs_nums++;
                }
            }
        }
        if($future_cs_nums > 0){
            return '还没有到刷卡时间!';
        }
        return '已过刷卡时间,请用登记考勤!';
    }

    /**
     * 刷卡考勤之后创建一条刷卡记录记录
     * @param Student $student
     * @return SwipingCardRecord
     */
    public static function createOneRecord(Student $student)
    {
        $model = new self();
        $data = [];
        $data['og_id'] = $student['og_id'];
        $data['bid'] = $student['bid'];
        $data['sid'] = $student['sid'];
        $data['card_no']  = $student['card_no'];
        $data['int_day']  = int_day(request()->time());
        $data['int_hour'] = int_hour(request()->time());
        $data['business_type'] = self::BUSINESS_TYPE_ATTEND_CLASS;
        $model->save($data);
        return $model;
    }




    public function wechat_tpl_notify()
    {
        $wechat = Wechat::getInstance($this);
        $message['appid'] = $wechat->appid;

        $scene = 'attendance_inform';
        $default_template_setting = config('tplmsg')[$scene];
        $message['url'] = $default_template_setting['weixin']['url'];//todo  替换[host]和业务[id]

        if ($wechat->default) {
            $message['template_id'] = $default_template_setting['weixin']['template_id'];
        } else {
            $w = [];
            $w['appid'] = $message['appid'];
            $w['scene'] = $scene;
            $target_tpl = WxmpTemplate::get($w);
            if (empty($target_tpl)) {
                Log::record('该公众号还没有成功设置该模板!');
                return false;
            }
            $message['template_id'] = $target_tpl['template_id'];
        }

        $user_template_setting = isset(Config::userConfig()['wechat_template'][$scene]) ? Config::userConfig()['wechat_template'][$scene] : null;
        if (empty($user_template_setting)) {
            /*客户如果没有设置公众号的模板消息的first字段、remark字段和颜色的设置，则使用系统默认的公众号的设置*/
            $user_template_setting = $default_template_setting;
        }

        $student = $this->getAttr('student');
        $temp = [];
        $temp['student_name'] = $student['student_name'];
        $temp['time']         = date('Y-m-d H:i:s', request()->time());
        $temp['address']      = $this->getAttr('branch')['address'];

        $search  = array_values($user_template_setting['tpl_fields']);
        $replace = array_values($temp);

        $data = $user_template_setting['weixin']['data'];
        foreach ($data as &$subject) {
            $subject = str_replace($search, $replace, $subject);
        }
        $sms_message = str_replace($search, $replace, $user_template_setting['sms']['tpl']);
        $message['data'] = $data;

        $user_list = $student['user'];
        if (empty($user_list)) {
            throw new Exception('该学生没有关联的家长账号!');
        }
        $inner_message = [];
        $inner_message['og_id'] = $this->getData('og_id');
        $inner_message['bid'] = $this->getData('bid');
        $inner_message['sid'] = $this->getData('sid');
        $inner_message['business_type'] = $scene;
        $inner_message['business_id']   = $this->getData($this->getPk());
        $inner_message['title']   = $default_template_setting['message']['title'];
        $inner_message['content'] = str_replace($search, $replace, $default_template_setting['message']['content']);
        $inner_message['url']     = $message['url'];
        foreach ($user_list as $user) {
            $inner_message['uid'] = $user['uid'];
            Message::create($inner_message);
            if ($user['mobile'] && $user_template_setting['sms_switch']) {
                queue_push('SendSmsMsg', [$user['mobile'], $sms_message]);
            }
            if ($user['openid'] && $user_template_setting['weixin']) {
                $w = [];
                $w['openid'] = $user['openid'];
                $w['subscribe'] = WxmpFans::SUBSCRIBE;
                if (WxmpFans::get($w)) {
                    $message['openid'] = $user['openid'];
                    queue_push('SendWxTplMsg', $message);
                }
            }
        }
    }
}