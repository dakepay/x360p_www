<?php

namespace app\vipapi\controller;

use think\Controller;
use think\Request;
use payment\alipay as alipayCls;
use app\common\Center;
use app\vipapi\model\Order;
use think\Log;

class Alipay extends Controller
{
    protected $c_pay = null;

    protected function verify(){
        $alipay_config  = Center::old_alipay_config(APP_DEBUG);

        $extra_config   = array(
            // 即时到账方式
            'payment_type' => 1,
            // 传输协议
            'transport' => 'http',
            // 编码方式
            'input_charset' => 'utf-8',
            // 签名方法
            'sign_type' => 'MD5',
            // 支付完成异步通知调用地址
            'notify_url' => 'https://vip.pro.xiao360.com/vipapi/Alipay/notify',
            // 支付完成同步返回地址
            'return_url' => 'https://vip.pro.xiao360.com/vipapi/Alipay/return'
        );

        $alipay_config = array_merge($alipay_config,$extra_config);


        $this->c_pay = new alipayCls($alipay_config);

        $result = $this->c_pay->verifyCallback();

        if(!$result){
           return 'fail';
        }

        return true;
    }

    public function Notify(){

        $result = $this->verify();

        if($result !== true){
            return $result;
        }

        $receive_data = $this->filterParameter($_POST);

        $w['order_no'] = $receive_data['out_trade_no'];

        $order = Order::get($w);

        if(!$order){
            return 'fail';
        }

        $message = "alipay notify content:\nget:\n".print_r($_GET,true)."\npost:\n".print_r($_POST,true);

        Log::write($message,Log::INFO);

        $result = false;
        if($receive_data['trade_status'] == 'TRADE_FINISHED'){
            $result = $order->payResult($receive_data,'alipay');
        }elseif($receive_data['trade_status'] == 'TRADE_SUCCESS'){
            $result = $order->payResult($receive_data,'alipay');
        }

        if(!$result){
            return 'fail';
        }

       return 'success';
    }

    public function Return(){
        $result = $this->verify();

        if($result !== true){
            return $result;
        }

        $w['order_no'] = $_GET['out_trade_no'];

        $order = Order::get($w);

        if(!$order){

            return 'fail';
        }

        $redirect_url = 'https://vip.pro.xiao360.com/#/alipaysuccess';
        
        return $this->redirect($redirect_url);
    }

    /**
    * 返回字符过滤
    * @param $parameter
    */
    private function filterParameter($parameter){
        $para = array();
        foreach ($parameter as $key => $value)
        {
            if ('sign' == $key || 'sign_type' == $key || '' == $value || 'm' == $key  || 'a' == $key  || 'c' == $key   || 'code' == $key ) continue;
            else $para[$key] = $value;
        }
        return $para;
    }
}