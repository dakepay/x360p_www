<?php


namespace app\common\job;

use think\Log;
use think\Config;
use think\queue\Job;
use app\common\Export;

class DataExport
{
    /**
     * 异步导出文件
     * @param Job $job
     * @param $data
     */
    public function fire(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
            Log::record("<warn>DataExport Job has been retried more than 3 times!"."</warn>\n", 'error');
            $job->delete();
            // 也可以重新发布这个任务
        }
        $isJobDone = $this->export($data);
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            Log::record("<info>DataExport Job has been done and deleted"."</info>\n", 'debug');
        } else {
            $job->release(60);
        }

    }

    /**
     * @param $data
     * @return bool
     */
    public function export($data)
    {
        if(empty($data['params'])) return true;
        Config::set('database',$data['database']);
        $input = $data['params'];
        $de = db('data_export')->find($input['de_id']);
        $cid = $input['cid'];
        $og_id = $de['og_id'];
        $uid = $de['create_uid'];
        $filename = date('YmdHis',$de['create_time']);
        $instance = Export::Load($input['params']['res_name'],$input['params']);
        $rel_path = sprintf('data/export/%s/%s/%s/%s.xls',$cid,$og_id,$uid,$filename);
        $real_path = PUBLIC_PATH.$rel_path;
        if(!is_dir(dirname($real_path))){
            mkdirss(dirname($real_path));
        }
        try {
            ini_set('memory_limit','2G');
            set_time_limit(0);
            $instance->asyncExport($real_path,1000);
        }catch(\Exception $e){
            Log::record("dataexport error:".$e->getMessage(),"error");
        }
        $w['de_id'] = $input['de_id'];
        $update['file_url'] = $rel_path;
        db('data_export')->where($w)->update($update);
        return true;
    }

}