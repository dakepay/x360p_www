<?php
/** 
 * Author: luo
 * Time: 2018-07-03 11:11
**/

namespace app\common\job;

use Curl\Curl;
use think\queue\Job;

/**
 * Class SendSms
 * @package app\common\job
 * @desc 阳光渥回调接口任务
 */
class Callback
{
    /*
     * Usage:
     *  $job_data['class'] = 'Callback';
     *  $job_data['url'] = 'http://210994a0.all123.net:8080/neza/message/push';
     *  $job_data['secret'] = '6a2ad446cc883020b730f242c33880f1';
     *  $job_data['data'] = ['sid' => 30]
     *  queue_push('Base', $job_data);
     */
    public function fire(Job $job, $data)
    {
        $isJobDone = $this->curl($data);
        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
            log_write('callback has been retried more than 3 times', 'error');
            $job->delete();
            // 也可以重新发布这个任务
        }
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
        }
    }

    public function curl($data)
    {
        if(empty($data['url']) || empty($data['secret'])) {
            log_write('callback fail with empty url or empty secret', 'error');
            return true;
        }

        $url = $data['url'];
        $secret = $data['secret'];
        $post_data_type = !empty($data['post_data_type']) ? $data['post_data_type'] : 'form';
        $post_data = $data['data'];

        $post_data = $this->filterData($post_data);
        $post_data = $this->makeSignature($post_data, $secret);

        $curl = new Curl();
        if($post_data_type == 'form') {
            $curl->post($url, $post_data);
        } else {
            $curl->setHeader('Content-Type', 'application/json;charset=UTF-8');
            $curl->post($url, json_encode($post_data,JSON_UNESCAPED_UNICODE));
        }
        if(!$curl->isSuccess()) {
            log_write($url, 'error');
            log_write($curl->http_status_code, 'error');
            log_write('callback fail, error: ' . $curl->error_message, 'error');
            return false;
        }

        return true;
    }

    public function makeSignature($data, $secret)
    {
        unset($data['signature']);
        $signature_data = $data;
        $signature_data['secret'] = $secret;
        ksort($signature_data);
        $query_string = http_build_query($signature_data);
        $signature = md5($query_string);

        $data['signature'] = $signature;
        return $data;
    }

    public function filterData($post_data)
    {
        $filter_field =  [
            'create_time', 'update_time', 'is_delete', 'delete_time', 'delete_uid',
        ];
        foreach($post_data as $key => &$value) {
            if(in_array($key, $filter_field)) {
                unset($post_data[$key]);
            }
            if(is_string($value)) {
                $value = trim($value);
            }
        }

        return $post_data;
    }

}