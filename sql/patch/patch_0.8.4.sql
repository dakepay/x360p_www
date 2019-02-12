
DROP TABLE IF EXISTS `x360p_course_prepare`;
CREATE TABLE `x360p_course_prepare` (
  `cp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课日期',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课开始时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课结束时间',
  `teach_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '授课老师',
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0班课,11对1，21对多)',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '一对一学员',
  `sids` varchar(255) DEFAULT '' COMMENT '学员ID，1对多有多个学员ID',
  `title` varchar(255) DEFAULT '' COMMENT '备课标题',
  `content` text COMMENT '备课内容',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课ID,默认为0',
  `is_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推送',
  `push_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`cp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='备课记录表';

DROP TABLE IF EXISTS `x360p_course_prepare_attachment`;
CREATE TABLE `x360p_course_prepare_attachment` (
  `cpa_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `cp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '备课记录表主键id',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '文件id',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件URL',
  `file_type` varchar(16) DEFAULT '' COMMENT '文件类型',
  `file_size` bigint(20) unsigned DEFAULT '0' COMMENT '文件大小',
  `file_name` varchar(64) DEFAULT '' COMMENT '文件名',
  `media_type` char(50) DEFAULT NULL COMMENT '媒体类型',
  `duration` varchar(255) DEFAULT NULL COMMENT '音频时长',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`cpa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='备课附件表';

DROP TABLE IF EXISTS `x360p_course_prepare_view`;
CREATE TABLE `x360p_course_prepare_view` (
  `cpv_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `cp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '备课记录表主键ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `student_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学习管家用户ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`cpv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='备课记录查看表';

DROP TABLE IF EXISTS `x360p_student_exam`;
CREATE TABLE `x360p_student_exam` (
  `se_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `exam_name` varchar(255) DEFAULT '' COMMENT '考试名称',
  `exam_type_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '考试类型ID',
  `exam_subject_dids` varchar(255) DEFAULT '' COMMENT '考试科目,逗号风格',
  `exam_int_day` int(11) DEFAULT NULL COMMENT '考试日期',
  `score_release_int_day` int(11) DEFAULT NULL COMMENT '成绩发布日期',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`se_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='考试记录表';


DROP TABLE IF EXISTS `x360p_student_exam_score`;
CREATE TABLE `x360p_student_exam_score` (
  `ses_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '意向学员ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `se_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '考试ID',
  `total_score` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '总分数，各个科目分数',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ses_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='考试分数表';

DROP TABLE IF EXISTS `x360p_student_exam_subject_score`;
CREATE TABLE `x360p_student_exam_subject_score` (
  `sess_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生id',
  `ses_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '考试ID',
  `exam_subject_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `score` decimal(6,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '成绩',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`sess_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='考试分数表';


-- 增加课时总数及剩余课时数
ALTER TABLE `x360p_student`
ADD COLUMN `student_lesson_hours` decimal(11,2) default '0.00' COMMENT '课时总数' AFTER `student_lesson_remain_times`,
ADD COLUMN `student_lesson_remain_hours` decimal(11,2) default '0.00' COMMENT '剩余课时数' AFTER `student_lesson_hours`
;


-- 学员课程记录新增
ALTER TABLE `x360p_student_lesson`
ADD COLUMN `sj_ids` varchar(255) DEFAULT '' COMMENT '可用科目' AFTER `lid`,
ADD COLUMN `refund_lesson_hours` decimal(11,2) DEFAULT '0.00' COMMENT '退费课时数' AFTER `lesson_hours`
;

-- 班级新增收费单价
ALTER TABLE `x360p_class`
ADD COLUMN `unit_price` decimal(11,2) DEFAULT '0.00' COMMENT '课时单价' AFTER `sj_id`
;

ALTER TABLE `x360p_subject`
ADD COLUMN `unit_price` decimal(11,2) DEFAULT '0.00' COMMENT '课时单价' AFTER `short_desc`
;

ALTER TABLE `x360p_subject_grade`
ADD COLUMN `unit_price` decimal(11,2) DEFAULT '0.00' COMMENT '课时单价' AFTER `title`
;

