-- 所有年级字段都更改为允许负数
ALTER TABLE `x360p_class`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT 0 COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_class_attendance`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_course_arrange`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_course_arrange_student`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

-- 排课学员表增加一个是否取消排课字段
ALTER TABLE `x360p_course_arrange_student`
ADD COLUMN `is_cancel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否取消' AFTER `is_attendance`
;

ALTER TABLE `x360p_employee_lesson_hour`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;


ALTER TABLE `x360p_lesson`
MODIFY COLUMN `fit_grade_start` smallint(11) NOT NULL DEFAULT 0 COMMENT '适合年级开始' AFTER `fit_age_end`,
MODIFY COLUMN `fit_grade_end` smallint(11) NOT NULL DEFAULT 0 COMMENT '适合年级结束' AFTER `fit_grade_start`
;


ALTER TABLE `x360p_student_attendance`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;


ALTER TABLE `x360p_student_absence`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;


ALTER TABLE `x360p_student_lesson`
MODIFY COLUMN `fit_grade_start` int(11) NOT NULL DEFAULT '0' COMMENT '适应年级start' AFTER `sj_ids`,
MODIFY COLUMN `fit_grade_end` int(11) NOT NULL DEFAULT '0' COMMENT '适应年级end' AFTER `fit_grade_start`
;

ALTER TABLE `x360p_student_lesson_hour`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;


ALTER TABLE `x360p_student_leave`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_makeup_arrange`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

-- 新增报表
DROP TABLE IF EXISTS `x360p_report_class_by_name`;
CREATE TABLE `x360p_report_class_by_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `lid` int(11) DEFAULT '0',
  `cid` int(11) DEFAULT '0',
  `sids` varchar(255) DEFAULT '0',
  `int_day` int(8) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  `teach_eid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `x360p_report_class_by_number`;
CREATE TABLE `x360p_report_class_by_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) DEFAULT '0',
  `cid` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `lid` int(11) DEFAULT '0',
  `sj_id` int(11) DEFAULT '0',
  `teach_eid` int(11) DEFAULT '0',
  `int_day` int(8) DEFAULT '0',
  `student_num` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `x360p_report_class_by_room`;
CREATE TABLE `x360p_report_class_by_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `cr_id` int(11) DEFAULT '0',
  `cids` varchar(50) DEFAULT '0',
  `ca_ids` varchar(255) DEFAULT '0',
  `arrange_nums` int(11) DEFAULT '0',
  `year` int(4) DEFAULT '0',
  `month` int(11) DEFAULT '0',
  `week` int(11) DEFAULT '0',
  `day` int(11) DEFAULT '0',
  `int_day` int(8) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `x360p_report_student_by_class`;
CREATE TABLE `x360p_report_student_by_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `cid` int(11) DEFAULT '0',
  `in_student_num` int(11) DEFAULT NULL,
  `out_student_num` int(11) DEFAULT '0',
  `year` int(4) DEFAULT '0',
  `month` int(2) DEFAULT '0',
  `week` int(11) DEFAULT NULL,
  `day` int(2) DEFAULT NULL,
  `int_day` int(8) DEFAULT NULL,
  `create_uid` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `lid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `x360p_report_student_by_lesson`;
CREATE TABLE `x360p_report_student_by_lesson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `sid` int(11) DEFAULT '0',
  `student_name` varchar(20) DEFAULT NULL,
  `sno` varchar(50) DEFAULT NULL,
  `first_tel` varchar(20) DEFAULT NULL,
  `lids` varchar(100) DEFAULT NULL,
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `x360p_report_student_by_quit`;
CREATE TABLE `x360p_report_student_by_quit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `sid` int(11) DEFAULT '0',
  `cids` varchar(50) DEFAULT NULL,
  `lids` varchar(50) DEFAULT NULL,
  `quit_reason` int(11) DEFAULT '0',
  `quit_time` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `x360p_report_student_by_school`;
CREATE TABLE `x360p_report_student_by_school` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `school_id` int(11) DEFAULT '0',
  `student_num` int(11) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `month` int(2) DEFAULT NULL,
  `week` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `int_day` int(8) DEFAULT NULL,
  `create_uid` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `x360p_report_student_lesson_class`;
CREATE TABLE `x360p_report_student_lesson_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `school_id` int(11) DEFAULT '0',
  `sid` int(11) DEFAULT '0',
  `lids` varchar(100) DEFAULT '',
  `cids` varchar(100) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 学情服务增加几个字段
ALTER TABLE `x360p_study_situation`
ADD COLUMN `is_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推送,1:推送短信,2:推送微信,3:短信、微信都推送' AFTER `lbs_id`,
ADD COLUMN `push_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送时间' AFTER `is_push`,
ADD COLUMN `is_query` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否查询,0为未查询，1为1查询' AFTER `push_time`,
ADD COLUMN `query_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查询时间' AFTER `is_query`,
ADD COLUMN `query_openid` varchar(32) DEFAULT '' COMMENT '查询openid' AFTER `query_time`,
ADD COLUMN `is_view` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否查看' AFTER `query_openid`,
ADD COLUMN `view_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查看时间' AFTER `is_view`
;
