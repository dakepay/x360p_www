<?php
namespace app\common\job;

use think\queue\Job;
use think\Config;
use think\Log;


/**
 *      每天自动推送任务
 */
class AutoPushCourseRemind
{
    /**
     * fire方法是消息队列默认调用的方法
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据
     */
    public function fire(Job $job,$data)
    {
        $queue_config = include(CONF_PATH.'extra/queue.php');
        Config::set('queue',$queue_config);
        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
            Log::record("<warn>AutoPushCourseRemind Job has been retried more than 3 times!"."</warn>\n", 'error');
            $job->delete();
            $this->doAutoNextCourseRemindJob($data);
            // 也可以重新发布这个任务
        }
        $isJobDone = $this->doAutoPushCourseRemindJob($data);
        if ($isJobDone) {
            $job->delete();
            $this->doAutoNextCourseRemindJob($data);
            //如果任务执行成功， 记得删除任务

            Log::record("<info>AutoPushCourseRemind Job has been done and deleted"."</info>\n", 'debug');
        } else {
            $job->release(60);
        }

    }


    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    public function doAutoPushCourseRemindJob($data)
    {
        // 根据消息中的数据进行实际的业务处理...
        if(!isset($data['database'])){
            return false;
        }
        Config::set('database', $data['database']);
        gvar('og_id',$data['og_id']);
        gvar('bid',$data['bid']);

        try {
            $mCourseArrange = new \app\api\model\CourseArrange();
            $course_arrange_list = $mCourseArrange->autoOneDayCourseArrange($data['day'], $data['bid']);
            if (!$course_arrange_list) {
                return true;
            }
            $result = $mCourseArrange->remind_course($course_arrange_list);
            if (false === $result) {
                return false;
            }

            if ($data['is_push_teacher'] == 1){
                $course_list = $mCourseArrange->autoOneDayCourseTeachers($data['day'], $data['bid']);
                if (!$course_list){
                    return true;
                }
                $result = $mCourseArrange->AutoRemindTeacher($course_list);
                if (false === $result) {
                    return false;
                }
            }

        }catch(\Exception $e){
            echo('error:'.$e->getMessage);
            Log::record("doAutoPushCourseRemindJob error:".$e->getMessage()."\n", 'error');
            return false;
        }
        return true;
    }


    /**
     * 根据消息中的数据自动创建议案一次推送任务
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    public function doAutoNextCourseRemindJob($data){
        $next_data = [
            "job" => "app\common\job\AutoPushCourseRemind",
            'og_id' => $data['og_id'],
            'bid' => $data['bid'],
            'day' => $data['day'],
            'is_push_teacher' => $data['is_push_teacher'],
            'task_id' => $data['task_id'],
        ];
        $delay = 86400;

        queue_push('AutoPushCourseRemind', $next_data, 'AutoPushCourseRemind', $delay, $data['task_id']);
    }
}