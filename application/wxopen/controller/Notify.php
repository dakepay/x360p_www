<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/24
 * Time: 11:13
 */

namespace app\wxopen\controller;

use app\api\model\Authorizer;
use EasyWeChat\OpenPlatform\Guard;
use think\Log;
use think\Request;

class Notify extends OpenApp
{
    public function index(Request $request)
    {
        $openPlatform = $this->openApp->open_platform;
        $server = $openPlatform->server;
        $server->setMessageHandler(function($event) use ($openPlatform) {
            Log::record($event,'wechat');
            // 事件类型常量定义在 \EasyWeChat\OpenPlatform\Guard 类里
            switch ($event->InfoType) {
                case Guard::EVENT_AUTHORIZED: // 授权成功
                    Log::record('授权成功','wechat');
                    /*全网发布测试*/
                    if ($event->AuthorizerAppid === self::WX_DISPLAY_CASE_APPID) {
                        /*使用授权码换取公众号的接口调用凭据和授权信息*/
                        $authorization_info = $this->openPlatform->getAuthorizationInfo($event->AuthorizationCode)['authorization_info'];
                        /*授权方Appid*/
                        $authorizer_appid = $authorization_info['authorizer_appid'];

                        /*获取授权公众号或小程序基本信息*/
                        $authorizer_info = $this->openPlatform->getAuthorizerInfo($event->AuthorizerAppid)['authorizer_info'];
                        unset($authorizer_info['authorization_info']);
                        $data = array_merge($authorization_info, $authorizer_info);
                        Authorizer::create($data, true);
                    }
                    return '';
                    break;
                case Guard::EVENT_UPDATE_AUTHORIZED: // 更新授权
                    return '';
                    break;
                case Guard::EVENT_UNAUTHORIZED: // 授权取消
                    // 更新数据库操作等...
                    $appid = $event->AuthorizerAppid;
                    Authorizer::unauthorized($appid);
                break;

            }
        });
        $response = $server->serve();
        $response->send(); // Laravel 里请使用：return $response;
    }
}