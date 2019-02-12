<?php
//系统连接定义
return [
    'pc'    => [
        'name'  => '机构PC端',
        'links' => [
            ['text'=>'录入市场名单','link'=>'recruiting/market/info-modal.vue','type'=>'modal'],
            ['text'=>'录入客户名单','link'=>'recruiting/recruiting-info-modal.vue','type'=>'modal'],
            ['text'=>'客户跟进情况','link'=>'recruiting/following/follow-info-modal.vue','type'=>'modal'],
            ['text'=>'试听安排','link'=>'recruiting/audition/info-modal.vue','type'=>'modal'],
            ['text'=>'报名','link'=>'/signup','type'=>'router'],
            ['text'=>'缴费','link'=>'/payment','type'=>'router'],
            ['text'=>'学员建档','link'=>'business/student/info-modal.vue','type'=>'modal'],
            ['text'=>'记一笔','link'=>'business/iae/tally-info.vue@modal','type'=>'modal']
        ]
    ],
    'm'     => [
        'name'  => '机构手机端',
        'links' => [
            ['text'=>'登录页','link'=>'/login','type'=>'router'],
            ['text'=>'忘记密码','link'=>'/forgotpwd','type'=>'router'],
            ['text'=>'首页','link'=>'/home/home','type'=>'router'],
            ['text'=>'班级','link'=>'/class/index','type'=>'router'],
            ['text'=>'学员','link'=>'/student/index','type'=>'router'],
            ['text'=>'我的','link'=>'/my/index','type'=>'router'],
            ['text'=>'点名','link'=>'/home/rollcall','type'=>'router'],
            ['text'=>'课标','link'=>'/home/schedule','type'=>'router'],
            ['text'=>'通知','link'=>'/home/news','type'=>'router'],
            ['text'=>'课评','link'=>'/home/comment','type'=>'router'],
            ['text'=>'作品','link'=>'/home/artwork','type'=>'router'],
            ['text'=>'作业','link'=>'/home/homework','type'=>'router'],
            ['text'=>'报表','link'=>'/report/index','type'=>'router']
        ]
    ],
    'student'   => [
        'name'  => '家长手机端',
        'links' => [
            ['text'=>'登录页','link'=>'/login','type'=>'router'],
            ['text'=>'忘记密码','link'=>'/forgotpwd','type'=>'router'],
            ['text'=>'报名信息登记','link'=>'/signup','type'=>'router'],
            ['text'=>'首页','link'=>'/home/home','type'=>'router'],
            ['text'=>'课表','link'=>'/home/schedules','type'=>'router'],
            ['text'=>'考勤','link'=>'/home/attendances','type'=>'router'],
            ['text'=>'课评','link'=>'/home/reviews','type'=>'router'],
            ['text'=>'通知','link'=>'/home/news','type'=>'router'],
            ['text'=>'课程','link'=>'/home/lesson/index','type'=>'router'],
            ['text'=>'我的课程','link'=>'/home/lesson/mylesson','type'=>'router'],
            ['text'=>'我的班级','link'=>'/home/lesson/class','type'=>'router'],
            ['text'=>'我的','link'=>'/my/index','type'=>'router'],
            ['text'=>'我的档案','link'=>'/my/archives','type'=>'router'],
            ['text'=>'我的消息','link'=>'/my/message','type'=>'router'],
            ['text'=>'我的积分','link'=>'/my/credit','type'=>'router'],
            ['text'=>'我的订单','link'=>'/my/orders','type'=>'router'],
            ['text'=>'我的缴费','link'=>'/my/payments','type'=>'router'],
            ['text'=>'投诉建议','link'=>'/my/complaints','type'=>'router'],
        ]
    ]
];