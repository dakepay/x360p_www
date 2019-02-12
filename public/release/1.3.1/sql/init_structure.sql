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
  `cp_id` int(11) NOT NULL DEFAULT '0' COMMENT '支付配置id',
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
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`al_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='系统操作日志表(记录系统操作日志)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_advice`
--

DROP TABLE IF EXISTS `x360p_advice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_advice` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT '校区id',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生id',
  `content` varchar(255) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='mobile投诉建议';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_advice_reply`
--

DROP TABLE IF EXISTS `x360p_advice_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_advice_reply` (
  `ar_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT '校区id',
  `aid` int(11) NOT NULL DEFAULT '0' COMMENT '投诉建议id',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '回复员工',
  `content` varchar(255) DEFAULT NULL COMMENT '回复内容',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生投诉建议回复';
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
  `url` varchar(255) DEFAULT NULL COMMENT '待办跳转地址',
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
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `parent_cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '由哪个班级升级而来',
  `class_name` varchar(255) NOT NULL DEFAULT '' COMMENT '班级名称',
  `class_no` varchar(32) NOT NULL DEFAULT '' COMMENT '班级编号',
  `class_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '班级类型，默认为0,1为临时班',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级',
  `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别',
  `unit_price` decimal(11,2) DEFAULT '0.00' COMMENT '课时单价',
  `per_lesson_hour_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '每课时多少分钟',
  `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '每次扣多少课时',
  `subject_grade` int(11) NOT NULL DEFAULT '0' COMMENT '科目等级',
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
  `season` char(1) NOT NULL DEFAULT 'A' COMMENT '季节',
  `start_lesson_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开课时间日期',
  `end_lesson_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结课时间日期',
  `status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '班级状态(0:待开课招生中,1:已开课,2:已结课)',
  `ext_id` varchar(32) NOT NULL DEFAULT '' COMMENT '对接外部系统的班级ID',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
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
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多,3:研学旅行团),创建课程的时候定义了',
  `is_trial` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否试听排班,1为是',
  `is_makeup` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开班补课的排课',
  `chapter_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次序号(对应的课程课次)',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级',
  `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课记录ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教老师ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `class_student_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '非补课和试听的应到人数',
  `need_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总的应到人数：包括正常学员，补课学员，试听学员',
  `in_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '实到人数',
  `absence_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '缺勤人数',
  `leave_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '请假人数',
  `later_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '迟到人数',
  `makeup_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '应到补课人数',
  `trial_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '应到试听人数',
  `lesson_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '排课的考勤备注，由老师在登记考勤的时候填写',
  `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣除课时',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`catt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='1.班课或一对一或一对多，不管是排课考勤，还是自由考勤，都会产生一条class_attendance记录。\r\n2.上同一节课的学生的考勤共享一条class_attendance记录。student_attendance belongs_to class_attendance。';
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_class_extra`
--

DROP TABLE IF EXISTS `x360p_class_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_class_extra` (
  `ce_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cid` varchar(128) DEFAULT '' COMMENT '模板名称',
  `content` text COMMENT '班级介绍',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ce_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='班级额外信息表';
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
  `cl_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0',
  `cid` int(11) unsigned NOT NULL COMMENT '班级id(x360p_class)',
  `sid` int(11) unsigned DEFAULT NULL COMMENT '学生id(与班级学生操作相关）',
  `event_type` int(11) unsigned NOT NULL COMMENT '事件类型，1：创建班级，2：编辑班级， 3：学生加入班级，4：学生退出班级，5：班级状态status更改，6：排课操作，7：考勤操作,8:升班操作，9：结课操作,10:该班级学生停课，11：该班级学生复课',
  `desc` varchar(255) DEFAULT NULL COMMENT '日志描述',
  `content` text COMMENT 'json数据',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`cl_id`)
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
  `season` char(1) NOT NULL DEFAULT 'A' COMMENT '季度',
  `week_day` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '星期（1-7）',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣多少课时',
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
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1正常,0停课,2转出,9结课',
  `is_end` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否结课: 0:no,1:yes（已废弃yr,2018/1/19）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`cs_id`),
  KEY `sid_normal` (`sid`) USING BTREE,
  KEY `cid_normal` (`cid`) USING BTREE
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
  `area` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '教室面积',
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
-- Table structure for table `x360p_comment`
--

DROP TABLE IF EXISTS `x360p_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_comment_click`
--

DROP TABLE IF EXISTS `x360p_comment_click`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评论点赞记录';
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
  UNIQUE KEY `idx_cfg_name` (`cfg_name`,`og_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表(KV结构)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_config_pay`
--

DROP TABLE IF EXISTS `x360p_config_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_config_pay` (
  `cp_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT '校区id',
  `name` varchar(255) DEFAULT NULL COMMENT '配置名称',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '支付类型：1微信支付',
  `appid` varchar(255) DEFAULT NULL COMMENT '第三方支付的appid',
  `config` text COMMENT '具体配置，json格式，不同的支付类型不的配置',
  `is_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`cp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付配置表';
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
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
  `name` varchar(128) DEFAULT '' COMMENT '排课名称,为试听排班的时候必须取名，为试听班名字',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `teach_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教id',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级',
  `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别',
  `cr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教室ID',
  `chapter_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次序号',
  `season` char(1) NOT NULL DEFAULT 'A' COMMENT '季度',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `is_attendance` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否考勤了（状态，0：未考勤， 1： 部分考勤，2：全部考勤）',
  `is_prepare` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '备课进度：0：没有备课，1：部分备课，2：完全备课',
  `prepare_file_nums` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '备课附件数目',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣多少课时',
  `create_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1机构排的课，2家长申请约课',
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
  `cu_id` int(11) DEFAULT '0' COMMENT '客户id,主要是试听的',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '班级id',
  `is_trial` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否试听',
  `is_makeup` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否补课',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级',
  `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `is_in` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '是否到',
  `is_leave` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否请假',
  `is_consume` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否扣课时',
  `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣多少课时',
  `remark` varchar(255) DEFAULT '' COMMENT '缺勤理由',
  `is_attendance` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否考勤（状态，0：未上课，1：已上课）',
  `is_cancel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否取消',
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
-- Table structure for table `x360p_course_prepare`
--

DROP TABLE IF EXISTS `x360p_course_prepare`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='备课记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_course_prepare_attachment`
--

DROP TABLE IF EXISTS `x360p_course_prepare_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_course_prepare_view`
--

DROP TABLE IF EXISTS `x360p_course_prepare_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_course_remind_log`
--

DROP TABLE IF EXISTS `x360p_course_remind_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_course_remind_log` (
  `crl_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0',
  `bid` int(11) NOT NULL DEFAULT '0',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生id',
  `ca_id` int(11) NOT NULL DEFAULT '0' COMMENT '课程',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`crl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课前提前醒';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_credit_rule`
--

DROP TABLE IF EXISTS `x360p_credit_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_credit_rule` (
  `cru_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `rule_name` varchar(255) DEFAULT '' COMMENT '规则名称',
  `hook_action` varchar(32) DEFAULT '' COMMENT '钩子名称(attendance_ok,leave_ok,homework_submit等)',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1,增加；2减少',
  `cate` tinyint(4) NOT NULL DEFAULT '1' COMMENT '积分类型： 1学习积分，2消费积分',
  `credit` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '积分数',
  `rule` text COMMENT '规则定义(json结构)',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否系统规划',
  `enable` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除UID',
  PRIMARY KEY (`cru_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='学员积分变动记录表';
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
  `home_address` varchar(255) DEFAULT NULL COMMENT '家庭住址',
  `openid` varchar(64) NOT NULL DEFAULT '' COMMENT '微信openid',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `trial_time` varchar(255) DEFAULT NULL COMMENT '试听时间段，1周一，2周二，3周三，4周四，1am,2pm,3night',
  `from_did` int(11) NOT NULL DEFAULT '0' COMMENT '招生来源(招生来源字典ID)',
  `input_from` int(11) NOT NULL DEFAULT '0' COMMENT '0手动录入，1扫码录入',
  `intention_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '意向程度1-5',
  `customer_status_did` int(11) NOT NULL DEFAULT '0' COMMENT '跟进状态(跟进状态字典ID)',
  `from_sid` int(11) NOT NULL DEFAULT '0' COMMENT '退学学员回流，以前的学员id',
  `is_reg` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否报读',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学员ID(报读之后的学员ID)',
  `signup_int_day` int(11) DEFAULT NULL COMMENT '报名日期',
  `signup_amount` decimal(11,2) DEFAULT '0.00' COMMENT '报名金额',
  `referer_sid` int(11) NOT NULL DEFAULT '0' COMMENT '介绍人,学员ID',
  `follow_eid` int(11) NOT NULL DEFAULT '0' COMMENT '主要跟进人（添加客户的时候选择的 主责任人，副责任人保存在customer_employee表中）',
  `assign_time` int(11) NOT NULL DEFAULT '0' COMMENT '客户分配给员工时间',
  `follow_times` int(11) NOT NULL DEFAULT '0' COMMENT '跟进次数',
  `visit_times` int(11) NOT NULL DEFAULT '0' COMMENT '到访次数',
  `trial_listen_times` int(11) NOT NULL DEFAULT '0' COMMENT '试听次数',
  `last_follow_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后跟进时间',
  `next_follow_time` int(11) NOT NULL DEFAULT '0' COMMENT '下次跟进时间',
  `mc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '市场渠道ID',
  `mcl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '市场名单ID',
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
  `intention_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '意向级别',
  `customer_status_did` int(11) NOT NULL DEFAULT '0' COMMENT '客户状态字典ID',
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
-- Table structure for table `x360p_customer_status_conversion`
--

DROP TABLE IF EXISTS `x360p_customer_status_conversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_customer_status_conversion` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0',
  `bid` int(11) unsigned NOT NULL DEFAULT '0',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户id',
  `follow_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销售责任人',
  `old_value` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '原来的客户跟进状态',
  `new_value` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新的跟进状态',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_debit_card`
--

DROP TABLE IF EXISTS `x360p_debit_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_debit_card` (
  `dc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bids` varchar(255) DEFAULT '' COMMENT '校区ID',
  `dpt_ids` varchar(255) DEFAULT '' COMMENT '大区ID',
  `card_name` varchar(64) DEFAULT '' COMMENT '卡名',
  `amount` decimal(11,2) DEFAULT '0.00' COMMENT '金额',
  `discount_define` text COMMENT '折扣定义(JSON格式)',
  `expire_days` int(11) DEFAULT '365' COMMENT '有效期天数0为无限制',
  `upgrade_vip_level` int(11) DEFAULT '0' COMMENT '升级到会员级别',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`dc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_demo_transfer_history`
--

DROP TABLE IF EXISTS `x360p_demo_transfer_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_demo_transfer_history` (
  `dth_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `from_cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '从哪个班级转换的',
  `teach_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '老师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教ID',
  `edu_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '咨询师ID',
  `sign_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '报名金额',
  `int_day` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`dth_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
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
  `is_system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否系统默认',
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
-- Table structure for table `x360p_edu_growup`
--

DROP TABLE IF EXISTS `x360p_edu_growup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_edu_growup_item`
--

DROP TABLE IF EXISTS `x360p_edu_growup_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_edu_growup_pic`
--

DROP TABLE IF EXISTS `x360p_edu_growup_pic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
-- Table structure for table `x360p_employee`
--

DROP TABLE IF EXISTS `x360p_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_employee` (
  `eid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '员工ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
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
  `option_fields` text COMMENT '自定义字段',
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
  `edu_eid` int(11) unsigned DEFAULT '0' COMMENT '导师ID',
  `lesson_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多,3:研学旅行团)',
  `change_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '变化类型：1考勤，2自由登记课耗',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级',
  `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生ID(1对1课有效)',
  `sids` varchar(255) DEFAULT '' COMMENT '学生ID(1对多课有效)',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课记录ID',
  `satt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生考勤记录ID(1对1课有效)',
  `catt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级考勤记录ID(班课考勤ID)',
  `slh_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联学员课耗记录ID,change_type 为2时有效',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `student_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课学生数，出勤的',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课时数',
  `lesson_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次时间长度（单位：分钟）',
  `total_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总计课时数',
  `total_lesson_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '总计课时金额',
  `payed_lesson_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '付款课时金额',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
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
  `sale_role_did` int(11) NOT NULL DEFAULT '0' COMMENT '销售角色ID',
  `orb_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收据ID',
  `or_id` int(11) NOT NULL DEFAULT '0' COMMENT '退款收款id',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '回款金额',
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
-- Table structure for table `x360p_employee_student`
--

DROP TABLE IF EXISTS `x360p_employee_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_employee_student` (
  `es_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '员工id',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '员工与学员关系，1：一对一、一对多上课老师与学生(班课不需要记录)，2：学管师与学生',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`es_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工学员关系表';
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
-- Table structure for table `x360p_event`
--

DROP TABLE IF EXISTS `x360p_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_event` (
  `event_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动ID',
  `event_name` varchar(255) NOT NULL DEFAULT '' COMMENT '活动名称',
  `is_event_online` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否线上活动',
  `bids` varchar(255) NOT NULL DEFAULT '' COMMENT '活动执行校区id用逗号分隔',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'scope位class的时候cid有效',
  `scope` enum('class','branch','global') NOT NULL DEFAULT 'global' COMMENT '活动范围',
  `event_type_did` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '活动类型字典ID(180:讲座,181:期中展示,182:期末展示,183:优秀评比)',
  `event_start_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动开始时间',
  `event_end_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动结束时间',
  `share_title` varchar(255) NOT NULL DEFAULT '' COMMENT '宣传分享标题',
  `share_image_url` varchar(255) NOT NULL DEFAULT '' COMMENT '分享图片路径(300*300)',
  `event_image_url` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图片(600px * 300px)',
  `event_meta` text COMMENT '活动结构化描述',
  `event_content` text COMMENT '活动内容介绍',
  `link_url` varchar(255) DEFAULT NULL COMMENT '链接URL',
  `status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态（0:已禁用,1:正常,2:已结束,3:已取消)',
  `allow_sign_up` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否允许报名: 0不允许， 1允许',
  `apply_nums_limit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '报名人数限制',
  `view_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览人数',
  `apply_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '报名人数',
  `summary` text COMMENT '活动总结',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_event_attachment`
--

DROP TABLE IF EXISTS `x360p_event_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_event_attachment` (
  `ea_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动附件ID',
  `event_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动ID',
  `file_name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `file_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件ID',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件路径',
  `file_size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '附件大小(字节)',
  `file_ext` char(8) NOT NULL DEFAULT '' COMMENT '附件拓展名',
  `create_time` int(11) NOT NULL,
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动对应的附件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_event_sign_up`
--

DROP TABLE IF EXISTS `x360p_event_sign_up`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_event_sign_up` (
  `esu_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0',
  `bid` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动id',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生id',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名（非学生报名）',
  `tel` char(20) DEFAULT NULL COMMENT '联系电话（非学生报名）',
  `openid` varchar(255) DEFAULT NULL COMMENT 'openid',
  `nickname` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_attend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否签到',
  `attend_time` int(11) NOT NULL DEFAULT '0' COMMENT '签到时间',
  `mcl_id` int(11) NOT NULL DEFAULT '0' COMMENT '转为市场名单',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`esu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动报名记录';
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
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `file_size` bigint(20) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `duration` varchar(25) NOT NULL DEFAULT '' COMMENT '当文件为mp3时该字段不为空。',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `appid` varchar(255) DEFAULT NULL COMMENT '公众号appid',
  `openid` varchar(225) NOT NULL DEFAULT '' COMMENT '用户openid',
  `media_type` char(20) NOT NULL DEFAULT '' COMMENT '媒体文件类型（微信回调消息类型）',
  `media_id` varchar(255) NOT NULL DEFAULT '' COMMENT '微信的media_id',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统文件表(所有上传的附件文件，都会记录下来，有一个唯一的file_id)';
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_file_package`
--

DROP TABLE IF EXISTS `x360p_file_package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_file_package` (
  `fp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `short_id` char(16) NOT NULL DEFAULT '' COMMENT '短ID唯一',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `title` varchar(255) DEFAULT '' COMMENT '文件包说明',
  `files_package_id` char(32) NOT NULL DEFAULT '' COMMENT '包所有文件id的md5值',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`fp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件包表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_file_package_file`
--

DROP TABLE IF EXISTS `x360p_file_package_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_file_package_file` (
  `fpf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `fp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课标ID',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '文件id',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件URL',
  `file_type` varchar(16) DEFAULT '' COMMENT '文件类型',
  `file_size` bigint(20) unsigned DEFAULT '0' COMMENT '文件大小',
  `file_name` varchar(64) DEFAULT '' COMMENT '文件名',
  `media_type` char(50) DEFAULT NULL COMMENT '媒体类型',
  `duration` varchar(255) DEFAULT NULL COMMENT '音频时长',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`fpf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件包附件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_file_package_view`
--

DROP TABLE IF EXISTS `x360p_file_package_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_file_package_view` (
  `fpv_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `openid` varchar(64) DEFAULT '' COMMENT '粉丝OPENID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`fpv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件包浏览记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_handover_money`
--

DROP TABLE IF EXISTS `x360p_handover_money`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_handover_money` (
  `hm_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL COMMENT '机构id',
  `bid` int(11) NOT NULL,
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '缴款人',
  `amount` decimal(11,2) NOT NULL COMMENT '缴费总额(包括现金）',
  `cash_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '现金部分总额',
  `ack_eid` int(11) NOT NULL DEFAULT '0' COMMENT '确认人',
  `ack_time` int(11) NOT NULL DEFAULT '0' COMMENT '确认时间',
  `tid` int(11) NOT NULL DEFAULT '0' COMMENT '汇款流水id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`hm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='交班后统一缴费';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_handover_work`
--

DROP TABLE IF EXISTS `x360p_handover_work`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_handover_work` (
  `hw_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `bid` int(11) NOT NULL COMMENT '校区id',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '交班人',
  `money_inc_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '值班期间增加的收款额，包括现金',
  `money_dec_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '值班期间减少的金额，退费',
  `cash_inc_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '值班期间增加的现金',
  `cash_dec_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '值班期间减少的现金',
  `to_eid` int(11) NOT NULL DEFAULT '0' COMMENT '交班接收人',
  `to_hw_id` int(11) NOT NULL DEFAULT '0' COMMENT '此次交班去向id',
  `hm_id` int(11) NOT NULL DEFAULT '0' COMMENT '统一缴款id',
  `submit_time` int(11) DEFAULT '0' COMMENT '交班时间',
  `ack_time` int(11) DEFAULT '0' COMMENT '确认时间',
  `start_time` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`hw_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COMMENT='交班记录';
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
-- Table structure for table `x360p_homework_attachment`
--

DROP TABLE IF EXISTS `x360p_homework_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_homework_attachment` (
  `ha_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `ht_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'homework_task表主键id,att_type为0时有效',
  `hc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'homework_complete表主键id,att_type为1时有效',
  `hr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'homework_reply表主键id,att_type为2时有效',
  `att_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '附件类型,0:作业任务附件,1:作业完成附件,2:作业回复附件',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '文件id',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件URL',
  `file_type` varchar(16) DEFAULT '' COMMENT '文件类型',
  `file_size` bigint(20) unsigned DEFAULT '0' COMMENT '文件大小',
  `file_name` varchar(64) DEFAULT '' COMMENT '文件名',
  `duration` varchar(255) DEFAULT NULL COMMENT '音频时长',
  `media_type` char(50) DEFAULT NULL COMMENT '媒体类型',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '11' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业附件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_homework_complete`
--

DROP TABLE IF EXISTS `x360p_homework_complete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `check_level` tinyint(1) unsigned DEFAULT '0' COMMENT '批改等级(0:普批，1：精批)',
  `check_content` text COMMENT '批改内容',
  `result_level` tinyint(1) unsigned DEFAULT '0' COMMENT '作业完成等级1-10，需要区分标准课程和非标准课程',
  `sart_id` int(11) NOT NULL DEFAULT '0' COMMENT '作品id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`hc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业完成表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_homework_publish`
--

DROP TABLE IF EXISTS `x360p_homework_publish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_homework_publish` (
  `hp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '作业发表ID',
  `bid` int(11) unsigned DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned DEFAULT '0' COMMENT '班级ID',
  `lid` int(11) unsigned DEFAULT '0' COMMENT '课程ID',
  `sid` int(11) unsigned DEFAULT '0' COMMENT '学生ID',
  `hc_id` int(11) unsigned DEFAULT '0' COMMENT '作业完成ID',
  `media_type` tinyint(1) unsigned DEFAULT '0' COMMENT '发表媒体类型(0:纸媒,1:线上媒体)',
  `media_name` varchar(255) DEFAULT '' COMMENT '媒体名称',
  `link_url` varchar(255) DEFAULT NULL COMMENT '可查看链接',
  `description` varchar(255) DEFAULT '' COMMENT '发表说明',
  `publish_time` int(11) unsigned DEFAULT NULL COMMENT '发表日期',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`hp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业发表记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_homework_reply`
--

DROP TABLE IF EXISTS `x360p_homework_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  PRIMARY KEY (`hr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业完成表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_homework_task`
--

DROP TABLE IF EXISTS `x360p_homework_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `deadline` int(11) NOT NULL DEFAULT '0' COMMENT '截止日期',
  `push_status` tinyint(1) unsigned DEFAULT '0' COMMENT '推送状态(0:待推送,1:已推送)',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ht_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业任务表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_homework_task_tpl_define`
--

DROP TABLE IF EXISTS `x360p_homework_task_tpl_define`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_homework_task_tpl_setting`
--

DROP TABLE IF EXISTS `x360p_homework_task_tpl_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_homework_view`
--

DROP TABLE IF EXISTS `x360p_homework_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_homework_view` (
  `hv_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `ht_id` int(11) NOT NULL DEFAULT '0' COMMENT '作业任务id',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生id',
  `times` int(11) NOT NULL DEFAULT '1' COMMENT '查看次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`hv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='作业查看记录';
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
-- Table structure for table `x360p_knowledge_item`
--

DROP TABLE IF EXISTS `x360p_knowledge_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_knowledge_item` (
  `ki_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `ktype_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '知识类型:211系统帮助,212工作指引,213沟通话术',
  `router_uri` varchar(255) DEFAULT '' COMMENT '路由URI',
  `system_uri` varchar(255) DEFAULT '' COMMENT '系统内置URI',
  `title` varchar(255) DEFAULT '' COMMENT '方案标题',
  `keywords` varchar(255) DEFAULT '' COMMENT '关键词,逗号分隔',
  `content` text COMMENT '内容',
  `stars` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '星星数',
  `create_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建员工ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ki_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='知识条目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_knowledge_item_like`
--

DROP TABLE IF EXISTS `x360p_knowledge_item_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_knowledge_item_like` (
  `kil_id` int(11) NOT NULL AUTO_INCREMENT,
  `ki_id` int(11) NOT NULL DEFAULT '0' COMMENT '知识id',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '点赞员工',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`kil_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='知识点赞';
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
  `season` char(1) NOT NULL DEFAULT 'A' COMMENT '学期季节',
  `sj_id` int(11) NOT NULL DEFAULT '0' COMMENT '科目id',
  `sj_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '科目ID（课时包有效)',
  `lesson_name` varchar(255) NOT NULL DEFAULT '' COMMENT '课程名称',
  `lesson_no` varchar(16) NOT NULL DEFAULT '' COMMENT '课程编号',
  `product_level_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '产品等级字典ID',
  `fit_age_start` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '适合年龄段开始',
  `fit_age_end` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '适合年龄结束',
  `fit_grade_start` smallint(11) NOT NULL DEFAULT '0' COMMENT '适合年级开始',
  `fit_grade_end` smallint(11) NOT NULL DEFAULT '0' COMMENT '适合年级结束',
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
  `unit_price` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '单价，跟随price_type',
  `unit_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单次课扣多少课时',
  `unit_lesson_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '单次课时长(单位分钟)',
  `sale_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课程售价',
  `per_lesson_hour_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '每课时多少分钟',
  `ext_lid` varchar(32) NOT NULL DEFAULT '' COMMENT '外部课程ID(对接浪腾系统)',
  `version` varchar(16) NOT NULL DEFAULT '' COMMENT '版本号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1为启用,0为禁用)',
  `is_package` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否课时包',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否缺省课程',
  `is_publish` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已经发布',
  `is_standard` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是标准课程，0：不是，1：是',
  `ability_did` int(11) NOT NULL DEFAULT '0' COMMENT '能力字典ID',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
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
-- Table structure for table `x360p_lesson_buy_suit`
--

DROP TABLE IF EXISTS `x360p_lesson_buy_suit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_lesson_buy_suit` (
  `lbs_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `title` varchar(255) DEFAULT '' COMMENT '方案标题',
  `lsd_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学习套餐ID',
  `define` text COMMENT '定义JSON结构:[{lid:nums}]',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`lbs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学习套餐购买定义表';
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
-- Table structure for table `x360p_lesson_price_define`
--

DROP TABLE IF EXISTS `x360p_lesson_price_define`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_lesson_price_define` (
  `lpd_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `dtype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '定义类型,0按课程定价，1按课程科目定价,2按课程等级定价',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `product_level_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '产品等级字典ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `bids` varchar(255) DEFAULT '' COMMENT '校区IDS',
  `dept_ids` varchar(255) DEFAULT '' COMMENT '分公司ID',
  `sale_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '价格',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`lpd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
-- Table structure for table `x360p_lesson_standard_file`
--

DROP TABLE IF EXISTS `x360p_lesson_standard_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_lesson_standard_file` (
  `lsf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联课程ID',
  `chapter_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '章节序号',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `csft_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课标类型字典ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`lsf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课标表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_lesson_standard_file_item`
--

DROP TABLE IF EXISTS `x360p_lesson_standard_file_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_lesson_standard_file_item` (
  `lsfi_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `lsf_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课标ID',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '文件id',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件URL',
  `file_type` varchar(16) DEFAULT '' COMMENT '文件类型',
  `file_size` bigint(20) unsigned DEFAULT '0' COMMENT '文件大小',
  `file_name` varchar(64) DEFAULT '' COMMENT '文件名',
  `media_type` char(50) DEFAULT NULL COMMENT '媒体类型',
  `duration` varchar(255) DEFAULT NULL COMMENT '音频时长',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`lsfi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课标文件条目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_lesson_suit_define`
--

DROP TABLE IF EXISTS `x360p_lesson_suit_define`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_lesson_suit_define` (
  `lsd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID, 如果是0则适用所有校区',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '套餐名',
  `define` text COMMENT '定义JSON结构:[{product_level_did:nums}]',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`lsd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学习套餐定义表';
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
  `cas_id` int(11) NOT NULL DEFAULT '0' COMMENT 'cas_id，主要是取消排课补课使用',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课课程ID',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'student_lesson表主键',
  `slv_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '请假记录ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课科目ID',
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级',
  `makeup_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '补课类型:0跟班补课,1排班补课',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '跟班补课班级ID(如果是排班补课则cid为0)',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课ID,course_arrange表关联',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课日期',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课开始上课时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '补课结束上课时间',
  `catt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'class_attendance表主键',
  `satt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'student_attendance表主键',
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
-- Table structure for table `x360p_market_channel`
--

DROP TABLE IF EXISTS `x360p_market_channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_market_channel` (
  `mc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID,可以为0',
  `channel_name` varchar(255) DEFAULT '' COMMENT '渠道名称(要排除重复)',
  `from_did` int(11) NOT NULL DEFAULT '0' COMMENT '招生来源(招生来源字典ID)',
  `is_share` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否共享渠道',
  `qr_config` text COMMENT '渠道二维码配置,JSON格式',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='市场渠道表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_market_channel_excel`
--

DROP TABLE IF EXISTS `x360p_market_channel_excel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_market_clue`
--

DROP TABLE IF EXISTS `x360p_market_clue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `is_reward` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否奖励',
  `is_visit` tinyint(1) DEFAULT '0' COMMENT '是否上门',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户名单ID,分配给咨询师以后',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID,成交以后',
  `recommend_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推荐学员ID',
  `recommend_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推荐学员用户ID',
  `recommend_note` varchar(255) DEFAULT NULL COMMENT '推荐说明,家长填写',
  `recommend_reward_note` varchar(255) DEFAULT NULL COMMENT '推荐奖励备注,内部员工填写',
  `assigned_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分配的员工ID,未分配为0',
  `assigned_time` int(11) NOT NULL DEFAULT '0' COMMENT '分配时间',
  `qr_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '二维码eid',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`mcl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='市场机会表';
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
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(255) NOT NULL COMMENT '物品名称',
  `unit` char(4) NOT NULL COMMENT '计量单位',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `image` varchar(255) NOT NULL COMMENT '图片',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `purchase_price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '进货价',
  `sale_price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '销售价',
  `is_cate` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否是分类栏目',
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
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单OrderId',
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
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT 'eid',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '班级id',
  `business_type` varchar(255) NOT NULL COMMENT '业务类型',
  `business_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '业务ID',
  `send_mode` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '消息发送渠道：0：站内信，1：微信，2：短信，4：微信+短信',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `tpl_data` text COMMENT '消息模板字段对应信息',
  `content` text NOT NULL COMMENT '消息内容',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '点击消息查看的url',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发送成功:0成功，其余失败',
  `error` varchar(255) DEFAULT NULL COMMENT '发送失败消息',
  `mgh_id` int(11) NOT NULL DEFAULT '0' COMMENT '群发id',
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
-- Table structure for table `x360p_message_group_history`
--

DROP TABLE IF EXISTS `x360p_message_group_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_message_group_history` (
  `mgh_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT 'og_id',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT 'bid',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1: 短信， 2：微信',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '需发送总数',
  `success_num` int(11) NOT NULL DEFAULT '0' COMMENT '成功人数',
  `content` text COMMENT '内容',
  `tpl_id` varchar(255) NOT NULL DEFAULT '0' COMMENT '短信tpl_id',
  `business_type` varchar(255) DEFAULT NULL COMMENT '微信模板',
  `tpl_data` text COMMENT '模板值',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  PRIMARY KEY (`mgh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短信群发记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_mobile_login_log`
--

DROP TABLE IF EXISTS `x360p_mobile_login_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_mobile_login_log` (
  `mll_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '家长学员',
  `ip` varchar(255) DEFAULT NULL COMMENT '登录ip',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '客户端信息',
  `login_time` int(11) DEFAULT NULL COMMENT '登录时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`mll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='家长端登录日志';
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
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
  `is_debit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否储值订单,默认为0',
  `sdc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员储值卡记录ID',
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
  `sid` int(11) DEFAULT '0' COMMENT '学生id',
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
  `dc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '储值卡ID',
  `is_deliver` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否发货或储值，针对物品和储值',
  `gtype` tinyint(1) unsigned DEFAULT '0' COMMENT '商品类型 0：课程，1：物品 2:储值卡',
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '课程id, 主要是暂存订单、付款为0时使用',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '班级id, 主要是暂存订单、付款为0时使用',
  `sl_id` int(11) NOT NULL DEFAULT '0' COMMENT '学生课程id(student_lesson表)',
  `nums` decimal(11,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '商品数量',
  `nums_unit` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '数量单位(0为物品的数量单位,1为课次,2为课时,3为月按时间',
  `origin_price` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '原始单价',
  `price` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '折后单价（成交单价）',
  `pr_id` int(11) unsigned DEFAULT '0' COMMENT '促销规则id',
  `origin_amount` decimal(11,2) unsigned DEFAULT '0.00' COMMENT '原始金额',
  `subtotal` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '小计金额',
  `paid_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '已付款',
  `discount_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '折扣金额',
  `reduced_amount` decimal(11,2) DEFAULT '0.00' COMMENT '分摊优惠减少的金额',
  `unit_lesson_hour_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '课耗单课时金额',
  `origin_lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原始课次数',
  `present_lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送课次数',
  `origin_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原始购买的的总课时数（lesson表：lesson_chapter * unit_hours）',
  `present_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送的课时数',
  `deduct_present_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣除赠送课时数',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
  `expire_time` int(11) DEFAULT NULL COMMENT '有效期',
  `sdc_id` int(11) NOT NULL DEFAULT '0' COMMENT '储蓄卡id',
  `consume_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '收费类型：1新报，2续报，3扩科',
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
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `orb_id` int(11) NOT NULL DEFAULT '0' COMMENT '收据id',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID,',
  `aa_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会计账号ID',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '收款金额',
  `paid_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '付款时间',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL,
  `delete_uid` int(11) unsigned DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`oph_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100001 DEFAULT CHARSET=utf8mb4 COMMENT='订单付款记录ID';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_order_payment_online`
--

DROP TABLE IF EXISTS `x360p_order_payment_online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_payment_online` (
  `opo_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `oid` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `aa_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收款账号ID',
  `paid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '付款金额',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0：待支付，1：已支付',
  `out_trade_no` varchar(255) DEFAULT NULL,
  `code_url` varchar(255) DEFAULT NULL COMMENT '生成的二维码地址',
  `trade_type` varchar(255) DEFAULT NULL COMMENT '支付类型：NATIVE, JSAPI, MICROPAY',
  `transaction_id` varchar(32) NOT NULL DEFAULT '' COMMENT '支付网关返回的支付ID',
  `pay_result` text,
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `oph_id` int(11) NOT NULL DEFAULT '0' COMMENT 'order_payment_history主键',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`opo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_order_payment_online_code`
--

DROP TABLE IF EXISTS `x360p_order_payment_online_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_payment_online_code` (
  `opoc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `oid` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `aa_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收款账号ID',
  `paid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '付款金额',
  `code` char(6) DEFAULT NULL COMMENT '随机支付码',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0：待支付，1：已支付',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`opoc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='手机在线支付的随机码记录';
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
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
  `is_debit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否储值收据,默认为0',
  `orb_no` varchar(32) DEFAULT '' COMMENT '收据编号',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '收款金额',
  `balance_paid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额付款金额(电子钱包抵扣金额)',
  `money_paid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '现金付款金额(收款金额-余额付款金额)',
  `unpaid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '欠缴金额',
  `student_money` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '超额预存部分',
  `hw_id` int(11) NOT NULL DEFAULT '0' COMMENT '收据交班id',
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
  `balance_paid_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '电子钱包支付',
  `money_paid_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '非电子钱包支付',
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
  `hw_id` int(11) NOT NULL DEFAULT '0' COMMENT '交班id',
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
  `present_nums` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣除赠送数量',
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
  `balance_amount` decimal(32,3) NOT NULL DEFAULT '0.000' COMMENT '结转到电子钱包的金额',
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
  `present_nums` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣除赠送数量',
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
  `host` char(20) NOT NULL DEFAULT '' COMMENT '三级域名',
  `org_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0非加盟商， 1加盟商',
  `mobile` char(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `org_short_name` varchar(64) NOT NULL DEFAULT '' COMMENT '机构简称',
  `province_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '省ID',
  `city_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '城市ID',
  `district_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '区域ID',
  `org_address` varchar(255) NOT NULL DEFAULT '' COMMENT '机构地址',
  `expire_day` int(11) NOT NULL COMMENT '到期日期',
  `charge_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '责任人eid',
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
-- Table structure for table `x360p_page`
--

DROP TABLE IF EXISTS `x360p_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_page` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `is_cate` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否分类',
  `parent_pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级页面ID,分类ID',
  `thumb_url` varchar(255) DEFAULT '' COMMENT '图片URL',
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='移动端页面';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_pay_item`
--

DROP TABLE IF EXISTS `x360p_pay_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_pay_item` (
  `pi_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `name` varchar(255) NOT NULL COMMENT '物品名称',
  `unit` char(4) NOT NULL COMMENT '计量单位',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `unit_price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '单价',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1启用，0禁用',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`pi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='收费项目表';
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
-- Table structure for table `x360p_questionnaire`
--

DROP TABLE IF EXISTS `x360p_questionnaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_questionnaire` (
  `qid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(255) DEFAULT '' COMMENT '标题',
  `qt_dids` varchar(255) DEFAULT '' COMMENT '问卷类型字典ID多选,逗号分隔',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`qid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学情问卷';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_questionnaire_item`
--

DROP TABLE IF EXISTS `x360p_questionnaire_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_questionnaire_item` (
  `qi_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `qid` int(11) DEFAULT '0' COMMENT '问卷id',
  `qt_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '问卷类型字典ID',
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `choices` text COMMENT '选项,JSON格式',
  `is_multi` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否多选',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`qi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学情问卷题目';
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_report_class_by_name`
--

DROP TABLE IF EXISTS `x360p_report_class_by_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_report_class_by_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `lid` int(11) DEFAULT '0',
  `cid` int(11) DEFAULT '0',
  `sid` int(11) DEFAULT '0',
  `int_day` int(8) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  `teach_eid` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_class_by_number`
--

DROP TABLE IF EXISTS `x360p_report_class_by_number`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_class_by_room`
--

DROP TABLE IF EXISTS `x360p_report_class_by_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_class_by_teacher`
--

DROP TABLE IF EXISTS `x360p_report_class_by_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_report_class_by_teacher` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `teach_eid` int(11) DEFAULT '0',
  `bids` varchar(50) DEFAULT '0',
  `cid` int(11) DEFAULT '0',
  `lid` int(11) DEFAULT '0',
  `ca_ids` varchar(50) DEFAULT NULL COMMENT '排课ids',
  `on_ca_ids` varchar(50) DEFAULT NULL COMMENT '已上排课ids',
  `total_arrange_nums` int(11) DEFAULT '0' COMMENT '总排课',
  `on_arrange_nums` int(11) DEFAULT '0' COMMENT '已上排课',
  `year` int(11) DEFAULT '0',
  `month` int(11) DEFAULT '0',
  `week` int(11) DEFAULT '0',
  `day` int(11) DEFAULT '0',
  `int_day` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_report_demolesson_by_lesson`
--

DROP TABLE IF EXISTS `x360p_report_demolesson_by_lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_report_demolesson_by_lesson` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(10) unsigned DEFAULT '0',
  `bids` varchar(255) DEFAULT '0',
  `lid` int(11) DEFAULT '0',
  `cids` varchar(255) DEFAULT '0',
  `sids` varchar(1000) DEFAULT '0',
  `transfered_sids` varchar(255) DEFAULT '0' COMMENT '体验学员转为正式学员ID',
  `int_day` int(8) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_demolesson_by_teacher`
--

DROP TABLE IF EXISTS `x360p_report_demolesson_by_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_report_demolesson_by_teacher` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(10) unsigned DEFAULT '0',
  `eid` int(11) DEFAULT '0',
  `cid` int(11) DEFAULT '0',
  `lid` int(11) DEFAULT '0',
  `sids` varchar(500) DEFAULT '0',
  `transfered_sids` varchar(255) DEFAULT '0',
  `int_day` int(8) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_lessonhour`
--

DROP TABLE IF EXISTS `x360p_report_lessonhour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_report_lessonhour` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `origin_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '期初课时',
  `origin_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '期初课时金额',
  `sign_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '报名课时',
  `sign_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '报名课时金额',
  `send_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '赠送课时',
  `convert_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '结转课时',
  `convert_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '结转课时金额',
  `consume_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '消耗课时',
  `consume_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '消费课时金额',
  `refund_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '退费课时',
  `refund_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '退费课时金额',
  `remain_lesson_num` decimal(11,2) DEFAULT '0.00' COMMENT '剩余课时',
  `remain_lesson_amount` decimal(20,6) DEFAULT '0.000000' COMMENT '剩余金额',
  `int_day` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_service_by_system`
--

DROP TABLE IF EXISTS `x360p_report_service_by_system`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `arrange_times` int(11) DEFAULT '0' COMMENT '排课次数',
  `attendance_times` int(11) DEFAULT '0' COMMENT '考勤次数',
  `year` int(11) DEFAULT '0',
  `month` int(11) DEFAULT '0',
  `week` int(11) DEFAULT '0',
  `day` int(11) DEFAULT '0',
  `int_day` int(11) DEFAULT '0',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5026 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_student_by_class`
--

DROP TABLE IF EXISTS `x360p_report_student_by_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_student_by_lesson`
--

DROP TABLE IF EXISTS `x360p_report_student_by_lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_student_by_quit`
--

DROP TABLE IF EXISTS `x360p_report_student_by_quit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_student_by_remainlessonhour`
--

DROP TABLE IF EXISTS `x360p_report_student_by_remainlessonhour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_report_student_by_remainlessonhour` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) DEFAULT '0',
  `bid` int(11) DEFAULT '0',
  `sid` int(11) DEFAULT '0',
  `cids` varchar(50) DEFAULT '0',
  `lids` varchar(50) DEFAULT '0',
  `lesson_hour` decimal(10,2) DEFAULT '0.00',
  `remain_lesson_hour` decimal(10,2) DEFAULT '0.00',
  `remain_money` decimal(10,2) DEFAULT '0.00',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_student_by_school`
--

DROP TABLE IF EXISTS `x360p_report_student_by_school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_student_by_teacher`
--

DROP TABLE IF EXISTS `x360p_report_student_by_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_report_student_by_teacher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0',
  `bids` varchar(20) DEFAULT '0',
  `teach_eid` int(11) DEFAULT '0',
  `class_nums` int(11) DEFAULT '0',
  `class_student_nums` int(11) DEFAULT '0',
  `onetoone_student_nums` int(11) DEFAULT '0',
  `onetomore_student_nums` int(11) DEFAULT '0',
  `is_on_job` tinyint(1) DEFAULT '1',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_student_lesson_class`
--

DROP TABLE IF EXISTS `x360p_report_student_lesson_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `status` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_report_summary`
--

DROP TABLE IF EXISTS `x360p_report_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_report_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构id',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区id',
  `customer_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '意向客户名单数',
  `order_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '报名数(订单数)',
  `lesson_hour_consume` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '消耗课时数',
  `lesson_hour_remain` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '剩余课时数',
  `money_consume` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '消耗课时金额',
  `money_remain` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '剩余课时金额',
  `lesson_hour_reward` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '教师课酬课时数',
  `money_reward` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '教师课酬金额',
  `income_total` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '收款合计',
  `arrearage_total` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '欠款合计',
  `refund_total` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款合计',
  `outlay_total` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '支出合计',
  `year` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '年份',
  `month` int(2) unsigned NOT NULL DEFAULT '0' COMMENT '月份',
  `week` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周数',
  `day` int(2) unsigned NOT NULL DEFAULT '0',
  `int_day` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '20171223',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bid_int_day` (`bid`,`int_day`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `x360p_report_trial`
--

DROP TABLE IF EXISTS `x360p_report_trial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_review`
--

DROP TABLE IF EXISTS `x360p_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_review` (
  `rvw_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '课评ID',
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
  `view_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  `share_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`rvw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课评主表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_review_file`
--

DROP TABLE IF EXISTS `x360p_review_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_review_file` (
  `rf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `rvw_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课评ID',
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
  PRIMARY KEY (`rf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课评关联附件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_review_student`
--

DROP TABLE IF EXISTS `x360p_review_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_review_student` (
  `rs_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `rvw_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课评ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型:0班课，1:1对1,2:1对多，分别对应接下来的3个字段',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '日期',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课结束时间',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `score` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分数/星星数',
  `score1` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分1',
  `score2` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分2',
  `score3` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分3',
  `score4` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分4',
  `score5` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分项5',
  `score6` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分项6',
  `score7` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分项7',
  `score8` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分项8',
  `score9` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分项9',
  `detail` text,
  `view_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  `share_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`rs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课评学员记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_review_tpl_define`
--

DROP TABLE IF EXISTS `x360p_review_tpl_define`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_review_tpl_define` (
  `rtd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `rts_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课评模板配置ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`rtd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='默认课评模板定义表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_review_tpl_setting`
--

DROP TABLE IF EXISTS `x360p_review_tpl_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_review_tpl_setting` (
  `rts_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '点评模板名称',
  `setting` text NOT NULL COMMENT 'JSON结构的设置',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`rts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课评模板配置表';
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
-- Table structure for table `x360p_service_push_task`
--

DROP TABLE IF EXISTS `x360p_service_push_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_service_push_task` (
  `spt_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `object_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '对象类型:0客户，1学员,2班级',
  `app_id` varchar(64) DEFAULT '' COMMENT '公众号AppId',
  `openid` varchar(64) DEFAULT '' COMMENT '粉丝openid',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `remark` varchar(255) DEFAULT '' COMMENT '推送备注',
  `url` varchar(255) DEFAULT '' COMMENT '推送的URL地址',
  `content_type` varchar(32) DEFAULT '' COMMENT '内容类型 file_package,link,page 三种类型',
  `rel_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联内容ID',
  `is_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推送',
  `push_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送时间',
  `push_success_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送数量',
  `push_failure_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送失败数量',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`spt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务推送任务表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_service_record`
--

DROP TABLE IF EXISTS `x360p_service_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_service_record` (
  `sr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `object_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '对象类型:0客户，1学员,2班级',
  `st_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '服务类型:任务操作字典ID:service_type = st',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成日期',
  `int_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成时间',
  `url` varchar(255) DEFAULT '' COMMENT '关联URL',
  `is_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推送',
  `rel_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联记录ID',
  `content` text COMMENT '服务于内容',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '员工ID',
  `student_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '覆盖学生人数',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`sr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务日程表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_service_record_file`
--

DROP TABLE IF EXISTS `x360p_service_record_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_service_record_file` (
  `srf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课标ID',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '文件id',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件URL',
  `file_type` varchar(16) DEFAULT '' COMMENT '文件类型',
  `file_size` bigint(20) unsigned DEFAULT '0' COMMENT '文件大小',
  `file_name` varchar(64) DEFAULT '' COMMENT '文件名',
  `media_type` char(50) DEFAULT NULL COMMENT '媒体类型',
  `duration` varchar(255) DEFAULT NULL COMMENT '音频时长',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`srf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务记录文件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_service_task`
--

DROP TABLE IF EXISTS `x360p_service_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_service_task` (
  `st_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `object_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '对象类型:0客户，1学员,2班级',
  `st_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '服务类型:任务操作字典ID:service_type = st',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务完成日期截止',
  `int_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务完成时间截止',
  `own_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务完成人员工ID',
  `remark` text COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0待办，1完成，-1取消',
  `create_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员工ID，0表示系统创建',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`st_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务日程表';
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
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT 'bid',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT 'sid',
  `cu_id` int(11) NOT NULL DEFAULT '0' COMMENT 'cu_id',
  `mcl_id` int(11) NOT NULL DEFAULT '0' COMMENT '市场名单',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '员工id',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '短信内容',
  `tpl_id` varchar(255) DEFAULT NULL,
  `tpl_data` varchar(255) DEFAULT NULL,
  `mgh_id` int(11) NOT NULL DEFAULT '0' COMMENT '群发id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发送成功:0成功，其余失败',
  `is_sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0为等待发送，1已经发送',
  `error` varchar(255) DEFAULT NULL COMMENT '发送错误消息',
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
-- Table structure for table `x360p_sms_tpl_define`
--

DROP TABLE IF EXISTS `x360p_sms_tpl_define`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_sms_tpl_define` (
  `std_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(128) DEFAULT '' COMMENT '模板名称',
  `service_name` varchar(64) DEFAULT '' COMMENT '短信服务商ID',
  `tpl_id` varchar(32) DEFAULT '' COMMENT '模板ID',
  `tpl_define` text COMMENT '模板定义,json结构',
  `apply_tpl` varchar(512) DEFAULT '' COMMENT '运营商短信模板',
  `business_type` varchar(32) DEFAULT '' COMMENT '业务类型',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`std_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短信模板配置';
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
  `grade_update_int_ym` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '年级更新年月',
  `school_class` varchar(32) NOT NULL DEFAULT '' COMMENT '学校班级',
  `school_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学校ID',
  `first_tel` varchar(16) NOT NULL DEFAULT '',
  `first_family_name` varchar(32) NOT NULL DEFAULT '' COMMENT '第一亲属姓名',
  `first_family_rel` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0:未设置,1:自己，2：爸爸，3：妈妈，4：其他',
  `first_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '首选联系人的账号uid',
  `second_family_name` varchar(32) NOT NULL DEFAULT '' COMMENT '第2亲属姓名',
  `second_family_rel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0：未设置，1:自己，2：爸爸，3：妈妈，4：其他',
  `second_tel` varchar(16) NOT NULL DEFAULT '' COMMENT '第2电话',
  `second_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '第二联系人的账号uid',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '分配的学管师',
  `sno` varchar(32) NOT NULL DEFAULT '' COMMENT '学号',
  `card_no` varchar(32) DEFAULT NULL COMMENT '考勤卡号',
  `money` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '电子钱包余额',
  `credit` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '学员积分',
  `credit2` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '消费积分',
  `vip_level` int(11) NOT NULL DEFAULT '-1' COMMENT 'VIP等级',
  `service_level` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '服务级别',
  `last_attendance_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后考勤时间',
  `is_lost` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否流失(1为是,0为否,由用户标记)',
  `status` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '学员状态：1.正常状态，20.停课状态，30.休学状态，90.已退学',
  `quit_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '退学原因',
  `student_type` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '学员类型：0.体验学员 1.正式学员 2.vip学员',
  `option_fields` text COMMENT '学员的额外字段记录',
  `student_lesson_times` int(11) NOT NULL DEFAULT '0' COMMENT '购买的总课次',
  `student_lesson_remain_times` int(11) NOT NULL DEFAULT '0' COMMENT '学生购买的课程总剩余次数',
  `student_lesson_hours` decimal(11,2) DEFAULT '0.00' COMMENT '课时总数',
  `student_lesson_remain_hours` decimal(11,2) DEFAULT '0.00' COMMENT '剩余课时数',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
  `is_demo_transfered` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课转换',
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
  `absence_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1: 考勤产生的缺勤，2：取消排课产生的缺勤',
  `slv_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '请假记录ID，student_leave表主键',
  `chapter_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次序号(对应的课程课次)',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级',
  `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `lesson_type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多,3:研学旅行团)',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课记录ID',
  `catt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'class_attendance考勤记录ID',
  `satt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生的考勤id（student_lesson）',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `second_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '助教老师ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '整数天(20170501)',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间整数(1700)',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间整数',
  `is_leave` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否请假(0:未请假,1:有请假)',
  `is_consume` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否扣课时',
  `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣课时数',
  `remark` varchar(255) DEFAULT '' COMMENT '缺课原因',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有补课(0:未补课,1:已安排，2:已补课结束（已考勤）)',
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
-- Table structure for table `x360p_student_artwork`
--

DROP TABLE IF EXISTS `x360p_student_artwork`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_artwork` (
  `sart_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '指导老师',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '班级',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学员',
  `art_name` varchar(255) DEFAULT '' COMMENT '作品名称',
  `art_desc` text COMMENT '作品介绍',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`sart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生作品记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_artwork_attachment`
--

DROP TABLE IF EXISTS `x360p_student_artwork_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_artwork_attachment` (
  `sarta_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `sart_id` int(11) NOT NULL DEFAULT '0' COMMENT '作品id',
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
  PRIMARY KEY (`sarta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生作品附件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_artwork_review`
--

DROP TABLE IF EXISTS `x360p_student_artwork_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_artwork_review` (
  `sartr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sart_id` int(11) NOT NULL DEFAULT '0' COMMENT '作品id',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点评老师',
  `content` text COMMENT '点评内容',
  `star` tinyint(4) NOT NULL DEFAULT '0' COMMENT '星级',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`sartr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生作品记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_attend_school_log`
--

DROP TABLE IF EXISTS `x360p_student_attend_school_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_attend_school_log` (
  `sasl_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT '校区id',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生id',
  `int_day` int(11) DEFAULT NULL COMMENT '到校日期',
  `is_attend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否到校',
  `attend_time` int(11) DEFAULT '0' COMMENT '到校时间',
  `is_leave` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否离校',
  `leave_time` int(11) NOT NULL DEFAULT '0' COMMENT '离校时间',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`sasl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生到离校记录';
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
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级',
  `sg_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目级别',
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
  `is_late` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否迟到(0:未迟到,1:迟到),只有刷卡考勤才会有这个字段',
  `is_leave` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否请假(0:未请假,1:有请假)',
  `is_makeup` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否补课(0:正常,1:补课)',
  `is_consume` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否计算课消，课耗',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
  `consume_lesson_hour` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣除课时',
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
  `cru_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '积分规则ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1,增加；2减少',
  `cate` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '积分类型： 1学习积分，2消费积分',
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
-- Table structure for table `x360p_student_debit_card`
--

DROP TABLE IF EXISTS `x360p_student_debit_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_debit_card` (
  `sdc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` varchar(255) DEFAULT '' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `dc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '储值卡ID',
  `buy_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '购买来源:0为订单购买,1为余额兑换',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单条目ID',
  `start_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '初始金额',
  `remain_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '剩余金额',
  `is_used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用0未使用，1部分使用，2全部使用',
  `buy_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买日期',
  `expire_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期日期',
  `is_expired` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否过期',
  `upgrade_vip_level` int(11) DEFAULT '0' COMMENT '升级到会员级别',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`sdc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_exam`
--

DROP TABLE IF EXISTS `x360p_student_exam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_exam_score`
--

DROP TABLE IF EXISTS `x360p_student_exam_score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_exam_subject_score`
--

DROP TABLE IF EXISTS `x360p_student_exam_subject_score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级',
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
  `ma_id` int(11) NOT NULL DEFAULT '0' COMMENT '补课id',
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
  `sj_ids` varchar(255) DEFAULT '' COMMENT '可用科目',
  `fit_grade_start` int(11) NOT NULL DEFAULT '0' COMMENT '适应年级start',
  `fit_grade_end` int(11) NOT NULL DEFAULT '0' COMMENT '适应年级end',
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多)',
  `price_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '计费模式（1:按课次计费,2:课时收费,3:按时间收费）',
  `is_package` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否课时包',
  `origin_lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原始课次数',
  `present_lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送课次数',
  `lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总的课次数',
  `origin_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原始购买的的总课时数（lesson表：lesson_chapter * unit_hours）',
  `present_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送的课时数',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总的课时数：origin_lesson_hours + present_lesson_hours',
  `import_lesson_hours` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '导入课时数',
  `refund_lesson_hours` decimal(11,2) DEFAULT '0.00' COMMENT '退费课时数',
  `transfer_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结转总课时',
  `trans_out_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '转出课时数',
  `trans_in_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '转入课时数',
  `expire_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '有效期至',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级id（暂时不需要 yr）',
  `ac_status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '分班状态(assign class status,0:未分班,1:部分分班,2:已分班)',
  `need_ac_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '需要分班数量',
  `ac_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已分班数量',
  `lesson_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程状态(0:未开始上课,1:上课中,2:已结课)',
  `is_stop` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否停课(0为否，1为是)',
  `use_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已上课次',
  `remain_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余课次',
  `use_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '已消耗课时数',
  `remain_lesson_hours` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '剩余课时数',
  `last_attendance_time` int(11) unsigned DEFAULT '0' COMMENT '最后考勤时间',
  `remain_arrange_times` int(11) DEFAULT '-99999' COMMENT '剩余待排课次数(默认创建时为lesson_times)',
  `remain_arrange_hours` decimal(11,2) NOT NULL DEFAULT '-99999.00' COMMENT '剩余排课课时',
  `start_int_day` int(11) NOT NULL DEFAULT '0' COMMENT '报名日期',
  `end_int_day` int(11) NOT NULL DEFAULT '0' COMMENT '课程结束日期',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
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
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'student_lesson表主键',
  `slil_id` int(11) NOT NULL DEFAULT '0' COMMENT '导入的记录id',
  `change_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '变化类型：1考勤，2自由登记课耗',
  `remark` varchar(255) DEFAULT NULL COMMENT '课耗备注',
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
  `edu_eid` int(11) unsigned DEFAULT '0' COMMENT '导师ID',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课时数',
  `lesson_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课次总时间长度（单位：分钟）',
  `lesson_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '课时金额',
  `consume_type` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课消类型:0课时课消,1:副课时课消,2:缺课课消,3:违约课消',
  `source_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '来源类型:1:课时,2:电子钱包',
  `is_pay` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否付款',
  `is_present` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否赠送:1为赠送',
  `is_makeup` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是补课，默认是正常排课',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`slh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生课时消耗记录表,一般情况下他与student_attendance表记录是一对一的冗余关系；特殊情况下可以不考勤，直接扣课时。';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_lesson_import_log`
--

DROP TABLE IF EXISTS `x360p_student_lesson_import_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_lesson_import_log` (
  `slil_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生id',
  `sj_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '适用科目',
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '科目id',
  `sl_id` int(11) NOT NULL DEFAULT '0' COMMENT 'sl_id',
  `lesson_hours` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '导入课时数量',
  `unit_lesson_hour_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '单课时金额',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`slil_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生课时导入记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_lesson_operate`
--

DROP TABLE IF EXISTS `x360p_student_lesson_operate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_lesson_operate` (
  `slo_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `op_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '操作类型1：手动赠送,2:结转，3:退费，4：购买时赠送',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID,可以为0',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单条目ID，可以为0',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员课时记录ID',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送课时数',
  `unit_price` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '结转单价',
  `lesson_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结转金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`slo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员课时操作记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_lesson_stop`
--

DROP TABLE IF EXISTS `x360p_student_lesson_stop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_lesson_stop` (
  `sls_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '停课ID自增',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `stop_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '停课类型（0停课，1休学）',
  `stop_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '停课日期',
  `recover_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '复课日期',
  `stop_remark` varchar(255) DEFAULT '' COMMENT '停课原因',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `stop_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '该条记录的真正停课执行时间,默认为0',
  `expired_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '该条停课记录的过期时间,复课之后产生',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`sls_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员停课休学记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_log`
--

DROP TABLE IF EXISTS `x360p_student_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_log` (
  `slg_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0',
  `bid` int(11) unsigned NOT NULL DEFAULT '0',
  `sid` int(11) unsigned NOT NULL DEFAULT '0',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0',
  `op_type` int(11) unsigned NOT NULL DEFAULT '0' COMMENT ' 操作类型,详情见文档',
  `lid` int(11) unsigned NOT NULL DEFAULT '0',
  `cid` int(11) unsigned NOT NULL DEFAULT '0',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '操作描述',
  `extra_param` text COMMENT '额外的参数 json格式',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`slg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员操作日志表';
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
  `business_type` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '业务类型:(1:结转,2:退费,3:充值, 4:下单, 5:订单续费 ,10 导入,11:用户手动增加， 12手动减少)',
  `business_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '业务关联ID,结转ID,退费ID,充值ID)',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `sdc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员储值卡记录ID',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单条目ID',
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
-- Table structure for table `x360p_student_return_visit`
--

DROP TABLE IF EXISTS `x360p_student_return_visit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_return_visit` (
  `srv_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生ID',
  `is_connect` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效沟通',
  `followup_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '跟进方式字典ID(QQ,电话,微信)',
  `content` text NOT NULL COMMENT '回访内容',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '实际回访日期',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回访员工ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`srv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='学生回访表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_student_return_visit_attachment`
--

DROP TABLE IF EXISTS `x360p_student_return_visit_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_return_visit_attachment` (
  `srva_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `srv_id` int(11) NOT NULL DEFAULT '0' COMMENT '回访id',
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
  PRIMARY KEY (`srva_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='学员回访记录附件表';
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
-- Table structure for table `x360p_study_situation`
--

DROP TABLE IF EXISTS `x360p_study_situation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_study_situation` (
  `ss_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `short_id` char(16) NOT NULL DEFAULT '' COMMENT '短ID唯一',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `qid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '问卷ID',
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `content` text COMMENT 'json数据',
  `create_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建人eid',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成日期',
  `int_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '评语',
  `lbs_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学习方案ID',
  `is_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推送,1:推送短信,2:推送微信,3:短信、微信都推送',
  `push_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送时间',
  `is_query` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否查询,0为未查询，1为1查询',
  `query_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查询时间',
  `query_openid` varchar(32) DEFAULT '' COMMENT '查询openid',
  `is_view` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否查看',
  `view_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查看时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ss_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学情记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_study_situation_item`
--

DROP TABLE IF EXISTS `x360p_study_situation_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_study_situation_item` (
  `ssi_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `ss_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学情服务ID',
  `qid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '问卷ID',
  `qi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '问卷题目ID',
  `answer` text COMMENT '答案JSON结构',
  `score` decimal(11,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '得分',
  `is_unknown` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否位未知',
  `next_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下次约定了解日期',
  `is_parent_focus` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否家长关注项',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ssi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学情记录条目表';
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
  `unit_price` decimal(11,2) DEFAULT '0.00' COMMENT '课时单价',
  `per_lesson_hour_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '每课时多少分钟',
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
-- Table structure for table `x360p_subject_grade`
--

DROP TABLE IF EXISTS `x360p_subject_grade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_subject_grade` (
  `sg_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL,
  `bid` int(11) unsigned NOT NULL,
  `sj_id` int(11) unsigned NOT NULL COMMENT '科目id',
  `grade` int(11) unsigned NOT NULL COMMENT '科目等级',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '简短介绍',
  `unit_price` decimal(11,2) DEFAULT '0.00' COMMENT '课时单价',
  `per_lesson_hour_minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '每课时多少分钟',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`sg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='科目等级';
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
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `int_day` int(11) DEFAULT NULL COMMENT '业务日期',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '相关的学生id',
  `is_demo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课',
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
  `season` char(1) NOT NULL DEFAULT 'A' COMMENT '季节',
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
-- Table structure for table `x360p_transfer_hour_history`
--

DROP TABLE IF EXISTS `x360p_transfer_hour_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_transfer_hour_history` (
  `thh_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` varchar(255) DEFAULT '' COMMENT '校区ID',
  `from_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转出学员ID',
  `to_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转入学员ID',
  `from_sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转出学员课时记录ID',
  `to_sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转入学员课时记录ID',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课时数',
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_ids` varchar(255) DEFAULT '' COMMENT '可用科目',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级id',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`thh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_transfer_money_history`
--

DROP TABLE IF EXISTS `x360p_transfer_money_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_transfer_money_history` (
  `tmh_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` varchar(255) DEFAULT '' COMMENT '校区ID',
  `from_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转出学员ID',
  `to_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转入学员ID',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`tmh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
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
  `catt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'class_attendance的主键',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `is_arrive` tinyint(1) unsigned DEFAULT '0' COMMENT '0：未到 1：已到',
  `remark` varchar(255) DEFAULT NULL COMMENT '未试听原因',
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
  `is_ext` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是来自dss导入的员工，0：否，1：是',
  `ext_password` varchar(32) DEFAULT NULL COMMENT '员工在dss系统的登录密码',
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
-- Table structure for table `x360p_user_per`
--

DROP TABLE IF EXISTS `x360p_user_per`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_user_per` (
  `up_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `pers` text COMMENT '权限',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`up_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='用户单独权限表';
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
-- Table structure for table `x360p_wechat_tpl_define`
--

DROP TABLE IF EXISTS `x360p_wechat_tpl_define`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wechat_tpl_define` (
  `wtd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(128) DEFAULT '' COMMENT '模板名称',
  `tpl_id` varchar(64) DEFAULT '' COMMENT '模板ID',
  `tpl_define` text COMMENT '模板定义,json结构',
  `business_type` varchar(32) DEFAULT '' COMMENT '业务类型(可选无)',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`wtd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信消息模板配置';
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
  `appid` varchar(255) DEFAULT '' COMMENT '公众号的appid',
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
  `tags` text COMMENT '公众号粉丝标签，json结构',
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
-- Table structure for table `x360p_wxmp_fans`
--

DROP TABLE IF EXISTS `x360p_wxmp_fans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_fans` (
  `fans_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id,家长uid',
  `employee_uid` int(11) DEFAULT '0' COMMENT '机构员工uid，微信绑定既可以家长，也同时可以员工',
  `appid` varchar(255) NOT NULL DEFAULT '' COMMENT '公众号的appid',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `original_id` varchar(255) NOT NULL DEFAULT '' COMMENT '用户绑定的公众号的原始id',
  `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '该粉丝是否是系统公众号：学习管家服务号的粉丝',
  `nickname` varchar(128) NOT NULL DEFAULT '' COMMENT '微信昵称',
  `avatar` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '微信图像',
  `headimgurl` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `province` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '省份',
  `city` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '城市',
  `country` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '国家',
  `language` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '语言',
  `privilege_0` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT '运营商',
  `subscribe` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否关注，默认关注',
  `subscribe_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关注时间',
  `unionid` varchar(255) NOT NULL DEFAULT '' COMMENT '只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注',
  `groupid` varchar(255) NOT NULL DEFAULT '' COMMENT '用户所在的分组ID（兼容旧的用户分组接口）',
  `tagid_list` varchar(255) NOT NULL DEFAULT '' COMMENT '用户被打上的标签ID列表',
  `unsubscribe_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '取消关注时间',
  `last_connect_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次联系时间，主要是发送客服消息要在48小时内',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`fans_id`),
  UNIQUE KEY `unique_openid` (`openid`) USING BTREE COMMENT '设置openid的唯一索引',
  KEY `normal_appid` (`appid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户自己的公众号的粉丝表，openid与user表的关联';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_wxmp_fans_message`
--

DROP TABLE IF EXISTS `x360p_wxmp_fans_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_fans_message` (
  `wfm_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `wxmp_id` int(11) NOT NULL DEFAULT '0',
  `appid` varchar(255) NOT NULL DEFAULT '',
  `request_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '公众号粉丝的uid（如果绑定了账号的情况有值）',
  `fans_id` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `response_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回复粉丝信息的员工uid',
  `msg_type` char(30) NOT NULL DEFAULT '' COMMENT '消息类型',
  `event` varchar(30) DEFAULT '' COMMENT '当msg_type=event时的事件类型',
  `data_json` text NOT NULL COMMENT '微信的完整请求数据，json结构',
  `files_info` varchar(500) NOT NULL DEFAULT '' COMMENT '用户上传的媒体文件处理之后的文件信息,json结构',
  `msg_id` varchar(64) NOT NULL DEFAULT '' COMMENT '微信的消息id，64位整型',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`wfm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公众号与粉丝的聊天记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x360p_wxmp_fans_tag`
--

DROP TABLE IF EXISTS `x360p_wxmp_fans_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_fans_tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fans_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'wxmp_fans表主键',
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '微信服务器返回的tag_id',
  `appid` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mapping` (`fans_id`,`tag_id`),
  KEY `index_fans_id` (`fans_id`),
  KEY `index_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='粉丝标签表，用于联表查询获取某个标签下的粉丝';
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
  `menuid` int(11) NOT NULL DEFAULT '0' COMMENT '微信菜单id',
  `group_name` varchar(255) DEFAULT NULL COMMENT '菜单组名称',
  `buttons` text COMMENT '公众号菜单的json配置结构',
  `matchrule` text COMMENT '菜单对应的用户分组规划，即特定用户的显示菜单',
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

