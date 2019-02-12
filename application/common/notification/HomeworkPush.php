<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/9/2
 * Time: 11:50
 */
namespace app\common\notification;

use app\api\model\Homework;
use app\api\model\Message;
use think\Log;

class HomeworkPush extends Notification
{
    protected $config_name = 'after_class_push';

    public function run()
    {
        if ($this->config['sms_switch']) {
            $this->smsPush();
        }

        if ($this->config['weixin_switch']) {
            $this->wechatPush();
        }
    }

    public function wechatPush()
    {
        $tpl_data = $this->config['weixin'];
        $content['class_name'] = $this->business->getAttr('cls')->class_name;
        $content['homework_title'] = $this->business->formatSchoolTime() . '的作业';
        $homework = $this->business->getAttr('homework');
        if (empty($homework)) {
            //这一步不能少(!important)
            $homework = Homework::get(['ca_id' => $this->business->getData('ca_id')]);
        }
        $content['homework_content'] = $homework['content'];
        $students = $this->business->getAttr('cls')->students;
        if (empty($students)) {
            $this->error = '没有查询到该班级关联学生,没有可推送对象';
            return false;
        }
        foreach ($students as $student) {
//            $content['student_name'] = $student->student_name;
            $parents = $student->user;
            foreach ($parents as $parent) {
                if (empty($parent['openid'])) {
                    continue;
                }
                $tpl_data['openid'] = $parent['openid'];
                $search = array_values($this->config['tpl_fields']);
                /*动态生成模板消息的url*/
                array_push($search, '{base_url}');
                array_push($search, '{hid}');
                $replace = array_values(array_merge($this->config['tpl_fields'], $content));
                Log::record(request()->domain());
                array_push($replace, request()->domain());
                array_push($replace, $homework['hid']);
                Log::record($tpl_data);
                queue_push('SendWxTplMsg', str_replace_json($search, $replace, $tpl_data));
//                $this->sendWechatTplMsg($tpl_data);
            }

            /*添加站内消息*/
            $message_field['sid'] = $student['sid'];
            $message_field['business_id'] = $homework['hid'];
            $message_field['content'] = $content;
            $this->addInternalMessage($message_field);
        }
        return true;
    }

    public function smsPush()
    {

    }

    /*添加课前推送的站内信*/
    protected function addInternalMessage($field)
    {
        $info['bid'] = $this->business['bid'];
        $info['cid'] = $this->business['cid'];

        $info['business_type'] = $this->config['tpl_id'];
        $info['title'] = $this->config['name'];
        $info['send_mode'] = $this->getSendMode();
        $info = array_merge($info, $field);

        Message::create($info);
    }
}