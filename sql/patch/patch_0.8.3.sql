-- 员工自定义字段
ALTER TABLE `x360p_base`.`x360p_employee` 
ADD COLUMN `option_fields` text NULL COMMENT '自定义字段' AFTER `remark`;


-- 学员考试记录表
DROP TABLE IF EXISTS `x360p_student_exam`;
CREATE TABLE `x360p_student_exam`(
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
-- 学员考试成绩表
DROP TABLE IF EXISTS `x360p_student_exam_score`;
CREATE TABLE `x360p_student_exam_score`(
  `ses_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '意向学员ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `se_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '考试ID',
  `exam_subject_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `score` decimal(6,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '成绩',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ses_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='考试分数表';
