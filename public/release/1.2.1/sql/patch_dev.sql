ALTER TABLE `x360p_class`
ADD COLUMN `class_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '班级类型，默认为0,1为临时班' AFTER `class_no`
;

ALTER TABLE `x360p_homework_complete`
ADD COLUMN `sart_id` int(11) NOT NULL DEFAULT '0' COMMENT '作品id' AFTER `result_level`
;

ALTER TABLE `x360p_market_clue`
ADD COLUMN `is_reward` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否奖励' AFTER `is_deal`,
ADD COLUMN `is_visit` tinyint(1) DEFAULT '0' COMMENT '是否上门' AFTER `is_reward`
;

ALTER TABLE `x360p_student_artwork`
ADD COLUMN `cid` int(11) NOT NULL DEFAULT '0' COMMENT '班级' AFTER `eid`
;

ALTER TABLE `x360p_student_lesson`
ADD COLUMN `price_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '计费模式（1:按课次计费,2:课时收费,3:按时间收费）' AFTER `lesson_type`
;

DROP TABLE IF EXISTS `x360p_report_class_by_teacher`;
CREATE TABLE `x360p_report_class_by_teacher` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `teach_eid` int(11) DEFAULT '0',
  `bids` varchar(50) DEFAULT '0',
  `cid` int(11) DEFAULT '0',
  `lid` int(11) DEFAULT '0',
  `ca_ids` varchar(50) DEFAULT NULL COMMENT '排课ids',
  `on_ca_ids` varchar(50) DEFAULT NULL COMMENT '已上排课ids',
  `total_arrange_nums` int(11) DEFAULT '0' COMMENT '总排课',
  `on_arrange_nums` int(11) DEFAULT '0' COMMENT '已上排课',
  `year` int(11) DEFAULT '0',
  `month` int(11) DEFAULT '0',
  `week` int(11) DEFAULT '0',
  `day` int(11) DEFAULT '0',
  `int_day` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `x360p_report_service_by_system`;
CREATE TABLE `x360p_report_service_by_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `eid` int(11) NOT NULL DEFAULT '0',
  `s1_times` int(11) DEFAULT '0' COMMENT '课前提醒次数',
  `s1_nums` int(11) DEFAULT '0' COMMENT '课前提醒人数',
  `s2_times` int(11) DEFAULT '0' COMMENT '备课服务次数',
  `s2_nums` int(11) DEFAULT '0' COMMENT '备课服务人数',
  `s3_times` int(11) DEFAULT '0' COMMENT '到离校通知次数',
  `s3_nums` int(11) DEFAULT '0' COMMENT '到离校通知人数',
  `s4_times` int(11) DEFAULT '0' COMMENT '课评服务次数',
  `s4_nums` int(11) DEFAULT '0' COMMENT '课评服务人数',
  `s5_times` int(11) DEFAULT '0' COMMENT '作业服务粗疏',
  `s5_nums` int(11) DEFAULT '0' COMMENT '作业服务人数',
  `s6_times` int(11) DEFAULT '0' COMMENT '作品服务次数',
  `s6_nums` int(11) DEFAULT '0' COMMENT '作品服务人数',
  `s7_times` int(11) DEFAULT '0' COMMENT '学员回访次数',
  `s7_nums` int(11) DEFAULT '0' COMMENT '学员回访人数',
  `arrange_times` int(11) DEFAULT '0' COMMENT '排课次数',
  `attendance_times` int(11) DEFAULT '0' COMMENT '考勤次数',
  `year` int(11) DEFAULT '0',
  `month` int(11) DEFAULT '0',
  `week` int(11) DEFAULT '0',
  `day` int(11) DEFAULT '0',
  `int_day` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `x360p_report_trial`;
CREATE TABLE `x360p_report_trial` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) DEFAULT '0' COMMENT '校区ID',
  `tla_id` int(11) DEFAULT '0' COMMENT '试听ID',
  `student_name` varchar(40) DEFAULT NULL COMMENT 'x学员姓名',
  `student_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：意向客户 1：正式学员',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：未报读 1：已报读',
  `teach_eid` int(11) NOT NULL DEFAULT '0' COMMENT '试听老师',
  `sign_amount` decimal(11,2) DEFAULT '0.00' COMMENT '报读金额',
  `sign_time` int(11) DEFAULT '0' COMMENT '报读时间',
  `lid` int(11) DEFAULT '0' COMMENT '报读课程',
  `receive_amount` decimal(11,2) DEFAULT '0.00' COMMENT '实收费金额',
  `eid` int(11) DEFAULT '0' COMMENT '收款人',
  `int_day` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `x360p_wechat_tpl_define`;
CREATE TABLE `x360p_wechat_tpl_define` (
  `wtd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(128) DEFAULT '' COMMENT '模板名称',
  `tpl_id` varchar(32) DEFAULT '' COMMENT '模板ID',
  `tpl_define` text COMMENT '模板定义,json结构',
  `business_type` varchar(32) DEFAULT '' COMMENT '业务类型(可选无)',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`wtd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信消息模板配置';