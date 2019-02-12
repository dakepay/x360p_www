<?php
return [
 	'main'	=>	[
    	[   'text'      => '工作台',
		    'name'      => 'dashboard',
		    'uri'       => 'dashboard',
		    'class'		=> 'fa fa-dashboard',
		    'ismenu'	=> false,
		    'sub'	=> [
		    	['text' => '查看数据', 'uri'=>'dashboard.view','ismenu'=>false ],
		    	['text' => '咨询登记','uri'=>'dashboard.reginquiry','ismenu'=>false],
		    	['text' => '服务登记', 'uri'=>'dashboard.regservice','ismenu'=>false],
		    	['text' => '回访登记', 'uri'=>'dashboard.regcall','ismenu'=>false],
		    	['text' => '删除公告', 'uri'=>'broadcast.delete','ismenu'=>false],
			['text' => '新增公告', 'uri'=>'broadcast.add','ismenu'=>false],
			['text' => '编辑公告', 'uri'=>'broadcast.edit','ismenu'=>false],
			['text' => '编辑字典', 'uri'=>'dicts.edit','ismenu'=>false],
			['text' => '删除字典', 'uri'=>'dicts.delete','ismenu'=>false],
			['text' => '新增字典', 'uri'=>'dicts.add','ismenu'=>false]
		    ]
		],
		[
            'text'	=> '跟单',
            'name'	=> 'crm',
            'uri'	=> 'crm',
            'class'	=> 'fa fa-users',
            'sub'	=> [
                ['text'	=> '客户名单','uri'	=> 'crm.customer','sub'=>[
                		['text'	=> '公海客户','uri'=>'customer.public','ismenu'=>false],
	                    ['text' => '添加客户','uri'=>'customer.add','ismenu'=>false],
	                    ['text' => '编辑客户','uri'=>'customer.edit','ismenu'=>false],
	                    ['text' => '删除客户','uri'=>'customer.delete','ismenu'=>false]
                    ]
                ],
                ['text'	=> '跟进情况','uri'	=> 'crm.following','sub'=>[
	                    ['text' => '新增跟进记录','uri'=>'following.add','ismenu'=>false],
	                    ['text' => '编辑跟进记录','uri'=>'following.edit','ismenu'=>false]
                 	]
                ]
            ]
        ],
		[
	        'text'	=> '运营',
	        'name'	=> 'business',
	        'uri'	=> 'business',
	        'class'	=> 'fa fa-ship',
	        'sub'	=> [
	            ['text'	=> '客户管理','uri'	=> 'business.clients',
	            	'sub'	=> [

						['text' => '开通账号','uri'=>'client.add','ismenu'=>false],
						['text' => '编辑客户','uri'=>'client.edit','ismenu'=>false],
                        ['text' => '修改付款信息','uri'=>'client.editpay','ismenu'=>false],
						['text' => '查看账号','uri'=>'client.view','ismenu'=>false],
						['text' => '冻结账号','uri'=>'client.frozen','ismenu'=>false],
						['text' => '解冻账号','uri'=>'client.unfrozen','ismenu'=>false],
						['text' => '延期','uri'=>'client.renew','ismenu'=>false],
						['text' => '扩容','uri'=>'client.expand','ismenu'=>false],
						['text' => '登录系统','uri'=>'client.login','ismenu'=>false],
						['text' => '登录账号','uri'=>'client.users','ismenu'=>false],
						['text' => '下属加盟商','uri'=>'client.orgs','ismenu'=>false],
						['text' => '运行SQL','uri'=>'client.exesql','ismenu'=>false],
						['text' => '版本升级','uri'=>'client.upgrade','ismenu'=>false],
						['text' => '删除客户','uri'=>'client.delete','ismenu'=>false],
						['text' => '恢复出厂','uri'=>'client.resetdb','ismenu'=>false],
						['text' => '查看所有客户','uri'=>'client.all','ismenu'=>false],
						['text' => '购买应用','uri'=>'client.buyapp','ismenu'=>false],
						['text' => '分配销售','uri'=>'client.sale','ismenu'=>false],
						['text' => '分配客服','uri'=>'client.service','ismenu'=>false]
	                ]
	            ],
	            ['text' => '客户应用记录','uri' => 'business.clientapp','sub' => [

	            ]],
	            ['text' => '客户消费记录','uri' => 'business.consume','sub' => [

	            ]],
	            ['text'	=> '订单记录','uri'	=> 'business.orders'],
	            ['text' => '服务记录','uri' => 'business.services',
                    'sub'   => [
                        ['text'=>'添加服务记录','uri'=>'services.add','ismenu'=>false]
                    ]
                ],
                ['text' => '收钱吧管理','uri' => 'business.sqb','sub' => [
                	['text' => '提交审核','uri' => 'sqb.review','ismenu'=>false]
                ]],
                ['text'	=> '版本管理','uri' => 'business.versions',
	            	'sub'	=> [
	            		['text' => '发布版本','uri'=>'versions.add','ismenu'=>false],
	            		['text' => '版本升级','uri'=>'versions.upgrade','ismenu'=>false],
	            		['text' => '版本删除','uri'=>'versions.del','ismenu'=>false]
	            	]
	            ],
	        ]
	    ],
	    [
	    	'text'	=> '业务员',
	    	'name'	=> 'sales',
	    	'uri'	=> 'sales',
	    	'class'	=> 'fa fa-user',
	    	'sub'	=> [
	    			['text'	=> '业务员列表','uri'=>'sales.list',
	    				'sub'	=> [
	    					['text' => '添加业务员','uri'=>'employee.add','ismenu'=>false],
	    					['text' => '编辑业务员','uri'=>'employee.edit','ismenu'=>false],
	    					['text' => '删除业务员','uri'=>'employee.delete','ismenu'=>false]
	    				]
	    			],
	    			['text'	=> '业务员客户','uri'=>'sales.clients']
	    	]
	    ],
	    [
	    	'text'	=> '活动',
	    	'name'	=> 'events',
	    	'uri'	=> 'events',
	    	'class'	=> 'fa fa-calendar',
	    	'sub'	=> [
    			['text'	=> '活动列表','uri'=>'events.list'],
    			['text' => '转发情况','uri'=>'events.effect']
	    	]
	    ],
	    [	'text'	=> '公共API',
	    	'name'	=> 'apis',
	    	'uri'	=> 'apis',
	    	'class'	=> 'fa fa-cube',
	    	'sub'	=> [
	    		[
		    		'text' => '公立学校',
		    		'uri'=>'apis.schools',
	    			'sub' => [
		    			['text' => '添加学校','uri'=>'schools.add','ismenu'=>false],
		    			['text' => '编辑学校','uri'=>'schools.edit','ismenu'=>false],
		    			['text' => '删除学校','uri'=>'schools.delete','ismenu'=>false]
	    			]
	    		],	    			
	    		['text' => '地区数据','uri'=>'apis.areas'],
	    		['text' => '法定节假日','uri'=>'apis.holidays']
	    	]
	    ],
		[
		    'text'	=> '系统',
		    'name'	=> 'system',
		    'uri'	=> 'system',
		    'class'	=> 'fa fa-cog',
		    'sub'	=> [
		        [
		            'text'  => '系统设置',
		            'name'  => 'system.config',
		            'uri'   => 'system.configs',
		            'hidesub'   => true,
		            'sub'   => [
		                ['text' => '系统参数','uri'=>'configs.params']
		            ]
		        ],
		        // [
		        //     'text' => '用户管理',
		        //     'uri'=>'system.users',
		        //     'sub'   => [
		        //         ['text' => '新增用户','uri'=>'users.add','ismenu'=>false],
		        //         ['text' => '编辑用户','uri'=>'users.edit','ismenu'=>false],
		        //         ['text' => '锁定账号','uri'=>'users.lock','ismenu'=>false],
		        //         ['text' => '启用账号','uri'=>'users.open','ismenu'=>false],
		        //         ['text' => '删除用户','uri'=>'users.delete','ismenu'=>false]
		        //     ]
		        // ],
		        [
		            'text' => '员工管理',
		            'uri'=>'system.employee',
		            'sub'   => [
		                ['text' => '添加员工','uri'=>'employee.add','ismenu'=>false],
    					['text' => '编辑员工','uri'=>'employee.edit','ismenu'=>false],
    					['text' => '删除员工','uri'=>'employee.delete','ismenu'=>false]
		            ]
		        ],
		        ['text' => '客户应用管理','uri' => 'system.apps','sub' => [
		        	['text' => '添加应用','uri'=>'apps.add','ismenu'=>false],
		        	['text' => '编辑应用','uri'=>'apps.edit','ismenu'=>false],
		        	['text' => '删除应用','uri'=>'apps.delete','ismenu'=>false]
	            ]],
		        [   
		            'text'  => '权限组管理',
		            'uri'   => 'system.roles',
		            'sub'   => [
		                ['text'=>'新增权限组','uri'=>'roles.add','ismenu'=>false],
		                ['text'=>'编辑权限组','uri'=>'roles.edit','ismenu'=>false],
		                ['text'=>'删除权限组','uri'=>'roles.delete','ismenu'=>false],
						['text'=>'修改权限',  'uri'=>'roles.per','ismenu'=>false]
		            ]
		        ],
		        [
                    'text'  => '数据字典',
                    'uri'   => 'system.dicts'
                ],
                [
                	'text'  => '云呼叫日志',
                	'uri'   => 'system.calllog'
                ]
		    ]
		]
	]
];