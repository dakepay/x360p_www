<?php

namespace app\vipapi\controller;
use \Exception;
use think\Controller;
use think\Request;
use think\Log;
use Yansongda\Pay\Pay;

use app\common\Center;
use app\vipapi\model\Order;

class Wxpay extends Controller
{ 
    /**
     * 公众账号ID   appid   是   String(32)  wx8888888888888888  微信分配的公众账号ID（企业号corpid即为此appId）
商户号 mch_id  是   String(32)  1900000109  微信支付分配的商户号
设备号 device_info 否   String(32)  013467007045764 微信支付分配的终端设备号，
随机字符串   nonce_str   是   String(32)  5K8264ILTKCH16CQ2502SI8ZNMTM67VS    随机字符串，不长于32位
签名  sign    是   String(32)  C380BEC2BFD727A4B6845133519F3AD6    签名，详见签名算法
签名类型    sign_type   否   String(32)  HMAC-SHA256 签名类型，目前支持HMAC-SHA256和MD5，默认为MD5
业务结果    result_code 是   String(16)  SUCCESS SUCCESS/FAIL
错误代码    err_code    否   String(32)  SYSTEMERROR 错误返回的信息描述
错误代码描述  err_code_des    否   String(128) 系统错误    错误返回的信息描述
用户标识    openid  是   String(128) wxd930ea5d5a258f4f  用户在商户appid下的唯一标识
是否关注公众账号    is_subscribe    否   String(1)   Y   用户是否关注公众账号，Y-关注，N-未关注，仅在公众账号类型支付有效
交易类型    trade_type  是   String(16)  JSAPI   JSAPI、NATIVE、APP
付款银行    bank_type   是   String(16)  CMC 银行类型，采用字符串类型的银行标识，银行类型见银行列表
订单金额    total_fee   是   Int 100 订单总金额，单位为分
应结订单金额  settlement_total_fee    否   Int 100 应结订单金额=订单金额-非充值代金券金额，应结订单金额<=订单金额。
货币种类    fee_type    否   String(8)   CNY 货币类型，符合ISO4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
现金支付金额  cash_fee    是   Int 100 现金支付金额订单现金支付金额，详见支付金额
现金支付货币类型    cash_fee_type   否   String(16)  CNY 货币类型，符合ISO4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
总代金券金额  coupon_fee  否   Int 10  代金券金额<=订单金额，订单金额-代金券金额=现金支付金额，详见支付金额
代金券使用数量 coupon_count    否   Int 1   代金券使用数量
代金券类型   coupon_type_$n  否   String  CASH    
CASH--充值代金券 
NO_CASH---非充值代金券
并且订单使用了免充值券后有返回（取值：CASH、NO_CASH）。$n为下标,从0开始编号，举例：coupon_type_0
代金券ID   coupon_id_$n    否   String(20)  10000   代金券ID,$n为下标，从0开始编号
单个代金券支付金额   coupon_fee_$n   否   Int 100 单个代金券支付金额,$n为下标，从0开始编号
微信支付订单号 transaction_id  是   String(32)  1217752501201407033233368018    微信支付订单号
商户订单号   out_trade_no    是   String(32)  1212321211201407033568112322    商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|*@ ，且在同一个商户号下唯一。
商家数据包   attach  否   String(128) 123456  商家数据包，原样返回
支付完成时间  time_end    是   String(14)  20141030133525  支付完成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
     */

    public function notify(Request $request)
    {
        $pay = Pay::wechat($this->config());

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！
            Log::debug('Wechat notify:'.print_r($data->all(),true));

            $m_order = Order::get(['order_no'=>$data->out_trade_no]);

            if(!$m_order){
                throw new Exception('订单号不存在:'.$data->out_trade_no);
            }

            $result = $m_order->payResult($data->all(),'wxpay');

            if(!$result){
                throw new Exception($m_order->getError());
            }

            //取消alipay的订单号
            //$this->cancel_alipay_order($data->out_trade_no);

        } catch (Exception $e) {
            Log::error('Wxpay Notify error'.$e->getMessage());
            return 'FAIL';
        }
        
        return $pay->success()->send();// laravel 框架中请直接 `return $pay->success()`
    }


    protected function config(){
        return Center::wxpay_config(APP_DEBUG);
    }

    /**
     * 取消支付宝订单
     * @param  [type] $out_trade_no [description]
     * @return [type]               [description]
     */
    protected function cancel_alipay_order($out_trade_no){
        $pay_config = Center::alipay_config(APP_DEBUG);
        $pay = Pay::alipay($pay_config);
        $pay->cancel(['out_trade_no'=>$out_trade_no]);
    }
}