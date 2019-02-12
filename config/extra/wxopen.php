<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/24
 * Time: 10:57
 */
return [
    'log' 		=> 	[
        'level'     => 'debug',
        'file'      => ROOT_PATH.'runtime/log/easywechat.log',
    ],
    'open_platform' => [
        'app_id'   => 'wxef30c07423002ed2',
        'secret'   => 'cddc8a885e136af05e35efa1bbc97288',
        'token'    => '41e7b82ce588730bebcb71db7f732a4d',
        'aes_key'  => '1c4110a878ec0730f5e3297bee0e111a78ec0730f5e'
    ],
    'oauth' => [
        'scopes'   => ['snsapi_userinfo'],
        'callback' => '/wxapi/oauth/callback',
    ],
    'guzzle'=>[
        'timeout' => 30.0
    ],
    'industry' => [
        16 => '教育-培训',
        2  => 'IT科技-IT软件与服务',
    ],
    'xq_cover'  => 'http://sp1.xiao360.com/static/wxcover/xq_cover.png',        //学情报告封面
    'key_reply_prefix' => ['XQ','BD']             //学情报告查询前缀
];