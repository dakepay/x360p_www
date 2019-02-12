<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/8/17
 * Time: 14:59
 */
namespace app\common\job;

use think\Log;
use think\queue\Job;
use util\sms;

/**
 * 20180608 新的通过多种服务商发送短信的任务
 */
class SendSms
{
    /**
     * 新的通过多种服务商发送短信的任务
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据 ['mobile' => 18921223153, 'content' => '', 'tpl_id' => sss, 'tpl_data' => ['key' => '', 'name' => ''], 'service_name' => null]
     */
    public function fire(Job $job, $data)
    {
        $isJobDone = $this->send($data);
        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
            Log::record("<warn>SendWxTplMsg Job has been retried more than 3 times!"."</warn>\n", 'error');
            $job->delete();
            // 也可以重新发布这个任务
        }
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            Log::record("<info>SendWxTplMsg Job has been done and deleted"."</info>\n", 'debug');
        }
    }

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function send($data)
    {
        if(empty($data['mobile'])) {
            log_write('job 发送短信失败，手机号错误', 'error');
            return false;
        }

        $mobile_list = is_array($data['mobile']) ? $data['mobile'] : [$data['mobile']];
        $content = empty($data['content']) ? '' : $data['content'];
        $tpl_id = empty($data['tpl_id']) ? '' : $data['tpl_id'];
        $tpl_data = empty($data['tpl_data']) ? [] : $data['tpl_data'];
        $service_name = empty($data['service_name']) ? null : $data['service_name'];

        foreach($mobile_list as $mobile) {
            $rs = sms\EasySms::Send($mobile, $content, $tpl_id, $tpl_data, $service_name);
            if($rs !== true) {
                log_write('sms send error ' . $rs, 'error');
                return false;
            }
        }

        return true;
    }
}