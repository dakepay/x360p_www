<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/30
 * Time: 17:48
 */
namespace app\wxapi\controller;

use app\api\controller\Base;
use app\common\Wechat;
use think\Request;
use app\api\model\OrderPaymentOnlineCode;
use app\wxapi\Wxpay;

class Qrcode extends Base
{
    public $apiAuth = false;
    public function index(Request $request)
    {
        $app = Wechat::getApp(input('appid'));
        $qrcode = $app->qrcode;
        $cid = 14;
        $og_id = 0;
        $bid = $request->param('bid', 1);
        $uid = $request->param('uid', 0);
        //todo check
        $data = [];
        $data['cid'] = $cid;
        $data['bid'] = $bid;
        $data['og_id'] = $og_id;
        $data['uid'] = $uid;
        $result = $qrcode->temporary(json_encode($data), 300);
        $ticket = $result->ticket;
        $url = $qrcode->url($ticket);
        return $this->sendSuccess(['url' => $url]);
    }

    public function check_payway(){
        $code = trim(input('code'));
        if(empty($code)) {
            return $this->sendError(400, '支付码错误');
        }

        $m_opoc = new OrderPaymentOnlineCode();
        $code_info = $m_opoc->where('code', $code)->find();
        if(empty($code_info)) return $this->sendError(400, '支付码没有对应的订单信息');

        $account_info = get_accounting_info($code_info['aa_id']);
        $configpay_info = get_config_pay_info($account_info['cp_id']);

        return $this->sendSuccess($configpay_info);
    }
}