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
 * 发送微信模板消息
 */
class SendSmsMsg
{
    /**
     * fire方法是消息队列默认调用的方法
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
        $isJobDone = $this->send($data);
        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
            Log::record("<warn>SendWxTplMsg Job has been retried more than 3 times!"."</warn>\n", 'debug');
            $job->delete();
            // 也可以重新发布这个任务
            //Log::record("<info>Hello Job will be availabe again after 2s."."</info>\n");
            //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
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
        list($mobile, $content) = $data;
        $result = sms::Send($mobile, $content);
        Log::write($result, 'log');
        return true;
    }
}