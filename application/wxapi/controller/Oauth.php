<?php
namespace app\wxapi\controller;

use app\api\controller\Base;
use app\api\model\User;
use app\api\model\WxmpFans;
use app\api\model\WechatUser;
use app\common\Wechat;
use think\Log;
use think\Request;

class Oauth extends Base
{
    public $apiAuth = false;

    public function index(Request $request)
    {
        $appid = 'wx1389af8071e3b6f5';
        Wechat::getApp($appid)->oauth->scopes(['snsapi_userinfo'])->redirect()->send();
    }

    //获取openid
	public function openid(Request $request)
    {
        $params = $request->param();
        $referer = $request->header('referer');
        foreach($params as $key => $val) {
            $params_arr[] = $key . '=' . $val;
        }
        $params_str = !empty($params_arr) ? implode('&', $params_arr) : '';
        $referer .= '?' . $params_str;
        halt($referer);
        halt($request->param());
        halt($request->header('referer'));
        $app = Wechat::getApp();
        $href = $request->param('href');
        try {
            $user = $app->oauth->user();
        } catch (\Exception $e) {
            $app->oauth->redirect($request->domain().'/wxapi/oauth/openid')->send();
            exit;
        }
        $p = 'codepay';
        $wechat_user = $user->toArray();
        //return $this->sendSuccess(['openid' => $wechat_user['id']]);
        $redirect_url = 'http://base.dev.xiao360.com/ui/school?tk='.$wechat_user['id'].'&p='.$p.'&token=';
        //halt($redirect_url);
        header('Location:'. $redirect_url);
        exit;
	}

	/**
	 * 用户同意授权后的回调url,自带3个get参数（code,state,appid）
     * /wxapi/oauth/callback?code=01197iq&state=21463&appid=wx1389af8071e3b6f5
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function callback(Request $request)
    {
        $appid = $request->param('appid');
		$oauth = Wechat::getApp($appid)->oauth;
		// 获取 OAuth 授权结果用户信息
		$user = $oauth->user();
        $wechat_user = $user->toArray();

        cookie('wechat_user',$wechat_user);

		$target_url = cookie('target_url');

		header('Location:'. $target_url);
		exit;
	}
}