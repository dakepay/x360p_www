-- 储值卡表
DROP TABLE IF EXISTS `x360p_debit_card`;
CREATE TABLE `x360p_debit_card` (
  `dc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bids` varchar(255)  DEFAULT '' COMMENT '校区ID',
  `dpt_ids` varchar(255) DEFAULT '' COMMENT '大区ID',
  `card_name` varchar(64) DEFAULT '' COMMENT '卡名',
  `amount` decimal(11,2) DEFAULT '0.00' COMMENT '金额',
  `discount_define` text COMMENT '折扣定义(JSON格式)',
  `expire_days` int(11) DEFAULT '365' COMMENT '有效期天数0为无限制',
  `upgrade_vip_level` int(11) DEFAULT '0' COMMENT '升级到会员级别',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`dc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- 学员储值卡记录
DROP TABLE IF EXISTS `x360p_student_debit_card`;
CREATE TABLE `x360p_student_debit_card` (
  `sdc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` varchar(255)  DEFAULT '' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `dc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '储值卡ID',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单条目ID',
  `remain_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '剩余金额',
  `is_used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用0未使用，1部分使用，2全部使用',
  `buy_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买日期',
  `expire_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期日期',
  `is_expired` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否过期',
  `upgrade_vip_level` int(11) DEFAULT '0' COMMENT '升级到会员级别',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`sdc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- 增加导入金额类型
ALTER TABLE `x360p_student_money_history`
MODIFY COLUMN `business_type` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '业务类型:(1:结转,2:退费,3:充值, 4:下单, 5:订单续费 ,10 导入,11:用户手动增加， 12手动减少)' AFTER `og_id`;

-- 学员充值记录需要关联学员储值卡记录
ALTER TABLE `x360p_student_money_history`
ADD COLUMN `sdc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员储值卡记录ID' AFTER `sid`,
ADD COLUMN `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单条目ID' AFTER `sdc_id`
;
-- 学员订单需要关联学员储值卡记录
ALTER TABLE `x360p_order`
ADD COLUMN `sdc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员储值卡记录ID' AFTER `is_demo`
;


ALTER TABLE `x360p_market_clue`
ADD COLUMN `assigned_time`  int(11) NOT NULL DEFAULT 0 COMMENT '分配时间' AFTER `assigned_eid`;

ALTER TABLE `x360p_order_item`
ADD COLUMN `sdc_id`  int(11) NOT NULL DEFAULT 0 COMMENT '储蓄卡id' AFTER `expire_time`;

ALTER TABLE `x360p_employee_lesson_hour`
ADD COLUMN `edu_eid`  int(11) UNSIGNED NULL DEFAULT 0 COMMENT '导师ID' AFTER `second_eid`;

ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `edu_eid`  int(11) UNSIGNED NULL DEFAULT 0 COMMENT '导师ID' AFTER `second_eid`;
-- 员工学员分配增加角色id
ALTER TABLE `x360p_employee_student`
ADD COLUMN `rid` int(11) unsigned NOT NULL DEFAULT '4' COMMENT '角色ID,默认是学管师' AFTER `type`
;
-- 更新默认值
UPDATE `x360p_employee_student`
set `rid`=4
where `type`=2
;

-- 课标增加chater_index
ALTER TABLE `x360p_lesson_standard_file`
ADD COLUMN `chapter_index` int(11) NOT NULL DEFAULT 0 COMMENT '课程章节' AFTER `title`
;