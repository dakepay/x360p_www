<?php

namespace app\ftapi\controller;

use think\Hook;
use think\Log;
use think\Request;
use think\captcha\Captcha;
use think\config;
use app\ftapi\model\User;
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

    public function _init()
    {
        $og_id = gvar('client')['og_id'];
        gvar('og_id', $og_id);
    }

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
     * 外教端登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function signin(Request $request){
        if(!$request->client_time_correct){
            return $this->sendError(400,_('client_time_not_correct'));
        }
        $input = $request->only(['account', 'password', 'captcha']);

        $client = gvar('client');
        $pos = strrpos($input['account'], '@');

        //如果没有通过subhost访问
        if(!$client || $client['defined_way'] == 'debug'){
            if($pos !== false){
                $sub_host = substr($input['account'],$pos+1);
                $client   = load_client_by_host($sub_host);
                gvar('client',$client);
                gvar('og_id',$client['og_id']);
                if($client['defined']){
                    $input['account'] = substr($input['account'], 0, -(strlen($sub_host) + 1));
                }

            }else{
                gvar('client',$client);
                gvar('og_id',$client['og_id']);
                if(!isset($client['info']['host'])) return $this->sendError(400, '帐号不存在');
                $sub_host = $client['info']['host'];
            }
        } else {

            if(!isset($client['info']['host'])) return $this->sendError(400, '帐号不存在');
            $sub_host = $client['info']['host'];

            if($pos !== false){
                $sub_host = substr($input['account'],$pos+1);
                $client   = load_client_by_host($sub_host);
                gvar('client',$client);
                gvar('og_id',$client['og_id']);
                if($client['defined']){
                    $input['account'] = substr($input['account'], 0, -(strlen($sub_host) + 1));
                }

            }
        }

        $result = $this->validate($input, 'User.signin');
        if ($result !== true) return $this->sendError(400, $result);

        $user = User::login($input['account'], $input['password'],1, 'mobile');

        if(!$user) return $this->sendError(400,User::$ERR);

        $ret = $user->loginInfo();
        $ret['x_sub_host'] =  $sub_host;
        $ret['base_url']   = $request->domain();
        $ret['product_url'] = $request->scheme().'://'.config('ui.domain');

        return $this->sendSuccess($ret,_('login success'));
    }

    /**
     * @desc UI界面配置
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function ui_config()
    {
        $config = get_ui_config('ft');
        return $this->sendSuccess($config);
    }


    /**
     * @desc  验证码验证
     * @param Request $request
     * @method POST
     */
    public function check_vcode(Request $request)
    {
        $input = $request->only(['mobile', 'vcode']);
        $rs = check_verify_code($input['mobile'], $input['vcode'], 'ft');
        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  重置密码
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method POST
     */
    public function reset_pwd(Request $request)
    {
        $input = $request->only(['mobile', 'vcode', 'password', 'repassword']);
        $rule = [
            'mobile|手机号'        => 'require',
            'vcode|验证码'          => 'require',
            'password|新密码'      => 'require|length:6,20',
            'repassword|确认新密码' => 'require|confirm:password',
        ];
        $result = $this->validate($input, $rule);
        if ($result !== true) {
            return $this->sendError(400, $result);
        }

        $w = [];
        $w['mobile'] = $input['mobile'];
        $w['user_type'] = User::EMPLOYEE_ACCOUNT;
        $user = User::get($w);
        if (empty($user)) {
            return $this->sendError(400, 'invalid request!');
        }

        $rs = check_verify_code($input['mobile'], $input['vcode'], 'ft');
        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }
        $ret = $user->data(['password' => $input['password']], true)->save();
        if ($ret === false) {
            return $this->sendError(400, $user->getError());
        }
        return $this->sendSuccess();
    }

    public function configs(Request $request, $name)
    {
        $allow_name = ['params', 'comment_tags', 'edu', 'mobile_swiper'];
        if(!in_array($name,$allow_name)){
            $cfg = [];
        }else{
            $cfg = user_config($name);
            if (empty($cfg)) {
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
            return $this->sendError(400, 'request busy');
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
    protected function open_client_info(){
        $client = gvar('client');

        return array_merge($client['info'],[
                'domain'    => $client['domain'],
                'subdomain' => $client['subdomain']
                ]);
    }

    public function redirect(Request $request){
        $url = $request->get('url');
        $url = str_replace('$','#',$url);
        header('Location: '.$url);
    }

    //在字典中查找特定的字段
    public function dicts()
    {
        $name = input('name');
        $dict_fields = ['did','og_id','pid','name','title','desc','is_system','sort'];
        $top_dicts   = model('dictionary')->where('name', $name)->field($dict_fields)->order('sort DESC')->select();
        $dict_id_map = [];
        $dicts = [];
        foreach($top_dicts as $d){

            $dict_id_map[$d['did']] = $d['name'];
            $dicts[$d['name']] = [];
        }


        $dict_items = model('dictionary')->where('pid','NEQ',0)->where('display',1)->order('sort DESC')->field($dict_fields)->select();

        foreach($dict_items as $item){
            if(isset($dict_id_map[$item['pid']])){
                $name = $dict_id_map[$item['pid']];
                if($name && isset($dicts[$name])){
                    array_push($dicts[$name],$item);
                }
            }
        }
        return $this->sendSuccess($dicts);
    }





}
