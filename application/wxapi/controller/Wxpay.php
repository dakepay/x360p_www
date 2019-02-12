<?php
/** 
 * Author: luo
 * Time: 2018-02-27 18:12
**/
namespace app\wxapi\controller;

use app\api\controller\Base;
use app\api\model\AccountingAccount;
use app\api\model\CenterWechatPayOrder;
use app\api\model\Classes;
use app\api\model\ConfigPay;
use app\api\model\Lesson;
use app\api\model\Material;
use app\api\model\OrderItem;
use app\api\model\OrderPaymentOnline;
use app\api\model\OrderPaymentOnlineCode;
use app\api\model\Student;
use app\common\Wechat;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use think\Config;
use think\Db;
use think\Log;
use think\Request;

class Wxpay extends Base
{
    //不需要认证的action
    protected $withoutAuthAction = ['callback', 'js_pay', 'js_auth', 'query_code', 'test', 'openid'];

    private function getWxmpOptions($appid = null)
    {
        if(is_null($appid)) {
            $appid = Wechat::getAppid();
        }

        $config_pay = ConfigPay::get(['appid' => $appid]);
        if(empty($config_pay)) return 'appid没有相应的支付配置';

        $config = $config_pay->getData('config');
        $config = json_decode($config, true);
        if(empty($config['merchant_id'])) return '先设置商户号';
        if(empty($config['key'])) return '先设置商户号密钥';
        if(empty($config['cert_path'])) return '先设置商户号支付证书文件';
        if(empty($config['key_path'])) return '先设置商户号私钥证书文件';

        $request = request();
        $options = [
            'app_id' => $appid,
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

    /*
     * 1. 通过out_trade_no在中心数据库中查找客户的数据库配置
     * 2. 在客户的数据库中查找相应的支付配置
     */
    private function getWxmpOptionsByOutTradeNo($out_trade_no)
    {
        //--1-- 查找订单
        $order = CenterWechatPayOrder::get(['out_trade_no' => $out_trade_no]);
        if(empty($order) || $order['cid'] <= 0 || empty($order['appid'])) return '中心订单不存在';

        //--2-- 连接客户数据库
        $center_db_cfg = Config::get('center_database');
        $db            = Db::connect($center_db_cfg);
        $client_db_config = $db->name('database_config')->where('cid', $order['cid'])->find();
        Config::set('database',$client_db_config);

        //--3-- 查询客户的支付配置
        $config_pay = ConfigPay::get(['appid' => $order['appid']]);
        if(empty($config_pay)) return 'appid没有相应的支付配置';
        $config = $config_pay->getData('config');
        $config = json_decode($config, true);
        if(empty($config['merchant_id'])) return '先设置商户号';
        if(empty($config['key'])) return '先设置商户号密钥';
        if(empty($config['cert_path'])) return '先设置商户号支付证书文件';
        if(empty($config['key_path'])) return '先设置商户号私钥证书文件';

        $request = request();
        $options = [
            'app_id' => $order['appid'],
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

    /*
     * 根据aa_id取得支付配置
    */
    private function getWxmpOptionsByAaId($aa_id)
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

    /*
     * 生成微信扫码支付的二维码地址
     */
    public function swipe(Request $request) {
       
        /*
         $options = [
             'app_id' => 'wx1389af8071e3b6f5',

             'payment' => [
                 'merchant_id'        => '1486940732',
                 'key'                => 'xiao360test3leqaseawwqakbsieqals',
                 'cert_path'          => CONF_PATH . DS . 'wechat_pay' . DS . 'apiclient_cert.pem',  # 绝对路径！！！！
                 'key_path'          => CONF_PATH . DS . 'wechat_pay' . DS . 'apiclient_key.pem',    # 绝对路径！！！！
                 'notify_url'         => $request->domain() . '/wxapi/wxpay/callback',       // 你也可以在下单时单独设置来想覆盖它
             ],
         ];
        */
        $rule = [
            'aa_id|帐户id' => 'require',
            'paid_amount|金额' => 'require|min:0.01',
            'oid|订单号' => 'require|number',
        ];
        $input = $request->param();
        $rs = $this->validate($input, $rule);
        if($rs !== true) return $this->sendError(400, $rs);

        $order = \app\api\model\Order::get($input['oid']);
        if(empty($order)) return $this->sendError(400, '订单不存在');

        //--1-- 获取支付码，手机端输入码支付
        $m_opoc = new OrderPaymentOnlineCode();
        $code = $m_opoc->produceCode($input['oid'], $input['aa_id'], $input['paid_amount']);
        if($code === false) return $this->sendError(400, $m_opoc->getErrorMsg());

        //--2-- 支付二维码如果已经生成过，则不再生成
        $oid = input('oid');
        $m_opo = new OrderPaymentOnline();
        $order_online = $m_opo->where('create_time', 'gt', time()-120*60)->where('oid', $oid)
            ->where('paid_amount', input('paid_amount'))->where('trade_type', 'NATIVE')
            ->where('status = 0')->order('create_time desc')->find();
        if(!empty($order_online) && !empty($order_online['code_url'])) {
            return $this->sendSuccess(['code_url' => $order_online['code_url'], 'code' => $code]);
        }

        //--3-- 学生名字作为支付内容一部份
        $student_name = '';
        if(!empty($order['sid'])) {
            $student = Student::get($order['sid']);
            $student_name = $student['student_name'];
        }

        $options = $this->getWxmpOptionsByAaId($input['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        $app = new Application($options);

        $client = gvar('client');
        if(empty($client)) return $this->sendError(400, '客户信息不存在');
        $cid = $client['cid'];
        $og_id = $client['og_id'];

        $body = input('body', $student_name . '报名缴费');
        $out_trade_no = $cid . '_' . $og_id . '_' . time() . '_' . rand(0, 1000000);
        $total_fee = $input['paid_amount'];
        $attributes = [
            'trade_type'       => 'NATIVE',
            'body'             => $body,
            'detail'           => $body,
            'out_trade_no'     => $out_trade_no,
            'total_fee'        => $total_fee * 100, // 单位：分
            'notify_url'       => $request->domain() . '/wxapi/wxpay/callback', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
        ];
        $order = new Order($attributes);
        $payment = $app->payment;
        /*
            object(EasyWeChat\Support\Collection)#207 (1) {
              ["items":protected] => array(10) {
                ["return_code"] => string(7) "SUCCESS"
                ["return_msg"] => string(2) "OK"
                ["appid"] => string(18) "wx1389af8071e3b6f5"
                ["mch_id"] => string(10) "1486940732"
                ["nonce_str"] => string(16) "cZWfvrvhqmP61gCl"
                ["sign"] => string(32) "D60D85CC478114454628ED04C191E3E9"
                ["result_code"] => string(7) "SUCCESS"
                ["prepay_id"] => string(36) "wx20180227180315548f4cd0ed0470216069"
                ["trade_type"] => string(6) "NATIVE"
                ["code_url"] => string(35) "weixin://wxpay/bizpayurl?pr=LcTUeNS"
              }
            }
         */
        $result = $payment->prepare($order);
        Log::record($result, 'wechat');
        if ($result->return_code != 'SUCCESS'){
            return $this->sendError(400, $result->return_msg);
        }

        $m_cwpo = new CenterWechatPayOrder();
        $data = [
            'cid' => $cid,
            'og_id' => $og_id,
            'appid' => $options['app_id'],
            'out_trade_no' => $attributes['out_trade_no'],
            'total_fee' => $total_fee, # 元
            'trade_type' => $attributes['trade_type'],
        ];
        $rs = $m_cwpo->allowField(true)->save($data);
        if($rs === false) return $this->sendError(400, '中心订单记录失败');

        $data = [
            'og_id' => $og_id,
            'oid' => $input['oid'],
            'aa_id' => $input['aa_id'],
            'paid_amount' => $input['paid_amount'],
            'out_trade_no' => $attributes['out_trade_no'],
            'code_url' => $result->code_url,
            'trade_type' => $attributes['trade_type'],
        ];
        $rs = $m_opo->allowField(true)->save($data);
        if($rs === false) return $this->sendError(400, '客户订单记录失败');

        return $this->sendSuccess(['code_url' => $result->code_url, 'code' => $code]);
    }

    //微信手机支付
    public function js_pay(Request $request) {
        /*
         $options = [
             'app_id' => 'wx1389af8071e3b6f5',

             'payment' => [
                 'merchant_id'        => '1486940732',
                 'key'                => 'xiao360test3leqaseawwqakbsieqals',
                 'cert_path'          => CONF_PATH . DS . 'wechat_pay' . DS . 'apiclient_cert.pem',  # 绝对路径！！！！
                 'key_path'          => CONF_PATH . DS . 'wechat_pay' . DS . 'apiclient_key.pem',    # 绝对路径！！！！
                 'notify_url'         => $request->domain() . '/wxapi/wxpay/callback',       // 你也可以在下单时单独设置来想覆盖它
             ],
         ];
        */
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

        $options = $this->getWxmpOptionsByAaId($code_info['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        $app = new Application($options);

        $client = gvar('client');
        if(empty($client)) return $this->sendError(400, '客户信息不存在');
        $cid = $client['cid'];
        $og_id = $client['og_id'];

        $body = input('body', $student_name . '报名缴费');
        $out_trade_no = $cid . '_' . $og_id . '_' . time() . '_' . rand(0, 1000000);
        $total_fee = $code_info['paid_amount'];
        $attributes = [
            'openid'            => $openid,
            'trade_type'       => 'JSAPI',
            'body'             => $body,
            'detail'           => $body,
            'out_trade_no'     => $out_trade_no,
            'total_fee'        => $total_fee * 100, // 单位：分
            'notify_url'       => $request->domain() . '/wxapi/wxpay/callback', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
        ];
        $order = new Order($attributes);
        $payment = $app->payment;
        /*
            object(EasyWeChat\Support\Collection)#207 (1) {
              ["items":protected] => array(10) {
                ["return_code"] => string(7) "SUCCESS"
                ["return_msg"] => string(2) "OK"
                ["appid"] => string(18) "wx1389af8071e3b6f5"
                ["mch_id"] => string(10) "1486940732"
                ["nonce_str"] => string(16) "cZWfvrvhqmP61gCl"
                ["sign"] => string(32) "D60D85CC478114454628ED04C191E3E9"
                ["result_code"] => string(7) "SUCCESS"
                ["prepay_id"] => string(36) "wx20180227180315548f4cd0ed0470216069"
                ["trade_type"] => string(6) "NATIVE"
                ["code_url"] => string(35) "weixin://wxpay/bizpayurl?pr=LcTUeNS"
              }
            }
         */
        $result = $payment->prepare($order);
        Log::record($result, 'wechat');
        if ($result->return_code != 'SUCCESS'){
            return $this->sendError(400, $result->return_msg);
        }

        //存储订单信息
        $m_cwpo = new CenterWechatPayOrder();
        $data = [
            'cid' => $cid,
            'og_id' => $og_id,
            'appid' => $options['app_id'],
            'out_trade_no' => $attributes['out_trade_no'],
            'total_fee' => $total_fee, # 元
            'trade_type' => $attributes['trade_type'],
        ];
        $rs = $m_cwpo->allowField(true)->save($data);
        if($rs === false) return $this->sendError(400, '中心订单记录失败');

        $m_opo = new OrderPaymentOnline();
        $data = [
            'og_id' => $og_id,
            'oid' => $code_info['oid'],
            'aa_id' => $code_info['aa_id'],
            'paid_amount' => $code_info['paid_amount'],
            'out_trade_no' => $attributes['out_trade_no'],
            'trade_type' => $attributes['trade_type'],
            'type' => OrderPaymentOnline::WXPAY
        ];
        $rs = $m_opo->allowField(true)->save($data);
        if($rs === false) return $this->sendError(400, '客户订单记录失败');

        $json = $payment->configForPayment($result->prepay_id); // 返回 json 字符串，如果想返回数组，传第二个参数 false
        $return_data = [
            'json' => $json,
            'paid_amount' => $code_info['paid_amount'],
            'order' => $this->order_detail($code_info['oid']),
        ];
        return $this->sendSuccess($return_data);
    }

    //通过支付码，取得用户openid, 用于下一步js支付
    public function js_auth(Request $request)
    {
        $code = trim(input('code'));
        if(empty($code)) {
            return $this->sendError(400, '支付码错误');
        }

        $m_opoc = new OrderPaymentOnlineCode();
        $code_info = $m_opoc->where('code', $code)->find();
        if(empty($code_info)) return $this->sendError(400, '支付码没有对应的订单信息2');

        $order = \app\api\model\Order::get($code_info['oid']);
        if(empty($order)) return $this->sendError(400, '订单不存在');

        $options = $this->getWxmpOptionsByAaId($code_info['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        $app = Wechat::getApp($options['app_id']);

        $client = gvar('client');
        if(empty($client)) return $this->sendError(400, '客户信息不存在');
        $cid = $client['cid'];
        if($cid <= 0) return $this->sendError(400, '客户id信息不存在');

        $domain = config('ui')['domain'];
        redis()->set($cid.'_'.$code, $request->domain());
        $redirect_url = $request->scheme() . '://' . $domain . '/wxapi/wxpay/openid/input_code/'.$code
            .'/cid/'.$cid;
        $app->oauth->scopes(['snsapi_base'])->redirect($redirect_url)->send();
        exit;

    }

    //支付码用户授权之后，获取用户openid
    public function openid()
    {
        $code = input('input_code');    # 用户输入的支付码
        $cid = input('cid');

        $center_db_cfg = Config::get('center_database');
        $db            = Db::connect($center_db_cfg);
        $client_db_config = $db->name('database_config')->where('cid', $cid)->find();
        if(empty($client_db_config)) return $this->sendError(400, '客户数据库信息不存在');
        Config::set('database',$client_db_config);

        if(empty($code)) {
            return $this->sendError(400, '支付码错误');
        }

        $m_opoc = new OrderPaymentOnlineCode();
        $code_info = $m_opoc->where('code', $code)->find();
        if(empty($code_info)) return $this->sendError(400, '支付码没有对应的订单信息3');

        $order = \app\api\model\Order::get($code_info['oid']);
        if(empty($order)) return $this->sendError(400, '订单不存在');

        $options = $this->getWxmpOptionsByAaId($code_info['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        $app = Wechat::getApp($options['app_id']);
        $wechat_user = $app->oauth->user();

        $redirect_url = redis()->get($cid.'_'.$code);
        $redirect_url .= '/school#/wxpay?code=' . $code . '&openid=' . $wechat_user['id'] . '&host='.$client_db_config['host'];

        header('Location:'. $redirect_url);
        exit;
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

        $options = $this->getWxmpOptionsByAaId($code_info['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        return $this->sendSuccess();
    }

    //暂存前端的地址，用于微信授权后的跳转
    public function cookie_referer($reset = false, Request $request = null)
    {
        if(!$reset) {
            $referer = cookie('referer');
            return $referer;
        }

        $params = $request->param();
        $referer = $request->header('referer');
        foreach($params as $key => $val) {
            $params_arr[] = $key . '=' . $val;
        }
        $params_str = !empty($params_arr) ? implode('&', $params_arr) : '';
        $referer .= '?' . $params_str;

        cookie('referer', $referer);

        return $referer;
    }

    public function order_detail($oid)
    {
        if(!is_numeric($oid)) return false;
        $order = \app\api\Model\Order::get(['oid' => $oid], ['student']);

        $item_list = OrderItem::all(['oid' => $order['oid']]);
        $list = [];

        foreach($item_list as $item) {

            /* 计算剩余次数 */
            if($item instanceof OrderItem) {
                $item = OrderItem::getItemNumsCondition($item);
            }

            $gid = $item->gid;
            $cid = $item->cid;
            $lid = $item->lid;

            /* 物品 */
            $item['material'] = Material::get(['mt_id' => $gid]);

            $item['lesson'] = Lesson::get($lid);

            /* 班级 */
            $item['one_class'] = Classes::get(['cid' => $cid]);

            $item['receipt_bill_item'] = $item->getItemPaymentHis($item);
            $item = $item->toArray();
            array_push($list, $item);
        }

        $order['order_items'] = $list;

        return $order;
    }

    //收银扫码枪支付
    public function swipe_bar_code(Request $request) {

        //--1-- 验证数据
        $rule = [
            'aa_id|帐户id' => 'require',
            'paid_amount|金额' => 'require|min:0.01',
            'oid|订单号' => 'require|number',
            'auth_code|二维码' => 'require|number',
        ];
        $input = $request->param();
        $rs = $this->validate($input, $rule);
        if($rs !== true) return $this->sendError(400, $rs);

        $order = \app\api\model\Order::get($input['oid']);
        if(empty($order)) return $this->sendError(400, '订单不存在');

        $student_name = '';
        if(!empty($order['sid'])) {
            $student = Student::get($order['sid']);
            $student_name = $student['student_name'];
        }

        //--2-- 获取支付配置信息
        $options = $this->getWxmpOptionsByAaId($input['aa_id']);
        if(!is_array($options)) return $this->sendError(400, $options);

        $app = new Application($options);

        $client = gvar('client');
        if(empty($client)) return $this->sendError(400, '客户信息不存在');
        $cid = $client['cid'];
        $og_id = $client['og_id'];

        //--3-- 设置下单信息
        $body = input('body', $student_name . '报名缴费');
        $out_trade_no = $cid . '_' . $og_id . '_' . time() . '_' . rand(0, 1000000);
        $total_fee = $input['paid_amount'];
        $auth_code = input('auth_code');
        $attributes = [
            'body'             => $body,
            'detail'           => $body,
            'out_trade_no'     => $out_trade_no,
            'total_fee'        => $total_fee * 100, // 单位：分
            'auth_code'        => $auth_code
        ];
        $order = new Order($attributes);

        $payment = $app->payment;
        $result = $payment->pay($order);
        /*
       "object(EasyWeChat\Support\Collection)#255 (1) {
             [\"items\":protected] => array(9) {
               [\"return_code\"] => string(7) \"SUCCESS\"
               [\"return_msg\"] => string(2) \"OK\"
               [\"appid\"] => string(18) \"wx1389af8071e3b6f5\"
               [\"mch_id\"] => string(10) \"1486940732\"
               [\"nonce_str\"] => string(16) \"vzSy0v23c9znpAfs\"
               [\"sign\"] => string(32) \"F9674E82A9F899A7F4621C713ED5BB69\"
               [\"result_code\"] => string(4) \"FAIL\"
               [\"err_code\"] => string(10) \"USERPAYING\"
               [\"err_code_des\"] => string(30) \"需要用户输入支付密码\"
             }
            }";
        */

        $m_cwpo = new CenterWechatPayOrder();
        $m_opo = new OrderPaymentOnline();
        //中心数据库在线订单数据
        $center_data = [
            'cid' => $cid,
            'og_id' => $og_id,
            'appid' => $options['app_id'],
            'out_trade_no' => $attributes['out_trade_no'],
            'total_fee' => $total_fee, # 元
            'trade_type' => 'MICROPAY',
        ];

        //客户数据库在线订单数据
        $client_data = [
            'og_id' => $og_id,
            'oid' => $input['oid'],
            'aa_id' => $input['aa_id'],
            'out_trade_no' => $attributes['out_trade_no'],
            'paid_amount' => $input['paid_amount'],
            'trade_type' => 'MICROPAY',
        ];

        Log::record($result, 'wechat');
        if($request->result_code != 'SUCCESS') {
            $rs = $m_cwpo->allowField(true)->save($center_data);
            if($rs === false) return $this->sendError(400, '中心订单记录失败');

            $rs = $m_opo->allowField(true)->save($client_data);
            if($rs === false) return $this->sendError(400, '客户订单记录失败');

            return $this->sendSuccess($result->err_code_des);

        } else {

            $center_data['status'] = 1;
            $rs = $m_cwpo->allowField(true)->save($center_data);
            if($rs === false) return $this->sendError(400, '中心订单记录失败');

            $client_data['status'] = 1;
            $client_data['pay_result'] = json_encode($request);
            $rs = $m_opo->allowField(true)->save($client_data);
            if($rs === false) return $this->sendError(400, '客户订单记录失败');

            return $this->sendSuccess('付款成功');
        }



    }

    /*
        [ wechat ] EasyWeChat\Support\Collection::__set_state(array(
    14     'items' =>
    15    array (
    16      'appid' => 'wx1389af8071e3b6f5',
    17      'bank_type' => 'CFT',
    18      'cash_fee' => '1',
    19      'fee_type' => 'CNY',
    20      'is_subscribe' => 'Y',
    21      'mch_id' => '1486940732',
    22      'nonce_str' => '5a9622f87035c',
    23      'openid' => 'oD5_L1JvlVICdSt_hHqgmWTJOhGQ',
    24      'out_trade_no' => '1_0_1519788792_867922',
    25      'result_code' => 'SUCCESS',
    26      'return_code' => 'SUCCESS',
    27      'sign' => 'C03410731CBAE37AB3EBEF0083147F6A',
    28      'time_end' => '20180228113838',
    29      'total_fee' => '1',
    30      'trade_type' => 'NATIVE',
    31      'transaction_id' => '4200000069201802289764889140',
    32    ),
    33  ))
     */
    public function callback(Request $request)
    {
        //--1-- 微信通知数据
        $request_data = $request->getInput();
        if(empty($request_data)) return false;
        Log::record($request->getInput(), 'wechat');
        $request_data = $this->xml_to_array($request->getInput());
        if(!$request_data) return false;

        //--2-- 获取客户支付配置
        $options = $this->getWxmpOptionsByOutTradeNo($request_data['out_trade_no']);
        if(!is_array($options)) {
            Log::record($options, 'wechat');
            return false;
        }
        $app = new Application($options);
        $payment = $app->payment;

        $response = $payment->handleNotify(function($notify, $successful) {

            //--2.1-- 更新中心数据库订单
            $m_cwpo = new CenterWechatPayOrder();
            $order = $m_cwpo->where('out_trade_no', $notify->out_trade_no)->find();
            if (empty($order)) {
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            if ($notify->return_code != 'SUCCESS') {
                $update_data = ['status' => $m_cwpo::STATUS_FAIL];
            } else {
                $update_data = ['status' => $m_cwpo::STATUS_SUCCESS];
            }

            $rs = $order->save($update_data);
            if ($rs === false) {
                Log::record($this->makeLog($notify->out_trade_no, '中心订单状态更新失败'), 'wechat');
                return false;
            }

            //--2.2-- 更新客户订单
            $client_info_arr = explode('_', $notify->out_trade_no);
            $cid = $client_info_arr[0];

            $center_db_cfg = Config::get('center_database');
            $db            = Db::connect($center_db_cfg);
            $client_db_config = $db->name('database_config')->where('cid', $cid)->find();
            Config::set('database',$client_db_config);

            if($update_data['status'] == $m_cwpo::STATUS_SUCCESS) {
                $opo = new OrderPaymentOnline();
                $update_data = [
                    'status' => $m_cwpo::STATUS_SUCCESS,
                    'transaction_id' => $notify->transaction_id,
                    'pay_result' => json_encode($notify),
                    'pay_time' => time(),
                ];
                $rs = $opo->updateWhenWechatCallback($notify->out_trade_no, $update_data);
                if($rs === false) {
                    Log::record($this->makeLog($notify->out_trade_no, $opo->getError()), 'wechat');
                    return false;
                }
            }

            return true;
        });

        $response->send();
    }

    private function makeLog($no, $msg)
    {
        return sprintf('out_trade_no:%s,%s', $no, $msg);
    }

    public function xml_to_array($xml){
        if(!$xml){
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

}