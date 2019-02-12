<?php
return [
    'api_auth'      => true,  //是否开启授权认证
    'auth_class'    => \app\center\auth\Auth::class, //授权认证类
    //'auth_class'  => \app\api\auth\OauthAuth::class, //授权认证类
    'api_debug'     => true,//是否开启调试
    'login_expire'	=> 7200,	//登录过期 2小时
];