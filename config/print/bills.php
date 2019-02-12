<?php
/**
 * 打印模板类型配置
 */
return [
    1 => [
        'name' => 'receipt_pay',
        'title' => '缴费收据模板',
        'fields' => [
            'sys' => [
                ['text' => '机构名称', 'field' => 'org_name'],
                ['text' => '校区名', 'field' => 'branch_name']
            ],
            'bs' => [
                ['text' => '学员姓名', 'field' => 'student_name'],
                ['text' => '考勤卡号', 'field' => 'card_no'],
                ['text' => '学员卡号', 'field' => 'sno'],
                ['text' => '学员电话', 'field' => 'first_tel'],
                ['text' => '交费日期', 'field' => 'pay_date'],
                ['text' => '收据编号', 'field' => 'receipt_no'],
                ['text' => '操作员', 'field' => 'op_name'],
                ['text' => '应收合计', 'field' => 'origin_amount'],
                ['text' => '直减优惠', 'field' => 'order_reduce_amount'],
                ['text' => '冲减电子钱包', 'field' => 'balance_paid_amount'],
                ['text' => '实收', 'field' => 'pay_amount'],
                ['text' => '计入电子钱包/欠缴金额', 'field' => 'pay_remain_amount'],
                ['text' => '实收大写', 'field' => 'pay_amount_b'],
                ['text' => '备注', 'field' => 'pay_remark'],
                ['text' => '关注二维码', 'field' => 'qrcode'],
                ['text' => '学习管家用户名', 'field' => 'account']
            ],
            'bm' => [
                ['text' => '项目', 'field' => 'item_name'],
                ['text' => '课程名称', 'field' => 'lesson_name'],
                ['text' => '班级', 'field' => 'class_name'],
                ['text' => '(课程/班级)年份','field'=>'year'],
                ['text' => '(课程/班级)期段','field'=>'season'],
                ['text' => '有效期', 'field' => 'expire_time'],
                ['text' => '数量', 'field' => 'nums'],
                ['text' => '单位', 'field' => 'nums_unit'],
                ['text' => '原价', 'field' => 'origin_price'],
                ['text' => '折后单价', 'field' => 'price'],
                ['text' => '小计', 'field' => 'subtotal'],
                ['text' => '备注', 'field' => 'remark'],
                ['text' => '已付', 'field' => 'paid_amount'],
                ['text' => '欠交', 'field' => 'unpay_amount']
            ]
        ]
    ],
    2 => [
        'name' => 'receipt_refund',
        'title' => '退费收据模板',
        'fields' => [
            'sys' => [
                ['text' => '机构名称', 'field' => 'org_name'],
                ['text' => '校区名', 'field' => 'branch_name']
            ],
            'bs' => [
                ['text' => '学员姓名', 'field' => 'student_name'],
                ['text' => '考勤卡号', 'field' => 'card_no'],
                ['text' => '学员卡号', 'field' => 'sno'],
                ['text' => '学员电话', 'field' => 'first_tel'],
                ['text' => '交费日期', 'field' => 'pay_date'],
                ['text' => '收据编号', 'field' => 'receipt_no'],
                ['text' => '操作员', 'field' => 'op_name'],
                ['text' => '应退金额', 'field' => 'need_refund_amount'],
                ['text' => '扣费金额', 'field' => 'cut_amount'],
                ['text' => '退电子钱包', 'field' => 'refund_balance_amount'],
                ['text' => '实退', 'field' => 'refund_amount'],
                ['text' => '实退大写', 'field' => 'renfund_amount_b'],
                ['text' => '备注', 'field' => 'refund_remark'],
                ['text' => '关注二维码', 'field' => 'qrcode']
            ],
            'bm' => [
                ['text' => '退项目', 'field' => 'item_name'],
                ['text' => '原数量', 'field' => 'old_nums'],
                ['text' => '退数量', 'field' => 'nums'],
                ['text' => '剩余数量', 'field' => 'remain_nums'],
                ['text' => '单位', 'field' => 'nums_unit'],
                ['text' => '单价', 'field' => 'unit_price'],
                ['text' => '小计', 'field' => 'subtotal'],
                ['text' => '备注', 'field' => 'remark']
            ]
        ]
    ],
    3 => [
        'name' => 'attendance',
        'title' => '考勤小票模板',
        'fields' => [
            'sys' => [
                ['text' => '机构名称', 'field' => 'org_name'],
                ['text' => '校区名', 'field' => 'branch_name']
            ],
            'bs' => [
                ['text' => '学员姓名', 'field' => 'student_name'],
                ['text' => '考勤卡号', 'field' => 'card_no'],
                ['text' => '学员卡号', 'field' => 'sno'],
                ['text' => '学员电话', 'field' => 'first_tel'],
                ['text' => '上课课程', 'field' => 'lesson_name'],
                ['text' => '上课科目', 'field' => 'subject_name'],
                ['text' => '上课老师', 'field' => 'teacher_name'],
                ['text' => '上课助教', 'field' => 'second_teacher_name'],
                ['text' => '上课教室', 'field' => 'room_name'],
                ['text' => '上课班级', 'field' => 'class_name'],
                ['text' => '上课时间', 'field' => 'lesson_time'],
                ['text' => '剩余课时', 'field' => 'remain_lesson_hours']
            ]
        ]
    ],
    4   => [
        'name'  => 'pre_charge',
        'title' => '预充值合同模板',
        'fields'    => [
            'sys' => [
                'name'  => '系统变量',
                'fields'    => [
                    ['text' => '机构名称', 'field' => 'org_name'],
                    ['text' => '校区名', 'field' => 'branch_name']
                ]
            ],
            'student'  => [
                'name'  => '学员信息',
                'fields'    => [
                    ['text'=>'学员姓名','field'=>'student_name'],
                    ['text'=>'第一联系人电话','field'=>'first_tel'],
                    ['text'=>'第一联系人姓名','field'=>'first_family_name'],
                    ['text'=>'第一联系人关系','field'=>'first_family_rel'],
                    ['text'=>'就读学校','field'=>'school_name'],
                    ['text'=>'年级','field'=>'school_grade'],
                    ['text'=>'性别','field'=>'sex'],
                    ['text'=>'学员类型','field'=>'student_type'],
                    ['text'=>'钱包余额','field'=>'money']

                ]
            ],
            'smh'   => [
                'name'  => '储值记录',
                'fields'    => [
                    ['text'=>'协议号','field'=>'contract_no'],
                    ['text'=>'储值金额','field'=>'amount'],
                    ['text'=>'储值前余额','field'=>'before_amount'],
                    ['text'=>'储值后余额','field'=>'after_amount']
                ]
            ],
            'dc'    => [
                'name'  => '储值卡',
                'fields'    => [
                    ['text'=>'金额','field'=>'amount'],
                    ['text'=>'卡名称','field'=>'name']
                ]
            ],
            'sdc'   => [
                'name'  => '学员储值信息',
                'fields'    => [
                    ['text'=>'购买日期','field'=>'buy_int_day'],
                    ['text'=>'有效期','field'=>'expire_int_day']
                ]
            ],
            'op'    => [
                'name'  => '经办信息',
                'fields'    => [
                    ['text'=>'姓名','field'=>'name'],
                    ['text'=>'经办日期','field'=>'create_time']
                ]
            ]
        ]
    ],
    5   => [
        'name'  => 'order_lesson',
        'title' => '课表打印模板',
        'fields'    => [
            'sys' => [
                'name'  => '系统变量',
                'fields'    => [
                    ['text' => '机构名称', 'field' => 'org_name'],
                    ['text' => '校区名', 'field' => 'branch_name']
                ]
            ],
            'student'  => [
                'name'  => '学员信息',
                'fields'    => [
                    ['text'=>'学员姓名','field'=>'student_name'],
                    ['text'=>'第一联系人电话','field'=>'first_tel'],
                    ['text'=>'第一联系人姓名','field'=>'first_family_name'],
                    ['text'=>'第一联系人关系','field'=>'first_family_rel'],
                    ['text'=>'就读学校','field'=>'school_name'],
                    ['text'=>'年级','field'=>'school_grade'],
                    ['text'=>'性别','field'=>'sex'],
                    ['text'=>'学员类型','field'=>'student_type'],
                    ['text'=>'钱包余额','field'=>'money']

                ]
            ]
        ]
    ],
    6 => [
        'name' => 'pay_contract',
        'title' => '缴费合同模板',
        'fields' => [
            'sys' => [
                ['text' => '机构名称', 'field' => 'org_name'],
                ['text' => '校区名', 'field' => 'branch_name']
            ],
            'student'  => [
                'name'  => '学员信息',
                'fields'    => [
                    ['text'=>'学员姓名','field'=>'student_name'],
                    ['text'=>'第一联系人电话','field'=>'first_tel'],
                    ['text'=>'第一联系人姓名','field'=>'first_family_name'],
                    ['text'=>'第一联系人关系','field'=>'first_family_rel'],
                    ['text'=>'就读学校','field'=>'school_name'],
                    ['text'=>'年级','field'=>'school_grade'],
                    ['text'=>'性别','field'=>'sex'],
                    ['text'=>'学员类型','field'=>'student_type'],
                    ['text'=>'钱包余额','field'=>'money']
                ]
            ],
            'order' => [
                'name' => '订单信息',
                'fields'    => [
                    ['text' => '学员姓名', 'field' => 'student_name'],
                    ['text' => '考勤卡号', 'field' => 'card_no'],
                    ['text' => '学员卡号', 'field' => 'sno'],
                    ['text' => '学员电话', 'field' => 'first_tel'],
                    ['text' => '交费日期', 'field' => 'pay_date'],
                    ['text' => '收据编号', 'field' => 'receipt_no'],
                    ['text' => '操作员', 'field' => 'op_name'],
                    ['text' => '应收合计', 'field' => 'origin_amount'],
                    ['text' => '直减优惠', 'field' => 'order_reduce_amount'],
                    ['text' => '冲减电子钱包', 'field' => 'balance_paid_amount'],
                    ['text' => '实收', 'field' => 'pay_amount'],
                    ['text' => '计入电子钱包/欠缴金额', 'field' => 'pay_remain_amount'],
                    ['text' => '实收大写', 'field' => 'pay_amount_b'],
                    ['text' => '备注', 'field' => 'pay_remark'],
                    ['text' => '关注二维码', 'field' => 'qrcode'],
                    ['text' => '学习管家用户名', 'field' => 'account']
                ]
            ],
            'items' => [
                'name'  => '缴费项目',
                'fields'    => [
                    ['text' => '项目', 'field' => 'item_name'],
                    ['text' => '课程名称', 'field' => 'lesson_name'],
                    ['text' => '班级', 'field' => 'class_name'],
                    ['text' => '(课程/班级)年份','field'=>'year'],
                    ['text' => '(课程/班级)期段','field'=>'season'],
                    ['text' => '有效期', 'field' => 'expire_time'],
                    ['text' => '数量', 'field' => 'nums'],
                    ['text' => '单位', 'field' => 'nums_unit'],
                    ['text' => '原价', 'field' => 'origin_price'],
                    ['text' => '折后单价', 'field' => 'price'],
                    ['text' => '小计', 'field' => 'subtotal'],
                    ['text' => '备注', 'field' => 'remark'],
                    ['text' => '已付', 'field' => 'paid_amount'],
                    ['text' => '欠交', 'field' => 'unpay_amount']
                ]
            ],
            'op'    => [
                'name'  => '经办信息',
                'fields'    => [
                    ['text'=>'姓名','field'=>'name'],
                    ['text'=>'经办日期','field'=>'create_time']
                ]
            ]
        ]
    ],

];