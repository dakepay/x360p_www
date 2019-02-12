<?php

namespace app\api\model;

class WechatUser extends Base
{

    static public function UpdateWechatUserInfo(&$wechat_user){

        $w['openid'] = $wechat_user['openid'];

        $wechat_user['avatar'] 		= $wechat_user['headimgurl'];
        $wechat_user['privilege_0'] = (isset($wechat_user['privilege']) && !empty($wechat_user['privilege']) )? $wechat_user['privilege'][0]:'';

        $user = (new self())->where($w)->find();

        if(!$user){
            (new self())->data($wechat_user)->allowField(true)->save();
        }else{
            $user->data($wechat_user)->allowField(true)->save();
        }
    }
}