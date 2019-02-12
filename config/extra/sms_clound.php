<?php
return [
    'aliyun' => [
        'name'	=> '阿里云',
        'enable'	=> 0,
        'site'=>'https://www.aliyun.com/product/sms?spm=5176.8142029.388261.415.e9396d3esZyBHJ',
        'params'=>[
            'access_key_id' => '',
            'access_key_secret' => '',
            'sign_name' => '',
        ]
    ],
    'alidayu'	=> [
        'name'	=> '阿里大于',
        'enable'	=> 0,
        'site'	=> 'http://www.alidayu.com',
        'params'	=> [
            'app_key' => '',
            'app_secret' => '',
            'sign_name' => '',
        ]

    ],
    'baidu'	=> [
        'name'	=> '百度云',
        'enable'	=> 0,
        'site'	=> 'https://cloud.baidu.com/product/sms.html',
        'params'	=> [
            'ak' => '',
            'sk' => '',
            'invoke_id' => '',
            'domain' => '',
        ]
    ],
    'qcloud'	=> [
        'name'	=> '腾讯云',
        'enable'	=> 0,
        'site'	=> 'https://cloud.tencent.com/product/sms',
        'params'	=> [
            'sdk_app_id' => '', // SDK APP ID
            'app_key' => '', // APP KEY
        ]
    ],
    'yuntongxun'	=> [
        'name'	=> '容联云通讯',
        'enable'	=> 0,
        'site'	=> 'http://www.yuntongxun.com/',
        'params'	=> [
            'app_id' => '',
            'account_sid' => '',
            'account_token' => '',
            'is_sub_account' => false,
        ]
    ],
    'yunpian'	=> [
        'name'	=> '云片',
        'enable'	=> 0,
        'site'	=> 'https://www.yunpian.com/',
        'params'	=> [
            'api_key' => '',
        ]
    ],

    'Submail'	=> [
        'name'	=> '赛邮·云通信',
        'enable'	=> 0,
        'site'	=> 'https://www.mysubmail.com/',
        'params'	=> [
            'app_id' => '',
            'app_key' => '',
            'project' => '',
        ]
    ],
    /*
    'luosimao'	=> [
        'name'	=> '螺丝帽',
        'enable'	=> 0,
        'site'	=> 'https://luosimao.com/',
        'params'	=> [
            'api_key' => '',
        ]
    ],

    'huyi'	=> [
        'name'	=> '互亿无线',
        'enable'	=> 0,
        'site'	=> 'http://www.ihuyi.com/',
        'params'	=> [
            'api_id' => '',
            'api_key' => '',
        ]
    ],
    'juhe'	=> [
        'name'	=> '聚合数据',
        'enable'	=> 0,
        'site'	=> 'https://www.juhe.cn/',
        'params'	=> [
            'app_key' => '',
        ]
    ],

    'huaxin'	=> [
        'name'	=> '华信短信平台',
        'enable'	=> 0,
        'site'	=> 'http://www.ipyy.com/',
        'params'	=> [
            'user_id'  => '',
            'password' => '',
            'account'  => '',
            'ip'       => '',
            'ext_no'   => '',
        ]
    ],
    'chuanglan'	=> [
        'name'	=> '253云通讯（创蓝）',
        'enable'	=> 0,
        'site'	=> 'https://www.253.com/',
        'params'	=> [
            'username'  => '',
            'password' => '',
        ]
    ],
    'rongcloud'	=> [
        'name'	=> '融云',
        'enable'	=> 0,
        'site'	=> 'http://www.rongcloud.cn/',
        'params'	=> [
            'app_key' => '',
            'app_secret' => '',
        ]
    ],
    'tianyiwuxian'	=> [
        'name'	=> '天毅无线',
        'enable'	=> 0,
        'site'	=> 'http://www.85hu.com/',
        'params'	=> [
            'username' => '', //用户名
            'password' => '', //密码
            'gwid' => '', //网关ID
        ]
    ],
    'twilio'	=> [
        'name'	=> 'twilio',
        'enable'	=> 0,
        'site'	=> 'https://www.twilio.com/',
        'params'	=> [
            'account_sid' => '', // sid
            'from' => '', // 发送的号码 可以在控制台购买
            'token' => '', // apitoken
        ]
    ],

    'SendCloud'	=> [
        'name'	=> 'SendCloud',
        'enable'	=> 0,
        'site'	=> 'http://www.sendcloud.net/',
        'params'	=> [
            'sms_user' => '',
            'sms_key' => '',
            'timestamp' => false, // 是否启用时间戳
        ]
    ]*/
];