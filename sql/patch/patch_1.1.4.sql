-- 体验课班级字段增加
ALTER TABLE `x360p_class`
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `ext_id`
;

ALTER TABLE `x360p_class_attendance`
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `consume_lesson_hour`
;


ALTER TABLE `x360p_course_arrange`
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `is_makeup`,
ADD COLUMN `create_type`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '1机构排的课，2家长申请约课' AFTER `consume_lesson_hour`
;

ALTER TABLE `x360p_customer`
ADD COLUMN `assign_time`  int(11) NOT NULL DEFAULT 0 COMMENT '客户分配给员工时间' AFTER `follow_eid`
;


-- 体验课相关表
-- 体验课学员转换历史记录
CREATE TABLE `x360p_demo_transfer_history` (
  `dth_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `sid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '学员ID',
  `from_cid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '从哪个班级转换的',
  `teach_eid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '老师ID',
  `second_eid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '助教ID',
  `edu_eid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '咨询师ID',
  `sign_amount` decimal(11,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '报名金额',
  `int_day` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`dth_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 教师课耗与学员课耗要关联
ALTER TABLE `x360p_employee_lesson_hour`
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `payed_lesson_amount`,
ADD COLUMN `slh_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关联学员课耗记录ID,change_type 为2时有效' AFTER `catt_id`
;



-- 课程单价修改为6位小数点存储
ALTER TABLE `x360p_lesson`
MODIFY COLUMN `unit_price` decimal(15, 6) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '单价，跟随price_type' AFTER `lesson_nums`,
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `ability_did`
;

ALTER TABLE `x360p_lesson_standard_file`
ADD COLUMN `enable`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用' AFTER `sort`
;

-- 市场渠道表新增字段
ALTER TABLE `x360p_market_channel`
ADD COLUMN `from_did` int(11) NOT NULL DEFAULT '0' COMMENT '招生来源(招生来源字典ID)' AFTER `channel_name`,
ADD COLUMN `is_share` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否共享渠道' AFTER `from_did`,
ADD COLUMN `qr_config` text COMMENT '渠道二维码配置,JSON格式' AFTER `is_share`
;


-- 市场名单增加 二维码eid
ALTER TABLE `x360p_market_clue`
ADD COLUMN `qr_eid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '二维码eid' AFTER `assigned_eid`
;


ALTER TABLE `x360p_order`
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `remark`
;


ALTER TABLE `x360p_order_item`
MODIFY COLUMN `expire_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '有效期' AFTER `deduct_present_lesson_hours`,
MODIFY COLUMN `origin_price` decimal(15, 6) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '原始单价' AFTER `nums_unit`,
MODIFY COLUMN `price` decimal(15, 6) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '折后单价（成交单价）' AFTER `origin_price`,
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `deduct_present_lesson_hours`
;


ALTER TABLE `x360p_order_payment_history`
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `paid_time`
;

ALTER TABLE `x360p_order_transfer`
ADD COLUMN `balance_amount`  decimal(32,3) NOT NULL DEFAULT 0 COMMENT '结转到电子钱包的金额' AFTER `bill_no`
;

-- 课时报表
DROP TABLE IF EXISTS `x360p_report_lessonhour`;
CREATE TABLE `x360p_report_lessonhour` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `origin_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '期初课时',
  `origin_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '期初课时金额',
  `sign_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '报名课时',
  `sign_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '报名课时金额',
  `send_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '赠送课时',
  `convert_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '结转课时',
  `convert_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '结转课时金额',
  `consume_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '消耗课时',
  `consume_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '消费课时金额',
  `refund_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '退费课时',
  `refund_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '退费课时金额',
  `remain_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '剩余课时',
  `remain_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '剩余金额',
  `int_day` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 更新学员的vip_level 字段
ALTER TABLE `x360p_student`
MODIFY COLUMN `vip_level` int(11) NOT NULL DEFAULT -1 COMMENT 'VIP等级' AFTER `credit2`,
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `student_lesson_remain_hours`,
ADD COLUMN `is_demo_transfered` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课转换' AFTER `is_demo`
;

ALTER TABLE `x360p_student_attendance`
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `is_consume`
;

ALTER TABLE `x360p_student_lesson`
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `end_int_day`
;


ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `is_makeup`,
ADD COLUMN `slil_id`  int(11) NOT NULL DEFAULT 0 COMMENT '导入的记录id' AFTER `sl_id`
;

ALTER TABLE `x360p_student_lesson_import_log`
ADD COLUMN `sl_id`  int NOT NULL DEFAULT 0 COMMENT 'sl_id' AFTER `lid`
;


ALTER TABLE `x360p_student_lesson_operate`
MODIFY COLUMN `op_type`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作类型1：手动赠送,2:结转，3:退费，4：购买时赠送' AFTER `bid`,
MODIFY COLUMN `unit_price`  decimal(15,6) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '结转单价' AFTER `lesson_hours`
;


ALTER TABLE `x360p_tally`
ADD COLUMN `is_demo` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `sid`
;


-- 更新教师课耗ID
UPDATE `x360p_employee_lesson_hour` elh LEFT JOIN `x360p_student_lesson_hour` slh on elh.create_time = slh.create_time and elh.eid = slh.eid
set elh.slh_id = slh.slh_id
where elh.change_type = 2 AND elh.slh_id = 0;


UPDATE `x360p_student`
SET `vip_level`=-1
WHERE `vip_level` = 0
;
























