<?php
/**
 * Author: luo
 * Time: 2018-08-04 15:00
**/

namespace app\common\job;

use app\api\model\Employee;
use app\api\model\WebcallCallLog;
use think\Log;
use think\Config;
use think\queue\Job;
use util\Webcall;

class WebcallAudio
{
    /**
     * 下载电话通信记录
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据 ['token' => 18921223153]
     */
    public function fire(Job $job, $data)
    {
        $isJobDone = $this->download($data);
        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
            Log::record("<warn>WebcallAudio Job has been retried more than 3 times!"."</warn>\n", 'error');
            $job->delete();
            // 也可以重新发布这个任务
        }
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            Log::record("<info>WebcallAudio Job has been done and deleted"."</info>\n", 'debug');
        } else {
            $job->release(60);
        }

    }

    public function download($data)
    {
        if(empty($data['token'])) return true;
        Config::set('database',$data['database']);
        if(isset($data['client'])){
            gvar('client',$data['client']);
        }
        if(isset($data['og_id'])){
            gvar('og_id',$data['og_id']);
        }

        $token = $data['token'];
        $m_wcl = new WebcallCallLog();
        $log = $m_wcl->skipOgId()->where('token', $token)->find();
        if($log['file_id'] > 0) return true;
        $pos = strrpos($log['recordurl'], '/voip/record/download');
        if($pos != false) {
            $download_path = substr($log['recordurl'], $pos);
            $client = new Webcall(config('webcall.appkey'), config('webcall.appsecret'));
            $audio_path = $client->downloadAudio($download_path,
                ['callid' => $log['callid']]);

            if(is_string($audio_path)) {
                $file_data = [
                    'local_file' => $audio_path,
                    'rel_id' => $log['eid'],
                    'create_uid' => Employee::getUidByEid($log['eid']),
                ];
                $file_id = $m_wcl->saveFile($file_data, true);
                $log->file_id = $file_id['file_id'];
                $log->file_url = $file_id['file_url'];
                $rs = $log->save();
                if($rs === false) return false;

                $center_call_log_conn = db('webcall_call_log', 'db_center');
                $center_wcl = $center_call_log_conn->where('token', $token)->find();
                $center_w['wcl_id'] = $center_wcl['wcl_id'];
                $update_center_wcl = [
                    'file_id' => $file_id['file_id'],
                    'file_url' => $file_id['file_url'],
                ];
                $rs = $center_call_log_conn->where($center_w)->update($update_center_wcl);
                if ($rs === false) exception('callback_arrive_times error');
                return true;
            }
        }

        return true;
    }

}