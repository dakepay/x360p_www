<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/9/5
 * Time: 11:00
 */
namespace app\common\notification;

use app\api\model\Message;
use think\Log;

class AlterClassTime extends Notification
{
    protected $config_name = 'alter_class_time';

    public function wechatPush()
    {
        $tpl_data = $this->config['weixin'];

        /*班级名称*/
        $content['class_name'] = $this->business->getAttr('cls')->class_name;
        /*调课原因*/
        $content['alter_reason'] = $this->business->getData('alter_reason');
        /*新的上课时间*/
        $content['class_time'] = $this->business->formatSchoolTime();

        $students = $this->business->getAttr('cls')->students;
        if (empty($students)) {
            $this->error = '没有查询到该班级关联学生,没有可推送对象';
            return false;
        }
        foreach ($students as $student) {
            /*学生姓名*/
            $content['student_name'] = $student['student_name'];
            $parents = $student->user;
            foreach ($parents as $parent) {
                if (empty($parent['openid'])) {
                    continue;
                }
                $tpl_data['openid'] = $parent['openid'];
                $search = array_values($this->config['tpl_fields']);
                $replace = array_values(array_merge($this->config['tpl_fields'], $content));
                $tpl_data = str_replace_json($search, $replace, $tpl_data);
//                queue_push('SendWxTplMsg', $tpl_data);
                $this->sendWechatTplMsg($tpl_data);
            }

            /*添加站内消息*/
            $message_field['sid'] = $student['sid'];
            $message_field['content'] = $content;
            $this->addInternalMessage($message_field);
        }
        return true;
    }

    protected function addInternalMessage($field)
    {
        $data['bid'] = $this->business['bid'];
        $data['cid'] = $this->business['cid'];
        $data['business_type'] = $this->config['tpl_id'];
        $data['business_id'] = $this->business['ca_id'];
        $data['send_mode'] = $this->getSendMode();
        $data['title'] = $this->config['name'];
        $data = array_merge($data, $field);
        Message::create($data);
    }


    public function smsPush()
    {

    }
}