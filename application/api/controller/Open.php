<?php

namespace app\api\controller;

use app\api\model\Page;
use app\api\model\WxmpMaterial;
use Curl\Curl;
use think\Hook;
use think\Log;
use think\Request;
use think\captcha\Captcha;
use think\config;
use app\api\model\User;
use Endroid\QrCode\QrCode;
use util\sms;
use think\Db;

use util\Captcha as Captcha2;


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
    public $noRest = true;


    public function _init()
    {
        $og_id = gvar('client')['og_id'];
        gvar('og_id', $og_id);
    }

    /**
     * 验证码
     * @return [type] [description]
     */
    public function captcha2(Request $request, $name = '')
    {
        $captcha_config = Config::get('captcha');

        if(isset($captcha_config[$name])) {
            $captcha_config = $captcha_config[$name];
        }
        $captcha = new Captcha($captcha_config);
        if($name == '') {
            $name = 'login';
        }
        return $captcha->entry($name);
    }


    /**
     * 验证码
     * @return [type] [description]
     */
    public function captcha(Request $request, $name = '')
    {
        $captcha_config = Config::get('captcha');

        if(isset($captcha_config[$name])) {
            $captcha_config = $captcha_config[$name];
        }
        $captcha = new Captcha2($captcha_config);
        if($name == '') {
            $name = 'login';
        }
        return $captcha->equation_entry($name);
    }

    /**
     * 注册
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function signup(Request $request)
    {
        $input = $request->post();
        $result = $this->validate($input, 'User.signup');
        if($result !== true) {
            return $this->sendError(400, $result);
        }

        unset($input['repassword']);

        $user = model('user');

        $input['user_type'] = 1;
        $result = $user->register($input);

        if(false === $result) {
            return $this->sendError(400, $user->getError());
        } else {
            return $this->sendSuccess($user);
        }
    }

    /**
     * 通过token登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function tklogin(Request $request)
    {
        if(!$request->client_time_correct) {
            return $this->sendError(400, _('client_time_not_correct'));
        }

        $token = input('post.token');

        $user = User::tokenLogin($token);

        if(!$user) {
            return $this->sendError(400, User::$ERR);
        }

        $ret = $user->loginInfo();
        $client = gvar('client');
        $ret['x_sub_host'] = $client['domain'];
        $ret['client'] = $this->open_client_info();
        $ret['base_url'] = $request->domain();
        $ret['product_url'] = $this->get_product_url();



        return $this->sendSuccess($ret, _('login success'));
    }

    /**
     * 机构pc登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function signin(Request $request)
    {
        if(!$request->client_time_correct) {
            return $this->sendError(400, _('client_time_not_correct'));
        }
        $client = gvar('client');

        $input = $request->only(['account', 'password', 'captcha','ui']);

        $pos = strrpos($input['account'], '@');

        if($pos === false) {
            if(!$client['defined']) {
                return $this->sendError(400, _('account_does_not_exists'));
            } else {
                $sub_host = $client['domain'];
            }
        } else {
            $sub_host = substr($input['account'], $pos + 1);

            if(!$client['defined'] || $client['domain'] != $sub_host) {
                $client = load_client_by_host($sub_host);
                gvar('client', $client);
                gvar('og_id',$client['og_id']);
            }

            if(!$client['defined']) {
                return $this->sendError(400, _('account_does_not_exists'));
            }
            $input['account'] = substr($input['account'], 0, -(strlen($sub_host) + 1));
        }

        $client_type = '';
        $ui_type = isset($input['ui'])?$input['ui']:'org';

        if($request->isMobile()) {
            $client_type = 'mobile';
        } else {
            if($ui_type == 'org') {
                $result = $this->validate($input, 'User.signin');
                if ($result !== true) {
                    return $this->sendError(400, $result);
                }
            }
        }



        $user = User::login($input['account'], $input['password'], 1, $client_type,$ui_type);

        if(!$user) {
            return $this->sendError(400, User::$ERR);
        }

        $ret = $user->loginInfo();
        $ret['x_sub_host'] = $sub_host;
        $ret['client'] = $this->open_client_info();
        $ret['base_url'] = $request->domain();
        $ret['product_url'] = $this->get_product_url();

        return $this->sendSuccess($ret, _('login success'));
    }

    /**
     * 接口获取token
     * @param Request $request
     */
    public function gentk(Request $request)
    {
        if(!$request->client_time_correct) {
            return $this->sendError(400, _('client_time_not_correct'));
        }
        $input = $request->only(['account', 'password']);

        $client = gvar('client');
        $pos = strrpos($input['account'], '@');

        if($pos === false) {
            if(!$client['defined']) {
                return $this->sendError(400, _('account_does_not_exists'));
            } else {
                $sub_host = $client['domain'];
            }

        } else {
            $sub_host = substr($input['account'], $pos + 1);

            if(!$client['defined'] || $client['domain'] != $sub_host) {
                $client = load_client_by_host($sub_host);
                gvar('client', $client);
                gvar('og_id',$client['og_id']);
            }

            if(!$client['defined']) {
                return $this->sendError(400, _('account_does_not_exists'));
            }
            $input['account'] = substr($input['account'], 0, -(strlen($sub_host) + 1));
        }

        $result = $this->validate($input, 'User.msignin');
        if($result !== true) {
            return $this->sendError(400, $result);
        }

        $token = User::gentk_login($input['account'], $input['password']);

        if(false === $token) {
            return $this->sendError(400, User::$ERR);
        }

        return $this->sendSuccess(['token' => $token], _('login success'));
    }

    public function qrcode(Request $request)
    {
        $uri = $request->get('uri');
        $r = $request->get('r');
        $token = $request->header('x-token');
        if(empty($token)) {
            $token = $request->get('token');
        }

        if(!empty($token)) {
            $param['token'] = $token;
        }
        $query_string = '';
        if(!empty($param)) {
            $query_string = http_build_query($param);
        }
        if(!empty($r)) {
            if($r == 'wxbind' && $token && cache(cache_key($token))) {
                cache(config('cache_key_prefix.orgwxbind') . $token, 1, 300);
                $uri = $request->domain() . '/admin/#/wxbind?' . $query_string;
            }
        }
        if(empty($uri)) {
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
            ->setImageType(QrCode::IMAGE_TYPE_PNG);
        $response = new \think\Response($qrCode->get(), 200);
        $response->contentType($qrCode->getContentType());
        return $response;
    }

    /**
     * @desc  界面设置
     * @author luo
     * @method GET
     */
    public function interface_config()
    {
        $config = \app\api\model\Config::get(['cfg_name' => 'pc_login_interface']);
        return $this->sendSuccess($config);
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
        $user = cache(cache_key($token));
        $data['uid'] = $user['uid'];
        $data['mobile'] = $user['mobile'];
        $data['name'] = $user['name'];
        return $this->sendSuccess($data);
    }

    public function org_wxbind(Request $request)
    {
        $token = $request->param('token');
        $openid = $request->param('openid');
        if(empty($token) || empty($openid)) {
            return $this->sendError(400, '参数错误');
        }
        $userinfo = cache(cache_key($token));
        if(empty($userinfo)) {
            return $this->sendError(401, 'token已过期, 请重新绑定。');
        }
        $user = model('org_user')->find($userinfo['uid']);
        $user->openid = $openid;
        $user->is_weixin_bind = 1;
        $user->save();
        return $this->sendSuccess();
    }

    public function wxbinding(Request $request)
    {
        $token = $request->param('token');
        if(empty($token) || empty(cache(cache_key($token)))) {
            return $this->sendError(401, 'token invalid or expired');
        }
        $status = $request->param('status');
        if(!isset($status)) {
            return $this->sendError(400, 'parameter status required');
        }
        $key = config('cache_key_prefix.orgwxbind') . $token;
        cache($key, $status, 300);
        return $this->sendSuccess();
    }

    public function configs(Request $request, $name)
    {
        $allow_name = ['params', 'comment_tags', 'edu', 'mobile_swiper'];
        if(!in_array($name, $allow_name)) {
            $cfg = [];
        } else {
            $cfg = user_config($name);
            if(empty($cfg)) {
                $cfg = config($name);
            }
        }
        return $this->sendSuccess($cfg);
    }

    /**
     * 验证码发送
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function vcode(Request $request)
    {
        $input = input();
        $input['code'] = random(4);
        $rule = [
            'mobile|手机号码' => 'require|regex:/^1\d{10}$/',
            'type|验证类型'   => 'require',
        ];
        $right = $this->validate($input, $rule);
        if($right !== true) {
            return $this->sendError(400, $right);
        }
        $allow_type = ['forget', 'reset_code','qrsign'];
        if(!in_array($input['type'], $allow_type)) {
            return $this->sendError(400, 'param type invalid');
        }
        $result = Hook::listen('sms_before_send', $input);
        if(!$result[0]) {
            return $this->sendError(400, '短信请求频率限制，请稍后再试');
        }

        if($input['type'] == 'forget') {
            $w = [];
            $w['user_type'] = User::EMPLOYEE_ACCOUNT;
            $w['mobile'] = $input['mobile'];
            $user = User::get($w);
            if(!$user) {
                return $this->sendError(400, '没有查询到该手机号码绑定的员工账号！');
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
     * 获得公开客户信息
     * @return [type] [description]
     */
    protected function open_client_info()
    {
        $client = gvar('client');
        if(empty($client['info']['params'])) {
            $client['info']['params'] = config('org_default_config.center_params');
        }
        return array_merge($client['info'], [
            'domain'    => $client['domain'],
            'subdomain' => $client['subdomain']
        ]);
    }

    public function wximage(Request $request)
    {
        $url = $request->get('url');
        $curl = new Curl();
        $curl->get($url);
        if($curl->error) {
            $this->error = $curl->error_code;
            return false;
        }
        header('Content-Type:image/jpg');
        return $curl->response;
    }


    public function redirect(Request $request)
    {
        $url = $request->get('url');
        $url = str_replace('$', '#', $url);
        header('Location: ' . $url);
    }

    public function voice_duration(Request $request)
    {
        $url = $request->param('url');
        if(empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->sendError(400, '缺少参数或者参数不是合法的url');
        }
        $curl = new Curl();
        $curl->get($url);
        if($curl->error) {
            return $this->sendError(400, $curl->error_code);
        } else {
            $ret = json_decode($curl->response, true);
            return $this->sendSuccess($ret);
        }
    }

    public function reset_pwd(Request $request)
    {
        $input = $request->only(['mobile', 'code', 'password', 'repassword']);
        $rule = [
            'mobile|手机号'       => 'require',
            'code|验证码'         => 'require',
            'password|新密码'     => 'require|length:6,20',
            'repassword|确认新密码' => 'require|confirm:password',
        ];
        $result = $this->validate($input, $rule);
        if($result !== true) {
            return $this->sendError(400, $result);
        }

        $w = [];
        $w['mobile'] = $input['mobile'];
        $w['user_type'] = User::EMPLOYEE_ACCOUNT;
        $user = User::get($w);
        if(empty($user)) {
            return $this->sendError(400, '账号不存在!');
        }

        $rs = check_verify_code($input['mobile'], $input['code'], 'forget');
        if($rs !== true) {
            return $this->sendError(400, $rs);
        }
        $ret = $user->save(['password' => $input['password']]);
        if($ret === false) {
            return $this->sendError(400, $user->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 帮助文档接口
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function helpdoc(Request $request)
    {
        $ret = [];
        $router = $request->param('router');
        $url = sprintf(config('help.search_url'), $router);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url);
        $content_type = $response->getHeader('content-type');
        $status = $response->getStatusCode();
        if($status == 200) {
            $json = $response->getBody();
            $result = json_decode($json, true);
            $ret = $result['data'];
        }
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  素材详细信息
     * @author luo
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method GET
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function get_material(Request $request)
    {
        $media_id = $request->param('media_id');
        if(empty($media_id)) {
            return $this->sendError(400, 'media_id is required');
        }
        $material = WxmpMaterial::get(['media_id' => $media_id], ['items']);
        return $this->sendSuccess($material);
    }

    public function pages(Request $request)
    {
        $page_id = input('page_id', 0);
        $m_page = new Page();
        $page = $m_page->get($page_id);
        if(empty($page)) return $this->sendSuccess();
        
        return $this->sendSuccess($page);
    }

    /**
     * 根据手机号获取登录token
     * @param Request $request
     */
    public function mobile_signin(Request $request)
    {
        $input = $request->post();

        if(!isset($input['key'])){
            return $this->sendError(400,'参数错误!');
        }

        $secret_key = user_config('org_api.secret');

        if($input['key'] != $secret_key){
            return $this->sendError(400,'KEY校验失败!');
        }

        if(!isset($input['mobile'])){
            return $this->sendError(400,'参数错误!');
        }

        $w['user_type'] = 1;
        $w['mobile']    = $input['mobile'];

        $user_info = get_user_info($w);

        if(!$user_info){
            return $this->sendError(400,'未查询到相关手机号!');
        }

        $client = gvar('client');

        $cid = $client['cid'];

        $option = [
            $cid,
            $user_info['og_id'],
            request()->time(),
            request()->ip(),
            random_str()
        ];
        $token  = md5(implode('', $option));


        $cache_key = cache_key($token);

        $login_user['uid']   = $user_info['uid'];
        $login_user['og_id'] = $user_info['og_id'];
        $login_user['from'] = 'tapp';

        $login_expire = user_config('api.login_expire');

        cache($cache_key,$login_user,$login_expire);


        $ret = ['token'=>$token];

        return $this->sendSuccess($ret);
    }


    /**
     * 获得UI配置
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function ui_config(Request $request){
        $t = 'pc';
        if($request->isMobile()){
            $t = 'm';
        }
        $ui_config = get_ui_config($t);
        return $this->sendSuccess($ui_config);
    }

    /**
     * 市场渠道二维码名单提交处理
     * post /api/open/qr_clue
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function qr_clue(Request $request){
        $input = $request->param();

        $w['mobile'] = $input['tel'];
        $w['type'] = 'qrsign';
        $w['expire_time'] = ['gt',time()];
        $code = Db::name('sms_vcode')->where($w)->value('code');
        
        if(empty($code)){
            return $this->sendError(400,'验证码已过期，请重新发送');
        }elseif($input['code'] != $code){
            return $this->sendError(400,'验证码错误');
        }

        //$wh['name'] = $input['name'];
        $wh['tel'] = $input['tel'];
        $mMarketClue = new \app\api\model\MarketClue();
        $ex_clue = $mMarketClue->skipBid()->where($wh)->find();
        if($ex_clue){
            return $this->sendError(400,'您的电话号码已经提交过,请不要重复提交!');
        }

        $mStudent = new \app\api\model\Student();
        $ws['first_tel|second_tel'] = $input['tel'];

        $ex_s = $mStudent->skipBid()->where($ws)->find();
        if($ex_s){
            return $this->sendError(400,'本次活动只对未报名的学员开放!');
        }

        if(isset($input['assigned_eid'])){
            $input['create_uid'] = m('employee')->where('eid',$input['assigned_eid'])->value('uid');
        }

        $result = m('MarketClue')->addQrClue($input);
        if(!$result){
            return $this->sendError(m('MarketClue')->getError(),400);
        }
        return $this->sendSuccess('ok');
    }

    public function qrsign_config(Request $request){
        $config = user_config('qrsign');
        return $this->sendSuccess($config);
    }

    /**
     * 获取市场渠道配置
     * get /api/open/qr_config?mc_id=5
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function qr_config(Request $request)
    {
        $mc_id = $request->get('mc_id');
        $qr_config = model('MarketChannel')->where('mc_id',$mc_id)->value('qr_config');

        if (empty($qr_config)){
            $qr_config = user_config('qrsign');
        }else{
            $qr_config = json_decode($qr_config,true);
        }
        return $this->sendSuccess($qr_config);
    }

    /**
     *
     * @return string
     */
    public function get_product_url(){
        $request = request();
        $client = gvar('client');
        $host = $client['domain'];
        $domains = include CONF_PATH . 'domains.php';
        $domain_map = [];
        foreach($domains as $d=>$k){
            $domain_map[$k] = $d;
        }
        $base_domain = config('ui.domain');
        /*
        if(isset($domain_map[$host])){
            $base_domain = str_replace($host.'.','',$domain_map[$host]);
        }*/
        return $request->scheme() . '://' . $base_domain;
    }
    

}
