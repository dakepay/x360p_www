<?php
/**
 * Author: luo
 * Time: 2017-12-29 10:57
**/

namespace app\api\model;


use app\common\exception\FailResult;
use app\common\Wechat;
use think\Exception;
use think\Hook;
use think\Log;

class Review extends Base
{
    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $type = [
        'content' => 'json',
    ];

    protected function setSidsAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    /*protected function setIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }*/

    public function setIntStartHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    public function setIntEndHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    public function reviewFile()
    {
        return $this->hasMany('ReviewFile', 'rvw_id', 'rvw_id');
    }

    public function reviewStudent()
    {
        return $this->hasMany('ReviewStudent', 'rvw_id', 'rvw_id');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function lesson()
    {
        return $this->hasOne('Lesson', 'lid', 'lid')->field('lid,lesson_name,short_desc');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'eid', 'eid')->field('eid,ename');
    }

    public function reviewTplSetting()
    {
        return $this->hasOne('ReviewTplSetting', 'rts_id', 'rts_id');
    }

    //创建点评
    public function addOneReview($review_data, $review_file = [], &$review_student = [])
    {
        $lesson = (new Lesson())->where('lid', $review_data['lid'])->field('lesson_type')->find();
        $review_data['lesson_type'] = $lesson ? $lesson['lesson_type'] : 0;

        if(isset($review_data['catt_id']) && $review_data['catt_id'] > 0) {
            $attendance = (new ClassAttendance())->field('ca_id')->find($review_data['catt_id']);
            if($attendance) {
                $review_data['ca_id'] = $attendance['ca_id'];
            }
        }

        $this->startTrans();
        try {
            //--1-- 添加总点评
            $review_data['int_day'] = format_int_day($review_data['int_day']);
            $rs = $this->data([])->isUpdate(false)->allowField(true)->save($review_data);
            if ($rs === false) return $this->user_error('添加点评失败');

            $rvw_id = $this->getAttr('rvw_id');

            //--2-- 添加点评相关文件
            if (!empty($review_file)) {
                $m_file = new File();
                $m_rf = new ReviewFile();
                foreach ($review_file as $per_file) {
                    if(empty($per_file['file_id'])) {
                        log_write($per_file, 'error');
                        continue;
                    }
                    $file = $m_file->find($per_file['file_id']);
                    $file = $file ? $file->toArray() : [];
                    $per_file = array_merge($per_file, $file);
                    $per_file['rvw_id'] = $rvw_id;
                    $rs = $m_rf->data([])->isUpdate(false)->allowField(true)->save($per_file);
                    if ($rs === false) throw new FailResult($m_rf->getErrorMsg());
                }
            }

            //--3-- 个人点评
            if (!empty($review_student)) {
                $review_student_data = [
                    'rvw_id'         => $rvw_id,
                    'lesson_type'    => $review_data['lesson_type'],
                    'cid'            => $review_data['cid'],
                    'lid'            => $review_data['lid'],
                    'int_day'        => $review_data['int_day'],
                    'int_start_hour' => $review_data['int_start_hour'],
                    'int_end_hour'   => $review_data['int_end_hour'],
                    'eid'            => $review_data['eid'],
                    'review_style'   => $review_data['review_style']
                ];

                foreach ($review_student as $k=>$per_review_student) {
                    $mReviewStudent = new ReviewStudent();
                    $per_review_student = array_merge($per_review_student, $review_student_data);
                    if ($per_review_student['review_style'] == 1){
                        $detail = [];
                        array_copy($detail,$per_review_student,['detail','honor','weak0','weak1','weak2','weak3','weak4','score0','score1','score2','score3','score4']);
                        $per_review_student['detail'] = $detail;
                        $rs = $mReviewStudent->data([])->isUpdate(false)->allowField(true)->save($per_review_student);
                    }else{
                        $rs = $mReviewStudent->data([])->isUpdate(false)->allowField(true)->save($per_review_student);
                    }
                    $review_student[$k]['rs_id'] = $mReviewStudent->rs_id;
                    if ($rs === false) return $this->user_error('点评学生失败');
                    if(isset($per_review_student['sid'])) {
                        add_service_record('review', ['sid' => $per_review_student['sid'], 'st_did' => 232]);

                        if(isset($per_review_student['score'])) {
                            //点评积分
                            $hook_data = [
                                'hook_action' => 'review',
                                'sid' => $per_review_student['sid'],
                                'star' => $per_review_student['score'],
                            ];
                            Hook::listen('handle_credit', $hook_data);
                        }
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

    //删除一个点评
    public function delOneReview($rvw_id, $review = null)
    {
        if(is_null($review)) {
            $review = $this->findOrFail($rvw_id);
        }

        try {
            $this->startTrans();
            $rs = (new ReviewStudent())->where('rvw_id', $rvw_id)->delete();
            if ($rs === false) throw new FailResult('删除学生个人点评失败');

            //$rs = ReviewFile::destroy(['rvw_id', $rvw_id]);
            $rs = (new ReviewFile())->where('rvw_id', $rvw_id)->delete();
            if ($rs === false) throw new FailResult('删除点评文件记录失败');

            $rs = $review->delete();
            if ($rs === false) throw new FailResult('删除点评失败');

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    //课评发送微信通知
    public function wechat_tpl_notify($sid,$rs_id,$delay = 0)
    {
        $wechat = Wechat::getInstance();
        $message['appid'] = $wechat->appid;
        $scene = 'review_push';
        $default_template_setting = config('tplmsg')[$scene];
        //$message['url'] = $default_template_setting['weixin']['url'];//todo  替换[host]和业务[id]
        $w_rs['sid'] = $sid;
        $w_rs['rvw_id'] = $this->getAttr('rvw_id');
        $m_rs = new ReviewStudent();
        $rs = $m_rs->where($w_rs)->find();
        $rs_id = $rs['rs_id'];
        $url_data['rs_id'] = $rs_id;
        $message['url'] = tplmsg_url($default_template_setting['weixin']['url'],$url_data);
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
        $temp['lesson_name']    = $this->getAttr('lesson')['lesson_name'];
        //$temp['create_time']      = $this->getAttr('create_time');
        $temp['create_time'] = int_day_to_date_str($this->getData('int_day')) . ' ' . int_hour_to_hour_str($this->getData('int_start_hour'))
            . '-' . int_hour_to_hour_str($this->getData('int_end_hour'));
        $temp['ename'] = $this->getAttr('employee')['ename'];

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
        $inner_message['business_id'] = $rs_id;//$this->getAttr('rvw_id');
        $inner_message['title']   = $default_template_setting['message']['title'];
        $inner_message['content'] = str_replace($search, $replace, $default_template_setting['message']['content']);
        $inner_message['url']     = $message['url'];
        foreach ($user_list as $user) {
            $inner_message['uid'] = $user['uid'];
            Message::create($inner_message);
            if ($user['mobile'] && $user_template_setting['sms_switch']) {
                queue_push('SendSmsMsg', [$user['mobile'], $sms_message],null,$delay);
            }
            if ($user['openid'] && $user_template_setting['weixin_switch']) {
                $w = [];
                $w['openid'] = $user['openid'];
                $w['subscribe'] = WxmpFans::SUBSCRIBE;
                if (WxmpFans::get($w)) {
                    $message['openid'] = $user['openid'];
                    queue_push('SendWxTplMsg', $message,null,$delay);
                }
            }
        }
    }

    /**
     * 推送
     * @param $sid
     * @param $frvw_id
     * @return bool
     */
    public function pushReview($sid,$rs_id, $delay = 0)
    {
        $mMessage = new Message();
        $scene = 'review_push';
        $student_info = get_student_info($sid);
        $default_template_setting = config('tplmsg');
        $mReviewStudent = new ReviewStudent();
        $review_student = $mReviewStudent->get($rs_id);
        $url_data['rs_id'] = $rs_id;
        $user_template_setting = isset(Config::userConfig()['wechat_template'][$scene]) ? Config::userConfig()['wechat_template'][$scene] : null;
        if (empty($user_template_setting)) {
            //客户如果没有设置公众号的模板消息的first字段、remark字段和颜色的设置，则使用系统默认的公众号的设置
            $user_template_setting = $default_template_setting[$scene];
        }
        $this->startTrans();
        try {
            $task_data['lesson_name'] = get_course_name_by_row($review_student);
            $task_data['create_time'] = int_day_to_date_str($review_student['int_day']) . ' ' . int_hour_to_hour_str($review_student['int_start_hour']) . '-' . int_hour_to_hour_str($review_student['int_end_hour']);
            $task_data['ename'] = get_employee_name($review_student['eid']);
            $task_data['rvw_id'] = $review_student['rvw_id'];
            $task_data['subject'] = '课后评价提醒';
            $task_data['url'] = tplmsg_url($default_template_setting[$scene]['weixin']['url'],$url_data);

            $replace = array_values($task_data);
            $search  = array_values($user_template_setting['tpl_fields']);

            $inner_message = [];
            $inner_message['og_id'] = $student_info['og_id'];
            $inner_message['bid'] = $student_info['bid'];
            $inner_message['sid'] = $student_info['sid'];
            $inner_message['business_type'] = $scene;
            $inner_message['business_id'] = $review_student['rvw_id'];
            $inner_message['title'] = $default_template_setting[$scene]['message']['title'];
            $inner_message['content'] = str_replace($search, $replace, $default_template_setting[$scene]['message']['content']);
            $inner_message['url'] = tplmsg_url($default_template_setting[$scene]['weixin']['url'],$url_data);

            $w_us['sid'] = $sid;
            $w_us['is_delete'] = 0;
            $us_list = db('user_student')->where($w_us)->select();
            if (empty($us_list)) {
                return $this->user_error('学员未开启学习管家账号!');
            }

            foreach ($us_list as $us){
                $task_data['uid'] = $us['uid'];
                $inner_message['uid'] = $us['uid'];
                Message::create($inner_message);
                $rs = $mMessage->sendTplMsg($scene,$task_data ,[],2,$delay);
                if($rs === false) return $this->user_error($mMessage->getErrorMsg());
            }
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

}