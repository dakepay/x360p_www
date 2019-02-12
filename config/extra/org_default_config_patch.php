<?php

/**
 * 本配置是org_default_config的补丁
 * 用于规定哪些键值配置是可以校区配置的
 */

return [
    'params'    =>  [
        'class_attendance'  => [
            '_bid_fields'    => [
                'allow_reg_history',
                'reg_history_pass_days',
                'reg_history_pass_months',
                'allow_del_history',
                'del_history_pass_days',
                'del_history_pass_months'
            ]
        ],
        'student_signup'    =>  [
            '_bid_fields'    =>  [
                'allow_modify_date',
                'modify_date_days',
                'modify_date_months',
                'enable_debit_card',
                'print_bill_type'
            ]
        ],
        '_bid_fields'    => [
            'student_refund'
        ]
    ]
];