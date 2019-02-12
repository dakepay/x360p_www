<?php

namespace app\common;

class Center{
	static public function wxpay_config($debug = false,$option = [],$mode = ''){
		$demo_config = [
	        'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
	        'app_id' => 'wxb3fxxxxxxxxxxx', // 公众号 APPID
	        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 小程序 APPID
	        'mch_id' => '14577xxxx',
	        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
	        'notify_url'  => 'https://vip.pro.xiao360.com/vipapi/Wxpay/notify',
	        'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
	        'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
	        'log' => [ // optional
	            'file'  => LOG_PATH .'wxpay.log',
	            'level' => 'debug'
	        ],
	        //'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
	    ];

	    $center_config = config('center.wxpay');
	    $option_config = array_merge($center_config,$option);

	    $config = [
	    	'notify_url'	=> $demo_config['notify_url']
	    ];

	    if($debug){
	    	$config['log'] = $demo_config['log'];
	    }

	    foreach($option_config as $key=>$val){
	    	if(isset($demo_config[$key])){
	    		$config[$key] = $val;
	    	}
	    }

	    if(in_array($mode,['dev','hk'])){
	    	$config['mode'] = $mode;
	    }

	    return $config;
	}

	static function old_alipay_config($debug = false){
		$center_config = config('center.oldalipay');

		return $center_config;
	}


	static function alipay_config($debug = false,$option = [],$mode = ''){
		$demo_config = [
	        'app_id' => '2016082000295641',
	        'notify_url' => 'https://vip.pro.xiao360.com/vipapi/Alipay/notify',
	        'return_url' => 'https://vip.pro.xiao360.com/vipapi/Alipay/return',
	        'ali_public_key' => '',
	        'private_key' => '',
	        'log' => [ // optional
	            'file'  => LOG_PATH.'alipay.log',
	            'level' => 'debug'
	        ],
	        //'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
	    ];

	    $center_config = config('center.alipay');


	    $option_config = array_merge($center_config,$option);

	    $config = [
	    	'notify_url'	=> $demo_config['notify_url']
	    ];

	    if($debug){
	    	$config['log'] = $demo_config['log'];
	    }
	    
	    foreach($option_config as $key=>$val){
	    	if(isset($demo_config[$key])){
	    		$config[$key] = $val;
	    	}
	    }

	    if(in_array($mode,['dev'])){
	    	$config['mode'] = $mode;
	    }

	   

	    return $config;
	}
}