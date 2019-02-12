<?php
/**
 * Author: luo
 * Time: 2018/3/7 14:45
 */

namespace app\Wxopen\controller;


use app\api\model\AccountingAccount;
use app\common\Wechat;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use think\Controller;
use think\Request;

class Test extends Controller
{


    public function index(Request $request)
    {
        //$url = isset($_GET['url']) ? $_GET['url'] : isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        //
        //$kv_k = '0:JSAPI:'.md5($url);
        //
        //$config = cache($kv_k);
        //
        //if(!$config){
        //    $js = Wechat::getApp('wx1389af8071e3b6f5')->js;
        //    if($url){
        //        $js->setUrl($url);
        //    }
        //    $api_list = array(
        //        'checkJsApi',
        //        'onMenuShareTimeline',
        //        'onMenuShareAppMessage',
        //        'onMenuShareQQ',
        //        'onMenuShareWeibo',
        //        'hideAllNonBaseMenuItem',
        //        'showAllNonBaseMenuItem',
        //        'chooseImage',
        //        'previewImage',
        //        'uploadImage',
        //    );
        //
        //    $config = $js->config($api_list, true, false, false);
        //
        //    cache($kv_k,$config,7200);
        //}
        //
        //$ret_type = isset($_GET[config('var_jsonp_handler')])?'JSONP':'JSON';
        //
        //$this->assign('config', json_encode($config));
        $input = $request->param();
        $json = $this->js_pay($input);
        //halt($json);
        $this->assign('json', $json);

        return view();

    }


     /*
     * 生成微信扫码支付的二维码
     */
    public function js_pay($input) {

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
        $rs = $this->validate($input, $rule);
        if($rs !== true) {
            echo 'luo-------1';exit;
        }

        $student_name = '';

        $options = $this->getWxmpOptionsByAaId($input['aa_id']);
        if(!is_array($options)) {
            echo 'luo-------2';exit;
        }

        $app = new Application($options);

        $client = gvar('client');
        if(empty($client)) {
            echo 'luo-------3';exit;
        }
        $cid = $client['cid'];
        $og_id = $client['og_id'];

        $body = input('body', $student_name . '报名缴费');
        $out_trade_no = $cid . '_' . $og_id . '_' . time() . '_' . rand(0, 1000000);
        $total_fee = $input['paid_amount'];
        $attributes = [
            'openid'            => 'oD5_L1JvlVICdSt_hHqgmWTJOhGQ',
            'trade_type'       => 'JSAPI',
            'body'             => $body,
            'detail'           => $body,
            'out_trade_no'     => $out_trade_no,
            'total_fee'        => $total_fee * 100, // 单位：分
            'notify_url'       => (\request())->domain() . '/wxapi/wxpay/callback', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
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
        $json = $payment->configForPayment($result->prepay_id); // 返回 json 字符串，如果想返回数组，传第二个参数 false

        return $json;
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



}