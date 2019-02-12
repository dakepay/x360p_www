<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | Company: YG | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/3/10 10:17
// +----------------------------------------------------------------------
// | TITLE: this to do?
// +----------------------------------------------------------------------

return [
    'api_auth'      => true,  //是否开启授权认证
    'auth_class'    => \app\api\auth\Auth::class, //授权认证类
    //'auth_class'  => \app\api\auth\OauthAuth::class, //授权认证类
    'api_debug'     => true,//是否开启调试
    'login_expire'	=> 7200,	//登录过期 2小时
    'base_uri'	    => 'http://pro.xiao360.com/api'
];