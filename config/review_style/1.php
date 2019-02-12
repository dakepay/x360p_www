<?php
/**
 * 课评模板 1号 专业课评
 */
return [
    '_name'          => '专业课评',
    'common_fields'  =>  [
        [
            'field'         =>  'cf1',
            'enable'        => 1,
            'student_view'  => 1,
            'label'         => '课堂内容'
        ],
        [
            'field'         =>  'cf2',
            'enable'        => 0,
            'student_view'  => 1,
            'label'         => '课堂内容2'
        ],
        [
            'field'         =>  'cf3',
            'enable'        => 0,
            'student_view'  => 1,
            'label'         => '课堂内容3'
        ]
    ],
    'student_fields'    => [
        'score' => [                //专业表现评价打分
            [
                'field'     => 'score0',
                'label'     =>'专业表现1',
                'default'   =>5,
                'enable'    => 1,
                'alias'     => [        //分数别名
                    1   => '较简单',
                    2   => '简单',
                    3   => '一般',
                    4   => '较难',
                    5   => '难'
                ]
            ],
            [
                'field'     => 'score1',
                'label'     =>'专业表现2',
                'default'   =>5,
                'enable'    => 1,
                'alias'     => [
                    1   => '较简单',
                    2   => '简单',
                    3   => '一般',
                    4   => '较难',
                    5   => '难'
                ]
            ],
            [
                'field'     => 'score2',
                'label'     =>'专业表现3',
                'default'   =>5,
                'enable'    => 1,
                'alias'     => [
                    1   => '较简单',
                    2   => '简单',
                    3   => '一般',
                    4   => '较难',
                    5   => '难'
                ]
            ],
            [
                'field'     => 'score3',
                'label'     =>'专业表现4',
                'default'   =>5,
                'enable'    => 1,
                'alias'     => [
                    1   => '较简单',
                    2   => '简单',
                    3   => '一般',
                    4   => '较难',
                    5   => '难'
                ]
            ],
            [
                'field'     => 'score4',
                'label'     =>'专业表现5',
                'default'   =>5,
                'enable'    => 1,
                'alias'     => [
                    1   => '较简单',
                    2   => '简单',
                    3   => '一般',
                    4   => '较难',
                    5   => '难'
                ]
            ],
        ],
        'weak'  =>  [               //专业问题解决
            [
                'field'     => 'weak0',
                'label'     =>'专业错误1',
                'enable'    => 1
            ],
            [
                'field'     => 'weak1',
                'label'     =>'专业错误2',
                'enable'    => 1
            ],
            [
                'field'     => 'weak2',
                'label'     =>'专业错误3',
                'enable'    => 1
            ],
            [
                'field'     => 'weak3',
                'label'     =>'专业错误4',
                'enable'    => 1
            ],
            [
                'field'     => 'weak4',
                'label'     =>'专业错误5',
                'enable'    => 1
            ],
        ],
        'honor'         => [            //获得称号
            [
                'field'     => 'honor1',    //字段
                'label'     => '称号1',       //称号名称
                'desc'      => [//简短分享语
                    ['name'=>'模板1','content'=>'简短分享语1，战胜90%同学，你真棒']
                ],
                'image'     => 'https://sp1.xiao360.com/review_tpl/1/honor1.gif',   //称号图片
                'teacher_say_tpl'   => [
                        ['name'=>'模板1','content'=>'获得称号1，老师要说的话']
                ]//老师说关联模版
            ],
            [
                'field'     => 'honor2',
                'label'     => '称号2',
                'desc'      => [//简短分享语
                    ['name'=>'模板1','content'=>'简短分享语1，战胜90%同学，你真棒']
                ],
                'image'     => 'https://sp1.xiao360.com/review_tpl/1/honor2.gif',
                'teacher_say_tpl'   => [
                    ['name'=>'模板1','content'=>'获得称号1，老师要说的话']
                ]//老师说关联模版
            ],
            [
                'field'     => 'honor3',
                'label'     => '称号3',
                'desc'      => [//简短分享语
                    ['name'=>'模板1','content'=>'简短分享语1，战胜90%同学，你真棒']
                ],
                'image'     => 'https://sp1.xiao360.com/review_tpl/1/honor3.gif',
                'teacher_say_tpl'   => [
                    ['name'=>'模板1','content'=>'获得称号1，老师要说的话']
                ]//老师说关联模版
            ],
            [
                'field'     => 'honor4',
                'label'     => '称号4',
                'desc'      => [//简短分享语
                    ['name'=>'模板1','content'=>'简短分享语1，战胜90%同学，你真棒']
                ],
                'image'     => 'https://sp1.xiao360.com/review_tpl/1/honor4.gif',
                'teacher_say_tpl'   => [
                    ['name'=>'模板1','content'=>'获得称号1，老师要说的话']
                ]//老师说关联模版
            ],
            [
                'field'     => 'honor5',
                'label'     => '称号4',
                'desc'      => [//简短分享语
                    ['name'=>'模板1','content'=>'简短分享语1，战胜90%同学，你真棒']
                ],
                'image'     => 'https://sp1.xiao360.com/review_tpl/1/honor5.gif',
                'teacher_say_tpl'   => [
                    ['name'=>'模板1','content'=>'获得称号1，老师要说的话']
                ]//老师说关联模版
            ]
        ],
        'teacher_say'       =>  [       //老师说的话
            'label'     => '%ename%老师说',
            'default'   =>  '默认老师说的话'
        ],
        'share_ad'     => [ //分享广告
            'qr'        => ''  ,     //二维码地址,
            'text'      => '文字描述',//广告二维码描述
        ]
    ]
];