<?php
/**
 * Author: luo
 * Time: 2018-08-10 17:54
 **/

namespace app\common\job;

use app\api\model\Employee;
use app\api\model\Message;
use app\api\model\MessageGroupHistory;
use app\api\model\SmsHistory;
use app\api\model\Student;
use app\api\model\WechatTplDefine;
use app\api\model\WxmpFans;
use app\common\Wechat;
use think\Log;
use think\queue\Job;
use util\sms;

/**
 * 群发消息
 */
class SendGroupMsg
{
    /**
     * fire方法是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param array|mixed $data 发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
        $isJobDone = $this->send($data);
        if($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
            Log::record("<warn>SendGroupMsg Job has been retried more than 3 times!" . "</warn>\n", 'debug');
            $job->delete();
            // 也可以重新发布这个任务
            //Log::record("<info>Hello Job will be availabe again after 2s."."</info>\n");
            //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
        }
        if($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            Log::record("<info>SendGroupMsg Job has been done and deleted" . "</info>\n", 'debug');
        }
    }

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed $data 发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function send($data)
    {
        if(empty($data['mgh_id'])) return true;

        $m_mgh = new MessageGroupHistory();
        $group_history = $m_mgh->where('mgh_id', $data['mgh_id'])->find();
        if(empty($group_history)) return true;

        //发送短信
        if($group_history['type'] == $m_mgh::TYPE_SMS) {
            $m_sh = new SmsHistory();
            $list = $m_sh->where('mgh_id', $data['mgh_id'])->select();
            foreach($list as $row) {
                try {
                    $rs = sms\EasySms::Send($row['mobile'], $row['content'], $row['tpl_id'], $row['tpl_data']);
                    if($rs !== true) exception($rs);
                } catch(\Exception $e) {
                    $m_sh->where('sh_id', $row['sh_id'])->update(['error' => substr($e->getMessage(), 0, 250)]);
                    continue;
                }

                $m_sh->where('sh_id', $row['sh_id'])->update(['status' => 0,'is_sent'=>1]);
            }

        }

        //发送微信
        if($group_history['type'] == $m_mgh::TYPE_WECHAT) {
            $m_message = new Message();
            $list = $m_message->where('mgh_id', $data['mgh_id'])->select();
            $appid = Wechat::getAppid();
            $m_student = new Student();
            $m_employee = new Employee();
            $m_wxmp_fans = new WxmpFans();
            foreach($list as $row) {
                if(!empty($row['sid'])) {
                    $m_student = $m_student->where('sid', $row['sid'])->cache(1)->find();
                    $user_list = $m_student->user;
                } elseif(!empty($row['eid'])) {
                    $employee = $m_employee->where('eid', $row['eid'])->cache(1)->find();
                    $user_list = [$employee->user];
                    //log_write($user_list, 'error');
                } else {
                    exception('既没有sid,也没有eid');
                }

                $tpl_define = WechatTplDefine::get(['tpl_id' => $group_history['tpl_id']], []);
                if(empty($tpl_define)) exception('微信模板不存在'.$group_history['tpl_id']);

                //处理发送内容，颜色
                $send_msg = [];
                $send_msg['first'][] = !empty($group_history['tpl_data']['first']) ? $group_history['tpl_data']['first'] : '';
                $send_msg['first'][] = !empty($tpl_define['tpl_define']['first'][1]) ? $tpl_define['tpl_define']['first'][1] : '#000000';
                $send_msg['remark'][] = !empty($group_history['tpl_data']['remark']) ? $group_history['tpl_data']['remark'] : '';
                $send_msg['remark'][] = !empty($tpl_define['tpl_define']['remark'][1]) ? $tpl_define['tpl_define']['remark'][1] : '#000000';

                foreach($tpl_define['tpl_define']['data'] as $item) {
                    if($item['field'] == '{{keyword1.DATA}}') {
                        $send_msg['keyword1'][] = !empty($group_history['tpl_data']['keyword1.DATA']) ? $group_history['tpl_data']['keyword1.DATA'] : '';
                        $send_msg['keyword1'][] = !empty($item['color']) ? $item['color'] : '#000000';
                    }
                    if($item['field'] == '{{keyword2.DATA}}') {
                        $send_msg['keyword2'][] = !empty($group_history['tpl_data']['keyword2.DATA']) ? $group_history['tpl_data']['keyword2.DATA'] : '';
                        $send_msg['keyword2'][] = !empty($item['color']) ? $item['color'] : '#000000';
                    }
                    if($item['field'] == '{{keyword3.DATA}}') {
                        $send_msg['keyword3'][] = !empty($group_history['tpl_data']['keyword3.DATA']) ? $group_history['tpl_data']['keyword3.DATA'] : '';
                        $send_msg['keyword3'][] = !empty($item['color']) ? $item['color'] : '#000000';
                    }
                    if($item['field'] == '{{keyword4.DATA}}') {
                        $send_msg['keyword4'][] = !empty($group_history['tpl_data']['keyword4.DATA']) ? $group_history['tpl_data']['keyword4.DATA'] : '';
                        $send_msg['keyword4'][] = !empty($item['color']) ? $item['color'] : '#000000';
                    }
                }

                try {
                    if($user_list) {
                        foreach($user_list as $user) {
                            if(empty($user['openid'])) exception('帐号没绑定微信');
                            $is_fans_subscribe = $m_wxmp_fans->isOpenidSubscribe($appid, $user['openid']);
                            if($is_fans_subscribe) {
                                if(empty($notice)) {
                                    $notice = Wechat::getApp($appid)->notice;
                                }
                                $rs = $notice->send([
                                    'touser'        => $user['openid'],
                                    'template_id'   => $tpl_define['tpl_id'],
                                    'url'           => !empty($group_history['tpl_data']['url']) ? $group_history['tpl_data']['url'] : '',
                                    'data'          => $send_msg,
                                ]);
                                if($rs === true) exception('发送失败');
                            } else {
                                exception('没关注公众号');
                            }

                        }
                    }

                } catch(\Exception $e) {
                    $m_message->where('id', $row['id'])->update(['error' => substr($e->getMessage(), 0, 250)]);
                    continue;
                }

                $m_message->where('id', $row['id'])->update(['status' => 0, 'error' => '']);
            }
        }

        $m_mgh::UpdateGroupHistory($data['mgh_id']);

        return true;
    }
}