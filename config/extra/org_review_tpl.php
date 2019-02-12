<?php
//点评模板默认
return array(
	'common_fields'	=> array(
		'lesson_content'	=>	array(
			'enable'			=> 1,
			'student_view'		=> 1,
			'label'				=> '课堂内容',
			'tpl'				=> ''
		),
		'lesson_after_task'	=> array(
			'enable'			=> 1,
			'student_view'		=> 1,
			'label'				=> '课后作业',
			'tpl'				=> ''
		),
		'next_task'			=> array(
			'enable'			=> 0,
			'student_view'		=> 0,
			'label'				=> '下次课内容'
		)
	),	//通用字段
	'score_fields'	=> array(
		'score'		=> array(
			'enable'	=> 1,
			'label'		=> '课堂表现',
			'default'	=> 5
		),
		'score1'		=> array(
			'enable'	=> 0,
			'label'		=> '打分项1',
			'default'	=> 5
		),
		'score2'		=> array(
			'enable'	=> 0,
			'label'		=> '打分项2',
			'default'	=> 5
		),
		'score3'		=> array(
			'enable'	=> 0,
			'label'		=> '打分项3',
			'default'	=> 5
		),
		'score4'		=> array(
			'enable'	=> 0,
			'label'		=> '打分项4',
			'default'	=> 5
		),
		'score5'		=> array(
			'enable'	=> 0,
			'label'		=> '打分项5',
			'default'	=> 5
		),
		'score6'		=> array(
			'enable'	=> 0,
			'label'		=> '打分项6',
			'default'	=> 5
		),
		'score7'		=> array(
			'enable'	=> 0,
			'label'		=> '打分项7',
			'default'	=> 5
		),
		'score8'		=> array(
			'enable'	=> 0,
			'label'		=> '打分项8',
			'default'	=> 5
		),
		'score9'		=> array(
			'enable'	=> 0,
			'label'		=> '打分项9',
			'default'	=> 5
		),
	),	//打分项
	'student_detail'	=> array(
		'default'		=> array(
			'label'		=> '个人点评',
			'tpl'		=> ''
		),
		'special'		=> array(
			array(
				'enable'	=> 0,
				'duration'	=> 'week',
				'times'		=> 8,
				'label'		=> '8周点评',
				'tpl'		=> ''
			)
		)
	)
);

