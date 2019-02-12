<?php

//作废
return [
	'debug'		=>  true,
	'app_id'	=>	'',
	'secret'	=>	'',
	'token'		=>  '',
	'aes_key' 	=>  '',
	'log' 		=> 	[
        'level'     => 'debug',
        'file'      => ROOT_PATH.'runtime/log/easywechat.log',
    ],
    'oauth' => [
        'scopes'    => ['snsapi_userinfo'],
        'callback'  => '/wxapi/oauth/handle',
    ],
    'payment' => [
        'merchant_id'        => '',
        'key'                => '',
        'cert_path'          => $_SERVER['DOCUMENT_ROOT'] . '/public/data/cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
        'key_path'           => $_SERVER['DOCUMENT_ROOT'] . '/public/data/cert/apiclient_key.pem',      // XXX: 绝对路径！！！！
        'notify_url'         => 'http://qms.xiao360.com/wxapi/wxpay/callback',       // 你也可以在下单时单独设置来想覆盖它
    ],
    'guzzle'=>[
        'timeout' => 30.0
    ],
];

//return [
//	'debug'		=> true,
//	'app_id'	=>	'wx1389af8071e3b6f5',
//	'secret'	=>	'ddef7a379ab54b525688f6374fdf8b2b',
////	'token'		=>  'b719adc06b722c30',
//	'token'		=>  'lantel',
////	'aes_key' 	=>  'BvgVDPWFytCY5dXsm9r1yJDv6BYjYGhPzpKPBryHcDk',
//	'aes_key' 	=>  '4nSy670owBEsCedZJrFWXKH0ZesKGAL4JdEo74Q1yv5',
//	'log' 		=> 	[
//        'level' => 'debug',
//        'file'  => ROOT_PATH.'runtime/log/easywechat.log',
//    ],
//    'oauth' => [
//        'scopes'   => ['snsapi_userinfo'],
//        'callback' => '/wxapi/oauth/handle',
//    ],
//    'tpl_message' => [
//        'homework_reminder' => 'GsiUnVL_ENqe4s3xYY-n9h0H6kbfoWEtaW3kua46vic',
//        'class_reminder' => 'mz7mTfnGHuk1-lnuOeYtSTj6aYAecbEhYaoREq31fiA',
//        'payment_success' => 'YTe6yQdS_yxluIzVZyYg7CQLGHpl2mUal4NIgsfW3yo',
//    ],
//    'payment' => [
//        'merchant_id'        => '1486940732',
//        'key'                => '8ce3fac7e23a02ab4e00cf0f1e03310a',
//        'cert_path'          => $_SERVER['DOCUMENT_ROOT'] . '/public/data/cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
//        'key_path'           => $_SERVER['DOCUMENT_ROOT'] . '/public/data/cert/apiclient_key.pem',      // XXX: 绝对路径！！！！
//        'notify_url'         => 'http://qms.xiao360.com/wxapi/wxpay/callback',       // 你也可以在下单时单独设置来想覆盖它
//    ],
//];