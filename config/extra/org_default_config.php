<?php
/**
 * 机构默认配置
 * 对应的是数据库 x360p_config表里的记录
 */
$_review_tpl = include(CONF_PATH .'extra'.DS.'org_review_tpl.php');
return [
//系统配置参数
    'params' => [
        'org_name' => '',
        'sysname' => '',
        'address' => '',
        'mobile' => '',
        'present_lesson_consume_method' => 1,
        'return_visit' => [         # 回访条件
            'ask_leave_times' => 3, # 请假三次以上
            'absence_times' => 3,   # 缺勤三次以上
            'not_hand_in_homework_times' => 3,  # 没交作业3次以上
            'student_lesson_remain_times' => 5, # 剩余课次
            'student_lesson_remain_rate' => 0.30,
        ],
        'remind_before_course' => [
            'days_before' => 1,
        ],
        'class_attendance' => [
            'enable_extra_consume' => 0, // 是否开启额外课消
            'min_before_class' => 10,
            'min_after_class' => 10,
            'allow_redo' => 0,  # 是否允许重新考勤
            'allow_debt_att' => 0, # 是否允许负课时考勤，0不允许，1允许，
            'reg_att_before_min'    => 10, #登记考勤允许的时间范围，提前多少分钟
            'allow_reg_history' =>  1,//是否允许登记历史考勤
            'reg_history_pass_days' => 0,//0为无限制，大于0为允许登记历史考勤记录的天数
            'reg_history_pass_months'=>0,//0为无限制，大于0为允许登记历史考勤的月数
            'allow_del_history' => 1,//是否允许撤销历史考勤记录
            'del_history_pass_days' => 0,//0为无限制
            'del_history_pass_months'   => 0,//0为无限制，大于0为允许登记历史考勤的月数
            'enable_money_consume'  => 0,//是否允许余额课消
            'print_attendance_bill'  => 0,//刷卡考勤是否打印小票
            'sl_bcu_subject'         => 0,       //student lesson be common use subject 课时按科目通用科目

        ],
        //学员报名参数
        'student_signup'    => [
            'allow_modify_date' => 1,       //1为允许,0为禁用
            'modify_date_days'  => 30,      //范围是当前日期往前30天
            'modify_date_months'    => 0,   //允许修改日期月数
            'enable_user_contract_no'       =>  0,  //是否启用自定义合同号
            'enable_user_receipt_no'        =>  0,   //是否启用自定义收据号
            'precharge_contract_month'  => 12,//储值协议默认有效月数
            'enable_debit'      =>  1,   //是否启用储值
            'enable_debit_card'      =>  1,   //是否启用储值卡储值
            'enable_debit_without_aa_id'=>  0,//是否允许不收款储值
            'must_from_did' => 0,  //是否必填 1为必填
            'print_bill_type'   => 1,   //缴费打印默认模板1：收据，2：合同
        ],
        //学员退费参数
        'student_refund'    => [
            'allow_modify_date' => 1,       //1为允许,0为禁用
            'modify_date_days'  => 15,      //范围是当前日期往前15天
            'modify_date_months'    =>0     //允许修改日期月数
        ],
        'default_sale_role_did' => [
            'new'       => 101,
            'renew'     => 102
        ],
        'student_leave' => [    # 学生请假设置
            'enable' => 1,      # 是否允许请假
            'minutes_before' => 1440,    # 允许提前几天
            'times_limit'   => 0,         //次数限制 0 为不限制
            'regatt_default_consume' => 0 #请假默认扣课时,0不扣1扣
        ],
        'course_arrange' => [
            'ignore_1by1_cc' => 0, # 忽略一对一排课教室冲突
            'ignore_class_cc'   => 0,   #是否忽略班课教室冲突
            'ignore_class_ec'   => 0,   #是否忽略班课教师冲突 ignore class employee conflict
            'allow_passed_arrange'  => 1,   # 是否允许过期排课
            'allow_mobile_change' => 1, # 允许手机调课
            'enable_tbs'          => 0,//是否启用授课内容设定
        ],
        'service'   => [
            'enable_homework_star'  => 0,   //是否启用作业星级和评分
            'auto_create_record' => 0,  //是否自动创建关联服务记录
            'enable_level'  => 0,       //是否启用服务星级
            'max_level'  => 5,
            'default_sm_pwd_type' => 1,  //学习管家默认密码参数设置
            'default_sm_pwd' => ''
        ],
	    'per_lesson_hour_minutes'   => 60,      //每个课时的时间长度（分钟)
        'per_lesson_hour_price'     => 0.00,    //每个课时的课耗单价
        'leave_warn_times'          => 5,       //请假预警次数
        'ignore_time_long_clh'      => 0,       //忽略排课时长扣课时影响
        'enable_grade'              => 0,      //是否启用年级
        'class_must_sel_lesson'     => 0,       //班级是否必须选课程
        'enable_lesson_ability'     => 0,       //是否启用课程能力设置
        'enable_company'            => 0,       //是否启用分公司
        'member'                    => [
            'enable'                => 0,       //是否开启会员
            'name'                  => 'VIP会员',//会员体系名称
            'max_level'             => 5,       //最大会员等级
            'level'                 => [
                0   =>  ['name'=>'体验课会员'],
                1   =>  ['name'=>'VIP1','amount'=>0,'discount'=>100],
                2   =>  ['name'=>'VIP2','amount'=>0,'discount'=>100],
                3   =>  ['name'=>'VIP3','amount'=>0,'discount'=>100],
                4   =>  ['name'=>'VIP4','amount'=>0,'discount'=>100],
                5   =>  ['name'=>'VIP5','amount'=>0,'discount'=>100]
            ]

        ],
        'class'                     =>[
            'book_filter_rule'      => '1',         //预约补课过滤班级规则，1按年级段  2按科目 3按课程
        ],
        'lesson'                    => [
            'enable_lesson_type'    => [
                0,1,2,3
            ]                                       //启用课程类型,默认是0只启用班课
        ],
        'xxgj'                      => [
            'hide_student_lesson'   => 0,           //是否隐藏学员课时默认为不隐藏
        ],
        'customer'                  => [
            'pc_un_follow_days'     => 0,          //进入公海未跟进天数
            'pc_before_remind_days' => 7,           //进入公海提前提醒天数
            'pc_limit_customer_num' => 200,         //从公海获取客户的跟进客户数量限制
            'follow_warning_days'   => 7,            //跟进提醒天数，橙色显示
	    'must_mc_id' => 0, //是否必填  1为必填
            'must_from_did' => 0, //是否必填  1为必填
            'must_intention_level' => 0,  //意向程度 是否必填  1为必填
            'must_customer_status_did' => 0, // 客户状态 是否必填
        ]

    ],
    'center_params' => [
        'pc'     => [
            'theme'       => "default",
            'url'         => "",
            'system_name' => "",
            'copyright'   => "",
            'login_img'   => "",
            'big_logo'    => "",
            'small_logo'  => "",
        ],
        'mobile' => [
            'url'         => "",
            'system_name' => "",
            'copyright'   => "",
            'logo'        => "",
        ],
    ],
	'storage'	=> [
			'engine'	=> 'qiniu',				//存储引擎 qiniu,local  
			'qiniu'			=> [
				'access_key'	=> 'p9mUPzEN5ihLHctwvBIk5w9MBckHvFSrXadVRlPY',//ak
				'secret_key'	=> 'UJRv2IaSnsFUmZyXmYWyhpcrPW7WIYnslnT749Fh',
				'bucket'		=> 'ygwqms',//
				'prefix'		=> 'qms/',		//KEY前缀
				'domain'		=> 'http://s10.xiao360.com/',//domain
			]
	],
	'review_tpl'	=> $_review_tpl,
    //学员自定义字段
    //type:text,textarea,number,date  
    //type:单行文本，多行文本，数字，日期
    'student_option_fields'    => [
        ['name'=>'f1','label'=>'自定义字段1','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f2','label'=>'自定义字段2','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f3','label'=>'自定义字段3','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f4','label'=>'自定义字段4','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f5','label'=>'自定义字段5','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f6','label'=>'自定义字段6','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f7','label'=>'自定义字段7','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f8','label'=>'自定义字段8','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f9','label'=>'自定义字段9','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f10','label'=>'自定义字段10','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
    ],
    //员工自定义字段
    'employee_option_fields'   => [
        ['name'=>'f1','label'=>'自定义字段1','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f2','label'=>'自定义字段2','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f3','label'=>'自定义字段3','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f4','label'=>'自定义字段4','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f5','label'=>'自定义字段5','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f6','label'=>'自定义字段6','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f7','label'=>'自定义字段7','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f8','label'=>'自定义字段8','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f9','label'=>'自定义字段9','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
        ['name'=>'f10','label'=>'自定义字段10','type'=>'text','enable'=>0,'required'=>0,'option_values'=>[]],
    ],
    //机构展示移动端配置项目
    'school_mobile' =>  [
        'codepay_title'         => '校360商户',            //收款码Title
    ]

];

//user_config('params.consume_type')