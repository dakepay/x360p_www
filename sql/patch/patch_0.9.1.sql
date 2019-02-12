-- 通用的评论及回复系统
-- 
DROP TABLE IF EXISTS `x360p_comment`;
CREATE TABLE `x360p_comment` (
  `cmt_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `app_name` varchar(32) DEFAULT '' COMMENT '应用名称:比如针对备课的评论,那么为prepare,作业的评论,那么为homework',
  `app_content_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '应用内容的ID',
  `content` text COMMENT '评论内容',
  `parent_cmt_id` int(11) NOT NULL DEFAULT '0' COMMENT '父评论ID,用于对评论的回复',
  `up_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `score` int(11) DEFAULT '0' COMMENT '打分项目 -1 0 1 ', 
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID,当留言人的身份是员工时为0',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '员工ID,当留言人的身份是学生家长时为0',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`cmt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评论主表';

CREATE TABLE `x360p_comment_click` (
  `cc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `cmt_id` int(11) NOT NULL DEFAULT '0' COMMENT '评论id',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生sid',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '老师id',
  `click_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '点击类型：1点赞，2踩',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`cc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COMMENT='评论点赞记录';

-- 市场名单表
--
DROP TABLE IF EXISTS `x360p_market_channel`;
CREATE TABLE `x360p_market_channel` (
  `mc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID,可以为0',
  `channel_name` varchar(255) DEFAULT '' COMMENT '渠道名称(要排除重复)',
  `total_num` int(11) NOT NULL DEFAULT '0' COMMENT '总名单数量',
  `valid_num` int(11) NOT NULL DEFAULT '0' COMMENT '有效数量',
  `visit_num` int(11) NOT NULL DEFAULT '0' COMMENT '上门数量',
  `deal_num` int(11) NOT NULL DEFAULT '0' COMMENT '成交数量',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`mc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='市场渠道表';



-- 渠道Excel文件表
DROP TABLE IF EXISTS `x360p_market_channel_excel`;
CREATE TABLE `x360p_market_channel_excel` (
    `mce_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
    `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID,可以为0',
    `file_path` varchar(255) DEFAULT '' COMMENT '本地文件路径',
    `file_name` varchar(255) DEFAULT '' COMMENT '文件名',
    `excel_config` text COMMENT 'EXCEL的定义,JSON结构',
    `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
    `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`mce_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='市场渠道ExceL文件表';

-- 市场渠道名单表
DROP TABLE IF EXISTS `x360p_market_clue`;
CREATE TABLE `x360p_market_clue` (
  `mcl_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID,可以为0',
  `mc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '市场渠道ID',
  `mce_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '市场渠道Excel文件ID',
  `name` varchar(32) DEFAULT '' COMMENT '姓名',
  `tel` varchar(32) DEFAULT '' COMMENT '电话号码，可能是座机',
  `email` varchar(32) DEFAULT '' COMMENT '邮箱',
  `sex` tinyint(1) unsigned DEFAULT '0' COMMENT '性别0:待确认,1:男,2:女',
  `birth_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生时间戳',
  `birth_year` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生年',
  `birth_month` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生月',
  `birth_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生日',
  `school_grade` int(11) DEFAULT '0' COMMENT '年级:0未确认,-3~-1:幼儿园,1~12 小学1年级到高中3年级',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `is_valid` tinyint(1) unsigned DEFAULT '0' COMMENT '是否有效0:待确认,1:有效,2:无效',
  `is_deal` tinyint(1) unsigned DEFAULT '0' COMMENT '是否成交',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户名单ID,分配给咨询师以后',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID,成交以后',
  `assigned_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分配的员工ID,未分配为0',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`mcl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='市场机会表';

-- 
ALTER TABLE `x360p_class_attendance`
ADD COLUMN `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣除课时' AFTER `lesson_remark`,
ADD COLUMN `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别' AFTER `sj_id`
;

ALTER TABLE `x360p_student_attendance`
ADD COLUMN `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣除课时' AFTER `is_consume`,
ADD COLUMN `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别' AFTER `sj_id`
;

ALTER TABLE `x360p_employee_lesson_hour`
ADD COLUMN `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别' AFTER `sj_id`
;


ALTER TABLE `x360p_class`
ADD COLUMN `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别' AFTER `sj_id`,
ADD COLUMN `per_lesson_hour_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '每课时多少分钟' AFTER `unit_price`,
ADD COLUMN `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '每次扣多少课时' AFTER `per_lesson_hour_minutes`
;

ALTER TABLE `x360p_lesson`
ADD COLUMN `per_lesson_hour_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '每课时多少分钟' AFTER `sale_price`
;

ALTER TABLE `x360p_subject`
ADD COLUMN `per_lesson_hour_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '每课时多少分钟' AFTER `unit_price`
;

ALTER TABLE `x360p_subject_grade`
ADD COLUMN `per_lesson_hour_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '每课时多少分钟' AFTER `unit_price`
;

ALTER TABLE `x360p_course_arrange`
ADD COLUMN `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别' AFTER `sj_id`,
ADD COLUMN `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣多少课时' AFTER `int_end_hour`
;


ALTER TABLE `x360p_course_arrange_student`
ADD COLUMN `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID' AFTER `lid`,
ADD COLUMN `cu_id`  int NULL DEFAULT 0 COMMENT '客户id,主要是试听的' AFTER `sid`,
ADD COLUMN `cid`  int(11) NOT NULL DEFAULT 0 COMMENT '班级id' AFTER `cu_id`,
ADD COLUMN `is_trial`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否试听' AFTER `cid`,
ADD COLUMN `is_makeup`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否补课' AFTER `is_trial`,
ADD COLUMN `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣多少课时' AFTER `int_end_hour`
;



-- 允许负课时数存在
ALTER TABLE `x360p_student_lesson` 
MODIFY COLUMN `remain_lesson_hours` decimal(11, 2) NOT NULL DEFAULT 0.00 COMMENT '剩余课时数' AFTER `use_lesson_hours`,
ADD COLUMN `transfer_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT 0.00 COMMENT '结转总课时' AFTER `refund_lesson_hours`,
ADD COLUMN `remain_arrange_hours` decimal(11,2) NOT NULL DEFAULT -99999.00 COMMENT '剩余排课课时' AFTER `remain_arrange_times`
;


ALTER TABLE `x360p_student_absence`
ADD COLUMN `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别' AFTER `sj_id`
;


ALTER TABLE `x360p_makeup_arrange`
ADD COLUMN `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别' AFTER `sj_id`,
ADD COLUMN `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教ID' AFTER `eid`,
MODIFY COLUMN `makeup_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '补课类型:0跟班补课,1排班补课,2登记补课' AFTER `sg_id`;
;


ALTER TABLE `x360p_swiping_card_record` 
ADD COLUMN `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员课时记录ID' AFTER `card_no`,
MODIFY COLUMN `business_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '业务类型:0未匹配到,1上课考勤,2离校通知,3到校通知,4课程签到' AFTER `int_hour`
;

ALTER TABLE `x360p_order_item`
ADD COLUMN `start_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始日期' AFTER `present_lesson_hours`
;

-- 物品出入记录表，增加order_item表关联记录
ALTER TABLE `x360p_material_history`
ADD COLUMN `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单条目ID' AFTER `to_ms_id`
;
-- 修复课程级别
update `x360p_lesson` set `product_level_did`=0 where `product_level_did`=2;

ALTER TABLE `x360p_review_file`
ADD COLUMN `media_type`  char(50) NULL AFTER `duration`;



ALTER TABLE `x360p_student_leave`
ADD COLUMN `ma_id`  int(11) NOT NULL DEFAULT 0 COMMENT '补课id' AFTER `satt_id`;



