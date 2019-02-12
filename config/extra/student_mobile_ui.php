<?php
return [
    'sysname' => '学习管家',
    'home_menu' => [
        [
            'name' => 'schedules',
            'label' => '课表',
            'link' => './schedules',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/menu_arrange.png',
            'enable' => 1
        ],
        [
            'name' => 'attendances',
            'label' => '考勤',
            'link' => './attendances',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/menu_attendence.png',
            'enable' => 1
        ],
        [
            'name' => 'homework',
            'label' => '作业',
            'link' => './homework',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/menu_homework.png',
            'enable' => 1
        ],
        [
            'name' => 'reviews',
            'label' => '课评',
            'link' => './reviews',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/menu_comment.png',
            'enable' => 1
        ],
        [
            'name' => 'news',
            'label' => '通知',
            'link' => './news',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/menu_message.png',
            'enable' => 1
        ],
        [
            'name' => 'artwork',
            'label' => '作品',
            'link' => './artwork',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/menu_zuopin.png',
            'enable' => 1
        ],
         [
             'name' => 'book',
             'label' => '图书',
             'link' => './book',
             'icon' => 'http://sp1.xiao360.com/static/ui/student/menu_book.png',
             'enable' => 0
         ]
    ],
    'tab_bar' => [
        [
            'label' => '首页',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/home.png',
            'icon_sel' => 'http://sp1.xiao360.com/static/ui/student/home_active.png',
            'name' => 'home',
            'link' => '/home/home',
            'enable' => 1,
            'is_system' => 1
        ],
        [
            'label' => '课程',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/lesson.png',
            'icon_sel' => 'http://sp1.xiao360.com/static/ui/student/lesson_active.png',
            'name' => 'lesson',
            'link' => '/lesson/lesson',
            'enable' => 1
        ],
        [
            'label' => '成长',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/growth.png',
            'icon_sel' => 'http://sp1.xiao360.com/static/ui/student/growth_active.png',
            'name' => 'edu',
            'link' => '/growth/growth',
            'enable' => 1
        ],
        [
            'label' => '活动',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/event.png',
            'icon_sel' => 'http://sp1.xiao360.com/static/ui/student/event_active.png',
            'name' => 'event',
            'link' => '/event/event',
            'enable' => 1
        ],
        [
            'label' => '我的',
            'icon' => 'http://sp1.xiao360.com/static/ui/student/mine.png',
            'icon_sel' => 'http://sp1.xiao360.com/static/ui/student/mine_active.png',
            'name' => 'my',
            'link' => '/my/my',
            'enable' => 1,
            'is_system' => 1
        ]
    ],
    'lesson_menu'   => [            //课程菜单
        [
            'label' => '我的课程',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/wdkc.png',
            'name'  => 'mylesson',
            'link'  => './mylesson',
            'enable' => 1
        ],
        /*
        [
            'label' => '课程导航',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/kcdh.png',
            'name'  => 'lessonmap',
            'link'  => '/lessonmap',
            'enable' => 1
        ],
        */
        [
            'label' => '我的班级',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/wdbj.png',
            'name'  => 'myclass',
            'link'  => './class',
            'enable' => 1
        ],
        [
            'label' => '预约补课',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/yybk.png',
            'name'  => 'appoint',
            'link'  => './appointment',
            'enable' => 1
        ],
        [
            'label' => '预约活动课',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/yybk.png',
            'name'  => 'appoint',
            'link'  => './appointment2',
            'enable' => 1
        ],
        [
            'label' => '预习',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/yx.png',
            'name'  => 'prepare',
            'link'  => '/home/prepare',
            'enable' => 1
        ],
    ],
    'edu_menu'      => [            //成长菜单
        [
            'label' => '成长关注',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/czgz.png',
            'name'  => 'focus',
            'link'  => './file_record',
            'enable' => 1
        ],
        [
            'label' => '成长对比',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/czdb.png',
            'name'  => 'compare',
            'link'  => './contrast',
            'enable' => 1
        ],
        [
            'label' => '作品册',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/zpc.png',
            'name'  => 'artwork',
            'link'  => '/home/artwork',
            'enable' => 1
        ]
    ],
    'event_menu' 	=> [
    		[
	            'label' => '喔团队',
	            'icon'  => 'http://sp1.xiao360.com/static/ui/student/otd.png',
	            'name'  => 'focus',
	            'link'  => './page',
	            'enable' => 1,
	            'is_system' => 1
        	],
        	[
	            'label' => '开课讲座',
	            'icon'  => 'http://sp1.xiao360.com/static/ui/student/kkjz.png',
	            'name'  => 'focus',
	            'link'  => './list?id=180',
	            'enable' => 1
        	],
        	[
	            'label' => '班级活动',
	            'icon'  => 'http://sp1.xiao360.com/static/ui/student/bjhd.png',
	            'name'  => 'focus',
	            'link'  => './list?id=181',
	            'enable' => 1
        	],
        	[
	            'label' => '线上活动',
	            'icon'  => 'http://sp1.xiao360.com/static/ui/student/xshd.png',
	            'name'  => 'focus',
	            'link'  => './list?id=182',
	            'enable' => 1
        	],
        	[
	            'label' => '单元展示',
	            'icon'  => 'http://sp1.xiao360.com/static/ui/student/dyzs.png',
	            'name'  => 'focus',
	            'link'  => './list?id=183',
	            'enable' => 1
        	],
    ],
    'my_menu'       => [            //我的菜单
        [
            'label' => '推荐有奖',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/my-number.png',
            'name'  => 'recommend',
            'link'  => '/recommend',
            'enable' => 1
        ],
        [
            'label' => '我的成绩',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/exam-score.png',
            'name'  => 'exam',
            'link'  => './score',
            'enable' => 1
        ],
        [
            'label' => '我的订单',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/order-from.png',
            'name'  => 'order',
            'link'  => './orders',
            'enable' => 1
        ],
        [
            'label' => '电子钱包',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/e-wallet.png',
            'name'  => 'wallet',
            'link'  => './wallet',
            'enable' => 1
        ],
        [
            'label' => '我的积分',
            'icon'  => 'http://sp1.xiao360.com/static/ui/student/integral.png',
            'name'  => 'credit',
            'link'  => './credit',
            'enable' => 1
        ],
    ]
];