<?php
/**
 * Author: luo
 * Time: 2018/7/4 18:41
 */

namespace app\api\controller;


use Curl\Curl;
use think\Request;

class Callback extends Base
{

    /**
     * @desc   回调测试
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {

        $post = $request->post();
        if(empty($post['url']) || empty($post['data'])) {
            return $this->sendError(400, '发送地址或者发送数据为空');
        }

        $api_config = user_config('org_api');
        if(empty($api_config) || empty($api_config['secret'])) return $this->sendError(400, '回调配置错误');

        $post_data = $post['data'];
        $post_data = is_string($post_data) ? json_decode($post_data, true) : $post_data;
        if(empty($post_data)) return $this->sendError(400, '测试数据错误');

        $queue_data['class'] = 'Callback';
        $queue_data['url']   = $post['url'];
        $queue_data['secret'] = $api_config['secret'];
        $queue_data['data'] = $post_data;

        queue_push('Base',$queue_data);


        /*
        $secret = $api_config['secret'];
        $m_callback = new \app\common\job\Callback();
        $post_data = $m_callback->filterData($post_data);
        $post_data = $m_callback->makeSignature($post_data, $secret);

        $curl = new Curl();
        $post_data_type = !empty($data['post_data_type']) ? $data['post_data_type'] : 'json';
        if($post_data_type == 'form') {
            $curl->post($post['url'], $post_data);
        } else {
            $curl->setHeader('Content-Type', 'application/json;charset=UTF-8');
            $curl->post($post['url'], json_encode($post_data));
        }

        if(!$curl->isSuccess()) {
            return $this->sendError(400, $curl->error_message);
        }
        */
        return $this->sendSuccess();
    }

}