-- 作业服务共
-- 作业任务
DROP TABLE IF EXISTS `x360p_homework_task`;
CREATE TABLE `x360p_homework_task` (
  `ht_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '作业任务ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned DEFAULT '0' COMMENT '校区ID',
  `lid` int(11) unsigned DEFAULT '0' COMMENT '课程ID',
  `ca_id` int(11) unsigned DEFAULT '0' COMMENT '班级排课ID',
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型，0：班课作业,1:1对1作业,2:1对多作业',
  `cid` int(11) unsigned DEFAULT '0' COMMENT '班级ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID,1对1的作业时用到',
  `sids` varchar(255) DEFAULT '' COMMENT '多个学员ID,1对多的作业时用到,最多允许8个ID',
  `eid` int(11) unsigned DEFAULT '0' COMMENT '老师ID',
  `htts_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '作业模板ID,默认为0',
  `content` text COMMENT '作业内容，当作业模板ID为0的时候，内容直接为文本，模板ID不为0时，以json形式保存',
  `remark` varchar(255) DEFAULT NULL COMMENT '老师给作业的备注',
  `push_status` tinyint(1) unsigned DEFAULT '0' COMMENT '推送状态(0:待推送,1:已推送)',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ht_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='作业任务表';

-- 作业任务模板设置表
DROP TABLE IF EXISTS `x360p_homework_task_tpl_setting`;
CREATE TABLE `x360p_homework_task_tpl_setting` (
  `htts_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '作业模板名称',
  `setting` text NOT NULL COMMENT 'JSON结构的设置',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`htts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业模板配置表';


-- 作业任务模板定义表
DROP TABLE IF EXISTS `x360p_homework_task_tpl_define`;
CREATE TABLE `x360p_homework_task_tpl_define` (
  `httd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `htts_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '作业模板配置ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`httd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='默认作业模板定义表';

-- 作业附件表（任务、完成、回复公用）
DROP TABLE IF EXISTS `x360p_homework_attachment`;
CREATE TABLE `x360p_homework_attachment` (
  `ha_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `ht_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'homework_task表主键id,att_type为0时有效',
  `hc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'homework_complete表主键id,att_type为1时有效',
  `hcr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'homework_complete_reply表主键id,att_type为2时有效',
  `att_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '附件类型,0:作业任务附件,1:作业完成附件,2:作业回复附件',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '11' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件URL',
  `file_type` varchar(16) DEFAULT '' COMMENT '文件类型',
  `file_size` bigint(20) unsigned DEFAULT '0' COMMENT '文件大小',
  `file_name` varchar(64) DEFAULT '' COMMENT '文件名',
  PRIMARY KEY (`ha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业附件表';

-- 作业完成表
DROP TABLE IF EXISTS `x360p_homework_complete`;
CREATE TABLE `x360p_homework_complete` (
  `hc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '作业提交ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned DEFAULT '0' COMMENT '班级ID',
  `ca_id` int(11) unsigned DEFAULT '0' COMMENT '班级排课ID',
  `lid` int(11) unsigned DEFAULT '0' COMMENT '课程ID',
  `ht_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '作业记录ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `star` tinyint(1) unsigned DEFAULT '0' COMMENT '作业星级(1~5)星级表态度',
  `content` text COMMENT '作业提交的文字内容',
  `is_check` tinyint(1) unsigned DEFAULT '0' COMMENT '是否批改',
  `is_publish` tinyint(1) unsigned DEFAULT '0' COMMENT '是否发表',
  `check_time` int(11) unsigned DEFAULT '0' COMMENT '批改时间',
  `check_uid` int(11) unsigned DEFAULT '0' COMMENT '批改用户ID',
  `check_level` tinyint(1) unsigned DEFAULT '0' COMMENT '批改等级(1:普批，2：精批)',
  `check_content` text COMMENT '批改内容',
  `result_level` tinyint(1) unsigned DEFAULT '0' COMMENT '作业完成等级1-10，需要区分标准课程和非标准课程',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`hc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业完成表';

-- 作业回复表
DROP TABLE IF EXISTS `x360p_homework_reply`;
CREATE TABLE `x360p_homework_reply` (
  `hr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '作业提交ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned DEFAULT '0' COMMENT '校区ID',
  `hc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '作业完成主键ID',
  `content` text COMMENT '回复的批注内容',
  `eid` int(11) unsigned DEFAULT '0' COMMENT '老师ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`hc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业完成表';


-- 剩余课时记录表
-- 课时日表
DROP TABLE IF EXISTS `x360p_lesson_hour_day`;
CREATE TABLE `x360p_lesson_hour_day`(
  `lhd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '日期整数20180101',
  `add_hour_num` decimal(11,2) unsignend NOT NULL DEFAULT '0.00' COMMENT '增加课时数',
  `add_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '增加课时金额',
  `reduce_hour_num` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '减少课时数',
  `reduce_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '减少课时金额',
  `last_hour_num` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT `上一期结余课时数`,
  `last_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT `上一期结余课时金额`,
  `remain_hour_num` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结余课时数',
  `remain_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '结余课时金额',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`lhd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课时日表';

-- 课时月报表
DROP TABLE IF EXISTS `x360p_lesson_hour_month`;
CREATE TABLE `x360p_lesson_hour_month`(
  `lhm_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `int_month` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '月份整数201801',
  `add_hour_num` decimal(11,2) unsignend NOT NULL DEFAULT '0.00' COMMENT '增加课时数',
  `add_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '增加课时金额',
  `reduce_hour_num` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '减少课时数',
  `reduce_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '减少课时金额',
  `last_hour_num` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT `上一期结余课时数`,
  `last_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT `上一期结余课时金额`,
  `remain_hour_num` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结余课时数',
  `remain_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '结余课时金额',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`lhm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课时月报表';

-- 课时年报表
DROP TABLE IF EXISTS `x360p_lesson_hour_year`;
CREATE TABLE `x360p_lesson_hour_year`(
  `lhy_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `int_year` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年份整数2018',
  `add_hour_num` decimal(11,2) unsignend NOT NULL DEFAULT '0.00' COMMENT '增加课时数',
  `add_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '增加课时金额',
  `reduce_hour_num` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '减少课时数',
  `reduce_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '减少课时金额',
  `last_hour_num` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT `上一期结余课时数`,
  `last_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT `上一期结余课时金额`,
  `remain_hour_num` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结余课时数',
  `remain_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '结余课时金额',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`lhy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课时年报表';








