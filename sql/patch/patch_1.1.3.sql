-- 服务报表系统
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
  `year` int(4) DEFAULT '0',
  `month` int(2) DEFAULT '0',
  `week` int(1) DEFAULT '0',
  `day` int(2) DEFAULT '0',
  `int_day` int(8) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `x360p_report_service_by_system`
ADD COLUMN `arrange_times`  int NULL DEFAULT 0 COMMENT '排课次数' AFTER `s7_nums`;

ALTER TABLE `x360p_report_service_by_system`
ADD COLUMN `attendance_times`  int NULL DEFAULT 0 COMMENT '考勤次数' AFTER `arrange_times`;


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


DROP TABLE IF EXISTS `x360p_report_lessonhour`;
CREATE TABLE `x360p_report_lessonhour` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `sign_lesson_num` decimal(10,2) DEFAULT '0.00' COMMENT '报名课时',
  `send_lesson_num` decimal(10,2) DEFAULT '0.00' COMMENT '赠送课时',
  `convert_lesson_num` decimal(10,2) DEFAULT '0.00' COMMENT '结转课时',
  `consume_lesson_num` decimal(10,2) DEFAULT '0.00' COMMENT '消耗课时',
  `refund_lesson_num` decimal(10,2) DEFAULT '0.00' COMMENT '退费课时',
  `remain_lesson_num` decimal(10,2) DEFAULT '0.00' COMMENT '剩余课时',
  `remain_lesson_amount` decimal(10,2) DEFAULT '0.00' COMMENT '剩余金额',
  `int_day` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `x360p_student_artwork`
ADD COLUMN `cid`  int NOT NULL DEFAULT 0 COMMENT '班级' AFTER `eid`;

ALTER TABLE `x360p_homework_complete`
ADD COLUMN `sart_id`  int NOT NULL DEFAULT 0 COMMENT '作品id' AFTER `result_level`;

ALTER TABLE `x360p_homework_publish`
DROP PRIMARY KEY,
ADD PRIMARY KEY (`hp_id`);
