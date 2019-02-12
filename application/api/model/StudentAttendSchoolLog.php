<?php
/**
 * Author: luo
 * Time: 2018-01-09 14:30
**/

namespace app\api\model;

use app\common\exception\FailResult;
use app\common\Wechat;
use think\Exception;

class StudentAttendSchoolLog extends Base
{
    protected $type = [
        'attend_time' => 'timestamp',
        'leave_time' => 'timestamp',
    ];

    public function getAttendTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i', $value) : $value;
    }

    public function getLeaveTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i', $value) : $value;
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')
            ->field('sid,student_name,photo_url,sex');
    }

    //多个学生同时登记到离校
    public function addLogs($post, $sids)
    {
        if(empty($sids)) return $this->user_error('sids error');
        is_string($sids) && $sids = explode(',', $sids);

        try {
            $this->startTrans();
            foreach ($sids as $sid) {
                $log_data = $post;
                $log_data['sid'] = $sid;
                $rs = $this->addOneLog($log_data);
                if ($rs === false) throw new FailResult($this->getErrorMsg());
            }
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //添加到离校记录
    public function addOneLog($input)
    {
        if(!isset($input['sid'])) return $this->user_error('param error');
        if(!isset($input['int_day'])){
            $input['int_day'] = int_day(time());
        }else{
            $input['int_day'] = format_int_day($input['int_day']);
        }
        $sid = $input['sid'];
        $w_s['sid'] = $sid;

        $m_student = $this->m_student->where($w_s)->cache(1)->find();

        if(!$m_student){
            return $this->user_error('学员ID不存在:'.$sid);
        }

        $input['og_id'] = $m_student->og_id;
        if(!isset($input['bid'])){
            $input['bid'] = $m_student->bid;
        }
        
        $w_sasl['sid']     = $input['sid'];
        $w_sasl['int_day'] = $input['int_day'];

        $m_log = $this->where($w_sasl)->order('create_time DESC')->find();

        if($m_log && isset($input['swipe'])){
            //如果是刷卡的
            $now_time = time();
            if(($now_time - $m_log->getData('create_time')) < 1800){
                if($m_log['is_leave'] == 1){
                    $action_text = '离校';
                }else{
                    $action_text = '到校';
                }
                $cs_config = user_config('params.class_attendance');
                $before_min = $cs_config['min_before_class'];
                $after_min  = $cs_config['min_after_class'];

                $attendance_notice = sprintf("如要刷卡考勤,请在上课前 %s 分钟至下课后 %s 分钟内这个时间端再刷卡",$before_min,$after_min);

                return $this->user_error('已经发送过'.$action_text.'通知，请不要重复刷卡!'.$attendance_notice);
            }
        }
        $ret = [
            'action'=>'',
            'msg'=>'',
            'student'=>$m_student->getData()
        ];
        $this->startTrans();
        try{
            if($m_log) {
                if ($m_log['is_attend'] == 1 && isset($input['is_attend']) && $input['is_attend'] == 1) {
                    $this->rollback();
                    return $this->user_error('已经进行过到校通知操作,请不要重新操作!');
                }

                if ($m_log['is_leave'] == 1 && isset($input['is_leave']) && $input['is_leave'] == 1) {
                    $this->rollback();
                    return $this->user_error('已经进行过离校通知操作，请不要重新操作!');
                }

                if(isset($input['is_leave']) && $input['is_leave'] == 1) {
                    $update_time = $m_log->getData('attend_time');
                    if((time() - $update_time) < 1800 || $m_log['is_leave'] == 1) {
                        $this->rollback();
                        return $this->user_error('您刚刚进行过到校操作，若要离校，请在半小时后操作!');
                    }
                }

                $update['is_leave'] = 1;
                $update['leave_time'] = time();

                

                $w_sasl_update['sasl_id'] = $m_log->sasl_id;

                $result = $m_log->allowField('is_leave,leave_time')->save($update,$w_sasl_update);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('student_attend_school_log');
                }
                $ret['action'] = 'leave';
                $ret['msg'] = '离校通知发送成功!';

                $bs_data = array_merge($m_log->getData(),$ret);
            } else {
                if(isset($input['is_leave']) && $input['is_leave'] == 1) {
                    $this->rollback();
                    return $this->user_error('需要先进行到校操作，才能进行离校操作');
                }
                if(!isset($input['is_attend'])){
                    $input['is_attend'] = 1;
                }

                $input['attend_time'] = time();

                $result = $this->data([])->allowField(true)->isUpdate(false)->save($input);
                if(!$result){
                    $this->rollback();
                    return $this->sql_add_error('student_attend_school_log');
                }
                $ret['action'] = 'attend';
                $ret['msg'] = '到校通知发送成功!';

                $bs_data = array_merge($this->getData(),$ret);
            }

            //发送模板消息
            $this->m_message->sendTplMsg('attend_school_push',$bs_data);
            add_service_record('attend_school', ['sid' => $input['sid'], 'st_did' => 224]);

        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();


        return $ret;
    }

    //课评发送微信通知
    public function wechat_tpl_notify($sid, $action_name)
    {
        $wechat = Wechat::getInstance();
        $message['appid'] = $wechat->appid;
        $scene = 'attend_school_push';
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
                //该公众号还没有成功设置该模板.
                return $this->user_error('该公众号还没有成功设置模板');
            }
            $message['template_id'] = $target_tpl['template_id'];
        }

        $user_template_setting = isset(Config::userConfig()['wechat_template'][$scene]) ? Config::userConfig()['wechat_template'][$scene] : null;
        if (empty($user_template_setting)) {
            //客户如果没有设置公众号的模板消息的first字段、remark字段和颜色的设置，则使用系统默认的公众号的设置
            $user_template_setting = $default_template_setting;
        }

        $student = (new Student())->where('sid', $sid)->find();
        $temp = [];
        $temp['action_name'] = $action_name;
        $temp['student_name'] = $student->getAttr('student_name');
        $temp['branch_name'] = $student->getAttr('branch')['branch_name'];
        $temp['create_time'] = date('Y-m-d H:i', time());
        $temp['create_day'] = date('Y-m-d', time());

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
            return $this->user_error('该学生没有关联的家长账号!');
        }
        $inner_message = [];
        $inner_message['og_id'] = $student->getAttr('og_id');
        $inner_message['bid'] = $student->getAttr('bid');
        $inner_message['sid'] = $student->getAttr('sid');
        $inner_message['business_type'] = $scene;
        $inner_message['business_id'] = isset($this->sasl_id) ? $this->sasl_id : 0;
        $inner_message['title']   = $default_template_setting['message']['title'];
        $inner_message['content'] = str_replace($search, $replace, $default_template_setting['message']['content']);
        $inner_message['url']     = $message['url'];
        foreach ($user_list as $user) {
            $inner_message['uid'] = $user['uid'];
            Message::create($inner_message);
            if ($user['mobile'] && $user_template_setting['sms_switch']) {
                queue_push('SendSmsMsg', [$user['mobile'], $sms_message]);
            }
            if ($user['openid'] && $user_template_setting['weixin_switch']) {
                $w = [];
                $w['appid']  = $message['appid'];
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