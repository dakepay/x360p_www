<?php

namespace app\wxapi\controller;

use app\api\controller\Base;
use app\api\model\AccountingAccount;
use app\api\model\ConfigPay;
use app\api\model\Order;
use app\api\model\OrderPaymentOnline;
use app\api\model\OrderPaymentOnlineCode;
use app\api\model\Student;
use think\Request;
use util\Sqb;

class SqbPay extends Base
{
//    public $apiAuth = false;

    protected $withoutAuthAction = ['callback'];
    const STATUS_DEFAULT = 0; # 默认状态，初始下单
    const STATUS_SUCCESS = 1; # 支付成功


    /*
    * 生成微信扫码支付的二维码地址
    */
    public function swipe(Request $request)
    {
        $rule = [
            'aa_id|帐户id' => 'require',
            'paid_amount|金额' => 'require|min:0.01',
            'oid|订单号' => 'require|number',
            'payway|支付方式' => 'require|number',
        ];
        $input = $request->param();
        $rs = $this->validate($input, $rule);
        if($rs !== true) return $this->sendError(400, $rs);

        $order = \app\api\model\Order::get($input['oid']);
        if(empty($order)) return $this->sendError(400, '订单不存在');

        $m_opoc = new OrderPaymentOnlineCode();
        $code = $m_opoc->produceCode($input['oid'], $input['aa_id'], $input['paid_amount']);
        if($code === false) return $this->sendError(400, $m_opoc->getErrorMsg());

        //支付二维码如果已经生成过，则不再生成
        $m_opo = new OrderPaymentOnline();
        $order_online = $m_opo->where(['out_trade_no' => $order['order_no']])->find();
        if(!empty($order_online) && !empty($order_online['code_url'])) {
            return $this->sendSuccess(['qr_code' => $order_online['code_url'], 'code' => $code]);
        }

        //学生名字作为支付内容一部份
        $student_name = '';
        if(!empty($order['sid'])) {
            $student = Student::get($order['sid']);
            $student_name = $student['student_name'];
        }
        $options = $this->getSqbOptionsByAaId($input['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);
        $client = gvar('client');
        if(empty($client)) return $this->sendError(400, '客户信息不存在');
        $og_id = $client['og_id'];
        $body = input('body', $student_name . '报名缴费');

        $params = [
            'terminal_sn'       =>  $options['terminal_sn'],
            'client_sn'         =>  $order['order_no'],
            'total_amount'      =>  sprintf($input['paid_amount'] * 100), // 单位：分,
            'payway'            =>  $input['payway'] , // 1:支付宝3:微信4:百度钱包5:京东钱包6:qq钱包
            'subject'           =>  $body,
            'operator'          =>  sprintf($order['create_uid']),
            'notify_url'        => $request->domain() . '/wxapi/sqb/callback', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
        ];

        $mSqb = new Sqb();
        $result = $mSqb->precreate($params,$options['terminal_sn'],$options['terminal_key']);
        if ($result['result_code'] != 200){
            return $this->sendError(400, $result['error_message'].':'.$result['error_code']);
        }

        $data = [
            'og_id' => $og_id,
            'oid' => $input['oid'],
            'aa_id' => $input['aa_id'],
            'paid_amount' => $input['paid_amount'],
            'status' => self::STATUS_DEFAULT,
            'out_trade_no' => $params['client_sn'],
            'code_url' =>$result['biz_response']['data']['qr_code'],
            'transaction_id'  => $result['biz_response']['data']['sn'],
            'type' => OrderPaymentOnline::SQBPAY
        ];
        $rs = $m_opo->allowField(true)->save($data);
        if($rs === false) return $this->sendError(400, '客户订单记录失败');
        $result['biz_response']['data']['code'] = $code;
        return $this->sendSuccess($result['biz_response']['data']);
    }

    /*
    * 根据aa_id取得支付配置
    */
    public function getSqbOptionsByAaId($aa_id)
    {
        $config_pay = AccountingAccount::getConfigByAaId($aa_id);
        if(empty($config_pay)) return '帐户没有相应的支付配置';
        $config = $config_pay->getData('config');
        $config = json_decode($config, true);

        if(empty($config['merchant_sn'])) return 'merchant_sn不存在';
        if(empty($config['store_sn'])) return 'store_sn不存在';
        if(empty($config['terminal_sn'])) return 'terminal_sn不存在';
        if(empty($config['terminal_key'])) return 'terminal_key不存在';

        return $config;
    }

    //查询支付码
    public function query_code()
    {
        $code = trim(input('code'));
        if(empty($code)) {
            return $this->sendError(400, '支付码错误');
        }

        $m_opoc = new OrderPaymentOnlineCode();
        $code_info = $m_opoc->where('code', $code)->find();
        if(empty($code_info)) return $this->sendError(400, '支付码没有对应的订单信息4');

        $order = \app\api\model\Order::get($code_info['oid']);
        if(empty($order)) return $this->sendError(400, '订单不存在');

        $options = $this->getSqbOptionsByAaId($code_info['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        return $this->sendSuccess();
    }

    //微信手机支付
    public function js_pay(Request $request) {

        $code = trim(input('code'));
        $openid = trim(input('openid'));
        if(empty($code) || empty($openid)) {
            return $this->sendError(400, '支付码或者openid错误');
        }

        $m_opoc = new OrderPaymentOnlineCode();
        $code_info = $m_opoc->where('code', $code)->find();
        if(empty($code_info)) return $this->sendError(400, '支付码没有对应的订单信息');

        $order = \app\api\model\Order::get($code_info['oid']);
        if(empty($order)) return $this->sendError(400, '订单不存在');
        $student_name = '';
        if(!empty($order['sid'])) {
            $student = Student::get($order['sid']);
            $student_name = $student['student_name'];
        }

        $options = $this->getSqbOptionsByAaId($code_info['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        $body = input('body', $student_name . '报名缴费');
        $params = [
            'terminal_sn'       =>  $options['terminal_sn'],
            'client_sn'         =>  $order['order_no'],
            'total_amount'      =>  sprintf($input['paid_amount'] * 100), // 单位：分,
            'payway'            =>  $input['payway'] , // 1:支付宝3:微信4:百度钱包5:京东钱包6:qq钱包
            'subject'           =>  $body,
            'operator'          =>  sprintf($order['create_uid']),
            'notify_url'        => $request->domain() . '/wxapi/sqb/callback', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'sub_payway'        => 3,
            'payer_uid'         => $openid
        ];

        $mSqb = new Sqb();
        $result = $mSqb->precreate($params,$options['terminal_sn'],$options['terminal_key']);
        if ($result['result_code'] != 200){
            return $this->sendError(400, $result['error_message'].':'.$result['error_code']);
        }

        $data = [
            'og_id' => $og_id,
            'oid' => $input['oid'],
            'aa_id' => $input['aa_id'],
            'paid_amount' => $input['paid_amount'],
            'status' => self::STATUS_DEFAULT,
            'out_trade_no' => $params['client_sn'],
            'code_url' =>$result['biz_response']['data']['qr_code'],
            'transaction_id'  => $result['biz_response']['data']['sn'],
            'type' => OrderPaymentOnline::SQBPAY
        ];
        $rs = $m_opo->allowField(true)->save($data);
        if($rs === false) return $this->sendError(400, '客户订单记录失败');
        $result['biz_response']['data']['code'] = $code;
        return $this->sendSuccess($result['biz_response']['data']);
    }


    //  激活
    public function activate(Request $request)
    {
        $rule = [
            'app_id|app_id' => 'require',
            'code|验证码' => 'require|number',
            'device_id|唯一设备号' => 'require|number',
        ];
        $input = $request->param();
        $rs = $this->validate($input, $rule);
        if($rs !== true) return $this->sendError(400, $rs);

        $webcall_config = config('shouqianba');
        $vender_sn     = $webcall_config['vender_sn'];
        $vender_key  = $webcall_config['vender_key'];
        $mSqb = new Sqb();
        $result = $mSqb->activate($input['app_id'],$input['code'],$input['device_id'],$vender_sn,$vender_key);

        if ($result['result_code'] != 200){
            return $this->sendError(400, $result['error_message']);
        }

        // 激活后数据更新
        $mConfigPay = new ConfigPay();
        $rs = $mConfigPay->where(['appid' => $input['app_id']])->find();
        if (!$rs) {
            return $this->sendError(400, '支付配置不存在');
        }
        $rs = $mConfigPay->updateTerminal($rs['cp_id'],$result['biz_response']);
        if (!$rs) {
            return $this->sendError(400, '支付配置更新失败');
        }

        return $this->sendSuccess($result['biz_response']);
    }

    //  签到
    public function checkin(Request $request)
    {
        $app_id = input('app_id');
        $m_cp = new ConfigPay();
        $rs = $m_cp->where(['appid' => $app_id])->find();
        if (!$rs) {
            return $this->user_error('支付配置不存在' . $rs->getError());
        }

        $options = $this->getSqbOptionsByAaId($app_id);
        if(!is_array($options)) return $this->sendError(400, $options);

        $mSqb = new Sqb();
        $device_id = substr($app_id,-4);
        $result = $mSqb->checkin($device_id,$options['terminal_sn'],$options['terminal_key']);
        if ($result['result_code'] != 200){
            return $result['error_message'];
        }

        // 激活后数据更新
        $rs = $m_cp->updateTerminal($rs['cp_id'],$result['biz_response']);
        if (!$rs) {
            return $this->user_error('支付配置更新失败' . $rs->getError());
        }

        return $result;
    }

    //   支付回调
    public function callback(Request $request)
    {
        //回调写入日志
        $log_path = ROOT_PATH.'public/data/sqb_log/' . date('Ymd');
        if(!file_exists($log_path)) {
            mkdirss($log_path, 0777, true);
        }
        $source_params = "request_url: " . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . "\n";
        $source_params .= file_get_contents('php://input');
        file_put_contents($log_path . '/' .date('Ymd') . '.log', '[' . date('Y-m-d H:i:s') . ']' . $source_params . "\n", FILE_APPEND);

        $request_data = $request->getInput();
        $res = json_decode($request_data,true);

        if ($res['order_status'] != 'PAID'){
            return $this->sendError(400,$res['order_status']);
        }

        $res = $this->update_order($res);
        if (!$res){
            return $this->sendError(400,'订单更新失败');
        }

        return $this->sendSuccess($res);
    }

    //  支付
//    public function pay($terminal_sn, $terminal_key,$params )
//    {
//        $url = self::API_DOMAIN . "/upay/v2/pay";
//
//        $ret = $this->pre_do_execute($params, $url, $terminal_sn, $terminal_key);
//
//        return $ret;
//    }

    //  退款
    public function refund(Request $request)
    {
        $order_no = input('order_no');

        $m_opo = new OrderPaymentOnline();
        $w['out_trade_no'] = $order_no;
        $opo_info = $m_opo->where($w)->find();

        if(!$opo_info) {
            return $this->sendError(400, '支付信息不存在');
        }
        $options = $this->getSqbOptionsByAaId($opo_info['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        $mSqb = new Sqb();
        $result = $mSqb->refund($opo_info['transaction_id'],$order_no,$options['terminal_sn'],$options['terminal_key']);

        if ($result['result_code'] != 200){
            return $this->sendError(400, $result['error_message'].':'.$result['error_code']);
        }
        $pay_info = $result['biz_response']['data'];

        if ($pay_info['order_status'] != 'PAID'){
            return $this->sendError(400,$pay_info['order_status']);
        }

        $res = $this->update_order($pay_info);
        if (!$res){
            return $this->sendError(400,'订单更新失败');
        }

        return $this->sendSuccess($pay_info);
    }

    //  自动撤单
    public function cancel(Request $request)
    {
        $order_no = input('order_no');
        $mOpo = new Order();
        $w['iod'] = $order_no;
        $opo_info = $mOpo->where($w)->find();

        if(!$opo_info) {
            return $this->sendError(400, '支付信息不存在');
        }

        $options = $this->getSqbOptionsByAaId($opo_info['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        $mSqb = new Sqb();
        $result = $mSqb->cancel($opo_info['transaction_id'],$order_no,$options['terminal_sn'],$options['terminal_key']);

        if ($result['result_code'] != 200){
            return $this->sendError(400, $result['error_message'].':'.$result['error_code']);
        }
        $pay_info = $result['biz_response']['data'];

        if ($pay_info['order_status'] != 'PAID'){
            return $this->sendError(400,$result['biz_response']['error_message']);
        }

        $res = $this->update_order($pay_info);
        if (!$res){
            return $this->sendError(400,'订单更新失败');
        }

        return $this->sendSuccess($pay_info);
    }

    // 主动撤单
    public function revoke($terminal_sn, $terminal_key)
    {
        $order_no = input('order_no');
        $mOpo = new Order();
        $w['iod'] = $order_no;
        $opo_info = $mOpo->where($w)->find();

        if(!$opo_info) {
            return $this->sendError(400, '支付信息不存在');
        }

        $options = $this->getSqbOptionsByAaId($opo_info['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        $mSqb = new Sqb();
        $result = $mSqb->cancel($opo_info['transaction_id'],$order_no,$options['terminal_sn'],$options['terminal_key']);

        if ($result['result_code'] != 200){
            return $this->sendError(400, $result['error_message'].':'.$result['error_code']);
        }
        $pay_info = $result['biz_response']['data'];

        if ($pay_info['order_status'] != 'PAID'){
            return $this->sendError(400,$result['biz_response']['error_message']);
        }

        $res = $this->update_order($pay_info);
        if (!$res){
            return $this->sendError(400,'订单更新失败');
        }

        return $this->sendSuccess($pay_info);
    }

    //  查找
    public function query(Request $request)
    {
        $order_no = input('order_no');
        $mOpo = new OrderPaymentOnline();
        $w['out_trade_no'] = $order_no;
        $opo_info = $mOpo->where($w)->find();

        if(!$opo_info) {
            return $this->sendError(400, '支付信息不存在');
        }

        $options = $this->getSqbOptionsByAaId($opo_info['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        $mSqb = new Sqb();
        $result = $mSqb->query($opo_info['transaction_id'],$order_no,$options['terminal_sn'],$options['terminal_key']);

        if ($result['result_code'] != 200){
            return $this->sendError(400, $result['error_message'].':'.$result['error_code']);
        }
        $pay_info = $result['biz_response']['data'];

        if ($pay_info['order_status'] != 'PAID'){
            return $this->sendError(400,$result['biz_response']['error_message']);
        }

        $res = $this->update_order($pay_info);
        if (!$res){
            return $this->sendError(400,'订单更新失败');
        }

        return $this->sendSuccess($pay_info);
    }

    //  订单支付更新订单信息
    public function update_order($pay_info){

        $mOrderPaymentOnline = new OrderPaymentOnline();
        $opo_update = [
            'status' => self::STATUS_SUCCESS,
            'pay_result' => json_encode($pay_info),
            'pay_time' => intval(substr($pay_info['channel_finish_time'],0,-3)),
        ];

        $rs = $mOrderPaymentOnline->updateWhenWechatCallback($pay_info['client_sn'], $opo_update);
        if (!$rs){
            return $this->sendError(400, '订单更新失败');
        }
        return true;
    }


}