<?php

return [
	'main'	=>	[
		[
			'text'	=> '课程',
			'name'	=> 'lesson',
			'uri'	=> 'lesson',
			'class'	=> 'icon-book',
			//'class'	=> 'lt-basic',
			'sub'	=> [
				['text'=>'导学图','uri'=>'lesson.guide'],
				['text'=>'课程地图','uri'=>'lesson.map'],
				['text'=>'已开课程','uri'=>'lesson.list'],
			]
				
		],
		[
			'text'	=> '教学',
			'name'	=> 'teaching',
			'uri'	=> 'teaching',
			'class'	=> 'icon-teach',
			//'class'	=> 'lt-basic',
			'sub'	=> [
				['text'	=> '班级','uri'=>'teaching.class'],
				['text'  => '课表','uri'=>'teaching.schedule'],
				['text'  => '备课','uri'=>'teaching.prepare'],
				['text'	=> '作业','uri'=>'teaching.homework'],
				['text'	=> '反馈','uri'=>'teaching.feedback'],
			]
				
		],
		[
			'text'	=> '教育',
			'name'	=> 'education',
			'uri'	=> 'education',
			'class'	=> 'icon-education',
			//'class'	=> 'lt-basic',
			'sub'	=> [
				['text'	=> '特服学员','uri'=>'education.sutdent'],
				['text'	=> '成长调查','uri'=>'education.research'],
				['text'	=> '能力测评','uri'=>'education.test'],
				['text'	=> '成长方案','uri'=>'education.project'],
				['text'	=> '成长效果','uri'=>'education.compare'],
				['text'	=> '活动','uri'=>'education.event'],
				['text'	=> '作品册','uri'	=> 'education.artalbum'],
			]
				
		],
		[
			'text'	=> '报表',
			'name'	=> 'report',
			'uri'	=> 'report',
			'class'	=> 'icon-list',
			//'class'	=> 'lt-basic',
			'sub'	=> [
				['text'	=> '汇总报表','uri'=>'report.summary'],
				['text'	=> '学情报表','uri'=>'report.learning'],
				['text'	=> '教学量表','uri'=>'report.teaching'],
				['text'	=> '教育量表','uri'=>'report.education'],
				['text'	=> '活动量表','uri'=>'report.event'],
			]
				
		],
		[
			'text'	=> '我的',
			'name'	=> 'my',
			'uri'	=> 'my',
			'class'	=> 'icon-us'
				
		],
        [
            'text'  => '应用',
            'name'  => 'app',
            'uri'   => 'app',
            'sub'   => [

            ]
        ]

	]
];
