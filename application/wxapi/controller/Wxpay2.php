<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/8/2
 * Time: 13:02
 */
namespace app\wxapi\controller;

use app\api\model\StudentUser;
use think\Request;
use EasyWeChat\Payment\Order;

class Wxpay extends Weapp
{
    //是否开启授权认证
    public $apiAuth = true;

    //不需要认证的action
    protected $withoutAuthAction = ['callback'];

    public function js_api_call(Request $request)
    {
        $order_no = $request->param('order_no');
        $uid = $request->user->uid;
        $where['order_no'] = $order_no;
        $where['uid'] = $uid;
        $m_order = m('order')->where($where)->find();
        if (!$m_order || $m_order['pay_status'] == 1) {
            return $this->sendError(400, '订单不存在或已支付');
        }

        $orderItems = $m_order->orderItems;
        $body = [];
        $detail = [];
        foreach ($orderItems as $item) {
            $body[] = $item->goods->title;
            $detail[] = $item->goods->short_desc;
        }

        $user = StudentUser::get($uid);
        $openid = $user->openid;
        $attributes = [
            'trade_type'       => 'JSAPI',
            'body'             => join('\n', $body),
            'detail'           => join('\n', $detail),
            'out_trade_no'     => $m_order['order_no'],
            'total_fee'        => $m_order['order_amount'] * 100, // 单位：分
            'notify_url'       => $request->domain() . '/wxapi/wxpay/callback', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid'           => $openid, // 当前用户的open_id， trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。
        ];
        $order = new Order($attributes);
        $payment = $this->wxapp->payment;
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
            $jsConfig = $payment->configForPayment($prepayId);
            return $this->sendSuccess(json_decode($jsConfig));
        }
        return $this->sendError(400, 'error', 400, $result);
    }

    public function callback()
    {
        $response = $this->wxapp->payment->handleNotify(function($notify, $successful){
            $order_no = $notify->out_trade_no;
            $order = m('order')->where('order_no', $order_no)->find();
            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            if ($order->pay_status) {
                return true;
            }
            if ($successful) {
                $order->payment($notify);
            } else {
                $order->pay_status = 2; //支付失败
            }
            $order->save();
            return true;
        });
        return $response;
    }

    public function dev(Request $request)
    {
        $order_no = $request->param('order_no');
        $order = m('order')->where('order_no', $order_no)->find();
        if (!$order) { // 如果订单不存在
            return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
        }
        if ($order->pay_status) {
            return '订单已支付过了';
        }

        $result = $order->payment(false);
        return $result;
    }
}