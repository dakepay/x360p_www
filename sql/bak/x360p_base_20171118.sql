-- MySQL dump 10.13  Distrib 5.7.11, for Linux (x86_64)
--
-- Host: localhost    Database: x360p_base
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
  `start_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '期初余额',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认账户',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`aa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='会计账户表(每创建一个校区要自动创建一个关联的账户表)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_accounting_account`
--

LOCK TABLES `x360p_accounting_account` WRITE;
/*!40000 ALTER TABLE `x360p_accounting_account` DISABLE KEYS */;
INSERT INTO `x360p_accounting_account` VALUES (1,0,'现金',0,'',1,0,1.00,0.00,'',0,NULL,0,NULL,NULL),(2,0,'支付宝账户',2,'',1,1,0.00,0.00,'',0,NULL,0,NULL,NULL),(3,0,'微信账户',2,'',1,1,0.00,0.00,'',0,NULL,0,NULL,NULL),(4,0,'建行储蓄卡',1,'',1,1,0.00,0.00,'',0,NULL,0,NULL,NULL),(5,0,'现金帐户',0,'40',0,1,0.00,0.00,'',1,1510362612,18,NULL,NULL),(6,0,'现金1',0,'40',0,1,0.00,0.00,'',0,NULL,0,NULL,NULL),(7,0,'现金',0,'35',0,1,0.00,89114.07,'',1,1510362612,18,NULL,NULL);
/*!40000 ALTER TABLE `x360p_accounting_account` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM AUTO_INCREMENT=8357 DEFAULT CHARSET=utf8mb4 COMMENT='系统操作日志表(记录系统操作日志)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_action_log`
--

LOCK TABLES `x360p_action_log` WRITE;
/*!40000 ALTER TABLE `x360p_action_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_action_log` ENABLE KEYS */;
UNLOCK TABLES;

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
  `sort` int(11) unsigned DEFAULT '0' COMMENT '排序',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `ext_id` varchar(20) DEFAULT NULL COMMENT 'dss3.0 校区id',
  PRIMARY KEY (`bid`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COMMENT='校区表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_branch`
--

LOCK TABLES `x360p_branch` WRITE;
/*!40000 ALTER TABLE `x360p_branch` DISABLE KEYS */;
INSERT INTO `x360p_branch` VALUES (1,0,'福田校区','福田','2','0755-82602960',0,19,291,3062,3062,'罗湖分校',0,1498124456,0,1507281197,0,0,123123,NULL),(2,0,'浪腾分校','浪腾1号','2','075582602960',0,3,73,1126,1126,'全国分校',0,1498124756,0,1498300073,0,0,123123,NULL),(3,0,'浪腾广州分校','广州浪腾','1','075582602960',0,2,56,890,890,'广州',1,1498125230,0,1504841303,0,0,123123,NULL),(4,0,'浪腾北京分校','北京浪腾','2','075582602960',0,1,38,577,577,'北京aaa',2,1498125306,0,1504841303,0,0,123123,NULL),(5,0,'浪腾上海分校','上海浪腾','2','075582602960',0,9,144,1819,1819,'上海',0,1498125611,0,1498299811,0,0,123123,NULL),(6,0,'浪腾武汉校区','武汉浪腾','1','075582602960',0,17,258,2812,2812,'武汉',0,1498127036,1,1498299676,0,0,123123,NULL),(7,0,'浪腾云南分校','云南浪腾','1','075582602960',0,2,56,890,890,'云南',0,1498127216,1,1498130807,1,1,123123,NULL),(8,0,'浪腾长沙分校','长沙浪腾','2','075582602960',0,2,57,904,904,'长沙',0,1498127375,1,1498130110,1,1,123123,NULL),(9,0,'罗湖校区','罗湖3','1','0755-82602960',0,19,291,3062,3062,'罗湖百汇大厦',0,1498616287,1,1498616287,0,0,123123,NULL),(10,0,'坂田校区','坂田','1','0755-82602960',0,19,291,3063,3063,'坂田',0,1499077008,1,1499077008,0,0,123123,NULL),(20,0,'龙洞教学中心','龙洞','1','020-37235083',0,19,289,3040,3040,'广州市天河区天源路1111号二楼204室',0,1500014189,1,1500014189,0,0,123123,NULL),(24,0,'花都天贵教学中心','花都天贵','1','020-36932116',0,19,289,3044,3044,'花都区新华街天贵路88号雅怡商务大厦2楼',0,1500014330,1,1500014330,0,0,123123,NULL),(25,0,'客村教学中心','客村','1','020-34027712',0,19,289,3041,3041,'海珠区新港中路356号丽影广场A区五楼',0,1500014330,1,1500014330,0,0,123123,NULL),(26,0,'白云教学中心','白云','1','020-87248570',0,19,289,3043,3043,'白云区广州大道北白灰场南路1号京隆大厦301',0,1500014330,1,1500014330,0,0,123123,NULL),(27,0,'测试校区001','001','1','075-4564358',0,2,56,890,890,'EEaa',0,1500017390,18,1500017390,0,0,123123,NULL),(28,0,'阳光喔罗湖校区','罗湖','1','020-22112321',0,19,289,3042,3042,'五羊路1号华晟大厦3楼(近中山三院)',0,1500018951,18,1500431454,1,18,123123,NULL),(29,0,'测试校区003','003','1','0755-82602960',2,19,291,3062,3062,'罗湖分校',0,1500019691,18,1501499258,1,18,123123,NULL),(30,0,'罗湖004','罗湖004','1','0755-88685686',3,1,37,567,567,'东大街',0,1501499361,18,1507281907,1,0,123123,NULL),(31,0,'深圳010','深圳010','2','0396-56895623',3,3,74,1150,1150,'东大街',0,1501568741,18,1507281884,1,0,123123,NULL),(32,0,'百汇','百汇','1','0786-13545652',0,3,76,1172,1172,'百汇',0,1507885620,18,1507885620,0,0,123123,NULL),(33,0,'坂田分校','坂田分校','1','',0,0,0,0,0,'',0,1508237729,18,1508237729,0,0,123123,NULL),(34,0,'福田分校','福田分校','1','',0,0,0,0,0,'',0,1508237737,18,1508237737,0,0,123123,NULL),(35,0,'龙岗大学','龙岗','1','0795-36365464',0,0,0,0,0,'五和',0,1508313545,18,1508379570,0,0,NULL,NULL),(36,0,'法国分校','法国','1','023-54544521',0,2,56,890,890,'法国艾利斯顿',0,1508323062,18,1508381686,0,0,NULL,NULL),(37,0,'福田分校','福田分校','1','',0,0,0,0,0,'',0,1508381582,18,1508381582,0,0,NULL,NULL),(38,0,'罗湖1号','罗湖1号','1','',0,0,0,0,0,'',0,1508393554,18,1508393554,0,0,NULL,NULL),(39,0,'加里敦分校','加里敦分校','1','',0,0,0,0,0,'',0,1508393583,18,1508393583,0,0,NULL,NULL),(40,0,'百汇分校','百汇分校','1','',0,0,0,0,0,'',0,1510362612,0,1510362612,0,0,NULL,NULL);
/*!40000 ALTER TABLE `x360p_branch` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_branch_employee`
--

LOCK TABLES `x360p_branch_employee` WRITE;
/*!40000 ALTER TABLE `x360p_branch_employee` DISABLE KEYS */;
INSERT INTO `x360p_branch_employee` VALUES (2,0,1,48,0,0,NULL,0,0,NULL),(3,0,1,49,0,0,NULL,0,0,NULL),(4,0,1,50,0,0,NULL,0,0,NULL),(5,0,1,51,0,0,NULL,0,0,NULL),(6,0,2,51,0,0,NULL,0,0,NULL),(7,0,1,52,0,0,NULL,0,0,NULL),(8,0,2,52,0,0,NULL,0,0,NULL),(24,0,1,60,0,0,NULL,0,0,NULL),(30,0,1,66,0,0,NULL,0,0,NULL),(31,0,3,67,0,0,NULL,0,0,NULL),(32,0,4,67,0,0,NULL,0,0,NULL),(33,0,2,68,0,0,NULL,0,0,NULL),(34,0,32,69,0,0,NULL,0,0,NULL),(35,0,1,70,0,0,NULL,0,0,NULL),(37,0,36,72,0,0,NULL,0,0,NULL),(40,0,38,75,0,0,NULL,0,0,NULL),(42,0,37,77,0,0,NULL,0,0,NULL),(43,0,35,78,0,0,NULL,0,0,NULL),(44,0,35,79,0,0,NULL,0,0,NULL),(45,0,35,80,0,0,NULL,0,0,NULL),(46,0,36,81,0,0,NULL,0,0,NULL),(47,0,35,81,0,0,NULL,0,0,NULL),(48,0,39,82,0,0,NULL,0,0,NULL),(49,0,38,82,0,0,NULL,0,0,NULL),(50,0,37,83,0,0,NULL,0,0,NULL),(51,0,36,83,0,0,NULL,0,0,NULL),(52,0,37,84,0,0,NULL,0,0,NULL),(53,0,35,84,0,0,NULL,0,0,NULL),(54,0,35,85,0,0,NULL,0,0,NULL),(55,0,36,85,0,0,NULL,0,0,NULL);
/*!40000 ALTER TABLE `x360p_branch_employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_cart`
--

DROP TABLE IF EXISTS `x360p_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_cart` (
  `cart_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `gid` int(11) unsigned NOT NULL COMMENT '商品id',
  `lid` int(11) unsigned NOT NULL COMMENT '课程id',
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `sid` int(11) unsigned NOT NULL COMMENT '学生id',
  `quantity` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '商品数量',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态（1：正常， 0：商品已下架或过期）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`cart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='购物车';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_cart`
--

LOCK TABLES `x360p_cart` WRITE;
/*!40000 ALTER TABLE `x360p_cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_cart` ENABLE KEYS */;
UNLOCK TABLES;

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
  `lesson_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课次数',
  `lesson_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前上课进度次数',
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COMMENT='班级表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class`
--

LOCK TABLES `x360p_class` WRITE;
/*!40000 ALTER TABLE `x360p_class` DISABLE KEYS */;
INSERT INTO `x360p_class` VALUES (1,0,0,0,'火箭班','',1,103,1,0,1,1,503,30,0.00,30,0,0,0,2017,'Q',20171001,20171230,0,'',0,0,NULL,0,NULL,0,1900,2000),(2,0,0,35,'语文A班22','YW01',123,105,81,0,0,25,16,2,0.00,16,0,0,0,2017,'Q',1509379200,1511971200,0,'',1509159079,18,1509532049,0,NULL,0,0,0),(3,0,0,35,'语文-按期2017秋季周二17:30~19:30','17Q-2-17301930',124,103,81,0,0,25,16,0,0.00,14,0,0,0,2017,'Q',1509379200,1517328000,0,'',1509445381,18,1509448295,1,1509448295,18,1730,1930),(4,0,0,35,'大学语文','DXYW',123,103,81,79,0,25,20,1,0.00,7,0,0,0,2017,'Q',1509379200,1511971200,0,'',1509448285,18,1509503444,0,NULL,0,0,0),(6,0,0,35,'语文2017秋季周二17:30~19:30','17Q-2-17301930',123,103,85,0,0,26,16,0,0.00,14,0,0,0,2017,'Q',1506960000,1517414400,0,'',1509449160,18,1509523665,0,NULL,0,1730,1930),(7,0,0,35,'语文-按期2017秋季周二17:30~19:30','17Q-2-17301930',124,103,79,0,0,28,16,0,0.00,14,0,0,0,2017,'Q',1509638400,1511971200,0,'',1509681139,18,1509681139,0,NULL,0,1730,1930),(10,0,0,35,'语文2017秋季周一17:30~19:30','17Q-1-17301930',123,103,79,0,0,27,16,0,0.00,14,0,0,0,2017,'Q',1510070400,1522252800,0,'',1510047246,18,1510047246,0,NULL,0,1730,1930),(11,0,0,35,'考勤测试班Test','AttendanceTest',129,105,81,81,0,25,50,0,0.00,7,0,0,0,2017,'Q',1510243200,1541001600,0,'',1510295580,18,1510295580,0,NULL,0,0,0),(13,0,0,35,'TestDev班级','123',130,101,81,80,0,27,30,2,0.00,7,0,0,0,2017,'Q',1510243200,1541088000,0,'',1510296130,18,1510296130,0,NULL,0,0,0),(14,0,0,35,'李将军开车001','001',130,101,79,81,0,26,10,0,0.00,0,0,0,0,2017,'H',1509465600,1511971200,0,'',1510393295,18,1510393295,0,NULL,0,0,0),(20,0,0,35,'yaorui-atd-test','',141,102,79,80,0,25,5,3,0.00,0,0,0,0,2017,'H',1510502400,1514044800,0,'',1510627183,18,1510627183,0,NULL,0,0,0),(22,0,0,35,'yairui-atd-test','',141,102,81,0,0,26,10,2,0.00,0,0,0,0,2017,'H',1510675200,1511971200,0,'',1510718161,18,1510718161,0,NULL,0,0,0),(23,0,0,35,'yaorui-11-16-att','',141,102,27,28,0,27,5,1,0.00,14,0,0,0,2017,'H',1510243200,1511971200,0,'',1510822547,18,1510822547,0,NULL,0,0,0),(24,0,0,35,'数学-按课时-按课时2017寒假周一08:30~09:30','17H-1-08300930',132,105,79,0,0,28,16,2,0.00,10,0,0,0,2017,'H',1510848000,1514563200,0,'',1510882017,18,1510882017,0,NULL,0,830,930),(25,0,0,35,'att-test','',130,105,1,27,0,29,10,5,0.00,10,0,0,0,2017,'H',1510761600,1514649600,0,'',1510920856,18,1510968140,0,NULL,0,0,0),(26,0,0,35,'test1','',129,105,4,4,0,28,10,6,0.00,10,0,7,0,2017,'Q',1510934400,1511107200,0,'',1510923941,18,1510968423,0,NULL,0,0,0);
/*!40000 ALTER TABLE `x360p_class` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8mb4 COMMENT='考勤记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class_attendance`
--

LOCK TABLES `x360p_class_attendance` WRITE;
/*!40000 ALTER TABLE `x360p_class_attendance` DISABLE KEYS */;
INSERT INTO `x360p_class_attendance` VALUES (103,0,35,20,141,1,0,133,79,80,20171114,800,900,3,3,2,0,0,0,0,'yaorui-test-attendance-lesson-remark',1510628542,18,1510628542,0,NULL,0),(121,0,35,20,141,2,0,134,79,80,20171115,800,900,3,3,3,0,0,0,0,'',1510640263,18,1510640263,0,NULL,0),(122,0,35,20,141,3,0,135,79,80,20171121,800,900,3,3,7,0,0,0,0,'',1510640793,18,1510640793,0,NULL,0),(123,0,35,20,141,4,0,136,79,80,20171122,800,900,3,3,3,0,0,0,0,'',1510640979,18,1510640979,0,NULL,0),(124,0,35,20,141,5,0,137,79,80,20171128,800,900,3,3,1,0,0,0,0,'',1510641340,18,1510641340,0,NULL,0),(125,0,35,20,141,6,0,138,79,80,20171129,800,900,3,3,3,0,0,0,0,'',1510642449,18,1510642449,0,NULL,0),(126,0,35,20,141,7,0,139,79,80,20171205,800,900,3,3,2,0,0,0,0,'',1510642523,18,1510642523,0,NULL,0),(127,0,35,20,141,8,0,140,79,80,20171206,800,900,3,6,3,0,0,0,3,'',1510643218,18,1510643218,0,NULL,0),(128,0,35,20,141,9,0,141,79,80,20171212,800,900,3,3,1,0,0,0,0,'',1510645307,18,1510645307,0,NULL,0),(129,0,35,20,141,10,0,142,79,80,20171213,800,900,3,3,2,0,0,0,0,'',1510656653,18,1510656653,0,NULL,0),(130,0,35,20,141,12,0,144,79,80,20171220,800,900,3,3,1,0,0,0,0,'',1510657070,18,1510657070,0,NULL,0),(131,0,35,20,141,11,0,143,79,80,20171219,800,900,3,3,1,0,0,0,0,'',1510661023,18,1510661023,0,NULL,0),(133,0,35,22,141,1,0,169,81,0,20171122,800,900,2,2,2,0,0,0,0,'',1510718279,18,1510718279,0,NULL,0),(141,0,35,22,141,5,0,173,81,0,20171129,800,900,2,2,4,0,0,0,0,'',1510825936,18,1510825936,0,NULL,0),(142,0,35,22,141,2,0,170,81,0,20171122,900,1000,2,2,2,1,0,0,0,'',1510826536,18,1510826536,0,NULL,0),(143,0,35,23,141,1,0,177,27,28,20171113,830,930,1,1,2,0,0,0,0,'',1510827605,18,1510827605,0,NULL,0),(144,0,35,23,141,2,0,178,27,28,20171114,800,900,1,1,8,3,0,0,0,'',1510827752,18,1510827752,0,NULL,0),(145,0,35,23,141,6,0,182,27,28,20171118,900,1000,1,1,1,0,0,0,0,'',1510883640,18,1510883640,0,NULL,0),(148,0,35,23,141,5,0,181,27,28,20171117,900,1000,1,1,1,0,0,0,0,'',1510892404,18,1510904150,1,1510904150,18),(150,0,35,23,141,7,0,183,27,28,20171119,900,1000,1,1,2,0,0,0,0,'',1510902242,18,1510903287,1,1510903287,18),(151,0,35,23,141,3,0,179,27,28,20171115,1200,1300,1,1,2,0,0,0,0,'',1510904880,18,1510904880,0,NULL,0),(152,0,35,23,141,4,0,180,27,28,20171116,900,1000,1,1,0,0,0,0,0,'',1510906415,18,1510906415,0,NULL,0),(157,0,35,25,130,3,0,203,1,27,20171117,2015,2115,3,3,5,0,0,0,0,'',1510921399,18,1510921399,0,NULL,0),(158,0,35,25,130,4,0,204,1,27,20171118,900,1000,5,5,4,0,1,0,0,'',1510966819,18,1510966819,0,NULL,0),(159,0,35,26,129,1,105,209,4,27,20171118,1000,1030,6,6,1,0,1,0,0,'',1510970403,18,1510970403,0,NULL,0);
/*!40000 ALTER TABLE `x360p_class_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_class_listen_apply`
--

DROP TABLE IF EXISTS `x360p_class_listen_apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_class_listen_apply` (
  `cla_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '试听申请ID',
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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COMMENT='班级家长听课申请记录表(学生家长申请听课的记录)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class_listen_apply`
--

LOCK TABLES `x360p_class_listen_apply` WRITE;
/*!40000 ALTER TABLE `x360p_class_listen_apply` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_class_listen_apply` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COMMENT='班级日志';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class_log`
--

LOCK TABLES `x360p_class_log` WRITE;
/*!40000 ALTER TABLE `x360p_class_log` DISABLE KEYS */;
INSERT INTO `x360p_class_log` VALUES (1,0,2,1,'User 创建了班级',NULL,'{\"class_name\":\"语文A班\",\"class_no\":\"YW01\",\"year\":\"2017\",\"season\":\"Q\",\"lid\":123,\"sj_id\":103,\"bid\":35,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":16,\"lesson_times\":12,\"cr_id\":26,\"schedule\":[{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":26}],\"course_arrange\":0,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"2\"}',1509159079,18,1509159079,0,NULL,0),(2,0,3,1,'User 创建了班级',NULL,'{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"lid\":124,\"sj_id\":103,\"sj_ids\":[],\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"class_name\":\"语文-按期2017秋季周二17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"year\":\"2017\",\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2018-01-31 00:00:00\",\"lesson_times\":14,\"plan_student_nums\":16,\"bid\":35,\"cid\":\"3\"}',1509445381,18,1509445381,0,NULL,0),(3,0,4,1,'User 创建了班级',NULL,'{\"class_name\":\"大学语文\",\"class_no\":\"DXYW\",\"year\":\"2017\",\"season\":\"Q\",\"lid\":123,\"sj_id\":103,\"bid\":35,\"teach_eid\":81,\"second_eid\":79,\"edu_eid\":0,\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":20,\"lesson_times\":7,\"cr_id\":25,\"schedule\":[{\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25},{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25},{\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"4\"}',1509448285,18,1509448285,0,NULL,0),(4,0,5,1,'User 创建了班级',NULL,'{\"class_name\":\"大学语文\",\"class_no\":\"DXYW\",\"year\":\"2017\",\"season\":\"Q\",\"lid\":123,\"sj_id\":103,\"bid\":35,\"teach_eid\":81,\"second_eid\":79,\"edu_eid\":0,\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":20,\"lesson_times\":7,\"cr_id\":25,\"schedule\":[{\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25},{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25},{\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"5\"}',1509448293,18,1509448293,0,NULL,0),(5,0,3,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":3,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587-\\u6309\\u671f2017\\u79cb\\u5b63\\u5468\\u4e8c17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":124,\"sj_id\":103,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2018-01-31 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"},\"old\":{\"cid\":3,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587-\\u6309\\u671f2017\\u79cb\\u5b63\\u5468\\u4e8c17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":124,\"sj_id\":103,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1517328000,\"status\":0,\"ext_id\":\"\",\"create_time\":1509445381,\"create_uid\":18,\"update_time\":1509445381,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":1730,\"int_end_hour\":1930},\"changed_data\":{\"delete_time\":1509448295,\"is_delete\":1,\"delete_uid\":18,\"update_time\":1509448295}}',1509448295,18,1509448295,0,NULL,0),(6,0,3,8,'User 删除了班级',NULL,'{\"cid\":3,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"语文-按期2017秋季周二17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":124,\"sj_id\":103,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2018-01-31 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}',1509448295,18,1509448295,0,NULL,0),(7,0,6,1,'User 创建了班级',NULL,'{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"lid\":123,\"sj_id\":103,\"sj_ids\":[],\"teach_eid\":85,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"class_name\":\"语文2017秋季周二17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"year\":\"2017\",\"season\":\"Q\",\"start_lesson_time\":\"2017-10-03 00:00:00\",\"end_lesson_time\":\"2018-02-01 00:00:00\",\"lesson_times\":14,\"plan_student_nums\":16,\"bid\":35,\"cid\":\"6\"}',1509449160,18,1509449160,0,NULL,0),(8,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":103,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":103,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509159079,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509502639}}',1509502639,18,1509502639,0,NULL,0),(9,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":103,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":4,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"},{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":26}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":103,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509502639,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":0,\"_rowKey\":4,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"},{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":26}],\"update_time\":1509502669}}',1509502669,18,1509502669,0,NULL,0),(10,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":103,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":103,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509502669,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509502810}}',1509502810,18,1509502810,0,NULL,0),(11,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":103,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":4,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":103,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509502810,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"_index\":0,\"_rowKey\":4,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509502819}}',1509502819,18,1509502819,0,NULL,0),(12,0,4,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":4,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u5927\\u5b66\\u8bed\\u6587\",\"class_no\":\"DXYW\",\"lid\":123,\"sj_id\":103,\"teach_eid\":81,\"second_eid\":79,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":20,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":7,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":1,\"_rowKey\":8,\"schedule\":[{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25}]},\"old\":{\"cid\":4,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u5927\\u5b66\\u8bed\\u6587\",\"class_no\":\"DXYW\",\"lid\":123,\"sj_id\":103,\"teach_eid\":81,\"second_eid\":79,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":20,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":7,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509448285,\"create_uid\":18,\"update_time\":1509448285,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":1,\"_rowKey\":8,\"schedule\":[{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25}],\"update_time\":1509503064}}',1509503064,18,1509503064,0,NULL,0),(13,0,4,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":4,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u5927\\u5b66\\u8bed\\u6587\",\"class_no\":\"DXYW\",\"lid\":123,\"sj_id\":103,\"teach_eid\":81,\"second_eid\":79,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":20,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":7,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":1,\"_rowKey\":2,\"schedule\":[{\"csd_id\":8,\"og_id\":0,\"bid\":35,\"cid\":4,\"eid\":81,\"cr_id\":25,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\"}]},\"old\":{\"cid\":4,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u5927\\u5b66\\u8bed\\u6587\",\"class_no\":\"DXYW\",\"lid\":123,\"sj_id\":103,\"teach_eid\":81,\"second_eid\":79,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":20,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":7,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509448285,\"create_uid\":18,\"update_time\":1509503064,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":1,\"_rowKey\":2,\"schedule\":[{\"csd_id\":8,\"og_id\":0,\"bid\":35,\"cid\":4,\"eid\":81,\"cr_id\":25,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\"}],\"update_time\":1509503444}}',1509503444,18,1509503444,0,NULL,0),(14,0,6,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u65872017\\u79cb\\u5b63\\u5468\\u4e8c17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":123,\"sj_id\":103,\"teach_eid\":85,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-03 00:00:00\",\"end_lesson_time\":\"2018-02-01 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"_index\":2,\"_rowKey\":6,\"schedule\":[{\"csd_id\":14,\"og_id\":0,\"bid\":35,\"cid\":6,\"eid\":85,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":26}]},\"old\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u65872017\\u79cb\\u5b63\\u5468\\u4e8c17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":123,\"sj_id\":103,\"teach_eid\":85,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1506960000,\"end_lesson_time\":1517414400,\"status\":0,\"ext_id\":\"\",\"create_time\":1509449160,\"create_uid\":18,\"update_time\":1509449160,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":1730,\"int_end_hour\":1930},\"changed_data\":{\"_index\":2,\"_rowKey\":6,\"schedule\":[{\"csd_id\":14,\"og_id\":0,\"bid\":35,\"cid\":6,\"eid\":85,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":26}],\"update_time\":1509503472}}',1509503472,18,1509503472,0,NULL,0),(15,0,6,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u65872017\\u79cb\\u5b63\\u5468\\u4e8c17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":123,\"sj_id\":103,\"teach_eid\":85,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-03 00:00:00\",\"end_lesson_time\":\"2018-02-01 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"_index\":2,\"_rowKey\":9,\"schedule\":[{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":26}]},\"old\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u65872017\\u79cb\\u5b63\\u5468\\u4e8c17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":123,\"sj_id\":103,\"teach_eid\":85,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1506960000,\"end_lesson_time\":1517414400,\"status\":0,\"ext_id\":\"\",\"create_time\":1509449160,\"create_uid\":18,\"update_time\":1509503472,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":1730,\"int_end_hour\":1930},\"changed_data\":{\"_index\":2,\"_rowKey\":9,\"schedule\":[{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":26}],\"update_time\":1509503499}}',1509503499,18,1509503499,0,NULL,0),(16,0,6,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u65872017\\u79cb\\u5b63\\u5468\\u4e8c17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":123,\"sj_id\":103,\"teach_eid\":85,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-03 00:00:00\",\"end_lesson_time\":\"2018-02-01 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"_index\":2,\"_rowKey\":12,\"schedule\":[{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":26},{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":26}]},\"old\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u65872017\\u79cb\\u5b63\\u5468\\u4e8c17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":123,\"sj_id\":103,\"teach_eid\":85,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1506960000,\"end_lesson_time\":1517414400,\"status\":0,\"ext_id\":\"\",\"create_time\":1509449160,\"create_uid\":18,\"update_time\":1509503499,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":1730,\"int_end_hour\":1930},\"changed_data\":{\"_index\":2,\"_rowKey\":12,\"schedule\":[{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":26},{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":26}],\"update_time\":1509503521}}',1509503521,18,1509503521,0,NULL,0),(17,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":103,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509502819,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"sj_id\":105,\"cr_id\":25,\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509518201}}',1509518201,18,1509518201,0,NULL,0),(18,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":4,\"schedule\":[{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509518201,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":0,\"_rowKey\":4,\"schedule\":[{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25}],\"update_time\":1509518253}}',1509518253,18,1509518253,0,NULL,0),(19,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":7,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509518253,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":0,\"_rowKey\":7,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25}],\"update_time\":1509518297}}',1509518297,18,1509518297,0,NULL,0),(20,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":10,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":27,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509518297,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":0,\"_rowKey\":10,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":27,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509519334}}',1509519334,18,1509519334,0,NULL,0),(21,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":13,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":78,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509519334,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"teach_eid\":81,\"_index\":0,\"_rowKey\":13,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509519384}}',1509519384,18,1509519384,0,NULL,0),(22,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":16,\"schedule\":[{\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509519384,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":0,\"_rowKey\":16,\"schedule\":[{\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25}],\"update_time\":1509519845}}',1509519845,18,1509519845,0,NULL,0),(23,0,6,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u65872017\\u79cb\\u5b63\\u5468\\u4e8c17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":123,\"sj_id\":103,\"teach_eid\":85,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-03 00:00:00\",\"end_lesson_time\":\"2018-02-01 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"_index\":2,\"_rowKey\":3,\"schedule\":[{\"csd_id\":14,\"og_id\":0,\"bid\":35,\"cid\":6,\"eid\":85,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u65872017\\u79cb\\u5b63\\u5468\\u4e8c17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"lid\":123,\"sj_id\":103,\"teach_eid\":85,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":26,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1506960000,\"end_lesson_time\":1517414400,\"status\":0,\"ext_id\":\"\",\"create_time\":1509449160,\"create_uid\":18,\"update_time\":1509503521,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":1730,\"int_end_hour\":1930},\"changed_data\":{\"_index\":2,\"_rowKey\":3,\"schedule\":[{\"csd_id\":14,\"og_id\":0,\"bid\":35,\"cid\":6,\"eid\":85,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509523665}}',1509523666,18,1509523666,0,NULL,0),(24,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509519845,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"lesson_times\":14,\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509524703}}',1509524703,18,1509524703,0,NULL,0),(25,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509524703,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"lesson_times\":12,\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509524960}}',1509524960,18,1509524960,0,NULL,0),(26,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":12,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509524960,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"lesson_times\":14,\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509525010}}',1509525010,18,1509525010,0,NULL,0),(27,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":16,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":14,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509525010,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"lesson_times\":16,\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"}],\"update_time\":1509526183}}',1509526183,18,1509526183,0,NULL,0),(28,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":16,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":16,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509526183,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25}],\"update_time\":1509526207}}',1509526207,18,1509526207,0,NULL,0),(29,0,2,2,'User 编辑了班级信息',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":16,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-10-31 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25}]},\"old\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"\\u8bed\\u6587A\\u73ed22\",\"class_no\":\"YW01\",\"lid\":123,\"sj_id\":105,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":25,\"plan_student_nums\":16,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":16,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1509379200,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"create_time\":1509159079,\"create_uid\":18,\"update_time\":1509526207,\"is_delete\":0,\"delete_time\":null,\"delete_uid\":0,\"int_start_hour\":0,\"int_end_hour\":0},\"changed_data\":{\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":2,\"og_id\":0,\"bid\":35,\"cid\":2,\"eid\":78,\"cr_id\":26,\"year\":2017,\"season\":\"Q\",\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\"},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25}],\"update_time\":1509532049}}',1509532049,18,1509532049,0,NULL,0),(30,0,7,1,'User 创建了班级',NULL,'{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"lid\":124,\"sj_id\":103,\"sj_ids\":[],\"teach_eid\":79,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":28,\"class_name\":\"语文-按期2017秋季周二17:30~19:30\",\"class_no\":\"17Q-2-17301930\",\"year\":\"2017\",\"season\":\"Q\",\"start_lesson_time\":\"2017-11-03 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"lesson_times\":14,\"plan_student_nums\":16,\"bid\":35,\"cid\":\"7\"}',1509681139,18,1509681139,0,NULL,0),(31,0,4,3,'User 让 刘小云 加入了班级',96,'{\"cid\":4,\"sid\":96,\"oid\":68,\"oi_id\":89,\"in_way\":1,\"in_time\":\"2017-11-06 11:41:01\",\"bid\":\"35\",\"cs_id\":\"1\"}',1509939661,18,1509939661,0,NULL,0),(32,0,4,3,'User 让 郑板桥 加入了班级',97,'{\"cid\":4,\"sid\":97,\"oid\":69,\"oi_id\":90,\"in_way\":1,\"in_time\":\"2017-11-06 11:41:01\",\"bid\":\"35\",\"cs_id\":\"2\"}',1509939661,18,1509939661,0,NULL,0),(33,0,4,3,'User 让 罗汉 加入了班级',98,'{\"cid\":4,\"sid\":98,\"oid\":70,\"oi_id\":91,\"in_way\":1,\"in_time\":\"2017-11-06 11:41:01\",\"bid\":\"35\",\"cs_id\":\"3\"}',1509939661,18,1509939661,0,NULL,0),(34,0,4,3,'User 让 曹操 加入了班级',99,'{\"cid\":4,\"sid\":99,\"oid\":71,\"oi_id\":92,\"in_way\":1,\"in_time\":\"2017-11-06 11:44:05\",\"bid\":\"35\",\"cs_id\":\"4\"}',1509939846,18,1509939846,0,NULL,0),(35,0,4,4,'User 让 曹操 退出了班级',99,'{\"cs_id\":4,\"og_id\":0,\"bid\":35,\"cid\":4,\"sid\":99,\"oid\":71,\"oi_id\":92,\"in_time\":\"2017-11-06 11:44:05\",\"out_time\":0,\"in_way\":1,\"status\":1}',1509940013,18,1509940013,0,NULL,0),(36,0,4,4,'User 让 罗汉 退出了班级',98,'{\"cs_id\":3,\"og_id\":0,\"bid\":35,\"cid\":4,\"sid\":98,\"oid\":70,\"oi_id\":91,\"in_time\":\"2017-11-06 11:41:01\",\"out_time\":0,\"in_way\":1,\"status\":1}',1509940075,18,1509940075,0,NULL,0),(37,0,4,3,'User 让 大明 加入了班级',100,'{\"cid\":4,\"sid\":100,\"oid\":72,\"oi_id\":93,\"in_way\":1,\"in_time\":\"2017-11-06 11:49:38\",\"bid\":\"35\",\"cs_id\":\"5\"}',1509940178,18,1509940178,0,NULL,0),(38,0,4,3,'User 让 李明3131 加入了班级',101,'{\"cid\":4,\"sid\":101,\"oid\":73,\"oi_id\":94,\"in_way\":1,\"in_time\":\"2017-11-06 11:50:28\",\"bid\":\"35\",\"cs_id\":\"6\"}',1509940228,18,1509940228,0,NULL,0),(39,0,4,4,'User 让 李明3131 退出了班级',101,'{\"cs_id\":6,\"og_id\":0,\"bid\":35,\"cid\":4,\"sid\":101,\"oid\":73,\"oi_id\":94,\"in_time\":\"2017-11-06 11:50:28\",\"out_time\":0,\"in_way\":1,\"status\":1}',1509940293,18,1509940293,0,NULL,0),(40,0,4,4,'User 让 大明 退出了班级',100,'{\"cs_id\":5,\"og_id\":0,\"bid\":35,\"cid\":4,\"sid\":100,\"oid\":72,\"oi_id\":93,\"in_time\":\"2017-11-06 11:49:38\",\"out_time\":0,\"in_way\":1,\"status\":1}',1509940351,18,1509940351,0,NULL,0),(41,0,4,4,'User 让 郑板桥 退出了班级',97,'{\"cs_id\":2,\"og_id\":0,\"bid\":35,\"cid\":4,\"sid\":97,\"oid\":69,\"oi_id\":90,\"in_time\":\"2017-11-06 11:41:01\",\"out_time\":0,\"in_way\":1,\"status\":1}',1509940658,18,1509940658,0,NULL,0),(42,0,4,3,'User 让 test 加入了班级',8,'{\"cid\":4,\"sid\":8,\"oid\":74,\"oi_id\":95,\"in_way\":1,\"in_time\":\"2017-11-06 12:04:51\",\"bid\":\"35\",\"cs_id\":\"7\"}',1509941091,18,1509941091,0,NULL,0),(43,0,4,3,'User 让 小花 加入了班级',7,'{\"cid\":4,\"sid\":7,\"oid\":75,\"oi_id\":96,\"in_way\":1,\"in_time\":\"2017-11-06 12:04:51\",\"bid\":\"35\",\"cs_id\":\"8\"}',1509941091,18,1509941091,0,NULL,0),(46,0,10,1,'User 创建了班级',NULL,'{\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"lid\":123,\"sj_id\":103,\"sj_ids\":[],\"teach_eid\":79,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":27,\"class_name\":\"语文2017秋季周一17:30~19:30\",\"class_no\":\"17Q-1-17301930\",\"year\":\"2017\",\"season\":\"Q\",\"start_lesson_time\":\"2017-11-08 00:00:00\",\"end_lesson_time\":\"2018-03-29 00:00:00\",\"lesson_times\":14,\"plan_student_nums\":16,\"bid\":35,\"cid\":\"10\"}',1510047246,18,1510047246,0,NULL,0),(47,0,2,3,'User 让 小花 加入了班级',7,'{\"cid\":2,\"sid\":7,\"sl_id\":126,\"in_way\":1,\"in_time\":\"2017-11-08 00:00:00\",\"bid\":\"35\",\"cs_id\":\"9\"}',1510134288,18,1510134288,0,NULL,0),(48,0,2,3,'User 让 林俊杰 加入了班级',107,'{\"cid\":2,\"sid\":107,\"sl_id\":127,\"in_way\":1,\"in_time\":\"2017-11-08 00:00:00\",\"bid\":\"35\",\"cs_id\":\"10\"}',1510134288,18,1510134288,0,NULL,0),(49,0,4,4,'User 让 test 退出了班级',8,'{\"cs_id\":7,\"og_id\":0,\"bid\":35,\"cid\":4,\"sid\":8,\"sl_id\":95,\"in_time\":\"2017-11-06 12:04:51\",\"out_time\":0,\"in_way\":1,\"status\":1}',1510294704,18,1510294704,0,NULL,0),(50,0,4,4,'User 让 小花 退出了班级',7,'{\"cs_id\":8,\"og_id\":0,\"bid\":35,\"cid\":4,\"sid\":7,\"sl_id\":96,\"in_time\":\"2017-11-06 12:04:51\",\"out_time\":0,\"in_way\":1,\"status\":1}',1510294706,18,1510294706,0,NULL,0),(51,0,11,1,'User 创建了班级',NULL,'{\"class_name\":\"考勤测试班Test\",\"class_no\":\"AttendanceTest\",\"year\":\"2017\",\"season\":\"Q\",\"lid\":129,\"sj_id\":105,\"bid\":35,\"teach_eid\":81,\"second_eid\":81,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-10 00:00:00\",\"end_lesson_time\":\"2018-11-01 00:00:00\",\"plan_student_nums\":50,\"lesson_times\":7,\"cr_id\":25,\"schedule\":[{\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25},{\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":25},{\"week_day\":7,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":25},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":25},{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25},{\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":25}],\"course_arrange\":0,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"11\"}',1510295580,18,1510295580,0,NULL,0),(52,0,11,3,'User 让 姚瑞 加入了班级',9,'{\"cid\":11,\"sid\":9,\"sl_id\":135,\"in_way\":1,\"in_time\":\"2017-11-10 00:00:00\",\"bid\":\"35\",\"cs_id\":\"11\"}',1510295622,18,1510295622,0,NULL,0),(53,0,11,3,'User 让 周杰伦 加入了班级',117,'{\"cid\":11,\"sid\":117,\"sl_id\":136,\"in_way\":1,\"in_time\":\"2017-11-10 00:00:00\",\"bid\":\"35\",\"cs_id\":\"12\"}',1510295622,18,1510295622,0,NULL,0),(55,0,13,1,'User 创建了班级',NULL,'{\"class_name\":\"TestDev班级\",\"class_no\":\"123\",\"year\":\"2017\",\"season\":\"Q\",\"lid\":130,\"sj_id\":101,\"bid\":35,\"teach_eid\":81,\"second_eid\":80,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-10 00:00:00\",\"end_lesson_time\":\"2018-11-02 00:00:00\",\"plan_student_nums\":30,\"lesson_times\":7,\"cr_id\":27,\"schedule\":[{\"week_day\":1,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":27},{\"week_day\":2,\"int_start_hour\":\"17:30\",\"int_end_hour\":\"19:30\",\"cr_id\":27},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":27},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":27},{\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"19:45\",\"cr_id\":27},{\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":27},{\"week_day\":7,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":27}],\"course_arrange\":0,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"13\"}',1510296130,18,1510296130,0,NULL,0),(56,0,13,3,'User 让 姚瑞 加入了班级',9,'{\"cid\":13,\"sid\":9,\"sl_id\":137,\"in_way\":1,\"in_time\":\"2017-11-10 00:00:00\",\"bid\":\"35\",\"cs_id\":\"13\"}',1510297032,18,1510297032,0,NULL,0),(57,0,13,3,'User 让 周杰伦 加入了班级',117,'{\"cid\":13,\"sid\":117,\"sl_id\":138,\"in_way\":1,\"in_time\":\"2017-11-10 00:00:00\",\"bid\":\"35\",\"cs_id\":\"14\"}',1510297032,18,1510297032,0,NULL,0),(58,0,11,4,'User 让 姚瑞 退出了班级',9,'{\"cs_id\":11,\"og_id\":0,\"bid\":35,\"cid\":11,\"sid\":9,\"sl_id\":135,\"in_time\":\"2017-11-10 00:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1510309585,18,1510309585,0,NULL,0),(59,0,11,4,'User 让 周杰伦 退出了班级',117,'{\"cs_id\":12,\"og_id\":0,\"bid\":35,\"cid\":11,\"sid\":117,\"sl_id\":136,\"in_time\":\"2017-11-10 00:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1510309587,18,1510309587,0,NULL,0),(60,0,14,1,'User 创建了班级',NULL,'{\"class_name\":\"李将军开车001\",\"class_no\":\"001\",\"year\":\"2017\",\"season\":\"H\",\"lid\":130,\"sj_id\":101,\"bid\":35,\"teach_eid\":79,\"second_eid\":81,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-01 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":10,\"lesson_times\":0,\"cr_id\":26,\"schedule\":[],\"course_arrange\":0,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"14\"}',1510393295,18,1510393295,0,NULL,0),(66,0,20,1,'User 创建了班级',NULL,'{\"class_name\":\"yaorui-atd-test\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"H\",\"lid\":141,\"sj_id\":102,\"bid\":35,\"teach_eid\":79,\"second_eid\":80,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-13 00:00:00\",\"end_lesson_time\":\"2017-12-24 00:00:00\",\"plan_student_nums\":5,\"lesson_times\":0,\"cr_id\":25,\"schedule\":[{\"week_day\":2,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"09:00\",\"cr_id\":25},{\"week_day\":3,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"09:00\",\"cr_id\":25}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"20\"}',1510627183,18,1510627183,0,NULL,0),(67,0,20,3,'User 让 yaorui001 加入了班级',127,'{\"cid\":20,\"sid\":127,\"sl_id\":162,\"in_way\":1,\"in_time\":\"2017-11-14 00:00:00\",\"bid\":\"35\",\"cs_id\":\"15\"}',1510627468,18,1510627468,0,NULL,0),(68,0,20,3,'User 让 yaorui002 加入了班级',128,'{\"cid\":20,\"sid\":128,\"sl_id\":163,\"in_way\":1,\"in_time\":\"2017-11-14 00:00:00\",\"bid\":\"35\",\"cs_id\":\"16\"}',1510627468,18,1510627468,0,NULL,0),(69,0,20,3,'User 让 yaorui003 加入了班级',129,'{\"cid\":20,\"sid\":129,\"sl_id\":164,\"in_way\":1,\"in_time\":\"2017-11-14 00:00:00\",\"bid\":\"35\",\"cs_id\":\"17\"}',1510627468,18,1510627468,0,NULL,0),(71,0,22,1,'User 创建了班级',NULL,'{\"class_name\":\"yairui-atd-test\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"H\",\"lid\":141,\"sj_id\":102,\"bid\":35,\"teach_eid\":81,\"second_eid\":0,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-15 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":10,\"lesson_times\":0,\"cr_id\":26,\"schedule\":[{\"week_day\":3,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"09:00\",\"cr_id\":26},{\"week_day\":3,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":26},{\"week_day\":3,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"cr_id\":26},{\"week_day\":3,\"int_start_hour\":\"12:00\",\"int_end_hour\":\"13:00\",\"cr_id\":26}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"22\"}',1510718161,18,1510718161,0,NULL,0),(72,0,22,3,'User 让 yaorui-trial 加入了班级',136,'{\"cid\":22,\"sid\":136,\"sl_id\":176,\"in_way\":1,\"in_time\":\"2017-11-15 00:00:00\",\"bid\":\"35\",\"cs_id\":\"18\"}',1510718229,18,1510718229,0,NULL,0),(73,0,22,3,'User 让 yaorui-customer-trial 加入了班级',138,'{\"cid\":22,\"sid\":138,\"sl_id\":177,\"in_way\":1,\"in_time\":\"2017-11-15 00:00:00\",\"bid\":\"35\",\"cs_id\":\"19\"}',1510718229,18,1510718229,0,NULL,0),(74,0,23,1,'User 创建了班级',NULL,'{\"class_name\":\"yaorui-11-16-att\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"H\",\"lid\":141,\"sj_id\":102,\"bid\":35,\"teach_eid\":27,\"second_eid\":28,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-10 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":5,\"lesson_times\":14,\"cr_id\":27,\"schedule\":[{\"week_day\":1,\"int_start_hour\":\"08:30\",\"int_end_hour\":\"09:30\",\"cr_id\":0},{\"week_day\":3,\"int_start_hour\":\"12:00\",\"int_end_hour\":\"13:00\",\"cr_id\":0},{\"week_day\":2,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"09:00\",\"cr_id\":0},{\"week_day\":4,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":27},{\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":27},{\"week_day\":5,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":27},{\"week_day\":7,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":27}],\"course_arrange\":0,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"23\"}',1510822547,18,1510822547,0,NULL,0),(75,0,23,3,'User 让 yaorui-11-16 加入了班级',143,'{\"cid\":23,\"sid\":143,\"sl_id\":182,\"in_way\":1,\"in_time\":\"2017-11-16 00:00:00\",\"bid\":\"35\",\"cs_id\":\"20\"}',1510827073,18,1510827073,0,NULL,0),(76,0,24,1,'User 创建了班级',NULL,'{\"week_day\":1,\"int_start_hour\":\"08:30\",\"int_end_hour\":\"09:30\",\"lid\":132,\"sj_id\":105,\"sj_ids\":[],\"teach_eid\":79,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":28,\"class_name\":\"数学-按课时-按课时2017寒假周一08:30~09:30\",\"class_no\":\"17H-1-08300930\",\"year\":\"2017\",\"season\":\"H\",\"start_lesson_time\":\"2017-11-17 00:00:00\",\"end_lesson_time\":\"2017-12-30 00:00:00\",\"lesson_times\":10,\"plan_student_nums\":16,\"bid\":35,\"cid\":\"24\"}',1510882017,18,1510882017,0,NULL,0),(77,0,24,3,'User 让 张静 加入了班级',144,'{\"cid\":24,\"sid\":144,\"sl_id\":186,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"21\"}',1510919574,18,1510919574,0,NULL,0),(78,0,24,3,'User 让 打印学员01 加入了班级',141,'{\"cid\":24,\"sid\":141,\"sl_id\":180,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"22\"}',1510919574,18,1510919574,0,NULL,0),(79,0,25,1,'User 创建了班级',NULL,'{\"class_name\":\"att-test\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"H\",\"lid\":130,\"sj_id\":105,\"bid\":35,\"teach_eid\":1,\"second_eid\":27,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-16 00:00:00\",\"end_lesson_time\":\"2017-12-31 00:00:00\",\"plan_student_nums\":10,\"lesson_times\":8,\"cr_id\":29,\"schedule\":[{\"week_day\":5,\"int_start_hour\":\"20:15\",\"int_end_hour\":\"21:15\",\"cr_id\":29},{\"week_day\":5,\"int_start_hour\":\"08:30\",\"int_end_hour\":\"09:30\",\"cr_id\":29},{\"week_day\":5,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":29},{\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":29},{\"week_day\":7,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":29}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"25\"}',1510920856,18,1510920856,0,NULL,0),(80,0,25,3,'User 让 yaorui-11-16 加入了班级',143,'{\"cid\":25,\"sid\":143,\"sl_id\":198,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"23\"}',1510920913,18,1510920913,0,NULL,0),(81,0,25,3,'User 让 刘子云02 加入了班级',147,'{\"cid\":25,\"sid\":147,\"sl_id\":197,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"24\"}',1510920913,18,1510920913,0,NULL,0),(82,0,25,3,'User 让 刘子云01 加入了班级',146,'{\"cid\":25,\"sid\":146,\"sl_id\":196,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"25\"}',1510920913,18,1510920913,0,NULL,0),(83,0,25,3,'User 让 袁培红 加入了班级',145,'{\"cid\":25,\"sid\":145,\"sl_id\":189,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"26\"}',1510922750,18,1510922750,0,NULL,0),(84,0,25,3,'User 让 张静 加入了班级',144,'{\"cid\":25,\"sid\":144,\"sl_id\":200,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"27\"}',1510922750,18,1510922750,0,NULL,0),(85,0,26,1,'User 创建了班级',NULL,'{\"class_name\":\"11-17 21:00\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"Q\",\"lid\":129,\"sj_id\":105,\"bid\":35,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-17 00:00:00\",\"end_lesson_time\":\"2017-11-20 00:00:00\",\"plan_student_nums\":10,\"lesson_times\":10,\"cr_id\":0,\"schedule\":[{\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"cr_id\":0},{\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"cr_id\":0},{\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"cr_id\":0}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"26\"}',1510923941,18,1510923941,0,NULL,0),(86,0,26,3,'User 让 刘子云02 加入了班级',147,'{\"cid\":26,\"sid\":147,\"sl_id\":201,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"28\"}',1510923964,18,1510923964,0,NULL,0),(87,0,26,3,'User 让 刘子云01 加入了班级',146,'{\"cid\":26,\"sid\":146,\"sl_id\":202,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"29\"}',1510923964,18,1510923964,0,NULL,0),(88,0,26,3,'User 让 袁培红 加入了班级',145,'{\"cid\":26,\"sid\":145,\"sl_id\":190,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"30\"}',1510923964,18,1510923964,0,NULL,0),(89,0,26,3,'User 让 张静 加入了班级',144,'{\"cid\":26,\"sid\":144,\"sl_id\":203,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"31\"}',1510923964,18,1510923964,0,NULL,0),(90,0,26,3,'User 让 yaorui-11-16 加入了班级',143,'{\"cid\":26,\"sid\":143,\"sl_id\":199,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"32\"}',1510923964,18,1510923964,0,NULL,0),(91,0,26,3,'User 让 考勤学员001 加入了班级',139,'{\"cid\":26,\"sid\":139,\"sl_id\":204,\"in_way\":1,\"in_time\":\"2017-11-17 00:00:00\",\"bid\":\"35\",\"cs_id\":\"33\"}',1510923964,18,1510923964,0,NULL,0),(92,0,25,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":25,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"att-test\",\"class_no\":\"\",\"lid\":130,\"sj_id\":105,\"teach_eid\":1,\"second_eid\":27,\"edu_eid\":0,\"cr_id\":29,\"plan_student_nums\":10,\"student_nums\":5,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-16 00:00:00\",\"end_lesson_time\":\"2017-12-31 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":1,\"_rowKey\":12,\"schedule\":[{\"csd_id\":68,\"og_id\":0,\"bid\":35,\"cid\":25,\"eid\":1,\"cr_id\":29,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"20:15\",\"int_end_hour\":\"21:15\",\"delete_uid\":0},{\"csd_id\":69,\"og_id\":0,\"bid\":35,\"cid\":25,\"eid\":1,\"cr_id\":29,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"08:30\",\"int_end_hour\":\"09:30\",\"delete_uid\":0},{\"csd_id\":70,\"og_id\":0,\"bid\":35,\"cid\":25,\"eid\":1,\"cr_id\":29,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":71,\"og_id\":0,\"bid\":35,\"cid\":25,\"eid\":1,\"cr_id\":29,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":72,\"og_id\":0,\"bid\":35,\"cid\":25,\"eid\":1,\"cr_id\":29,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"10:30\",\"cr_id\":29},{\"week_day\":6,\"int_start_hour\":\"12:00\",\"int_end_hour\":\"12:30\",\"cr_id\":29},{\"week_day\":6,\"int_start_hour\":\"11:30\",\"int_end_hour\":\"12:00\",\"cr_id\":29},{\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"11:30\",\"cr_id\":29},{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"11:00\",\"cr_id\":29}]},\"old\":[],\"changed_data\":{\"cid\":25,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"att-test\",\"class_no\":\"\",\"lid\":130,\"sj_id\":105,\"teach_eid\":1,\"second_eid\":27,\"edu_eid\":0,\"cr_id\":29,\"plan_student_nums\":10,\"student_nums\":5,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1510761600,\"end_lesson_time\":1514649600,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":1,\"_rowKey\":12,\"schedule\":[{\"csd_id\":68,\"og_id\":0,\"bid\":35,\"cid\":25,\"eid\":1,\"cr_id\":29,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"20:15\",\"int_end_hour\":\"21:15\",\"delete_uid\":0},{\"csd_id\":69,\"og_id\":0,\"bid\":35,\"cid\":25,\"eid\":1,\"cr_id\":29,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"08:30\",\"int_end_hour\":\"09:30\",\"delete_uid\":0},{\"csd_id\":70,\"og_id\":0,\"bid\":35,\"cid\":25,\"eid\":1,\"cr_id\":29,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":71,\"og_id\":0,\"bid\":35,\"cid\":25,\"eid\":1,\"cr_id\":29,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":72,\"og_id\":0,\"bid\":35,\"cid\":25,\"eid\":1,\"cr_id\":29,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"10:30\",\"cr_id\":29},{\"week_day\":6,\"int_start_hour\":\"12:00\",\"int_end_hour\":\"12:30\",\"cr_id\":29},{\"week_day\":6,\"int_start_hour\":\"11:30\",\"int_end_hour\":\"12:00\",\"cr_id\":29},{\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"11:30\",\"cr_id\":29},{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"11:00\",\"cr_id\":29}],\"update_time\":1510968140}}',1510968140,18,1510968140,0,NULL,0),(93,0,26,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":26,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"test1\",\"class_no\":\"\",\"lid\":129,\"sj_id\":105,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"cr_id\":0,\"plan_student_nums\":10,\"student_nums\":6,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-20 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":41,\"schedule\":[{\"csd_id\":73,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"delete_uid\":0},{\"csd_id\":74,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":75,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":76,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"delete_uid\":0},{\"csd_id\":77,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"delete_uid\":0},{\"csd_id\":78,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":26,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"test1\",\"class_no\":\"\",\"lid\":129,\"sj_id\":105,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"cr_id\":0,\"plan_student_nums\":10,\"student_nums\":6,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511107200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":41,\"schedule\":[{\"csd_id\":73,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"delete_uid\":0},{\"csd_id\":74,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":75,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":76,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"delete_uid\":0},{\"csd_id\":77,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"delete_uid\":0},{\"csd_id\":78,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1510968197}}',1510968197,18,1510968197,0,NULL,0),(94,0,26,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":26,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"test1\",\"class_no\":\"\",\"lid\":129,\"sj_id\":105,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"cr_id\":28,\"plan_student_nums\":10,\"student_nums\":6,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-20 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":51,\"schedule\":[{\"csd_id\":73,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"delete_uid\":0},{\"csd_id\":74,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":75,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":76,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"delete_uid\":0},{\"csd_id\":77,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"delete_uid\":0},{\"csd_id\":78,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":26,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"test1\",\"class_no\":\"\",\"lid\":129,\"sj_id\":105,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"cr_id\":28,\"plan_student_nums\":10,\"student_nums\":6,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511107200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":51,\"schedule\":[{\"csd_id\":73,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"delete_uid\":0},{\"csd_id\":74,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":75,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":76,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"delete_uid\":0},{\"csd_id\":77,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"delete_uid\":0},{\"csd_id\":78,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1510968218}}',1510968218,18,1510968218,0,NULL,0),(95,0,26,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":26,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"test1\",\"class_no\":\"\",\"lid\":129,\"sj_id\":105,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"cr_id\":28,\"plan_student_nums\":10,\"student_nums\":6,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-20 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":81,\"schedule\":[{\"csd_id\":73,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"delete_uid\":0},{\"csd_id\":74,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":75,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":76,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"delete_uid\":0},{\"csd_id\":77,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"delete_uid\":0},{\"csd_id\":78,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":26,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"test1\",\"class_no\":\"\",\"lid\":129,\"sj_id\":105,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"cr_id\":28,\"plan_student_nums\":10,\"student_nums\":6,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511107200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":81,\"schedule\":[{\"csd_id\":73,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"delete_uid\":0},{\"csd_id\":74,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":75,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":76,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"delete_uid\":0},{\"csd_id\":77,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"delete_uid\":0},{\"csd_id\":78,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1510968301}}',1510968301,18,1510968301,0,NULL,0),(96,0,26,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":26,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"test1\",\"class_no\":\"\",\"lid\":129,\"sj_id\":105,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"cr_id\":28,\"plan_student_nums\":10,\"student_nums\":6,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-20 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":111,\"schedule\":[{\"csd_id\":73,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"delete_uid\":0},{\"csd_id\":74,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":75,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":76,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"delete_uid\":0},{\"csd_id\":77,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"delete_uid\":0},{\"csd_id\":78,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":26,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"test1\",\"class_no\":\"\",\"lid\":129,\"sj_id\":105,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"cr_id\":28,\"plan_student_nums\":10,\"student_nums\":6,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511107200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":111,\"schedule\":[{\"csd_id\":73,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"delete_uid\":0},{\"csd_id\":74,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":75,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":76,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"delete_uid\":0},{\"csd_id\":77,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"delete_uid\":0},{\"csd_id\":78,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1510968366}}',1510968366,18,1510968366,0,NULL,0),(97,0,26,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":26,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"test1\",\"class_no\":\"\",\"lid\":129,\"sj_id\":105,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"cr_id\":28,\"plan_student_nums\":10,\"student_nums\":6,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-20 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":151,\"schedule\":[{\"csd_id\":73,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"delete_uid\":0},{\"csd_id\":74,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":75,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":76,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"delete_uid\":0},{\"csd_id\":77,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"delete_uid\":0},{\"csd_id\":78,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":26,\"og_id\":0,\"parent_cid\":0,\"bid\":35,\"class_name\":\"test1\",\"class_no\":\"\",\"lid\":129,\"sj_id\":105,\"teach_eid\":4,\"second_eid\":4,\"edu_eid\":0,\"cr_id\":28,\"plan_student_nums\":10,\"student_nums\":6,\"nums_rate\":\"0.00\",\"lesson_times\":10,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511107200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":151,\"schedule\":[{\"csd_id\":73,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"21:30\",\"int_end_hour\":\"22:30\",\"delete_uid\":0},{\"csd_id\":74,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":75,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":5,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":76,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"16:00\",\"int_end_hour\":\"17:00\",\"delete_uid\":0},{\"csd_id\":77,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"15:00\",\"delete_uid\":0},{\"csd_id\":78,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"13:00\",\"int_end_hour\":\"14:00\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"10:00\",\"int_end_hour\":\"11:00\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":35,\"cid\":26,\"eid\":4,\"cr_id\":0,\"year\":2017,\"season\":\"Q\",\"week_day\":6,\"int_start_hour\":\"09:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1510968423}}',1510968423,18,1510968423,0,NULL,0);
/*!40000 ALTER TABLE `x360p_class_log` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COMMENT='排班计划表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class_schedule`
--

LOCK TABLES `x360p_class_schedule` WRITE;
/*!40000 ALTER TABLE `x360p_class_schedule` DISABLE KEYS */;
INSERT INTO `x360p_class_schedule` VALUES (1,0,35,2,78,26,2017,'Q',4,1900,1945,1509159079,18,1,18,1509451530,1509451530),(2,0,35,2,78,26,2017,'Q',1,1730,1930,1509430287,18,0,0,NULL,1509430287),(3,0,35,3,81,25,2017,'Q',2,1730,1930,1509445381,18,1,18,1509448295,1509448295),(4,0,35,4,81,25,2017,'Q',1,1730,1930,1509448285,18,1,18,1509448581,1509448581),(5,0,35,4,81,25,2017,'Q',2,1730,1930,1509448285,18,1,18,1509448518,1509448518),(6,0,35,4,81,25,2017,'Q',3,1900,1945,1509448285,18,1,18,1509448500,1509448500),(7,0,35,4,81,25,2017,'Q',4,1900,1945,1509448285,18,1,18,1509448564,1509448564),(8,0,35,4,81,25,2017,'Q',5,1900,1945,1509448285,18,0,0,NULL,1509448285),(14,0,35,6,85,26,2017,'Q',2,1730,1930,1509449160,18,0,0,NULL,1509449160),(15,0,35,7,79,28,2017,'Q',2,1730,1930,1509681139,18,0,0,NULL,1509681139),(16,0,35,10,79,27,2017,'Q',1,1730,1930,1510047246,18,0,0,NULL,1510047246),(17,0,35,11,81,25,2017,'Q',5,1900,1945,1510295580,18,0,0,NULL,1510295580),(18,0,35,11,81,25,2017,'Q',6,900,1000,1510295580,18,0,0,NULL,1510295580),(19,0,35,11,81,25,2017,'Q',7,900,1000,1510295580,18,0,0,NULL,1510295580),(20,0,35,11,81,25,2017,'Q',4,1900,1945,1510295580,18,0,0,NULL,1510295580),(21,0,35,11,81,25,2017,'Q',3,1900,1945,1510295580,18,0,0,NULL,1510295580),(22,0,35,11,81,25,2017,'Q',2,1730,1930,1510295580,18,0,0,NULL,1510295580),(23,0,35,11,81,25,2017,'Q',1,1730,1930,1510295580,18,0,0,NULL,1510295580),(31,0,35,13,81,27,2017,'Q',1,1730,1930,1510296130,18,0,0,NULL,1510296130),(32,0,35,13,81,27,2017,'Q',2,1730,1930,1510296130,18,0,0,NULL,1510296130),(33,0,35,13,81,27,2017,'Q',3,1900,1945,1510296130,18,0,0,NULL,1510296130),(34,0,35,13,81,27,2017,'Q',4,1900,1945,1510296130,18,0,0,NULL,1510296130),(35,0,35,13,81,27,2017,'Q',5,1900,1945,1510296130,18,0,0,NULL,1510296130),(36,0,35,13,81,27,2017,'Q',6,900,1000,1510296130,18,0,0,NULL,1510296130),(37,0,35,13,81,27,2017,'Q',7,900,1000,1510296130,18,0,0,NULL,1510296130),(48,0,35,20,79,25,2017,'H',2,800,900,1510627183,18,0,0,NULL,1510627183),(49,0,35,20,79,25,2017,'H',3,800,900,1510627183,18,0,0,NULL,1510627183),(54,0,35,22,81,26,2017,'H',3,800,900,1510718161,18,0,0,NULL,1510718161),(55,0,35,22,81,26,2017,'H',3,900,1000,1510718161,18,0,0,NULL,1510718161),(56,0,35,22,81,26,2017,'H',3,1100,1200,1510718161,18,0,0,NULL,1510718161),(57,0,35,22,81,26,2017,'H',3,1200,1300,1510718161,18,0,0,NULL,1510718161),(58,0,35,23,27,0,2017,'H',1,830,930,1510822547,18,0,0,NULL,1510822547),(59,0,35,23,27,0,2017,'H',3,1200,1300,1510822547,18,0,0,NULL,1510822547),(60,0,35,23,27,0,2017,'H',2,800,900,1510822547,18,0,0,NULL,1510822547),(61,0,35,23,27,27,2017,'H',4,900,1000,1510822547,18,0,0,NULL,1510822547),(62,0,35,23,27,27,2017,'H',6,900,1000,1510822547,18,0,0,NULL,1510822547),(63,0,35,23,27,27,2017,'H',5,900,1000,1510822547,18,0,0,NULL,1510822547),(64,0,35,23,27,27,2017,'H',7,900,1000,1510822547,18,0,0,NULL,1510822547),(65,0,35,24,79,28,2017,'H',1,830,930,1510882017,18,0,0,NULL,1510882017),(66,0,35,20,79,25,2017,'H',4,900,1000,1510882130,18,1,18,1510882198,1510882198),(67,0,35,24,79,28,2017,'H',4,900,1000,1510882206,18,0,0,NULL,1510882206),(68,0,35,25,1,29,2017,'H',5,2015,2115,1510920856,18,0,0,NULL,1510920856),(69,0,35,25,1,29,2017,'H',5,830,930,1510920856,18,0,0,NULL,1510920856),(70,0,35,25,1,29,2017,'H',5,900,1000,1510920856,18,0,0,NULL,1510920856),(71,0,35,25,1,29,2017,'H',6,900,1000,1510920856,18,0,0,NULL,1510920856),(72,0,35,25,1,29,2017,'H',7,900,1000,1510920856,18,0,0,NULL,1510920856),(73,0,35,26,4,0,2017,'Q',5,2130,2230,1510923941,18,0,0,NULL,1510923941),(74,0,35,26,4,0,2017,'Q',5,1100,1200,1510923941,18,0,0,NULL,1510923941),(75,0,35,26,4,0,2017,'Q',5,800,1000,1510923941,18,0,0,NULL,1510923941),(76,0,35,26,4,0,2017,'Q',6,1600,1700,1510923941,18,0,0,NULL,1510923941),(77,0,35,26,4,0,2017,'Q',6,1400,1500,1510923941,18,0,0,NULL,1510923941),(78,0,35,26,4,0,2017,'Q',6,1300,1400,1510923941,18,0,0,NULL,1510923941),(79,0,35,26,4,0,2017,'Q',6,1100,1200,1510923941,18,0,0,NULL,1510923941),(80,0,35,26,4,0,2017,'Q',6,1000,1100,1510923941,18,0,0,NULL,1510923941),(81,0,35,26,4,0,2017,'Q',6,900,1000,1510923941,18,0,0,NULL,1510923941),(82,0,35,25,1,29,2017,'H',6,1000,1030,1510968140,18,0,0,NULL,1510968140),(83,0,35,25,1,29,2017,'H',6,1200,1230,1510968140,18,0,0,NULL,1510968140),(84,0,35,25,1,29,2017,'H',6,1130,1200,1510968140,18,0,0,NULL,1510968140),(85,0,35,25,1,29,2017,'H',6,1100,1130,1510968140,18,0,0,NULL,1510968140),(86,0,35,25,1,29,2017,'H',6,1030,1100,1510968140,18,0,0,NULL,1510968140);
/*!40000 ALTER TABLE `x360p_class_schedule` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COMMENT='班级学生表（记录每个班级里面有哪些学生)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class_student`
--

LOCK TABLES `x360p_class_student` WRITE;
/*!40000 ALTER TABLE `x360p_class_student` DISABLE KEYS */;
INSERT INTO `x360p_class_student` VALUES (1,0,35,4,96,89,1509939661,0,1,1,1509939661,18,1509939661,0,NULL,0),(9,0,35,2,7,126,1510070400,0,1,1,1510134288,18,1510134288,0,NULL,0),(10,0,35,2,107,127,1510070400,0,1,1,1510134288,18,1510134288,0,NULL,0),(13,0,35,13,9,137,1510243200,0,1,1,1510297032,18,1510297032,0,NULL,0),(14,0,35,13,117,138,1510243200,0,1,1,1510297032,18,1510297032,0,NULL,0),(15,0,35,20,127,162,1510588800,0,1,1,1510627468,18,1510627468,0,NULL,0),(16,0,35,20,128,163,1510588800,0,1,1,1510627468,18,1510627468,0,NULL,0),(17,0,35,20,129,164,1510588800,0,1,1,1510627468,18,1510627468,0,NULL,0),(18,0,35,22,136,176,1510675200,0,1,1,1510718229,18,1510718229,0,NULL,0),(19,0,35,22,138,177,1510675200,0,1,1,1510718229,18,1510718229,0,NULL,0),(20,0,35,23,143,182,1510761600,0,1,1,1510827073,18,1510827073,0,NULL,0),(21,0,35,24,144,186,1510848000,0,1,1,1510919574,18,1510919574,0,NULL,0),(22,0,35,24,141,180,1510848000,0,1,1,1510919574,18,1510919574,0,NULL,0),(23,0,35,25,143,198,1510848000,0,1,1,1510920913,18,1510920913,0,NULL,0),(24,0,35,25,147,197,1510848000,0,1,1,1510920913,18,1510920913,0,NULL,0),(25,0,35,25,146,196,1510848000,0,1,1,1510920913,18,1510920913,0,NULL,0),(26,0,35,25,145,189,1510848000,0,1,1,1510922750,18,1510922750,0,NULL,0),(27,0,35,25,144,200,1510848000,0,1,1,1510922750,18,1510922750,0,NULL,0),(28,0,35,26,147,201,1510848000,0,1,1,1510923964,18,1510923964,0,NULL,0),(29,0,35,26,146,202,1510848000,0,1,1,1510923964,18,1510923964,0,NULL,0),(30,0,35,26,145,190,1510848000,0,1,1,1510923964,18,1510923964,0,NULL,0),(31,0,35,26,144,203,1510848000,0,1,1,1510923964,18,1510923964,0,NULL,0),(32,0,35,26,143,199,1510848000,0,1,1,1510923964,18,1510923964,0,NULL,0),(33,0,35,26,139,204,1510848000,0,1,1,1510923964,18,1510923964,0,NULL,0);
/*!40000 ALTER TABLE `x360p_class_student` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COMMENT='教室表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_classroom`
--

LOCK TABLES `x360p_classroom` WRITE;
/*!40000 ALTER TABLE `x360p_classroom` DISABLE KEYS */;
INSERT INTO `x360p_classroom` VALUES (23,0,35,'123',16,0,8,NULL,1508928547,18,1508931604,1,1508931604,18),(24,0,35,'dsgfdgrdgr',16,0,8,'[[1,1,1,1,1],[1,1,1,1,1],[1,1,0,1,1],[1,1,1,1,1],[0,1,1,1,0]]',1508931613,18,1509974144,0,NULL,0),(25,0,35,'A01',16,0,5,'[[1,1,1,1,1],[1,1,1,1,1],[1,1,0,1,1],[1,1,1,1,1],[1,1,1,1,1]]',1509099319,18,1509973994,0,NULL,0),(26,0,35,'A02',16,0,5,NULL,1509099470,18,1509099470,0,NULL,0),(27,0,35,'A03',16,0,5,NULL,1509099654,18,1509099654,0,NULL,0),(28,0,35,'A04',16,0,5,NULL,1509100069,18,1509100069,0,NULL,0),(29,0,35,'A05',16,0,5,NULL,1509100537,18,1509100537,0,NULL,0),(30,0,35,'A09',16,0,5,NULL,1509100657,18,1509100657,0,NULL,0),(31,0,35,'A10',16,0,5,NULL,1509100853,18,1509100853,0,NULL,0),(32,0,35,'A11',16,0,5,NULL,1509100981,18,1509100981,0,NULL,0),(33,0,35,'A13',16,0,5,NULL,1509101036,18,1509101036,0,NULL,0),(34,0,35,'A16',16,0,5,NULL,1509101152,18,1509101152,0,NULL,0),(35,0,35,'A18',16,0,5,NULL,1509101259,18,1509101259,0,NULL,0),(36,0,35,'A19',16,0,5,NULL,1509101312,18,1509101312,0,NULL,0),(37,0,35,'B01',16,0,5,NULL,1509102044,18,1509102044,0,NULL,0),(38,0,35,'B02',16,0,5,NULL,1509102067,18,1509102067,0,NULL,0),(39,0,35,'B03',16,0,5,NULL,1509103296,18,1509103296,0,NULL,0),(40,0,37,'101',16,0,4,NULL,1509430427,18,1509430427,0,NULL,0);
/*!40000 ALTER TABLE `x360p_classroom` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_collect`
--

DROP TABLE IF EXISTS `x360p_collect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_collect` (
  `collect_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `uid` int(11) unsigned NOT NULL COMMENT '用户uid',
  `gid` int(11) unsigned NOT NULL COMMENT '商品id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态（1：正常， 0：商品已过期或删除）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`collect_id`),
  UNIQUE KEY `unique_uid_gid` (`uid`,`gid`) USING BTREE COMMENT 'uid和gid的唯一索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='收藏表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_collect`
--

LOCK TABLES `x360p_collect` WRITE;
/*!40000 ALTER TABLE `x360p_collect` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_collect` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_config`
--

LOCK TABLES `x360p_config` WRITE;
/*!40000 ALTER TABLE `x360p_config` DISABLE KEYS */;
INSERT INTO `x360p_config` VALUES (1,0,'wxmp','{\"app_id\":\"wx809dc542c7ba3ccf\",\"secret\":\"cdb4e5f0c3f9bc6bd9e1d5ce3c8e0438\"}','json',0,0,1502849390,0,NULL,0),(3,0,'wxpay','{\"enable\":true,\"merchant_id\":\"4566511313213\",\"key\":\"sdfsdf\",\"cert_path\":\"dsfdsf\",\"key_path\":\"dsfsdfds\"}','json',1501644940,18,1502849473,0,NULL,0),(4,0,'params','{\"org_name\":\"深圳浪腾计算机软件公司\",\"sysname\":\"质量服务平台\",\"edu_recommend_text\":\"导师推荐语,用于作业推荐界面，引导家长点击进入导师的个人介绍页面\"}','json',1501646181,18,1508291608,0,NULL,0),(7,0,'storage','{\"access_key\":\"p9mUPzEN5ihLHctwvBIk5w9MBckHvFSrXadVRlPY\",\"secret_key\":\"UJRv2IaSnsFUmZyXmYWyhpcrPW7WIYnslnT749Fh\",\"bucket\":\"ygwqms\",\"prefix\":\"qms\\/\",\"domain\":\"http:\\/\\/s10.xiao360.com\\/\",\"engine\":\"qiniu\",\"file\":{\"prefix\":\"\\/data\\/uploads\\/\"},\"qiniu\":{\"access_key\":\"p9mUPzEN5ihLHctwvBIk5w9MBckHvFSrXadVRlPY\",\"secret_key\":\"UJRv2IaSnsFUmZyXmYWyhpcrPW7WIYnslnT749Fh\",\"bucket\":\"ygwqms\",\"prefix\":\"qms\\/\",\"domain\":\"http:\\/\\/s10.xiao360.com\\/\"}}','json',1504077704,18,1504077704,0,NULL,0),(9,0,'mobile_swiper','{\"swiper\":[[{\"url\":\"http:\\/\\/tu.duowan.com\\/m\\/bxgif\\/\",\"img\":\"http:\\/\\/s10.xiao360.com\\/qms\\/\\/18\\/17\\/09\\/15\\/d1ab1bf8cc7abd4e1f667e99c69106d8.jpg\",\"title\":\"爆笑gif\"},{\"url\":\"\",\"img\":\"http:\\/\\/s10.xiao360.com\\/qms\\/\\/18\\/17\\/09\\/15\\/f0b86ac32c70630bdab21a18e904478c.png\",\"title\":\"暴走漫画\"},{\"url\":\"\",\"img\":\"http:\\/\\/s10.xiao360.com\\/qms\\/\\/18\\/17\\/09\\/15\\/521c30bb069a0ce834118a62beb6fd80.jpg\",\"title\":\"表情包\"}],[{\"url\":\"http:\\/\\/tu.duowan.com\\/m\\/bxgif\\/\",\"img\":\"http:\\/\\/s10.xiao360.com\\/qms\\/\\/18\\/17\\/09\\/15\\/4cbf184ae74bc685b52e7023085fd36b.gif\",\"title\":\"送你一朵fua\"},{\"url\":\"\",\"img\":\"http:\\/\\/s10.xiao360.com\\/qms\\/\\/18\\/17\\/09\\/15\\/62c915eab44ae6778589a6c24f7eaa91.jpg\",\"title\":\"送你一辆车\"},{\"url\":\"\",\"img\":\"http:\\/\\/s10.xiao360.com\\/qms\\/\\/18\\/17\\/09\\/15\\/248eef1ba8dbb75f0fafcb440ccde17f.jpg\",\"title\":\"送你一次旅行\"}],[{\"url\":\"\",\"img\":\"http:\\/\\/s10.xiao360.com\\/qms\\/\\/18\\/17\\/09\\/15\\/2a9ff1b3fefaf8f53e842112ac755871.jpg\",\"title\":\"送你一朵fua\"},{\"url\":\"\",\"img\":\"http:\\/\\/s10.xiao360.com\\/qms\\/\\/18\\/17\\/09\\/15\\/7ce6e5b011a7edc96dfae519fe06c3f4.jpg\",\"title\":\"送你一辆车\"},{\"url\":\"\",\"img\":\"http:\\/\\/s10.xiao360.com\\/qms\\/\\/18\\/17\\/09\\/15\\/3f9a9d2b369ad1f87a3c330b06619c77.jpg\",\"title\":\"送你一次旅行\"}]]}','json',1505110386,18,1506478658,0,NULL,0),(10,0,'lesson','{\"grade\":[{\"id\":1,\"name\":\"一年级\",\"enable\":true},{\"id\":2,\"name\":\"二年级\",\"enable\":true},{\"id\":3,\"name\":\"三年级\",\"enable\":true},{\"id\":4,\"name\":\"四年级\",\"enable\":true},{\"id\":5,\"name\":\"五年级\",\"enable\":true},{\"id\":6,\"name\":\"六年级\",\"enable\":true},{\"id\":7,\"name\":\"七年级\",\"enable\":true},{\"id\":8,\"name\":\"八年级\",\"enable\":true},{\"id\":9,\"name\":\"九年级\",\"enable\":true}],\"ability\":[{\"id\":1,\"name\":\"倾听力\",\"enable\":true},{\"id\":2,\"name\":\"阅读力\",\"enable\":true},{\"id\":3,\"name\":\"研学力\",\"enable\":true},{\"id\":4,\"name\":\"思维力\",\"enable\":true},{\"id\":5,\"name\":\"口语表达力\",\"enable\":true},{\"id\":6,\"name\":\"书面表达力\",\"enable\":true,\"sub_abilities\":[{\"id\":601,\"name\":\"文笔\",\"enable\":true},{\"id\":602,\"name\":\"构篇\",\"enable\":true},{\"id\":603,\"name\":\"材料\",\"enable\":true},{\"id\":604,\"name\":\"主题\",\"enable\":true}]},{\"id\":7,\"name\":\"表演力\",\"enable\":true}],\"attachment_type\":[{\"id\":1,\"name\":\"简案\",\"enable\":true},{\"id\":2,\"name\":\"详案\",\"enable\":true},{\"id\":3,\"name\":\"课件\",\"is_prepare\":1,\"enable\":true,\"is_video\":1},{\"id\":4,\"name\":\"学案\",\"enable\":true},{\"id\":5,\"name\":\"教程\",\"enable\":true},{\"id\":6,\"name\":\"教案\",\"enable\":true},{\"id\":7,\"name\":\"说课\",\"is_prepare\":1,\"is_video\":1,\"enable\":true},{\"id\":8,\"name\":\"示范课\",\"enable\":true}],\"product_level\":[{\"id\":1,\"name\":\"体验课\",\"unit_hour\":1,\"chapter_nums\":7,\"enable\":true},{\"id\":2,\"name\":\"标准课\",\"unit_hour\":1,\"chapter_nums\":14,\"enable\":true},{\"id\":3,\"name\":\"高端课\",\"unit_hour\":1,\"chapter_nums\":14,\"enable\":true},{\"id\":4,\"name\":\"助力课\",\"unit_hour\":1,\"chapter_nums\":14,\"enable\":true},{\"id\":5,\"name\":\"定制课\",\"unit_hour\":1,\"chapter_nums\":7,\"enable\":true}],\"lesson_level\":[{\"id\":1,\"name\":\"1级\",\"enable\":true},{\"id\":2,\"name\":\"2级\",\"enable\":true},{\"id\":3,\"name\":\"3级\",\"enable\":true},{\"id\":4,\"name\":\"4级\",\"enable\":true},{\"id\":5,\"name\":\"5级\",\"enable\":true},{\"id\":6,\"name\":\"6级\",\"enable\":true},{\"id\":7,\"name\":\"7级\",\"enable\":true},{\"id\":8,\"name\":\"8级\",\"enable\":true},{\"id\":9,\"name\":\"9级\",\"enable\":true}],\"season\":[{\"id\":1,\"name\":\"寒假\",\"mark\":\"H\",\"enable\":true},{\"id\":2,\"name\":\"春季\",\"mark\":\"C\",\"enable\":true},{\"id\":3,\"name\":\"暑假\",\"mark\":\"S\",\"enable\":true},{\"id\":4,\"name\":\"秋季\",\"mark\":\"Q\",\"enable\":true}],\"content_fields\":[{\"field\":\"c1\",\"name\":\"课程目标\",\"content\":\"\",\"enable\":true},{\"field\":\"c2\",\"name\":\"课程标准\",\"content\":\"\",\"enable\":true},{\"field\":\"c3\",\"name\":\"课程原理\",\"content\":\"\",\"enable\":true},{\"field\":\"c4\",\"name\":\"课程内容\",\"content\":\"\",\"enable\":true},{\"field\":\"c5\",\"name\":\"课程特点\",\"content\":\"\",\"enable\":true},{\"field\":\"c6\",\"name\":\"课程效果\",\"content\":\"\",\"enable\":true},{\"field\":\"c7\",\"name\":\"课程时长\",\"content\":\"\",\"enable\":true}]}','json',1506138470,18,1506138525,0,NULL,0),(11,0,'email','{\"code\":1}','json',1507256394,0,1507257210,0,NULL,0);
/*!40000 ALTER TABLE `x360p_config` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_coupon`
--

LOCK TABLES `x360p_coupon` WRITE;
/*!40000 ALTER TABLE `x360p_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_coupon` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_coupon_code`
--

LOCK TABLES `x360p_coupon_code` WRITE;
/*!40000 ALTER TABLE `x360p_coupon_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_coupon_code` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8mb4 COMMENT='班级排课记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_course_arrange`
--

LOCK TABLES `x360p_course_arrange` WRITE;
/*!40000 ALTER TABLE `x360p_course_arrange` DISABLE KEYS */;
INSERT INTO `x360p_course_arrange` VALUES (1,0,35,0,0,'',4,81,0,123,103,25,1,'Q',20171106,0,0,0,1730,1930,0,0,0,'',0,0,0,0,0,NULL,1509448285,18,1510296886,1,1510296886,18),(2,0,35,0,0,'',4,81,0,123,103,25,2,'Q',20171107,0,0,0,1730,1930,0,0,0,'',0,0,0,0,0,NULL,1509448285,18,1510296889,1,1510296889,18),(3,0,35,0,0,'',4,81,0,123,103,25,3,'Q',20171108,0,0,0,1900,1945,0,0,0,'',0,0,0,0,0,NULL,1509448285,18,1510296891,1,1510296891,18),(4,0,35,0,0,'',4,81,0,123,103,25,4,'Q',20171109,0,0,0,1900,1945,0,0,0,'',0,0,0,0,0,NULL,1509448285,18,1510296893,1,1510296893,18),(5,0,35,0,0,'',4,81,0,123,103,25,5,'Q',20171110,0,0,0,1900,1945,0,0,0,'',0,0,0,0,0,NULL,1509448285,18,1510296894,1,1510296894,18),(6,0,35,0,0,'',4,81,0,123,103,25,6,'Q',20171113,0,0,0,1730,1930,0,0,0,'',0,0,0,0,0,NULL,1509448285,18,1510296896,1,1510296896,18),(7,0,35,0,0,'',4,81,0,123,103,25,7,'Q',20171114,0,0,0,1730,1930,0,0,0,'',0,0,0,0,0,NULL,1509448285,18,1510296898,1,1510296898,18),(15,0,0,0,1,'班级名称',0,81,0,113,103,25,0,'H',20171101,0,0,0,1500,1600,0,0,0,'',0,0,0,0,0,NULL,1509525920,18,1509525920,0,NULL,0),(16,0,0,0,1,'语文-按期',0,80,0,124,103,26,0,'H',20171101,0,0,0,1200,1400,0,0,0,'',0,0,0,0,0,NULL,1509527401,18,1509527401,0,NULL,0),(17,0,35,0,1,'一个人的视听班',0,80,0,113,103,26,0,'H',20171101,0,0,0,800,845,0,0,0,'',0,0,0,0,0,NULL,1509530700,18,1509530700,0,NULL,0),(18,0,35,0,0,'',13,81,80,130,0,28,1,'H',20171110,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510296936,18,1510296936,0,NULL,0),(19,0,35,0,0,'',13,81,80,130,0,28,2,'H',20171110,0,0,0,1100,1200,0,0,0,'',0,0,0,0,0,NULL,1510296955,18,1510317879,1,1510317879,18),(20,0,35,0,0,'',11,79,85,129,0,31,1,'H',20171110,0,0,0,1000,1200,0,0,0,'',0,0,0,0,0,NULL,1510309458,18,1510309458,0,NULL,0),(21,0,35,0,0,'',11,79,85,129,0,31,2,'H',20171110,1,0,0,1400,1600,0,0,0,'',0,0,0,0,0,NULL,1510309475,18,1510309475,0,NULL,0),(22,0,35,2,0,'',0,79,0,127,0,26,0,'H',20171109,1,0,0,2000,2100,0,0,0,'',0,0,0,0,0,NULL,1510311030,18,1510317886,1,1510317886,18),(23,0,35,2,0,'',0,80,0,127,0,26,0,'H',20171108,1,0,0,330,430,0,0,0,'',0,0,0,0,0,NULL,1510311246,18,1510317890,1,1510317890,18),(24,0,35,2,0,'',0,78,0,127,0,27,0,'H',20171108,0,0,0,1915,2015,0,0,0,'',0,0,0,0,0,NULL,1510311559,18,1510317895,1,1510317895,18),(133,0,35,0,0,'',20,79,80,141,0,25,1,'H',20171114,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510632016,0,NULL,0),(134,0,35,0,0,'',20,79,80,141,0,25,2,'H',20171115,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510640263,0,NULL,0),(135,0,35,0,0,'',20,79,80,141,0,25,3,'H',20171121,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510640793,0,NULL,0),(136,0,35,0,0,'',20,79,80,141,0,25,4,'H',20171122,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510640979,0,NULL,0),(137,0,35,0,0,'',20,79,80,141,0,25,5,'H',20171128,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510641340,0,NULL,0),(138,0,35,0,0,'',20,79,80,141,0,25,6,'H',20171129,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510642449,0,NULL,0),(139,0,35,0,0,'',20,79,80,141,0,25,7,'H',20171205,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510642523,0,NULL,0),(140,0,35,0,0,'',20,79,80,141,0,25,8,'H',20171206,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510643218,0,NULL,0),(141,0,35,0,0,'',20,79,80,141,0,25,9,'H',20171212,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510645307,0,NULL,0),(142,0,35,0,0,'',20,79,80,141,0,25,10,'H',20171213,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510656653,0,NULL,0),(143,0,35,0,0,'',20,79,80,141,0,25,11,'H',20171219,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510661023,0,NULL,0),(144,0,35,0,0,'',20,79,80,141,0,25,12,'H',20171220,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510627183,18,1510657070,0,NULL,0),(169,0,35,0,0,'',22,81,0,141,0,26,1,'H',20171122,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510718161,18,1510826470,0,NULL,0),(170,0,35,0,0,'',22,81,0,141,0,26,2,'H',20171122,1,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510718161,18,1510826536,0,NULL,0),(171,0,35,0,0,'',22,81,0,141,0,26,3,'H',20171122,0,0,0,1100,1200,0,0,0,'',0,0,0,0,0,NULL,1510718161,18,1510718161,0,NULL,0),(172,0,35,0,0,'',22,81,0,141,0,26,4,'H',20171122,0,0,0,1200,1300,0,0,0,'',0,0,0,0,0,NULL,1510718161,18,1510718161,0,NULL,0),(173,0,35,0,0,'',22,81,0,141,0,26,5,'H',20171129,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510718161,18,1510825936,0,NULL,0),(174,0,35,0,0,'',22,81,0,141,0,26,6,'H',20171129,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510718161,18,1510718161,0,NULL,0),(175,0,35,0,0,'',22,81,0,141,0,26,7,'H',20171129,0,0,0,1100,1200,0,0,0,'',0,0,0,0,0,NULL,1510718161,18,1510718161,0,NULL,0),(176,0,35,0,0,'',22,81,0,141,0,26,8,'H',20171129,0,0,0,1200,1300,0,0,0,'',0,0,0,0,0,NULL,1510718161,18,1510718161,0,NULL,0),(177,0,35,0,0,'',23,27,28,141,0,27,1,'H',20171113,1,0,0,830,930,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510827605,0,NULL,0),(178,0,35,0,0,'',23,27,28,141,0,27,2,'H',20171114,1,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510827752,0,NULL,0),(179,0,35,0,0,'',23,27,28,141,0,27,3,'H',20171115,2,0,0,1200,1300,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510904880,0,NULL,0),(180,0,35,0,0,'',23,27,28,141,0,27,4,'H',20171116,2,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510906415,0,NULL,0),(181,0,35,0,0,'',23,27,28,141,0,27,5,'H',20171117,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510904150,0,NULL,0),(182,0,35,0,0,'',23,27,28,141,0,27,6,'H',20171118,2,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510967837,0,NULL,0),(183,0,35,0,0,'',23,27,28,141,0,27,7,'H',20171119,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510903287,0,NULL,0),(184,0,35,0,0,'',23,27,28,141,0,27,8,'H',20171120,0,0,0,830,930,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510822578,0,NULL,0),(185,0,35,0,0,'',23,27,28,141,0,27,9,'H',20171121,0,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510822578,0,NULL,0),(186,0,35,0,0,'',23,27,28,141,0,27,10,'H',20171122,0,0,0,1200,1300,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510822578,0,NULL,0),(187,0,35,0,0,'',23,27,28,141,0,27,11,'H',20171123,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510822578,0,NULL,0),(188,0,35,0,0,'',23,27,28,141,0,27,12,'H',20171124,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510822578,0,NULL,0),(189,0,35,0,0,'',23,27,28,141,0,27,13,'H',20171125,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510822578,0,NULL,0),(190,0,35,0,0,'',23,27,28,141,0,27,14,'H',20171126,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510822578,18,1510822578,0,NULL,0),(191,0,35,0,0,'',24,79,0,132,0,28,1,'H',20171120,0,0,0,830,930,0,0,0,'',0,0,0,0,0,NULL,1510882294,18,1510882294,0,NULL,0),(192,0,35,0,0,'',24,79,0,132,0,28,2,'H',20171123,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510882294,18,1510882294,0,NULL,0),(193,0,35,0,0,'',24,79,0,132,0,28,3,'H',20171127,0,0,0,830,930,0,0,0,'',0,0,0,0,0,NULL,1510882294,18,1510882294,0,NULL,0),(194,0,35,0,0,'',24,79,0,132,0,28,4,'H',20171130,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510882294,18,1510882294,0,NULL,0),(195,0,35,0,0,'',24,79,0,132,0,28,5,'H',20171204,0,0,0,830,930,0,0,0,'',0,0,0,0,0,NULL,1510882294,18,1510882294,0,NULL,0),(196,0,35,0,0,'',24,79,0,132,0,28,6,'H',20171207,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510882294,18,1510882294,0,NULL,0),(197,0,35,0,0,'',24,79,0,132,0,28,7,'H',20171211,0,0,0,830,930,0,0,0,'',0,0,0,0,0,NULL,1510882294,18,1510882294,0,NULL,0),(198,0,35,0,0,'',24,79,0,132,0,28,8,'H',20171214,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510882294,18,1510882294,0,NULL,0),(199,0,35,0,0,'',24,79,0,132,0,28,9,'H',20171218,0,0,0,830,930,0,0,0,'',0,0,0,0,0,NULL,1510882294,18,1510882294,0,NULL,0),(200,0,35,0,0,'',24,79,0,132,0,28,10,'H',20171221,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510882294,18,1510882294,0,NULL,0),(201,0,35,0,0,'',25,1,27,130,0,29,1,'H',20171117,0,0,0,830,930,0,0,0,'',0,0,0,0,0,NULL,1510920856,18,1510920856,0,NULL,0),(202,0,35,0,0,'',25,1,27,130,0,29,2,'H',20171117,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510920856,18,1510920856,0,NULL,0),(203,0,35,0,0,'',25,1,27,130,0,29,3,'H',20171117,2,0,0,2015,2115,0,0,0,'',0,0,0,0,0,NULL,1510920856,18,1510923053,0,NULL,0),(204,0,35,0,0,'',25,1,27,130,0,29,4,'H',20171118,1,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510920856,18,1510966819,0,NULL,0),(205,0,35,0,0,'',25,1,27,130,0,29,5,'H',20171119,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510920856,18,1510920856,0,NULL,0),(206,0,35,0,0,'',25,1,27,130,0,29,6,'H',20171124,0,0,0,830,930,0,0,0,'',0,0,0,0,0,NULL,1510920856,18,1510920856,0,NULL,0),(207,0,35,0,0,'',25,1,27,130,0,29,7,'H',20171124,0,0,0,900,1000,0,0,0,'',0,0,0,0,0,NULL,1510920856,18,1510920856,0,NULL,0),(208,0,35,0,0,'',25,1,27,130,0,29,8,'H',20171124,0,0,0,2015,2115,0,0,0,'',0,0,0,0,0,NULL,1510920856,18,1510920856,0,NULL,0),(209,0,35,0,0,'',26,4,27,129,105,25,1,'H',20171118,1,0,0,1000,1030,0,0,0,'',0,0,0,0,0,NULL,1510968786,18,1510970403,0,NULL,0),(210,0,35,0,0,'',26,4,4,129,105,29,2,'H',20171118,0,0,0,1030,1100,0,0,0,'',0,0,0,0,0,NULL,1510969001,18,1510969001,0,NULL,0),(211,0,35,0,0,'',26,4,4,129,105,29,3,'H',20171118,0,0,0,1100,1130,0,0,0,'',0,0,0,0,0,NULL,1510969021,18,1510969021,0,NULL,0),(212,0,35,0,0,'',26,4,4,129,105,29,4,'H',20171118,0,0,0,1130,1200,0,0,0,'',0,0,0,0,0,NULL,1510969032,18,1510969032,0,NULL,0),(213,0,35,0,0,'',26,4,4,129,105,29,5,'H',20171118,0,0,0,1200,1230,0,0,0,'',0,0,0,0,0,NULL,1510969050,18,1510969050,0,NULL,0),(214,0,35,0,0,'',26,4,4,129,105,29,6,'H',20171118,0,0,0,1215,1230,0,0,0,'',0,0,0,0,0,NULL,1510969080,18,1510969080,0,NULL,0),(215,0,35,0,0,'',26,4,4,129,105,29,7,'H',20171118,0,0,0,1230,1245,0,0,0,'',0,0,0,0,0,NULL,1510969120,18,1510969120,0,NULL,0);
/*!40000 ALTER TABLE `x360p_course_arrange` ENABLE KEYS */;
UNLOCK TABLES;

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
  `is_attendance` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否上课了（状态，0：未上课，1：已上课）',
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
-- Dumping data for table `x360p_course_arrange_student`
--

LOCK TABLES `x360p_course_arrange_student` WRITE;
/*!40000 ALTER TABLE `x360p_course_arrange_student` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_course_arrange_student` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_credit_rule`
--

LOCK TABLES `x360p_credit_rule` WRITE;
/*!40000 ALTER TABLE `x360p_credit_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_credit_rule` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COMMENT='客户表(市场招生)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_customer`
--

LOCK TABLES `x360p_customer` WRITE;
/*!40000 ALTER TABLE `x360p_customer` DISABLE KEYS */;
INSERT INTO `x360p_customer` VALUES (1,0,35,'刘晓宇1','','1',1265212800,1970,1,1,0,'',0,'18316227457','刘大雨',2,'',0,'','',107,4,114,0,0,81,80,0,0,0,1508930860,18,1509159754,1,1509159754,18),(2,0,35,'刘晓宇222','','1',708883200,1992,6,19,0,'',0,'15327579657','saa',1,'',0,'','',107,4,0,1,78,0,81,4,0,0,1508932971,18,1509609091,0,NULL,0),(5,0,35,'郑板桥',' bridge Zheng','1',0,1970,1,1,0,'',0,'13455648856','',1,'',0,'','郑板桥',108,4,0,1,97,85,80,1,0,0,1508987069,18,1509939661,0,NULL,0),(6,0,35,'刘备','','2',0,1970,1,1,0,'',0,'18316912981','',0,'',0,'','',108,4,0,1,74,81,80,1,0,0,1509004021,18,1509608388,0,NULL,0),(7,0,35,'关羽','guanyu','1',1083340800,2004,5,1,0,'',0,'18919281121','',1,'',0,'','',109,5,0,1,75,0,80,1,0,0,1509078411,18,1509608576,0,NULL,0),(8,0,35,'关羽','','1',0,1970,1,1,0,'',0,'19812981321','',0,'',0,'','',109,0,0,0,0,0,80,0,0,0,1509078495,18,1509158047,1,1509158047,18),(9,0,35,'罗汉','','1',0,1970,1,1,0,'',0,'18655468842','',0,'',0,'','',108,0,114,1,98,0,0,0,0,0,1509153409,18,1509939661,0,NULL,0),(10,0,35,'曹操','','1',0,1970,1,1,0,'',0,'15617165564','',0,'',0,'','',108,0,0,1,99,0,0,0,0,0,1509153880,18,1510734841,0,NULL,0),(11,0,35,'张翼德','','1',1014998400,1970,1,1,0,'',0,'13222325523','',0,'',0,'','',107,0,0,0,0,0,0,0,0,0,1509156230,18,1509157957,1,1509157957,18),(12,0,35,'李明3131','狗蛋','1',945446400,1999,12,18,2,'3',316706,'19812121231','李大江',2,'袁丽',3,'13298121298','',0,0,0,1,101,0,0,0,0,0,1509423004,18,1509940228,0,NULL,0),(13,0,35,'小明3131','狗蛋','1',945446400,1999,12,18,2,'3',316706,'19812121231','李大江',2,'袁丽',3,'13298121298','',0,0,0,1,103,0,0,0,0,0,1509423005,18,1509973661,0,NULL,0),(14,0,35,'大明','狗蛋','1',945446400,1999,12,18,2,'3',316706,'19812121231','李大江',2,'袁丽',3,'13298121298','',0,0,0,1,100,0,0,0,0,0,1509423494,18,1509940178,0,NULL,0),(15,0,35,'大明12','狗蛋','1',945446400,1999,12,18,2,'3',316706,'19812121231','李大江',2,'袁丽',3,'13298121298','',107,0,0,1,115,0,0,0,0,0,1509423561,18,1510734102,0,NULL,0),(16,0,35,'大明1211','狗蛋','1',0,1970,1,1,2,'3',316706,'19812121231','李大江',2,'袁丽',3,'13298121298','',107,0,0,1,113,0,0,0,0,0,1509423641,18,1510735005,0,NULL,0),(17,0,37,'王明1211','狗蛋','1',945446400,1970,1,1,2,'3',316706,'19812121231','李大江',2,'袁丽',3,'13298121298','',0,0,0,0,0,0,0,0,0,0,1509423714,18,1509423714,0,NULL,0),(18,0,37,'厭2112工','狗蛋','1',945446400,1970,1,1,2,'3',316706,'19812121231','李大江',2,'袁丽',3,'13298121298','',0,0,0,0,0,0,0,0,0,0,1509423714,18,1509423714,0,NULL,0),(19,0,37,'王明12111','狗蛋','1',945446400,1999,12,18,2,'3',316706,'19812121231','李大江',2,'袁丽',3,'13298121298','',0,0,0,0,0,0,0,0,0,0,1509424175,18,1509424175,0,NULL,0),(20,0,37,'厭2112工1','狗蛋','1',945446400,1999,12,18,2,'3',316706,'19812121231','李大江',2,'袁丽',3,'13298121298','',0,0,0,0,0,0,0,0,0,0,1509424175,18,1509424175,0,NULL,0),(21,0,37,'李明123','ali','1',945446400,1999,12,18,2,'3',316706,'19812142231','李大江',2,'袁丽',3,'13298121298','',0,0,0,0,0,0,0,0,0,0,1509430011,18,1509430011,0,NULL,0),(33,0,35,'考勤学员001','','1',0,1970,1,1,0,'',0,'13422345568','',0,'',0,'','',107,0,0,1,139,0,0,0,0,0,1510297686,18,1510708288,0,NULL,0),(34,0,35,'考勤学员002','','1',1507564800,2017,10,10,0,'',0,'13455487756','',0,'',0,'','',109,0,0,1,137,0,0,0,0,0,1510297716,18,1510734476,0,NULL,0),(35,0,35,'yaorui-trial','','1',0,1970,1,1,0,'',0,'18128874005','',0,'',0,'','',107,0,0,1,136,0,0,0,0,0,1510642839,18,1510662265,0,NULL,0),(36,0,35,'yaorui-customer-trial','','1',0,1970,1,1,0,'',0,'18128874006','',0,'',0,'','',109,0,0,1,138,0,0,0,0,0,1510642936,18,1510733464,0,NULL,0),(37,0,35,'yaorui-11-16','','1',0,0,0,0,0,'',0,'17768026488','',0,'',0,'','',107,0,0,1,143,0,0,0,0,0,1510824664,18,1510824699,0,NULL,0);
/*!40000 ALTER TABLE `x360p_customer` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='客户销售辅助跟进角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_customer_employee`
--

LOCK TABLES `x360p_customer_employee` WRITE;
/*!40000 ALTER TABLE `x360p_customer_employee` DISABLE KEYS */;
INSERT INTO `x360p_customer_employee` VALUES (1,0,0,5,81,101,0,0,NULL,0,NULL,0),(5,0,0,5,82,102,0,0,NULL,0,NULL,0),(7,0,0,1,85,101,0,0,NULL,0,NULL,0),(11,0,0,36,27,102,0,0,NULL,0,NULL,0);
/*!40000 ALTER TABLE `x360p_customer_employee` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COMMENT='客户跟进记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_customer_follow_up`
--

LOCK TABLES `x360p_customer_follow_up` WRITE;
/*!40000 ALTER TABLE `x360p_customer_follow_up` DISABLE KEYS */;
INSERT INTO `x360p_customer_follow_up` VALUES (7,0,35,2,1,123,'沟通很愉快',0,112,0,1,20171031,20171028,4,114,3,1509162064,18,1509411493,0,NULL,0),(8,0,35,2,1,122,'沟通沟通',1,112,20171028,0,20171028,20171028,0,114,3,1509176701,18,1509182136,0,NULL,0),(9,0,35,2,1,125,'厉害啦',1,112,20171028,0,0,20171028,5,114,3,1509177647,18,1509177647,0,NULL,0),(10,0,35,2,1,124,'eeee',1,111,20171030,0,20171028,20171028,4,114,3,1509180737,18,1509186105,0,NULL,0),(11,0,35,2,0,122,'无效沟通',1,111,20171029,1,20171028,20171029,0,114,3,1509183399,18,1509185112,0,NULL,0),(12,0,35,6,1,122,'这位同学很有天赋，不如就加入我们吧',1,112,20171031,0,0,20171031,4,114,18,1509414727,18,1509414727,0,NULL,0),(13,0,35,7,1,122,'可以可以',1,112,20171031,0,0,20171031,0,114,18,1509415148,18,1509415148,0,NULL,0),(14,0,35,5,1,122,'ffffffffff',1,111,20171117,0,0,20171108,5,113,18,1510047180,18,1510047180,0,NULL,0);
/*!40000 ALTER TABLE `x360p_customer_follow_up` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb4 COMMENT='客户意向表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_customer_intention`
--

LOCK TABLES `x360p_customer_intention` WRITE;
/*!40000 ALTER TABLE `x360p_customer_intention` DISABLE KEYS */;
INSERT INTO `x360p_customer_intention` VALUES (1,0,0,5,112,81,0,0,NULL,0,NULL,0),(3,0,0,5,124,75,0,0,NULL,0,NULL,0),(22,0,0,8,124,0,0,0,NULL,0,NULL,0),(42,0,0,9,0,0,0,0,NULL,0,NULL,0),(43,0,0,9,0,0,0,0,NULL,0,NULL,0),(141,0,0,11,123,0,0,0,NULL,0,NULL,0),(146,0,0,1,0,81,0,0,NULL,0,NULL,0),(147,0,0,1,123,0,0,0,NULL,0,NULL,0),(150,0,0,7,125,0,0,0,NULL,0,NULL,0),(152,0,0,2,123,0,0,0,NULL,0,NULL,0);
/*!40000 ALTER TABLE `x360p_customer_intention` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COMMENT='部门表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_department`
--

LOCK TABLES `x360p_department` WRITE;
/*!40000 ALTER TABLE `x360p_department` DISABLE KEYS */;
INSERT INTO `x360p_department` VALUES (1,0,0,2,'华北大区',0,1508237226,18,1508984619,1,1508984619,18),(2,0,0,2,'广东总校区',0,1508237369,18,1508378218,0,NULL,0),(3,0,0,2,'华中大地区',0,1508237378,18,1508317632,0,NULL,0),(4,0,0,2,'中原地区',0,1508237388,18,1508317300,1,1508317300,18),(5,0,0,2,'上海地区',0,1508237397,18,1508316770,1,1508316770,18),(7,0,2,1,'坂田分校',33,1508237729,18,1508237729,0,NULL,0),(8,0,2,1,'福田分校',34,1508237737,18,1508237737,0,NULL,0),(9,0,7,0,'文艺部',0,1508237769,18,1508324160,1,1508324160,18),(10,0,7,0,'数理化部',0,1508237775,18,1508400694,1,1508400694,18),(11,0,0,1,'龙岗大学',35,1508313545,18,1508325626,0,NULL,0),(12,0,0,0,'福田医院',0,1508320380,18,1508324124,1,1508324124,18),(13,0,0,1,'法国分校',36,1508323062,18,1508381529,0,NULL,0),(14,0,0,1,'福田分校',37,1508381582,18,1508381582,0,NULL,0),(15,0,0,1,'罗湖1号',38,1508393554,18,1508393554,0,NULL,0),(16,0,0,1,'加里敦分校',39,1508393583,18,1508393583,0,NULL,0),(17,0,0,1,'百汇分校',1,1510362612,18,1510362612,0,NULL,0);
/*!40000 ALTER TABLE `x360p_department` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_dictionary`
--

LOCK TABLES `x360p_dictionary` WRITE;
/*!40000 ALTER TABLE `x360p_dictionary` DISABLE KEYS */;
INSERT INTO `x360p_dictionary` VALUES (1,0,0,'sale_role','销售角色','销售角色','',0,1,0,0,NULL,0,0,NULL),(2,0,0,'jobtitle','部门职位','部门职位','',0,1,0,0,NULL,0,0,NULL),(3,0,0,'joblevel','职位级别','职位级别','',0,1,0,0,NULL,0,0,NULL),(4,0,0,'product_level','课程等级','产品等级','',0,1,0,0,NULL,0,0,NULL),(5,0,0,'from','招生来源','招生来源','',0,1,0,0,NULL,0,0,NULL),(6,0,0,'followup','跟进方式','跟进方式','',0,1,0,0,NULL,0,0,NULL),(7,0,0,'promise','诺到类型','诺到类型','',0,1,0,0,NULL,0,0,NULL),(8,0,0,'customer_status','客户跟进状态','客户跟进状态','',0,1,0,0,NULL,0,0,NULL),(9,0,0,'leave_reason','请假原因','请假原因','',0,1,0,0,NULL,0,0,NULL),(10,0,0,'comm_type','沟通方式','沟通方式','',0,1,0,0,NULL,0,0,NULL),(11,0,0,'grade','年级','课程所属年级','',0,1,0,0,NULL,0,0,NULL),(12,0,0,'season','期段','班级课程所属期段','',0,1,0,0,NULL,0,0,NULL),(13,0,0,'timelong','课时长','课时长(分钟)','',0,1,0,0,NULL,0,0,NULL),(14,0,0,'cutamount','结转退费扣款项','结转退费扣款项','',0,1,0,0,NULL,0,0,NULL),(101,0,1,'签单人','签单人','系统内置','',0,1,1508255015,18,1508929710,0,0,NULL),(102,0,1,'电话招生员','电话招生员','','',0,1,1508255053,18,1509007388,0,0,NULL),(103,0,1,'传单宣传员','传单宣传员','','',0,1,0,0,NULL,0,0,NULL),(104,0,1,'客户接待员','客户接待员','','',0,1,0,0,1508920532,0,0,NULL),(105,0,4,'常规课','常规课','常规课','',0,1,1508917664,18,1508917664,0,0,NULL),(106,0,4,'体验课','体验课','体验课','',0,1,1508917709,18,1508917709,0,0,NULL),(107,0,5,'主动上门','主动上门','主动上门','',0,1,1508918008,18,1508918008,0,0,NULL),(108,0,5,'户外广告','户外广告','户外广告','',0,1,1508918340,18,1508918340,0,0,NULL),(109,0,5,'招生活动','招生活动','招生活动','',0,1,1508918360,18,1508918360,0,0,NULL),(110,0,5,'转介绍','转介绍','转介绍','',0,1,1508918385,18,1508918385,0,0,NULL),(111,0,7,'参访校区','参访校区','参访校区','',0,1,1508918581,18,1508918581,0,0,NULL),(112,0,7,'了解课程','了解课程','了解课程','',0,1,1508918591,18,1508918591,0,0,NULL),(113,0,8,'转化成功','转化成功','转化成功','',0,1,1508918674,18,1508918674,0,0,NULL),(114,0,8,'未上门','未上门','未上门','',0,1,1508918739,18,1508918739,0,0,NULL),(115,0,8,'已试听','已试听','已试听','',0,1,1508918752,18,1508918752,0,0,NULL),(116,0,9,'病假','病假','病假','',0,1,1508918772,18,1508918772,0,0,NULL),(117,0,9,'事假','事假','事假','',0,1,1508918781,18,1508918781,0,0,NULL),(118,0,10,'电话','电话','','',0,1,1508918809,18,1508918809,0,0,NULL),(119,0,10,'微信','微信','','',0,1,1508918815,18,1508918815,0,0,NULL),(120,0,10,'QQ','QQ','','',0,1,1508918829,18,1508918829,0,0,NULL),(121,0,10,'面谈','面谈','','',0,1,1508918837,18,1508918837,0,0,NULL),(122,0,6,'电话','电话','','',0,1,1508918934,18,1508918934,0,0,NULL),(123,0,6,'微信','微信','','',0,1,1508918940,18,1508918940,0,0,NULL),(124,0,6,'短信','短信','','',0,1,1508918947,18,1508918947,0,0,NULL),(125,0,6,'QQ','QQ','','',0,1,1508918960,18,1508918960,0,0,NULL),(126,0,2,'课程顾问','课程顾问','','',0,1,1508919046,18,1508919046,0,0,NULL),(127,0,2,'学管师','学管师','','',0,1,1508919056,18,1508919056,0,0,NULL),(128,0,2,'部门主管','部门主管','','',0,1,1508919077,18,1508919077,0,0,NULL),(129,0,11,'1','小1','','',0,1,1508919282,18,1508919282,0,0,NULL),(130,0,11,'2','小2','','',0,1,1508919288,18,1508919288,0,0,NULL),(131,0,11,'3','小3','','',0,1,1508919294,18,1508919294,0,0,NULL),(134,0,11,'4','小4','','',0,1,1508919299,18,1508919299,0,0,NULL),(135,0,11,'5','小5','','',0,1,1508919305,18,1508919305,0,0,NULL),(136,0,11,'6','小6','','',0,1,1508919312,18,1508919312,0,0,NULL),(137,0,11,'7','初一','','',0,1,1508919318,18,1508919331,0,0,NULL),(138,0,11,'8','初二','','',0,1,1508919342,18,1508919342,0,0,NULL),(139,0,11,'9','初三','','',0,1,1508919352,18,1508919352,0,0,NULL),(140,0,11,'10','高一','','',0,1,1508919361,18,1508919361,0,0,NULL),(141,0,11,'11','高二','','',0,1,1508919369,18,1508919369,0,0,NULL),(142,0,11,'12','高三','','',0,1,1508919377,18,1508919377,0,0,NULL),(143,0,12,'H','寒假','H','',0,1,1508919920,18,1508919920,0,0,NULL),(144,0,12,'C','春季','C','',0,1,1508919930,18,1508919938,0,0,NULL),(145,0,12,'S','暑假','S','',0,1,1508919946,18,1508919946,0,0,NULL),(146,0,12,'Q','秋季','Q','',0,1,1508919955,18,1508919955,0,0,NULL),(147,0,13,'30','30分钟','半小时','',0,1,0,0,NULL,0,0,NULL),(148,0,13,'45','45分钟','45分钟','',0,1,0,0,NULL,0,0,NULL),(149,0,13,'60','60分钟','1小时','',0,1,0,0,NULL,0,0,NULL),(150,0,13,'90','90分钟','1个半小时','',0,1,0,0,NULL,0,0,NULL),(151,0,13,'120','120分钟','2个小时','',0,1,0,0,NULL,0,0,NULL),(152,0,13,'150','150分钟','2个半小时','',0,1,0,0,NULL,0,0,NULL),(153,0,13,'180','180分钟','3个小时','',0,1,0,0,NULL,0,0,NULL),(154,0,3,'','初级','初级','',0,1,0,0,NULL,0,0,NULL),(155,0,3,'','中级','中级','',0,1,0,0,NULL,0,0,NULL),(156,0,3,'','高级','高级','',0,1,0,0,NULL,0,0,NULL),(1001,0,1,'','二傻子','负责装傻','\0',0,1,1508927160,18,1508931777,1,18,1508931777),(1002,0,4,'','微信引流','微信引流','\0',0,1,1508929755,18,1508929755,0,0,NULL),(1003,0,14,'','税费','','',0,1,1510631119,18,1510631119,0,0,NULL),(1004,0,13,'','发票','','',0,1,1510631134,18,1510631134,0,0,NULL);
/*!40000 ALTER TABLE `x360p_dictionary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_edu_attachment`
--

DROP TABLE IF EXISTS `x360p_edu_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_edu_attachment` (
  `ea_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '教育服务附件ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned DEFAULT '0' COMMENT '校区ID',
  `table_id` int(11) unsigned DEFAULT '0' COMMENT '主表名ID(用枚举定义ID，research:1,test:2,scheme:3,works:4)',
  `record_id` int(11) unsigned DEFAULT '0' COMMENT '记录ID',
  `url` varchar(255) DEFAULT NULL COMMENT '附件路径',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`ea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='教育服务相关附件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_edu_attachment`
--

LOCK TABLES `x360p_edu_attachment` WRITE;
/*!40000 ALTER TABLE `x360p_edu_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_edu_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_edu_comment`
--

DROP TABLE IF EXISTS `x360p_edu_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_edu_comment` (
  `ec_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'edu_comment教育评论表',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL,
  `table_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '主表名ID(用枚举定义ID，1:scheme_video)',
  `record_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '记录ID',
  `comment` varchar(255) NOT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`ec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_edu_comment`
--

LOCK TABLES `x360p_edu_comment` WRITE;
/*!40000 ALTER TABLE `x360p_edu_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_edu_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_edu_growup`
--

DROP TABLE IF EXISTS `x360p_edu_growup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_edu_growup` (
  `eg_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '成长对比ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
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
-- Dumping data for table `x360p_edu_growup`
--

LOCK TABLES `x360p_edu_growup` WRITE;
/*!40000 ALTER TABLE `x360p_edu_growup` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_edu_growup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_edu_growup_item`
--

DROP TABLE IF EXISTS `x360p_edu_growup_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_edu_growup_item` (
  `egi_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '成长对比记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `eg_id` int(11) unsigned NOT NULL COMMENT '成长对比ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '成长对比标题',
  `before_content` text NOT NULL COMMENT '成长对比之前内容（文字描述)',
  `after_content` text NOT NULL COMMENT '成长对比之后内容（文字描述)',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`egi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成长对比记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_edu_growup_item`
--

LOCK TABLES `x360p_edu_growup_item` WRITE;
/*!40000 ALTER TABLE `x360p_edu_growup_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_edu_growup_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_edu_growup_pic`
--

DROP TABLE IF EXISTS `x360p_edu_growup_pic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_edu_growup_pic` (
  `egp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '成长对比图片ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
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
-- Dumping data for table `x360p_edu_growup_pic`
--

LOCK TABLES `x360p_edu_growup_pic` WRITE;
/*!40000 ALTER TABLE `x360p_edu_growup_pic` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_edu_growup_pic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_edu_research`
--

DROP TABLE IF EXISTS `x360p_edu_research`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_edu_research` (
  `er_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '调查ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `title` varchar(255) DEFAULT '' COMMENT '调查标题',
  `edu_eid` int(11) unsigned DEFAULT '0' COMMENT '导师ID',
  `way` tinyint(2) unsigned DEFAULT '0' COMMENT '调查方式(比如QQ,微信,问卷,面谈等)具体详情看配置edu',
  `fire_time` int(11) unsigned DEFAULT '0' COMMENT '调查日期时间',
  `content` text COMMENT '调查内容或结果（文字描述)',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`er_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成长调查表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_edu_research`
--

LOCK TABLES `x360p_edu_research` WRITE;
/*!40000 ALTER TABLE `x360p_edu_research` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_edu_research` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_edu_scheme`
--

DROP TABLE IF EXISTS `x360p_edu_scheme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_edu_scheme` (
  `es_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '方案ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `title` varchar(255) DEFAULT '' COMMENT '成长方案标题',
  `ability_ids` varchar(255) DEFAULT '' COMMENT '提升能力ID，多ID之间用逗号分隔',
  `edu_eid` int(11) unsigned DEFAULT '0' COMMENT '导师ID',
  `content` text COMMENT '调查内容或结果（文字描述)',
  `start_time` int(11) unsigned DEFAULT '0' COMMENT '开始执行时间',
  `end_time` int(11) unsigned DEFAULT NULL COMMENT '结束时间',
  `is_confirm` tinyint(1) DEFAULT '0' COMMENT '是否确认',
  `confirm_time` int(11) unsigned DEFAULT '0' COMMENT '确认时间',
  `confirm_uid` int(11) unsigned DEFAULT '0' COMMENT '确认UID',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '状态:(0:待确认,1:已确认,2:执行中,10：执行完毕)',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`es_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成长方案表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_edu_scheme`
--

LOCK TABLES `x360p_edu_scheme` WRITE;
/*!40000 ALTER TABLE `x360p_edu_scheme` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_edu_scheme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_edu_scheme_video`
--

DROP TABLE IF EXISTS `x360p_edu_scheme_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_edu_scheme_video` (
  `esv_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '视频ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `es_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '方案ID',
  `title` varchar(255) DEFAULT '' COMMENT '成长方案标题',
  `url` varchar(255) DEFAULT '' COMMENT '视频URL',
  `size` varchar(50) NOT NULL COMMENT '视频大小',
  `type` varchar(30) DEFAULT NULL COMMENT '视频类型',
  `from_type` tinyint(1) unsigned DEFAULT '0' COMMENT '来源(0:家长,1:导师)',
  `view_times` int(11) unsigned DEFAULT '0' COMMENT '播放次数',
  `star` tinyint(1) unsigned DEFAULT NULL COMMENT '导师给视频内容打的星星数目',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`esv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='方案视频表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_edu_scheme_video`
--

LOCK TABLES `x360p_edu_scheme_video` WRITE;
/*!40000 ALTER TABLE `x360p_edu_scheme_video` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_edu_scheme_video` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_edu_test`
--

DROP TABLE IF EXISTS `x360p_edu_test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_edu_test` (
  `et_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '测评ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `title` varchar(255) DEFAULT '' COMMENT '测评标题',
  `edu_eid` int(11) unsigned DEFAULT '0' COMMENT '导师ID',
  `way` varchar(32) DEFAULT '' COMMENT '测评方式(比如QQ,微信,问卷,面谈等)',
  `fire_time` int(11) unsigned DEFAULT '0' COMMENT '测评日期时间',
  `content` text COMMENT '调查内容或结果（文字描述)',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`et_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='测评结果表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_edu_test`
--

LOCK TABLES `x360p_edu_test` WRITE;
/*!40000 ALTER TABLE `x360p_edu_test` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_edu_test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_edu_works`
--

DROP TABLE IF EXISTS `x360p_edu_works`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_edu_works` (
  `ew_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '作品标题',
  `content` text NOT NULL COMMENT '内容',
  `is_class_collect` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否班级作品',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ew_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_edu_works`
--

LOCK TABLES `x360p_edu_works` WRITE;
/*!40000 ALTER TABLE `x360p_edu_works` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_edu_works` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COMMENT='员工表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee`
--

LOCK TABLES `x360p_employee` WRITE;
/*!40000 ALTER TABLE `x360p_employee` DISABLE KEYS */;
INSERT INTO `x360p_employee` VALUES (1,0,'刘子云','','0,2,1','0,3','','','',0,'','1','17768026488','888@qq.com','','','',825609600,1996,3,1,0,1,0,0,'',0,1498189748,1,1508559924,1,NULL,18,0),(2,0,'lzy','','0,1,2','0,3','','','',21,'','1','13466548865','liuziy@qq.com','','','',949507200,2000,2,3,0,1,0,0,'',1,1498198413,1,1508559932,1,1508559932,18,0),(3,0,'刘子云3号','','1','1,3,4','','','',18,'liuzy003','1','13544654112','qwe@qq.com','','','http://s10.xiao360.com/qms/avatar/18/17/09/28/62a96cb4aa2d0e6520489da00cf403d7.jpg',1206979200,2017,9,28,0,1,0,0,'',0,1498198630,1,1506569179,0,NULL,0,0),(4,0,'刘子云4号','','2','2','','','',0,'','1','15327579658','lzy@qq.com','','','',0,2017,7,19,0,1,0,0,'',0,1498198688,1,1498296783,0,NULL,0,0),(5,0,'刘子云6号','','2','3','','','',0,'','1','','','','','',1496246400,2017,8,1,0,1,0,0,'',0,1498198751,1,1501553058,1,1501553058,18,0),(6,0,'刘子云5号呢','','3','3','','','',0,'','1','','','','','',1496332800,2017,6,23,0,1,0,0,'',0,1498198832,1,1498206223,1,1498206223,1,0),(7,0,'汤景呃呃呃','','2','1','','','',0,'','1','','','','','',1262275200,2017,7,18,0,1,0,0,'',0,1498206948,1,1500344757,1,1500344757,18,0),(8,0,'姚瑞','','2','1','','','',0,'','1','','','','','',760204800,2017,7,4,0,1,0,0,'',0,1498207438,1,1499141471,1,1499141471,1,0),(9,0,'王力宏','','2','1,2','','','',0,'','1','','','','','',681058800,2017,7,18,0,1,0,0,'',0,1498208508,1,1500350430,1,1500350430,18,0),(10,0,'姚瑞','','2,4,6','1,2,3,4','','','',42,'yaorui','1','','','','','',1496246400,2017,9,13,0,1,0,0,'',1,1498211061,1,1505300142,0,NULL,0,0),(11,0,'姚瑞3号','','0,1','0,2','','','',0,'','1','','','','','',1496332800,2017,9,5,0,1,0,0,'',0,1498211169,1,1504603550,1,1504603550,18,0),(22,0,'学员1号007','','0,9','0,2','','','',0,'','1','','','','','',983376000,2017,7,18,0,1,0,0,'',1,1499140627,1,1500361423,1,1500361423,18,0),(23,0,'测试学员008','','0,13','0,2','','','',0,'','1','','','','','',983376000,2017,7,18,0,1,0,0,'',0,1499141341,1,1500360789,1,1500360789,18,0),(24,0,'员工001','','6','2','','','',0,'','1','15487468863','chushi@qq.com','','','',760204800,2017,7,18,0,1,0,0,'',0,1500004316,18,1500344303,1,1500344303,18,0),(25,0,'员工002','','2,4','1,2','','','',22,'yuang002','1','13544658846','employee002@qq.com','','','',0,0,0,0,0,1,0,0,'',1,1500009702,18,1500021254,1,1500021254,18,0),(26,0,'测试员工009','','1,2','1,2,3','','','',0,'','1','13466541254','abc@qq.com','','','',1272384000,2017,7,18,0,1,0,0,'',0,1500282639,18,1500344269,1,1500344269,18,0),(27,0,'姚瑞','','1','1,28,30','','','',25,'yaorui','1','17768026485','6666@qq.com','','','',691689600,2017,7,17,0,1,0,0,'',1,1500285333,18,1500285333,0,NULL,0,0),(28,0,'姚瑞2','','2,2','10,20,26,4','','','',26,'yaorui2','1','17768026475','7777@qq.com','','','',693158400,2017,9,7,0,1,0,0,'',1,1500285333,18,1500285333,0,NULL,0,0),(29,0,'test','','4','1,3','','','',0,'','1','17798652365','','','','',0,0,0,0,0,1,0,0,'',0,1500290945,18,1500344248,1,1500344248,18,0),(30,0,'yaorui','','1,2','2,3','','','',27,'17768026485','1','17768026485','yaorui@qq.com','','','',1498838400,2017,9,5,0,1,0,0,'',1,1500427724,18,1504603570,1,1504603570,18,0),(31,0,'测试员工007','','1,2,9','1,28','','','',0,'','2','18886865656','5555@qq.com','','','',831312000,2017,7,19,0,1,0,0,'',0,1500431581,18,1500431581,0,NULL,0,0),(32,0,'测试员工008','','1,2,9','1,28','','','',0,'','1','18864513321','5555@qq.com','','','',831312000,2017,7,19,0,1,0,0,'',0,1500431582,18,1500431582,0,NULL,0,0),(34,0,'姚瑞','','4','3','','','',28,'17768026489','1','17768026489','562247587@qq.com','','','',1500393600,2017,9,5,0,1,0,0,'',1,1500435315,18,1504603792,1,1504603792,18,0),(35,0,'yaotest','','1,2','1,3,4','','','',36,'yaotest','1','15555555555','55dfdf5@qq.com','','','',1268236800,2017,9,5,0,1,0,0,'',1,1504246940,18,1504603796,1,1504603796,18,0),(36,0,'董振峰','','1','3','','','',37,'13006617501','1','13006617501','','','','',0,0,0,0,0,1,0,0,'',1,1504755918,18,1504755918,0,NULL,0,0),(38,0,'刘大大','','1,2','1','','','',38,'liudd','1','15334086673','877470170@qq.com','','','',1220284800,2017,9,8,0,1,0,0,'',1,1504842197,18,1504842197,0,NULL,0,0),(39,0,'马云','','2,1','4,3','','','',39,'17768026486','1','17768026486','jack@alibaba.com','','','',0,0,0,0,0,1,0,0,'',1,1505183656,18,1505183656,0,NULL,0,0),(41,0,'test123','','1','3,4','','','',0,'','1','','','','','',0,0,0,0,0,1,0,0,'',0,1505207222,18,1505207222,0,NULL,0,0),(43,0,'刘子云','','1','2','','','',43,'liuzy1','1','15327579657','877470171@qq.com','','','',1505923200,2017,9,21,0,1,0,0,'',1,1505984051,18,1505984051,0,NULL,0,0),(44,0,'董老师','','1,2','9','','','',44,'dongls','1','13006617500','dongls@lantel.net','','','',0,0,0,0,0,1,0,0,'',1,1506323831,18,1506323831,0,NULL,0,0),(45,0,'金城武','','1,2','1,2','','','',0,'','1','13733519526','13733519526@163.com','','','',1188921600,2017,9,28,0,1,0,0,'',0,1506593593,18,1506593593,0,NULL,0,0),(48,0,'xiaoming','','1','1','','','',0,'','0','','','','','',0,0,0,0,0,1,0,0,'',0,1507286229,0,1507286229,0,NULL,0,0),(49,0,'xiaoming','','1','1','','','',0,'','0','','','','','',0,0,0,0,0,1,0,0,'',0,1507286328,0,1507286328,0,NULL,0,0),(50,0,'xiaoming','','1','1','','','',0,'','0','','','','','',0,0,0,0,0,1,0,0,'',0,1507286330,0,1507286330,0,NULL,0,0),(51,0,'xiaoming','','1','1,2','','','',0,'','0','','','','','',0,0,0,0,0,1,0,0,'',0,1507286930,0,1507286930,0,NULL,0,0),(52,0,'xiaohong','','1','1,2','','','',0,'','0','','','','','',0,0,0,0,0,1,0,0,'',0,1507286937,0,1507286937,0,NULL,0,0),(59,0,'小虹','','1,2','1,3','','','',42,'xiaohong','1','','','','','',0,0,0,0,0,1,0,0,'',1,1507287269,0,1507346159,1,1507346159,0,0),(60,0,'荆轲','','1,2','1','','','',0,'','0','','','','','',0,0,0,0,0,1,0,0,'',0,1507346831,0,1507346831,0,NULL,0,0),(66,0,'张羽','','1,2','1','','','',43,'zhangyu','1','','','','','',0,0,0,0,0,1,0,0,'',0,1507347006,0,1507348921,0,NULL,0,0),(67,0,'郑庙华','','1','3,4','','','',0,'','1','13125132038','496694940@qq.com','','','',1265126400,2010,2,3,0,1,0,0,'',0,1507884714,18,1507884714,0,NULL,0,0),(68,0,'庙华','','1','2','','','',0,'','1','13125132030','49669494@qq.com','','','',1506960000,2017,10,3,0,1,0,0,'',0,1507885142,18,1507885142,0,NULL,0,0),(69,0,'瓜皮','','3','32','','','',0,'','1','15946588845','246546@mak.com','','','',1507046400,2017,10,4,0,1,0,0,'',0,1507886561,18,1507886561,0,NULL,0,0),(70,0,'aaa','','15','1','','','',0,'','1','13125548485','dsfddfs@qq.coo','','','',1506960000,2017,10,3,0,1,0,0,'',0,1507977418,18,1507977418,0,NULL,0,0),(71,0,'呱呱','','1,3','35','','','',0,'','1','15221358889','123233@qq.com','','','',1507478400,2017,10,9,0,1,0,0,'',0,1508380061,18,1508566055,1,1508566055,18,0),(72,0,'梅梅','','2','36','','','',0,'','2','15556478987','46546@163.com','','','',1506960000,2017,10,3,0,1,0,0,'',0,1508380109,18,1508380109,0,NULL,0,0),(73,0,'李大厨','','1,2,3','35','','','',0,'','1','18888888888','888888@188.com','','','',1184083200,2007,7,11,0,1,0,0,'',0,1508557923,18,1508725417,1,1508725417,18,0),(74,0,'李老师','','1','35','','','',0,'','1','13588888888','45454654@233.com','','','',750009600,1993,10,8,0,1,0,0,'',0,1508558121,18,1508563369,1,1508563369,18,0),(75,0,'吴老师','','1','38','','','',0,'','2','15945668788','4646@qq.com','','','',1050422400,2003,4,16,0,1,0,0,'',0,1508559612,18,1508559612,0,NULL,0,0),(76,0,'测试老师','','1','36','','','',44,'fdsf','1','13665555469','dfs@ss.cpm','','','',1245254400,2009,6,18,0,1,0,0,'',1,1508562878,18,1508566124,1,1508566124,18,0),(77,0,'李梅','','1','37','','','',0,'','1','13655556666','54646@base.com','','','',1381161600,2013,10,8,0,1,0,0,'',0,1508566114,18,1508566114,0,NULL,0,0),(78,0,'小明3','','1','35','','','',47,'gdfsgd','1','13555454666','dsfjsa@base.com','','','',1380211200,2013,9,27,0,1,0,0,'',1,1508566207,18,1510040142,0,NULL,0,0),(79,0,'大明','','1','35','','','',46,'dsfdsf','1','13544446666','4565@base.com','','','',1508342400,2017,10,19,0,1,0,0,'',1,1508570215,18,1510040151,0,NULL,0,0),(80,0,'三明','','1','35','','','',45,'admin@base','1','13988888888','fdsfs@base.com','','','',1381939200,2013,10,17,0,1,0,0,'',0,1508570272,18,1508928474,0,NULL,0,0),(81,0,'刘子云','','1','36,35','','','',0,'','1','13555556666','dsfs@base.com','','','',1318435200,2011,10,13,0,1,0,0,'',0,1508570384,18,1508570384,0,NULL,0,0),(82,0,'姚瑞','','1,2','39,38','','','',0,'','1','15888887777','41646@base.com','','','',1507132800,2017,10,5,0,1,0,0,'',0,1508725191,18,1508725191,0,NULL,0,0),(83,0,'姚瑞1','','1,2','37,36','','','',0,'','1','15277774855','fddsf@na.com','','','',1507651200,2017,10,11,0,1,0,0,'',0,1508725244,18,1508725244,0,NULL,0,0),(84,0,'asfdsf','','1','37,35','','','',0,'','1','13548879963','fdsfsd@base.com','','','',1507651200,2017,10,11,0,1,0,0,'',0,1508725785,18,1508725800,0,NULL,0,0),(85,0,'王小明','','1,2','35,36','','','',0,'','1','13146524456','146@base.com','','','',1506873600,2017,10,2,0,1,0,0,'',0,1508726988,18,1510749550,0,NULL,0,0);
/*!40000 ALTER TABLE `x360p_employee` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_employee_dept`
--

LOCK TABLES `x360p_employee_dept` WRITE;
/*!40000 ALTER TABLE `x360p_employee_dept` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_employee_dept` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8mb4 COMMENT='教师课时产出记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee_lesson_hour`
--

LOCK TABLES `x360p_employee_lesson_hour` WRITE;
/*!40000 ALTER TABLE `x360p_employee_lesson_hour` DISABLE KEYS */;
INSERT INTO `x360p_employee_lesson_hour` VALUES (101,0,35,79,80,0,141,0,20,0,'',133,0,103,20171114,800,900,1,1.00,60,1.00,200.00,1,1510632016,18,1510632016,0,NULL,0),(102,0,35,79,80,0,141,0,20,0,'',134,0,121,20171115,800,900,3,1.00,60,3.00,600.00,1,1510640263,18,1510640264,0,NULL,0),(103,0,35,79,80,0,141,0,20,0,'',135,0,122,20171121,800,900,3,1.00,60,3.00,600.00,1,1510640793,18,1510640794,0,NULL,0),(104,0,35,79,80,0,141,0,20,0,'',136,0,123,20171122,800,900,3,1.00,60,3.00,600.00,1,1510640979,18,1510640980,0,NULL,0),(105,0,35,79,80,0,141,0,20,0,'',137,0,124,20171128,800,900,1,1.00,60,1.00,200.00,1,1510641340,18,1510641340,0,NULL,0),(106,0,35,79,80,0,141,0,20,0,'',138,0,125,20171129,800,900,3,1.00,60,3.00,600.00,1,1510642449,18,1510642450,0,NULL,0),(107,0,35,81,80,0,141,0,20,0,'',139,0,126,20171205,800,900,2,1.00,60,2.00,400.00,1,1510642524,18,1510642524,0,NULL,0),(108,0,35,79,80,0,141,0,20,0,'',140,0,127,20171206,800,900,3,1.00,60,3.00,600.00,1,1510643218,18,1510643219,0,NULL,0),(109,0,35,79,80,0,141,0,20,0,'',141,0,128,20171212,800,900,1,1.00,60,1.00,200.00,1,1510645308,18,1510645308,0,NULL,0),(110,0,35,79,80,0,141,0,20,0,'',142,0,129,20171213,800,900,2,1.00,60,2.00,400.00,1,1510656654,18,1510656654,0,NULL,0),(111,0,35,79,80,0,141,0,20,0,'',144,0,130,20171220,800,900,1,1.00,60,1.00,200.00,1,1510657070,18,1510657070,0,NULL,0),(112,0,35,79,80,0,141,0,20,0,'',143,0,131,20171219,800,900,1,1.00,60,1.00,200.00,1,1510661023,18,1510661023,0,NULL,0),(113,0,35,81,0,0,141,0,22,0,'',173,0,141,20171129,800,900,4,1.00,60,4.00,800.00,1,1510825936,18,1510826411,0,NULL,0),(114,0,35,81,0,0,141,0,22,0,'',169,0,133,20171122,800,900,1,1.00,60,1.00,200.00,1,1510826471,18,1510826471,0,NULL,0),(115,0,35,81,0,0,141,0,22,0,'',170,0,142,20171122,900,1000,2,1.00,60,2.00,400.00,1,1510826536,18,1510826708,0,NULL,0),(116,0,35,27,28,0,141,0,23,0,'',177,0,143,20171113,830,930,1,1.00,60,1.00,200.00,1,1510827605,18,1510827605,0,NULL,0),(117,0,35,27,28,0,141,0,23,0,'',178,0,144,20171114,800,900,4,1.00,60,4.00,800.00,1,1510827752,18,1510829801,0,NULL,0),(118,0,35,27,28,0,141,0,23,0,'',181,0,148,20171117,900,1000,1,1.00,60,1.00,200.00,1,1510892404,18,1510904150,1,1510904150,18),(119,0,35,27,28,0,141,0,23,0,'',183,0,150,20171119,900,1000,1,1.00,60,1.00,200.00,1,1510902242,18,1510903287,1,1510903287,18),(120,0,35,27,28,0,141,0,23,0,'',179,0,151,20171115,1200,1300,1,1.00,60,1.00,200.00,1,1510904880,18,1510904880,0,NULL,0),(121,0,35,1,27,0,130,0,25,0,'',203,0,157,20171117,2015,2115,5,1.00,60,5.00,600.00,1,1510921399,18,1510923053,0,NULL,0),(122,0,35,1,27,0,130,0,25,0,'',204,0,158,20171118,900,1000,4,1.00,60,4.00,480.00,1,1510966819,18,1510967690,0,NULL,0),(123,0,35,27,28,0,141,0,23,0,'',182,0,145,20171118,900,1000,1,1.00,60,1.00,200.00,1,1510967838,18,1510967838,0,NULL,0),(124,0,35,4,27,0,129,105,26,0,'',209,0,159,20171118,1000,1030,1,1.00,60,1.00,120.00,1,1510970403,18,1510970403,0,NULL,0);
/*!40000 ALTER TABLE `x360p_employee_lesson_hour` ENABLE KEYS */;
UNLOCK TABLES;

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
  `introduce` text NOT NULL COMMENT '介绍',
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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee_profile`
--

LOCK TABLES `x360p_employee_profile` WRITE;
/*!40000 ALTER TABLE `x360p_employee_profile` DISABLE KEYS */;
INSERT INTO `x360p_employee_profile` VALUES (2,0,48,'',NULL,NULL,NULL,1507286229,0,1507286229,0,NULL,0),(3,0,49,'',NULL,NULL,NULL,1507286328,0,1507286328,0,NULL,0),(4,0,50,'',NULL,NULL,NULL,1507286330,0,1507286330,0,NULL,0),(5,0,51,'',NULL,NULL,NULL,1507286930,0,1507286930,0,NULL,0),(6,0,52,'',NULL,NULL,NULL,1507286937,0,1507286937,0,NULL,0),(13,0,59,'',NULL,NULL,NULL,1507287269,0,1507287269,0,NULL,0),(14,0,60,'',NULL,NULL,NULL,1507346831,0,1507346831,0,NULL,0),(20,0,66,'',NULL,NULL,NULL,1507347006,0,1507347006,0,NULL,0),(21,0,67,'',NULL,NULL,NULL,1507884714,18,1507884714,0,NULL,0),(22,0,68,'',NULL,NULL,NULL,1507885142,18,1507885142,0,NULL,0),(23,0,69,'',NULL,NULL,NULL,1507886561,18,1507886561,0,NULL,0),(24,0,70,'',NULL,NULL,NULL,1507977418,18,1507977418,0,NULL,0),(25,0,71,'',NULL,NULL,NULL,1508380061,18,1508380061,0,NULL,0),(26,0,72,'',NULL,NULL,NULL,1508380109,18,1508380109,0,NULL,0),(27,0,73,'',NULL,NULL,NULL,1508557923,18,1508557923,0,NULL,0),(28,0,74,'',NULL,NULL,NULL,1508558121,18,1508558121,0,NULL,0),(29,0,75,'',NULL,NULL,NULL,1508559612,18,1508559612,0,NULL,0),(30,0,76,'',NULL,NULL,NULL,1508562878,18,1508562878,0,NULL,0),(31,0,77,'',NULL,NULL,NULL,1508566114,18,1508566115,0,NULL,0),(32,0,78,'',NULL,NULL,NULL,1508566207,18,1508566207,0,NULL,0),(33,0,79,'',NULL,NULL,NULL,1508570215,18,1508570215,0,NULL,0),(34,0,80,'',NULL,NULL,NULL,1508570272,18,1508570272,0,NULL,0),(35,0,81,'',NULL,NULL,NULL,1508570384,18,1508570384,0,NULL,0),(36,0,82,'',NULL,NULL,NULL,1508725191,18,1508725191,0,NULL,0),(37,0,83,'',NULL,NULL,NULL,1508725244,18,1508725244,0,NULL,0),(38,0,84,'',NULL,NULL,NULL,1508725785,18,1508725785,0,NULL,0),(39,0,85,'',NULL,NULL,NULL,1508726988,18,1508726988,0,NULL,0);
/*!40000 ALTER TABLE `x360p_employee_profile` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_employee_receipt`
--

LOCK TABLES `x360p_employee_receipt` WRITE;
/*!40000 ALTER TABLE `x360p_employee_receipt` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_employee_receipt` ENABLE KEYS */;
UNLOCK TABLES;

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
  PRIMARY KEY (`er_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COMMENT='用户所属角色表(每一个用户可以拥有0个或多个用户角色)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee_role`
--

LOCK TABLES `x360p_employee_role` WRITE;
/*!40000 ALTER TABLE `x360p_employee_role` DISABLE KEYS */;
INSERT INTO `x360p_employee_role` VALUES (1,0,48,1,0,0,NULL),(2,0,49,1,0,0,NULL),(3,0,50,1,0,0,NULL),(4,0,51,1,0,0,NULL),(5,0,52,1,0,0,NULL),(14,0,60,1,0,0,NULL),(15,0,60,2,0,0,NULL),(26,0,66,1,0,0,NULL),(27,0,66,2,0,0,NULL),(28,0,67,1,0,0,NULL),(29,0,68,1,0,0,NULL),(30,0,69,3,0,0,NULL),(31,0,70,15,0,0,NULL),(34,0,72,2,0,0,NULL),(37,0,75,1,0,0,NULL),(39,0,77,1,0,0,NULL),(40,0,78,1,0,0,NULL),(41,0,79,1,0,0,NULL),(42,0,80,1,0,0,NULL),(43,0,81,1,0,0,NULL),(47,0,82,1,0,0,NULL),(48,0,82,2,0,0,NULL),(49,0,83,1,0,0,NULL),(50,0,83,2,0,0,NULL),(51,0,84,1,0,0,NULL),(52,0,85,1,0,0,NULL),(53,0,85,2,0,0,NULL);
/*!40000 ALTER TABLE `x360p_employee_role` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='员工部门职能表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee_subject`
--

LOCK TABLES `x360p_employee_subject` WRITE;
/*!40000 ALTER TABLE `x360p_employee_subject` DISABLE KEYS */;
INSERT INTO `x360p_employee_subject` VALUES (1,0,0,80,101,0,0,0,0,NULL,0),(2,0,0,81,102,0,0,0,0,NULL,0),(3,0,0,82,102,0,0,0,0,NULL,0),(4,0,0,82,103,0,0,0,0,NULL,0),(5,0,0,83,103,0,0,0,0,NULL,0),(6,0,0,83,102,0,0,0,0,NULL,0),(7,0,0,84,105,0,0,0,0,NULL,0),(8,0,0,84,103,0,0,0,0,NULL,0),(9,0,0,79,103,0,0,0,0,NULL,0);
/*!40000 ALTER TABLE `x360p_employee_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_event`
--

DROP TABLE IF EXISTS `x360p_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_event` (
  `event_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `event_name` varchar(255) NOT NULL DEFAULT '' COMMENT '活动名称',
  `is_event_online` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否线上活动',
  `bids` varchar(255) NOT NULL DEFAULT '' COMMENT '活动执行校区id用逗号分隔',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'scope位class的时候cid有效',
  `scope` enum('class','branch','global') NOT NULL DEFAULT 'global' COMMENT '活动范围',
  `event_type` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '活动类型(1:讲座,2:期中展示,3:期末展示,4:优秀评比)',
  `event_start_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动开始时间',
  `event_end_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动结束时间',
  `share_title` varchar(255) NOT NULL DEFAULT '' COMMENT '宣传分享标题',
  `share_image_url` varchar(255) NOT NULL DEFAULT '' COMMENT '分享图片路径(300*300)',
  `event_image_url` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图片(600px * 300px)',
  `event_meta` text COMMENT '活动结构化描述',
  `event_content` text NOT NULL COMMENT '活动内容介绍',
  `link_url` varchar(255) DEFAULT NULL COMMENT '链接URL',
  `status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态（0:已禁用,1:正常,2:已结束,3:已取消)',
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
-- Dumping data for table `x360p_event`
--

LOCK TABLES `x360p_event` WRITE;
/*!40000 ALTER TABLE `x360p_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_event_attachment`
--

DROP TABLE IF EXISTS `x360p_event_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_event_attachment` (
  `ea_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动附件ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `event_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
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
-- Dumping data for table `x360p_event_attachment`
--

LOCK TABLES `x360p_event_attachment` WRITE;
/*!40000 ALTER TABLE `x360p_event_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_event_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_event_student`
--

DROP TABLE IF EXISTS `x360p_event_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_event_student` (
  `es_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `event_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`es_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动和报名活动的学生的关联中间表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_event_student`
--

LOCK TABLES `x360p_event_student` WRITE;
/*!40000 ALTER TABLE `x360p_event_student` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_event_student` ENABLE KEYS */;
UNLOCK TABLES;

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
  `file_name` varchar(128) NOT NULL,
  `file_size` bigint(20) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COMMENT='系统文件表(所有上传的附件文件，都会记录下来，有一个唯一的file_id)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_file`
--

LOCK TABLES `x360p_file` WRITE;
/*!40000 ALTER TABLE `x360p_file` DISABLE KEYS */;
INSERT INTO `x360p_file` VALUES (1,0,'lesson_cover_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/lesson_cover_picture/18/17/10/25/b195dc2d584620000ef621fd967293ed.jpg','http://s10.xiao360.com/qms/lesson_cover_picture/18/17/10/25/b195dc2d584620000ef621fd967293ed.jpg','image','006BfqPigy1fgry0r2sh8j30c80ay3zd.jpg',38377,1508929173,18,1508929173,0,NULL,0),(2,0,'lesson_cover_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/lesson_cover_picture/18/17/10/25/e0b171cb6560c45239b29ce961976b9c.jpg','http://s10.xiao360.com/qms/lesson_cover_picture/18/17/10/25/e0b171cb6560c45239b29ce961976b9c.jpg','image','006BfqPigy1fgry0r2sh8j30c80ay3zd.jpg',38377,1508929186,18,1508929186,0,NULL,0),(4,0,'avatar','qiniu',18,'/storage1/www/pro.xiao360.com/public/data/uploads/avatar/18/17/11/04/1e5aa583869f2e8865cbf3591a57b3af.png','http://s10.xiao360.com/qms/avatar/18/17/11/04/1e5aa583869f2e8865cbf3591a57b3af.png','image','_XU8C8D$USHJLVY8(V@K@TM.png',13903,1509761616,18,1509761616,0,NULL,0),(5,0,'student_avatar','qiniu',3,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/6b32d344c93b652e2ab13af5a202b8d1.png','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/6b32d344c93b652e2ab13af5a202b8d1.png','image','Koala',1513403,1509764606,18,1509764606,0,NULL,0),(6,0,'student_avatar','qiniu',3,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/4cd672b57120eb84e31eec3df4838faf.png','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/4cd672b57120eb84e31eec3df4838faf.png','image','Penguins',1513403,1509764632,18,1509764632,0,NULL,0),(7,0,'student_avatar','qiniu',3,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/d91b485c9e463bd1e154c732f036d0d5.png','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/d91b485c9e463bd1e154c732f036d0d5.png','image','Penguins',1513403,1509764667,18,1509764667,0,NULL,0),(8,0,'student_avatar','qiniu',3,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/0f9a3544a77c36c961ca4336eb611998.png','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/0f9a3544a77c36c961ca4336eb611998.png','image','Penguins',1111777,1509764709,18,1509764709,0,NULL,0),(9,0,'student_avatar','qiniu',3,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/d3d72c9ee0b2080bfcdbd6166cb35656.png','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/d3d72c9ee0b2080bfcdbd6166cb35656.png','image','Koala',1252707,1509764773,18,1509764773,0,NULL,0),(10,0,'student_avatar','qiniu',4,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/fcbb9ee51d660f7c1d21e274e4f70d69.png','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/fcbb9ee51d660f7c1d21e274e4f70d69.png','image','03E4A78C7F9520F06876C8446A24F9AC',41007,1509764990,18,1509764990,0,NULL,0),(11,0,'student_avatar','qiniu',4,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/966df36fefc8ddfe7c5224a1eeab6579.png','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/966df36fefc8ddfe7c5224a1eeab6579.png','image','%XRRZK2HM1(W1_]~69]%0S9',48399,1509765621,18,1509765621,0,NULL,0),(12,0,'student_avatar','qiniu',3,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/53a42f819c648c0417d9ad5d36e74a59.png','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/53a42f819c648c0417d9ad5d36e74a59.png','image','6B9D45FF59298FEA5171D9F6A7C31A5B',22958,1509787037,18,1509787037,0,NULL,0),(13,0,'student_avatar','qiniu',3,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/dbdf661babbca158858d6bb2cfc3c369.gif','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/dbdf661babbca158858d6bb2cfc3c369.gif','image','%XRRZK2HM1(W1_]~69]%0S9',306828,1509787146,18,1509787146,0,NULL,0),(14,0,'student_avatar','qiniu',3,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/fe6ae8c99109201107907461f155b61d.gif','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/fe6ae8c99109201107907461f155b61d.gif','image','03E4A78C7F9520F06876C8446A24F9AC',993935,1509787301,18,1509787301,0,NULL,0),(15,0,'student_avatar','qiniu',4,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/a44158bb77e2a5306ad342e552a04c78.gif','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/a44158bb77e2a5306ad342e552a04c78.gif','image','%XRRZK2HM1(W1_]~69]%0S9',306828,1509788767,18,1509788767,0,NULL,0),(16,0,'student_avatar','qiniu',7,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/04/9b6839e21f44c1cb0d49a755e7653c9f.gif','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/9b6839e21f44c1cb0d49a755e7653c9f.gif','image','D52EF0283770626CE03757A1ABFA6976',168607,1509789558,18,1509789558,0,NULL,0),(17,0,'student_avatar','qiniu',8,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/06/9860cb678bcd09faa6da40f20da980d8.gif','http://s10.xiao360.com/qms/student_avatar/18/17/11/06/9860cb678bcd09faa6da40f20da980d8.gif','image','6B9D45FF59298FEA5171D9F6A7C31A5B',18788,1509938134,18,1509938134,0,NULL,0),(18,0,'student_avatar','qiniu',102,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/06/6f0e86dc5f09456434ccb4f8176cf05e.gif','http://s10.xiao360.com/qms/student_avatar/18/17/11/06/6f0e86dc5f09456434ccb4f8176cf05e.gif','image','F4A353889172C12D763106BEC8385FC6',71613,1509952039,18,1509952039,0,NULL,0),(19,0,'student_avatar','qiniu',8,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/06/5b57c79b6bd8e169b8437b850e7759dc.gif','http://s10.xiao360.com/qms/student_avatar/18/17/11/06/5b57c79b6bd8e169b8437b850e7759dc.gif','image','%XRRZK2HM1(W1_]~69]%0S9',306828,1509957420,18,1509957420,0,NULL,0),(20,0,'lesson_cover_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/lesson_cover_picture/18/17/11/07/f44b172f585aa78209918256b2f50954.jpg','http://s10.xiao360.com/qms/lesson_cover_picture/18/17/11/07/f44b172f585aa78209918256b2f50954.jpg','image','a00687d3f5f6783060668bbf15ef252f.jpg',51729,1510057799,18,1510057799,0,NULL,0),(21,0,'lesson_cover_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/lesson_cover_picture/18/17/11/07/f5cb608678039ea9e778ffb2fa02396b.jpg','http://s10.xiao360.com/qms/lesson_cover_picture/18/17/11/07/f5cb608678039ea9e778ffb2fa02396b.jpg','image','a00687d3f5f6783060668bbf15ef252f.jpg',51729,1510057963,18,1510057963,0,NULL,0),(22,0,'lesson_cover_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/lesson_cover_picture/18/17/11/07/0ab2b20c4e331aff72ff23e4adcf71c3.jpg','http://s10.xiao360.com/qms/lesson_cover_picture/18/17/11/07/0ab2b20c4e331aff72ff23e4adcf71c3.jpg','image','a00687d3f5f6783060668bbf15ef252f.jpg',51729,1510058035,18,1510058035,0,NULL,0),(23,0,'student_avatar','qiniu',1,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/08/ffda8a4cf3b837b9b66ff59669b4a4ce.gif','http://s10.xiao360.com/qms/student_avatar/18/17/11/08/ffda8a4cf3b837b9b66ff59669b4a4ce.gif','image','95394C909022B743E28783ABD99D2A25',25419,1510112400,18,1510112400,0,NULL,0),(24,0,'student_avatar','qiniu',1,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/11/2121f1e3e0b61fcdbabf3cbce45f4379.png','http://s10.xiao360.com/qms/student_avatar/18/17/11/11/2121f1e3e0b61fcdbabf3cbce45f4379.png','image','报名照片',59508,1510393473,18,1510393473,0,NULL,0),(25,0,'lesson_cover_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/lesson_cover_picture/18/17/11/14/8b8c4fa6e519d864b07b854f848802d8.png','http://s10.xiao360.com/qms/lesson_cover_picture/18/17/11/14/8b8c4fa6e519d864b07b854f848802d8.png','image','$%)(Z@D`2S13R~54X35F1TC.png',2108,1510622499,18,1510622499,0,NULL,0),(26,0,'lesson_cover_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/lesson_cover_picture/18/17/11/14/23ea48b5c39af4e9873c308fdf939ab3.jpg','http://s10.xiao360.com/qms/lesson_cover_picture/18/17/11/14/23ea48b5c39af4e9873c308fdf939ab3.jpg','image','printbg.jpg',51729,1510622581,18,1510622581,0,NULL,0),(27,0,'lesson_cover_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/lesson_cover_picture/18/17/11/14/9c7cdddeecd3b07a4e7937d9153726bc.jpg','http://s10.xiao360.com/qms/lesson_cover_picture/18/17/11/14/9c7cdddeecd3b07a4e7937d9153726bc.jpg','image','KdyG-fymzqsa5738521.jpg',150814,1510626207,18,1510626207,0,NULL,0),(28,0,'student_avatar','qiniu',139,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/18/17/11/15/ebfbbf2724aaeb76ab444615a65faa38.jpeg','http://s10.xiao360.com/qms/student_avatar/18/17/11/15/ebfbbf2724aaeb76ab444615a65faa38.jpeg','image','}@(20Y3NR)B6ES@O(@2ROSW',1815,1510728080,18,1510728080,0,NULL,0),(29,0,'lesson_cover_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/lesson_cover_picture/18/17/11/17/563e14f7f1b5d309af75eaca8febdcb4.jpg','http://s10.xiao360.com/qms/lesson_cover_picture/18/17/11/17/563e14f7f1b5d309af75eaca8febdcb4.jpg','image','banner.jpg',222090,1510899506,18,1510899506,0,NULL,0);
/*!40000 ALTER TABLE `x360p_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_goods`
--

DROP TABLE IF EXISTS `x360p_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_goods` (
  `gid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `gtype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '商品类型(0:课程,1:物品)',
  `bids` varchar(255) NOT NULL DEFAULT '' COMMENT '校区IDS（多校区ID)',
  `is_global` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否全局',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `short_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '简要描述',
  `goods_image` varchar(255) NOT NULL DEFAULT '' COMMENT '商品图片URL',
  `content` text NOT NULL COMMENT '商品介绍',
  `sale_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '销售价',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '商品状态(1为上架,0为下架)',
  `on_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上架时间',
  `off_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下架时间',
  `order_limit_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '报名截止时间',
  `is_top` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否热门课程',
  `year` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '年份',
  `season` enum('H','Q','S','C') NOT NULL COMMENT '季节(H:寒假,C:春季,S:暑假,Q:秋季)',
  `limit_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '限制报名人数',
  `order_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '报名人数',
  `share_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享次数',
  `view_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COMMENT='商品表(课程建立之后，上架操作会产生一个商品)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_goods`
--

LOCK TABLES `x360p_goods` WRITE;
/*!40000 ALTER TABLE `x360p_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_goods` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_holiday`
--

LOCK TABLES `x360p_holiday` WRITE;
/*!40000 ALTER TABLE `x360p_holiday` DISABLE KEYS */;
INSERT INTO `x360p_holiday` VALUES (1,0,0,'寒衣节',20171118,2017,1509930552,18,1509930552,0,NULL,0),(5,0,0,'放假',20171123,2017,1510969461,18,1510969461,0,NULL,0),(6,0,0,'也放假',20171201,2017,1510969461,18,1510969461,0,NULL,0),(7,0,0,'周四节',20171207,2017,1510970016,18,1510970016,0,NULL,0),(8,0,0,'',20171221,2017,1510970135,18,1510970135,0,NULL,0);
/*!40000 ALTER TABLE `x360p_holiday` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_homework`
--

DROP TABLE IF EXISTS `x360p_homework`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_homework` (
  `hid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '作业ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned DEFAULT '0' COMMENT '校区ID',
  `lid` int(11) unsigned DEFAULT '0' COMMENT '课程ID',
  `cid` int(11) unsigned DEFAULT '0' COMMENT '班级ID',
  `ca_id` int(11) unsigned DEFAULT '0' COMMENT '班级排课ID',
  `eid` int(11) unsigned DEFAULT '0' COMMENT '老师ID',
  `content` text COMMENT '作业内容',
  `remark` varchar(255) DEFAULT NULL COMMENT '老师给作业的备注',
  `push_status` tinyint(1) unsigned DEFAULT '0' COMMENT '推送状态(0:待推送,1:已推送)',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`hid`,`delete_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_homework`
--

LOCK TABLES `x360p_homework` WRITE;
/*!40000 ALTER TABLE `x360p_homework` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_homework` ENABLE KEYS */;
UNLOCK TABLES;

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
  `hid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '作业记录ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `star` tinyint(1) unsigned DEFAULT '0' COMMENT '作业星级(1~5)星级表态度',
  `content` text COMMENT '作业提交的文字内容',
  `is_check` tinyint(1) unsigned DEFAULT '0' COMMENT '是否批改',
  `is_publish` tinyint(1) unsigned DEFAULT '0' COMMENT '是否发表',
  `check_time` int(11) unsigned DEFAULT '0' COMMENT '批改时间',
  `check_uid` int(11) unsigned DEFAULT '0' COMMENT '批改用户ID',
  `check_level` tinyint(1) unsigned DEFAULT '0' COMMENT '批改等级(1:普批，2：精批)',
  `check_content` text COMMENT '批改内容',
  `result_level` tinyint(1) unsigned DEFAULT '0' COMMENT '作业完成等级(1-4分别对应A-D)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`hc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业完成表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_homework_complete`
--

LOCK TABLES `x360p_homework_complete` WRITE;
/*!40000 ALTER TABLE `x360p_homework_complete` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_homework_complete` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_homework_complete_attachment`
--

DROP TABLE IF EXISTS `x360p_homework_complete_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_homework_complete_attachment` (
  `hca_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned DEFAULT '0' COMMENT '班级ID',
  `sid` int(11) unsigned DEFAULT '0' COMMENT '学员ID',
  `hc_id` int(11) unsigned DEFAULT '0' COMMENT '作业完成ID',
  `is_check` tinyint(1) unsigned DEFAULT '0' COMMENT '是否批改附件(0为学生提交的作业附件，1为老师批改的附件)',
  `url` varchar(255) DEFAULT NULL COMMENT '路径',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`hca_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业完成附件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_homework_complete_attachment`
--

LOCK TABLES `x360p_homework_complete_attachment` WRITE;
/*!40000 ALTER TABLE `x360p_homework_complete_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_homework_complete_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_homework_publish`
--

DROP TABLE IF EXISTS `x360p_homework_publish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_homework_publish` (
  `hp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '作业发表ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
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
  PRIMARY KEY (`hp_id`,`delete_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业发表记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_homework_publish`
--

LOCK TABLES `x360p_homework_publish` WRITE;
/*!40000 ALTER TABLE `x360p_homework_publish` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_homework_publish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_input_template`
--

DROP TABLE IF EXISTS `x360p_input_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_input_template` (
  `it_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT '模板名称',
  `template` tinytext COMMENT '模板数据',
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`it_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='表单数据模板';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_input_template`
--

LOCK TABLES `x360p_input_template` WRITE;
/*!40000 ALTER TABLE `x360p_input_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_input_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_invoice_apply`
--

DROP TABLE IF EXISTS `x360p_invoice_apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_invoice_apply` (
  `ia_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'invoice_apply发票申请',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `oid` int(11) unsigned NOT NULL COMMENT '订单ID（x360p_order）',
  `type` enum('paper','electronic') NOT NULL DEFAULT 'paper' COMMENT '发票类型',
  `content` varchar(255) DEFAULT NULL COMMENT '发票内容',
  `invoice_title_type` enum('personal','company') DEFAULT 'personal' COMMENT '发票抬头类型',
  `invoice_title` varchar(255) DEFAULT NULL COMMENT '发票抬头，发票抬头类型如果是个人填个人姓名；如果是公司，填公司名称',
  `identify_number` varchar(255) DEFAULT NULL COMMENT '纳税人识别号（发票抬头类型为公司需要填写）',
  `take_method` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '取票方式，1：自取，2：快递',
  `mobile` varchar(20) DEFAULT NULL COMMENT '快递寄送联系电话(取票方式为快递)',
  `address` varchar(255) DEFAULT NULL COMMENT '快递寄送地址（取票方式为快递）',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`ia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='发票申请表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_invoice_apply`
--

LOCK TABLES `x360p_invoice_apply` WRITE;
/*!40000 ALTER TABLE `x360p_invoice_apply` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_invoice_apply` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COMMENT='课程表(关键的课程主表,记录课程的基本信息)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_lesson`
--

LOCK TABLES `x360p_lesson` WRITE;
/*!40000 ALTER TABLE `x360p_lesson` DISABLE KEYS */;
INSERT INTO `x360p_lesson` VALUES (106,0,'37,36',2018,'C',102,'','语文','YW',105,1,18,1,9,'介绍一下','\" \"','',14,0,0,2,1,0,10.00,100.00,1.00,60,1000.00,'','',1,0,1,0,0,1508489473,18,1508548956,1,18,1508548956),(107,0,'37,36',2018,'C',103,'','语文','YW',105,1,18,1,9,'介绍','\" \"','',14,0,0,2,1,0,100.00,1.00,1.00,60,100.00,'','',1,0,1,0,0,1508489887,18,1508548968,1,18,1508548968),(108,0,'37,36',2019,'C',0,'','这是一个课时包','',105,1,18,1,6,'这是一个课时包哦~','\" \"','',14,1,0,1,1,1,10.00,1000.00,1.00,60,1000.00,'','',1,1,1,0,0,1508495066,18,1508550501,1,18,1508550501),(109,0,'38,37',2018,'S',103,'','按课次收费-按期收费','',105,1,18,1,9,'按课次收费-按期收费','\" \"','',14,0,0,2,1,1,10.00,5000.00,1.00,60,5000.00,'','',1,0,1,0,0,1508496990,18,1509001379,1,18,1509001379),(110,0,'37,36',2017,'C',103,'','按课时收费-按期收费','',105,1,18,1,9,'按课时收费-按期收费','\" \"','',14,0,0,2,2,1,5.00,120.00,1.00,60,600.00,'','',1,0,1,0,0,1508497800,18,1508931524,1,18,1508931524),(111,0,'37,36',2018,'C',0,'102,103','课时包','',105,1,18,1,9,'又是一个课时包','\" \"','',14,0,0,2,1,1,5.00,0.00,1.00,60,0.00,'','',1,1,1,0,0,1508497894,18,1508931529,1,18,1508931529),(112,0,'36,37',2018,'C',0,'103,102,101','测试课时包','',105,1,18,1,9,'测试课时包','\"\"','',10,0,0,2,1,0,10.00,10.00,1.00,60,100.00,'','',1,1,1,0,0,1508501021,18,1510294722,1,18,1510294722),(113,0,'38',2018,'H',102,'','测试校区','',105,1,18,1,9,'测试校区','\"\"','',10,0,0,2,1,0,10.00,100.00,1.00,60,1000.00,'','',1,0,1,0,0,1508501336,18,1510201651,1,18,1510201651),(114,0,'39,38',2018,'Q',103,'','按时间收费','',105,1,18,1,9,'按时间收费','\" \"','',14,0,0,2,3,0,1.00,1500.00,1.00,60,1500.00,'','',1,0,1,0,0,1508548402,18,1510294430,1,18,1510294430),(115,0,'38',2018,'C',103,'','test','',105,1,18,1,9,'介绍','\" \"','',14,0,0,2,1,0,1.00,0.00,1.00,60,0.00,'','',1,0,1,0,0,1508553843,18,1508918183,1,18,1508918183),(116,0,'',0,'H',0,'','测试2','',105,1,18,1,9,'介绍','\" \"','',14,0,0,2,1,0,1.00,0.00,1.00,60,0.00,'','',1,0,1,0,0,1508729076,18,1508918178,1,18,1508918178),(117,0,'',0,'C',0,'','dsf','',105,1,18,1,9,'dsfs','\" \"','',14,0,0,2,1,0,1.00,0.00,1.00,60,0.00,'','',1,0,1,0,0,1508729172,18,1508918173,1,18,1508918173),(123,0,'35',2019,'H',103,'','语文','',105,1,18,1,9,'介绍','\" \"','',14,0,0,2,1,0,1.00,100.00,1.00,60,100.00,'','',1,0,1,0,0,1508920501,18,1510294433,1,18,1510294433),(124,0,'35',2019,'C',103,'','语文-按期','',105,1,18,1,9,'介绍','\" \"','',14,0,0,2,1,1,10.00,1000.00,1.00,60,1000.00,'','',1,0,1,0,0,1508920557,18,1510294438,1,18,1510294438),(125,0,'37',2018,'C',105,'','数学-按课时-按期','',105,1,18,1,9,'介绍','\" \"','',14,0,0,2,2,1,10.00,1000.00,1.00,60,1000.00,'','',1,0,1,0,0,1508925405,18,1510294441,1,18,1510294441),(126,0,'38,35,36,37,39',2017,'S',101,'','我是二傻子','',105,1,18,1,9,'个电话发个','\" \"','',2,0,0,2,1,0,80.00,900.00,1.00,60,72000.00,'','',1,0,1,0,0,1508927785,18,1509786181,1,18,1509786181),(127,0,'37,39,38,36,35',2018,'H',102,'','六脉神剑高级','',105,1,18,1,9,'六脉神剑九式学习课程','\" \"','',14,2,0,1,2,0,1.00,199.00,2.00,60,199.00,'','',1,0,1,0,0,1509431270,18,1510294748,1,18,1510294748),(128,0,'35,36,37,38,39',0,'Q',103,'','散文与构篇','',2,1,18,1,12,'散文与构篇','\"\"','',14,0,0,2,1,0,1.00,0.00,1.00,60,0.00,'','',1,0,1,0,0,1509779048,18,1509786174,1,18,1509786174),(129,0,'38,37',2017,'Q',105,'','语文-按次-按次','',2,1,18,1,12,'语文-按次-按次','\"\"','',5,0,0,1,1,0,1.00,120.00,1.00,60,120.00,'','',1,0,1,0,0,1510294318,18,1510294318,0,0,NULL),(130,0,'35,36',2019,'C',105,'','语文-按次-按期','',2,1,18,1,12,'语文-按期-按次','\"\"','',5,0,0,1,1,1,5.00,120.00,1.00,60,600.00,'','',1,0,1,0,0,1510294418,18,1510294418,0,0,NULL),(131,0,'35,36',2017,'S',105,'','数学-按课时-按期','',2,1,18,1,12,'数学-按课时-按期','\"\"','',10,0,0,1,1,1,10.00,150.00,1.00,60,1500.00,'','',1,0,1,0,0,1510294531,18,1510294584,1,18,1510294584),(132,0,'35,36',2017,'S',105,'','数学-按课时-按课时','',2,1,18,1,12,'数学-按课时-按期','\"\"','',10,0,0,1,2,0,10.00,150.00,1.00,60,150.00,'','',1,0,1,0,0,1510294595,18,1510294595,0,0,NULL),(133,0,'35,36',2017,'S',105,'','数学-按课时-按期','',2,1,18,1,12,'数学-按课时-按期','\"\"','',10,0,0,1,2,1,10.00,150.00,1.00,60,1500.00,'','',1,0,1,0,0,1510294632,18,1510294632,0,0,NULL),(134,0,'35,36',2017,'S',105,'','数学-按月-按月','',2,1,18,1,12,'数学-按月-按月','\"\"','',10,0,0,1,3,0,10.00,200.00,1.00,60,200.00,'','',1,0,1,0,0,1510294691,18,1510294691,0,0,NULL),(135,0,'35,36',2017,'S',105,'','数学-按月-按期','',2,1,18,1,12,'数学-按月-按期','\"\"','',10,0,0,1,3,1,10.00,200.00,1.00,60,2000.00,'','',1,0,1,0,0,1510294713,18,1510294713,0,0,NULL),(136,0,'35,36',2017,'S',105,'','按次-按次','',2,1,18,1,12,'按次-按次','\"\"','',10,0,0,1,1,0,10.00,199.00,1.00,60,199.00,'','',1,0,1,0,0,1510294892,18,1510294892,0,0,NULL),(137,0,'35',2018,'Q',105,'','降龙十八掌','',105,1,18,1,12,'武功','\"\"','',5,0,0,1,2,1,10.00,100.00,2.00,60,1000.00,'','',1,0,1,0,0,1510297163,18,1510297163,0,0,NULL),(138,0,'35',2017,'Q',0,'101,102','一对一课程','OTO',105,1,18,1,12,'一对一课程啦','\"\"','',7,1,0,1,2,1,7.00,200.00,1.00,60,1400.00,'','',1,1,1,0,0,1510367569,18,1510367569,0,0,NULL),(139,0,'35',2017,'Q',0,'103,105','一对多课程','OTM',105,1,18,1,12,'一对多课程','\"\"','',10,2,0,1,1,1,10.00,150.00,1.00,60,1500.00,'','',1,1,1,0,0,1510367783,18,1510367783,0,0,NULL),(140,0,'35,40',2017,'S',102,'','百汇英语','',2,1,18,1,12,'百汇英语','\"\"','',1,0,0,1,1,0,1.00,299.00,1.00,60,299.00,'','',1,0,1,0,0,1510380633,18,1510380633,0,0,NULL),(141,0,'35',2017,'H',102,'','yaorui-lesson-按次计费-按期收费-14次课','',2,1,18,1,12,'姚瑞的测试课程','\"\"','http://s10.xiao360.com/qms/lesson_cover_picture/18/17/11/14/9c7cdddeecd3b07a4e7937d9153726bc.jpg',14,0,0,1,1,1,14.00,200.00,1.00,60,2800.00,'','',1,0,1,0,0,1510626251,18,1510626251,0,0,NULL),(142,0,'35',2017,'H',102,'','yaorui-lesson-one-one','',2,1,18,1,12,'姚瑞的一对一测试课程','\"\"','',7,1,0,1,1,1,7.00,500.00,2.00,120,3500.00,'','',1,0,1,0,0,1510646099,18,1510646099,0,0,NULL);
/*!40000 ALTER TABLE `x360p_lesson` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_lesson_attachment`
--

LOCK TABLES `x360p_lesson_attachment` WRITE;
/*!40000 ALTER TABLE `x360p_lesson_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_lesson_attachment` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_lesson_chapter`
--

LOCK TABLES `x360p_lesson_chapter` WRITE;
/*!40000 ALTER TABLE `x360p_lesson_chapter` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_lesson_chapter` ENABLE KEYS */;
UNLOCK TABLES;

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
  `is_attendance` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否登记考勤',
  `attendance_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '出勤状态，与student_attendance相对应。0：缺勤，1：出勤',
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
-- Dumping data for table `x360p_makeup_arrange`
--

LOCK TABLES `x360p_makeup_arrange` WRITE;
/*!40000 ALTER TABLE `x360p_makeup_arrange` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_makeup_arrange` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_material`
--

DROP TABLE IF EXISTS `x360p_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_material` (
  `mt_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL COMMENT '物品名称',
  `unit` char(4) NOT NULL COMMENT '计量单位',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `image` varchar(255) NOT NULL COMMENT '图片',
  `num` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '数量',
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
-- Dumping data for table `x360p_material`
--

LOCK TABLES `x360p_material` WRITE;
/*!40000 ALTER TABLE `x360p_material` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_material_store`
--

DROP TABLE IF EXISTS `x360p_material_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_material_store` (
  `ms_id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `bid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL COMMENT '仓库名',
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
-- Dumping data for table `x360p_material_store`
--

LOCK TABLES `x360p_material_store` WRITE;
/*!40000 ALTER TABLE `x360p_material_store` DISABLE KEYS */;
INSERT INTO `x360p_material_store` VALUES (1,0,40,'百汇分校',1510362612,18,1510362612,0,0,NULL);
/*!40000 ALTER TABLE `x360p_material_store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_material_store_qty`
--

DROP TABLE IF EXISTS `x360p_material_store_qty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_material_store_qty` (
  `msq_id` int(11) NOT NULL AUTO_INCREMENT,
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
-- Dumping data for table `x360p_material_store_qty`
--

LOCK TABLES `x360p_material_store_qty` WRITE;
/*!40000 ALTER TABLE `x360p_material_store_qty` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_material_store_qty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_message`
--

DROP TABLE IF EXISTS `x360p_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_message` (
  `mid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
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
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户消息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_message`
--

LOCK TABLES `x360p_message` WRITE;
/*!40000 ALTER TABLE `x360p_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_migrations`
--

DROP TABLE IF EXISTS `x360p_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_migrations` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ThinkPHP自动生成的数据库迁移记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_migrations`
--

LOCK TABLES `x360p_migrations` WRITE;
/*!40000 ALTER TABLE `x360p_migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_migrations` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_news`
--

LOCK TABLES `x360p_news` WRITE;
/*!40000 ALTER TABLE `x360p_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_news_content`
--

DROP TABLE IF EXISTS `x360p_news_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_news_content` (
  `nc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新闻ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `ob_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `bid` int(11) NOT NULL COMMENT '新闻ID',
  `content` text NOT NULL,
  PRIMARY KEY (`nc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告通知内容表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_news_content`
--

LOCK TABLES `x360p_news_content` WRITE;
/*!40000 ALTER TABLE `x360p_news_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_news_content` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_news_view`
--

LOCK TABLES `x360p_news_view` WRITE;
/*!40000 ALTER TABLE `x360p_news_view` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_news_view` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8mb4 COMMENT='订单记录表(学员选课之后会产生订单记录)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order`
--

LOCK TABLES `x360p_order` WRITE;
/*!40000 ALTER TABLE `x360p_order` DISABLE KEYS */;
INSERT INTO `x360p_order` VALUES (134,0,0,130,35,0,'2017111434528',1700.00,0.00,0.00,1700.00,1,1510625729,2,0.00,1700.00,1700.00,1700.00,0.00,1,0,0,0,'','',1510625911,18,1510625911,0,NULL,0),(135,0,0,130,35,0,'2017111430786',3390.00,0.00,0.00,3390.00,1,1510625806,2,0.00,3390.00,3390.00,3390.00,0.00,1,0,0,0,'','',1510625982,18,1510625982,0,NULL,0),(136,0,0,127,35,0,'2017111437149',2800.00,0.00,0.00,2800.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510627468,18,1510627468,0,NULL,0),(137,0,0,128,35,0,'2017111466317',2800.00,0.00,0.00,2800.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510627468,18,1510627468,0,NULL,0),(138,0,0,129,35,0,'2017111478100',2800.00,0.00,0.00,2800.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510627468,18,1510627468,0,NULL,0),(139,0,0,130,35,0,'2017111417778',2000.00,0.00,100.00,1900.00,11,1510627375,1,0.00,1900.00,1500.00,1500.00,0.00,1,0,2,0,'','',1510627553,18,1510644189,0,NULL,0),(140,0,0,130,35,0,'2017111482457',120.00,0.00,0.00,120.00,1,1510627757,2,0.00,120.00,121.00,121.00,0.00,1,0,0,0,'','',1510627920,18,1510627921,0,NULL,0),(141,0,0,131,35,0,'2017111436123',2800.00,0.00,0.00,2800.00,0,1510642657,0,0.00,2800.00,0.00,0.00,2800.00,0,0,0,0,'','',1510642720,18,1510642720,0,NULL,0),(142,0,0,133,35,0,'2017111483487',150.00,0.00,0.00,150.00,1,1510643333,2,0.00,150.00,200.00,200.00,0.00,1,0,0,0,'','',1510643494,18,1510643494,0,NULL,0),(143,0,0,134,35,0,'2017111412802',3500.00,0.00,0.00,3500.00,1,1510646102,2,0.00,3500.00,3500.00,3500.00,0.00,1,0,0,0,'','',1510646158,18,1510646158,0,NULL,0),(144,0,0,135,35,0,'2017111477152',1700.00,0.00,0.00,1700.00,1,1510656150,2,0.00,1700.00,1700.00,1700.00,150.00,1,0,0,0,'','',1510656315,18,1510656315,0,NULL,0),(145,0,0,138,35,0,'2017111418309',600.00,0.00,0.00,600.00,1,1510662446,2,0.00,600.00,600.00,600.00,0.00,1,0,0,0,'','',1510662607,18,1510662607,0,NULL,0),(146,0,0,138,35,0,'2017111442281',150.00,0.00,0.00,150.00,0,1510662446,0,0.00,150.00,0.00,0.00,150.00,0,0,0,0,'','',1510662652,18,1510662652,0,NULL,0),(147,0,0,135,35,0,'2017111402348',1650.00,0.00,0.00,1650.00,1,1510663929,1,1080.00,570.00,100.00,1180.00,42.73,1,0,0,0,'','',1510664097,18,1510664097,0,NULL,0),(148,0,0,135,35,0,'2017111552536',150.00,0.00,0.00,150.00,1,1510707599,2,0.00,150.00,200.00,200.00,0.00,1,0,0,0,'','',1510707762,18,1510707762,0,NULL,0),(149,0,0,140,35,0,'2017111513156',600.00,0.00,0.00,600.00,1,1510718093,2,0.00,600.00,600.00,600.00,0.00,1,0,0,0,'','',1510718204,18,1510741360,0,NULL,0),(150,0,0,136,35,0,'2017111547304',2800.00,0.00,0.00,2800.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510718229,18,1510718229,0,NULL,0),(151,0,0,138,35,0,'2017111592093',2800.00,0.00,0.00,2800.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510718229,18,1510718229,0,NULL,0),(152,0,0,140,35,0,'2017111581403',600.00,0.00,0.00,600.00,1,1510718157,2,0.00,600.00,600.00,600.00,0.00,1,0,0,0,'','',1510718241,18,1510741360,0,NULL,0),(154,0,0,140,35,0,'2017111598641',1000.00,0.00,0.00,1000.00,1,1510741408,2,0.00,1000.00,1000.00,1000.00,0.00,1,0,0,0,'','',1510741506,18,1510741506,0,NULL,0),(155,0,0,141,35,0,'2017111653646',150.00,0.00,0.00,150.00,1,1510816760,1,0.00,150.00,145.00,145.00,5.00,1,0,0,0,'','',1510817291,18,1510831081,0,NULL,0),(156,0,0,142,35,0,'2017111691075',600.00,0.00,0.00,600.00,1,1510817691,2,0.00,600.00,600.00,600.00,0.00,1,0,0,0,'','',1510817785,18,1510829982,0,NULL,0),(157,0,0,127,35,0,'2017111687982',2800.00,0.00,0.00,2800.00,1,1510822340,2,0.00,2800.00,2800.00,2800.00,0.00,1,0,0,0,'','',1510822380,18,1510822380,0,NULL,0),(161,0,0,136,35,0,'2017111634297',2800.00,0.00,0.00,2800.00,1,1510822829,2,0.00,2800.00,2800.00,2800.00,0.00,1,0,0,0,'','',1510822851,18,1510822851,0,NULL,0),(162,0,0,143,35,0,'2017111644601',2800.00,0.00,0.00,2800.00,1,1510824699,2,0.00,2800.00,2800.00,2800.00,0.00,1,0,0,0,'','',1510824722,18,1510824722,0,NULL,0),(165,0,0,144,35,0,'2017111689622',1900.00,0.00,0.00,1900.00,1,1510833395,2,0.00,1900.00,1900.00,1900.00,0.00,1,0,0,0,'','',1510833478,18,1510833479,0,NULL,0),(166,0,0,141,35,0,'2017111683981',1500.00,0.00,0.00,1500.00,1,1510833505,2,0.00,1500.00,1500.00,1500.00,0.00,1,0,0,0,'','',1510833660,18,1510833660,0,NULL,0),(167,0,0,144,35,0,'2017111602995',150.00,0.00,0.00,150.00,1,1510833505,2,0.00,150.00,150.00,150.00,0.00,1,0,0,0,'','',1510834115,18,1510834115,0,NULL,0),(168,0,0,145,35,0,'2017111759896',1650.00,0.00,0.00,1650.00,1,1510881331,2,0.00,1650.00,1650.00,1650.00,0.00,1,0,0,0,'','',1510881473,18,1510881473,0,NULL,0),(169,0,0,145,35,0,'2017111768737',600.00,0.00,0.00,600.00,0,1510902729,0,0.00,600.00,0.00,0.00,600.00,0,0,0,0,'','',1510902961,18,1510902961,0,NULL,0),(170,0,0,145,35,0,'2017111774806',600.00,0.00,0.00,600.00,0,1510902729,0,0.00,600.00,0.00,0.00,600.00,0,0,0,0,'','',1510902974,18,1510902974,0,NULL,0),(171,0,0,145,35,0,'2017111736812',600.00,0.00,0.00,600.00,1,1510902729,2,0.00,600.00,600.00,600.00,0.00,1,0,0,0,'','',1510903001,18,1510903001,0,NULL,0),(172,0,0,145,35,0,'2017111721929',120.00,0.00,0.00,120.00,0,1510902729,0,0.00,120.00,0.00,0.00,120.00,0,0,0,0,'','',1510903035,18,1510903035,0,NULL,0),(173,0,0,145,35,0,'2017111737752',1600.00,0.00,0.00,1600.00,1,1510902959,2,100.00,1600.00,1500.00,1600.00,0.00,1,0,0,0,'','',1510903134,18,1510903450,0,NULL,0),(174,0,0,145,35,0,'2017111780348',2000.00,0.00,0.00,2000.00,1,1510903248,1,0.00,2000.00,1500.00,1500.00,500.00,1,0,0,0,'','',1510903475,18,1510903475,0,NULL,0),(175,0,0,145,35,0,'2017111731881',2300.00,0.00,0.00,2300.00,1,1510904201,1,0.00,2300.00,1000.00,1000.00,1300.00,1,0,0,0,'','',1510904400,18,1510905629,0,NULL,0),(176,0,0,145,35,0,'2017111798522',4400.00,0.00,0.00,4400.00,1,1510904270,1,1550.00,2850.00,2000.00,3550.00,850.00,1,0,0,0,'','',1510904724,18,1510904724,0,NULL,0),(177,0,0,145,35,0,'2017111744786',1620.00,0.00,0.00,1620.00,1,1510904675,1,0.00,1620.00,694.07,694.07,925.93,1,0,0,0,'','',1510904843,18,1510905629,0,NULL,0),(178,0,0,144,35,0,'2017111758889',5000.00,0.00,0.00,5000.00,11,1510910012,1,0.00,5000.00,2500.00,2500.00,1750.00,1,0,2,0,'','',1510910219,18,1510911180,0,NULL,0),(179,0,0,146,35,0,'2017111730343',1200.00,0.00,0.00,1200.00,1,1510920233,2,0.00,1200.00,1200.00,1200.00,0.00,1,0,0,0,'','',1510920461,18,1510920461,0,NULL,0),(180,0,0,147,35,0,'2017111716834',1200.00,0.00,0.00,1200.00,1,1510920367,2,0.00,1200.00,1200.00,1200.00,0.00,1,0,0,0,'','',1510920534,18,1510920534,0,NULL,0),(181,0,0,143,35,0,'2017111783315',1320.00,0.00,0.00,1320.00,1,1510920480,2,0.00,1320.00,1320.00,1320.00,0.00,1,0,0,0,'','',1510920667,18,1510920668,0,NULL,0),(182,0,0,144,35,0,'2017111789507',600.00,0.00,0.00,600.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510922750,18,1510922750,0,NULL,0),(183,0,0,147,35,0,'2017111776616',120.00,0.00,0.00,120.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510923964,18,1510923964,0,NULL,0),(184,0,0,146,35,0,'2017111746535',120.00,0.00,0.00,120.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510923964,18,1510923964,0,NULL,0),(185,0,0,144,35,0,'2017111790727',120.00,0.00,0.00,120.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510923964,18,1510923964,0,NULL,0),(186,0,0,139,35,0,'2017111767151',120.00,0.00,0.00,120.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510923964,18,1510923964,0,NULL,0),(187,0,0,147,35,0,'2017111899835',12000.00,0.00,0.00,12000.00,1,1510968445,2,0.00,12000.00,12000.00,12000.00,0.00,1,0,0,0,'','',1510968606,18,1510968606,0,NULL,0),(188,0,0,146,35,0,'2017111841937',12000.00,0.00,0.00,12000.00,1,1510968445,2,0.00,12000.00,12000.00,12000.00,0.00,1,0,0,0,'','',1510968621,18,1510968621,0,NULL,0),(189,0,0,145,35,0,'2017111842810',12000.00,0.00,0.00,12000.00,1,1510968445,2,0.00,12000.00,12000.00,12000.00,0.00,1,0,0,0,'','',1510968639,18,1510968639,0,NULL,0),(190,0,0,144,35,0,'2017111885594',12000.00,0.00,0.00,12000.00,1,1510968445,2,0.00,12000.00,12000.00,12000.00,0.00,1,0,0,0,'','',1510968654,18,1510968654,0,NULL,0),(191,0,0,143,35,0,'2017111833358',12000.00,0.00,0.00,12000.00,1,1510968445,2,0.00,12000.00,12000.00,12000.00,0.00,1,0,0,0,'','',1510968672,18,1510968672,0,NULL,0),(192,0,0,139,35,0,'2017111802458',12000.00,0.00,0.00,12000.00,1,1510968445,2,0.00,12000.00,12000.00,12000.00,0.00,1,0,0,0,'','',1510968688,18,1510968688,0,NULL,0);
/*!40000 ALTER TABLE `x360p_order` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_cut_amount`
--

LOCK TABLES `x360p_order_cut_amount` WRITE;
/*!40000 ALTER TABLE `x360p_order_cut_amount` DISABLE KEYS */;
INSERT INTO `x360p_order_cut_amount` VALUES (1,0,35,1,5,0,1003,6.00,1510655847,18,1510655847,0,NULL,0),(2,0,35,1,0,12,1003,10.00,1510661944,18,1510661944,0,NULL,0),(3,0,35,1,0,13,1003,10.00,1510661989,18,1510661989,0,NULL,0),(4,0,35,1,0,15,1003,10.00,1510664306,18,1510664306,0,NULL,0),(5,0,35,1,0,16,1003,16.00,1510664872,18,1510664872,0,NULL,0),(6,0,35,1,6,0,1003,14.00,1510664943,18,1510664943,0,NULL,0),(7,0,35,1,7,0,1003,12.00,1510665010,18,1510665010,0,NULL,0),(8,0,35,1,8,0,1003,10.00,1510707478,18,1510707478,0,NULL,0),(9,0,35,1,9,0,1003,10.00,1510707684,18,1510707684,0,NULL,0),(10,0,35,1,10,0,1003,10.00,1510707786,18,1510707786,0,NULL,0);
/*!40000 ALTER TABLE `x360p_order_cut_amount` ENABLE KEYS */;
UNLOCK TABLES;

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
  `gtype` tinyint(1) unsigned DEFAULT '0' COMMENT '商品类型 0：课程，1：物品',
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
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`oi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=246 DEFAULT CHARSET=utf8mb4 COMMENT='订单项目表(每一个订单对应1到多个订单项目记录)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_item`
--

LOCK TABLES `x360p_order_item` WRITE;
/*!40000 ALTER TABLE `x360p_order_item` DISABLE KEYS */;
INSERT INTO `x360p_order_item` VALUES (172,0,134,35,130,0,0,158,1.00,3,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,0.00,0.00,0.00,0.00,1510625911,18,1510625911,0,NULL,0),(173,0,134,35,130,0,0,159,10.00,2,150.00,150.00,0,1500.00,1500.00,1500.00,0.00,0.00,10.00,0.00,10.00,0.00,1510625911,18,1510625911,0,NULL,0),(174,0,135,35,130,0,0,160,10.00,1,199.00,199.00,0,1990.00,1990.00,1990.00,0.00,0.00,10.00,0.00,10.00,0.00,1510625982,18,1510625982,0,NULL,0),(175,0,135,35,130,0,0,161,7.00,2,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1510625982,18,1510625982,0,NULL,0),(176,0,136,35,127,0,0,162,14.00,1,200.00,200.00,0,2800.00,2800.00,0.00,0.00,0.00,14.00,0.00,14.00,0.00,1510627468,18,1510627468,0,NULL,0),(177,0,137,35,128,0,0,163,14.00,1,200.00,200.00,0,2800.00,2800.00,0.00,0.00,0.00,14.00,0.00,14.00,0.00,1510627468,18,1510627468,0,NULL,0),(178,0,138,35,129,0,0,164,14.00,1,200.00,200.00,0,2800.00,2800.00,0.00,0.00,0.00,14.00,0.00,14.00,0.00,1510627468,18,1510627468,0,NULL,0),(179,0,139,35,130,0,0,165,10.00,3,200.00,200.00,0,2000.00,1900.00,1500.00,0.00,100.00,0.00,0.00,0.00,0.00,1510627553,18,1510627553,0,NULL,0),(180,0,140,35,130,0,0,166,1.00,1,120.00,120.00,0,120.00,120.00,120.00,0.00,0.00,1.00,0.00,1.00,0.00,1510627920,18,1510627920,0,NULL,0),(181,0,141,35,131,0,0,167,14.00,1,200.00,200.00,0,2800.00,2800.00,0.00,0.00,0.00,14.00,0.00,14.00,0.00,1510642720,18,1510642720,0,NULL,0),(182,0,142,35,133,0,0,168,1.00,2,150.00,150.00,0,150.00,150.00,150.00,0.00,0.00,1.00,0.00,1.00,0.00,1510643494,18,1510643494,0,NULL,0),(183,0,143,35,134,0,0,169,7.00,1,500.00,500.00,0,3500.00,3500.00,3500.00,0.00,0.00,7.00,0.00,14.00,0.00,1510646158,18,1510646158,0,NULL,0),(184,0,144,35,135,0,0,170,10.00,2,150.00,150.00,0,1500.00,1500.00,1500.00,0.00,0.00,10.00,0.00,10.00,0.00,1510656315,18,1510656315,0,NULL,0),(185,0,144,35,135,0,0,171,1.00,3,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,0.00,0.00,0.00,0.00,1510656315,18,1510656315,0,NULL,0),(186,0,145,35,138,0,0,172,5.00,1,120.00,120.00,0,600.00,600.00,600.00,0.00,0.00,5.00,0.00,5.00,0.00,1510662607,18,1510662607,0,NULL,0),(187,0,146,35,138,0,0,173,1.00,2,150.00,150.00,0,150.00,150.00,0.00,0.00,0.00,1.00,0.00,1.00,0.00,1510662652,18,1510662652,0,NULL,0),(188,0,147,35,135,0,0,174,1.00,2,150.00,150.00,0,150.00,150.00,107.27,0.00,0.00,1.00,0.00,1.00,0.00,1510664097,18,1510664097,0,NULL,0),(189,0,147,35,135,0,0,170,10.00,2,150.00,150.00,0,1500.00,1500.00,1072.73,0.00,0.00,10.00,0.00,10.00,0.00,1510664097,18,1510664097,0,NULL,0),(190,0,148,35,135,0,0,174,1.00,2,150.00,150.00,0,150.00,150.00,150.00,0.00,0.00,1.00,0.00,1.00,0.00,1510707762,18,1510707762,0,NULL,0),(191,0,149,35,140,0,0,175,5.00,1,120.00,120.00,0,600.00,600.00,600.00,0.00,0.00,5.00,0.00,5.00,0.00,1510718204,18,1510718204,0,NULL,0),(192,0,150,35,136,0,0,176,14.00,1,200.00,200.00,0,2800.00,2800.00,0.00,0.00,0.00,14.00,0.00,14.00,0.00,1510718229,18,1510718229,0,NULL,0),(193,0,151,35,138,0,0,177,14.00,1,200.00,200.00,0,2800.00,2800.00,0.00,0.00,0.00,14.00,0.00,14.00,0.00,1510718229,18,1510718229,0,NULL,0),(194,0,152,35,140,0,0,178,3.00,3,200.00,200.00,0,600.00,600.00,600.00,0.00,0.00,0.00,0.00,0.00,0.00,1510718241,18,1510718241,0,NULL,0),(196,0,154,35,140,0,0,179,10.00,2,100.00,100.00,0,1000.00,1000.00,1000.00,0.00,0.00,5.00,0.00,10.00,0.00,1510741506,18,1510741506,0,NULL,0),(197,0,155,35,141,0,0,180,1.00,2,150.00,150.00,0,150.00,150.00,145.00,0.00,0.00,1.00,0.00,1.00,0.00,1510817291,18,1510817291,0,NULL,0),(198,0,156,35,142,0,0,181,5.00,1,120.00,120.00,0,600.00,600.00,600.00,0.00,0.00,5.00,0.00,5.00,0.00,1510817785,18,1510817785,0,NULL,0),(199,0,157,35,127,0,0,162,14.00,1,200.00,200.00,0,2800.00,2800.00,2800.00,0.00,0.00,14.00,0.00,14.00,0.00,1510822380,18,1510822380,0,NULL,0),(203,0,161,35,136,0,0,176,14.00,1,200.00,200.00,0,2800.00,2800.00,2800.00,0.00,0.00,14.00,0.00,14.00,0.00,1510822851,18,1510822851,0,NULL,0),(204,0,162,35,143,0,0,182,14.00,1,200.00,200.00,0,2800.00,2800.00,2800.00,0.00,0.00,14.00,0.00,14.00,0.00,1510824722,18,1510824722,0,NULL,0),(207,0,165,35,144,0,0,183,10.00,2,150.00,150.00,0,1500.00,1500.00,1500.00,0.00,0.00,10.00,1.00,10.00,1.00,1510833478,18,1510833478,0,NULL,0),(208,0,165,35,144,0,0,184,2.00,3,200.00,200.00,0,400.00,400.00,400.00,0.00,0.00,2.00,0.00,0.00,0.00,1510833478,18,1510833478,0,NULL,0),(209,0,166,35,141,0,0,185,10.00,2,150.00,150.00,0,1500.00,1500.00,1500.00,0.00,0.00,10.00,0.00,10.00,0.00,1510833660,18,1510833660,0,NULL,0),(210,0,167,35,144,0,0,186,1.00,2,150.00,150.00,0,150.00,150.00,150.00,0.00,0.00,1.00,0.00,1.00,0.00,1510834115,18,1510834115,0,NULL,0),(211,0,168,35,145,0,0,187,10.00,2,150.00,150.00,0,1500.00,1500.00,1500.00,0.00,0.00,10.00,0.00,10.00,0.00,1510881473,18,1510881473,0,NULL,0),(212,0,168,35,145,0,0,188,1.00,2,150.00,150.00,0,150.00,150.00,150.00,0.00,0.00,1.00,0.00,1.00,0.00,1510881473,18,1510881473,0,NULL,0),(213,0,169,35,145,0,0,189,5.00,1,120.00,120.00,0,600.00,600.00,0.00,0.00,0.00,5.00,0.00,5.00,0.00,1510902961,18,1510902961,0,NULL,0),(214,0,170,35,145,0,0,189,5.00,1,120.00,120.00,0,600.00,600.00,0.00,0.00,0.00,5.00,0.00,5.00,0.00,1510902974,18,1510902974,0,NULL,0),(215,0,171,35,145,0,0,189,5.00,1,120.00,120.00,0,600.00,600.00,600.00,0.00,0.00,5.00,0.00,5.00,0.00,1510903001,18,1510903001,0,NULL,0),(216,0,172,35,145,0,0,190,1.00,1,120.00,120.00,0,120.00,120.00,0.00,0.00,0.00,1.00,0.00,1.00,0.00,1510903035,18,1510903035,0,NULL,0),(217,0,173,35,145,0,0,191,1.00,3,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,0.00,0.00,1510903134,18,1510903134,0,NULL,0),(218,0,173,35,145,0,0,192,7.00,2,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1510903134,18,1510903134,0,NULL,0),(219,0,174,35,145,0,0,193,10.00,3,200.00,200.00,0,2000.00,2000.00,1500.00,0.00,0.00,10.00,0.00,0.00,0.00,1510903475,18,1510903475,0,NULL,0),(220,0,175,35,145,0,0,189,5.00,1,120.00,120.00,0,600.00,600.00,260.87,0.00,0.00,5.00,0.00,5.00,0.00,1510904400,18,1510904400,0,NULL,0),(221,0,175,35,145,0,0,187,10.00,2,150.00,150.00,0,1500.00,1500.00,652.17,0.00,0.00,10.00,0.00,10.00,0.00,1510904400,18,1510904400,0,NULL,0),(222,0,175,35,145,0,0,191,1.00,3,200.00,200.00,0,200.00,200.00,86.96,0.00,0.00,1.00,0.00,0.00,0.00,1510904400,18,1510904400,0,NULL,0),(223,0,176,35,145,0,0,193,10.00,3,200.00,200.00,0,2000.00,2000.00,1613.63,0.00,0.00,10.00,0.00,0.00,0.00,1510904724,18,1510904724,0,NULL,0),(224,0,176,35,145,0,0,194,10.00,2,100.00,100.00,0,1000.00,1000.00,806.82,0.00,0.00,5.00,0.00,10.00,0.00,1510904724,18,1510904724,0,NULL,0),(225,0,176,35,145,0,0,192,7.00,2,200.00,200.00,0,1400.00,1400.00,1129.55,0.00,0.00,7.00,0.00,7.00,0.00,1510904724,18,1510904724,0,NULL,0),(226,0,177,35,145,0,0,190,1.00,1,120.00,120.00,0,120.00,120.00,120.00,0.00,0.00,1.00,0.00,1.00,0.00,1510904843,18,1510904843,0,NULL,0),(227,0,177,35,145,0,0,195,10.00,1,150.00,150.00,0,1500.00,1500.00,574.07,0.00,0.00,10.00,0.00,10.00,0.00,1510904843,18,1510904843,0,NULL,0),(228,0,178,35,144,0,0,186,10.00,2,150.00,150.00,0,1500.00,1500.00,750.00,0.00,0.00,10.00,0.00,10.00,0.00,1510910219,18,1510910219,0,NULL,0),(229,0,178,35,144,0,0,183,10.00,2,150.00,150.00,0,1500.00,1500.00,750.00,0.00,0.00,10.00,0.00,10.00,0.00,1510910219,18,1510910219,0,NULL,0),(230,0,178,35,144,0,0,184,10.00,3,200.00,200.00,0,2000.00,2000.00,1000.00,0.00,0.00,10.00,0.00,0.00,0.00,1510910219,18,1510910219,0,NULL,0),(231,0,179,35,146,0,0,196,10.00,1,120.00,120.00,0,1200.00,1200.00,1200.00,0.00,0.00,10.00,0.00,10.00,0.00,1510920461,18,1510920461,0,NULL,0),(232,0,180,35,147,0,0,197,10.00,1,120.00,120.00,0,1200.00,1200.00,1200.00,0.00,0.00,10.00,0.00,10.00,0.00,1510920534,18,1510920534,0,NULL,0),(233,0,181,35,143,0,0,198,10.00,1,120.00,120.00,0,1200.00,1200.00,1200.00,0.00,0.00,10.00,0.00,10.00,0.00,1510920667,18,1510920667,0,NULL,0),(234,0,181,35,143,0,0,199,1.00,1,120.00,120.00,0,120.00,120.00,120.00,0.00,0.00,1.00,0.00,1.00,0.00,1510920667,18,1510920667,0,NULL,0),(235,0,182,35,144,0,0,200,8.00,1,120.00,120.00,0,960.00,960.00,0.00,0.00,0.00,8.00,0.00,8.00,0.00,1510922750,18,1510922750,0,NULL,0),(236,0,183,35,147,0,0,201,10.00,1,120.00,120.00,0,1200.00,1200.00,0.00,0.00,0.00,10.00,0.00,10.00,0.00,1510923964,18,1510923964,0,NULL,0),(237,0,184,35,146,0,0,202,10.00,1,120.00,120.00,0,1200.00,1200.00,0.00,0.00,0.00,10.00,0.00,10.00,0.00,1510923964,18,1510923964,0,NULL,0),(238,0,185,35,144,0,0,203,10.00,1,120.00,120.00,0,1200.00,1200.00,0.00,0.00,0.00,10.00,0.00,10.00,0.00,1510923964,18,1510923964,0,NULL,0),(239,0,186,35,139,0,0,204,10.00,1,120.00,120.00,0,1200.00,1200.00,0.00,0.00,0.00,10.00,0.00,10.00,0.00,1510923964,18,1510923964,0,NULL,0),(240,0,187,35,147,0,0,201,100.00,1,120.00,120.00,0,12000.00,12000.00,12000.00,0.00,0.00,100.00,0.00,100.00,0.00,1510968606,18,1510968606,0,NULL,0),(241,0,188,35,146,0,0,202,100.00,1,120.00,120.00,0,12000.00,12000.00,12000.00,0.00,0.00,100.00,0.00,100.00,0.00,1510968621,18,1510968621,0,NULL,0),(242,0,189,35,145,0,0,190,100.00,1,120.00,120.00,0,12000.00,12000.00,12000.00,0.00,0.00,100.00,0.00,100.00,0.00,1510968639,18,1510968639,0,NULL,0),(243,0,190,35,144,0,0,203,100.00,1,120.00,120.00,0,12000.00,12000.00,12000.00,0.00,0.00,100.00,0.00,100.00,0.00,1510968654,18,1510968654,0,NULL,0),(244,0,191,35,143,0,0,199,100.00,1,120.00,120.00,0,12000.00,12000.00,12000.00,0.00,0.00,100.00,0.00,100.00,0.00,1510968672,18,1510968672,0,NULL,0),(245,0,192,35,139,0,0,204,100.00,1,120.00,120.00,0,12000.00,12000.00,12000.00,0.00,0.00,100.00,0.00,100.00,0.00,1510968688,18,1510968688,0,NULL,0);
/*!40000 ALTER TABLE `x360p_order_item` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COMMENT='订单付款记录ID';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_payment_history`
--

LOCK TABLES `x360p_order_payment_history` WRITE;
/*!40000 ALTER TABLE `x360p_order_payment_history` DISABLE KEYS */;
INSERT INTO `x360p_order_payment_history` VALUES (70,0,35,28,0,1,100.00,1510156800,1510209624,18,1510209624,0,NULL,0),(71,0,35,29,0,1,1000.00,1510156800,1510209676,18,1510209676,0,NULL,0),(72,0,35,30,0,1,199.00,1510156800,1510213613,18,1510213613,0,NULL,0),(73,0,35,31,0,1,300.00,1510156800,1510213652,18,1510213652,0,NULL,0),(74,0,35,32,0,1,500.00,1510243200,1510284970,18,1510284970,0,NULL,0),(75,0,35,33,0,1,1700.00,1510243200,1510308440,18,1510308440,0,NULL,0),(76,0,35,34,0,1,1500.00,1510243200,1510308477,18,1510308477,0,NULL,0),(77,0,35,35,0,1,1500.00,1510243200,1510308545,18,1510308545,0,NULL,0),(78,0,35,36,0,1,1300.00,1510243200,1510309872,18,1510309872,0,NULL,0),(79,0,35,37,0,1,1000.00,1510243200,1510310613,18,1510310613,0,NULL,0),(80,0,35,38,0,1,500.00,1510243200,1510313870,18,1510313870,0,NULL,0),(81,0,35,39,0,1,1400.00,1510329600,1510367641,18,1510367641,0,NULL,0),(82,0,35,40,0,1,1500.00,1510329600,1510367832,18,1510367832,0,NULL,0),(83,0,35,41,0,1,2900.00,1510329600,1510367899,18,1510367899,0,NULL,0),(84,0,35,42,0,2,1900.00,1510329600,1510385984,18,1510385984,0,NULL,0),(85,0,35,43,0,2,100.00,1510329600,1510386037,18,1510386037,0,NULL,0),(86,0,35,45,0,2,1000.00,1510329600,1510386947,18,1510386947,0,NULL,0),(87,0,35,46,0,2,3000.00,1510329600,1510387142,18,1510387142,0,NULL,0),(88,0,35,47,0,2,2000.00,1510329600,1510391938,18,1510391938,0,NULL,0),(89,0,35,48,0,7,1700.00,1510588800,1510625911,18,1510625911,0,NULL,0),(90,0,35,49,0,7,3390.00,1510588800,1510625982,18,1510625982,0,NULL,0),(91,0,35,50,0,7,1500.00,1510588800,1510627553,18,1510627553,0,NULL,0),(92,0,35,51,0,7,121.00,1510588800,1510627920,18,1510627920,0,NULL,0),(93,0,35,52,0,7,200.00,1510588800,1510643494,18,1510643494,0,NULL,0),(94,0,35,53,0,7,3500.00,1510588800,1510646158,18,1510646158,0,NULL,0),(95,0,35,54,0,7,1700.00,1510588800,1510656315,18,1510656315,0,NULL,0),(96,0,35,55,0,7,600.00,1510588800,1510662607,18,1510662607,0,NULL,0),(97,0,35,56,0,7,100.00,1510588800,1510664097,18,1510664097,0,NULL,0),(98,0,35,57,0,7,200.00,1510675200,1510707762,18,1510707762,0,NULL,0),(99,0,35,58,0,7,300.00,1510675200,1510718204,18,1510718204,0,NULL,0),(100,0,35,59,0,7,300.00,1510675200,1510718241,18,1510718241,0,NULL,0),(101,0,35,61,0,7,250.00,1510675200,1510741506,18,1510741506,0,NULL,0),(102,0,35,61,0,2,250.00,1510675200,1510741506,18,1510741506,0,NULL,0),(103,0,35,61,0,3,250.00,1510675200,1510741506,18,1510741506,0,NULL,0),(104,0,35,61,0,4,250.00,1510675200,1510741506,18,1510741506,0,NULL,0),(105,0,35,62,0,7,50.00,1510761600,1510817291,18,1510817291,0,NULL,0),(106,0,35,63,0,7,50.00,1510761600,1510817395,18,1510817395,0,NULL,0),(107,0,35,64,0,7,25.00,1510761600,1510817446,18,1510817446,0,NULL,0),(108,0,35,65,0,7,300.00,1510761600,1510817785,18,1510817785,0,NULL,0),(109,0,35,66,0,7,150.00,1510761600,1510817886,18,1510817886,0,NULL,0),(110,0,35,67,0,7,75.00,1510761600,1510818075,18,1510818075,0,NULL,0),(111,0,35,68,0,7,15.00,1510761600,1510818832,18,1510818832,0,NULL,0),(112,0,35,69,0,7,30.00,1510761600,1510818855,18,1510818855,0,NULL,0),(113,0,35,70,0,7,10.00,1510761600,1510818909,18,1510818909,0,NULL,0),(114,0,35,71,0,7,5.00,1510761600,1510819011,18,1510819011,0,NULL,0),(115,0,35,72,0,7,2800.00,1510761600,1510822380,18,1510822380,0,NULL,0),(116,0,35,73,0,7,2800.00,1510761600,1510822851,18,1510822851,0,NULL,0),(117,0,35,74,0,7,2800.00,1510761600,1510824722,18,1510824722,0,NULL,0),(118,0,35,75,0,7,5.00,1510761600,1510829053,18,1510829053,0,NULL,0),(119,0,35,76,0,7,5.00,1510761600,1510829209,18,1510829209,0,NULL,0),(120,0,35,77,0,7,2.00,1510761600,1510829737,18,1510829737,0,NULL,0),(121,0,35,78,0,7,1.00,1510761600,1510829819,18,1510829819,0,NULL,0),(122,0,35,79,0,7,1.00,1510761600,1510829855,18,1510829855,0,NULL,0),(123,0,35,80,0,7,1.00,1510761600,1510829982,18,1510829982,0,NULL,0),(124,0,35,81,0,7,5.00,1510761600,1510830031,18,1510830031,0,NULL,0),(125,0,35,82,0,7,5.00,1510761600,1510830638,18,1510830638,0,NULL,0),(126,0,35,83,0,7,5.00,1510761600,1510830975,18,1510830975,0,NULL,0),(127,0,35,84,0,7,5.00,1510761600,1510831081,18,1510831081,0,NULL,0),(128,0,35,85,0,7,1900.00,1510761600,1510833479,18,1510833479,0,NULL,0),(129,0,35,86,0,7,1500.00,1510761600,1510833660,18,1510833660,0,NULL,0),(130,0,35,87,0,7,150.00,1510761600,1510834115,18,1510834115,0,NULL,0),(131,0,35,88,0,7,1650.00,1510848000,1510881473,18,1510881473,0,NULL,0),(132,0,35,89,0,7,600.00,1510848000,1510903001,18,1510903001,0,NULL,0),(133,0,35,90,0,7,1500.00,1510848000,1510903134,18,1510903134,0,NULL,0),(134,0,35,92,0,7,1500.00,1510848000,1510903475,18,1510903475,0,NULL,0),(135,0,35,93,0,7,2000.00,1510848000,1510904724,18,1510904724,0,NULL,0),(136,0,35,94,0,7,620.00,1510848000,1510904843,18,1510904843,0,NULL,0),(137,0,35,95,0,7,74.07,1510848000,1510905629,18,1510905629,0,NULL,0),(138,0,35,96,0,7,2500.00,1510848000,1510910219,18,1510910219,0,NULL,0),(139,0,35,97,0,7,1200.00,1510848000,1510920461,18,1510920461,0,NULL,0),(140,0,35,98,0,7,1200.00,1510848000,1510920534,18,1510920534,0,NULL,0),(141,0,35,99,0,7,1320.00,1510848000,1510920668,18,1510920668,0,NULL,0),(142,0,35,100,0,7,12000.00,1510934400,1510968606,18,1510968606,0,NULL,0),(143,0,35,101,0,7,12000.00,1510934400,1510968621,18,1510968621,0,NULL,0),(144,0,35,102,0,7,12000.00,1510934400,1510968639,18,1510968639,0,NULL,0),(145,0,35,103,0,7,12000.00,1510934400,1510968654,18,1510968654,0,NULL,0),(146,0,35,104,0,7,12000.00,1510934400,1510968672,18,1510968672,0,NULL,0),(147,0,35,105,0,7,12000.00,1510934400,1510968688,18,1510968688,0,NULL,0);
/*!40000 ALTER TABLE `x360p_order_payment_history` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_order_performance`
--

LOCK TABLES `x360p_order_performance` WRITE;
/*!40000 ALTER TABLE `x360p_order_performance` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_order_performance` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COMMENT='订单收据表主表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_receipt_bill`
--

LOCK TABLES `x360p_order_receipt_bill` WRITE;
/*!40000 ALTER TABLE `x360p_order_receipt_bill` DISABLE KEYS */;
INSERT INTO `x360p_order_receipt_bill` VALUES (26,0,35,107,'zbr20171108165056',398.00,0.00,398.00,0.00,1510131056,18,1510131056,0,0,NULL),(27,0,35,107,'bin20171109122804',2000.00,0.00,2000.00,500.00,1510201684,18,1510201684,0,0,NULL),(28,0,35,116,'cyr20171109144024',100.00,0.00,100.00,0.00,1510209624,18,1510209624,0,0,NULL),(29,0,35,116,'epw20171109144116',1000.00,0.00,1000.00,799.00,1510209676,18,1510209676,0,0,NULL),(30,0,35,117,'ybm20171109154653',199.00,0.00,199.00,0.00,1510213613,18,1510213613,0,0,NULL),(31,0,35,117,'tas20171109154732',300.00,0.00,300.00,198.00,1510213652,18,1510213652,0,0,NULL),(32,0,35,117,'yda20171110113610',500.00,0.00,500.00,694.00,1510284970,18,1510284970,0,0,NULL),(33,0,35,118,'ovz20171110180720',1700.00,0.00,1700.00,0.00,1510308440,18,1510308440,0,0,NULL),(34,0,35,118,'akd20171110180756',1500.00,0.00,1500.00,500.00,1510308477,18,1510308477,0,0,NULL),(35,0,35,118,'gvu20171110180905',1500.00,0.00,1500.00,115.00,1510308545,18,1510308545,0,0,NULL),(36,0,35,119,'djw20171110183112',1300.00,0.00,1300.00,268.00,1510309872,18,1510309872,0,0,NULL),(37,0,35,120,'fvz20171110184333',1000.00,0.00,1000.00,468.00,1510310613,18,1510310613,0,0,NULL),(38,0,35,121,'zcn20171110193749',500.00,0.00,500.00,48.50,1510313870,18,1510313870,0,0,NULL),(39,0,35,122,'sbz20171111103401',1400.00,0.00,1400.00,0.00,1510367641,18,1510367641,0,0,NULL),(40,0,35,123,'irc20171111103712',1500.00,0.00,1500.00,0.00,1510367832,18,1510367832,0,0,NULL),(41,0,35,124,'dhr20171111103818',2900.00,0.00,2900.00,0.00,1510367898,18,1510367898,0,0,NULL),(42,0,35,126,'qkf20171111153944',0.00,0.00,0.00,0.00,1510385984,18,1510385984,0,0,NULL),(43,0,35,126,'oah20171111154037',100.00,0.00,100.00,50.00,1510386037,18,1510386037,0,0,NULL),(44,0,35,126,'xjf20171111154609',50.00,0.00,50.00,50.00,1510386369,18,1510386369,0,0,NULL),(45,0,35,126,'ual20171111155547',1000.00,0.00,1000.00,500.00,1510386947,18,1510386947,0,0,NULL),(46,0,35,126,'vjx20171111155901',3000.00,0.00,3000.00,1500.00,1510387141,18,1510387141,0,0,NULL),(47,0,35,126,'xvr20171111171858',2480.00,480.00,2000.00,2520.00,1510391938,18,1510391938,0,0,NULL),(48,0,35,130,'tsf20171114101831',1700.00,0.00,1700.00,0.00,1510625911,18,1510625911,0,0,NULL),(49,0,35,130,'mri20171114101942',3390.00,0.00,3390.00,0.00,1510625982,18,1510625982,0,0,NULL),(50,0,35,130,'wrj20171114104553',1500.00,0.00,1500.00,400.00,1510627553,18,1510627553,0,0,NULL),(51,0,35,130,'tnx20171114105200',121.00,0.00,121.00,0.00,1510627920,18,1510627920,0,0,NULL),(52,0,35,133,'ult20171114151134',200.00,0.00,200.00,0.00,1510643494,18,1510643494,0,0,NULL),(53,0,35,134,'gvq20171114155558',3500.00,0.00,3500.00,0.00,1510646158,18,1510646158,0,0,NULL),(54,0,35,135,'rpi20171114184515',1700.00,0.00,1700.00,0.00,1510656315,18,1510656315,0,0,NULL),(55,0,35,138,'xin20171114203007',600.00,0.00,600.00,0.00,1510662607,18,1510662607,0,0,NULL),(56,0,35,135,'ern20171114205457',1180.00,1080.00,100.00,470.00,1510664097,18,1510664097,0,0,NULL),(57,0,35,135,'mkf20171115090242',200.00,0.00,200.00,0.00,1510707762,18,1510707762,0,0,NULL),(58,0,35,140,'qxk20171115115644',300.00,0.00,300.00,300.00,1510718204,18,1510718204,0,0,NULL),(59,0,35,140,'puc20171115115721',300.00,0.00,300.00,300.00,1510718241,18,1510718241,0,0,NULL),(60,0,35,140,'CWZ20171115182240',600.00,0.00,600.00,600.00,1510741360,18,1510741360,0,0,NULL),(61,0,35,140,'PXU20171115182506',1000.00,0.00,1000.00,0.00,1510741506,18,1510741506,0,0,NULL),(62,0,35,141,'EGN20171116152811',50.00,0.00,50.00,100.00,1510817291,18,1510817291,0,0,NULL),(63,0,35,141,'VHR20171116152955',50.00,0.00,50.00,150.00,1510817395,18,1510817395,0,0,NULL),(64,0,35,141,'RCV20171116153046',25.00,0.00,25.00,75.00,1510817446,18,1510817446,0,0,NULL),(65,0,35,142,'MLS20171116153625',300.00,0.00,300.00,300.00,1510817785,18,1510817785,0,0,NULL),(66,0,35,142,'IBU20171116153806',150.00,0.00,150.00,450.00,1510817886,18,1510817886,0,0,NULL),(67,0,35,142,'QAS20171116154115',75.00,0.00,75.00,225.00,1510818075,18,1510818075,0,0,NULL),(68,0,35,142,'HEQ20171116155352',15.00,0.00,15.00,135.00,1510818832,18,1510818832,0,0,NULL),(69,0,35,142,'BWG20171116155415',30.00,0.00,30.00,90.00,1510818855,18,1510818855,0,0,NULL),(70,0,35,142,'XOC20171116155509',10.00,0.00,10.00,50.00,1510818909,18,1510818909,0,0,NULL),(71,0,35,142,'NQA20171116155651',5.00,0.00,5.00,35.00,1510819011,18,1510819011,0,0,NULL),(72,0,35,127,'OGL20171116165300',2800.00,0.00,2800.00,0.00,1510822380,18,1510822380,0,0,NULL),(73,0,35,136,'FNZ20171116170051',2800.00,0.00,2800.00,0.00,1510822851,18,1510822851,0,0,NULL),(74,0,35,143,'YSB20171116173202',2800.00,0.00,2800.00,0.00,1510824722,18,1510824722,0,0,NULL),(75,0,35,142,'SIP20171116184413',5.00,0.00,5.00,25.00,1510829053,18,1510829053,0,0,NULL),(76,0,35,142,'LKM20171116184649',5.00,0.00,5.00,15.00,1510829209,18,1510829209,0,0,NULL),(77,0,35,142,'TWO20171116185537',2.00,0.00,2.00,8.00,1510829737,18,1510829737,0,0,NULL),(78,0,35,142,'KGD20171116185659',1.00,0.00,1.00,5.00,1510829819,18,1510829819,0,0,NULL),(79,0,35,142,'HJE20171116185735',1.00,0.00,1.00,3.00,1510829855,18,1510829855,0,0,NULL),(80,0,35,142,'BIF20171116185942',1.00,0.00,1.00,1.00,1510829982,18,1510829982,0,0,NULL),(81,0,35,141,'EOD20171116190031',5.00,0.00,5.00,45.00,1510830031,18,1510830031,0,0,NULL),(82,0,35,141,'OHR20171116191037',5.00,0.00,5.00,35.00,1510830637,18,1510830637,0,0,NULL),(83,0,35,141,'WKS20171116191615',5.00,0.00,5.00,25.00,1510830975,18,1510830975,0,0,NULL),(84,0,35,141,'QML20171116191801',5.00,0.00,5.00,15.00,1510831081,18,1510831081,0,0,NULL),(85,0,35,144,'VQZ20171116195758',1900.00,0.00,1900.00,0.00,1510833479,18,1510833479,0,0,NULL),(86,0,35,141,'AKM20171116200100',1500.00,0.00,1500.00,0.00,1510833660,18,1510833660,0,0,NULL),(87,0,35,144,'QEJ20171116200835',150.00,0.00,150.00,0.00,1510834115,18,1510834115,0,0,NULL),(88,0,35,145,'LON20171117091753',1650.00,0.00,1650.00,0.00,1510881473,18,1510881473,0,0,NULL),(89,0,35,145,'VEO20171117151641',600.00,0.00,600.00,0.00,1510903001,18,1510903001,0,0,NULL),(90,0,35,145,'MJG20171117151854',1500.00,0.00,1500.00,100.00,1510903134,18,1510903134,0,0,NULL),(91,0,35,145,'RHK20171117152410',100.00,100.00,0.00,100.00,1510903450,18,1510903450,0,0,NULL),(92,0,35,145,'TBL20171117152435',1500.00,0.00,1500.00,500.00,1510903475,18,1510903475,0,0,NULL),(93,0,35,145,'DKN20171117154524',3550.00,1550.00,2000.00,850.00,1510904724,18,1510904724,0,0,NULL),(94,0,35,145,'JEH20171117154723',620.00,0.00,620.00,1000.00,1510904843,18,1510904843,0,0,NULL),(95,0,35,145,'PHA20171117160029',74.07,0.00,74.07,5925.93,1510905629,18,1510905629,0,0,NULL),(96,0,35,144,'NEZ20171117171659',2500.00,0.00,2500.00,2500.00,1510910219,18,1510910219,0,0,NULL),(97,0,35,146,'WAO20171117200741',1200.00,0.00,1200.00,0.00,1510920461,18,1510920461,0,0,NULL),(98,0,35,147,'JBL20171117200854',1200.00,0.00,1200.00,0.00,1510920534,18,1510920534,0,0,NULL),(99,0,35,143,'DYU20171117201107',1320.00,0.00,1320.00,0.00,1510920668,18,1510920668,0,0,NULL),(100,0,35,147,'SBM20171118093006',12000.00,0.00,12000.00,0.00,1510968606,18,1510968606,0,0,NULL),(101,0,35,146,'ZRG20171118093021',12000.00,0.00,12000.00,0.00,1510968621,18,1510968621,0,0,NULL),(102,0,35,145,'MDN20171118093039',12000.00,0.00,12000.00,0.00,1510968639,18,1510968639,0,0,NULL),(103,0,35,144,'HMF20171118093054',12000.00,0.00,12000.00,0.00,1510968654,18,1510968654,0,0,NULL),(104,0,35,143,'RXE20171118093112',12000.00,0.00,12000.00,0.00,1510968672,18,1510968672,0,0,NULL),(105,0,35,139,'HGJ20171118093128',12000.00,0.00,12000.00,0.00,1510968688,18,1510968688,0,0,NULL);
/*!40000 ALTER TABLE `x360p_order_receipt_bill` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COMMENT='订单收据条目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_receipt_bill_item`
--

LOCK TABLES `x360p_order_receipt_bill_item` WRITE;
/*!40000 ALTER TABLE `x360p_order_receipt_bill_item` DISABLE KEYS */;
INSERT INTO `x360p_order_receipt_bill_item` VALUES (32,0,35,111,0,93,116,100.00,1510028465,18,1510028465,0,0,NULL),(33,0,35,111,0,93,117,1500.00,1510028465,18,1510028465,0,0,NULL),(34,0,35,112,0,94,118,100.00,1510028839,18,1510028839,0,0,NULL),(35,0,35,112,0,94,119,100.00,1510028839,18,1510028839,0,0,NULL),(42,0,35,114,22,100,126,0.00,1510048234,18,1510048234,0,0,NULL),(43,0,35,114,22,100,127,0.00,1510048234,18,1510048234,0,0,NULL),(44,0,35,107,23,99,124,0.00,1510048388,18,1510048388,0,0,NULL),(45,0,35,107,23,99,125,0.00,1510048388,18,1510048388,0,0,NULL),(46,0,35,107,25,96,121,100.00,1510058637,18,1510058637,0,0,NULL),(47,0,35,107,26,104,129,398.00,1510131056,18,1510131056,0,0,NULL),(48,0,35,107,27,108,133,1200.00,1510201684,18,1510201684,0,0,NULL),(49,0,35,107,27,108,134,800.00,1510201684,18,1510201684,0,0,NULL),(50,0,35,116,28,109,135,100.00,1510209624,18,1510209624,0,0,NULL),(51,0,35,116,29,110,136,58.85,1510209676,18,1510209676,0,0,NULL),(52,0,35,116,29,110,137,830.91,1510209676,18,1510209676,0,0,NULL),(53,0,35,116,29,110,138,110.24,1510209676,18,1510209676,0,0,NULL),(54,0,35,117,30,111,139,199.00,1510213613,18,1510213613,0,0,NULL),(55,0,35,117,31,112,140,60.24,1510213652,18,1510213652,0,0,NULL),(56,0,35,117,31,112,141,239.76,1510213652,18,1510213652,0,0,NULL),(57,0,35,117,32,113,142,500.00,1510284970,18,1510284970,0,0,NULL),(58,0,35,118,33,118,147,1500.00,1510308440,18,1510308440,0,0,NULL),(59,0,35,118,33,118,148,200.00,1510308440,18,1510308440,0,0,NULL),(60,0,35,118,34,119,149,1500.00,1510308477,18,1510308477,0,0,NULL),(61,0,35,118,35,120,150,1327.64,1510308545,18,1510308545,0,0,NULL),(62,0,35,118,35,120,151,172.36,1510308545,18,1510308545,0,0,NULL),(63,0,35,119,36,121,152,118.18,1510309872,18,1510309872,0,0,NULL),(64,0,35,119,36,121,153,1181.82,1510309872,18,1510309872,0,0,NULL),(65,0,35,120,37,122,154,909.09,1510310613,18,1510310613,0,0,NULL),(66,0,35,120,37,122,155,90.91,1510310613,18,1510310613,0,0,NULL),(67,0,35,121,38,123,156,114.50,1510313870,18,1510313870,0,0,NULL),(68,0,35,121,38,123,157,231.30,1510313870,18,1510313870,0,0,NULL),(69,0,35,121,38,123,158,154.20,1510313870,18,1510313870,0,0,NULL),(70,0,35,122,39,124,159,1400.00,1510367641,18,1510367641,0,0,NULL),(71,0,35,123,40,125,160,1500.00,1510367832,18,1510367832,0,0,NULL),(72,0,35,124,41,126,161,1400.00,1510367899,18,1510367899,0,0,NULL),(73,0,35,124,41,126,162,1500.00,1510367899,18,1510367899,0,0,NULL),(74,0,35,126,42,128,164,0.00,1510385984,18,1510385984,0,0,NULL),(75,0,35,126,43,129,165,100.00,1510386037,18,1510386037,0,0,NULL),(76,0,35,126,44,129,165,50.00,1510386369,18,1510386369,0,0,NULL),(77,0,35,126,45,130,166,1000.00,1510386947,18,1510386947,0,0,NULL),(78,0,35,126,46,131,167,1000.00,1510387141,18,1510387141,0,0,NULL),(79,0,35,126,46,131,168,2000.00,1510387141,18,1510387141,0,0,NULL),(80,0,35,126,47,132,169,744.00,1510391938,18,1510391938,0,0,NULL),(81,0,35,126,47,132,170,744.00,1510391938,18,1510391938,0,0,NULL),(82,0,35,126,47,132,171,992.00,1510391938,18,1510391938,0,0,NULL),(83,0,35,130,48,134,172,200.00,1510625911,18,1510625911,0,0,NULL),(84,0,35,130,48,134,173,1500.00,1510625911,18,1510625911,0,0,NULL),(85,0,35,130,49,135,174,1990.00,1510625982,18,1510625982,0,0,NULL),(86,0,35,130,49,135,175,1400.00,1510625982,18,1510625982,0,0,NULL),(87,0,35,130,50,139,179,1500.00,1510627553,18,1510627553,0,0,NULL),(88,0,35,130,51,140,180,120.00,1510627920,18,1510627920,0,0,NULL),(89,0,35,133,52,142,182,150.00,1510643494,18,1510643494,0,0,NULL),(90,0,35,134,53,143,183,3500.00,1510646158,18,1510646158,0,0,NULL),(91,0,35,135,54,144,184,1500.00,1510656315,18,1510656315,0,0,NULL),(92,0,35,135,54,144,185,200.00,1510656315,18,1510656315,0,0,NULL),(93,0,35,138,55,145,186,600.00,1510662607,18,1510662607,0,0,NULL),(94,0,35,135,56,147,188,107.27,1510664097,18,1510664097,0,0,NULL),(95,0,35,135,56,147,189,1072.73,1510664097,18,1510664097,0,0,NULL),(96,0,35,135,57,148,190,150.00,1510707762,18,1510707762,0,0,NULL),(97,0,35,140,58,149,191,300.00,1510718204,18,1510718204,0,0,NULL),(98,0,35,140,59,152,194,300.00,1510718241,18,1510718241,0,0,NULL),(99,0,35,140,60,152,194,300.00,1510741360,18,1510741360,0,0,NULL),(100,0,35,140,60,149,191,300.00,1510741360,18,1510741360,0,0,NULL),(101,0,35,140,61,154,196,1000.00,1510741506,18,1510741506,0,0,NULL),(102,0,35,141,62,155,197,50.00,1510817291,18,1510817291,0,0,NULL),(103,0,35,141,63,155,197,50.00,1510817395,18,1510817395,0,0,NULL),(104,0,35,141,64,155,197,25.00,1510817446,18,1510817446,0,0,NULL),(105,0,35,142,65,156,198,300.00,1510817785,18,1510817785,0,0,NULL),(106,0,35,142,66,156,198,150.00,1510817886,18,1510817886,0,0,NULL),(107,0,35,142,67,156,198,75.00,1510818075,18,1510818075,0,0,NULL),(108,0,35,142,68,156,198,15.00,1510818832,18,1510818832,0,0,NULL),(109,0,35,142,69,156,198,30.00,1510818855,18,1510818855,0,0,NULL),(110,0,35,142,70,156,198,10.00,1510818909,18,1510818909,0,0,NULL),(111,0,35,142,71,156,198,5.00,1510819011,18,1510819011,0,0,NULL),(112,0,35,127,72,157,199,2800.00,1510822380,18,1510822380,0,0,NULL),(113,0,35,136,73,161,203,2800.00,1510822851,18,1510822851,0,0,NULL),(114,0,35,143,74,162,204,2800.00,1510824722,18,1510824722,0,0,NULL),(115,0,35,142,75,156,198,5.00,1510829053,18,1510829053,0,0,NULL),(116,0,35,142,76,156,198,5.00,1510829209,18,1510829209,0,0,NULL),(117,0,35,142,77,156,198,2.00,1510829737,18,1510829737,0,0,NULL),(118,0,35,142,78,156,198,1.00,1510829819,18,1510829819,0,0,NULL),(119,0,35,142,79,156,198,1.00,1510829855,18,1510829855,0,0,NULL),(120,0,35,142,80,156,198,1.00,1510829982,18,1510829982,0,0,NULL),(121,0,35,141,81,155,197,5.00,1510830031,18,1510830031,0,0,NULL),(122,0,35,141,82,155,197,5.00,1510830637,18,1510830637,0,0,NULL),(123,0,35,141,83,155,197,5.00,1510830975,18,1510830975,0,0,NULL),(124,0,35,141,84,155,197,5.00,1510831081,18,1510831081,0,0,NULL),(125,0,35,144,85,165,207,1500.00,1510833479,18,1510833479,0,0,NULL),(126,0,35,144,85,165,208,400.00,1510833479,18,1510833479,0,0,NULL),(127,0,35,141,86,166,209,1500.00,1510833660,18,1510833660,0,0,NULL),(128,0,35,144,87,167,210,150.00,1510834115,18,1510834115,0,0,NULL),(129,0,35,145,88,168,211,1500.00,1510881473,18,1510881473,0,0,NULL),(130,0,35,145,88,168,212,150.00,1510881473,18,1510881473,0,0,NULL),(131,0,35,145,89,171,215,600.00,1510903001,18,1510903001,0,0,NULL),(132,0,35,145,90,173,217,187.50,1510903134,18,1510903134,0,0,NULL),(133,0,35,145,90,173,218,1312.50,1510903134,18,1510903134,0,0,NULL),(134,0,35,145,91,173,217,12.50,1510903450,18,1510903450,0,0,NULL),(135,0,35,145,91,173,218,87.50,1510903450,18,1510903450,0,0,NULL),(136,0,35,145,92,174,219,1500.00,1510903475,18,1510903475,0,0,NULL),(137,0,35,145,93,176,223,1613.63,1510904724,18,1510904724,0,0,NULL),(138,0,35,145,93,176,224,806.82,1510904724,18,1510904724,0,0,NULL),(139,0,35,145,93,176,225,1129.55,1510904724,18,1510904724,0,0,NULL),(140,0,35,145,94,177,226,45.93,1510904843,18,1510904843,0,0,NULL),(141,0,35,145,94,177,227,574.07,1510904843,18,1510904843,0,0,NULL),(142,0,35,145,95,177,226,74.07,1510905629,18,1510905629,0,0,NULL),(143,0,35,145,95,177,227,0.00,1510905629,18,1510905629,0,0,NULL),(144,0,35,145,95,176,223,0.00,1510905629,18,1510905629,0,0,NULL),(145,0,35,145,95,176,224,0.00,1510905629,18,1510905629,0,0,NULL),(146,0,35,145,95,176,225,0.00,1510905629,18,1510905629,0,0,NULL),(147,0,35,145,95,175,220,0.00,1510905629,18,1510905629,0,0,NULL),(148,0,35,145,95,175,221,0.00,1510905629,18,1510905629,0,0,NULL),(149,0,35,145,95,175,222,0.00,1510905629,18,1510905629,0,0,NULL),(150,0,35,145,95,174,219,0.00,1510905629,18,1510905629,0,0,NULL),(151,0,35,144,96,178,228,750.00,1510910219,18,1510910219,0,0,NULL),(152,0,35,144,96,178,229,750.00,1510910219,18,1510910219,0,0,NULL),(153,0,35,144,96,178,230,1000.00,1510910219,18,1510910219,0,0,NULL),(154,0,35,146,97,179,231,1200.00,1510920461,18,1510920461,0,0,NULL),(155,0,35,147,98,180,232,1200.00,1510920534,18,1510920534,0,0,NULL),(156,0,35,143,99,181,233,1200.00,1510920668,18,1510920668,0,0,NULL),(157,0,35,143,99,181,234,120.00,1510920668,18,1510920668,0,0,NULL),(158,0,35,147,100,187,240,12000.00,1510968606,18,1510968606,0,0,NULL),(159,0,35,146,101,188,241,12000.00,1510968621,18,1510968621,0,0,NULL),(160,0,35,145,102,189,242,12000.00,1510968639,18,1510968639,0,0,NULL),(161,0,35,144,103,190,243,12000.00,1510968654,18,1510968654,0,0,NULL),(162,0,35,143,104,191,244,12000.00,1510968672,18,1510968672,0,0,NULL),(163,0,35,139,105,192,245,12000.00,1510968688,18,1510968688,0,0,NULL);
/*!40000 ALTER TABLE `x360p_order_receipt_bill_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_order_receipt_bill_print_history`
--

DROP TABLE IF EXISTS `x360p_order_receipt_bill_print_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_order_receipt_bill_print_history` (
  `orbph_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '打印记录ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `ob_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
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
-- Dumping data for table `x360p_order_receipt_bill_print_history`
--

LOCK TABLES `x360p_order_receipt_bill_print_history` WRITE;
/*!40000 ALTER TABLE `x360p_order_receipt_bill_print_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_order_receipt_bill_print_history` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COMMENT='订单结转记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_refund`
--

LOCK TABLES `x360p_order_refund` WRITE;
/*!40000 ALTER TABLE `x360p_order_refund` DISABLE KEYS */;
INSERT INTO `x360p_order_refund` VALUES (1,0,35,0,130,'',150.00,150.00,0.00,150.00,'',1510636208,18,1510636208,0,NULL,0),(2,0,35,0,130,'',120.00,0.00,0.00,120.00,'',1510643395,18,1510643395,0,NULL,0),(3,0,35,0,133,'',150.00,0.00,0.00,150.00,'',1510643513,18,1510643513,0,NULL,0),(4,0,35,0,130,'',570.00,0.00,0.00,170.00,'',1510644189,18,1510644189,0,NULL,0),(5,0,35,0,130,'',190.00,151.00,6.00,511.00,'',1510655847,18,1510655847,0,NULL,0),(6,0,35,0,135,'',0.00,296.00,14.00,296.00,'',1510664943,18,1510664943,0,NULL,0),(7,0,35,0,135,'',150.00,0.00,12.00,150.00,'',1510665010,18,1510665010,0,NULL,0),(8,0,35,0,135,'',150.00,0.00,10.00,150.00,'',1510707478,18,1510707478,0,NULL,0),(9,0,35,0,135,'',150.00,0.00,10.00,150.00,'',1510707684,18,1510707684,0,NULL,0),(10,0,35,0,135,'',150.00,50.00,10.00,200.00,'',1510707786,18,1510707786,0,NULL,0),(11,0,35,0,141,'OVB20171117105929',150.00,0.00,0.00,150.00,'',1510887569,18,1510887569,0,NULL,0),(12,0,35,0,141,'GSU20171117142816',150.00,0.00,0.00,150.00,'',1510900096,18,1510900096,0,NULL,0),(13,0,35,0,141,'NJY20171117142842',150.00,0.00,0.00,150.00,'',1510900122,18,1510900122,0,NULL,0),(14,0,35,0,141,'BIY20171117143013',150.00,0.00,0.00,150.00,'',1510900213,18,1510900213,0,NULL,0),(15,0,35,0,144,'PID20171117173300',750.00,0.00,0.00,0.00,'',1510911180,18,1510911180,0,NULL,0);
/*!40000 ALTER TABLE `x360p_order_refund` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COMMENT='订单付款记录ID';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_refund_history`
--

LOCK TABLES `x360p_order_refund_history` WRITE;
/*!40000 ALTER TABLE `x360p_order_refund_history` DISABLE KEYS */;
INSERT INTO `x360p_order_refund_history` VALUES (1,0,35,1,0,7,151.00,0,1510636208,18,1510636208,0,NULL,0),(2,0,35,2,0,7,271.00,0,1510643395,18,1510643395,0,NULL,0),(3,0,35,3,0,7,200.00,0,1510643514,18,1510643514,0,NULL,0),(4,0,35,4,0,7,321.00,0,1510644189,18,1510644189,0,NULL,0),(5,0,35,5,0,7,335.00,0,1510655847,18,1510655847,0,NULL,0),(6,0,35,6,0,7,282.73,0,1510664943,18,1510664943,0,NULL,0),(7,0,35,7,0,7,138.73,0,1510665010,18,1510665010,0,NULL,0),(8,0,35,8,0,7,140.73,0,1510707478,18,1510707478,0,NULL,0),(9,0,35,9,0,7,140.73,0,1510707684,18,1510707684,0,NULL,0),(10,0,35,10,0,7,190.73,0,1510707786,18,1510707786,0,NULL,0),(11,0,35,11,0,7,150.00,0,1510887569,18,1510887569,0,NULL,0),(12,0,35,12,0,7,150.00,0,1510900096,18,1510900096,0,NULL,0),(13,0,35,13,0,7,150.00,0,1510900122,18,1510900122,0,NULL,0),(14,0,35,14,0,7,150.00,0,1510900213,18,1510900213,0,NULL,0),(15,0,35,15,0,7,0.00,0,1510911180,18,1510911180,0,NULL,0);
/*!40000 ALTER TABLE `x360p_order_refund_history` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COMMENT='订单退费记录项目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_refund_item`
--

LOCK TABLES `x360p_order_refund_item` WRITE;
/*!40000 ALTER TABLE `x360p_order_refund_item` DISABLE KEYS */;
INSERT INTO `x360p_order_refund_item` VALUES (1,0,1,173,1.00,150.00,150.00,1510636208,18,1510636208,0,NULL,0),(2,0,2,180,1.00,120.00,120.00,1510643395,18,1510643395,0,NULL,0),(3,0,3,182,1.00,150.00,150.00,1510643513,18,1510643513,0,NULL,0),(4,0,4,179,3.00,190.00,570.00,1510644189,18,1510644189,0,NULL,0),(5,0,5,179,1.00,190.00,190.00,1510655847,18,1510655847,0,NULL,0),(6,0,7,189,1.00,150.00,150.00,1510665010,18,1510665010,0,NULL,0),(7,0,8,189,1.00,150.00,150.00,1510707478,18,1510707478,0,NULL,0),(8,0,9,189,1.00,150.00,150.00,1510707684,18,1510707684,0,NULL,0),(9,0,10,189,1.00,150.00,150.00,1510707786,18,1510707786,0,NULL,0),(10,0,11,209,1.00,150.00,150.00,1510887569,18,1510887569,0,NULL,0),(11,0,12,209,1.00,150.00,150.00,1510900096,18,1510900096,0,NULL,0),(12,0,13,209,1.00,150.00,150.00,1510900122,18,1510900122,0,NULL,0),(13,0,14,209,1.00,150.00,150.00,1510900213,18,1510900213,0,NULL,0),(14,0,15,228,5.00,150.00,750.00,1510911180,18,1510911180,0,NULL,0);
/*!40000 ALTER TABLE `x360p_order_refund_item` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COMMENT='订单结转记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_transfer`
--

LOCK TABLES `x360p_order_transfer` WRITE;
/*!40000 ALTER TABLE `x360p_order_transfer` DISABLE KEYS */;
INSERT INTO `x360p_order_transfer` VALUES (3,0,35,0,121,'',125.60,1510313925,18,1510313925,0,NULL,0),(4,0,35,0,121,'',50.75,1510313993,18,1510313993,0,NULL,0),(5,0,35,0,126,'',190.00,1510386458,18,1510386458,0,NULL,0),(6,0,35,0,126,'',600.00,1510387007,18,1510387007,0,NULL,0),(7,0,35,0,126,'',1500.00,1510390695,18,1510390695,0,NULL,0),(8,0,35,0,126,'',1240.00,1510391888,18,1510391888,0,NULL,0),(9,0,35,0,134,'',500.00,1510656282,18,1510656282,0,NULL,0),(10,0,35,0,135,'',350.00,1510656336,18,1510656336,0,NULL,0),(11,0,35,0,135,'',150.00,1510661889,18,1510661889,0,NULL,0),(12,0,35,0,135,'',300.00,1510661944,18,1510661944,0,NULL,0),(13,0,35,0,135,'',150.00,1510661989,18,1510661989,0,NULL,0),(14,0,35,0,135,'',150.00,1510662346,18,1510662346,0,NULL,0),(15,0,35,0,135,'',450.00,1510664306,18,1510664306,0,NULL,0),(16,0,35,0,135,'',150.00,1510664871,18,1510664871,0,NULL,0),(17,0,35,0,135,'',150.00,1510664910,18,1510664910,0,NULL,0),(18,0,35,0,145,'KTZ20171117091913',1650.00,1510881553,18,1510881553,0,NULL,0),(19,0,35,0,136,'EGP20171117191332',200.00,1510917212,18,1510917212,0,NULL,0);
/*!40000 ALTER TABLE `x360p_order_transfer` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COMMENT='结转项目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_transfer_item`
--

LOCK TABLES `x360p_order_transfer_item` WRITE;
/*!40000 ALTER TABLE `x360p_order_transfer_item` DISABLE KEYS */;
INSERT INTO `x360p_order_transfer_item` VALUES (1,0,3,156,1.00,125.60,125.60,1510313925,18,1510313925,0,NULL,0),(2,0,4,157,1.00,50.75,50.75,1510313993,18,1510313993,0,NULL,0),(3,0,5,164,1.00,190.00,190.00,1510386458,18,1510386458,0,NULL,0),(4,0,6,166,4.00,150.00,600.00,1510387007,18,1510387007,0,NULL,0),(5,0,7,168,10.00,150.00,1500.00,1510390695,18,1510390695,0,NULL,0),(6,0,8,166,6.00,150.00,900.00,1510391888,18,1510391888,0,NULL,0),(7,0,8,165,1.00,150.00,150.00,1510391888,18,1510391888,0,NULL,0),(8,0,8,164,1.00,190.00,190.00,1510391888,18,1510391888,0,NULL,0),(9,0,9,183,1.00,500.00,500.00,1510656282,18,1510656282,0,NULL,0),(10,0,10,184,1.00,150.00,150.00,1510656336,18,1510656336,0,NULL,0),(11,0,10,185,1.00,200.00,200.00,1510656336,18,1510656336,0,NULL,0),(12,0,11,184,1.00,150.00,150.00,1510661889,18,1510661889,0,NULL,0),(13,0,12,184,2.00,150.00,300.00,1510661944,18,1510661944,0,NULL,0),(14,0,13,184,1.00,150.00,150.00,1510661989,18,1510661989,0,NULL,0),(15,0,14,184,1.00,150.00,150.00,1510662346,18,1510662346,0,NULL,0),(16,0,15,189,3.00,150.00,450.00,1510664306,18,1510664306,0,NULL,0),(17,0,16,189,1.00,150.00,150.00,1510664871,18,1510664871,0,NULL,0),(18,0,17,189,1.00,150.00,150.00,1510664910,18,1510664910,0,NULL,0),(19,0,18,211,10.00,150.00,1500.00,1510881553,18,1510881553,0,NULL,0),(20,0,18,212,1.00,150.00,150.00,1510881553,18,1510881553,0,NULL,0),(21,0,19,203,1.00,200.00,200.00,1510917212,18,1510917212,0,NULL,0);
/*!40000 ALTER TABLE `x360p_order_transfer_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_org`
--

DROP TABLE IF EXISTS `x360p_org`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_org` (
  `og_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '机构ID',
  `org_name` varchar(255) NOT NULL DEFAULT '' COMMENT '机构名称',
  `org_short_name` varchar(64) NOT NULL DEFAULT '' COMMENT '机构简称',
  `province` varchar(64) NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(64) NOT NULL DEFAULT '' COMMENT '城市',
  `district` varchar(64) DEFAULT NULL,
  `area_id` int(11) NOT NULL,
  `org_address` varchar(255) NOT NULL DEFAULT '' COMMENT '机构地址',
  `branch_num_limit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区数限制',
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
-- Dumping data for table `x360p_org`
--

LOCK TABLES `x360p_org` WRITE;
/*!40000 ALTER TABLE `x360p_org` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_org` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_payment_log`
--

LOCK TABLES `x360p_payment_log` WRITE;
/*!40000 ALTER TABLE `x360p_payment_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_payment_log` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_print_tpl`
--

LOCK TABLES `x360p_print_tpl` WRITE;
/*!40000 ALTER TABLE `x360p_print_tpl` DISABLE KEYS */;
INSERT INTO `x360p_print_tpl` VALUES (19,0,35,1,2,'{\"content\":\"<div class=\\\"fe\\\" style=\\\"padding: 20px 12px 0px 8px; font-size: 10px; margin-left: 0px; margin-top: 0px;\\\">\\r\\n\\t<p class=\\\"textCenter\\\">{{sys.org_name}}<\\/p>\\r\\n\\t<p style=\\\"margin-top: 3px;\\\">\\r\\n\\t\\t<\\/p><div class=\\\"leftFloat\\\"><\\/div>\\r\\n\\t\\t<div class=\\\"rightFloat\\\">{{bs.receipt_no}}<\\/div>\\r\\n\\t\\t<div class=\\\"clearFloat\\\"><\\/div>\\r\\n\\t<p><\\/p>\\r\\n\\t<p style=\\\"padding-top: 10px;\\\">学生姓名：{{bs.student_name}}<\\/p>\\r\\n\\t<p>学&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号：{{bs.sno}}<\\/p>\\r\\n\\t<p>交费日期：{{bs.pay_date}}<\\/p>\\r\\n\\t<table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" class=\\\"dashed-top\\\" style=\\\"margin-bottom: 5px;\\\">\\r\\n\\t\\t<tbody>\\r\\n\\t\\t\\t<tr>\\r\\n\\t\\t\\t\\t<th class=\\\"textLeft\\\" style=\\\"width: 70px;\\\">班别\\/项目<\\/th>\\r\\n\\t\\t\\t\\t<th class=\\\"textRight\\\" width=\\\"55px\\\">数量&nbsp;&nbsp;<\\/th>\\r\\n\\t\\t\\t\\t<th class=\\\"textRight\\\" style=\\\"width: 55px;\\\">单价<\\/th>\\r\\n\\t\\t\\t\\t<th class=\\\"textRight\\\" width=\\\"65px\\\">折后金额<\\/th>\\r\\n\\t\\t\\t<\\/tr>\\r\\n\\t\\t<\\/tbody>\\r\\n\\t<\\/table>\\r\\n\\t<table>\\r\\n\\t\\t<tbody v-for=\\\"item in bm\\\">\\r\\n\\t\\t\\t<tr>\\r\\n\\t\\t\\t\\t<td colspan=\\\"4\\\" style=\\\"width: 1600px;\\\">\\r\\n\\t\\t\\t\\t\\t{{item.lesson_name}}\\r\\n\\t\\t\\t\\t<\\/td>\\r\\n\\t\\t\\t<\\/tr>\\r\\n\\t\\t\\t<tr>\\r\\n\\t\\t\\t\\t<td colspan=\\\"2\\\" class=\\\"textRight text-nowrap\\\" style=\\\"padding-right: 3px;\\\">{{item.nums}}<\\/td>\\r\\n\\t\\t\\t\\t<td class=\\\"textRight\\\">{{item.origin_price}}<\\/td>\\r\\n\\t\\t\\t\\t<td class=\\\"textRight\\\">{{item.price}}<\\/td>\\r\\n\\t\\t\\t<\\/tr>\\r\\n\\t\\t<\\/tbody>\\r\\n\\t<\\/table>\\t\\r\\n\\t<\\/table>\\r\\n\\t<div class=\\\"dashed-top\\\" style=\\\" padding-top: 10px;\\\">\\r\\n\\t\\t<p>应收合计：{{bs.origin_amount}}<\\/p>\\r\\n\\t\\t<p>\\r\\n\\t\\t\\t<span style=\\\"display: inline;\\\">冲减电子钱包：<\\/span>\\r\\n\\t\\t\\t<span>{{bs.balance_paid_amount}}<\\/span>\\r\\n\\t\\t<\\/p>\\r\\n\\t\\t<p>直减优惠：{{bs.order_reduce_amount}}<\\/p>\\r\\n\\t\\t<p>实收金额：{{bs.pay_amount}}<\\/p>\\t\\t\\r\\n\\t\\t<p>\\r\\n\\t\\t\\t<span style=\\\"display: inline;\\\">计入电子钱包：<\\/span>\\t\\t\\t\\r\\n\\t\\t\\t<span>{{bs.pay_remain_amount}}<\\/span>\\r\\n\\t\\t<\\/p>\\r\\n\\t\\t<p>大写金额：{{bs.pay_amount_b}}<\\/p>\\r\\n\\t\\t<p style=\\\"line-height: 1.2;\\\">备注：{{bs.pay_remark}}<\\/p>\\r\\n\\t<\\/div>\\r\\n\\t\\r\\n\\t<div class=\\\"dashed-top dashed-bottom\\\" style=\\\"padding-top: 10px;\\\">\\r\\n\\t\\t<p>经办人：{{bs.op_name}}<\\/p>\\r\\n\\t\\t<p>客户签名：<\\/p>\\r\\n\\t\\t<p style=\\\"line-height:1.3;\\\"><\\/p>\\r\\n\\t<\\/div>\\r\\n\\t<div class=\\\"dashed-bottom\\\" style=\\\"display: block;\\\">\\r\\n\\t\\t\\t\\t<p style=\\\"line-height: 1;margin-top: 4px;\\\">掌握孩子学情,请微信关注\\\"校360\\\"<\\/p>\\r\\n\\t\\t\\t\\t<p>用户名：{{bs.amount}}<\\/p>\\r\\n\\t\\t\\t\\t<p>(密码请咨询学校工作人员)<\\/p>\\r\\n\\t\\t\\t\\t\\r\\n\\t\\t\\t\\t<div class=\\\"ecode\\\">\\r\\n\\t\\t\\t\\t\\t<img width=\\\"80%\\\" src=\\\"\\/static\\/img\\/mpqr.jpg\\\">\\r\\n\\t\\t\\t\\t<\\/div>\\r\\n\\t\\t\\t\\t<div class=\\\"clearFloat\\\"><\\/div>\\r\\n\\t<\\/div>\\r\\n\\t<p class=\\\"info\\\"><\\/p>\\r\\n<\\/div>\\r\\n\"}',0,1510887054,18,1510922043,0,0,NULL),(20,0,35,2,2,'{\"content\":\"<div class=\\\"refundfe\\\">\\r\\n\\t<p class=\\\"textCenter\\\">{{sys.org_name}}<\\/p>\\r\\n\\t<p style=\\\"margin-top: 3px;\\\">\\r\\n\\t\\t<\\/p><div class=\\\"leftFloat\\\"><\\/div>\\r\\n\\t\\t<div class=\\\"rightFloat\\\">{{bs.receipt_no}}<\\/div>\\r\\n\\t\\t<div class=\\\"clearFloat\\\"><\\/div>\\r\\n\\t<p><\\/p>\\r\\n\\t<p style=\\\"padding-top: 10px;\\\">学生姓名：{{bs.student_name}}<\\/p>\\r\\n\\t<p>学&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号：{{bs.sno}}<\\/p>\\r\\n\\t<p>退费日期：{{bs.pay_date}}<\\/p>\\r\\n\\t<table cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" class=\\\"dashed-top\\\" style=\\\"margin-bottom: 5px; width: 99%;\\\">\\r\\n\\t\\t<tbody>\\r\\n\\t\\t\\t<tr>\\r\\n\\t\\t\\t\\t<th class=\\\"textLeft\\\" style=\\\"width: 65px;\\\">项目名称<\\/th>\\r\\n\\t\\t\\t\\t<th class=\\\"textRight\\\" width=\\\"55px\\\">数量&nbsp;&nbsp;<\\/th>\\r\\n\\t\\t\\t\\t<th class=\\\"textRight\\\" style=\\\"width: 55px;\\\">单位<\\/th>\\r\\n\\t\\t\\t\\t<th class=\\\"textRight\\\" width=\\\"70px\\\">金额<\\/th>\\r\\n\\t\\t\\t<\\/tr>\\r\\n\\t\\t<\\/tbody>\\r\\n\\t<\\/table>\\r\\n\\t<table>\\r\\n\\t\\t<tbody v-for=\\\"item in bm\\\">\\r\\n\\t\\t\\t<tr>\\r\\n\\t\\t\\t\\t<td colspan=\\\"4\\\" style=\\\"width: 1600px;\\\">{{item.lesson_name}}<\\/td>\\r\\n\\t\\t\\t<\\/tr>\\r\\n\\t\\t\\t<tr>\\r\\n\\t\\t\\t\\t<td><\\/td>\\r\\n\\t\\t\\t\\t<td class=\\\"textRight\\\">{{item.nums}}<\\/td>\\r\\n\\t\\t\\t\\t<td class=\\\"textRight\\\">{{item.nums_unit}}<\\/td>\\r\\n\\t\\t\\t\\t<td class=\\\"textRight\\\">{{item.subtotal}}<\\/td>\\r\\n\\t\\t\\t<\\/tr>\\r\\n\\t\\t<\\/tbody>\\r\\n\\t<\\/table>\\r\\n\\t<div class=\\\"dashed-top\\\" style=\\\" padding-top: 5px;\\\">\\r\\n\\t\\t<p>退费项目合计：{{bs.need_refund_amount}}<\\/p>\\r\\n\\t\\t<p>退电子钱包：{{bs.refund_balance_amount}}<\\/p>\\r\\n\\t\\t<p>扣款金额：{{bs.cut_amount}}<\\/p>\\r\\n\\t\\t<p>实际退费：{{bs.refund_amount}}<\\/p>\\r\\n\\t\\t<p>大写金额：{{bs.refund_amount_b}}<\\/p>\\r\\n\\t<\\/div>\\r\\n\\t\\r\\n\\t<div class=\\\"dashed-top dashed-bottom\\\" style=\\\"padding-top: 5px;\\\">\\r\\n\\t\\t<p>经办人：{{bs.op_name}}<\\/p>\\r\\n\\t\\t<p>客户签字：<\\/p>\\r\\n\\t\\t<p style=\\\"line-height:1.3;\\\"><\\/p>\\r\\n\\t<\\/div>\\r\\n\\t<div class=\\\"dashed-bottom\\\" style=\\\"display: block;\\\">\\r\\n\\t\\t<div class=\\\"shishengxin\\\">\\r\\n\\t\\t\\t<p style=\\\"line-height: 1;margin-top: 4px;\\\">掌握孩子学情,请微信关注\\\"校360\\\"<\\/p>\\t\\t\\t\\t\\t\\r\\n\\t\\t<\\/div>\\r\\n\\t\\t<div class=\\\"ecode\\\">\\r\\n\\t\\t\\t<img width=\\\"80%\\\" src=\\\"\\/static\\/img\\/mpqr.jpg\\\">\\r\\n\\t\\t<\\/div>\\r\\n\\t<\\/div>\\r\\n\\t<div class=\\\"info\\\"><\\/div>\\r\\n<\\/div>\"}',1,1510900186,18,1510900186,0,0,NULL),(21,0,35,1,3,'{\"content\":{\"paper_width\":210,\"paper_height\":140,\"items\":[{\"width\":111,\"height\":20,\"left\":274,\"top\":17,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"org_name\",\"text\":\"机构名称\",\"type\":\"sys\"},{\"width\":159,\"height\":20,\"left\":81,\"top\":83,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"student_name\",\"text\":\"学员姓名\",\"type\":\"bs\"},{\"width\":140,\"height\":20,\"left\":384,\"top\":305,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"pay_date\",\"text\":\"交费日期\",\"type\":\"bs\"},{\"width\":140,\"height\":20,\"left\":81,\"top\":306,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"op_name\",\"text\":\"操作员\",\"type\":\"bs\"},{\"width\":67,\"height\":20,\"left\":638,\"top\":197,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"origin_amount\",\"text\":\"应收合计\",\"type\":\"bs\"},{\"width\":319,\"height\":20,\"left\":218,\"top\":197,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"pay_remark\",\"text\":\"备注\",\"type\":\"bs\"},{\"width\":141,\"height\":20,\"left\":51,\"top\":140,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"item_name\",\"text\":\"项目\",\"type\":\"bm\"},{\"width\":104,\"height\":20,\"left\":479,\"top\":140,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"origin_price\",\"text\":\"原价\",\"type\":\"bm\"},{\"width\":68,\"height\":20,\"left\":553,\"top\":141,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"price\",\"text\":\"折后单价\",\"type\":\"bm\"},{\"width\":62,\"height\":20,\"left\":641,\"top\":141,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"subtotal\",\"text\":\"小计\",\"type\":\"bm\"},{\"width\":303,\"height\":20,\"left\":220,\"top\":273,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"pay_amount_b\",\"text\":\"实收大写\",\"type\":\"bs\"},{\"width\":71,\"height\":20,\"left\":639,\"top\":237,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"pay_amount\",\"text\":\"实收\",\"type\":\"bs\"},{\"width\":58,\"height\":20,\"left\":411,\"top\":139,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"nums\",\"text\":\"数量\",\"type\":\"bm\"},{\"width\":101,\"height\":20,\"left\":288,\"top\":138,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"expire_time\",\"text\":\"有效期\",\"type\":\"bm\"},{\"width\":53,\"height\":20,\"left\":218,\"top\":139,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"class_name\",\"text\":\"班级\",\"type\":\"bm\"},{\"width\":69,\"height\":20,\"left\":640,\"top\":274,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"pay_remain_amount\",\"text\":\"计入电子钱包\\/欠缴金额\",\"type\":\"bs\"},{\"width\":96,\"height\":20,\"left\":218,\"top\":235,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"balance_paid_amount\",\"text\":\"冲减电子钱包\",\"type\":\"bs\"},{\"width\":67,\"height\":20,\"left\":447,\"top\":236,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"order_reduce_amount\",\"text\":\"直减优惠\",\"type\":\"bs\"},{\"width\":140,\"height\":20,\"left\":533,\"top\":85,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"branch_name\",\"text\":\"校区名\",\"type\":\"sys\"}]}}',1,1510911248,18,1510922043,0,0,NULL);
/*!40000 ALTER TABLE `x360p_print_tpl` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_promotion_rule`
--

LOCK TABLES `x360p_promotion_rule` WRITE;
/*!40000 ALTER TABLE `x360p_promotion_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_promotion_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_public_school`
--

DROP TABLE IF EXISTS `x360p_public_school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_public_school` (
  `ps_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
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
-- Dumping data for table `x360p_public_school`
--

LOCK TABLES `x360p_public_school` WRITE;
/*!40000 ALTER TABLE `x360p_public_school` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_public_school` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_recommend_lesson`
--

DROP TABLE IF EXISTS `x360p_recommend_lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_recommend_lesson` (
  `rl_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `gid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sid` int(11) unsigned NOT NULL COMMENT '学生id',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`rl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生计划课程表（由老师推送）';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_recommend_lesson`
--

LOCK TABLES `x360p_recommend_lesson` WRITE;
/*!40000 ALTER TABLE `x360p_recommend_lesson` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_recommend_lesson` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_review`
--

DROP TABLE IF EXISTS `x360p_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_review` (
  `review_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `review_type` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '点评类型(1:校区环境点评,2:课堂点评,3:听课点评,4:老师点评,5:导师点评,6:活动点评)',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区id',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '老师ID或导师ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `chapter_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程章节序号',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课堂排课ID',
  `cla_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '试听申请ID',
  `comment_tags` varchar(255) NOT NULL DEFAULT '' COMMENT '评论标签数组(json)',
  `event_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动ID',
  `score1` int(4) NOT NULL DEFAULT '0' COMMENT '打分1',
  `score2` int(4) NOT NULL DEFAULT '0' COMMENT '打分2',
  `score3` int(4) NOT NULL DEFAULT '0' COMMENT '打分3',
  `score4` int(4) NOT NULL DEFAULT '0' COMMENT '打分4',
  `score5` int(4) NOT NULL DEFAULT '0' COMMENT '打分5',
  `score6` int(4) NOT NULL DEFAULT '0' COMMENT '打分6',
  `score7` int(4) NOT NULL DEFAULT '0' COMMENT '打分7',
  `score8` int(4) NOT NULL DEFAULT '0' COMMENT '打分8',
  `score9` int(4) NOT NULL DEFAULT '0' COMMENT '打分9',
  `score10` int(4) NOT NULL DEFAULT '0' COMMENT '打分10',
  `review_content` tinytext NOT NULL COMMENT '点评内容',
  `is_view` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否查阅',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户评价表(学生家长对校区、老师、试听、活动产生的评价都在这里)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_review`
--

LOCK TABLES `x360p_review` WRITE;
/*!40000 ALTER TABLE `x360p_review` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_review` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COMMENT='系统角色表(每一个用户都对应有1到多个角色)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_role`
--

LOCK TABLES `x360p_role` WRITE;
/*!40000 ALTER TABLE `x360p_role` DISABLE KEYS */;
INSERT INTO `x360p_role` VALUES (1,0,'老师','传授知识 , 教书育人',',basic,account,branchs,employees,classes,students,students.list,students.new,students.import,students.edit,students.delete,students.pushlesson,goods,goods.list','',1,1498095552,1,1504668175,NULL,0,0,NULL,NULL),(2,0,'导师','灵魂导师啊啊啊啊',',basic,account,branchs,employees,classes,students,students.list,students.new,students.import,students.edit,students.delete,students.pushlesson,lessons,lessons.list,goods,goods.list','',1,1498098725,1,1504668445,NULL,0,0,NULL,NULL),(3,0,'课程经理','课程设计','',NULL,1,1498290947,1,1498290947,NULL,0,0,NULL,NULL),(5,0,'客服','负责客户咨询方面工作','',NULL,0,1498290965,1,1498290965,NULL,0,0,NULL,NULL),(6,0,'厨师','负责学校食堂伙食','',NULL,0,1498290995,1,1498290995,NULL,0,0,NULL,NULL),(7,0,'宿管','负责学生寝室清洁与安全','',NULL,0,1498291022,1,1498291022,NULL,0,0,NULL,NULL),(9,0,'财务','负责学员缴费工作','',NULL,0,1498291051,1,1498291051,NULL,0,0,NULL,NULL),(10,0,'教务主管','负责老师教学任务分配',',summer,summer.apply,summer.review,basic,account,branchs,employees,classes,students,students.list,students.new,students.import,students.edit,students.delete,students.pushlesson,lessons,lessons.list,lessons.new,lessons.edit,lessons.upload_file,lessons.update_file,lessons.on,lessons.off,lessons.delete,goods,goods.list,goods.new,goods.edit,goods.off,goods.on,goods.delete',NULL,0,1498291075,1,1504663475,NULL,0,0,NULL,NULL),(11,0,'财务主管','负责校区财务工作','account,account.students,account.orgs',NULL,0,1498291136,1,1498295975,NULL,0,0,NULL,NULL),(12,0,'学管主管','负责学管师任务分配','account,account.students,account.orgs',NULL,0,1498291159,1,1498291876,NULL,0,0,NULL,NULL),(13,0,'校长','负责学校运营管理',',summer,summer.apply,summer.review,basic,account,branchs,employees,classes,students,students.list,students.new,students.import,students.edit,students.delete,students.pushlesson',NULL,0,1498293037,1,1504667998,NULL,0,0,NULL,NULL),(14,0,'副校长','学校二把手','',NULL,0,1498296408,1,1498296408,NULL,0,0,NULL,NULL),(15,0,'政教处主任','负责学校风气管理','',NULL,0,1498296474,1,1498296474,NULL,0,0,NULL,NULL),(16,0,'体育老师','负责教学生体育课','',NULL,0,1498296553,1,1498296553,NULL,0,0,NULL,NULL),(17,0,'排课员','负责课程教室安排','',NULL,0,1498297478,1,1498297478,NULL,0,0,NULL,NULL),(19,0,'管理员','管理系统啊啊啊','',NULL,0,1499771311,1,1499771311,NULL,0,0,NULL,NULL);
/*!40000 ALTER TABLE `x360p_role` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_season_date`
--

LOCK TABLES `x360p_season_date` WRITE;
/*!40000 ALTER TABLE `x360p_season_date` DISABLE KEYS */;
INSERT INTO `x360p_season_date` VALUES (1,0,0,0,'H',99990121,99990219,0,0,NULL,0,0,0),(2,0,0,0,'C',99990225,99990706,0,0,NULL,0,0,0),(3,0,0,0,'S',99990713,99990831,0,0,NULL,0,0,0),(4,0,0,0,'Q',99990901,99990110,0,0,NULL,0,0,0),(5,0,0,2017,'H',20170121,20170219,1510045897,0,NULL,0,0,1510045897),(6,0,0,2017,'C',20170225,20170706,1510050209,0,NULL,0,0,1510050209),(7,0,0,2017,'S',20170713,20170831,1510050211,0,NULL,0,0,1510050211),(8,0,0,2017,'Q',20170901,20170110,1510050213,0,NULL,0,0,1510050213),(9,0,35,2017,'Q',20170901,20170110,1510050225,0,NULL,0,0,1510050225);
/*!40000 ALTER TABLE `x360p_season_date` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_share_history`
--

DROP TABLE IF EXISTS `x360p_share_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_share_history` (
  `sh_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'share_history_id  主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `uid` int(11) unsigned NOT NULL COMMENT '用户ID',
  `eid` int(11) unsigned NOT NULL COMMENT '员工id',
  `business_type` tinyint(2) unsigned NOT NULL COMMENT '1：课程，2：老师简介，3：活动',
  `record_id` int(11) unsigned NOT NULL COMMENT '具体的业务数据的ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`sh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分享记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_share_history`
--

LOCK TABLES `x360p_share_history` WRITE;
/*!40000 ALTER TABLE `x360p_share_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_share_history` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_sms_history`
--

LOCK TABLES `x360p_sms_history` WRITE;
/*!40000 ALTER TABLE `x360p_sms_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_sms_history` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_sms_vcode`
--

LOCK TABLES `x360p_sms_vcode` WRITE;
/*!40000 ALTER TABLE `x360p_sms_vcode` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_sms_vcode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_stats_edu`
--

DROP TABLE IF EXISTS `x360p_stats_edu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_stats_edu` (
  `se_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT '校区id',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '活动id',
  `year` smallint(6) NOT NULL DEFAULT '0',
  `season` enum('H','Q','S','C') DEFAULT NULL,
  `total_class_num` int(11) NOT NULL DEFAULT '0' COMMENT '班级数量',
  `total_student_num` int(11) NOT NULL DEFAULT '0' COMMENT '学生数量',
  `total_research_num` int(11) NOT NULL DEFAULT '0' COMMENT '调查次数',
  `total_scheme_num` int(11) NOT NULL DEFAULT '0' COMMENT '方案次数',
  `total_growup_num` int(11) NOT NULL DEFAULT '0' COMMENT '学员成长对比次数',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete-uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`se_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='统计教育服务表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_stats_edu`
--

LOCK TABLES `x360p_stats_edu` WRITE;
/*!40000 ALTER TABLE `x360p_stats_edu` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_stats_edu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_stats_service`
--

DROP TABLE IF EXISTS `x360p_stats_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_stats_service` (
  `ss_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT '校区id',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '教师id',
  `year` smallint(6) NOT NULL DEFAULT '0',
  `season` enum('H','C','S','Q') DEFAULT NULL,
  `cur_class_num` smallint(6) NOT NULL DEFAULT '0' COMMENT '当前服务班级数',
  `plan_class_num` smallint(6) NOT NULL DEFAULT '0' COMMENT '计划服务班级数',
  `total_class_num` int(11) NOT NULL DEFAULT '0' COMMENT '累计服务班级数',
  `cur_student_num` int(11) NOT NULL DEFAULT '0' COMMENT '当前服务学员数',
  `plan_student_num` int(11) NOT NULL DEFAULT '0' COMMENT '计划服务学员数',
  `total_student_num` int(11) NOT NULL DEFAULT '0' COMMENT '累计服务学员数',
  `total_share_lesson_num` int(11) NOT NULL DEFAULT '0' COMMENT '累计分享课程次数',
  `cur_course_num` int(11) NOT NULL DEFAULT '0' COMMENT '本期上课次数',
  `total_course_num` int(11) NOT NULL DEFAULT '0' COMMENT '累计上课次数',
  `cur_scheme_num` int(11) NOT NULL DEFAULT '0' COMMENT '本期方案次数',
  `total_scheme_num` int(11) NOT NULL DEFAULT '0' COMMENT '累计方案次数',
  `cur_experience_lesson_num` int(11) NOT NULL DEFAULT '0' COMMENT '本期体验课次数',
  `total_experience_lesson_num` int(11) NOT NULL DEFAULT '0' COMMENT '累计体验课次数',
  `cur_listen_apply_num` int(11) NOT NULL DEFAULT '0' COMMENT '本期预约听课次数',
  `total_listen_apply_num` int(11) NOT NULL DEFAULT '0' COMMENT '累计预约听课次数',
  `review_avg_score` smallint(6) NOT NULL DEFAULT '0' COMMENT '服务星级评价平均分',
  `push_prepare_num` int(11) NOT NULL DEFAULT '0' COMMENT '推送预习数',
  `push_homework_num` int(11) NOT NULL DEFAULT '0' COMMENT '推送作业数',
  `edu_research_num` int(11) NOT NULL DEFAULT '0' COMMENT '调研次数',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ss_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='教育服务统计表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_stats_service`
--

LOCK TABLES `x360p_stats_service` WRITE;
/*!40000 ALTER TABLE `x360p_stats_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_stats_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_stats_study`
--

DROP TABLE IF EXISTS `x360p_stats_study`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_stats_study` (
  `ss_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT '校区id',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生id',
  `year` smallint(6) NOT NULL DEFAULT '0',
  `season` enum('H','Q','S','C') DEFAULT NULL,
  `learned_num` int(11) NOT NULL DEFAULT '0' COMMENT '已读课程数',
  `learning_num` int(11) NOT NULL DEFAULT '0' COMMENT '在读课程',
  `will_learn_num` int(11) NOT NULL DEFAULT '0' COMMENT '计划读课程数',
  `label` smallint(6) NOT NULL DEFAULT '0' COMMENT '服务星级，取自student',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`ss_id`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=utf8 COMMENT='统计学生学习报表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_stats_study`
--

LOCK TABLES `x360p_stats_study` WRITE;
/*!40000 ALTER TABLE `x360p_stats_study` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_stats_study` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_stats_teaching`
--

DROP TABLE IF EXISTS `x360p_stats_teaching`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_stats_teaching` (
  `st_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT '校区id',
  `eid` int(11) NOT NULL DEFAULT '0',
  `year` smallint(6) NOT NULL DEFAULT '0',
  `season` enum('C','S','Q','H') DEFAULT NULL,
  `total_class_num` int(11) NOT NULL DEFAULT '0' COMMENT '班级数量',
  `total_student_num` int(11) NOT NULL DEFAULT '0' COMMENT '学生数量',
  `teached_course_num` int(11) NOT NULL DEFAULT '0' COMMENT '已经教的课程',
  `teaching_course_num` int(11) NOT NULL DEFAULT '0' COMMENT '这期课程数',
  `publish_homework_num` int(11) NOT NULL DEFAULT '0' COMMENT '发布作业数量',
  `check_homework_num` int(11) NOT NULL DEFAULT '0' COMMENT '批改作业数量',
  `carefully_check_num` int(11) NOT NULL DEFAULT '0' COMMENT '精批作业数量',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`st_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='统计教学相关数据';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_stats_teaching`
--

LOCK TABLES `x360p_stats_teaching` WRITE;
/*!40000 ALTER TABLE `x360p_stats_teaching` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_stats_teaching` ENABLE KEYS */;
UNLOCK TABLES;

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
  `second_family_name` varchar(32) NOT NULL DEFAULT '' COMMENT '第2亲属姓名',
  `second_family_rel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0：未设置，1:自己，2：爸爸，3：妈妈，4：其他',
  `second_tel` varchar(16) NOT NULL DEFAULT '' COMMENT '第2电话',
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
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COMMENT='学员表(学员的记录信息)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student`
--

LOCK TABLES `x360p_student` WRITE;
/*!40000 ALTER TABLE `x360p_student` DISABLE KEYS */;
INSERT INTO `x360p_student` VALUES (1,0,35,'学员001','','1','http://s10.xiao360.com/qms/student_avatar/18/17/11/11/2121f1e3e0b61fcdbabf3cbce45f4379.png',823305600,2017,9,25,0,'',0,'13434086673','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1499848253,1,1510393473,0,NULL,18,''),(2,0,35,'学员002','','2','',762742934,2017,7,13,0,'',0,'13434086674','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1499853276,1,1506327412,1,1506327412,18,''),(3,0,35,'学员003','','1','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/fe6ae8c99109201107907461f155b61d.gif',1052787750,2017,7,13,0,'',0,'13534257745','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1499853438,1,1509787301,0,NULL,0,''),(4,0,35,'学员0001','','1','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/a44158bb77e2a5306ad342e552a04c78.gif',0,0,0,0,0,'',0,'13544657756','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1500081293,18,1509788767,0,NULL,0,''),(5,0,35,'小就','m','1','http://s10.xiao360.com/qms/avatar/18/17/07/18/c10053c4f5c99a0413bef1706964a80e.jpg',1278432000,2017,9,23,5,'3',1,'13398765624','大明',2,'小明',1,'15689745632','',NULL,0.00,0.00,0,0,0,1500081995,18,1506153992,1,1506153992,18,''),(6,0,35,'小刚','','1','',0,0,0,0,0,'',0,'17786954569','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1500082431,18,1506155853,1,1506155853,18,''),(7,0,35,'小花','','2','http://s10.xiao360.com/qms/student_avatar/18/17/11/04/9b6839e21f44c1cb0d49a755e7653c9f.gif',1278345600,2017,7,15,3,'1',2,'17789564521','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1500082479,18,1509789558,0,NULL,0,''),(8,0,35,'test','','1','http://s10.xiao360.com/qms/student_avatar/18/17/11/06/5b57c79b6bd8e169b8437b850e7759dc.gif',0,0,0,0,0,'',0,'17768026456','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1500273972,18,1509957420,0,NULL,0,''),(9,0,35,'姚瑞','pony','1','',691689600,1991,12,3,0,'0',0,'17768026485','姚瑞啊',2,'',1,'','','abc',0.00,0.00,0,0,0,1500274384,18,1509714277,0,NULL,0,''),(10,0,35,'tes1','zzz','2','',1278485710,0,0,0,0,'',0,'17768565485','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1500274665,18,1500274665,0,NULL,0,''),(11,0,35,'姚瑞1','pony','1','',691689600,0,0,0,0,'',0,'17768026486','姚瑞',1,'姚瑞',4,'17768026485','',NULL,0.00,0.00,0,0,0,1500276272,18,1500278717,0,NULL,0,''),(12,0,35,'小明','','1','',0,0,0,0,0,'',0,'13006617502','张生',2,'',0,'','',NULL,0.00,0.00,0,0,0,1500345830,18,1500345830,0,NULL,0,''),(13,0,35,'李明','','1','',0,0,0,0,0,'',0,'17768026485','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501148665,18,1501148665,0,NULL,0,''),(14,0,35,'黎明','liming','1','',1499184000,0,0,0,2,'9',2,'17768026485','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501148721,18,1501148721,0,NULL,0,''),(15,0,35,'李敏','liming','1','',0,0,0,0,2,'',0,'17768026485','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501148784,18,1501148784,0,NULL,0,''),(16,0,35,'测试学员123','','1','',965232000,0,0,0,1,'1班',4,'13455216654','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501207877,18,1501207877,0,NULL,0,''),(17,0,35,'测试学院234','','1','',1304438400,0,0,0,1,'2班',1,'15466544865','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501207979,18,1501207979,0,NULL,0,''),(18,0,35,'测试学员002','','1','',1043596800,0,0,0,5,'2班',2,'15327579658','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501234752,32,1506333245,0,NULL,0,''),(19,0,35,'测试学员003','','1','',1088265600,0,0,0,3,'2班',2,'15327579658','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501234900,32,1501234900,0,NULL,0,''),(20,0,35,'张三','','1','',1248710400,2017,7,29,2,'7班',4,'15327579658','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501235181,32,1501297327,0,NULL,0,''),(21,0,35,'张三','','1','',951148800,2017,8,2,2,'7班',4,'15327579658','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501235298,32,1501669564,0,NULL,0,''),(22,0,35,'小花','','1','http://s10.xiao360.com/qms/student_upload/32/17/08/03/c899d35ceeaf2463381ccf9c9718c0e4.jpg',1311868800,2017,8,11,4,'8班',3,'15327579658','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501292365,32,1502419172,0,NULL,0,''),(23,0,35,'汤大大','','2','http://s10.xiao360.com/qms/student_upload/32/17/08/05/3ecff295fcf6fe9bddc408c381050848.jpg',1122566400,2017,8,5,4,'火箭班',2,'15327579658','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1501312090,32,1504666297,0,NULL,0,''),(26,0,35,'文文','','2','http://s10.xiao360.com/qms/student_upload/34/17/08/16/e2ac4ba45014988ab4b4748b0d0951f3.jpg',1452873600,0,0,0,2,'五班',3,'13733519525','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1502870908,34,1506333287,0,NULL,0,''),(33,0,35,'熊大','大大','1','',1336492800,0,0,0,2,'1',2,'13545554674','老爸',2,'',0,'','',NULL,0.00,0.00,0,0,0,1506158975,18,1506158975,0,NULL,0,''),(34,0,35,'袁雨缘','拍拍','2','',1252857600,2017,9,25,3,'5',4,'13006617500','袁生',2,'',0,'','',NULL,0.00,0.00,0,0,0,1506323945,18,1506324812,0,NULL,0,''),(122,0,35,'OTO学员01','','1','',0,0,0,0,2,'二班',2,'13455486654','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1510367641,18,1510367641,0,NULL,0,''),(123,0,35,'OTM学员01','','1','',0,0,0,0,0,'0',1,'13455648865','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1510367832,18,1510367832,0,NULL,0,''),(124,0,35,'OTOM学员01','','1','',0,0,0,0,2,'二班',2,'13566548845','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1510367898,18,1510367898,0,NULL,0,''),(125,0,40,'周杰伦','','1','',0,0,0,0,0,'0',0,'15454565550','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1510384400,18,1510384400,0,NULL,0,''),(126,0,35,'周杰伦','','1','',0,0,0,0,0,'0',0,'15152454560','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1510384702,18,1510384702,0,NULL,0,''),(127,0,35,'yaorui001','','0','',1276012800,0,0,0,0,'0',0,'18128874425','',1,'',0,'','','001',0.00,0.00,0,1510661023,0,1510625692,18,1510661023,0,NULL,0,''),(128,0,35,'yaorui002','','0','',1291046400,0,0,0,0,'0',0,'18128874002','',1,'',0,'','','002',0.00,0.00,0,1510661023,0,1510625763,18,1510661023,0,NULL,0,''),(129,0,35,'yaorui003','','0','',1293724800,0,0,0,0,'0',0,'18128874003','',1,'',0,'','','003',0.00,0.00,0,1510661023,0,1510625785,18,1510661023,0,NULL,0,''),(130,0,35,'林俊杰','','1','',0,0,0,0,0,'0',0,'13125132038','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1510625911,18,1510625911,0,NULL,0,''),(131,0,35,'yaorui-makeup','','0','',1293724800,0,0,0,0,'0',0,'18128874004','',1,'',0,'','','',0.00,0.00,0,150832000,0,1510642655,18,1510642655,0,NULL,0,''),(132,0,35,'yaorui-trial-student','','0','',0,0,0,0,0,'0',0,'18128874007','',0,'',0,'','','',0.00,0.00,0,0,0,1510642965,18,1510642965,0,NULL,0,''),(133,0,35,'刘伟','','1','',0,0,0,0,0,'0',0,'13124243434','',1,'',0,'','',NULL,50.00,0.00,0,0,0,1510643494,18,1510643494,0,NULL,0,''),(134,0,35,'yaorui-one-one','','0','',0,0,0,0,0,'0',0,'18128874008','',1,'',0,'','','',500.00,0.00,0,0,0,1510645987,18,1510645987,0,NULL,0,''),(135,0,35,'时辰','','1','',0,0,0,0,0,'0',0,'13234545455','',1,'',0,'','',NULL,0.73,0.00,0,0,0,1510656315,18,1510656315,0,NULL,0,''),(136,0,35,'yaorui-trial','','1','',0,0,0,0,0,'0',0,'18128874005','',0,'',0,'','',NULL,200.00,0.00,0,1510826708,0,1510662265,18,1510826708,0,NULL,0,''),(137,0,35,'考勤学员002','','1','',0,0,0,0,0,'0',0,'13455487756','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1510662280,18,1510662280,0,NULL,0,''),(138,0,35,'yaorui-customer-trial','','1','',0,0,0,0,0,'0',0,'18128874006','',0,'',0,'','',NULL,0.00,0.00,0,1510826536,0,1510662588,18,1510826537,0,NULL,0,''),(139,0,35,'考勤学员001','','1','http://s10.xiao360.com/qms/student_avatar/18/17/11/15/ebfbbf2724aaeb76ab444615a65faa38.jpeg',0,0,0,0,0,'0',0,'13422345568','',0,'',0,'','','0000042061',0.00,0.00,0,0,0,1510708288,18,1510888853,0,NULL,0,''),(140,0,35,'订单学员','','1','',0,0,0,0,1,'0',629248,'13255456654','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1510718204,18,1510718204,0,NULL,0,''),(141,0,35,'打印学员01','','1','',0,0,0,0,0,'0',0,'13244568853','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1510817291,18,1510817291,0,NULL,0,''),(142,0,35,'打印学员02','','1','',0,0,0,0,0,'0',629248,'15466584412','',1,'',0,'','',NULL,0.00,0.00,0,0,0,1510817785,18,1510817785,0,NULL,0,''),(143,0,35,'yaorui-11-16','','1','',0,0,0,0,0,'0',0,'17768026488','',0,'',0,'','','0000094341',0.00,0.00,0,1510967837,0,1510824699,18,1510967837,0,NULL,0,''),(144,0,35,'张静','','1','',0,0,0,0,0,'0',0,'13928312283','',1,'',0,'','','0008600838',0.00,0.00,0,1510970403,0,1510833478,18,1510970403,0,NULL,0,''),(145,0,35,'袁培红','','1','',0,0,0,0,3,'0',0,'13006617500','',1,'',0,'','','0008659832',0.00,0.00,0,1510967082,0,1510881473,18,1510967082,0,NULL,0,''),(146,0,35,'刘子云01','','1','',0,0,0,0,0,'0',0,'13125132038','',1,'',0,'','','0008697203',0.00,0.00,0,1510967690,0,1510920461,18,1510967690,0,NULL,0,''),(147,0,35,'刘子云02','','1','',0,0,0,0,0,'0',0,'13244455544','',1,'',0,'','','0008674251',0.00,0.00,0,1510967239,0,1510920534,18,1510967239,0,NULL,0,'');
/*!40000 ALTER TABLE `x360p_student` ENABLE KEYS */;
UNLOCK TABLES;

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
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`sa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COMMENT='缺勤记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_absence`
--

LOCK TABLES `x360p_student_absence` WRITE;
/*!40000 ALTER TABLE `x360p_student_absence` DISABLE KEYS */;
INSERT INTO `x360p_student_absence` VALUES (103,0,35,143,141,182,0,6,0,23,0,182,0,27,28,20171118,900,1000,0,'',0,1510883640,18,1510883640,0,NULL,0),(104,0,35,143,141,182,0,3,0,23,0,179,0,27,28,20171115,1200,1300,0,'',0,1510904892,18,1510904892,0,NULL,0),(105,0,35,143,141,182,0,4,0,23,0,180,0,27,28,20171116,900,1000,0,'',0,1510906415,18,1510906415,0,NULL,0);
/*!40000 ALTER TABLE `x360p_student_absence` ENABLE KEYS */;
UNLOCK TABLES;

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
  `att_way` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '考勤方式(0:后台登记,1:刷卡考勤,2:老师点名考勤)',
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
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8mb4 COMMENT='考勤记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_attendance`
--

LOCK TABLES `x360p_student_attendance` WRITE;
/*!40000 ALTER TABLE `x360p_student_attendance` DISABLE KEYS */;
INSERT INTO `x360p_student_attendance` VALUES (5,0,35,1,124,1,102,10,1,0,1,18,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508983975,0,1508983975,0,NULL,0),(8,0,35,1,124,1,102,10,1,0,1,21,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508984045,0,1508984045,0,NULL,0),(10,0,35,1,124,1,103,10,1,0,1,23,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508984778,0,1508984778,0,NULL,0),(12,0,35,1,124,1,105,55,1,0,1,25,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508984800,0,1508984800,0,NULL,0),(14,0,35,1,124,1,105,55,1,0,1,27,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508984949,0,1508984949,0,NULL,0),(16,0,35,1,124,1,102,55,1,0,1,29,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508984999,0,1508984999,0,NULL,0),(18,0,35,1,124,1,103,55,1,0,1,31,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508985081,0,1508985081,0,NULL,0),(19,0,35,1,124,1,102,55,1,0,1,32,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508985142,0,1508985142,0,NULL,0),(22,0,2,1,124,1,0,55,1,0,1,35,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508985294,0,1508985294,0,NULL,0),(24,0,2,1,124,1,0,55,1,0,1,37,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508986603,0,1508986603,0,NULL,0),(26,0,2,1,124,1,0,55,1,0,1,39,1,0,20171023,1900,2000,0,0,2,1,0,0,0,1,'','',1508989369,0,1508989369,0,NULL,0),(34,0,35,127,141,1,0,20,162,0,133,0,79,80,20171114,800,900,0,0,0,1,0,0,0,1,'','',1510632016,18,1510632016,0,NULL,0),(35,0,35,128,141,1,0,20,163,0,133,0,79,80,20171114,800,900,0,0,0,0,0,0,0,1,'','不来了',1510632016,18,1510632016,0,NULL,0),(36,0,35,129,141,1,0,20,164,0,133,0,79,80,20171114,800,900,0,0,0,0,0,1,0,1,'','病假',1510632016,18,1510632016,0,NULL,0),(48,0,35,127,141,2,0,20,162,0,134,0,79,80,20171115,800,900,0,0,0,1,0,0,0,1,'','',1510640263,18,1510640263,0,NULL,0),(49,0,35,128,141,2,0,20,163,0,134,0,79,80,20171115,800,900,0,0,0,1,0,0,0,1,'','',1510640263,18,1510640263,0,NULL,0),(50,0,35,129,141,2,0,20,164,0,134,0,79,80,20171115,800,900,0,0,0,1,0,0,0,1,'','',1510640264,18,1510640264,0,NULL,0),(51,0,35,127,141,3,0,20,162,0,135,0,79,80,20171121,800,900,0,0,0,1,0,0,0,1,'','',1510640793,18,1510640793,0,NULL,0),(52,0,35,128,141,3,0,20,163,0,135,0,79,80,20171121,800,900,0,0,0,1,0,0,0,1,'','',1510640793,18,1510640793,0,NULL,0),(53,0,35,129,141,3,0,20,164,0,135,0,79,80,20171121,800,900,0,0,0,1,0,0,0,1,'','',1510640794,18,1510640794,0,NULL,0),(54,0,35,127,141,4,0,20,162,0,136,0,79,80,20171122,800,900,0,0,0,1,0,0,0,1,'','',1510640979,18,1510640979,0,NULL,0),(55,0,35,128,141,4,0,20,163,0,136,0,79,80,20171122,800,900,0,0,0,1,0,0,0,1,'','',1510640979,18,1510640979,0,NULL,0),(56,0,35,129,141,4,0,20,164,0,136,0,79,80,20171122,800,900,0,0,0,1,0,0,0,1,'','',1510640980,18,1510640980,0,NULL,0),(57,0,35,127,141,5,0,20,162,0,137,0,79,80,20171128,800,900,0,0,0,1,0,0,0,1,'','',1510641340,18,1510641340,0,NULL,0),(58,0,35,128,141,5,0,20,163,0,137,0,79,80,20171128,800,900,0,0,0,0,0,1,0,1,'','',1510641340,18,1510641340,0,NULL,0),(59,0,35,129,141,5,0,20,164,0,137,0,79,80,20171128,800,900,0,0,0,0,0,0,0,1,'','',1510641340,18,1510641340,0,NULL,0),(60,0,35,127,141,6,0,20,162,0,138,0,79,80,20171129,800,900,0,0,0,1,0,0,0,1,'','',1510642449,18,1510642449,0,NULL,0),(61,0,35,128,141,6,0,20,163,0,138,0,79,80,20171129,800,900,0,0,0,1,0,0,0,1,'','',1510642449,18,1510642449,0,NULL,0),(62,0,35,129,141,6,0,20,164,0,138,0,79,80,20171129,800,900,0,0,0,1,0,0,0,1,'','',1510642450,18,1510642450,0,NULL,0),(63,0,35,127,141,7,0,20,162,0,139,0,81,80,20171205,800,900,0,0,0,1,0,0,0,1,'','',1510642523,18,1510642523,0,NULL,0),(64,0,35,128,141,7,0,20,163,0,139,0,81,80,20171205,800,900,0,0,0,1,0,0,0,1,'','',1510642524,18,1510642524,0,NULL,0),(65,0,35,129,141,7,0,20,164,0,139,0,81,80,20171205,800,900,0,0,0,0,0,1,0,1,'','',1510642524,18,1510642524,0,NULL,0),(66,0,35,127,141,8,0,20,162,0,140,0,79,80,20171206,800,900,0,0,0,1,0,0,0,1,'','',1510643218,18,1510643218,0,NULL,0),(67,0,35,128,141,8,0,20,163,0,140,0,79,80,20171206,800,900,0,0,0,1,0,0,0,1,'','',1510643218,18,1510643218,0,NULL,0),(68,0,35,129,141,8,0,20,164,0,140,0,79,80,20171206,800,900,0,0,0,1,0,0,0,1,'','',1510643219,18,1510643219,0,NULL,0),(69,0,35,127,141,9,0,20,162,0,141,0,79,80,20171212,800,900,0,0,0,1,0,0,0,1,'','',1510645307,18,1510645307,0,NULL,0),(70,0,35,128,141,9,0,20,163,0,141,0,79,80,20171212,800,900,0,0,0,0,0,1,0,1,'','',1510645308,18,1510645308,0,NULL,0),(71,0,35,129,141,9,0,20,164,0,141,0,79,80,20171212,800,900,0,0,0,0,0,0,0,1,'','',1510645308,18,1510645308,0,NULL,0),(72,0,35,127,141,10,0,20,162,0,142,0,79,80,20171213,800,900,0,0,0,1,0,0,0,1,'','',1510656653,18,1510656653,0,NULL,0),(73,0,35,128,141,10,0,20,163,0,142,0,79,80,20171213,800,900,0,0,0,1,0,0,0,1,'','',1510656654,18,1510656654,0,NULL,0),(74,0,35,129,141,10,0,20,164,0,142,0,79,80,20171213,800,900,0,0,0,0,0,1,0,1,'','病假',1510656654,18,1510656654,0,NULL,0),(75,0,35,127,141,12,0,20,162,0,144,0,79,80,20171220,800,900,0,0,0,1,0,0,0,1,'','',1510657070,18,1510657070,0,NULL,0),(76,0,35,128,141,12,0,20,163,0,144,0,79,80,20171220,800,900,0,0,0,0,0,1,0,1,'','',1510657070,18,1510657070,0,NULL,0),(77,0,35,129,141,12,0,20,164,0,144,0,79,80,20171220,800,900,0,0,0,0,0,0,0,1,'','',1510657070,18,1510657070,0,NULL,0),(78,0,35,127,141,11,0,20,162,0,143,0,79,80,20171219,800,900,0,0,0,1,0,0,0,1,'','病假',1510661023,18,1510661023,0,NULL,0),(79,0,35,128,141,11,0,20,163,0,143,0,79,80,20171219,800,900,0,0,0,0,0,1,0,1,'','病假',1510661023,18,1510661023,0,NULL,0),(80,0,35,129,141,11,0,20,164,0,143,0,79,80,20171219,800,900,0,0,0,0,0,0,0,1,'','翘课了',1510661023,18,1510661023,0,NULL,0),(82,0,35,138,141,1,0,22,177,0,169,0,81,0,20171122,800,900,0,0,0,1,0,0,0,1,'','',1510718279,18,1510718279,0,NULL,0),(94,0,35,136,141,5,0,22,176,0,173,0,81,0,20171129,800,900,0,0,0,1,0,0,0,1,'','',1510826410,18,1510826410,0,NULL,0),(95,0,35,138,141,5,0,22,177,0,173,0,81,0,20171129,800,900,0,0,0,1,0,0,0,1,'','',1510826410,18,1510826410,0,NULL,0),(96,0,35,136,141,1,0,22,176,0,169,0,81,0,20171122,800,900,0,0,0,1,0,0,0,1,'','',1510826470,18,1510826470,0,NULL,0),(98,0,35,138,141,2,0,22,177,0,170,0,81,0,20171122,900,1000,0,0,0,0,0,1,0,0,'','事假',1510826537,18,1510826537,0,NULL,0),(99,0,35,136,141,2,0,22,176,0,170,0,81,0,20171122,900,1000,0,0,0,1,0,0,0,1,'','',1510826708,18,1510826708,0,NULL,0),(101,0,35,143,141,1,0,23,182,0,177,0,27,28,20171113,830,930,0,0,0,1,0,0,0,0,'','',1510827609,18,1510827609,0,NULL,0),(117,0,35,143,141,2,0,23,182,0,178,0,27,28,20171114,800,900,0,0,0,1,0,0,0,1,'','',1510829801,18,1510904255,1,1510904255,18),(118,0,35,143,141,6,0,23,182,0,182,0,27,28,20171118,900,1000,0,0,0,0,0,0,0,1,'','',1510883640,18,1510904621,1,1510904621,18),(121,0,35,143,141,5,0,23,182,0,181,0,27,28,20171117,900,1000,1510892404,0,1,1,0,0,0,1,'','',1510892404,18,1510904150,1,1510904150,18),(123,0,35,143,141,7,0,23,182,0,183,0,27,28,20171119,900,1000,0,0,0,1,0,0,0,1,'','',1510902242,18,1510902246,1,1510902246,18),(124,0,35,143,141,7,0,23,182,0,183,0,27,28,20171119,900,1000,0,0,0,1,0,0,0,0,'','',1510902246,18,1510903287,1,1510903287,18),(125,0,35,143,141,3,0,23,182,0,179,0,27,28,20171115,1200,1300,0,0,0,1,0,0,0,1,'','',1510904880,18,1510904889,1,1510904889,18),(126,0,35,143,141,3,0,23,182,0,179,0,27,28,20171115,1200,1300,0,0,0,1,0,0,0,0,'','',1510904889,18,1510904892,1,1510904892,18),(127,0,35,143,141,3,0,23,182,0,179,0,27,28,20171115,1200,1300,0,0,0,0,0,0,0,0,'','',1510904892,18,1510904892,0,NULL,0),(128,0,35,143,141,4,0,23,182,0,180,0,27,28,20171116,900,1000,0,0,0,0,0,0,0,0,'','',1510906415,18,1510906415,0,NULL,0),(133,0,35,143,130,3,0,25,198,0,203,0,1,27,20171117,2015,2115,1510921399,0,1,1,0,0,0,1,'','',1510921399,18,1510921399,0,NULL,0),(134,0,35,147,130,3,0,25,197,0,203,0,1,27,20171117,2015,2115,1510921873,0,1,1,0,0,0,1,'','',1510921873,18,1510921873,0,NULL,0),(135,0,35,146,130,3,0,25,196,0,203,0,1,27,20171117,2015,2115,1510921891,0,1,1,0,0,0,1,'','',1510921891,18,1510921891,0,NULL,0),(136,0,35,145,130,3,0,25,189,0,203,0,1,27,20171117,2015,2115,1510922897,0,1,1,0,0,0,1,'','',1510922897,18,1510922897,0,NULL,0),(137,0,35,144,130,3,0,25,200,0,203,0,1,27,20171117,2015,2115,1510923053,0,1,1,0,0,0,1,'','',1510923053,18,1510923053,0,NULL,0),(138,0,35,144,130,4,0,25,200,0,204,0,1,27,20171118,900,1000,1510966819,0,1,1,1,0,0,1,'','',1510966819,18,1510966819,0,NULL,0),(139,0,35,145,130,4,0,25,189,0,204,0,1,27,20171118,900,1000,1510967082,0,1,1,0,0,0,1,'','',1510967082,18,1510967082,0,NULL,0),(140,0,35,147,130,4,0,25,197,0,204,0,1,27,20171118,900,1000,1510967239,0,1,1,0,0,0,1,'','',1510967239,18,1510967239,0,NULL,0),(141,0,35,146,130,4,0,25,196,0,204,0,1,27,20171118,900,1000,1510967690,0,1,1,0,0,0,1,'','',1510967690,18,1510967690,0,NULL,0),(142,0,35,143,141,6,0,23,182,0,182,0,27,28,20171118,900,1000,1510967837,0,1,1,0,0,0,1,'','',1510967837,18,1510967837,0,NULL,0),(143,0,35,144,129,1,105,26,203,0,209,0,4,27,20171118,1000,1030,1510970403,0,1,1,1,0,0,1,'','',1510970403,18,1510970403,0,NULL,0);
/*!40000 ALTER TABLE `x360p_student_attendance` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_student_credit_history`
--

LOCK TABLES `x360p_student_credit_history` WRITE;
/*!40000 ALTER TABLE `x360p_student_credit_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_student_credit_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_student_label_history`
--

DROP TABLE IF EXISTS `x360p_student_label_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_label_history` (
  `ssh_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `sid` int(11) unsigned NOT NULL,
  `eid` int(11) unsigned NOT NULL COMMENT '标注学生的当前员工',
  `label` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '星级标注 0:潜退、1:零期、2:领袖',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ssh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_label_history`
--

LOCK TABLES `x360p_student_label_history` WRITE;
/*!40000 ALTER TABLE `x360p_student_label_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_student_label_history` ENABLE KEYS */;
UNLOCK TABLES;

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
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`slv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COMMENT='请假记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_leave`
--

LOCK TABLES `x360p_student_leave` WRITE;
/*!40000 ALTER TABLE `x360p_student_leave` DISABLE KEYS */;
INSERT INTO `x360p_student_leave` VALUES (105,0,35,143,141,0,23,182,0,178,20171114,800,900,NULL,0,1510829108,18,1510829108,0,NULL,0);
/*!40000 ALTER TABLE `x360p_student_leave` ENABLE KEYS */;
UNLOCK TABLES;

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
  `lesson_times` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课次数',
  `origin_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '原始购买的的总课时数（lesson表：lesson_chapter * unit_hours）',
  `present_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送的课时数',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总的课时数：origin_lesson_hours + present_lesson_hours',
  `expire_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '有效期至',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级id（创建订单的时候选择了班级）',
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
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=utf8mb4 COMMENT='学生课程班级表（与order_item关联）';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_lesson`
--

LOCK TABLES `x360p_student_lesson` WRITE;
/*!40000 ALTER TABLE `x360p_student_lesson` DISABLE KEYS */;
INSERT INTO `x360p_student_lesson` VALUES (130,0,35,116,123,0,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,2,0,0,0,0,2,0.00,2.00,0,-99999,1510209624,18,1510209624,0,NULL,0),(131,0,35,116,114,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,2,0,0,0,0,0,0.00,0.00,0,-99999,1510209676,18,1510209676,0,NULL,0),(132,0,35,116,127,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,0,0.00,0.00,0,-99999,1510209676,18,1510209676,0,NULL,0),(135,0,35,9,129,0,0.00,0.00,0.00,0.00,0.00,0.00,0,11,2,1,1,0,0,0,0,0.00,0.00,0,0,1510295622,18,1510295622,0,NULL,0),(137,0,35,9,130,0,5.00,0.00,5.00,5.00,0.00,5.00,0,13,2,1,1,0,0,0,5,0.00,5.00,0,5,1510297031,18,1510297031,0,NULL,0),(139,0,35,118,133,0,20.00,0.00,20.00,20.00,0.00,20.00,0,0,0,1,0,0,0,0,20,0.00,20.00,0,20,1510308440,18,1510308440,0,NULL,0),(140,0,35,118,134,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,0,0.00,0.00,0,0,1510308440,18,1510308440,0,NULL,0),(141,0,35,118,135,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,0,0.00,0.00,0,0,1510308476,18,1510308476,0,NULL,0),(142,0,35,119,132,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1510309872,18,1510309872,0,NULL,0),(143,0,35,119,133,0,10.00,0.00,10.00,10.00,0.00,10.00,0,0,0,1,0,0,0,0,10,0.00,10.00,0,10,1510309872,18,1510309872,0,NULL,0),(144,0,35,120,133,0,10.00,0.00,10.00,10.00,0.00,10.00,0,0,0,1,0,0,0,0,10,0.00,10.00,0,10,1510310613,18,1510310613,0,NULL,0),(145,0,35,120,132,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1510310613,18,1510310613,0,NULL,0),(146,0,35,121,132,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,1,0,0,0,1,0,1.00,0.00,0,0,1510313869,18,1510313869,0,NULL,0),(147,0,35,121,130,0,4.00,0.00,4.00,4.00,0.00,4.00,0,0,0,1,0,0,0,1,4,1.00,4.00,0,4,1510313869,18,1510313869,0,NULL,0),(148,0,35,121,134,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,0,0.00,0.00,0,0,1510313869,18,1510313869,0,NULL,0),(149,0,35,122,138,1,7.00,0.00,7.00,7.00,0.00,7.00,0,0,0,1,0,0,0,0,7,0.00,7.00,0,7,1510367641,18,1510367641,0,NULL,0),(150,0,35,123,139,2,10.00,0.00,10.00,10.00,0.00,10.00,0,0,0,1,0,0,0,0,10,0.00,10.00,0,10,1510367832,18,1510367832,0,NULL,0),(151,0,35,124,138,1,7.00,0.00,7.00,7.00,0.00,7.00,0,0,0,1,0,0,0,0,7,0.00,7.00,0,7,1510367898,18,1510367898,0,NULL,0),(152,0,35,124,139,2,10.00,0.00,10.00,10.00,0.00,10.00,0,0,0,1,0,0,0,0,10,0.00,10.00,0,10,1510367898,18,1510367898,0,NULL,0),(153,0,40,125,140,0,10.00,0.00,10.00,10.00,0.00,10.00,0,0,0,1,0,0,0,0,10,0.00,10.00,0,10,1510384400,18,1510384400,0,NULL,0),(154,0,35,126,134,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,0,0.00,0.00,0,0,1510384702,18,1510384702,0,NULL,0),(155,0,35,126,132,0,31.00,0.00,31.00,31.00,0.00,31.00,0,0,0,1,0,0,0,11,20,11.00,20.00,0,20,1510386037,18,1510386037,0,NULL,0),(156,0,35,126,133,0,30.00,0.00,30.00,30.00,0.00,30.00,0,0,0,1,0,0,0,10,20,10.00,20.00,0,20,1510387141,18,1510387141,0,NULL,0),(158,0,35,130,134,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,0,0.00,0.00,0,0,1510625911,18,1510625911,0,NULL,0),(159,0,35,130,133,0,10.00,0.00,10.00,10.00,0.00,10.00,0,0,0,1,0,0,0,1,9,1.00,9.00,0,9,1510625911,18,1510625911,0,NULL,0),(160,0,35,130,136,0,10.00,0.00,10.00,10.00,0.00,10.00,0,0,0,1,0,0,0,0,10,0.00,10.00,0,10,1510625982,18,1510625982,0,NULL,0),(161,0,35,130,138,1,7.00,0.00,7.00,7.00,0.00,7.00,0,0,0,1,0,0,0,0,7,0.00,7.00,0,7,1510625982,18,1510625982,0,NULL,0),(162,0,35,127,141,0,28.00,0.00,28.00,28.00,0.00,28.00,0,20,2,1,1,0,0,12,16,12.00,16.00,1510661023,28,1510627468,18,1510661023,0,NULL,0),(163,0,35,128,141,0,14.00,0.00,14.00,14.00,0.00,14.00,0,20,2,1,1,0,0,7,7,7.00,7.00,1510656653,14,1510627468,18,1510656654,0,NULL,0),(164,0,35,129,141,0,14.00,0.00,14.00,14.00,0.00,14.00,0,20,2,1,1,0,0,5,9,5.00,9.00,0,14,1510627468,18,1510627468,0,NULL,0),(165,0,35,130,135,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,0,0.00,0.00,0,0,1510627553,18,1510627553,0,NULL,0),(166,0,35,130,129,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,1,0,1.00,0.00,0,0,1510627920,18,1510627920,0,NULL,0),(167,0,35,131,141,0,14.00,0.00,14.00,14.00,0.00,14.00,0,0,0,1,0,0,0,0,14,0.00,14.00,0,14,1510642720,18,1510642720,0,NULL,0),(168,0,35,133,132,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,1,0,1.00,0.00,0,0,1510643494,18,1510643494,0,NULL,0),(169,0,35,134,142,1,7.00,0.00,7.00,14.00,0.00,14.00,0,0,0,1,0,0,0,1,6,2.00,12.00,0,6,1510646158,18,1510646158,0,NULL,0),(170,0,35,135,133,0,20.00,0.00,20.00,20.00,0.00,20.00,0,0,0,1,0,0,0,15,5,15.00,5.00,0,5,1510656315,18,1510656315,0,NULL,0),(171,0,35,135,134,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,0,0.00,0.00,0,0,1510656315,18,1510656315,0,NULL,0),(172,0,35,138,130,0,5.00,0.00,5.00,5.00,0.00,5.00,0,0,0,1,0,0,0,0,5,0.00,5.00,0,5,1510662607,18,1510662607,0,NULL,0),(173,0,35,138,132,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1510662652,18,1510662652,0,NULL,0),(174,0,35,135,132,0,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,0,2,0.00,2.00,0,2,1510664097,18,1510664097,0,NULL,0),(175,0,35,140,130,0,5.00,0.00,5.00,5.00,0.00,5.00,0,0,0,1,0,0,0,0,5,0.00,5.00,0,5,1510718204,18,1510718204,0,NULL,0),(176,0,35,136,141,0,28.00,0.00,28.00,28.00,0.00,28.00,0,22,2,1,1,0,0,4,24,4.00,24.00,1510826708,27,1510718229,18,1510826708,0,NULL,0),(177,0,35,138,141,0,14.00,0.00,14.00,14.00,0.00,14.00,0,22,2,1,1,0,0,1,13,1.00,13.00,1510826410,14,1510718229,18,1510826708,0,NULL,0),(178,0,35,140,134,0,0.00,0.00,0.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,0,0.00,0.00,0,0,1510718241,18,1510718241,0,NULL,0),(179,0,35,140,137,0,5.00,0.00,5.00,10.00,0.00,10.00,0,0,0,1,0,0,0,0,5,0.00,10.00,0,5,1510741506,18,1510741506,0,NULL,0),(180,0,35,141,132,0,1.00,0.00,1.00,1.00,0.00,1.00,1510761600,24,2,1,0,0,0,0,1,0.00,1.00,0,1,1510817291,18,1510919574,0,NULL,0),(181,0,35,142,130,0,5.00,0.00,5.00,5.00,0.00,5.00,0,0,0,1,0,0,0,0,5,0.00,5.00,0,5,1510817785,18,1510817785,0,NULL,0),(182,0,35,143,141,0,14.00,0.00,14.00,14.00,0.00,14.00,0,23,2,1,0,0,0,2,12,2.00,12.00,1510967837,14,1510824722,18,1510967837,0,NULL,0),(183,0,35,144,133,0,20.00,1.00,21.00,20.00,1.00,21.00,0,0,0,1,0,0,0,0,21,0.00,21.00,0,21,1510833478,18,1510833478,0,NULL,0),(184,0,35,144,134,0,12.00,0.00,12.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,12,0.00,0.00,0,12,1510833478,18,1510833478,0,NULL,0),(185,0,35,141,133,0,10.00,0.00,10.00,10.00,0.00,10.00,0,0,0,1,0,0,0,4,6,4.00,6.00,0,6,1510833660,18,1510833660,0,NULL,0),(186,0,35,144,132,0,11.00,0.00,11.00,11.00,0.00,11.00,0,24,2,1,0,0,0,5,6,5.00,6.00,0,6,1510834115,18,1510919574,0,NULL,0),(187,0,35,145,133,0,20.00,0.00,20.00,20.00,0.00,20.00,0,0,0,1,0,0,0,10,10,10.00,10.00,0,10,1510881473,18,1510881473,0,NULL,0),(188,0,35,145,132,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,1,0,1.00,0.00,0,0,1510881473,18,1510881473,0,NULL,0),(189,0,35,145,130,0,20.00,0.00,20.00,20.00,0.00,20.00,0,25,2,1,0,0,0,2,18,2.00,18.00,1510967082,20,1510902961,18,1510967082,0,NULL,0),(190,0,35,145,129,0,102.00,0.00,102.00,102.00,0.00,102.00,0,26,2,1,0,0,0,0,102,0.00,102.00,0,102,1510903035,18,1510923964,0,NULL,0),(191,0,35,145,134,0,2.00,0.00,2.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,2,0.00,0.00,0,2,1510903134,18,1510903134,0,NULL,0),(192,0,35,145,138,1,14.00,0.00,14.00,14.00,0.00,14.00,0,0,0,1,0,0,0,0,14,0.00,14.00,0,14,1510903134,18,1510903134,0,NULL,0),(193,0,35,145,135,0,20.00,0.00,20.00,0.00,0.00,0.00,0,0,0,1,0,0,0,0,20,0.00,0.00,0,20,1510903475,18,1510903475,0,NULL,0),(194,0,35,145,137,0,5.00,0.00,5.00,10.00,0.00,10.00,0,0,0,1,0,0,0,0,5,0.00,10.00,0,5,1510904724,18,1510904724,0,NULL,0),(195,0,35,145,139,2,10.00,0.00,10.00,10.00,0.00,10.00,0,0,0,1,0,0,0,0,10,0.00,10.00,0,10,1510904843,18,1510904843,0,NULL,0),(196,0,35,146,130,0,10.00,0.00,10.00,10.00,0.00,10.00,0,25,2,1,0,0,0,2,8,2.00,8.00,1510967690,10,1510920461,18,1510967690,0,NULL,0),(197,0,35,147,130,0,10.00,0.00,10.00,10.00,0.00,10.00,0,25,2,1,0,0,0,2,8,2.00,8.00,1510967239,10,1510920534,18,1510967239,0,NULL,0),(198,0,35,143,130,0,10.00,0.00,10.00,10.00,0.00,10.00,0,25,2,1,0,0,0,1,9,1.00,9.00,1510921399,10,1510920667,18,1510921399,0,NULL,0),(199,0,35,143,129,0,101.00,0.00,101.00,101.00,0.00,101.00,0,26,2,1,0,0,0,0,101,0.00,101.00,0,101,1510920667,18,1510923964,0,NULL,0),(200,0,35,144,130,0,8.00,0.00,8.00,8.00,0.00,8.00,0,25,2,1,1,0,0,2,6,2.00,6.00,1510966819,8,1510922750,18,1510966819,0,NULL,0),(201,0,35,147,129,0,110.00,0.00,110.00,110.00,0.00,110.00,0,26,2,1,1,0,0,0,110,0.00,110.00,0,110,1510923964,18,1510923964,0,NULL,0),(202,0,35,146,129,0,110.00,0.00,110.00,110.00,0.00,110.00,0,26,2,1,1,0,0,0,110,0.00,110.00,0,110,1510923964,18,1510923964,0,NULL,0),(203,0,35,144,129,0,110.00,0.00,110.00,110.00,0.00,110.00,0,26,2,1,1,0,0,1,109,1.00,109.00,1510970403,110,1510923964,18,1510970403,0,NULL,0),(204,0,35,139,129,0,110.00,0.00,110.00,110.00,0.00,110.00,0,26,2,1,1,0,0,0,110,0.00,110.00,0,110,1510923964,18,1510923964,0,NULL,0);
/*!40000 ALTER TABLE `x360p_student_lesson` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COMMENT='学生课时消耗记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_lesson_hour`
--

LOCK TABLES `x360p_student_lesson_hour` WRITE;
/*!40000 ALTER TABLE `x360p_student_lesson_hour` DISABLE KEYS */;
INSERT INTO `x360p_student_lesson_hour` VALUES (104,0,35,127,141,0,20,0,0,133,34,103,20171114,800,900,79,80,1.00,0,200.00,1,0,1510632016,18,1510632016,0,NULL,0),(112,0,35,127,141,0,20,0,0,134,48,121,20171115,800,900,79,80,1.00,0,200.00,1,0,1510640263,18,1510640263,0,NULL,0),(113,0,35,128,141,0,20,0,0,134,49,121,20171115,800,900,79,80,1.00,0,200.00,1,0,1510640264,18,1510640264,0,NULL,0),(114,0,35,129,141,0,20,0,0,134,50,121,20171115,800,900,79,80,1.00,0,200.00,1,0,1510640264,18,1510640264,0,NULL,0),(115,0,35,127,141,0,20,0,0,135,51,122,20171121,800,900,79,80,1.00,0,200.00,1,0,1510640793,18,1510640793,0,NULL,0),(116,0,35,128,141,0,20,0,0,135,52,122,20171121,800,900,79,80,1.00,0,200.00,1,0,1510640793,18,1510640793,0,NULL,0),(117,0,35,129,141,0,20,0,0,135,53,122,20171121,800,900,79,80,1.00,0,200.00,1,0,1510640794,18,1510640794,0,NULL,0),(118,0,35,127,141,0,20,0,0,136,54,123,20171122,800,900,79,80,1.00,0,200.00,1,0,1510640979,18,1510640979,0,NULL,0),(119,0,35,128,141,0,20,0,0,136,55,123,20171122,800,900,79,80,1.00,0,200.00,1,0,1510640979,18,1510640979,0,NULL,0),(120,0,35,129,141,0,20,0,0,136,56,123,20171122,800,900,79,80,1.00,0,200.00,1,0,1510640980,18,1510640980,0,NULL,0),(121,0,35,127,141,0,20,0,0,137,57,124,20171128,800,900,79,80,1.00,0,200.00,1,0,1510641340,18,1510641340,0,NULL,0),(122,0,35,127,141,0,20,0,0,138,60,125,20171129,800,900,79,80,1.00,0,200.00,1,0,1510642449,18,1510642449,0,NULL,0),(123,0,35,128,141,0,20,0,0,138,61,125,20171129,800,900,79,80,1.00,0,200.00,1,0,1510642450,18,1510642450,0,NULL,0),(124,0,35,129,141,0,20,0,0,138,62,125,20171129,800,900,79,80,1.00,0,200.00,1,0,1510642450,18,1510642450,0,NULL,0),(125,0,35,127,141,0,20,0,0,139,63,126,20171205,800,900,81,80,1.00,0,200.00,1,0,1510642524,18,1510642524,0,NULL,0),(126,0,35,128,141,0,20,0,0,139,64,126,20171205,800,900,81,80,1.00,0,200.00,1,0,1510642524,18,1510642524,0,NULL,0),(127,0,35,127,141,0,20,0,0,140,66,127,20171206,800,900,79,80,1.00,0,200.00,1,0,1510643218,18,1510643218,0,NULL,0),(128,0,35,128,141,0,20,0,0,140,67,127,20171206,800,900,79,80,1.00,0,200.00,1,0,1510643219,18,1510643219,0,NULL,0),(129,0,35,129,141,0,20,0,0,140,68,127,20171206,800,900,79,80,1.00,0,200.00,1,0,1510643219,18,1510643219,0,NULL,0),(130,0,35,127,141,0,20,0,0,141,69,128,20171212,800,900,79,80,1.00,0,200.00,1,0,1510645308,18,1510645308,0,NULL,0),(131,0,35,127,141,0,20,0,0,142,72,129,20171213,800,900,79,80,1.00,0,200.00,1,0,1510656653,18,1510656653,0,NULL,0),(132,0,35,128,141,0,20,0,0,142,73,129,20171213,800,900,79,80,1.00,0,200.00,1,0,1510656654,18,1510656654,0,NULL,0),(133,0,35,127,141,0,20,0,0,144,75,130,20171220,800,900,79,80,1.00,0,200.00,1,0,1510657070,18,1510657070,0,NULL,0),(134,0,35,127,141,0,20,0,0,143,78,131,20171219,800,900,79,80,1.00,0,200.00,1,0,1510661023,18,1510661023,0,NULL,0),(137,0,35,136,141,0,22,0,0,173,94,141,20171129,800,900,81,0,1.00,0,200.00,1,0,1510826410,18,1510826410,0,NULL,0),(138,0,35,138,141,0,22,0,0,173,95,141,20171129,800,900,81,0,1.00,0,200.00,1,0,1510826410,18,1510826410,0,NULL,0),(139,0,35,136,141,0,22,0,0,169,96,133,20171122,800,900,81,0,1.00,0,200.00,1,0,1510826470,18,1510826470,0,NULL,0),(141,0,35,136,141,0,22,0,0,170,99,142,20171122,900,1000,81,0,1.00,0,200.00,1,0,1510826708,18,1510826708,0,NULL,0),(149,0,35,143,141,0,23,0,0,182,118,145,20171118,900,1000,27,28,1.00,0,200.00,1,0,1510883640,18,1510883640,0,NULL,0),(153,0,35,143,130,0,25,0,0,203,133,157,20171117,2015,2115,1,27,1.00,0,120.00,1,0,1510921399,18,1510921399,0,NULL,0),(154,0,35,147,130,0,25,0,0,203,134,157,20171117,2015,2115,1,27,1.00,0,120.00,1,0,1510921873,18,1510921873,0,NULL,0),(155,0,35,146,130,0,25,0,0,203,135,157,20171117,2015,2115,1,27,1.00,0,120.00,1,0,1510921891,18,1510921891,0,NULL,0),(156,0,35,145,130,0,25,0,0,203,136,157,20171117,2015,2115,1,27,1.00,0,120.00,1,0,1510922897,18,1510922897,0,NULL,0),(157,0,35,144,130,0,25,0,0,203,137,157,20171117,2015,2115,1,27,1.00,0,120.00,1,0,1510923053,18,1510923053,0,NULL,0),(158,0,35,144,130,0,25,0,0,204,138,158,20171118,900,1000,1,27,1.00,0,120.00,1,0,1510966819,18,1510966819,0,NULL,0),(159,0,35,145,130,0,25,0,0,204,139,158,20171118,900,1000,1,27,1.00,0,120.00,1,0,1510967082,18,1510967082,0,NULL,0),(160,0,35,147,130,0,25,0,0,204,140,158,20171118,900,1000,1,27,1.00,0,120.00,1,0,1510967239,18,1510967239,0,NULL,0),(161,0,35,146,130,0,25,0,0,204,141,158,20171118,900,1000,1,27,1.00,0,120.00,1,0,1510967690,18,1510967690,0,NULL,0),(162,0,35,143,141,0,23,0,0,182,142,145,20171118,900,1000,27,28,1.00,0,200.00,1,0,1510967838,18,1510967838,0,NULL,0),(163,0,35,144,129,105,26,0,0,209,143,159,20171118,1000,1030,4,27,1.00,0,120.00,1,0,1510970403,18,1510970403,0,NULL,0);
/*!40000 ALTER TABLE `x360p_student_lesson_hour` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COMMENT='电子钱包余额变动记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_money_history`
--

LOCK TABLES `x360p_student_money_history` WRITE;
/*!40000 ALTER TABLE `x360p_student_money_history` DISABLE KEYS */;
INSERT INTO `x360p_student_money_history` VALUES (1,0,3,64,35,94,2000.00,0.00,2000.00,'',1509777912,18,1509777912,0,NULL,0),(2,0,3,65,35,95,300.00,0.00,300.00,'',1509778120,18,1509778120,0,NULL,0),(3,0,3,66,35,92,100.00,0.00,100.00,'',1509789419,18,1509789419,0,NULL,0),(4,0,3,78,35,102,1900.50,0.00,1900.50,'',1509953629,18,1509953629,0,NULL,0),(5,0,4,79,35,102,10.00,1900.50,1890.50,'',1509954324,18,1509954324,0,NULL,0),(6,0,4,80,35,102,100.00,1890.50,1790.50,'',1509954654,18,1509954654,0,NULL,0),(7,0,1,3,35,121,77.10,0.00,77.10,'',1510313925,18,1510313925,0,NULL,0),(8,0,1,4,35,121,2.25,77.10,79.35,'',1510313993,18,1510313993,0,NULL,0),(9,0,1,5,35,126,190.00,0.00,190.00,'',1510386458,18,1510386458,0,NULL,0),(10,0,1,6,35,126,100.00,190.00,290.00,'',1510387007,18,1510387007,0,NULL,0),(11,0,1,8,35,126,190.00,290.00,480.00,'',1510391888,18,1510391888,0,NULL,0),(12,0,4,132,35,126,480.00,480.00,0.00,'',1510391938,18,1510391938,0,NULL,0),(13,0,3,140,35,130,1.00,0.00,1.00,'',1510627921,18,1510627921,0,NULL,0),(14,0,2,1,35,130,150.00,1.00,151.00,'',1510636208,18,1510636208,0,NULL,0),(15,0,3,142,35,133,50.00,0.00,50.00,'',1510643494,18,1510643494,0,NULL,0),(16,0,2,5,35,130,151.00,151.00,0.00,'',1510655847,18,1510655847,0,NULL,0),(17,0,1,9,35,134,500.00,0.00,500.00,'',1510656282,18,1510656282,0,NULL,0),(18,0,1,10,35,135,350.00,0.00,350.00,'',1510656336,18,1510656336,0,NULL,0),(19,0,1,11,35,135,150.00,350.00,500.00,'',1510661889,18,1510661889,0,NULL,0),(20,0,1,12,35,135,290.00,500.00,790.00,'',1510661944,18,1510661944,0,NULL,0),(21,0,1,13,35,135,140.00,790.00,930.00,'',1510661989,18,1510661989,0,NULL,0),(22,0,1,14,35,135,150.00,930.00,1080.00,'',1510662346,18,1510662346,0,NULL,0),(23,0,4,147,35,135,1080.00,1080.00,0.00,'',1510664097,18,1510664097,0,NULL,0),(24,0,1,15,35,135,12.73,0.00,12.73,'',1510664306,18,1510664306,0,NULL,0),(25,0,1,16,35,135,134.00,12.73,146.73,'',1510664872,18,1510664872,0,NULL,0),(26,0,1,17,35,135,150.00,146.73,296.73,'',1510664910,18,1510664910,0,NULL,0),(27,0,2,6,35,135,296.00,296.73,0.73,'',1510664943,18,1510664943,0,NULL,0),(28,0,3,148,35,135,50.00,0.73,50.73,'',1510707762,18,1510707762,0,NULL,0),(29,0,2,10,35,135,50.00,50.73,0.73,'',1510707786,18,1510707786,0,NULL,0),(30,0,1,18,35,145,1650.00,0.00,1650.00,'',1510881553,18,1510881553,0,NULL,0),(31,0,5,91,35,145,100.00,1650.00,1550.00,'',1510903450,18,1510903450,0,NULL,0),(32,0,4,176,35,145,1550.00,1550.00,0.00,'',1510904724,18,1510904724,0,NULL,0),(33,0,1,19,35,136,200.00,0.00,200.00,'',1510917212,18,1510917212,0,NULL,0);
/*!40000 ALTER TABLE `x360p_student_money_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_student_suspend`
--

DROP TABLE IF EXISTS `x360p_student_suspend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_student_suspend` (
  `ss_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'student_suspend表主键',
  `gid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
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
-- Dumping data for table `x360p_student_suspend`
--

LOCK TABLES `x360p_student_suspend` WRITE;
/*!40000 ALTER TABLE `x360p_student_suspend` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_student_suspend` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COMMENT='科目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_subject`
--

LOCK TABLES `x360p_subject` WRITE;
/*!40000 ALTER TABLE `x360p_subject` DISABLE KEYS */;
INSERT INTO `x360p_subject` VALUES (101,0,'本草纲目','神农尝遍百草~',1508386695,18,1509693564,0,0,NULL),(102,0,'户外课堂','户外学习游玩',1508386776,18,1508386776,0,0,NULL),(103,0,'大学语文','毛毛爱听故事',1508393100,18,1508393100,0,0,NULL),(104,0,'数学竞赛','斗地主全国挑战赛',1508393197,18,1508394596,1,18,1508394596),(105,0,'天外飞仙','观看神州20号起飞',1508394654,18,1508394654,0,0,NULL),(106,0,'合同法很反感','凤凰股份',1508928454,18,1508928454,0,0,NULL);
/*!40000 ALTER TABLE `x360p_subject` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=211 DEFAULT CHARSET=utf8 COMMENT='刷卡记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_swiping_card_record`
--

LOCK TABLES `x360p_swiping_card_record` WRITE;
/*!40000 ALTER TABLE `x360p_swiping_card_record` DISABLE KEYS */;
INSERT INTO `x360p_swiping_card_record` VALUES (101,0,35,139,'0000042061',20171117,1123,0,1510888984,18,1510888984,0,NULL,0),(102,0,35,139,'0000042061',20171117,1125,0,1510889152,18,1510889152,0,NULL,0),(103,0,35,139,'0000042061',20171117,1126,0,1510889169,18,1510889169,0,NULL,0),(104,0,35,139,'0000042061',20171117,1126,0,1510889196,18,1510889196,0,NULL,0),(105,0,35,139,'0000042061',20171117,1134,0,1510889682,18,1510889682,0,NULL,0),(106,0,35,139,'0000042061',20171117,1136,0,1510889768,18,1510889768,0,NULL,0),(107,0,35,139,'0000042061',20171117,1136,0,1510889775,18,1510889775,0,NULL,0),(108,0,35,139,'0000042061',20171117,1146,0,1510890370,18,1510890370,0,NULL,0),(109,0,35,143,'0008600838',20171117,1146,0,1510890419,18,1510890419,0,NULL,0),(110,0,35,143,'0000094341',20171117,1149,0,1510890584,18,1510890584,0,NULL,0),(111,0,35,143,'0000094341',20171117,1159,0,1510891195,18,1510891195,0,NULL,0),(112,0,35,143,'0000094341',20171117,1200,0,1510891249,18,1510891249,0,NULL,0),(113,0,35,143,'0000094341',20171117,1200,0,1510891249,18,1510891249,0,NULL,0),(114,0,35,143,'0000094341',20171117,1204,0,1510891444,18,1510891444,0,NULL,0),(115,0,35,143,'0000094341',20171117,1204,0,1510891481,18,1510891481,0,NULL,0),(116,0,35,143,'0000094341',20171117,1206,0,1510891567,18,1510891567,0,NULL,0),(117,0,35,143,'0000094341',20171117,1206,0,1510891601,18,1510891601,0,NULL,0),(118,0,35,143,'0000094341',20171117,1208,0,1510891699,18,1510891699,0,NULL,0),(119,0,35,143,'0000094341',20171117,1213,0,1510892021,18,1510892021,0,NULL,0),(120,0,35,143,'0000094341',20171117,1214,0,1510892088,18,1510892088,0,NULL,0),(121,0,35,143,'0000094341',20171117,1216,0,1510892173,18,1510892173,0,NULL,0),(122,0,35,143,'0000094341',20171117,1216,0,1510892217,18,1510892217,0,NULL,0),(123,0,35,143,'0000094341',20171117,1220,1,1510892404,18,1510892404,0,NULL,0),(124,0,35,143,'0000094341',20171117,1220,0,1510892420,18,1510892420,0,NULL,0),(125,0,35,139,'0000042061',20171117,1949,0,1510919351,18,1510919351,0,NULL,0),(126,0,35,139,'0000042061',20171117,1954,0,1510919659,18,1510919659,0,NULL,0),(127,0,35,139,'0000042061',20171117,1955,0,1510919735,18,1510919735,0,NULL,0),(128,0,35,139,'0000042061',20171117,1958,0,1510919906,18,1510919906,0,NULL,0),(129,0,35,143,'0000094341',20171117,1959,0,1510919978,18,1510919978,0,NULL,0),(130,0,35,143,'0000094341',20171117,2001,0,1510920062,18,1510920062,0,NULL,0),(131,0,35,143,'0000094341',20171117,2001,0,1510920101,18,1510920101,0,NULL,0),(132,0,35,143,'0000094341',20171117,2003,0,1510920213,18,1510920213,0,NULL,0),(133,0,35,139,'0000042061',20171117,2004,0,1510920262,18,1510920262,0,NULL,0),(134,0,35,143,'0000094341',20171117,2004,0,1510920289,18,1510920289,0,NULL,0),(135,0,35,147,'0008674251',20171117,2009,0,1510920554,18,1510920554,0,NULL,0),(136,0,35,147,'0008674251',20171117,2009,0,1510920556,18,1510920556,0,NULL,0),(137,0,35,143,'0000094341',20171117,2016,0,1510920977,18,1510920977,0,NULL,0),(138,0,35,143,'0000094341',20171117,2018,0,1510921128,18,1510921128,0,NULL,0),(139,0,35,143,'0000094341',20171117,2020,0,1510921253,18,1510921253,0,NULL,0),(140,0,35,143,'0000094341',20171117,2022,0,1510921337,18,1510921337,0,NULL,0),(141,0,35,143,'0000094341',20171117,2023,1,1510921399,18,1510921399,0,NULL,0),(142,0,35,143,'0000094341',20171117,2030,0,1510921833,18,1510921833,0,NULL,0),(143,0,35,147,'0008674251',20171117,2031,1,1510921872,18,1510921873,0,NULL,0),(144,0,35,147,'0008674251',20171117,2031,0,1510921873,18,1510921873,0,NULL,0),(145,0,35,146,'0008697203',20171117,2031,1,1510921891,18,1510921891,0,NULL,0),(146,0,35,145,'0008659832',20171117,2048,1,1510922897,18,1510922897,0,NULL,0),(147,0,35,144,'0008600838',20171117,2050,1,1510923053,18,1510923053,0,NULL,0),(148,0,35,144,'0008600838',20171117,2052,0,1510923123,18,1510923123,0,NULL,0),(149,0,35,144,'0008600838',20171117,2052,0,1510923141,18,1510923141,0,NULL,0),(150,0,35,144,'0008600838',20171117,2053,0,1510923212,18,1510923212,0,NULL,0),(151,0,35,147,'0008674251',20171117,2108,0,1510924089,18,1510924089,0,NULL,0),(152,0,35,144,'0008600838',20171117,2108,0,1510924097,18,1510924097,0,NULL,0),(153,0,35,143,'0000094341',20171117,2108,0,1510924118,18,1510924118,0,NULL,0),(154,0,35,144,'0008600838',20171117,2108,0,1510924138,18,1510924138,0,NULL,0),(155,0,35,145,'0008659832',20171117,2109,0,1510924189,18,1510924189,0,NULL,0),(156,0,35,144,'0008600838',20171117,2110,0,1510924213,18,1510924213,0,NULL,0),(157,0,35,144,'0008600838',20171117,2110,0,1510924217,18,1510924217,0,NULL,0),(158,0,35,144,'0008600838',20171117,2110,0,1510924222,18,1510924222,0,NULL,0),(159,0,35,144,'0008600838',20171118,851,0,1510966264,18,1510966264,0,NULL,0),(160,0,35,144,'0008600838',20171118,851,0,1510966272,18,1510966272,0,NULL,0),(161,0,35,144,'0008600838',20171118,851,0,1510966279,18,1510966279,0,NULL,0),(162,0,35,144,'0008600838',20171118,851,0,1510966295,18,1510966295,0,NULL,0),(163,0,35,144,'0008600838',20171118,851,0,1510966302,18,1510966302,0,NULL,0),(164,0,35,144,'0008600838',20171118,851,0,1510966310,18,1510966310,0,NULL,0),(165,0,35,144,'0008600838',20171118,851,0,1510966317,18,1510966317,0,NULL,0),(166,0,35,144,'0008600838',20171118,852,0,1510966324,18,1510966324,0,NULL,0),(167,0,35,144,'0008600838',20171118,852,0,1510966329,18,1510966329,0,NULL,0),(168,0,35,144,'0008600838',20171118,853,0,1510966386,18,1510966386,0,NULL,0),(169,0,35,139,'0000042061',20171118,853,0,1510966404,18,1510966404,0,NULL,0),(170,0,35,144,'0008600838',20171118,853,0,1510966437,18,1510966437,0,NULL,0),(171,0,35,144,'0008600838',20171118,855,0,1510966543,18,1510966543,0,NULL,0),(172,0,35,144,'0008600838',20171118,855,0,1510966553,18,1510966553,0,NULL,0),(173,0,35,144,'0008600838',20171118,856,0,1510966570,18,1510966570,0,NULL,0),(174,0,35,144,'0008600838',20171118,856,0,1510966585,18,1510966585,0,NULL,0),(175,0,35,144,'0008600838',20171118,856,0,1510966596,18,1510966596,0,NULL,0),(176,0,35,144,'0008600838',20171118,856,0,1510966603,18,1510966603,0,NULL,0),(177,0,35,144,'0008600838',20171118,856,0,1510966617,18,1510966617,0,NULL,0),(178,0,35,144,'0008600838',20171118,857,0,1510966633,18,1510966633,0,NULL,0),(179,0,35,144,'0008600838',20171118,857,0,1510966645,18,1510966645,0,NULL,0),(180,0,35,144,'0008600838',20171118,857,0,1510966651,18,1510966651,0,NULL,0),(181,0,35,144,'0008600838',20171118,858,0,1510966694,18,1510966694,0,NULL,0),(182,0,35,144,'0008600838',20171118,858,0,1510966706,18,1510966706,0,NULL,0),(183,0,35,144,'0008600838',20171118,858,0,1510966732,18,1510966732,0,NULL,0),(184,0,35,144,'0008600838',20171118,859,0,1510966741,18,1510966741,0,NULL,0),(185,0,35,144,'0008600838',20171118,859,0,1510966746,18,1510966746,0,NULL,0),(186,0,35,144,'0008600838',20171118,859,0,1510966755,18,1510966755,0,NULL,0),(187,0,35,144,'0008600838',20171118,859,0,1510966765,18,1510966765,0,NULL,0),(188,0,35,144,'0008600838',20171118,859,0,1510966776,18,1510966776,0,NULL,0),(189,0,35,144,'0008600838',20171118,859,0,1510966786,18,1510966786,0,NULL,0),(190,0,35,144,'0008600838',20171118,900,1,1510966819,18,1510966819,0,NULL,0),(191,0,35,139,'0000042061',20171118,904,0,1510967075,18,1510967075,0,NULL,0),(192,0,35,145,'0008659832',20171118,904,1,1510967082,18,1510967082,0,NULL,0),(193,0,35,145,'0008659832',20171118,907,0,1510967221,18,1510967221,0,NULL,0),(194,0,35,147,'0008674251',20171118,907,1,1510967239,18,1510967239,0,NULL,0),(195,0,35,145,'0008659832',20171118,914,0,1510967684,18,1510967684,0,NULL,0),(196,0,35,147,'0008674251',20171118,914,0,1510967687,18,1510967687,0,NULL,0),(197,0,35,146,'0008697203',20171118,914,1,1510967690,18,1510967690,0,NULL,0),(198,0,35,146,'0008697203',20171118,917,0,1510967831,18,1510967831,0,NULL,0),(199,0,35,143,'0000094341',20171118,917,1,1510967837,18,1510967838,0,NULL,0),(200,0,35,144,'0008600838',20171118,934,0,1510968840,18,1510968840,0,NULL,0),(201,0,35,139,'0000042061',20171118,934,0,1510968846,18,1510968846,0,NULL,0),(202,0,35,144,'0008600838',20171118,944,0,1510969458,18,1510969458,0,NULL,0),(203,0,35,144,'0008600838',20171118,945,0,1510969518,18,1510969518,0,NULL,0),(204,0,35,144,'0008600838',20171118,946,0,1510969598,18,1510969598,0,NULL,0),(205,0,35,144,'0008600838',20171118,948,0,1510969693,18,1510969693,0,NULL,0),(206,0,35,144,'0008600838',20171118,950,0,1510969818,18,1510969818,0,NULL,0),(207,0,35,144,'0008600838',20171118,950,0,1510969836,18,1510969836,0,NULL,0),(208,0,35,144,'0008600838',20171118,952,0,1510969945,18,1510969945,0,NULL,0),(209,0,35,144,'0008600838',20171118,959,0,1510970370,18,1510970370,0,NULL,0),(210,0,35,144,'0008600838',20171118,1000,1,1510970403,18,1510970403,0,NULL,0);
/*!40000 ALTER TABLE `x360p_swiping_card_record` ENABLE KEYS */;
UNLOCK TABLES;

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
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:收入，2支出',
  `relate_id` int(11) NOT NULL DEFAULT '0' COMMENT '相关业务id,如票据id',
  `aa_id` int(11) NOT NULL DEFAULT '0' COMMENT '帐户id',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COMMENT='帐户流水表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_tally`
--

LOCK TABLES `x360p_tally` WRITE;
/*!40000 ALTER TABLE `x360p_tally` DISABLE KEYS */;
INSERT INTO `x360p_tally` VALUES (1,0,35,1,128,7,1900,'',1510833479,18,1510833479,0,NULL,0),(2,0,35,1,129,7,1500,'',1510833660,18,1510833660,0,NULL,0),(3,0,35,1,130,7,150,'',1510834115,18,1510834115,0,NULL,0),(4,0,35,1,131,7,1650,'',1510881473,18,1510881473,0,NULL,0),(5,0,35,2,11,7,150,'',1510887569,18,1510887569,0,NULL,0),(6,0,35,2,12,7,150,'',1510900096,18,1510900096,0,NULL,0),(7,0,35,2,13,7,150,'',1510900122,18,1510900122,0,NULL,0),(8,0,35,2,14,7,150,'',1510900213,18,1510900213,0,NULL,0),(9,0,35,1,132,7,600,'',1510903001,18,1510903001,0,NULL,0),(10,0,35,1,133,7,1500,'',1510903134,18,1510903134,0,NULL,0),(11,0,35,1,134,7,1500,'',1510903475,18,1510903475,0,NULL,0),(12,0,35,1,135,7,2000,'',1510904724,18,1510904724,0,NULL,0),(13,0,35,1,136,7,620,'',1510904843,18,1510904843,0,NULL,0),(14,0,35,1,137,7,74,'',1510905629,18,1510905629,0,NULL,0),(15,0,35,1,138,7,2500,'',1510910219,18,1510910219,0,NULL,0),(16,0,35,2,15,7,0,'',1510911180,18,1510911180,0,NULL,0),(17,0,35,1,139,7,1200,'',1510920461,18,1510920461,0,NULL,0),(18,0,35,1,140,7,1200,'',1510920534,18,1510920534,0,NULL,0),(19,0,35,1,141,7,1320,'',1510920668,18,1510920668,0,NULL,0),(20,0,35,1,142,7,12000,'',1510968606,18,1510968606,0,NULL,0),(21,0,35,1,143,7,12000,'',1510968621,18,1510968621,0,NULL,0),(22,0,35,1,144,7,12000,'',1510968639,18,1510968639,0,NULL,0),(23,0,35,1,145,7,12000,'',1510968654,18,1510968654,0,NULL,0),(24,0,35,1,146,7,12000,'',1510968672,18,1510968672,0,NULL,0),(25,0,35,1,147,7,12000,'',1510968688,18,1510968688,0,NULL,0);
/*!40000 ALTER TABLE `x360p_tally` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COMMENT='时间段表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_time_section`
--

LOCK TABLES `x360p_time_section` WRITE;
/*!40000 ALTER TABLE `x360p_time_section` DISABLE KEYS */;
INSERT INTO `x360p_time_section` VALUES (96,0,0,'Q',1,1,800,1000,'',1502964032,18,1502964032,0,NULL,0),(97,0,0,'Q',1,3,1400,1600,'',1502964045,18,1502964426,0,NULL,0),(98,0,0,'Q',1,4,1800,2000,'',1502964057,18,1502964426,0,NULL,0),(101,0,0,'Q',1,2,1000,1200,'',1502964426,18,1502964426,0,NULL,0),(102,0,0,'S',1,1,800,1000,'',1502965405,18,1502965405,0,NULL,0),(104,0,0,'Q',2,1,800,1000,'',1503041416,18,1503041416,0,NULL,0),(105,0,0,'Q',3,1,800,1000,'',1503041416,18,1503041416,0,NULL,0),(106,0,0,'Q',4,1,800,1000,'',1503041416,18,1503041416,0,NULL,0),(107,0,0,'Q',5,1,800,1000,'',1503041416,18,1503041416,0,NULL,0),(108,0,0,'Q',2,3,1400,1600,'',1503041425,18,1503041425,0,NULL,0),(109,0,0,'Q',3,3,1400,1600,'',1503041425,18,1503041425,0,NULL,0),(110,0,0,'Q',4,2,1400,1600,'',1503041425,18,1503041425,0,NULL,0),(111,0,0,'Q',5,2,1400,1600,'',1503041425,18,1503041425,0,NULL,0),(112,0,0,'Q',2,4,1800,2000,'',1503041433,18,1503041433,0,NULL,0),(113,0,0,'Q',3,4,1800,2000,'',1503041433,18,1503041433,0,NULL,0),(114,0,0,'Q',4,4,1800,2000,'',1503041433,18,1503041433,0,NULL,0),(115,0,0,'Q',5,3,1800,2000,'',1503041433,18,1503041433,0,NULL,0),(116,0,0,'Q',6,1,900,1000,'',1503041448,18,1503041448,0,NULL,0),(117,0,0,'Q',6,2,1100,1200,'',1503041459,18,1503041459,0,NULL,0),(118,0,0,'Q',7,1,900,1000,'',1503041465,18,1503041465,0,NULL,0),(119,0,0,'Q',7,2,1100,1200,'',1503041469,18,1503041469,0,NULL,0),(120,0,0,'H',1,1,800,1000,'',1504930271,18,1504930271,0,NULL,0),(121,0,0,'H',1,2,1030,1230,'',1504930296,18,1504930296,0,NULL,0),(122,0,0,'H',1,3,1400,1600,'',1504930312,18,1504930312,0,NULL,0),(123,0,0,'H',1,4,1630,1830,'',1504930321,18,1504930321,0,NULL,0),(124,0,0,'H',2,1,800,1000,'',1504930333,18,1504930333,0,NULL,0),(125,0,0,'H',3,1,800,1000,'',1504930333,18,1504930333,0,NULL,0),(126,0,0,'H',4,1,800,1000,'',1504930333,18,1504930333,0,NULL,0),(127,0,0,'H',5,1,800,1000,'',1504930333,18,1504930333,0,NULL,0),(128,0,0,'H',2,2,1030,1230,'',1504930339,18,1504930339,0,NULL,0),(129,0,0,'H',3,2,1030,1230,'',1504930339,18,1504930339,0,NULL,0),(130,0,0,'H',4,2,1030,1230,'',1504930339,18,1504930339,0,NULL,0),(131,0,0,'H',5,2,1030,1230,'',1504930339,18,1504930339,0,NULL,0),(132,0,0,'H',2,3,1400,1600,'',1504930344,18,1504930344,0,NULL,0),(133,0,0,'H',3,3,1400,1600,'',1504930344,18,1504930344,0,NULL,0),(134,0,0,'H',4,3,1400,1600,'',1504930344,18,1504930344,0,NULL,0),(135,0,0,'H',5,3,1400,1600,'',1504930344,18,1504930344,0,NULL,0),(136,0,0,'H',2,4,1630,1830,'',1504930349,18,1504930349,0,NULL,0),(137,0,0,'H',3,4,1630,1830,'',1504930349,18,1504930349,0,NULL,0),(138,0,0,'H',4,4,1630,1830,'',1504930349,18,1504930349,0,NULL,0),(139,0,0,'H',5,4,1630,1830,'',1504930349,18,1504930349,0,NULL,0),(140,0,0,'C',1,1,800,1000,'',1505467813,18,1505467813,0,NULL,0),(141,0,0,'Q',2,5,2100,2200,'',1505525223,18,1505525223,0,NULL,0),(142,0,1,'Q',1,1,800,1000,'',1505533622,18,1505533622,0,NULL,0),(143,0,1,'Q',1,2,1030,1230,'',1505533640,18,1505533640,0,NULL,0),(145,0,1,'Q',1,3,1400,1630,'',1505533675,18,1505533680,0,NULL,0),(146,0,1,'Q',1,4,1700,1900,'',1505533696,18,1505533696,0,NULL,0),(147,0,1,'Q',6,1,800,1000,'',1505533727,18,1505533727,0,NULL,0),(148,0,1,'Q',7,1,800,1000,'',1505533727,18,1505533727,0,NULL,0),(149,0,1,'Q',2,1,800,1000,'',1505533727,18,1505533727,0,NULL,0),(150,0,1,'Q',3,1,800,1000,'',1505533727,18,1505533727,0,NULL,0),(151,0,1,'Q',4,1,800,1000,'',1505533727,18,1505533727,0,NULL,0),(152,0,1,'Q',5,1,800,1000,'',1505533727,18,1505533727,0,NULL,0),(153,0,1,'Q',2,2,1030,1230,'',1505533732,18,1505533732,0,NULL,0),(154,0,1,'Q',3,2,1030,1230,'',1505533732,18,1505533732,0,NULL,0),(155,0,1,'Q',4,2,1030,1230,'',1505533732,18,1505533732,0,NULL,0),(156,0,1,'Q',5,2,1030,1230,'',1505533732,18,1505533732,0,NULL,0),(157,0,1,'Q',6,2,1030,1230,'',1505533732,18,1505533732,0,NULL,0),(158,0,1,'Q',7,2,1030,1230,'',1505533732,18,1505533732,0,NULL,0),(159,0,1,'Q',2,3,1400,1630,'',1505533738,18,1505533738,0,NULL,0),(160,0,1,'Q',3,3,1400,1630,'',1505533738,18,1505533738,0,NULL,0),(161,0,1,'Q',4,3,1400,1630,'',1505533738,18,1505533738,0,NULL,0),(162,0,1,'Q',5,3,1400,1630,'',1505533738,18,1505533738,0,NULL,0),(163,0,1,'Q',2,4,1700,1900,'',1505533741,18,1505533741,0,NULL,0),(164,0,1,'Q',3,4,1700,1900,'',1505533741,18,1505533741,0,NULL,0),(165,0,1,'Q',4,4,1700,1900,'',1505533741,18,1505533741,0,NULL,0),(166,0,1,'Q',5,4,1700,1900,'',1505533741,18,1505533741,0,NULL,0),(167,0,35,'Q',1,2,1730,1930,'',1509093925,18,1510192744,0,NULL,0),(168,0,35,'Q',2,1,1730,1930,'',1509094147,18,1509094147,0,NULL,0),(169,0,35,'Q',3,3,1900,1945,'',1509102096,18,1510301902,0,NULL,0),(170,0,35,'Q',4,1,1900,1945,'',1509103279,18,1509103279,0,NULL,0),(171,0,35,'Q',5,3,1900,1945,'',1509155716,18,1510301875,0,NULL,0),(172,0,35,'Q',6,1,900,1000,'',1509155732,18,1509155732,0,NULL,0),(173,0,35,'Q',6,2,1000,1100,'',1509155740,18,1509155740,0,NULL,0),(174,0,35,'Q',6,3,1100,1200,'',1509155749,18,1509155749,0,NULL,0),(175,0,35,'Q',6,4,1300,1400,'',1509155758,18,1509155758,0,NULL,0),(176,0,35,'Q',6,5,1400,1500,'',1509155770,18,1509155770,0,NULL,0),(177,0,35,'Q',6,6,1600,1700,'',1509155780,18,1509155780,0,NULL,0),(178,0,35,'Q',7,1,900,1000,'',1509155797,18,1509155797,0,NULL,0),(179,0,35,'Q',7,2,1000,1100,'',1509155806,18,1509155806,0,NULL,0),(180,0,35,'Q',7,3,1100,1200,'',1509155814,18,1509155814,0,NULL,0),(181,0,35,'Q',7,4,1300,1400,'',1509155823,18,1509155823,0,NULL,0),(182,0,35,'Q',7,5,1400,1500,'',1509155831,18,1509155831,0,NULL,0),(183,0,35,'Q',7,6,1600,1700,'',1509155840,18,1509155840,0,NULL,0),(191,0,35,'Q',3,1,330,430,'03:30~04:30',1509518232,18,1509518232,0,NULL,0),(192,0,35,'Q',3,4,1915,2015,'19:15~20:15',1509532004,18,1510301902,0,NULL,0),(194,0,0,'H',6,1,1000,1200,'',1509931427,18,1509931427,0,NULL,0),(195,0,0,'H',7,1,1100,1200,'',1509931446,18,1509931446,0,NULL,0),(196,0,35,'H',1,1,830,930,'08:30~09:30',1510019934,18,1510019934,0,NULL,0),(197,0,35,'Q',1,1,800,1000,'08:00~10:00',1510192744,18,1510192744,0,NULL,0),(198,0,35,'Q',4,2,2000,2100,'晚2',1510231332,18,1510231332,0,NULL,0),(199,0,35,'Q',5,1,800,1000,'08:00~10:00',1510301555,18,1510301555,0,NULL,0),(200,0,35,'Q',5,2,1100,1200,'11:00~12:00',1510301875,18,1510301875,0,NULL,0),(201,0,35,'Q',3,2,800,845,'08:00~08:45',1510301902,18,1510301902,0,NULL,0),(202,0,35,'H',2,1,800,900,'08:00~09:00',1510626795,18,1510626795,0,NULL,0),(203,0,35,'H',3,1,800,900,'08:00~09:00',1510626806,18,1510626806,0,NULL,0),(204,0,35,'H',3,2,900,1000,'09:00~10:00',1510718074,18,1510718074,0,NULL,0),(205,0,35,'H',3,3,1100,1200,'11:00~12:00',1510718092,18,1510718092,0,NULL,0),(206,0,35,'H',3,4,1200,1300,'12:00~13:00',1510718109,18,1510718109,0,NULL,0),(207,0,35,'H',4,1,900,1000,'09:00~10:00',1510822516,18,1510822516,0,NULL,0),(208,0,35,'H',5,2,900,1000,'09:00~10:00',1510822524,18,1510919869,0,NULL,0),(209,0,35,'H',6,1,900,1000,'09:00~10:00',1510822534,18,1510822534,0,NULL,0),(210,0,35,'H',7,1,900,1000,'09:00~10:00',1510822543,18,1510822543,0,NULL,0),(211,0,35,'H',5,1,830,930,'08:30~09:30',1510919869,18,1510919869,0,NULL,0),(212,0,35,'H',5,3,2015,2115,'20:15~21:15',1510919901,18,1510919901,0,NULL,0),(213,0,35,'Q',5,4,2130,2230,'21:30~22:30',1510923903,18,1510923903,0,NULL,0),(214,0,35,'H',6,2,1000,1030,'10:00~10:30',1510968034,18,1510968034,0,NULL,0),(215,0,35,'H',6,3,1030,1100,'10:30~11:00',1510968047,18,1510968047,0,NULL,0),(216,0,35,'H',6,4,1100,1130,'11:00~11:30',1510968059,18,1510968059,0,NULL,0),(217,0,35,'H',6,5,1130,1200,'11:30~12:00',1510968094,18,1510968094,0,NULL,0),(218,0,35,'H',6,6,1200,1230,'12:00~12:30',1510968107,18,1510968107,0,NULL,0);
/*!40000 ALTER TABLE `x360p_time_section` ENABLE KEYS */;
UNLOCK TABLES;

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
  `is_attendance` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否到堂',
  `is_transfered` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否转化为正式学员',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`tla_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COMMENT='试听安排记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_trial_listen_arrange`
--

LOCK TABLES `x360p_trial_listen_arrange` WRITE;
/*!40000 ALTER TABLE `x360p_trial_listen_arrange` DISABLE KEYS */;
INSERT INTO `x360p_trial_listen_arrange` VALUES (1,0,35,0,0,7,0,123,4,4,81,20171109,1900,1945,0,0,1509520060,18,1509536801,1,1509536801,18),(2,0,35,0,0,6,0,123,4,3,81,20171108,1900,1945,0,0,1509522310,18,1509537484,1,1509537484,18),(3,0,35,1,0,0,4,123,4,1,81,20171106,1730,1930,0,0,1509524218,18,1509524218,0,NULL,0),(4,0,35,1,0,0,7,123,4,1,81,20171106,1730,1930,0,0,1509524218,18,1509524218,0,NULL,0),(5,0,35,1,0,0,9,123,4,5,81,20171110,1900,1945,0,0,1509525501,18,1509525501,0,NULL,0),(6,0,35,1,0,0,10,123,4,5,81,20171110,1900,1945,0,0,1509525501,18,1509525501,0,NULL,0),(7,0,35,0,0,2,0,123,4,5,81,20171110,1900,1945,0,0,1509525501,18,1509525501,0,NULL,0),(8,0,35,0,0,6,0,123,4,5,81,20171110,1900,1945,0,0,1509525501,18,1509525501,0,NULL,0),(9,0,35,1,1,0,14,113,0,17,80,20171101,800,845,0,0,1509530700,18,1509530700,0,NULL,0),(10,0,35,1,0,0,7,123,4,5,81,20171110,1900,1945,0,0,1510277346,18,1510277346,0,NULL,0),(11,0,35,1,0,0,12,123,4,5,81,20171110,1900,1945,0,0,1510277346,18,1510277346,0,NULL,0),(12,0,35,1,0,0,7,130,13,18,81,20171110,800,1000,0,0,1510297608,18,1510297608,0,NULL,0),(13,0,35,1,0,0,12,130,13,18,81,20171110,800,1000,1,0,1510297608,18,1510625781,0,NULL,0),(14,0,35,0,0,33,0,130,13,18,81,20171110,800,1000,1,0,1510297757,18,1510625782,0,NULL,0),(15,0,35,0,0,34,0,130,13,18,81,20171110,800,1000,0,0,1510297757,18,1510297757,0,NULL,0),(16,0,35,1,0,0,132,141,20,140,79,20171206,800,900,1,0,1510643082,18,1510643220,0,NULL,0),(17,0,35,1,0,0,131,141,20,140,79,20171206,800,900,1,0,1510643082,18,1510643220,0,NULL,0),(18,0,35,0,0,36,0,141,20,140,79,20171206,800,900,1,0,1510643144,18,1510643220,0,NULL,0);
/*!40000 ALTER TABLE `x360p_trial_listen_arrange` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COMMENT='用户表(机构用户和学生用户2类)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_user`
--

LOCK TABLES `x360p_user` WRITE;
/*!40000 ALTER TABLE `x360p_user` DISABLE KEYS */;
INSERT INTO `x360p_user` VALUES (18,0,'admin','15327579658','877470170@qq.com','我是管理员','1',1,'123456','5f1d7a84db00d2fce00b31a7fc73224f','http://s10.xiao360.com/qms/avatar/18/17/11/04/1e5aa583869f2e8865cbf3591a57b3af.png','',0,1,1,0,1510969498,'192.168.3.14',690,0,1,1,1509761616,0,0,NULL,0,1505209347,'1c8a81376eb86f482fad1662766e6d5d'),(20,0,'liuzy','17768026488','888@qq.com','刘子云','1',1,'npjdaD','e93acb86461cd0e81303577e99324187','http://s10.xiao360.com/qms/avatar/20/17/09/12/4785a4a4f2ed4b057f970bd518a0cdd9.jpg','',0,0,0,0,1505184118,'192.168.3.171',1,1500004075,1,0,1505182754,18,0,NULL,0,NULL,NULL),(21,0,'liuzy002','13466548865','liuziy@qq.com','lzy','1',1,'h2EIzO','70eef8f90647506ee7807a5fc8f82565','','',0,0,0,0,0,'',0,1500004740,1,0,1508559932,18,1,1508559932,18,NULL,NULL),(22,0,'yuang002','13544658846','employee002@qq.com','员工002','1',1,'22','a4eddb215f978fb5b6771b7cbefb13b9','','',0,0,0,0,0,'',0,1500009703,1,0,1500021254,18,1,1500021254,18,NULL,NULL),(23,0,'yuang002','13544658846','employee002@qq.com','员工002','1',2,'7ssJmm','a4eddb215f978fb5b6771b7cbefb13b9','','',0,0,0,0,0,'',0,1500009703,1,0,1500021254,18,1,NULL,18,NULL,NULL),(24,0,'liuzy003','13544654112','qwe@qq.com','刘子云3号','1',1,'cjxPyP','a6a774593388b07595d5cfb80df59ede','','',0,0,0,0,1500028874,'192.168.3.47',1,1500028848,1,0,1500516882,18,0,NULL,0,NULL,NULL),(25,0,'yaorui','17768026485','6666@qq.com','姚瑞','1',1,'VjrFUA','77fa188dfb18f8504f0f9b0395d49a7c','','',0,0,0,0,1500285407,'192.168.3.185',1,1500285333,1,0,1500290038,18,1,1500290038,18,NULL,NULL),(26,0,'yaorui2','17768026475','7777@qq.com','姚瑞2','1',1,'dpnI6e','03c7601190c1ce424e0926db5f1577ce','','',0,0,0,0,0,'',0,1500285333,1,0,1500289441,18,1,1500289441,18,NULL,NULL),(27,0,'17768026487','17768026485','yaorui@qq.com','yaorui','1',1,'45DFhc','b94eb80e4fa88068888cac964f2bbc81','','',0,0,0,0,0,'',0,1500427724,1,0,1504603570,18,1,1504603570,18,NULL,NULL),(28,0,'17768026489','17768026489','562247587@qq.com','姚瑞','1',1,'TxuptI','546a6f25187817cd31b061bb1ff25507','','',0,0,0,0,0,'',0,1500435315,1,0,1504603792,18,1,1504603792,18,NULL,NULL),(31,0,'13006617500','13006617500','','','0',2,'1kkbDF','e11b12996b993ec7eece316f4aa074bd','','',0,0,0,0,1502442710,'192.168.3.209',16,1500695794,1,0,1500695794,0,0,NULL,0,NULL,NULL),(32,0,'15327579658','15327579658','','刘子云','0',2,'38SY9S','381cde60693a922d37b9948fcab9f5c4','','',23,1,0,0,1505287226,'192.168.3.171',171,1500954837,1,0,1505209945,0,0,NULL,18,1505209945,'25a8ab8585e854641e580ef2cbd3318b'),(34,0,'13733519525','13733519525','','','0',2,'8sZKVT','f1d1f0c238f7a0857604382f406bd562','','',26,0,0,0,1502870878,'192.168.3.209',5,1502867692,1,0,1502867692,0,0,NULL,0,NULL,NULL),(36,0,'yaotest','15555555555','55dfdf5@qq.com','yaotest','1',1,'UVBqia','ce41ac211d4cf7e695942ddc2717644f','','',0,0,0,0,0,'',0,1504247396,1,0,1504603796,18,1,1504603796,18,NULL,NULL),(37,0,'13006617501','13006617501','','董振峰','1',1,'d9HQ60','e331dee51b08cef2d5f81e4d888c25c9','','',0,0,0,0,1504755936,'192.168.3.143',1,1504755918,1,0,1504755918,18,0,NULL,0,NULL,NULL),(38,0,'liudd','15334086673','877470170@qq.com','刘大大','1',1,'kEKg6P','f18159c6463ecce4fa57463634712b58','','',0,0,0,0,0,'',0,1504842197,1,0,1504842197,18,0,NULL,0,NULL,NULL),(39,0,'17768026486','17768026486','jack@alibaba.com','马云','1',1,'P3VFnq','c1ce8b52129af4a4b37c8e8ce3b18c30','','',0,0,0,0,1505189445,'192.168.3.225',2,1505183656,1,0,1505183656,18,0,NULL,0,NULL,NULL),(41,0,'17768026485','17768026485','','','0',2,'Ufmy1c','d15aa8b7c7f5d891a73f389bccd96f3a','','',0,1,0,0,1505265741,'192.168.3.171',1,1505265741,1,0,1505266036,0,0,NULL,0,1505266036,'76f13d1364715af529ce35e8354cf1e2'),(42,0,'xiaohong','','','小虹','1',1,'dPpzn5','0c1de4fb112e8f4ede246209c1cca6ba','','',0,0,0,0,1507287346,'127.0.0.1',1,1507287270,1,0,1507346159,0,1,1507346159,0,NULL,NULL),(43,0,'zhangyu','','','张羽','1',1,'UwoQkQ','96162c8a501d3e05b7ccd9544c0756c5','','',0,0,0,0,0,'',0,1507347006,0,0,1507348921,0,0,NULL,0,NULL,NULL),(44,0,'fdsf','13665555469','dfs@ss.cpm','测试老师','1',1,'4cIosC','450ed7d3462dc3ce89f0bd17b3d65948','','',0,0,0,0,0,'',0,1508562878,1,0,1508566123,18,1,1508566123,18,NULL,NULL),(45,0,'admin@base','13988888888','fdsfs@base.com','三明','1',1,'NEFNe2','44a09bb6807f8c5a7349164616a154d2','','',0,0,0,0,0,'',0,1508723385,0,0,1508928474,18,0,NULL,0,NULL,NULL),(46,0,'dsfdsf','13544446666','4565@base.com','大明','1',1,'E9VoEy','29244cbd46ecaec29a7583b7b2c35deb','','',0,0,0,0,0,'',0,1508725462,1,0,1510040151,18,0,NULL,0,NULL,NULL),(47,0,'gdfsgd','13555454666','dsfjsa@base.com','小明3','1',1,'Qh4qkV','e0e32dd63102e72e82c2c26d5fbf8998','','',0,0,0,0,0,'',0,1508725493,1,0,1510040142,18,0,NULL,0,NULL,NULL);
/*!40000 ALTER TABLE `x360p_user` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_user_student`
--

LOCK TABLES `x360p_user_student` WRITE;
/*!40000 ALTER TABLE `x360p_user_student` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_user_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_wechat_bind`
--

DROP TABLE IF EXISTS `x360p_wechat_bind`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wechat_bind` (
  `wb_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `original_id` varchar(255) NOT NULL DEFAULT '' COMMENT '用户绑定的公众号的原始id',
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `bid` int(11) unsigned NOT NULL COMMENT '校区id',
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
-- Dumping data for table `x360p_wechat_bind`
--

LOCK TABLES `x360p_wechat_bind` WRITE;
/*!40000 ALTER TABLE `x360p_wechat_bind` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wechat_bind` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_wechat_user`
--

DROP TABLE IF EXISTS `x360p_wechat_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wechat_user` (
  `wu_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
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
-- Dumping data for table `x360p_wechat_user`
--

LOCK TABLES `x360p_wechat_user` WRITE;
/*!40000 ALTER TABLE `x360p_wechat_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wechat_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_wxmp`
--

DROP TABLE IF EXISTS `x360p_wxmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp` (
  `wxmp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区id',
  `name` varchar(255) DEFAULT NULL COMMENT '公众号名称',
  `account` varchar(255) DEFAULT NULL COMMENT '公众号账号----微信号',
  `original_id` varchar(255) DEFAULT NULL COMMENT '原始ID',
  `app_id` varchar(255) DEFAULT NULL COMMENT 'appid',
  `secret` varchar(255) DEFAULT NULL COMMENT 'appsecret',
  `token` varchar(255) DEFAULT '' COMMENT '令牌',
  `aes_key` varchar(255) DEFAULT NULL COMMENT '消息加解密密钥,EncodingAESKey，安全模式下请一定要填写！！！',
  `avatar` varchar(255) DEFAULT NULL COMMENT '公众号头像',
  `qrcode_url` varchar(255) DEFAULT NULL COMMENT '公众号二维码',
  `merchant_id` varchar(50) DEFAULT '' COMMENT '支付设置商户id',
  `key` varchar(255) DEFAULT NULL COMMENT '支付密钥',
  `cert_path` varchar(255) DEFAULT NULL COMMENT '支付证书路径',
  `key_path` varchar(255) DEFAULT NULL COMMENT '证书私钥路径',
  `enable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启微信支付',
  `default_message` int(11) unsigned DEFAULT NULL COMMENT '默认消息的回复规则',
  `welcome_message` int(11) unsigned DEFAULT NULL COMMENT '关注事件的回复规则wxmp_rule',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `template_message_config` text,
  PRIMARY KEY (`wxmp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信公众号';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_wxmp`
--

LOCK TABLES `x360p_wxmp` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_wxmp_material`
--

DROP TABLE IF EXISTS `x360p_wxmp_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_material` (
  `material_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wxmp_id` int(11) unsigned NOT NULL,
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
-- Dumping data for table `x360p_wxmp_material`
--

LOCK TABLES `x360p_wxmp_material` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_material` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_material` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_wxmp_material_news`
--

LOCK TABLES `x360p_wxmp_material_news` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_material_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_material_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x360p_wxmp_menu`
--

DROP TABLE IF EXISTS `x360p_wxmp_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x360p_wxmp_menu` (
  `wm_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单组id',
  `wxmp_id` int(11) unsigned NOT NULL COMMENT 'qms_wxmp表的主键',
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
-- Dumping data for table `x360p_wxmp_menu`
--

LOCK TABLES `x360p_wxmp_menu` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_menu` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_wxmp_reply_image`
--

LOCK TABLES `x360p_wxmp_reply_image` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_reply_image` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_reply_image` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_wxmp_reply_news`
--

LOCK TABLES `x360p_wxmp_reply_news` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_reply_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_reply_news` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_wxmp_reply_text`
--

LOCK TABLES `x360p_wxmp_reply_text` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_reply_text` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_reply_text` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_wxmp_reply_video`
--

LOCK TABLES `x360p_wxmp_reply_video` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_reply_video` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_reply_video` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_wxmp_reply_voice`
--

LOCK TABLES `x360p_wxmp_reply_voice` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_reply_voice` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_reply_voice` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_wxmp_rule`
--

LOCK TABLES `x360p_wxmp_rule` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_rule` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_wxmp_rule_keyword`
--

LOCK TABLES `x360p_wxmp_rule_keyword` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_rule_keyword` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_rule_keyword` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-18 10:03:22
