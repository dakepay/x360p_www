-- 加年级字段
ALTER TABLE `x360p_class`
ADD COLUMN `grade` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_class_attendance`
ADD COLUMN `grade` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_class_schedule`
ADD COLUMN `consume_lesson_hour`  decimal(11,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '扣多少课时' AFTER `int_end_hour`;

ALTER TABLE `x360p_course_arrange`
ADD COLUMN `grade` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_course_arrange_student`
ADD COLUMN `grade` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_credit_rule`
DROP COLUMN `op`,
ADD COLUMN `type`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '1,增加；2减少' AFTER `hook_action`,
ADD COLUMN `cate`  tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '积分类型： 1学习积分，2消费积分' AFTER `type`,
ADD COLUMN `is_system`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否系统规划' AFTER `rule`,
ADD COLUMN `enable`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否启用' AFTER `is_system`
;


ALTER TABLE `x360p_customer`
ADD COLUMN `home_address`  varchar(255) NULL COMMENT '家庭住址' AFTER `second_tel`,
ADD COLUMN `openid` varchar(64) NOT NULL DEFAULT '' COMMENT '微信openid' AFTER `home_address`
;

-- 成长对比
DROP TABLE IF EXISTS `x360p_edu_growup`;
CREATE TABLE `x360p_edu_growup` (
  `eg_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '成长对比ID',
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '加盟商id',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `ability_ids` varchar(255) DEFAULT '' COMMENT '提升能力ID，多ID之间用逗号分隔',
  `title` varchar(255) DEFAULT '' COMMENT '成长对比标题',
  `content` text COMMENT '成长对比内容（文字描述)',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`eg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成长对比表';

DROP TABLE IF EXISTS `x360p_edu_growup_item`;
CREATE TABLE `x360p_edu_growup_item` (
  `egi_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '成长对比记录ID',
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '加盟商',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `eg_id` int(11) unsigned NOT NULL COMMENT '成长对比ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '成长对比标题',
  `before_content` text COMMENT '成长对比之前内容（文字描述)',
  `after_content` text COMMENT '成长对比之后内容（文字描述)',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`egi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成长对比记录';

DROP TABLE IF EXISTS `x360p_edu_growup_pic`;
CREATE TABLE `x360p_edu_growup_pic` (
  `egp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '成长对比图片ID',
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '加盟商',
  `bid` int(11) unsigned DEFAULT '0' COMMENT '校区ID',
  `eg_id` int(11) unsigned NOT NULL COMMENT '成长对比ID',
  `egi_id` int(11) unsigned NOT NULL COMMENT '成长对比记录ID',
  `position` enum('after','before') NOT NULL COMMENT '图片位置，之前还是之后',
  `url` varchar(255) DEFAULT NULL COMMENT '附件路径',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`egp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成长对比图片表';


ALTER TABLE `x360p_employee_lesson_hour`
ADD COLUMN `grade` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_makeup_arrange`
ADD COLUMN `grade` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

-- 学员积分
ALTER TABLE `x360p_student`
MODIFY COLUMN `credit`  decimal(11,2) NOT NULL DEFAULT 0.00 COMMENT '学员积分' AFTER `money`,
ADD COLUMN `credit2`  decimal(11,2) NOT NULL DEFAULT 0 COMMENT '消费积分' AFTER `credit`
;

ALTER TABLE `x360p_student_absence`
ADD COLUMN `grade` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_student_attendance`
ADD COLUMN `grade` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;
-- 积分历史记录类别
ALTER TABLE `x360p_student_credit_history`
ADD COLUMN `cate`  tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '积分类型： 1学习积分，2消费积分' AFTER `type`
;

ALTER TABLE `x360p_student_leave`
ADD COLUMN `grade` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_student_lesson`
ADD COLUMN `fit_grade_start` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '适应年级start' AFTER `sj_ids`,
ADD COLUMN `fit_grade_end` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '适应年级end' AFTER `fit_grade_start`
;

ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `grade` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

-- 更新课程年龄年级字段
update `x360p_lesson`
set `fit_grade_start` = 0,`fit_grade_end` = 0,`fit_age_start` = 0 ,`fit_age_end` = 0
where `fit_grade_start` = 1 and `fit_grade_end` = 12 and `fit_age_start` = 1 and `fit_age_end` = 18
;