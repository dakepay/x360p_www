<?php

namespace app\admapi\controller;

use think\Hook;
use think\Log;
use think\Request;
use think\captcha\Captcha;
use think\config;
use app\admapi\model\User;
use Endroid\QrCode\QrCode;
use util\sms;

/**
 * Class Open
 * @title 开放接口
 * @url /
 * @desc  不需要验证登录的公共接口
 * @version 1.0
 * @readme /md/api/api_open.md
 */
class Open extends Base
{
    public $apiAuth = false;
    public $noRest  = true;
    /**
     * 验证码
     * @return [type] [description]
     */
    public function captcha(Request $request,$name = ''){
        $captcha_config = Config::get('captcha');
       
        if(isset($captcha_config[$name])){
            $captcha_config = $captcha_config[$name];
        }
        $captcha = new Captcha($captcha_config);
        return $captcha->entry($name);
    }
   
    /**
     * 注册
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function signup(Request $request){
        $input = $request->post();
        $result = $this->validate($input, 'User.signup');
        if ($result !== true) {
            return $this->sendError(400,$result);
        }

        unset($input['repassword']);

        $user = model('user');

        $input['user_type'] = 1;
        $result = $user->register($input);

        if (false === $result) {
            return $this->sendError(400,$user->getError());
        }else{
           return $this->sendSuccess($user);
        }
    }

    /**
     * 机构pc登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function signin(Request $request){
        if(!$request->client_time_correct){
            return $this->sendError(400,_('client_time_not_correct'));
        }
        
        $input = $request->only(['account', 'password', 'captcha']);

        $result = $this->validate($input, 'User.signin');
        if ($result !== true) {
            return $this->sendError(400, $result);
        }

        $client_type = '';
        if ($request->isMobile()) {
            $client_type = 'mobile';
        }
       
        $user = User::login($input['account'], $input['password'],1, $client_type);

        if(!$user){
            return $this->sendError(400,User::$ERR);
        }

        $ret = $user->loginInfo();
        /*
        $ret['global'] = $this->getGlobalVars();
        $ret['global']['branchs'] = $user->getPermissionBranchs($ret['global']['branchs']);
        */
	    /*读取数据表config所有保存的配置*/
        /*
        $ret['global']['configs'] = user_config();
        //$ret['base_url'] = $request->baseUrl();
        */
        return $this->sendSuccess($ret,_('login success'));
    }

    public function qrcode(Request $request)
    {
        $uri = $request->get('uri');
        $r = $request->get('r');
        $token = $request->header('x-token');
        if (empty($token)) {
            $token = $request->get('token');
        }

        if (!empty($token)) {
            $param['token'] = $token;
        }
        $query_string = '';
        if (!empty($param)) {
            $query_string = http_build_query($param);
        }
        if (!empty($r)) {
            if ($r == 'wxbind' && $token && cache(cache_key( $token))) {
                cache(config('cache_key_prefix.orgwxbind') . $token, 1, 300);
                $uri = $request->domain() . '/admin/#/wxbind?' . $query_string;
            }
        }
        if (empty($uri)) {
            return $this->sendError(400, '参数错误');
        }

        $qrCode = new QrCode();
        $qrCode
            ->setText($uri)
            ->setSize(300)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;
        $response = new \think\Response($qrCode->get(), 200);
        $response->contentType($qrCode->getContentType());
        return $response;
    }

    /**
     * @desc 根据token得到用户信息 （用于机构用户绑定微信）
     * @param string token
     * @return array data
     */
    public function userinfo(Request $request)
    {
        $token = $request->get('token');
        //token解密处理
        $user = cache(cache_key( $token));
        $data['uid'] = $user['uid'];
        $data['mobile'] = $user['mobile'];
        $data['name'] = $user['name'];
        return $this->sendSuccess($data);
    }

    /**
     * 验证码发送
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function vcode(Request $request)
    {
        $input = $request->only(['mobile', 'type']);
        $input['code'] = random(4);
        $rule = [
            'mobile|手机号码' => 'require|regex:/^1[0-9]{10}$/',
            'type|验证类型' => 'require',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $result = Hook::listen('sms_before_send', $input);
        if (!$result[0]) {
            return $this->sendError(400, '短信请求频率限制，请稍后再试');
        }

        if($input['type'] == 'forget'){
            $user = m('User')
                ->where('user_type',1)
                ->where('mobile', $input['mobile'])
                ->where('is_mobile_bind',1)
                ->find();
            if(!$user){
                return $this->sendError(400,'该手机号还没有注册账号，请联系管理员添加');
            }

        }

        $sms_tpl = config('sms_tpl.verify');
        $tpl_data['code'] = $input['code'];
        $tpl_data['minute'] = 5;
        $sms_content = tpl_replace($sms_tpl, $tpl_data);
        $result = sms::Send($input['mobile'], $sms_content);

        Log::record($result, 'info');
        Hook::listen('sms_after_send', $input);

        return $this->sendSuccess('发送成功');
    }

    /**
     * @title 不需要登录获得全局变量
     * @url   global/:name
     * @desc  获得系统的全局变量
     * @return [type] [description]
     */
    public function glob(Request $request){
        $name = $request->param('name');
        $data = $this->getGlobalVars($name);
        if($name == 'branchs'){
            $data = $request->user->getPermissionBranchs($data);
        }

        return $this->sendSuccess($data);
    }

}
