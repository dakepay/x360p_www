<?php
return [
 	'main'	=>	[
    	[   'text'      => '工作台',
		    'name'      => 'dashboard',
		    'uri'       => 'dashboard',
		    'ismenu'	=> false,
		    'sub'	=> [
		    	['text' => '查看数据', 'uri'=>'dashboard.view','ismenu'=>false ],
		    	['text' => '咨询登记','uri'=>'dashboard.reginquiry','ismenu'=>false],
		    	['text' => '服务登记', 'uri'=>'dashboard.regservice','ismenu'=>false],
		    	['text' => '回访登记', 'uri'=>'dashboard.regcall','ismenu'=>false]
		    ]
		],
		[
            'text'	=> '跟单',
            'name'	=> 'crm',
            'uri'	=> 'crm',
            'class'	=> 'icon-zhaosheng',
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
	        'class'	=> 'icon-yunying',
	        'sub'	=> [
	            ['text'	=> '客户管理','uri'	=> 'business.clients',
	            	'sub'	=> [

						['text' => '开通账号','uri'=>'clients.add','ismenu'=>false],
						['text' => '编辑客户','uri'=>'client.edit','ismenu'=>false],
						['text' => '查看账号','uri'=>'client.view','ismenu'=>false],
						['text' => '冻结账号','uri'=>'client.lock','ismenu'=>false],
						['text' => '延期','uri'=>'client.renew','ismenu'=>false],
						['text' => '扩容','uri'=>'client.expand','ismenu'=>false],
						['text' => '登录系统','uri'=>'client.login','ismenu'=>false],
						['text' => '版本升级','uri'=>'client.upgrade','ismenu'=>false],
						['text' => '删除客户','uri'=>'client.delete','ismenu'=>false]
	                ]
	            ],
	            ['text'	=> '版本管理','uri' => 'business.versions',
	            	'sub'	=> [
	            		['text' => '发布版本','uri'=>'versions.add','ismenu'=>false],
	            		['text' => '版本升级','uri'=>'versions.upgrade','ismenu'=>false]
	            	]
	            ],
	            ['text'	=> '订单记录','uri'	=> 'business.orders'],
	            ['text' => '服务记录','uri' => 'business.services']
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
	    		['text' => '公立学校','uri'=>'apis.schools'],
	    		['text' => '地区数据','uri'=>'apis.areas'],
	    		['text' => '法定节假日','uri'=>'apis.holidays']
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
		                ['text' => '系统参数','uri'=>'configs.params']
		            ]
		        ],
		        [
		            'text' => '用户管理',
		            'uri'=>'system.users',
		            'sub'   => [
		                ['text' => '新增用户','uri'=>'users.add','ismenu'=>false],
		                ['text' => '编辑用户','uri'=>'users.edit','ismenu'=>false],
		                ['text' => '锁定账号','uri'=>'users.lock','ismenu'=>false],
		                ['text' => '删除用户','uri'=>'users.delete','ismenu'=>false]
		            ]
		        ],
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
                ]
		    ]
		]
	]
];