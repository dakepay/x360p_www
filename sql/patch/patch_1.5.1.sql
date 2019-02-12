-- 外教课评学员记录表
DROP TABLE IF EXISTS `x360p_evaluate`;
CREATE TABLE `x360p_evaluate` (
  `eva_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '校区ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '客户ID',
  `sid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '学员ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '日期',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '开始时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '上课结束时间',
  `eid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '上课老师ID',
  `result` text COMMENT '评估结果',
  `result_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '结果录入时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户UID',
  PRIMARY KEY (`eva_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='1对1评估表';


-- 老师可用时间段表
DROP TABLE IF EXISTS `x360p_employee_time_section`;
CREATE TABLE `x360p_employee_time_section` (
  `ets_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `bid` int(11) NOT NULL COMMENT '校区id',
  `eid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '员ID',
  `week_day` smallint(1) DEFAULT -1 COMMENT '星期几',
  `int_start_hour` int(4) unsigned NOT NULL DEFAULT 0 COMMENT '开始时间(800)',
  `int_end_hour` int(4) unsigned NOT NULL DEFAULT 0 COMMENT '结束时间(1000)',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned DEFAULT 0,
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
  PRIMARY KEY (`ets_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='老师可用时间段表';


-- 转介绍学员
DROP TABLE IF EXISTS `x360p_student_referer`;
CREATE TABLE `x360p_student_referer` (
  `sr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `bid` int(11) NOT NULL COMMENT '校区id',
  `sid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '被介绍学员ID',
  `referer_sid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '介绍学员ID',
  `referer_cc_eid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '咨询师',
  `referer_teacher_eids` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '老师eid',
  `referer_edu_eid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '学管师eid',
  `referer_int_day` int(8) unsigned NOT NULL DEFAULT 0 COMMENT '介绍日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned DEFAULT 0,
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
  PRIMARY KEY (`sr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='转介绍学员';



-- 杂费添加是否可以修改单价
ALTER TABLE `x360p_pay_item`
ADD COLUMN `is_flex_price` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否弹性价格' AFTER `is_performance`
;


ALTER TABLE `x360p_class_attendance`
  ADD COLUMN `is_confirm` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否确认考勤（一对一）' AFTER `is_demo`,
  ADD COLUMN `confirm_eid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '确认人EID' AFTER `is_confirm`,
  ADD COLUMN `confirm_time` int(11) unsigned DEFAULT NULL DEFAULT 0 COMMENT '确认时间' AFTER `confirm_eid`
;

ALTER TABLE `x360p_employee_student`
ADD COLUMN `rid` int(11) NOT NULL DEFAULT 0 COMMENT '角色ID' AFTER `eid`,
ADD COLUMN `lid` int(11) NOT NULL DEFAULT 0 COMMENT '课程ID' AFTER `sid`,
ADD COLUMN `cid` int(11) NOT NULL DEFAULT 0 COMMENT '班级ID' AFTER `type`
;

-- 配置参数增加校区ID
ALTER TABLE `x360p_config`
ADD COLUMN `bid` int(11) NOT NULL DEFAULT 0 COMMENT '校区ID' AFTER `og_id`,
DROP INDEX `idx_cfg_name`,
ADD UNIQUE INDEX `idx_cfg_name`(`cfg_name`, `og_id`, `bid`) USING BTREE
;

-- 公告正价推送人条件
ALTER TABLE `x360p_broadcast`
ADD COLUMN `lids` varchar(255) DEFAULT NULL COMMENT '课程ID' AFTER `dpt_ids`,
ADD COLUMN `cids` varchar(255) DEFAULT NULL COMMENT '班级ID' AFTER `lid`,
ADD COLUMN `sids` varchar(255) DEFAULT NULL COMMENT '学生ID' AFTER `cid`
;


DROP TABLE IF EXISTS `x360p_ft_login_log`;
CREATE TABLE `x360p_ft_login_log` (
  `fll_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT 0 COMMENT 'og_id',
  `bid` int(11) NOT NULL DEFAULT 0 COMMENT 'bid',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `fe_id` int(11) NOT NULL DEFAULT 0 COMMENT '外教员工',
  `ip` varchar(255) DEFAULT NULL COMMENT '登录ip',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '客户端信息',
  `login_time` int(11) DEFAULT NULL COMMENT '登录时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`fll_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='外教端登录日志';

alter table x360p_book modify remark text COMMENT '备注';