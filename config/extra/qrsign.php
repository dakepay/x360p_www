<?php
return [
    'banner'        =>      '/static/img/org/qrsign-bg.png',
    'title'         =>      '请留下您的信息',
    'desc'          =>      '提交您的联系信息,我们将有专业的课程顾问与您联系',
    'fields'        => [
        ['field'=>'grade','label'=>'年　　级','placeholder'=>'请输入年级','enable'=>true, 'default_value'=>''],
        ['field'=>'email','label'=>'邮　　箱','placeholder'=>'请输入Email地址','enable'=>true,'default_value'=>''],
        ['field'=>'remark','label'=>'备　　注','placeholder'=>'其他补充信息','enable'=>true,'default_value'=>'']
    ],
    'must_fields'	=> [		//必填字段
    	 ['field'=>'birth_time','label'=>'出生日期','placeholder'=>'请选择出生日期','enable'=>true,'default_value'=>'2012-01-01'],
    ],
    'msg'           => [
        'title'     => '提交成功',
        'description'   => '您的信息我们已经收到，我们会尽快与您联系!',
	'redirect_url'	=> ''
    ],
    'logo'          =>  'http://s1.xiao360.com/x360p/images/logo.png'
];