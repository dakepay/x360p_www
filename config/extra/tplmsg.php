<?php
/**
 * 模板消息配置
 * 默认的公众号：学习管家服务号
 */
return [
    //缴费成功通知
    'pay_success'			=> [
        'name'				=> '缴费成功通知',
        'desc'              => '缴费成功通知',
        'sms_switch'		=> 0,
        'sms'				=>  [
            'std_id'        => 0,
            'tpl'			=> '您好，您已成功购买课程!订单号:[订单号],支付金额:[支付金额],订单内容:[课程信息],缴费方式:微信支付'
        ],
        'message'           => [
            'title'         => '缴费成功通知',
            'content'       => '您好，您已成功购买课程!订单号:[订单号],支付金额:[支付金额],订单内容:[课程信息],缴费方式:微信支付',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'ZrxM7xJ8MoJJGg9ZH2odvt27WWDgzPPRWuQ0YBYjcFA',
            'short_id'      => 'OPENTM406872252',
            'tpl_title'     => '支付成功通知',
            'tpl_industry'  => '教育-培训',
            'url'			=> '{base_url}/my/orders/{oid}',
            'data'          => [
                'first'			=> ['你好，你已成功购买课程','#000000'],
                'keyword1'		=> ['微信支付[支付金额]元','#000000'],
                'keyword2'		=> ['[机构名称][校区名称][课程信息]','#0000FF'],
                'keyword3'		=> ['[订单号]','#000000'],
                'remark'		=> ['此条通知可作为收据凭证，感谢您购买我们的服务，祝您生活愉快!','#000000']//备注
            ],
        ],
        'tpl_fields'		=> [
            'pay_amount'	=> '[支付金额]',
            'course_info'	=> '[课程信息]',
            'out_trade_no'  => '[订单号]',
            'org_name'      => '[机构名称]',
            'branch_name'   => '[校区名称]',
        ]
    ],
    
    //课前提醒
    'before_class_push'     => [
        'name'				=> '课前提醒',
        'desc'              => '推送老师备课',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '[学生姓名]家长，您好。请准时参加以下课程：课程名称:[课程名称],上课时间:[上课时间],上课地点:[上课地点],联系电话:[联系电话],温馨提示：请提前做好准备，带好学习用品'
        ],
        'message'           => [
            'title'         => '课前提醒',
            'content'       => '[学生姓名]家长，您好。请准时参加以下课程：课程名称:[课程名称],上课时间:[上课时间],上课地点:[上课地点],联系电话:[联系电话],温馨提示：请提前做好准备，带好学习用品',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> '6epY9c8HP9hN8di44x_MCBQI1zQk1hQFGbznQbr9kjE',
            'short_id'      => 'OPENTM206931461',
            'tpl_title'     => '课前提醒',
            'tpl_industry'  => '教育-培训',
            //'url'			=> '{base_url}/preview_push?ca_id={ca_id}',
            'url' => '',
            'data'          => [
                'first'			=> ['[学生姓名]家长，您好。请准时参加如下课程：','#000000'],
                'keyword1'		=> ['[课程名称]','#000000'],
                'keyword2'		=> ['[上课时间]','#0000FF'],
                'keyword3'		=> ['[上课地点]','#000000'],
                'keyword4'		=> ['[联系电话]','#000000'],
                'remark'		=> ['温馨提示：请提前做好准备，带好学习用品','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'student_name'	=> '[学生姓名]',
            'lesson_name'	=> '[课程名称]',
            'school_time'   => '[上课时间]',
            'address'       => '[上课地点]',
            'mobile'        => '[联系电话]',
        ]
    ],

    //备完课提醒
    'remind_before_class'     => [
        'name'				=> '课前备完课提醒',
        'desc'              => '课前提醒 通知家长记得上课和注意事项。',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '[学生姓名]家长，您好。请准时参加以下课程：课程名称:[课程名称],上课时间:[上课时间],上课地点:[上课地点],联系电话:[联系电话],温馨提示：[温馨提示]'
        ],
        'message'           => [
            'title'         => '课前提醒',
            'content'       => '[学生姓名]家长，您好。请准时参加以下课程：课程名称:[课程名称],上课时间:[上课时间],上课地点:[上课地点],联系电话:[联系电话],温馨提示：[温馨提示]',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> '6epY9c8HP9hN8di44x_MCBQI1zQk1hQFGbznQbr9kjE',
            'short_id'      => 'OPENTM206931461',
            'tpl_title'     => '课前提醒',
            'tpl_industry'  => '教育-培训',
            'url'			=> '{base_url}/home/schedules/{ca_id}',
            'data'          => [
                'first'			=> ['[学生姓名]家长，您好。请准时参加如下课程：','#000000'],
                'keyword1'		=> ['[课程名称]','#000000'],
                'keyword2'		=> ['[上课时间]','#0000FF'],
                'keyword3'		=> ['[上课地点]','#000000'],
                'keyword4'		=> ['[联系电话]','#000000'],
                'remark'		=> ['温馨提示：[温馨提示]','#000000']	//备注  可配置
            ],
        ],
        'tpl_fields'		=> [
            'student_name'	=> '[学生姓名]',
            'lesson_name'	=> '[课程名称]',
            'school_time'   => '[上课时间]',
            'address'       => '[上课地点]',
            'mobile'        => '[联系电话]',
            'remark'        => '[温馨提示]',
        ]
    ],

    //课后推送|作业推送(阳光喔)
    'after_class_push'     => [
        'name'				=> '课后作业推送',
        'desc'              => '课后作业推送',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '您有新的作业了，请查收。!班级名称:[班级名称],作业名称:[作业名称],作业详情:[作业详情],感谢您的查阅，请认真对待，按时完成作业。'
        ],
        'message'           => [
            'title'         => '课后作业通知',
            'content'       => '您有新的作业了，请查收。!班级名称:[班级名称],作业名称:[作业名称],作业详情:[作业详情],感谢您的查阅，请认真对待，按时完成作业。',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'YxJacyXeeMtcfcHMbo4pnxizQxubPiorH97E2kGAFM4',
            'short_id'      => 'OPENTM405774022',
            'tpl_title'     => '作业提醒',
            'tpl_industry'  => '教育-培训',
            //'url'			=> '{base_url}/review_push?ht_id={ht_id}',
            'url'			=> '{base_url}/home/homework/{ht_id}',
            'data'          => [
                'first'			=> ['您有新的作业了，请查收。','#000000'],
                'keyword1'		=> ['[班级名称]','#000000'],
                'keyword2'		=> ['[作业名称]','#0000FF'],
                'keyword3'		=> ['[作业详情]','#000000'],
                'remark'		=> ['感谢您的查阅，请认真对待，按时完成作业。','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'lesson_name'	    => '[班级名称]',
            'homework_name'	=> '[作业名称]',
            'detail'  => '[作业详情]',
        ]
    ],

    //上课时间调整通知
    'alter_class_time'     => [
        'name'				=> '上课时间调整通知',
        'desc'				=> '上课时间调整通知',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '[学生姓名]家长，您好！有一个上课时间调整通知，请及时查看。所在班级：[班级名称],调课原因：[调课原因],上课时间调整到：[上课时间],给您的生活带来的不便敬请谅解！'
        ],
        'message'           => [
            'title'         => 'title！',
            'content'       => 'content',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'cVB8KmVJCnheYKCXqcsoR1plSfhxf_HEA6Y2wNwrgwU',
            'short_id'      => 'OPENTM205990150',
            'tpl_title'     => '上课时间调整通知',
            'tpl_industry'  => '教育-培训',
            'url'			=> '{base_url}',//todo
            'data'          => [
                'first'			=> ['[学生姓名]家长，您好！有一个上课时间调整通知，请及时查看','#000000'],
                'keyword1'		=> ['[班级名称]','#000000'],
                'keyword2'		=> ['[调课原因]','#0000FF'],
                'keyword3'		=> ['[上课时间]','#000000'],
                'remark'		=> ['给您的生活带来的不便敬请谅解！','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'student_name'	    => '[学生姓名]',
            'class_name'	    => '[班级名称]',
            'alter_reason'	    => '[调课原因]',
            'class_time'        => '[上课时间]',
        ]
    ],

    //待办任务提醒
    'transfer_media'     => [
        'name'				=> '公众号上传媒体文件',
        'desc'				=> '公众号上传媒体文件',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> ''
        ],
        'message'           => [
            'title'         => '待办任务',
            'content'       => '待办任务',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'YFTCDC938MNE4f-Qp2DtzLg_CFBxIRFcKiZS4isLj7Y',
            //'short_id'      => 'OPENTM213512088',
            'short_id'      => 'OPENTM217977989',
            'tpl_title'     => '待办任务提醒',
            'tpl_industry'  => 'IT科技-IT软件与服务',
            'url'			=> '{base_url}',//todo
            'data'          => [
                'first'			=> ['您好！您有一个媒体文件需要上传','#000000'],
                'keyword1'		=> ['请在对话框内上传图片、视频或语音！','#000000'],
                'keyword2'		=> ['5分钟内','#000000'],
                'remark'		=> ['请直接在对话框内上传图片，视频和语音！','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
        ]
    ],

    //考勤
    'attendance_inform'     => [
        'name'				=> '考勤通知',
        'desc'				=> '考勤通知',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '新的考勤通知！学员姓名：[姓名],时间：[时间],地点：[地点]',
        ],
        'message'           => [
            'title'         => '收到一条新的考勤通知!',
            'content'       => '新的考勤通知！学员姓名：[姓名],时间：[时间],地点：[地点]!',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'mZjJltJcaIZWmzBPKyoCS-mOlDWITZttyXtifDkWRf8',
            'short_id'      => 'OPENTM202840455',
            'tpl_title'     => '考勤通知',
            'tpl_industry'  => '教育-培训',
            'url'			=> '{base_url}/home/attendances',
            'data'          => [
                'first'			=> ['您好，您在[机构名称]有一条考勤通知！','#000000'],
                'keyword1'		=> ['[姓名]','#000000'],
                'keyword2'		=> ['[时间]','#000000'],
                'keyword3'		=> ['[地点]','#000000'],
                'remark'		=> ['感谢你的使用！','#000000']
            ],
        ],
        'tpl_fields'		=> [
            "student_name" => '[姓名]',
            "time"         => '[时间]',
            "address"      => '[地点]',
            "org_name"      => '[机构名称]',
            'student_lesson_hours' => '[学生总课时]',
            'student_lesson_remain_hours' => '[学生剩余课时]'
        ]
    ],

    //订单购买通知
    'order_purchase_success' => [
        'name'				=> '订单购买通知',
        'desc'				=> '订单购买通知',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '你有新的订单了！订单编号：[订单编号],下单时间：[下单时间]。'
        ],
        'message'           => [
            'title'         => '你有新的订单了！',
            'content'       => '你有新的订单了！订单编号：[订单编号],下单时间：[下单时间]。',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 's160Nr2TJ_Wj7hAr6E9sEvK3AqPE4348-ctDYutJXI0',
            'short_id'      => 'OPENTM405464651',
            'tpl_title'     => '订单购买通知',
            'tpl_industry'  => '教育-培训',
            'url'			=> '{base_url}/my/orders/{oid}',
            'data'          => [
                'first'			=> ['购买订单通知！','#000000'],
                'keyword1'		=> ['[订单编号]','#000000'],
                'keyword2'		=> ['[订单详情]','#000000'],
                'keyword3'		=> ['[下单时间]','#000000'],
                'remark'		=> ['感谢您的购买。','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'order_no'    => '[订单编号]',
            'detail'      => '[订单详情]',
            'create_time' => '[下单时间]',
        ]
    ],

    //课评推送
    'review_push'     => [
        'name'				=> '课评通知',
        'desc'              => '课评通知',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '您有新的课评了，请查收。课程名称：[课程名称]；上课时间：[评价时间]；课评老师：[课评老师]'
        ],
        'message'           => [
            'title'         => '您有新的课评了！',
            'content'       => '课程名称：[课程名称]；上课时间：[评价时间]；课评老师：[课评老师]',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> '5db32DN59RjftZxwaRjMf-z7Y--hPkIxJQ2NmawPJwQ',  # 学习管家服务号
            'short_id'      => 'OPENTM413022972',
            'tpl_title'     => '课后评价提醒',
            'tpl_industry'  => '教育-培训',
            'url'			=> '{base_url}/reviews/{rs_id}',//todo
            'data'          => [
                'first'			=> ['您已收到一条课后评价。','#000000'],
                'keyword1'		=> ['[课程名称]','#000000'],
                'keyword2'		=> ['[评价时间]','#0000FF'],
                'keyword3'		=> ['[课评老师]','#000000'],
                'remark'		=> ['查看课评，全面了解孩子的课堂表现。','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'lesson_name'	=> '[课程名称]',
            'create_time'	=> '[评价时间]',
            'ename'         => '[课评老师]',
        ]
    ],

    //到离校通知
    'attend_school_push'     => [
        'name'				=> '到离校提醒',
        'desc'              => '到离校提醒',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '您好，[学生姓名][到离校]，时间：[时间]，校区：[校区]。'
        ],
        'message'           => [
            'title'         => '您有新的到离校通知！',
            'content'       => '孩子名称：[学生姓名]；时间：[时间]；校区：[校区]',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'L3agDAaLaDhZaemSMK1fSuvbddzUda8wO0XwW6_AVTg',  # 服务号
            'short_id'      => 'OPENTM410857041',
            'tpl_title'     => '到离校提醒',
            'tpl_industry'  => '教育-培训',
            'url'			=> '',//todo
            'data'          => [
                'first'			=> ['您已收到一条[到离校]提醒。','#000000'],
                'keyword1'		=> ['[学生姓名]','#000000'],
                'keyword2'		=> ['[日期]','#0000FF'],
                'keyword3'		=> ['[时间]','#0000FF'],
                'keyword4'		=> ['[校区]','#000000'],
                'remark'		=> ['备注信息','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'action_name'	=> '[到离校]',
            'student_name'	=> '[学生姓名]',
            'create_time'	=> '[日期]',
            'create_day'	=> '[时间]',
            'branch_name'   => '[校区]',
        ]
    ],

    //待办提醒，主要给粉丝发送模板消息
    'to_do'     => [
        'name'				=> '待办事项提醒',
        'desc'              => '待办事项提醒',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '您好，有新的待办提醒。[待办主题]，[待办内容]，[待办日期]。'
        ],
        'message'           => [
            'title'         => '您好，有新的待办提醒。',
            'content'       => '[待办主题]，[待办内容]，[待办日期]。',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'EvtGLtAHaKpZI914mmBt2ptAZGbit7IzQnH0z0aZFyM',  # 服务号
            //'short_id'      => 'OPENTM406777482',
            'short_id'      => 'OPENTM217977989',
            'tpl_title'     => '待办事项提醒',
            'tpl_industry'  => '教育-培训',
            'url'			=> '{url}',
            'data'          => [
                'first'			=> ['您已收到一条待办提醒。','#000000'],
                'keyword1'		=> ['[待办主题]','#000000'],
                'keyword2'		=> ['[待办内容]','#0000FF'],
                'keyword3'		=> ['[待办日期]','#0000FF'],
                'remark'		=> ['[备注信息]','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'subject'	=> '[待办主题]',
            'content'	=> '[待办内容]',
            'date'	=> '[待办日期]',
            'remark' => '[备注信息]'
        ]
    ],

    //工作台待办事项提醒
    'back_log'     => [
        'name'				=> '待办事项提醒',
        'desc'              => '待办事项提醒',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '您好，有新的待办提醒。[待办主题]，[待办内容]，[待办日期]。'
        ],
        'message'           => [
            'title'         => '您好，有新的待办提醒。',
            'content'       => '[待办主题]，[待办内容]，[待办日期]。',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'EvtGLtAHaKpZI914mmBt2ptAZGbit7IzQnH0z0aZFyM',  # 服务号
            //'short_id'      => 'OPENTM406777482',
            'short_id'      => 'OPENTM217977989',
            'tpl_title'     => '待办事项提醒',
            'tpl_industry'  => '教育-培训',
            'url'			=> '',//todo
            'data'          => [
                'first'			=> ['您已收到一条待办提醒。','#000000'],
                'keyword1'		=> ['[待办内容]','#0000FF'],
                'remark'		=> ['[备注信息]','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'content'	=> '[待办内容]',
        ]
    ],

    //工作台公告推送
    'broadcast'     => [
        'name'				=> '公告推送',
        'desc'              => '公告推送',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '您好，有新的公告提醒。'
        ],
        'message'           => [
            'title'         => '您好，有新的公告提醒。',
            'content'       => '[公告内容]，[公告日期]。',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'EvtGLtAHaKpZI914mmBt2ptAZGbit7IzQnH0z0aZFyM',  # 服务号
            //'short_id'      => 'OPENTM406777482',
            'short_id'      => 'OPENTM217977989',
            'tpl_title'     => '公告推送',
            'tpl_industry'  => '教育-培训',
            'url'			=> '',//todo
            'data'          => [
                'first'			=> ['您已收到一条公告提醒。','#000000'],
                'keyword1'		=> ['[公告标题]','#0000FF'],
                'keyword2'		=> ['[公告内容]','#0000FF'],
                'remark'		=> ['[备注信息]','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'title'	=> '[公告标题]',
            'content'	=> '[公告内容]',
        ]
    ],

    //课程安排提醒
    'course_remind'     => [
        'name'				=> '课程安排提醒',
        'desc'              => '课程安排提醒',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '您收到一条课程安排，请查收。上课时间：[上课时间]；上课地点：[上课地点]；上课老师：[上课老师]；课程名称：[课程名称]；'
        ],
        'message'           => [
            'title'         => '您收到一条课程安排',
            'content'       => '上课时间：[上课时间]；上课地点：[上课地点]；上课老师：[上课老师]；课程名称：[课程名称]；',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'vzZ0eNXMRewFlERnaRz2IoMdQXKJ2jHbbuoyDArhqDc',  # 学习管家服务号
            'short_id'      => 'OPENTM207867515',
            'tpl_title'     => '课程安排提醒',
            'tpl_industry'  => '教育-培训',
            'url'			=> '',//todo
            'data'          => [
                'first'			=> ['您已收到一条课程安排。','#000000'],
                'keyword1'		=> ['[上课时间]','#000000'],
                'keyword2'		=> ['[上课地点]','#0000FF'],
                'keyword3'		=> ['[上课老师]','#000000'],
                'keyword4'		=> ['[课程名称]','#000000'],
                'remark'		=> ['请提前准备，准时出席。','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'lesson_time'	=> '[上课时间]',
            'classroom'	=> '[上课地点]',
            'ename'	=> '[上课老师]',
            'lesson_name'	=> '[课程名称]',
        ]
    ],
    
    //学习反馈通知
    'study_situation'     => [
        'name'				=> '学习反馈通知',
        'desc'              => '学习反馈通知',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'apply_tpl'     => '${name}家长，您好!就刚才与您沟通的孩子学习问题我们生成了一份报告 ，请关注公众号“学习管家服务号"，回复关键词"${key}"获取查看。',
            'tpl'			=> '[学生姓名]家长，您好!就刚才与您沟通的孩子学习问题我们生成了一份报告 ，请关注公众号“学习管家服务号"，回复关键词"[回复关键词]"获取查看。'
        ],
        'message'           => [
            'title'         => '学习反馈通知',
            'content'       => '家长，您好。收到关于[学生姓名]的学习反馈。',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> '',
            'short_id'      => 'OPENTM413793286',
            'tpl_title'     => '学习反馈通知',
            'tpl_industry'  => '教育-培训',
            'url' => '{base_url}/student#/sq?id={short_id}',
            'data'          => [
                'first'			=> ['家长，您好。收到关于[学生姓名]的学习反馈','#000000'],
                'keyword1'		=> ['[学生姓名]','#000000'],
                'keyword2'		=> ['[评语]','#0000FF'],
                'remark'		=> ['点击查看详细分析','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'student_name'	=> '[学生姓名]',
            'key'           => '[回复关键词]',
            'remark'	    => '[评语]',
        ]
    ],
    //市场派单通知家长
    'clue_to_student'=> [
        'name'              => '市场派单短信通知学生家长',
        'desc'              => '市场派单短信通知学生家长',
        'tpl_fields'        => [
            'name'  => '[学生姓名]',
            'ename' => '[跟单人员]'
        ],
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'apply_tpl'     => '${name}家长，您好!我们的课程顾问${ename}将在15分钟内与您取得联系。',
            'tpl'			=> '[学生姓名]家长，您好!我们的课程顾问[跟单人员]将在15分钟内与您取得联系。'
        ],
    ],
    
    //市场派单通知员工
    'clue_to_employee'=>    [
        'name'              => '市场派单微信通知员工',
        'desc'              => '市场派单微信通知员工',
        'tpl_fields'        => [
            'name'  => '[姓名]',
        ],
        'weixin_switch'     => 1,
        'weixin'            =>[
            'template_id'	=> '',
            'short_id'      => 'OPENTM413793286',
            'tpl_title'     => '派单通知',
            'tpl_industry'  => '教育-培训',
            'url' => '{base_url}/student#/sq?id={short_id}',
            'data'          => [
                'first'			=> ['您有一条派单通知','#000000'],
                'keyword1'		=> ['[姓名]','#000000'],
                'remark'		=> ['请于15分钟内于客户取得联系','#000000']	//备注
            ],
        ]
    ],
    
    //课前提醒通知
    'remind_teacher'     => [
        'name'				=> '课前提醒通知',
        'desc'              => '上课前通知老师',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '您好，有新的上课。[待办主题]，[待办内容]，[待办日期]。'
        ],
        'message'           => [
            'title'         => '您好，有新的授课信息。',
            'content'       => '[待办主题]，[待办内容]，[待办日期]。',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'EvtGLtAHaKpZI914mmBt2ptAZGbit7IzQnH0z0aZFyM',  # 服务号
            //'short_id'      => 'OPENTM406777482',
            'short_id'      => 'OPENTM217977989',
            'tpl_title'     => '授课提醒',
            'tpl_industry'  => '教育-培训',
            'url'			=> '{url}',
            'data'          => [
                'first'			=> ['您已收到一条授课提醒。','#000000'],
                'keyword1'		=> ['[授课主题]','#000000'],
                'keyword2'		=> ['[授课内容]','#0000FF'],
                'keyword3'		=> ['[授课日期]','#0000FF'],
                'remark'		=> ['[备注信息]','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'subject'	=> '[授课主题]',
            'content'	=> '[授课内容]',
            'date'	=> '[授课日期]',
            'remark' => '[备注信息]'
        ]
    ],

    //外教端翻译每日统计
    'ft_review_remind'     => [
        'name'				=> '待办事项提醒',
        'desc'              => '待办事项提醒',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '您好，有新的待办提醒。[统计内容]。'
        ],
        'message'           => [
            'title'         => '您好，有新的待办提醒。',
            'content'       => '[统计内容]。',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> 'EvtGLtAHaKpZI914mmBt2ptAZGbit7IzQnH0z0aZFyM',  # 服务号
            //'short_id'      => 'OPENTM406777482',
            'short_id'      => 'OPENTM217977989',
            'tpl_title'     => '待办事项提醒',
            'tpl_industry'  => '教育-培训',
            'url'			=> '',//todo
            'data'          => [
                'first'			=> ['您已收到一条每日翻译统计。','#000000'],
                'keyword1'		=> ['[统计内容]','#0000FF'],
                'remark'		=> ['[备注信息]','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'content'	=> '[统计内容]',
        ]
    ],
    
    //作业驳回通知
    'homework_rejected'     => [
        'name'				=> '作业驳回通知',
        'desc'              => '作业驳回通知',
        'sms_switch'		=> 0,
        'sms'				=> [
            'std_id'        => 0,
            'tpl'			=> '[学生姓名]家长，您好!收到关于[学生姓名]的作业驳回通知 ，请关注公众号“学习管家服务号"查看。'
        ],
        'message'           => [
            'title'         => '作业驳回通知',
            'content'       => '家长，您好。收到关于[学生姓名]的作业驳回通知。',
        ],
        'weixin_switch'		=> 1,
        'weixin'			=> [
            'template_id'	=> '',
            'short_id'      => 'OPENTM413793286',
            'tpl_title'     => '学习反馈通知',
            'tpl_industry'  => '教育-培训',
            'url' => '{base_url}/student#/home/homework?id={hc_id}',
            'data'          => [
                'first'			=> ['家长，您好。收到关于[学生姓名]的作业驳回通知。','#000000'],
                'keyword1'		=> ['[学生姓名]','#000000'],
                'keyword2'		=> ['[评语]','#0000FF'],
                'remark'		=> ['备注信息','#000000']	//备注
            ],
        ],
        'tpl_fields'		=> [
            'student_name'	=> '[学生姓名]',
            'remark'	    => '[评语]',
        ]
    ],
];
