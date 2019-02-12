<?php
//教育助手移动端定义
return [
    'sysname'       => '校360教育助手',
    'home_menu'     => [
        //老师
        ['rids'=>[1,4],'name'=>'rollcall','label'=>'点名','link'=>'./rollcall','icon'=>'http://sp1.xiao360.com/static/ui/student/menu_arrange.png','enable'=>1],
        ['rids'=>[1,4],'name'=>'comment','label'=>'课评','link'=>'./comment','icon'=>'http://sp1.xiao360.com/static/ui/student/menu_comment.png','enable'=>1],
        ['rids'=>[1,4],'name'=>'schedule','label'=>'课表','link'=>'./schedule','icon'=>'http://sp1.xiao360.com/static/ui/student/menu_attendence.png','enable'=>1],
        ['rids'=>[1,4],'name'=>'news','label'=>'通知','link'=>'./news','icon'=>'http://sp1.xiao360.com/static/ui/student/menu_message.png','enable'=>1],
        ['rids'=>[1,4],'name'=>'homework','label'=>'作业','link'=>'./homework','icon'=>'http://sp1.xiao360.com/static/ui/student/menu_homework.png','enable'=>1],
        ['rids'=>[1,4],'name'=>'artwork','label'=>'作品','link'=>'./artwork','icon'=>'http://sp1.xiao360.com/static/ui/student/menu_zuopin.png','enable'=>1],
        ['rids'=>[1,4],'name'=>'prepare','label'=>'备课','link'=>'./prepare','icon'=>'http://sp1.xiao360.com/static/ui/student/menu_beike.png','enable'=>1],
        ['rids'=>[1,4],'name'=>'exam','label'=>'成绩','link'=>'./exam','icon'=>'http://sp1.xiao360.com/static/ui/student/menu_score.png','enable'=>1],
        ['rids'=>[1,4],'name'=>'exam','label'=>'成长对比','link'=>'./growth_contrast','icon'=>'http://sp1.xiao360.com/static/ui/student/menu_growth.png','enable'=>1],


        //咨询师
        ['rids'=>[7],'name'=>'customer','label'=>'客户名单','link'=>'/recruit/list','icon'=>'http://sp1.xiao360.com/static/ui/m/kehu.png','enable'=>1],
        ['rids'=>[7],'name'=>'following','label'=>'跟进客户','link'=>'/recruit/following','icon'=>'http://sp1.xiao360.com/static/ui/m/genjin.png','enable'=>1],
        ['rids'=>[7],'name'=>'customer_add','label'=>'添加客户','link'=>'/recruit/add','icon'=>'http://sp1.xiao360.com/static/ui/m/tianjiakehu.png','enable'=>1],
        ['rids'=>[7],'name'=>'audition','label'=>'试听名单','link'=>'/recruit/audition','icon'=>'http://sp1.xiao360.com/static/ui/m/tongzhi.png','enable'=>1],
        //市场人员

        //校长
        ['rids'=>[3],'name'=>'report_daily','label'=>'运营总表','link'=>'/report/dailyreport','icon'=>'http://sp1.xiao360.com/static/ui/m/yunying.png','enable'=>1],
        ['rids'=>[3],'name'=>'report_iae','label'=>'收支总表','link'=>'/report/incomeandexpend','icon'=>'http://sp1.xiao360.com/static/ui/m/shouzhi.png','enable'=>1],
        ['rids'=>[3],'name'=>'report_customer','label'=>'招生总表','link'=>'/report/customer','icon'=>'http://sp1.xiao360.com/static/ui/m/zhaosheng.png','enable'=>1],
        ['rids'=>[3],'name'=>'report_on','label'=>'在读学员','link'=>'/report/on','icon'=>'http://sp1.xiao360.com/static/ui/m/xueyuan.png','enable'=>1],
        ['rids'=>[3],'name'=>'report_income','label'=>'课耗报表','link'=>'/report/income','icon'=>'http://sp1.xiao360.com/static/ui/m/kehao.png','enable'=>1],
        ['rids'=>[3],'name'=>'report_performance','label'=>'业绩报表','link'=>'/report/performance','icon'=>'http://sp1.xiao360.com/static/ui/m/yeji.png','enable'=>1],
        ['rids'=>[3],'name'=>'report_attendance','label'=>'考勤总表','link'=>'/report/attendance','icon'=>'http://sp1.xiao360.com/static/ui/m/kaoqin.png','enable'=>1]

    ],
    'tab_bar'   => [
        [
            'label'=>'首页',
            'icon'=>'http://sp1.xiao360.com/static/ui/m/home.png',
            'icon_sel'=>'http://sp1.xiao360.com/static/ui/m/home_active.png',
            'name'=>'home',
            'link'=>'/home/home',
            'per'=>[]
        ],
        [
            'label' => '班级',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/class.png',
            'icon_sel'=>'http://sp1.xiao360.com/static/ui/student/class_active.png',
            'name'=>'class',
            'link'=>'/class/class',
            'per'=>[1,4]
        ],
        [
            'label' => '学员',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/student.png',
            'icon_sel'=>'http://sp1.xiao360.com/static/ui/student/student_active.png',
            'name'=>'edu',
            'link'=>'/student/student',
            'per'=>[1,4]
        ],
        [
            'label' => '我的',
            'icon'  => 'http://sp1.xiao360.com/static/ui/m/mine.png',
            'icon_sel'=>'http://sp1.xiao360.com/static/ui/m/mine_active.png',
            'name'=>'my',
            'link'=>'/my/my',
            'per'=>[]
        ]
    ],
    'my_menu'   => [        //我的菜单
        [
            'name'=>'medias',
            'label'=>'我的文件',
            'icon'=>'ios-folder-outline',
            'link'=>'/home/medias',
            'enable'=>true
        ],
        [
            'name'=>'output',
            'label'=>'我的产出',
            'icon'=>'ios-analytics-outline',
            'link'=>'/report/output',
            'enable'=>true
        ]
    ]
];