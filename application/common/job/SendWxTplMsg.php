<?php

namespace app\common\job;

use app\common\Wechat;
use think\Log;
use think\queue\Job;

/**
 * 发送微信模板消息
 */
class SendWxTplMsg
{
    public function __construct()
    {
        // $this->wxapp = new Application(wechat_options());
    }

    /**
       * fire方法是消息队列默认调用的方法
       * @param Job            $job      当前的任务对象
       * @param array|mixed    $data     发布任务时自定义的数据
    */
    public function fire(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
            Log::record("<warn>SendWxTplMsg Job has been retried more than 3 times!"."</warn>\n", 'debug');
            $job->delete();
            // 也可以重新发布这个任务
            //Log::record("<info>Hello Job will be availabe again after 2s."."</info>\n");
            //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
        }
        $isJobDone = $this->send($data);
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
        try {
            $notice = Wechat::getApp($data['appid'])->notice;
            $messageId = $notice->send([
                'touser' => $data['openid'],
                'template_id' => $data['template_id'],
                'url' => $data['url'],
                'data' => $data['data'],
            ]);
            Log::record('SendWxTplMsg messageId','debug');
            Log::record($messageId,'debug');
        }catch(\Exception $e){
            Log::record("SendWxTplMsg exception error:".$e->getMessage(),'error');
        }
        if ($messageId) {
            return true;
        }
        return false;
	}
}