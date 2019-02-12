<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/9/21
 * Time: 15:50
 */
return [
    /*class_log*/
    'class_insert'          => 'name 创建了班级 class',
    'class_import'          => 'name 导入了班级 class',
    'class_update'          => 'name 编辑了班级 class 的信息',
    'class_delete'          => 'name 删除了班级 class',
    'class_student_insert'  => 'name 让学员 student 加入了班级 class',
    'class_student_delete'  => 'name 让学员 student 退出了班级 class',
    // 'class_student_close'   => 'name 给 student 办理了班级: class 的结课',
    'class_close'           => 'name 给班级 class 办理了结课',
    'class_arrange'         => 'name 在 time 给班级 class 进行了排课 排课教室：cr_id 排课老师：teach_eid',
    'delete_class_arrange'  => 'name 删除了班级 class 在 time 的排课',
    'class_upgrade'         => 'name 给班级 old 进行了升班，升班后的班级为 new',
    'class_service'         => 'name 在 time 给班级 class 做了 st_did 记录 服务内容：content',
    'class_service_task'    => 'name 在 time 给班级 class 做了 st_did 安排 备注：remark',
    'class_attendance'      => 'name 给班级 class 在 time 的排课进行了考勤',

    'class_update_status'   => 'name 修改班级状态为 status_text',
    'class_student_stop'    => 'name 给 student_name 办理了停课',
    'class_student_recover' => 'name 给 student_name 办理了复课',

    'add_class_schedule'    => 'name 给 班级 class 创建了排课计划，排课时间为： week start-end 教室： cr_id 老师： eid',
    'delete_class_schedule' => 'name 删除了班级 class 的排课计划 排课时间为： week start-end 教室： cr_id 老师： eid',
    



    /*student_log*/
    'student_stop_lesson'      => 'name 为学生 student 办理了课程 lesson 的停课 停课时间：stop_time 预计复课时间：recover_time 备注：stop_remark',
    'student_recover_lesson'   => 'name 为学生 student 办理了课程 lesson 的复课 复课日期 time',
    'student_suspend'          => 'name 为学生 student 办理了休学 休学日期：suspend_date 预计复学日期：back_date 休学原因：suspend_reason',
    'student_back'             => 'name 为学生 student 办理了复学',
    'student_quit'             => 'name 为学生 student 办理了退学 退学原因：reason 备注：remark',
    'student_enrol'            => 'name 为学生 student 办理了入学',
    'student_close_lesson'     => 'name 为学生 student 办理了课程：lesson 的结课',
    'student_back_to_customer' => 'name 为学生 student 办理了客户回流',
    'student_transfer_branch'  => '转校,由校区{from_branch_name}转到{to_branch_name}',
    'student_leave'            => 'name 为学生 student 办理了上课时间为 time 的请假，请假课程：lid, 请假类型：type, 请假原因：reason', 
    'student_leave_delete'     => 'name 为学生 student 撤销了上课时间为 time 的请假',
    'student_transfer_class'   => 'name 为学生 student 办理了转班 转出班级：from_cid 转入班级：to_cid ',
    'student_insert'           => 'name 添加了学员 student',
    'student_import'           => 'name 导入了学员 student',
    'student_delete'           => 'name 删除了学员 student',
    'student_edit'             => 'name 编辑了学员 student 信息',
    'student_pay_order'        => 'name 为学员 student 进行了缴费 订单编号：order_no, 缴费金额：amount',
    'student_order_refund'     => 'name 为学员 student 办理了退费 退费日期：time 退费金额：amount',
    'student_transfer'         => 'name 为学员 student 办理了课程结转 结转金额：amount 扣款金额：cut',
    'student_transhours'       => 'name 将学员 from_sid 的 lesson_hours个( lid )课时转让给学员 to_sid 备注：remark',
    'student_transmoneys'      => 'from_sid 转让金额 amount 给学员 to_sid  备注：remark',
    'student_service'          => 'name 在 time 为学员 student 做了 st_did 服务记录 服务内容：content',
    'student_service_task'     => 'name 在 time 为学员 student 做了 st_did 服务安排 备注：remark',
    'student_assign_teacher'   => 'name 将学员 student 分配给班主任 teacher',
    'student_edit_avatar'      => 'name 更换了学员 student 的头像',


    /*customer_log*/
    'customer_assign'     =>  'name 在 time 将客户 customer 分配给了 follow_eid',
    'customer_to_student' =>  'name 将 客户 customer 转为正式学员',
    'customer_follow_up'  =>  'name 对客户 customer 进行了跟单', 
    'customer_edit'       =>  'name 编辑了客户 customer 的信息',
    'customer_add'        =>  'name 添加了客户 customer',
    'customer_delete'     =>  'name 删除了客户 customer',
    'customer_import'     =>  'name 导入了客户 customer',


    /*market_clue_log*/
    'market_clue_insert'  => 'name 添加了市场名单 market_clue',
    'market_clue_import'  => 'name 导入了市场名单 market_clue',
    'market_clue_edit'    => 'name 编辑了市场名单 market_clue 的信息',
    'market_clue_delete'  => 'name 删除了市场名单 market_clue',
    'market_clue_change'  => 'name 将市场名单 market_clue 转为客户名单',
    'market_clue_to_market_to_bid' => 'name 将市场名单 market_clue 分配到市场名单 分配的校区为 bid',
    'maeket_clue_to_market_to_eid' => 'name 将市场名单 market_clue 分配到市场名单 跟进人是 eid',
    'market_clue_to_customer_to_bid' => 'name 将市场名单 market_clue 分配到客户名单 分配的校区为 bid',
    'maeket_clue_to_customer_to_eid' => 'name 将市场名单 market_clue 分配到客户名单 跟进人是 eid',

];