-- 课前提醒计划
DROP TABLE IF EXISTS `x360p_course_remind_plan`;
CREATE TABLE `x360p_course_remind_plan` (
  `crp_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'og_id',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `day0_push` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否当日推送',
  `day1_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '提前一天是否推送',
  `day2_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '提前2天是否推送',
  `day3_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '提前3天是否推送',
  `day0_push_int_hour` int(11) NOT NULL DEFAULT '0' COMMENT '当天推送时间,6:00 ~ 9:00 范围',
  `dayn_push_int_hour` int(11) NOT NULL DEFAULT '0' COMMENT '提前推送时间，18:00 ~ 22:00 范围',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`crp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课前提醒计划';

-- 异步导出数据表
DROP TABLE IF EXISTS `x360p_data_export`;
CREATE TABLE `x360p_data_export`(
  `de_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT 'og_id',
  `title` varchar(128) DEFAULT '' COMMENT '文件标题',
  `params` text COMMENT '导出参数',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件地址',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`de_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='数据导出记录';


-- 违约扣款记录金额允许负数

ALTER TABLE `x360p_order_cut_amount`
MODIFY COLUMN `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '扣款金额' AFTER `cutamount_did`
;

ALTER TABLE `x360p_order_refund`
MODIFY COLUMN `cut_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '扣款金额'
;


-- 储值增加协议开始日期
ALTER TABLE `x360p_student_money_history`
ADD COLUMN `c_start_int_day` int(11) NOT NULL DEFAULT '0' COMMENT '协议开始日期' AFTER `after_amount`,
ADD COLUMN `c_end_int_day` int(11) NOT NULL DEFAULT '0' COMMENT '协议结束日期' AFTER `c_start_int_day`
;

ALTER TABLE `x360p_student_debit_card`
ADD COLUMN `start_int_day` int(11) NOT NULL DEFAULT '0' COMMENT '协议开始日期' AFTER `buy_int_day`
;

-- 订单条目增加c_start_int_day
ALTER TABLE `x360p_order_item`
ADD COLUMN `c_start_int_day` int(11) NOT NULL DEFAULT '0' COMMENT '协议开始日期' AFTER `expire_time`
;

-- 余额变动历史记录变化成带负数的
ALTER TABLE `x360p_student_money_history`
MODIFY COLUMN `amount` decimal(15,6) NOT NULL DEFAULT '0.00' COMMENT '金额' AFTER `oi_id`,
MODIFY COLUMN `before_amount` decimal(15,6) NOT NULL DEFAULT '0.00' COMMENT '操作前余额' AFTER `amount`,
MODIFY COLUMN `after_amount` decimal(15,6) NOT NULL DEFAULT '0.00' COMMENT '操作后余额' AFTER `before_amount`
;
-- 更新储值卡
UPDATE `x360p_student_debit_card`
SET `start_int_day` = `buy_int_day`
WHERE `start_int_day` = 0 ;

-- 员工部门表新增是否负责任
ALTER TABLE `x360p_employee_dept`
ADD COLUMN `is_charge` tinyint(1) DEFAULT '0' COMMENT '是否负责人' AFTER `jobtitle_did`
;
-- 支付方式
ALTER TABLE `x360p_order_payment_online`
ADD COLUMN `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式：0微信2支付宝3收钱吧' AFTER `status`
;

-- 助教IDS
ALTER TABLE `x360p_class_schedule`
ADD COLUMN `second_eids` varchar(255) DEFAULT '' COMMENT '助教IDS' AFTER `eid`
;


ALTER TABLE `x360p_report_employee_performance_summary`
ADD COLUMN `refund_nums`  int(11) NOT NULL DEFAULT 0 COMMENT '退单数量' AFTER `performance_nums`,
ADD COLUMN `refund_amount` decimal(15,6) NOT NULL DEFAULT '0.000000' COMMENT '退单金额' AFTER `refund_nums`
;

ALTER TABLE `x360p_report_key`
ADD COLUMN `mc_customer_nums` int(11) NOT NULL DEFAULT 0 COMMENT '客户名单数' AFTER `market_channel_nums`,
ADD COLUMN `attendance_nums` int(11) NOT NULL DEFAULT 0 COMMENT '实际出勤人数' AFTER `no_arrange_student_nums`,
ADD COLUMN `class_room_base_nums` int(11) NOT NULL DEFAULT 0 COMMENT '校区每间教室每周上课基数' AFTER `cr_arrange_nums`,
ADD COLUMN `renew_order_nums` int(11) NOT NULL DEFAULT 0 COMMENT '续费订单数' AFTER `renew_student_nums`,
ADD COLUMN `order_nums` int(11) NOT NULL DEFAULT 0 COMMENT '订单数' AFTER `renew_order_nums`
;

DROP TABLE IF EXISTS `x360p_customer_log`;
CREATE TABLE `x360p_customer_log` (
  `clg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '校区ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '客户ID',
  `op_type` int(11) NOT NULL DEFAULT 0 COMMENT '操作类型',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `content` text DEFAULT NULL COMMENT '内容',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) DEFAULT 0 COMMENT '删除人',
  PRIMARY KEY (`clg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户操作日志表';

DROP TABLE IF EXISTS `x360p_market_clue_log`;
CREATE TABLE `x360p_market_clue_log`(
  `mlg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '校区ID',
  `mcl_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '市场名单ID',
  `op_type` int(11) NOT NULL DEFAULT 0 COMMENT '操作类型',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `content` text DEFAULT NULL COMMENT '内容',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) DEFAULT 0 COMMENT '删除人',
  PRIMARY KEY (`mlg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='市场名单操作日志表';

