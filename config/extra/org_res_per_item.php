<?php
/**
 * 资源权限项目
 */
return [
    ['text'=>'所有渠道','uri'=>'channel.all'],
    ['text'=>'所有市场名单','uri'=>'clue.all'],
    ['text'=>'所有客户名单','uri'=>'customer.all'],
    ['text'=>'所有跟进记录','uri'=>'flist.all'],
    ['text'=>'所有体验班级','uri'=>'democlass.checkAll'],
    ['text'=>'所有学员','uri'=>'student.checkAll'],
    ['text'=>'所有订单','uri'=>'order.all'],
    ['text'=>'所有报名项目','uri'=>'orderitems.all'],
    ['text'=>'所有班级','uri'=>'class.checkAll'],
    ['text'=>'所有排课','uri'=>'arrange.checkAll'],
    ['text'=>'所有%老师%课耗','uri'=>'hour.checkAllteacher'],
    ['text'=>'所有备课','uri'=>'prepare.all'],
    ['text'=>'所有课评','uri'=>'comments.all'],
    ['text'=>'所有作品','uri'=>'artwork.all'],
    ['text'=>'所有作业','uri'=>'homework.all'],
    ['text'=>'所有回访记录','uri'=>'visit.all'],
    ['text'=>'所有学情服务','uri'=>'situation.checkAll'],
    ['text'=>'所有学习方案','uri'=>'lesson_buy_suit.checkAll'],
    ['text'=>'所有加盟商','uri'=>'orgs.all',
        'need_user_field'    => [
            'og_id' => 0
        ],
        'need_client_field' => [
            'is_org_open' => 1
        ]
    ],
    ['text'=>'所有加盟商服务记录','uri'=>'franchisee.allService',
        'need_user_field'    => [
            'og_id' => 0
        ],
        'need_client_field' => [
            'is_org_open' => 1
        ]
    ],
    ['text'=>'所有加盟商合同记录','uri'=>'franchisee.allContract',
        'need_user_field'    => [
            'og_id' => 0
        ],
        'need_client_field' => [
            'is_org_open' => 1
        ]
    ],
    ['text'=>'所有校360系统','uri'=>'franchisee.allSystem',
        'need_user_field'    => [
            'og_id' => 0
        ],
        'need_client_field' => [
            'is_org_open' => 1
        ]
    ],
    ['text'=>'所有加盟商报表','uri'=>'franchisee.allReport',
        'need_user_field'    => [
            'og_id' => 0
        ],
        'need_client_field' => [
            'is_org_open' => 1
        ]
    ]
];