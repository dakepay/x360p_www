-- MySQL dump 10.13  Distrib 5.6.24, for Win64 (x86_64)
--
-- Host: 192.168.3.188    Database: x360p_base
-- ------------------------------------------------------
-- Server version	5.7.11-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `x360p_accounting_account`
--

DROP TABLE IF EXISTS `x360p_accounting_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_accounting_account` (
  `aa_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '机构账户ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '账户名称',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '账户类型(0:现金,1:银行存款,2:电子钱包(支付宝,微信支付等),3:应收款(债权),4:应付款(债务)',
  `bids` varchar(255) NOT NULL DEFAULT '' COMMENT '所属校区',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否公用账户(1是可以用于所有校区,0否)',
  `is_front` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否用于前台收款',
  `th_id` int(11) NOT NULL DEFAULT '0' COMMENT '往来客户id',
  `start_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '期初余额',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认账户',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`aa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会计账户表(每创建一个校区要自动创建一个关联的账户表)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_action_log`
--

DROP TABLE IF EXISTS `x360p_action_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_action_log` (
  `al_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '操作日志ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `uri` varchar(256) NOT NULL,
  `log_params` text NOT NULL COMMENT '日志参数(json格式)',
  `log_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '日志描述用户描述',
  `ip` varchar(32) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`al_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='系统操作日志表(记录系统操作日志)';
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_backlog`
--

DROP TABLE IF EXISTS `x360p_backlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_backlog` (
  `bl_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `desc` text NOT NULL COMMENT '描述',
  `int_day` int(11) DEFAULT NULL COMMENT '待办日期',
  `int_hour` int(11) DEFAULT NULL COMMENT '待办时间 1530',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '待办状态 1:待办， 2:完成，3:废弃',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`bl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='待办事项';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_branch`
--

DROP TABLE IF EXISTS `x360p_branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_branch` (
  `bid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '校区ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `branch_name` varchar(128) NOT NULL DEFAULT '' COMMENT '校区名称',
  `short_name` varchar(32) NOT NULL DEFAULT '' COMMENT '校区简称',
  `branch_type` enum('2','1') NOT NULL DEFAULT '1' COMMENT '校区类型(1:直营,2:加盟)',
  `branch_tel` varchar(32) NOT NULL DEFAULT '' COMMENT '校区电话',
  `big_area_id` smallint(2) NOT NULL DEFAULT '0' COMMENT '大区id:配置文件big_area',
  `province_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '省ID',
  `city_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '城市ID',
  `district_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '区域ID',
  `area_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '行政区ID',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `ms_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库id',
  `sort` int(11) unsigned DEFAULT '0' COMMENT '排序',
  `appid` varchar(255) NOT NULL DEFAULT '' COMMENT '校区默认公众号的appid',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `ext_id` varchar(20) DEFAULT NULL COMMENT 'dss3.0 校区id',
  PRIMARY KEY (`bid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='校区表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_branch_employee`
--

DROP TABLE IF EXISTS `x360p_branch_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_branch_employee` (
  `be_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL COMMENT '校区id',
  `eid` int(11) unsigned NOT NULL COMMENT '员工id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`be_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COMMENT='校区和员工表的中间表';
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_broadcast`
--

DROP TABLE IF EXISTS `x360p_broadcast`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_broadcast` (
  `bc_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型：1内部公告， 2外部公告',
  `is_global` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否全局公告（1是，0否）',
  `dpt_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '部门',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `desc` text NOT NULL COMMENT '描述',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`bc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_class`
--

DROP TABLE IF EXISTS `x360p_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_class` (
  `cid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `parent_cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '由哪个班级升级而来',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `class_name` varchar(255) NOT NULL DEFAULT '' COMMENT '班级名称',
  `class_no` varchar(32) NOT NULL DEFAULT '' COMMENT '班级编号',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `teach_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教学员工ID（老师ID)',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教ID',
  `edu_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教育员工ID(导师ID)',
  `cr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教室id(classroom)',
  `plan_student_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '计划招生人数',
  `student_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员人数',
  `nums_rate` decimal(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '满班率(现有学生数/计划招生数)*100',
  `lesson_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课次数，创建班级的时候填写',
  `lesson_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前上课进度次数(暂时不用管yr)',
  `arrange_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已排课次数',
  `attendance_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已考勤次数',
  `year` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年份',
  `season` enum('Q','S','C','H') NOT NULL,
  `start_lesson_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开课时间日期',
  `end_lesson_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结课时间日期',
  `status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '班级状态(0:待开课招生中,1:已开课,2:已结课)',
  `ext_id` varchar(32) NOT NULL DEFAULT '' COMMENT '对接外部系统的班级ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  PRIMARY KEY (`cid`),
  KEY `idx_extid` (`ext_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='班级表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_class_attendance`
--

DROP TABLE IF EXISTS `x360p_class_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_class_attendance` (
  `catt_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '班级考勤记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `chapter_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次序号(对应的课程课次)',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课记录ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教老师ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `class_student_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级人数',
  `need_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '应到人数',
  `in_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '实到人数',
  `leave_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '请假人数',
  `later_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '迟到人数',
  `makeup_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课人数',
  `trial_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '试听人数',
  `lesson_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '排课的考勤备注，由老师在登记考勤的时候填写',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`catt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='考勤记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_class_listen_apply`
--

DROP TABLE IF EXISTS `x360p_class_listen_apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_class_listen_apply` (
  `cla_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '听课申请ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级排课记录ID(对应qms_course_arrange表)',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '申请用户ID',
  `cli_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户id',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `apply_name` varchar(32) NOT NULL DEFAULT '' COMMENT '申请人姓名',
  `apply_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '申请时间',
  `is_approve` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否通过',
  `approve_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '通过时间',
  `reject_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '未通过原因备注',
  `is_arrive` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否到达',
  `is_review` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否点评',
  `review_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点评时间',
  `arrive_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '到达时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`cla_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='班级家长听课申请记录表(学生家长申请听课的记录)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_class_log`
--

DROP TABLE IF EXISTS `x360p_class_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_class_log` (
  `ct_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键（x360p_class_timeline）',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `cid` int(11) unsigned NOT NULL COMMENT '班级id(x360p_class)',
  `event_type` tinyint(1) unsigned NOT NULL COMMENT '事件类型，1：创建班级，2：编辑班级， 3：学生加入班级，4：学生退出班级，5：班级状态status更改，6：排课操作，7：考勤操作',
  `desc` varchar(255) DEFAULT NULL COMMENT '日志描述',
  `sid` int(11) unsigned DEFAULT NULL COMMENT '学生id(与班级学生操作相关）',
  `content` text COMMENT 'json数据',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`ct_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='班级日志';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_class_schedule`
--

DROP TABLE IF EXISTS `x360p_class_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_class_schedule` (
  `csd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '排班计划ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '教师id',
  `cr_id` int(11) NOT NULL DEFAULT '0' COMMENT '排班的教室，可能与班级表的教室id不一样',
  `year` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '年份',
  `season` enum('H','Q','S','C') DEFAULT NULL COMMENT '季度',
  `week_day` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '星期（1-7）',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`csd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='排班计划表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_class_student`
--

DROP TABLE IF EXISTS `x360p_class_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_class_student` (
  `cs_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'student_lesson表主键',
  `in_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加入班级时间',
  `out_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出班日期',
  `in_way` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:order_item订单类型，2：分班操作，3：dss导入',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1正常,0停课,2转出',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`cs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='班级学生表（记录每个班级里面有哪些学生)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_classroom`
--

DROP TABLE IF EXISTS `x360p_classroom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_classroom` (
  `cr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '教室ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `room_name` varchar(32) NOT NULL DEFAULT '' COMMENT '教室名',
  `seat_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '座位数',
  `max_seat_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最多容纳人数',
  `listen_nums_limit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '听课最多允许人数',
  `seat_config` text COMMENT '座位号配置(json格式)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`cr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='教室表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_config`
--

DROP TABLE IF EXISTS `x360p_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_config` (
  `cfg_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `cfg_name` varchar(32) NOT NULL DEFAULT '' COMMENT '配置名称',
  `cfg_value` text NOT NULL COMMENT '配置值',
  `format` enum('int','string','json') NOT NULL DEFAULT 'string',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`cfg_id`),
  UNIQUE KEY `idx_cfg_name` (`cfg_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表(KV结构)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_coupon`
--

DROP TABLE IF EXISTS `x360p_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_coupon` (
  `coupon_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '优惠券名称',
  `promotion_type` tinyint(1) unsigned NOT NULL COMMENT '优惠券促销类型1.金额抵扣，2.打折券，3.送课次，4.送课时',
  `deduction` decimal(11,2) unsigned DEFAULT NULL COMMENT '减除额',
  `discount_rate` tinyint(2) unsigned DEFAULT NULL COMMENT '折扣率',
  `present_lesson_times` int(11) unsigned DEFAULT NULL COMMENT '赠送课次',
  `present_lesson_hours` decimal(11,2) unsigned DEFAULT NULL COMMENT '赠送课时数',
  `branch_scope` varchar(255) DEFAULT NULL COMMENT '促销校区范围',
  `lesson_scope` varchar(255) DEFAULT NULL COMMENT '促销课程范围',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态 1：有效，0：失效',
  `start_time` int(11) unsigned DEFAULT NULL COMMENT '有效期开始日期',
  `end_time` int(11) unsigned DEFAULT NULL COMMENT '有效期结束日期',
  `number` int(11) unsigned NOT NULL COMMENT '发行数量',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券类型';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_coupon_code`
--

DROP TABLE IF EXISTS `x360p_coupon_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_coupon_code` (
  `cd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `coupon_id` int(11) unsigned NOT NULL COMMENT '优惠券类型id',
  `coupon_code` varchar(255) NOT NULL,
  `used_time` int(11) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态(是否使用) 0：未使用，1：已使用',
  `o_id` int(11) unsigned DEFAULT NULL COMMENT '订单ID(x360p_order表主键）',
  `oi_id` int(11) unsigned DEFAULT NULL COMMENT 'x360p_order_item表主键',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`cd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券表发行记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_course_arrange`
--

DROP TABLE IF EXISTS `x360p_course_arrange`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_course_arrange` (
  `ca_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '排课ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0为班课,1为1对1课,2为1对多课',
  `is_trial` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否试听排班,1为是,name不能为空',
  `is_makeup` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开班补课的排课',
  `name` varchar(128) DEFAULT '' COMMENT '排课名称,为试听排班的时候必须取名，为试听班名字',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `teach_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教id',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `cr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教室ID',
  `chapter_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次序号',
  `season` enum('H','Q','S','C') NOT NULL,
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `is_attendance` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否考勤了（状态，0：未考勤， 1： 部分考勤，2：全部考勤）',
  `is_prepare` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '备课进度：0：没有备课，1：部分备课，2：完全备课',
  `prepare_file_nums` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '备课附件数目',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `listen_apply_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '听课申请人数',
  `listen_approve_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '听课实际允许人数',
  `listen_arrive_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '听课到达人数',
  `prepare_message` varchar(255) DEFAULT '' COMMENT '课前推送内容',
  `is_before_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否课前推送',
  `is_after_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否课后推送',
  `before_push_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课前推送时间',
  `after_push_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课后推送时间',
  `is_cancel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否取消本次课程 0：正常，1：该课程已被取消',
  `reason` varchar(255) DEFAULT NULL COMMENT '取消排课或修改排课日期的原因',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ca_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='班级排课记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_course_arrange_student`
--

DROP TABLE IF EXISTS `x360p_course_arrange_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_course_arrange_student` (
  `cas_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '排课学生ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `is_attendance` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否考勤（状态，0：未上课，1：已上课）',
  `satt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生的考勤记录id（student_attendance）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`cas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='排课学生列表(适用于1对1或1对多的排课)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_credit_rule`
--

DROP TABLE IF EXISTS `x360p_credit_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_credit_rule` (
  `cr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `rule_name` varchar(255) DEFAULT '' COMMENT '规则名称',
  `hook_action` varchar(32) DEFAULT '' COMMENT '钩子名称(attendance_ok,leave_ok,homework_submit等)',
  `op` char(1) NOT NULL DEFAULT '+' COMMENT '操作+/-',
  `credit` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '积分数',
  `rule` text COMMENT '规则定义(json结构)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除UID',
  PRIMARY KEY (`cr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员积分变动记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_customer`
--

DROP TABLE IF EXISTS `x360p_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_customer` (
  `cu_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '客户ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '学员姓名',
  `pinyin` varchar(50) NOT NULL DEFAULT '' COMMENT '学员姓名name的全拼',
  `pinyin_abbr` varchar(50) NOT NULL DEFAULT '' COMMENT '学员姓名name拼音的首字符缩写',
  `nick_name` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称/英文名',
  `sex` enum('2','1','0') NOT NULL DEFAULT '0' COMMENT '性别(0:未确定,1:男,2:女)',
  `birth_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生时间戳',
  `birth_year` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生年',
  `birth_month` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生月',
  `birth_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生日',
  `school_grade` int(11) NOT NULL DEFAULT '0' COMMENT '学校年级',
  `school_class` varchar(32) NOT NULL DEFAULT '' COMMENT '学校班级',
  `school_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学校ID',
  `first_tel` varchar(16) NOT NULL DEFAULT '',
  `first_family_name` varchar(32) NOT NULL DEFAULT '' COMMENT '第一亲属姓名',
  `first_family_rel` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0:未设置,1:自己，2：爸爸，3：妈妈，4：其他',
  `second_family_name` varchar(32) NOT NULL DEFAULT '' COMMENT '第2亲属姓名',
  `second_family_rel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0：未设置，1:自己，2：爸爸，3：妈妈，4：其他',
  `second_tel` varchar(16) NOT NULL DEFAULT '' COMMENT '第2电话',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `from_did` int(11) NOT NULL DEFAULT '0' COMMENT '招生来源(招生来源字典ID)',
  `intention_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '意向程度1-5',
  `customer_status_did` int(11) NOT NULL DEFAULT '0' COMMENT '跟进状态(跟进状态字典ID)',
  `is_reg` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否报读',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学员ID(报读之后的学员ID)',
  `signup_int_day` int(11) DEFAULT NULL COMMENT '报名日期',
  `signup_amount` decimal(11,2) DEFAULT '0.00' COMMENT '报名金额',
  `referer_sid` int(11) NOT NULL DEFAULT '0' COMMENT '介绍人,学员ID',
  `follow_eid` int(11) NOT NULL DEFAULT '0' COMMENT '主要跟进人',
  `follow_times` int(11) NOT NULL DEFAULT '0' COMMENT '跟进次数',
  `last_follow_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后跟进时间',
  `next_follow_time` int(11) NOT NULL DEFAULT '0' COMMENT '下次跟进时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`cu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户表(市场招生)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_customer_employee`
--

DROP TABLE IF EXISTS `x360p_customer_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_customer_employee` (
  `ce_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '员工ID',
  `sale_role_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销售角色字典ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`ce_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户销售辅助跟进角色表';
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `x360p_customer_follow_up`
--

DROP TABLE IF EXISTS `x360p_customer_follow_up`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_customer_follow_up` (
  `cfu_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `is_connect` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效沟通',
  `followup_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '跟进方式字典ID(QQ,电话,微信)',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '跟进内容',
  `is_promise` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否诺到',
  `promise_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '诺到类型字典ID',
  `promise_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '诺到日期',
  `is_visit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否到访',
  `visit_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '实际到访日期',
  `next_follow_time` int(11) NOT NULL DEFAULT '0' COMMENT '下次跟进日期,0为待定',
  `intetion_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '意向级别',
  `customer_status_did` tinyint(1) NOT NULL DEFAULT '0' COMMENT '客户状态字典ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '跟进员工ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`cfu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户跟进记录表';
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `x360p_customer_intention`
--

DROP TABLE IF EXISTS `x360p_customer_intention`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_customer_intention` (
  `ci_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '意向课程ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '意向老师ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`ci_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户意向表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_department`
--

DROP TABLE IF EXISTS `x360p_department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_department` (
  `dpt_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级部门ID',
  `dpt_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '部门类型:(0,部门,1:校区,2:大区)',
  `dpt_name` varchar(255) DEFAULT '' COMMENT '部门名称',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID(如果类型是校区，则绑定校区ID)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除UID',
  PRIMARY KEY (`dpt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='部门表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_dictionary`
--

DROP TABLE IF EXISTS `x360p_dictionary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_dictionary` (
  `did` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` char(64) DEFAULT '' COMMENT '名称',
  `title` varchar(32) DEFAULT NULL COMMENT '标题(用于PID为0的字典名)',
  `desc` varchar(255) DEFAULT '' COMMENT '描述',
  `is_system` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否系统默认',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`did`)
) ENGINE=InnoDB AUTO_INCREMENT=1005 DEFAULT CHARSET=utf8mb4 COMMENT='字典表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_email_history`
--

DROP TABLE IF EXISTS `x360p_email_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_email_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `email` varchar(32) NOT NULL DEFAULT '' COMMENT '手机号',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '短信内容',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送成功:0成功，其余失败',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_email_history`
--

LOCK TABLES `x360p_email_history` WRITE;
/*!40000 ALTER TABLE `x360p_email_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_email_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_email_vcode`
--

DROP TABLE IF EXISTS `x360p_email_vcode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_email_vcode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `email` varchar(32) DEFAULT NULL COMMENT '邮箱地址',
  `type` varchar(32) DEFAULT NULL COMMENT '验证码类型标记',
  `code` varchar(16) DEFAULT NULL COMMENT '验证码内容',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `expire_time` int(11) unsigned DEFAULT '0' COMMENT '过期时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email_type` (`email`,`type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_email_vcode`
--

LOCK TABLES `x360p_email_vcode` WRITE;
/*!40000 ALTER TABLE `x360p_email_vcode` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_email_vcode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_employee`
--

DROP TABLE IF EXISTS `x360p_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_employee` (
  `eid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '员工ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `ename` varchar(32) NOT NULL DEFAULT '' COMMENT '员工姓名',
  `pinyin` varchar(50) NOT NULL DEFAULT '' COMMENT '员工姓名ename的全拼',
  `pinyin_abbr` varchar(50) NOT NULL DEFAULT '' COMMENT '员工姓名ename拼音的首字符缩写',
  `nick_name` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称',
  `rids` varchar(255) NOT NULL DEFAULT '' COMMENT '角色ID,逗号分隔',
  `bids` varchar(255) NOT NULL DEFAULT '' COMMENT '校区ID，逗号分隔',
  `lids` varchar(255) NOT NULL DEFAULT '' COMMENT '老师教授的课程id，多个id用逗号分隔',
  `sj_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '科目ID,逗号分隔',
  `grades` varchar(255) NOT NULL DEFAULT '' COMMENT '年级ID(数字用逗号分隔)',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `account` varchar(64) NOT NULL DEFAULT '' COMMENT '账号',
  `sex` enum('2','1','0') NOT NULL DEFAULT '0' COMMENT '性别(0:未确定,1:男,2:女)',
  `mobile` varchar(16) NOT NULL COMMENT '手机号码',
  `email` varchar(64) NOT NULL DEFAULT '' COMMENT 'Email地址',
  `id_card_no` varchar(20) NOT NULL DEFAULT '' COMMENT '身份证号',
  `bank_card_no` varchar(20) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `photo_url` varchar(255) NOT NULL DEFAULT '' COMMENT ' 图像地址',
  `birth_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生时间戳',
  `birth_year` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生年',
  `birth_month` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生月份',
  `birth_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生日',
  `is_part_job` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否兼职(1为是,0为否)',
  `is_on_job` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否在职(1:在职,0:离职)',
  `join_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '入职日期',
  `official_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转正日期',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `user_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户账号状态(0为禁用,1为启用)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  `ext_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Dss对应的employeeId',
  PRIMARY KEY (`eid`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='员工表';
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `x360p_employee_dept`
--

DROP TABLE IF EXISTS `x360p_employee_dept`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_employee_dept` (
  `ed_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '员工部门ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '员工ID',
  `dpt_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '部门类型:(0,部门,1:校区,2:大区)',
  `dpt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '部门ID',
  `jobtitle_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '职能字典ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID(如果类型是校区，则需要校区ID)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除UID',
  PRIMARY KEY (`ed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工部门职能表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_employee_dimission`
--

DROP TABLE IF EXISTS `x360p_employee_dimission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_employee_dimission` (
  `eds_id` int(11) NOT NULL AUTO_INCREMENT,
  `eid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '离职原因：1主动离职，2辞退',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `int_day` int(11) DEFAULT NULL COMMENT '离职日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`eds_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工离职记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_employee_lesson_hour`
--

DROP TABLE IF EXISTS `x360p_employee_lesson_hour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_employee_lesson_hour` (
  `elh_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '课时消耗ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教老师ID',
  `lesson_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多,3:研学旅行团)',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生ID(1对1课有效)',
  `sids` varchar(255) DEFAULT '' COMMENT '学生ID(1对多课有效)',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课记录ID',
  `satt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生考勤记录ID(1对1课有效)',
  `catt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级考勤记录ID(班课考勤ID)',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `student_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课学生数，出勤的',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课时数',
  `lesson_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次时间长度（单位：分钟）',
  `total_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总计课时数',
  `total_lesson_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总计课时金额',
  `payed_lesson_amount` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '付款课时金额',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`elh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='教师课时产出记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_employee_profile`
--

DROP TABLE IF EXISTS `x360p_employee_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_employee_profile` (
  `ep_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'employee profile',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `eid` int(11) unsigned NOT NULL COMMENT '员工id',
  `introduce` text COMMENT '介绍',
  `sign` text COMMENT '个人签名',
  `recommend_text` mediumtext,
  `background_img` varchar(255) DEFAULT NULL COMMENT '员工pc端个人中心设置背景图，用于老师导师个人介绍页面',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`ep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `x360p_employee_receipt`
--

DROP TABLE IF EXISTS `x360p_employee_receipt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_employee_receipt` (
  `erc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '员工回款记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `eid` int(11) unsigned NOT NULL COMMENT '员工ID',
  `orb_id` int(11) unsigned NOT NULL COMMENT '收据ID',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '回款金额',
  `receipt_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回款日期,默认与create_time相同',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`erc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工回款记录表(用于计算业绩提成)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_employee_role`
--

DROP TABLE IF EXISTS `x360p_employee_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_employee_role` (
  `er_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户角色ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户UID',
  `rid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_uid` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`er_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户所属角色表(每一个用户可以拥有0个或多个用户角色)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_employee_subject`
--

DROP TABLE IF EXISTS `x360p_employee_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_employee_subject` (
  `es_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '员工科目ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID(兼容字段，不用理会)',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '员工ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除UID',
  PRIMARY KEY (`es_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工部门职能表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_file`
--

DROP TABLE IF EXISTS `x360p_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_file` (
  `file_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `mod` varchar(32) NOT NULL DEFAULT '' COMMENT '模块',
  `storage` varchar(16) NOT NULL DEFAULT '' COMMENT '存储引擎(local,qiniu)',
  `rel_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联ID',
  `local_file` varchar(255) NOT NULL DEFAULT '' COMMENT '本地存储路径',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件存储路径',
  `file_type` varchar(16) NOT NULL DEFAULT '' COMMENT '文件类型',
  `file_name` varchar(128) NOT NULL DEFAULT '',
  `file_size` bigint(20) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `duration` varchar(25) NOT NULL DEFAULT '' COMMENT '当文件为mp3时该字段不为空。',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `openid` varchar(225) NOT NULL DEFAULT '' COMMENT '用户openid',
  `media_type` char(20) NOT NULL DEFAULT '' COMMENT '媒体文件类型（微信回调消息类型）',
  `media_id` varchar(255) NOT NULL DEFAULT '' COMMENT '微信的media_id',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统文件表(所有上传的附件文件，都会记录下来，有一个唯一的file_id)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_holiday`
--

DROP TABLE IF EXISTS `x360p_holiday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_holiday` (
  `hid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) NOT NULL COMMENT '校区id',
  `name` varchar(255) NOT NULL COMMENT '假期名称',
  `int_day` int(11) NOT NULL,
  `year` int(4) unsigned NOT NULL COMMENT '年份',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`hid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_input_template`
--

DROP TABLE IF EXISTS `x360p_input_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_input_template` (
  `it_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型：1报名， 2记账',
  `cate` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类别：如收入、支出、转账、应收、应付',
  `name` varchar(255) DEFAULT NULL COMMENT '模板名称',
  `template` text COMMENT '模板数据',
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`it_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='表单数据模板';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_lesson`
--

DROP TABLE IF EXISTS `x360p_lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_lesson` (
  `lid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bids` varchar(255) NOT NULL COMMENT '校区ID，逗号分隔',
  `year` smallint(4) NOT NULL DEFAULT '0' COMMENT '年份',
  `season` enum('C','S','Q','H') NOT NULL COMMENT '学期季节',
  `sj_id` int(11) NOT NULL DEFAULT '0' COMMENT '科目id',
  `sj_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '科目ID（课时包有效)',
  `lesson_name` varchar(255) NOT NULL DEFAULT '' COMMENT '课程名称',
  `lesson_no` varchar(16) NOT NULL DEFAULT '' COMMENT '课程编号',
  `product_level_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '产品等级字典ID',
  `fit_age_start` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '适合年龄段开始',
  `fit_age_end` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '适合年龄结束',
  `fit_grade_start` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '适合年级开始',
  `fit_grade_end` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '适合年级结束',
  `short_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '简短介绍',
  `public_content` text NOT NULL COMMENT '宣传介绍(HTML文本)',
  `lesson_cover_picture` varchar(255) NOT NULL DEFAULT '' COMMENT '课程封面图片路径',
  `chapter_nums` smallint(3) unsigned NOT NULL DEFAULT '1' COMMENT '章节数量',
  `lesson_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多,3:研学旅行团)',
  `is_multi_class` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否分班上课,班课有效,如果是则报名后需要分多个班',
  `ac_class_nums` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'assign class需要分班数量(is_multi为1时有效)',
  `price_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '收费模式（1:按课次计费,2:课时收费,3:按时间收费）',
  `is_term` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否按期收费',
  `lesson_nums` decimal(11,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '课次数(price_type为1时单位为课次，为2是单位为课时，为3时为月)',
  `unit_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单价，跟随price_type',
  `unit_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单次课扣多少课时',
  `unit_lesson_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '单次课时长(单位分钟)',
  `sale_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课程售价',
  `ext_lid` varchar(32) NOT NULL DEFAULT '' COMMENT '外部课程ID(对接浪腾系统)',
  `version` varchar(16) NOT NULL DEFAULT '' COMMENT '版本号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1为启用,0为禁用)',
  `is_package` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否课时包',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否缺省课程',
  `is_publish` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已经发布',
  `is_standard` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是标准课程，0：不是，1：是',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`lid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课程表(关键的课程主表,记录课程的基本信息)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_lesson_attachment`
--

DROP TABLE IF EXISTS `x360p_lesson_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_lesson_attachment` (
  `la_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程附件ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `lc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程章节ID(第几讲)',
  `chapter_index` int(11) NOT NULL DEFAULT '-1' COMMENT '章节序号',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `is_lesson_std` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否课标',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '附件标题',
  `name` varchar(255) DEFAULT NULL COMMENT '非课程标准的附件上传（la_type=0）需要同时提交一个附件的name字段值',
  `la_type` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程附件类型(简案、详案、课件、学案、教程、教案、说课、示范课)',
  `file_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件ID',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `file_size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小 (字节数)',
  `file_ext` char(8) NOT NULL DEFAULT '' COMMENT '文件拓展名(类型)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) NOT NULL,
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`la_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课程附件表(课程关联的附件文件)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_lesson_chapter`
--

DROP TABLE IF EXISTS `x360p_lesson_chapter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_lesson_chapter` (
  `lc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程章节ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `chapter_index` int(11) NOT NULL DEFAULT '-1' COMMENT '章节索引',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '章节标题',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课程时长',
  `has_homework` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有作业,0:无，1：有，默认有作业',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '11' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`lc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课程内容章节表(每一个课程对应多个内容章节)';
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_lesson_material`
--

DROP TABLE IF EXISTS `x360p_lesson_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_lesson_material` (
  `lm_id` int(11) NOT NULL AUTO_INCREMENT,
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '课程ID',
  `mt_id` int(11) NOT NULL DEFAULT '0' COMMENT '物品ID',
  `default_num` int(11) NOT NULL DEFAULT '0' COMMENT '默认购买数量',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`lm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课程相关物品';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_makeup_arrange`
--

DROP TABLE IF EXISTS `x360p_makeup_arrange`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_makeup_arrange` (
  `ma_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '补课安排ID,自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `sa_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '缺课记录ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课课程ID',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'student_lesson表主键',
  `slv_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '请假记录ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课科目ID',
  `makeup_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '补课类型:0跟班补课,1排班补课',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '跟班补课班级ID(如果是排班补课则cid为0)',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课ID,course_arrange表关联',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课日期',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课开始上课时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课结束上课时间',
  `is_attendance` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否考勤，与attendance_status结合使用',
  `attendance_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '出勤状态 0：缺勤，1：出勤。与is_attendance结合使用',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`ma_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='补课安排记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_material`
--

DROP TABLE IF EXISTS `x360p_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_material` (
  `mt_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `name` varchar(255) NOT NULL COMMENT '物品名称',
  `unit` char(4) NOT NULL COMMENT '计量单位',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `image` varchar(255) NOT NULL COMMENT '图片',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `purchase_price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '进货价',
  `sale_price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '销售价',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`mt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='物品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_material_history`
--

DROP TABLE IF EXISTS `x360p_material_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_material_history` (
  `mh_id` int(11) NOT NULL AUTO_INCREMENT,
  `mt_id` int(11) NOT NULL DEFAULT '0' COMMENT '物品ID( material )',
  `ms_id` int(11) NOT NULL DEFAULT '0' COMMENT '仓库ID ( material_store )',
  `to_ms_id` int(11) NOT NULL DEFAULT '0' COMMENT '调拔到的仓库',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '变化数量',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '经手人( employee )',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '类型：1进库、2出库',
  `cate` tinyint(4) NOT NULL DEFAULT '0' COMMENT '分类：1进货、2领用、3调拔、4报损、5报名下单',
  `int_day` int(11) NOT NULL DEFAULT '0' COMMENT '操作日期',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`mh_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='物品出入记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_material_store`
--

DROP TABLE IF EXISTS `x360p_material_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_material_store` (
  `ms_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '仓库类型：1分仓，2总仓',
  `name` varchar(255) NOT NULL COMMENT '仓库名',
  `desc` varchar(255) NOT NULL COMMENT '仓库描述',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ms_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='仓库表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_material_store_qty`
--

DROP TABLE IF EXISTS `x360p_material_store_qty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_material_store_qty` (
  `msq_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `ms_id` int(11) NOT NULL COMMENT '仓库id',
  `mt_id` int(11) NOT NULL DEFAULT '0' COMMENT '物品id',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`msq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='仓库物品库存表';
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_message`
--

DROP TABLE IF EXISTS `x360p_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加盟商id',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区id',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '班级id',
  `business_type` varchar(255) NOT NULL COMMENT '业务类型',
  `business_id` int(11) unsigned NOT NULL COMMENT '业务ID',
  `send_mode` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '消息发送渠道：0：站内信，1：微信，2：短信，4：微信+短信',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '消息内容',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户消息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_news`
--

DROP TABLE IF EXISTS `x360p_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_news` (
  `nid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `title` varchar(255) NOT NULL COMMENT '新闻标题',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型:0为内部新闻，1为外部新闻',
  `scope` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '可见范围，0为全局所有校区可见,1为本校区可见(bid有效),2为多校区可见(bids有效)',
  `bids` varchar(255) DEFAULT '' COMMENT '多校区ID',
  `views` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '阅读次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`nid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告通知主表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_news_content`
--

DROP TABLE IF EXISTS `x360p_news_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_news_content` (
  `nc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `nid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新闻ID',
  `ob_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `bid` int(11) NOT NULL COMMENT '新闻ID',
  `content` text NOT NULL,
  PRIMARY KEY (`nc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告通知内容表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_news_view`
--

DROP TABLE IF EXISTS `x360p_news_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_news_view` (
  `nv_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `nid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新闻ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '时间',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '账号ID',
  `visitor_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '访问者类型:0为内部，1为外部',
  PRIMARY KEY (`nv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='公告通知浏览记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_order`
--

DROP TABLE IF EXISTS `x360p_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order` (
  `oid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID(在线报名用户ID)',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区id',
  `order_from` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单来源类型(0:线下,1:线上)',
  `order_no` varchar(32) NOT NULL,
  `origin_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原金额',
  `order_discount_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '折扣金额',
  `order_reduced_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '直减金额',
  `order_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `order_status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态(0:已下单,1:已支付,2:已分班,10:已申请退款,11:已退款)',
  `paid_time` int(11) unsigned DEFAULT NULL COMMENT '支付时间',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '付款状态(0:未付款,1:部分付款,2:全部付款)',
  `balance_paid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '电子钱包余额付款金额(电子钱包冲减金额)',
  `money_pay_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '现金应付款金额(除电子钱包付款以外的)',
  `money_paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '现金已付金额（除了电子钱包）',
  `paid_amount` decimal(11,2) unsigned DEFAULT '0.00' COMMENT '已付金额',
  `unpaid_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '未付款金额',
  `is_submit` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否暂存订单, 0暂存，1提交',
  `ac_status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '分班状态(assign class status,0:未分班,1:部分分班,2:已分班)',
  `refund_status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态(0:未退款,1:退款中,2:已退款)',
  `bill_status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发票状态(0:未申请，1:已申请,2:已开)',
  `invoice_no` varchar(32) DEFAULT '' COMMENT '发票编号',
  `remark` varchar(255) DEFAULT '' COMMENT '订单备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`oid`),
  UNIQUE KEY `idx_order_no` (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单记录表(学员报名、选课之后会产生订单记录)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_order_cut_amount`
--

DROP TABLE IF EXISTS `x360p_order_cut_amount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_cut_amount` (
  `oca_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1:为结转,2:为退款',
  `or_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '退费ID',
  `ot_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结转ID',
  `cutamount_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '字典ID',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣款金额',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`oca_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='扣费记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_order_item`
--

DROP TABLE IF EXISTS `x360p_order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_item` (
  `oi_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单商品ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区id',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `gid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID(线下订单gid为0)',
  `is_deliver` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否发货，针对物品',
  `gtype` tinyint(1) unsigned DEFAULT '0' COMMENT '商品类型 0：课程，1：物品',
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '课程id, 主要是暂存订单、付款为0时使用',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '班级id, 主要是暂存订单、付款为0时使用',
  `sl_id` int(11) NOT NULL DEFAULT '0' COMMENT '学生课程id(student_lesson表)',
  `nums` decimal(11,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '商品数量',
  `nums_unit` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '数量单位(0为物品的数量单位,1为课次,2为课时,3为月按时间',
  `origin_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原始单价',
  `price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '折后单价（成交单价）',
  `pr_id` int(11) unsigned DEFAULT '0' COMMENT '促销规则id',
  `origin_amount` decimal(11,2) unsigned DEFAULT '0.00' COMMENT '原始金额',
  `subtotal` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '小计金额',
  `paid_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '已付款',
  `discount_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '折扣金额',
  `reduced_amount` decimal(11,2) DEFAULT '0.00' COMMENT '分摊优惠减少的金额',
  `origin_lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原始课次数',
  `present_lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送课次数',
  `origin_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原始购买的的总课时数（lesson表：lesson_chapter * unit_hours）',
  `present_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送的课时数',
  `expire_time` int(11) DEFAULT NULL COMMENT '有效期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`oi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单项目表(每一个订单对应1到多个订单项目记录)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_order_payment_history`
--

DROP TABLE IF EXISTS `x360p_order_payment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_payment_history` (
  `oph_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单付款记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `orb_id` int(11) NOT NULL DEFAULT '0' COMMENT '收据id',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID,',
  `aa_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会计账号ID',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '收款金额',
  `paid_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '付款时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL,
  `delete_uid` int(11) unsigned DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`oph_id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COMMENT='订单付款记录ID';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_order_performance`
--

DROP TABLE IF EXISTS `x360p_order_performance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_performance` (
  `op_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单业绩ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `eid` int(11) NOT NULL,
  `sale_role_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销售角色ID',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '业绩金额',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`op_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单销售业绩表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_order_receipt_bill`
--

DROP TABLE IF EXISTS `x360p_order_receipt_bill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_receipt_bill` (
  `orb_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '收据ID自增',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `orb_no` varchar(32) DEFAULT '' COMMENT '收据编号',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '收款金额',
  `balance_paid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额付款金额(电子钱包抵扣金额)',
  `money_paid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '现金付款金额(收款金额-余额付款金额)',
  `unpaid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '欠缴金额',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`orb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单收据表主表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_order_receipt_bill_item`
--

DROP TABLE IF EXISTS `x360p_order_receipt_bill_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_receipt_bill_item` (
  `orbi_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '收据条目ID自增',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `orb_id` int(11) NOT NULL DEFAULT '0' COMMENT '收据ID',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单条目ID',
  `paid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '收款金额',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`orbi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COMMENT='订单收据条目表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_order_receipt_bill_print_history`
--

DROP TABLE IF EXISTS `x360p_order_receipt_bill_print_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_receipt_bill_print_history` (
  `orbph_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '打印记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `orb_id` int(11) unsigned NOT NULL COMMENT '收据ID',
  `nums` int(11) unsigned NOT NULL COMMENT '打印份数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`orbph_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单收据打印记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_order_refund`
--

DROP TABLE IF EXISTS `x360p_order_refund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_refund` (
  `or_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '退费ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `bill_no` varchar(32) NOT NULL DEFAULT '' COMMENT '收据编号',
  `need_refund_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '应该退费金额',
  `refund_balance_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退电子钱包金额',
  `cut_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣款金额',
  `refund_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际退费金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除UID',
  PRIMARY KEY (`or_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单结转记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_order_refund_history`
--

DROP TABLE IF EXISTS `x360p_order_refund_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_refund_history` (
  `orh_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单退款记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `or_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '退款ID',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `aa_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会计账号ID',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '退款时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL,
  `delete_uid` int(11) unsigned DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`orh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单付款记录ID';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_order_refund_item`
--

DROP TABLE IF EXISTS `x360p_order_refund_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_refund_item` (
  `ori_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '退费项目ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `or_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '退款ID',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单项目ID',
  `nums` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退费数量',
  `unit_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退费单价',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退费金额',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ori_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单退费记录项目表';
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `x360p_order_transfer`
--

DROP TABLE IF EXISTS `x360p_order_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_transfer` (
  `ot_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '结转ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `bill_no` varchar(32) NOT NULL DEFAULT '' COMMENT '收据编号',
  `transfer_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结转金额(包含扣款部分）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除UID',
  PRIMARY KEY (`ot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单结转记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_order_transfer_item`
--

DROP TABLE IF EXISTS `x360p_order_transfer_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_transfer_item` (
  `oti_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '结转项目ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `ot_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结转ID',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单项目ID',
  `nums` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结转数量',
  `unit_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结转单价',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结转金额',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`oti_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='结转项目表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_org`
--

DROP TABLE IF EXISTS `x360p_org`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_org` (
  `og_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '机构ID',
  `parent_og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级机构ID(属于哪个机构)',
  `org_name` varchar(255) NOT NULL DEFAULT '' COMMENT '机构名称',
  `org_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0非加盟商， 1加盟商',
  `mobile` char(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `org_short_name` varchar(64) NOT NULL DEFAULT '' COMMENT '机构简称',
  `province_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '省ID',
  `city_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '城市ID',
  `district_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '区域ID',
  `org_address` varchar(255) NOT NULL DEFAULT '' COMMENT '机构地址',
  `expire_day` int(11) NOT NULL COMMENT '到期日期',
  `account_num_limit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '账号数限制，0为不限制',
  `branch_num_limit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区数限制',
  `student_num_limit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员数量限制',
  `is_frozen` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否冻结账户',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`og_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机构表（加盟商）';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_org_renew_log`
--

DROP TABLE IF EXISTS `x360p_org_renew_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_org_renew_log` (
  `orl_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '加盟商id',
  `pre_day` int(11) NOT NULL DEFAULT '0' COMMENT '延期前的过期时间',
  `new_day` int(11) NOT NULL DEFAULT '0' COMMENT '延期后的过期时间',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`orl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='延期记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_payment_log`
--

DROP TABLE IF EXISTS `x360p_payment_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_payment_log` (
  `pl_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `oid` int(11) unsigned NOT NULL COMMENT '订单id',
  `appid` varchar(255) DEFAULT NULL,
  `mch_id` varchar(255) DEFAULT NULL,
  `bank_type` varchar(255) DEFAULT NULL,
  `cash_fee` int(11) DEFAULT NULL,
  `total_fee` int(11) DEFAULT NULL,
  `fee_type` varchar(255) DEFAULT NULL,
  `is_subscribe` enum('Y','N') DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `out_trade_no` varchar(255) DEFAULT NULL,
  `result_code` varchar(50) DEFAULT NULL,
  `return_code` varchar(50) DEFAULT NULL,
  `trade_type` varchar(20) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`pl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付日志表(用于在线支付接口的日志记录)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_print_tpl`
--

DROP TABLE IF EXISTS `x360p_print_tpl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_print_tpl` (
  `pt_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `bill_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '单据类型(1:缴费收据,2:退费收据)',
  `format` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '格式(1:A4打白单,2:小票打印,3:套打)',
  `json` text COMMENT '配置json格式',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认(每种bill_type,只有1个is_default为1)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`pt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_promotion_rule`
--

DROP TABLE IF EXISTS `x360p_promotion_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_promotion_rule` (
  `pr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '促销名称',
  `promotion_type` tinyint(1) unsigned NOT NULL COMMENT '促销类型 1:打折,2:满减,3:直减,4:送课时',
  `limit` int(11) unsigned DEFAULT NULL COMMENT '限度，eg: 满3000减200, limit=3000',
  `discount_rate` tinyint(2) unsigned DEFAULT NULL COMMENT '折扣率',
  `money_off` decimal(11,2) unsigned DEFAULT NULL COMMENT '满减金额',
  `direct_money_off` decimal(11,2) unsigned DEFAULT NULL COMMENT '直减金额',
  `present_lesson_hours` decimal(11,2) unsigned DEFAULT NULL COMMENT '赠送课时数',
  `branch_scope` varchar(255) DEFAULT NULL COMMENT '促销范围(校区)',
  `lesson_scope` varchar(255) DEFAULT NULL COMMENT '促销范围(课程)',
  `start_time` int(11) unsigned DEFAULT NULL COMMENT '促销开始时间',
  `end_time` int(11) unsigned DEFAULT NULL COMMENT '促销结束时间',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态 0:失效，1：正常',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`pr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='促销规则表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_public_school`
--

DROP TABLE IF EXISTS `x360p_public_school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_public_school` (
  `ps_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `school_name` varchar(255) NOT NULL COMMENT '学习名称',
  `province_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '省ID',
  `city_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '城市ID',
  `district_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '区域ID',
  `area_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '行政区ID',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ps_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公立学校表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_role`
--

DROP TABLE IF EXISTS `x360p_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_role` (
  `rid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `role_name` varchar(64) NOT NULL DEFAULT '' COMMENT '角色名',
  `role_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '角色描述',
  `pers` text COMMENT '权限记录，以逗号分隔',
  `mobile_pers` text COMMENT '机构用户移动端权限',
  `is_system` tinyint(1) unsigned DEFAULT '0' COMMENT '是否是系统定义角色（不可以修改）0：不是，1：是',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  `ext_id` int(11) DEFAULT NULL COMMENT 'dss对应的角色id',
  `sort` int(11) unsigned DEFAULT NULL COMMENT '显示顺序',
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='系统角色表(每一个用户都对应有1到多个角色)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_season_date`
--

DROP TABLE IF EXISTS `x360p_season_date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_season_date` (
  `sd_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `year` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '年份',
  `season` char(1) NOT NULL DEFAULT '' COMMENT '季节',
  `int_day_start` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始日期',
  `int_day_end` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned DEFAULT '0' COMMENT '删除用户ID',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`sd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='季度日期表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_sms_history`
--

DROP TABLE IF EXISTS `x360p_sms_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_sms_history` (
  `sh_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '短信内容',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送成功:0成功，其余失败',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`sh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短信发送记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_sms_vcode`
--

DROP TABLE IF EXISTS `x360p_sms_vcode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_sms_vcode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `mobile` varchar(16) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `code` varchar(16) DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `expire_time` int(11) unsigned DEFAULT '0' COMMENT '过期时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_mobile_type` (`mobile`,`type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短息验证码记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_student`
--

DROP TABLE IF EXISTS `x360p_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student` (
  `sid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '学员ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `student_name` varchar(32) NOT NULL DEFAULT '' COMMENT '学员姓名',
  `pinyin` varchar(50) NOT NULL DEFAULT '' COMMENT 'student_name的全拼',
  `pinyin_abbr` varchar(50) NOT NULL DEFAULT '' COMMENT 'student_name拼音的首字符缩写',
  `nick_name` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称/英文名',
  `sex` enum('2','1','0') NOT NULL DEFAULT '0' COMMENT '性别(0:未确定,1:男,2:女)',
  `photo_url` varchar(255) NOT NULL DEFAULT '' COMMENT '头像URL',
  `birth_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生时间戳',
  `birth_year` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生年',
  `birth_month` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生月',
  `birth_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出生日',
  `school_grade` int(11) NOT NULL DEFAULT '0' COMMENT '学校年级',
  `school_class` varchar(32) NOT NULL DEFAULT '' COMMENT '学校班级',
  `school_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学校ID',
  `first_tel` varchar(16) NOT NULL DEFAULT '',
  `first_family_name` varchar(32) NOT NULL DEFAULT '' COMMENT '第一亲属姓名',
  `first_family_rel` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0:未设置,1:自己，2：爸爸，3：妈妈，4：其他',
  `first_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '首选联系人的账号uid',
  `first_openid` varchar(255) NOT NULL DEFAULT '' COMMENT '首选联系人绑定的openid',
  `second_family_name` varchar(32) NOT NULL DEFAULT '' COMMENT '第2亲属姓名',
  `second_family_rel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0：未设置，1:自己，2：爸爸，3：妈妈，4：其他',
  `second_tel` varchar(16) NOT NULL DEFAULT '' COMMENT '第2电话',
  `second_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '第二联系人的账号uid',
  `second_openid` varchar(255) NOT NULL DEFAULT '' COMMENT '第二联系绑定的openid',
  `sno` varchar(32) NOT NULL DEFAULT '' COMMENT '学号',
  `card_no` varchar(32) DEFAULT NULL COMMENT '考勤卡号',
  `money` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '电子钱包余额',
  `credit` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '积分',
  `vip_level` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'VIP等级',
  `last_attendance_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后考勤时间',
  `is_lost` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否流失(1为是,0为否,由用户标记)',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  `ext_id` varchar(32) NOT NULL DEFAULT '' COMMENT '拓展系统ID(对接浪腾系统)',
  PRIMARY KEY (`sid`),
  KEY `normal_ext_id` (`ext_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员表(学员的记录信息)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_absence`
--

DROP TABLE IF EXISTS `x360p_student_absence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_absence` (
  `sa_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '缺勤记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'student_lesson表主键',
  `slv_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '请假记录ID，student_leave表主键',
  `chapter_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次序号(对应的课程课次)',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `lesson_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多,3:研学旅行团)',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课记录ID',
  `catt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班课考勤记录ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教老师ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `is_leave` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否请假(0:未请假,1:有请假)',
  `remark` varchar(255) DEFAULT '' COMMENT '缺课原因',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有补课(0:未补课,1:已安排，2:已补课结束（已考勤）)',
  `satt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生的考勤id（student_lesson）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`sa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='缺勤记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_attendance`
--

DROP TABLE IF EXISTS `x360p_student_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_attendance` (
  `satt_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '考勤记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `chapter_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次序号(对应的课程课次)',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'student_lesson表的主键',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'order_item表主键',
  `lesson_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多,3:研学旅行团)',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课记录ID',
  `catt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班课考勤记录ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教老师ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `in_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出勤时间',
  `left_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '离校时间(如果离校刷卡就记录离校时间)',
  `att_way` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '考勤方式(0:登记考勤,1:刷卡考勤,2:老师点名考勤，3:自由登记考勤)',
  `is_in` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否出勤(0:缺勤,1:出勤)',
  `is_late` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否迟到(0:未迟到,1:迟到)',
  `is_leave` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否请假(0:未请假,1:有请假)',
  `is_makeup` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否补课(0:正常,1:补课)',
  `is_consume` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否计算课消，课耗',
  `lesson_content` varchar(255) DEFAULT '' COMMENT '授课内容(1对1考勤有效)',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '学生的考勤备注,由老师在登记考勤的时候填写',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`satt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='考勤记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_credit_history`
--

DROP TABLE IF EXISTS `x360p_student_credit_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_credit_history` (
  `sch_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '电子钱包余额变动记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `cr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '积分规则ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `credit` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '积分数',
  `before_credit` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '变动前余额',
  `after_credit` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '变动后余额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除UID',
  PRIMARY KEY (`sch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员积分变动记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_student_leave`
--

DROP TABLE IF EXISTS `x360p_student_leave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_leave` (
  `slv_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '请假记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'student_lesson主键',
  `lesson_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多,3:研学旅行团)',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课记录ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `reason` varchar(255) DEFAULT '' COMMENT '请假原因',
  `leave_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:其他,1:病假,2:事假',
  `satt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生的考勤id（student_lesson）,如果是提前请假值为0。',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`slv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='请假记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_lesson`
--

DROP TABLE IF EXISTS `x360p_student_lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_lesson` (
  `sl_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单商品ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '课程ID',
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多)',
  `origin_lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原始课次数',
  `present_lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送课次数',
  `lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总的课次数',
  `origin_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原始购买的的总课时数（lesson表：lesson_chapter * unit_hours）',
  `present_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送的课时数',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总的课时数：origin_lesson_hours + present_lesson_hours',
  `expire_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '有效期至',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级id（暂时不需要 yr）',
  `ac_status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '分班状态(assign class status,0:未分班,1:部分分班,2:已分班)',
  `need_ac_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '需要分班数量',
  `ac_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已分班数量',
  `lesson_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程状态(0:未开始上课,1:上课中,2:已结束)',
  `is_stop` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否停课(0为否，1为是)',
  `use_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已上课次',
  `remain_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余课次',
  `use_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已消耗课时数',
  `remain_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '剩余课时数',
  `last_attendance_time` int(11) unsigned DEFAULT '0' COMMENT '最后考勤时间',
  `remain_arrange_times` int(11) DEFAULT '-99999' COMMENT '剩余待排课次数(默认创建时为lesson_times)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`sl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生课程班级表（与order_item关联）';
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `x360p_student_lesson_hour`
--

DROP TABLE IF EXISTS `x360p_student_lesson_hour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_lesson_hour` (
  `slh_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '课时消耗ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'student_lesson表主键',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'order_item表主键',
  `lesson_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多,3:研学旅行团)',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课记录ID',
  `satt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生考勤记录id',
  `catt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级考勤id',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教老师ID',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课时数',
  `lesson_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次总时间长度（单位：分钟）',
  `lesson_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课时金额',
  `is_pay` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否付款',
  `is_makeup` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是补课，默认是正常排课',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`slh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生课时消耗记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_student_money_history`
--

DROP TABLE IF EXISTS `x360p_student_money_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_money_history` (
  `smh_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '电子钱包余额变动记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `business_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '业务类型:(1:结转,2:退费,3:充值, 4:下单, 5:订单续费 0:用户手动操作)',
  `business_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '业务关联ID,结转ID,退费ID,充值ID)',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '金额',
  `before_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '操作前余额',
  `after_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '操作后余额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除UID',
  PRIMARY KEY (`smh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='电子钱包余额变动记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_suspend`
--

DROP TABLE IF EXISTS `x360p_student_suspend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_suspend` (
  `ss_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'student_suspend表主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `lid` int(11) unsigned NOT NULL COMMENT '课程id',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'student_lesson表主键',
  `begin_time` int(11) unsigned NOT NULL COMMENT '停课开始时间',
  `end_time` int(11) unsigned NOT NULL COMMENT '停课结束时间',
  `suspend_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '停课原因',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ss_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生停课表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_subject`
--

DROP TABLE IF EXISTS `x360p_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_subject` (
  `sj_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `subject_name` varchar(255) NOT NULL DEFAULT '' COMMENT '科目名称',
  `short_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '简短介绍',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`sj_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='科目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_swiping_card_record`
--

DROP TABLE IF EXISTS `x360p_swiping_card_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_swiping_card_record` (
  `scr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '刷卡ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `card_no` varchar(32) NOT NULL DEFAULT '' COMMENT '卡号',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '刷卡时间整数(1700)',
  `business_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '业务类型:0未匹配到,1上课考勤,2离校通知,3到校通知',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID,从哪台机刷的卡',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`scr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='刷卡记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_tally`
--

DROP TABLE IF EXISTS `x360p_tally`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_tally` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0',
  `bid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:进账，2出账',
  `cate` tinyint(4) NOT NULL DEFAULT '0' COMMENT '分类：1收入，2支出，3转账，4应收，5应付',
  `relate_id` int(11) NOT NULL DEFAULT '0' COMMENT '相关业务id,如票据id',
  `aa_id` int(11) NOT NULL DEFAULT '0' COMMENT '帐户id',
  `to_aa_id` int(11) NOT NULL DEFAULT '0' COMMENT '往来帐、转账相对的账户',
  `tt_id` int(11) NOT NULL DEFAULT '0' COMMENT '收支小类（tally_type)',
  `item_th_id` int(11) NOT NULL DEFAULT '0' COMMENT '核算项目（tally_help）',
  `client_th_id` int(11) DEFAULT '0' COMMENT '核算客户（tally_help）',
  `employee_th_id` int(11) NOT NULL DEFAULT '0' COMMENT '核算人员（tally_help）',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `int_day` int(11) DEFAULT NULL COMMENT '业务日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='帐户流水表';
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_tally_help`
--

DROP TABLE IF EXISTS `x360p_tally_help`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_tally_help` (
  `th_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `type` enum('client','employee','item') NOT NULL COMMENT '类别',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`th_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='记帐辅助核算';
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_tally_type`
--

DROP TABLE IF EXISTS `x360p_tally_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_tally_type` (
  `tt_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1：收入，2：支出',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级id',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`tt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='记帐收支分类';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_time_section`
--

DROP TABLE IF EXISTS `x360p_time_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_time_section` (
  `tsid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) NOT NULL COMMENT '校区id',
  `season` enum('C','S','Q','H') DEFAULT NULL COMMENT '季节',
  `week_day` smallint(1) DEFAULT '-1' COMMENT '星期几',
  `time_index` smallint(3) unsigned NOT NULL COMMENT '时间段序号',
  `int_start_hour` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间(800)',
  `int_end_hour` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间(1000)',
  `name` varchar(16) DEFAULT '' COMMENT '时段命名',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`tsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='时间段表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_trial_listen_arrange`
--

DROP TABLE IF EXISTS `x360p_trial_listen_arrange`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_trial_listen_arrange` (
  `tla_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '试听ID,自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `is_student` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否正式学员试听,是的话sid不为0,否的话cu_id不为0',
  `listen_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '试听类型:0跟班试听,1排班试听',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '试听课程ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '跟班试听ID',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课ID,course_arrange表关联',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '试听日期',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '试听开始上课时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '试听结束上课时间',
  `is_attendance` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否考勤，与attendance_status结合使用',
  `attendance_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '考勤状态 0：缺勤，初始值。1：出勤。与is_attendance结合使用',
  `is_transfered` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否转化为正式学员',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`tla_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='试听安排记录表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_user`
--

DROP TABLE IF EXISTS `x360p_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_user` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `account` varchar(64) NOT NULL DEFAULT '' COMMENT '账号',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(64) NOT NULL DEFAULT '' COMMENT 'Email地址',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` enum('2','0','1') NOT NULL DEFAULT '0' COMMENT '性别(0:未确定,1:男,2:女)',
  `user_type` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型(1:机构用户,2:学员家长)',
  `salt` varchar(16) NOT NULL DEFAULT '' COMMENT '随机安全码',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码加密存储',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像地址',
  `openid` varchar(64) NOT NULL DEFAULT '' COMMENT '微信openid',
  `default_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '一个用户有多个学生的情况，设置一个默认学生',
  `is_mobile_bind` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '手机号是否绑定',
  `is_email_bind` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'email是否绑定',
  `is_weixin_bind` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '微信是否绑定',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` varchar(32) NOT NULL DEFAULT '' COMMENT '最后登录IP地址',
  `login_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录总次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` smallint(3) unsigned NOT NULL DEFAULT '1' COMMENT '账号状态(1:正常,0:禁用)',
  `is_main` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是主帐号，加盟商主帐号',
  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否管理员',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `reset_time` int(11) unsigned DEFAULT NULL COMMENT '密码重置时间（提交手机号验证码验证成功的时间）',
  `reset_token` varchar(255) DEFAULT NULL COMMENT '重置密码的token',
  PRIMARY KEY (`uid`),
  KEY `idx_openid` (`openid`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='用户表(机构用户和学生用户2类)';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_user_student`
--

DROP TABLE IF EXISTS `x360p_user_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_user_student` (
  `us_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户学生ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生ID',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间表',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`us_id`),
  UNIQUE KEY `idx_usd` (`uid`,`sid`,`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户学生表(每个用户账号可以绑定1到多个学生)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_wechat_bind`
--

DROP TABLE IF EXISTS `x360p_wechat_bind`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wechat_bind` (
  `wb_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `appid` varchar(255) NOT NULL DEFAULT '' COMMENT '公众号的appid',
  `original_id` varchar(255) NOT NULL DEFAULT '' COMMENT '用户绑定的公众号的原始id',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区id',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`wb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户与用户绑定的公众号的关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_wechat_user`
--

DROP TABLE IF EXISTS `x360p_wechat_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wechat_user` (
  `wu_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `appid` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '公众号的appid',
  `openid` varchar(32) NOT NULL DEFAULT '' COMMENT '微信OPENID',
  `nickname` varchar(128) CHARACTER SET utf8mb4 NOT NULL DEFAULT '微信昵称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '微信图像',
  `sex` smallint(2) unsigned NOT NULL COMMENT '性别',
  `province` varchar(32) DEFAULT NULL COMMENT '省份',
  `city` varchar(32) DEFAULT NULL COMMENT '城市',
  `country` varchar(32) DEFAULT NULL COMMENT '国家',
  `language` varchar(32) DEFAULT NULL COMMENT '语言',
  `privilege_0` varchar(32) DEFAULT NULL COMMENT '运营商',
  `subscribe` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否关注',
  `subscribe_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关注时间',
  `unsubscribe_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '取消关注时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`wu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信公众号用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_wxmp`
--

DROP TABLE IF EXISTS `x360p_wxmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp` (
  `wxmp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户id',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0',
  `authorizer_appid` varchar(90) NOT NULL COMMENT '授权公众号的appid',
  `authorizer_access_token` varchar(255) NOT NULL COMMENT '授权方令牌（2小时刷新一次,easywechat自己实现了缓存，所以这个字段基本没用）',
  `authorizer_refresh_token` varchar(255) NOT NULL COMMENT '！！！授权方的刷新令牌，刷新令牌主要用于第三方平台获取和刷新已授权用户的access_token，只会在授权时刻提供，请妥善保存。 一旦丢失，只能让用户重新授权，才能再次拿到新的刷新令牌',
  `func_info` text COMMENT '授权给开发者的权限集列表',
  `nick_name` varchar(255) NOT NULL DEFAULT '' COMMENT '授权方昵称',
  `head_img` varchar(255) NOT NULL DEFAULT '' COMMENT '授权方头像',
  `service_type_info` tinyint(1) unsigned NOT NULL COMMENT '授权方公众号类型，0代表订阅号，1代表由历史老帐号升级后的订阅号，2代表服务号',
  `verify_type_info` tinyint(1) unsigned NOT NULL COMMENT '授权方认证类型',
  `user_name` varchar(255) NOT NULL COMMENT '授权方公众号的原始ID',
  `principal_name` varchar(255) NOT NULL DEFAULT '' COMMENT '公众号的主体名称',
  `alias` varchar(255) NOT NULL DEFAULT '' COMMENT '授权方公众号所设置的微信号，可能为空',
  `business_info` text COMMENT '用以了解功能的开通状况',
  `qrcode_url` varchar(255) NOT NULL DEFAULT '' COMMENT ' 二维码图片的URL，开发者最好自行也进行保存',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '授权状态 ',
  `merchant_id` varchar(50) DEFAULT '' COMMENT '支付设置商户id',
  `key` varchar(255) DEFAULT NULL COMMENT '支付密钥',
  `cert_path` varchar(255) DEFAULT NULL COMMENT '支付证书路径',
  `key_path` varchar(255) DEFAULT NULL COMMENT '证书私钥路径',
  `enable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启微信支付',
  `default_message` int(11) unsigned DEFAULT NULL COMMENT '默认消息的回复规则',
  `welcome_message` int(11) unsigned DEFAULT NULL COMMENT '关注事件的回复规则wxmp_rule',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是默认公众号',
  `template_enable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '消息模板是否同步',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`wxmp_id`),
  UNIQUE KEY `unique_appid` (`authorizer_appid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公众号授权信息与基础信息表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_wxmp_material`
--

DROP TABLE IF EXISTS `x360p_wxmp_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_material` (
  `material_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wxmp_id` int(11) unsigned NOT NULL,
  `appid` varchar(255) NOT NULL DEFAULT '' COMMENT 'appid',
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(355) NOT NULL DEFAULT '',
  `media_id` varchar(255) NOT NULL DEFAULT '',
  `width` int(10) unsigned NOT NULL DEFAULT '0',
  `height` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('news','image','video','voice') CHARACTER SET utf8 NOT NULL COMMENT '素材类型：图文，图片，语音，视频',
  `model` enum('local','wechat') CHARACTER SET utf8 NOT NULL COMMENT '附件模式，本地数据库素材还是微信永久素材',
  `tag` varchar(5000) NOT NULL DEFAULT '',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_wxmp_material_news`
--

DROP TABLE IF EXISTS `x360p_wxmp_material_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_material_news` (
  `news_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wxmp_id` int(11) unsigned NOT NULL,
  `material_id` int(11) unsigned NOT NULL,
  `thumb_media_id` varchar(255) DEFAULT '',
  `thumb_url` varchar(255) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `author` varchar(255) DEFAULT '',
  `digest` varchar(255) DEFAULT '',
  `content` longtext,
  `content_source_url` varchar(200) DEFAULT NULL,
  `show_cover_pic` tinyint(3) unsigned DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `displayorder` int(2) NOT NULL,
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_wxmp_menu`
--

DROP TABLE IF EXISTS `x360p_wxmp_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_menu` (
  `wm_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单组id',
  `wxmp_id` int(11) unsigned NOT NULL COMMENT 'qms_wxmp表的主键',
  `appid` varchar(255) NOT NULL DEFAULT '',
  `group_name` varchar(255) DEFAULT NULL COMMENT '菜单组名称',
  `buttons` text COMMENT '公众号菜单的json配置结构',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态, 1为生效，同一个微信公众号只能有一个正在生效中的菜单配置',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`wm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_wxmp_reply_image`
--

DROP TABLE IF EXISTS `x360p_wxmp_reply_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_reply_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(10) unsigned NOT NULL,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `media_id` varchar(255) NOT NULL COMMENT '这个字段是微信素材中的图片素材的media_id',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `rid` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_wxmp_reply_news`
--

DROP TABLE IF EXISTS `x360p_wxmp_reply_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_reply_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(10) unsigned NOT NULL,
  `media_id` varchar(255) NOT NULL,
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `rid` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_wxmp_reply_text`
--

DROP TABLE IF EXISTS `x360p_wxmp_reply_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_reply_text` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) unsigned NOT NULL COMMENT '规则id',
  `content` text,
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `rid` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='关键字的回复内（文本）';
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `x360p_wxmp_reply_video`
--

DROP TABLE IF EXISTS `x360p_wxmp_reply_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_reply_video` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(10) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `mediaid` varchar(255) NOT NULL,
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `rid` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='视频回复';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_wxmp_reply_voice`
--

DROP TABLE IF EXISTS `x360p_wxmp_reply_voice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_reply_voice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(10) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `media_id` varchar(255) NOT NULL,
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `rid` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `x360p_wxmp_rule`
--

DROP TABLE IF EXISTS `x360p_wxmp_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_rule` (
  `rule_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wxmp_id` int(11) unsigned NOT NULL,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '规则名称',
  `displayorder` int(10) unsigned NOT NULL COMMENT '优先级',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启，0：不开启，1：开启。默认为开启状态',
  `is_top` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '全局置顶',
  `containtype` varchar(255) NOT NULL DEFAULT '' COMMENT '触发后回复内容类型，用逗号隔开。类型对应回复表的表名',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信公众号 关键字自动回复的规则表';
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `x360p_wxmp_rule_keyword`
--

DROP TABLE IF EXISTS `x360p_wxmp_rule_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_rule_keyword` (
  `keyword_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wxmp_id` int(11) unsigned NOT NULL,
  `rule_id` int(11) unsigned NOT NULL COMMENT '关键字所属的规则id',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '关键词',
  `type` tinyint(1) unsigned NOT NULL COMMENT '匹配模式类型：1:精准触发 2:包含关键字触发 3:正则匹配关键字触发',
  `displayorder` int(10) unsigned NOT NULL COMMENT '关键词在规则中的添加顺序（==显示属性）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='自动回复的关键字表';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `x360p_wxmp_template`
--

DROP TABLE IF EXISTS `x360p_wxmp_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_template` (
  `wt_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `bid` int(11) unsigned NOT NULL DEFAULT '0',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0',
  `wxmp_id` int(11) unsigned NOT NULL DEFAULT '0',
  `scene` varchar(255) NOT NULL DEFAULT '' COMMENT '模板消息应用场景',
  `appid` varchar(255) NOT NULL,
  `short_id` varchar(255) NOT NULL,
  `template_id` varchar(255) NOT NULL COMMENT '模板id(重要)',
  `setting` varchar(255) NOT NULL DEFAULT '',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`wt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模板消息';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

