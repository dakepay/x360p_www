<?php
/**
 * 菜单定义说明
 * 菜单字段 need_user_field   需要用户信息的字段要求
 * 菜单字段 need_client_field 需要用户的客户信息的字段要求
 */
return [
    'main'	=>	[
        [   'text'      => '工作台',
            'name'      => 'dashboard',
            'uri'       => 'dashboard',
            'ismenu'	=> false,
            'sub'	=> [
                ['text' => '咨询登记', 'uri'=>'dashboard.addcustomer','ismenu'=>false ],
                ['text' => '客户跟进', 'uri'=>'dashboard.follow','ismenu'=>false ],
                ['text' => '报名', 'uri'=>'dashboard.signup','ismenu'=>false ],
                ['text' => '缴费', 'uri'=>'dashboard.payment','ismenu'=>false ],

                ['text' => '考勤', 'uri'=>'dashboard.attendance','ismenu'=>false ],
                ['text' => '结转', 'uri'=>'dashboard.transfer','ismenu'=>false ],
                ['text' => '退费', 'uri'=>'dashboard.refund','ismenu'=>false ],
                ['text' => '请假', 'uri'=>'dashboard.leave','ismenu'=>false ],
                ['text' => '发短信', 'uri'=>'dashboard.sendsms','ismenu'=>false ],
                ['text' => '发微信', 'uri'=>'dashboard.sendwx','ismenu'=>false ],

                ['text' => '课次预警', 'uri'=>'dashboard.lessonwarn','ismenu'=>false ],
                ['text' => '排课预警', 'uri'=>'dashboard.timeswarn','ismenu'=>false ],
                ['text' => '流失预警', 'uri'=>'dashboard.lostwarn','ismenu'=>false ],
                ['text' => '试听提醒', 'uri'=>'dashboard.todaytrial','ismenu'=>false ],
                ['text' => '生日提醒', 'uri'=>'dashboard.birthday','ismenu'=>false ],
                ['text' => '学员统计', 'uri'=>'dashboard.studentstats','ismenu'=>false ],
                [
                    'text' => '公告管理','uri'=>'dashboard.broadcast','ismenu'=>false,'sub'=>[
                    ['text'=>'添加公告','uri'=>'broadcast.add','ismenu'=>false],
                    ['text'=>'编辑公告','uri'=>'broadcast.edit','ismenu'=>false],
                    ['text'=>'删除公告','uri'=>'broadcast.delete','ismenu'=>false]
                ]

                ],
                [
                    'text' => '待办管理','uri'=>'dashboard.backlogs','ismenu'=>false,'sub'=>[
                    ['text'=>'添加待办','uri'=>'backlogs.add','ismenu'=>false],
                    ['text'=>'编辑待办','uri'=>'backlogs.edit','ismenu'=>false],
                    ['text'=>'删除待办','uri'=>'backlogs.delete','ismenu'=>false],
                    ['text'=>'标记完成','uri'=>'backlogs.finished','ismenu'=>false],
                    ['text'=>'标记待办','uri'=>'backlogs.unfinished','ismenu'=>false],
                    ['text'=>'标记废弃','uri'=>'backlogs.addline','ismenu'=>false],
                    ['text'=>'取消废弃','uri'=>'backlogs.cancelline','ismenu'=>false]
                ]

                ],
                ['text' => '考勤看板', 'uri'=>'dashboard.side_attendance_panel','ismenu'=>false]
            ]
        ],
        [
            'text'	=> '招生',
            'name'	=> 'recruiting',
            'uri'	=> 'recruiting',
            'class'	=> 'icon-zhaosheng',
            'sub'	=> [
                [
                    'text' => '市场名单',
                    'name' => 'recruiting.market',
                    'uri' => 'recruiting.market',
                    'hidesub' => true,
                    'sub'=>[
                        ['text' => '概览','uri'=>'market.overview'],
                        ['text' => '来源渠道','uri'=>'market.channel','sub'=>[
                            ['text' => '添加来源渠道','uri'=>'channel.add','ismenu'=>false],
                            ['text' => '编辑来源渠道','uri'=>'channel.edit','ismenu'=>false],
                            ['text' => '删除来源渠道','uri'=>'channel.delete','ismenu'=>false],
                            ['text' => '录入市场名单','uri'=>'channel.addclue','ismenu'=>false],
                            ['text' => '批量导入市场名单','uri'=>'channel.importclue','ismenu'=>false],
                            ['text' => '扫码录入市场名单','uri'=>'channel.scannerclue','ismenu'=>false],

                        ]],
                        ['text' =>'市场名单','uri'=>'market.clue','sub'=>[
                            ['text' => '录入名单','uri'=>'clue.add','ismenu'=>false],
                            ['text' => '编辑名单','uri'=>'clue.edit','ismenu'=>false],
                            ['text' => '删除名单','uri'=>'clue.delete','ismenu'=>false],

                            ['text' => '导入名单','uri'=>'clue.import','ismenu'=>false],
                            ['text' => '导出名单','uri'=>'clue.export','ismenu'=>false],
                            ['text' => '分配名单','uri'=>'clue.distribute','ismenu'=>false],
                            ['text' => '转为客户','uri'=>'clue.transfer','ismenu'=>false],
                            ['text' => '批量操作','uri'=>'clue.batch','ismenu'=>false]
                        ]],
                        ['text' =>'推荐名单','uri'=>'market.recommend']
                ]],
                [
                    'text'	=> '客户名单',
                    'name'  => 'recruiting.list',
                    'uri'	=> 'recruiting.list',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'list.overview'],
                        ['text' => '客户名单','uri'=>'list.customer','sub'=>[
                            ['text' => '添加客户','uri'=>'customer.add','ismenu'=>false],
                            ['text' => '批量导入客户','uri'=>'customer.import','ismenu'=>false],
                            ['text' => '导出客户名单','uri'=>'customer.export','ismenu'=>false],
                            ['text' => '批量操作','uri'=>'customer.batch','ismenu'=>false],
                            ['text' => '报名','uri'=>'customer.signup','ismenu'=>false],
                            ['text' => '跟单','uri'=>'customer.follow','ismenu'=>false],
                            ['text' => '编辑客户','uri'=>'customer.edit','ismenu'=>false],
                            ['text' => '删除客户','uri'=>'customer.delete','ismenu'=>false],

                            ['text' => '分配客户责任人','uri'=>'customer.distribution','ismenu'=>false]
                        ]],
                        ['text' => '流失学员','uri'=>'list.lost'],
                        ['text' => '流失预警学员','uri'=>'list.lostwarn']
                    ]
                ],
                [
                    'text'  => '跟进情况',
                    'name'  => 'recruiting.following',
                    'uri'   => 'recruiting.following',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'following.overview'],
                        ['text' => '跟进记录','uri'=>'following.flist','sub'=>[
                            ['text' => '新增跟进记录','uri'=>'flist.add','ismenu'=>false],
                            ['text' => '删除跟进记录','uri'=>'flist.delete','ismenu'=>false],
                            ['text' => '编辑跟进记录','uri'=>'flist.edit','ismenu'=>false],
                            ['text' => '导出跟进记录','uri'=>'flist.export','ismenu'=>false],
                        ]],
                        ['text' => '转化统计','uri'=>'following.summary'],
                    ]
                ],
                [
                    'text'  => '试听管理',
                    'name'  => 'recruiting.audition',
                    'uri'   => 'recruiting.audition',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'audition.overview'],
                        ['text' => '试听记录','uri'=>'audition.aslist','sub'=>[
                            ['text' => '试听安排','uri'=>'aslist.add','ismenu'=>false],
                            ['text' => '取消试听','uri'=>'aslist.delete','ismenu'=>false],
                            ['text' => '登记试听','uri'=>'aslist.complete','ismenu'=>false]
                        ]],
                        ['text' => '试听排课','uri'=>'audition.aclist','sub'=>[
                            ['text' => '添加试听排课','uri'=>'aclist.add','ismenu'=>false],
                            ['text' => '编辑试听排课','uri'=>'aclist.edit','ismenu'=>false],
                            ['text' => '删除试听排课','uri'=>'aclist.delete','ismenu'=>false],
                            ['text' => '试听学员管理','uri'=>'aclist.student','ismenu'=>false]
                        ]],
                    ]
                ],
                [
                    'text' => '体验课管理',
                    'name' => 'recruiting.demo',
                    'uri'  => 'recruiting.demo',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'demo.overview'],
                        ['text' => '体验课课程','uri'=>'demo.lesson','sub'=>[
                            ['text'=>'添加体验课','uri'=>'demolesson.add','ismenu'=>false],
                            ['text'=>'编辑体验课','uri'=>'demolesson.edit','ismenu'=>false],
                            ['text'=>'删除体验课','uri'=>'demolesson.delete','ismenu'=>false],
                        ]],
                        ['text' => '体验课报名','uri'=>'demo.signup','sub'=>[
                            ['text' => '分班','uri'=>'demoorder.asclass','ismenu'=>false],
                            ['text' => '补缴','uri'=>'demoorder.makeup','ismenu'=>false],
                            ['text' => '结转','uri'=>'demoorder.transfer','ismenu'=>false],
                            ['text' => '删除订单','uri'=>'demoorder.delete','ismenu'=>false],
                            ['text' => '继续支付','uri'=>'demoorder.pay','ismenu'=>false],
                            ['text' => '退费','uri'=>'demoorder.refund','ismenu'=>false]
                        ]],
                        ['text' => '体验课班级','uri'=>'demo.class','sub'=>[
                            ['text' => '创建体验班','uri'=>'democlass.add','ismenu'=>false],
                            ['text' => '编辑体验班','uri'=>'democlass.edit','ismenu'=>false],

                            ['text' => '班级排课管理','uri'=>'democlass.arrange','ismenu'=>false],
                            ['text' => '班级学员管理','uri'=>'democlass.students','ismenu'=>false],
                            ['text' => '删除','uri'=>'democlass.delete','ismenu'=>false],
                            ['text' => '结课','uri'=>'democlass.close','ismenu'=>false],
                        ]],
                    ]
                ]
            ]
        ],
        [
            'text'	=> '运营',
            'name'	=> 'business',
            'uri'	=> 'business',
            'class'	=> 'icon-yunying',
            'sub'	=> [
                [
                    'text'	=> '学员管理',
                    'name'  => 'business.student',
                    'uri'	=> 'business.student',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'student.overview'],
                        ['text' => '学员档案','uri'=>'student.archive','sub'=>[
                            ['text' => '创建档案','uri'=>'student.add','ismenu'=>false],
                            ['text' => '学员发卡','uri'=>'student.issueCard','ismenu'=>false],
                            ['text' => '编辑档案','uri'=>'student.edit','ismenu'=>false],
                            ['text' => '导出档案','uri'=>'student.export','ismenu'=>false],

                            ['text' => '结课','uri'=>'student.closeLesson','ismenu'=>false],
                            ['text' => '结转','uri'=>'student.transfer','ismenu'=>false],
                            ['text' => '转让余额','uri'=>'student.transmoney','ismenu'=>false],
                            ['text' => '转让课时','uri'=>'student.transhour','ismenu'=>false],
                            ['text' => '储值','uri'=>'student.debit','ismenu'=>false],
                            ['text' => '兑换储值卡','uri'=>'student.changedc','ismenu'=>false],
                            ['text' => '缴费','uri'=>'student.pay','ismenu'=>false],
                            ['text' => '退费','uri'=>'student.refund','ismenu'=>false],
                            ['text' => '转班','uri'=>'student.transferclass','ismenu'=>false],
                            ['text' => '转校','uri'=>'student.transferschool','ismenu'=>false],
                            ['text' => '停课','uri'=>'student.stopLesson','ismenu'=>false],
                            ['text' => '复课','uri'=>'student.resumeLesson','ismenu'=>false],
                            ['text' => '休学','uri'=>'student.leaveSchool','ismenu'=>false],
                            ['text' => '复学','uri'=>'student.backSchool','ismenu'=>false],
                            ['text' => '退学','uri'=>'student.dropOut','ismenu'=>false],
                            ['text' => '入学','uri'=>'student.admission','ismenu'=>false],
                            ['text' => '删除','uri'=>'student.delete','ismenu'=>false],
                            ['text' => '请假','uri'=>'student.leave','ismenu'=>false],
                            ['text' => '解封','uri'=>'student.unarchive','ismenu'=>false],
                            ['text' => '添加服务记录','uri'=>'student.addrecord','ismenu'=>false],
                            ['text' => '添加服务安排','uri'=>'student.addtask','ismenu'=>false],
                            ['text' => '积分操作','uri'=>'student.integral','ismenu'=>false],
			    ['text' => '查看完整手机号','uri'=>'student.showalltel','ismenu'=>false]
                        ]],
                        ['text' => '学员分配','uri'=>'student.assign','sub'=>[
                            ['text' => '分配学管师','uri'=>'assign.add','ismenu'=>false],
                            ['text' => '批量取消学管师','uri'=>'assign.cancel','ismenu'=>false],
                            ['text' => '批量调整学管师','uri'=>'assign.batch','ismenu'=>false]
                        ]],
                        ['text' => '学员积分','uri'=>'student.integral','sub'=>[
                            ['text' => '批量操作','uri'=>'integral.batch','ismenu'=>false],
                            ['text' => '添加积分规则','uri'=>'integral.ruleadd','ismenu'=>false]
                        ]],
                        ['text' => '学员分班','uri'=>'student.class']
                    ]
                ],
                [
                    'text'	=> '报名管理',
                    'name'  => 'business.order',
                    'uri'	=> 'business.order',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'order.overview'],
                        ['text' => '订单管理','uri'=>'order.offline','sub' => [
                            ['text' => '分班','uri'=>'offline.asclass','ismenu'=>false],
                            ['text' => '补缴','uri'=>'offline.makeup','ismenu'=>false],
                            ['text' => '结转','uri'=>'offline.transfer','ismenu'=>false],
                            ['text' => '打印课表','uri'=>'offline.print','ismenu'=>false],
                            ['text' => '删除订单','uri'=>'offline.delete','ismenu'=>false],
                            ['text' => '继续支付','uri'=>'offline.pay','ismenu'=>false],
                            ['text' => '退费','uri'=>'offline.refund','ismenu'=>false],
                            ['text' => '编辑','uri'=>'offline.edit','ismenu'=>false],
                        ]],
                        ['text' => '课时管理','uri'=>'order.hours','sub' => [
                            ['text' => '批量导入','uri'=>'hours.import','ismenu'=>false],
                            ['text' => '编辑','uri'=>'hours.edit','ismenu'=>false],
                            ['text' => '赠送课时','uri'=>'hours.present','ismenu'=>false],
                            ['text' => '结转课时','uri'=>'hours.transfer','ismenu'=>false],
                            ['text' => '撤销结转','uri'=>'transfer.undo','ismenu'=>false]
                        ]]
                    ]
                ],
                [
                    'text'	=> '班级管理',
                    'name'  => 'business.class',
                    'uri'	=> 'business.class',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'class.overview'],
                        ['text' => '班级','uri'=>'class.list','sub' => [
                            ['text' => '创建班级','uri'=>'class.add','ismenu'=>false],
                            ['text' => '编辑班级','uri'=>'class.edit','ismenu'=>false],

                            ['text' => '班级排课管理','uri'=>'class.arrange','ismenu'=>false],
                            ['text' => '班级学员管理','uri'=>'class.students','ismenu'=>false],
                            ['text' => '删除班级','uri'=>'class.delete','ismenu'=>false],
                            ['text' => '升班','uri'=>'class.up','ismenu'=>false],
                            ['text' => '结课','uri'=>'class.close','ismenu'=>false],
                            ['text' => '撤销结课','uri'=>'class.undo','ismenu'=>false]
                        ]],
                        ['text' => '临时班级','uri'=>'class.templist'],
			['text' => '班级学员','uri'=>'class.students']
                    ]
                ],
                [
                    'text'	=> '排课管理',
                    'name'  => 'business.arrange',
                    'uri'	=> 'business.arrange',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'arrange.overview'],
                        ['text' => '排课','uri'=>'arrange.lists','sub' => [
                            ['text' => '编辑排课','uri' => 'arrange.edit','ismenu'=>false],
                            ['text' => '单次排课','uri'=>'arrange.add','ismenu'=>false],
                            ['text' => '可视排课','uri'=>'arrange.visual','ismenu'=>false],
                            ['text' => '取消排课','uri'=>'arrange.cancel','ismenu'=>false],
                            ['text' => '删除排课','uri'=>'arrange.delete','ismenu'=>false],

                            ['text' => '批量考勤','uri'=>'arrange.batch','ismenu'=>false],
                            ['text' => '导出排课','uri'=>'arrange.export','ismenu'=>false]
                        ]],
                        ['text' => '周排课表','uri'=>'arrange.schedule','sub' => [
                            ['text' => '查看课评','uri'=>'schedule.viewreview','ismenu'=>false],
                            ['text' => '添加课评','uri'=>'schedule.addreview','ismenu'=>false],
                            ['text' => '删除排课','uri'=>'schedule.deletearrange','ismenu'=>false]
                        ]]
                    ]
                ],
                [
                    'text'	=> '考勤管理',
                    'name'  => 'business.attendance',
                    'uri'	=> 'business.attendance',
                    'hidesub' => true,
                    'sub'=>[
                        ['text' => '概览','uri'=>'attendance.overview'],
                        ['text' => '授课记录','uri'=>'attendance.teach','sub' => [
                            ['text' => '添加点评','uri'=>'attendance.review','ismenu'=>false],
                            ['text' => '撤销考勤','uri'=>'attendance.cancel','ismenu'=>false],
                            ['text' => '确认考勤','uri'=>'attendance.confirm','ismenu'=>false],
                            ['text' => '取消确认考勤','uri'=>'attendance.cancel_confirm','ismenu'=>false],
			                ['text' => '导出','uri'=>'class_attendance.export','ismenu'=>false]
                        ]],
                        ['text' => '学员考勤记录','uri'=>'attendance.student','sub' => [
			                ['text' => '导出','uri'=>'student_attendance.export','ismenu'=>false]
                        ]],
                        ['text' => '刷卡记录','uri'=>'attendance.swipe','sub' => [
			                ['text' => '导出','uri'=>'swipe.export','ismenu'=>false]
                        ]],
                        ['text' => '请假记录','uri'=>'attendance.leave','sub' => [
			                ['text' => '导出','uri'=>'leave.export','ismenu'=>false]
                        ]],
                        ['text' => '缺课记录','uri'=>'attendance.absence','sub' => [
			                ['text' => '导出','uri'=>'absence.export','ismenu'=>false]
                        ]],
                        ['text' => '补课记录','uri'=>'attendance.makeup','sub' => [
			                ['text' => '导出','uri'=>'makeup.export','ismenu'=>false]
                        ]]
                    ]
                ],
                [
                    'text'	=> '课耗管理',
                    'name'  => 'business.hour',
                    'uri'	=> 'business.hour',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'hour.overview'],
                        ['text' => '学员课耗','uri' => 'hour.student','sub'=>[
                            ['text' => '登记课耗','uri' => 'hour.reg','ismenu'=>false],
                            ['text' => '扣课时','uri' => 'hour.deduct','ismenu'=>false],
                            ['text' => '删除','uri' => 'hour.delete','ismenu'=>false]
                        ]],
                        ['text' => '课程产出','uri' => 'hour.lesson'],
                        ['text' => '%老师%产出','uri' => 'hour.employee'],
                        ['text' => '%学管师%产出','uri' => 'hour.teacher']

                    ],
                ],
                [
                    'text'	=> '财务管理',
                    'name'  => 'business.iae', 
                    'uri'	=> 'business.iae',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'iae.overview'],
                        ['text' => '收支流水','uri' => 'iae.tally','sub'=>[
                            ['text' => '记一笔','uri' => 'tally.add','ismenu'=>false],
                            ['text' => '删除记一笔','uri'=>'tally.delete','ismenu'=>false],
                            ['text' => '编辑','uri'=>'tally.edit','ismenu'=>false]
                        ]],
                        ['text' => '缴费记录','uri' => 'iae.bills','sub'=>[
                            ['text' => '收据作废','uri' => 'bills.invalid','ismenu'=>false],
                            ['text' => '修改收据号','uri' => 'bills.edit','ismenu'=>false]
                        ]],
                        ['text' => '退费记录','uri' => 'iae.refund','sub'=>[
                            ['text' => '撤销','uri' => 'refund.undo','ismenu'=>false],
                            ['text' => '打印收据','uri' => 'refund.print','ismenu'=>false]
                        ]],
                        ['text' => '业绩记录','uri' => 'iae.receipt','sub'=>[
                            ['text' => '编辑','uri' => 'receipt.edit','ismenu'=>false],
                            ['text' => '删除','uri' => 'receipt.delete','ismenu'=>false]
                        ]],
                        ['text' => '交款记录','uri' => 'iae.hand','sub'=>[
                            ['text' => '确认交款','uri' => 'hand.ack','ismenu'=>false],
                            ['text' => '转账','uri' => 'hand.transfer','ismenu'=>false]
                        ]],
                        ['text' => '资产负债','uri' => 'iae.asset'],
                        ['text' => '收支汇总','uri' => 'iae.summary'],
                        ['text' => '收支类别','uri' => 'iae.type','sub'=>[
                            ['text' => '添加类别','uri' => 'type.add','ismenu'=>false]
                        ]],
                        ['text' => '辅助核算','uri' => 'iae.help','sub'=>[
                            ['text' => '添加','uri' => 'help.add','ismenu'=>false]
                        ]]
                ]]
            ]
        ],
        [
            'text'	=> '服务',
            'name'	=> 'service',
            'uri'	=> 'service',
            'class'	=> 'icon-fuwu',
            'sub'	=> [
                [
                    'text'  => '课前服务',
                    'name'  => 'service.bclass',
                    'uri'   => 'service.bclass',
                    'hidesub'   => true,
                    'sub'   => [
                        ['text' => '概览','uri'=>'bclass.overview'],
                        ['text' => '课前提醒','uri'=>'bclass.remind','sub' => [
                            ['text'=>'推送课前提醒','uri'=>'remind.push','ismenu'=>false],
                            ['text'=>'课前提醒计划','uri'=>'remind.plan','ismenu'=>false],
                        ]],
                        ['text' => '备课服务','uri'=>'bclass.prepare','sub'=>[
                            ['text'=>'添加备课','uri'=>'prepare.add','ismenu'=>false],
                            ['text'=>'编辑备课','uri'=>'prepare.edit','ismenu'=>false],
                            ['text'=>'删除备课','uri'=>'prepare.delete','ismenu'=>false],
			    ['text'=>'导出备课','uri'=>'prepare.export','ismenu'=>false]

                        ]],
                        ['text' => '到离校通知','uri'=>'bclass.notice']
                    ]
                ],
                [
                    'text'  => '课后服务',
                    'name'  => 'service.aclass',
                    'uri'   => 'service.aclass',
                    'hidesub'   => true,
                    'sub'   => [
                        ['text' => '概览','uri'=>'aclass.overview'],
                        ['text' => '课评服务','uri'=>'aclass.comments','sub'=>[
                            ['text' => '添加课评','uri'=>'comments.add','ismenu'=>false],
                            ['text' => '删除课评','uri'=>'comments.delete','ismenu'=>false],
                            ['text' => '课评推送','uri'=>'comments.send','ismenu'=>false],
			    ['text' => '课评导出','uri'=>'comments.export','ismenu'=>false]

                        ]],
                        ['text' => '作业服务','uri'=>'aclass.homework','sub'=>[
                            ['text' => '布置作业','uri'=>'homework.add','ismenu'=>false],
                            ['text' => '推送作业','uri'=>'homework.push','ismenu'=>false],
                            ['text' => '删除作业','uri'=>'homework.delete','ismenu'=>false],
			    ['text' => '作业导出','uri'=>'homework.export','ismenu'=>false]

                        ]],
                        ['text' => '作品服务','uri'=>'aclass.artwork','sub'=>[
                            ['text' => '发布作品','uri'=>'artwork.add','ismenu'=>false],
                            ['text' => '删除作品','uri'=>'artwork.delete','ismenu'=>false],
			    ['text' => '作品导出','uri'=>'artwork.export','ismenu'=>false]

                        ]],
                        ['text' => '学员回访','uri'=>'aclass.visit','sub'=>[
                            ['text' => '添加回访','uri'=>'visit.add','ismenu'=>false],
                            ['text' => '删除回访','uri'=>'visit.delete','ismenu'=>false],
			    ['text' => '回访导出','uri'=>'visit.export','ismenu'=>false]

                        ]],
                    ]
                ],
                [
                    'text' => '学习管家',
                    'name' => 'service.study',
                    'uri'=>'service.study',
                    'hidesub'  => true,
                    'sub'=>[
                        ['text' => '概览','uri'=> 'study.overview'],
                        ['text' => '账号管理','uri'=>'study.student'],
                        ['text' => '登录情况','uri'=>'study.log'],
			['text' => '学员回评','uri'=>'study.reply'],
                        ['text' => '投诉建议','uri'=>'study.complaint']
                    ]
                ],
                /*
                ['text' => '学情服务','uri'=>'service.situation','sub'=>[
                    ['text' => '添加学情服务','uri'=>'situation.add','ismenu'=>false],
                    ['text' => '编辑学情服务','uri'=>'situation.edit','ismenu'=>false],
                    ['text' => '删除学情服务','uri'=>'situation.delete','ismenu'=>false],

                ]],
                ['text' => '学习方案','uri'=>'service.lesson_buy_suit','ismenu'=>false,'sub'=>[
                    ['text' => '添加学习方案','uri'=>'lesson_buy_suit.add','ismenu'=>false],
                    ['text' => '编辑学习方案','uri'=>'lesson_buy_suit.edit','ismenu'=>false],
                    ['text' => '删除学习方案','uri'=>'lesson_buy_suit.delete','ismenu'=>false],

                ]],
                */
            ]
        ],
        [
            'text'  => '应用',
            'name'  => 'app',
            'uri'   => 'app',
            'class' => 'icon-jichu',
            'sub'   => [
                [
                    'text'      =>'图书管理',
                    'name'      =>'app.books',
                    'uri'       =>'app.books',
                    'hidesub'   =>  true
                ],
                [
                    'text'      => '物品管理',
                    'name'      => 'app.materials',
                    'uri'       => 'app.materials',
                    'hidesub'   =>  true,
                    'sub'       => [
                        ['text'  => '物品添加','uri'=>'materials.add','ismenu'=>false],
                        ['text'  => '物品编辑','uri'=>'materials.edit','ismenu'=>false],
                        ['text'  => '物品删除','uri'=>'materials.delete','ismenu'=>false],
                        ['text'  => '物品入库','uri'=>'materials.in','ismenu'=>false],
                        ['text'  => '物品出库','uri'=>'materials.out','ismenu'=>false],
                        ['text'  => '物品调拨','uri'=>'materials.transfer','ismenu'=>false],
                        ['text'  => '仓库管理','uri'=>'materials.store','ismenu'=>false]
                    ]
                ],
                [
                    'text'      => '成绩管理',
                    'name'      => 'app.achievement',
                    'uri'       =>'app.achievement',
                    'hidesub'   => true,
                    'sub'       => [
                        ['text' => '考试管理','uri'=>'achievement.exam','sub' => [
                            ['text' => '添加考试','uri'=>'exam.add','ismenu'=>false],
                            ['text' => '编辑考试','uri'=>'exam.edit','ismenu'=>false],
                            ['text' => '删除考试','uri'=>'exam.delete','ismenu'=>false],
                        ]],
                        ['text' => '成绩查询','uri'=>'achievement.score','sub' => [
                            ['text' => '成绩录入','uri'=>'score.entry','ismenu'=>false],
                            ['text' => '成绩导入','uri'=>'score.import','ismenu'=>false],
                            ['text' => '成绩导出','uri'=>'score.export','ismenu'=>false],
                            ['text' => '成绩删除','uri'=>'score.delete','ismenu'=>false],
                            ['text' => '成绩编辑','uri'=>'score.edit','ismenu'=>false]
                        ]]
                    ]
                ],
                [
                    'text'      => '知识库管理',
                    'name'      => 'app.knowledge',
                    'uri'       => 'app.knowledge',
                    'hidesub'   =>  true,
                    'sub'       => [
                        ['text' => '添加知识','uri'=>'knowledge.add','ismenu'=>false],
                        ['text' => '编辑知识','uri'=>'knowledge.edit','ismenu'=>false],
                        ['text' => '删除知识','uri'=>'knowledge.delete','ismenu'=>false]
                    ]
                ],
                /*
                [
                    'text'      => '粉丝管理',
                    'name'      => 'app.fans',
                    'uri'       => 'app.fans',
                    'hidesub'   =>  true
                ],

                [
                    'text'=>'活动管理',
                    'name'=>'app.event',
                    'uri'=>'app.event',
                    'hidesub'   => true,
                    'sub'=>[
                        ['text' => '活动列表','uri'=>'event.list','sub'=>[
                            ['text' => '添加活动','uri'=>'event.add','ismenu'=>false],
                            ['text' => '编辑活动','uri'=>'event.edit','ismenu'=>false],
                            ['text' => '删除活动','uri'=>'event.delete','ismenu'=>false]
                        ]],
                        ['text' => '报名管理','uri'=>'event.signup','sub'=>[

                        ]]
                    ]
                ],
                */
                [
                    'text'  =>  '加盟商管理',
                    'name'  =>  'app.franchisees',
                    'uri'   =>  'app.franchisees',
                    'need_user_field'    => [
                        'og_id' => 0
                    ],
                    'need_client_field' => [
                        'is_org_open' => 1
                    ],
                    'hidesub'   =>  true,
                    'sub'   =>  [
                        ['text'=>'盟商资料','uri'=>'franchisees.archive','sub' => [
                            ['text' => '编辑','uri'=>'francharchive.edit','ismenu'=>false],
                            ['text' => '删除','uri'=>'francharchive.delete','ismenu'=>false],
                            ['text' => '创建合同','uri'=>'francharchive.contract','ismenu'=>false],
                            ['text' => '联系人管理','uri'=>'francharchive.contact','ismenu'=>false],
                            ['text' => '开通校360','uri'=>'francharchive.open','ismenu'=>false],
                            ['text' => '关联已有系统','uri'=>'francharchive.link','ismenu'=>false],
                            ['text' => '添加服务记录','uri'=>'francharchive.service','ismenu'=>false],
                            ['text' => '添加联系人','uri'=>'contact.add','ismenu'=>false],
                            ['text' => '编辑联系人','uri'=>'contact.edit','ismenu'=>false],
                            ['text' => '删除联系人','uri'=>'contact.delete','ismenu'=>false]
                        ]],
                        ['text'=>'加盟合同','uri'=>'franchisees.contract','sub' =>[
                            ['text' => '编辑','uri'=>'franchcontract.edit','ismenu'=>false],
                            ['text' => '删除','uri'=>'franchcontract.delete','ismenu'=>false],
                        ]],
                        ['text'=>'督导服务','uri'=>'franchisees.service','sub'=>[
                            ['text' => '编辑服务申请','uri'=>'apply.edit','ismenu'=>false],
                            ['text' => '删除服务申请','uri'=>'apply.delete','ismenu'=>false]
                        ]],
                        ['text'=>'校360系统','uri'=>'franchisees.system','sub'=>[
                            ['text'=>'新增系统','uri'=>'franchsystem.add','ismenu'=>false],
                            ['text'=>'编辑系统','uri'=>'franchsystem.edit','ismenu'=>false],
                            ['text'=>'锁定系统','uri'=>'franchsystem.lock','ismenu'=>false],
                            ['text'=>'解锁系统','uri'=>'franchsystem.unlock','ismenu'=>false],
                            ['text'=>'系统续费','uri'=>'franchsystem.renew','ismenu'=>false],
                            ['text'=>'删除系统','uri'=>'franchsystem.delete','ismenu'=>false],
                            ['text'=>'系统配置','uri'=>'franchsystem.config','ismenu'=>false],
                            ['text'=>'修改账号','uri'=>'franchsystem.reset','ismenu'=>false],
                            ['text'=>'登录员工账号','uri'=>'franchsystem.login','ismenu'=>false],
                            ['text'=>'系统审核','uri'=>'franchsystem.review','ismenu'=>false]
                        ]],
                        ['text'=>'加盟商报表','uri'=>'franchisees.report']
                    ]
                ],
                [
                    'text' => '应用中心',
                    'uri'  => 'app.center'
                ]
            ]
        ],
        [
            'text'  => '报表',
            'name'  => 'report',
            'uri'   => 'reports',
            'class' => 'icon-baobiao',
            'sub'   => [
                ['text' => '运营总表','uri'=>'reports.overview'],
                ['text' => '招生统计表','uri'=>'reports.customer'],
                ['text' => '试听报读统计表','uri'=>'reports.trial'],
                ['text' => '体验课报表','uri'=>'reports.demolesson'],
                ['text' => '学员分析报表','uri'=>'reports.on'],
                ['text' => '班级报表','uri'=>'reports.class'],
                // ['text' => '学员剩余课时报表','uri'=>'reports.remainlessonhour'],
                ['text' => '课耗确收表','uri'=>'reports.income'],
                ['text' => '考勤统计表','uri'=>'reports.attendance'],
                ['text' => '服务报表','uri'=>'reports.service'],
                [
                    'text'  => '业绩报表',
                    'uri'   =>    'reports.performance',
                    'hidesub'   =>  true,
                    'sub'   =>  [
                        ['text' => '人员业绩','uri' => 'performance.employee'],
                        ['text' => '校区业绩','uri' => 'performance.branch'],
                        ['text' => '签单回款汇总','uri' => 'performance.stats'],
                        ['text' => '%老师%业绩汇总','uri' => 'performance.teacher']
                    ]
                ],
                ['text' => '收支汇总表','uri'=>'reports.incomeandexpend'],
		        
                ['text' => '导出','uri'=>'reports.export','ismenu'=>false]

            ]
        ],
        [
            'text'	=> '系统',
            'name'	=> 'system',
            'uri'	=> 'system',
            'class'	=> 'icon-shezhi',
            'sub'	=> [
                [
                    'text'  => '系统设置',
                    'name'  => 'system.config',
                    'uri'   => 'system.configs',
                    'hidesub'   => true,
                    'sub'   => [
                        ['text' => '系统参数','uri'=>'configs.params'],
                        ['text' => '界面设置','uri'=>'configs.ui'],
                        ['text' => '打印模板设置','uri'=>'configs.print'],
                        ['text' => '微信公众号设置','uri'=>'configs.wxmp','sub'=>[
                            ['text' => '公众号管理','uri'=>'wxmp.list','ismenu'=>false],
                            ['text' => '基础配置','uri'=>'wxmp.basic','ismenu'=>false],
                            ['text' => '模板消息','uri'=>'wxmp.tplmsg','ismenu'=>false],
                            ['text' => '自定义菜单','uri'=>'wxmp.menu','ismenu'=>false],
                            ['text' => '素材管理','uri'=>'wxmp.material','ismenu'=>false],
                            ['text' => '自动回复','uri'=>'wxmp.reply','ismenu'=>false]
                            // ['text' => '自定义接口回复','uri'=>'wxmp.interface','ismenu'=>false]
                        ]
                        ],
                        ['text' => '收款账户设置','uri'=>'configs.account','sub'=>[
                            ['text' => '添加账户','uri' => 'payaccount.add','ismenu'=>false],
                            ['text' => '编辑账户','uri' => 'payaccount.edit','ismenu'=>false],
                            ['text' => '删除账户','uri' => 'payaccount.delete','ismenu'=>false]
                        ]],
                        ['text' => '微信模板设置','uri'=>'configs.wechat'],
                        ['text' => '短信模板设置','uri'=>'configs.sms','sub'=>[
                            ['text' => '短信接口设置','uri'=>'sms.gateway','ismenu'=>false],
                            ['text' => '短信模板设置','uri'=>'sms.tpls','ismenu'=>false]
                        ]],
                        ['text' => '模板消息设置','uri'=>'configs.template'],
                        ['text' => '支付设置','uri'=>'configs.payment','sub' => [
                            ['text' => '收钱吧申请编辑','uri' => 'sqb.applyedit','ismenu'=>false],
                            ['text' => '收钱吧申请删除','uri' => 'sqb.applydelete','ismenu'=>false],
                            ['text' => '收钱吧配置激活','uri' => 'sqb.configactive','ismenu'=>false],
                            ['text' => '收钱吧配置编辑','uri' => 'sqb.configedit','ismenu'=>false],
                            ['text' => '收钱吧配置删除','uri' => 'sqb.configdelete','ismenu'=>false],
                        ]],
                        ['text' => '云存储设置','uri'=>'configs.storage'],
                        // ['text' => '移动端设置','uri'=>'configs.mobile'],
                        ['text' => '服务标准设置','uri'=>'configs.service_standard'],
                        ['text' => '课评模板设置','uri'=>'configs.reviews_tpl'],
                        //['text' => '前台业务码设置','uri'=>'configs.business_code'],
			['text' => '推荐有奖设置','uri'=>'configs.recommend'],
                        ['text' => '市场扫码录入设置','uri'=>'configs.qrsign'],
                        ['text' => '自定义字段','uri'=>'configs.customer_fields'],
                        //['text' => 'API接口设置','uri'=>'configs.api'],
                        ['text' => '系统维护','uri' => 'configs.maintenance']
                    ]
                ],
                [
                    'text'  => '基础设置',
                    'name'  => 'system.basic',
                    'uri'   => 'system.basic',
                    'hidesub'   => true,
                    'sub'   => [
                        [
                            'text'  => '课程管理',
                            'uri'=>'basic.lesson',
                            'sub'   => [
                                ['text' => '添加新课程','uri'=>'lesson.new','ismenu'=>false],
                                ['text' => '绑定物品','uri'=>'lesson.bindmaterial','ismenu'=>false],
                                ['text' => '删除课程','uri'=>'lesson.delete','ismenu'=>false],
                                ['text' => '编辑课程','uri'=>'lesson.edit','ismenu'=>false],
                                ['text' => '课程跨校区价格定义','uri'=>'lesson.define','ismenu'=>false]
                            ]
                        ],
                        [
                            'text' => '课标设置',
                            'uri'  => 'basic.course_standard_file',
                            'sub'  => [
                                ['text' => '添加课标','uri'=>'course_standard_file.add','ismenu'=>false],
                                ['text' => '编辑课标','uri'=>'course_standard_file.edit','ismenu'=>false],
                                ['text' => '删除课标','uri'=>'course_standard_file.delete','ismenu'=>false]
                            ]
                        ],
                        [
                            'text'  => '科目设置',
                            'uri'=>'basic.subject',
                            'sub' => [
                                ['text' => '新增科目','uri'=>'subject.add','ismenu'=>false],
                                ['text' => '编辑科目','uri'=>'subject.edit','ismenu'=>false],
                                ['text' => '删除科目','uri'=>'subject.delete','ismenu'=>false],
                                ['text' => '级别管理','uri'=>'subject.grademanage','ismenu'=>false]
                            ]
                        ],
                        [
                            'text'  => '%老师%管理',
                            'uri'=>'basic.teachers',
                            'sub'   => [
                                ['text' => '新增老师','uri'=>'teachers.add','ismenu'=>false],
                                ['text' => '编辑老师','uri'=>'teachers.edit','ismenu'=>false],
                                ['text' => '删除老师','uri'=>'teachers.delete','ismenu'=>false],
                                ['text'=>'添加账号','uri'=>'account.add','ismenu'=>false],
                                ['text'=>'锁定账号','uri'=>'account.lock','ismenu'=>false],
                                ['text'=>'重置密码','uri'=>'account.reset','ismenu'=>false]
                            ]
                        ],
                        [
                            'text' => '教室设置',
                            'uri'=>'basic.classrooms',
                            'sub'   => [
                                ['text' => '新增教室','uri'=>'classrooms.add','ismenu'=>false],
                                ['text' => '编辑教室','uri'=>'classrooms.edit','ismenu'=>false],
                                ['text' => '删除教室','uri'=>'classrooms.delete','ismenu'=>false]
                            ]
                        ],
                        [
                            'text' => '上课时间段管理',
                            'uri'=>'basic.time',
                            'sub'   => [
                                ['text' => '新增时间段','uri'=>'time.add','ismenu'=>false],
                                ['text' => '编辑时间段','uri'=>'time.edit','ismenu'=>false],
                                ['text' => '删除时间段','uri'=>'time.delete','ismenu'=>false]
                            ]
                        ],
                        [
                            'text' => '节假日设置',
                            'uri'=>'basic.holiday'
                        ],
                        [
                            'text'      => '杂费管理',
                            'uri'       => 'basic.fees',
                            'sub'       => [
                                ['text' => '添加杂费项','uri'=>'fees.add','ismenu'=>false],
                                ['text' => '编辑杂费项','uri'=>'fees.edit','ismenu'=>false],
                                ['text' => '删除杂费项','uri'=>'fees.delete','ismenu'=>false]
                            ]
                        ],
                        [
                            'text'      =>  '公立学校管理',
                            'uri'       =>  'basic.schools',
                            'sub'       =>  [
                                ['text' => '增加学校','uri'=>'schools.add','ismenu'=>false],
                                ['text' => '编辑学校','uri'=>'schools.edit','ismenu'=>false],
                                ['text' => '删除学校','uri'=>'schools.delete','ismenu'=>false]
                            ]
                        ],
                        [
                            'text'      => '移动端页面管理',
                            'uri'       => 'basic.mobile_page',
                            'sub'       => [
                                ['text' => '添加页面','uri'=>'mobile_page.add','ismenu'=>false],
                                ['text' => '编辑页面','uri'=>'mobile_page.edit','ismenu'=>false],
                                ['text' => '删除页面','uri'=>'mobile_page.delete','ismenu'=>false]
                            ]
                        ],
                        [
                            'text'      => '问卷管理',
                            'uri'       => 'basic.questionnaire',
                            'sub'       => [
                                ['text' => '添加问卷','uri'=>'ques.add','ismenu'=>false],
                                ['text' => '编辑问卷','uri'=>'ques.edit','ismenu'=>false],
                                ['text' => '删除问卷','uri'=>'ques.delete','ismenu'=>false],
                                ['text' => '添加条目','uri'=>'quesitem.add','ismenu'=>false],
                                ['text' => '编辑条目','uri'=>'quesitem.edit','ismenu'=>false],
                                ['text' => '删除条目','uri'=>'quesitem.delete','ismenu'=>false]
                            ]
                        ],
                        [
                            'text'      => '学习套餐',
                            'uri'       => 'basic.lesson_suit_define',
                            'sub'       => [
                                ['text' => '添加套餐','uri'=>'lesson_suit_define.add','ismenu'=>false],
                                ['text' => '编辑套餐','uri'=>'lesson_suit_define.edit','ismenu'=>false],
                                ['text' => '删除套餐','uri'=>'lesson_suit_define.delete','ismenu'=>false]
                            ]
                        ],
                        [
                            'text'      => '储值卡管理',
                            'uri'       => 'basic.debit',
                            'sub'       => [
                                ['text' => '添加储值卡','uri'=>'debit_card.add','ismenu'=>false],
                                ['text' => '编辑储值卡','uri'=>'debit_card.edit','ismenu'=>false],
                                ['text' => '删除储值卡','uri'=>'debit_card.delete','ismenu'=>false],
                                ['text' => '打印购买合同','uri'=>'debit_card_history.print','ismenu'=>false],
                                ['text' => '修改有效期','uri'=>'debit_card_history.edit','ismenu'=>false],
                                ['text' => '删除购买记录','uri'=>'debit_card_history.delete','ismenu'=>false],
                            ]
                        ]
                    ]
                ],
                [
                    'text'  => '人员设置',
                    'name'   => 'system.staff',
                    'uri'   => 'system.staff',
                    'hidesub'   => true,
                    'sub'   =>  [
                        [
                            'text'  => '部门设置',
                            'uri'=>'staff.departments',
                            'sub' => [
                                ['text' => '新增部门','uri'=>'departments.add','ismenu'=>false],
                                ['text' => '编辑部门','uri'=>'departments.edit','ismenu'=>false],
                                ['text' => '删除部门','uri'=>'departments.delete','ismenu'=>false],
                                ['text' => '编辑校区','uri'=>'branchs.edit','ismenu'=>false]
                            ]
                        ],
                        [
                            'text' => '员工管理',
                            'uri'=>'staff.employees',
                            'sub'   => [
                                ['text' => '新增员工','uri'=>'employees.add','ismenu'=>false],
                                ['text' => '编辑员工','uri'=>'employees.edit','ismenu'=>false],
                                ['text' => '删除员工','uri'=>'employees.delete','ismenu'=>false],
                                ['text' => '离职','uri'=>'employees.leave','ismenu'=>false],
                                ['text' => '恢复入职','uri'=>'employees.restore','ismenu'=>false],
                                ['text' => '额外权限','uri'=>'employees.extraper','ismenu'=>false]
                            ]
                        ],
                        [
                            'text'  => '权限组管理',
                            'uri'   => 'staff.roles',
                            'sub'   => [
                                ['text'=>'新增权限组','uri'=>'roles.add','ismenu'=>false],
                                ['text'=>'编辑权限组','uri'=>'roles.edit','ismenu'=>false],
                                ['text'=>'删除权限组','uri'=>'roles.delete','ismenu'=>false],
                                ['text'=>'修改权限',  'uri'=>'roles.per','ismenu'=>false]
                            ]
                        ],
                    ]
                ],
                [
                    'text'  => '数据字典',
                    'uri'   => 'system.dicts'
                ],
                [
                    'text'  => '加盟商管理',
                    'uri'   => 'system.orgs',
                    //need_user_field  需要用户的字段满足哪些条件才显示这个菜单
                    'need_user_field'    => [
                        'og_id' => 0
                    ],
                    'need_client_field' => [
                        'is_org_open' => 1
                    ],

                    'sub'   => [
                        ['text'=>'新增加盟商','uri'=>'orgs.add','ismenu'=>false],
                        ['text'=>'修改加盟商信息','uri'=>'orgs.edit','ismenu'=>false],
                        ['text'=>'锁定加盟商','uri'=>'orgs.lock','ismenu'=>false],
                        ['text'=>'解锁加盟商','uri'=>'orgs.unlock','ismenu'=>false],
                        ['text'=>'加盟商续费','uri'=>'orgs.renew','ismenu'=>false],
                        ['text'=>'删除加盟商','uri'=>'orgs.delete','ismenu'=>false],
                        ['text'=>'系统配置','uri'=>'orgs.config','ismenu'=>false],
                        ['text'=>'修改账号','uri'=>'orgs.reset','ismenu'=>false],
                    ]
                ],
                [
                    'text'  => '日志',
                    'uri'   => 'system.logs'
                ]
            ]
        ]
    ]
];
