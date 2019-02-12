<?php 

namespace app\common\job;

use think\Log;
use think\queue\Job;
use think\Db;
use think\Config;

/**
 * 班级排课时创建排课学生记录 CourseArrangeStudent
 */
class AddCourseArrangeStudents
{
    /**
       * fire方法是消息队列默认调用的方法
       * @param Job            $job      当前的任务对象
       * @param array|mixed    $data     发布任务时自定义的数据
       */
	public function fire(Job $job,$data)
	{
		    // 如有必要,可以根据业务需求和数据库中的最新数据,判断该任务是否仍有必要执行.
        $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        if(!$isJobStillNeedToBeDone){
            $job->delete();
            return;
        }

        $isJobDone = $this->doAddCourseArrangeStudentsJob($data);
        if($isJobDone){
        	  $job->delete();
        	  Log::record("<info>AddStudents Job has been done and delete"."</info>\n",'debug');
        }else{
        	  if($job->attempts() > 3){
        		    Log::record("<warn>AddStudents Job has been retired more than 3 times!"."</warn>\n","debug");
        		    $job->delete();
          	}
        }


	}


  	/**
      * 有些消息在到达消费者时,可能已经不再需要执行了
      * @param array|mixed    $data     发布任务时自定义的数据
      * @return boolean                 任务执行的结果
      */
    private function checkDatabaseToSeeIfJobNeedToBeDone($data){
        return true;
    }


    /**
      * 根据消息中的数据进行实际的业务处理
      * @param array|mixed    $data     发布任务时自定义的数据
      * @return boolean                 任务执行的结果
      */
    private function doAddCourseArrangeStudentsJob($data)
    {
        // 根据消息中的数据进行实际的业务处理...
        if(!isset($data['database'])){
            return false;
        }
        if(!isset($data['client'])){
            return false;
        }
        Config::set('database', $data['database']);
        gvar('client',$data['client']);
        gvar('og_id',$data['og_id']);

        $ca_id  = $data['ca_id'];

        $mCourseArrange = new \app\api\model\CourseArrange();

        $m_ca = $mCourseArrange->find($ca_id);

        if(!$m_ca){
            return false;
        }

        $m_ca->getAttObjects(0, true, false);
        return true;
    }



}