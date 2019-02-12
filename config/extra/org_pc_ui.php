<?php
return [
    'quick_menu'    => [
        [
            'name'  => '',
            'rid'   => 0,
            'items' => [
                [
                    'name'  => 'addcustomer',
                    'text'  => '咨询登记',
                    'icon'  => 'https://sp1.xiao360.com/static/ui/pc/t/default/register-fast.png',
                    'uri'   => 'recruiting/recruiting-info-modal.vue',
                    'is_system' => 1
                ],
                [
                    'name'  => 'follow',
                    'text'  => '客户跟进',
                    'icon'  => 'https://sp1.xiao360.com/static/ui/pc/t/default/visit-fast.png',
                    'uri'   => 'recruiting/following/follow-info-modal.vue',
                    'is_system' => 1
                ],
                [
                    'name'  => 'signup',
                    'text'  => '新生报名',
                    'icon'  => 'https://sp1.xiao360.com/static/ui/pc/t/default/signup-fast.png',
                    'uri'   => './signup',
                    'is_system' => 1
                ],
                [
                    'name'  => 'payment',
                    'text'  => '老生缴费',
                    'icon'  => 'https://sp1.xiao360.com/static/ui/pc/t/default/payment-fast.png',
                    'uri'   => './payment',
                    'is_system' => 1
                ],
                [
                    'name'  => 'attendance',
                    'text'  => '考勤',
                    'icon'  => 'https://sp1.xiao360.com/static/ui/pc/t/default/attend-fast.png',
                    'uri'   => 'business/attendance/attendance-list.vue@modal',
                    'is_system' => 1
                ],
                [
                    'name'  => 'transfer',
                    'text'  => '结转',
                    'icon'  => 'https://sp1.xiao360.com/static/ui/pc/t/default/transfer-fast.png',
                    'uri'   => './transfer',
                    'is_system' => 1
                ],
                [
                    'name'  => 'refund',
                    'text'  => '退费',
                    'icon'  => 'https://sp1.xiao360.com/static/ui/pc/t/default/refund-fast.png',
                    'uri'   => './refund',
                    'is_system' => 1
                ],
                [
                    'name'  => 'leave',
                    'text'  => '请假',
                    'icon'  => 'https://sp1.xiao360.com/static/ui/pc/t/default/leave-fast.png',
                    'uri'   => 'business/leave/info-modal.vue',
                    'is_system' => 1
                ],
                [
                    'name'  => 'sendsms',
                    'text'  => '发短信',
                    'icon'  => 'https://sp1.xiao360.com/static/ui/pc/t/default/sms-fast.png',
                    'uri'   => 'app/modal/sms-template-modal.vue',
                    'is_system' => 1
                ],
                [
                    'name'  => 'sendwx',
                    'text'  => '发微信',
                    'icon'  => 'https://sp1.xiao360.com/static/ui/pc/t/default/wx-fast.png',
                    'uri'   => 'app/modal/wx-template-modal.vue',
                    'is_system' => 1
                ]
            ]
        ]
    ],
    'disabled_per_items' => []
];