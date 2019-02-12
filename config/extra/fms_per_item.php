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
            'ismenu'	=> false
        ],
        [
            'text'	=> '销售',
            'name'	=> 'sale',
            'uri'	=> 'sale',
            'class'	=> 'icon-sale',
            'sub'	=> [
                [
                    'text'	=> '客户名单',
                    'name'  => 'sale.clients',
                    'uri'	=> 'sale.clients',
                    'sub' => [
                            ['text' => '查看名单','uri'=>'clients.list','ismenu'=>false],
                            ['text' => '添加客户','uri'=>'clients.add','ismenu'=>false],
                            ['text' => '批量导入客户','uri'=>'clients.import','ismenu'=>false],
                            ['text' => '导出客户名单','uri'=>'clients.export','ismenu'=>false],
                            ['text' => '批量操作','uri'=>'clients.batch','ismenu'=>false],
                            ['text' => '签约','uri'=>'clients.signup','ismenu'=>false],
                            ['text' => '跟单','uri'=>'clients.follow','ismenu'=>false],
                            ['text' => '编辑客户','uri'=>'clients.edit','ismenu'=>false],
                            ['text' => '删除客户','uri'=>'clients.delete','ismenu'=>false],

                            ['text' => '分配客户责任人','uri'=>'clients.distribution','ismenu'=>false]
                    ]
                ],
                [
                    'text'    => '跟进情况',
                    'name'    => 'sale.followups',
                    'uri'     => 'sale.followups',
                    'hidesub' => true,
                    'sub' => [
                        ['text' => '概览','uri'=>'followups.overview'],
                        ['text' => '跟进记录','uri'=>'followups.list','sub'=>[
                            ['text' => '新增跟进记录','uri'=>'followups.add','ismenu'=>false],
                            ['text' => '删除跟进记录','uri'=>'followups.delete','ismenu'=>false],
                            ['text' => '编辑跟进记录','uri'=>'followups.edit','ismenu'=>false],
                            ['text' => '导出跟进记录','uri'=>'followups.export','ismenu'=>false],
                        ]],
                        ['text' => '转化统计','uri'=>'followups.summary'],
                    ]
                ],
                ['text'=>'盟商管理','uri'=>'sale.franchisees','sub'=>[
                    ['text' => '添加盟商','uri'=>'franchisees.add','ismenu'=>false],
                    ['text' => '批量导入客户','uri'=>'franchisees.import','ismenu'=>false],
                    ['text' => '删除盟商','uri'=>'franchisees.delete','ismenu'=>false]
                ]],
                ['text'=>'合同管理','uri'=>'sale.contracts']
            ]
        ],
        [
            'text'  => '服务',
            'name'  => 'aftersale',
            'uri'   => 'aftersale',
            'class' => 'icon-aftersale',
            'sub'   => [
                ['text'=>'督导服务','uri'=>'aftersale.services'],
                ['text'=>'校360系统','uri'=>'aftersale.x360s'],
                ['text'=>'培训安排','uri'=>'aftersale.trains'],
                ['text'=>'客服工单','uri'=>'aftersale.tickets']
            ]
        ],
        [
            'text'  => '报表',
            'uri'   => 'reports',
            'class' => 'icon-report',
            'sub'   => [
                ['text' => '盟商运营总表','uri'=>'reports.overview'],
                ['text' => '业绩表','uri'=>'reports.performance']
            ]
        ],
        [
            'text'	=> '系统',
            'uri'	=> 'system',
            'class'	=> 'icon-system',
            'sub'	=> [
                [
                    'text'  => '参数设置',

                    'uri'   => 'system.configs'
                ],
                [
                    'text'  => '人员设置',

                    'uri'   => 'system.employees'
                ],
                [
                    'text'  => '权限组设置',
                    
                    'uri'   => 'system.roles'
                ],
                [
                    'text'  => '字典设置',
                    'uri'   => 'system.dicts'
                ]
            ]
        ]
    ]
];
