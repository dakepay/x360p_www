<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/24
 * Time: 12:07
 */

namespace app\wxopen\controller;

use app\api\model\Authorizer;
use think\Cache;
use think\Log;
use think\Request;

class Callback extends OpenApp
{
    /**
     * /wxopen/callback?cid=14&og_id=0&bid=0&href=http://pro.xiao360.com/ui/pc/&auth_code=queryauthcode@@@TGexaNhBsGUZzyepOrF4nDtDaVWnsijBXR1SoGjVJlJNnFJPhjAXd9eTTfSshBaEWZ9_bcKrot6G6GnSXYeAIA&expires_in=3600
     * 公众号的授权后回调URI，得到授权码（authorization_code）和过期时间
     */
    public function index(Request $request)
    {
        $auth_code = input('auth_code');
        /*使用授权码换取公众号的接口调用凭据和授权信息*/
        $authorization_info = $this->openPlatform->getAuthorizationInfo($auth_code)['authorization_info'];

        /*授权方Appid*/
        $authorizer_appid = $authorization_info['authorizer_appid'];

        /*获取授权公众号或小程序基本信息*/
        $authorizer_info = $this->openPlatform->getAuthorizerInfo($authorizer_appid)['authorizer_info'];
        unset($authorizer_info['authorization_info']);
        // 保存数据库操作等...
        $data = array_merge($authorization_info, $authorizer_info);
        $data['cid'] = input('cid/d');
        $data['bids'] = input('bids');
        $data['og_id'] = input('og_id');

        $rs = Authorizer::authorized($data);
        $temp['appid'] = $authorizer_appid;
        $temp['code'] = 200;
        if ($rs !== true) {
            $temp['message'] = $rs;
        }

        $href = input('href') . '?' . http_build_query($temp);
        return $this->sendRedirect(str_replace('$', '#', $href));
    }
}