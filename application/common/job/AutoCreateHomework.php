<?php

namespace app\common\job;

use app\api\model\Homework;
use think\Log;
use think\queue\Job;

class AutoCreateHomework
{
    /**
     * fire方法是消息队列默认调用的方法
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
        $isJobDone = $this->doJob($data);

        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
//            $job->delete();
            Log::record("<info>" . __CLASS__ . " Job has been done"."</info>\n");
        } else {
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                Log::record("<warn>Hello Job has been retried more than 3 times!"."</warn>\n");
//                $job->delete();
                // 也可以重新发布这个任务
                Log::record("<info>Hello Job will be availabe again after 10s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
        $job->release(60);
    }

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function doJob($data)
    {
        Log::record("<info>AutoCreateHomework doingJob</info>");
        Log::record($data, 'debug');
        $result = Homework::autoCreateHomework();
        return $result;
    }
}