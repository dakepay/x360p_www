
ALTER TABLE `x360p_report_key`
ADD COLUMN `mc_customer_nums` int(11) NOT NULL DEFAULT 0 COMMENT '客户名单数' AFTER `market_channel_nums`,
ADD COLUMN `attendance_nums` int(11) NOT NULL DEFAULT 0 COMMENT '实际出勤人数' AFTER `no_arrange_student_nums`,
ADD COLUMN `class_room_base_nums` int(11) NOT NULL DEFAULT 0 COMMENT '校区每间教室每周上课基数' AFTER `cr_arrange_nums`,
ADD COLUMN `renew_order_nums` int(11) NOT NULL DEFAULT 0 COMMENT '续费订单数' AFTER `renew_student_nums`,
ADD COLUMN `order_nums` int(11) NOT NULL DEFAULT 0 COMMENT '订单数' AFTER `renew_order_nums`
;

DROP TABLE IF EXISTS `x360p_customer_log`;
CREATE TABLE `x360p_customer_log` (
  `clg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '校区ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '客户ID',
  `op_type` int(11) NOT NULL DEFAULT 0 COMMENT '操作类型',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `content` text DEFAULT NULL COMMENT '内容',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) DEFAULT 0 COMMENT '删除人',
  PRIMARY KEY (`clg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户操作日志表';

DROP TABLE IF EXISTS `x360p_market_clue_log`;
CREATE TABLE `x360p_market_clue_log`(
  `mlg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '校区ID',
  `mcl_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '市场名单ID',
  `op_type` int(11) NOT NULL DEFAULT 0 COMMENT '操作类型',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `content` text DEFAULT NULL COMMENT '内容',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) DEFAULT 0 COMMENT '删除人',
  PRIMARY KEY (`mlg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='市场名单操作日志表';


ALTER TABLE `x360p_course_remind_plan`
ADD COLUMN `is_push_teacher` tinyint(1) DEFAULT '0' COMMENT '是否推送老师' AFTER `dayn_push_int_hour`;


-- 外教端数据库
-- x360p_ft_employee  (外教员工表)
-- x360p_ft_review (外教报告表)
-- x360p_ft_review_file (外教报告附件表)
-- 外教员工表
DROP TABLE IF EXISTS `x360p_ft_employee`;
CREATE TABLE `x360p_ft_employee`(
  `fe_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `eid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '员工ID',
  `origin_country` varchar(32) DEFAULT '' COMMENT '来自哪个国家',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) NOT NULL DEFAULT 0 COMMENT '创建人',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) DEFAULT 0 COMMENT '删除人',
  PRIMARY KEY (`fe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='外教人员表';
-- 外教课评表
DROP TABLE IF EXISTS `x360p_ft_review`;
CREATE TABLE `x360p_ft_review`(
  `frvw_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '外教课评ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `rts_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点评模板配置ID',
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型:0班课，1:1对1,2:1对多，分别对应接下来的3个字段',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `sids` varchar(255) NOT NULL DEFAULT '' COMMENT '1对多学员ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `catt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级考勤ID',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '日期',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课结束时间',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `content` text COMMENT '上课内容:json格式lesson_content,lesson_after_task',
  `trans_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送的翻译助教ID,为0就是所有中教都可以翻译',
  `sent_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '报告发送状态(0待翻译,1正在翻译中,2发送)',
  `rvw_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联的课评ID,发送以后',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`frvw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='外教课评报告主表';
-- 外教课评附件表
DROP TABLE IF EXISTS `x360p_ft_review_file`;
CREATE TABLE `x360p_ft_review_file` (
  `frf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `frvw_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课评ID',
  `file_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件ID',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `file_type` varchar(16) NOT NULL DEFAULT 'image' COMMENT '文件类型:image,audio,video,file',
  `duration` varchar(25) NOT NULL DEFAULT '' COMMENT '当文件为mp3时该字段不为空。',
  `media_type` char(50) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`frf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='外教课评关联附件表';

-- 外教课评学员记录表
DROP TABLE IF EXISTS `x360p_ft_review_student`;
CREATE TABLE `x360p_ft_review_student` (
  `frs_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '校区ID',
  `frvw_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '课评ID',
  `sid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '学员ID',
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '课程类型:0班课，1:1对1,2:1对多，分别对应接下来的3个字段',
  `cid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '班级ID',
  `lid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '课程ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '日期',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '开始时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '上课结束时间',
  `eid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '上课老师ID',
  `score` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分数/星星数',
  `score1` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '打分1',
  `score2` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '打分2',
  `score3` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '打分3',
  `score4` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '打分4',
  `score5` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '打分项5',
  `score6` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '打分项6',
  `score7` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '打分项7',
  `score8` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '打分项8',
  `score9` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '打分项9',
  `detail` text DEFAULT NULL,
  `view_times` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '查看次数',
  `share_times` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分享次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户UID',
  PRIMARY KEY (`frs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='外教课评学员记录表';

-- 外教课评学员记录表
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='外教端登录日志';


ALTER TABLE `x360p_report_branch_performance_summary`
ADD COLUMN `refund_nums`  int(11) NOT NULL DEFAULT 0 COMMENT '退单数量' AFTER `lesson_amount`,
ADD COLUMN `refund_amount` decimal(15,6) NOT NULL DEFAULT '0.000000' COMMENT '退单金额' AFTER `refund_nums`,
ADD COLUMN `cut_amount` decimal(15,6) NOT NULL DEFAULT '0.000000' COMMENT '违约金额' AFTER `refund_amount`, 
;