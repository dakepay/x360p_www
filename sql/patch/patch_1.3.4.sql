-- 市场名单新增2个字段
ALTER TABLE `x360p_market_clue`
ADD COLUMN `get_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '获取时间' AFTER `qr_eid`,
ADD COLUMN `family_rel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '家庭关系' AFTER `tel`
;

UPDATE `x360p_market_clue`
set `get_time` = `create_time`
where `get_time` = 0
;


-- student_lesson增加 lesson_amount 字段及 remain_lesson_amount字段
ALTER TABLE `x360p_student_lesson`
ADD COLUMN `lesson_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课时金额' AFTER `lesson_hours`,
ADD COLUMN `remain_lesson_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '剩余课时金额' AFTER `remain_lesson_hours`
;

-- 学员表新增面部特征是否录入字段
ALTER TABLE `x360p_student`
ADD COLUMN `is_face_input` tinyint(1) default '0' COMMENT '面部是否录入' AFTER `is_demo_transfered`,
ADD COLUMN `face_id` varchar(32) default '' COMMENT '盛开人员ID' AFTER `is_face_input`
;

-- 增加索引
ALTER TABLE `x360p_student`
ADD INDEX `idx_card_no`(`card_no`) USING BTREE,
ADD INDEX `idx_face_id`(`face_id`) USING BTREE
;

-- 客户表新增是否公海客户字段
ALTER TABLE `x360p_customer`
ADD COLUMN `is_public` tinyint(1) default '0' COMMENT '是否为公海客户(1是0否)' AFTER `next_follow_time`,
ADD COLUMN `in_public_time` int(11) default '0' COMMENT '转入公海客户时间' AFTER `is_public`,
ADD COLUMN `get_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '获取时间' AFTER `in_public_time`
;

UPDATE `x360p_customer`
set `get_time` = `create_time`
where `get_time` = 0
;

-- 刷脸通知记录
CREATE TABLE `x360p_face_notify_record` (
  `fcr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '刷脸ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '学员ID',
  `face_id` varchar(32) NOT NULL DEFAULT '' COMMENT '卡号',
  `int_day` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '整数天(20170501)',
  `int_hour` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '刷卡时间整数(1700)',
  `business_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '业务类型:0未匹配到,1上课考勤,2离校通知,3到校通知',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
  PRIMARY KEY (`fcr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='刷脸记录表';

-- 学员表新增介绍人ID
ALTER TABLE `x360p_student`
ADD COLUMN `referer_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推荐学员ID' AFTER `is_demo_transfered`
;

-- 更新客户的试听次数
update x360p_customer c
 set c.trial_listen_times = (
  select count(*) from x360p_course_arrange_student cas
  where cas.cu_id = c.cu_id
  and is_trial = 1
  and is_in = 1
  and is_attendance = 1
  and is_delete = 0
 )
where is_delete = 0 and trial_listen_times > 0;
-- 订单条目表新增pi_id
ALTER TABLE `x360p_order_item`
ADD COLUMN `pi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '杂费条目ID' AFTER `gid`
;


-- 订单及订单业绩增加不计提成金额
ALTER TABLE `x360p_order`
ADD COLUMN `unp_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '不计业绩金额' AFTER `order_amount`
;
ALTER TABLE `x360p_order_performance`
ADD COLUMN `unp_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '不计业绩金额' AFTER `amount`
;
-- 员工业绩增加不计提成金额
ALTER TABLE `x360p_employee_receipt`
ADD COLUMN `unp_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '不计业绩金额' AFTER `sid`,
ADD COLUMN `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID' AFTER `orb_id`
;
-- 更新员工收款业绩订单ID
UPDATE `x360p_employee_receipt` erc LEFT JOIN `x360p_order_receipt_bill` orb
ON erc.orb_id = orb.orb_id
set erc.oid = orb.oid
WHERE orb.oid IS NOT NULL AND erc.oid = 0
;

-- course_arrange_student 新增字段是否有额外课消
ALTER TABLE `x360p_course_arrange_student`
ADD COLUMN `has_extra_consume` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有额外课消' AFTER `consume_lesson_hour`
;

ALTER TABLE `x360p_class_attendance`
ADD COLUMN `has_extra_consume` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有额外课消' AFTER `consume_lesson_hour`
;

ALTER TABLE `x360p_student_attendance`
ADD COLUMN `has_extra_consume` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有额外课消' AFTER `consume_lesson_hour`
;

-- student_lesson_hour添加extra_did
ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `is_extra_consume` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否额外课消' AFTER `is_demo`,
ADD COLUMN `extra_consume_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '额外课消项目字典ID' AFTER `is_extra_consume`
;

ALTER TABLE `x360p_employee_lesson_hour`
ADD COLUMN `is_extra_consume` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否额外课消' AFTER `is_demo`,
ADD COLUMN `extra_consume_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '额外课消项目字典ID' AFTER `is_extra_consume`
;


-- 排课记录新增扣课时还是扣余额
ALTER TABLE `x360p_course_arrange`
ADD COLUMN `consume_source_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '课消来源:1:课时,2:电子钱包' AFTER `int_end_hour`
;
-- 排课学员记录新增扣课时还是扣余额
ALTER TABLE `x360p_course_arrange_student`
ADD COLUMN `consume_source_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '课消来源:1:课时,2:电子钱包' AFTER `int_end_hour`
;
-- 授课记录新增扣课时还是扣余额
ALTER TABLE `x360p_class_attendance`
ADD COLUMN `consume_source_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '课消来源:1:课时,2:电子钱包' AFTER `lesson_remark`
;
-- 班级新增扣课时还是扣余额
ALTER TABLE `x360p_class`
ADD COLUMN `consume_source_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '课消来源:1:课时,2:电子钱包' AFTER `per_lesson_hour_minutes`
;


