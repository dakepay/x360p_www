<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/8/17
 * Time: 15:34
 */
namespace app\common\job;

use think\Log;
use think\queue\Job;
use util\Email;

/**
 * 发送微信模板消息
 */
class SendEmail
{
    /**
     * fire方法是消息队列默认调用的方法
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
        $isJobDone = $this->send($data);

        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            Log::record("<info>Hello Job has been done and deleted"."</info>\n");
        } else {
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                Log::record("<warn>Hello Job has been retried more than 3 times!"."</warn>\n");
                $job->delete();
                // 也可以重新发布这个任务
                //Log::record("<info>Hello Job will be availabe again after 2s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function send($data)
    {
        Log::record("<info>SendEmail doingJob</info>");
        Log::record($data, 'debug');
        $mail = (new Email())->getMail();
        //todo
        $mail->addAddress($data['address']);
        $mail->Subject = $data['subject'];
        $mail->Body = $data['body'];
        $result = $mail->send();
        if ($result) {
            return true;
        } else {
            $msg = '发送邮件失败，错误信息为：' . $mail->ErrorInfo;
            Log::write($msg, 'log');
            return false;
        }
    }
}