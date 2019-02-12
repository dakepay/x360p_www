<?php

return [
    // 默认操作名
    'default_action'         => 'restful',
    'empty_controller'       => 'DefaultRest',
    'api'					 => [
	    'api_auth'      => true,  //是否开启授权认证
	    'auth_class'    => \app\vipapi\auth\Auth::class, //授权认证类
	    'api_debug'     => true,//是否开启调试
	    'login_expire'	=> 7200,	//登录过期 2小时
	    'base_uri'	    => 'http://pro.xiao360.com/vipapi'
	]
];