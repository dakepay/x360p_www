<?php
namespace app\common\job;

use think\queue\Job;

/**
 * 统一的任务处理类
 * @desc $data = ['class' => 'SendSms', 'mobile' => '18316227457']; queue_push('Base', $data);
 */
class Base
{
    /**
     * @param Job $job
     * @param $data
     */
    public function fire(Job $job, $data)
    {

        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
            log_write("<warn>Handle Job has been retried more than 3 times!"."</warn>\n", 'error');
            $job->delete();
        }
        $isJobDone = $this->handle($job, $data);
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
        }
    }


    private function handle($job, $data)
    {
        if(empty($data['class'])) {
            log_write('任务未知要执行的类', 'error');
            log_write($data, 'error');
            return true;
        }

        $job_class = __NAMESPACE__ . '\\' . $data['class'];
        if(class_exists($job_class) && method_exists($job_class, 'fire')) {
            try {
                if(empty($data['database'])) exception('队列任务没有相关数据库信息');
                \think\Config::set('database', $data['database']);
                (new $job_class())->fire($job, $data);
            } catch(\Exception $e) {

                log_write($job_class . ' fire error: ' . $e->getMessage() . ' file:' . $e->getFile() . ' line:'.$e->getLine(), 'error');
            }
        } else {
            log_write($job_class . ' not exist or fire method not exist', 'error');
            return true;
        }

        return true;
    }
}