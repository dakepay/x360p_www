<?php
namespace app\mccapi\controller;

use think\Log;
use think\Request;


class Open extends Base{
	public $apiAuth = false;
    public $noRest  = true;

    /**
     * 通过code登录
     * @param Request $request [description]
     */
    public function Signin(Request $request){
    	$code 		= input('code/s');
    	$developer  = input('developer/s');

    	$developer  = $developer ? $developer : 'production';

    	if(!$code){
    		return $this->sendError(400,'params error,code needed!');
    	}

    	$mapp = mapp_instance($developer);

    	$res = $mapp->sns->getSessionKey($code);

    	if(!$res->openid){
    		return $this->sendError($res);
    	}

    	$cc_user = $this->m_user->getUserByOpenid($res->openid);

        $cc_user->last_login_time = request()->time();
        $cc_user->save();

    	$login_info = $cc_user->toArray();
    	$login_info['uid']   = $login_info['cu_id'];
    	$login_info['token'] = $res->session_key;
    	$login_expire = config('api.login_expire');
		$login_info['login_server_time'] = request()->time();
		$login_info['expired'] = $login_expire;

    	$cache_key = cache_key($res->session_key);

    	cache($cache_key,$login_info,$login_expire);

    	return $this->sendSuccess($login_info);
    }
}