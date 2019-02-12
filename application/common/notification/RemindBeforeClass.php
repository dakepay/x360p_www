<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/9/2
 * Time: 11:50
 */
namespace app\common\notification;

use app\api\model\Message;
use think\Log;

/**
 * 课前提醒推送，推送课前提醒消息，通知家长记得上课和注意事项。
 * Class PreparePush
 * @package app\common\notification
 */
class RemindBeforeClass extends Notification
{
    protected $template_name = 'remind_before_class';

    public function wechatPush()
    {
        $tpl_data = $this->template_config['weixin'];
        $content['lesson_name'] = $this->business->getAttr('lesson')->lesson_name;
        $content['address']     = $this->business->getAttr('branch')->address;
        $content['mobile']      = $this->business->getAttr('teacher')->mobile;
        $content['school_time'] = $this->business->formatSchoolTime();
        $content['remark']      = $this->business->remind_message or '请提前15分钟到';//todo

        $tpl_data['wxmp']    = $this->wxmp->toArray();
        $tpl_data['client']  = config('g_client');

        $students = $this->business->getAttr('cls')->students;
        if (empty($students)) {
            $this->error = '没有查询到该班级关联学生,没有可推送对象';
            return false;
        }
        foreach ($students as $student) {
            $content['student_name'] = $student->student_name;
            $parents = $student->user;
            foreach ($parents as $parent) {
                if (empty($parent['openid'])) {
                    continue;
                }
                $tpl_data['openid'] = $parent['openid'];
                $search = array_values($this->template_config['tpl_fields']);
                /*动态生成模板消息的url(这里不需要模板链接)*/
//                array_push($search, '{base_url}');
//                array_push($search, '{ca_id}');
                $replace = array_values(array_merge($this->template_config['tpl_fields'], $content));
//                array_push($replace, request()->domain());
//                array_push($replace, $this->business->ca_id);
                queue_push('SendWxTplMsg', str_replace_json($search, $replace, $tpl_data));
//                $this->sendWechatTplMsg($tpl_data);
            }
            /*添加站内消息*/
            $message_field = [];
            $message_field['sid'] = $student['sid'];
            $message_field['business_id'] = $this->business['ca_id'];
            $message_field['content'] = $content;
            $this->addInternalMessage($message_field);
        }
        return true;
    }

    public function smsPush()
    {

    }

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