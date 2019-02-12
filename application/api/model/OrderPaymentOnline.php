<?php
/**
 * Author: luo
 * Time: 2018-03-06 15:36
**/

namespace app\api\model;

use EasyWeChat\Foundation\Application;
use app\wxapi\controller\SqbPay;
use util\Sqb;

class OrderPaymentOnline extends Base
{
    const STATUS_DEFAULT = 0; # 默认状态，初始下单
    const STATUS_SUCCESS = 1; # 支付成功
    const WXPAY = 1; # 微信支付
    const ZFBPAY = 2; # 支付宝支付
    const SQBPAY = 3; # 收钱吧支付


    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid', 'pay_result'];

    public function getPayTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i', $value) : '';
    }

    public function oneOrder()
    {
        return $this->hasOne('Order', 'oid', 'oid');
    }

    public function orderPaymentHistory()
    {
        return $this->hasOne('OrderPaymentHistory', 'oph_id', 'oph_id');
    }

    public function updateWhenWechatCallback($out_trade_no, $data)
    {
        $payment = $this->where('out_trade_no', $out_trade_no)->find();
        if (!empty($payment)) {
            $rs = $payment->allowField(true)->isUpdate(true)->save($data);
            if($rs === false) return $this->user_error('客户订单状态更新失败');
        }

        return true;
    }

    public function updatePayment($opo_id, $payment = null, $data)
    {
        if(is_null($payment)) {
            $payment = $this->find($opo_id);
        }

        $rs = $payment->allowField(true)->save($data);
        if($rs === false) return $this->user_error('更新在线付款订单信息失败');

        return true;
    }

    // 根据aa_id取得支付配置
    public function getWxmpOptionsByAaId($aa_id)
    {
        $config_pay = AccountingAccount::getConfigByAaId($aa_id);
        if(empty($config_pay)) return '帐户没有相应的支付配置';
        $config = $config_pay->getData('config');
        $config = json_decode($config, true);
        if(empty($config['merchant_id'])) return '先设置商户号';
        if(empty($config['key'])) return '先设置商户号密钥';
        if(empty($config['cert_path'])) return '先设置商户号支付证书文件';
        if(empty($config['key_path'])) return '先设置商户号私钥证书文件';

        $request = request();
        $options = [
            'app_id' => $config_pay['appid'],
            'payment' => [
                'merchant_id' => $config['merchant_id'],
                'key' => $config['key'],
                'cert_path' => $config['cert_path'],
                'key_path' => $config['key_path'],
                'notify_url' => $request->domain() . '/wxapi/wxpay/callback',
            ],
        ];

        return $options;
    }

    //查询微信的订单接口，更新订单支付状态，主要是主动扫用户条形码的支付状态更新
    public function updateByOutTradeNo($out_trade_no, OrderPaymentOnline $payment_online = null)
    {
        if(is_null($payment_online)) {
            $payment_online = $this->where('out_trade_no', $out_trade_no)->find();
        }

        if(empty($payment_online)) return $this->user_error('没有相关的在线支付订单');


        if ($payment_online['type'] == 1){
            $options = $this->getWxmpOptionsByAaId($payment_online['aa_id']);
            if(!is_array($options)) return $this->user_error(400, $options);

            $app = new Application($options);
            $payment = $app->payment;
            $result = $payment->query($out_trade_no);
            if($result->trade_state == 'SUCCESS') {
                $payment_online->status = 1;
                $payment_online->transaction_id = $result->transaction_id;
                $payment_online->pay_result = json_encode($result);
                $payment_online->pay_time = strtotime($result->time_end);
                $rs = $payment_online->save();
                if($rs === false) return $this->user_error('更新在线支付订单失败');

                return true;
            } else {
                return false;
            }
        }elseif ($payment_online['type'] == 2){
            //  支付宝支付暂未开通
            return false;
        }elseif ($payment_online['type'] == 3){
            $mSqbpay = new SqbPay();
            $options = $mSqbpay->getSqbOptionsByAaId($payment_online['aa_id']);
            if(!is_array($options)) return $this->user_error(400, $options);

            $mSqb = new Sqb();
            $result = $mSqb->query($payment_online['transaction_id'],$out_trade_no,$options['terminal_sn'],$options['terminal_key']);

            if ($result['biz_response']['data']['order_status'] == 'PAID'){
                $payment_online->status = 1;
                $payment_online->pay_result = json_encode($result);
                $payment_online->pay_time = intval(substr($result['biz_response']['data']['channel_finish_time'],0,-3));
                $rs = $payment_online->save();
                if($rs === false) return $this->user_error('更新在线支付订单失败');

                return true;
            } else {
                return false;
            }

        }else{
            return false;
        }



    }

}