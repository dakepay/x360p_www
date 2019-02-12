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
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COMMENT='会计账户表(每创建一个校区要自动创建一个关联的账户表)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_accounting_account`
--

LOCK TABLES `x360p_accounting_account` WRITE;
/*!40000 ALTER TABLE `x360p_accounting_account` DISABLE KEYS */;
INSERT INTO `x360p_accounting_account` VALUES (1,0,'前台收费【现金】',0,'',1,1,0,1.00,490.00,'现金【人民币】',0,NULL,0,1511489346,NULL,NULL,0),(2,0,'现金',0,'2',0,1,0,0.00,1221978.29,'',1,1510971257,1,NULL,NULL,NULL,0),(3,0,'现金',0,'3',0,1,0,0.00,0.00,'',1,1510971281,1,NULL,NULL,NULL,0),(4,0,'现金',0,'4',0,1,0,0.00,2900.00,'',1,1511165914,1,NULL,NULL,NULL,0),(5,0,'支付宝【内置】',2,'',1,1,0,0.00,5000.00,'支付宝呢',0,1511336924,1,NULL,NULL,NULL,0),(6,0,'阳光喔年服务费',3,'3,4,5',0,0,5,10000.00,-1000.00,'阳光喔年服务费',0,1511338933,1,1511338933,NULL,NULL,0),(7,0,'现金',0,'5',0,1,0,0.00,0.00,'',1,1511351217,1,1511351217,NULL,NULL,0),(8,0,'现金',0,'6',0,1,0,0.00,0.00,'',1,1511352851,1,1511352851,NULL,NULL,0),(9,0,'公司税务',4,'',1,0,0,10000.00,10000.00,'公用税务',0,1511432875,1,1511525415,NULL,NULL,0),(23,0,'现金',0,'20',0,1,0,0.00,0.00,'',1,1511581035,1,1511581035,NULL,NULL,0),(24,0,'现金',0,'21',0,1,0,0.00,0.00,'',1,1511581047,1,1511581047,NULL,NULL,0),(25,0,'现金',0,'22',0,1,0,0.00,0.00,'',1,1511581067,1,1511581067,NULL,NULL,0),(26,0,'现金',0,'23',0,1,0,0.00,0.00,'',1,1511581132,1,1511581132,NULL,NULL,0),(27,0,'现金',0,'24',0,1,0,0.00,0.00,'',1,1511581259,1,1511581259,NULL,NULL,0),(28,0,'现金',0,'25',0,1,0,0.00,0.00,'',1,1511581551,1,1511581551,NULL,NULL,0),(29,0,'现金',0,'26',0,1,0,0.00,0.00,'',1,1511581575,1,1511581575,NULL,NULL,0),(30,0,'现金',0,'27',0,1,0,0.00,0.00,'',1,1511581600,1,1511581600,NULL,NULL,0),(31,0,'现金',0,'28',0,1,0,0.00,0.00,'',1,1511581621,1,1511581621,NULL,NULL,0),(32,0,'现金',0,'29',0,1,0,0.00,0.00,'',1,1511581896,1,1511581896,NULL,NULL,0),(33,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511593181,1,1511593363,NULL,NULL,0),(34,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511593374,1,1511593427,NULL,NULL,0),(35,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511593882,1,1511593978,NULL,NULL,0),(36,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511594032,1,1511594528,NULL,NULL,0),(37,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511594550,1,1511594567,NULL,NULL,0),(38,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511594669,1,1511594707,NULL,NULL,0),(39,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511594669,1,1511594691,NULL,NULL,0),(40,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511594922,1,1511594951,NULL,NULL,0),(41,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511594961,1,1511595040,NULL,NULL,0),(42,0,'现金',0,'39',0,1,0,0.00,0.00,'',1,1511595055,1,1511595055,NULL,NULL,0),(43,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511601063,1,1511601068,NULL,NULL,0),(44,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1511601111,1,1511601118,NULL,NULL,0),(45,0,'微信【前台收费】',2,'',1,1,0,100.00,100.00,'',0,1511748585,1,1511748585,NULL,NULL,0),(46,0,'现金',0,'',0,1,0,0.00,0.00,'',1,1512030887,1,1512031018,NULL,NULL,0),(47,0,'现金',0,'43',0,1,0,0.00,0.00,'',1,1512042870,1,1512042870,NULL,NULL,0),(48,0,'现金',0,'44',0,1,0,0.00,0.00,'',1,1512042881,1,1512042881,NULL,NULL,0),(49,0,'现金',0,'45',0,1,0,0.00,0.00,'',1,1512042889,1,1512042889,NULL,NULL,0),(50,0,'现金',0,'46',0,1,0,0.00,0.00,'',1,1512042897,1,1512042897,NULL,NULL,0),(51,0,'现金',0,'47',0,1,0,0.00,0.00,'',1,1512042908,1,1512042908,NULL,NULL,0),(52,15,'现金',0,'48',0,1,0,0.00,0.00,'',1,1513657356,1,1513657356,NULL,NULL,0),(53,16,'现金',0,'49',0,1,0,0.00,0.00,'',1,1513657811,1,1513665391,1513665391,1,1);
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='系统操作日志表(记录系统操作日志)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_action_log`
--

LOCK TABLES `x360p_action_log` WRITE;
/*!40000 ALTER TABLE `x360p_action_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_action_log` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COMMENT='待办事项';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_backlog`
--

LOCK TABLES `x360p_backlog` WRITE;
/*!40000 ALTER TABLE `x360p_backlog` DISABLE KEYS */;
INSERT INTO `x360p_backlog` VALUES (21,0,'打雷了',20171205,911,3,1512436482,1,1512467109,1,1,1512467109),(22,0,'收衣服了',20171206,932,1,1512437754,1,1512437754,0,0,NULL),(23,0,'下雨了',20171205,1421,2,1512454991,1,1512467106,1,1,1512467106),(24,0,'出席达沃斯论坛',20171208,1440,2,1512456186,1,1512700120,0,0,NULL),(25,0,'收衣服了',20171205,1454,3,1512456983,1,1512467103,1,1,1512467103),(26,0,'生孩子了',20171205,1454,3,1512456995,1,1512467098,1,1,1512467098),(27,0,'回家了',20171205,1457,3,1512457075,1,1512458708,1,1,1512458708),(28,0,'翻车了哇哇',20171205,1544,2,1512459711,1,1512475032,0,0,NULL),(29,0,'吃鸡吃鸡',20171205,1625,2,1512462434,1,1512475021,0,0,NULL),(30,0,'今天LOL',20171206,1637,3,1512463211,1,1512531467,0,0,NULL),(31,0,'咕咕咕咕',20171205,1844,3,1512467084,1,1512468312,0,0,NULL),(32,0,'提醒我给小明打电话',20171206,944,2,1512474214,1,1512531464,0,0,NULL),(33,0,'提醒我给小明联系',20171206,1144,3,1512474256,1,1512522522,0,0,NULL),(34,0,'阴天',20171207,920,1,1512609556,1,1512609804,1,1,1512609804),(35,0,'吃鸡吃鸡',20171207,2122,1,1512638679,1,1512638679,0,0,NULL),(36,0,'今天天气真好',20171208,917,2,1512695813,1,1512724635,0,0,NULL),(37,0,'12.12来了',20171209,1345,2,1512791268,1,1512791363,0,0,NULL),(38,0,'吃鸡吃鸡',20171209,1502,1,1512792288,1,1512792288,0,0,NULL);
/*!40000 ALTER TABLE `x360p_backlog` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COMMENT='校区表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_branch`
--

LOCK TABLES `x360p_branch` WRITE;
/*!40000 ALTER TABLE `x360p_branch` DISABLE KEYS */;
INSERT INTO `x360p_branch` VALUES (1,0,'默认校区','默认','2','0755-82602960',0,19,291,3062,3062,'学校地址',0,0,'',1498124456,0,0,0,0,0,NULL),(2,0,'笋岗校区','笋岗校区','1','',0,0,0,0,0,'',39,0,'',1510971257,0,1511593306,0,0,NULL,NULL),(3,0,'德兴校区(>_<)','德兴校区','1','15487458645',0,19,291,3062,3062,'德兴花园',0,0,'',1510971281,0,1511754264,0,0,NULL,NULL),(4,0,'百汇老年校区','百汇校区','1','',0,0,0,0,0,'',0,0,'',1511165914,0,1511592640,0,0,NULL,NULL),(5,0,'罗湖校区','罗湖校区','1','13548456684',0,19,291,3062,3062,'笋岗东路百汇大厦南座18G',2,0,'',1511351217,0,1511581635,0,0,1511581635,NULL),(6,0,'龙岗校区啊','龙岗校区','1','15327579658',0,19,291,3063,3063,'龙岗五和地铁站',0,0,'',1511352851,0,1511581637,0,0,1511581637,NULL),(20,0,'坂田校区','坂田校区','1','',0,0,0,0,0,'',11,0,'',1511581035,0,1511581035,0,0,NULL,NULL),(21,0,'宝安校区','宝安校区','1','',0,0,0,0,0,'',12,0,'',1511581047,0,1511581047,0,0,NULL,NULL),(22,0,'福田校区','福田校区','1','',0,0,0,0,0,'',13,0,'',1511581067,0,1511581067,0,0,NULL,NULL),(23,0,'罗湖2区','罗湖2区','1','',0,0,0,0,0,'',14,0,'',1511581132,0,1511581132,0,0,NULL,NULL),(24,0,'湖北校区','湖北校区','1','',0,0,0,0,0,'',15,0,'',1511581259,0,1511581259,0,0,NULL,NULL),(25,0,'文家校区','文家校区','1','',5,0,0,0,0,'',16,0,'',1511581551,0,1511581551,0,0,NULL,NULL),(26,0,'马蹄山校区','马蹄山校区','1','',0,0,0,0,0,'',17,0,'',1511581575,0,1511581575,0,0,NULL,NULL),(27,0,'百汇大夏','百汇大夏','1','078-34333333',0,4,86,1321,1321,'漯河',18,0,'',1511581600,0,1511602312,0,0,NULL,NULL),(28,0,'文家校区','文家校区','1','',0,0,0,0,0,'',19,0,'',1511581621,0,1511581621,0,0,NULL,NULL),(29,0,'陇南校区','陇南校区','1','010-27346666',35,3,75,1165,1165,'4号街道',20,0,'',1511581896,0,1511603277,0,0,NULL,NULL),(30,0,'西丽校区','西丽校区','1','',0,0,0,0,0,'',21,0,'',1511593181,0,1511593363,0,0,1511593363,NULL),(31,0,'西丽校区','西丽校区','1','',0,0,0,0,0,'',22,0,'',1511593374,0,1511593427,0,0,1511593427,NULL),(32,0,'光明顶校区','光明顶校区','1','',0,0,0,0,0,'',23,0,'',1511593882,0,1511593978,0,0,1511593978,NULL),(33,0,'光明顶校区','光明顶校区','1','',0,0,0,0,0,'',24,0,'',1511594032,0,1511594528,0,0,1511594528,NULL),(34,0,'光明顶校区','光明顶校区','1','',0,0,0,0,0,'',25,0,'',1511594550,0,1511594567,0,0,1511594567,NULL),(35,0,'光明顶校区','光明顶校区','1','',0,0,0,0,0,'',26,0,'',1511594669,0,1511594707,0,0,1511594707,NULL),(36,0,'光明顶校区','光明顶校区','1','',0,0,0,0,0,'',27,0,'',1511594669,0,1511594691,0,0,1511594691,NULL),(37,0,'光明顶校区','光明顶校区','1','',0,0,0,0,0,'',28,0,'',1511594922,0,1511594951,0,0,1511594951,NULL),(38,0,'光明顶校区','光明顶校区','1','',0,0,0,0,0,'',29,0,'',1511594961,0,1511595040,0,0,1511595040,NULL),(39,0,'光明顶校区','光明顶校区','1','027-43549839',0,1,37,568,568,'28号楼里',30,0,'',1511595055,0,1511603226,0,0,NULL,NULL),(40,0,'花都校区','花都校区','1','',0,0,0,0,0,'',31,0,'',1511601063,0,1511601068,0,0,1511601068,NULL),(41,0,'蛇口校区','蛇口校区','1','',0,0,0,0,0,'',32,0,'',1511601111,0,1511601118,0,0,1511601118,NULL),(42,0,'龙岗校区','龙岗校区','1','',0,0,0,0,0,'',33,0,'',1512030887,0,1512031018,0,0,1512031018,NULL),(43,0,'中山校区','中山校区','1','',0,0,0,0,0,'',34,0,'',1512042870,0,1512042870,0,0,NULL,NULL),(44,0,'喆聪校区','喆聪校区','1','',0,0,0,0,0,'',35,0,'',1512042881,0,1512042881,0,0,NULL,NULL),(45,0,'唐军校区','唐军校区','1','',0,0,0,0,0,'',36,0,'',1512042889,0,1512042889,0,0,NULL,NULL),(46,0,'振威校区','振威校区','1','',0,0,0,0,0,'',37,0,'',1512042897,0,1512042897,0,0,NULL,NULL),(47,0,'成建校区','成建校区','1','',0,0,0,0,0,'',38,0,'',1512042908,0,1512042908,0,0,NULL,NULL),(48,15,'guapicaozuo','guapicaozuo','2','14345678994',0,3,74,1153,1153,'trtrtrhtrhtjtrhrt',40,0,'',1513657356,1,1513742371,0,0,NULL,NULL),(49,16,'siyi','siyi','1','',0,0,0,0,0,'',41,0,'',1513657811,1,1513665391,1,1,1513665391,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=utf8mb4 COMMENT='校区和员工表的中间表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_branch_employee`
--

LOCK TABLES `x360p_branch_employee` WRITE;
/*!40000 ALTER TABLE `x360p_branch_employee` DISABLE KEYS */;
INSERT INTO `x360p_branch_employee` VALUES (2,0,1,1,0,0,NULL,0,0,NULL),(56,0,2,10002,0,0,NULL,0,0,NULL),(57,0,3,10002,0,0,NULL,0,0,NULL),(58,0,2,10003,0,0,NULL,0,0,NULL),(59,0,3,10003,0,0,NULL,0,0,NULL),(60,0,2,10004,0,0,NULL,0,0,NULL),(64,0,5,10006,0,0,NULL,0,0,NULL),(67,0,4,10009,0,0,NULL,0,0,NULL),(68,0,2,10009,0,0,NULL,0,0,NULL),(69,0,3,10009,0,0,NULL,0,0,NULL),(70,0,5,10009,0,0,NULL,0,0,NULL),(71,0,6,10009,0,0,NULL,0,0,NULL),(82,0,22,10012,0,0,NULL,0,0,NULL),(83,0,20,10013,0,0,NULL,0,0,NULL),(84,0,21,10013,0,0,NULL,0,0,NULL),(85,0,2,10014,0,0,NULL,0,0,NULL),(86,0,3,10014,0,0,NULL,0,0,NULL),(87,0,4,10014,0,0,NULL,0,0,NULL),(88,0,20,10014,0,0,NULL,0,0,NULL),(89,0,21,10014,0,0,NULL,0,0,NULL),(90,0,22,10014,0,0,NULL,0,0,NULL),(91,0,23,10014,0,0,NULL,0,0,NULL),(92,0,24,10014,0,0,NULL,0,0,NULL),(93,0,25,10014,0,0,NULL,0,0,NULL),(94,0,26,10014,0,0,NULL,0,0,NULL),(95,0,27,10014,0,0,NULL,0,0,NULL),(96,0,28,10014,0,0,NULL,0,0,NULL),(97,0,29,10014,0,0,NULL,0,0,NULL),(98,0,39,10014,0,0,NULL,0,0,NULL),(99,0,28,10015,0,0,NULL,0,0,NULL),(100,0,24,10015,0,0,NULL,0,0,NULL),(101,0,20,10015,0,0,NULL,0,0,NULL),(102,0,23,10015,0,0,NULL,0,0,NULL),(103,0,27,10015,0,0,NULL,0,0,NULL),(104,0,2,10016,0,0,NULL,0,0,NULL),(105,0,3,10016,0,0,NULL,0,0,NULL),(106,0,4,10016,0,0,NULL,0,0,NULL),(107,0,20,10016,0,0,NULL,0,0,NULL),(108,0,21,10016,0,0,NULL,0,0,NULL),(109,0,22,10016,0,0,NULL,0,0,NULL),(110,0,23,10016,0,0,NULL,0,0,NULL),(111,0,24,10016,0,0,NULL,0,0,NULL),(112,0,25,10016,0,0,NULL,0,0,NULL),(113,0,26,10016,0,0,NULL,0,0,NULL),(114,0,27,10016,0,0,NULL,0,0,NULL),(115,0,28,10016,0,0,NULL,0,0,NULL),(116,0,29,10016,0,0,NULL,0,0,NULL),(117,0,39,10016,0,0,NULL,0,0,NULL),(118,0,26,10017,0,0,NULL,0,0,NULL),(132,0,2,10018,0,0,NULL,0,0,NULL),(133,0,3,10018,0,0,NULL,0,0,NULL),(134,0,4,10018,0,0,NULL,0,0,NULL),(135,0,20,10018,0,0,NULL,0,0,NULL),(138,0,23,10018,0,0,NULL,0,0,NULL),(139,0,24,10018,0,0,NULL,0,0,NULL),(140,0,25,10018,0,0,NULL,0,0,NULL),(145,0,39,10018,0,0,NULL,0,0,NULL),(156,0,27,10019,0,0,NULL,0,0,NULL),(177,0,28,10021,0,0,NULL,0,0,NULL),(184,0,28,10018,0,0,NULL,0,0,NULL),(186,0,3,10022,0,0,NULL,0,0,NULL),(187,0,22,10022,0,0,NULL,0,0,NULL),(188,0,2,10023,0,0,NULL,0,0,NULL),(189,0,22,10023,0,0,NULL,0,0,NULL),(190,0,3,10023,0,0,NULL,0,0,NULL),(191,0,4,10023,0,0,NULL,0,0,NULL),(192,0,20,10023,0,0,NULL,0,0,NULL),(193,0,21,10023,0,0,NULL,0,0,NULL),(194,0,23,10023,0,0,NULL,0,0,NULL),(195,0,24,10023,0,0,NULL,0,0,NULL),(196,0,25,10023,0,0,NULL,0,0,NULL),(197,0,26,10023,0,0,NULL,0,0,NULL),(198,0,27,10023,0,0,NULL,0,0,NULL),(199,0,28,10023,0,0,NULL,0,0,NULL),(200,0,29,10023,0,0,NULL,0,0,NULL),(201,0,39,10023,0,0,NULL,0,0,NULL),(202,0,43,10023,0,0,NULL,0,0,NULL),(203,0,44,10023,0,0,NULL,0,0,NULL),(204,0,45,10023,0,0,NULL,0,0,NULL),(205,0,46,10023,0,0,NULL,0,0,NULL),(206,0,47,10023,0,0,NULL,0,0,NULL),(207,0,2,10024,0,0,NULL,0,0,NULL),(208,0,3,10024,0,0,NULL,0,0,NULL),(209,0,4,10024,0,0,NULL,0,0,NULL),(210,0,20,10024,0,0,NULL,0,0,NULL),(211,0,21,10024,0,0,NULL,0,0,NULL),(212,0,22,10024,0,0,NULL,0,0,NULL),(213,0,23,10024,0,0,NULL,0,0,NULL),(214,0,24,10024,0,0,NULL,0,0,NULL),(215,0,25,10024,0,0,NULL,0,0,NULL),(216,0,26,10024,0,0,NULL,0,0,NULL),(217,0,27,10024,0,0,NULL,0,0,NULL),(218,0,28,10024,0,0,NULL,0,0,NULL),(219,0,29,10024,0,0,NULL,0,0,NULL),(220,0,39,10024,0,0,NULL,0,0,NULL),(221,0,43,10024,0,0,NULL,0,0,NULL),(222,0,44,10024,0,0,NULL,0,0,NULL),(223,0,45,10024,0,0,NULL,0,0,NULL),(224,0,46,10024,0,0,NULL,0,0,NULL),(225,0,47,10024,0,0,NULL,0,0,NULL),(227,0,22,10002,0,0,NULL,0,0,NULL),(229,0,23,10002,0,0,NULL,0,0,NULL),(230,0,4,10002,0,0,NULL,0,0,NULL),(231,0,20,10002,0,0,NULL,0,0,NULL),(232,0,21,10002,0,0,NULL,0,0,NULL),(233,0,24,10002,0,0,NULL,0,0,NULL),(234,0,25,10002,0,0,NULL,0,0,NULL),(235,0,26,10002,0,0,NULL,0,0,NULL),(236,0,27,10002,0,0,NULL,0,0,NULL),(237,0,28,10002,0,0,NULL,0,0,NULL),(238,0,29,10002,0,0,NULL,0,0,NULL),(239,0,39,10002,0,0,NULL,0,0,NULL),(240,0,43,10002,0,0,NULL,0,0,NULL),(241,0,44,10002,0,0,NULL,0,0,NULL),(242,0,45,10002,0,0,NULL,0,0,NULL),(243,0,46,10002,0,0,NULL,0,0,NULL),(244,0,47,10002,0,0,NULL,0,0,NULL),(245,0,2,10025,0,0,NULL,0,0,NULL),(246,0,4,10003,0,0,NULL,0,0,NULL),(247,0,2,10026,0,0,NULL,0,0,NULL);
/*!40000 ALTER TABLE `x360p_branch_employee` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COMMENT='公告';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_broadcast`
--

LOCK TABLES `x360p_broadcast` WRITE;
/*!40000 ALTER TABLE `x360p_broadcast` DISABLE KEYS */;
INSERT INTO `x360p_broadcast` VALUES (1,0,1,1,'','这是一条公告','<p>哈哈</p>',1511520392,1,1511521193,1,1,1511521193),(2,0,1,1,'','这是一条很好笑的公告','<p>哈哈</p>',1511521216,1,1511521397,1,1,1511521397),(3,0,1,1,'','明天放假三天！','',1511521229,1,1511525404,1,1,1511525404),(4,0,1,1,'','防火防盗防闺蜜','<p>嗯嗯</p>',1511521387,1,1511525402,1,1,1511525402),(5,0,1,1,'','公告','<p>公告</p>',1511523627,1,1511523940,1,1,1511523940),(6,0,1,1,'','新的公告','<p>嗯嗯</p>',1511524085,1,1511525399,1,1,1511525399),(7,0,1,1,'','dd','<p>dd</p>',1511524353,1,1511524479,1,1,1511524479),(8,0,1,1,'','gg','<p>ff</p>',1511524491,1,1511524899,1,1,1511524899),(9,0,1,1,'','trhrhr','<p>htrhrhrh</p>',1511524511,1,1511524813,1,1,1511524813),(10,0,1,1,'','gonggaoddd','<p>ddd</p>',1511525522,1,1511525545,1,1,1511525545),(11,0,1,1,'','dddd','<p>dddd</p>',1511525633,1,1511525684,1,1,1511525684),(12,0,1,1,'','dddddd','<p>ddddd</p>',1511525644,1,1511525680,1,1,1511525680),(13,0,1,1,'','明天放假三天！回家好好休息','<p style=\"margin-top: 22px; margin-bottom: 0px; padding: 0px; line-height: 24px; color: rgb(51, 51, 51); text-align: justify; font-family: arial; white-space: normal; background-color: rgb(255, 255, 255);\">官方简历显示，1984年至1994年，鲁炜曾在桂林任中院书记员、广西法制报记者、新华社桂林支社社长等职。</p><p style=\"margin-top: 22px; margin-bottom: 0px; padding: 0px; line-height: 24px; color: rgb(51, 51, 51); text-align: justify; font-family: arial; white-space: normal; background-color: rgb(255, 255, 255);\">再比如，针对鲁炜落马的原因，该报《“首虎”落马小雪前夜，贪腐只会面对寒冬》一文直接点出：鲁炜正是被重点查处的“三类人”的典型代表。所谓的“三类人”是指十八大以后不收敛、不收手；问题线索反映集中、群众反映强烈；现在重要岗位且可能还要提拔使用的领导干部。</p><p style=\"margin-top: 22px; margin-bottom: 0px; padding: 0px; line-height: 24px; color: rgb(51, 51, 51); text-align: justify; font-family: arial; white-space: normal; background-color: rgb(255, 255, 255);\">而对此，中纪委网站《决不给不收敛不收手者留下任何幻想》更加明白地指出：鲁炜也好，刘强也罢，他们都有一个共同特点，即在党的十八大后依然不收敛、不收手，而且作为60后中管干部，落马前都在重要领导岗位且可能进一步提拔重用，他们正是要被严肃查处的重中之重。</p><p><br/></p>',1511525815,1,1511571455,0,0,NULL),(14,0,1,1,'','fdg','<p>gfdg</p>',1511525825,1,1511526827,1,1,1511526827),(15,0,1,1,'','dsfaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaahgfhghfhgfdhgsdfhsafdsaf','<p>dsfsdf<br/></p>',1511525886,1,1513152486,1,1,1513152486),(16,0,1,1,'','fdasggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggghgjgghgjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjsdghfsddddddddddddddd','<p>fergred</p>',1511525959,1,1513152544,1,1,1513152544),(17,0,1,1,'','去吧皮卡丘','<p style=\"margin-top: 22px; margin-bottom: 0px; padding: 0px; line-height: 24px; color: rgb(51, 51, 51); text-align: justify; font-family: arial; white-space: normal; background-color: rgb(255, 255, 255);\">&nbsp; &nbsp; 11月23日下午2时，刘强被宣布因涉嫌严重违纪而接受组织审查。仅过4个小时后，中纪委网站就以“网言网语”的形式摘编了18位网友的留言，诠释出“连打两虎彰显党中央坚如磐石决心”的深意。</p><p style=\"margin-top: 22px; margin-bottom: 0px; padding: 0px; line-height: 24px; color: rgb(51, 51, 51); text-align: justify; font-family: arial; white-space: normal; background-color: rgb(255, 255, 255);\">&nbsp; &nbsp; 除了“又快又有料”，中纪委网站最近的新特点之二是密集发声。正如上文所述，鲁炜落马当天（11月21日）发文表态、第二天（11月22日）揭秘，刘强被查后当天（11月23日）第一时间集纳舆情，第二天（11月24日）刊发专评“决不给不收敛不收手者留下任何幻想”。</p><p style=\"margin-top: 22px; margin-bottom: 0px; padding: 0px; line-height: 24px; color: rgb(51, 51, 51); text-align: justify; font-family: arial; white-space: normal; background-color: rgb(255, 255, 255);\">换句话说，中纪委网站这4天一点都没歇着，连发4文，像炮弹一样打向贪腐。在记者的印象中，这种不再仅仅简单发布打虎消息，而是密集发声的态势此前并不多见。</p><p style=\"margin-top: 22px; margin-bottom: 0px; padding: 0px; line-height: 24px; color: rgb(51, 51, 51); text-align: justify; font-family: arial; white-space: normal; background-color: rgb(255, 255, 255);\">中纪委机关报：反腐评论上头版头条</p><p style=\"margin-top: 22px; margin-bottom: 0px; padding: 0px; line-height: 24px; color: rgb(51, 51, 51); text-align: justify; font-family: arial; white-space: normal; background-color: rgb(255, 255, 255);\">与中纪委网站发生显著变化类似，看法新闻记者注意到，最近的《中国纪检监察报》也在悄然“换了种操作”。</p><p style=\"margin-top: 22px; margin-bottom: 0px; padding: 0px; line-height: 24px; color: rgb(51, 51, 51); text-align: justify; font-family: arial; white-space: normal; background-color: rgb(255, 255, 255);\">众所周知，该报作为早报，事发第二天才能刊播和发声，但这丝毫没有影响其展现巨大的影响力：</p><p style=\"margin-top: 22px; margin-bottom: 0px; padding: 0px; line-height: 24px; color: rgb(51, 51, 51); text-align: justify; font-family: arial; white-space: normal; background-color: rgb(255, 255, 255);\">自鲁炜落马第二天（11月22日）至刘强倒下第二天（11月24日），这家中纪委机关报3天共刊发4篇相关评论性、综述性文章，总字数达到5600多。至于效果，官方的表述是“《广安观潮》栏目刊发的评论文章被广泛转载”。</p><p><br/></p>',1511527591,1,1511571567,0,0,NULL),(18,0,1,1,'2','今天放学后全校大扫除','<p>一个都不许少<img class=\"img-responsive\" src=\"http://s10.xiao360.com/qms/data/uploads/file/20171125/1511572685452060.jpg\"/></p>',1511572733,1,1513152441,1,1,1513152441),(19,0,1,1,'38,51,53,54','11-29公告','<p>今天天气不错</p>',1511948385,1,1512023437,0,0,NULL),(20,0,1,1,'1,3,38,51,53,4,5,6,2,29,49','所有部门的新闻','<p class=\"otitle\" style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">（原标题：让十九大精神像金达莱花一样在基层绽放）</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">社区是“小社会、大家庭”，社区干部是“小职务、大管家”。在吉林省延吉市，党的十九大代表、社区党委书记林松淑家喻户晓，居民遇到困难，最先想到的就是这位朝鲜族“阿玛尼”。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">盛会归来，林松淑身上多了一份沉甸甸的责任：十九大精神宣讲与践行。她说：“在朝鲜族群众心里，金达莱花是美好幸福的象征，我要让十九大精神像金达莱一样绽放在社区。”</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">热情宣讲</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">用家常话唠“精神”</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">“林书记，‘房子是用来住的，不是用来炒的’是啥意思?”“林书记，报告对养老有啥好政策?”“书记，孩子未来上学是不是更方便?”……社区里，居民们的问题像连珠炮。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">已连续宣讲多场的林松淑，面对眼前的居民耐心作答：“国家已经出台政策有力调控，未来住房保障机制将更健全”“住院吃药会越来越优惠”……</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">十六年的工作经验，林松淑深知群众所思所求，她欣喜地看到，这些切实的需求在十九大报告中都有所体现。她把报告结合居民生活宣讲得更加通俗易懂，用朝鲜语和老年朝鲜族群众交流报告，让他们理解起来更轻松。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">“我们最关心医疗问题，林书记的解读让我们更有信心。”71岁的朝鲜族群众李今子说，“作为老党员，我更要带头学习，带头宣讲。”</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">凝心聚神</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">社区就是新时代传习所</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">林松淑从事社区工作近20年，记了几十万字的民生日记：从原来生火取暖到现在集中供暖，从原来跑很远交电费到现在社区代收，从原来孤寡老人孤独生活到现在得到社区关怀，党的好政策让社区居民生活水平不断提高，真正体现了以人民为中心。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">社区的每一点变化都真真切切得到群众认可，点点滴滴的改变极大提升了居民的获得感，朝鲜族、汉族群众更加团结。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">前段时间，园辉社区“新时代传习所”挂牌，标识悬挂在社区门口，十分醒目。林松淑说，社区是服务群众的最后一公里，有着良好群众基础的社区，就是十九大精神最好的讲习所、培训班、操作间。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">社区有场所，场所有活动，活动有人气，是十九大精神离老百姓最近的地方。在林松淑带动下，园辉社区用朝鲜语、汉语双语种做宣传，拿群众生活中最常遇、最关心的养老、住房、医疗说事。“平时来的群众很多，能起到覆盖效果。”林松淑说。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">朝鲜族群众能歌善舞，园辉社区活动室就是社区居民唱歌跳舞的场所。林松淑挑选了几位能编能跳的群众组织者，为他们深入讲解十九大精神，结合到日常文娱活动中，事半功倍。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">社区干部经常要深入居民家中了解情况，服务群众。他们发挥串户进门优势，到一家，讲一点，学一分。干部们“聚是一团火，散是满天星”，把十九大精神带到了千家万户。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">身体力行</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">自己当榜样别人才服气</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">把居民需要作为最大动力，把居民满意作为最大成绩，把居民拥护作为最大幸福。林松淑深知，要想让群众信服，就必须加倍奉献，持之以恒服务，身体力行做到。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">“上面千条线，社区一根针”，让老百姓享受党的好政策，让越来越多群众会聚在党旗下，首先要成为他们的贴心人，把百姓的事时刻放在心上。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\"><br/></p><p><a target=\"_self\" class=\"ad_hover_href\" style=\"color: rgb(15, 107, 153); text-decoration-line: underline; width: 30px; height: 17px; position: absolute; left: 0px; bottom: 0px; z-index: 10; background: url(&quot;http://img1.126.net/channel18/ad.png&quot;) no-repeat;\"></a></p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">既是便民电话，又是求助热线，电话十多年没换过号码，怕群众找不到;既是老年人的好儿女，又是孩子们的好妈妈，前后收养六位孩子，有的已经上了大学;既是服务员，又是好管家，为出国人员保管贵重物品，无一差错……社区里的大事小情林松淑都放在心里，记在十几本厚厚的民生日记上。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">“盛会归来，我必须更加认真。”林松淑说，“只有这样群众才能更信任社区。”</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">这是林松淑近几天的安排：上午在社区宣讲，下午为延边州的党员干部传达精神，晚上照顾有心脏病的丈夫;上午为社区内的经营者解读创业就业政策，下午按照惯例登门看望孤寡老人;周末搭乘高铁去吉林省孤儿学校，看望曾经领养的孤儿，为学校教职员工宣讲。结束后又匆匆坐上高铁，赶回延吉已是深夜。</p><p style=\"margin-top: 32px; margin-bottom: 0px; padding: 0px; font-size: 18px; text-indent: 2em; font-stretch: normal; line-height: 32px; font-family: &quot;Microsoft Yahei&quot;; color: rgb(64, 64, 64); text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);\">日程紧密、马不停蹄，但林松淑乐在其中，信心满满：“一定让十九大精神像金达莱一样绽放!”</p><p><br/></p>',1512022519,1,1513153076,1,1,1513153076);
/*!40000 ALTER TABLE `x360p_broadcast` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COMMENT='班级表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class`
--

LOCK TABLES `x360p_class` WRITE;
/*!40000 ALTER TABLE `x360p_class` DISABLE KEYS */;
INSERT INTO `x360p_class` VALUES (1,0,0,2,'yaorui-test-att','',1,1,10002,0,0,3,10,10,0.00,12,0,6,4,2017,'H',1510934400,1511971200,0,'',1510972292,1,1510972292,0,NULL,0,0,0),(2,0,0,2,'test01','',1,1,1,1,0,9,10,2,0.00,30,0,10,1,2017,'S',1510934400,1511539200,0,'',1510972355,1,1510972388,0,NULL,0,0,0),(3,0,0,3,'数学辅导班','mathH',1,1,10003,10002,0,8,30,0,0.00,30,0,0,1,2017,'Q',1510934400,1527177600,0,'',1510993008,1,1510993008,0,NULL,0,0,0),(6,0,0,2,'音乐培训班','art001',5,4,10003,0,0,7,20,11,0.00,36,0,5,9,2017,'H',1510934400,1523548800,0,'',1510994475,1,1510995510,0,NULL,0,0,0),(7,0,0,2,'test_class_warn','407',1,1,10003,10002,0,5,12,5,0.00,30,0,1,2,2017,'Q',1510934400,1511971200,0,'',1510995550,1,1511150471,0,NULL,0,0,0),(8,0,0,2,'哎呀呀','0078',8,3,10003,10002,0,0,11,12,0.00,3,0,0,1,2017,'C',1511107200,1541001600,0,'',1511151853,1,1511151853,0,NULL,0,0,0),(9,0,0,2,'考勤课耗测试cid-9','',9,4,10004,0,0,8,10,2,0.00,7,0,7,3,2017,'H',1511193600,1511971200,0,'',1511257877,1,1511257877,0,NULL,0,0,0),(10,0,0,2,'test','',1,1,10003,0,0,0,6,0,0.00,9,0,2,1,2017,'H',1511193600,1511971200,0,'',1511266386,1,1511339449,0,NULL,0,0,0),(11,0,0,2,'赠送课次','',9,4,1,0,0,6,5,6,0.00,7,0,6,6,2017,'H',1511280000,1511971200,0,'',1511345414,1,1511345414,0,NULL,0,0,0),(12,0,0,2,'两课时班级','',5,4,10003,10003,0,6,10,2,0.00,2,0,2,1,2017,'H',1510675200,1514476800,0,'',1511345979,1,1511345979,0,NULL,0,0,0),(13,0,0,2,'班课自有登记考勤测试','',9,4,10004,0,0,2,5,4,0.00,6,0,18,5,2017,'H',1511280000,1511971200,0,'',1511354244,1,1513072927,0,NULL,0,0,0),(14,0,0,3,'艺术课课时包2017寒假周一19:00~21:30','ART00117H-1-19002130',5,2,10003,0,0,5,40,0,0.00,15,0,0,0,2017,'H',1512576000,1518710400,0,'',1512629950,1,1512629950,0,NULL,0,1900,2130);
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
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COMMENT='考勤记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class_attendance`
--

LOCK TABLES `x360p_class_attendance` WRITE;
/*!40000 ALTER TABLE `x360p_class_attendance` DISABLE KEYS */;
INSERT INTO `x360p_class_attendance` VALUES (1,0,2,1,1,5,1,9,10002,0,20171118,1000,1200,9,9,7,1,0,0,0,'',1510972694,1,1510972694,0,NULL,0),(2,0,2,1,1,7,1,21,10002,10002,20171118,1500,1515,9,9,7,1,0,0,0,'',1510988833,1,1510990849,1,1510990849,1),(3,0,2,1,1,8,1,22,10002,10002,20171118,1530,1545,9,9,40,0,0,0,0,'',1510990864,1,1510990864,0,NULL,0),(7,0,2,6,5,3,4,54,10002,0,20171121,1900,2130,7,7,7,0,0,0,0,'考勤测试-yr',1511236430,1,1511236432,0,NULL,0),(8,0,2,1,1,4,1,5,10002,0,20171125,800,1000,9,10,8,1,0,0,0,'',1511237289,1,1511237291,0,NULL,0),(9,0,2,6,5,30,4,85,10002,10003,20171120,1230,1300,7,7,7,0,0,0,0,'',1511237790,1,1511237793,0,NULL,0),(10,0,2,1,1,5,1,6,10002,0,20171125,1030,1230,9,10,8,1,0,0,0,'',1511238107,1,1511238110,0,NULL,0),(11,0,2,6,5,7,4,59,10002,0,20171125,1030,1230,7,7,7,0,0,0,0,'',1511238502,1,1511238505,0,NULL,0),(13,0,2,6,5,1,4,84,10003,10003,20171120,1200,1215,7,7,7,0,0,0,0,'',1511244986,1,1511244989,0,NULL,0),(14,0,2,6,5,5,4,56,10002,0,20171123,1900,2130,7,7,7,0,0,0,0,'',1511245478,1,1511245480,0,NULL,0),(18,0,2,6,5,6,4,58,10002,0,20171125,800,1000,7,8,4,0,0,0,1,'',1511246713,1,1511247847,0,NULL,0),(19,0,2,1,1,8,1,86,10002,0,20171121,1500,1700,9,9,0,0,0,0,0,'',1511249484,1,1511255550,1,1511255550,1),(20,0,2,9,9,1,4,87,10004,0,20171128,1900,2130,2,2,0,0,0,0,0,'考勤测试',1511258165,1,1511265288,1,1511265288,1),(26,0,2,9,9,1,4,87,10004,0,20171128,1900,2130,2,2,2,0,0,0,0,'',1511340091,1,1511347122,0,NULL,0),(27,0,2,9,9,7,4,93,10004,0,20171203,1030,1230,2,2,0,0,0,0,0,'',1511340119,1,1511343963,0,NULL,0),(28,0,2,11,9,1,4,103,1,0,20171129,1900,2130,2,2,2,0,0,0,0,'',1511345504,1,1511345504,0,NULL,0),(30,0,2,12,5,1,4,109,10003,10003,20171122,1900,2130,1,1,1,0,0,0,0,'',1511346471,1,1511346471,0,NULL,0),(33,0,2,9,9,2,4,88,10004,0,20171129,1900,2130,2,2,0,0,0,0,0,'',1511346966,1,1511347103,1,1511347103,1),(34,0,2,11,9,5,4,107,1,0,20171202,1030,1230,2,2,0,0,0,0,0,'',1511352652,1,1511352668,1,1511352668,1),(35,0,2,9,9,2,4,88,10004,0,20171129,1900,2130,2,2,0,0,0,0,0,'',1511352707,1,1511352707,0,NULL,0),(36,0,2,11,9,2,4,104,1,0,20171130,1900,2130,2,2,0,0,0,0,0,'',1511352808,1,1511352878,1,1511352878,1),(37,0,2,11,9,2,4,104,1,0,20171130,1900,2130,2,2,1,1,0,0,0,'',1511352903,1,1511352972,0,NULL,0),(39,0,2,11,9,0,4,0,1,0,20171122,1900,2130,2,2,0,1,0,0,0,'test班课的自由考勤',1511353354,1,1511353354,0,NULL,0),(40,0,2,11,9,0,4,0,1,0,20171123,1900,2130,2,2,1,0,0,0,0,'test 班课自由考勤',1511353956,1,1511353956,0,NULL,0),(41,0,2,11,9,0,4,0,1,0,20171124,1900,2130,2,2,1,0,0,0,0,'',1511354045,1,1511354045,0,NULL,0),(43,0,2,13,9,0,4,0,1,0,20171122,1900,2130,2,2,0,0,0,0,0,'',1511354582,1,1511354582,0,NULL,0),(44,0,2,13,9,0,4,0,10005,0,20171125,800,1000,2,2,2,0,0,0,0,'',1511355394,1,1511355394,0,NULL,0),(45,0,2,13,9,0,4,0,1,0,20171126,800,1000,2,2,1,1,0,0,0,'',1511355603,1,1511355603,0,NULL,0),(46,0,2,6,5,30,4,113,10004,0,20171124,1900,2130,7,7,7,0,0,0,0,'',1511432684,1,1511432687,0,NULL,0),(47,0,2,1,1,9,1,102,10004,0,20171121,1900,2130,9,9,9,0,0,0,0,'',1511432735,1,1511432739,0,NULL,0),(48,0,2,1,1,8,1,86,10002,0,20171121,1500,1700,9,9,7,1,0,0,0,'',1511573588,1,1511573591,0,NULL,0),(49,0,2,9,9,3,4,89,10004,0,20171130,1900,2130,2,2,0,0,0,0,0,'',1512009291,1,1512371792,1,1512371792,1),(50,0,2,6,5,13,4,69,10003,0,20171203,800,1000,8,11,0,0,0,0,3,'试听跟班考勤',1512360340,1,1512371341,1,1512371341,1),(51,0,2,6,5,13,4,69,10003,0,20171203,800,1000,8,11,0,0,0,0,3,'试听跟班考勤啊',1512360984,1,1512371341,1,1512371341,1),(52,0,2,6,5,13,4,69,10002,0,20171203,800,1000,8,11,0,0,0,0,3,'试听考勤啊',1512361714,1,1512371341,1,1512371341,1),(53,0,2,6,5,13,4,69,10002,0,20171203,800,1000,8,11,0,0,0,0,3,'',1512367674,1,1512371341,1,1512371341,1),(54,0,2,6,5,13,4,69,10002,0,20171203,800,1000,8,11,0,0,0,0,3,'',1512368463,1,1512371341,1,1512371341,1),(58,0,2,11,9,6,4,108,10002,0,20171203,1030,1230,3,5,3,0,0,0,0,'',1512377419,1,1512377421,0,NULL,0),(59,0,2,6,5,13,4,69,10006,0,20171203,800,1000,8,11,8,0,0,0,3,'',1512381375,1,1512381379,0,NULL,0),(60,0,2,13,9,0,4,0,10003,0,20171205,1900,2130,1,3,0,0,0,0,0,'',1512475491,1,1512475491,0,NULL,0),(61,0,2,13,9,0,4,0,10003,0,20171206,1900,2130,1,3,0,0,0,0,0,'嗯啊',1512527718,1,1512527744,0,NULL,0),(62,0,2,13,9,0,4,0,10003,0,20171204,1900,2130,1,3,0,0,0,0,0,'呃呃',1512528638,1,1512701622,1,1512701622,1),(63,0,2,6,5,19,4,75,10004,0,20171208,1445,1545,9,9,3,0,0,0,0,'',1512716635,1,1512716638,0,NULL,0),(64,0,2,6,5,25,4,82,0,0,20171213,1900,2130,11,11,0,0,0,0,0,'',1513168985,1,1513422394,1,1513422394,1),(69,0,2,7,1,0,1,0,10003,10002,20171221,1900,2130,5,5,5,0,0,0,0,'测试退费课消',1513821705,1,1513821705,0,NULL,0);
/*!40000 ALTER TABLE `x360p_class_attendance` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COMMENT='班级日志';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class_log`
--

LOCK TABLES `x360p_class_log` WRITE;
/*!40000 ALTER TABLE `x360p_class_log` DISABLE KEYS */;
INSERT INTO `x360p_class_log` VALUES (1,0,1,1,'User 创建了班级',NULL,'{\"class_name\":\"yaorui-test-att\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"H\",\"lid\":1,\"sj_id\":1,\"bid\":2,\"teach_eid\":10002,\"second_eid\":0,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":10,\"lesson_times\":10,\"cr_id\":3,\"schedule\":[{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":3},{\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"cr_id\":3},{\"week_day\":7,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"cr_id\":3},{\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":3}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"1\"}',1510972292,1,1510972292,0,NULL,0),(2,0,1,3,'User 让 yaorui001 加入了班级',5,'{\"cid\":1,\"sid\":5,\"sl_id\":6,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"1\"}',1510972332,1,1510972332,0,NULL,0),(3,0,1,3,'User 让 yaorui002 加入了班级',6,'{\"cid\":1,\"sid\":6,\"sl_id\":7,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"2\"}',1510972332,1,1510972332,0,NULL,0),(4,0,1,3,'User 让 yaorui003 加入了班级',7,'{\"cid\":1,\"sid\":7,\"sl_id\":8,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"3\"}',1510972332,1,1510972332,0,NULL,0),(5,0,2,1,'User 创建了班级',NULL,'{\"class_name\":\"test01\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"S\",\"lid\":1,\"sj_id\":1,\"bid\":2,\"teach_eid\":1,\"second_eid\":1,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-25 00:00:00\",\"plan_student_nums\":10,\"lesson_times\":30,\"cr_id\":9,\"schedule\":[{\"week_day\":6,\"int_start_hour\":\"12:00\",\"int_end_hour\":\"12:15\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"11:45\",\"int_end_hour\":\"12:00\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"11:30\",\"int_end_hour\":\"11:45\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"11:15\",\"int_end_hour\":\"11:30\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"11:15\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"10:45\",\"cr_id\":0}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"2\"}',1510972355,1,1510972355,0,NULL,0),(6,0,2,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test01\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":1,\"second_eid\":1,\"edu_eid\":0,\"cr_id\":9,\"plan_student_nums\":10,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":30,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"S\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-25 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":74,\"schedule\":[{\"csd_id\":5,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"12:00\",\"int_end_hour\":\"12:15\",\"delete_uid\":0},{\"csd_id\":6,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"11:45\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":7,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"11:30\",\"int_end_hour\":\"11:45\",\"delete_uid\":0},{\"csd_id\":8,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"11:15\",\"int_end_hour\":\"11:30\",\"delete_uid\":0},{\"csd_id\":9,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"11:15\",\"delete_uid\":0},{\"csd_id\":10,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"10:45\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":2,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test01\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":1,\"second_eid\":1,\"edu_eid\":0,\"cr_id\":9,\"plan_student_nums\":10,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":30,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"S\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511539200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":74,\"schedule\":[{\"csd_id\":5,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"12:00\",\"int_end_hour\":\"12:15\",\"delete_uid\":0},{\"csd_id\":6,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"11:45\",\"int_end_hour\":\"12:00\",\"delete_uid\":0},{\"csd_id\":7,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"11:30\",\"int_end_hour\":\"11:45\",\"delete_uid\":0},{\"csd_id\":8,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"11:15\",\"int_end_hour\":\"11:30\",\"delete_uid\":0},{\"csd_id\":9,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"11:15\",\"delete_uid\":0},{\"csd_id\":10,\"og_id\":0,\"bid\":2,\"cid\":2,\"eid\":1,\"cr_id\":0,\"year\":2017,\"season\":\"S\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"10:45\",\"delete_uid\":0}],\"update_time\":1510972388}}',1510972388,1,1510972388,0,NULL,0),(7,0,1,3,'User 让 刘子云05 加入了班级',8,'{\"cid\":1,\"sid\":8,\"sl_id\":5,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"4\"}',1510972458,1,1510972458,0,NULL,0),(8,0,1,3,'User 让 刘子云06 加入了班级',9,'{\"cid\":1,\"sid\":9,\"sl_id\":9,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"5\"}',1510972458,1,1510972458,0,NULL,0),(9,0,1,3,'User 让 刘子云04 加入了班级',4,'{\"cid\":1,\"sid\":4,\"sl_id\":4,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"6\"}',1510972458,1,1510972458,0,NULL,0),(10,0,1,3,'User 让 刘子云03 加入了班级',3,'{\"cid\":1,\"sid\":3,\"sl_id\":3,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"7\"}',1510972458,1,1510972458,0,NULL,0),(11,0,1,3,'User 让 刘子云02 加入了班级',2,'{\"cid\":1,\"sid\":2,\"sl_id\":2,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"8\"}',1510972458,1,1510972458,0,NULL,0),(12,0,1,3,'User 让 刘子云01 加入了班级',1,'{\"cid\":1,\"sid\":1,\"sl_id\":1,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"9\"}',1510972458,1,1510972458,0,NULL,0),(13,0,3,1,'User 创建了班级',NULL,'{\"class_name\":\"数学辅导班\",\"class_no\":\"mathH\",\"year\":\"2017\",\"season\":\"Q\",\"lid\":1,\"sj_id\":1,\"bid\":3,\"teach_eid\":10003,\"second_eid\":10002,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2018-05-25 00:00:00\",\"plan_student_nums\":30,\"lesson_times\":30,\"cr_id\":8,\"schedule\":[{\"week_day\":1,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:00\",\"cr_id\":8},{\"week_day\":2,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:00\",\"cr_id\":8},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:00\",\"cr_id\":8},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:00\",\"cr_id\":8},{\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:00\",\"cr_id\":8},{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":8},{\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":8},{\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"cr_id\":8},{\"week_day\":7,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"cr_id\":8}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"3\"}',1510993008,1,1510993008,0,NULL,0),(16,0,6,1,'User 创建了班级',NULL,'{\"class_name\":\"音乐培训班\",\"class_no\":\"art001\",\"year\":\"2017\",\"season\":\"H\",\"lid\":5,\"sj_id\":\"\",\"bid\":2,\"teach_eid\":0,\"second_eid\":0,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2018-04-13 00:00:00\",\"plan_student_nums\":20,\"lesson_times\":30,\"cr_id\":7,\"schedule\":[{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":7},{\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":7},{\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":7},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":7},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":7},{\"week_day\":2,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":7},{\"week_day\":1,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":7},{\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"cr_id\":7},{\"week_day\":7,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"cr_id\":7}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"6\"}',1510994475,1,1510994475,0,NULL,0),(17,0,6,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u97f3\\u4e50\\u57f9\\u8bad\\u73ed\",\"class_no\":\"art001\",\"lid\":5,\"sj_id\":0,\"teach_eid\":10003,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":7,\"plan_student_nums\":20,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":30,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2018-04-13 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":11,\"schedule\":[{\"csd_id\":38,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":39,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":40,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":41,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":42,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":43,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":2,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":44,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":1,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":45,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":46,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u97f3\\u4e50\\u57f9\\u8bad\\u73ed\",\"class_no\":\"art001\",\"lid\":5,\"sj_id\":0,\"teach_eid\":10003,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":7,\"plan_student_nums\":20,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":30,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1523548800,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":11,\"schedule\":[{\"csd_id\":38,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":39,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":40,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":41,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":42,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":43,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":2,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":44,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":1,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":45,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":46,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1510994516}}',1510994516,1,1510994516,0,NULL,0),(18,0,6,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u97f3\\u4e50\\u57f9\\u8bad\\u73ed\",\"class_no\":\"art001\",\"lid\":5,\"sj_id\":4,\"teach_eid\":10003,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":7,\"plan_student_nums\":20,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":30,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2018-04-13 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":38,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":39,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":40,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":41,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":42,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":43,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":2,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":44,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":1,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":45,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":46,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":6,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u97f3\\u4e50\\u57f9\\u8bad\\u73ed\",\"class_no\":\"art001\",\"lid\":5,\"sj_id\":4,\"teach_eid\":10003,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":7,\"plan_student_nums\":20,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":30,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1523548800,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":38,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":39,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":40,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":41,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":42,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":43,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":2,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":44,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":1,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":45,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0},{\"csd_id\":46,\"og_id\":0,\"bid\":2,\"cid\":6,\"eid\":0,\"cr_id\":7,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1510995510}}',1510995510,1,1510995510,0,NULL,0),(19,0,7,1,'User 创建了班级',NULL,'{\"class_name\":\"test_class_warn\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"S\",\"lid\":1,\"sj_id\":1,\"bid\":2,\"teach_eid\":10002,\"second_eid\":0,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":12,\"lesson_times\":1,\"cr_id\":6,\"schedule\":[{\"week_day\":6,\"int_start_hour\":\"12:00\",\"int_end_hour\":\"12:15\",\"cr_id\":6}],\"course_arrange\":0,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"7\"}',1510995550,1,1510995550,0,NULL,0),(20,0,7,3,'User 让 test_lesson5 加入了班级',15,'{\"cid\":7,\"sid\":15,\"sl_id\":18,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"10\"}',1510995565,1,1510995565,0,NULL,0),(21,0,7,3,'User 让 test_lesson3 加入了班级',13,'{\"cid\":7,\"sid\":13,\"sl_id\":19,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"11\"}',1510995565,1,1510995565,0,NULL,0),(22,0,7,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":7,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test_class_warn\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10002,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":6,\"plan_student_nums\":12,\"student_nums\":2,\"nums_rate\":\"0.00\",\"lesson_times\":1,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"S\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"week_day\":7,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"14:45\",\"cr_id\":6}]},\"old\":[],\"changed_data\":{\"cid\":7,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test_class_warn\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10002,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":6,\"plan_student_nums\":12,\"student_nums\":2,\"nums_rate\":\"0.00\",\"lesson_times\":1,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":0,\"year\":2017,\"season\":\"S\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"week_day\":7,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"14:45\",\"cr_id\":6}],\"update_time\":1510995664}}',1510995664,1,1510995664,0,NULL,0),(23,0,7,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":7,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test_class_warn\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10002,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":6,\"plan_student_nums\":12,\"student_nums\":2,\"nums_rate\":\"0.00\",\"lesson_times\":2,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"S\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":48,\"og_id\":0,\"bid\":2,\"cid\":7,\"eid\":10002,\"cr_id\":6,\"year\":2017,\"season\":\"S\",\"week_day\":7,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"14:45\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":7,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test_class_warn\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10002,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":6,\"plan_student_nums\":12,\"student_nums\":2,\"nums_rate\":\"0.00\",\"lesson_times\":2,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"S\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":48,\"og_id\":0,\"bid\":2,\"cid\":7,\"eid\":10002,\"cr_id\":6,\"year\":2017,\"season\":\"S\",\"week_day\":7,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"14:45\",\"delete_uid\":0}],\"update_time\":1510995874}}',1510995874,1,1510995874,0,NULL,0),(24,0,7,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":7,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test_class_warn\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10002,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":6,\"plan_student_nums\":12,\"student_nums\":2,\"nums_rate\":\"0.00\",\"lesson_times\":2,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"S\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":48,\"og_id\":0,\"bid\":2,\"cid\":7,\"eid\":10002,\"cr_id\":6,\"year\":2017,\"season\":\"S\",\"week_day\":7,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"14:45\",\"delete_uid\":0},{\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"11:15\",\"cr_id\":6}]},\"old\":[],\"changed_data\":{\"cid\":7,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test_class_warn\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10002,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":6,\"plan_student_nums\":12,\"student_nums\":2,\"nums_rate\":\"0.00\",\"lesson_times\":2,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"S\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":48,\"og_id\":0,\"bid\":2,\"cid\":7,\"eid\":10002,\"cr_id\":6,\"year\":2017,\"season\":\"S\",\"week_day\":7,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"14:45\",\"delete_uid\":0},{\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"11:15\",\"cr_id\":6}],\"update_time\":1510995902}}',1510995902,1,1510995902,0,NULL,0),(25,0,7,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":7,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test_class_warn\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10002,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":6,\"plan_student_nums\":12,\"student_nums\":2,\"nums_rate\":\"0.00\",\"lesson_times\":2,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"S\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":5,\"schedule\":[{\"csd_id\":48,\"og_id\":0,\"bid\":2,\"cid\":7,\"eid\":10002,\"cr_id\":6,\"year\":2017,\"season\":\"S\",\"week_day\":7,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"14:45\",\"delete_uid\":0},{\"week_day\":7,\"int_start_hour\":\"17:00\",\"int_end_hour\":\"17:45\",\"cr_id\":6}]},\"old\":[],\"changed_data\":{\"cid\":7,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test_class_warn\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10002,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":6,\"plan_student_nums\":12,\"student_nums\":2,\"nums_rate\":\"0.00\",\"lesson_times\":2,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"S\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":5,\"schedule\":[{\"csd_id\":48,\"og_id\":0,\"bid\":2,\"cid\":7,\"eid\":10002,\"cr_id\":6,\"year\":2017,\"season\":\"S\",\"week_day\":7,\"int_start_hour\":\"14:00\",\"int_end_hour\":\"14:45\",\"delete_uid\":0},{\"week_day\":7,\"int_start_hour\":\"17:00\",\"int_end_hour\":\"17:45\",\"cr_id\":6}],\"update_time\":1510995934}}',1510995934,1,1510995934,0,NULL,0),(26,0,7,3,'User 让 王二 加入了班级',20,'{\"cid\":7,\"sid\":20,\"sl_id\":23,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"12\"}',1511003930,1,1511003930,0,NULL,0),(27,0,6,3,'User 让 王二 加入了班级',20,'{\"cid\":6,\"sid\":20,\"sl_id\":24,\"in_way\":1,\"in_time\":\"2017-11-18 00:00:00\",\"bid\":\"2\",\"cs_id\":\"13\"}',1511003943,1,1511003943,0,NULL,0),(28,0,6,3,'User 让 刘子云01 加入了班级',1,'{\"cid\":6,\"sid\":1,\"sl_id\":29,\"in_way\":1,\"in_time\":\"2017-11-20 00:00:00\",\"bid\":\"2\",\"cs_id\":\"14\"}',1511150330,1,1511150330,0,NULL,0),(29,0,6,3,'User 让 刘子云02 加入了班级',2,'{\"cid\":6,\"sid\":2,\"sl_id\":30,\"in_way\":1,\"in_time\":\"2017-11-20 00:00:00\",\"bid\":\"2\",\"cs_id\":\"15\"}',1511150330,1,1511150330,0,NULL,0),(30,0,6,3,'User 让 刘子云03 加入了班级',3,'{\"cid\":6,\"sid\":3,\"sl_id\":31,\"in_way\":1,\"in_time\":\"2017-11-20 00:00:00\",\"bid\":\"2\",\"cs_id\":\"16\"}',1511150330,1,1511150330,0,NULL,0),(31,0,6,3,'User 让 刘子云04 加入了班级',4,'{\"cid\":6,\"sid\":4,\"sl_id\":32,\"in_way\":1,\"in_time\":\"2017-11-20 00:00:00\",\"bid\":\"2\",\"cs_id\":\"17\"}',1511150330,1,1511150330,0,NULL,0),(32,0,6,3,'User 让 刘子云05 加入了班级',8,'{\"cid\":6,\"sid\":8,\"sl_id\":33,\"in_way\":1,\"in_time\":\"2017-11-20 00:00:00\",\"bid\":\"2\",\"cs_id\":\"18\"}',1511150330,1,1511150330,0,NULL,0),(33,0,6,3,'User 让 刘子云06 加入了班级',9,'{\"cid\":6,\"sid\":9,\"sl_id\":34,\"in_way\":1,\"in_time\":\"2017-11-20 00:00:00\",\"bid\":\"2\",\"cs_id\":\"19\"}',1511150330,1,1511150330,0,NULL,0),(34,0,7,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":7,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test_class_warn\",\"class_no\":\"407\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10003,\"second_eid\":10002,\"edu_eid\":0,\"cr_id\":5,\"plan_student_nums\":12,\"student_nums\":3,\"nums_rate\":\"0.00\",\"lesson_times\":30,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":\"2017-11-18 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":123,\"schedule\":[{\"week_day\":2,\"int_start_hour\":\"01:15\",\"int_end_hour\":\"02:00\",\"cr_id\":6},{\"week_day\":6,\"int_start_hour\":\"11:30\",\"int_end_hour\":\"11:45\",\"cr_id\":5},{\"week_day\":4,\"int_start_hour\":\"04:45\",\"int_end_hour\":\"02:00\",\"cr_id\":4},{\"week_day\":5,\"int_start_hour\":\"04:45\",\"int_end_hour\":\"02:00\",\"cr_id\":5},{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"10:45\",\"cr_id\":5},{\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"11:15\",\"cr_id\":5}]},\"old\":[],\"changed_data\":{\"cid\":7,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test_class_warn\",\"class_no\":\"407\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10003,\"second_eid\":10002,\"edu_eid\":0,\"cr_id\":5,\"plan_student_nums\":12,\"student_nums\":3,\"nums_rate\":\"0.00\",\"lesson_times\":30,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"Q\",\"start_lesson_time\":1510934400,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":123,\"schedule\":[{\"week_day\":2,\"int_start_hour\":\"01:15\",\"int_end_hour\":\"02:00\",\"cr_id\":6},{\"week_day\":6,\"int_start_hour\":\"11:30\",\"int_end_hour\":\"11:45\",\"cr_id\":5},{\"week_day\":4,\"int_start_hour\":\"04:45\",\"int_end_hour\":\"02:00\",\"cr_id\":4},{\"week_day\":5,\"int_start_hour\":\"04:45\",\"int_end_hour\":\"02:00\",\"cr_id\":5},{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"10:45\",\"cr_id\":5},{\"week_day\":6,\"int_start_hour\":\"11:00\",\"int_end_hour\":\"11:15\",\"cr_id\":5}],\"update_time\":1511150471}}',1511150471,1,1511150471,0,NULL,0),(35,0,8,1,'User 创建了班级',NULL,'{\"class_name\":\"哎呀呀\",\"class_no\":\"0078\",\"year\":\"2017\",\"season\":\"C\",\"lid\":8,\"sj_id\":3,\"bid\":2,\"teach_eid\":10003,\"second_eid\":10002,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-20 00:00:00\",\"end_lesson_time\":\"2018-11-01 00:00:00\",\"plan_student_nums\":11,\"lesson_times\":3,\"cr_id\":0,\"schedule\":[],\"course_arrange\":0,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"8\"}',1511151853,1,1511151853,0,NULL,0),(36,0,9,1,'User 创建了班级',NULL,'{\"class_name\":\"考勤课耗测试cid-9\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"H\",\"lid\":9,\"sj_id\":4,\"bid\":2,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-21 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":10,\"lesson_times\":7,\"cr_id\":8,\"schedule\":[{\"week_day\":2,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":8},{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":8},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":8},{\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"cr_id\":8},{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":8},{\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":8},{\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":8}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"9\"}',1511257877,1,1511257877,0,NULL,0),(37,0,9,3,'User 让 学生26 加入了班级',26,'{\"cid\":9,\"sid\":26,\"sl_id\":37,\"in_way\":1,\"in_time\":\"2017-11-21 00:00:00\",\"bid\":\"2\",\"cs_id\":\"20\"}',1511257914,1,1511257914,0,NULL,0),(38,0,9,3,'User 让 学生27 加入了班级',27,'{\"cid\":9,\"sid\":27,\"sl_id\":38,\"in_way\":1,\"in_time\":\"2017-11-21 00:00:00\",\"bid\":\"2\",\"cs_id\":\"21\"}',1511257914,1,1511257914,0,NULL,0),(39,0,10,1,'User 创建了班级',NULL,'{\"class_name\":\"test\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"H\",\"lid\":1,\"sj_id\":1,\"bid\":2,\"teach_eid\":10003,\"second_eid\":0,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-21 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":6,\"lesson_times\":2,\"cr_id\":0,\"schedule\":[{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":0}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"10\"}',1511266386,1,1511266386,0,NULL,0),(40,0,10,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":10,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10003,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":0,\"plan_student_nums\":6,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":3,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-21 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":22,\"schedule\":[{\"csd_id\":66,\"og_id\":0,\"bid\":2,\"cid\":10,\"eid\":10003,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":0},{\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"cr_id\":0}]},\"old\":[],\"changed_data\":{\"cid\":10,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10003,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":0,\"plan_student_nums\":6,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":3,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1511193600,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":22,\"schedule\":[{\"csd_id\":66,\"og_id\":0,\"bid\":2,\"cid\":10,\"eid\":10003,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":0},{\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"cr_id\":0}],\"update_time\":1511339099}}',1511339099,1,1511339099,0,NULL,0),(41,0,10,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":10,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10003,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":0,\"plan_student_nums\":6,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":9,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-21 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":22,\"schedule\":[{\"csd_id\":66,\"og_id\":0,\"bid\":2,\"cid\":10,\"eid\":10003,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":0},{\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"cr_id\":0}]},\"old\":[],\"changed_data\":{\"cid\":10,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"test\",\"class_no\":\"\",\"lid\":1,\"sj_id\":1,\"teach_eid\":10003,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":0,\"plan_student_nums\":6,\"student_nums\":0,\"nums_rate\":\"0.00\",\"lesson_times\":9,\"lesson_index\":0,\"arrange_times\":1,\"attendance_times\":0,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1511193600,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":22,\"schedule\":[{\"csd_id\":66,\"og_id\":0,\"bid\":2,\"cid\":10,\"eid\":10003,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":0},{\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"cr_id\":0}],\"update_time\":1511339449}}',1511339449,1,1511339449,0,NULL,0),(42,0,11,1,'User 创建了班级',NULL,'{\"class_name\":\"赠送课次\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"H\",\"lid\":9,\"sj_id\":4,\"bid\":2,\"teach_eid\":1,\"second_eid\":0,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-22 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":5,\"lesson_times\":7,\"cr_id\":6,\"schedule\":[{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":6},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":6},{\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"cr_id\":6},{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":6},{\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":6},{\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":6}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"11\"}',1511345414,1,1511345414,0,NULL,0),(43,0,11,3,'User 让 学生29 加入了班级',29,'{\"cid\":11,\"sid\":29,\"sl_id\":44,\"in_way\":1,\"in_time\":\"2017-11-22 00:00:00\",\"bid\":\"2\",\"cs_id\":\"22\"}',1511345483,1,1511345483,0,NULL,0),(44,0,11,3,'User 让 学生30 加入了班级',30,'{\"cid\":11,\"sid\":30,\"sl_id\":43,\"in_way\":1,\"in_time\":\"2017-11-22 00:00:00\",\"bid\":\"2\",\"cs_id\":\"23\"}',1511345483,1,1511345483,0,NULL,0),(45,0,12,1,'User 创建了班级',NULL,'{\"class_name\":\"两课时班级\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"H\",\"lid\":5,\"sj_id\":4,\"bid\":2,\"teach_eid\":10003,\"second_eid\":10003,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-15 00:00:00\",\"end_lesson_time\":\"2017-12-29 00:00:00\",\"plan_student_nums\":10,\"lesson_times\":2,\"cr_id\":6,\"schedule\":[{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":6},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":6}],\"course_arrange\":1,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"12\"}',1511345979,1,1511345979,0,NULL,0),(46,0,12,3,'User 让 刘溜球 加入了班级',34,'{\"cid\":12,\"sid\":\"34\",\"sl_id\":45,\"in_way\":1,\"in_time\":\"2017-11-22 18:21:07\",\"bid\":\"2\",\"cs_id\":\"24\"}',1511346067,1,1511346067,0,NULL,0),(47,0,13,1,'User 创建了班级',NULL,'{\"class_name\":\"班课自有登记考勤测试\",\"class_no\":\"\",\"year\":\"2017\",\"season\":\"H\",\"lid\":9,\"sj_id\":4,\"bid\":2,\"teach_eid\":10005,\"second_eid\":0,\"edu_eid\":0,\"start_lesson_time\":\"2017-11-22 00:00:00\",\"end_lesson_time\":\"2017-11-30 00:00:00\",\"plan_student_nums\":5,\"lesson_times\":7,\"cr_id\":0,\"schedule\":[{\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":0},{\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":0},{\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":0},{\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":0},{\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"cr_id\":0},{\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"cr_id\":0}],\"course_arrange\":0,\"is_check\":1,\"exclude_holidays\":1,\"cid\":\"13\"}',1511354245,1,1511354245,0,NULL,0),(48,0,13,3,'User 让 yaorui36 加入了班级',36,'{\"cid\":13,\"sid\":36,\"sl_id\":47,\"in_way\":1,\"in_time\":\"2017-11-22 00:00:00\",\"bid\":\"2\",\"cs_id\":\"25\"}',1511354498,1,1511354498,0,NULL,0),(49,0,13,3,'User 让 yaorui35 加入了班级',35,'{\"cid\":13,\"sid\":35,\"sl_id\":46,\"in_way\":1,\"in_time\":\"2017-11-22 00:00:00\",\"bid\":\"2\",\"cs_id\":\"26\"}',1511354498,1,1511354498,0,NULL,0),(50,0,13,4,'User 让 yaorui35 退出了班级',35,'{\"cs_id\":26,\"og_id\":0,\"bid\":2,\"cid\":13,\"sid\":35,\"sl_id\":46,\"in_time\":\"2017-11-22 00:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1511524999,1,1511524999,0,NULL,0),(51,0,7,3,'User 让 杨过 加入了班级',41,'{\"cid\":7,\"sid\":41,\"sl_id\":63,\"in_way\":1,\"in_time\":\"2017-11-30 09:32:09\",\"bid\":\"2\",\"cs_id\":\"27\"}',1512005529,1,1512005529,0,NULL,0),(52,0,11,3,'User 让 德莱厄斯 加入了班级',43,'{\"cid\":11,\"sid\":43,\"sl_id\":68,\"in_way\":1,\"in_time\":\"2017-11-30 18:09:59\",\"bid\":\"2\",\"cs_id\":\"28\"}',1512036599,1,1512036599,0,NULL,0),(53,0,9,3,'User 让 测试1 加入了班级',49,'{\"cid\":9,\"sid\":49,\"sl_id\":78,\"in_way\":1,\"in_time\":\"2017-12-01 16:27:26\",\"bid\":\"2\",\"cs_id\":\"29\"}',1512116846,1,1512116846,0,NULL,0),(54,0,6,3,'User 让 测试1 加入了班级',49,'{\"cid\":6,\"sid\":49,\"sl_id\":80,\"in_way\":1,\"in_time\":\"2017-12-01 16:49:35\",\"bid\":\"2\",\"cs_id\":\"30\"}',1512118175,1,1512118175,0,NULL,0),(55,0,12,3,'User 让 德莱厄斯 加入了班级',43,'{\"sid\":43,\"cid\":12,\"sl_id\":68,\"bid\":\"2\",\"cs_id\":\"31\"}',1512121663,1,1512121663,0,NULL,0),(56,0,11,4,'User 让 德莱厄斯 退出了班级',43,'{\"cs_id\":28,\"og_id\":0,\"bid\":2,\"cid\":11,\"sid\":43,\"sl_id\":68,\"in_time\":\"2017-11-30 18:09:59\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512121663,1,1512121663,0,NULL,0),(57,0,11,3,'User 让 德莱厄斯 加入了班级',43,'{\"sid\":43,\"cid\":11,\"sl_id\":68,\"bid\":\"2\",\"cs_id\":\"32\"}',1512121738,1,1512121738,0,NULL,0),(58,0,12,4,'User 让 德莱厄斯 退出了班级',43,'{\"cs_id\":31,\"og_id\":0,\"bid\":2,\"cid\":12,\"sid\":43,\"sl_id\":68,\"in_time\":\"1970-01-01 08:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512121738,1,1512121738,0,NULL,0),(59,0,12,3,'User 让 德莱厄斯 加入了班级',43,'{\"sid\":43,\"cid\":12,\"sl_id\":68,\"bid\":\"2\",\"cs_id\":\"33\"}',1512121779,1,1512121779,0,NULL,0),(60,0,11,4,'User 让 德莱厄斯 退出了班级',43,'{\"cs_id\":32,\"og_id\":0,\"bid\":2,\"cid\":11,\"sid\":43,\"sl_id\":68,\"in_time\":\"1970-01-01 08:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512121779,1,1512121779,0,NULL,0),(61,0,13,3,'User 让 德莱厄斯 加入了班级',43,'{\"sid\":43,\"cid\":13,\"sl_id\":68,\"bid\":\"2\",\"cs_id\":\"34\"}',1512121790,1,1512121790,0,NULL,0),(62,0,12,4,'User 让 德莱厄斯 退出了班级',43,'{\"cs_id\":33,\"og_id\":0,\"bid\":2,\"cid\":12,\"sid\":43,\"sl_id\":68,\"in_time\":\"1970-01-01 08:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512121790,1,1512121790,0,NULL,0),(63,0,11,3,'User 让 德莱厄斯 加入了班级',43,'{\"sid\":43,\"cid\":11,\"sl_id\":68,\"bid\":\"2\",\"cs_id\":\"35\"}',1512121801,1,1512121801,0,NULL,0),(64,0,13,4,'User 让 德莱厄斯 退出了班级',43,'{\"cs_id\":34,\"og_id\":0,\"bid\":2,\"cid\":13,\"sid\":43,\"sl_id\":68,\"in_time\":\"1970-01-01 08:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512121801,1,1512121801,0,NULL,0),(65,0,12,3,'User 让 yaorui001 加入了班级',5,'{\"cid\":12,\"sid\":\"5\",\"sl_id\":82,\"in_way\":1,\"in_time\":\"2017-12-04 09:25:35\",\"bid\":\"4\",\"cs_id\":\"36\"}',1512350735,1,1512350735,0,NULL,0),(66,0,13,3,'User 让 德莱厄斯 加入了班级',43,'{\"sid\":43,\"cid\":13,\"sl_id\":68,\"bid\":\"2\",\"cs_id\":\"37\"}',1512441468,1,1512441468,0,NULL,0),(67,0,11,4,'User 让 德莱厄斯 退出了班级',43,'{\"cs_id\":35,\"og_id\":0,\"bid\":2,\"cid\":11,\"sid\":43,\"sl_id\":68,\"in_time\":\"1970-01-01 08:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512441468,1,1512441468,0,NULL,0),(68,0,13,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":5,\"student_nums\":1,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":6,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-22\",\"end_lesson_time\":\"2017-11-30\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":51,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":83,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":5,\"student_nums\":1,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":6,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1511280000,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":51,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":83,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":0,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1512545391}}',1512545391,1,1512545391,0,NULL,0),(69,0,13,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":5,\"student_nums\":1,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":6,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-22\",\"end_lesson_time\":\"2017-11-30\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":67,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":2},{\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"cr_id\":2},{\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"cr_id\":2}]},\"old\":[],\"changed_data\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":5,\"student_nums\":1,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":0,\"attendance_times\":6,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1511280000,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":67,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"cr_id\":2},{\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"cr_id\":2},{\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"cr_id\":2}],\"update_time\":1512549843}}',1512549843,1,1512549843,0,NULL,0),(70,0,12,3,'User 让 德莱厄斯 加入了班级',43,'{\"cid\":12,\"sid\":43,\"sl_id\":103,\"in_way\":1,\"in_time\":\"2017-12-07 00:00:00\",\"bid\":\"2\",\"cs_id\":\"38\"}',1512615429,1,1512615429,0,NULL,0),(71,0,12,3,'User 让 德莱厄斯 加入了班级',43,'{\"cid\":12,\"sid\":43,\"sl_id\":103,\"in_way\":1,\"in_time\":\"2017-12-07 00:00:00\",\"bid\":\"2\",\"cs_id\":\"39\"}',1512615429,1,1512615429,0,NULL,0),(72,0,14,1,'User 创建了班级',NULL,'{\"week_day\":1,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"lid\":5,\"sj_id\":2,\"sj_ids\":[],\"teach_eid\":10003,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":5,\"class_name\":\"艺术课课时包2017寒假周一19:00~21:30\",\"class_no\":\"ART00117H-1-19002130\",\"year\":\"2017\",\"season\":\"H\",\"start_lesson_time\":\"2017-12-07\",\"end_lesson_time\":\"2018-02-16\",\"lesson_times\":15,\"plan_student_nums\":40,\"bid\":3,\"cid\":\"14\"}',1512629950,1,1512629950,0,NULL,0),(73,0,13,4,'User 让 德莱厄斯 退出了班级',43,'{\"cs_id\":37,\"og_id\":0,\"bid\":2,\"cid\":13,\"sid\":43,\"sl_id\":68,\"in_time\":\"1970-01-01 08:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512635364,1,1512635364,0,NULL,0),(74,0,11,3,'User 让 老李 加入了班级',56,'{\"cid\":11,\"sid\":56,\"sl_id\":110,\"in_way\":1,\"in_time\":\"2017-12-07 00:00:00\",\"bid\":\"2\",\"cs_id\":\"40\"}',1512636896,1,1512636896,0,NULL,0),(76,0,12,3,'User 让 老李 加入了班级',56,'{\"sid\":56,\"cid\":12,\"sl_id\":110,\"bid\":\"2\",\"cs_id\":\"41\"}',1512701023,1,1512701023,0,NULL,0),(77,0,11,4,'User 让 老李 退出了班级',56,'{\"cs_id\":40,\"og_id\":0,\"bid\":2,\"cid\":11,\"sid\":56,\"sl_id\":110,\"in_time\":\"2017-12-07 00:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512701023,1,1512701023,0,NULL,0),(78,0,13,3,'User 让 打印学员04 加入了班级',61,'{\"cid\":13,\"sid\":61,\"sl_id\":111,\"in_way\":1,\"in_time\":\"2017-12-08 00:00:00\",\"bid\":\"2\",\"cs_id\":\"42\"}',1512702013,1,1512702013,0,NULL,0),(79,0,12,4,'User 让 德莱厄斯 退出了班级',43,'{\"cs_id\":38,\"og_id\":0,\"bid\":2,\"cid\":12,\"sid\":43,\"sl_id\":103,\"in_time\":\"2017-12-07 00:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512702354,1,1512702354,0,NULL,0),(80,0,12,4,'User 让 德莱厄斯 退出了班级',43,'{\"cs_id\":39,\"og_id\":0,\"bid\":2,\"cid\":12,\"sid\":43,\"sl_id\":103,\"in_time\":\"2017-12-07 00:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512702358,1,1512702358,0,NULL,0),(81,0,9,4,'User 让 测试11 退出了班级',49,'{\"cs_id\":29,\"og_id\":0,\"bid\":2,\"cid\":9,\"sid\":49,\"sl_id\":78,\"in_time\":\"2017-12-01 16:27:26\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512702494,1,1512702494,0,NULL,0),(82,0,6,3,'User 让 teststs 加入了班级',70,'{\"cid\":6,\"sid\":70,\"sl_id\":116,\"in_way\":1,\"in_time\":\"2017-12-08 00:00:00\",\"bid\":\"2\",\"cs_id\":\"43\"}',1512716251,1,1512716251,0,NULL,0),(83,0,1,3,'User 让 teststs 加入了班级',70,'{\"cid\":1,\"sid\":70,\"sl_id\":117,\"in_way\":1,\"in_time\":\"2017-12-08 15:33:02\",\"bid\":\"2\",\"cs_id\":\"44\"}',1512718382,1,1512718382,0,NULL,0),(84,0,11,3,'User 让 teststs 加入了班级',70,'{\"cid\":11,\"sid\":70,\"sl_id\":118,\"in_way\":1,\"in_time\":\"2017-12-08 15:39:09\",\"bid\":\"2\",\"cs_id\":\"45\"}',1512718749,1,1512718749,0,NULL,0),(85,0,13,4,'User 让 打印学员04 退出了班级',61,'{\"cs_id\":42,\"og_id\":0,\"bid\":2,\"cid\":13,\"sid\":61,\"sl_id\":111,\"in_time\":\"2017-12-08 00:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1512724091,1,1512724091,0,NULL,0),(86,0,13,3,'User 让 德莱厄斯 加入了班级',43,'{\"cid\":13,\"sid\":43,\"sl_id\":68,\"in_way\":1,\"in_time\":\"2017-12-12 00:00:00\",\"bid\":\"2\",\"cs_id\":\"46\"}',1513048704,1,1513048704,0,NULL,0),(87,0,13,3,'User 让 teststs 加入了班级',70,'{\"cid\":13,\"sid\":70,\"sl_id\":118,\"in_way\":1,\"in_time\":\"2017-12-12 00:00:00\",\"bid\":\"2\",\"cs_id\":\"47\"}',1513050857,1,1513050857,0,NULL,0),(88,0,13,3,'User 让 lisi22 加入了班级',69,'{\"cid\":13,\"sid\":69,\"sl_id\":120,\"in_way\":1,\"in_time\":\"2017-12-12 00:00:00\",\"bid\":\"2\",\"cs_id\":\"48\"}',1513050857,1,1513050857,0,NULL,0),(89,0,13,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":6,\"student_nums\":3,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":6,\"attendance_times\":5,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-22\",\"end_lesson_time\":\"2017-11-30\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":6,\"student_nums\":3,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":6,\"attendance_times\":5,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1511280000,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":1,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1513052050}}',1513052050,1,1513052050,0,NULL,0),(90,0,13,3,'User 让 测试学员11 加入了班级',72,'{\"cid\":13,\"sid\":72,\"sl_id\":122,\"in_way\":1,\"in_time\":\"2017-12-12 00:00:00\",\"bid\":\"2\",\"cs_id\":\"49\"}',1513058936,1,1513058936,0,NULL,0),(91,0,13,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":7,\"student_nums\":4,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":6,\"attendance_times\":5,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-22\",\"end_lesson_time\":\"2017-11-30\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":7,\"student_nums\":4,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":6,\"attendance_times\":5,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1511280000,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1513059689}}',1513059689,1,1513059689,0,NULL,0),(92,0,13,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":6,\"student_nums\":4,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":6,\"attendance_times\":5,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-22\",\"end_lesson_time\":\"2017-11-30\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":6,\"student_nums\":4,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":6,\"attendance_times\":5,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1511280000,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1513059933}}',1513059933,1,1513059933,0,NULL,0),(93,0,13,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":7,\"student_nums\":4,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":6,\"attendance_times\":5,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-22\",\"end_lesson_time\":\"2017-11-30\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":7,\"student_nums\":4,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":6,\"attendance_times\":5,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1511280000,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1513059970}}',1513059970,1,1513059970,0,NULL,0),(94,0,6,3,'User 让 测试学员11 加入了班级',72,'{\"cid\":6,\"sid\":72,\"sl_id\":125,\"in_way\":1,\"in_time\":\"2017-12-12 16:38:20\",\"bid\":\"2\",\"cs_id\":\"50\"}',1513067900,1,1513067900,0,NULL,0),(95,0,1,3,'User 让 测试学员11 加入了班级',72,'{\"cid\":1,\"sid\":72,\"sl_id\":124,\"in_way\":1,\"in_time\":\"2017-12-12 00:00:00\",\"bid\":\"2\",\"cs_id\":\"51\"}',1513068837,1,1513068837,0,NULL,0),(96,0,1,3,'User 让 独孤求败 加入了班级',71,'{\"cid\":1,\"sid\":71,\"sl_id\":126,\"in_way\":1,\"in_time\":\"2017-12-12 00:00:00\",\"bid\":\"2\",\"cs_id\":\"52\"}',1513068924,1,1513068924,0,NULL,0),(97,0,8,3,'User 让 独孤求败 加入了班级',71,'{\"cid\":8,\"sid\":71,\"sl_id\":127,\"in_way\":1,\"in_time\":\"2017-12-12 17:49:05\",\"bid\":\"2\",\"cs_id\":\"53\"}',1513072145,1,1513072145,0,NULL,0),(98,0,6,3,'User 让 测试学员12 加入了班级',74,'{\"cid\":6,\"sid\":\"74\",\"sl_id\":129,\"in_way\":1,\"in_time\":\"2017-12-12 17:50:02\",\"bid\":\"2\",\"cs_id\":\"54\"}',1513072202,1,1513072202,0,NULL,0),(99,0,8,3,'User 让 测试学员12 加入了班级',74,'{\"cid\":8,\"sid\":74,\"sl_id\":130,\"in_way\":1,\"in_time\":\"2017-12-12 17:50:48\",\"bid\":\"2\",\"cs_id\":\"55\"}',1513072248,1,1513072248,0,NULL,0),(100,0,1,4,'User 让 yaorui001 退出了班级',5,'{\"cs_id\":1,\"og_id\":0,\"bid\":2,\"cid\":1,\"sid\":5,\"sl_id\":6,\"in_time\":\"2017-11-18 00:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1513072806,1,1513072806,0,NULL,0),(101,0,1,4,'User 让 yaorui002 退出了班级',6,'{\"cs_id\":2,\"og_id\":0,\"bid\":2,\"cid\":1,\"sid\":6,\"sl_id\":7,\"in_time\":\"2017-11-18 00:00:00\",\"out_time\":0,\"in_way\":1,\"status\":1}',1513072809,1,1513072809,0,NULL,0),(102,0,13,5,'User 修改班级状态为 ',NULL,'{\"new\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":5,\"student_nums\":4,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":18,\"attendance_times\":5,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":\"2017-11-22\",\"end_lesson_time\":\"2017-11-30\",\"status\":0,\"ext_id\":\"\",\"int_start_hour\":\"0\",\"int_end_hour\":\"0\",\"_index\":0,\"_rowKey\":11,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}]},\"old\":[],\"changed_data\":{\"cid\":13,\"og_id\":0,\"parent_cid\":0,\"bid\":2,\"class_name\":\"\\u73ed\\u8bfe\\u81ea\\u6709\\u767b\\u8bb0\\u8003\\u52e4\\u6d4b\\u8bd5\",\"class_no\":\"\",\"lid\":9,\"sj_id\":4,\"teach_eid\":10004,\"second_eid\":0,\"edu_eid\":0,\"cr_id\":2,\"plan_student_nums\":5,\"student_nums\":4,\"nums_rate\":\"0.00\",\"lesson_times\":6,\"lesson_index\":0,\"arrange_times\":18,\"attendance_times\":5,\"year\":2017,\"season\":\"H\",\"start_lesson_time\":1511280000,\"end_lesson_time\":1511971200,\"status\":0,\"ext_id\":\"\",\"int_start_hour\":0,\"int_end_hour\":0,\"_index\":0,\"_rowKey\":11,\"schedule\":[{\"csd_id\":78,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":3,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":79,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":4,\"int_start_hour\":\"19:00\",\"int_end_hour\":\"21:30\",\"delete_uid\":0},{\"csd_id\":80,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":5,\"int_start_hour\":\"05:45\",\"int_end_hour\":\"08:15\",\"delete_uid\":0},{\"csd_id\":81,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":82,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":7,\"int_start_hour\":\"10:30\",\"int_end_hour\":\"12:30\",\"delete_uid\":0},{\"csd_id\":84,\"og_id\":0,\"bid\":2,\"cid\":13,\"eid\":10005,\"cr_id\":2,\"year\":2017,\"season\":\"H\",\"week_day\":6,\"int_start_hour\":\"08:00\",\"int_end_hour\":\"10:00\",\"delete_uid\":0}],\"update_time\":1513072927}}',1513072927,1,1513072927,0,NULL,0),(103,0,8,3,'User 让 测试学员16 加入了班级',76,'{\"cid\":8,\"sid\":76,\"sl_id\":132,\"in_way\":1,\"in_time\":\"2017-12-12 18:16:59\",\"bid\":\"2\",\"cs_id\":\"56\"}',1513073819,1,1513073819,0,NULL,0),(104,0,8,3,'User 让 测试学员11 加入了班级',72,'{\"cid\":8,\"sid\":72,\"sl_id\":123,\"in_way\":1,\"in_time\":\"2017-12-12 19:45:35\",\"bid\":\"2\",\"cs_id\":\"57\"}',1513079135,1,1513079135,0,NULL,0),(105,0,8,3,'User 让 测试分班1 加入了班级',77,'{\"cid\":8,\"sid\":77,\"sl_id\":133,\"in_way\":1,\"in_time\":\"2017-12-12 20:02:17\",\"bid\":\"2\",\"cs_id\":\"58\"}',1513080137,1,1513080137,0,NULL,0),(106,0,11,3,'User 让 测试分班1 加入了班级',77,'{\"cid\":11,\"sid\":77,\"sl_id\":134,\"in_way\":1,\"in_time\":\"2017-12-12 20:02:17\",\"bid\":\"2\",\"cs_id\":\"59\"}',1513080137,1,1513080137,0,NULL,0),(112,0,2,3,'User 让 测试分班2 加入了班级',78,'{\"cid\":2,\"sid\":78,\"sl_id\":135,\"in_way\":1,\"in_time\":\"2017-12-12 20:33:55\",\"bid\":\"2\",\"cs_id\":\"65\"}',1513082035,1,1513082035,0,NULL,0),(113,0,8,3,'User 让 打印学员08 加入了班级',65,'{\"cid\":8,\"sid\":65,\"sl_id\":138,\"in_way\":1,\"in_time\":\"2017-12-13 00:00:00\",\"bid\":\"2\",\"cs_id\":\"66\"}',1513132440,1,1513132440,0,NULL,0),(114,0,8,3,'User 让 打印学员07 加入了班级',64,'{\"cid\":8,\"sid\":64,\"sl_id\":139,\"in_way\":1,\"in_time\":\"2017-12-13 00:00:00\",\"bid\":\"2\",\"cs_id\":\"67\"}',1513132441,1,1513132441,0,NULL,0),(115,0,8,3,'User 让 打印学员06 加入了班级',63,'{\"cid\":8,\"sid\":63,\"sl_id\":140,\"in_way\":1,\"in_time\":\"2017-12-13 00:00:00\",\"bid\":\"2\",\"cs_id\":\"68\"}',1513132441,1,1513132441,0,NULL,0),(116,0,8,3,'User 让 打印学员05 加入了班级',62,'{\"cid\":8,\"sid\":62,\"sl_id\":141,\"in_way\":1,\"in_time\":\"2017-12-13 00:00:00\",\"bid\":\"2\",\"cs_id\":\"69\"}',1513132441,1,1513132441,0,NULL,0),(117,0,8,3,'User 让 打印学员04 加入了班级',61,'{\"cid\":8,\"sid\":61,\"sl_id\":142,\"in_way\":1,\"in_time\":\"2017-12-13 00:00:00\",\"bid\":\"2\",\"cs_id\":\"70\"}',1513132441,1,1513132441,0,NULL,0),(118,0,8,3,'User 让 打印学员03 加入了班级',60,'{\"cid\":8,\"sid\":60,\"sl_id\":143,\"in_way\":1,\"in_time\":\"2017-12-13 00:00:00\",\"bid\":\"2\",\"cs_id\":\"71\"}',1513132441,1,1513132441,0,NULL,0),(119,0,8,3,'User 让 测试分班2 加入了班级',78,'{\"cid\":8,\"sid\":78,\"sl_id\":137,\"in_way\":1,\"in_time\":\"2017-12-13 10:48:58\",\"bid\":\"2\",\"cs_id\":\"72\"}',1513133338,1,1513133338,0,NULL,0),(120,0,2,3,'User 让 测试分班3 加入了班级',79,'{\"cid\":2,\"sid\":79,\"sl_id\":145,\"in_way\":1,\"in_time\":\"2017-12-13 11:06:08\",\"bid\":\"2\",\"cs_id\":\"73\"}',1513134368,1,1513134368,0,NULL,0),(121,0,7,3,'User 让 学生001 加入了班级',82,'{\"cid\":7,\"sid\":82,\"sl_id\":148,\"in_way\":1,\"in_time\":\"2017-12-21 00:00:00\",\"og_id\":0,\"bid\":\"2\",\"cs_id\":\"74\"}',1513820982,1,1513820982,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COMMENT='排班计划表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class_schedule`
--

LOCK TABLES `x360p_class_schedule` WRITE;
/*!40000 ALTER TABLE `x360p_class_schedule` DISABLE KEYS */;
INSERT INTO `x360p_class_schedule` VALUES (1,0,2,1,10002,3,2017,'H',6,1030,1230,1510972292,1,0,0,NULL,1510972292),(2,0,2,1,10002,3,2017,'H',6,800,1000,1510972292,1,0,0,NULL,1510972292),(3,0,2,1,10002,3,2017,'H',7,800,1000,1510972292,1,0,0,NULL,1510972292),(4,0,2,1,10002,3,2017,'H',7,1030,1230,1510972292,1,0,0,NULL,1510972292),(5,0,2,2,1,0,2017,'S',6,1200,1215,1510972355,1,0,0,NULL,1510972355),(6,0,2,2,1,0,2017,'S',6,1145,1200,1510972355,1,0,0,NULL,1510972355),(7,0,2,2,1,0,2017,'S',6,1130,1145,1510972355,1,0,0,NULL,1510972355),(8,0,2,2,1,0,2017,'S',6,1115,1130,1510972355,1,0,0,NULL,1510972355),(9,0,2,2,1,0,2017,'S',6,1100,1115,1510972355,1,0,0,NULL,1510972355),(10,0,2,2,1,0,2017,'S',6,1030,1045,1510972355,1,0,0,NULL,1510972355),(11,0,3,3,10003,8,2017,'Q',1,1900,2100,1510993008,1,0,0,NULL,1510993008),(12,0,3,3,10003,8,2017,'Q',2,1900,2100,1510993008,1,0,0,NULL,1510993008),(13,0,3,3,10003,8,2017,'Q',3,1900,2100,1510993008,1,0,0,NULL,1510993008),(14,0,3,3,10003,8,2017,'Q',4,1900,2100,1510993008,1,0,0,NULL,1510993008),(15,0,3,3,10003,8,2017,'Q',5,1900,2100,1510993008,1,0,0,NULL,1510993008),(16,0,3,3,10003,8,2017,'Q',6,1030,1230,1510993008,1,0,0,NULL,1510993008),(17,0,3,3,10003,8,2017,'Q',7,1030,1230,1510993008,1,0,0,NULL,1510993008),(18,0,3,3,10003,8,2017,'Q',6,800,1000,1510993008,1,0,0,NULL,1510993008),(19,0,3,3,10003,8,2017,'Q',7,800,1000,1510993008,1,0,0,NULL,1510993008),(38,0,2,6,0,7,2017,'H',6,1030,1230,1510994475,1,0,0,NULL,1510994475),(39,0,2,6,0,7,2017,'H',7,1030,1230,1510994475,1,0,0,NULL,1510994475),(40,0,2,6,0,7,2017,'H',5,1900,2130,1510994475,1,0,0,NULL,1510994475),(41,0,2,6,0,7,2017,'H',4,1900,2130,1510994475,1,0,0,NULL,1510994475),(42,0,2,6,0,7,2017,'H',3,1900,2130,1510994475,1,0,0,NULL,1510994475),(43,0,2,6,0,7,2017,'H',2,1900,2130,1510994475,1,0,0,NULL,1510994475),(44,0,2,6,0,7,2017,'H',1,1900,2130,1510994475,1,0,0,NULL,1510994475),(45,0,2,6,0,7,2017,'H',6,800,1000,1510994475,1,0,0,NULL,1510994475),(46,0,2,6,0,7,2017,'H',7,800,1000,1510994475,1,0,0,NULL,1510994475),(51,0,2,7,10003,6,2017,'Q',2,115,200,1511150471,1,0,0,NULL,1511150471),(52,0,2,7,10003,5,2017,'Q',6,1130,1145,1511150471,1,0,0,NULL,1511150471),(53,0,2,7,10003,4,2017,'Q',4,445,200,1511150471,1,0,0,NULL,1511150471),(54,0,2,7,10003,5,2017,'Q',5,445,200,1511150471,1,0,0,NULL,1511150471),(55,0,2,7,10003,5,2017,'Q',6,1030,1045,1511150471,1,0,0,NULL,1511150471),(56,0,2,7,10003,5,2017,'Q',6,1100,1115,1511150471,1,0,0,NULL,1511150471),(57,0,2,1,10002,3,2017,'H',2,1900,2130,1511150696,1,0,0,NULL,1511150696),(58,0,2,1,10002,3,2017,'H',1,545,815,1511150890,1,0,0,NULL,1511150890),(59,0,2,9,10004,8,2017,'H',2,1900,2130,1511257877,1,1,1,1511259998,1511259998),(60,0,2,9,10004,8,2017,'H',3,1900,2130,1511257877,1,0,0,NULL,1511257877),(61,0,2,9,10004,8,2017,'H',4,1900,2130,1511257877,1,0,0,NULL,1511257877),(62,0,2,9,10004,8,2017,'H',5,545,815,1511257877,1,0,0,NULL,1511257877),(63,0,2,9,10004,8,2017,'H',6,1030,1230,1511257877,1,0,0,NULL,1511257877),(64,0,2,9,10004,8,2017,'H',7,1030,1230,1511257877,1,0,0,NULL,1511257877),(65,0,2,9,10004,8,2017,'H',5,1900,2130,1511257877,1,0,0,NULL,1511257877),(66,0,2,10,10003,2,2017,'H',3,1900,2130,1511266386,1,0,0,NULL,1511266386),(67,0,2,10,10003,0,2017,'H',4,1900,2130,1511339099,1,1,1,1512629045,1512629045),(68,0,2,10,10003,0,2017,'H',5,545,815,1511339099,1,0,0,NULL,1511339099),(69,0,2,1,10002,3,2017,'H',1,1900,2130,1511339650,1,0,0,NULL,1511339650),(70,0,2,11,1,6,2017,'H',3,1900,2130,1511345414,1,0,0,NULL,1511345414),(71,0,2,11,1,6,2017,'H',4,1900,2130,1511345414,1,0,0,NULL,1511345414),(72,0,2,11,1,6,2017,'H',5,545,815,1511345414,1,0,0,NULL,1511345414),(73,0,2,11,1,6,2017,'H',6,1030,1230,1511345414,1,0,0,NULL,1511345414),(74,0,2,11,1,6,2017,'H',7,1030,1230,1511345414,1,0,0,NULL,1511345414),(75,0,2,11,1,6,2017,'H',5,1900,2130,1511345414,1,0,0,NULL,1511345414),(76,0,2,12,10003,6,2017,'H',3,1900,2130,1511345979,1,0,0,NULL,1511345979),(77,0,2,12,10003,6,2017,'H',4,1900,2130,1511345979,1,0,0,NULL,1511345979),(78,0,2,13,10005,2,2017,'H',3,1900,2130,1511354245,1,0,0,NULL,1511354245),(79,0,2,13,10005,2,2017,'H',4,1900,2130,1511354245,1,0,0,NULL,1511354245),(80,0,2,13,10005,2,2017,'H',5,545,815,1511354245,1,0,0,NULL,1511354245),(81,0,2,13,10005,2,2017,'H',6,1030,1230,1511354245,1,0,0,NULL,1511354245),(82,0,2,13,10005,2,2017,'H',7,1030,1230,1511354245,1,0,0,NULL,1511354245),(84,0,2,13,10005,2,2017,'H',6,800,1000,1511354245,1,0,0,NULL,1511354245),(85,0,3,14,10003,5,2017,'H',1,1900,2130,1512629950,1,0,0,NULL,1512629950);
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
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COMMENT='班级学生表（记录每个班级里面有哪些学生)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_class_student`
--

LOCK TABLES `x360p_class_student` WRITE;
/*!40000 ALTER TABLE `x360p_class_student` DISABLE KEYS */;
INSERT INTO `x360p_class_student` VALUES (3,0,2,1,7,8,1510934400,0,1,1,1510972332,1,1510972332,0,NULL,0),(4,0,2,1,8,5,1510934400,0,1,1,1510972458,1,1510972458,0,NULL,0),(5,0,2,1,9,9,1510934400,0,1,1,1510972458,1,1510972458,0,NULL,0),(6,0,2,1,4,4,1510934400,0,1,1,1510972458,1,1510972458,0,NULL,0),(7,0,2,1,3,3,1510934400,0,1,1,1510972458,1,1510972458,0,NULL,0),(8,0,2,1,2,2,1510934400,0,1,1,1510972458,1,1510972458,0,NULL,0),(9,0,2,1,1,1,1510934400,0,1,1,1510972458,1,1510972458,0,NULL,0),(10,0,2,7,15,18,1510934400,0,1,1,1510995565,1,1510995565,0,NULL,0),(11,0,2,7,13,19,1510934400,0,1,1,1510995565,1,1510995565,0,NULL,0),(12,0,2,7,20,23,1510934400,0,1,1,1511003930,1,1511003930,0,NULL,0),(13,0,2,6,20,24,1510934400,0,1,1,1511003943,1,1511003943,0,NULL,0),(14,0,2,6,1,29,1511107200,0,1,1,1511150330,1,1511150330,0,NULL,0),(15,0,2,6,2,30,1511107200,0,1,1,1511150330,1,1511150330,0,NULL,0),(16,0,2,6,3,31,1511107200,0,1,1,1511150330,1,1511150330,0,NULL,0),(17,0,2,6,4,32,1511107200,0,1,1,1511150330,1,1511150330,0,NULL,0),(18,0,2,6,8,33,1511107200,0,1,1,1511150330,1,1511150330,0,NULL,0),(19,0,2,6,9,34,1511107200,0,1,1,1511150330,1,1511150330,0,NULL,0),(20,0,2,9,26,37,1511193600,0,1,1,1511257914,1,1511257914,0,NULL,0),(21,0,2,9,27,38,1511193600,0,1,1,1511257914,1,1511257914,0,NULL,0),(22,0,2,11,29,44,1511280000,0,1,1,1511345483,1,1511345483,0,NULL,0),(23,0,2,11,30,43,1511280000,0,1,1,1511345483,1,1511345483,0,NULL,0),(24,0,2,12,34,45,1511346067,0,1,1,1511346067,1,1511346067,0,NULL,0),(25,0,2,13,36,47,1511280000,0,1,1,1511354498,1,1511354498,0,NULL,0),(27,0,2,7,41,63,1512005529,0,1,1,1512005529,1,1512005529,0,NULL,0),(28,0,2,11,43,68,1512036599,0,1,1,1512036599,1,1512121663,1,1512121663,1),(30,0,2,6,49,80,1512118175,0,1,1,1512118175,1,1512118175,0,NULL,0),(31,0,2,12,43,68,0,0,1,1,1512121663,1,1512121738,1,1512121738,1),(32,0,2,11,43,68,0,0,1,1,1512121738,1,1512121779,1,1512121779,1),(33,0,2,12,43,68,0,0,1,1,1512121779,1,1512121790,1,1512121790,1),(34,0,2,13,43,68,0,0,1,1,1512121790,1,1512121801,1,1512121801,1),(35,0,2,11,43,68,0,0,1,1,1512121801,1,1512441468,1,1512441468,1),(36,0,4,12,5,82,1512350735,0,1,1,1512350735,1,1512350735,0,NULL,0),(40,0,2,11,56,110,1512576000,0,1,1,1512636896,1,1512701023,1,1512701023,1),(41,0,2,12,56,110,0,0,1,1,1512701023,1,1512701023,0,NULL,0),(43,0,2,6,70,116,1512662400,0,1,1,1512716251,1,1512716251,0,NULL,0),(44,0,2,1,70,117,1512718382,0,1,1,1512718382,1,1512718382,0,NULL,0),(45,0,2,11,70,118,1512718749,0,1,1,1512718749,1,1512718749,0,NULL,0),(46,0,2,13,43,68,1513008000,0,1,1,1513048704,1,1513048704,0,NULL,0),(47,0,2,13,70,118,1513008000,0,1,1,1513050857,1,1513050857,0,NULL,0),(48,0,2,13,69,120,1513008000,0,1,1,1513050857,1,1513050857,0,NULL,0),(49,0,2,13,72,122,1513008000,0,1,1,1513058936,1,1513058936,0,NULL,0),(50,0,2,6,72,125,1513067900,0,1,1,1513067900,1,1513067900,0,NULL,0),(51,0,2,1,72,124,1513008000,0,1,1,1513068837,1,1513068837,0,NULL,0),(52,0,2,1,71,126,1513008000,0,1,1,1513068924,1,1513068924,0,NULL,0),(53,0,2,8,71,127,1513072145,0,1,1,1513072145,1,1513072145,0,NULL,0),(54,0,2,6,74,129,1513072202,0,1,1,1513072202,1,1513072202,0,NULL,0),(55,0,2,8,74,130,1513072248,0,1,1,1513072248,1,1513072248,0,NULL,0),(56,0,2,8,76,132,1513073819,0,1,1,1513073819,1,1513073819,0,NULL,0),(57,0,2,8,72,123,1513079135,0,1,1,1513079135,1,1513079135,0,NULL,0),(58,0,2,8,77,133,1513080137,0,1,1,1513080137,1,1513080137,0,NULL,0),(59,0,2,11,77,134,1513080137,0,1,1,1513080137,1,1513080137,0,NULL,0),(65,0,2,2,78,135,1513082035,0,1,1,1513082035,1,1513082035,0,NULL,0),(66,0,2,8,65,138,1513094400,0,1,1,1513132440,1,1513132440,0,NULL,0),(67,0,2,8,64,139,1513094400,0,1,1,1513132441,1,1513132441,0,NULL,0),(68,0,2,8,63,140,1513094400,0,1,1,1513132441,1,1513132441,0,NULL,0),(69,0,2,8,62,141,1513094400,0,1,1,1513132441,1,1513132441,0,NULL,0),(70,0,2,8,61,142,1513094400,0,1,1,1513132441,1,1513132441,0,NULL,0),(71,0,2,8,60,143,1513094400,0,1,1,1513132441,1,1513132441,0,NULL,0),(72,0,2,8,78,137,1513133338,0,1,1,1513133338,1,1513133338,0,NULL,0),(73,0,2,2,79,145,1513134368,0,1,1,1513134368,1,1513134368,0,NULL,0),(74,0,2,7,82,148,1513785600,0,1,1,1513820982,1,1513820982,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='教室表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_classroom`
--

LOCK TABLES `x360p_classroom` WRITE;
/*!40000 ALTER TABLE `x360p_classroom` DISABLE KEYS */;
INSERT INTO `x360p_classroom` VALUES (2,0,35,'东区102',40,0,5,NULL,1510971845,1,1510971845,0,NULL,0),(3,0,35,'东区103',40,0,5,NULL,1510971851,1,1510971851,0,NULL,0),(4,0,3,'东区101',40,0,5,NULL,1510971962,1,1510971962,0,NULL,0),(5,0,3,'东区102',40,0,5,NULL,1510971968,1,1510971968,0,NULL,0),(6,0,3,'东区103',40,0,5,NULL,1510971975,1,1510971975,0,NULL,0),(7,0,3,'东区104',40,0,5,NULL,1510971979,1,1510971979,0,NULL,0),(8,0,3,'东区105',40,0,5,NULL,1510971983,1,1510971983,0,NULL,0),(9,0,2,'A101',16,0,0,NULL,1510972338,1,1510972338,0,NULL,0),(10,0,2,'A123',20,0,8,'[[1,1,1,1,1],[1,1,1,1,1],[1,1,1,1,1],[1,1,1,1,1]]',1511160894,1,1511160941,0,NULL,0),(11,0,2,'46肉',16,0,8,'[[1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1],[1,1,1,1,1,1,1,1,1]]',1511343025,1,1513149990,1,1511343036,1);
/*!40000 ALTER TABLE `x360p_classroom` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表(KV结构)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_config`
--

LOCK TABLES `x360p_config` WRITE;
/*!40000 ALTER TABLE `x360p_config` DISABLE KEYS */;
INSERT INTO `x360p_config` VALUES (1,0,'wxmp','{\"app_id\":\"wx809dc542c7ba3ccf\",\"secret\":\"cdb4e5f0c3f9bc6bd9e1d5ce3c8e0438\"}','json',0,0,1502849390,0,NULL,0),(2,0,'wxpay','{\"enable\":true,\"merchant_id\":\"4566511313213\",\"key\":\"sdfsdf\",\"cert_path\":\"dsfdsf\",\"key_path\":\"dsfsdfds\"}','json',1501644940,18,1502849473,0,NULL,0),(3,0,'params','{\"org_name\":\"浪腾培训\",\"sysname\":\"校务管理系统\"}','json',1501646181,18,1510971224,0,NULL,0),(4,0,'storage','{\"engine\":\"qiniu\",\"file\":{\"prefix\":\"\\/data\\/uploads\\/\"},\"qiniu\":{\"access_key\":\"p9mUPzEN5ihLHctwvBIk5w9MBckHvFSrXadVRlPY\",\"secret_key\":\"UJRv2IaSnsFUmZyXmYWyhpcrPW7WIYnslnT749Fh\",\"bucket\":\"ygwqms\",\"prefix\":\"\\/x360p\",\"domain\":\"http://s10.xiao360.com/\"}}','json',1504077704,18,1504077704,0,NULL,0),(5,0,'wechat_template','{\"pay_success\":{\"name\":\"缴费成功通知\",\"desc\":\"缴费成功通知\",\"sms_switch\":0,\"sms\":{\"tpl\":\"您好，您已成功购买课程!订单号:[订单号],支付金额:[支付金额],订单内容:[课程信息],缴费方式:微信支付\"},\"weixin_switch\":1,\"weixin\":{\"template_id\":\"YTe6yQdS_yxluIzVZyYg7CQLGHpl2mUal4NIgsfW3yo\",\"short_id\":\"OPENTM406872252\",\"tpl_title\":\"支付成功通知\",\"tpl_industry\":\"教育-培训\",\"url\":\"{base_url}\\/student\\/lesson\\/order_detail?oid={oid}\",\"data\":{\"first\":[\"你好，你已成功购买课程\",\"#ED3F14\"],\"keyword1\":[\"微信支付[支付金额]元\",\"#000000\"],\"keyword2\":[\"[机构名称][校区名称][课程信息]\",\"#0000FF\"],\"keyword3\":[\"[订单号]\",\"#000000\"],\"remark\":[\"此条通知可作为收据凭证，感谢您购买我们的服务，祝您生活愉快!\",\"#000000\"]}},\"tpl_fields\":{\"pay_amount\":\"[支付金额]\",\"course_info\":\"[课程信息]\",\"out_trade_no\":\"[订单号]\",\"org_name\":\"[机构名称]\",\"branch_name\":\"[校区名称]\"}},\"before_class_push\":{\"name\":\"课前提醒|课前推送备课\",\"desc\":\"推送老师备课\",\"sms_switch\":0,\"sms\":{\"tpl\":\"[学生姓名]家长，您好。请准时参加以下课程：课程名称:[课程名称],上课时间:[上课时间],上课地点:[上课地点],联系电话:[联系电话],温馨提示：请提前15分钟到，带好学习用品\"},\"weixin_switch\":1,\"weixin\":{\"template_id\":\"mz7mTfnGHuk1-lnuOeYtSTj6aYAecbEhYaoREq31fiA\",\"short_id\":\"OPENTM206931461\",\"tpl_title\":\"课前提醒\",\"tpl_industry\":\"教育-培训\",\"url\":\"{base_url}\\/student\\/preview_push?ca_id={ca_id}\",\"data\":{\"first\":[\"[学生姓名]家长，您好。请准时参加如下课程：\",\"#000000\"],\"keyword1\":[\"[课程名称]\",\"#000000\"],\"keyword2\":[\"[上课时间]\",\"#0000FF\"],\"keyword3\":[\"[上课地点]\",\"#000000\"],\"keyword4\":[\"[联系电话]\",\"#000000\"],\"remark\":[\"温馨提示：请提前15分钟到，带好学习用品\",\"#000000\"]}},\"tpl_fields\":{\"student_name\":\"[学生姓名]\",\"lesson_name\":\"[课程名称]\",\"school_time\":\"[上课时间]\",\"address\":\"[上课地点]\",\"mobile\":\"[联系电话]\"}},\"remind_before_class\":{\"name\":\"课前提醒\",\"desc\":\"课前提醒 通知家长记得上课和注意事项。\",\"sms_switch\":0,\"sms\":{\"tpl\":\"[学生姓名]家长，您好。请准时参加以下课程：课程名称:[课程名称],上课时间:[上课时间],上课地点:[上课地点],联系电话:[联系电话],温馨提示：[温馨提示]\"},\"weixin_switch\":1,\"weixin\":{\"template_id\":\"mz7mTfnGHuk1-lnuOeYtSTj6aYAecbEhYaoREq31fiA\",\"short_id\":\"OPENTM206931461\",\"tpl_title\":\"课前提醒\",\"tpl_industry\":\"教育-培训\",\"url\":\"\",\"data\":{\"first\":[\"[学生姓名]家长，您好。请准时参加如下课程：\",\"#000000\"],\"keyword1\":[\"[课程名称]\",\"#000000\"],\"keyword2\":[\"[上课时间]\",\"#0000FF\"],\"keyword3\":[\"[上课地点]\",\"#000000\"],\"keyword4\":[\"[联系电话]\",\"#000000\"],\"remark\":[\"温馨提示：[温馨提示]\",\"#000000\"]}},\"tpl_fields\":{\"student_name\":\"[学生姓名]\",\"lesson_name\":\"[课程名称]\",\"school_time\":\"[上课时间]\",\"address\":\"[上课地点]\",\"mobile\":\"[联系电话]\",\"remark\":\"[温馨提示]\"}},\"after_class_push\":{\"name\":\"课后作业推送\",\"desc\":\"课后作业推送\",\"sms_switch\":0,\"sms\":{\"tpl\":\"您有新的作业了，请查收。!班级名称:[班级名称],作业名称:[作业名称],作业详情:[作业详情],感谢您的查阅，请认真对待，按时完成作业。\"},\"weixin_switch\":1,\"weixin\":{\"template_id\":\"GsiUnVL_ENqe4s3xYY-n9h0H6kbfoWEtaW3kua46vic\",\"short_id\":\"OPENTM405774022\",\"tpl_title\":\"作业提醒\",\"tpl_industry\":\"教育-培训\",\"url\":\"{base_url}\\/student\\/review_push?hid={hid}\",\"data\":{\"first\":[\"您有新的作业了，请查收。\",\"#000000\"],\"keyword1\":[\"[班级名称]\",\"#000000\"],\"keyword2\":[\"[作业名称]\",\"#0000FF\"],\"keyword3\":[\"[作业详情]\",\"#000000\"],\"remark\":[\"感谢您的查阅，请认真对待，按时完成作业。\",\"#000000\"]}},\"tpl_fields\":{\"class_name\":\"[班级名称]\",\"homework_title\":\"[作业名称]\",\"homework_content\":\"[作业详情]\"}},\"alter_class_time\":{\"name\":\"上课时间调整通知\",\"desc\":\"上课时间调整通知\",\"sms_switch\":0,\"sms\":{\"tpl\":\"[学生姓名]家长，您好！有一个上课时间调整通知，请及时查看。所在班级：[班级名称],调课原因：[调课原因],上课时间调整到：[上课时间],给您的生活带来的不便敬请谅解！\"},\"weixin_switch\":1,\"weixin\":{\"template_id\":\"mY65u1okDLHaZYe_ARBHsDsmOvgdBxAsTmbAzPzylHY\",\"short_id\":\"OPENTM205990150\",\"tpl_title\":\"上课时间调整通知\",\"tpl_industry\":\"教育-培训\",\"url\":\"push\",\"data\":{\"first\":[\"[学生姓名]家长，您好！有一个上课时间调整通知，请及时查看\",\"#000000\"],\"keyword1\":[\"[班级名称]\",\"#000000\"],\"keyword2\":[\"[调课原因]\",\"#0000FF\"],\"keyword3\":[\"[上课时间]\",\"#000000\"],\"remark\":[\"给您的生活带来的不便敬请谅解！\",\"#000000\"]}},\"tpl_fields\":{\"student_name\":\"[学生姓名]\",\"class_name\":\"[班级名称]\",\"alter_reason\":\"[调课原因]\",\"class_time\":\"[上课时间]\"}},\"transfer_media\":{\"name\":\"公众号上传媒体文件\",\"desc\":\"公众号上传媒体文件\",\"sms_switch\":0,\"sms\":{\"tpl\":\"\"},\"weixin_switch\":1,\"weixin\":{\"template_id\":\"unsqejtDs_cSEUwmZvkBRGe0tnbdd63lKIerkVnNTHg\",\"short_id\":\"OPENTM213512088\",\"tpl_title\":\"待办任务提醒\",\"tpl_industry\":\"IT科技-IT软件与服务\",\"url\":\"\",\"data\":{\"first\":[\"您好！您有一个媒体文件需要上传\",\"#000000\"],\"keyword1\":[\"请在对话框内上传图片、视频或语音！\",\"#000000\"],\"keyword2\":[\"5分钟内\",\"#000000\"],\"remark\":[\"请直接在对话框内上传图片，视频和语音！\",\"#000000\"]}},\"tpl_fields\":[]}}','json',1512531709,1,1512612191,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COMMENT='班级排课记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_course_arrange`
--

LOCK TABLES `x360p_course_arrange` WRITE;
/*!40000 ALTER TABLE `x360p_course_arrange` DISABLE KEYS */;
INSERT INTO `x360p_course_arrange` VALUES (1,0,2,0,0,0,'',1,10002,0,1,0,3,1,'H',20171125,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510972292,1,1510972418,1,1510972418,1),(2,0,2,0,0,0,'',1,10002,0,1,0,3,2,'H',20171125,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510972292,1,1510972418,1,1510972418,1),(3,0,2,0,0,0,'',1,10002,0,1,0,3,3,'H',20171126,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510972292,1,1510972418,1,1510972418,1),(4,0,2,0,0,0,'',1,10002,0,1,0,3,4,'H',20171126,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510972292,1,1510972418,1,1510972418,1),(5,0,2,0,0,0,'',1,10002,0,1,0,3,4,'H',20171125,1,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510972418,1,1511237289,0,NULL,0),(6,0,2,0,0,0,'',1,10002,0,1,0,3,5,'H',20171125,1,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510972418,1,1511238107,0,NULL,0),(7,0,2,0,0,0,'',1,10002,0,1,0,3,6,'H',20171126,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510972418,1,1510972418,0,NULL,0),(8,0,2,0,0,0,'',1,10002,0,1,0,3,7,'H',20171126,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510972418,1,1510972418,0,NULL,0),(9,0,2,0,0,0,'',1,10002,0,1,1,3,1,'H',20171118,2,0,0,1000,1200,0,0,0,'',0,0,0,0,0,NULL,1510972471,1,1510991983,0,NULL,0),(10,0,2,0,0,0,'',2,10002,0,1,1,5,1,'H',20171203,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976617,1,1510976617,0,NULL,0),(11,0,2,0,0,0,'',2,10002,0,1,1,5,2,'H',20171210,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976617,1,1510976617,0,NULL,0),(12,0,2,0,0,0,'',2,10002,0,1,1,5,3,'H',20171217,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976617,1,1510976617,0,NULL,0),(13,0,2,0,0,0,'',2,10002,0,1,1,5,4,'H',20171224,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976617,1,1510976617,0,NULL,0),(14,0,2,0,0,0,'',2,10002,0,1,1,5,5,'H',20171231,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976617,1,1510978689,1,1510978689,1),(15,0,2,0,0,0,'',2,10002,0,1,1,5,6,'H',20180107,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976617,1,1510976617,0,NULL,0),(16,0,2,0,0,0,'',2,10002,0,1,1,5,7,'H',20180114,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976617,1,1510976617,0,NULL,0),(17,0,2,0,0,0,'',2,10002,0,1,1,5,8,'H',20180121,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976617,1,1510976617,0,NULL,0),(18,0,2,0,0,0,'',2,10002,0,1,1,5,9,'H',20180128,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976617,1,1510976617,0,NULL,0),(19,0,2,0,0,0,'',2,10002,0,1,1,5,10,'H',20180204,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976617,1,1510976617,0,NULL,0),(20,0,2,0,0,0,'',1,10002,0,1,1,6,6,'H',20171125,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510976793,1,1511173525,1,1511173525,1),(21,0,2,0,0,0,'',1,10002,10002,1,1,2,2,'H',20171118,0,0,0,1500,1515,0,0,0,'',0,0,0,0,0,NULL,1510987642,1,1510990849,0,NULL,0),(22,0,2,0,0,0,'',1,10002,10002,1,1,2,3,'H',20171118,1,0,0,1530,1545,0,0,0,'',0,0,0,0,0,NULL,1510989720,1,1510990872,0,NULL,0),(23,0,3,0,0,0,'',3,10003,10002,1,0,8,1,'Q',20171120,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993335,1,1510993335,1),(24,0,3,0,0,0,'',3,10003,10002,1,0,8,1,'Q',20171121,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(25,0,3,0,0,0,'',3,10003,10002,1,0,8,2,'Q',20171122,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(26,0,3,0,0,0,'',3,10003,10002,1,0,8,3,'Q',20171123,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(27,0,3,0,0,0,'',3,10003,10002,1,0,8,4,'Q',20171124,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(28,0,3,0,0,0,'',3,10003,10002,1,0,8,5,'Q',20171125,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(29,0,3,0,0,0,'',3,10003,10002,1,0,8,6,'Q',20171125,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(30,0,3,0,0,0,'',3,10003,10002,1,0,8,7,'Q',20171126,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(31,0,3,0,0,0,'',3,10003,10002,1,0,8,8,'Q',20171126,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(32,0,3,0,0,0,'',3,10003,10002,1,0,8,9,'Q',20171127,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(33,0,3,0,0,0,'',3,10003,10002,1,0,8,10,'Q',20171128,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(34,0,3,0,0,0,'',3,10003,10002,1,0,8,11,'Q',20171129,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(35,0,3,0,0,0,'',3,10003,10002,1,0,8,12,'Q',20171130,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(36,0,3,0,0,0,'',3,10003,10002,1,0,8,13,'Q',20171201,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(37,0,3,0,0,0,'',3,10003,10002,1,0,8,14,'Q',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(38,0,3,0,0,0,'',3,10003,10002,1,0,8,15,'Q',20171202,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(39,0,3,0,0,0,'',3,10003,10002,1,0,8,16,'Q',20171203,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(40,0,3,0,0,0,'',3,10003,10002,1,0,8,17,'Q',20171203,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(41,0,3,0,0,0,'',3,10003,10002,1,0,8,18,'Q',20171204,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(42,0,3,0,0,0,'',3,10003,10002,1,0,8,19,'Q',20171205,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(43,0,3,0,0,0,'',3,10003,10002,1,0,8,20,'Q',20171206,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(44,0,3,0,0,0,'',3,10003,10002,1,0,8,21,'Q',20171207,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(45,0,3,0,0,0,'',3,10003,10002,1,0,8,22,'Q',20171208,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(46,0,3,0,0,0,'',3,10003,10002,1,0,8,23,'Q',20171209,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(47,0,3,0,0,0,'',3,10003,10002,1,0,8,24,'Q',20171209,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(48,0,3,0,0,0,'',3,10003,10002,1,0,8,25,'Q',20171210,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(49,0,3,0,0,0,'',3,10003,10002,1,0,8,26,'Q',20171210,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(50,0,3,0,0,0,'',3,10003,10002,1,0,8,27,'Q',20171211,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(51,0,3,0,0,0,'',3,10003,10002,1,0,8,28,'Q',20171212,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(52,0,3,0,0,0,'',3,10003,10002,1,0,8,29,'Q',20171213,0,0,0,1900,2100,0,0,0,'',0,0,0,0,0,NULL,1510993008,1,1510993008,0,NULL,0),(53,0,2,0,0,0,'',6,0,0,5,0,7,3,'H',20171120,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(54,0,2,0,0,0,'',6,0,0,5,0,7,4,'H',20171121,2,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511236432,0,NULL,0),(55,0,2,0,0,0,'',6,0,0,5,0,7,4,'H',20171122,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511346952,1,1511346952,1),(56,0,2,0,0,0,'',6,0,0,5,0,7,5,'H',20171123,2,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511245480,0,NULL,0),(57,0,2,0,0,0,'',6,0,0,5,0,7,5,'H',20171124,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511150294,1,1511150294,1),(58,0,2,0,0,0,'',6,0,0,5,0,7,7,'H',20171125,2,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511246716,0,NULL,0),(59,0,2,0,0,0,'',6,0,0,5,0,7,8,'H',20171125,2,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511238505,0,NULL,0),(60,0,2,0,0,0,'',6,0,0,5,0,7,9,'H',20171126,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(61,0,2,0,0,0,'',6,0,0,5,0,7,10,'H',20171126,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(62,0,2,0,0,0,'',6,0,0,5,0,7,11,'H',20171127,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(63,0,2,0,0,0,'',6,0,0,5,0,7,12,'H',20171128,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(64,0,2,0,0,0,'',6,0,0,5,0,7,13,'H',20171129,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511837173,1,1511837173,1),(65,0,2,0,0,0,'',6,0,0,5,0,7,14,'H',20171130,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511837169,1,1511837169,1),(66,0,2,0,0,0,'',6,0,0,5,0,7,15,'H',20171201,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511837165,1,1511837165,1),(67,0,2,0,0,0,'',6,0,0,5,0,7,13,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511837210,1,1511837210,1),(68,0,2,0,0,0,'',6,0,0,5,0,7,16,'H',20171202,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511837160,1,1511837160,1),(69,0,2,0,0,0,'',6,0,0,5,0,7,13,'H',20171203,1,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1513421286,0,NULL,0),(70,0,2,0,0,0,'',6,0,0,5,0,7,14,'H',20171203,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(71,0,2,0,0,0,'',6,0,0,5,0,7,15,'H',20171204,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(72,0,2,0,0,0,'',6,0,0,5,0,7,16,'H',20171205,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(73,0,2,0,0,0,'',6,0,0,5,0,7,17,'H',20171206,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(74,0,2,0,0,0,'',6,0,0,5,0,7,18,'H',20171207,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(75,0,2,0,0,0,'',6,10004,0,5,0,9,19,'H',20171208,1,0,0,1445,1545,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1513421733,0,NULL,0),(76,0,2,0,0,0,'',6,0,0,5,0,7,20,'H',20171209,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(77,0,2,0,0,0,'',6,0,0,5,0,7,21,'H',20171209,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(78,0,2,0,0,0,'',6,0,0,5,0,7,25,'H',20171210,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1511151500,1,1511151500,1),(79,0,2,0,0,0,'',6,0,0,5,0,7,22,'H',20171210,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(80,0,2,0,0,0,'',6,0,0,5,0,7,23,'H',20171211,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(81,0,2,0,0,0,'',6,0,0,5,0,7,24,'H',20171212,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1510994475,0,NULL,0),(82,0,2,0,0,0,'',6,0,0,5,0,7,25,'H',20171213,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1510994475,1,1513422394,0,NULL,0),(83,0,2,0,0,0,'',7,10003,0,1,1,2,1,'H',20171120,0,0,0,1515,1545,0,0,0,'',0,0,0,0,0,NULL,1510995696,1,1510995696,0,NULL,0),(84,0,2,0,0,0,'',6,10003,10003,5,4,7,1,'H',20171120,2,0,0,1200,1215,0,0,0,'',0,0,0,0,0,NULL,1511150296,1,1511244989,0,NULL,0),(85,0,2,0,0,0,'',6,10002,10003,5,4,5,2,'H',20171120,2,0,0,1230,1300,0,0,0,'',0,0,0,0,0,NULL,1511151530,1,1511237793,0,NULL,0),(86,0,2,0,0,0,'',1,10002,0,1,1,7,8,'H',20171121,2,0,0,1500,1700,0,0,0,'',0,0,0,0,0,NULL,1511248544,1,1511573592,0,NULL,0),(87,0,2,0,0,0,'',9,10004,0,9,4,8,1,'H',20171128,2,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511257877,1,1511347122,0,NULL,0),(88,0,2,0,0,0,'',9,10004,0,9,4,8,2,'H',20171129,1,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511257877,1,1511355671,0,NULL,0),(89,0,2,0,0,0,'',9,10004,0,9,4,8,3,'H',20171130,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511257877,1,1512371792,0,NULL,0),(90,0,2,0,0,0,'',9,10004,0,9,4,8,4,'H',20171201,0,0,0,545,815,0,0,0,'',0,0,0,0,0,NULL,1511257877,1,1511257877,0,NULL,0),(91,0,2,0,0,0,'',9,10004,0,9,4,8,5,'H',20171201,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511257877,1,1511257877,0,NULL,0),(92,0,2,0,0,0,'',9,10004,0,9,4,8,6,'H',20171202,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1511257877,1,1511257877,0,NULL,0),(93,0,2,0,0,0,'',9,10004,0,9,4,8,7,'H',20171203,1,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1511257877,1,1511344568,0,NULL,0),(94,0,2,0,0,0,'',10,10003,0,1,1,0,1,'H',20171122,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511266386,1,1511339441,1,1511339441,1),(95,0,2,0,0,0,'',10,10003,0,1,1,2,1,'H',20171122,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511339102,1,1511339441,1,1511339441,1),(96,0,2,0,0,0,'',10,10003,0,1,1,0,2,'H',20171123,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511339102,1,1511339441,1,1511339441,1),(97,0,2,0,0,0,'',10,10003,0,1,1,0,3,'H',20171124,0,0,0,545,815,0,0,0,'',0,0,0,0,0,NULL,1511339102,1,1511339441,1,1511339441,1),(98,0,2,0,0,0,'',10,10003,0,1,1,2,1,'H',20171122,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511339441,1,1511347040,1,1511347040,1),(99,0,2,0,0,0,'',10,10003,0,1,1,0,1,'H',20171123,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511339441,1,1511339441,0,NULL,0),(100,0,2,0,0,0,'',10,10003,0,1,1,0,2,'H',20171124,0,0,0,545,815,0,0,0,'',0,0,0,0,0,NULL,1511339441,1,1511339441,0,NULL,0),(101,0,2,0,0,0,'',10,10003,10005,1,1,5,4,'H',20171123,0,0,0,330,500,0,0,0,'',0,0,0,0,0,NULL,1511339452,1,1511339474,1,1511339474,1),(102,0,2,0,0,0,'',1,10004,0,1,1,9,9,'H',20171121,2,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511345088,1,1511432739,0,NULL,0),(103,0,2,0,0,0,'',11,1,0,9,4,6,1,'H',20171129,2,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511345414,1,1511345505,0,NULL,0),(104,0,2,0,0,0,'',11,1,0,9,4,6,2,'H',20171130,2,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511345414,1,1511352972,0,NULL,0),(105,0,2,0,0,0,'',11,1,0,9,4,6,3,'H',20171201,0,0,0,545,815,0,0,0,'',0,0,0,0,0,NULL,1511345414,1,1511345414,0,NULL,0),(106,0,2,0,0,0,'',11,1,0,9,4,6,4,'H',20171201,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511345414,1,1511345414,0,NULL,0),(107,0,2,0,0,0,'',11,1,0,9,4,6,5,'H',20171202,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1511345414,1,1511352668,0,NULL,0),(108,0,2,0,0,0,'',11,1,0,9,4,6,6,'H',20171203,1,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1511345414,1,1512377419,0,NULL,0),(109,0,2,0,0,0,'',12,10003,10003,5,4,6,1,'H',20171122,2,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511345979,1,1511346471,0,NULL,0),(110,0,2,0,0,0,'',12,10003,10003,5,4,6,2,'H',20171123,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511345979,1,1511775204,1,1511775204,1),(111,0,2,0,0,0,'',6,10004,0,5,4,10,30,'H',20171124,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511347002,1,1511347028,1,1511347028,1),(112,0,2,0,0,0,'',6,10005,0,5,4,10,30,'H',20171124,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511347090,1,1511347154,1,1511347154,1),(113,0,2,0,0,0,'',6,10004,0,5,4,10,6,'H',20171124,2,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1511347176,1,1511432687,0,NULL,0),(114,0,2,1,0,0,'',0,10003,0,4,0,10,0,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1511852689,1,1511852701,1,1511852701,1),(115,0,2,1,0,0,'',0,10003,0,4,0,9,0,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1511852790,1,1511852831,1,1511852831,1),(116,0,2,1,0,0,'',0,10016,0,4,0,9,0,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1511852921,1,1511853857,1,1511853857,1),(117,0,2,1,0,0,'',0,10004,0,4,0,9,0,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1511853528,1,1511853551,1,1511853551,1),(118,0,2,1,0,0,'',0,10004,0,4,0,9,0,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1511853558,1,1511853563,1,1511853563,1),(119,0,2,1,0,0,'',0,10004,0,4,0,10,0,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1511854343,1,1511854402,1,1511854402,1),(120,0,2,1,0,0,'',0,10002,0,4,0,10,0,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1511854479,1,1511854515,1,1511854515,1),(121,0,2,0,0,0,'',13,10004,0,9,4,2,1,'H',20171129,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1512549809,1,1513062340,1,1513062340,1),(122,0,2,0,0,0,'',13,10004,0,9,4,2,2,'H',20171130,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1512549809,1,1513062340,1,1513062340,1),(123,0,2,0,0,0,'',13,10004,0,9,4,2,3,'H',20171201,0,0,0,545,815,0,0,0,'',0,0,0,0,0,NULL,1512549809,1,1513062340,1,1513062340,1),(124,0,2,0,0,0,'',13,10004,0,9,4,2,4,'H',20171201,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1512549809,1,1513062340,1,1513062340,1),(125,0,2,0,0,0,'',13,10004,0,9,4,2,5,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1512549809,1,1513062340,1,1513062340,1),(126,0,2,0,0,0,'',13,10004,0,9,4,2,6,'H',20171202,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1512549809,1,1513062340,1,1513062340,1),(127,0,2,0,0,0,'',13,10004,0,9,4,2,1,'H',20171129,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1512549814,1,1513062340,1,1513062340,1),(128,0,2,0,0,0,'',13,10004,0,9,4,2,1,'H',20171130,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1512549814,1,1513062340,1,1513062340,1),(129,0,2,0,0,0,'',13,10004,0,9,4,2,1,'H',20171201,0,0,0,545,815,0,0,0,'',0,0,0,0,0,NULL,1512549814,1,1513062340,1,1513062340,1),(130,0,2,0,0,0,'',13,10004,0,9,4,9,1,'H',20171201,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1512549814,1,1513062340,1,1513062340,1),(131,0,2,0,0,0,'',13,10004,0,9,4,9,2,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1512549814,1,1513062340,1,1513062340,1),(132,0,2,0,0,0,'',13,10004,0,9,4,10,3,'H',20171202,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1512549814,1,1513062340,1,1513062340,1),(133,0,2,0,0,0,'',13,10004,0,9,4,2,1,'H',20171129,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1512723862,1,1513062340,1,1513062340,1),(134,0,2,0,0,0,'',13,10004,0,9,4,2,2,'H',20171130,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1512723862,1,1513062340,1,1513062340,1),(135,0,2,0,0,0,'',13,10004,0,9,4,2,3,'H',20171201,0,0,0,545,815,0,0,0,'',0,0,0,0,0,NULL,1512723862,1,1513062340,1,1513062340,1),(136,0,2,0,0,0,'',13,10004,0,9,4,2,4,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1512723862,1,1513062340,1,1513062340,1),(137,0,2,0,0,0,'',13,10004,0,9,4,2,5,'H',20171202,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1512723862,1,1513062340,1,1513062340,1),(138,0,2,0,0,0,'',13,10004,0,9,4,2,6,'H',20171203,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1512723862,1,1513062340,1,1513062340,1),(139,0,2,0,1,0,'test_shiting',0,10004,0,1,0,9,0,'H',20171208,0,0,0,2000,2045,0,0,0,'',0,0,0,0,0,NULL,1512727389,1,1512727389,0,NULL,0),(140,0,2,0,1,0,'老王试听',0,10003,0,4,0,9,0,'H',20171208,0,0,0,2300,2330,0,0,0,'',0,0,0,0,0,NULL,1512728716,1,1512728716,0,NULL,0),(141,0,2,0,0,1,'一对多补课排课',0,10003,0,3,0,10,0,'H',20171212,0,0,0,800,900,0,0,0,'',0,0,0,0,0,NULL,1513060186,1,1513060186,0,NULL,0),(143,0,2,0,0,1,'一对多排课',0,10006,0,3,0,10,0,'H',20171212,0,0,0,100,200,0,0,0,'',0,0,0,0,0,NULL,1513061875,1,1513061875,0,NULL,0),(144,0,2,0,0,0,'',13,10004,0,9,4,2,1,'H',20171129,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1513062323,1,1513062340,1,1513062340,1),(145,0,2,0,0,0,'',13,10004,0,9,4,2,2,'H',20171130,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1513062323,1,1513062340,1,1513062340,1),(146,0,2,0,0,0,'',13,10004,0,9,4,2,3,'H',20171201,0,0,0,545,815,0,0,0,'',0,0,0,0,0,NULL,1513062323,1,1513062340,1,1513062340,1),(147,0,2,0,0,0,'',13,10004,0,9,4,2,4,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1513062323,1,1513062340,1,1513062340,1),(148,0,2,0,0,0,'',13,10004,0,9,4,2,5,'H',20171202,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1513062323,1,1513062340,1,1513062340,1),(149,0,2,0,0,0,'',13,10004,0,9,4,2,6,'H',20171203,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1513062323,1,1513062340,1,1513062340,1),(150,0,2,0,0,0,'',13,10004,0,9,4,2,1,'H',20171129,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1513062340,1,1513062340,0,NULL,0),(151,0,2,0,0,0,'',13,10004,0,9,4,2,2,'H',20171130,0,0,0,1900,2130,0,0,0,'',0,0,0,0,0,NULL,1513062340,1,1513062340,0,NULL,0),(152,0,2,0,0,0,'',13,10004,0,9,4,2,3,'H',20171201,0,0,0,545,815,0,0,0,'',0,0,0,0,0,NULL,1513062340,1,1513062340,0,NULL,0),(153,0,2,0,0,0,'',13,10004,0,9,4,2,4,'H',20171202,0,0,0,800,1000,0,0,0,'',0,0,0,0,0,NULL,1513062340,1,1513062340,0,NULL,0),(154,0,2,0,0,0,'',13,10004,0,9,4,2,5,'H',20171202,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1513062340,1,1513062340,0,NULL,0),(155,0,2,0,0,0,'',13,10004,0,9,4,2,6,'H',20171203,0,0,0,1030,1230,0,0,0,'',0,0,0,0,0,NULL,1513062340,1,1513062340,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=209 DEFAULT CHARSET=utf8mb4 COMMENT='客户表(市场招生)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_customer`
--

LOCK TABLES `x360p_customer` WRITE;
/*!40000 ALTER TABLE `x360p_customer` DISABLE KEYS */;
INSERT INTO `x360p_customer` VALUES (1,0,2,'李二狗','liergou','leg','ergou','1',0,0,0,0,0,'',0,'15345345322','',0,'',0,'','',109,0,0,1,24,NULL,200.00,0,0,0,0,0,1510998834,1,1512713599,0,NULL,0),(2,0,2,'王二','wanger','we','','1',1479398400,2016,11,18,2,'fghd',0,'13234234444','',0,'tfjfhtd',2,'13456788776','tfhfjhytrfcfthtdhtfjftjtdhx后端监控江湖救急洋甘菊看一个乖乖怪怪军',109,4,0,1,20,NULL,200.00,0,10002,1,0,0,1511001082,1,1512618505,0,NULL,0),(3,0,2,'小仙女','','','xiaoxiannv','2',1321286400,2011,11,15,0,'',0,'13474639999','',1,'',0,'','',109,4,114,1,25,NULL,0.00,10003,10003,1,0,0,1511158139,1,1511166175,0,NULL,0),(4,0,2,'西门大官人','','','Ximengqing','1',1194796800,2007,11,12,0,'',0,'18396372833','',1,'',0,'','',108,3,113,1,32,NULL,0.00,10003,10004,0,0,0,1511321864,1,1511333935,0,NULL,0),(5,0,2,'胡歌','','','','1',485539200,1985,5,22,137,'',0,'13808851851','胡曲',4,'',0,'','',107,3,113,1,33,NULL,0.00,10003,10002,1,0,0,1511336155,1,1512045444,0,NULL,0),(6,0,3,'黄鹤','huanghe','hh','yellow bird','1',1078416000,2004,3,5,0,'',0,'15486875648','',1,'',0,'','',110,4,113,1,28,20171212,0.00,10003,0,0,0,0,1511748780,1,1513063100,0,NULL,0),(7,0,43,'邢佳栋','','','','1',0,0,0,0,137,'',0,'13501588588','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045437,1,1512045437,0,NULL,0),(8,0,44,'李学庆','','','','1',0,0,0,0,137,'',0,'13510510510','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(9,0,45,'高昊','','','','1',0,0,0,0,137,'',0,'13528851851','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(10,0,46,'潘粤明','','','','1',0,0,0,0,137,'',0,'13530285285','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(11,0,47,'戴军','','','','1',0,0,0,0,137,'',0,'13530556556','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(12,0,43,'薛之谦','','','','1',0,0,0,0,137,'',0,'13530885885','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(13,0,44,'贾宏声','','','','1',0,0,0,0,137,'',0,'13537552552','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(14,0,45,'于波','','','','1',0,0,0,0,137,'',0,'13537665665','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(15,0,46,'李连杰','','','','1',0,0,0,0,137,'',0,'13682559559','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(16,0,47,'王斑','','','','1',0,0,0,0,137,'',0,'13602644644','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(17,0,43,'蓝雨','','','','1',0,0,0,0,137,'',0,'13632508508','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(18,0,44,'刘恩佑','','','','1',0,0,0,0,137,'',0,'13631508508','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(19,0,45,'任泉','','','','1',0,0,0,0,137,'',0,'13622358358','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(20,0,46,'李光洁','','','','1',0,0,0,0,137,'',0,'13662658658','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045438,1,1512045438,0,NULL,0),(21,0,47,'姜文','','','','1',0,0,0,0,137,'',0,'13631589589','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(22,0,43,'黑龙','','','','1',0,0,0,0,137,'',0,'17876995995','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(23,0,44,'张殿菲','','','','1',0,0,0,0,137,'',0,'13923878878','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(24,0,45,'邓超','','','','1',0,0,0,0,137,'',0,'15989867867','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(25,0,46,'张杰','','','','1',0,0,0,0,137,'',0,'18312522522','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(26,0,47,'杨坤','','','','1',0,0,0,0,137,'',0,'18320955955','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(27,0,43,'沙溢','','','','1',0,0,0,0,137,'',0,'13823737737','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(28,0,44,'李茂','','','','1',0,0,0,0,137,'',0,'13924644644','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(29,0,45,'黄磊','','','','1',0,0,0,0,137,'',0,'15220234234','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(30,0,46,'于小伟','','','','1',0,0,0,0,137,'',0,'15012665665','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(31,0,47,'刘冠翔','','','','1',0,0,0,0,137,'',0,'18820207207','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045439,1,1512045439,0,NULL,0),(32,0,43,'秦俊杰','','','','1',0,0,0,0,137,'',0,'18819078078','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(33,0,44,'张琳','','','','1',0,0,0,0,137,'',0,'18823195195','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(34,0,45,'陈坤','','','','1',0,0,0,0,137,'',0,'15989533533','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(35,0,46,'黄觉','','','','1',0,0,0,0,137,'',0,'13823536536','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(36,0,47,'邵峰','','','','1',0,0,0,0,137,'',0,'18820255255','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(37,0,43,'陈旭','','','','1',0,0,0,0,137,'',0,'18818695695','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(38,0,44,'马天宇','','','','1',0,0,0,0,137,'',0,'18818565565','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(39,0,45,'杨子','','','','1',0,0,0,0,137,'',0,'13825295295','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(40,0,46,'邓安奇','','','','1',0,0,0,0,137,'',0,'13808837837','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(41,0,47,'赵鸿飞','','','','1',0,0,0,0,137,'',0,'13802707707','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(42,0,43,'马可','','','','1',0,0,0,0,137,'',0,'13802578578','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045440,1,1512045440,0,NULL,0),(43,0,44,'黄海波','','','','1',0,0,0,0,137,'',0,'13823586586','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045441,1,1512045441,0,NULL,0),(44,0,45,'黄志忠','','','','1',0,0,0,0,137,'',0,'13926589589','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045441,1,1512045441,0,NULL,0),(45,0,46,'李晨','','','','1',0,0,0,0,137,'',0,'13828815815','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045441,1,1512045441,0,NULL,0),(46,0,47,'后弦','','','','1',0,0,0,0,137,'',0,'13826538538','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045441,1,1512045441,0,NULL,0),(47,0,43,'王挺','','','','1',0,0,0,0,137,'',0,'13501589589','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045441,1,1512045441,0,NULL,0),(48,0,44,'何炅','','','','1',0,0,0,0,137,'',0,'13528805805','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045441,1,1512045441,0,NULL,0),(49,0,45,'朱亚文','','','','1',0,0,0,0,137,'',0,'13528895895','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045441,1,1512045441,0,NULL,0),(50,0,46,'胡军','','','','1',0,0,0,0,137,'',0,'13530518518','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045441,1,1512045441,0,NULL,0),(51,0,47,'许亚军','','','','1',0,0,0,0,137,'',0,'13530565565','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(52,0,43,'张涵予','','','','1',0,0,0,0,137,'',0,'13530965965','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(53,0,44,'贾乃亮','','','','1',0,0,0,0,137,'',0,'13537598598','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(54,0,45,'陆虎','','','','1',0,0,0,0,137,'',0,'13699788788','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(55,0,46,'印小天','','','','1',0,0,0,0,137,'',0,'13602560560','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(56,0,47,'于和伟','','','','1',0,0,0,0,137,'',0,'13682500500','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(57,0,43,'田亮','','','','1',0,0,0,0,137,'',0,'13603050050','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(58,0,44,'夏雨','','','','1',0,0,0,0,137,'',0,'13613007007','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(59,0,45,'李亚鹏','','','','1',0,0,0,0,137,'',0,'13682515515','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(60,0,46,'胡兵','','','','1',0,0,0,0,137,'',0,'13689552552','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(61,0,47,'王睿','','','','1',0,0,0,0,137,'',0,'13823252252','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(62,0,43,'保剑锋','','','','1',0,0,0,0,137,'',0,'13802563563','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045442,1,1512045442,0,NULL,0),(63,0,44,'于震','','','','1',0,0,0,0,137,'',0,'15989864864','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045443,1,1512045443,0,NULL,0),(64,0,45,'苏醒','','','','1',0,0,0,0,137,'',0,'15889355355','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045443,1,1512045443,0,NULL,0),(65,0,46,'胡夏','','','','1',0,0,0,0,137,'',0,'18814355355','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045443,1,1512045443,0,NULL,0),(66,0,47,'张丰毅','','','','1',0,0,0,0,137,'',0,'15112598598','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045443,1,1512045443,0,NULL,0),(67,0,43,'刘翔','','','','1',0,0,0,0,137,'',0,'13925275275','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045443,1,1512045443,0,NULL,0),(68,0,44,'李玉刚','','','','1',0,0,0,0,137,'',0,'13925272272','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045443,1,1512045443,0,NULL,0),(69,0,45,'林依轮','','','','1',0,0,0,0,137,'',0,'18820261261','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045443,1,1512045443,0,NULL,0),(70,0,46,'袁弘','','','','1',0,0,0,0,137,'',0,'15989865865','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045443,1,1512045443,0,NULL,0),(71,0,47,'朱雨辰','','','','1',0,0,0,0,137,'',0,'18823769769','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045443,1,1512045443,0,NULL,0),(72,0,43,'丁志诚','','','','1',0,0,0,0,137,'',0,'13923837837','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045443,1,1512045443,0,NULL,0),(73,0,44,'黄征','','','','1',0,0,0,0,137,'',0,'18820988988','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045444,1,1512045444,0,NULL,0),(74,0,45,'张子健','','','','1',0,0,0,0,137,'',0,'13823529529','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045444,1,1512045444,0,NULL,0),(75,0,46,'许嵩','','','','1',0,0,0,0,137,'',0,'18820995995','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045444,1,1512045444,0,NULL,0),(76,0,47,'向鼎','','','','1',0,0,0,0,137,'',0,'15118151151','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045444,1,1512045444,0,NULL,0),(77,0,43,'陆毅','','','','1',0,0,0,0,137,'',0,'18818561561','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045444,1,1512045444,0,NULL,0),(78,0,44,'乔振宇','','','','1',0,0,0,0,137,'',0,'13823506506','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045444,1,1512045444,0,NULL,0),(79,0,45,'闫肃','','','','1',0,0,0,0,137,'',0,'18818781781','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045444,1,1512045444,0,NULL,0),(80,0,46,'李健','','','','1',0,0,0,0,137,'',0,'15919951951','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045444,1,1512045444,0,NULL,0),(81,0,47,'王啸坤','','','','1',0,0,0,0,137,'',0,'13808805805','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045444,1,1512045444,0,NULL,0),(82,0,44,'吉杰','','','','1',0,0,0,0,137,'',0,'13828895895','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045444,1,1512045444,0,NULL,0),(83,0,45,'吴俊余','','','','1',0,0,0,0,137,'',0,'13826578578','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045445,1,1512045445,0,NULL,0),(84,0,46,'韩寒','','','','1',0,0,0,0,137,'',0,'18826518518','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045445,1,1512045445,0,NULL,0),(85,0,47,'黄海冰','','','','1',0,0,0,0,137,'',0,'18813678678','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045445,1,1512045445,0,NULL,0),(86,0,43,'魏晨','','','','1',0,0,0,0,137,'',0,'18818891888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045445,1,1512097603,0,NULL,0),(87,0,44,'郭敬明','','','','1',0,0,0,0,137,'',0,'13926099999','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045445,1,1512045445,0,NULL,0),(88,0,45,'何晟铭','','','','1',0,0,0,0,137,'',0,'13660777777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045445,1,1512045445,0,NULL,0),(89,0,46,'巫迪文','','','','1',0,0,0,0,137,'',0,'13688880888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045445,1,1512045445,0,NULL,0),(90,0,47,'谢苗','','','','1',0,0,0,0,137,'',0,'13829788888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045445,1,1512045445,0,NULL,0),(91,0,43,'郑源','','','','1',0,0,0,0,137,'',0,'13688881111','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(92,0,44,'欢子','','','','1',0,0,0,0,137,'',0,'18312666666','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(93,0,45,'文章','','','','1',0,0,0,0,137,'',0,'13544666666','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(94,0,46,'陈翔','','','','1',0,0,0,0,137,'',0,'13533222222','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(95,0,47,'井柏然','','','','1',0,0,0,0,137,'',0,'13903077777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(96,0,43,'左小祖咒','','','','1',0,0,0,0,137,'',0,'13533333338','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(97,0,44,'含笑','','','','1',0,0,0,0,137,'',0,'14716999999','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(98,0,45,'李咏','','','','1',0,0,0,0,137,'',0,'15889998888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(99,0,46,'徐誉滕','','','','1',0,0,0,0,137,'',0,'13822278888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(100,0,47,'段奕宏','','','','1',0,0,0,0,137,'',0,'13710188888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(101,0,43,'李炜','','','','1',0,0,0,0,137,'',0,'13688888858','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(102,0,44,'罗中旭','','','','1',0,0,0,0,137,'',0,'13929558888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045446,1,1512045446,0,NULL,0),(103,0,45,'张远','','','','1',0,0,0,0,137,'',0,'15889977777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(104,0,46,'李立','','','','1',0,0,0,0,137,'',0,'13826444444','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(105,0,47,'释小龙','','','','1',0,0,0,0,137,'',0,'13560388888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(106,0,43,'大左','','','','1',0,0,0,0,137,'',0,'15813399999','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(107,0,44,'君君','','','','1',0,0,0,0,137,'',0,'15112000000','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(108,0,45,'毛宁','','','','1',0,0,0,0,137,'',0,'18988889888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(109,0,46,'樊凡','','','','1',0,0,0,0,137,'',0,'18898880888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(110,0,47,'周一围','','','','1',0,0,0,0,137,'',0,'13422221111','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(111,0,43,'于荣光','','','','1',0,0,0,0,137,'',0,'15013131313','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(112,0,44,'汤潮','','','','1',0,0,0,0,137,'',0,'13570399999','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(113,0,45,'张晓晨','','','','1',0,0,0,0,137,'',0,'18898882888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045447,1,1512045447,0,NULL,0),(114,0,46,'吴京','','','','1',0,0,0,0,137,'',0,'13922244444','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(115,0,47,'山野','','','','1',0,0,0,0,137,'',0,'15918444444','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(116,0,43,'陈龙','','','','1',0,0,0,0,137,'',0,'15999994444','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(117,0,44,'侯勇','','','','1',0,0,0,0,137,'',0,'15811898888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(118,0,45,'张国强','','','','1',0,0,0,0,137,'',0,'13926133333','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(119,0,46,'玉米提','','','','1',0,0,0,0,137,'',0,'13168886888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(120,0,47,'周觅','','','','1',0,0,0,0,137,'',0,'13544688888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(121,0,43,'张丹峰','','','','1',0,0,0,0,137,'',0,'18302020202','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(122,0,44,'俞思远','','','','1',0,0,0,0,137,'',0,'13822267777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(123,0,45,'姚明','','','','1',0,0,0,0,137,'',0,'13711156789','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(124,0,46,'冯绍峰','','','','1',0,0,0,0,137,'',0,'18898887777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045448,1,1512045448,0,NULL,0),(125,0,47,'陈玉建','','','','1',0,0,0,0,137,'',0,'13928888188','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045449,1,1512045449,0,NULL,0),(126,0,43,'吴建飞','','','','1',0,0,0,0,137,'',0,'13826200000','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045449,1,1512045449,0,NULL,0),(127,0,44,'郑钧','','','','1',0,0,0,0,137,'',0,'15915918888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045449,1,1512045449,0,NULL,0),(128,0,45,'胡彦斌','','','','1',0,0,0,0,137,'',0,'13622755555','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045449,1,1512045449,0,NULL,0),(129,0,46,'李智楠','','','','1',0,0,0,0,137,'',0,'13929522222','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045449,1,1512045449,0,NULL,0),(130,0,47,'钱枫','','','','1',0,0,0,0,137,'',0,'13538887777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045449,1,1512045449,0,NULL,0),(131,0,43,'高曙光','','','','1',0,0,0,0,137,'',0,'18011788888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045449,1,1512045449,0,NULL,0),(132,0,44,'谢和弦','','','','1',0,0,0,0,137,'',0,'15818181888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045450,1,1512045450,0,NULL,0),(133,0,45,'陈道明','','','','1',0,0,0,0,137,'',0,'13929511111','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045450,1,1512045450,0,NULL,0),(134,0,46,'柳云龙','','','','1',0,0,0,0,137,'',0,'18318888881','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045450,1,1512045450,0,NULL,0),(135,0,47,'汪峰','','','','1',0,0,0,0,137,'',0,'13538889888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045450,1,1512045450,0,NULL,0),(136,0,43,'陈楚生','','','','1',0,0,0,0,137,'',0,'13710558888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045450,1,1512045450,0,NULL,0),(137,0,44,'陈思成','','','','1',0,0,0,0,137,'',0,'13539889999','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045450,1,1512045450,0,NULL,0),(138,0,46,'马雪阳','','','','1',0,0,0,0,137,'',0,'13660300000','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045450,1,1512045450,0,NULL,0),(139,0,47,'袁成杰','','','','1',0,0,0,0,137,'',0,'13928885588','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045450,1,1512045450,0,NULL,0),(140,0,43,'崔健','','','','1',0,0,0,0,137,'',0,'15999999922','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045450,1,1512045450,0,NULL,0),(141,0,44,'杜淳','','','','1',0,0,0,0,137,'',0,'13500037777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045450,1,1512045450,0,NULL,0),(142,0,45,'林申','','','','1',0,0,0,0,137,'',0,'13668938888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(143,0,46,'刘洲成','','','','1',0,0,0,0,137,'',0,'13825111888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(144,0,47,'黄晓明','','','','1',0,0,0,0,137,'',0,'18898888899','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(145,0,43,'刘烨','','','','1',0,0,0,0,137,'',0,'13809777777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(146,0,44,'张翰','','','','1',0,0,0,0,137,'',0,'13650999999','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(147,0,45,'杨洋','','','','1',0,0,0,0,137,'',0,'13822211111','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(148,0,46,'宋晓波','','','','1',0,0,0,0,137,'',0,'13660555555','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(149,0,47,'解小东','','','','1',0,0,0,0,137,'',0,'13688888588','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(150,0,43,'窦唯','','','','1',0,0,0,0,137,'',0,'13544555555','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(151,0,44,'姜武','','','','1',0,0,0,0,137,'',0,'13600077777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(152,0,45,'陈泽宇','','','','1',0,0,0,0,137,'',0,'13632277777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(153,0,46,'彭坦','','','','1',0,0,0,0,137,'',0,'13688888887','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045451,1,1512045451,0,NULL,0),(154,0,47,'张一山','','','','1',0,0,0,0,137,'',0,'15899993333','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(155,0,43,'李易峰','','','','1',0,0,0,0,137,'',0,'13719111111','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(156,0,44,'严宽','','','','1',0,0,0,0,137,'',0,'13670111111','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(157,0,45,'东来东往','','','','1',0,0,0,0,137,'',0,'13822177777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(158,0,46,'张国立','','','','1',0,0,0,0,137,'',0,'13660188888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(159,0,47,'王志文','','','','1',0,0,0,0,137,'',0,'13926488888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(160,0,43,'佟大为','','','','1',0,0,0,0,137,'',0,'13711008888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(161,0,44,'柏栩栩','','','','1',0,0,0,0,137,'',0,'13622877777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(162,0,45,'蒲巴甲','','','','1',0,0,0,0,137,'',0,'13729899999','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(163,0,46,'凌潇肃','','','','1',0,0,0,0,137,'',0,'13922222299','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(164,0,47,'李行亮','','','','1',0,0,0,0,137,'',0,'13503099999','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045452,1,1512045452,0,NULL,0),(165,0,43,'毛方圆','','','','1',0,0,0,0,137,'',0,'15915958888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045453,1,1512045453,0,NULL,0),(166,0,44,'张嘉译','','','','1',0,0,0,0,137,'',0,'13822244444','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045453,1,1512045453,0,NULL,0),(167,0,45,'大张伟','','','','1',0,0,0,0,137,'',0,'15099999992','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045453,1,1512045453,0,NULL,0),(168,0,46,'师洋','','','','1',0,0,0,0,137,'',0,'18898886888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045453,1,1512045453,0,NULL,0),(169,0,47,'李幼斌','','','','1',0,0,0,0,137,'',0,'13544388888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045453,1,1512045453,0,NULL,0),(170,0,43,'张磊','','','','1',0,0,0,0,137,'',0,'15815828888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045453,1,1512045453,0,NULL,0),(171,0,44,'朱梓骁','','','','1',0,0,0,0,137,'',0,'13622268888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045453,1,1512045453,0,NULL,0),(172,0,45,'武艺','','','','1',0,0,0,0,137,'',0,'18898883888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(173,0,46,'杨俊毅','','','','1',0,0,0,0,137,'',0,'18818829999','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(174,0,47,'耿乐','','','','1',0,0,0,0,137,'',0,'13826078888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(175,0,43,'钱泳辰','','','','1',0,0,0,0,137,'',0,'13822236666','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(176,0,44,'撒贝宁','','','','1',0,0,0,0,137,'',0,'18320000001','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(177,0,45,'徐峥','','','','1',0,0,0,0,137,'',0,'15917388888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(178,0,46,'谭杰希','','','','1',0,0,0,0,137,'',0,'13829798888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(179,0,47,'黄晟晟','','','','1',0,0,0,0,137,'',0,'13533333313','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(180,0,43,'海鸣威','','','','2',0,0,0,0,137,'',0,'18819266666','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(181,0,44,'汪涵','','','','2',0,0,0,0,137,'',0,'13682255555','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(182,0,45,'王学兵','','','','2',0,0,0,0,137,'',0,'15889933333','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(183,0,46,'贾一平','','','','2',0,0,0,0,137,'',0,'13710155555','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045454,1,1512045454,0,NULL,0),(184,0,47,'孙红雷','','','','2',0,0,0,0,137,'',0,'18320000002','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(185,0,43,'袁文康','','','','2',0,0,0,0,137,'',0,'13763300000','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(186,0,44,'蔡国庆','','','','2',0,0,0,0,137,'',0,'13925100000','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(187,0,45,'吴秀波','','','','2',0,0,0,0,137,'',0,'13422008888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(188,0,46,'王栎鑫','','','','2',0,0,0,0,137,'',0,'18898888883','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(189,0,47,'安琥','','','','2',0,0,0,0,137,'',0,'13610007777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(190,0,43,'刘心','','','','2',0,0,0,0,137,'',0,'13802996666','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(191,0,44,'俞灏明','','','','2',0,0,0,0,137,'',0,'18898888868','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(192,0,45,'张超','','','','2',0,0,0,0,137,'',0,'13660611111','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(193,0,46,'于小彤','','','','2',0,0,0,0,137,'',0,'18898885555','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(194,0,47,'张峻宁','','','','2',0,0,0,0,137,'',0,'13688857777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045455,1,1512045455,0,NULL,0),(195,0,43,'乔任梁','','','','2',0,0,0,0,137,'',0,'18898883333','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045456,1,1512045456,0,NULL,0),(196,0,44,'朴树','','','','2',0,0,0,0,137,'',0,'13533333533','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045456,1,1512045456,0,NULL,0),(197,0,45,'赵帆','','','','2',0,0,0,0,137,'',0,'13533309999','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045456,1,1512045456,0,NULL,0),(198,0,46,'张译','','','','2',0,0,0,0,137,'',0,'13728011111','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045456,1,1512045456,0,NULL,0),(199,0,47,'聂远','','','','2',0,0,0,0,137,'',0,'15999999222','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045456,1,1512045456,0,NULL,0),(200,0,43,'张敬轩','','','','2',0,0,0,0,137,'',0,'18898880000','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045456,1,1512045456,0,NULL,0),(201,0,44,'付辛博','','','','2',0,0,0,0,137,'',0,'18316667777','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045456,1,1512045456,0,NULL,0),(202,0,45,'黄明','','','','2',0,0,0,0,137,'',0,'13802936666','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045456,1,1512045456,0,NULL,0),(203,0,46,'杜海涛','','','','2',0,0,0,0,137,'',0,'18825168888','',1,'',0,'','',0,0,0,0,0,NULL,0.00,0,0,0,0,0,1512045456,1,1512045456,0,NULL,0),(204,0,2,'咨询名单1','','','','1',0,0,0,0,0,'',0,'13928412281','',0,'',0,'','',107,2,113,1,6,20171204,0.00,0,0,1,0,0,1512353547,1,1512372280,0,NULL,0),(205,0,2,'独孤求败','duguqiubai','dgqb','topdog','1',0,1970,1,1,0,'',0,'15684535123','',0,'',0,'','',108,0,113,1,26,20171208,0.00,0,1,3,0,0,1512376518,1,1512730668,0,NULL,0),(206,0,35,'姚明','yaoming','ym','','1',0,1970,1,1,0,'',0,'15532154253','',0,'',0,'','',107,0,113,1,10,20171206,0.00,0,0,0,0,0,1512548862,1,1512549034,0,NULL,0),(207,0,2,'老王','laowang','lw','old wang','1',0,1970,1,1,0,'',0,'15325486456','',0,'',0,'','',108,4,0,0,0,NULL,0.00,0,10003,3,0,0,1512550363,1,1512718329,0,NULL,0),(208,0,2,'小王','xiaowang','xw','','1',0,0,0,0,0,'',0,'15648451245','',0,'',0,'','',109,0,0,0,0,NULL,0.00,0,10002,0,0,0,1513399395,1,1513399395,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='客户销售辅助跟进角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_customer_employee`
--

LOCK TABLES `x360p_customer_employee` WRITE;
/*!40000 ALTER TABLE `x360p_customer_employee` DISABLE KEYS */;
INSERT INTO `x360p_customer_employee` VALUES (1,0,0,4,10005,103,0,0,NULL,0,NULL,0),(2,0,0,207,1,101,0,0,NULL,0,NULL,0),(3,0,0,207,10002,102,0,0,NULL,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='客户跟进记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_customer_follow_up`
--

LOCK TABLES `x360p_customer_follow_up` WRITE;
/*!40000 ALTER TABLE `x360p_customer_follow_up` DISABLE KEYS */;
INSERT INTO `x360p_customer_follow_up` VALUES (1,0,2,2,1,123,'附近工会经费添加加东方今典复合大师艰苦奋斗烧烤架复活甲卡的很空间都哈个',1,112,20171122,0,0,20171122,0,114,1,1511162443,1,1511162443,0,NULL,0),(2,0,2,3,1,123,'发谈谈价格科技园功夫兔',1,112,20171125,1,20171120,20171123,4,113,1,1511162494,1,1511162609,0,NULL,0),(3,0,2,5,1,124,'昨晚吃了啥',1,112,20171201,0,0,20171130,0,115,1,1511336319,1,1511336319,0,NULL,0),(4,0,2,204,1,122,'还没来。',0,0,0,0,0,20171205,3,114,1,1512353621,1,1512353621,0,NULL,0),(5,0,2,207,1,122,'本月活动经费如何使用',1,111,20171208,0,0,20171208,5,113,1,1512638593,1,1512638593,0,NULL,0),(6,0,2,207,1,125,'本次沟通很愉快',1,111,20171209,0,0,20171208,4,113,1,1512717795,1,1512717845,0,NULL,0),(7,0,2,207,0,122,'哈哈哈',1,111,20171216,0,0,20171209,4,113,1,1512718537,1,1512722645,0,NULL,0),(8,0,2,205,1,122,'饿不饿啊',0,111,0,0,0,0,4,113,1,1512721013,1,1512721013,0,NULL,0),(9,0,2,205,1,122,'了解课程',0,112,0,0,0,0,0,113,1,1512721321,1,1512721321,0,NULL,0),(10,0,2,205,0,122,'啊哈哈哈啊啊啊',0,112,0,0,0,0,0,113,1,1512721397,1,1512722047,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='客户意向表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_customer_intention`
--

LOCK TABLES `x360p_customer_intention` WRITE;
/*!40000 ALTER TABLE `x360p_customer_intention` DISABLE KEYS */;
INSERT INTO `x360p_customer_intention` VALUES (1,0,0,204,2,0,0,0,NULL,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COMMENT='部门表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_department`
--

LOCK TABLES `x360p_department` WRITE;
/*!40000 ALTER TABLE `x360p_department` DISABLE KEYS */;
INSERT INTO `x360p_department` VALUES (1,0,0,1,'笋岗校区',2,1510971257,1,1510971258,0,NULL,0),(2,0,0,1,'德兴校区(>_<)',3,1510971281,1,1511754088,0,NULL,0),(3,0,1,0,'后勤部',0,1511164996,1,1511164996,0,NULL,0),(4,0,0,1,'百汇老年校区',4,1511165914,1,1511592640,0,NULL,0),(5,0,4,0,'文艺部',0,1511166263,1,1511166263,0,NULL,0),(6,0,5,0,'班级小组',0,1511166421,1,1511166421,0,NULL,0),(7,0,0,1,'罗湖校区',5,1511351217,1,1511581635,1,1511581635,1),(8,0,0,1,'龙岗校区啊',6,1511352851,1,1511581637,1,1511581637,1),(9,0,0,2,'日日日',0,1511578805,1,1511578824,1,1511578824,1),(10,0,0,2,'水电费',0,1511578812,1,1511578826,1,1511578826,1),(17,0,0,0,'小卖部',0,1511579165,1,1511581267,1,1511581267,1),(22,0,0,2,'坂田校区',0,1511580482,1,1511580800,1,1511580800,1),(26,0,0,1,'坂田校区',0,1511581035,1,1511581269,1,1511581269,1),(27,0,0,1,'宝安校区',0,1511581047,1,1511581632,1,1511581632,1),(28,0,0,1,'福田校区',0,1511581067,1,1511581271,1,1511581271,1),(29,0,0,0,'英语部',0,1511581114,1,1511581114,0,NULL,0),(30,0,0,1,'罗湖2区',0,1511581132,1,1511581265,1,1511581265,1),(31,0,0,1,'湖北校区',0,1511581259,1,1511592509,1,1511592509,1),(32,0,5,1,'文家小学',0,1511581551,1,1511593829,1,1511593829,1),(33,0,0,1,'马蹄山校区',0,1511581575,1,1511592507,1,1511592507,1),(34,0,0,1,'百汇大夏',0,1511581600,1,1511592505,1,1511592505,1),(35,0,0,0,'数学部',0,1511581609,1,1511592503,1,1511592503,1),(36,0,0,1,'文家校区',0,1511581620,1,1511591208,1,1511591208,1),(37,0,35,1,'陇南校区',0,1511581896,1,1511581936,1,1511581936,1),(38,0,1,0,'保洁小组',0,1511593050,1,1511593064,0,NULL,0),(40,0,0,1,'西丽校区',30,1511593181,1,1511593363,1,1511593363,1),(41,0,0,1,'西丽校区',31,1511593374,1,1511593427,1,1511593427,1),(42,0,0,1,'光明顶校区',32,1511593882,1,1511593978,1,1511593978,1),(43,0,0,1,'光明顶校区',33,1511594032,1,1511594528,1,1511594528,1),(44,0,0,1,'光明顶校区',34,1511594550,1,1511594567,1,1511594567,1),(45,0,0,1,'光明顶校区',35,1511594669,1,1511594707,1,1511594707,1),(46,0,0,1,'光明顶校区',36,1511594669,1,1511594691,1,1511594691,1),(47,0,0,1,'光明顶校区',37,1511594922,1,1511594951,1,1511594951,1),(48,0,0,1,'光明顶校区',38,1511594961,1,1511595040,1,1511595040,1),(49,0,0,1,'光明顶校区',39,1511595055,1,1511595055,0,NULL,0),(50,0,0,1,'花都校区',40,1511601063,1,1511601068,1,1511601068,1),(51,0,38,0,'二队',0,1511601082,1,1511601089,0,NULL,0),(52,0,0,1,'蛇口校区',41,1511601111,1,1511601118,1,1511601118,1),(53,0,51,0,'1组',0,1511604948,1,1511604948,0,NULL,0),(54,0,5,0,'表演小组',0,1512010787,1,1512010787,0,NULL,0),(55,0,0,1,'龙岗校区',42,1512030887,1,1512031018,1,1512031018,1),(56,0,0,1,'中山校区',43,1512042870,1,1512042870,0,NULL,0),(57,0,0,1,'喆聪校区',44,1512042881,1,1512042881,0,NULL,0),(58,0,0,1,'唐军校区',45,1512042889,1,1512042889,0,NULL,0),(59,0,0,1,'振威校区',46,1512042897,1,1512042897,0,NULL,0),(60,0,0,1,'成建校区',47,1512042908,1,1512042908,0,NULL,0),(61,0,0,0,'保卫科',0,1513579422,1,1513579422,0,NULL,0),(66,15,0,1,'guapicaozuo',48,1513657356,1,1513657356,0,NULL,0),(67,16,0,1,'siyi',49,1513657811,1,1513665391,1,1513665391,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=1010 DEFAULT CHARSET=utf8mb4 COMMENT='字典表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_dictionary`
--

LOCK TABLES `x360p_dictionary` WRITE;
/*!40000 ALTER TABLE `x360p_dictionary` DISABLE KEYS */;
INSERT INTO `x360p_dictionary` VALUES (1,0,0,'sale_role','销售角色','销售角色','\0',0,1,0,0,NULL,0,0,NULL),(2,0,0,'jobtitle','部门职位','部门职位','',0,1,0,0,NULL,0,0,NULL),(3,0,0,'joblevel','职位级别','职位级别','',0,1,0,0,NULL,0,0,NULL),(4,0,0,'product_level','课程等级','产品等级','',0,1,0,0,NULL,0,0,NULL),(5,0,0,'from','招生来源','招生来源','',0,1,0,0,NULL,0,0,NULL),(6,0,0,'followup','跟进方式','跟进方式','',0,1,0,0,NULL,0,0,NULL),(7,0,0,'promise','诺到类型','诺到类型','',0,1,0,0,NULL,0,0,NULL),(8,0,0,'customer_status','客户跟进状态','客户跟进状态','',0,1,0,0,NULL,0,0,NULL),(9,0,0,'leave_reason','请假原因','请假原因','',0,1,0,0,NULL,0,0,NULL),(10,0,0,'comm_type','沟通方式','沟通方式','',0,1,0,0,NULL,0,0,NULL),(11,0,0,'grade','年级','课程所属年级','',0,1,0,0,NULL,0,0,NULL),(12,0,0,'season','期段','班级课程所属期段','',0,1,0,0,NULL,0,0,NULL),(13,0,0,'timelong','课时长','课时长(分钟)','',0,1,0,0,NULL,0,0,NULL),(14,0,0,'cutamount','结转退费扣款项','结转退费扣款项','',0,1,0,0,NULL,0,0,NULL),(101,0,1,'签单人','签单人','系统内置','',0,1,1508255015,18,1508929710,0,0,NULL),(102,0,1,'电话招生员','电话招生员','','',0,1,1508255053,18,1509007388,0,0,NULL),(103,0,1,'传单宣传员','传单宣传员','','',0,1,0,0,NULL,0,0,NULL),(104,0,1,'客户接待员','客户接待员','','',0,1,0,0,1508920532,0,0,NULL),(105,0,4,'常规课','常规课','常规课','',0,1,1508917664,18,1511343832,0,0,NULL),(106,0,4,'体验课','体验课','体验课','',0,1,1508917709,18,1508917709,0,0,NULL),(107,0,5,'主动上门','主动上门','主动上门','',0,1,1508918008,18,1511343843,0,0,NULL),(108,0,5,'户外广告','户外广告','户外广告','',0,1,1508918340,18,1508918340,0,0,NULL),(109,0,5,'招生活动','招生活动','招生活动','',0,1,1508918360,18,1508918360,0,0,NULL),(110,0,5,'转介绍','转介绍','转介绍','',0,1,1508918385,18,1508918385,0,0,NULL),(111,0,7,'参访校区','参访校区','参访校区','',0,1,1508918581,18,1508918581,0,0,NULL),(112,0,7,'了解课程','了解课程','了解课程','',0,1,1508918591,18,1508918591,0,0,NULL),(113,0,8,'转化成功','转化成功','转化成功','',0,1,1508918674,18,1508918674,0,0,NULL),(114,0,8,'未上门','未上门','未上门','',0,1,1508918739,18,1508918739,0,0,NULL),(115,0,8,'已试听','已试听','已试听','',0,1,1508918752,18,1508918752,0,0,NULL),(116,0,9,'病假','病假','病假','',0,1,1508918772,18,1508918772,0,0,NULL),(117,0,9,'事假','事假','事假','',0,1,1508918781,18,1508918781,0,0,NULL),(118,0,10,'电话','电话','','',0,1,1508918809,18,1508918809,0,0,NULL),(119,0,10,'微信','微信','','',0,1,1508918815,18,1508918815,0,0,NULL),(120,0,10,'QQ','QQ','','',0,1,1508918829,18,1508918829,0,0,NULL),(121,0,10,'面谈','面谈','','',0,1,1508918837,18,1508918837,0,0,NULL),(122,0,6,'电话','电话','','',0,1,1508918934,18,1508918934,0,0,NULL),(123,0,6,'微信','微信','','',0,1,1508918940,18,1508918940,0,0,NULL),(124,0,6,'短信','短信','','',0,1,1508918947,18,1508918947,0,0,NULL),(125,0,6,'QQ','QQ','','',0,1,1508918960,18,1508918960,0,0,NULL),(126,0,2,'课程顾问','课程顾问','','',0,0,1508919046,18,1511345507,0,0,NULL),(127,0,2,'学管师','学管师','','',0,1,1508919056,18,1508919056,0,0,NULL),(128,0,2,'部门主管','部门主管','','',0,1,1508919077,18,1508919077,0,0,NULL),(129,0,11,'1','小一','','',0,1,1508919282,18,1511344003,0,0,NULL),(130,0,11,'2','小二','','',0,1,1508919288,18,1511344011,0,0,NULL),(131,0,11,'3','小三','','',0,1,1508919294,18,1511344021,0,0,NULL),(134,0,11,'4','小四','','',0,1,1508919299,18,1511344027,0,0,NULL),(135,0,11,'5','小五','','',0,1,1508919305,18,1511344034,0,0,NULL),(136,0,11,'6','小六','','',0,1,1508919312,18,1511344043,0,0,NULL),(137,0,11,'7','初一','','',0,1,1508919318,18,1508919331,0,0,NULL),(138,0,11,'8','初二','','',0,1,1508919342,18,1508919342,0,0,NULL),(139,0,11,'9','初三','','',0,1,1508919352,18,1508919352,0,0,NULL),(140,0,11,'10','高一','','',0,1,1508919361,18,1508919361,0,0,NULL),(141,0,11,'11','高二','','',0,1,1508919369,18,1508919369,0,0,NULL),(142,0,11,'12','高三','','',0,1,1508919377,18,1508919377,0,0,NULL),(143,0,12,'H','寒假','H','',0,1,1508919920,18,1508919920,0,0,NULL),(144,0,12,'C','春季','C','',0,1,1508919930,18,1508919938,0,0,NULL),(145,0,12,'S','暑假','S','',0,1,1508919946,18,1508919946,0,0,NULL),(146,0,12,'Q','秋季','Q','',0,1,1508919955,18,1508919955,0,0,NULL),(147,0,13,'30','30分钟','半小时','',0,1,0,0,NULL,0,0,NULL),(148,0,13,'45','45分钟','45分钟','',0,1,0,0,NULL,0,0,NULL),(149,0,13,'60','60分钟','1小时','',0,1,0,0,NULL,0,0,NULL),(150,0,13,'90','90分钟','1个半小时','',0,1,0,0,NULL,0,0,NULL),(151,0,13,'120','120分钟','2个小时','',0,1,0,0,NULL,0,0,NULL),(152,0,13,'150','150分钟','2个半小时','',0,1,0,0,NULL,0,0,NULL),(153,0,13,'180','180分钟','3个小时','',0,1,0,0,NULL,0,0,NULL),(154,0,3,'','初级','初级','',0,1,0,0,NULL,0,0,NULL),(155,0,3,'','中级','中级','',0,1,0,0,NULL,0,0,NULL),(156,0,3,'','高级','高级','',0,1,0,0,NULL,0,0,NULL),(1005,0,4,'','高端课','高端课','\0',0,1,1511165205,1,1511343827,1,1,1511343827),(1006,0,1,'','奉公守法','递四方速递','\0',0,1,1511343774,1,1511343779,1,1,1511343779),(1007,0,9,'','奉公守法','递四方速递','\0',0,1,1511343961,1,1511343966,1,1,1511343966),(1008,0,14,'','违约金','','\0',0,1,1513820731,1,1513820731,0,0,NULL),(1009,0,14,'','物品折旧费用','','\0',0,1,1513820753,1,1513820753,0,0,NULL);
/*!40000 ALTER TABLE `x360p_dictionary` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10027 DEFAULT CHARSET=utf8mb4 COMMENT='员工表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee`
--

LOCK TABLES `x360p_employee` WRITE;
/*!40000 ALTER TABLE `x360p_employee` DISABLE KEYS */;
INSERT INTO `x360p_employee` VALUES (1,0,'管理员','','','','0,2','0,3','','','',1,'','1','17768026488','admin@admin.com','','','',825609600,1996,3,1,0,1,0,0,'',0,1498189748,1,1511339791,1,NULL,0,0),(10002,0,'姚瑞','','','Lenbilon','1,10','2,3,22,23,4,20,21,24,25,26,27,28,29,39,43,44,45,46,47','','','',10001,'17768026489','2','17768026489','yaorui@qq.com','','','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/7954068ab8b53b242c66acd05321dc1b.jpeg',814032000,0,0,0,0,0,20171201,0,'',0,1510972195,1,1513657109,0,NULL,0,0),(10003,0,'刘子云','liuziyun','lzy','Aise','2,4,1','2,3,4','','3,4','',10002,'18617076286','2','18617076286','87740070@qq.com','','','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/6a4b02a7012f905927bf2fbc62ce70d0.png',826214400,1996,3,8,0,0,0,0,'',1,1510992932,1,1512700374,0,NULL,0,0),(10004,0,'老师10004','','','','1','2','','4','',10003,'17768026499','1','17768026499','56224589@qq.com','','','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/937c1873a9d6f772694c46e9dfc44149.jpeg',1265212800,2010,2,4,0,1,20171108,20171108,'',1,1511256940,1,1512440643,0,NULL,0,0),(10005,0,'老师10005','','','','1','2,3,4','','','',10004,'admin@base','1','15623658950','5623@qq.com','','','',1264953600,2010,2,1,0,1,0,0,'',1,1511257049,1,1511495986,1,1511495986,1,0),(10006,0,'马冬梅','','','','1','5','','','',10005,'madongmei','2','18886865656','5555@qq.com','','','',796665600,1995,4,1,0,1,0,0,'',1,1511353834,1,1511353834,0,NULL,0,0),(10009,0,'李花花','','','huauha','4,10,2','4,2,3,5,6','0','','',10006,'huahua@base.com','2','13132323345','www.lantel@qq.com','362321199103298310','6222023202025845706','',0,0,0,0,0,1,20171117,0,'',1,1511495109,1,1512041802,0,NULL,0,0),(10012,0,'dfdfs','','','dd','3,1','22','','','',10007,'dddddd','1','15233434344','','','','',0,0,0,0,0,1,0,0,'',1,1511610986,1,1511919738,0,NULL,0,0),(10013,0,'黄老板','','','','1,2','20,21','','','',10008,'18475486545','1','18475486545','erd@qq.com','','','',633801600,1990,2,1,0,1,0,0,'',1,1511831437,1,1512445790,0,NULL,0,0),(10014,0,'时光','','','','10,7','2,3,4,20,21,22,23,24,25,26,27,28,29,39','0','','',10011,'guanliyuan','1','15234342429','','','','',0,0,0,0,0,1,0,0,'',1,1511835173,1,1512041786,0,NULL,0,0),(10015,0,'德玛西亚','','','demaxiya','1,2,3,4,5,11,12,14','28,24,20,23,27','0','','',10009,'demxy','1','13124222344','4699996969@qq.com','','','',1242057600,2009,5,12,0,1,0,0,'',1,1511836687,1,1511926281,0,NULL,0,0),(10016,0,'德邦总管','','','debang','2,4,5,6,10,11,12','2,3,4,20,21,22,23,24,25,26,27,28,29,39','0','','',10010,'debzg','1','13124233444','ddddd@qq.com','','','',0,0,0,0,0,1,0,0,'',0,1511837091,1,1512041843,0,NULL,0,0),(10017,0,'赵子龙','zhaozilong','zzl','zilong','2,3,4,5,11,14,7,15,1','26','','','',10012,'haha','2','13125132038','yiganchagnqiangn@163.com','','','',0,0,0,0,0,1,0,0,'',1,1511842332,1,1513754634,0,NULL,0,0),(10018,0,'阿木木','','','','2,5,6','2,3,4,20,23,24,25,39,28','0','','',10013,'mumu','1','13123333334','','','','',0,0,0,0,0,1,0,0,'',1,1511845974,1,1512041969,0,NULL,0,0),(10019,0,'金克斯','','','','14','27','0','','',10014,'kesi@lantel.com','1','18123423333','','','','',0,0,0,0,0,1,20171109,0,'',0,1511920164,1,1512040291,0,NULL,0,0),(10020,0,'黄老板2','','','黄老板2','','','','','',0,'','1','15325486254','87747@qq.com','','','',636220800,1990,3,1,0,1,0,0,'',0,1511951486,1,1511951486,0,NULL,0,0),(10021,0,'黄老板3','','','','1,2,3,4,5,6,7,11,12,14','28','0','','',0,'','1','15496844512','1234@qq.com','','','',673023600,1991,5,1,0,1,0,0,'',0,1512040299,1,1512040972,0,NULL,0,0),(10022,0,'李瓜瓜','','','','1','3,22','','','',0,'','1','13123232334','','','','',0,0,0,0,0,1,0,0,'',0,1512041867,1,1512041867,0,NULL,0,0),(10023,0,'刘培红','liupeihong','lph','Rose','1,10','2,22,3,4,20,21,23,24,25,26,27,28,29,39,43,44,45,46,47','','3,2,4','',10015,'liupy','1','13006617502','','','','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/04/305f49ff244ef7d908ebe3a07848c72e.jpeg',1102435200,2004,12,8,0,0,0,0,'',1,1512046182,1,1513760972,0,NULL,0,0),(10024,0,'测试老师','ceshilaoshi','csls','Anni','1,4,11,3','2,3,4,20,21,22,23,24,25,26,27,28,29,39,43,44,45,46,47','','','',0,'','1','15234344343','5435534@qq.com','362321199508018319','6222012398987654321','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/04/6223843989c638d38bd042bb3ef606c4.jpeg',1354896000,2012,12,8,0,0,20171213,20171213,'',0,1512188652,1,1512720795,0,NULL,0,0),(10025,0,'马云','mayun','my','','1,2','2','','','',10029,'may','1','15568789856','','','','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/cd216bccf99221803fec2b9ea270216a.jpeg',881510400,1997,12,8,0,1,0,0,'',1,1512549866,1,1513760961,0,NULL,0,0),(10026,0,'张三','zhangsan','zs','','','2','','','',0,'','1','13821229898','','','','',0,0,0,0,0,1,0,0,'',0,1513578293,1,1513743374,0,NULL,0,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COMMENT='员工部门职能表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee_dept`
--

LOCK TABLES `x360p_employee_dept` WRITE;
/*!40000 ALTER TABLE `x360p_employee_dept` DISABLE KEYS */;
INSERT INTO `x360p_employee_dept` VALUES (1,0,10002,1,1,127,2,0,0,0,0,NULL,0),(2,0,10003,1,2,127,3,0,0,0,0,NULL,0),(3,0,10009,1,7,127,5,0,0,0,0,NULL,0),(6,0,10014,0,29,127,0,0,0,0,0,NULL,0),(7,0,10015,1,4,127,4,0,0,0,0,NULL,0),(8,0,10016,0,29,127,0,0,0,0,0,NULL,0),(9,0,10017,1,4,127,4,0,0,0,0,NULL,0),(10,0,10018,0,29,128,0,0,0,0,0,NULL,0),(11,0,10018,1,1,127,2,0,0,0,0,NULL,0),(12,0,10017,1,1,128,2,0,0,0,0,NULL,0),(13,0,10016,1,1,127,2,0,0,0,0,NULL,0),(14,0,10016,1,2,127,3,0,0,0,0,NULL,0),(15,0,10016,1,4,127,4,0,0,0,0,NULL,0),(16,0,10014,1,1,127,2,0,0,0,0,NULL,0),(17,0,10014,1,2,127,3,0,0,0,0,NULL,0),(18,0,10014,1,4,127,4,0,0,0,0,NULL,0),(19,0,10018,1,2,127,3,0,0,0,0,NULL,0),(20,0,10018,1,4,127,4,0,0,0,0,NULL,0),(21,0,10009,1,1,127,2,0,0,0,0,NULL,0),(22,0,10009,1,2,127,3,0,0,0,0,NULL,0),(23,0,10009,1,4,127,4,0,0,0,0,NULL,0),(24,0,10019,1,4,127,4,0,0,0,0,NULL,0),(25,0,10020,0,29,127,0,0,0,0,0,NULL,0),(26,0,10023,1,1,128,2,0,0,0,0,NULL,0),(27,0,10024,1,1,127,2,0,0,0,0,NULL,0),(28,0,10025,0,3,127,0,0,0,0,0,NULL,0);
/*!40000 ALTER TABLE `x360p_employee_dept` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='员工离职记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee_dimission`
--

LOCK TABLES `x360p_employee_dimission` WRITE;
/*!40000 ALTER TABLE `x360p_employee_dimission` DISABLE KEYS */;
INSERT INTO `x360p_employee_dimission` VALUES (1,10009,1,'',20171125,1511575413,1,1511575413,0,0,NULL),(2,10004,1,'',20171125,1511575507,1,1511575507,0,0,NULL),(3,10002,1,'',20171125,1511575806,1,1511575806,0,0,NULL),(4,10003,1,'就这样吧',20171125,1511575924,1,1511575924,0,0,NULL),(5,10002,1,'',20171125,1511606093,1,1511606093,0,0,NULL),(6,10018,1,'',20171128,1511848960,1,1511848960,0,0,NULL),(7,10024,1,'',20171204,1512367864,1,1512367864,0,0,NULL),(8,10023,1,'',20171204,1512367897,1,1512367897,0,0,NULL);
/*!40000 ALTER TABLE `x360p_employee_dimission` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COMMENT='教师课时产出记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee_lesson_hour`
--

LOCK TABLES `x360p_employee_lesson_hour` WRITE;
/*!40000 ALTER TABLE `x360p_employee_lesson_hour` DISABLE KEYS */;
INSERT INTO `x360p_employee_lesson_hour` VALUES (1,0,2,10002,0,0,1,1,1,0,'',9,0,1,20171118,1000,1200,7,2.00,120,14.00,1680.00,1,1510972694,1,1510991984,0,NULL,0),(2,0,2,10002,10002,0,1,1,1,0,'',21,0,2,20171118,1500,1515,7,2.00,120,14.00,1680.00,1,1510988833,1,1510990849,1,1510990849,1),(3,0,2,10002,10002,0,1,1,1,0,'',22,0,3,20171118,1530,1545,27,2.00,120,54.00,6480.00,1,1510990865,1,1510990878,0,NULL,0),(4,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511237790,1,1511237790,0,NULL,0),(5,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511237790,1,1511237790,0,NULL,0),(6,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511237791,1,1511237791,0,NULL,0),(7,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511237791,1,1511237791,0,NULL,0),(8,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511237792,1,1511237792,0,NULL,0),(9,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511237792,1,1511237792,0,NULL,0),(10,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511237793,1,1511237793,0,NULL,0),(11,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,2.00,120,2.00,0.00,1,1511238107,1,1511238107,0,NULL,0),(12,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,2.00,120,2.00,0.00,1,1511238107,1,1511238107,0,NULL,0),(13,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,2.00,120,2.00,0.00,1,1511238108,1,1511238108,0,NULL,0),(14,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,2.00,120,2.00,0.00,1,1511238108,1,1511238108,0,NULL,0),(15,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,2.00,120,2.00,0.00,1,1511238109,1,1511238109,0,NULL,0),(16,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,2.00,120,2.00,0.00,1,1511238109,1,1511238109,0,NULL,0),(17,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,2.00,120,2.00,0.00,1,1511238110,1,1511238110,0,NULL,0),(18,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,2.00,120,2.00,0.00,1,1511238110,1,1511238110,0,NULL,0),(19,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511238502,1,1511238502,0,NULL,0),(20,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511238502,1,1511238502,0,NULL,0),(21,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511238503,1,1511238503,0,NULL,0),(22,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511238503,1,1511238503,0,NULL,0),(23,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511238504,1,1511238504,0,NULL,0),(24,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511238504,1,1511238504,0,NULL,0),(25,0,2,0,0,0,0,0,0,0,'',0,0,0,0,0,0,1,1.00,60,1.00,0.00,1,1511238505,1,1511238505,0,NULL,0),(26,0,2,10003,10003,0,5,4,6,0,'',84,102,13,20171120,1200,1215,7,1.00,60,7.00,0.00,1,1511244986,1,1511244989,0,NULL,0),(27,0,2,10002,0,0,5,4,6,0,'',56,110,14,20171123,1900,2130,7,1.00,60,7.00,0.00,1,1511245478,1,1511245481,0,NULL,0),(28,0,2,10002,0,0,5,4,6,0,'',58,150,18,20171125,800,1000,34,1.00,60,34.00,6800.00,1,1511246713,1,1511247847,0,NULL,0),(29,0,2,10002,0,0,1,1,1,0,'',86,173,19,20171121,1500,1700,12,2.00,120,24.00,2880.00,1,1511249484,1,1511255550,1,1511255550,1),(32,0,2,10003,0,1,2,0,0,23,'',0,166,0,20171121,1900,2130,1,2.00,120,2.00,363.88,1,1511253544,1,1511253544,0,NULL,0),(33,0,2,10004,0,0,9,4,9,0,'',87,176,20,20171128,1900,2130,0,1.00,60,0.00,0.00,1,1511258165,1,1511265288,1,1511265288,1),(34,0,2,10005,0,1,4,0,0,31,'',0,183,0,20171122,1900,2130,0,1.00,60,0.00,0.00,1,1511316994,1,1511323973,0,NULL,0),(35,0,2,10004,0,0,9,4,9,0,'',87,199,26,20171128,1900,2130,2,1.00,60,2.00,400.00,1,1511340091,1,1511347122,0,NULL,0),(36,0,2,10004,0,0,9,4,9,0,'',93,189,27,20171203,1030,1230,0,1.00,60,0.00,0.00,1,1511340119,1,1511344609,0,NULL,0),(37,0,2,1,0,0,9,4,11,0,'',103,193,28,20171129,1900,2130,2,1.00,60,2.00,220.00,1,1511345504,1,1511345505,0,NULL,0),(38,0,2,10003,10003,0,5,4,12,0,'',109,194,30,20171122,1900,2130,1,1.00,60,1.00,200.00,1,1511346471,1,1511346471,0,NULL,0),(39,0,2,10004,0,0,9,4,9,0,'',88,198,33,20171129,1900,2130,0,1.00,60,0.00,0.00,1,1511346966,1,1511347103,1,1511347103,1),(40,0,2,1,0,0,9,4,11,0,'',107,201,34,20171202,1030,1230,0,1.00,60,0.00,0.00,1,1511352652,1,1511352668,1,1511352668,1),(41,0,2,10004,0,0,9,4,9,0,'',88,202,35,20171129,1900,2130,0,1.00,60,0.00,0.00,1,1511352707,1,1511355671,0,NULL,0),(42,0,2,1,0,0,9,4,11,0,'',104,206,36,20171130,1900,2130,0,1.00,60,0.00,0.00,1,1511352808,1,1511352878,1,1511352878,1),(43,0,2,1,0,0,9,4,11,0,'',104,209,37,20171130,1900,2130,2,1.00,60,2.00,220.00,1,1511352904,1,1511352972,0,NULL,0),(44,0,2,1,10005,0,9,0,13,0,'',0,218,43,20171122,1900,2130,1,1.00,60,1.00,100.00,1,1511353354,1,1511355352,0,NULL,0),(45,0,2,1,1,0,9,0,11,0,'',0,213,40,20171123,1900,2130,1,1.00,60,1.00,100.00,1,1511353956,1,1511353956,0,NULL,0),(46,0,2,1,1,0,9,0,11,0,'',0,215,41,20171124,1900,2130,1,1.00,60,1.00,100.00,1,1511354045,1,1511354045,0,NULL,0),(47,0,2,10005,10005,0,9,0,13,0,'',0,222,44,20171125,800,1000,2,1.00,60,2.00,254.17,1,1511355394,1,1511355394,0,NULL,0),(48,0,2,1,10005,0,9,0,13,0,'',0,224,45,20171126,800,1000,1,1.00,60,1.00,162.50,1,1511355603,1,1511355603,0,NULL,0),(49,0,2,10004,0,0,5,4,6,0,'',113,231,46,20171124,1900,2130,7,1.00,60,7.00,1400.00,1,1511432684,1,1511432688,0,NULL,0),(50,0,2,10004,0,0,1,1,1,0,'',102,240,47,20171121,1900,2130,9,2.00,120,18.00,2160.00,1,1511432735,1,1511432739,0,NULL,0),(51,0,2,10002,0,0,1,1,1,0,'',86,249,48,20171121,1500,1700,7,2.00,120,14.00,1680.00,1,1511573588,1,1511573592,0,NULL,0),(52,0,2,1,0,1,4,0,0,33,'',0,250,0,20171125,1030,1230,1,1.00,60,1.00,200.00,1,1511582586,1,1511582586,0,NULL,0),(53,0,2,10004,0,0,9,4,9,0,'',89,252,49,20171130,1900,2130,0,1.00,60,0.00,0.00,1,1512009291,1,1512371792,1,1512371792,1),(54,0,2,10003,0,0,5,4,6,0,'',69,260,50,20171203,800,1000,0,1.00,60,0.00,0.00,1,1512360340,1,1512381337,1,1512381337,1),(55,0,2,10003,0,0,5,4,6,0,'',69,268,51,20171203,800,1000,0,1.00,60,0.00,0.00,1,1512360984,1,1512381337,1,1512381337,1),(56,0,2,10002,0,0,5,4,6,0,'',69,276,52,20171203,800,1000,0,1.00,60,0.00,0.00,1,1512361714,1,1512381337,1,1512381337,1),(57,0,2,10002,0,0,5,4,6,0,'',69,284,53,20171203,800,1000,0,1.00,60,0.00,0.00,1,1512367674,1,1512381337,1,1512381337,1),(58,0,2,10002,0,0,5,4,6,0,'',69,292,54,20171203,800,1000,0,1.00,60,0.00,0.00,1,1512368463,1,1512381337,1,1512381337,1),(59,0,2,10002,0,0,5,4,6,0,'',69,300,55,20171203,800,1000,0,1.00,60,0.00,0.00,1,1512371561,1,1512381337,1,1512381337,1),(60,0,2,10015,0,0,5,4,6,0,'',69,308,56,20171203,800,1000,0,1.00,60,0.00,0.00,1,1512376791,1,1512381337,1,1512381337,1),(61,0,2,10009,0,0,5,4,6,0,'',69,316,57,20171203,800,1000,0,1.00,60,0.00,0.00,1,1512377269,1,1512381337,1,1512381337,1),(62,0,2,10002,0,0,9,4,11,0,'',108,321,58,20171203,1030,1230,3,1.00,60,3.00,420.00,1,1512377419,1,1512377422,0,NULL,0),(63,0,2,10006,0,0,5,4,6,0,'',69,327,59,20171203,800,1000,6,1.00,60,6.00,1200.00,1,1512381375,1,1512381378,0,NULL,0),(64,0,2,10003,10005,0,9,0,13,0,'',0,335,60,20171205,1900,2130,3,1.00,60,3.00,454.17,1,1512475491,1,1512475801,0,NULL,0),(65,0,2,10002,10005,0,9,0,13,0,'',0,338,61,20171206,1900,2130,0,1.00,60,0.00,0.00,1,1512527718,1,1512527744,0,NULL,0),(66,0,2,10003,10005,0,9,0,13,0,'',0,341,61,20171206,1900,2130,3,1.00,60,3.00,454.17,1,1512527744,1,1512527744,0,NULL,0),(67,0,2,10003,10005,0,9,0,13,0,'',0,344,62,20171204,1900,2130,0,1.00,60,0.00,0.00,1,1512528638,1,1512701622,1,1512701622,1),(68,0,2,10004,0,0,5,4,6,0,'',75,353,63,20171208,1445,1545,3,1.00,60,3.00,600.00,1,1512716636,1,1513421737,0,NULL,0),(69,0,2,10003,0,2,3,0,0,0,'57,52',0,355,0,20171208,1900,2130,1,2.00,120,2.00,360.00,1,1512725205,1,1513421314,0,NULL,0),(70,0,2,0,0,0,5,4,6,0,'',82,358,64,20171213,1900,2130,0,1.00,60,0.00,0.00,1,1513168985,1,1513422394,1,1513422394,1),(72,0,2,10003,10003,0,1,0,7,0,'',0,366,69,20171221,1900,2130,1,2.00,120,2.00,240.00,1,1513821706,1,1513821706,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee_profile`
--

LOCK TABLES `x360p_employee_profile` WRITE;
/*!40000 ALTER TABLE `x360p_employee_profile` DISABLE KEYS */;
INSERT INTO `x360p_employee_profile` VALUES (1,0,1,'','人生已经如此的难忘',NULL,NULL,1507286229,0,1512006141,0,NULL,0),(40,0,10002,NULL,NULL,NULL,NULL,1510972195,1,1510972195,0,NULL,0),(41,0,10003,NULL,NULL,NULL,NULL,1510992932,1,1510992932,0,NULL,0),(42,0,10003,'<p>风一样的男人</p>',NULL,NULL,NULL,1510992932,1,1512377010,0,NULL,0),(43,0,10004,NULL,NULL,NULL,NULL,1511256940,1,1511256940,0,NULL,0),(44,0,10005,NULL,NULL,NULL,NULL,1511257049,1,1511257049,0,NULL,0),(45,0,10006,NULL,NULL,NULL,NULL,1511353834,1,1511353834,0,NULL,0),(48,0,10009,NULL,NULL,NULL,NULL,1511495109,1,1511495109,0,NULL,0),(51,0,10012,NULL,NULL,NULL,NULL,1511610986,1,1511610986,0,NULL,0),(52,0,10013,NULL,NULL,NULL,NULL,1511831437,1,1511831437,0,NULL,0),(53,0,10013,'<p>哈哈</p>',NULL,NULL,NULL,1511831437,1,1512445790,0,NULL,0),(54,0,10014,NULL,NULL,NULL,NULL,1511835173,1,1511835173,0,NULL,0),(55,0,10015,NULL,NULL,NULL,NULL,1511836687,1,1511836687,0,NULL,0),(56,0,10016,NULL,NULL,NULL,NULL,1511837091,1,1511837091,0,NULL,0),(57,0,10017,NULL,NULL,NULL,NULL,1511842332,1,1511842332,0,NULL,0),(58,0,10018,NULL,NULL,NULL,NULL,1511845974,1,1511845974,0,NULL,0),(59,0,10019,NULL,NULL,NULL,NULL,1511920164,1,1511920164,0,NULL,0),(60,0,10020,NULL,NULL,NULL,NULL,1511951486,1,1511951486,0,NULL,0),(61,0,10021,NULL,NULL,NULL,NULL,1512040299,1,1512040299,0,NULL,0),(62,0,10022,NULL,NULL,NULL,NULL,1512041867,1,1512041867,0,NULL,0),(63,0,10023,NULL,NULL,NULL,NULL,1512046182,1,1512046182,0,NULL,0),(64,0,10024,NULL,NULL,NULL,NULL,1512188652,1,1512188652,0,NULL,0),(65,0,10025,NULL,NULL,NULL,NULL,1512549866,1,1512549866,0,NULL,0),(66,0,10026,NULL,NULL,NULL,NULL,1513578293,1,1513578293,0,NULL,0);
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
  `delete_uid` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`er_id`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8mb4 COMMENT='用户所属角色表(每一个用户可以拥有0个或多个用户角色)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee_role`
--

LOCK TABLES `x360p_employee_role` WRITE;
/*!40000 ALTER TABLE `x360p_employee_role` DISABLE KEYS */;
INSERT INTO `x360p_employee_role` VALUES (5,0,10004,1,0,0,NULL,0,NULL),(8,0,10006,1,0,0,NULL,0,NULL),(10,0,10009,4,0,0,NULL,0,NULL),(11,0,10009,10,0,0,NULL,0,NULL),(19,0,10012,3,0,0,NULL,0,NULL),(20,0,10013,1,0,0,NULL,0,NULL),(21,0,10014,10,0,0,NULL,0,NULL),(22,0,10015,1,0,0,NULL,0,NULL),(23,0,10015,2,0,0,NULL,0,NULL),(24,0,10015,3,0,0,NULL,0,NULL),(25,0,10015,4,0,0,NULL,0,NULL),(26,0,10015,5,0,0,NULL,0,NULL),(28,0,10015,11,0,0,NULL,0,NULL),(29,0,10015,12,0,0,NULL,0,NULL),(30,0,10015,14,0,0,NULL,0,NULL),(32,0,10016,2,0,0,NULL,0,NULL),(34,0,10016,4,0,0,NULL,0,NULL),(35,0,10016,5,0,0,NULL,0,NULL),(38,0,10016,10,0,0,NULL,0,NULL),(39,0,10016,11,0,0,NULL,0,NULL),(40,0,10016,12,0,0,NULL,0,NULL),(43,0,10017,2,0,0,NULL,0,NULL),(44,0,10017,3,0,0,NULL,0,NULL),(45,0,10017,4,0,0,NULL,0,NULL),(46,0,10017,5,0,0,NULL,0,NULL),(50,0,10017,11,0,0,NULL,0,NULL),(52,0,10017,14,0,0,NULL,0,NULL),(54,0,10018,2,0,0,NULL,0,NULL),(56,0,10012,1,0,0,NULL,0,NULL),(57,0,10013,2,0,0,NULL,0,NULL),(60,0,10018,5,0,0,NULL,0,NULL),(62,0,10019,14,0,0,NULL,0,NULL),(76,0,10021,1,0,0,NULL,0,NULL),(77,0,10021,2,0,0,NULL,0,NULL),(78,0,10021,3,0,0,NULL,0,NULL),(79,0,10021,4,0,0,NULL,0,NULL),(80,0,10021,5,0,0,NULL,0,NULL),(82,0,10021,7,0,0,NULL,0,NULL),(84,0,10021,11,0,0,NULL,0,NULL),(85,0,10021,12,0,0,NULL,0,NULL),(86,0,10021,14,0,0,NULL,0,NULL),(89,0,10017,7,0,0,NULL,0,NULL),(90,0,10017,15,0,0,NULL,0,NULL),(91,0,10017,1,0,0,NULL,0,NULL),(92,0,10014,7,0,0,NULL,0,NULL),(93,0,10009,2,0,0,NULL,0,NULL),(94,0,10022,1,0,0,NULL,0,NULL),(97,0,10024,1,0,0,NULL,0,NULL),(98,0,10024,4,0,0,NULL,0,NULL),(99,0,10024,11,0,0,NULL,0,NULL),(100,0,10024,3,0,0,NULL,0,NULL),(101,0,10002,4,0,0,NULL,0,NULL),(102,0,10002,2,0,0,NULL,0,NULL),(103,0,10002,2,0,0,NULL,0,NULL),(104,0,10002,3,0,0,NULL,0,NULL),(105,0,10002,4,0,0,NULL,0,NULL),(106,0,10002,3,0,0,NULL,0,NULL),(107,0,10002,4,0,0,NULL,0,NULL),(108,0,10002,3,0,0,NULL,0,NULL),(109,0,10002,4,0,0,NULL,0,NULL),(110,0,10002,3,0,0,NULL,0,NULL),(111,0,10002,4,0,0,NULL,0,NULL),(112,0,10002,2,0,0,NULL,0,NULL),(113,0,10002,3,0,0,NULL,0,NULL),(114,0,10002,4,0,0,NULL,0,NULL),(115,0,10002,2,0,0,NULL,0,NULL),(116,0,10002,3,0,0,NULL,0,NULL),(117,0,10002,4,0,0,NULL,0,NULL),(118,0,10002,3,0,0,NULL,0,NULL),(119,0,10002,4,0,0,NULL,0,NULL),(120,0,10002,2,0,0,NULL,0,NULL),(121,0,10002,3,0,0,NULL,0,NULL),(122,0,10002,4,0,0,NULL,0,NULL),(123,0,10002,2,0,0,NULL,0,NULL),(125,0,10002,3,0,0,NULL,0,NULL),(126,0,10002,4,0,0,NULL,0,NULL),(128,0,10002,3,0,0,NULL,0,NULL),(130,0,10002,2,0,0,NULL,0,NULL),(132,0,10002,2,0,0,NULL,0,NULL),(134,0,10002,2,0,0,NULL,0,NULL),(135,0,10002,3,0,0,NULL,0,NULL),(137,0,10002,2,0,0,NULL,0,NULL),(138,0,10002,3,0,0,NULL,0,NULL),(139,0,10002,4,0,0,NULL,0,NULL),(141,0,10002,2,0,0,NULL,0,NULL),(142,0,10002,4,0,0,NULL,0,NULL),(144,0,10002,2,0,0,NULL,0,NULL),(145,0,10002,4,0,0,NULL,0,NULL),(146,0,10002,3,0,0,NULL,0,NULL),(147,0,10004,4,0,0,NULL,0,NULL),(148,0,10004,3,0,0,NULL,0,NULL),(149,0,10004,2,0,0,NULL,0,NULL),(151,0,10002,2,0,0,NULL,0,NULL),(152,0,10002,4,0,0,NULL,0,NULL),(153,0,10002,3,0,0,NULL,0,NULL),(154,0,10023,3,0,0,NULL,0,NULL),(156,0,10002,2,0,0,NULL,0,NULL),(157,0,10002,3,0,0,NULL,0,NULL),(159,0,10002,2,0,0,NULL,0,NULL),(160,0,10002,3,0,0,NULL,0,NULL),(161,0,10002,4,0,0,NULL,0,NULL),(162,0,10023,3,0,0,NULL,0,NULL),(163,0,10023,2,0,0,NULL,0,NULL),(165,0,10002,2,0,0,NULL,0,NULL),(166,0,10002,3,0,0,NULL,0,NULL),(167,0,10003,3,0,0,NULL,0,NULL),(169,0,10002,2,0,0,NULL,0,NULL),(170,0,10002,3,0,0,NULL,0,NULL),(171,0,10002,4,0,0,NULL,0,NULL),(173,0,10002,2,0,0,NULL,0,NULL),(174,0,10002,3,0,0,NULL,0,NULL),(176,0,10002,2,0,0,NULL,0,NULL),(177,0,10002,3,0,0,NULL,0,NULL),(179,0,10002,2,0,0,NULL,0,NULL),(180,0,10002,3,0,0,NULL,0,NULL),(181,0,10002,4,0,0,NULL,0,NULL),(183,0,10002,2,0,0,NULL,0,NULL),(184,0,10002,3,0,0,NULL,0,NULL),(186,0,10002,2,0,0,NULL,0,NULL),(187,0,10002,3,0,0,NULL,0,NULL),(188,0,10002,4,0,0,NULL,0,NULL),(190,0,10002,2,0,0,NULL,0,NULL),(191,0,10002,3,0,0,NULL,0,NULL),(192,0,10002,4,0,0,NULL,0,NULL),(194,0,10025,1,0,0,NULL,0,NULL),(195,0,10025,2,0,0,NULL,0,NULL),(198,0,10002,2,0,0,NULL,0,NULL),(199,0,10002,3,0,0,NULL,0,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COMMENT='员工部门职能表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_employee_subject`
--

LOCK TABLES `x360p_employee_subject` WRITE;
/*!40000 ALTER TABLE `x360p_employee_subject` DISABLE KEYS */;
INSERT INTO `x360p_employee_subject` VALUES (1,0,0,10004,4,0,0,0,0,NULL,0),(11,0,0,10021,3,0,0,0,0,NULL,0),(12,0,0,10021,2,0,0,0,0,NULL,0),(27,0,0,10003,3,0,0,0,0,NULL,0),(28,0,0,10023,3,0,0,0,0,NULL,0),(29,0,0,10023,2,0,0,0,0,NULL,0),(31,0,0,10023,4,0,0,0,0,NULL,0),(33,0,0,10003,4,0,0,0,0,NULL,0);
/*!40000 ALTER TABLE `x360p_employee_subject` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COMMENT='系统文件表(所有上传的附件文件，都会记录下来，有一个唯一的file_id)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_file`
--

LOCK TABLES `x360p_file` WRITE;
/*!40000 ALTER TABLE `x360p_file` DISABLE KEYS */;
INSERT INTO `x360p_file` VALUES (1,0,'student_avatar','qiniu',36,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/1/17/11/22/99539c442b28deab4545600a1e41a73f.jpeg','http://s10.xiao360.com//x360pstudent_avatar/1/17/11/22/99539c442b28deab4545600a1e41a73f.jpeg','image','Koala',780831,'',1511355047,1,1511355047,0,NULL,0,'sfdsfs','',''),(2,0,'lesson_cover_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/lesson_cover_picture/1/17/11/23/00f754c8bc2484725664e40e0393493e.jpg','http://s10.xiao360.com//x360plesson_cover_picture/1/17/11/23/00f754c8bc2484725664e40e0393493e.jpg','image','}@(20Y3NR)B6ES@O(@2ROSW.jpg',1815,'',1511436665,1,1511436665,0,NULL,0,'','',''),(3,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/11/24/ae48031d06e691a09ab6881b3d2ee41b.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/24/ae48031d06e691a09ab6881b3d2ee41b.jpg','image','8E9$V2X3{EL981]9S~B$~EY.jpg',22299,'',1511497733,1,1511497733,0,NULL,0,'','',''),(4,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/11/24/82afa94a68b334bb802f0fa028865790.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/24/82afa94a68b334bb802f0fa028865790.jpg','image','A2A919689210E0C8ED6E4CC6BE9F81D5.jpg',62605,'',1511503706,1,1511503706,0,NULL,0,'','',''),(5,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/11/24/ff25c67a53d118ddb7a107e82845ec24.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/24/ff25c67a53d118ddb7a107e82845ec24.jpg','image','8H19XW%DMN8EWV2ETX4_ZHP.jpg',12281,'',1511504970,1,1511504970,0,NULL,0,'','',''),(6,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/11/28/5c46a9a77d59a7ea76df1d5769b68ebe.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/28/5c46a9a77d59a7ea76df1d5769b68ebe.jpg','image','59f299b4Nc9e78adb.jpg',15780,'',1511833918,1,1511833918,0,NULL,0,'','',''),(8,0,'student_avatar','qiniu',36,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/1/17/11/30/2a506ec46c110a79323ad91ad9b30dd4.gif','http://s10.xiao360.com//x360pstudent_avatar/1/17/11/30/2a506ec46c110a79323ad91ad9b30dd4.gif','image','03E4A78C7F9520F06876C8446A24F9AC',993935,'',1512005442,1,1512005442,0,NULL,0,'','',''),(10,0,'student_avatar','qiniu',36,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/1/17/11/30/455a2907bf8495e4e9bf51b3ab423c71.png','http://s10.xiao360.com//x360pstudent_avatar/1/17/11/30/455a2907bf8495e4e9bf51b3ab423c71.png','image','03E4A78C7F9520F06876C8446A24F9AC',41518,'',1512005863,1,1512005863,0,NULL,0,'','',''),(11,0,'student_avatar','qiniu',36,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/1/17/11/30/30146890afc60dbbfb1facf9e5c2df49.gif','http://s10.xiao360.com//x360pstudent_avatar/1/17/11/30/30146890afc60dbbfb1facf9e5c2df49.gif','image','4DDAB2AF72C60C6E501D7DEED3F22BA0',15488,'',1512005893,1,1512005893,0,NULL,0,'','',''),(12,0,'avatar','qiniu',1,'/storage1/www/pro.xiao360.com/public/data/uploads/avatar/1/17/11/30/06b23553ffcd1dd9f71a659fc15a5b48.jpg','http://s10.xiao360.com//x360pavatar/1/17/11/30/06b23553ffcd1dd9f71a659fc15a5b48.jpg','image','}@(20Y3NR)B6ES@O(@2ROSW.jpg',5358,'',1512006401,1,1512006401,0,NULL,0,'','',''),(15,0,'student_avatar','qiniu',41,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/1/17/11/30/3861e1e25d8c2c979243e3c7b1272e87.jpeg','http://s10.xiao360.com//x360pstudent_avatar/1/17/11/30/3861e1e25d8c2c979243e3c7b1272e87.jpeg','image','}@(20Y3NR)B6ES@O(@2ROSW',1815,'',1512008996,1,1512008996,0,NULL,0,'','',''),(21,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/11/30/f2cbaff69942f452ac66d835843236a2.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/30/f2cbaff69942f452ac66d835843236a2.jpg','image','45FED593D4D0C2C8B9EAB1325F4FE33F.jpg',87583,'',1512010051,1,1512010051,0,NULL,0,'','',''),(22,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/11/30/7781121bb13c6bbdfc63750a33c6fdff.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/30/7781121bb13c6bbdfc63750a33c6fdff.jpg','image','SQEVFWP3P{8)QIZ6`MKXNPO.jpg',2267,'',1512012074,1,1512012074,0,NULL,0,'','',''),(23,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/11/30/aba47839b0e9c7ba167cb739742c174f.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/30/aba47839b0e9c7ba167cb739742c174f.jpg','image','3T4203(T6DU5SAF{8_@`226.jpg',2835,'',1512012329,1,1512012329,0,NULL,0,'','',''),(24,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/11/30/85d4670d0f1e1723bea124d4c14b6b02.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/30/85d4670d0f1e1723bea124d4c14b6b02.jpg','image',')Z5SHJJNNYZOO430IH`KSBJ.jpg',46539,'',1512012388,1,1512012388,0,NULL,0,'','',''),(25,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/11/30/b4c264aff5f799bc65a3effa5b970838.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/30/b4c264aff5f799bc65a3effa5b970838.jpg','image','8E9$V2X3{EL981]9S~B$~EY.jpg',22299,'',1512012471,1,1512012471,0,NULL,0,'','',''),(26,0,'user_avatar','qiniu',1,'/storage1/www/pro.xiao360.com/public/data/uploads/user_avatar/1/17/12/04/f29a6e24323727fb995602be786f4c78.jpeg','http://s10.xiao360.com//x360puser_avatar/1/17/12/04/f29a6e24323727fb995602be786f4c78.jpeg','image','u=2764371306,3467823016&fm=214&gp=0',11113,'',1512360401,1,1512360401,0,NULL,0,'','',''),(31,0,'employee_avatar','qiniu',10023,'/storage1/www/pro.xiao360.com/public/data/uploads/employee_avatar/1/17/12/04/305f49ff244ef7d908ebe3a07848c72e.jpeg','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/04/305f49ff244ef7d908ebe3a07848c72e.jpeg','image','帅哥',10604,'',1512361208,1,1513418083,0,NULL,0,'','',''),(32,0,'employee_avatar','qiniu',10024,'/storage1/www/pro.xiao360.com/public/data/uploads/employee_avatar/1/17/12/04/6223843989c638d38bd042bb3ef606c4.jpeg','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/04/6223843989c638d38bd042bb3ef606c4.jpeg','image','alaboren.jpg',13415,'',1512361612,1,1513418257,0,NULL,0,'','',''),(36,0,'student_avatar','qiniu',65,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/1/17/12/07/e885e1b891ecda4eaa25c53ce4210b08.jpeg','http://s10.xiao360.com//x360pstudent_avatar/1/17/12/07/e885e1b891ecda4eaa25c53ce4210b08.jpeg','image','3T4203(T6DU5SAF{8_@`226',2835,'',1512633168,1,1512633168,0,NULL,0,'','',''),(39,0,'student_avatar','qiniu',78,'/storage1/www/pro.xiao360.com/public/data/uploads/student_avatar/1/17/12/13/bd0cc536cd4c2e95fab530f7067e1946.jpeg','http://s10.xiao360.com//x360pstudent_avatar/1/17/12/13/bd0cc536cd4c2e95fab530f7067e1946.jpeg','image','二瓜',19013,'',1513168095,1,1513418269,0,NULL,0,'','',''),(43,0,'employee_avatar','qiniu',10003,'/storage1/www/pro.xiao360.com/public/data/uploads/employee_avatar/1/17/12/14/6a4b02a7012f905927bf2fbc62ce70d0.png','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/6a4b02a7012f905927bf2fbc62ce70d0.png','image','小黄鸡.jpg',160028,'',1513232729,1,1513418224,0,NULL,0,'o7y5d0aBJiz7zw-TMwa5N80o_c30','',''),(47,0,'employee_avatar','qiniu',10002,'/storage1/www/pro.xiao360.com/public/data/uploads/employee_avatar/1/17/12/14/7954068ab8b53b242c66acd05321dc1b.jpeg','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/7954068ab8b53b242c66acd05321dc1b.jpeg','image','泡泡',4781,'',1513233185,1,1513420372,0,NULL,0,'o7y5d0aBJiz7zw-TMwa5N80o_c30','',''),(50,0,'employee_avatar','qiniu',10004,'/storage1/www/pro.xiao360.com/public/data/uploads/employee_avatar/1/17/12/14/937c1873a9d6f772694c46e9dfc44149.jpeg','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/937c1873a9d6f772694c46e9dfc44149.jpeg','image','傻蛋',7252,'',1513233527,1,1513421108,0,NULL,0,'o7y5d0aBJiz7zw-TMwa5N80o_c30','',''),(51,0,'employee_avatar','qiniu',10025,'/storage1/www/pro.xiao360.com/public/data/uploads/employee_avatar/1/17/12/14/cd216bccf99221803fec2b9ea270216a.jpeg','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/cd216bccf99221803fec2b9ea270216a.jpeg','image','sha',8697,'',1513234671,1,1513234671,0,NULL,0,'o7y5d0aBJiz7zw-TMwa5N80o_c30','',''),(52,0,'','qiniu',0,'','http://s10.xiao360.com/qms//demo/wechat_media/XkuCOlPTvmg43bCdfKN_nKyp6U3Ue2yl2ubdPDv_3b773NnnirqohMl1F4N3u4Tp.mp4','mp4','这是一段视频',1535218,'32',1513221014,1,1513419434,0,NULL,0,'o7y5d0aBJiz7zw-TMwa5N80o_c30','video','XkuCOlPTvmg43bCdfKN_nKyp6U3Ue2yl2ubdPDv_3b773NnnirqohMl1F4N3u4Tp'),(53,0,'','qiniu',0,'','http://s10.xiao360.com/qms//demo/wechat_media/FXtxpyhAu8_gV5zca-6-JkKJBZvMbmDjAc7mB2YKaDCQQE75F7r6dZl973da8-4O.mp3','mp3','这是一段语音',3534,'5',1513232430,1,1513421285,0,NULL,0,'o7y5d0aBJiz7zw-TMwa5N80o_c30','voice','FXtxpyhAu8_gV5zca-6-JkKJBZvMbmDjAc7mB2YKaDCQQE75F7r6dZl973da8-4O'),(54,0,'','qiniu',0,'','http://s10.xiao360.com/qms//demo/wechat_media/lw3e9GVyJWUwKJw5LMHkiXygrDr3oH1xw2jFlmXfFNfrBIuMfdG-EjQXUmkl6zI3.mp3','mp3','这是一段语音',3464,'12',1513394283,1,1513421294,0,NULL,0,'o7y5d0aBJiz7zw-TMwa5N80o_c30','voice','lw3e9GVyJWUwKJw5LMHkiXygrDr3oH1xw2jFlmXfFNfrBIuMfdG-EjQXUmkl6zI3'),(55,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/12/19/7ffda78deacb49cecf82d2ab07524657.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/12/19/7ffda78deacb49cecf82d2ab07524657.jpg','image','IMG_3172.JPG',100478,'',1513677107,1,1513677107,0,NULL,0,'','',''),(56,0,'material_picture','qiniu',0,'/storage1/www/pro.xiao360.com/public/data/uploads/material_picture/1/17/12/19/ddcc2a6de8222740a3a9d400152033f9.jpg','http://s10.xiao360.com//x360pmaterial_picture/1/17/12/19/ddcc2a6de8222740a3a9d400152033f9.jpg','image','IMG_3169.JPG',100835,'',1513677134,1,1513677134,0,NULL,0,'','','');
/*!40000 ALTER TABLE `x360p_file` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_holiday`
--

LOCK TABLES `x360p_holiday` WRITE;
/*!40000 ALTER TABLE `x360p_holiday` DISABLE KEYS */;
INSERT INTO `x360p_holiday` VALUES (1,0,0,'周末放假',20171126,2017,1510971361,1,1510971361,0,NULL,0),(2,0,0,'周末放假',20171203,2017,1510971361,1,1510971361,0,NULL,0),(3,0,0,'放假',20170418,2017,1511161250,1,1511161250,0,NULL,0);
/*!40000 ALTER TABLE `x360p_holiday` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='表单数据模板';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_input_template`
--

LOCK TABLES `x360p_input_template` WRITE;
/*!40000 ALTER TABLE `x360p_input_template` DISABLE KEYS */;
INSERT INTO `x360p_input_template` VALUES (1,0,2,2,'支出1','{\"tid\":38,\"og_id\":0,\"bid\":2,\"type\":2,\"cate\":\"2\",\"relate_id\":0,\"aa_id\":5,\"to_aa_id\":0,\"tt_id\":4,\"item_th_id\":2,\"client_th_id\":1,\"employee_th_id\":3,\"amount\":1000,\"remark\":\"年费啊\",\"int_day\":\"2017-11-23\"}',1,1511422356,NULL,NULL,0,NULL),(2,0,2,1,'收入1','{\"tid\":37,\"og_id\":0,\"bid\":2,\"type\":1,\"cate\":\"1\",\"relate_id\":0,\"aa_id\":5,\"to_aa_id\":0,\"tt_id\":2,\"item_th_id\":7,\"client_th_id\":4,\"employee_th_id\":6,\"amount\":5000,\"remark\":\"年费\",\"int_day\":\"2017-11-22\"}',1,1511423264,NULL,NULL,0,NULL),(3,0,1,0,'数学一条龙','[{\"id\":1,\"lid\":1,\"gid\":0,\"name\":\"3年级奥数\",\"gtype\":0,\"nums\":30,\"nums_unit\":2,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"120.00\",\"price\":\"120.00\",\"origin_amount\":3600,\"discount_amount\":0,\"paid_amount\":3600,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":3600,\"reduced_amount\":0,\"cid\":0,\"_is_term\":1,\"_unit_lesson_hours\":\"2.00\",\"_is_average\":false,\"_share_own_amount\":0},{\"id\":2,\"lid\":2,\"gid\":0,\"name\":\"3年级奥数1对1\",\"gtype\":0,\"nums\":30,\"nums_unit\":2,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"220.00\",\"price\":\"220.00\",\"origin_amount\":6600,\"discount_amount\":0,\"paid_amount\":6600,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":6600,\"reduced_amount\":0,\"cid\":0,\"_is_term\":1,\"_unit_lesson_hours\":\"2.00\",\"_is_average\":false,\"_share_own_amount\":0}]',1,1511943352,1511946793,1,1,1511946793),(4,0,1,0,'艺术之旅','[{\"id\":1,\"lid\":5,\"gid\":0,\"name\":\"艺术课课时包\",\"gtype\":0,\"nums\":1,\"nums_unit\":1,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"200.00\",\"price\":\"200.00\",\"origin_amount\":200,\"discount_amount\":0,\"paid_amount\":200,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":200,\"reduced_amount\":0,\"cid\":0,\"_is_term\":1,\"_unit_lesson_hours\":\"1.00\",\"_is_average\":false,\"_share_own_amount\":0},{\"id\":2,\"lid\":6,\"gid\":0,\"name\":\"一对一艺术课时包\",\"gtype\":0,\"nums\":1,\"nums_unit\":1,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"200.00\",\"price\":\"200.00\",\"origin_amount\":200,\"discount_amount\":0,\"paid_amount\":200,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":200,\"reduced_amount\":0,\"cid\":0,\"_is_term\":0,\"_unit_lesson_hours\":\"1.00\",\"_is_average\":false,\"_share_own_amount\":0}]',1,1511944947,1511946790,1,1,1511946790),(5,0,1,0,'作文天地','[{\"id\":3,\"lid\":8,\"gid\":0,\"name\":\"作文作文作文\",\"gtype\":0,\"nums\":1,\"nums_unit\":2,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"900.00\",\"price\":\"900.00\",\"origin_amount\":900,\"discount_amount\":0,\"paid_amount\":900,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":900,\"reduced_amount\":0,\"cid\":0,\"_is_term\":1,\"_unit_lesson_hours\":\"1.00\",\"_is_average\":false,\"_share_own_amount\":0}]',1,1511944999,1511946667,1,1,1511946667),(6,0,1,0,'数学天地','[{\"id\":1,\"lid\":3,\"gid\":0,\"name\":\"3年级奥数1对多\",\"gtype\":0,\"nums\":30,\"nums_unit\":2,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"180.00\",\"price\":\"180.00\",\"origin_amount\":5400,\"discount_amount\":0,\"paid_amount\":5400,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":5400,\"reduced_amount\":0,\"cid\":0,\"_is_term\":1,\"_unit_lesson_hours\":\"2.00\",\"_is_average\":false,\"_share_own_amount\":0},{\"id\":2,\"lid\":2,\"gid\":0,\"name\":\"3年级奥数1对1\",\"gtype\":0,\"nums\":30,\"nums_unit\":2,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"220.00\",\"price\":\"220.00\",\"origin_amount\":6600,\"discount_amount\":0,\"paid_amount\":6600,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":6600,\"reduced_amount\":0,\"cid\":0,\"_is_term\":1,\"_unit_lesson_hours\":\"2.00\",\"_is_average\":false,\"_share_own_amount\":0}]',1,1511947045,1511947045,NULL,0,NULL),(7,0,1,0,'艺术细胞很多','[{\"id\":1,\"lid\":5,\"gid\":0,\"name\":\"艺术课课时包\",\"gtype\":0,\"nums\":1,\"nums_unit\":1,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"200.00\",\"price\":\"200.00\",\"origin_amount\":200,\"discount_amount\":0,\"paid_amount\":200,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":200,\"reduced_amount\":0,\"_is_term\":1,\"_unit_lesson_hours\":\"1.00\",\"_is_average\":false,\"_share_own_amount\":0}]',1,1511947107,1511947107,NULL,0,NULL),(8,0,1,0,'作文辅导','[{\"id\":1,\"lid\":8,\"gid\":0,\"name\":\"作文作文作文\",\"gtype\":0,\"nums\":3,\"nums_unit\":2,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"900.00\",\"price\":\"900.00\",\"origin_amount\":\"2700.00\",\"discount_amount\":\"0.00\",\"paid_amount\":2700,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":2700,\"reduced_amount\":0,\"_is_term\":1,\"_unit_lesson_hours\":\"1.00\",\"_is_average\":false,\"_share_own_amount\":0}]',1,1511947297,1511947297,NULL,0,NULL),(9,0,1,0,'全方位发展','[{\"id\":1,\"lid\":3,\"gid\":0,\"name\":\"3年级奥数1对多\",\"gtype\":0,\"nums\":30,\"nums_unit\":2,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"180.00\",\"price\":\"180.00\",\"origin_amount\":5400,\"discount_amount\":0,\"paid_amount\":5023.26,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":5337.21,\"reduced_amount\":\"62.79\",\"_is_term\":1,\"_unit_lesson_hours\":\"2.00\",\"_is_average\":true,\"_share_own_amount\":\"313.95\"},{\"id\":2,\"lid\":5,\"gid\":0,\"name\":\"艺术课课时包\",\"gtype\":0,\"nums\":1,\"nums_unit\":1,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"200.00\",\"price\":\"200.00\",\"origin_amount\":200,\"discount_amount\":0,\"paid_amount\":186.04,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":197.67,\"reduced_amount\":\"2.33\",\"_is_term\":1,\"_unit_lesson_hours\":\"1.00\",\"_is_average\":true,\"_share_own_amount\":\"11.63\"},{\"id\":3,\"lid\":7,\"gid\":0,\"name\":\"一对多英语\",\"gtype\":0,\"nums\":30,\"nums_unit\":2,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"100.00\",\"price\":\"100.00\",\"origin_amount\":3000,\"discount_amount\":0,\"paid_amount\":2790.7,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":2965.12,\"reduced_amount\":\"34.88\",\"_is_term\":1,\"_unit_lesson_hours\":\"1.00\",\"_is_average\":true,\"_share_own_amount\":\"174.42\"}]',1,1512004808,1512004808,NULL,0,NULL),(10,0,1,0,'数学特招','[{\"id\":1,\"lid\":3,\"gid\":0,\"name\":\"3年级奥数1对多\",\"gtype\":0,\"nums\":30,\"nums_unit\":2,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"180.00\",\"price\":\"180.00\",\"origin_amount\":5400,\"discount_amount\":0,\"paid_amount\":5400,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":5400,\"reduced_amount\":0,\"_is_term\":1,\"_unit_lesson_hours\":\"2.00\",\"_is_average\":false,\"_share_own_amount\":0},{\"id\":2,\"lid\":2,\"gid\":0,\"name\":\"3年级奥数1对1\",\"gtype\":0,\"nums\":30,\"nums_unit\":2,\"expire_time\":false,\"_discount_rate\":100,\"origin_price\":\"220.00\",\"price\":\"220.00\",\"origin_amount\":6600,\"discount_amount\":0,\"paid_amount\":6600,\"origin_lesson_times\":1,\"present_lesson_times\":0,\"lesson_times\":0,\"origin_lesson_hours\":0,\"present_lesson_hours\":0,\"lesson_hours\":1,\"subtotal\":6600,\"reduced_amount\":0,\"_is_term\":1,\"_unit_lesson_hours\":\"2.00\",\"_is_average\":false,\"_share_own_amount\":0}]',1,1512028636,1512028636,NULL,0,NULL);
/*!40000 ALTER TABLE `x360p_input_template` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='课程表(关键的课程主表,记录课程的基本信息)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_lesson`
--

LOCK TABLES `x360p_lesson` WRITE;
/*!40000 ALTER TABLE `x360p_lesson` DISABLE KEYS */;
INSERT INTO `x360p_lesson` VALUES (1,0,'2,3',2017,'S',1,'','3年级奥数',' 3AS',105,1,18,1,12,'3年级奥数入门班','\"\"','',15,0,0,1,2,1,30.00,120.00,2.00,120,3600.00,'','',1,0,1,0,0,1510971501,1,1510971501,0,0,NULL),(2,0,'2,3',2017,'S',1,'','3年级奥数1对1',' 3AS',105,1,18,1,12,'3年级奥数提高班','\"\"','',15,1,0,1,2,1,30.00,220.00,2.00,120,6600.00,'','',1,0,1,0,0,1510971579,1,1510971579,0,0,NULL),(3,0,'2,3',2017,'S',1,'','3年级奥数1对多',' 3AS3',105,1,18,1,12,'3年级奥数提高班','\"\"','',15,2,0,1,2,1,30.00,180.00,2.00,120,5400.00,'','',1,0,1,0,0,1510971614,1,1510971629,0,0,NULL),(4,0,'2,3',2017,'Q',1,'','一对一数学','OTOmath',105,1,18,1,12,'一对一数学辅导','\"\"','',1,1,0,1,1,1,1.00,200.00,1.00,60,200.00,'','',1,0,1,0,0,1510971693,1,1510971708,0,0,NULL),(5,0,'2,3',2017,'Q',0,'4,2','艺术课课时包','ART001',105,1,18,1,12,'在学习音乐的同时提升英语水准，一举多得','\"\"','',15,0,0,1,1,1,1.00,200.00,1.00,60,200.00,'','',1,1,1,0,0,1510994171,1,1510994171,0,0,NULL),(6,0,'2,3',2017,'Q',0,'4,2','一对一艺术课时包','OTOART001',105,1,18,1,12,'在娱乐中学习，让学习变得更有趣','\"\"','',30,1,0,1,1,0,30.00,200.00,1.00,60,200.00,'','',1,1,1,0,0,1510996677,1,1511342931,0,0,NULL),(7,0,'2,3',2017,'Q',2,'','一对多英语','OTMEng',105,1,18,1,12,'一对多英语','\"\"','',30,2,0,1,2,1,30.00,100.00,1.00,60,3000.00,'','',1,0,1,0,0,1511004097,1,1511004097,0,0,NULL),(8,0,'2,3',0,'C',3,'','作文作文作文','',2,1,18,1,12,'门前大桥下，游过一群鸭\n','\"\"','',20,0,0,1,2,1,1.00,900.00,1.00,60,900.00,'','',1,0,1,0,0,1511151285,1,1512022834,0,0,NULL),(9,0,'2,3,4',2017,'H',4,'','课程lid-9-班课-按次计费-60m-1课时-200元','',2,1,18,1,12,'考勤课耗测试','\"\"','',7,0,0,1,1,1,7.00,200.00,1.00,60,1400.00,'','',1,0,1,0,0,1511257584,1,1512700294,0,0,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COMMENT='课程相关物品';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_lesson_material`
--

LOCK TABLES `x360p_lesson_material` WRITE;
/*!40000 ALTER TABLE `x360p_lesson_material` DISABLE KEYS */;
INSERT INTO `x360p_lesson_material` VALUES (7,9,1,1,1512642180,1,1512642180,0,NULL,0),(8,8,1,1,1512642202,1,1512642202,0,NULL,0),(9,7,3,2,1512698518,1,1512698518,0,NULL,0),(10,5,8,3,1512698565,1,1512698565,0,NULL,0),(11,4,8,2,1512698967,1,1512698967,0,NULL,0),(12,4,1,2,1512698967,1,1512698967,0,NULL,0),(13,3,3,1,1512700234,1,1512700234,0,NULL,0),(14,3,8,1,1512700234,1,1512700234,0,NULL,0),(15,3,1,1,1512700234,1,1512700234,0,NULL,0),(16,3,5,3,1512700234,1,1512700234,0,NULL,0);
/*!40000 ALTER TABLE `x360p_lesson_material` ENABLE KEYS */;
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
  `is_attendance` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否考勤，与attendance_status结合使用',
  `attendance_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '出勤状态 0：缺勤，1：出勤。与is_attendance结合使用',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`ma_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='补课安排记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_makeup_arrange`
--

LOCK TABLES `x360p_makeup_arrange` WRITE;
/*!40000 ALTER TABLE `x360p_makeup_arrange` DISABLE KEYS */;
INSERT INTO `x360p_makeup_arrange` VALUES (2,0,2,7,27,3,83,0,0,0,0,143,10006,20171212,100,200,0,0,1513061875,1,1513067973,1,1513067973,1),(3,0,2,49,28,3,73,0,0,0,0,143,10006,20171212,100,200,0,0,1513061875,1,1513068182,1,1513068182,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='物品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_material`
--

LOCK TABLES `x360p_material` WRITE;
/*!40000 ALTER TABLE `x360p_material` DISABLE KEYS */;
INSERT INTO `x360p_material` VALUES (1,0,'康师傅统一方便面啊','包','好吃不贵','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/24/82afa94a68b334bb802f0fa028865790.jpg',653,1.50,3.00,1511504636,1511503742,1,0,0,NULL),(2,0,'黄金脆皮鸡','只','干脆可口','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/24/ff25c67a53d118ddb7a107e82845ec24.jpg',360,20.00,50.00,1511504999,1511504999,1,0,0,NULL),(3,0,'Vue.js实践','本','尤雨溪推荐 随书附赠示例代码下载资源 vue社区iView组件贡献者 前端大神梁灏编写 突出实战 应用为王 大鹏展趐 鹰击长空 Vue.js实战助你攀上前端之巅','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/28/5c46a9a77d59a7ea76df1d5769b68ebe.jpg',206,62.40,90.00,1511833920,1511833794,1,0,0,NULL),(4,0,'天线宝宝','只','你没得天线，没法子跟你解释','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/30/f2cbaff69942f452ac66d835843236a2.jpg',50,50.00,150.00,1512010078,1512010078,1,0,0,NULL),(5,0,'日本正宗柴犬','只','遛狗必备','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/30/7781121bb13c6bbdfc63750a33c6fdff.jpg',-3,5000.00,20000.00,1512012117,1512012117,1,0,0,NULL),(6,0,'微笑中的双下巴','个','嘴上笑嘻嘻，心里mmp','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/30/aba47839b0e9c7ba167cb739742c174f.jpg',0,0.50,1.00,1512012358,1512012358,1,0,0,NULL),(7,0,'海之蓝','提','你值得拥有','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/30/85d4670d0f1e1723bea124d4c14b6b02.jpg',0,420.00,600.00,1512012573,1512012422,1,0,0,NULL),(8,0,'PHP 15天快速入门','本','15天快速入门，学会PHP','http://s10.xiao360.com//x360pmaterial_picture/1/17/11/30/b4c264aff5f799bc65a3effa5b970838.jpg',-46,78.00,102.00,1512012519,1512012519,1,0,0,NULL),(9,0,'默认图片','张','','http://s10.xiao360.com//x360pmaterial_picture/1/17/12/19/ddcc2a6de8222740a3a9d400152033f9.jpg',0,0.50,1.00,1513677138,1512030639,1,0,0,NULL);
/*!40000 ALTER TABLE `x360p_material` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COMMENT='物品出入记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_material_history`
--

LOCK TABLES `x360p_material_history` WRITE;
/*!40000 ALTER TABLE `x360p_material_history` DISABLE KEYS */;
INSERT INTO `x360p_material_history` VALUES (1,0,0,0,0,0,0,0,0,NULL,0,0,1512095971,1,1512095971,1),(2,2,6,0,100,1,1,1,20171124,NULL,1511528116,1,1511528116,0,NULL,0),(3,2,5,0,200,1,1,1,20171124,NULL,1511528116,1,1511528116,0,NULL,0),(4,1,3,0,100,1,1,1,20171124,NULL,1511528359,1,1511528359,0,NULL,0),(5,1,6,0,200,1,1,1,20171124,NULL,1511528359,1,1511528359,0,NULL,0),(6,1,5,0,300,1,1,1,20171124,NULL,1511528359,1,1511528359,0,NULL,0),(7,1,31,0,10,1,1,1,20171127,NULL,1511746611,1,1511746611,0,NULL,0),(8,1,32,0,20,1,1,1,20171127,NULL,1511746611,1,1511746611,0,NULL,0),(9,2,32,0,10,1,1,1,20171127,NULL,1511746727,1,1511746727,0,NULL,0),(10,2,32,0,10,1,1,1,20171127,NULL,1511747503,1,1511747503,0,NULL,0),(11,2,31,0,20,1,1,1,20171127,NULL,1511747503,1,1511747503,0,NULL,0),(12,2,32,0,20,1,1,1,20171127,NULL,1511747526,1,1511747526,0,NULL,0),(13,2,32,0,10,1,1,1,20171127,NULL,1511748200,1,1511748200,0,NULL,0),(14,2,31,0,15,1,2,2,20171127,NULL,1511764618,1,1511764618,0,NULL,0),(15,2,32,0,5,1,2,2,20171127,NULL,1511764618,1,1511764618,0,NULL,0),(16,2,32,0,5,1,1,1,20171128,NULL,1511832027,1,1511832027,0,NULL,0),(17,2,31,0,5,1,1,1,20171128,NULL,1511832027,1,1511832027,0,NULL,0),(18,2,32,31,5,1,2,3,20171128,NULL,1511832626,1,1511832626,0,NULL,0),(19,2,31,32,5,1,1,3,20171128,NULL,1511832626,1,1511832626,0,NULL,0),(20,3,32,0,50,1,1,1,20171128,NULL,1511833822,1,1511833822,0,NULL,0),(21,3,31,0,50,1,1,1,20171128,NULL,1511833822,1,1511833822,0,NULL,0),(22,3,30,0,50,1,1,1,20171128,NULL,1511833822,1,1511833822,0,NULL,0),(23,4,32,0,50,1,1,1,20171130,NULL,1512010768,1,1512096322,1,1512096322,1),(24,4,31,0,50,1,1,1,20171130,NULL,1512010768,1,1512010768,0,NULL,0),(25,3,32,31,1,1,2,3,20171130,NULL,1512029481,1,1512029481,0,NULL,0),(26,3,31,32,1,1,1,3,20171130,NULL,1512029481,1,1512029481,0,NULL,0),(27,3,32,0,12,1,2,2,20171130,NULL,1512029511,1,1512029511,0,NULL,0),(28,8,39,0,10,1,1,1,20171130,NULL,1512045324,1,1512045324,0,NULL,0),(29,3,39,0,100,1,1,1,20171201,NULL,1512090294,1,1512090294,0,NULL,0),(30,1,39,0,100,1,1,1,20171201,NULL,1512090509,1,1512090509,0,NULL,0),(31,3,39,0,1,0,2,5,0,NULL,1512090620,1,1512096283,1,1512096283,1),(32,8,39,0,1,0,2,5,0,NULL,1512090620,1,1512090620,0,NULL,0),(33,1,39,0,1,0,2,5,0,NULL,1512090733,1,1512090733,0,NULL,0),(34,8,39,38,1,1,2,3,20171201,NULL,1512091348,1,1512091348,0,NULL,0),(35,8,38,39,1,1,1,3,20171201,NULL,1512091348,1,1512091348,0,NULL,0),(36,8,39,0,1,0,1,0,0,'订单结转',1512094841,1,1512094841,0,NULL,0),(37,3,39,0,1,0,2,5,0,NULL,1512094995,1,1512096160,1,1512096160,1),(38,3,39,0,1,0,1,0,0,'订单结转',1512095013,1,1512095013,0,NULL,0),(39,1,39,0,1,0,2,5,0,NULL,1512098994,1,1512098994,0,NULL,0),(40,3,39,0,1,0,2,5,0,NULL,1512098994,1,1512098994,0,NULL,0),(41,8,39,0,1,0,2,5,0,NULL,1512098994,1,1512098994,0,NULL,0),(42,1,39,0,1,0,2,5,0,NULL,1512099627,1,1512099627,0,NULL,0),(43,3,39,0,1,0,2,5,0,NULL,1512099627,1,1512099627,0,NULL,0),(44,8,39,0,1,0,2,5,0,NULL,1512099627,1,1512099627,0,NULL,0),(45,1,39,0,1,0,2,5,0,NULL,1512099640,1,1512099640,0,NULL,0),(46,1,39,0,1,0,2,5,0,NULL,1512099651,1,1512099651,0,NULL,0),(47,3,39,0,1,0,2,5,0,NULL,1512099651,1,1512099651,0,NULL,0),(48,8,39,0,1,0,2,5,0,NULL,1512099651,1,1512099651,0,NULL,0),(49,1,39,0,10,0,2,5,0,'订单购买',1512111149,1,1512111149,0,NULL,0),(50,1,39,0,10,0,2,5,0,'订单购买',1512111302,1,1512111302,0,NULL,0),(51,1,39,0,5,0,2,5,0,'订单购买',1512111483,1,1512111483,0,NULL,0),(52,3,39,0,7,0,2,5,0,'订单购买',1512111483,1,1512111483,0,NULL,0),(53,3,39,0,1,0,2,5,0,'订单购买',1512116680,1,1512116680,0,NULL,0),(54,1,39,0,1,0,2,5,0,'订单购买',1512116680,1,1512116680,0,NULL,0),(55,3,39,0,1,0,2,5,0,'订单购买',1512116710,1,1512116710,0,NULL,0),(56,1,39,0,1,0,2,5,0,'订单购买',1512116710,1,1512116710,0,NULL,0),(57,1,39,0,1,0,2,5,0,'订单购买',1512116846,1,1512116846,0,NULL,0),(58,3,39,0,1,0,2,5,0,'订单购买',1512117598,1,1512117598,0,NULL,0),(59,1,39,0,1,0,2,5,0,'订单购买',1512117598,1,1512117598,0,NULL,0),(60,3,39,0,1,0,2,5,0,'订单购买',1512119558,1,1512119558,0,NULL,0),(61,3,39,0,1,0,2,5,0,'订单购买',1512119732,1,1512119732,0,NULL,0),(62,1,39,0,1,0,2,5,0,'订单购买',1512465297,1,1512465297,0,NULL,0),(63,3,39,0,1,0,2,5,0,'订单购买',1512465297,1,1512465297,0,NULL,0),(64,3,39,0,1,0,2,5,0,'订单购买',1512529320,1,1512529320,0,NULL,0),(65,3,39,0,1,0,2,5,0,'订单购买',1512533556,1,1512533556,0,NULL,0),(66,3,39,0,1,0,2,5,0,'订单购买',1512541852,1,1512541852,0,NULL,0),(67,3,39,0,1,0,2,5,0,'订单购买',1512546480,1,1512546480,0,NULL,0),(68,3,39,0,1,0,1,0,0,'订单退回',1512554013,1,1512554013,0,NULL,0),(69,3,39,0,1,0,1,0,0,'订单退回',1512554831,1,1512554831,0,NULL,0),(70,3,39,0,1,0,1,0,0,'订单退回',1512555230,1,1512555230,0,NULL,0),(71,1,39,0,1,0,1,0,0,'订单退回',1512555230,1,1512555230,0,NULL,0),(72,3,39,0,1,0,2,5,0,'订单购买',1512556401,1,1512556401,0,NULL,0),(73,3,39,0,11,0,2,5,0,'订单购买',1512611348,1,1512611348,0,NULL,0),(74,5,39,0,10,1,1,1,20171207,NULL,1512611526,1,1513050036,1,1513050036,1),(75,3,39,0,1,0,1,0,0,'订单退回',1512613804,1,1513050061,1,1513050061,1),(76,1,39,0,16,0,2,5,0,'订单购买',1512614181,1,1512614181,0,NULL,0),(77,9,39,38,11,1,2,3,20171208,NULL,1512700572,1,1512700572,0,NULL,0),(78,9,38,39,11,1,1,3,20171208,NULL,1512700572,1,1512700572,0,NULL,0),(79,1,39,0,1,0,2,5,0,'订单购买',1512702013,1,1512702013,0,NULL,0),(80,8,39,0,12,0,2,5,0,'订单购买',1512713010,1,1512713010,0,NULL,0),(81,8,39,0,6,0,2,5,0,'订单购买',1512713180,1,1512713180,0,NULL,0),(82,8,39,0,6,0,2,5,0,'订单购买',1512713395,1,1512713395,0,NULL,0),(83,8,39,0,3,0,2,5,0,'订单购买',1512713702,1,1513064023,1,1513064023,1),(84,1,39,0,1,0,2,5,0,'订单购买',1512718749,1,1512718749,0,NULL,0),(85,1,39,0,1,0,2,5,0,'订单购买',1513050857,1,1513050857,0,NULL,0),(86,1,39,0,1,0,2,5,20171212,'订单购买2017121241763',1513059662,1,1513059662,0,NULL,0),(87,8,39,0,3,0,2,5,20171212,'订单购买2017121227477',1513067900,1,1513067900,0,NULL,0),(88,1,38,39,10,1,2,3,20171212,NULL,1513069634,1,1513069634,0,NULL,0),(89,1,39,38,10,1,1,3,20171212,NULL,1513069634,1,1513069634,0,NULL,0),(90,1,39,0,2,0,2,5,20171212,'订单购买2017121263345',1513071375,1,1513071375,0,NULL,0),(91,1,39,0,1,0,2,5,20171212,'订单购买2017121286881',1513071539,1,1513071539,0,NULL,0),(92,1,39,0,1,0,2,5,20171212,'订单购买2017121222387',1513071789,1,1513071789,0,NULL,0),(93,8,39,0,3,0,2,5,20171212,'订单购买2017121227890',1513071948,1,1513071948,0,NULL,0),(94,8,39,0,3,0,2,5,20171212,'订单购买2017121217311',1513071994,1,1513071994,0,NULL,0),(95,1,39,0,1,0,2,5,20171212,'订单购买2017121297717',1513072145,1,1513072145,0,NULL,0),(96,8,39,0,3,0,2,5,20171212,'订单购买2017121256479',1513072202,1,1513072202,0,NULL,0),(97,1,39,0,1,0,2,5,20171212,'订单购买2017121234578',1513072248,1,1513072248,0,NULL,0),(98,1,39,0,1,0,2,5,20171212,'订单购买2017121291269',1513072338,1,1513072338,0,NULL,0),(99,1,39,0,1,0,2,5,20171212,'订单购买2017121288425',1513072406,1,1513072406,0,NULL,0),(100,1,39,0,2,0,2,5,20171212,'订单购买2017121276669',1513072878,1,1513072878,0,NULL,0),(101,1,39,0,2,0,2,5,20171212,'订单购买2017121240057',1513073819,1,1513073819,0,NULL,0),(102,1,39,0,2,0,2,5,20171212,'订单购买2017121248818',1513080069,1,1513080069,0,NULL,0),(103,8,39,0,3,0,2,5,20171212,'订单购买2017121250261',1513081124,1,1513081124,0,NULL,0),(104,8,39,0,3,0,2,5,20171213,'订单购买2017121329052',1513129216,1,1513129216,0,NULL,0),(105,1,39,0,1,0,2,5,20171213,'订单购买2017121329052',1513129216,1,1513129216,0,NULL,0),(106,1,39,0,1,0,2,5,20171213,'订单购买2017121389009',1513133823,1,1513133823,0,NULL,0),(107,8,39,0,2,0,2,5,20171214,'订单购买2017121441911',1513243794,1,1513243794,0,NULL,0),(108,1,39,0,2,0,2,5,20171214,'订单购买2017121441911',1513243794,1,1513243794,0,NULL,0),(109,3,39,0,1,0,2,5,20171214,'订单购买2017121490218',1513243886,1,1513243886,0,NULL,0),(110,8,39,0,1,0,2,5,20171214,'订单购买2017121490218',1513243886,1,1513243886,0,NULL,0),(111,1,39,0,1,0,2,5,20171214,'订单购买2017121490218',1513243886,1,1513243886,0,NULL,0),(112,5,39,0,3,0,2,5,20171214,'订单购买2017121490218',1513243886,1,1513243886,0,NULL,0),(113,8,39,0,3,0,2,5,20171221,'订单购买2017122199969',1513820370,1,1513820370,0,NULL,0),(114,8,39,0,3,0,1,0,20171221,'订单结转2017122199969',1513823167,1,1513823167,0,NULL,0),(115,8,39,0,5,0,2,5,20171221,'订单购买2017122141596',1513825288,1,1513825288,0,NULL,0),(116,1,39,0,2,0,2,5,20171221,'订单购买2017122141596',1513825288,1,1513825288,0,NULL,0),(117,3,39,0,3,0,2,5,20171221,'订单购买2017122117357',1513826157,1,1513826157,0,NULL,0),(118,1,39,0,2,0,2,5,20171221,'订单购买2017122117357',1513826157,1,1513826157,0,NULL,0),(119,8,39,0,3,0,2,5,20171221,'订单购买2017122193286',1513828597,1,1513828597,0,NULL,0);
/*!40000 ALTER TABLE `x360p_material_history` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COMMENT='仓库表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_material_store`
--

LOCK TABLES `x360p_material_store` WRITE;
/*!40000 ALTER TABLE `x360p_material_store` DISABLE KEYS */;
INSERT INTO `x360p_material_store` VALUES (1,0,1,'默认仓库','',1510362612,18,1510362612,0,0,NULL),(2,0,1,'笋岗校区','哈哈哈额额',1510971258,1,1511513558,0,0,NULL),(3,0,1,'德兴校区','',1510971281,1,1510971281,0,0,NULL),(4,0,1,'百汇校区','',1511165914,1,1511165914,0,0,NULL),(5,0,1,'罗湖校区','',1511351217,1,1511351217,0,0,NULL),(6,0,1,'龙岗校区','',1511352851,1,1511352851,0,0,NULL),(9,0,1,'罗湖仓库','罗湖仓库',1511512417,1,1511512492,1,1,1511512492),(10,0,1,'龙岗仓库','龙岗',1511514294,1,1511514297,1,1,1511514297),(11,0,1,'坂田校区','校区仓库',1511581035,1,1511581035,0,0,NULL),(12,0,1,'宝安校区','校区仓库',1511581047,1,1511581047,0,0,NULL),(13,0,1,'福田校区','校区仓库',1511581067,1,1511581067,0,0,NULL),(14,0,1,'罗湖2区','校区仓库',1511581132,1,1511581132,0,0,NULL),(15,0,1,'湖北校区','校区仓库',1511581259,1,1511581259,0,0,NULL),(16,0,1,'文家校区','校区仓库',1511581551,1,1511581551,0,0,NULL),(17,0,1,'马蹄山校区','校区仓库',1511581576,1,1511581576,0,0,NULL),(18,0,1,'百汇大夏','校区仓库',1511581600,1,1511581600,0,0,NULL),(19,0,1,'文家校区','校区仓库',1511581621,1,1511581621,0,0,NULL),(20,0,1,'陇南校区','校区仓库',1511581896,1,1511581896,0,0,NULL),(21,0,1,'西丽校区','校区仓库',1511593181,1,1511593181,0,0,NULL),(22,0,1,'西丽校区','校区仓库',1511593374,1,1511593374,0,0,NULL),(23,0,1,'光明顶校区','校区仓库',1511593882,1,1511593882,0,0,NULL),(24,0,1,'光明顶校区','校区仓库',1511594032,1,1511594032,0,0,NULL),(25,0,1,'光明顶校区','校区仓库',1511594550,1,1511594550,0,0,NULL),(26,0,1,'光明顶校区','校区仓库',1511594669,1,1511594669,0,0,NULL),(27,0,1,'光明顶校区','校区仓库',1511594669,1,1511594669,0,0,NULL),(28,0,1,'光明顶校区','校区仓库',1511594922,1,1511594922,0,0,NULL),(29,0,1,'光明顶校区','校区仓库',1511594961,1,1511594961,0,0,NULL),(30,0,1,'光明顶校区','校区仓库',1511595055,1,1511595055,0,0,NULL),(31,0,1,'花都校区','校区仓库',1511601063,1,1511601063,0,0,NULL),(32,0,1,'蛇口校区','校区仓库',1511601111,1,1511601111,0,0,NULL),(33,0,1,'龙岗校区','校区仓库',1512030887,1,1512030887,0,0,NULL),(34,0,1,'中山校区','校区仓库',1512042870,1,1512042870,0,0,NULL),(35,0,1,'喆聪校区','校区仓库',1512042881,1,1512042881,0,0,NULL),(36,0,1,'唐军校区','校区仓库',1512042889,1,1512042889,0,0,NULL),(37,0,1,'振威校区','校区仓库',1512042897,1,1512042897,0,0,NULL),(38,0,1,'成建校区','校区仓库',1512042908,1,1512042908,0,0,NULL),(39,0,1,'笋岗仓库','',1512045240,1,1512045240,0,0,NULL),(40,15,1,'guapicaozuo','校区仓库',1513657356,1,1513657356,0,0,NULL),(41,16,1,'siyi','校区仓库',1513657811,1,1513665391,1,1,1513665391);
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COMMENT='仓库物品库存表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_material_store_qty`
--

LOCK TABLES `x360p_material_store_qty` WRITE;
/*!40000 ALTER TABLE `x360p_material_store_qty` DISABLE KEYS */;
INSERT INTO `x360p_material_store_qty` VALUES (1,0,31,1,10,1511746611,1,1511746611,0,0,NULL),(2,0,32,1,20,1511746611,1,1511746611,0,0,NULL),(3,0,32,2,45,1511746727,1,1511746727,0,0,NULL),(4,0,31,2,15,1511747503,1,1511747503,0,0,NULL),(5,0,32,3,37,1511833822,1,1511833822,0,0,NULL),(6,0,31,3,51,1511833822,1,1511833822,0,0,NULL),(7,0,30,3,50,1511833822,1,1511833822,0,0,NULL),(8,0,32,4,0,1512010768,1,1512010768,0,0,NULL),(9,0,31,4,50,1512010768,1,1512010768,0,0,NULL),(10,0,39,8,-47,1512045324,1,1512045324,0,0,NULL),(11,0,39,3,68,1512090294,1,1512090294,0,0,NULL),(12,0,39,1,33,1512090509,1,1512090509,0,0,NULL),(13,0,38,8,1,1512091348,1,1512091348,0,0,NULL),(14,0,39,5,-3,1512611526,1,1512611526,0,0,NULL),(15,0,39,9,-11,1512700572,1,1512700572,0,0,NULL),(16,0,38,9,11,1512700572,1,1512700572,0,0,NULL),(17,0,38,1,-10,1513069634,1,1513069634,0,0,NULL);
/*!40000 ALTER TABLE `x360p_material_store_qty` ENABLE KEYS */;
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
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `nid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新闻ID',
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
) ENGINE=InnoDB AUTO_INCREMENT=180 DEFAULT CHARSET=utf8mb4 COMMENT='订单记录表(学员报名、选课之后会产生订单记录)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order`
--

LOCK TABLES `x360p_order` WRITE;
/*!40000 ALTER TABLE `x360p_order` DISABLE KEYS */;
INSERT INTO `x360p_order` VALUES (1,0,0,1,2,0,'2017111823586',3600.00,0.00,0.00,3600.00,1,1510971446,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1510971618,1,1510971618,0,NULL,0),(2,0,0,2,2,0,'2017111869867',3600.00,0.00,0.00,3600.00,1,1510971518,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1510971695,1,1510971695,0,NULL,0),(3,0,0,3,2,0,'2017111870367',3600.00,0.00,0.00,3600.00,1,1510971518,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1510971718,1,1510971718,0,NULL,0),(4,0,0,4,2,0,'2017111861510',3600.00,0.00,0.00,3600.00,1,1510971518,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1510971763,1,1510971764,0,NULL,0),(5,0,0,8,2,0,'2017111841657',3600.00,0.00,0.00,3600.00,1,1510971812,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1510971975,1,1510971975,0,NULL,0),(6,0,0,5,2,0,'2017111885722',3600.00,0.00,0.00,3600.00,1,1510971938,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1510971976,1,1510971977,0,NULL,0),(7,0,0,6,2,0,'2017111801437',3600.00,0.00,0.00,3600.00,1,1510972007,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1510972024,1,1510972024,0,NULL,0),(8,0,0,7,2,0,'2017111884531',3600.00,0.00,0.00,3600.00,1,1510972007,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1510972041,1,1510972042,0,NULL,0),(9,0,0,9,2,0,'2017111867555',3600.00,0.00,0.00,3600.00,1,1510971883,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1510972045,1,1510972045,0,NULL,0),(10,0,0,10,2,0,'2017111898651',3600.00,0.00,0.00,3600.00,1,1510974259,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1510974276,1,1510974276,0,NULL,0),(11,0,0,11,2,0,'2017111872252',120.00,0.00,0.00,120.00,1,1510993699,2,0.00,120.00,120.00,120.00,0.00,1,0,0,0,'','',1510993877,1,1510993877,0,NULL,0),(12,0,0,12,2,0,'2017111880571',200.00,0.00,0.00,200.00,1,1510994045,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1510994219,1,1510994219,0,NULL,0),(13,0,0,13,2,0,'2017111801236',200.00,0.00,0.00,200.00,1,1510994527,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1510994693,1,1510994693,0,NULL,0),(14,0,0,14,2,0,'2017111851450',200.00,0.00,0.00,200.00,1,1510994527,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1510994712,1,1510994712,0,NULL,0),(15,0,0,15,2,0,'2017111873235',200.00,0.00,0.00,200.00,1,1510994573,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1510994740,1,1510994740,0,NULL,0),(16,0,0,16,2,0,'2017111823772',200.00,0.00,0.00,200.00,1,1510994573,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1510994756,1,1510994756,0,NULL,0),(17,0,0,17,2,0,'2017111820991',200.00,0.00,0.00,200.00,1,1510994573,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1510994770,1,1510994770,0,NULL,0),(18,0,0,15,2,0,'2017111827024',3600.00,0.00,0.00,3600.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510995565,1,1510995565,0,NULL,0),(19,0,0,13,2,0,'2017111834438',3600.00,0.00,0.00,3600.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1510995565,1,1510995565,0,NULL,0),(20,0,0,18,2,0,'2017111870484',6800.00,0.00,0.00,6800.00,1,1510995545,2,0.00,6800.00,6800.00,6800.00,0.00,1,0,0,0,'','',1510995734,1,1510995734,0,NULL,0),(21,0,0,19,2,0,'2017111861252',6000.00,0.00,0.00,6000.00,1,1510996633,2,0.00,6000.00,6500.00,6500.00,0.00,1,0,0,0,'','',1510996786,1,1510996786,0,NULL,0),(22,0,0,20,2,0,'2017111874700',3600.00,0.00,0.00,3600.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1511003930,1,1511003930,0,NULL,0),(23,0,0,20,2,0,'2017111837955',200.00,0.00,0.00,200.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1511003943,1,1511003943,0,NULL,0),(24,0,0,21,2,0,'2017111885019',3000.00,0.00,0.00,3000.00,1,1511004045,2,0.00,3000.00,3000.00,3000.00,0.00,1,0,0,0,'','',1511004143,1,1511004143,0,NULL,0),(25,0,0,22,2,0,'2017111809011',5400.00,0.00,0.00,5400.00,1,1511004119,2,0.00,5400.00,5400.00,5400.00,0.00,1,0,0,0,'','',1511004217,1,1511004217,0,NULL,0),(26,0,0,23,2,0,'2017112012300',27800.00,2376.00,1000.00,24424.00,1,1511143935,2,0.00,24424.00,24424.00,24424.00,0.00,1,0,0,0,'','',1511144986,1,1511144986,0,NULL,0),(27,0,0,1,2,0,'2017112067863',200.00,0.00,0.00,200.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1511150330,1,1511150330,0,NULL,0),(28,0,0,2,2,0,'2017112012750',200.00,0.00,0.00,200.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1511150330,1,1511150330,0,NULL,0),(29,0,0,3,2,0,'2017112098917',200.00,0.00,0.00,200.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1511150330,1,1511150330,0,NULL,0),(30,0,0,4,2,0,'2017112048720',200.00,0.00,0.00,200.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1511150330,1,1511150330,0,NULL,0),(31,0,0,8,2,0,'2017112029405',200.00,0.00,0.00,200.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1511150330,1,1511150330,0,NULL,0),(32,0,0,9,2,0,'2017112027601',200.00,0.00,0.00,200.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1511150330,1,1511150330,0,NULL,0),(33,0,0,24,2,0,'2017112010549',81000.00,0.00,0.00,81000.00,1,1511151747,2,0.00,81000.00,81000.00,81000.00,0.00,1,0,0,0,'','',1511151653,1,1511151653,0,NULL,0),(34,0,0,23,2,0,'2017112064889',9000.00,0.00,0.00,9000.00,1,1511161433,2,0.00,9000.00,9000.00,9000.00,0.00,1,0,0,0,'','',1511161354,1,1511161354,0,NULL,0),(35,0,0,26,2,0,'2017112125453',1400.00,0.00,0.00,1400.00,1,1511257649,2,0.00,1400.00,1400.00,1400.00,0.00,1,0,0,0,'','',1511257691,1,1511257691,0,NULL,0),(36,0,0,27,2,0,'2017112140492',1400.00,0.00,0.00,1400.00,1,1511257649,2,0.00,1400.00,1400.00,1400.00,0.00,1,0,0,0,'','',1511257709,1,1511257709,0,NULL,0),(37,0,0,31,2,0,'2017112256520',1400.00,0.00,0.00,1400.00,1,1511323496,2,0.00,1400.00,1400.00,1400.00,0.00,1,0,0,0,'','',1511323518,1,1511323518,0,NULL,0),(38,0,0,31,2,0,'2017112255174',1400.00,0.00,0.00,1400.00,1,1511323595,2,0.00,1400.00,1400.00,1400.00,0.00,1,0,0,0,'','',1511323616,1,1511323616,0,NULL,0),(39,0,0,32,2,0,'2017112217317',630000.00,31500.00,0.00,598500.00,1,1511334049,2,0.00,598500.00,598500.00,598500.00,0.00,1,0,0,0,'','',1511334001,1,1511334001,0,NULL,0),(40,0,0,33,2,0,'2017112248243',200.00,0.00,0.00,200.00,0,1511338035,0,0.00,200.00,0.00,0.00,200.00,0,0,0,0,'','',1511338048,1,1511338048,0,NULL,0),(41,0,0,30,2,0,'2017112261628',1400.00,0.00,200.00,1200.00,1,1511345139,2,0.00,1200.00,1200.00,1200.00,0.00,1,0,0,0,'','',1511345233,1,1511345233,0,NULL,0),(42,0,0,29,2,0,'2017112213168',1400.00,0.00,0.00,1400.00,1,1511345290,2,0.00,1400.00,1400.00,1400.00,0.00,1,0,0,0,'','',1511345331,1,1511345331,0,NULL,0),(43,0,0,34,2,0,'2017112281613',200.00,0.00,0.00,200.00,1,1511345932,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1511346067,1,1511346067,0,NULL,0),(44,0,0,35,2,0,'2017112215955',1400.00,0.00,100.00,1300.00,1,1511354402,2,0.00,1300.00,1300.00,1300.00,0.00,1,0,0,0,'','',1511354433,1,1511354434,0,NULL,0),(45,0,0,36,2,0,'2017112232932',1400.00,0.00,300.00,1100.00,1,1511354453,2,0.00,1100.00,1100.00,1100.00,0.00,1,0,0,0,'','',1511354475,1,1511354475,0,NULL,0),(46,0,0,37,2,0,'2017112913331',10200.00,0.00,0.00,10200.00,1,1511942906,2,0.00,10200.00,10200.00,10200.00,0.00,1,0,0,0,'','',1511943146,1,1511943146,0,NULL,0),(47,0,0,37,2,0,'2017112996430',10200.00,0.00,0.00,10200.00,1,1511943157,2,0.00,10200.00,10200.00,10200.00,0.00,1,0,0,0,'','',1511943352,1,1511943352,0,NULL,0),(48,0,0,37,2,0,'2017112959920',400.00,0.00,0.00,400.00,1,1511944772,2,0.00,400.00,400.00,400.00,0.00,1,0,0,0,'','',1511944947,1,1511944947,0,NULL,0),(49,0,0,37,2,0,'2017112936747',900.00,0.00,0.00,900.00,1,1511944772,2,0.00,900.00,900.00,900.00,0.00,1,0,0,0,'','',1511944999,1,1511944999,0,NULL,0),(50,0,0,37,2,0,'2017112904929',16120.00,0.00,0.00,16120.00,1,1511946656,2,0.00,16120.00,16120.00,16120.00,0.00,1,0,0,0,'','',1511946839,1,1511946840,0,NULL,0),(51,0,0,37,2,0,'2017112963461',6800.00,0.00,0.00,6800.00,1,1511946726,2,0.00,6800.00,6800.00,6800.00,0.00,1,0,0,0,'','',1511946896,1,1511946896,0,NULL,0),(52,0,0,37,2,0,'2017112903535',5400.00,0.00,0.00,5400.00,1,1511946786,2,0.00,5400.00,5400.00,5400.00,0.00,1,0,0,0,'','',1511946953,1,1511946953,0,NULL,0),(53,0,0,37,2,0,'2017112900804',12000.00,0.00,0.00,12000.00,1,1511946875,2,0.00,12000.00,12000.00,12000.00,0.00,1,0,0,0,'','',1511947045,1,1511947045,0,NULL,0),(54,0,0,38,2,0,'2017112903837',200.00,0.00,0.00,200.00,1,1511946900,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1511947107,1,1511947107,0,NULL,0),(55,0,0,39,2,0,'2017112944680',2700.00,0.00,0.00,2700.00,1,1511947106,2,0.00,2700.00,2700.00,2700.00,0.00,1,0,0,0,'','',1511947296,1,1511947296,0,NULL,0),(56,0,0,40,2,0,'2017113002189',8600.00,0.00,100.00,8500.00,1,1512004559,1,0.00,8500.00,8000.00,8000.00,500.00,1,0,0,0,'','',1512004807,1,1512004807,0,NULL,0),(57,0,0,41,2,0,'2017113062592',12200.00,0.00,0.00,12200.00,1,1511971200,2,0.00,12200.00,12200.00,12200.00,0.00,1,0,0,0,'','',1512005259,1,1512005549,0,NULL,0),(58,0,0,41,2,0,'2017113027059',9000.00,0.00,0.00,9000.00,1,1512005266,2,0.00,9000.00,9000.00,9000.00,0.00,1,0,0,0,'','',1512005529,1,1512005529,0,NULL,0),(59,0,0,37,2,0,'2017113008309',12000.00,0.00,0.00,12000.00,1,1512028461,2,0.00,12000.00,12000.00,12000.00,0.00,1,0,0,0,'','',1512028636,1,1512028636,0,NULL,0),(61,0,0,43,2,0,'2017113018300',32600.00,0.00,0.00,32600.00,1,1512036154,2,0.00,32600.00,32600.00,32600.00,0.00,1,0,0,0,'','',1512036356,1,1512036356,0,NULL,0),(62,0,0,43,2,0,'2017113026787',12702.00,0.00,0.00,12702.00,1,1512036336,2,0.00,12702.00,12702.00,12702.00,0.00,1,0,0,0,'','',1512036526,1,1512036526,0,NULL,0),(63,0,0,43,2,0,'2017113094025',1601.00,0.00,0.00,1601.00,1,1512036336,2,0.00,1601.00,1601.00,1601.00,0.00,1,0,0,0,'','',1512036599,1,1512036599,0,NULL,0),(67,0,0,37,2,0,'2017120147699',5792.00,0.00,0.00,5792.00,1,1512090432,2,0.00,5792.00,5792.00,5792.00,0.00,1,0,0,0,'','',1512090620,1,1512090620,0,NULL,0),(68,0,0,38,2,0,'2017120109595',5403.00,0.00,0.00,5403.00,1,1512090549,2,0.00,5403.00,5403.00,5403.00,0.00,1,0,0,0,'','',1512090733,1,1512090733,0,NULL,0),(69,0,0,37,2,0,'2017120166287',290.00,0.00,0.00,290.00,1,1512094793,2,290.00,0.00,0.00,290.00,0.00,1,0,0,0,'','',1512094995,1,1512094995,0,NULL,0),(70,0,0,37,2,0,'2017120144527',5595.00,0.00,0.00,5595.00,1,1512098796,2,192.00,5403.00,5403.00,5595.00,0.00,1,0,0,0,'','',1512098994,1,1512098994,0,NULL,0),(71,0,0,4,2,0,'2017120118018',12000.00,0.00,0.00,12000.00,1,1512099411,2,0.00,12000.00,12000.00,12000.00,0.00,1,0,0,0,'','',1512099589,1,1512099589,0,NULL,0),(72,0,0,49,2,0,'2017120120315',5795.00,0.00,0.00,5795.00,1,1512099461,2,0.00,5795.00,5795.00,5795.00,0.00,1,0,0,0,'','',1512099627,1,1512099627,0,NULL,0),(73,0,0,49,2,0,'2017120139315',203.00,0.00,0.00,203.00,1,1512099461,2,0.00,203.00,203.00,203.00,0.00,1,0,0,0,'','',1512099640,1,1512099640,0,NULL,0),(74,0,0,49,2,0,'2017120184767',195.00,0.00,0.00,195.00,1,1512099461,2,0.00,195.00,195.00,195.00,0.00,1,0,0,0,'','',1512099651,1,1512099651,0,NULL,0),(75,0,0,49,2,0,'2017120116132',6630.00,0.00,0.00,6630.00,1,1512110965,2,580.00,6050.00,6050.00,6630.00,0.00,1,0,0,0,'','',1512111149,1,1512111149,0,NULL,0),(76,0,0,49,2,0,'2017120105064',3630.00,0.00,0.00,3630.00,1,1512111060,2,0.00,3630.00,3630.00,3630.00,0.00,1,0,0,0,'','',1512111302,1,1512123379,0,NULL,0),(77,0,0,49,2,0,'2017120184071',6245.00,0.00,0.00,6245.00,0,1512111292,0,0.00,6245.00,0.00,0.00,6245.00,0,0,0,0,'','',1512111483,1,1512111483,0,NULL,0),(78,0,0,49,2,0,'2017120109282',6600.00,0.00,0.00,6600.00,0,1512113646,0,0.00,6600.00,0.00,0.00,6600.00,0,0,0,0,'','',1512113952,1,1512554815,1,1512554815,1),(79,0,0,49,2,0,'2017120150838',9093.00,0.00,0.00,9093.00,1,1512116486,2,0.00,9093.00,9093.00,9093.00,0.00,1,0,0,0,'','',1512116680,1,1512116680,0,NULL,0),(80,0,0,49,2,0,'2017120147450',10293.00,0.00,100.00,10193.00,1,1512057600,1,0.00,10193.00,9520.53,9520.53,672.47,1,0,0,0,'','',1512116709,1,1512123379,0,NULL,0),(81,0,0,49,2,0,'2017120171321',6803.00,0.00,0.00,6803.00,1,1512057600,2,0.00,6803.00,6803.00,6803.00,0.00,1,0,0,0,'','',1512116846,1,1512117268,0,NULL,0),(82,0,0,49,2,0,'2017120162437',4593.00,0.00,0.00,4593.00,0,1512117418,0,0.00,4593.00,0.00,0.00,4593.00,0,0,0,0,'','',1512117598,1,1512555230,1,1512555230,1),(83,0,0,49,2,0,'2017120139232',200.00,0.00,0.00,200.00,1,1512057600,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1512118175,1,1512119102,0,NULL,0),(84,0,0,49,2,0,'2017120170783',4590.00,0.00,0.00,4590.00,1,1512057600,2,0.00,4590.00,4590.00,4590.00,0.00,1,0,0,0,'','',1512119558,1,1512120542,0,NULL,0),(85,0,0,49,2,0,'2017120117172',90.00,0.00,0.00,90.00,0,1512119368,0,0.00,90.00,0.00,0.00,90.00,0,0,0,0,'','',1512119732,1,1512554831,1,1512554831,1),(86,0,0,5,4,0,'2017120465858',3000.00,0.00,100.00,2900.00,1,1512350603,2,0.00,2900.00,2900.00,2900.00,0.00,1,0,0,0,'','',1512350735,1,1512350735,0,NULL,0),(87,0,0,7,2,0,'2017120508472',5693.00,0.00,0.00,5693.00,1,1512465084,2,0.00,5693.00,5693.00,5693.00,0.00,1,0,0,0,'','',1512465297,1,1512465297,0,NULL,0),(88,0,0,52,2,0,'2017120594017',5400.00,0.00,0.00,5400.00,1,1512469105,1,0.00,5400.00,5209.00,5209.00,191.00,1,0,0,0,'','',1512469292,1,1512541724,0,NULL,0),(89,0,0,8,2,0,'2017120610476',12090.00,0.00,0.00,12090.00,0,1512529033,0,0.00,12090.00,0.00,0.00,12090.00,0,0,0,0,'','',1512529320,1,1512554013,1,1512554013,1),(90,0,0,53,2,0,'2017120602317',5400.00,0.00,0.00,5400.00,0,1512529175,0,0.00,5400.00,0.00,0.00,5400.00,0,0,0,0,'','',1512529343,1,1512554027,1,1512554027,1),(91,0,0,53,2,0,'2017120682275',5400.00,0.00,0.00,5400.00,0,1512529593,0,0.00,5400.00,0.00,0.00,5400.00,0,0,0,0,'','',1512529762,1,1512553902,1,1512553902,1),(92,0,0,53,2,0,'2017120652364',200.00,0.00,0.00,200.00,0,NULL,0,0.00,200.00,0.00,0.00,200.00,0,0,0,0,'','',1512530064,1,1512553859,1,1512553859,1),(93,0,0,53,2,0,'2017120620408',900.00,0.00,0.00,900.00,0,NULL,0,0.00,900.00,0.00,0.00,900.00,0,0,0,0,'','',1512530414,1,1512553843,1,1512553843,1),(94,0,0,53,2,0,'2017120611282',6600.00,0.00,0.00,6600.00,0,1512489600,0,0.00,6600.00,0.00,0.00,6600.00,0,0,0,0,'','',1512530976,1,1512553889,1,1512553889,1),(95,0,0,53,2,0,'2017120610061',200.00,0.00,0.00,200.00,0,1512489600,0,0.00,200.00,0.00,0.00,200.00,0,0,0,0,'','',1512531123,1,1512553876,1,1512553876,1),(96,0,0,53,2,0,'2017120633696',5400.00,0.00,0.00,5400.00,0,1512489600,0,0.00,5400.00,0.00,0.00,5400.00,0,0,0,0,'','',1512531212,1,1512553816,1,1512553816,1),(97,0,0,52,2,0,'2017120618949',6690.00,0.00,0.00,6690.00,1,1512489600,2,0.00,6690.00,6690.00,6690.00,0.00,1,0,0,0,'','',1512533556,1,1512534428,0,NULL,0),(98,0,0,52,2,0,'2017120678049',5490.00,0.00,0.00,5490.00,1,1512489600,1,0.00,5490.00,5000.00,5000.00,490.00,1,0,0,0,'','',1512541852,1,1512541852,0,NULL,0),(99,0,0,52,2,0,'2017120627160',5490.00,0.00,0.00,5490.00,11,1512489600,2,0.00,5490.00,5490.00,5490.00,0.00,1,0,2,0,'','',1512546480,1,1512546480,0,NULL,0),(100,0,0,53,2,0,'2017120687967',3600.00,0.00,0.00,3600.00,1,1512489600,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1512549245,1,1512549245,0,NULL,0),(101,0,0,11,2,0,'2017120689205',3600.00,0.00,0.00,3600.00,1,1512549170,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1512549247,1,1512549247,0,NULL,0),(102,0,0,12,2,0,'2017120636561',3600.00,0.00,0.00,3600.00,11,1512549342,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,2,0,'','',1512549483,1,1512549483,0,NULL,0),(103,0,0,57,2,0,'2017120636337',3600.00,0.00,0.00,3600.00,11,1512549547,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,2,0,'','',1512549683,1,1512549684,0,NULL,0),(104,0,0,57,2,0,'2017120637300',5400.00,0.00,0.00,5400.00,0,1512489600,0,0.00,5400.00,0.00,0.00,5400.00,0,0,0,0,'','',1512555067,1,1512555111,1,1512555111,1),(105,0,0,57,2,0,'2017120665726',200.00,0.00,0.00,200.00,0,1512489600,0,0.00,200.00,0.00,0.00,200.00,0,0,0,0,'','',1512555077,1,1512555119,1,1512555119,1),(106,0,0,57,2,0,'2017120670692',5600.00,0.00,0.00,5600.00,0,1512489600,0,0.00,5600.00,0.00,0.00,5600.00,0,0,0,0,'','',1512555087,1,1512555130,1,1512555130,1),(107,0,0,57,2,0,'2017120620246',5600.00,0.00,0.00,5600.00,0,1512489600,0,0.00,5600.00,0.00,0.00,5600.00,0,0,0,0,'','',1512555097,1,1512555430,1,1512555430,1),(108,0,0,57,2,0,'2017120647147',12000.00,0.00,0.00,12000.00,1,1512489600,2,0.00,12000.00,12000.00,12000.00,0.00,1,0,0,0,'','',1512556368,1,1512613276,0,NULL,0),(109,0,0,57,2,0,'2017120604844',12090.00,0.00,0.00,12090.00,0,1512489600,0,0.00,12090.00,0.00,0.00,12090.00,0,0,0,0,'','',1512556401,1,1512556401,0,NULL,0),(110,0,0,57,2,0,'2017120690692',6600.00,0.00,0.00,6600.00,0,1512489600,0,0.00,6600.00,0.00,0.00,6600.00,0,0,0,0,'','',1512558812,1,1512558812,0,NULL,0),(111,0,0,13,2,0,'2017120770228',4590.00,0.00,0.00,4590.00,1,1512576000,1,0.00,4590.00,90.00,90.00,4500.00,1,0,0,0,'','',1512611348,1,1512611348,0,NULL,0),(112,0,0,14,2,0,'2017120787145',3648.00,0.00,0.00,3648.00,1,1512576000,1,0.00,3648.00,48.00,48.00,3600.00,1,0,0,0,'','',1512614181,1,1512614181,0,NULL,0),(113,0,0,43,2,0,'2017120768528',200.00,0.00,0.00,200.00,1,NULL,0,0.00,0.00,0.00,0.00,0.00,1,2,0,0,'','',1512615429,1,1512615429,0,NULL,0),(114,0,0,57,2,0,'2017120784211',3600.00,0.00,0.00,3600.00,1,1512576000,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1512615958,1,1512615958,0,NULL,0),(115,0,0,57,2,0,'2017120791769',3600.00,0.00,0.00,3600.00,1,1512576000,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1512615974,1,1512615974,0,NULL,0),(116,0,0,15,2,0,'2017120722975',6600.00,0.00,0.00,6600.00,1,1512576000,1,0.00,6600.00,600.00,600.00,6000.00,1,0,0,0,'','',1512617145,1,1512617146,0,NULL,0),(117,0,0,16,2,0,'2017120787113',5400.00,0.00,0.00,5400.00,1,1512576000,1,0.00,5400.00,400.00,400.00,5000.00,1,0,0,0,'','',1512617931,1,1512617931,0,NULL,0),(118,0,0,17,2,0,'2017120795740',200.00,0.00,0.00,200.00,1,1512576000,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1512618093,1,1512618093,0,NULL,0),(119,0,0,18,2,0,'2017120722103',1400.00,0.00,0.00,1400.00,1,1512576000,2,0.00,1400.00,1400.00,1400.00,0.00,1,0,0,0,'','',1512618204,1,1512618205,0,NULL,0),(120,0,0,19,2,0,'2017120741506',900.00,0.00,0.00,900.00,1,1512576000,2,0.00,900.00,900.00,900.00,0.00,1,0,0,0,'','',1512618266,1,1512618266,0,NULL,0),(121,0,0,20,2,0,'2017120798654',200.00,0.00,0.00,200.00,1,1512576000,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1512618505,1,1512618505,0,NULL,0),(122,0,0,43,2,0,'2017120773218',1400.00,0.00,0.00,1400.00,1,1512576000,2,0.00,1400.00,1400.00,1400.00,0.00,1,0,0,0,'','',1512630478,1,1512630478,0,NULL,0),(123,0,0,43,2,0,'2017120723751',200.00,0.00,0.00,200.00,1,1512576000,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1512630543,1,1512630543,0,NULL,0),(124,0,0,43,2,0,'2017120768149',200.00,0.00,0.00,200.00,1,1512576000,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1512630769,1,1512630769,0,NULL,0),(125,0,0,56,2,0,'2017120705919',1400.00,0.00,0.00,1400.00,1,NULL,2,0.00,0.00,1400.00,1400.00,0.00,1,2,0,0,'','',1512636896,1,1512701582,0,NULL,0),(126,0,0,61,2,0,'2017120804182',1203.00,0.00,0.00,1203.00,1,NULL,0,0.00,0.00,0.00,0.00,1203.00,1,2,0,0,'','',1512702013,1,1512702013,0,NULL,0),(127,0,0,65,2,0,'2017120896768',6600.00,0.00,0.00,6600.00,0,1512662400,0,0.00,6600.00,0.00,0.00,6600.00,0,0,0,0,'','',1512702354,1,1512702354,0,NULL,0),(128,0,0,65,2,0,'2017120876954',6600.00,0.00,0.00,6600.00,1,1512662400,2,0.00,6600.00,6600.00,6600.00,0.00,1,0,0,0,'','',1512707405,1,1512707405,0,NULL,0),(129,0,0,21,2,0,'2017120872464',2024.00,0.00,0.00,2024.00,1,1512662400,2,0.00,2024.00,2024.00,2024.00,0.00,1,0,0,0,'','',1512713010,1,1512713010,0,NULL,0),(130,0,0,22,2,0,'2017120875750',1012.00,0.00,0.00,1012.00,1,1512662400,2,0.00,1012.00,1012.00,1012.00,0.00,1,0,0,0,'','',1512713180,1,1512713180,0,NULL,0),(131,0,0,23,2,0,'2017120839914',1012.00,0.00,0.00,1012.00,1,1512662400,2,0.00,1012.00,1012.00,1012.00,0.00,1,0,0,0,'','',1512713395,1,1512713395,0,NULL,0),(132,0,0,24,2,0,'2017120887383',200.00,0.00,0.00,200.00,1,1512662400,2,0.00,200.00,200.00,200.00,0.00,1,0,0,0,'','',1512713598,1,1512713599,0,NULL,0),(133,0,0,70,2,0,'2017120820815',506.00,0.00,0.00,506.00,1,1512662400,2,0.00,506.00,506.00,506.00,0.00,1,0,0,0,'','',1512713702,1,1512713702,0,NULL,0),(134,0,0,70,2,0,'2017120867671',3600.00,0.00,0.00,3600.00,1,1512662400,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1512718382,1,1512718382,0,NULL,0),(135,0,0,70,2,0,'2017120803502',8003.00,0.00,0.00,8003.00,1,1512662400,2,0.00,8003.00,8003.00,8003.00,0.00,1,0,0,0,'','',1512718749,1,1512718749,0,NULL,0),(136,0,0,69,2,0,'2017121222766',1203.00,0.00,0.00,1203.00,0,NULL,0,0.00,0.00,0.00,0.00,1203.00,1,2,0,0,'','',1513050857,1,1513050857,0,NULL,0),(137,0,0,72,2,0,'2017121210662',6600.00,0.00,0.00,6600.00,1,1513008000,2,0.00,6600.00,6600.00,6600.00,6600.00,1,0,0,0,'','',1513058791,1,1513081951,0,NULL,0),(138,0,0,72,2,0,'2017121237185',1200.00,0.00,0.00,1200.00,0,NULL,2,0.00,0.00,1200.00,1200.00,0.00,1,2,0,0,'','',1513058936,1,1513065195,0,NULL,0),(139,0,0,72,2,0,'2017121241763',903.00,0.00,0.00,903.00,0,1513008000,1,0.00,903.00,2.00,2.00,901.00,1,0,0,0,'','',1513059662,1,1513065163,0,NULL,0),(140,0,0,72,2,0,'2017121227477',4106.00,0.00,0.00,4106.00,1,1513008000,2,0.00,4106.00,4106.00,4106.00,0.00,1,0,0,0,'','',1513067900,1,1513067900,0,NULL,0),(141,0,0,71,2,0,'2017121268863',1440.00,0.00,0.00,1440.00,0,NULL,0,0.00,0.00,0.00,0.00,1440.00,1,2,0,0,'','',1513068924,1,1513068924,0,NULL,0),(142,0,0,72,2,0,'2017121263345',1806.00,0.00,0.00,1806.00,1,1513008000,2,0.00,1806.00,1806.00,1806.00,0.00,1,0,0,0,'','',1513071375,1,1513071375,0,NULL,0),(143,0,0,72,2,0,'2017121286881',4503.00,0.00,0.00,4503.00,1,1513008000,2,0.00,4503.00,4503.00,4503.00,0.00,1,0,0,0,'','',1513071539,1,1513071539,0,NULL,0),(144,0,0,72,2,0,'2017121222387',903.00,0.00,0.00,903.00,1,1513008000,2,0.00,903.00,903.00,903.00,0.00,1,0,0,0,'','',1513071789,1,1513071789,0,NULL,0),(145,0,0,72,2,0,'2017121232004',7200.00,0.00,0.00,7200.00,1,1513008000,2,0.00,7200.00,7200.00,7200.00,0.00,1,0,0,0,'','',1513071844,1,1513071844,0,NULL,0),(146,0,0,72,2,0,'2017121227890',4106.00,0.00,0.00,4106.00,1,1513008000,2,0.00,4106.00,4106.00,4106.00,0.00,1,0,0,0,'','',1513071948,1,1513071948,0,NULL,0),(147,0,0,72,2,0,'2017121217311',506.00,0.00,0.00,506.00,1,1513008000,2,0.00,506.00,506.00,506.00,0.00,1,0,0,0,'','',1513071994,1,1513071994,0,NULL,0),(148,0,0,71,2,0,'2017121297717',903.00,0.00,0.00,903.00,1,1513008000,2,0.00,903.00,903.00,903.00,0.00,1,0,0,0,'','',1513072145,1,1513072145,0,NULL,0),(149,0,0,74,2,0,'2017121256479',4106.00,0.00,0.00,4106.00,1,1513008000,2,0.00,4106.00,4106.00,4106.00,0.00,1,0,0,0,'','',1513072202,1,1513072202,0,NULL,0),(150,0,0,74,2,0,'2017121234578',903.00,0.00,0.00,903.00,1,1513008000,2,0.00,903.00,903.00,903.00,0.00,1,0,0,0,'','',1513072248,1,1513072248,0,NULL,0),(151,0,0,72,2,0,'2017121291269',903.00,0.00,0.00,903.00,1,1513008000,2,0.00,903.00,903.00,903.00,0.00,1,0,0,0,'','',1513072338,1,1513072338,0,NULL,0),(152,0,0,72,2,0,'2017121288425',903.00,0.00,0.00,903.00,1,1513008000,2,0.00,903.00,903.00,903.00,0.00,1,0,0,0,'','',1513072406,1,1513072406,0,NULL,0),(153,0,0,75,2,0,'2017121276669',1806.00,0.00,0.00,1806.00,1,1513008000,2,0.00,1806.00,1806.00,1806.00,0.00,1,0,0,0,'','',1513072878,1,1513072879,0,NULL,0),(154,0,0,76,2,0,'2017121240057',1806.00,0.00,0.00,1806.00,1,1513008000,2,0.00,1806.00,1806.00,1806.00,0.00,1,0,0,0,'','',1513073819,1,1513073819,0,NULL,0),(155,0,0,77,2,0,'2017121248818',2306.00,0.00,0.00,2306.00,1,1513008000,2,0.00,2306.00,2306.00,2306.00,0.00,1,0,0,0,'','',1513080069,1,1513080069,0,NULL,0),(156,0,0,78,2,0,'2017121250261',4106.00,0.00,0.00,4106.00,1,1513008000,2,0.00,4106.00,4106.00,4106.00,0.00,1,0,0,0,'','',1513081124,1,1513081124,0,NULL,0),(157,0,0,72,2,0,'2017121280899',3600.00,0.00,0.00,3600.00,1,1513008000,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1513082011,1,1513082011,0,NULL,0),(158,0,0,78,2,0,'2017121243350',3600.00,0.00,0.00,3600.00,1,1513008000,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1513082035,1,1513082035,0,NULL,0),(161,0,0,78,2,0,'2017121329052',1409.00,0.00,0.00,1409.00,1,1513094400,2,0.00,1409.00,1409.00,1409.00,0.00,1,0,0,0,'','',1513129216,1,1513129216,0,NULL,0),(162,0,0,65,2,0,'2017121324237',2700.00,0.00,0.00,2700.00,0,NULL,0,0.00,0.00,0.00,0.00,2700.00,1,2,0,0,'','',1513132440,1,1513132440,0,NULL,0),(163,0,0,64,2,0,'2017121399544',2700.00,0.00,0.00,2700.00,0,NULL,0,0.00,0.00,0.00,0.00,2700.00,1,2,0,0,'','',1513132441,1,1513132441,0,NULL,0),(164,0,0,63,2,0,'2017121321065',2700.00,0.00,0.00,2700.00,0,NULL,0,0.00,0.00,0.00,0.00,2700.00,1,2,0,0,'','',1513132441,1,1513132441,0,NULL,0),(165,0,0,62,2,0,'2017121354646',2700.00,0.00,0.00,2700.00,0,NULL,0,0.00,0.00,0.00,0.00,2700.00,1,2,0,0,'','',1513132441,1,1513132441,0,NULL,0),(166,0,0,61,2,0,'2017121313080',2700.00,0.00,0.00,2700.00,0,NULL,0,0.00,0.00,0.00,0.00,2700.00,1,2,0,0,'','',1513132441,1,1513132441,0,NULL,0),(167,0,0,60,2,0,'2017121301506',2700.00,0.00,0.00,2700.00,0,NULL,0,0.00,0.00,0.00,0.00,2700.00,1,2,0,0,'','',1513132441,1,1513132441,0,NULL,0),(168,0,0,79,2,0,'2017121389009',4503.00,0.00,0.00,4503.00,1,1513094400,2,0.00,4503.00,4503.00,4503.00,0.00,1,0,0,0,'','',1513133823,1,1513133823,0,NULL,0),(169,0,0,79,2,0,'2017121441911',410.00,0.00,0.00,410.00,1,1513180800,2,0.00,410.00,410.00,410.00,0.00,1,0,0,0,'','',1513243794,1,1513243794,0,NULL,0),(170,0,0,79,2,0,'2017121490218',65595.00,0.00,0.00,65595.00,1,1513180800,2,0.00,65595.00,65595.00,65595.00,0.00,1,0,0,0,'','',1513243886,1,1513243886,0,NULL,0),(171,0,0,82,2,0,'2017122136481',3600.00,0.00,0.00,3600.00,1,1513785600,2,0.00,3600.00,3600.00,3600.00,3600.00,1,0,0,0,'','',1513819578,1,1513819850,0,NULL,0),(172,0,0,82,2,0,'2017122199969',506.00,0.00,0.00,506.00,0,1513785600,0,0.00,506.00,0.00,0.00,0.00,1,0,0,0,'','',1513820370,1,1513820370,0,NULL,0),(173,0,0,83,2,0,'2017122138469',3600.00,0.00,0.00,3600.00,1,1513785600,1,0.00,3600.00,2600.00,2600.00,2000.00,1,0,0,0,'','',1513824352,1,1513824352,0,NULL,0),(174,0,0,83,2,0,'2017122141596',916.00,0.00,0.00,916.00,1,1513785600,2,0.00,916.00,916.00,916.00,0.00,1,0,0,0,'','',1513825288,1,1513825288,0,NULL,0),(175,0,0,83,2,0,'2017122117357',5476.00,0.00,0.00,5476.00,1,1513785600,1,0.00,5476.00,2000.00,2000.00,3476.00,1,0,0,0,'','',1513826157,1,1513826157,0,NULL,0),(176,0,0,83,2,0,'2017122195508',3600.00,0.00,0.00,3600.00,1,1513785600,2,0.00,3600.00,3600.00,3600.00,0.00,1,0,0,0,'','',1513826325,1,1513826325,0,NULL,0),(177,0,0,84,2,0,'2017122109260',3600.00,0.00,0.00,3600.00,1,1513785600,2,0.00,3600.00,3600.00,3600.00,3600.00,1,0,0,0,'','',1513828003,1,1513837503,0,NULL,0),(178,0,0,84,2,0,'2017122127049',6600.00,0.00,0.00,6600.00,1,1513785600,2,0.00,6600.00,6600.00,6600.00,6600.00,1,0,0,0,'','',1513828028,1,1513837503,0,NULL,0),(179,0,0,84,2,0,'2017122193286',506.00,0.00,0.00,506.00,1,1513785600,2,0.00,506.00,506.00,506.00,506.00,1,0,0,0,'','',1513828597,1,1513837503,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='扣费记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_cut_amount`
--

LOCK TABLES `x360p_order_cut_amount` WRITE;
/*!40000 ALTER TABLE `x360p_order_cut_amount` DISABLE KEYS */;
INSERT INTO `x360p_order_cut_amount` VALUES (1,0,2,1,0,10,1008,100.00,1513823167,1,1513823167,0,NULL,0),(2,0,2,1,0,10,1009,100.00,1513823167,1,1513823167,0,NULL,0);
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
  `is_deliver` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否发货，针对物品',
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
) ENGINE=InnoDB AUTO_INCREMENT=306 DEFAULT CHARSET=utf8mb4 COMMENT='订单项目表(每一个订单对应1到多个订单项目记录)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_item`
--

LOCK TABLES `x360p_order_item` WRITE;
/*!40000 ALTER TABLE `x360p_order_item` DISABLE KEYS */;
INSERT INTO `x360p_order_item` VALUES (1,0,1,2,1,0,1,0,1,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510971618,1,1510971618,0,NULL,0),(2,0,2,2,2,0,1,0,2,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510971695,1,1510971695,0,NULL,0),(3,0,3,2,3,0,1,0,3,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510971718,1,1510971718,0,NULL,0),(4,0,4,2,4,0,1,0,4,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510971764,1,1510971764,0,NULL,0),(5,0,5,2,8,0,1,0,5,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510971975,1,1510971975,0,NULL,0),(6,0,6,2,5,0,1,0,6,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510971977,1,1510971977,0,NULL,0),(7,0,7,2,6,0,1,0,7,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510972024,1,1510972024,0,NULL,0),(8,0,8,2,7,0,1,0,8,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510972042,1,1510972042,0,NULL,0),(9,0,9,2,9,0,1,0,9,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510972045,1,1510972045,0,NULL,0),(10,0,10,2,10,0,1,0,10,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510974276,1,1510974276,0,NULL,0),(11,0,11,2,11,0,1,0,11,1.00,2,120.00,120.00,0,120.00,120.00,120.00,0.00,0.00,0.00,0.00,1.00,0.00,1510993877,1,1510993877,0,NULL,0),(12,0,12,2,12,0,1,0,12,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1510994219,1,1510994219,0,NULL,0),(13,0,13,2,13,0,1,0,13,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1510994693,1,1510994693,0,NULL,0),(14,0,14,2,14,0,1,0,14,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1510994712,1,1510994712,0,NULL,0),(15,0,15,2,15,0,1,0,15,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1510994740,1,1510994740,0,NULL,0),(16,0,16,2,16,0,1,0,16,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1510994756,1,1510994756,0,NULL,0),(17,0,17,2,17,0,1,0,17,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1510994770,1,1510994770,0,NULL,0),(18,0,18,2,15,0,1,0,18,1.00,2,120.00,120.00,0,120.00,120.00,0.00,0.00,0.00,0.00,0.00,1.00,0.00,1510995565,1,1510995565,0,NULL,0),(19,0,19,2,13,0,1,0,19,1.00,2,120.00,120.00,0,120.00,120.00,0.00,0.00,0.00,0.00,0.00,1.00,0.00,1510995565,1,1510995565,0,NULL,0),(20,0,20,2,18,0,1,0,20,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1510995734,1,1510995734,0,NULL,0),(21,0,20,2,18,0,1,0,21,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1510995734,1,1510995734,0,NULL,0),(22,0,21,2,19,0,1,0,22,30.00,1,200.00,200.00,0,6000.00,6000.00,6000.00,0.00,0.00,30.00,0.00,30.00,0.00,1510996786,1,1510996786,0,NULL,0),(23,0,22,2,20,0,1,0,23,2.00,2,120.00,120.00,0,240.00,240.00,0.00,0.00,0.00,1.00,0.00,2.00,0.00,1511003930,1,1511003930,0,NULL,0),(24,0,23,2,20,0,1,0,24,30.00,1,200.00,200.00,0,6000.00,6000.00,0.00,0.00,0.00,30.00,0.00,30.00,0.00,1511003943,1,1511003943,0,NULL,0),(25,0,24,2,21,0,1,0,25,30.00,2,100.00,100.00,0,3000.00,3000.00,3000.00,0.00,0.00,30.00,0.00,30.00,0.00,1511004143,1,1511004143,0,NULL,0),(26,0,25,2,22,0,1,0,26,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1511004217,1,1511004217,0,NULL,0),(27,0,26,2,23,0,1,0,27,90.00,2,220.00,193.60,0,19800.00,16738.66,16738.66,2376.00,685.34,45.00,1.00,90.00,2.00,1511144986,1,1511144986,0,NULL,0),(28,0,26,2,23,0,1,0,28,40.00,1,200.00,200.00,0,8000.00,7685.34,7685.34,0.00,314.66,40.00,3.00,40.00,3.00,1511144986,1,1511144986,0,NULL,0),(29,0,27,2,1,0,1,0,29,30.00,1,200.00,200.00,0,6000.00,6000.00,0.00,0.00,0.00,30.00,0.00,30.00,0.00,1511150330,1,1511150330,0,NULL,0),(30,0,28,2,2,0,1,0,30,30.00,1,200.00,200.00,0,6000.00,6000.00,0.00,0.00,0.00,30.00,0.00,30.00,0.00,1511150330,1,1511150330,0,NULL,0),(31,0,29,2,3,0,1,0,31,30.00,1,200.00,200.00,0,6000.00,6000.00,0.00,0.00,0.00,30.00,0.00,30.00,0.00,1511150330,1,1511150330,0,NULL,0),(32,0,30,2,4,0,1,0,32,30.00,1,200.00,200.00,0,6000.00,6000.00,0.00,0.00,0.00,30.00,0.00,30.00,0.00,1511150330,1,1511150330,0,NULL,0),(33,0,31,2,8,0,1,0,33,30.00,1,200.00,200.00,0,6000.00,6000.00,0.00,0.00,0.00,30.00,0.00,30.00,0.00,1511150330,1,1511150330,0,NULL,0),(34,0,32,2,9,0,1,0,34,30.00,1,200.00,200.00,0,6000.00,6000.00,0.00,0.00,0.00,30.00,0.00,30.00,0.00,1511150330,1,1511150330,0,NULL,0),(35,0,33,2,24,0,1,0,35,90.00,2,900.00,900.00,0,81000.00,81000.00,81000.00,0.00,0.00,90.00,0.00,90.00,0.00,1511151653,1,1511151653,0,NULL,0),(36,0,34,2,23,0,1,0,36,10.00,2,900.00,900.00,0,9000.00,9000.00,9000.00,0.00,0.00,10.00,0.00,10.00,0.00,1511161354,1,1511161354,0,NULL,0),(37,0,35,2,26,0,1,0,37,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1511257691,1,1511257691,0,NULL,0),(38,0,36,2,27,0,1,0,38,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1511257709,1,1511257709,0,NULL,0),(39,0,37,2,31,0,1,0,39,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1511323518,1,1511323518,0,NULL,0),(40,0,38,2,31,0,1,0,40,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1511323616,1,1511323616,0,NULL,0),(41,0,39,2,32,0,1,0,41,700.00,2,900.00,855.00,0,630000.00,598500.00,598500.00,31500.00,0.00,700.00,0.00,700.00,0.00,1511334001,1,1511334001,0,NULL,0),(42,0,40,2,33,0,1,0,42,1.00,1,200.00,200.00,0,200.00,200.00,0.00,0.00,0.00,1.00,0.00,1.00,0.00,1511338048,1,1511338048,0,NULL,0),(43,0,41,2,30,0,1,0,43,7.00,1,200.00,200.00,0,1400.00,1200.00,1200.00,0.00,200.00,7.00,3.00,7.00,3.00,1511345233,1,1511345233,0,NULL,0),(44,0,42,2,29,0,1,0,44,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,7.00,7.00,7.00,1511345331,1,1511345331,0,NULL,0),(45,0,43,2,34,0,1,0,45,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1511346067,1,1511346067,0,NULL,0),(46,0,44,2,35,0,1,0,46,7.00,1,200.00,200.00,0,1400.00,1300.00,1300.00,0.00,100.00,7.00,1.00,7.00,1.00,1511354434,1,1511354434,0,NULL,0),(47,0,45,2,36,0,1,0,47,7.00,1,200.00,200.00,0,1400.00,1100.00,1100.00,0.00,300.00,7.00,5.00,7.00,5.00,1511354475,1,1511354475,0,NULL,0),(48,0,46,2,37,0,1,0,48,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1511943146,1,1511943146,0,NULL,0),(49,0,46,2,37,0,1,0,49,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1511943146,1,1511943146,0,NULL,0),(50,0,47,2,37,0,1,0,49,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1511943352,1,1511943352,0,NULL,0),(51,0,47,2,37,0,1,0,48,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1511943352,1,1511943352,0,NULL,0),(52,0,48,2,37,0,1,0,50,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1511944947,1,1511944947,0,NULL,0),(53,0,48,2,37,0,1,0,51,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1511944947,1,1511944947,0,NULL,0),(54,0,49,2,37,0,1,0,52,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1511944999,1,1511944999,0,NULL,0),(55,0,50,2,37,0,1,0,49,31.00,2,120.00,120.00,0,3720.00,3720.00,3720.00,0.00,0.00,15.00,0.00,31.00,0.00,1511946840,1,1511946840,0,NULL,0),(56,0,50,2,37,0,1,0,48,31.00,2,220.00,220.00,0,6820.00,6820.00,6820.00,0.00,0.00,15.00,0.00,31.00,0.00,1511946840,1,1511946840,0,NULL,0),(57,0,50,2,37,0,1,0,53,31.00,2,180.00,180.00,0,5580.00,5580.00,5580.00,0.00,0.00,15.00,0.00,31.00,0.00,1511946840,1,1511946840,0,NULL,0),(58,0,51,2,37,0,1,0,48,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1511946896,1,1511946896,0,NULL,0),(59,0,51,2,37,0,1,0,54,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1511946896,1,1511946896,0,NULL,0),(60,0,52,2,37,0,1,0,53,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1511946953,1,1511946953,0,NULL,0),(61,0,53,2,37,0,1,0,53,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1511947045,1,1511947045,0,NULL,0),(62,0,53,2,37,0,1,0,48,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1511947045,1,1511947045,0,NULL,0),(63,0,54,2,38,0,1,0,55,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1511947107,1,1511947107,0,NULL,0),(64,0,55,2,39,0,1,0,56,3.00,2,900.00,900.00,0,2700.00,2700.00,2700.00,0.00,0.00,3.00,0.00,3.00,0.00,1511947296,1,1511947296,0,NULL,0),(65,0,56,2,40,0,1,0,57,30.00,2,180.00,180.00,0,5400.00,5337.21,5023.26,0.00,62.79,15.00,0.00,30.00,0.00,1512004807,1,1512004807,0,NULL,0),(66,0,56,2,40,0,1,0,58,1.00,1,200.00,200.00,0,200.00,197.67,186.04,0.00,2.33,1.00,0.00,1.00,0.00,1512004807,1,1512004807,0,NULL,0),(67,0,56,2,40,0,1,0,59,30.00,2,100.00,100.00,0,3000.00,2965.12,2790.70,0.00,34.88,30.00,0.00,30.00,0.00,1512004807,1,1512004807,0,NULL,0),(68,0,57,2,41,0,1,0,60,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512005259,1,1512005549,0,NULL,0),(69,0,57,2,41,0,1,0,61,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512005259,1,1512005549,0,NULL,0),(70,0,57,2,41,0,1,0,62,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512005259,1,1512005549,0,NULL,0),(71,0,58,2,41,0,1,0,61,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512005529,1,1512005529,0,NULL,0),(72,0,58,2,41,0,1,0,63,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512005529,1,1512005529,0,NULL,0),(73,0,59,2,37,0,1,0,53,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512028636,1,1512028636,0,NULL,0),(74,0,59,2,37,0,1,0,48,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512028636,1,1512028636,0,NULL,0),(77,0,61,2,43,0,1,0,66,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512036356,1,1512036356,0,NULL,0),(78,0,61,2,43,0,1,0,67,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512036356,1,1512036356,0,NULL,0),(79,0,61,2,43,7,1,1,0,1.00,0,600.00,600.00,0,600.00,600.00,600.00,0.00,0.00,1.00,0.00,0.00,0.00,1512036356,1,1512036356,0,NULL,0),(80,0,61,2,43,5,1,1,0,1.00,0,20000.00,20000.00,0,20000.00,20000.00,20000.00,0.00,0.00,1.00,0.00,0.00,0.00,1512036356,1,1512036356,0,NULL,0),(81,0,62,2,43,0,1,0,66,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512036526,1,1512036526,0,NULL,0),(82,0,62,2,43,0,1,0,67,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512036526,1,1512036526,0,NULL,0),(83,0,62,2,43,7,1,1,0,1.00,0,600.00,600.00,0,600.00,600.00,600.00,0.00,0.00,1.00,0.00,0.00,0.00,1512036526,1,1512036526,0,NULL,0),(84,0,62,2,43,8,1,1,0,1.00,0,102.00,102.00,0,102.00,102.00,102.00,0.00,0.00,1.00,0.00,0.00,0.00,1512036526,1,1512036526,0,NULL,0),(85,0,63,2,43,0,1,0,68,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1512036599,1,1512036599,0,NULL,0),(86,0,63,2,43,0,1,0,69,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512036599,1,1512036599,0,NULL,0),(87,0,63,2,43,6,1,1,0,1.00,0,1.00,1.00,0,1.00,1.00,1.00,0.00,0.00,1.00,0.00,0.00,0.00,1512036599,1,1512036599,0,NULL,0),(88,0,67,2,37,0,1,0,53,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512090620,1,1512090620,0,NULL,0),(89,0,67,2,37,0,1,0,54,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512090620,1,1512090620,0,NULL,0),(90,0,67,2,37,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1512090620,1,1512090620,0,NULL,0),(91,0,67,2,37,8,1,1,0,1.00,0,102.00,102.00,0,102.00,102.00,102.00,0.00,0.00,1.00,0.00,0.00,0.00,1512090620,1,1512090620,0,NULL,0),(92,0,68,2,38,0,1,0,70,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512090733,1,1512090733,0,NULL,0),(93,0,68,2,38,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1512090733,1,1512090733,0,NULL,0),(94,0,69,2,37,0,1,0,50,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512094995,1,1512094995,0,NULL,0),(95,0,69,2,37,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1512094995,1,1512094995,0,NULL,0),(96,0,70,2,37,0,1,0,53,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512098994,1,1512098994,0,NULL,0),(97,0,70,2,37,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1512098994,1,1512098994,0,NULL,0),(98,0,70,2,37,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1512098994,1,1512098994,0,NULL,0),(99,0,70,2,37,8,1,1,0,1.00,0,102.00,102.00,0,102.00,102.00,102.00,0.00,0.00,1.00,0.00,0.00,0.00,1512098994,1,1512098994,0,NULL,0),(100,0,71,2,4,0,1,0,71,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512099589,1,1512099589,0,NULL,0),(101,0,71,2,4,0,1,0,72,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512099589,1,1512099589,0,NULL,0),(102,0,72,2,49,0,1,0,73,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512099627,1,1512099627,0,NULL,0),(103,0,72,2,49,0,1,0,74,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512099627,1,1512099627,0,NULL,0),(104,0,72,2,49,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1512099627,1,1512099627,0,NULL,0),(105,0,72,2,49,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1512099627,1,1512099627,0,NULL,0),(106,0,72,2,49,8,1,1,0,1.00,0,102.00,102.00,0,102.00,102.00,102.00,0.00,0.00,1.00,0.00,0.00,0.00,1512099627,1,1512099627,0,NULL,0),(107,0,73,2,49,0,1,0,75,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512099640,1,1512099640,0,NULL,0),(108,0,73,2,49,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1512099640,1,1512099640,0,NULL,0),(109,0,74,2,49,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1512099651,1,1512099651,0,NULL,0),(110,0,74,2,49,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1512099651,1,1512099651,0,NULL,0),(111,0,74,2,49,8,1,1,0,1.00,0,102.00,102.00,0,102.00,102.00,102.00,0.00,0.00,1.00,0.00,0.00,0.00,1512099651,1,1512099651,0,NULL,0),(112,0,75,2,49,0,1,0,76,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512111149,1,1512111149,0,NULL,0),(113,0,75,2,49,1,1,1,0,10.00,0,3.00,3.00,0,30.00,30.00,30.00,0.00,0.00,1.00,0.00,0.00,0.00,1512111149,1,1512111149,0,NULL,0),(114,0,76,2,49,0,1,0,77,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512111302,1,1512111302,0,NULL,0),(115,0,76,2,49,1,1,1,0,10.00,0,3.00,3.00,0,30.00,30.00,30.00,0.00,0.00,1.00,0.00,0.00,0.00,1512111302,1,1512111302,0,NULL,0),(116,0,77,2,49,0,1,0,74,1.00,1,200.00,200.00,0,200.00,200.00,0.00,0.00,0.00,1.00,0.00,1.00,0.00,1512111483,1,1512111483,0,NULL,0),(117,0,77,2,49,0,1,0,73,30.00,2,180.00,180.00,0,5400.00,5400.00,0.00,0.00,0.00,15.00,0.00,30.00,0.00,1512111483,1,1512111483,0,NULL,0),(118,0,77,2,49,1,1,1,0,5.00,0,3.00,3.00,0,15.00,15.00,0.00,0.00,0.00,1.00,0.00,0.00,0.00,1512111483,1,1512111483,0,NULL,0),(119,0,77,2,49,3,1,1,0,7.00,0,90.00,90.00,0,630.00,630.00,0.00,0.00,0.00,1.00,0.00,0.00,0.00,1512111483,1,1512111483,0,NULL,0),(121,0,79,2,49,0,1,0,73,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512116680,1,1512116680,0,NULL,0),(122,0,79,2,49,0,1,0,77,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512116680,1,1512116680,0,NULL,0),(123,0,79,2,49,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1512116680,1,1512116680,0,NULL,0),(124,0,79,2,49,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1512116680,1,1512116680,0,NULL,0),(125,0,80,2,49,0,1,0,76,30.00,2,220.00,220.00,0,6600.00,6600.00,5827.53,0.00,0.00,15.00,0.00,30.00,0.00,1512116709,1,1512122645,0,NULL,0),(126,0,80,2,49,0,1,0,77,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,97.48,15.00,0.00,30.00,0.00,1512116709,1,1512122645,0,NULL,0),(127,0,80,2,49,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,2.44,1.00,0.00,0.00,0.00,1512116710,1,1512122645,0,NULL,0),(128,0,80,2,49,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.08,1.00,0.00,0.00,0.00,1512116710,1,1512122645,0,NULL,0),(129,0,81,2,49,0,1,0,73,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512116846,1,1512117268,0,NULL,0),(130,0,81,2,49,0,1,0,78,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1512116846,1,1512117268,0,NULL,0),(131,0,81,2,49,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1512116846,1,1512117268,0,NULL,0),(136,0,83,2,49,0,1,0,80,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512118175,1,1512119102,0,NULL,0),(137,0,84,2,49,0,1,0,77,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512119558,1,1512120542,0,NULL,0),(138,0,84,2,49,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1512119558,1,1512120542,0,NULL,0),(139,0,84,2,49,0,1,0,79,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1512119558,1,1512120542,0,NULL,0),(141,0,86,4,5,0,1,0,81,14.00,1,200.00,200.00,0,2800.00,2700.00,2700.00,0.00,100.00,14.00,0.00,14.00,0.00,1512350735,1,1512350735,0,NULL,0),(142,0,86,4,5,0,1,0,82,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512350735,1,1512350735,0,NULL,0),(143,0,87,2,7,0,1,0,83,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512465297,1,1512465297,0,NULL,0),(144,0,87,2,7,0,1,0,84,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512465297,1,1512465297,0,NULL,0),(145,0,87,2,7,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1512465297,1,1512465297,0,NULL,0),(146,0,87,2,7,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1512465297,1,1512465297,0,NULL,0),(147,0,88,2,52,0,1,0,85,30.00,2,180.00,180.00,0,5400.00,5400.00,5209.00,0.00,0.00,15.00,0.00,30.00,0.00,1512469292,1,1512469292,0,NULL,0),(158,0,97,2,52,0,1,0,93,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512533556,1,1512533556,0,NULL,0),(159,0,97,2,52,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1512533556,1,1512533556,0,NULL,0),(160,0,98,2,52,0,1,0,85,30.00,2,180.00,180.00,0,5400.00,5400.00,4918.03,0.00,0.00,15.00,0.00,30.00,0.00,1512541852,1,1512541852,0,NULL,0),(161,0,98,2,52,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,81.97,0.00,0.00,1.00,0.00,0.00,0.00,1512541852,1,1512541852,0,NULL,0),(162,0,99,2,52,0,1,0,85,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512546480,1,1512546480,0,NULL,0),(163,0,99,2,52,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1512546480,1,1512546480,0,NULL,0),(164,0,100,2,53,0,1,0,94,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512549245,1,1512549245,0,NULL,0),(165,0,101,2,11,0,1,0,11,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512549247,1,1512549247,0,NULL,0),(166,0,102,2,12,0,1,0,95,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512549483,1,1512549483,0,NULL,0),(167,0,103,2,57,0,1,0,96,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512549683,1,1512549683,0,NULL,0),(174,0,108,2,57,0,1,0,100,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512556368,1,1512556368,0,NULL,0),(175,0,108,2,57,0,1,0,101,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512556368,1,1512556368,0,NULL,0),(176,0,109,2,57,0,1,0,100,30.00,2,220.00,220.00,0,6600.00,6600.00,0.00,0.00,0.00,15.00,0.00,30.00,0.00,1512556401,1,1512556401,0,NULL,0),(177,0,109,2,57,0,1,0,101,30.00,2,180.00,180.00,0,5400.00,5400.00,0.00,0.00,0.00,15.00,0.00,30.00,0.00,1512556401,1,1512556401,0,NULL,0),(178,0,109,2,57,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,0.00,0.00,0.00,1.00,0.00,0.00,0.00,1512556401,1,1512556401,0,NULL,0),(179,0,110,2,57,0,1,0,100,30.00,2,220.00,220.00,0,6600.00,6600.00,0.00,0.00,0.00,15.00,0.00,30.00,0.00,1512558812,1,1512558812,0,NULL,0),(180,0,111,2,13,0,1,0,19,30.00,2,120.00,120.00,0,3600.00,3600.00,70.59,0.00,0.00,15.00,0.00,30.00,0.00,1512611348,1,1512611348,0,NULL,0),(181,0,111,2,13,3,1,1,0,11.00,0,90.00,90.00,0,990.00,990.00,19.41,0.00,0.00,1.00,0.00,0.00,0.00,1512611348,1,1512611348,0,NULL,0),(182,0,112,2,14,0,1,0,102,30.00,2,120.00,120.00,0,3600.00,3600.00,47.37,0.00,0.00,15.00,0.00,30.00,0.00,1512614181,1,1512614181,0,NULL,0),(183,0,112,2,14,1,1,1,0,16.00,0,3.00,3.00,0,48.00,48.00,0.63,0.00,0.00,1.00,0.00,0.00,0.00,1512614181,1,1512614181,0,NULL,0),(184,0,113,2,43,0,1,0,103,2.00,1,200.00,200.00,0,400.00,400.00,0.00,0.00,0.00,2.00,0.00,2.00,0.00,1512615429,1,1512615429,0,NULL,0),(185,0,114,2,57,0,1,0,96,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512615958,1,1512615958,0,NULL,0),(186,0,115,2,57,0,1,0,96,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512615974,1,1512615974,0,NULL,0),(187,0,116,2,15,0,1,0,104,30.00,2,220.00,220.00,0,6600.00,6600.00,600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512617146,1,1512617146,0,NULL,0),(188,0,117,2,16,0,1,0,105,30.00,2,180.00,180.00,0,5400.00,5400.00,400.00,0.00,0.00,15.00,0.00,30.00,0.00,1512617931,1,1512617931,0,NULL,0),(189,0,118,2,17,0,1,0,106,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512618093,1,1512618093,0,NULL,0),(190,0,119,2,18,0,1,0,107,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1512618204,1,1512618204,0,NULL,0),(191,0,120,2,19,0,1,0,108,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1512618266,1,1512618266,0,NULL,0),(192,0,121,2,20,0,1,0,109,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512618505,1,1512618505,0,NULL,0),(193,0,122,2,43,0,1,0,68,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1512630478,1,1512630478,0,NULL,0),(194,0,123,2,43,0,1,0,103,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512630543,1,1512630543,0,NULL,0),(195,0,124,2,43,0,1,0,103,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512630769,1,1512630769,0,NULL,0),(196,0,125,2,56,0,1,0,110,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1512636896,1,1512636896,0,NULL,0),(197,0,126,2,61,0,1,0,111,6.00,1,200.00,200.00,0,1200.00,1200.00,0.00,0.00,0.00,6.00,0.00,6.00,0.00,1512702013,1,1512702013,0,NULL,0),(198,0,126,2,61,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,1512702013,1,1512702013,0,NULL,0),(199,0,127,2,65,0,1,0,112,30.00,2,220.00,220.00,0,6600.00,6600.00,0.00,0.00,0.00,15.00,0.00,30.00,0.00,1512702354,1,1512702354,0,NULL,0),(200,0,128,2,65,0,1,0,112,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512707405,1,1512707405,0,NULL,0),(201,0,129,2,21,0,1,0,113,2.00,1,200.00,200.00,0,400.00,400.00,400.00,0.00,0.00,2.00,0.00,2.00,0.00,1512713010,1,1512713010,0,NULL,0),(202,0,129,2,21,8,0,1,0,12.00,0,102.00,102.00,0,1224.00,1224.00,1224.00,0.00,0.00,1.00,0.00,0.00,0.00,1512713010,1,1512713010,0,NULL,0),(203,0,129,2,21,0,1,0,113,2.00,1,200.00,200.00,0,400.00,400.00,400.00,0.00,0.00,2.00,0.00,2.00,0.00,1512713010,1,1512713010,0,NULL,0),(204,0,130,2,22,0,1,0,114,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512713180,1,1512713180,0,NULL,0),(205,0,130,2,22,8,0,1,0,6.00,0,102.00,102.00,0,612.00,612.00,612.00,0.00,0.00,1.00,0.00,0.00,0.00,1512713180,1,1512713180,0,NULL,0),(206,0,130,2,22,0,1,0,114,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512713180,1,1512713180,0,NULL,0),(207,0,131,2,23,0,1,0,28,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512713395,1,1512713395,0,NULL,0),(208,0,131,2,23,8,0,1,0,6.00,0,102.00,102.00,0,612.00,612.00,612.00,0.00,0.00,1.00,0.00,0.00,0.00,1512713395,1,1512713395,0,NULL,0),(209,0,131,2,23,0,1,0,28,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512713395,1,1512713395,0,NULL,0),(210,0,132,2,24,0,1,0,115,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512713599,1,1512713599,0,NULL,0),(211,0,133,2,70,0,1,0,116,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1512713702,1,1512713702,0,NULL,0),(212,0,133,2,70,8,0,1,0,3.00,0,102.00,102.00,0,306.00,306.00,306.00,0.00,0.00,1.00,0.00,0.00,0.00,1512713702,1,1512713702,0,NULL,0),(213,0,134,2,70,0,1,0,117,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512718382,1,1512718382,0,NULL,0),(214,0,135,2,70,0,1,0,118,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1512718749,1,1512718749,0,NULL,0),(215,0,135,2,70,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1512718749,1,1512718749,0,NULL,0),(216,0,135,2,70,0,1,0,119,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1512718749,1,1512718749,0,NULL,0),(217,0,136,2,69,0,1,0,120,6.00,1,200.00,200.00,0,1200.00,1200.00,0.00,0.00,0.00,6.00,0.00,6.00,0.00,1513050857,1,1513050857,0,NULL,0),(218,0,136,2,69,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,1513050857,1,1513050857,0,NULL,0),(219,0,137,2,72,0,1,0,121,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513058791,1,1513081951,0,NULL,0),(220,0,138,2,72,0,1,0,122,6.00,1,200.00,200.00,0,1200.00,1200.00,1200.00,0.00,0.00,6.00,0.00,6.00,0.00,1513058936,1,1513058936,0,NULL,0),(221,0,139,2,72,0,1,0,123,1.00,2,900.00,900.00,0,900.00,900.00,1.00,0.00,0.00,1.00,0.00,1.00,0.00,1513059662,1,1513059662,0,NULL,0),(222,0,139,2,72,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,1.00,0.00,0.00,1.00,0.00,0.00,0.00,1513059662,1,1513059662,0,NULL,0),(223,0,140,2,72,0,1,0,124,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513067900,1,1513067900,0,NULL,0),(224,0,140,2,72,0,1,0,125,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1513067900,1,1513067900,0,NULL,0),(225,0,140,2,72,8,0,1,0,3.00,0,102.00,102.00,0,306.00,306.00,306.00,0.00,0.00,1.00,0.00,0.00,0.00,1513067900,1,1513067900,0,NULL,0),(226,0,141,2,71,0,1,0,126,12.00,2,120.00,120.00,0,1440.00,1440.00,0.00,0.00,0.00,6.00,0.00,12.00,0.00,1513068924,1,1513068924,0,NULL,0),(227,0,142,2,72,0,1,0,123,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513071375,1,1513071375,0,NULL,0),(228,0,142,2,72,1,1,1,0,2.00,0,3.00,3.00,0,6.00,6.00,6.00,0.00,0.00,1.00,0.00,0.00,0.00,1513071375,1,1513071375,0,NULL,0),(229,0,142,2,72,0,1,0,123,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513071375,1,1513071375,0,NULL,0),(230,0,143,2,72,0,1,0,123,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513071539,1,1513071539,0,NULL,0),(231,0,143,2,72,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1513071539,1,1513071539,0,NULL,0),(232,0,143,2,72,0,1,0,124,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513071539,1,1513071539,0,NULL,0),(233,0,144,2,72,0,1,0,123,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513071789,1,1513071789,0,NULL,0),(234,0,144,2,72,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1513071789,1,1513071789,0,NULL,0),(235,0,145,2,72,0,1,0,124,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513071844,1,1513071844,0,NULL,0),(236,0,145,2,72,0,1,0,124,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513071844,1,1513071844,0,NULL,0),(237,0,146,2,72,0,1,0,125,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1513071948,1,1513071948,0,NULL,0),(238,0,146,2,72,8,0,1,0,3.00,0,102.00,102.00,0,306.00,306.00,306.00,0.00,0.00,1.00,0.00,0.00,0.00,1513071948,1,1513071948,0,NULL,0),(239,0,146,2,72,0,1,0,124,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513071948,1,1513071948,0,NULL,0),(240,0,147,2,72,0,1,0,125,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1513071994,1,1513071994,0,NULL,0),(241,0,147,2,72,8,0,1,0,3.00,0,102.00,102.00,0,306.00,306.00,306.00,0.00,0.00,1.00,0.00,0.00,0.00,1513071994,1,1513071994,0,NULL,0),(242,0,148,2,71,0,1,0,127,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513072145,1,1513072145,0,NULL,0),(243,0,148,2,71,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1513072145,1,1513072145,0,NULL,0),(244,0,149,2,74,0,1,0,128,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513072202,1,1513072202,0,NULL,0),(245,0,149,2,74,0,1,0,129,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1513072202,1,1513072202,0,NULL,0),(246,0,149,2,74,8,0,1,0,3.00,0,102.00,102.00,0,306.00,306.00,306.00,0.00,0.00,1.00,0.00,0.00,0.00,1513072202,1,1513072202,0,NULL,0),(247,0,150,2,74,0,1,0,130,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513072248,1,1513072248,0,NULL,0),(248,0,150,2,74,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1513072248,1,1513072248,0,NULL,0),(249,0,151,2,72,0,1,0,123,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513072338,1,1513072338,0,NULL,0),(250,0,151,2,72,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1513072338,1,1513072338,0,NULL,0),(251,0,152,2,72,0,1,0,123,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513072406,1,1513072406,0,NULL,0),(252,0,152,2,72,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1513072406,1,1513072406,0,NULL,0),(253,0,153,2,75,0,1,0,131,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513072878,1,1513072878,0,NULL,0),(254,0,153,2,75,1,1,1,0,2.00,0,3.00,3.00,0,6.00,6.00,6.00,0.00,0.00,1.00,0.00,0.00,0.00,1513072878,1,1513072878,0,NULL,0),(255,0,153,2,75,0,1,0,131,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513072878,1,1513072878,0,NULL,0),(256,0,154,2,76,0,1,0,132,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513073819,1,1513073819,0,NULL,0),(257,0,154,2,76,1,1,1,0,2.00,0,3.00,3.00,0,6.00,6.00,6.00,0.00,0.00,1.00,0.00,0.00,0.00,1513073819,1,1513073819,0,NULL,0),(258,0,154,2,76,0,1,0,132,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513073819,1,1513073819,0,NULL,0),(259,0,155,2,77,0,1,0,133,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513080069,1,1513080069,0,NULL,0),(260,0,155,2,77,1,1,1,0,2.00,0,3.00,3.00,0,6.00,6.00,6.00,0.00,0.00,1.00,0.00,0.00,0.00,1513080069,1,1513080069,0,NULL,0),(261,0,155,2,77,0,1,0,134,7.00,1,200.00,200.00,0,1400.00,1400.00,1400.00,0.00,0.00,7.00,0.00,7.00,0.00,1513080069,1,1513080069,0,NULL,0),(262,0,156,2,78,0,1,0,135,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513081124,1,1513081124,0,NULL,0),(263,0,156,2,78,0,1,0,136,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1513081124,1,1513081124,0,NULL,0),(264,0,156,2,78,8,0,1,0,3.00,0,102.00,102.00,0,306.00,306.00,306.00,0.00,0.00,1.00,0.00,0.00,0.00,1513081124,1,1513081124,0,NULL,0),(265,0,157,2,72,0,1,0,124,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513082011,1,1513082011,0,NULL,0),(266,0,158,2,78,0,1,0,135,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513082035,1,1513082035,0,NULL,0),(267,0,161,2,78,0,1,0,136,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1513129216,1,1513129216,0,NULL,0),(268,0,161,2,78,8,0,1,0,3.00,0,102.00,102.00,0,306.00,306.00,306.00,0.00,0.00,1.00,0.00,0.00,0.00,1513129216,1,1513129216,0,NULL,0),(269,0,161,2,78,0,1,0,137,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513129216,1,1513129216,0,NULL,0),(270,0,161,2,78,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1513129216,1,1513129216,0,NULL,0),(271,0,162,2,65,0,1,0,138,3.00,2,900.00,900.00,0,2700.00,2700.00,0.00,0.00,0.00,3.00,0.00,3.00,0.00,1513132440,1,1513132440,0,NULL,0),(272,0,163,2,64,0,1,0,139,3.00,2,900.00,900.00,0,2700.00,2700.00,0.00,0.00,0.00,3.00,0.00,3.00,0.00,1513132441,1,1513132441,0,NULL,0),(273,0,164,2,63,0,1,0,140,3.00,2,900.00,900.00,0,2700.00,2700.00,0.00,0.00,0.00,3.00,0.00,3.00,0.00,1513132441,1,1513132441,0,NULL,0),(274,0,165,2,62,0,1,0,141,3.00,2,900.00,900.00,0,2700.00,2700.00,0.00,0.00,0.00,3.00,0.00,3.00,0.00,1513132441,1,1513132441,0,NULL,0),(275,0,166,2,61,0,1,0,142,3.00,2,900.00,900.00,0,2700.00,2700.00,0.00,0.00,0.00,3.00,0.00,3.00,0.00,1513132441,1,1513132441,0,NULL,0),(276,0,167,2,60,0,1,0,143,3.00,2,900.00,900.00,0,2700.00,2700.00,0.00,0.00,0.00,3.00,0.00,3.00,0.00,1513132441,1,1513132441,0,NULL,0),(277,0,168,2,79,0,1,0,144,1.00,2,900.00,900.00,0,900.00,900.00,900.00,0.00,0.00,1.00,0.00,1.00,0.00,1513133823,1,1513133823,0,NULL,0),(278,0,168,2,79,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1513133823,1,1513133823,0,NULL,0),(279,0,168,2,79,0,1,0,145,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513133823,1,1513133823,0,NULL,0),(280,0,169,2,79,0,1,0,146,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1513243794,1,1513243794,0,NULL,0),(281,0,169,2,79,8,0,1,0,2.00,0,102.00,102.00,0,204.00,204.00,204.00,0.00,0.00,1.00,0.00,0.00,0.00,1513243794,1,1513243794,0,NULL,0),(282,0,169,2,79,1,1,1,0,2.00,0,3.00,3.00,0,6.00,6.00,6.00,0.00,0.00,1.00,0.00,0.00,0.00,1513243794,1,1513243794,0,NULL,0),(283,0,170,2,79,0,1,0,147,30.00,2,180.00,180.00,0,5400.00,5400.00,5400.00,0.00,0.00,15.00,0.00,30.00,0.00,1513243886,1,1513243886,0,NULL,0),(284,0,170,2,79,3,1,1,0,1.00,0,90.00,90.00,0,90.00,90.00,90.00,0.00,0.00,1.00,0.00,0.00,0.00,1513243886,1,1513243886,0,NULL,0),(285,0,170,2,79,8,0,1,0,1.00,0,102.00,102.00,0,102.00,102.00,102.00,0.00,0.00,1.00,0.00,0.00,0.00,1513243886,1,1513243886,0,NULL,0),(286,0,170,2,79,1,1,1,0,1.00,0,3.00,3.00,0,3.00,3.00,3.00,0.00,0.00,1.00,0.00,0.00,0.00,1513243886,1,1513243886,0,NULL,0),(287,0,170,2,79,5,0,1,0,3.00,0,20000.00,20000.00,0,60000.00,60000.00,60000.00,0.00,0.00,1.00,0.00,0.00,0.00,1513243886,1,1513243886,0,NULL,0),(288,0,171,2,82,0,1,0,148,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513819578,1,1513819578,0,NULL,0),(289,0,172,2,82,0,1,0,149,1.00,1,200.00,200.00,0,200.00,200.00,0.00,0.00,0.00,1.00,0.00,1.00,0.00,1513820370,1,1513820370,0,NULL,0),(290,0,172,2,82,8,0,1,0,3.00,0,102.00,102.00,0,306.00,306.00,0.00,0.00,0.00,1.00,0.00,0.00,0.00,1513820370,1,1513820370,0,NULL,0),(291,0,173,2,83,0,1,0,150,30.00,2,120.00,120.00,0,3600.00,3600.00,2600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513824352,1,1513824352,0,NULL,0),(292,0,174,2,83,0,1,0,151,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1513825288,1,1513825288,0,NULL,0),(293,0,174,2,83,8,0,1,0,5.00,0,102.00,102.00,0,510.00,510.00,510.00,0.00,0.00,1.00,0.00,0.00,0.00,1513825288,1,1513825288,0,NULL,0),(294,0,174,2,83,1,1,1,0,2.00,0,3.00,3.00,0,6.00,6.00,6.00,0.00,0.00,1.00,0.00,0.00,0.00,1513825288,1,1513825288,0,NULL,0),(295,0,174,2,83,0,1,0,152,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1513825288,1,1513825288,0,NULL,0),(296,0,175,2,83,0,1,0,153,30.00,2,100.00,100.00,0,3000.00,3000.00,1095.70,0.00,0.00,30.00,0.00,30.00,0.00,1513826157,1,1513826157,0,NULL,0),(297,0,175,2,83,3,1,1,0,3.00,0,90.00,90.00,0,270.00,270.00,98.61,0.00,0.00,1.00,0.00,0.00,0.00,1513826157,1,1513826157,0,NULL,0),(298,0,175,2,83,0,1,0,154,2.00,2,900.00,900.00,0,1800.00,1800.00,657.41,0.00,0.00,2.00,0.00,2.00,0.00,1513826157,1,1513826157,0,NULL,0),(299,0,175,2,83,1,1,1,0,2.00,0,3.00,3.00,0,6.00,6.00,2.19,0.00,0.00,1.00,0.00,0.00,0.00,1513826157,1,1513826157,0,NULL,0),(300,0,175,2,83,0,1,0,155,2.00,1,200.00,200.00,0,400.00,400.00,146.09,0.00,0.00,2.00,0.00,2.00,0.00,1513826157,1,1513826157,0,NULL,0),(301,0,176,2,83,0,1,0,150,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513826325,1,1513826325,0,NULL,0),(302,0,177,2,84,0,1,0,156,30.00,2,120.00,120.00,0,3600.00,3600.00,3600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513828003,1,1513828003,0,NULL,0),(303,0,178,2,84,0,1,0,157,30.00,2,220.00,220.00,0,6600.00,6600.00,6600.00,0.00,0.00,15.00,0.00,30.00,0.00,1513828028,1,1513828028,0,NULL,0),(304,0,179,2,84,0,1,0,158,1.00,1,200.00,200.00,0,200.00,200.00,200.00,0.00,0.00,1.00,0.00,1.00,0.00,1513828597,1,1513828597,0,NULL,0),(305,0,179,2,84,8,0,1,0,3.00,0,102.00,102.00,0,306.00,306.00,306.00,0.00,0.00,1.00,0.00,0.00,0.00,1513828597,1,1513828597,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=278 DEFAULT CHARSET=utf8mb4 COMMENT='订单付款记录ID';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_payment_history`
--

LOCK TABLES `x360p_order_payment_history` WRITE;
/*!40000 ALTER TABLE `x360p_order_payment_history` DISABLE KEYS */;
INSERT INTO `x360p_order_payment_history` VALUES (132,0,2,1,0,2,3600.00,1510934400,1510971618,1,1510971618,0,NULL,0),(133,0,2,2,0,2,3600.00,1510934400,1510971695,1,1510971695,0,NULL,0),(134,0,2,3,0,2,3600.00,1510934400,1510971718,1,1510971718,0,NULL,0),(135,0,2,4,0,2,3600.00,1510934400,1510971764,1,1510971764,0,NULL,0),(136,0,2,5,0,2,3600.00,1510934400,1510971975,1,1510971975,0,NULL,0),(137,0,2,6,0,2,3600.00,1510934400,1510971977,1,1510971977,0,NULL,0),(138,0,2,7,0,2,3600.00,1510934400,1510972024,1,1510972024,0,NULL,0),(139,0,2,8,0,2,3600.00,1510934400,1510972042,1,1510972042,0,NULL,0),(140,0,2,9,0,2,3600.00,1510934400,1510972045,1,1510972045,0,NULL,0),(141,0,2,10,0,2,3600.00,1510934400,1510974276,1,1510974276,0,NULL,0),(142,0,2,11,0,2,120.00,1510934400,1510993877,1,1510993877,0,NULL,0),(143,0,2,12,0,2,200.00,1510934400,1510994219,1,1510994219,0,NULL,0),(144,0,2,13,0,2,200.00,1510934400,1510994693,1,1510994693,0,NULL,0),(145,0,2,14,0,2,200.00,1510934400,1510994712,1,1510994712,0,NULL,0),(146,0,2,15,0,2,200.00,1510934400,1510994740,1,1510994740,0,NULL,0),(147,0,2,16,0,2,200.00,1510934400,1510994756,1,1510994756,0,NULL,0),(148,0,2,17,0,2,200.00,1510934400,1510994770,1,1510994770,0,NULL,0),(149,0,2,18,0,2,6800.00,1510934400,1510995734,1,1510995734,0,NULL,0),(150,0,2,19,0,2,6500.00,1510934400,1510996786,1,1510996786,0,NULL,0),(151,0,2,20,0,2,3000.00,1510934400,1511004143,1,1511004143,0,NULL,0),(152,0,2,21,0,2,5400.00,1510934400,1511004217,1,1511004217,0,NULL,0),(153,0,2,22,0,2,24424.00,1511107200,1511144986,1,1511144986,0,NULL,0),(154,0,2,23,0,2,81000.00,1511107200,1511151653,1,1511151653,0,NULL,0),(155,0,2,24,0,2,9000.00,1511107200,1511161354,1,1511161354,0,NULL,0),(156,0,2,25,0,2,1400.00,1511193600,1511257691,1,1511257691,0,NULL,0),(157,0,2,26,0,2,1400.00,1511193600,1511257709,1,1511257709,0,NULL,0),(158,0,2,27,0,2,1400.00,1511280000,1511323518,1,1511323518,0,NULL,0),(159,0,2,28,0,2,1400.00,1511280000,1511323616,1,1511323616,0,NULL,0),(160,0,2,29,0,2,598500.00,1511280000,1511334001,1,1511334001,0,NULL,0),(161,0,2,30,0,2,1200.00,1511280000,1511345233,1,1511345233,0,NULL,0),(162,0,2,31,0,2,1400.00,1511280000,1511345331,1,1511345331,0,NULL,0),(163,0,2,32,0,2,200.00,1511280000,1511346067,1,1511346067,0,NULL,0),(164,0,2,33,0,2,1300.00,1511280000,1511354434,1,1511354434,0,NULL,0),(165,0,2,34,0,2,1100.00,1511280000,1511354475,1,1511354475,0,NULL,0),(166,0,2,35,0,2,10200.00,1511884800,1511943146,1,1511943146,0,NULL,0),(167,0,2,36,0,2,10200.00,1511884800,1511943352,1,1511943352,0,NULL,0),(168,0,2,37,0,2,400.00,1511884800,1511944947,1,1511944947,0,NULL,0),(169,0,2,38,0,2,900.00,1511884800,1511944999,1,1511944999,0,NULL,0),(170,0,2,39,0,2,16120.00,1511884800,1511946840,1,1511946840,0,NULL,0),(171,0,2,40,0,2,6800.00,1511884800,1511946896,1,1511946896,0,NULL,0),(172,0,2,41,0,2,5400.00,1511884800,1511946953,1,1511946953,0,NULL,0),(173,0,2,42,0,2,12000.00,1511884800,1511947045,1,1511947045,0,NULL,0),(174,0,2,43,0,2,200.00,1511884800,1511947107,1,1511947107,0,NULL,0),(175,0,2,44,0,2,2700.00,1511884800,1511947296,1,1511947296,0,NULL,0),(176,0,2,45,0,2,8000.00,1511971200,1512004807,1,1512004807,0,NULL,0),(177,0,2,46,0,2,9000.00,1511971200,1512005529,1,1512005529,0,NULL,0),(178,0,2,47,0,2,12200.00,1511971200,1512005549,1,1512005549,0,NULL,0),(179,0,2,48,0,2,12000.00,1511971200,1512028636,1,1512028636,0,NULL,0),(180,0,2,49,0,2,32600.00,1511971200,1512036356,1,1512036356,0,NULL,0),(181,0,2,50,0,2,12702.00,1511971200,1512036526,1,1512036526,0,NULL,0),(182,0,2,51,0,2,1601.00,1511971200,1512036599,1,1512036599,0,NULL,0),(183,0,2,52,0,2,5792.00,1512057600,1512090620,1,1512090620,0,NULL,0),(184,0,2,53,0,2,5403.00,1512057600,1512090733,1,1512090733,0,NULL,0),(185,0,2,55,0,2,5403.00,1512057600,1512098994,1,1512098994,0,NULL,0),(186,0,2,56,0,2,12000.00,1512057600,1512099589,1,1512099589,0,NULL,0),(187,0,2,57,0,2,5795.00,1512057600,1512099627,1,1512099627,0,NULL,0),(188,0,2,58,0,2,203.00,1512057600,1512099640,1,1512099640,0,NULL,0),(189,0,2,59,0,2,195.00,1512057600,1512099651,1,1512099651,0,NULL,0),(190,0,2,60,0,2,6050.00,1512057600,1512111149,1,1512111149,0,NULL,0),(191,0,2,61,0,2,2500.00,1512057600,1512111302,1,1512111302,0,NULL,0),(192,0,2,62,0,2,9093.00,1512057600,1512116680,1,1512116680,0,NULL,0),(193,0,2,63,0,2,6803.00,1512057600,1512117268,1,1512117268,0,NULL,0),(194,0,2,64,0,2,200.00,1512057600,1512119102,1,1512119102,0,NULL,0),(195,0,2,65,0,2,4590.00,1512057600,1512120542,1,1512120542,0,NULL,0),(196,0,2,66,0,2,9000.00,1512057600,1512122645,1,1512122645,0,NULL,0),(197,0,2,67,0,2,1650.53,1512057600,1512123379,1,1512123379,0,NULL,0),(198,0,4,68,0,4,2900.00,1512316800,1512350735,1,1512350735,0,NULL,0),(199,0,2,69,0,2,5693.00,1512403200,1512465297,1,1512465297,0,NULL,0),(200,0,2,70,0,2,5000.00,1512403200,1512469292,1,1512469292,0,NULL,0),(201,0,2,71,0,2,200.00,1512403200,1512532147,1,1512532147,0,NULL,0),(202,0,2,72,0,2,1.00,1512403200,1512532314,1,1512532314,0,NULL,0),(203,0,2,73,0,2,1.00,1512403200,1512533285,1,1512533285,0,NULL,0),(204,0,2,74,0,2,1.00,1512403200,1512533390,1,1512533390,0,NULL,0),(205,0,2,75,0,2,1.00,1512403200,1512533433,1,1512533433,0,NULL,0),(206,0,2,76,0,2,5000.00,1512489600,1512533556,1,1512533556,0,NULL,0),(207,0,2,77,0,2,11.00,1512403200,1512533735,1,1512533735,0,NULL,0),(208,0,2,78,0,2,1681.00,1512403200,1512534428,1,1512534428,0,NULL,0),(209,0,2,79,0,2,1.00,1512403200,1512540608,1,1512540608,0,NULL,0),(210,0,2,80,0,2,1.00,1512403200,1512540682,1,1512540682,0,NULL,0),(211,0,2,81,0,2,1.00,1512403200,1512541724,1,1512541724,0,NULL,0),(212,0,2,82,0,2,5000.00,1512489600,1512541852,1,1512541852,0,NULL,0),(213,0,2,83,0,2,5000.00,1512489600,1512546480,1,1512546480,0,NULL,0),(214,0,2,83,0,1,490.00,1512489600,1512546480,1,1512546480,0,NULL,0),(215,0,2,84,0,2,3600.00,1512489600,1512549245,1,1512549245,0,NULL,0),(216,0,2,85,0,2,3600.00,1512489600,1512549247,1,1512549247,0,NULL,0),(217,0,2,86,0,2,3600.00,1512489600,1512549483,1,1512549483,0,NULL,0),(218,0,2,87,0,2,3600.00,1512489600,1512549684,1,1512549684,0,NULL,0),(219,0,2,88,0,2,10000.00,1512489600,1512556368,1,1512556368,0,NULL,0),(220,0,2,89,0,2,90.00,1512576000,1512611348,1,1512611348,0,NULL,0),(221,0,2,90,0,2,900.00,1512489600,1512612857,1,1512612857,0,NULL,0),(222,0,2,91,0,2,2.00,1512489600,1512613263,1,1512613263,0,NULL,0),(223,0,2,92,0,2,1098.00,1512489600,1512613276,1,1512613276,0,NULL,0),(224,0,2,93,0,2,48.00,1512576000,1512614181,1,1512614181,0,NULL,0),(225,0,2,94,0,2,3600.00,1512576000,1512615958,1,1512615958,0,NULL,0),(226,0,2,95,0,2,3600.00,1512576000,1512615974,1,1512615974,0,NULL,0),(227,0,2,96,0,2,600.00,1512576000,1512617146,1,1512617146,0,NULL,0),(228,0,2,97,0,2,400.00,1512576000,1512617931,1,1512617931,0,NULL,0),(229,0,2,98,0,2,200.00,1512576000,1512618093,1,1512618093,0,NULL,0),(230,0,2,99,0,2,1400.00,1512576000,1512618205,1,1512618205,0,NULL,0),(231,0,2,100,0,2,900.00,1512576000,1512618266,1,1512618266,0,NULL,0),(232,0,2,101,0,2,200.00,1512576000,1512618505,1,1512618505,0,NULL,0),(233,0,2,102,0,2,1400.00,1512576000,1512630478,1,1512630478,0,NULL,0),(234,0,2,103,0,2,200.00,1512576000,1512630543,1,1512630543,0,NULL,0),(235,0,2,104,0,2,200.00,1512576000,1512630769,1,1512630769,0,NULL,0),(236,0,2,105,0,2,1400.00,0,1512701582,1,1512701582,0,NULL,0),(237,0,2,106,0,2,6600.00,1512662400,1512707405,1,1512707405,0,NULL,0),(238,0,2,107,0,2,2024.00,1512662400,1512713010,1,1512713010,0,NULL,0),(239,0,2,108,0,2,1012.00,1512662400,1512713180,1,1512713180,0,NULL,0),(240,0,2,109,0,2,1012.00,1512662400,1512713395,1,1512713395,0,NULL,0),(241,0,2,110,0,2,200.00,1512662400,1512713599,1,1512713599,0,NULL,0),(242,0,2,111,0,2,506.00,1512662400,1512713702,1,1512713702,0,NULL,0),(243,0,2,112,0,2,3600.00,1512662400,1512718382,1,1512718382,0,NULL,0),(244,0,2,113,0,2,8003.00,1512662400,1512718749,1,1512718749,0,NULL,0),(245,0,2,114,0,2,2.00,1513008000,1513065163,1,1513065163,0,NULL,0),(246,0,2,115,0,2,1200.00,0,1513065195,1,1513065195,0,NULL,0),(247,0,2,116,0,2,4106.00,1513008000,1513067900,1,1513067900,0,NULL,0),(248,0,2,117,0,2,1806.00,1513008000,1513071375,1,1513071375,0,NULL,0),(249,0,2,118,0,2,4503.00,1513008000,1513071539,1,1513071539,0,NULL,0),(250,0,2,119,0,2,903.00,1513008000,1513071789,1,1513071789,0,NULL,0),(251,0,2,120,0,2,7200.00,1513008000,1513071844,1,1513071844,0,NULL,0),(252,0,2,121,0,2,4106.00,1513008000,1513071948,1,1513071948,0,NULL,0),(253,0,2,122,0,2,506.00,1513008000,1513071994,1,1513071994,0,NULL,0),(254,0,2,123,0,2,903.00,1513008000,1513072145,1,1513072145,0,NULL,0),(255,0,2,124,0,2,4106.00,1513008000,1513072202,1,1513072202,0,NULL,0),(256,0,2,125,0,2,903.00,1513008000,1513072248,1,1513072248,0,NULL,0),(257,0,2,126,0,2,903.00,1513008000,1513072338,1,1513072338,0,NULL,0),(258,0,2,127,0,2,903.00,1513008000,1513072406,1,1513072406,0,NULL,0),(259,0,2,128,0,2,1806.00,1513008000,1513072878,1,1513072878,0,NULL,0),(260,0,2,129,0,2,1806.00,1513008000,1513073819,1,1513073819,0,NULL,0),(261,0,2,130,0,2,2306.00,1513008000,1513080069,1,1513080069,0,NULL,0),(262,0,2,131,0,2,4106.00,1513008000,1513081124,1,1513081124,0,NULL,0),(263,0,2,132,0,2,6600.00,1513008000,1513081951,1,1513081951,0,NULL,0),(264,0,2,133,0,2,3600.00,1513008000,1513082011,1,1513082011,0,NULL,0),(265,0,2,134,0,2,3600.00,1513008000,1513082035,1,1513082035,0,NULL,0),(266,0,2,135,0,2,1409.00,1513094400,1513129216,1,1513129216,0,NULL,0),(267,0,2,136,0,2,4503.00,1513094400,1513133823,1,1513133823,0,NULL,0),(268,0,2,137,0,2,410.00,1513180800,1513243794,1,1513243794,0,NULL,0),(269,0,2,138,0,2,65595.00,1513180800,1513243886,1,1513243886,0,NULL,0),(270,0,2,139,0,2,1600.00,1513785600,1513819745,1,1513819745,0,NULL,0),(271,0,2,140,0,2,2000.00,1513785600,1513819850,1,1513819850,0,NULL,0),(272,0,2,141,0,2,1600.00,1513785600,1513824352,1,1513824352,0,NULL,0),(273,0,2,142,0,2,1000.00,1513785600,1513824931,1,1513824931,0,NULL,0),(274,0,2,143,0,2,916.00,1513785600,1513825288,1,1513825288,0,NULL,0),(275,0,2,144,0,2,2000.00,1513785600,1513826157,1,1513826157,0,NULL,0),(276,0,2,145,0,2,3600.00,1513785600,1513826325,1,1513826325,0,NULL,0),(277,0,2,146,0,2,10706.00,1513785600,1513837503,1,1513837503,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4 COMMENT='订单收据表主表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_receipt_bill`
--

LOCK TABLES `x360p_order_receipt_bill` WRITE;
/*!40000 ALTER TABLE `x360p_order_receipt_bill` DISABLE KEYS */;
INSERT INTO `x360p_order_receipt_bill` VALUES (1,0,2,1,'RHX20171118102018',3600.00,0.00,3600.00,0.00,1510971618,1,1510971618,0,0,NULL),(2,0,2,2,'GXY20171118102135',3600.00,0.00,3600.00,0.00,1510971695,1,1510971695,0,0,NULL),(3,0,2,3,'ENX20171118102158',3600.00,0.00,3600.00,0.00,1510971718,1,1510971718,0,0,NULL),(4,0,2,4,'JRD20171118102244',3600.00,0.00,3600.00,0.00,1510971764,1,1510971764,0,0,NULL),(5,0,2,8,'RYQ20171118102615',3600.00,0.00,3600.00,0.00,1510971975,1,1510971975,0,0,NULL),(6,0,2,5,'VYA20171118102617',3600.00,0.00,3600.00,0.00,1510971977,1,1510971977,0,0,NULL),(7,0,2,6,'QHK20171118102704',3600.00,0.00,3600.00,0.00,1510972024,1,1510972024,0,0,NULL),(8,0,2,7,'NPG20171118102722',3600.00,0.00,3600.00,0.00,1510972042,1,1510972042,0,0,NULL),(9,0,2,9,'XWS20171118102725',3600.00,0.00,3600.00,0.00,1510972045,1,1510972045,0,0,NULL),(10,0,2,10,'NCM20171118110436',3600.00,0.00,3600.00,0.00,1510974276,1,1510974276,0,0,NULL),(11,0,2,11,'GMI20171118163117',120.00,0.00,120.00,0.00,1510993877,1,1510993877,0,0,NULL),(12,0,2,12,'VJP20171118163659',200.00,0.00,200.00,0.00,1510994219,1,1510994219,0,0,NULL),(13,0,2,13,'YPM20171118164453',200.00,0.00,200.00,0.00,1510994693,1,1510994693,0,0,NULL),(14,0,2,14,'ADZ20171118164512',200.00,0.00,200.00,0.00,1510994712,1,1510994712,0,0,NULL),(15,0,2,15,'CTJ20171118164540',200.00,0.00,200.00,0.00,1510994740,1,1510994740,0,0,NULL),(16,0,2,16,'YGD20171118164556',200.00,0.00,200.00,0.00,1510994756,1,1510994756,0,0,NULL),(17,0,2,17,'AKN20171118164610',200.00,0.00,200.00,0.00,1510994770,1,1510994770,0,0,NULL),(18,0,2,18,'LZH20171118170214',6800.00,0.00,6800.00,0.00,1510995734,1,1510995734,0,0,NULL),(19,0,2,19,'AMR20171118171946',6500.00,0.00,6500.00,0.00,1510996786,1,1510996786,0,0,NULL),(20,0,2,21,'FUY20171118192223',3000.00,0.00,3000.00,0.00,1511004143,1,1511004143,0,0,NULL),(21,0,2,22,'FRH20171118192337',5400.00,0.00,5400.00,0.00,1511004217,1,1511004217,0,0,NULL),(22,0,2,23,'VSC20171120102946',24424.00,0.00,24424.00,0.00,1511144986,1,1511144986,0,0,NULL),(23,0,2,24,'HCE20171120122053',81000.00,0.00,81000.00,0.00,1511151653,1,1511151653,0,0,NULL),(24,0,2,23,'CPW20171120150234',9000.00,0.00,9000.00,0.00,1511161354,1,1511161354,0,0,NULL),(25,0,2,26,'SHO20171121174811',1400.00,0.00,1400.00,0.00,1511257691,1,1511257691,0,0,NULL),(26,0,2,27,'GXW20171121174829',1400.00,0.00,1400.00,0.00,1511257709,1,1511257709,0,0,NULL),(27,0,2,31,'NKM20171122120518',1400.00,0.00,1400.00,0.00,1511323518,1,1511323518,0,0,NULL),(28,0,2,31,'UCQ20171122120656',1400.00,0.00,1400.00,0.00,1511323616,1,1511323616,0,0,NULL),(29,0,2,32,'KDU20171122150001',598500.00,0.00,598500.00,0.00,1511334001,1,1511334001,0,0,NULL),(30,0,2,30,'MJN20171122180713',1200.00,0.00,1200.00,0.00,1511345233,1,1511345233,0,0,NULL),(31,0,2,29,'CLG20171122180851',1400.00,0.00,1400.00,0.00,1511345331,1,1511345331,0,0,NULL),(32,0,2,34,'BKP20171122182107',200.00,0.00,200.00,0.00,1511346067,1,1511346067,0,0,NULL),(33,0,2,35,'VYF20171122204034',1300.00,0.00,1300.00,0.00,1511354434,1,1511354434,0,0,NULL),(34,0,2,36,'SOQ20171122204115',1100.00,0.00,1100.00,0.00,1511354475,1,1511354475,0,0,NULL),(35,0,2,37,'JSP20171129161226',10200.00,0.00,10200.00,0.00,1511943146,1,1511943146,0,0,NULL),(36,0,2,37,'GCT20171129161552',10200.00,0.00,10200.00,0.00,1511943352,1,1511943352,0,0,NULL),(37,0,2,37,'PZX20171129164227',400.00,0.00,400.00,0.00,1511944947,1,1511944947,0,0,NULL),(38,0,2,37,'CBE20171129164319',900.00,0.00,900.00,0.00,1511944999,1,1511944999,0,0,NULL),(39,0,2,37,'IVN20171129171400',16120.00,0.00,16120.00,0.00,1511946840,1,1511946840,0,0,NULL),(40,0,2,37,'WRQ20171129171456',6800.00,0.00,6800.00,0.00,1511946896,1,1511946896,0,0,NULL),(41,0,2,37,'GQP20171129171553',5400.00,0.00,5400.00,0.00,1511946953,1,1511946953,0,0,NULL),(42,0,2,37,'XNT20171129171725',12000.00,0.00,12000.00,0.00,1511947045,1,1511947045,0,0,NULL),(43,0,2,38,'LSQ20171129171827',200.00,0.00,200.00,0.00,1511947107,1,1511947107,0,0,NULL),(44,0,2,39,'IDE20171129172136',2700.00,0.00,2700.00,0.00,1511947296,1,1511947296,0,0,NULL),(45,0,2,40,'ZAK20171130092007',8000.00,0.00,8000.00,500.00,1512004807,1,1512004807,0,0,NULL),(46,0,2,41,'UGT20171130093209',9000.00,0.00,9000.00,0.00,1512005529,1,1512005529,0,0,NULL),(47,0,2,41,'PKQ20171130093229',12200.00,0.00,12200.00,0.00,1512005549,1,1512005549,0,0,NULL),(48,0,2,37,'QUV20171130155716',12000.00,0.00,12000.00,0.00,1512028636,1,1512028636,0,0,NULL),(49,0,2,43,'ISD20171130180556',32600.00,0.00,32600.00,0.00,1512036356,1,1512036356,0,0,NULL),(50,0,2,43,'WAB20171130180846',12702.00,0.00,12702.00,0.00,1512036526,1,1512036526,0,0,NULL),(51,0,2,43,'IEN20171130180959',1601.00,0.00,1601.00,0.00,1512036599,1,1512036599,0,0,NULL),(52,0,2,37,'BKH20171201091020',5792.00,0.00,5792.00,0.00,1512090620,1,1512090620,0,0,NULL),(53,0,2,38,'HWU20171201091213',5403.00,0.00,5403.00,0.00,1512090733,1,1512090733,0,0,NULL),(54,0,2,37,'YRP20171201102315',290.00,290.00,0.00,0.00,1512094995,1,1512094995,0,0,NULL),(55,0,2,37,'NVB20171201112954',5595.00,192.00,5403.00,0.00,1512098994,1,1512098994,0,0,NULL),(56,0,2,4,'SHJ20171201113949',12000.00,0.00,12000.00,0.00,1512099589,1,1512099589,0,0,NULL),(57,0,2,49,'PKB20171201114027',5795.00,0.00,5795.00,0.00,1512099627,1,1512099627,0,0,NULL),(58,0,2,49,'LOE20171201114040',203.00,0.00,203.00,0.00,1512099640,1,1512099640,0,0,NULL),(59,0,2,49,'JEP20171201114051',195.00,0.00,195.00,0.00,1512099651,1,1512099651,0,0,NULL),(60,0,2,49,'VLA20171201145229',6630.00,580.00,6050.00,0.00,1512111149,1,1512111149,0,0,NULL),(61,0,2,49,'GCV20171201145502',2500.00,0.00,2500.00,1130.00,1512111302,1,1512111302,0,0,NULL),(62,0,2,49,'PRX20171201162440',9093.00,0.00,9093.00,0.00,1512116680,1,1512116680,0,0,NULL),(63,0,2,49,'SGQ20171201163428',0.00,0.00,0.00,0.00,1512117268,1,1512117268,0,0,NULL),(64,0,2,49,'JEM20171201170502',0.00,0.00,0.00,0.00,1512119102,1,1512119102,0,0,NULL),(65,0,2,49,'YNB20171201172902',0.00,0.00,0.00,0.00,1512120542,1,1512120542,0,0,NULL),(66,0,2,49,'KFT20171201180405',0.00,0.00,0.00,1193.00,1512122645,1,1512122645,0,0,NULL),(67,0,2,49,'ALP20171201181619',1650.53,0.00,1650.53,3095.47,1512123379,1,1512123379,0,0,NULL),(68,0,4,5,'DTA20171204092535',2900.00,0.00,2900.00,0.00,1512350735,1,1512350735,0,0,NULL),(69,0,2,7,'ZXT20171205171457',5693.00,0.00,5693.00,0.00,1512465297,1,1512465297,0,0,NULL),(70,0,2,52,'FDP20171205182132',5000.00,0.00,5000.00,400.00,1512469292,1,1512469292,0,0,NULL),(71,0,2,52,'NMK20171206114907',200.00,0.00,200.00,600.00,1512532147,1,1512532147,0,0,NULL),(72,0,2,52,'TUG20171206115154',1.00,0.00,1.00,399.00,1512532314,1,1512532314,0,0,NULL),(73,0,2,52,'KZQ20171206120805',1.00,0.00,1.00,397.00,1512533285,1,1512533285,0,0,NULL),(74,0,2,52,'KOF20171206120950',1.00,0.00,1.00,395.00,1512533390,1,1512533390,0,0,NULL),(75,0,2,52,'HJD20171206121033',1.00,0.00,1.00,393.00,1512533433,1,1512533433,0,0,NULL),(76,0,2,52,'EFL20171206121236',5000.00,0.00,5000.00,1690.00,1512533556,1,1512533556,0,0,NULL),(77,0,2,52,'VEU20171206121535',11.00,0.00,11.00,3761.00,1512533735,1,1512533735,0,0,NULL),(78,0,2,52,'PFM20171206122708',1681.00,0.00,1681.00,2069.00,1512534428,1,1512534428,0,0,NULL),(79,0,2,52,'DZF20171206141008',1.00,0.00,1.00,387.00,1512540608,1,1512540608,0,0,NULL),(80,0,2,52,'RNL20171206141122',1.00,0.00,1.00,385.00,1512540682,1,1512540682,0,0,NULL),(81,0,2,52,'CQH20171206142844',1.00,0.00,1.00,383.00,1512541724,1,1512541724,0,0,NULL),(82,0,2,52,'YQK20171206143052',5000.00,0.00,5000.00,490.00,1512541852,1,1512541852,0,0,NULL),(83,0,2,52,'UCB20171206154800',5490.00,0.00,5490.00,0.00,1512546480,1,1512546480,0,0,NULL),(84,0,2,53,'WYB20171206163405',3600.00,0.00,3600.00,0.00,1512549245,1,1512549245,0,0,NULL),(85,0,2,11,'XIS20171206163407',3600.00,0.00,3600.00,0.00,1512549247,1,1512549247,0,0,NULL),(86,0,2,12,'FKO20171206163803',3600.00,0.00,3600.00,0.00,1512549483,1,1512549483,0,0,NULL),(87,0,2,57,'CZF20171206164123',3600.00,0.00,3600.00,0.00,1512549683,1,1512549683,0,0,NULL),(88,0,2,57,'FJS20171206183248',10000.00,0.00,10000.00,2000.00,1512556368,1,1512556368,0,0,NULL),(89,0,2,13,'ZDF20171207094908',90.00,0.00,90.00,4500.00,1512611348,1,1512611348,0,0,NULL),(90,0,2,57,'UDG20171207101417',900.00,0.00,900.00,3100.00,1512612857,1,1512612857,0,0,NULL),(91,0,2,57,'DWI20171207102103',2.00,0.00,2.00,2198.00,1512613263,1,1512613263,0,0,NULL),(92,0,2,57,'XSH20171207102116',1098.00,0.00,1098.00,1098.00,1512613276,1,1512613276,0,0,NULL),(93,0,2,14,'ZON20171207103621',48.00,0.00,48.00,3600.00,1512614181,1,1512614181,0,0,NULL),(94,0,2,57,'BKM20171207110558',3600.00,0.00,3600.00,0.00,1512615958,1,1512615958,0,0,NULL),(95,0,2,57,'ZME20171207110614',3600.00,0.00,3600.00,0.00,1512615974,1,1512615974,0,0,NULL),(96,0,2,15,'CWB20171207112546',600.00,0.00,600.00,6000.00,1512617146,1,1512617146,0,0,NULL),(97,0,2,16,'UVQ20171207113851',400.00,0.00,400.00,5000.00,1512617931,1,1512617931,0,0,NULL),(98,0,2,17,'HCP20171207114133',200.00,0.00,200.00,0.00,1512618093,1,1512618093,0,0,NULL),(99,0,2,18,'HQF20171207114324',1400.00,0.00,1400.00,0.00,1512618204,1,1512618204,0,0,NULL),(100,0,2,19,'ZYW20171207114426',900.00,0.00,900.00,0.00,1512618266,1,1512618266,0,0,NULL),(101,0,2,20,'BUG20171207114825',200.00,0.00,200.00,0.00,1512618505,1,1512618505,0,0,NULL),(102,0,2,43,'DMH20171207150758',1400.00,0.00,1400.00,0.00,1512630478,1,1512630478,0,0,NULL),(103,0,2,43,'DGW20171207150903',200.00,0.00,200.00,0.00,1512630543,1,1512630543,0,0,NULL),(104,0,2,43,'UOJ20171207151249',200.00,0.00,200.00,0.00,1512630769,1,1512630769,0,0,NULL),(105,0,2,56,'XSZ20171208105302',1400.00,0.00,1400.00,1400.00,1512701582,1,1512701582,0,0,NULL),(106,0,2,65,'EVO20171208123005',6600.00,0.00,6600.00,0.00,1512707405,1,1512707405,0,0,NULL),(107,0,2,21,'WAT20171208140330',2024.00,0.00,2024.00,0.00,1512713010,1,1512713010,0,0,NULL),(108,0,2,22,'VBU20171208140620',1012.00,0.00,1012.00,0.00,1512713180,1,1512713180,0,0,NULL),(109,0,2,23,'NGY20171208140955',1012.00,0.00,1012.00,0.00,1512713395,1,1512713395,0,0,NULL),(110,0,2,24,'RJS20171208141319',200.00,0.00,200.00,0.00,1512713599,1,1512713599,0,0,NULL),(111,0,2,70,'RPS20171208141502',506.00,0.00,506.00,0.00,1512713702,1,1512713702,0,0,NULL),(112,0,2,70,'CLE20171208153302',3600.00,0.00,3600.00,0.00,1512718382,1,1512718382,0,0,NULL),(113,0,2,70,'EUY20171208153909',8003.00,0.00,8003.00,0.00,1512718749,1,1512718749,0,0,NULL),(114,0,2,72,'YIZ20171212155243',2.00,0.00,2.00,1804.00,1513065163,1,1513065163,0,0,NULL),(115,0,2,72,'PYO20171212155315',1200.00,0.00,1200.00,1200.00,1513065195,1,1513065195,0,0,NULL),(116,0,2,72,'FRE20171212163820',4106.00,0.00,4106.00,0.00,1513067900,1,1513067900,0,0,NULL),(117,0,2,72,'USY20171212173615',1806.00,0.00,1806.00,0.00,1513071375,1,1513071375,0,0,NULL),(118,0,2,72,'DSV20171212173859',4503.00,0.00,4503.00,0.00,1513071539,1,1513071539,0,0,NULL),(119,0,2,72,'KVH20171212174309',903.00,0.00,903.00,0.00,1513071789,1,1513071789,0,0,NULL),(120,0,2,72,'OFT20171212174404',7200.00,0.00,7200.00,0.00,1513071844,1,1513071844,0,0,NULL),(121,0,2,72,'OQC20171212174548',4106.00,0.00,4106.00,0.00,1513071948,1,1513071948,0,0,NULL),(122,0,2,72,'PRB20171212174634',506.00,0.00,506.00,0.00,1513071994,1,1513071994,0,0,NULL),(123,0,2,71,'QKY20171212174905',903.00,0.00,903.00,0.00,1513072145,1,1513072145,0,0,NULL),(124,0,2,74,'FIK20171212175002',4106.00,0.00,4106.00,0.00,1513072202,1,1513072202,0,0,NULL),(125,0,2,74,'JFN20171212175048',903.00,0.00,903.00,0.00,1513072248,1,1513072248,0,0,NULL),(126,0,2,72,'TDK20171212175218',903.00,0.00,903.00,0.00,1513072338,1,1513072338,0,0,NULL),(127,0,2,72,'ERB20171212175326',903.00,0.00,903.00,0.00,1513072406,1,1513072406,0,0,NULL),(128,0,2,75,'SZY20171212180118',1806.00,0.00,1806.00,0.00,1513072878,1,1513072878,0,0,NULL),(129,0,2,76,'GTU20171212181659',1806.00,0.00,1806.00,0.00,1513073819,1,1513073819,0,0,NULL),(130,0,2,77,'CUK20171212200109',2306.00,0.00,2306.00,0.00,1513080069,1,1513080069,0,0,NULL),(131,0,2,78,'CTS20171212201844',4106.00,0.00,4106.00,0.00,1513081124,1,1513081124,0,0,NULL),(132,0,2,72,'VND20171212203231',0.00,0.00,0.00,6600.00,1513081951,1,1513081951,0,0,NULL),(133,0,2,72,'FET20171212203331',3600.00,0.00,3600.00,0.00,1513082011,1,1513082011,0,0,NULL),(134,0,2,78,'YLA20171212203355',3600.00,0.00,3600.00,0.00,1513082035,1,1513082035,0,0,NULL),(135,0,2,78,'FTE20171213094016',1409.00,0.00,1409.00,0.00,1513129216,1,1513129216,0,0,NULL),(136,0,2,79,'TQD20171213105703',4503.00,0.00,4503.00,0.00,1513133823,1,1513133823,0,0,NULL),(137,0,2,79,'ENI20171214172954',410.00,0.00,410.00,0.00,1513243794,1,1513243794,0,0,NULL),(138,0,2,79,'KPJ20171214173126',65595.00,0.00,65595.00,0.00,1513243886,1,1513243886,0,0,NULL),(139,0,2,82,'MLY20171221092905',1600.00,0.00,1600.00,5600.00,1513819745,1,1513819745,0,0,NULL),(140,0,2,82,'UZS20171221093050',2000.00,0.00,2000.00,3600.00,1513819850,1,1513819850,0,0,NULL),(141,0,2,83,'CTS20171221104552',1600.00,0.00,1600.00,2000.00,1513824352,1,1513824352,0,0,NULL),(142,0,2,83,'FIO20171221105531',1000.00,0.00,1000.00,3000.00,1513824931,1,1513824931,0,0,NULL),(143,0,2,83,'UQG20171221110128',916.00,0.00,916.00,0.00,1513825288,1,1513825288,0,0,NULL),(144,0,2,83,'AHO20171221111557',2000.00,0.00,2000.00,3476.00,1513826157,1,1513826157,0,0,NULL),(145,0,2,83,'EVD20171221111845',3600.00,0.00,3600.00,0.00,1513826325,1,1513826325,0,0,NULL),(146,0,2,84,'UKW20171221142503',10706.00,0.00,10706.00,10706.00,1513837503,1,1513837503,0,0,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=406 DEFAULT CHARSET=utf8mb4 COMMENT='订单收据条目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_receipt_bill_item`
--

LOCK TABLES `x360p_order_receipt_bill_item` WRITE;
/*!40000 ALTER TABLE `x360p_order_receipt_bill_item` DISABLE KEYS */;
INSERT INTO `x360p_order_receipt_bill_item` VALUES (131,0,2,1,1,1,1,3600.00,1510971618,1,1510971618,0,0,NULL),(132,0,2,2,2,2,2,3600.00,1510971695,1,1510971695,0,0,NULL),(133,0,2,3,3,3,3,3600.00,1510971718,1,1510971718,0,0,NULL),(134,0,2,4,4,4,4,3600.00,1510971764,1,1510971764,0,0,NULL),(135,0,2,8,5,5,5,3600.00,1510971975,1,1510971975,0,0,NULL),(136,0,2,5,6,6,6,3600.00,1510971977,1,1510971977,0,0,NULL),(137,0,2,6,7,7,7,3600.00,1510972024,1,1510972024,0,0,NULL),(138,0,2,7,8,8,8,3600.00,1510972042,1,1510972042,0,0,NULL),(139,0,2,9,9,9,9,3600.00,1510972045,1,1510972045,0,0,NULL),(140,0,2,10,10,10,10,3600.00,1510974276,1,1510974276,0,0,NULL),(141,0,2,11,11,11,11,120.00,1510993877,1,1510993877,0,0,NULL),(142,0,2,12,12,12,12,200.00,1510994219,1,1510994219,0,0,NULL),(143,0,2,13,13,13,13,200.00,1510994693,1,1510994693,0,0,NULL),(144,0,2,14,14,14,14,200.00,1510994712,1,1510994712,0,0,NULL),(145,0,2,15,15,15,15,200.00,1510994740,1,1510994740,0,0,NULL),(146,0,2,16,16,16,16,200.00,1510994756,1,1510994756,0,0,NULL),(147,0,2,17,17,17,17,200.00,1510994770,1,1510994770,0,0,NULL),(148,0,2,18,18,20,20,6600.00,1510995734,1,1510995734,0,0,NULL),(149,0,2,18,18,20,21,200.00,1510995734,1,1510995734,0,0,NULL),(150,0,2,19,19,21,22,6000.00,1510996786,1,1510996786,0,0,NULL),(151,0,2,21,20,24,25,3000.00,1511004143,1,1511004143,0,0,NULL),(152,0,2,22,21,25,26,5400.00,1511004217,1,1511004217,0,0,NULL),(153,0,2,23,22,26,27,16738.66,1511144986,1,1511144986,0,0,NULL),(154,0,2,23,22,26,28,7685.34,1511144986,1,1511144986,0,0,NULL),(155,0,2,24,23,33,35,81000.00,1511151653,1,1511151653,0,0,NULL),(156,0,2,23,24,34,36,9000.00,1511161354,1,1511161354,0,0,NULL),(157,0,2,26,25,35,37,1400.00,1511257691,1,1511257691,0,0,NULL),(158,0,2,27,26,36,38,1400.00,1511257709,1,1511257709,0,0,NULL),(159,0,2,31,27,37,39,1400.00,1511323518,1,1511323518,0,0,NULL),(160,0,2,31,28,38,40,1400.00,1511323616,1,1511323616,0,0,NULL),(161,0,2,32,29,39,41,598500.00,1511334001,1,1511334001,0,0,NULL),(162,0,2,30,30,41,43,1200.00,1511345233,1,1511345233,0,0,NULL),(163,0,2,29,31,42,44,1400.00,1511345331,1,1511345331,0,0,NULL),(164,0,2,34,32,43,45,200.00,1511346067,1,1511346067,0,0,NULL),(165,0,2,35,33,44,46,1300.00,1511354434,1,1511354434,0,0,NULL),(166,0,2,36,34,45,47,1100.00,1511354475,1,1511354475,0,0,NULL),(167,0,2,37,35,46,48,6600.00,1511943146,1,1511943146,0,0,NULL),(168,0,2,37,35,46,49,3600.00,1511943146,1,1511943146,0,0,NULL),(169,0,2,37,36,47,50,3600.00,1511943352,1,1511943352,0,0,NULL),(170,0,2,37,36,47,51,6600.00,1511943352,1,1511943352,0,0,NULL),(171,0,2,37,37,48,52,200.00,1511944947,1,1511944947,0,0,NULL),(172,0,2,37,37,48,53,200.00,1511944947,1,1511944947,0,0,NULL),(173,0,2,37,38,49,54,900.00,1511944999,1,1511944999,0,0,NULL),(174,0,2,37,39,50,55,3720.00,1511946840,1,1511946840,0,0,NULL),(175,0,2,37,39,50,56,6820.00,1511946840,1,1511946840,0,0,NULL),(176,0,2,37,39,50,57,5580.00,1511946840,1,1511946840,0,0,NULL),(177,0,2,37,40,51,58,6600.00,1511946896,1,1511946896,0,0,NULL),(178,0,2,37,40,51,59,200.00,1511946896,1,1511946896,0,0,NULL),(179,0,2,37,41,52,60,5400.00,1511946953,1,1511946953,0,0,NULL),(180,0,2,37,42,53,61,5400.00,1511947045,1,1511947045,0,0,NULL),(181,0,2,37,42,53,62,6600.00,1511947045,1,1511947045,0,0,NULL),(182,0,2,38,43,54,63,200.00,1511947107,1,1511947107,0,0,NULL),(183,0,2,39,44,55,64,2700.00,1511947296,1,1511947296,0,0,NULL),(184,0,2,40,45,56,65,5023.26,1512004807,1,1512004807,0,0,NULL),(185,0,2,40,45,56,66,186.04,1512004807,1,1512004807,0,0,NULL),(186,0,2,40,45,56,67,2790.70,1512004807,1,1512004807,0,0,NULL),(187,0,2,41,46,58,71,5400.00,1512005529,1,1512005529,0,0,NULL),(188,0,2,41,46,58,72,3600.00,1512005529,1,1512005529,0,0,NULL),(189,0,2,41,47,57,68,0.00,1512005549,1,1512005549,0,0,NULL),(190,0,2,41,47,57,69,0.00,1512005549,1,1512005549,0,0,NULL),(191,0,2,41,47,57,70,0.00,1512005549,1,1512005549,0,0,NULL),(192,0,2,37,48,59,73,5400.00,1512028636,1,1512028636,0,0,NULL),(193,0,2,37,48,59,74,6600.00,1512028636,1,1512028636,0,0,NULL),(194,0,2,43,49,61,77,6600.00,1512036356,1,1512036356,0,0,NULL),(195,0,2,43,49,61,78,5400.00,1512036356,1,1512036356,0,0,NULL),(196,0,2,43,49,61,79,600.00,1512036356,1,1512036356,0,0,NULL),(197,0,2,43,49,61,80,20000.00,1512036356,1,1512036356,0,0,NULL),(198,0,2,43,50,62,81,6600.00,1512036526,1,1512036526,0,0,NULL),(199,0,2,43,50,62,82,5400.00,1512036526,1,1512036526,0,0,NULL),(200,0,2,43,50,62,83,600.00,1512036526,1,1512036526,0,0,NULL),(201,0,2,43,50,62,84,102.00,1512036526,1,1512036526,0,0,NULL),(202,0,2,43,51,63,85,1400.00,1512036599,1,1512036599,0,0,NULL),(203,0,2,43,51,63,86,200.00,1512036599,1,1512036599,0,0,NULL),(204,0,2,43,51,63,87,1.00,1512036599,1,1512036599,0,0,NULL),(205,0,2,37,52,67,88,5400.00,1512090620,1,1512090620,0,0,NULL),(206,0,2,37,52,67,89,200.00,1512090620,1,1512090620,0,0,NULL),(207,0,2,37,52,67,90,90.00,1512090620,1,1512090620,0,0,NULL),(208,0,2,37,52,67,91,102.00,1512090620,1,1512090620,0,0,NULL),(209,0,2,38,53,68,92,5400.00,1512090733,1,1512090733,0,0,NULL),(210,0,2,38,53,68,93,3.00,1512090733,1,1512090733,0,0,NULL),(211,0,2,37,54,69,94,200.00,1512094995,1,1512094995,0,0,NULL),(212,0,2,37,54,69,95,90.00,1512094995,1,1512094995,0,0,NULL),(213,0,2,37,55,70,96,5400.00,1512098994,1,1512098994,0,0,NULL),(214,0,2,37,55,70,97,3.00,1512098994,1,1512098994,0,0,NULL),(215,0,2,37,55,70,98,90.00,1512098994,1,1512098994,0,0,NULL),(216,0,2,37,55,70,99,102.00,1512098994,1,1512098994,0,0,NULL),(217,0,2,4,56,71,100,5400.00,1512099589,1,1512099589,0,0,NULL),(218,0,2,4,56,71,101,6600.00,1512099589,1,1512099589,0,0,NULL),(219,0,2,49,57,72,102,5400.00,1512099627,1,1512099627,0,0,NULL),(220,0,2,49,57,72,103,200.00,1512099627,1,1512099627,0,0,NULL),(221,0,2,49,57,72,104,3.00,1512099627,1,1512099627,0,0,NULL),(222,0,2,49,57,72,105,90.00,1512099627,1,1512099627,0,0,NULL),(223,0,2,49,57,72,106,102.00,1512099627,1,1512099627,0,0,NULL),(224,0,2,49,58,73,107,200.00,1512099640,1,1512099640,0,0,NULL),(225,0,2,49,58,73,108,3.00,1512099640,1,1512099640,0,0,NULL),(226,0,2,49,59,74,109,3.00,1512099651,1,1512099651,0,0,NULL),(227,0,2,49,59,74,110,90.00,1512099651,1,1512099651,0,0,NULL),(228,0,2,49,59,74,111,102.00,1512099651,1,1512099651,0,0,NULL),(229,0,2,49,60,75,112,6600.00,1512111149,1,1512111149,0,0,NULL),(230,0,2,49,60,75,113,30.00,1512111149,1,1512111149,0,0,NULL),(231,0,2,49,61,76,114,2479.34,1512111302,1,1512111302,0,0,NULL),(232,0,2,49,61,76,115,20.66,1512111302,1,1512111302,0,0,NULL),(233,0,2,49,62,79,121,5400.00,1512116680,1,1512116680,0,0,NULL),(234,0,2,49,62,79,122,3600.00,1512116680,1,1512116680,0,0,NULL),(235,0,2,49,62,79,123,90.00,1512116680,1,1512116680,0,0,NULL),(236,0,2,49,62,79,124,3.00,1512116680,1,1512116680,0,0,NULL),(237,0,2,49,63,81,129,0.00,1512117268,1,1512117268,0,0,NULL),(238,0,2,49,63,81,130,0.00,1512117268,1,1512117268,0,0,NULL),(239,0,2,49,63,81,131,0.00,1512117268,1,1512117268,0,0,NULL),(240,0,2,49,64,83,136,0.00,1512119102,1,1512119102,0,0,NULL),(241,0,2,49,65,84,137,0.00,1512120542,1,1512120542,0,0,NULL),(242,0,2,49,65,84,138,0.00,1512120542,1,1512120542,0,0,NULL),(243,0,2,49,65,84,139,0.00,1512120542,1,1512120542,0,0,NULL),(244,0,2,49,66,80,125,0.00,1512122645,1,1512122645,0,0,NULL),(245,0,2,49,66,80,126,0.00,1512122645,1,1512122645,0,0,NULL),(246,0,2,49,66,80,127,0.00,1512122645,1,1512122645,0,0,NULL),(247,0,2,49,66,80,128,0.00,1512122645,1,1512122645,0,0,NULL),(248,0,2,49,67,80,125,0.00,1512123379,1,1512123379,0,0,NULL),(249,0,2,49,67,80,126,507.42,1512123379,1,1512123379,0,0,NULL),(250,0,2,49,67,80,127,12.69,1512123379,1,1512123379,0,0,NULL),(251,0,2,49,67,80,128,0.42,1512123379,1,1512123379,0,0,NULL),(252,0,2,49,67,76,114,1120.66,1512123379,1,1512123379,0,0,NULL),(253,0,2,49,67,76,115,9.34,1512123379,1,1512123379,0,0,NULL),(254,0,4,5,68,86,141,2700.00,1512350735,1,1512350735,0,0,NULL),(255,0,4,5,68,86,142,200.00,1512350735,1,1512350735,0,0,NULL),(256,0,2,7,69,87,143,5400.00,1512465297,1,1512465297,0,0,NULL),(257,0,2,7,69,87,144,200.00,1512465297,1,1512465297,0,0,NULL),(258,0,2,7,69,87,145,3.00,1512465297,1,1512465297,0,0,NULL),(259,0,2,7,69,87,146,90.00,1512465297,1,1512465297,0,0,NULL),(260,0,2,52,70,88,147,5000.00,1512469292,1,1512469292,0,0,NULL),(261,0,2,52,71,88,147,200.00,1512532147,1,1512532147,0,0,NULL),(262,0,2,52,72,88,147,1.00,1512532314,1,1512532314,0,0,NULL),(263,0,2,52,73,88,147,1.00,1512533285,1,1512533285,0,0,NULL),(264,0,2,52,74,88,147,1.00,1512533390,1,1512533390,0,0,NULL),(265,0,2,52,75,88,147,1.00,1512533433,1,1512533433,0,0,NULL),(266,0,2,52,76,97,158,4932.74,1512533556,1,1512533556,0,0,NULL),(267,0,2,52,76,97,159,67.26,1512533556,1,1512533556,0,0,NULL),(268,0,2,52,77,97,158,7.26,1512533735,1,1512533735,0,0,NULL),(269,0,2,52,77,97,159,2.74,1512533735,1,1512533735,0,0,NULL),(270,0,2,52,77,88,147,1.00,1512533735,1,1512533735,0,0,NULL),(271,0,2,52,78,97,158,1660.00,1512534428,1,1512534428,0,0,NULL),(272,0,2,52,78,97,159,20.00,1512534428,1,1512534428,0,0,NULL),(273,0,2,52,78,88,147,1.00,1512534428,1,1512534428,0,0,NULL),(274,0,2,52,79,88,147,1.00,1512540608,1,1512540608,0,0,NULL),(275,0,2,52,80,88,147,1.00,1512540682,1,1512540682,0,0,NULL),(276,0,2,52,81,88,147,1.00,1512541724,1,1512541724,0,0,NULL),(277,0,2,52,82,98,160,4918.03,1512541852,1,1512541852,0,0,NULL),(278,0,2,52,82,98,161,81.97,1512541852,1,1512541852,0,0,NULL),(279,0,2,52,83,99,162,5400.00,1512546480,1,1512546480,0,0,NULL),(280,0,2,52,83,99,163,90.00,1512546480,1,1512546480,0,0,NULL),(281,0,2,53,84,100,164,3600.00,1512549245,1,1512549245,0,0,NULL),(282,0,2,11,85,101,165,3600.00,1512549247,1,1512549247,0,0,NULL),(283,0,2,12,86,102,166,3600.00,1512549483,1,1512549483,0,0,NULL),(284,0,2,57,87,103,167,3600.00,1512549684,1,1512549684,0,0,NULL),(285,0,2,57,88,108,174,5500.00,1512556368,1,1512556368,0,0,NULL),(286,0,2,57,88,108,175,4500.00,1512556368,1,1512556368,0,0,NULL),(287,0,2,13,89,111,180,70.59,1512611348,1,1512611348,0,0,NULL),(288,0,2,13,89,111,181,19.41,1512611348,1,1512611348,0,0,NULL),(289,0,2,57,90,108,174,100.00,1512612857,1,1512612857,0,0,NULL),(290,0,2,57,90,108,175,800.00,1512612857,1,1512612857,0,0,NULL),(291,0,2,57,91,108,174,1.00,1512613263,1,1512613263,0,0,NULL),(292,0,2,57,91,108,175,1.00,1512613263,1,1512613263,0,0,NULL),(293,0,2,57,92,108,174,999.00,1512613276,1,1512613276,0,0,NULL),(294,0,2,57,92,108,175,99.00,1512613276,1,1512613276,0,0,NULL),(295,0,2,14,93,112,182,47.37,1512614181,1,1512614181,0,0,NULL),(296,0,2,14,93,112,183,0.63,1512614181,1,1512614181,0,0,NULL),(297,0,2,57,94,114,185,3600.00,1512615958,1,1512615958,0,0,NULL),(298,0,2,57,95,115,186,3600.00,1512615974,1,1512615974,0,0,NULL),(299,0,2,15,96,116,187,600.00,1512617146,1,1512617146,0,0,NULL),(300,0,2,16,97,117,188,400.00,1512617931,1,1512617931,0,0,NULL),(301,0,2,17,98,118,189,200.00,1512618093,1,1512618093,0,0,NULL),(302,0,2,18,99,119,190,1400.00,1512618205,1,1512618205,0,0,NULL),(303,0,2,19,100,120,191,900.00,1512618266,1,1512618266,0,0,NULL),(304,0,2,20,101,121,192,200.00,1512618505,1,1512618505,0,0,NULL),(305,0,2,43,102,122,193,1400.00,1512630478,1,1512630478,0,0,NULL),(306,0,2,43,103,123,194,200.00,1512630543,1,1512630543,0,0,NULL),(307,0,2,43,104,124,195,200.00,1512630769,1,1512630769,0,0,NULL),(308,0,2,56,105,125,196,1400.00,1512701582,1,1512701582,0,0,NULL),(309,0,2,65,106,128,200,6600.00,1512707405,1,1512707405,0,0,NULL),(310,0,2,21,107,129,201,400.00,1512713010,1,1512713010,0,0,NULL),(311,0,2,21,107,129,202,1224.00,1512713010,1,1512713010,0,0,NULL),(312,0,2,21,107,129,203,400.00,1512713010,1,1512713010,0,0,NULL),(313,0,2,22,108,130,204,200.00,1512713180,1,1512713180,0,0,NULL),(314,0,2,22,108,130,205,612.00,1512713180,1,1512713180,0,0,NULL),(315,0,2,22,108,130,206,200.00,1512713180,1,1512713180,0,0,NULL),(316,0,2,23,109,131,207,200.00,1512713395,1,1512713395,0,0,NULL),(317,0,2,23,109,131,208,612.00,1512713395,1,1512713395,0,0,NULL),(318,0,2,23,109,131,209,200.00,1512713395,1,1512713395,0,0,NULL),(319,0,2,24,110,132,210,200.00,1512713599,1,1512713599,0,0,NULL),(320,0,2,70,111,133,211,200.00,1512713702,1,1512713702,0,0,NULL),(321,0,2,70,111,133,212,306.00,1512713702,1,1512713702,0,0,NULL),(322,0,2,70,112,134,213,3600.00,1512718382,1,1512718382,0,0,NULL),(323,0,2,70,113,135,214,1400.00,1512718749,1,1512718749,0,0,NULL),(324,0,2,70,113,135,215,3.00,1512718749,1,1512718749,0,0,NULL),(325,0,2,70,113,135,216,6600.00,1512718749,1,1512718749,0,0,NULL),(326,0,2,72,114,139,221,1.00,1513065163,1,1513065163,0,0,NULL),(327,0,2,72,114,139,222,1.00,1513065163,1,1513065163,0,0,NULL),(328,0,2,72,115,138,220,1200.00,1513065195,1,1513065195,0,0,NULL),(329,0,2,72,116,140,223,3600.00,1513067900,1,1513067900,0,0,NULL),(330,0,2,72,116,140,224,200.00,1513067900,1,1513067900,0,0,NULL),(331,0,2,72,116,140,225,306.00,1513067900,1,1513067900,0,0,NULL),(332,0,2,72,117,142,227,900.00,1513071375,1,1513071375,0,0,NULL),(333,0,2,72,117,142,228,6.00,1513071375,1,1513071375,0,0,NULL),(334,0,2,72,117,142,229,900.00,1513071375,1,1513071375,0,0,NULL),(335,0,2,72,118,143,230,900.00,1513071539,1,1513071539,0,0,NULL),(336,0,2,72,118,143,231,3.00,1513071539,1,1513071539,0,0,NULL),(337,0,2,72,118,143,232,3600.00,1513071539,1,1513071539,0,0,NULL),(338,0,2,72,119,144,233,900.00,1513071789,1,1513071789,0,0,NULL),(339,0,2,72,119,144,234,3.00,1513071789,1,1513071789,0,0,NULL),(340,0,2,72,120,145,235,3600.00,1513071844,1,1513071844,0,0,NULL),(341,0,2,72,120,145,236,3600.00,1513071844,1,1513071844,0,0,NULL),(342,0,2,72,121,146,237,200.00,1513071948,1,1513071948,0,0,NULL),(343,0,2,72,121,146,238,306.00,1513071948,1,1513071948,0,0,NULL),(344,0,2,72,121,146,239,3600.00,1513071948,1,1513071948,0,0,NULL),(345,0,2,72,122,147,240,200.00,1513071994,1,1513071994,0,0,NULL),(346,0,2,72,122,147,241,306.00,1513071994,1,1513071994,0,0,NULL),(347,0,2,71,123,148,242,900.00,1513072145,1,1513072145,0,0,NULL),(348,0,2,71,123,148,243,3.00,1513072145,1,1513072145,0,0,NULL),(349,0,2,74,124,149,244,3600.00,1513072202,1,1513072202,0,0,NULL),(350,0,2,74,124,149,245,200.00,1513072202,1,1513072202,0,0,NULL),(351,0,2,74,124,149,246,306.00,1513072202,1,1513072202,0,0,NULL),(352,0,2,74,125,150,247,900.00,1513072248,1,1513072248,0,0,NULL),(353,0,2,74,125,150,248,3.00,1513072248,1,1513072248,0,0,NULL),(354,0,2,72,126,151,249,900.00,1513072338,1,1513072338,0,0,NULL),(355,0,2,72,126,151,250,3.00,1513072338,1,1513072338,0,0,NULL),(356,0,2,72,127,152,251,900.00,1513072406,1,1513072406,0,0,NULL),(357,0,2,72,127,152,252,3.00,1513072406,1,1513072406,0,0,NULL),(358,0,2,75,128,153,253,900.00,1513072878,1,1513072878,0,0,NULL),(359,0,2,75,128,153,254,6.00,1513072878,1,1513072878,0,0,NULL),(360,0,2,75,128,153,255,900.00,1513072878,1,1513072878,0,0,NULL),(361,0,2,76,129,154,256,900.00,1513073819,1,1513073819,0,0,NULL),(362,0,2,76,129,154,257,6.00,1513073819,1,1513073819,0,0,NULL),(363,0,2,76,129,154,258,900.00,1513073819,1,1513073819,0,0,NULL),(364,0,2,77,130,155,259,900.00,1513080069,1,1513080069,0,0,NULL),(365,0,2,77,130,155,260,6.00,1513080069,1,1513080069,0,0,NULL),(366,0,2,77,130,155,261,1400.00,1513080069,1,1513080069,0,0,NULL),(367,0,2,78,131,156,262,3600.00,1513081124,1,1513081124,0,0,NULL),(368,0,2,78,131,156,263,200.00,1513081124,1,1513081124,0,0,NULL),(369,0,2,78,131,156,264,306.00,1513081124,1,1513081124,0,0,NULL),(370,0,2,72,132,137,219,0.00,1513081951,1,1513081951,0,0,NULL),(371,0,2,72,133,157,265,3600.00,1513082011,1,1513082011,0,0,NULL),(372,0,2,78,134,158,266,3600.00,1513082035,1,1513082035,0,0,NULL),(373,0,2,78,135,161,267,200.00,1513129216,1,1513129216,0,0,NULL),(374,0,2,78,135,161,268,306.00,1513129216,1,1513129216,0,0,NULL),(375,0,2,78,135,161,269,900.00,1513129216,1,1513129216,0,0,NULL),(376,0,2,78,135,161,270,3.00,1513129216,1,1513129216,0,0,NULL),(377,0,2,79,136,168,277,900.00,1513133823,1,1513133823,0,0,NULL),(378,0,2,79,136,168,278,3.00,1513133823,1,1513133823,0,0,NULL),(379,0,2,79,136,168,279,3600.00,1513133823,1,1513133823,0,0,NULL),(380,0,2,79,137,169,280,200.00,1513243794,1,1513243794,0,0,NULL),(381,0,2,79,137,169,281,204.00,1513243794,1,1513243794,0,0,NULL),(382,0,2,79,137,169,282,6.00,1513243794,1,1513243794,0,0,NULL),(383,0,2,79,138,170,283,5400.00,1513243886,1,1513243886,0,0,NULL),(384,0,2,79,138,170,284,90.00,1513243886,1,1513243886,0,0,NULL),(385,0,2,79,138,170,285,102.00,1513243886,1,1513243886,0,0,NULL),(386,0,2,79,138,170,286,3.00,1513243886,1,1513243886,0,0,NULL),(387,0,2,79,138,170,287,60000.00,1513243886,1,1513243886,0,0,NULL),(388,0,2,82,139,171,288,1600.00,1513819745,1,1513819745,0,0,NULL),(389,0,2,82,140,171,288,2000.00,1513819850,1,1513819850,0,0,NULL),(390,0,2,83,141,173,291,1600.00,1513824352,1,1513824352,0,0,NULL),(391,0,2,83,142,173,291,1000.00,1513824931,1,1513824931,0,0,NULL),(392,0,2,83,143,174,292,200.00,1513825288,1,1513825288,0,0,NULL),(393,0,2,83,143,174,293,510.00,1513825288,1,1513825288,0,0,NULL),(394,0,2,83,143,174,294,6.00,1513825288,1,1513825288,0,0,NULL),(395,0,2,83,143,174,295,200.00,1513825288,1,1513825288,0,0,NULL),(396,0,2,83,144,175,296,1095.70,1513826157,1,1513826157,0,0,NULL),(397,0,2,83,144,175,297,98.61,1513826157,1,1513826157,0,0,NULL),(398,0,2,83,144,175,298,657.41,1513826157,1,1513826157,0,0,NULL),(399,0,2,83,144,175,299,2.19,1513826157,1,1513826157,0,0,NULL),(400,0,2,83,144,175,300,146.09,1513826157,1,1513826157,0,0,NULL),(401,0,2,83,145,176,301,3600.00,1513826325,1,1513826325,0,0,NULL),(402,0,2,84,146,179,304,200.00,1513837503,1,1513837503,0,0,NULL),(403,0,2,84,146,179,305,306.00,1513837503,1,1513837503,0,0,NULL),(404,0,2,84,146,178,303,6600.00,1513837503,1,1513837503,0,0,NULL),(405,0,2,84,146,177,302,3600.00,1513837503,1,1513837503,0,0,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COMMENT='订单结转记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_refund`
--

LOCK TABLES `x360p_order_refund` WRITE;
/*!40000 ALTER TABLE `x360p_order_refund` DISABLE KEYS */;
INSERT INTO `x360p_order_refund` VALUES (1,0,2,0,23,'FDW20171120151353',900.00,16739.10,0.00,17639.10,'',1511162033,1,1511162033,0,NULL,0),(2,0,2,0,21,'TIQ20171120154754',3000.00,0.00,0.00,3000.00,'',1511164074,1,1511164074,0,NULL,0),(3,0,2,0,41,'KHR20171130093449',2700.00,120.00,0.00,2820.00,'',1512005689,1,1512005689,0,NULL,0),(4,0,2,0,49,'OKX20171201143547',180.00,0.00,0.00,180.00,'',1512110147,1,1512110147,0,NULL,0),(5,0,2,0,36,'CWI20171201143604',157.14,0.00,0.00,157.14,'',1512110164,1,1512110164,0,NULL,0),(6,0,2,0,49,'VJH20171201143632',180.00,0.00,0.00,180.00,'',1512110192,1,1512110192,0,NULL,0),(7,0,2,0,43,'SOW20171201144044',200.00,0.00,0.00,200.00,'',1512110444,1,1512110444,0,NULL,0),(8,0,2,0,52,'KQS20171206155356',540.00,0.00,0.00,540.00,'',1512546836,1,1512546836,0,NULL,0),(9,0,2,0,53,'MQD20171206163439',120.00,0.00,0.00,120.00,'',1512549279,1,1512549279,0,NULL,0),(10,0,2,0,53,'URO20171206163759',120.00,0.00,0.00,120.00,'',1512549479,1,1512549479,0,NULL,0),(11,0,2,0,57,'ONJ20171206164211',120.00,0.00,0.00,120.00,'',1512549731,1,1512549731,0,NULL,0),(12,0,2,0,57,'ZJS20171206164357',240.00,0.00,0.00,240.00,'',1512549837,1,1512549837,0,NULL,0),(13,0,2,0,57,'FMC20171206164751',120.00,0.00,0.00,120.00,'',1512550071,1,1512550071,0,NULL,0),(14,0,2,0,52,'PSX20171206164916',180.00,360.00,0.00,540.00,'',1512550156,1,1512550156,0,NULL,0),(15,0,2,0,12,'XED20171206182616',120.00,0.00,0.00,120.00,'',1512555976,1,1512555976,0,NULL,0),(16,0,2,0,12,'TRC20171206182729',120.00,0.00,0.00,120.00,'',1512556049,1,1512556049,0,NULL,0),(17,0,2,0,12,'YBO20171206182900',120.00,0.00,0.00,120.00,'',1512556140,1,1512556140,0,NULL,0),(18,0,2,0,57,'OGM20171206183108',120.00,0.00,0.00,120.00,'',1512556268,1,1512556268,0,NULL,0),(19,0,2,0,52,'RFI20171207101553',4320.00,0.00,0.00,4320.00,'',1512612953,1,1512612953,0,NULL,0),(20,0,2,0,52,'AXJ20171207103004',90.00,0.00,0.00,90.00,'',1512613804,1,1512613804,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COMMENT='订单付款记录ID';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_refund_history`
--

LOCK TABLES `x360p_order_refund_history` WRITE;
/*!40000 ALTER TABLE `x360p_order_refund_history` DISABLE KEYS */;
INSERT INTO `x360p_order_refund_history` VALUES (1,0,2,1,0,2,17639.10,0,1511162033,1,1511162033,0,NULL,0),(2,0,2,2,0,2,3000.00,0,1511164074,1,1511164074,0,NULL,0),(3,0,2,3,0,2,2820.00,0,1512005689,1,1512005689,0,NULL,0),(4,0,2,4,0,2,180.00,0,1512110147,1,1512110147,0,NULL,0),(5,0,2,5,0,2,157.14,0,1512110165,1,1512110165,0,NULL,0),(6,0,2,6,0,2,180.00,0,1512110192,1,1512110192,0,NULL,0),(7,0,2,7,0,2,200.00,0,1512110444,1,1512110444,0,NULL,0),(8,0,2,8,0,2,540.00,0,1512546836,1,1512546836,0,NULL,0),(9,0,2,9,0,2,120.00,0,1512549280,1,1512549280,0,NULL,0),(10,0,2,10,0,2,120.00,0,1512549479,1,1512549479,0,NULL,0),(11,0,2,11,0,2,120.00,0,1512549731,1,1512549731,0,NULL,0),(12,0,2,12,0,2,240.00,0,1512549837,1,1512549837,0,NULL,0),(13,0,2,13,0,2,120.00,0,1512550071,1,1512550071,0,NULL,0),(14,0,2,14,0,2,540.00,0,1512550156,1,1512550156,0,NULL,0),(15,0,2,15,0,2,120.00,0,1512555977,1,1512555977,0,NULL,0),(16,0,2,16,0,2,120.00,0,1512556049,1,1512556049,0,NULL,0),(17,0,2,17,0,2,120.00,0,1512556140,1,1512556140,0,NULL,0),(18,0,2,18,0,2,120.00,0,1512556268,1,1512556268,0,NULL,0),(19,0,2,19,0,2,4320.00,0,1512612953,1,1512612953,0,NULL,0),(20,0,2,20,0,2,90.00,0,1512613804,1,1512613804,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COMMENT='订单退费记录项目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_refund_item`
--

LOCK TABLES `x360p_order_refund_item` WRITE;
/*!40000 ALTER TABLE `x360p_order_refund_item` DISABLE KEYS */;
INSERT INTO `x360p_order_refund_item` VALUES (1,0,1,36,1.00,900.00,900.00,1511162033,1,1511162033,0,NULL,0),(2,0,2,25,30.00,100.00,3000.00,1511164074,1,1511164074,0,NULL,0),(3,0,3,71,15.00,180.00,2700.00,1512005689,1,1512005689,0,NULL,0),(4,0,4,102,1.00,180.00,180.00,1512110147,1,1512110147,0,NULL,0),(5,0,5,47,1.00,157.14,157.14,1512110164,1,1512110164,0,NULL,0),(6,0,6,102,1.00,180.00,180.00,1512110192,1,1512110192,0,NULL,0),(7,0,7,85,1.00,200.00,200.00,1512110444,1,1512110444,0,NULL,0),(8,0,8,162,3.00,180.00,540.00,1512546836,1,1512546836,0,NULL,0),(9,0,9,164,1.00,120.00,120.00,1512549279,1,1512549279,0,NULL,0),(10,0,10,164,1.00,120.00,120.00,1512549479,1,1512549479,0,NULL,0),(11,0,11,167,1.00,120.00,120.00,1512549731,1,1512549731,0,NULL,0),(12,0,12,167,2.00,120.00,240.00,1512549837,1,1512549837,0,NULL,0),(13,0,13,167,1.00,120.00,120.00,1512550071,1,1512550071,0,NULL,0),(14,0,14,162,1.00,180.00,180.00,1512550156,1,1512550156,0,NULL,0),(15,0,15,166,1.00,120.00,120.00,1512555976,1,1512555976,0,NULL,0),(16,0,16,166,1.00,120.00,120.00,1512556049,1,1512556049,0,NULL,0),(17,0,17,166,1.00,120.00,120.00,1512556140,1,1512556140,0,NULL,0),(18,0,18,167,1.00,120.00,120.00,1512556268,1,1512556268,0,NULL,0),(19,0,19,162,24.00,180.00,4320.00,1512612953,1,1512612953,0,NULL,0),(20,0,20,163,1.00,90.00,90.00,1512613804,1,1512613804,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='订单结转记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_transfer`
--

LOCK TABLES `x360p_order_transfer` WRITE;
/*!40000 ALTER TABLE `x360p_order_transfer` DISABLE KEYS */;
INSERT INTO `x360p_order_transfer` VALUES (1,0,2,0,23,'IGP20171120150738',16739.10,1511161658,1,1511161658,0,NULL,0),(2,0,2,0,41,'BWV20171130093404',2700.00,1512005644,1,1512005644,0,NULL,0),(3,0,2,0,37,'KSM20171201101818',200.00,1512094698,1,1512094698,0,NULL,0),(4,0,2,0,37,'DGO20171201101902',90.00,1512094742,1,1512094742,0,NULL,0),(5,0,2,0,37,'BQF20171201102041',102.00,1512094841,1,1512094841,0,NULL,0),(6,0,2,0,37,'LRI20171201102333',90.00,1512095013,1,1512095013,0,NULL,0),(7,0,2,0,38,'VUX20171201143008',180.00,1512109808,1,1512109808,0,NULL,0),(8,0,2,0,49,'XUR20171201144135',580.00,1512110495,1,1512110495,0,NULL,0),(9,0,2,0,52,'HCX20171206162934',360.00,1512548974,1,1512548974,0,NULL,0),(10,0,2,0,82,'EJC20171221102607',1346.00,1513823167,1,1513823167,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COMMENT='结转项目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_order_transfer_item`
--

LOCK TABLES `x360p_order_transfer_item` WRITE;
/*!40000 ALTER TABLE `x360p_order_transfer_item` DISABLE KEYS */;
INSERT INTO `x360p_order_transfer_item` VALUES (1,0,1,27,90.00,185.99,16739.10,1511161658,1,1511161658,0,NULL,0),(2,0,2,71,15.00,180.00,2700.00,1512005644,1,1512005644,0,NULL,0),(3,0,3,89,1.00,200.00,200.00,1512094698,1,1512094698,0,NULL,0),(4,0,4,90,1.00,90.00,90.00,1512094742,1,1512094742,0,NULL,0),(5,0,5,91,1.00,102.00,102.00,1512094841,1,1512094841,0,NULL,0),(6,0,6,95,1.00,90.00,90.00,1512095013,1,1512095013,0,NULL,0),(7,0,7,92,1.00,180.00,180.00,1512109808,1,1512109808,0,NULL,0),(8,0,8,107,1.00,200.00,200.00,1512110495,1,1512110495,0,NULL,0),(9,0,8,102,1.00,180.00,180.00,1512110495,1,1512110495,0,NULL,0),(10,0,8,103,1.00,200.00,200.00,1512110495,1,1512110495,0,NULL,0),(11,0,9,162,2.00,180.00,360.00,1512548974,1,1512548974,0,NULL,0),(12,0,10,289,1.00,200.00,200.00,1513823167,1,1513823167,0,NULL,0),(13,0,10,290,3.00,102.00,306.00,1513823167,1,1513823167,0,NULL,0),(14,0,10,288,7.00,120.00,840.00,1513823167,1,1513823167,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='机构表（加盟商）';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_org`
--

LOCK TABLES `x360p_org` WRITE;
/*!40000 ALTER TABLE `x360p_org` DISABLE KEYS */;
INSERT INTO `x360p_org` VALUES (6,0,'siyi',0,'18967656452','siyi',2,56,890,'mm街道',20171230,12,12,12,1,1513591455,1,1513655907,1,1513655907,1),(7,0,'yitong',0,'','yitong',4,87,1327,'',20180614,13,16,100,0,1513591727,1,1513593837,1,1513593837,1),(8,0,'小小运动馆',0,'13232313222','小小',4,87,1327,'ss街道',20180314,14,16,1000,1,1513591946,1,1513655903,1,1513655903,1),(9,0,'xutong',0,'18776565554','xutong',4,87,1327,'rr街道',20171207,12,12,222,0,1513596832,1,1513655864,1,1513655864,1),(10,0,'sihan',0,'13424243333','sihan',2,56,890,'xx街道',20180413,10,10,10,1,1513650832,1,1513665402,1,1513665402,1),(15,0,'guapicaozuo',1,'13734343433','gaupi',2,55,883,'uu街道',20171220,12,12,12,0,1513657356,1,1513657356,0,NULL,0),(16,0,'siyi',1,'13454522220','siyi',1,37,567,'ii街道',20171220,12,12,12,0,1513657811,1,1513665391,1,1513665391,1);
/*!40000 ALTER TABLE `x360p_org` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COMMENT='延期记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_org_renew_log`
--

LOCK TABLES `x360p_org_renew_log` WRITE;
/*!40000 ALTER TABLE `x360p_org_renew_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_org_renew_log` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_print_tpl`
--

LOCK TABLES `x360p_print_tpl` WRITE;
/*!40000 ALTER TABLE `x360p_print_tpl` DISABLE KEYS */;
INSERT INTO `x360p_print_tpl` VALUES (1,0,2,1,3,'{\"content\":{\"paper_width\":210,\"paper_height\":140,\"items\":[{\"field\":\"student_name\",\"type\":\"bs\",\"text\":\"学员姓名\",\"left\":67,\"top\":85,\"width\":101,\"height\":20},{\"field\":\"org_name\",\"type\":\"sys\",\"text\":\"机构\\/学校名称\",\"left\":235,\"top\":25,\"width\":140,\"height\":20},{\"field\":\"school_name\",\"type\":\"bs\",\"text\":\"学员学校\",\"left\":218,\"top\":84,\"width\":115,\"height\":20},{\"field\":\"grade\",\"type\":\"bs\",\"text\":\"学员年级\",\"left\":376,\"top\":83,\"width\":113,\"height\":20},{\"field\":\"ob_name\",\"type\":\"bs\",\"text\":\"校区\",\"left\":525,\"top\":81,\"width\":140,\"height\":20},{\"field\":\"index\",\"type\":\"bm\",\"text\":\"项次\",\"left\":41,\"top\":144,\"width\":159,\"height\":20,\"row\":\"items\"},{\"field\":\"note\",\"type\":\"bm\",\"text\":\"备注\",\"left\":221,\"top\":197,\"width\":313,\"height\":20,\"row\":\"items\"},{\"field\":\"total_amount\",\"type\":\"bs\",\"text\":\"应缴金额\",\"left\":640,\"top\":198,\"width\":66,\"height\":20},{\"field\":\"pay_amount\",\"type\":\"bs\",\"text\":\"实缴金额\",\"left\":639,\"top\":234,\"width\":67,\"height\":20},{\"field\":\"unpay_amount\",\"type\":\"bs\",\"text\":\"未缴金额\",\"left\":218,\"top\":236,\"width\":99,\"height\":20},{\"field\":\"op_name\",\"type\":\"bs\",\"text\":\"开票人\",\"left\":82,\"top\":306,\"width\":79,\"height\":20},{\"field\":\"receiver_name\",\"type\":\"bs\",\"text\":\"收款人\",\"left\":237,\"top\":309,\"width\":101,\"height\":20},{\"field\":\"receive_date\",\"type\":\"bs\",\"text\":\"收费日期\",\"left\":375,\"top\":307,\"width\":118,\"height\":20},{\"width\":317,\"height\":20,\"left\":218,\"top\":271,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"pay_amount_b\",\"text\":\"实收大写\",\"type\":\"bs\"},{\"width\":52,\"height\":20,\"left\":219,\"top\":141,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"class_name\",\"text\":\"班级\",\"type\":\"bm\"},{\"width\":102,\"height\":20,\"left\":290,\"top\":141,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"expire_time\",\"text\":\"有效期\",\"type\":\"bm\"},{\"width\":53,\"height\":20,\"left\":410,\"top\":142,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"nums\",\"text\":\"数量\",\"type\":\"bm\"},{\"width\":57,\"height\":20,\"left\":480,\"top\":142,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"origin_price\",\"text\":\"原价\",\"type\":\"bm\"},{\"width\":67,\"height\":20,\"left\":554,\"top\":141,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"price\",\"text\":\"折后单价\",\"type\":\"bm\"},{\"width\":66,\"height\":20,\"left\":639,\"top\":141,\"font_size\":\"inherit\",\"letter_spacing\":0,\"field\":\"subtotal\",\"text\":\"小计\",\"type\":\"bm\"}]}}',0,1511006172,1,1512619060,0,0,NULL),(2,0,2,1,1,'{\"content\":\"<div class=\\\"a4\\\"><div style=\\\"font-size: 20px;text-align: center;\\\" class=\\\"title titleinfo\\\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src=\\\"\\/static\\/img\\/fplogo.jpg\\\" style=\\\"width: 120px;height: 20px; margin-top: 7px;\\\"\\/>\\n &nbsp; &nbsp; &nbsp; &nbsp;校360标准收费收据<\\/div><div style=\\\"font-size: 14px;\\\">姓名：<span style=\\\"margin-right: 30px;\\\">{{bs.student_name}}<\\/span>学号：<span style=\\\"margin-right: 30px;\\\">{{bs.sno}}<\\/span>交费日期：<span style=\\\"margin-right: 30px;\\\">{{bs.pay_date}}<\\/span><span style=\\\"float: right;\\\">{{bs.receipt_no}}<\\/span>\\n &nbsp; &nbsp;<\\/div><table><tbody><tr><td class=\\\"title\\\">项目<\\/td><td class=\\\"title\\\">班级<\\/td><td class=\\\"title\\\">有效期至<\\/td><td class=\\\"title\\\">数量<\\/td><td class=\\\"title\\\">单价<\\/td><td class=\\\"title\\\" width=\\\"85px\\\">折后金额<\\/td><td class=\\\"title\\\" width=\\\"85px\\\">小计金额<\\/td><\\/tr><!--没有打印数据的是要空出来一行--><tr class=\\\"nocolsborder\\\" v-for=\\\"(item,index) in bm\\\" :key=\\\"index\\\"><td>{{item.lesson_name}}<\\/td><td>{{item.class_name}}<\\/td><td>{{item.expire_time}}<\\/td><td class=\\\"right\\\">{{item.nums}}<\\/td><td class=\\\"right\\\">{{item.origin_price}}<\\/td><td class=\\\"right\\\">{{item.price}}<\\/td><td class=\\\"right\\\">{{item.subtotal}}<\\/td><\\/tr><\\/tbody><\\/table><table class=\\\"border-top-none\\\"><tbody><tr><td width=\\\"120px\\\" class=\\\"title\\\">备注<\\/td><td colspan=\\\"3\\\">{{bs.pay_remark}}<\\/td><td class=\\\"title\\\" width=\\\"85px\\\">应收合计<\\/td><td width=\\\"85px\\\" class=\\\"right\\\">{{bs.origin_amount}}<\\/td><\\/tr><tr><td class=\\\"title\\\" decimallabel=\\\"ReserveMoney\\\">冲减电子钱包\\/计加以往欠交金额<\\/td><td class=\\\"right\\\">{{bs.balance_paid_amount}}<\\/td><td class=\\\"title\\\">直减优惠<\\/td><td class=\\\"right\\\">{{bs.order_reduce_amount}}<\\/td><td class=\\\"title\\\">实收<\\/td><td class=\\\"right\\\">{{bs.pay_amount}}<\\/td><\\/tr><tr><td class=\\\"title\\\">金额(大写)<\\/td><td colspan=\\\"3\\\">{{bs.pay_amount_b}}<\\/td><td class=\\\"title\\\">计入电子钱包\\/欠交金额<\\/td><td class=\\\"right\\\">{{bs.pay_remain_amount}}<\\/td><\\/tr><\\/tbody><\\/table><div class=\\\"clearfix otherinfo\\\"><div style=\\\"position: absolute;left:0;right: 320px;\\\"><p style=\\\"margin: 0;\\\">经办人：{{bs.op_name}}(签名)<\\/p><p style=\\\"margin: 0;line-height: 1.1;\\\">1、请家长妥善保管此单和发票，凭此单和发票上课；<\\/p><p style=\\\"margin: 0;line-height: 1.1;\\\">2、记住上课时间和地点；<\\/p><p style=\\\"margin: 0;line-height: 1.1;\\\">3、如需退费，请在开学一周前到办理报名的教学点凭发票和此单办理。<\\/p><\\/div><img qrcode=\\\"true\\\" src=\\\"\\/static\\/img\\/mpqr.jpg\\\" height=\\\"110px\\\" width=\\\"110px\\\"\\/>\\n &nbsp; &nbsp; &nbsp; &nbsp;<div style=\\\"width: 195px;display: inline-block;float: right;\\\"><p style=\\\"margin: 0;\\\">掌握孩子学情，请微信关注&quot;师生信&quot;<\\/p><p style=\\\"margin: 0;\\\">用户名：{{bs.account}}<\\/p><p style=\\\"margin: 0;\\\">(密码请咨询学校工作人员)<\\/p><p style=\\\"margin: 0;\\\">客户签名：<\\/p><\\/div><\\/div><\\/div>\"}',0,1512618435,1,1512619060,0,0,NULL),(3,0,2,1,1,'{\"content\":\"<div class=\\\"a4\\\"><div style=\\\"font-size: 20px;text-align: center;\\\" class=\\\"title titleinfo\\\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src=\\\"\\/static\\/img\\/fplogo.jpg\\\" style=\\\"width: 120px;height: 20px; margin-top: 7px;\\\"\\/>\\n &nbsp; &nbsp; &nbsp; &nbsp;校360标准收费收据<\\/div><div style=\\\"font-size: 14px;\\\">姓名：<span style=\\\"margin-right: 30px;\\\">{{bs.student_name}}<\\/span>学号：<span style=\\\"margin-right: 30px;\\\">{{bs.sno}}<\\/span>交费日期：<span style=\\\"margin-right: 30px;\\\">{{bs.pay_date}}<\\/span><span style=\\\"float: right;\\\">{{bs.receipt_no}}<\\/span>\\n &nbsp; &nbsp;<\\/div><table><tbody><tr><td class=\\\"title\\\">项目<\\/td><td class=\\\"title\\\">班级<\\/td><td class=\\\"title\\\">有效期至<\\/td><td class=\\\"title\\\">数量<\\/td><td class=\\\"title\\\">单价<\\/td><td class=\\\"title\\\" width=\\\"85px\\\">折后金额<\\/td><td class=\\\"title\\\" width=\\\"85px\\\">小计金额<\\/td><\\/tr><!--没有打印数据的是要空出来一行--><tr class=\\\"nocolsborder\\\" v-for=\\\"(item,index) in bm\\\" :key=\\\"index\\\"><td>{{item.lesson_name}}<\\/td><td>{{item.class_name}}<\\/td><td>{{item.expire_time}}<\\/td><td class=\\\"right\\\">{{item.nums}}<\\/td><td class=\\\"right\\\">{{item.origin_price}}<\\/td><td class=\\\"right\\\">{{item.price}}<\\/td><td class=\\\"right\\\">{{item.subtotal}}<\\/td><\\/tr><\\/tbody><\\/table><table class=\\\"border-top-none\\\"><tbody><tr><td width=\\\"120px\\\" class=\\\"title\\\">备注<\\/td><td colspan=\\\"3\\\">{{bs.pay_remark}}<\\/td><td class=\\\"title\\\" width=\\\"85px\\\">应收合计<\\/td><td width=\\\"85px\\\" class=\\\"right\\\">{{bs.origin_amount}}<\\/td><\\/tr><tr><td class=\\\"title\\\" decimallabel=\\\"ReserveMoney\\\">冲减电子钱包\\/计加以往欠交金额<\\/td><td class=\\\"right\\\">{{bs.balance_paid_amount}}<\\/td><td class=\\\"title\\\">直减优惠<\\/td><td class=\\\"right\\\">{{bs.order_reduce_amount}}<\\/td><td class=\\\"title\\\">实收<\\/td><td class=\\\"right\\\">{{bs.pay_amount}}<\\/td><\\/tr><tr><td class=\\\"title\\\">金额(大写)<\\/td><td colspan=\\\"3\\\">{{bs.pay_amount_b}}<\\/td><td class=\\\"title\\\">计入电子钱包\\/欠交金额<\\/td><td class=\\\"right\\\">{{bs.pay_remain_amount}}<\\/td><\\/tr><\\/tbody><\\/table><div class=\\\"clearfix otherinfo\\\"><div style=\\\"position: absolute;left:0;right: 320px;\\\"><p style=\\\"margin: 0;\\\">经办人：{{bs.op_name}}(签名)<\\/p><p style=\\\"margin: 0;line-height: 1.1;\\\">1、请家长妥善保管此单和发票，凭此单和发票上课；<\\/p><p style=\\\"margin: 0;line-height: 1.1;\\\">2、记住上课时间和地点；<\\/p><p style=\\\"margin: 0;line-height: 1.1;\\\">3、如需退费，请在开学一周前到办理报名的教学点凭发票和此单办理。<\\/p><\\/div><img qrcode=\\\"true\\\" src=\\\"\\/static\\/img\\/mpqr.jpg\\\" height=\\\"110px\\\" width=\\\"110px\\\"\\/>\\n &nbsp; &nbsp; &nbsp; &nbsp;<div style=\\\"width: 195px;display: inline-block;float: right;\\\"><p style=\\\"margin: 0;\\\">掌握孩子学情，请微信关注&quot;师生信&quot;<\\/p><p style=\\\"margin: 0;\\\">用户名：{{bs.account}}<\\/p><p style=\\\"margin: 0;\\\">(密码请咨询学校工作人员)<\\/p><p style=\\\"margin: 0;\\\">客户签名：<\\/p><\\/div><\\/div><\\/div>\"}',1,1512619060,1,1512619060,0,0,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COMMENT='公立学校表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_public_school`
--

LOCK TABLES `x360p_public_school` WRITE;
/*!40000 ALTER TABLE `x360p_public_school` DISABLE KEYS */;
INSERT INTO `x360p_public_school` VALUES (1,0,43,'罗湖二小',19,291,3062,3062,'笋岗东路家乐福附近',1512045838,1,1512045838,0,NULL,0),(2,0,43,'笋岗中学',19,291,3062,3062,'笋岗路笋岗中学',1512046947,1,1512046947,0,NULL,0),(3,0,43,'龙岗分校',19,291,3063,3063,'龙岗分校a',1512047015,1,1512102475,1,1512102475,1),(4,0,2,'阳光喔分校',9,143,1814,1814,'阳光喔分校啊',1512097990,1,1512097990,0,NULL,0),(5,0,20,'育才二小',19,291,3058,3058,'蛇口工业8路18号11',1512352625,1,1512352773,1,1512352773,1),(6,0,20,'育才1小',19,291,3058,3058,'',1512352689,1,1512352689,0,NULL,0),(7,0,20,'育才3小',19,291,3058,3058,'',1512352739,1,1512352739,0,NULL,0);
/*!40000 ALTER TABLE `x360p_public_school` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COMMENT='系统角色表(每一个用户都对应有1到多个角色)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_role`
--

LOCK TABLES `x360p_role` WRITE;
/*!40000 ALTER TABLE `x360p_role` DISABLE KEYS */;
INSERT INTO `x360p_role` VALUES (1,0,'老师','拥有查看学员信息、查看课表、上课点名等权限。','dashboard,dashboard.view,dashboard.dosignup,dashboard.signupconfirm,dashboard.saveorder,dashboard.chargeconfirm,dashboard.dosettle,dashboard.chargemakeup,dashboard.dotransfer,dashboard.dorefund,dashboard.toformal,dashboard.broadcast,broadcast.add,broadcast.edit,broadcast.delete,dashboard.backlogs,backlogs.add,backlogs.edit,backlogs.delete,backlogs.finished,backlogs.unfinished,backlogs.addline,backlogs.cancelline,recruiting,recruiting.list,recruiting.add,recruiting.edit,recruiting.delete,recruiting.following,following.add,following.edit,recruiting.audition,audition.add,audition.delete,business,business.student,student.add,student.edit,student.stop,student.transfer,student.transferclass,student.transferschool,business.order,order.pay,order.refund,business.class,class.add,class.edit,class.arrange,class.students,business.arrange,arrange.add,arrange.visual,arrange.delete,business.attendance,attendance.cancel,business.hour,business.iae,service,service.study,service.visit,service.remind,service.comments,service.homeworks,reports,reports.overview,reports.all,reports.on,reports.income,reports.attendance,reports.performance,reports.incomeandexpend,reports.service,basic,basic.lesson,lesson.new,lesson.bindmaterial,lesson.delete,lesson.edit,basic.subject,subject.add,subject.edit,subject.delete,basic.teachers,teachers.add,teachers.edit,teachers.delete,account.add,account.lock,account.reset,basic.classrooms,classrooms.add,classrooms.edit,classrooms.delete,basic.time,time.add,time.edit,time.delete,basic.holiday,basic.materials,materials.add,materials.edit,materials.delete,materials.in,materials.out,materials.transfer,materials.store,basic.schools,schools.add,system,system.configs,configs.params,configs.print,configs.wxmp,wxmp.list,wxmp.basic,wxmp.tplmsg,wxmp.menu,wxmp.material,wxmp.reply,configs.template,configs.payment,configs.storage,configs.mobile,system.departments,departments.add,departments.edit,departments.delete,branchs.edit,system.employees,employees.add,employees.edit,employees.delete,employees.leave,employees.restore,system.roles,roles.add,roles.edit,roles.delete,roles.per,system.dicts,system.orgs,orgs.add,orgs.edit,orgs.lock,orgs.renew,orgs.delete','',1,1498095552,1,1513764383,NULL,0,0,NULL,NULL),(2,0,'助教','拥有设置组织架构、管理员工信息、分配系统操作权限、统计查看员工业绩报表等权限。','','',1,1498095552,1,1511181223,NULL,0,0,NULL,NULL),(3,0,'校长','除了不能查看系统操作日志外，拥有所有其他权限。','basic,basic.lesson,lesson.new,lesson.on,lesson.off,lesson.delete,lesson.edit,basic.subject,subject.add,subject.edit,subject.delete,basic.teachers,teachers.add,teachers.edit,teachers.delete,account.add,account.lock,account.reset,basic.classrooms,classrooms.add,classrooms.edit,classrooms.delete,basic.time,time.add,time.edit,time.delete,basic.holiday,basic.materials,materials.add,materials.edit,materials.delete,materials.in,materials.out,materials.transfer','',1,1498098725,1,1511335677,NULL,0,0,NULL,NULL),(4,0,'学管师','拥有查看所自己名下的学员信息、费用信息、给名下的学员进行上课点名、发送作业通知、回复家长等权限','basic,basic.lesson,lesson.new,lesson.on,lesson.off,lesson.delete,lesson.edit,basic.subject,subject.add,subject.edit,subject.delete,basic.teachers,teachers.add,teachers.edit,teachers.delete,account.add,account.lock,account.reset,basic.classrooms,classrooms.add,classrooms.edit,classrooms.delete,basic.time,time.add,time.edit,time.delete,basic.holiday,basic.materials,materials.add,materials.edit,materials.delete,materials.in,materials.out,materials.transfer','',1,1498290947,1,1511331166,NULL,0,0,NULL,NULL),(5,0,'前台','拥有新学员报名、收费、退费、班级信息查询、上课信息查询、点名上课等权限。','','',0,1498290965,1,1511181287,NULL,0,0,NULL,NULL),(7,0,'招生咨询组','拥有意向客户信息管理、录入客户沟通记录等权限。','','',0,1498291022,1,1511181097,NULL,0,0,NULL,NULL),(10,0,'系统管理员','系统管理员拥有最高权限','dashboard,dashboard.view,dashboard.dosignup,dashboard.signupconfirm,dashboard.saveorder,dashboard.chargeconfirm,dashboard.dosettle,dashboard.chargemakeup,dashboard.dotransfer,dashboard.dorefund,dashboard.toformal,dashboard.broadcast,broadcast.add,broadcast.edit,broadcast.delete,dashboard.backlogs,backlogs.add,backlogs.edit,backlogs.delete,backlogs.finished,backlogs.unfinished,backlogs.addline,backlogs.cancelline,recruiting,recruiting.list,recruiting.add,recruiting.edit,recruiting.delete,recruiting.following,following.add,following.edit,recruiting.audition,audition.add,audition.delete,business,business.student,student.add,student.edit,student.stop,student.transfer,student.transferclass,student.transferschool,business.order,order.pay,order.refund,business.class,class.add,class.edit,class.arrange,class.students,business.arrange,arrange.add,arrange.visual,arrange.delete,business.attendance,attendance.cancel,business.hour,business.iae,service,service.study,service.visit,service.remind,service.comments,service.homeworks,reports,reports.overview,reports.all,reports.on,reports.income,reports.attendance,reports.performance,reports.incomeandexpend,reports.service,basic,basic.lesson,lesson.new,lesson.bindmaterial,lesson.delete,lesson.edit,basic.subject,subject.add,subject.edit,subject.delete,basic.teachers,teachers.add,teachers.edit,teachers.delete,account.add,account.lock,account.reset,basic.classrooms,classrooms.add,classrooms.edit,classrooms.delete,basic.time,time.add,time.edit,time.delete,basic.holiday,basic.materials,materials.add,materials.edit,materials.delete,materials.in,materials.out,materials.transfer,materials.store,basic.schools,schools.add,system,system.configs,configs.params,configs.print,configs.wxmp,wxmp.list,wxmp.basic,wxmp.tplmsg,wxmp.menu,wxmp.material,wxmp.reply,configs.template,configs.payment,configs.storage,configs.mobile,system.departments,departments.add,departments.edit,departments.delete,branchs.edit,system.employees,employees.add,employees.edit,employees.delete,employees.leave,employees.restore,system.roles,roles.add,roles.edit,roles.delete,roles.per,system.dicts,system.orgs,orgs.add,orgs.edit,orgs.lock,orgs.renew,orgs.delete',NULL,0,1498291051,1,1513764369,NULL,0,0,NULL,NULL),(11,0,'班组任组','拥有与班级事务相关的权限，如：上课点名、发送作业通知、议价学员','','',0,1511181007,1,1511181020,NULL,0,0,NULL,NULL),(12,0,'后厨','负责食堂做饭打饭',NULL,NULL,0,1511342317,1,1511342317,NULL,0,0,NULL,NULL),(14,0,'清洁组','负责内外清洁',NULL,NULL,0,1511354367,1,1511354367,NULL,0,0,NULL,NULL),(15,0,'保卫科','负责全校安全保卫工作',NULL,NULL,0,1511919718,1,1511919718,NULL,0,0,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='季度日期表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_season_date`
--

LOCK TABLES `x360p_season_date` WRITE;
/*!40000 ALTER TABLE `x360p_season_date` DISABLE KEYS */;
INSERT INTO `x360p_season_date` VALUES (1,0,0,0,'H',99990121,99990219,0,0,NULL,0,0,0),(2,0,0,0,'C',99990225,99990706,0,0,NULL,0,0,0),(3,0,0,0,'S',99990713,99990831,0,0,NULL,0,0,0),(4,0,0,0,'Q',99990901,99990110,0,0,NULL,0,0,0);
/*!40000 ALTER TABLE `x360p_season_date` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COMMENT='短信发送记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_sms_history`
--

LOCK TABLES `x360p_sms_history` WRITE;
/*!40000 ALTER TABLE `x360p_sms_history` DISABLE KEYS */;
INSERT INTO `x360p_sms_history` VALUES (5,0,'17768026485','1405 (校360验证码,5分钟内有效) 【校360】',0,1513684339,0,1513684339,0,NULL,0),(6,0,'13125132038','0432 (校360验证码,5分钟内有效) 【校360】',0,1513754698,0,1513754698,0,NULL,0),(7,0,'13125132038','8122 (校360验证码,5分钟内有效) 【校360】',0,1513755689,0,1513755689,0,NULL,0),(8,0,'13125132038','9616 (校360验证码,5分钟内有效) 【校360】',0,1513755991,0,1513755991,0,NULL,0),(9,0,'13125132038','2925 (校360验证码,5分钟内有效) 【校360】',0,1513756308,0,1513756308,0,NULL,0),(10,0,'13125132038','9460 (校360验证码,5分钟内有效) 【校360】',0,1513756340,0,1513756340,0,NULL,0),(11,0,'13125132038','2591 (校360验证码,5分钟内有效) 【校360】',0,1513756568,0,1513756568,0,NULL,0),(12,0,'13125132038','3889 (校360验证码,5分钟内有效) 【校360】',0,1513756633,0,1513756633,0,NULL,0),(13,0,'13125132038','2732 (校360验证码,5分钟内有效) 【校360】',0,1513756681,0,1513756681,0,NULL,0),(14,0,'13125132038','8649 (校360验证码,5分钟内有效) 【校360】',0,1513756700,0,1513756700,0,NULL,0),(15,0,'13125132038','9822 (校360验证码,5分钟内有效) 【校360】',0,1513756720,0,1513756720,0,NULL,0),(16,0,'13125132038','9346 (校360验证码,5分钟内有效) 【校360】',0,1513756794,0,1513756794,0,NULL,0),(17,0,'13125132038','5469 (校360验证码,5分钟内有效) 【校360】',0,1513756970,0,1513756970,0,NULL,0),(18,0,'13125132038','4706 (校360验证码,5分钟内有效) 【校360】',0,1513757162,0,1513757162,0,NULL,0),(19,0,'13125132038','8996 (校360验证码,5分钟内有效) 【校360】',0,1513757209,0,1513757209,0,NULL,0),(20,0,'13125132038','5461 (校360验证码,5分钟内有效) 【校360】',0,1513757222,0,1513757222,0,NULL,0),(21,0,'13125132038','2609 (校360验证码,5分钟内有效) 【校360】',0,1513757306,0,1513757306,0,NULL,0),(22,0,'13125132038','6475 (校360验证码,5分钟内有效) 【校360】',0,1513757448,0,1513757448,0,NULL,0),(23,0,'13125132038','8997 (校360验证码,5分钟内有效) 【校360】',0,1513757626,0,1513757626,0,NULL,0),(24,0,'13125132038','8857 (校360验证码,5分钟内有效) 【校360】',0,1513757864,0,1513757864,0,NULL,0),(25,0,'13125132038','1229 (校360验证码,5分钟内有效) 【校360】',0,1513759146,0,1513759146,0,NULL,0),(26,0,'13125132038','6182 (校360验证码,5分钟内有效) 【校360】',0,1513762671,0,1513762671,0,NULL,0),(27,0,'13125132038','0500 (校360验证码,5分钟内有效) 【校360】',0,1513762750,0,1513762750,0,NULL,0),(28,0,'13125132038','5297 (校360验证码,5分钟内有效) 【校360】',0,1513763164,0,1513763164,0,NULL,0),(29,0,'13125132038','4728 (校360验证码,5分钟内有效) 【校360】',0,1513763712,0,1513763712,0,NULL,0),(30,0,'13125132038','9788 (校360验证码,5分钟内有效) 【校360】',0,1513763916,0,1513763916,0,NULL,0),(31,0,'13125132038','0651 (校360验证码,5分钟内有效) 【校360】',0,1513763999,0,1513763999,0,NULL,0),(32,0,'13125132038','6684 (校360验证码,5分钟内有效) 【校360】',0,1513764196,0,1513764196,0,NULL,0),(33,0,'13125132038','4472 (校360验证码,5分钟内有效) 【校360】',0,1513764508,0,1513764508,0,NULL,0),(34,0,'13125132038','1361 (校360验证码,5分钟内有效) 【校360】',0,1513765145,0,1513765145,0,NULL,0),(35,0,'13125132038','0414 (校360验证码,5分钟内有效) 【校360】',0,1513765467,0,1513765467,0,NULL,0),(36,0,'13125132038','8132 (校360验证码,5分钟内有效) 【校360】',0,1513765607,0,1513765607,0,NULL,0),(37,0,'13125132038','4135 (校360验证码,5分钟内有效) 【校360】',0,1513768925,0,1513768925,0,NULL,0),(38,0,'13125132038','9108 (校360验证码,5分钟内有效) 【校360】',0,1513768990,0,1513768990,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COMMENT='短息验证码记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_sms_vcode`
--

LOCK TABLES `x360p_sms_vcode` WRITE;
/*!40000 ALTER TABLE `x360p_sms_vcode` DISABLE KEYS */;
INSERT INTO `x360p_sms_vcode` VALUES (3,0,'17768026485','forget','1405',1513684340,1513684640,0,NULL,0,NULL,0),(36,0,'13125132038','forget','9108',1513768991,1513769021,0,NULL,0,NULL,0);
/*!40000 ALTER TABLE `x360p_sms_vcode` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COMMENT='学员表(学员的记录信息)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student`
--

LOCK TABLES `x360p_student` WRITE;
/*!40000 ALTER TABLE `x360p_student` DISABLE KEYS */;
INSERT INTO `x360p_student` VALUES (1,0,2,'刘子云01','liuziyun01','lzy0','','1','',0,0,0,0,0,'0',0,'13125132345','',1,0,'','',0,'',0,'','','0008659832',0.00,0.00,0,1513168985,0,1510971618,1,1513168985,0,NULL,0,''),(2,0,2,'刘子云02','liuziyun02','lzy0','','1','',0,0,0,0,0,'0',0,'18888444444','',1,0,'','',0,'',0,'','','0000042061',0.00,0.00,0,1512716635,0,1510971695,1,1512716636,0,NULL,0,''),(3,0,2,'刘子云03','liuziyun03','lzy0','','1','',0,0,0,0,0,'0',0,'15555555555','',1,0,'','',0,'',0,'','','0008600838',0.00,0.00,0,1512716635,0,1510971718,1,1512716636,0,NULL,0,''),(4,0,2,'刘子云04','liuziyun04','lzy0','','1','',0,0,0,0,0,'0',0,'13124234456','',1,0,'','',0,'',0,'','','0000094341',0.00,0.00,0,1512716635,0,1510971763,1,1512716637,0,NULL,0,''),(5,0,2,'yaorui001','','','','0','',0,0,0,0,0,'0',0,'17768026485','',1,0,'','',0,'',0,'','','',0.00,0.00,0,1511573588,0,1510971826,1,1511573588,0,NULL,0,''),(6,0,2,'yaorui002','','','','0','',0,0,0,0,0,'0',0,'17768026486','',0,0,'','',0,'',0,'','','',0.00,0.00,0,1511573588,0,1510971912,1,1511573588,0,NULL,0,''),(7,0,2,'yaorui003','yaorui003','y','','0','',0,0,0,0,0,'0',0,'17768026487','',0,0,'','',0,'',0,'','','',0.00,0.00,0,1512725205,0,1510971927,1,1512725205,0,NULL,0,''),(8,0,2,'刘子云05','liuziyun05','lzy0','','1','',0,0,0,0,0,'0',0,'13244454566','',1,0,'','',0,'',0,'','','0008674251',0.00,0.00,0,1512716635,0,1510971975,1,1512716637,0,NULL,0,''),(9,0,2,'刘子云06','liuziyun06','lzy0','','1','',0,0,0,0,0,'0',0,'13243445445','',1,0,'','',0,'',0,'','','0008697203',0.00,0.00,0,1512716635,0,1510972045,1,1512716638,0,NULL,0,''),(10,0,2,'yaorui004','','','','0','',0,0,0,0,0,'0',0,'17768026490','',0,0,'','',0,'',0,'','','',0.00,0.00,0,0,0,1510974259,1,1510974259,0,NULL,0,''),(11,0,2,'test_lesson1','','','','1','',0,0,0,0,0,'0',0,'15124234243','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1510993877,1,1510993877,0,NULL,0,''),(12,0,2,'test_lesson2','','','','1','',0,0,0,0,0,'0',0,'13232453424','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,1511253506,0,1510994219,1,1511253506,0,NULL,0,''),(13,0,2,'test_lesson3','testlesson3','tl','','1','',0,0,0,0,0,'0',0,'18977363737','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,1513821705,0,1510994693,1,1513821706,0,NULL,0,''),(14,0,2,'test_lesson4','','','','1','',0,0,0,0,0,'0',0,'13344545566','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1510994712,1,1510994712,0,NULL,0,''),(15,0,2,'test_lesson5','testlesson5','tl','','1','',0,0,0,0,0,'0',0,'15857374734','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,1513821705,0,1510994740,1,1513821706,0,NULL,0,''),(16,0,2,'test_lesson6','','','','1','',0,0,0,0,0,'0',0,'15323234324','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1510994756,1,1510994756,0,NULL,0,''),(17,0,2,'test_lesson7','','','','1','',0,0,0,0,0,'0',0,'15566575673','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,1511266483,0,1510994770,1,1511266483,0,NULL,0,''),(18,0,2,'刘一飞','','','','1','',1193932800,2007,11,2,2,'201班',629805,'15322486654','',1,0,'','',0,'',0,'','','',0.00,0.00,0,1511323354,0,1510995734,1,1511323354,0,NULL,0,''),(19,0,2,'刘海','','','','1','',0,0,0,0,3,'302班',629071,'13544865874','',1,0,'','',0,'',0,'','',NULL,500.00,0.00,0,1511323120,0,1510996786,1,1511323120,0,NULL,0,''),(20,0,2,'王二','wanger','we','','1','',1479398400,2016,11,18,0,'0',0,'13234234444','',0,0,'','',0,'',0,'','',NULL,0.00,0.00,0,1513821705,0,1511001089,1,1513821706,0,NULL,0,''),(21,0,2,'刘翔','','','','1','',0,0,0,0,0,'0',0,'15488476846','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1511004143,1,1511004143,0,NULL,0,''),(22,0,2,'刘黎灯','','','','1','',1509552000,2017,11,2,0,'0',0,'15354865852','',1,0,'','',0,'',0,'','','sdqa',0.00,0.00,0,0,0,1511004217,1,1511005379,0,NULL,0,''),(23,0,2,'汤大仙儿','','','','1','',1141747200,2006,3,8,2,'0',0,'18192569377','',1,0,'','',0,'',0,'','','',0.00,0.00,0,1511322572,0,1511144986,1,1511322572,0,NULL,0,''),(24,0,2,'李二狗','','','ergou','1','',0,0,0,0,0,'0',0,'15345345322','',0,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1511151617,1,1511151617,0,NULL,0,''),(25,0,2,'小仙女','','','xiaoxiannv','2','',1321286400,2011,11,15,0,'0',0,'13474639999','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1511166175,1,1511166175,0,NULL,0,''),(26,0,2,'学生26','','','','0','',1288800000,0,0,0,0,'0',0,'17756236856','',1,0,'','',0,'',0,'','','',0.00,0.00,0,1512009291,0,1511257116,1,1512009291,0,NULL,0,''),(27,0,2,'学生27','','','','0','',0,0,0,0,0,'0',0,'15623654562','',0,0,'','',0,'',0,'','','',0.00,0.00,0,1512009291,0,1511257166,1,1512009291,0,NULL,0,''),(28,0,2,'学生28','','','','0','',0,0,0,0,0,'0',0,'15623657895','',0,0,'','',0,'',0,'','','',0.00,0.00,0,0,0,1511257189,1,1511257189,0,NULL,0,''),(29,0,2,'学生29','','','','0','',0,0,0,0,0,'0',0,'18878956235','',0,0,'','',0,'',0,'','','',0.00,0.00,0,1512377419,0,1511257246,1,1512377419,0,NULL,0,''),(30,0,2,'学生30','','','','0','',0,0,0,0,0,'0',0,'13356234527','',0,0,'','',0,'',0,'','','',0.00,0.00,0,1512377419,0,1511257272,1,1512377420,0,NULL,0,''),(31,0,2,'学生31free-one2one','','','','0','',0,0,0,0,0,'0',0,'17758654521','',0,0,'','',0,'',0,'','','',0.00,0.00,0,1511323779,0,1511323486,1,1511323779,0,NULL,0,''),(32,0,2,'西门大官人','','','Ximengqing','1','',1194796800,2007,11,12,0,'0',0,'18396372833','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1511333935,1,1511333935,0,NULL,0,''),(33,0,2,'胡歌','','','','1','',485539200,1985,5,22,0,'0',0,'18928374662','胡曲',4,0,'','',0,'',0,'','',NULL,0.00,0.00,0,1511582586,0,1511338035,1,1511582586,0,NULL,0,''),(34,0,2,'刘溜球','','','','1','',0,0,0,0,0,'0',0,'13548521546','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,1511346471,0,1511346067,1,1511346471,0,NULL,0,''),(35,0,2,'yaorui35','','','','0','',0,0,0,0,0,'0',0,'13366565555','',0,0,'','',0,'',0,'','','',0.00,0.00,0,1512528638,0,1511354386,1,1512528638,0,NULL,0,''),(36,0,2,'yaorui36','','','','0','http://s10.xiao360.com//x360pstudent_avatar/1/17/11/30/30146890afc60dbbfb1facf9e5c2df49.gif',0,0,0,0,0,'0',0,'18878956548','',0,0,'','',0,'',0,'','','',0.00,0.00,0,1512528638,0,1511354399,1,1512528638,0,NULL,0,''),(37,0,2,'李四','','','','1','',0,0,0,0,0,'0',0,'13123333221','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1511943146,1,1511943146,0,NULL,0,''),(38,0,2,'王五','','','','1','',0,0,0,0,0,'0',0,'13444432326','',1,0,'','',0,'',0,'','',NULL,180.00,0.00,0,0,0,1511947107,1,1511947107,0,NULL,0,''),(39,0,2,'孙子','','','','1','',0,0,0,0,0,'0',0,'15654332222','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1511947296,1,1511947296,0,NULL,0,''),(40,0,2,'杨幂','','','','1','',0,0,0,0,0,'0',0,'15124343455','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512004807,1,1512004807,0,NULL,0,''),(41,0,2,'杨过','yangguo','yg','','1','http://s10.xiao360.com//x360pstudent_avatar/1/17/11/30/3861e1e25d8c2c979243e3c7b1272e87.jpeg',0,0,0,0,0,'0',0,'15676765544','',1,0,'','',0,'',0,'','',NULL,2580.00,0.00,0,1513821705,0,1512005259,1,1513821706,0,NULL,0,''),(43,0,2,'德莱厄斯','','','','1','',0,0,0,0,3,'1',0,'15123221221','琼斯',2,0,'','',0,'',0,'','','',0.00,0.00,0,1512528638,0,1512036356,1,1512528638,0,NULL,0,''),(44,0,35,'yaorui001','','','','0','',1293033600,0,0,0,0,'0',0,'18128874425','',1,0,'','',0,'',0,'','','',0.00,0.00,0,0,0,1512096363,1,1512096363,0,NULL,0,''),(45,0,35,'yaorui002','','','','0','',1293120000,0,0,0,0,'0',0,'18128874426','',1,0,'','',0,'',0,'','','',0.00,0.00,0,0,0,1512096749,1,1512096749,0,NULL,0,''),(46,0,35,'yaorui003','','','','0','',1293120000,0,0,0,0,'0',0,'18128874427','',1,0,'','',0,'',0,'','','',0.00,0.00,0,0,0,1512097253,1,1512097253,0,NULL,0,''),(47,0,35,'yaorui004','','','','0','',1293120000,0,0,0,0,'0',0,'18128874428','',1,0,'','',0,'',0,'','','',0.00,0.00,0,0,0,1512097366,1,1512097366,0,NULL,0,''),(48,0,35,'yaorui005','yaorui005','y','yaorui','0','',1293724800,0,0,0,0,'0',0,'17768026485','father',2,10063,'','mother',3,'17768026491',0,'','','',0.00,0.00,0,0,0,1512098199,1,1513667708,0,NULL,0,''),(49,0,2,'测试11','ceshi11','cs1','test_nick','1','',0,0,0,0,0,'0',0,'15132324435','',1,0,'','',0,'',0,'','','',0.00,0.00,0,1512725205,0,1512099589,1,1512725205,0,NULL,0,''),(50,0,4,'肖邦','','','','1','',0,0,0,0,0,'0',0,'13928412238','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512350735,1,1512350735,0,NULL,0,''),(51,0,2,'咨询名单1','','','','1','',1165248000,2006,12,5,0,'0',0,'13928412281','',0,0,'','',0,'',0,'','','',0.00,0.00,0,0,0,1512372280,1,1512464401,0,NULL,0,''),(52,0,2,'test-12','test12','t1','','1','',0,0,0,0,0,'0',0,'13123422221','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,1512725205,0,1512465297,1,1512725205,0,NULL,0,''),(53,0,2,'test-6','','','','1','',0,0,0,0,0,'0',0,'15134564565','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512529320,1,1512529320,0,NULL,0,''),(54,0,35,'姚瑞','yaorui','yr','','0','',1512489600,2017,12,6,0,'0',0,'18878956324','',0,0,'','',0,'',0,'','','',0.00,0.00,0,0,0,1512548091,1,1512548251,0,NULL,0,''),(55,0,35,'姚明','yaoming','ym','','1','',0,0,0,0,0,'0',0,'15532154253','',0,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512549034,1,1512549034,0,NULL,0,''),(56,0,2,'老李','laoli','ll','','1','',0,0,0,0,2,'0',0,'15454845645','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512549247,1,1512549247,0,NULL,0,''),(57,0,2,'岳飞','yuefei','yf','','1','',0,0,0,0,0,'0',0,'18932123333','',1,0,'','',0,'',0,'','','',0.00,0.00,0,1512725205,0,1512549483,1,1512725205,0,NULL,0,''),(58,0,2,'打印学员01','dayinxueyuan01','dyxy0','','1','',0,0,0,0,0,'0',0,'15054254856','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512611347,1,1512611347,0,NULL,0,''),(59,0,2,'打印学员02','dayinxueyuan02','dyxy0','','1','',0,0,0,0,0,'0',0,'15064851234','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512614181,1,1512614181,0,NULL,0,''),(60,0,2,'打印学员03','dayinxueyuan03','dyxy0','','1','',0,0,0,0,0,'0',0,'15354868478','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512617145,1,1512617145,0,NULL,0,''),(61,0,2,'打印学员04','dayinxueyuan04','dyxy0','','1','',0,0,0,0,0,'0',0,'15248456263','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512617931,1,1512617931,0,NULL,0,''),(62,0,2,'打印学员05','dayinxueyuan05','dyxy0','','1','',0,0,0,0,0,'0',0,'18745123654','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512618093,1,1512618093,0,NULL,0,''),(63,0,2,'打印学员06','dayinxueyuan06','dyxy0','','1','',0,0,0,0,0,'0',0,'15846523154','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512618204,1,1512618204,0,NULL,0,''),(64,0,2,'打印学员07','dayinxueyuan07','dyxy0','','1','',0,0,0,0,0,'0',0,'15958452156','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512618266,1,1512618266,0,NULL,0,''),(65,0,2,'打印学员08','dayinxueyuan08','dyxy0','','1','http://s10.xiao360.com//x360pstudent_avatar/1/17/12/07/e885e1b891ecda4eaa25c53ce4210b08.jpeg',0,0,0,0,0,'0',0,'15451236544','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512618505,1,1512633168,0,NULL,0,''),(66,0,2,'test_bind','testbind','tb','','1','',0,0,0,0,0,'0',0,'13124223123','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512713010,1,1512713010,0,NULL,0,''),(67,0,2,'test_bindm','testbindm','tb','','1','',0,0,0,0,0,'0',0,'15334234248','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512713180,1,1512713180,0,NULL,0,''),(68,0,2,'test_mm','testmm','tm','','1','',0,0,0,0,0,'0',0,'13123232133','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512713395,1,1512713395,0,NULL,0,''),(69,0,2,'lisi22','lisi22','l','','1','',0,0,0,0,0,'0',0,'18316229898','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512713598,1,1512713598,0,NULL,0,''),(70,0,2,'teststs','teststs','t','','1','',1260201600,2009,12,8,0,'0',0,'18943423333','',1,0,'','',0,'',0,'','','',0.00,0.00,0,1512716635,0,1512713702,1,1512720759,0,NULL,0,''),(71,0,2,'独孤求败','duguqiubai','dgqb','topdog','1','',0,0,0,0,0,'0',0,'15684535123','',0,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1512730668,1,1512730668,0,NULL,0,''),(72,0,2,'测试学员11','ceshixueyuan11','csxy1','','1','',0,0,0,0,0,'0',0,'15123423221','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1513058791,1,1513058791,0,NULL,0,''),(73,0,3,'黄鹤','huanghe','hh','yellow bird','1','',1078416000,2004,3,5,0,'0',0,'15486875648','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1513063100,1,1513063100,0,NULL,0,''),(74,0,2,'测试学员12','ceshixueyuan12','csxy1','','1','',0,0,0,0,0,'0',0,'13123231322','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1513072202,1,1513072202,0,NULL,0,''),(75,0,2,'测试学员13','ceshixueyuan13','csxy1','','1','',0,0,0,0,0,'0',0,'18790997660','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1513072878,1,1513072878,0,NULL,0,''),(76,0,2,'测试学员16','ceshixueyuan16','csxy1','','1','',0,0,0,0,0,'0',0,'15234324244','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1513073819,1,1513073819,0,NULL,0,''),(77,0,2,'测试分班1','ceshifenban1','csfb1','','1','',0,0,0,0,0,'0',0,'13124343234','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1513080069,1,1513080069,0,NULL,0,''),(78,0,2,'测试分班2','ceshifenban2','csfb2','','1','http://s10.xiao360.com//x360pstudent_avatar/1/17/12/13/bd0cc536cd4c2e95fab530f7067e1946.jpeg',0,0,0,0,0,'0',0,'13123232123','',1,0,'','',0,'',0,'','',NULL,0.00,0.00,0,0,0,1513081124,1,1513168095,0,NULL,0,''),(79,0,2,'测试分班3','ceshifenban3','csfb3','','1','',886608000,1998,2,5,0,'0',0,'15345435542','( ⊙o⊙ )哇',3,0,'','๑乛㉨乛๑',2,'18617076286',0,'','','0008659832',0.00,0.00,0,0,0,1513133823,1,1513320008,0,NULL,0,''),(80,0,2,'微信学员01','weixinxueyuan01','wxxy0','','0','',1417536000,2014,12,3,0,'0',0,'15345435542','๑乛㉨乛๑',2,10052,'','',0,'',0,'','','',0.00,0.00,0,0,0,1513334523,1,1513408597,0,NULL,0,''),(81,0,2,'微信学员02','weixinxueyuan02','wxxy0','','0','',1480953600,2016,12,6,0,'0',0,'15345435545','小寒',3,10051,'','',0,'',0,'','','',0.00,0.00,0,0,0,1513651453,1,1513652565,0,NULL,0,''),(82,0,2,'学生001','xuesheng001','xs0','','0','',0,0,0,0,0,'0',0,'17768026485','father',2,10063,'','',0,'',0,'','','',640.00,0.00,0,1513821705,0,1513653081,1,1513821705,0,NULL,0,''),(83,0,2,'学生002','xuesheng002','xs0','','0','',0,0,0,0,0,'0',0,'17768026485','',0,10063,'','father',2,'22222222222',10060,'','','',0.00,0.00,0,0,0,1513653100,1,1513669308,0,NULL,0,''),(84,0,2,'学生003','xuesheng003','xs0','','0','',0,0,0,0,0,'0',0,'17768026485','',0,10063,'','',0,'22222222222',10060,'','','',0.00,0.00,0,0,0,1513666928,1,1513669308,0,NULL,0,''),(85,0,2,'学生004','xuesheng004','xs0','','0','',0,0,0,0,0,'0',0,'18128874425','',0,10064,'','',0,'17768026485',10063,'','','',0.00,0.00,0,0,0,1513670836,1,1513671228,0,NULL,0,''),(86,0,2,'学生005','xuesheng005','xs0','','0','',0,0,0,0,0,'0',0,'17768026485','',0,10063,'','',0,'18128874425',10064,'','','',0.00,0.00,0,0,0,1513671148,1,1513671148,0,NULL,0,''),(87,0,2,'刘海','liuhai','lh','','0','',1291132800,0,0,0,0,'0',0,'18617076286','刘大',2,10065,'','',0,'',0,'','','',0.00,0.00,0,0,0,1513732735,1,1513732735,0,NULL,0,'');
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
  `satt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生的考勤id（student_lesson）',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`sa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COMMENT='缺勤记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_absence`
--

LOCK TABLES `x360p_student_absence` WRITE;
/*!40000 ALTER TABLE `x360p_student_absence` DISABLE KEYS */;
INSERT INTO `x360p_student_absence` VALUES (1,0,2,4,1,4,1,7,1,1,0,21,0,10002,0,20171118,1500,1515,1,'',0,0,1510988835,1,1510988835,0,NULL,0),(3,0,2,5,1,6,2,5,1,1,0,9,0,10002,0,20171118,1000,1200,1,'',0,0,1510991982,1,1510991982,0,NULL,0),(4,0,2,6,1,7,0,5,1,1,0,9,0,10002,0,20171118,1000,1200,0,'',0,0,1510991983,1,1510991983,0,NULL,0),(5,0,2,9,5,34,5,6,4,6,0,58,18,10002,0,20171125,800,1000,1,'事假',0,151,1511247847,1,1511248428,1,1511248428,1),(7,0,2,6,1,7,7,8,1,1,0,86,19,10002,0,20171121,1500,1700,1,'事假',0,168,1511255517,1,1511255550,1,1511255550,1),(8,0,2,27,9,38,8,1,4,9,0,87,26,10004,0,20171128,1900,2130,1,'事假',0,185,1511340091,1,1511341458,1,1511341458,1),(9,0,2,27,9,38,0,2,4,9,0,88,35,10004,0,20171129,1900,2130,0,'test',0,203,1511352708,1,1511352708,0,NULL,0),(10,0,2,30,9,43,9,2,4,11,0,104,36,1,0,20171130,1900,2130,1,'事假',0,205,1511352808,1,1511352824,1,1511352824,1),(11,0,2,30,9,43,10,2,4,11,0,104,36,1,0,20171130,1900,2130,1,'事假',0,206,1511352840,1,1511352878,1,1511352878,1),(12,0,2,29,9,44,11,2,4,11,0,104,37,1,0,20171130,1900,2130,1,'事假',0,207,1511352903,1,1511352945,1,1511352945,1),(13,0,2,30,9,43,12,2,4,11,0,104,37,1,0,20171130,1900,2130,1,'事假',0,208,1511352903,1,1511352903,0,NULL,0),(14,0,2,36,9,47,13,0,0,13,0,0,45,1,10005,20171126,800,1000,1,'事假',0,223,1511355603,1,1511355603,0,NULL,0),(15,0,2,7,1,8,14,8,1,1,0,86,48,10002,0,20171121,1500,1700,1,'事假',0,243,1511573589,1,1511573589,0,NULL,0),(16,0,2,4,1,4,0,8,1,1,0,86,48,10002,0,20171121,1500,1700,0,'hgk',0,246,1511573590,1,1511573590,0,NULL,0),(17,0,2,9,5,34,15,13,4,6,0,69,56,10015,0,20171203,800,1000,1,'病假',0,307,1512376794,1,1512376890,1,1512376890,1),(18,0,2,1,5,29,16,13,4,6,0,69,57,10009,0,20171203,800,1000,1,'事假',0,310,1512377269,1,1512381337,1,1512381337,1),(20,0,2,43,9,68,17,6,4,11,0,108,58,10002,0,20171203,1030,1230,1,'病假',0,320,1512377421,1,1512377422,1,1512377422,1),(21,0,2,9,5,34,0,13,4,6,0,69,59,10006,0,20171203,800,1000,0,'ea',0,328,1512381378,1,1512381378,0,NULL,0),(22,0,2,49,5,80,18,13,4,6,0,69,59,10006,0,20171203,800,1000,1,'病假',0,329,1512381378,1,1513421286,1,1513421286,1),(23,0,2,20,5,24,0,19,4,6,0,75,63,10004,0,20171208,1445,1545,0,'',0,345,1512716635,1,1512716635,0,NULL,0),(24,0,2,3,5,31,0,19,4,6,0,75,63,10004,0,20171208,1445,1545,0,'',0,348,1512716637,1,1512716637,0,NULL,0),(25,0,2,9,5,34,0,19,4,6,0,75,63,10004,0,20171208,1445,1545,0,'',0,351,1512716638,1,1512716638,0,NULL,0),(26,0,2,49,5,80,0,19,4,6,0,75,63,10004,0,20171208,1445,1545,0,'',0,352,1512716638,1,1512716638,0,NULL,0),(27,0,2,7,3,83,0,0,0,0,2,0,0,10003,0,20171208,1900,2130,0,'',0,356,1512725205,1,1513067973,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=371 DEFAULT CHARSET=utf8mb4 COMMENT='考勤记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_attendance`
--

LOCK TABLES `x360p_student_attendance` WRITE;
/*!40000 ALTER TABLE `x360p_student_attendance` DISABLE KEYS */;
INSERT INTO `x360p_student_attendance` VALUES (1,0,2,1,1,5,1,1,1,0,9,0,10002,0,20171118,1000,1200,1510972694,0,1,1,0,0,0,1,'','',1510972694,1,1510972694,0,NULL,0),(2,0,2,2,1,5,1,1,2,0,9,0,10002,0,20171118,1000,1200,1510972710,0,1,1,0,0,0,1,'','',1510972710,1,1510972710,0,NULL,0),(3,0,2,8,1,5,1,1,5,0,9,0,10002,0,20171118,1000,1200,1510973477,0,1,1,0,0,0,1,'','',1510973477,1,1510973477,0,NULL,0),(4,0,2,9,1,5,1,1,9,0,9,0,10002,0,20171118,1000,1200,1510973564,0,1,1,0,0,0,1,'','',1510973564,1,1510973564,0,NULL,0),(5,0,2,3,1,5,1,1,3,0,9,0,10002,0,20171118,1000,1200,1510973644,0,1,1,0,0,0,1,'','',1510973644,1,1510973644,0,NULL,0),(6,0,2,5,1,7,1,1,6,0,21,0,10002,10002,20171118,1500,1515,0,0,0,1,0,0,0,1,'','',1510988833,1,1510990849,1,1510990849,1),(7,0,2,6,1,7,1,1,7,0,21,0,10002,10002,20171118,1500,1515,0,0,0,1,0,0,0,1,'','',1510988833,1,1510990849,1,1510990849,1),(8,0,2,7,1,7,1,1,8,0,21,0,10002,10002,20171118,1500,1515,0,0,0,1,0,0,0,1,'','',1510988834,1,1510990849,1,1510990849,1),(9,0,2,8,1,7,1,1,5,0,21,0,10002,10002,20171118,1500,1515,0,0,0,1,0,0,0,1,'','',1510988834,1,1510990849,1,1510990849,1),(10,0,2,9,1,7,1,1,9,0,21,0,10002,10002,20171118,1500,1515,0,0,0,1,0,0,0,1,'','',1510988834,1,1510990849,1,1510990849,1),(11,0,2,4,1,7,1,1,4,0,21,0,10002,10002,20171118,1500,1515,0,0,0,0,0,1,0,0,'','病假',1510988835,1,1510990849,1,1510990849,1),(12,0,2,3,1,7,1,1,3,0,21,0,10002,10002,20171118,1500,1515,0,0,0,0,0,0,0,0,'','这小子翘课了',1510988835,1,1510990849,1,1510990849,1),(13,0,2,2,1,7,1,1,2,0,21,0,10002,10002,20171118,1500,1515,0,0,0,1,0,0,0,1,'','',1510988836,1,1510990849,1,1510990849,1),(14,0,2,1,1,7,1,1,1,0,21,0,10002,10002,20171118,1500,1515,0,0,0,1,0,0,0,1,'','',1510988836,1,1510990849,1,1510990849,1),(15,0,2,5,1,8,1,1,6,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990864,1,1510990866,1,1510990866,1),(16,0,2,6,1,8,1,1,7,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990865,1,1510990867,1,1510990867,1),(17,0,2,7,1,8,1,1,8,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990865,1,1510990868,1,1510990868,1),(18,0,2,8,1,8,1,1,5,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990866,1,1510990869,1,1510990869,1),(19,0,2,5,1,8,1,1,6,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990866,1,1510990867,1,1510990867,1),(20,0,2,9,1,8,1,1,9,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990867,1,1510990870,1,1510990870,1),(21,0,2,6,1,8,1,1,7,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990867,1,1510990868,1,1510990868,1),(22,0,2,4,1,8,1,1,4,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990867,1,1510990871,1,1510990871,1),(23,0,2,5,1,8,1,1,6,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990867,1,1510990868,1,1510990868,1),(24,0,2,5,1,8,1,1,6,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990868,1,1510990868,1,1510990868,1),(25,0,2,3,1,8,1,1,3,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990868,1,1510990873,1,1510990873,1),(26,0,2,5,1,8,1,1,6,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990868,1,1510990869,1,1510990869,1),(27,0,2,7,1,8,1,1,8,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990868,1,1510990869,1,1510990869,1),(28,0,2,6,1,8,1,1,7,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990868,1,1510990869,1,1510990869,1),(29,0,2,5,1,8,1,1,6,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990868,1,1510990868,0,NULL,0),(30,0,2,5,1,8,1,1,6,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990869,1,1510990869,0,NULL,0),(31,0,2,2,1,8,1,1,2,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990869,1,1510990874,1,1510990874,1),(32,0,2,8,1,8,1,1,5,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990869,1,1510990871,1,1510990871,1),(33,0,2,6,1,8,1,1,7,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990870,1,1510990870,0,NULL,0),(34,0,2,7,1,8,1,1,8,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990870,1,1510990870,1,1510990870,1),(35,0,2,1,1,8,1,1,1,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990870,1,1510990875,1,1510990875,1),(36,0,2,9,1,8,1,1,9,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990871,1,1510990872,1,1510990872,1),(37,0,2,7,1,8,1,1,8,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990871,1,1510990871,0,NULL,0),(38,0,2,8,1,8,1,1,5,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990871,1,1510990872,1,1510990872,1),(39,0,2,4,1,8,1,1,4,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990872,1,1510990873,1,1510990873,1),(40,0,2,8,1,8,1,1,5,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990872,1,1510990872,0,NULL,0),(41,0,2,9,1,8,1,1,9,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990872,1,1510990873,1,1510990873,1),(42,0,2,3,1,8,1,1,3,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990873,1,1510990874,1,1510990874,1),(43,0,2,9,1,8,1,1,9,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990873,1,1510990873,0,NULL,0),(44,0,2,4,1,8,1,1,4,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990873,1,1510990874,1,1510990874,1),(45,0,2,2,1,8,1,1,2,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990874,1,1510990875,1,1510990875,1),(46,0,2,4,1,8,1,1,4,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990874,1,1510990874,0,NULL,0),(47,0,2,3,1,8,1,1,3,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990875,1,1510990875,1,1510990875,1),(48,0,2,1,1,8,1,1,1,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990875,1,1510990876,1,1510990876,1),(49,0,2,3,1,8,1,1,3,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990876,1,1510990876,0,NULL,0),(50,0,2,2,1,8,1,1,2,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990876,1,1510990876,1,1510990876,1),(51,0,2,2,1,8,1,1,2,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990877,1,1510990877,0,NULL,0),(52,0,2,1,1,8,1,1,1,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990877,1,1510990877,1,1510990877,1),(53,0,2,1,1,8,1,1,1,0,22,0,10002,10002,20171118,1530,1545,0,0,0,1,0,0,0,1,'','',1510990878,1,1510990878,0,NULL,0),(54,0,2,5,1,5,1,1,6,0,9,0,10002,0,20171118,1000,1200,0,0,0,0,0,1,0,0,'','病假',1510991982,1,1510991982,0,NULL,0),(55,0,2,6,1,5,1,1,7,0,9,0,10002,0,20171118,1000,1200,0,0,0,0,0,0,0,0,'','缺勤了',1510991982,1,1510991982,0,NULL,0),(56,0,2,7,1,5,1,1,8,0,9,0,10002,0,20171118,1000,1200,0,0,0,1,0,0,0,1,'','',1510991983,1,1510991983,0,NULL,0),(57,0,2,4,1,5,1,1,4,0,9,0,10002,0,20171118,1000,1200,0,0,0,1,0,0,0,1,'','',1510991983,1,1510991983,0,NULL,0),(58,0,2,20,5,3,4,6,24,0,54,7,10002,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511236430,1,1511236430,0,NULL,0),(59,0,2,1,5,3,4,6,29,0,54,7,10002,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511236430,1,1511236430,0,NULL,0),(60,0,2,2,5,3,4,6,30,0,54,7,10002,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511236431,1,1511236431,0,NULL,0),(61,0,2,3,5,3,4,6,31,0,54,7,10002,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511236431,1,1511236431,0,NULL,0),(62,0,2,4,5,3,4,6,32,0,54,7,10002,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511236432,1,1511236432,0,NULL,0),(63,0,2,8,5,3,4,6,33,0,54,7,10002,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511236432,1,1511236432,0,NULL,0),(64,0,2,9,5,3,4,6,34,0,54,7,10002,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511236432,1,1511236432,0,NULL,0),(65,0,2,5,1,4,1,1,6,0,5,8,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511237289,1,1511237289,0,NULL,0),(66,0,2,6,1,4,1,1,7,0,5,8,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511237289,1,1511237289,0,NULL,0),(67,0,2,7,1,4,1,1,8,0,5,8,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511237289,1,1511237289,0,NULL,0),(68,0,2,8,1,4,1,1,5,0,5,8,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511237290,1,1511237290,0,NULL,0),(69,0,2,9,1,4,1,1,9,0,5,8,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511237290,1,1511237290,0,NULL,0),(70,0,2,4,1,4,1,1,4,0,5,8,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511237290,1,1511237290,0,NULL,0),(71,0,2,3,1,4,1,1,3,0,5,8,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511237291,1,1511237291,0,NULL,0),(72,0,2,2,1,4,1,1,2,0,5,8,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511237291,1,1511237291,0,NULL,0),(73,0,2,20,5,30,4,6,24,0,85,9,10002,10003,20171120,1230,1300,0,0,0,1,0,0,0,1,'','',1511237790,1,1511237790,0,NULL,0),(74,0,2,1,5,30,4,6,29,0,85,9,10002,10003,20171120,1230,1300,0,0,0,1,0,0,0,1,'','',1511237790,1,1511237790,0,NULL,0),(75,0,2,2,5,30,4,6,30,0,85,9,10002,10003,20171120,1230,1300,0,0,0,1,0,0,0,1,'','',1511237791,1,1511237791,0,NULL,0),(76,0,2,3,5,30,4,6,31,0,85,9,10002,10003,20171120,1230,1300,0,0,0,1,0,0,0,1,'','',1511237791,1,1511237791,0,NULL,0),(77,0,2,4,5,30,4,6,32,0,85,9,10002,10003,20171120,1230,1300,0,0,0,1,0,0,0,1,'','',1511237792,1,1511237792,0,NULL,0),(78,0,2,8,5,30,4,6,33,0,85,9,10002,10003,20171120,1230,1300,0,0,0,1,0,0,0,1,'','',1511237792,1,1511237792,0,NULL,0),(79,0,2,9,5,30,4,6,34,0,85,9,10002,10003,20171120,1230,1300,0,0,0,1,0,0,0,1,'','',1511237793,1,1511237793,0,NULL,0),(80,0,2,5,1,5,1,1,6,0,6,10,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238107,1,1511238107,0,NULL,0),(81,0,2,6,1,5,1,1,7,0,6,10,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238107,1,1511238107,0,NULL,0),(82,0,2,7,1,5,1,1,8,0,6,10,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238107,1,1511238107,0,NULL,0),(83,0,2,8,1,5,1,1,5,0,6,10,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238108,1,1511238108,0,NULL,0),(84,0,2,9,1,5,1,1,9,0,6,10,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238108,1,1511238108,0,NULL,0),(85,0,2,4,1,5,1,1,4,0,6,10,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238109,1,1511238109,0,NULL,0),(86,0,2,3,1,5,1,1,3,0,6,10,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238109,1,1511238109,0,NULL,0),(87,0,2,2,1,5,1,1,2,0,6,10,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238110,1,1511238110,0,NULL,0),(88,0,2,20,5,7,4,6,24,0,59,11,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238502,1,1511238502,0,NULL,0),(89,0,2,1,5,7,4,6,29,0,59,11,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238502,1,1511238502,0,NULL,0),(90,0,2,2,5,7,4,6,30,0,59,11,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238503,1,1511238503,0,NULL,0),(91,0,2,3,5,7,4,6,31,0,59,11,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238503,1,1511238503,0,NULL,0),(92,0,2,4,5,7,4,6,32,0,59,11,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238504,1,1511238504,0,NULL,0),(93,0,2,8,5,7,4,6,33,0,59,11,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238504,1,1511238504,0,NULL,0),(94,0,2,9,5,7,4,6,34,0,59,11,10002,0,20171125,1030,1230,0,0,0,1,0,0,0,1,'','',1511238505,1,1511238505,0,NULL,0),(96,0,2,20,5,1,4,6,24,0,84,13,10003,10003,20171120,1200,1215,0,0,0,1,0,0,0,1,'','',1511244986,1,1511244986,0,NULL,0),(97,0,2,1,5,1,4,6,29,0,84,13,10003,10003,20171120,1200,1215,0,0,0,1,0,0,0,1,'','',1511244986,1,1511244986,0,NULL,0),(98,0,2,2,5,1,4,6,30,0,84,13,10003,10003,20171120,1200,1215,0,0,0,1,0,0,0,1,'','',1511244987,1,1511244987,0,NULL,0),(99,0,2,3,5,1,4,6,31,0,84,13,10003,10003,20171120,1200,1215,0,0,0,1,0,0,0,1,'','',1511244987,1,1511244987,0,NULL,0),(100,0,2,4,5,1,4,6,32,0,84,13,10003,10003,20171120,1200,1215,0,0,0,1,0,0,0,1,'','',1511244988,1,1511244988,0,NULL,0),(101,0,2,8,5,1,4,6,33,0,84,13,10003,10003,20171120,1200,1215,0,0,0,1,0,0,0,1,'','',1511244988,1,1511244988,0,NULL,0),(102,0,2,9,5,1,4,6,34,0,84,13,10003,10003,20171120,1200,1215,0,0,0,1,0,0,0,1,'','',1511244989,1,1511244989,0,NULL,0),(104,0,2,20,5,5,4,6,24,0,56,14,10002,0,20171123,1900,2130,0,0,0,1,0,0,0,1,'','',1511245478,1,1511245478,0,NULL,0),(105,0,2,1,5,5,4,6,29,0,56,14,10002,0,20171123,1900,2130,0,0,0,1,0,0,0,1,'','',1511245478,1,1511245478,0,NULL,0),(106,0,2,2,5,5,4,6,30,0,56,14,10002,0,20171123,1900,2130,0,0,0,1,0,0,0,1,'','',1511245478,1,1511245478,0,NULL,0),(107,0,2,3,5,5,4,6,31,0,56,14,10002,0,20171123,1900,2130,0,0,0,1,0,0,0,1,'','',1511245479,1,1511245479,0,NULL,0),(108,0,2,4,5,5,4,6,32,0,56,14,10002,0,20171123,1900,2130,0,0,0,1,0,0,0,1,'','',1511245479,1,1511245479,0,NULL,0),(109,0,2,8,5,5,4,6,33,0,56,14,10002,0,20171123,1900,2130,0,0,0,1,0,0,0,1,'','',1511245480,1,1511245480,0,NULL,0),(110,0,2,9,5,5,4,6,34,0,56,14,10002,0,20171123,1900,2130,0,0,0,1,0,0,0,1,'','',1511245480,1,1511245480,0,NULL,0),(115,0,2,20,5,6,4,6,24,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511246713,1,1511247402,1,1511247402,1),(116,0,2,1,5,6,4,6,29,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511246713,1,1511247402,1,1511247402,1),(117,0,2,2,5,6,4,6,30,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511246714,1,1511247403,1,1511247403,1),(118,0,2,3,5,6,4,6,31,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511246714,1,1511247404,1,1511247404,1),(119,0,2,4,5,6,4,6,32,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511246715,1,1511247404,1,1511247404,1),(120,0,2,8,5,6,4,6,33,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511246715,1,1511247405,1,1511247405,1),(121,0,2,9,5,6,4,6,34,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511246716,1,1511247406,1,1511247406,1),(124,0,2,20,5,6,4,6,24,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247402,1,1511247744,1,1511247744,1),(125,0,2,1,5,6,4,6,29,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247402,1,1511247745,1,1511247745,1),(126,0,2,2,5,6,4,6,30,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247403,1,1511247745,1,1511247745,1),(127,0,2,3,5,6,4,6,31,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247404,1,1511247746,1,1511247746,1),(128,0,2,4,5,6,4,6,32,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247405,1,1511247747,1,1511247747,1),(129,0,2,8,5,6,4,6,33,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247406,1,1511247748,1,1511247748,1),(130,0,2,9,5,6,4,6,34,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247406,1,1511247749,1,1511247749,1),(131,0,2,20,5,6,4,6,24,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247744,1,1511247815,1,1511247815,1),(132,0,2,1,5,6,4,6,29,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247745,1,1511247815,1,1511247815,1),(133,0,2,2,5,6,4,6,30,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247746,1,1511247816,1,1511247816,1),(134,0,2,3,5,6,4,6,31,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247747,1,1511247817,1,1511247817,1),(135,0,2,4,5,6,4,6,32,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247747,1,1511247818,1,1511247818,1),(136,0,2,8,5,6,4,6,33,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247748,1,1511247819,1,1511247819,1),(137,0,2,9,5,6,4,6,34,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247749,1,1511247820,1,1511247820,1),(138,0,2,20,5,6,4,6,24,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247815,1,1511247842,1,1511247842,1),(139,0,2,1,5,6,4,6,29,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247816,1,1511247843,1,1511247843,1),(140,0,2,2,5,6,4,6,30,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247816,1,1511247843,1,1511247843,1),(141,0,2,3,5,6,4,6,31,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247817,1,1511247844,1,1511247844,1),(142,0,2,4,5,6,4,6,32,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247818,1,1511247845,1,1511247845,1),(143,0,2,8,5,6,4,6,33,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247819,1,1511247846,1,1511247846,1),(144,0,2,9,5,6,4,6,34,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247820,1,1511247847,1,1511247847,1),(145,0,2,20,5,6,4,6,24,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247842,1,1511247842,0,NULL,0),(146,0,2,1,5,6,4,6,29,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247843,1,1511247843,0,NULL,0),(147,0,2,2,5,6,4,6,30,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247844,1,1511247844,0,NULL,0),(148,0,2,3,5,6,4,6,31,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247845,1,1511249757,1,1511249757,1),(149,0,2,4,5,6,4,6,32,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247845,1,1511247845,0,NULL,0),(150,0,2,8,5,6,4,6,33,0,58,18,10002,0,20171125,800,1000,0,0,0,1,0,0,0,1,'','',1511247846,1,1511248449,1,1511248449,1),(151,0,2,9,5,6,4,6,34,0,58,18,10002,0,20171125,800,1000,0,0,0,0,0,1,0,0,'','事假',1511247847,1,1511248428,1,1511248428,1),(152,0,2,9,1,8,1,1,9,0,86,19,10002,0,20171121,1500,1700,1511249484,0,1,1,0,0,0,1,'','',1511249484,1,1511249712,1,1511249712,1),(153,0,2,9,1,8,1,1,9,0,86,19,10002,0,20171121,1500,1700,1511249881,0,1,1,0,0,0,1,'','',1511249881,1,1511249977,1,1511249977,1),(154,0,2,8,1,8,1,1,5,0,86,19,10002,0,20171121,1500,1700,1511249922,0,1,1,0,0,0,1,'','',1511249922,1,1511249973,1,1511249973,1),(155,0,2,9,1,8,1,1,9,0,86,19,10002,0,20171121,1500,1700,1511250001,0,1,1,0,0,0,1,'','',1511250001,1,1511255550,1,1511255550,1),(156,0,2,8,1,8,1,1,5,0,86,19,10002,0,20171121,1500,1700,1511250005,0,1,1,0,0,0,1,'','',1511250005,1,1511255550,1,1511255550,1),(165,0,2,12,4,0,0,0,12,1,0,0,10002,0,20171121,1900,2130,0,0,3,1,0,0,0,0,'','',1511253506,1,1511253506,0,NULL,0),(166,0,2,23,2,0,0,0,27,1,0,0,10003,0,20171121,1900,2130,0,0,3,1,0,0,0,1,'','',1511253544,1,1511253653,1,1511253653,1),(167,0,2,5,1,8,1,1,6,0,86,19,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511255516,1,1511255550,1,1511255550,1),(168,0,2,6,1,8,1,1,7,0,86,19,10002,0,20171121,1500,1700,0,0,0,0,0,1,0,1,'','事假',1511255517,1,1511255550,1,1511255550,1),(169,0,2,7,1,8,1,1,8,0,86,19,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511255517,1,1511255550,1,1511255550,1),(170,0,2,4,1,8,1,1,4,0,86,19,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511255518,1,1511255550,1,1511255550,1),(171,0,2,3,1,8,1,1,3,0,86,19,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511255518,1,1511255550,1,1511255550,1),(172,0,2,2,1,8,1,1,2,0,86,19,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511255519,1,1511255550,1,1511255550,1),(173,0,2,1,1,8,1,1,1,0,86,19,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511255519,1,1511255550,1,1511255550,1),(174,0,2,26,9,1,4,9,37,0,87,20,10004,0,20171128,1900,2130,0,0,0,1,0,0,0,1,'','',1511258165,1,1511259566,1,1511259566,1),(175,0,2,27,9,1,4,9,38,0,87,20,10004,0,20171128,1900,2130,0,0,0,1,0,0,0,1,'','',1511258165,1,1511265288,1,1511265288,1),(176,0,2,26,9,1,4,9,37,0,87,20,10004,0,20171128,1900,2130,0,0,0,1,0,0,0,1,'','',1511264839,1,1511265288,1,1511265288,1),(177,0,2,17,4,0,0,0,17,1,0,0,10003,0,20171121,1900,2130,0,0,3,1,0,0,0,1,'','',1511266483,1,1511266483,0,NULL,0),(178,0,2,19,6,0,4,0,22,1,0,0,10005,0,20171122,1900,2130,0,0,3,1,0,0,0,1,'','',1511316994,1,1511317200,1,1511317200,1),(179,0,2,23,2,0,0,0,27,1,0,0,10005,0,20171122,1900,2130,0,0,3,1,0,0,0,1,'','',1511322572,1,1511322572,0,NULL,0),(180,0,2,19,6,0,4,0,22,1,0,0,10005,0,20171122,1900,2130,0,0,3,1,0,0,0,1,'','',1511323120,1,1511323120,0,NULL,0),(181,0,2,18,2,0,0,0,20,1,0,0,10005,0,20171122,1900,2130,0,0,3,1,0,0,0,1,'','',1511323225,1,1511323225,0,NULL,0),(182,0,2,18,2,0,0,0,20,1,0,0,10005,0,20171121,1900,2130,0,0,3,1,0,0,0,1,'','',1511323354,1,1511323354,0,NULL,0),(183,0,2,31,4,0,0,0,40,1,0,0,10005,0,20171122,1900,2130,0,0,3,1,0,0,0,1,'','',1511323779,1,1511323973,1,1511323973,1),(184,0,2,26,9,1,4,9,37,0,87,26,10004,0,20171128,1900,2130,0,0,0,1,0,0,0,1,'','',1511340091,1,1511341467,1,1511341467,1),(185,0,2,27,9,1,4,9,38,0,87,26,10004,0,20171128,1900,2130,0,0,0,0,0,1,0,0,'','事假',1511340091,1,1511341458,1,1511341458,1),(186,0,2,26,9,7,4,9,37,0,93,27,10004,0,20171203,1030,1230,0,0,0,1,0,0,0,1,'','',1511340119,1,1511340419,1,1511340419,1),(187,0,2,27,9,7,4,9,38,0,93,27,10004,0,20171203,1030,1230,0,0,0,1,0,0,0,0,'','',1511340119,1,1511341463,1,1511341463,1),(188,0,2,26,9,7,4,9,37,0,93,27,10004,0,20171203,1030,1230,0,0,0,1,0,0,0,1,'','',1511343963,1,1511344568,1,1511344568,1),(189,0,2,27,9,7,4,9,38,0,93,27,10004,0,20171203,1030,1230,0,0,0,1,0,0,0,1,'','',1511343963,1,1511344609,1,1511344609,1),(190,0,2,26,9,1,4,9,37,0,87,26,10004,0,20171128,1900,2130,0,0,0,1,0,0,0,1,'','',1511344861,1,1511344880,1,1511344880,1),(191,0,2,27,9,1,4,9,38,0,87,26,10004,0,20171128,1900,2130,0,0,0,1,0,0,0,1,'','',1511344862,1,1511344862,0,NULL,0),(192,0,2,29,9,1,4,11,44,0,103,28,1,0,20171129,1900,2130,0,0,0,1,0,0,0,1,'','',1511345504,1,1511345504,0,NULL,0),(193,0,2,30,9,1,4,11,43,0,103,28,1,0,20171129,1900,2130,0,0,0,1,0,0,0,1,'','',1511345505,1,1511345505,0,NULL,0),(194,0,2,34,5,1,4,12,45,0,109,30,10003,10003,20171122,1900,2130,0,0,0,1,0,0,0,1,'','',1511346471,1,1511346471,0,NULL,0),(197,0,2,26,9,2,4,9,37,0,88,33,10004,0,20171129,1900,2130,0,0,0,1,0,0,0,1,'','',1511346966,1,1511347042,1,1511347042,1),(198,0,2,27,9,2,4,9,38,0,88,33,10004,0,20171129,1900,2130,0,0,0,1,0,0,0,1,'','',1511346966,1,1511347103,1,1511347103,1),(199,0,2,26,9,1,4,9,37,0,87,26,10004,0,20171128,1900,2130,0,0,0,1,0,0,0,1,'','',1511347122,1,1511347122,0,NULL,0),(200,0,2,29,9,5,4,11,44,0,107,34,1,0,20171202,1030,1230,0,0,0,1,0,0,0,1,'','',1511352652,1,1511352668,1,1511352668,1),(201,0,2,30,9,5,4,11,43,0,107,34,1,0,20171202,1030,1230,0,0,0,1,0,0,0,1,'','',1511352652,1,1511352663,1,1511352663,1),(202,0,2,26,9,2,4,9,37,0,88,35,10004,0,20171129,1900,2130,0,0,0,1,0,0,0,1,'','',1511352707,1,1511355671,1,1511355671,1),(203,0,2,27,9,2,4,9,38,0,88,35,10004,0,20171129,1900,2130,0,0,0,0,0,0,0,0,'','test',1511352707,1,1511352707,0,NULL,0),(204,0,2,29,9,2,4,11,44,0,104,36,1,0,20171130,1900,2130,0,0,0,1,0,0,0,1,'','',1511352808,1,1511352878,1,1511352878,1),(205,0,2,30,9,2,4,11,43,0,104,36,1,0,20171130,1900,2130,0,0,0,0,0,1,0,1,'','事假',1511352808,1,1511352824,1,1511352824,1),(206,0,2,30,9,2,4,11,43,0,104,36,1,0,20171130,1900,2130,0,0,0,0,0,1,0,1,'','事假',1511352840,1,1511352878,1,1511352878,1),(207,0,2,29,9,2,4,11,44,0,104,37,1,0,20171130,1900,2130,0,0,0,0,0,1,0,0,'','事假',1511352903,1,1511352945,1,1511352945,1),(208,0,2,30,9,2,4,11,43,0,104,37,1,0,20171130,1900,2130,0,0,0,0,0,1,0,1,'','事假',1511352903,1,1511352903,0,NULL,0),(209,0,2,29,9,2,4,11,44,0,104,37,1,0,20171130,1900,2130,0,0,0,1,0,0,0,1,'','',1511352972,1,1511352972,0,NULL,0),(210,0,2,29,9,0,0,11,44,0,0,39,1,1,20171122,1900,2130,0,0,3,1,0,0,0,1,'','',1511353354,1,1511353354,1,1511353354,1),(211,0,2,29,9,0,0,11,44,0,0,39,1,1,20171122,1900,2130,0,0,3,1,0,0,0,1,'','',1511353354,1,1511353354,0,NULL,0),(212,0,2,29,9,0,0,11,44,0,0,40,1,1,20171123,1900,2130,0,0,3,1,0,0,0,1,'','',1511353956,1,1511353956,1,1511353956,1),(213,0,2,29,9,0,0,11,44,0,0,40,1,1,20171123,1900,2130,0,0,3,1,0,0,0,1,'','',1511353956,1,1511353956,0,NULL,0),(214,0,2,29,9,0,0,11,44,0,0,41,1,1,20171124,1900,2130,0,0,3,1,0,0,0,1,'','',1511354045,1,1511354045,1,1511354045,1),(215,0,2,29,9,0,0,11,44,0,0,41,1,1,20171124,1900,2130,0,0,3,1,0,0,0,1,'','',1511354045,1,1511354045,0,NULL,0),(217,0,2,36,9,0,0,13,47,0,0,43,1,10005,20171122,1900,2130,0,0,3,1,0,0,0,1,'','',1511354582,1,1511354582,1,1511354582,1),(218,0,2,36,9,0,0,13,47,0,0,43,1,10005,20171122,1900,2130,0,0,3,1,0,0,0,1,'','',1511354582,1,1511355352,1,1511355352,1),(219,0,2,36,9,0,0,13,47,0,0,43,1,10005,20171122,1900,2130,0,0,3,1,0,0,0,0,'','',1511355352,1,1511355352,0,NULL,0),(220,0,2,35,9,0,0,13,46,0,0,43,1,10005,20171122,1900,2130,0,0,3,1,0,0,0,0,'','',1511355352,1,1511355352,0,NULL,0),(221,0,2,36,9,0,0,13,47,0,0,44,10005,10005,20171125,800,1000,0,0,3,1,0,0,0,1,'','',1511355394,1,1511355394,0,NULL,0),(222,0,2,35,9,0,0,13,46,0,0,44,10005,10005,20171125,800,1000,0,0,3,1,0,0,0,1,'','',1511355394,1,1511355394,0,NULL,0),(223,0,2,36,9,0,0,13,47,0,0,45,1,10005,20171126,800,1000,0,0,3,0,0,1,0,0,'','事假',1511355603,1,1511355603,0,NULL,0),(224,0,2,35,9,0,0,13,46,0,0,45,1,10005,20171126,800,1000,0,0,3,1,0,0,0,1,'','',1511355603,1,1511355603,0,NULL,0),(225,0,2,20,5,30,4,6,24,0,113,46,10004,0,20171124,1900,2130,0,0,0,1,0,0,0,1,'','',1511432684,1,1511432684,0,NULL,0),(226,0,2,1,5,30,4,6,29,0,113,46,10004,0,20171124,1900,2130,0,0,0,1,0,0,0,1,'','',1511432685,1,1511432685,0,NULL,0),(227,0,2,2,5,30,4,6,30,0,113,46,10004,0,20171124,1900,2130,0,0,0,1,0,0,0,1,'','',1511432685,1,1511432685,0,NULL,0),(228,0,2,3,5,30,4,6,31,0,113,46,10004,0,20171124,1900,2130,0,0,0,1,0,0,0,1,'','',1511432686,1,1511432686,0,NULL,0),(229,0,2,4,5,30,4,6,32,0,113,46,10004,0,20171124,1900,2130,0,0,0,1,0,0,0,1,'','',1511432686,1,1511432686,0,NULL,0),(230,0,2,8,5,30,4,6,33,0,113,46,10004,0,20171124,1900,2130,0,0,0,1,0,0,0,1,'','',1511432687,1,1511432687,0,NULL,0),(231,0,2,9,5,30,4,6,34,0,113,46,10004,0,20171124,1900,2130,0,0,0,1,0,0,0,1,'','',1511432687,1,1511432687,0,NULL,0),(232,0,2,5,1,9,1,1,6,0,102,47,10004,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511432735,1,1511432735,0,NULL,0),(233,0,2,6,1,9,1,1,7,0,102,47,10004,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511432736,1,1511432736,0,NULL,0),(234,0,2,7,1,9,1,1,8,0,102,47,10004,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511432736,1,1511432736,0,NULL,0),(235,0,2,8,1,9,1,1,5,0,102,47,10004,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511432737,1,1511432737,0,NULL,0),(236,0,2,9,1,9,1,1,9,0,102,47,10004,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511432737,1,1511432737,0,NULL,0),(237,0,2,4,1,9,1,1,4,0,102,47,10004,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511432738,1,1511432738,0,NULL,0),(238,0,2,3,1,9,1,1,3,0,102,47,10004,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511432738,1,1511432738,0,NULL,0),(239,0,2,2,1,9,1,1,2,0,102,47,10004,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511432738,1,1511432738,0,NULL,0),(240,0,2,1,1,9,1,1,1,0,102,47,10004,0,20171121,1900,2130,0,0,0,1,0,0,0,1,'','',1511432739,1,1511432739,0,NULL,0),(241,0,2,5,1,8,1,1,6,0,86,48,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511573588,1,1511573588,0,NULL,0),(242,0,2,6,1,8,1,1,7,0,86,48,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511573588,1,1511573588,0,NULL,0),(243,0,2,7,1,8,1,1,8,0,86,48,10002,0,20171121,1500,1700,0,0,0,0,0,1,0,0,'','事假',1511573589,1,1511573589,0,NULL,0),(244,0,2,8,1,8,1,1,5,0,86,48,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511573589,1,1511573589,0,NULL,0),(245,0,2,9,1,8,1,1,9,0,86,48,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511573590,1,1511573590,0,NULL,0),(246,0,2,4,1,8,1,1,4,0,86,48,10002,0,20171121,1500,1700,0,0,0,0,0,0,0,0,'','hgk',1511573590,1,1511573590,0,NULL,0),(247,0,2,3,1,8,1,1,3,0,86,48,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511573590,1,1511573590,0,NULL,0),(248,0,2,2,1,8,1,1,2,0,86,48,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511573591,1,1511573591,0,NULL,0),(249,0,2,1,1,8,1,1,1,0,86,48,10002,0,20171121,1500,1700,0,0,0,1,0,0,0,1,'','',1511573591,1,1511573591,0,NULL,0),(250,0,2,33,4,0,0,0,42,1,0,0,1,0,20171125,1030,1230,0,0,3,1,0,0,0,1,'','',1511582586,1,1511582586,0,NULL,0),(251,0,2,26,9,3,4,9,37,0,89,49,10004,0,20171130,1900,2130,0,0,0,1,0,0,0,1,'','',1512009291,1,1512371792,1,1512371792,1),(252,0,2,27,9,3,4,9,38,0,89,49,10004,0,20171130,1900,2130,0,0,0,1,0,0,0,1,'','',1512009291,1,1512371792,1,1512371792,1),(253,0,2,20,5,13,4,6,24,0,69,50,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360340,1,1512360949,1,1512360949,1),(254,0,2,1,5,13,4,6,29,0,69,50,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360340,1,1512360949,1,1512360949,1),(255,0,2,2,5,13,4,6,30,0,69,50,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360341,1,1512360949,1,1512360949,1),(256,0,2,3,5,13,4,6,31,0,69,50,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360341,1,1512360949,1,1512360949,1),(257,0,2,4,5,13,4,6,32,0,69,50,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360342,1,1512360949,1,1512360949,1),(258,0,2,8,5,13,4,6,33,0,69,50,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360342,1,1512360949,1,1512360949,1),(259,0,2,9,5,13,4,6,34,0,69,50,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360343,1,1512360949,1,1512360949,1),(260,0,2,49,5,13,4,6,80,0,69,50,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360343,1,1512360949,1,1512360949,1),(261,0,2,20,5,13,4,6,24,0,69,51,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360984,1,1512361647,1,1512361647,1),(262,0,2,1,5,13,4,6,29,0,69,51,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360984,1,1512361647,1,1512361647,1),(263,0,2,2,5,13,4,6,30,0,69,51,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360984,1,1512361647,1,1512361647,1),(264,0,2,3,5,13,4,6,31,0,69,51,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360985,1,1512361647,1,1512361647,1),(265,0,2,4,5,13,4,6,32,0,69,51,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360985,1,1512361647,1,1512361647,1),(266,0,2,8,5,13,4,6,33,0,69,51,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360986,1,1512361647,1,1512361647,1),(267,0,2,9,5,13,4,6,34,0,69,51,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360987,1,1512361647,1,1512361647,1),(268,0,2,49,5,13,4,6,80,0,69,51,10003,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512360987,1,1512361648,1,1512361648,1),(269,0,2,20,5,13,4,6,24,0,69,52,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512361714,1,1512367642,1,1512367642,1),(270,0,2,1,5,13,4,6,29,0,69,52,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512361714,1,1512367642,1,1512367642,1),(271,0,2,2,5,13,4,6,30,0,69,52,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512361714,1,1512367642,1,1512367642,1),(272,0,2,3,5,13,4,6,31,0,69,52,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512361715,1,1512367642,1,1512367642,1),(273,0,2,4,5,13,4,6,32,0,69,52,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512361715,1,1512367642,1,1512367642,1),(274,0,2,8,5,13,4,6,33,0,69,52,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512361716,1,1512367642,1,1512367642,1),(275,0,2,9,5,13,4,6,34,0,69,52,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512361716,1,1512367642,1,1512367642,1),(276,0,2,49,5,13,4,6,80,0,69,52,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512361717,1,1512367642,1,1512367642,1),(277,0,2,20,5,13,4,6,24,0,69,53,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512367674,1,1512368050,1,1512368050,1),(278,0,2,1,5,13,4,6,29,0,69,53,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512367674,1,1512368050,1,1512368050,1),(279,0,2,2,5,13,4,6,30,0,69,53,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512367675,1,1512368050,1,1512368050,1),(280,0,2,3,5,13,4,6,31,0,69,53,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512367675,1,1512368050,1,1512368050,1),(281,0,2,4,5,13,4,6,32,0,69,53,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512367676,1,1512368050,1,1512368050,1),(282,0,2,8,5,13,4,6,33,0,69,53,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512367676,1,1512368050,1,1512368050,1),(283,0,2,9,5,13,4,6,34,0,69,53,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512367677,1,1512368050,1,1512368050,1),(284,0,2,49,5,13,4,6,80,0,69,53,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512367678,1,1512368050,1,1512368050,1),(285,0,2,20,5,13,4,6,24,0,69,54,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512368463,1,1512371341,1,1512371341,1),(286,0,2,1,5,13,4,6,29,0,69,54,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512368464,1,1512371341,1,1512371341,1),(287,0,2,2,5,13,4,6,30,0,69,54,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512368464,1,1512371341,1,1512371341,1),(288,0,2,3,5,13,4,6,31,0,69,54,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512368465,1,1512371341,1,1512371341,1),(289,0,2,4,5,13,4,6,32,0,69,54,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512368465,1,1512371341,1,1512371341,1),(290,0,2,8,5,13,4,6,33,0,69,54,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512368466,1,1512371341,1,1512371341,1),(291,0,2,9,5,13,4,6,34,0,69,54,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512368466,1,1512371341,1,1512371341,1),(292,0,2,49,5,13,4,6,80,0,69,54,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512368467,1,1512371211,1,1512371211,1),(293,0,2,20,5,13,4,6,24,0,69,55,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512371561,1,1512376421,1,1512376421,1),(294,0,2,1,5,13,4,6,29,0,69,55,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512371561,1,1512376421,1,1512376421,1),(295,0,2,2,5,13,4,6,30,0,69,55,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512371562,1,1512376421,1,1512376421,1),(296,0,2,3,5,13,4,6,31,0,69,55,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512371562,1,1512376421,1,1512376421,1),(297,0,2,4,5,13,4,6,32,0,69,55,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512371563,1,1512376421,1,1512376421,1),(298,0,2,8,5,13,4,6,33,0,69,55,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512371563,1,1512376421,1,1512376421,1),(299,0,2,9,5,13,4,6,34,0,69,55,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512371564,1,1512376421,1,1512376421,1),(300,0,2,49,5,13,4,6,80,0,69,55,10002,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512371565,1,1512374727,1,1512374727,1),(301,0,2,20,5,13,4,6,24,0,69,56,10015,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512376791,1,1512376890,1,1512376890,1),(302,0,2,1,5,13,4,6,29,0,69,56,10015,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512376791,1,1512376890,1,1512376890,1),(303,0,2,2,5,13,4,6,30,0,69,56,10015,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512376792,1,1512376890,1,1512376890,1),(304,0,2,3,5,13,4,6,31,0,69,56,10015,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512376792,1,1512376890,1,1512376890,1),(305,0,2,4,5,13,4,6,32,0,69,56,10015,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512376793,1,1512376890,1,1512376890,1),(306,0,2,8,5,13,4,6,33,0,69,56,10015,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512376793,1,1512376890,1,1512376890,1),(307,0,2,9,5,13,4,6,34,0,69,56,10015,0,20171203,800,1000,0,0,0,0,0,1,0,1,'','病假',1512376794,1,1512376890,1,1512376890,1),(308,0,2,49,5,13,4,6,80,0,69,56,10015,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512376795,1,1512376890,1,1512376890,1),(309,0,2,20,5,13,4,6,24,0,69,57,10009,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512377269,1,1512381337,1,1512381337,1),(310,0,2,1,5,13,4,6,29,0,69,57,10009,0,20171203,800,1000,0,0,0,0,0,1,0,0,'','事假',1512377269,1,1512381337,1,1512381337,1),(311,0,2,2,5,13,4,6,30,0,69,57,10009,0,20171203,800,1000,0,0,0,0,0,0,0,1,'','',1512377269,1,1512381337,1,1512381337,1),(312,0,2,3,5,13,4,6,31,0,69,57,10009,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512377270,1,1512381337,1,1512381337,1),(313,0,2,4,5,13,4,6,32,0,69,57,10009,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512377270,1,1512381337,1,1512381337,1),(314,0,2,8,5,13,4,6,33,0,69,57,10009,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512377271,1,1512381337,1,1512381337,1),(315,0,2,9,5,13,4,6,34,0,69,57,10009,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512377272,1,1512381337,1,1512381337,1),(316,0,2,49,5,13,4,6,80,0,69,57,10009,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512377272,1,1512381337,1,1512381337,1),(317,0,2,29,9,6,4,11,44,0,108,58,10002,0,20171203,1030,1230,0,0,0,1,0,0,0,1,'','',1512377419,1,1512377419,0,NULL,0),(318,0,2,30,9,6,4,11,43,0,108,58,10002,0,20171203,1030,1230,0,0,0,1,0,0,0,1,'','',1512377420,1,1512377420,0,NULL,0),(319,0,2,43,9,6,4,11,68,0,108,58,10002,0,20171203,1030,1230,0,0,0,1,0,0,0,1,'','',1512377420,1,1512377421,1,1512377421,1),(320,0,2,43,9,6,4,11,68,0,108,58,10002,0,20171203,1030,1230,0,0,0,0,0,1,0,0,'','病假',1512377421,1,1512377422,1,1512377422,1),(321,0,2,43,9,6,4,11,68,0,108,58,10002,0,20171203,1030,1230,0,0,0,1,0,0,0,1,'','',1512377422,1,1512377422,0,NULL,0),(322,0,2,20,5,13,4,6,24,0,69,59,10006,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512381375,1,1512381375,0,NULL,0),(323,0,2,1,5,13,4,6,29,0,69,59,10006,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512381375,1,1512381375,0,NULL,0),(324,0,2,2,5,13,4,6,30,0,69,59,10006,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512381376,1,1512381376,0,NULL,0),(325,0,2,3,5,13,4,6,31,0,69,59,10006,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512381376,1,1512381376,0,NULL,0),(326,0,2,4,5,13,4,6,32,0,69,59,10006,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512381377,1,1512381377,0,NULL,0),(327,0,2,8,5,13,4,6,33,0,69,59,10006,0,20171203,800,1000,0,0,0,1,0,0,0,1,'','',1512381377,1,1512381377,0,NULL,0),(328,0,2,9,5,13,4,6,34,0,69,59,10006,0,20171203,800,1000,0,0,0,0,0,0,0,0,'','ea',1512381378,1,1512381378,0,NULL,0),(329,0,2,49,5,13,4,6,80,0,69,59,10006,0,20171203,800,1000,1512259200,0,0,0,0,1,0,0,'','病假',1512381378,1,1513421286,1,1513421286,1),(330,0,2,43,9,0,0,13,68,0,0,60,10003,10005,20171205,1900,2130,0,0,3,1,0,0,0,1,'','',1512475491,1,1512475801,1,1512475801,1),(331,0,2,36,9,0,0,13,47,0,0,60,10003,10005,20171205,1900,2130,0,0,3,1,0,0,0,1,'','',1512475491,1,1512475801,1,1512475801,1),(332,0,2,35,9,0,0,13,46,0,0,60,10003,10005,20171205,1900,2130,0,0,3,1,0,0,0,1,'','',1512475491,1,1512475801,1,1512475801,1),(333,0,2,43,9,0,0,13,68,0,0,60,10003,10005,20171205,1900,2130,0,0,3,1,0,0,0,1,'','',1512475801,1,1512475801,0,NULL,0),(334,0,2,36,9,0,0,13,47,0,0,60,10003,10005,20171205,1900,2130,0,0,3,1,0,0,0,1,'','',1512475801,1,1512475801,0,NULL,0),(335,0,2,35,9,0,0,13,46,0,0,60,10003,10005,20171205,1900,2130,0,0,3,1,0,0,0,1,'','',1512475801,1,1512475801,0,NULL,0),(336,0,2,43,9,0,0,13,68,0,0,61,10002,10005,20171206,1900,2130,0,0,3,1,0,0,0,1,'','',1512527718,1,1512527744,1,1512527744,1),(337,0,2,36,9,0,0,13,47,0,0,61,10002,10005,20171206,1900,2130,0,0,3,1,0,0,0,1,'','',1512527718,1,1512527744,1,1512527744,1),(338,0,2,35,9,0,0,13,46,0,0,61,10002,10005,20171206,1900,2130,0,0,3,1,0,0,0,1,'','',1512527718,1,1512527744,1,1512527744,1),(339,0,2,43,9,0,0,13,68,0,0,61,10003,10005,20171206,1900,2130,0,0,3,1,0,0,0,1,'','',1512527744,1,1512527744,0,NULL,0),(340,0,2,36,9,0,0,13,47,0,0,61,10003,10005,20171206,1900,2130,0,0,3,1,0,0,0,1,'','',1512527744,1,1512527744,0,NULL,0),(341,0,2,35,9,0,0,13,46,0,0,61,10003,10005,20171206,1900,2130,0,0,3,1,0,0,0,1,'','',1512527744,1,1512527744,0,NULL,0),(342,0,2,43,9,0,0,13,68,0,0,62,10003,10005,20171204,1900,2130,1512385200,0,3,1,0,0,0,1,'','',1512528638,1,1512701622,1,1512701622,1),(343,0,2,36,9,0,0,13,47,0,0,62,10003,10005,20171204,1900,2130,1512385200,0,3,1,0,0,0,1,'','',1512528638,1,1512701622,1,1512701622,1),(344,0,2,35,9,0,0,13,46,0,0,62,10003,10005,20171204,1900,2130,1512385200,0,3,1,0,0,0,1,'','',1512528638,1,1512701622,1,1512701622,1),(345,0,2,20,5,19,4,6,24,0,75,63,10004,0,20171208,1445,1545,1512715500,0,0,0,0,0,0,0,'','',1512716635,1,1512716635,0,NULL,0),(346,0,2,1,5,19,4,6,29,0,75,63,10004,0,20171208,1445,1545,1512715500,0,0,1,0,0,0,1,'','',1512716635,1,1512716635,0,NULL,0),(347,0,2,2,5,19,4,6,30,0,75,63,10004,0,20171208,1445,1545,1512715500,0,0,1,0,0,0,1,'','',1512716636,1,1512716636,0,NULL,0),(348,0,2,3,5,19,4,6,31,0,75,63,10004,0,20171208,1445,1545,1512715500,0,0,0,0,0,0,0,'','',1512716637,1,1512716637,0,NULL,0),(349,0,2,4,5,19,4,6,32,0,75,63,10004,0,20171208,1445,1545,1512715500,0,0,1,0,0,0,1,'','',1512716637,1,1513421737,1,1513421737,1),(350,0,2,8,5,19,4,6,33,0,75,63,10004,0,20171208,1445,1545,1512715500,0,0,1,0,0,0,1,'','',1512716637,1,1512716637,0,NULL,0),(351,0,2,9,5,19,4,6,34,0,75,63,10004,0,20171208,1445,1545,1512715500,0,0,0,0,0,0,0,'','',1512716638,1,1512716638,0,NULL,0),(352,0,2,49,5,19,4,6,80,0,75,63,10004,0,20171208,1445,1545,1512715500,0,0,0,0,0,0,0,'','',1512716638,1,1512716638,0,NULL,0),(353,0,2,70,5,19,4,6,116,0,75,63,10004,0,20171208,1445,1545,1512715500,0,0,1,0,0,0,1,'','',1512716639,1,1513421733,1,1513421733,1),(354,0,2,57,3,0,0,0,101,2,0,0,10003,0,20171208,1900,2130,1512730800,0,3,1,0,0,0,1,'','',1512725205,1,1513421314,1,1513421314,1),(355,0,2,52,3,0,0,0,85,2,0,0,10003,0,20171208,1900,2130,1512730800,0,3,1,0,0,0,1,'','',1512725205,1,1512725205,0,NULL,0),(356,0,2,7,3,0,0,0,83,2,0,0,10003,0,20171208,1900,2130,1512730800,0,3,0,0,0,0,0,'','',1512725205,1,1512725205,0,NULL,0),(357,0,2,49,3,0,0,0,73,2,0,0,10003,0,20171208,1900,2130,1512730800,0,3,0,0,0,0,0,'','',1512725205,1,1513421346,1,1513421346,1),(358,0,2,1,5,25,4,6,29,0,82,64,0,0,20171213,1900,2130,1513168985,0,1,1,0,0,0,1,'','',1513168985,1,1513422394,1,1513422394,1),(366,0,2,82,1,0,0,7,148,0,0,69,10003,10003,20171221,1900,2130,1513854000,0,3,1,0,0,0,1,'','',1513821705,1,1513821705,0,NULL,0),(367,0,2,41,1,0,0,7,63,0,0,69,10003,10003,20171221,1900,2130,1513854000,0,3,1,0,0,0,0,'','',1513821706,1,1513821706,0,NULL,0),(368,0,2,20,1,0,0,7,23,0,0,69,10003,10003,20171221,1900,2130,1513854000,0,3,1,0,0,0,0,'','',1513821706,1,1513821706,0,NULL,0),(369,0,2,15,1,0,0,7,18,0,0,69,10003,10003,20171221,1900,2130,1513854000,0,3,1,0,0,0,0,'','',1513821706,1,1513821706,0,NULL,0),(370,0,2,13,1,0,0,7,19,0,0,69,10003,10003,20171221,1900,2130,1513854000,0,3,1,0,0,0,0,'','',1513821706,1,1513821706,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COMMENT='请假记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_leave`
--

LOCK TABLES `x360p_student_leave` WRITE;
/*!40000 ALTER TABLE `x360p_student_leave` DISABLE KEYS */;
INSERT INTO `x360p_student_leave` VALUES (2,0,2,5,1,1,1,6,0,9,20171118,1000,1200,NULL,0,0,1510991982,1,1510991982,0,NULL,0),(3,0,2,1,1,0,1,1,0,5,20171125,800,1000,'感冒发烧',116,0,1511177696,1,1511177696,0,NULL,0),(4,0,2,1,1,0,1,1,0,6,20171125,1030,1230,'事出有因，',117,0,1511233957,1,1511233957,0,NULL,0),(5,0,2,9,5,4,6,34,0,58,20171125,800,1000,'事假',2,151,1511247847,1,1511248428,1,1511248428,1),(7,0,2,6,1,1,1,7,0,86,20171121,1500,1700,'事假',2,168,1511255517,1,1511255550,1,1511255550,1),(8,0,2,27,9,4,9,38,0,87,20171128,1900,2130,'事假',2,185,1511340091,1,1511341458,1,1511341458,1),(9,0,2,30,9,4,11,43,0,104,20171130,1900,2130,'事假',2,205,1511352808,1,1511352824,1,1511352824,1),(10,0,2,30,9,4,11,43,0,104,20171130,1900,2130,'事假',2,206,1511352840,1,1511352878,1,1511352878,1),(11,0,2,29,9,4,11,44,0,104,20171130,1900,2130,'事假',2,207,1511352903,1,1511352945,1,1511352945,1),(12,0,2,30,9,4,11,43,0,104,20171130,1900,2130,'事假',2,208,1511352903,1,1511352903,0,NULL,0),(13,0,2,36,9,0,13,47,0,0,20171126,800,1000,'事假',2,223,1511355603,1,1511355603,0,NULL,0),(14,0,2,7,1,1,1,8,0,86,20171121,1500,1700,'事假',2,243,1511573589,1,1511573589,0,NULL,0),(15,0,2,9,5,4,6,34,0,69,20171203,800,1000,'病假',1,307,1512376794,1,1512376890,1,1512376890,1),(16,0,2,1,5,4,6,29,0,69,20171203,800,1000,'事假',2,310,1512377269,1,1512381337,1,1512381337,1),(17,0,2,43,9,4,11,68,0,108,20171203,1030,1230,'病假',1,320,1512377421,1,1512377422,1,1512377422,1),(18,0,2,49,5,4,6,80,0,69,20171203,800,1000,'病假',1,329,1512381378,1,1513421286,1,1513421286,1),(19,0,2,79,1,1,2,1,0,12,20171217,800,1000,NULL,0,0,1513735936,10051,1513735936,0,NULL,0),(20,0,2,79,1,1,2,1,0,10,20171203,800,1000,NULL,0,0,1513742069,10051,1513742069,0,NULL,0),(21,0,2,79,1,1,2,1,0,11,20171210,800,1000,NULL,0,0,1513742118,10051,1513742118,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8mb4 COMMENT='学生课程班级表（与order_item关联）';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_lesson`
--

LOCK TABLES `x360p_student_lesson` WRITE;
/*!40000 ALTER TABLE `x360p_student_lesson` DISABLE KEYS */;
INSERT INTO `x360p_student_lesson` VALUES (1,0,2,1,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,1,2,1,0,0,0,4,11,8.00,22.00,1511573588,15,1510971618,1,1511573592,0,NULL,0),(2,0,2,2,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,1,2,1,0,0,0,6,9,12.00,18.00,1511573588,15,1510971695,1,1511573591,0,NULL,0),(3,0,2,3,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,1,2,1,0,0,0,6,9,12.00,18.00,1511573588,15,1510971718,1,1511573591,0,NULL,0),(4,0,2,4,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,1,2,1,0,0,0,5,10,10.00,20.00,1511432735,15,1510971764,1,1511432738,0,NULL,0),(5,0,2,8,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,1,2,1,0,0,0,6,9,12.00,18.00,1511573588,15,1510971975,1,1511573589,0,NULL,0),(6,0,2,5,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,1,2,1,0,0,0,5,10,10.00,20.00,1511573588,15,1510971976,1,1511573588,0,NULL,0),(7,0,2,6,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,1,2,1,0,0,0,5,10,10.00,20.00,1511573588,15,1510972024,1,1511573588,0,NULL,0),(8,0,2,7,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,1,2,1,0,0,0,5,10,10.00,20.00,1511432735,15,1510972042,1,1511432736,0,NULL,0),(9,0,2,9,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,1,2,1,0,0,0,6,9,12.00,18.00,1511573588,15,1510972045,1,1511573590,0,NULL,0),(10,0,2,10,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1510974276,1,1510974276,0,NULL,0),(11,0,2,11,1,0,15.00,0.00,15.00,31.00,0.00,31.00,0,0,0,1,0,0,0,0,15,0.00,31.00,0,15,1510993877,1,1510993877,0,NULL,0),(12,0,2,12,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1510994219,1,1510994219,0,NULL,0),(13,0,2,13,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1510994693,1,1510994693,0,NULL,0),(14,0,2,14,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1510994712,1,1510994712,0,NULL,0),(15,0,2,15,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1510994740,1,1510994740,0,NULL,0),(16,0,2,16,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1510994756,1,1510994756,0,NULL,0),(17,0,2,17,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1510994770,1,1510994770,0,NULL,0),(18,0,2,15,1,0,0.00,0.00,0.00,1.00,0.00,1.00,0,7,2,1,1,0,0,0,0,0.00,1.00,0,0,1510995565,1,1510995565,0,NULL,0),(19,0,2,13,1,0,15.00,0.00,15.00,31.00,0.00,31.00,0,7,2,1,1,0,0,0,15,0.00,31.00,0,15,1510995565,1,1510995565,0,NULL,0),(20,0,2,18,2,1,15.00,0.00,15.00,30.00,0.00,30.00,1541001600,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1510995734,1,1510995734,0,NULL,0),(21,0,2,18,5,0,1.00,0.00,1.00,1.00,0.00,1.00,1541001600,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1510995734,1,1510995734,0,NULL,0),(22,0,2,19,6,1,30.00,0.00,30.00,30.00,0.00,30.00,1519228800,0,0,1,0,0,0,0,30,0.00,30.00,1511316994,30,1510996786,1,1511317200,0,NULL,0),(23,0,2,20,1,0,1.00,0.00,1.00,2.00,0.00,2.00,0,7,2,1,1,0,0,0,1,0.00,2.00,0,1,1511003930,1,1511003930,0,NULL,0),(24,0,2,20,5,0,30.00,0.00,30.00,30.00,0.00,30.00,0,6,2,1,1,0,0,8,22,8.00,22.00,1512381375,30,1511003943,1,1512381375,0,NULL,0),(25,0,2,21,7,2,30.00,0.00,30.00,30.00,0.00,30.00,0,0,0,1,0,0,0,30,0,30.00,0.00,0,0,1511004143,1,1511004143,0,NULL,0),(26,0,2,22,3,2,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1511004217,1,1511004217,0,NULL,0),(27,0,2,23,2,1,45.00,1.00,46.00,90.00,2.00,92.00,1514736000,0,0,1,0,0,0,45,1,90.00,2.00,1511253544,1,1511144986,1,1511253653,0,NULL,0),(28,0,2,23,5,0,42.00,3.00,45.00,42.00,3.00,45.00,0,0,0,1,0,0,0,0,45,0.00,45.00,0,45,1511144986,1,1511144986,0,NULL,0),(29,0,2,1,5,0,30.00,0.00,30.00,30.00,0.00,30.00,0,6,2,1,1,0,0,9,21,9.00,21.00,1513168985,30,1511150330,1,1513422394,0,NULL,0),(30,0,2,2,5,0,30.00,0.00,30.00,30.00,0.00,30.00,0,6,2,1,1,0,0,9,21,9.00,21.00,1512716635,30,1511150330,1,1512716636,0,NULL,0),(31,0,2,3,5,0,30.00,0.00,30.00,30.00,0.00,30.00,0,6,2,1,1,0,0,7,23,7.00,23.00,1512381375,30,1511150330,1,1512381376,0,NULL,0),(32,0,2,4,5,0,30.00,0.00,30.00,30.00,0.00,30.00,0,6,2,1,1,0,0,8,22,8.00,22.00,1512716635,30,1511150330,1,1513421737,0,NULL,0),(33,0,2,8,5,0,30.00,0.00,30.00,30.00,0.00,30.00,0,6,2,1,1,0,0,8,22,8.00,22.00,1512716635,30,1511150330,1,1512716638,0,NULL,0),(34,0,2,9,5,0,30.00,0.00,30.00,30.00,0.00,30.00,0,6,2,1,1,0,0,5,25,5.00,25.00,1512377269,30,1511150330,1,1512381337,0,NULL,0),(35,0,2,24,8,0,90.00,0.00,90.00,90.00,0.00,90.00,0,0,0,1,0,0,0,0,90,0.00,90.00,0,90,1511151653,1,1511151653,0,NULL,0),(36,0,2,23,8,0,10.00,0.00,10.00,10.00,0.00,10.00,0,0,0,1,0,0,0,1,9,1.00,9.00,0,9,1511161354,1,1511161354,0,NULL,0),(37,0,2,26,9,0,7.00,0.00,7.00,7.00,0.00,7.00,0,9,2,1,0,0,0,1,6,1.00,6.00,1512009291,7,1511257691,1,1512371792,0,NULL,0),(38,0,2,27,9,0,7.00,0.00,7.00,7.00,0.00,7.00,0,9,2,1,0,0,0,1,6,1.00,6.00,1512009291,7,1511257709,1,1512371792,0,NULL,0),(39,0,2,31,9,0,7.00,0.00,7.00,7.00,0.00,7.00,0,0,0,1,0,0,0,0,7,0.00,7.00,0,7,1511323518,1,1511323518,0,NULL,0),(40,0,2,31,4,1,7.00,0.00,7.00,7.00,0.00,7.00,0,0,0,1,0,0,0,0,7,0.00,7.00,1511323779,7,1511323616,1,1511323973,0,NULL,0),(41,0,2,32,8,0,700.00,0.00,700.00,700.00,0.00,700.00,0,0,0,1,0,0,0,0,700,0.00,700.00,0,700,1511334001,1,1511334001,0,NULL,0),(42,0,2,33,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,1,0,1.00,0.00,1511582586,1,1511338048,1,1511582586,0,NULL,0),(43,0,2,30,9,0,7.00,3.00,10.00,7.00,3.00,10.00,0,11,2,1,0,0,0,3,7,3.00,7.00,1512377419,10,1511345233,1,1512377420,0,NULL,0),(44,0,2,29,9,0,7.00,7.00,14.00,7.00,7.00,14.00,0,11,2,1,0,0,0,6,8,6.00,8.00,1512377419,14,1511345331,1,1512377419,0,NULL,0),(45,0,2,34,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,12,2,1,0,0,0,1,0,1.00,0.00,1511346471,1,1511346067,1,1511346471,0,NULL,0),(46,0,2,35,9,0,7.00,1.00,8.00,7.00,1.00,8.00,0,13,2,1,0,0,0,4,4,4.00,4.00,1512528638,8,1511354433,1,1512701622,0,NULL,0),(47,0,2,36,9,0,7.00,5.00,12.00,7.00,5.00,12.00,0,13,2,1,0,0,0,4,8,4.00,8.00,1512528638,11,1511354475,1,1512701622,0,NULL,0),(48,0,2,37,2,1,90.00,0.00,90.00,181.00,0.00,181.00,0,0,0,1,0,0,0,0,90,0.00,181.00,0,90,1511943146,1,1511943146,0,NULL,0),(49,0,2,37,1,0,45.00,0.00,45.00,91.00,0.00,91.00,0,0,0,1,0,0,0,0,45,0.00,91.00,0,45,1511943146,1,1511943146,0,NULL,0),(50,0,2,37,5,0,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,0,2,0.00,2.00,0,2,1511944947,1,1511944947,0,NULL,0),(51,0,2,37,6,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1511944947,1,1511944947,0,NULL,0),(52,0,2,37,8,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1511944999,1,1511944999,0,NULL,0),(53,0,2,37,3,2,90.00,0.00,90.00,181.00,0.00,181.00,0,0,0,1,0,0,0,0,90,0.00,181.00,0,90,1511946840,1,1511946840,0,NULL,0),(54,0,2,37,4,1,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,1,1,1.00,1.00,0,1,1511946896,1,1511946896,0,NULL,0),(55,0,2,38,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1511947107,1,1511947107,0,NULL,0),(56,0,2,39,8,0,3.00,0.00,3.00,3.00,0.00,3.00,0,0,0,1,0,0,0,0,3,0.00,3.00,0,3,1511947296,1,1511947296,0,NULL,0),(57,0,2,40,3,2,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512004807,1,1512004807,0,NULL,0),(58,0,2,40,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512004807,1,1512004807,0,NULL,0),(59,0,2,40,7,2,30.00,0.00,30.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,30,0.00,30.00,0,30,1512004807,1,1512004807,0,NULL,0),(60,0,2,41,2,1,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512005259,1,1512005259,0,NULL,0),(61,0,2,41,3,2,30.00,0.00,30.00,60.00,0.00,60.00,0,0,0,1,0,0,0,14,16,30.00,30.00,0,16,1512005259,1,1512005259,0,NULL,0),(62,0,2,41,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512005259,1,1512005259,0,NULL,0),(63,0,2,41,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,7,2,1,1,0,0,0,15,0.00,30.00,0,15,1512005529,1,1512005529,0,NULL,0),(66,0,2,43,2,1,30.00,0.00,30.00,60.00,0.00,60.00,0,0,0,1,0,0,0,0,30,0.00,60.00,0,30,1512036356,1,1512036356,0,NULL,0),(67,0,2,43,3,2,30.00,0.00,30.00,60.00,0.00,60.00,0,0,0,1,0,0,0,0,30,0.00,60.00,0,30,1512036356,1,1512036356,0,NULL,0),(68,0,2,43,9,0,14.00,0.00,14.00,14.00,0.00,14.00,0,13,2,1,1,0,0,4,10,4.00,10.00,1512528638,13,1512036599,1,1512701622,0,NULL,0),(69,0,2,43,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512036599,1,1512036599,0,NULL,0),(70,0,2,38,3,2,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,1.00,29.00,0,15,1512090733,1,1512090733,0,NULL,0),(71,0,2,4,3,2,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512099589,1,1512099589,0,NULL,0),(72,0,2,4,2,1,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512099589,1,1512099589,0,NULL,0),(73,0,2,49,3,2,60.00,0.00,60.00,120.00,0.00,120.00,0,0,0,1,0,0,0,0,60,3.00,117.00,0,60,1512099627,1,1512099627,0,NULL,0),(74,0,2,49,4,1,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,1,1,1.00,1.00,0,1,1512099627,1,1512099627,0,NULL,0),(75,0,2,49,6,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,1,0,1.00,0.00,0,0,1512099640,1,1512099640,0,NULL,0),(76,0,2,49,2,1,45.00,0.00,45.00,90.00,0.00,90.00,0,0,0,1,0,0,0,15,30,30.00,60.00,0,30,1512111149,1,1512111149,0,NULL,0),(77,0,2,49,1,0,75.00,0.00,75.00,150.00,0.00,150.00,0,0,0,1,0,0,0,15,60,30.00,120.00,0,60,1512111302,1,1512111302,0,NULL,0),(78,0,2,49,9,0,7.00,0.00,7.00,7.00,0.00,7.00,0,9,2,1,1,0,0,0,7,0.00,7.00,0,7,1512116846,1,1512116846,0,NULL,0),(79,0,2,49,8,0,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,1,1,1.00,1.00,0,1,1512117598,1,1512117598,0,NULL,0),(80,0,2,49,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,6,2,1,1,0,0,0,1,0.00,1.00,1512377269,1,1512118175,1,1512381337,0,NULL,0),(81,0,4,5,9,0,14.00,0.00,14.00,14.00,0.00,14.00,0,0,0,1,0,0,0,0,14,0.00,14.00,0,14,1512350735,1,1512350735,0,NULL,0),(82,0,4,5,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,12,2,1,1,0,0,0,1,0.00,1.00,0,1,1512350735,1,1512350735,0,NULL,0),(83,0,2,7,3,2,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512465297,1,1512465297,0,NULL,0),(84,0,2,7,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512465297,1,1512465297,0,NULL,0),(85,0,2,52,3,2,45.00,0.00,45.00,90.00,0.00,90.00,0,0,0,1,0,0,0,15,30,32.00,58.00,1512725205,31,1512469292,1,1512725205,0,NULL,0),(86,0,2,8,2,1,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512529320,1,1512554013,1,1512554013,1),(87,0,2,8,3,2,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512529320,1,1512554013,1,1512554013,1),(88,0,2,53,3,2,45.00,0.00,45.00,90.00,0.00,90.00,0,0,0,1,0,0,0,30,15,60.00,30.00,0,15,1512529343,1,1512554027,1,1512554027,1),(89,0,2,53,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512530064,1,1512553859,1,1512553859,1),(90,0,2,53,8,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512530414,1,1512553843,1,1512553843,1),(91,0,2,53,2,1,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512530976,1,1512553889,1,1512553889,1),(92,0,2,53,6,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512531123,1,1512553876,1,1512553876,1),(93,0,2,52,2,1,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512533556,1,1512533556,0,NULL,0),(94,0,2,53,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,2.00,28.00,0,15,1512549245,1,1512549245,0,NULL,0),(95,0,2,12,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,3.00,27.00,0,15,1512549483,1,1512549483,0,NULL,0),(96,0,2,57,1,0,45.00,0.00,45.00,90.00,0.00,90.00,0,0,0,1,0,0,0,1,44,5.00,85.00,0,44,1512549683,1,1512549683,0,NULL,0),(97,0,2,57,3,2,45.00,0.00,45.00,90.00,0.00,90.00,0,0,0,1,0,0,0,30,15,60.00,30.00,0,15,1512555067,1,1512555430,1,1512555430,1),(98,0,2,57,5,0,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,1,1,1.00,1.00,0,1,1512555077,1,1512555130,1,1512555130,1),(99,0,2,57,6,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512555097,1,1512555430,1,1512555430,1),(100,0,2,57,2,1,45.00,0.00,45.00,90.00,0.00,90.00,0,0,0,1,0,0,0,0,45,0.00,90.00,0,45,1512556368,1,1512556368,0,NULL,0),(101,0,2,57,3,2,30.00,0.00,30.00,60.00,0.00,60.00,0,0,0,1,0,0,0,0,30,0.00,60.00,1512725205,30,1512556368,1,1513421314,0,NULL,0),(102,0,2,14,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512614181,1,1512614181,0,NULL,0),(103,0,2,43,5,0,4.00,0.00,4.00,4.00,0.00,4.00,0,12,2,1,1,0,0,0,4,0.00,4.00,0,4,1512615429,1,1512615429,0,NULL,0),(104,0,2,15,2,1,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512617145,1,1512617145,0,NULL,0),(105,0,2,16,3,2,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512617931,1,1512617931,0,NULL,0),(106,0,2,17,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512618093,1,1512618093,0,NULL,0),(107,0,2,18,9,0,7.00,0.00,7.00,7.00,0.00,7.00,0,0,0,1,0,0,0,0,7,0.00,7.00,0,7,1512618204,1,1512618204,0,NULL,0),(108,0,2,19,8,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512618266,1,1512618266,0,NULL,0),(109,0,2,20,6,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512618505,1,1512618505,0,NULL,0),(110,0,2,56,9,0,7.00,0.00,7.00,7.00,0.00,7.00,0,12,2,1,1,0,0,0,7,0.00,7.00,0,7,1512636896,1,1512636896,0,NULL,0),(111,0,2,61,9,0,6.00,0.00,6.00,6.00,0.00,6.00,0,13,2,1,1,0,0,0,6,0.00,6.00,0,6,1512702013,1,1512702013,0,NULL,0),(112,0,2,65,2,1,30.00,0.00,30.00,60.00,0.00,60.00,0,0,0,1,0,0,0,0,30,0.00,60.00,0,30,1512702354,1,1512702354,0,NULL,0),(113,0,2,21,5,0,4.00,0.00,4.00,4.00,0.00,4.00,0,0,0,1,0,0,0,0,4,0.00,4.00,0,4,1512713010,1,1512713010,0,NULL,0),(114,0,2,22,5,0,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,0,2,0.00,2.00,0,2,1512713180,1,1512713180,0,NULL,0),(115,0,2,24,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1512713599,1,1512713599,0,NULL,0),(116,0,2,70,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,6,2,1,0,0,0,0,1,0.00,1.00,1512716635,1,1512713702,1,1513421733,0,NULL,0),(117,0,2,70,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,1,2,1,1,0,0,0,15,0.00,30.00,0,15,1512718382,1,1512718382,0,NULL,0),(118,0,2,70,9,0,7.00,0.00,7.00,7.00,0.00,7.00,0,13,2,1,1,0,0,0,7,0.00,7.00,0,7,1512718749,1,1513050857,0,NULL,0),(119,0,2,70,2,1,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1512718749,1,1512718749,0,NULL,0),(120,0,2,69,9,0,6.00,0.00,6.00,6.00,0.00,6.00,0,13,2,1,1,0,0,0,6,0.00,6.00,0,6,1513050857,1,1513050857,0,NULL,0),(121,0,2,72,2,1,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1513058791,1,1513058791,0,NULL,0),(122,0,2,72,9,0,6.00,0.00,6.00,6.00,0.00,6.00,0,13,2,1,1,0,0,0,6,0.00,6.00,0,6,1513058936,1,1513058936,0,NULL,0),(123,0,2,72,8,0,7.00,0.00,7.00,7.00,0.00,7.00,0,8,2,1,1,0,0,0,7,0.00,7.00,0,7,1513059662,1,1513079135,0,NULL,0),(124,0,2,72,1,0,90.00,0.00,90.00,180.00,0.00,180.00,0,1,2,1,0,0,0,0,90,0.00,180.00,0,90,1513067900,1,1513068837,0,NULL,0),(125,0,2,72,5,0,3.00,0.00,3.00,3.00,0.00,3.00,0,6,2,1,1,0,0,0,3,0.00,3.00,0,3,1513067900,1,1513067900,0,NULL,0),(126,0,2,71,1,0,6.00,0.00,6.00,12.00,0.00,12.00,0,1,2,1,1,0,0,0,6,0.00,12.00,0,6,1513068924,1,1513068924,0,NULL,0),(127,0,2,71,8,0,1.00,0.00,1.00,1.00,0.00,1.00,0,8,2,1,1,0,0,0,1,0.00,1.00,0,1,1513072145,1,1513072145,0,NULL,0),(128,0,2,74,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1513072202,1,1513072202,0,NULL,0),(129,0,2,74,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,6,2,1,1,0,0,0,1,0.00,1.00,0,1,1513072202,1,1513072202,0,NULL,0),(130,0,2,74,8,0,1.00,0.00,1.00,1.00,0.00,1.00,0,8,2,1,1,0,0,0,1,0.00,1.00,0,1,1513072248,1,1513072248,0,NULL,0),(131,0,2,75,8,0,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,0,2,0.00,2.00,0,2,1513072878,1,1513072878,0,NULL,0),(132,0,2,76,8,0,2.00,0.00,2.00,2.00,0.00,2.00,0,8,2,1,1,0,0,0,2,0.00,2.00,0,2,1513073819,1,1513073819,0,NULL,0),(133,0,2,77,8,0,1.00,0.00,1.00,1.00,0.00,1.00,0,8,2,1,1,0,0,0,1,0.00,1.00,0,1,1513080069,1,1513080137,0,NULL,0),(134,0,2,77,9,0,7.00,0.00,7.00,7.00,0.00,7.00,0,11,2,1,1,0,0,0,7,0.00,7.00,0,7,1513080069,1,1513080137,0,NULL,0),(135,0,2,78,1,0,30.00,0.00,30.00,60.00,0.00,60.00,0,2,2,1,1,0,0,0,30,0.00,60.00,0,30,1513081124,1,1513082035,0,NULL,0),(136,0,2,78,5,0,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,0,2,0.00,2.00,0,2,1513081124,1,1513081124,0,NULL,0),(137,0,2,78,8,0,1.00,0.00,1.00,1.00,0.00,1.00,0,8,2,1,1,0,0,0,1,0.00,1.00,0,1,1513129216,1,1513133338,0,NULL,0),(138,0,2,65,8,0,3.00,0.00,3.00,3.00,0.00,3.00,0,8,2,1,1,0,0,0,3,0.00,3.00,0,3,1513132440,1,1513132440,0,NULL,0),(139,0,2,64,8,0,3.00,0.00,3.00,3.00,0.00,3.00,0,8,2,1,1,0,0,0,3,0.00,3.00,0,3,1513132441,1,1513132441,0,NULL,0),(140,0,2,63,8,0,3.00,0.00,3.00,3.00,0.00,3.00,0,8,2,1,1,0,0,0,3,0.00,3.00,0,3,1513132441,1,1513132441,0,NULL,0),(141,0,2,62,8,0,3.00,0.00,3.00,3.00,0.00,3.00,0,8,2,1,1,0,0,0,3,0.00,3.00,0,3,1513132441,1,1513132441,0,NULL,0),(142,0,2,61,8,0,3.00,0.00,3.00,3.00,0.00,3.00,0,8,2,1,1,0,0,0,3,0.00,3.00,0,3,1513132441,1,1513132441,0,NULL,0),(143,0,2,60,8,0,3.00,0.00,3.00,3.00,0.00,3.00,0,8,2,1,1,0,0,0,3,0.00,3.00,0,3,1513132441,1,1513132441,0,NULL,0),(144,0,2,79,8,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1513133823,1,1513133823,0,NULL,0),(145,0,2,79,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,2,2,1,1,0,0,0,15,0.00,30.00,0,15,1513133823,1,1513134368,0,NULL,0),(146,0,2,79,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1513243794,1,1513243794,0,NULL,0),(147,0,2,79,3,2,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1513243886,1,1513243886,0,NULL,0),(148,0,2,82,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,7,2,1,0,0,0,4,11,9.00,21.00,1513821705,12,1513819578,1,1513821705,0,NULL,0),(149,0,2,82,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,1,0,1.00,0.00,0,0,1513820370,1,1513820370,0,NULL,0),(150,0,2,83,1,0,30.00,0.00,30.00,60.00,0.00,60.00,0,0,0,1,0,0,0,0,30,0.00,60.00,0,30,1513824352,1,1513824352,0,NULL,0),(151,0,2,83,4,1,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1513825288,1,1513825288,0,NULL,0),(152,0,2,83,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1513825288,1,1513825288,0,NULL,0),(153,0,2,83,7,2,30.00,0.00,30.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,30,0.00,30.00,0,30,1513826157,1,1513826157,0,NULL,0),(154,0,2,83,8,0,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,0,2,0.00,2.00,0,2,1513826157,1,1513826157,0,NULL,0),(155,0,2,83,6,1,2.00,0.00,2.00,2.00,0.00,2.00,0,0,0,1,0,0,0,0,2,0.00,2.00,0,2,1513826157,1,1513826157,0,NULL,0),(156,0,2,84,1,0,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1513828003,1,1513828003,0,NULL,0),(157,0,2,84,2,1,15.00,0.00,15.00,30.00,0.00,30.00,0,0,0,1,0,0,0,0,15,0.00,30.00,0,15,1513828028,1,1513828028,0,NULL,0),(158,0,2,84,5,0,1.00,0.00,1.00,1.00,0.00,1.00,0,0,0,1,0,0,0,0,1,0.00,1.00,0,1,1513828597,1,1513828597,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=302 DEFAULT CHARSET=utf8mb4 COMMENT='学生课时消耗记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_lesson_hour`
--

LOCK TABLES `x360p_student_lesson_hour` WRITE;
/*!40000 ALTER TABLE `x360p_student_lesson_hour` DISABLE KEYS */;
INSERT INTO `x360p_student_lesson_hour` VALUES (1,0,2,1,1,1,1,0,0,9,1,1,20171118,1000,1200,10002,0,2.00,0,240.00,1,0,1510972694,1,1510972694,0,NULL,0),(2,0,2,2,1,1,1,0,0,9,2,1,20171118,1000,1200,10002,0,2.00,0,240.00,1,0,1510972710,1,1510972710,0,NULL,0),(3,0,2,8,1,1,1,0,0,9,3,1,20171118,1000,1200,10002,0,2.00,0,240.00,1,0,1510973477,1,1510973477,0,NULL,0),(4,0,2,9,1,1,1,0,0,9,4,1,20171118,1000,1200,10002,0,2.00,0,240.00,1,0,1510973565,1,1510973565,0,NULL,0),(5,0,2,3,1,1,1,0,0,9,5,1,20171118,1000,1200,10002,0,2.00,0,240.00,1,0,1510973644,1,1510973644,0,NULL,0),(6,0,2,5,1,1,1,0,0,21,6,2,20171118,1500,1515,10002,10002,2.00,0,240.00,1,0,1510988833,1,1510990849,1,1510990849,1),(7,0,2,6,1,1,1,0,0,21,7,2,20171118,1500,1515,10002,10002,2.00,0,240.00,1,0,1510988833,1,1510990849,1,1510990849,1),(8,0,2,7,1,1,1,0,0,21,8,2,20171118,1500,1515,10002,10002,2.00,0,240.00,1,0,1510988834,1,1510990849,1,1510990849,1),(9,0,2,8,1,1,1,0,0,21,9,2,20171118,1500,1515,10002,10002,2.00,0,240.00,1,0,1510988834,1,1510990849,1,1510990849,1),(10,0,2,9,1,1,1,0,0,21,10,2,20171118,1500,1515,10002,10002,2.00,0,240.00,1,0,1510988835,1,1510990849,1,1510990849,1),(11,0,2,2,1,1,1,0,0,21,13,2,20171118,1500,1515,10002,10002,2.00,0,240.00,1,0,1510988836,1,1510990849,1,1510990849,1),(12,0,2,1,1,1,1,0,0,21,14,2,20171118,1500,1515,10002,10002,2.00,0,240.00,1,0,1510988836,1,1510990849,1,1510990849,1),(13,0,2,5,1,1,1,0,0,22,15,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990865,1,1510990866,1,1510990866,1),(14,0,2,6,1,1,1,0,0,22,16,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990865,1,1510990867,1,1510990867,1),(15,0,2,7,1,1,1,0,0,22,17,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990866,1,1510990868,1,1510990868,1),(16,0,2,8,1,1,1,0,0,22,18,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990866,1,1510990869,1,1510990869,1),(17,0,2,5,1,1,1,0,0,22,19,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990866,1,1510990867,1,1510990867,1),(18,0,2,9,1,1,1,0,0,22,20,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990867,1,1510990870,1,1510990870,1),(19,0,2,6,1,1,1,0,0,22,21,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990867,1,1510990868,1,1510990868,1),(20,0,2,5,1,1,1,0,0,22,23,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990867,1,1510990868,1,1510990868,1),(21,0,2,4,1,1,1,0,0,22,22,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990868,1,1510990871,1,1510990871,1),(22,0,2,5,1,1,1,0,0,22,24,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990868,1,1510990868,1,1510990868,1),(23,0,2,5,1,1,1,0,0,22,26,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990868,1,1510990869,1,1510990869,1),(24,0,2,3,1,1,1,0,0,22,25,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990868,1,1510990873,1,1510990873,1),(25,0,2,5,1,1,1,0,0,22,29,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990868,1,1510990868,0,NULL,0),(26,0,2,7,1,1,1,0,0,22,27,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990869,1,1510990869,1,1510990869,1),(27,0,2,6,1,1,1,0,0,22,28,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990869,1,1510990869,1,1510990869,1),(28,0,2,5,1,1,1,0,0,22,30,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990869,1,1510990869,0,NULL,0),(29,0,2,2,1,1,1,0,0,22,31,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990869,1,1510990874,1,1510990874,1),(30,0,2,8,1,1,1,0,0,22,32,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990870,1,1510990870,1,1510990870,1),(31,0,2,6,1,1,1,0,0,22,33,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990870,1,1510990870,0,NULL,0),(32,0,2,7,1,1,1,0,0,22,34,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990870,1,1510990870,1,1510990870,1),(33,0,2,1,1,1,1,0,0,22,35,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990870,1,1510990875,1,1510990875,1),(34,0,2,9,1,1,1,0,0,22,36,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990871,1,1510990872,1,1510990872,1),(35,0,2,7,1,1,1,0,0,22,37,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990871,1,1510990871,0,NULL,0),(36,0,2,8,1,1,1,0,0,22,38,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990871,1,1510990871,1,1510990871,1),(37,0,2,4,1,1,1,0,0,22,39,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990872,1,1510990873,1,1510990873,1),(38,0,2,8,1,1,1,0,0,22,40,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990872,1,1510990872,0,NULL,0),(39,0,2,9,1,1,1,0,0,22,41,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990872,1,1510990873,1,1510990873,1),(40,0,2,3,1,1,1,0,0,22,42,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990873,1,1510990874,1,1510990874,1),(41,0,2,9,1,1,1,0,0,22,43,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990874,1,1510990874,0,NULL,0),(42,0,2,4,1,1,1,0,0,22,44,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990874,1,1510990874,1,1510990874,1),(43,0,2,2,1,1,1,0,0,22,45,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990875,1,1510990875,1,1510990875,1),(44,0,2,4,1,1,1,0,0,22,46,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990875,1,1510990875,0,NULL,0),(45,0,2,3,1,1,1,0,0,22,47,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990875,1,1510990875,1,1510990875,1),(46,0,2,1,1,1,1,0,0,22,48,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990876,1,1510990876,1,1510990876,1),(47,0,2,3,1,1,1,0,0,22,49,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990876,1,1510990876,0,NULL,0),(48,0,2,2,1,1,1,0,0,22,50,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990876,1,1510990876,1,1510990876,1),(49,0,2,2,1,1,1,0,0,22,51,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990877,1,1510990877,0,NULL,0),(50,0,2,1,1,1,1,0,0,22,52,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990877,1,1510990877,1,1510990877,1),(51,0,2,1,1,1,1,0,0,22,53,3,20171118,1530,1545,10002,10002,2.00,0,240.00,1,0,1510990878,1,1510990878,0,NULL,0),(52,0,2,7,1,1,1,0,0,9,56,1,20171118,1000,1200,10002,0,2.00,0,240.00,1,0,1510991983,1,1510991983,0,NULL,0),(53,0,2,4,1,1,1,0,0,9,57,1,20171118,1000,1200,10002,0,2.00,0,240.00,1,0,1510991983,1,1510991983,0,NULL,0),(54,0,2,20,5,4,6,24,0,85,73,9,20171120,1230,1300,10002,10003,1.00,0,0.00,1,0,1511237790,1,1511237790,0,NULL,0),(55,0,2,1,5,4,6,29,0,85,74,9,20171120,1230,1300,10002,10003,1.00,0,0.00,1,0,1511237790,1,1511237790,0,NULL,0),(56,0,2,2,5,4,6,30,0,85,75,9,20171120,1230,1300,10002,10003,1.00,0,0.00,1,0,1511237791,1,1511237791,0,NULL,0),(57,0,2,3,5,4,6,31,0,85,76,9,20171120,1230,1300,10002,10003,1.00,0,0.00,1,0,1511237791,1,1511237791,0,NULL,0),(58,0,2,4,5,4,6,32,0,85,77,9,20171120,1230,1300,10002,10003,1.00,0,0.00,1,0,1511237792,1,1511237792,0,NULL,0),(59,0,2,8,5,4,6,33,0,85,78,9,20171120,1230,1300,10002,10003,1.00,0,0.00,1,0,1511237792,1,1511237792,0,NULL,0),(60,0,2,9,5,4,6,34,0,85,79,9,20171120,1230,1300,10002,10003,1.00,0,0.00,1,0,1511237793,1,1511237793,0,NULL,0),(61,0,2,5,1,1,1,6,0,6,80,10,20171125,1030,1230,10002,0,2.00,0,0.00,1,0,1511238107,1,1511238107,0,NULL,0),(62,0,2,6,1,1,1,7,0,6,81,10,20171125,1030,1230,10002,0,2.00,0,0.00,1,0,1511238107,1,1511238107,0,NULL,0),(63,0,2,7,1,1,1,8,0,6,82,10,20171125,1030,1230,10002,0,2.00,0,0.00,1,0,1511238108,1,1511238108,0,NULL,0),(64,0,2,8,1,1,1,5,0,6,83,10,20171125,1030,1230,10002,0,2.00,0,0.00,1,0,1511238108,1,1511238108,0,NULL,0),(65,0,2,9,1,1,1,9,0,6,84,10,20171125,1030,1230,10002,0,2.00,0,0.00,1,0,1511238109,1,1511238109,0,NULL,0),(66,0,2,4,1,1,1,4,0,6,85,10,20171125,1030,1230,10002,0,2.00,0,0.00,1,0,1511238109,1,1511238109,0,NULL,0),(67,0,2,3,1,1,1,3,0,6,86,10,20171125,1030,1230,10002,0,2.00,0,0.00,1,0,1511238110,1,1511238110,0,NULL,0),(68,0,2,2,1,1,1,2,0,6,87,10,20171125,1030,1230,10002,0,2.00,0,0.00,1,0,1511238110,1,1511238110,0,NULL,0),(69,0,2,20,5,4,6,24,0,59,88,11,20171125,1030,1230,10002,0,1.00,0,0.00,1,0,1511238502,1,1511238502,0,NULL,0),(70,0,2,1,5,4,6,29,0,59,89,11,20171125,1030,1230,10002,0,1.00,0,0.00,1,0,1511238502,1,1511238502,0,NULL,0),(71,0,2,2,5,4,6,30,0,59,90,11,20171125,1030,1230,10002,0,1.00,0,0.00,1,0,1511238503,1,1511238503,0,NULL,0),(72,0,2,3,5,4,6,31,0,59,91,11,20171125,1030,1230,10002,0,1.00,0,0.00,1,0,1511238503,1,1511238503,0,NULL,0),(73,0,2,4,5,4,6,32,0,59,92,11,20171125,1030,1230,10002,0,1.00,0,0.00,1,0,1511238504,1,1511238504,0,NULL,0),(74,0,2,8,5,4,6,33,0,59,93,11,20171125,1030,1230,10002,0,1.00,0,0.00,1,0,1511238504,1,1511238504,0,NULL,0),(75,0,2,9,5,4,6,34,0,59,94,11,20171125,1030,1230,10002,0,1.00,0,0.00,1,0,1511238505,1,1511238505,0,NULL,0),(76,0,2,20,5,4,6,24,0,84,96,13,20171120,1200,1215,10003,10003,1.00,0,0.00,1,0,1511244986,1,1511244986,0,NULL,0),(77,0,2,1,5,4,6,29,0,84,97,13,20171120,1200,1215,10003,10003,1.00,0,0.00,1,0,1511244986,1,1511244986,0,NULL,0),(78,0,2,2,5,4,6,30,0,84,98,13,20171120,1200,1215,10003,10003,1.00,0,0.00,1,0,1511244987,1,1511244987,0,NULL,0),(79,0,2,3,5,4,6,31,0,84,99,13,20171120,1200,1215,10003,10003,1.00,0,0.00,1,0,1511244987,1,1511244987,0,NULL,0),(80,0,2,4,5,4,6,32,0,84,100,13,20171120,1200,1215,10003,10003,1.00,0,0.00,1,0,1511244988,1,1511244988,0,NULL,0),(81,0,2,8,5,4,6,33,0,84,101,13,20171120,1200,1215,10003,10003,1.00,0,0.00,1,0,1511244988,1,1511244988,0,NULL,0),(82,0,2,9,5,4,6,34,0,84,102,13,20171120,1200,1215,10003,10003,1.00,0,0.00,1,0,1511244989,1,1511244989,0,NULL,0),(83,0,2,20,5,4,6,24,0,56,104,14,20171123,1900,2130,10002,0,1.00,0,0.00,1,0,1511245478,1,1511245478,0,NULL,0),(84,0,2,1,5,4,6,29,0,56,105,14,20171123,1900,2130,10002,0,1.00,0,0.00,1,0,1511245478,1,1511245478,0,NULL,0),(85,0,2,2,5,4,6,30,0,56,106,14,20171123,1900,2130,10002,0,1.00,0,0.00,1,0,1511245479,1,1511245479,0,NULL,0),(86,0,2,3,5,4,6,31,0,56,107,14,20171123,1900,2130,10002,0,1.00,0,0.00,1,0,1511245479,1,1511245479,0,NULL,0),(87,0,2,4,5,4,6,32,0,56,108,14,20171123,1900,2130,10002,0,1.00,0,0.00,1,0,1511245480,1,1511245480,0,NULL,0),(88,0,2,8,5,4,6,33,0,56,109,14,20171123,1900,2130,10002,0,1.00,0,0.00,1,0,1511245480,1,1511245480,0,NULL,0),(89,0,2,9,5,4,6,34,0,56,110,14,20171123,1900,2130,10002,0,1.00,0,0.00,1,0,1511245481,1,1511245481,0,NULL,0),(90,0,2,20,5,4,6,24,0,58,115,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511246713,1,1511247402,1,1511247402,1),(91,0,2,1,5,4,6,29,0,58,116,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511246714,1,1511247402,1,1511247402,1),(92,0,2,2,5,4,6,30,0,58,117,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511246714,1,1511247403,1,1511247403,1),(93,0,2,3,5,4,6,31,0,58,118,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511246715,1,1511247404,1,1511247404,1),(94,0,2,4,5,4,6,32,0,58,119,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511246715,1,1511247404,1,1511247404,1),(95,0,2,8,5,4,6,33,0,58,120,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511246716,1,1511247405,1,1511247405,1),(96,0,2,9,5,4,6,34,0,58,121,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511246716,1,1511247406,1,1511247406,1),(97,0,2,20,5,4,6,24,0,58,124,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247402,1,1511247744,1,1511247744,1),(98,0,2,1,5,4,6,29,0,58,125,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247402,1,1511247745,1,1511247745,1),(99,0,2,2,5,4,6,30,0,58,126,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247403,1,1511247745,1,1511247745,1),(100,0,2,3,5,4,6,31,0,58,127,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247404,1,1511247746,1,1511247746,1),(101,0,2,4,5,4,6,32,0,58,128,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247405,1,1511247747,1,1511247747,1),(102,0,2,8,5,4,6,33,0,58,129,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247406,1,1511247748,1,1511247748,1),(103,0,2,9,5,4,6,34,0,58,130,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247407,1,1511247749,1,1511247749,1),(104,0,2,20,5,4,6,24,0,58,131,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247744,1,1511247815,1,1511247815,1),(105,0,2,1,5,4,6,29,0,58,132,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247745,1,1511247815,1,1511247815,1),(106,0,2,2,5,4,6,30,0,58,133,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247746,1,1511247816,1,1511247816,1),(107,0,2,3,5,4,6,31,0,58,134,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247747,1,1511247817,1,1511247817,1),(108,0,2,4,5,4,6,32,0,58,135,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247748,1,1511247818,1,1511247818,1),(109,0,2,8,5,4,6,33,0,58,136,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247748,1,1511247819,1,1511247819,1),(110,0,2,9,5,4,6,34,0,58,137,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247749,1,1511247820,1,1511247820,1),(111,0,2,20,5,4,6,24,0,58,138,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247815,1,1511247842,1,1511247842,1),(112,0,2,1,5,4,6,29,0,58,139,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247816,1,1511247843,1,1511247843,1),(113,0,2,2,5,4,6,30,0,58,140,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247817,1,1511247843,1,1511247843,1),(114,0,2,3,5,4,6,31,0,58,141,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247817,1,1511247844,1,1511247844,1),(115,0,2,4,5,4,6,32,0,58,142,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247818,1,1511247845,1,1511247845,1),(116,0,2,8,5,4,6,33,0,58,143,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247819,1,1511247846,1,1511247846,1),(117,0,2,9,5,4,6,34,0,58,144,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247820,1,1511247847,1,1511247847,1),(118,0,2,20,5,4,6,24,0,58,145,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247842,1,1511247842,0,NULL,0),(119,0,2,1,5,4,6,29,0,58,146,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247843,1,1511247843,0,NULL,0),(120,0,2,2,5,4,6,30,0,58,147,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247844,1,1511247844,0,NULL,0),(121,0,2,3,5,4,6,31,0,58,148,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247845,1,1511249757,1,1511249757,1),(122,0,2,4,5,4,6,32,0,58,149,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247846,1,1511247846,0,NULL,0),(123,0,2,8,5,4,6,33,0,58,150,18,20171125,800,1000,10002,0,1.00,0,200.00,1,0,1511247846,1,1511248449,1,1511248449,1),(124,0,2,9,1,1,1,9,0,86,152,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511249484,1,1511249712,1,1511249712,1),(125,0,2,9,1,1,1,9,0,86,153,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511249881,1,1511249977,1,1511249977,1),(126,0,2,8,1,1,1,5,0,86,154,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511249922,1,1511249973,1,1511249973,1),(127,0,2,9,1,1,1,9,0,86,155,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511250001,1,1511255550,1,1511255550,1),(128,0,2,8,1,1,1,5,0,86,156,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511250005,1,1511255550,1,1511255550,1),(132,0,2,23,2,0,0,27,1,0,166,0,20171121,1900,2130,10003,0,2.00,0,363.88,1,0,1511253544,1,1511253653,1,1511253653,1),(133,0,2,5,1,1,1,6,0,86,167,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511255516,1,1511255550,1,1511255550,1),(134,0,2,6,1,1,1,7,0,86,168,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511255517,1,1511255550,1,1511255550,1),(135,0,2,7,1,1,1,8,0,86,169,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511255518,1,1511255550,1,1511255550,1),(136,0,2,4,1,1,1,4,0,86,170,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511255518,1,1511255550,1,1511255550,1),(137,0,2,3,1,1,1,3,0,86,171,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511255519,1,1511255550,1,1511255550,1),(138,0,2,2,1,1,1,2,0,86,172,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511255519,1,1511255550,1,1511255550,1),(139,0,2,1,1,1,1,1,0,86,173,19,20171121,1500,1700,10002,0,2.00,0,240.00,1,0,1511255520,1,1511255550,1,1511255550,1),(140,0,2,26,9,4,9,37,0,87,174,20,20171128,1900,2130,10004,0,1.00,60,200.00,1,0,1511258165,1,1511259566,1,1511259566,1),(141,0,2,27,9,4,9,38,0,87,175,20,20171128,1900,2130,10004,0,1.00,60,200.00,1,0,1511258165,1,1511265288,1,1511265288,1),(142,0,2,26,9,4,9,37,0,87,176,20,20171128,1900,2130,10004,0,1.00,60,200.00,1,0,1511264839,1,1511265288,1,1511265288,1),(143,0,2,19,6,4,0,22,1,0,178,0,20171122,1900,2130,10005,0,1.00,60,200.00,1,0,1511316994,1,1511317200,1,1511317200,1),(144,0,2,31,4,0,0,40,1,0,183,0,20171122,1900,2130,10005,0,1.00,60,200.00,1,0,1511323779,1,1511323973,1,1511323973,1),(145,0,2,26,9,4,9,37,0,87,184,26,20171128,1900,2130,10004,0,1.00,60,200.00,1,0,1511340091,1,1511341467,1,1511341467,1),(146,0,2,26,9,4,9,37,0,93,186,27,20171203,1030,1230,10004,0,1.00,60,200.00,1,0,1511340119,1,1511340419,1,1511340419,1),(147,0,2,26,9,4,9,37,0,93,188,27,20171203,1030,1230,10004,0,1.00,60,200.00,1,0,1511343963,1,1511344568,1,1511344568,1),(148,0,2,27,9,4,9,38,0,93,189,27,20171203,1030,1230,10004,0,1.00,60,200.00,1,0,1511343963,1,1511344609,1,1511344609,1),(149,0,2,26,9,4,9,37,0,87,190,26,20171128,1900,2130,10004,0,1.00,60,200.00,1,0,1511344861,1,1511344880,1,1511344880,1),(150,0,2,27,9,4,9,38,0,87,191,26,20171128,1900,2130,10004,0,1.00,60,200.00,1,0,1511344862,1,1511344862,0,NULL,0),(151,0,2,29,9,4,11,44,0,103,192,28,20171129,1900,2130,1,0,1.00,60,100.00,1,0,1511345504,1,1511345504,0,NULL,0),(152,0,2,30,9,4,11,43,0,103,193,28,20171129,1900,2130,1,0,1.00,60,120.00,1,0,1511345505,1,1511345505,0,NULL,0),(153,0,2,34,5,4,12,45,0,109,194,30,20171122,1900,2130,10003,10003,1.00,60,200.00,1,0,1511346471,1,1511346471,0,NULL,0),(154,0,2,26,9,4,9,37,0,88,197,33,20171129,1900,2130,10004,0,1.00,60,200.00,1,0,1511346966,1,1511347042,1,1511347042,1),(155,0,2,27,9,4,9,38,0,88,198,33,20171129,1900,2130,10004,0,1.00,60,200.00,1,0,1511346966,1,1511347103,1,1511347103,1),(156,0,2,26,9,4,9,37,0,87,199,26,20171128,1900,2130,10004,0,1.00,60,200.00,1,0,1511347122,1,1511347122,0,NULL,0),(157,0,2,29,9,4,11,44,0,107,200,34,20171202,1030,1230,1,0,1.00,60,100.00,1,0,1511352652,1,1511352668,1,1511352668,1),(158,0,2,30,9,4,11,43,0,107,201,34,20171202,1030,1230,1,0,1.00,60,120.00,1,0,1511352652,1,1511352663,1,1511352663,1),(159,0,2,26,9,4,9,37,0,88,202,35,20171129,1900,2130,10004,0,1.00,60,200.00,1,0,1511352707,1,1511355671,1,1511355671,1),(160,0,2,29,9,4,11,44,0,104,204,36,20171130,1900,2130,1,0,1.00,60,100.00,1,0,1511352808,1,1511352878,1,1511352878,1),(161,0,2,30,9,4,11,43,0,104,205,36,20171130,1900,2130,1,0,1.00,60,120.00,1,0,1511352808,1,1511352824,1,1511352824,1),(162,0,2,30,9,4,11,43,0,104,206,36,20171130,1900,2130,1,0,1.00,60,120.00,1,0,1511352840,1,1511352878,1,1511352878,1),(163,0,2,30,9,4,11,43,0,104,208,37,20171130,1900,2130,1,0,1.00,60,120.00,1,0,1511352903,1,1511352903,0,NULL,0),(164,0,2,29,9,4,11,44,0,104,209,37,20171130,1900,2130,1,0,1.00,60,100.00,1,0,1511352972,1,1511352972,0,NULL,0),(165,0,2,29,9,0,11,44,0,0,210,39,20171122,1900,2130,1,1,1.00,60,100.00,1,0,1511353354,1,1511353354,1,1511353354,1),(166,0,2,29,9,0,11,44,0,0,211,39,20171122,1900,2130,1,1,1.00,60,100.00,1,0,1511353354,1,1511353354,0,NULL,0),(167,0,2,29,9,0,11,44,0,0,212,40,20171123,1900,2130,1,1,1.00,60,100.00,1,0,1511353956,1,1511353956,1,1511353956,1),(168,0,2,29,9,0,11,44,0,0,213,40,20171123,1900,2130,1,1,1.00,60,100.00,1,0,1511353956,1,1511353956,0,NULL,0),(169,0,2,29,9,0,11,44,0,0,214,41,20171124,1900,2130,1,1,1.00,60,100.00,1,0,1511354045,1,1511354045,1,1511354045,1),(170,0,2,29,9,0,11,44,0,0,215,41,20171124,1900,2130,1,1,1.00,60,100.00,1,0,1511354045,1,1511354045,0,NULL,0),(171,0,2,36,9,0,13,47,0,0,217,43,20171122,1900,2130,1,10005,1.00,60,91.67,1,0,1511354582,1,1511354582,1,1511354582,1),(172,0,2,36,9,0,13,47,0,0,218,43,20171122,1900,2130,1,10005,1.00,60,91.67,1,0,1511354582,1,1511355352,1,1511355352,1),(173,0,2,36,9,0,13,47,0,0,221,44,20171125,800,1000,10005,10005,1.00,60,91.67,1,0,1511355394,1,1511355394,0,NULL,0),(174,0,2,35,9,0,13,46,0,0,222,44,20171125,800,1000,10005,10005,1.00,60,162.50,1,0,1511355394,1,1511355394,0,NULL,0),(175,0,2,35,9,0,13,46,0,0,224,45,20171126,800,1000,1,10005,1.00,60,162.50,1,0,1511355603,1,1511355603,0,NULL,0),(176,0,2,20,5,4,6,24,0,113,225,46,20171124,1900,2130,10004,0,1.00,60,200.00,1,0,1511432684,1,1511432684,0,NULL,0),(177,0,2,1,5,4,6,29,0,113,226,46,20171124,1900,2130,10004,0,1.00,60,200.00,1,0,1511432685,1,1511432685,0,NULL,0),(178,0,2,2,5,4,6,30,0,113,227,46,20171124,1900,2130,10004,0,1.00,60,200.00,1,0,1511432685,1,1511432685,0,NULL,0),(179,0,2,3,5,4,6,31,0,113,228,46,20171124,1900,2130,10004,0,1.00,60,200.00,1,0,1511432686,1,1511432686,0,NULL,0),(180,0,2,4,5,4,6,32,0,113,229,46,20171124,1900,2130,10004,0,1.00,60,200.00,1,0,1511432686,1,1511432686,0,NULL,0),(181,0,2,8,5,4,6,33,0,113,230,46,20171124,1900,2130,10004,0,1.00,60,200.00,1,0,1511432687,1,1511432687,0,NULL,0),(182,0,2,9,5,4,6,34,0,113,231,46,20171124,1900,2130,10004,0,1.00,60,200.00,1,0,1511432687,1,1511432687,0,NULL,0),(183,0,2,5,1,1,1,6,0,102,232,47,20171121,1900,2130,10004,0,2.00,120,240.00,1,0,1511432735,1,1511432735,0,NULL,0),(184,0,2,6,1,1,1,7,0,102,233,47,20171121,1900,2130,10004,0,2.00,120,240.00,1,0,1511432736,1,1511432736,0,NULL,0),(185,0,2,7,1,1,1,8,0,102,234,47,20171121,1900,2130,10004,0,2.00,120,240.00,1,0,1511432736,1,1511432736,0,NULL,0),(186,0,2,8,1,1,1,5,0,102,235,47,20171121,1900,2130,10004,0,2.00,120,240.00,1,0,1511432737,1,1511432737,0,NULL,0),(187,0,2,9,1,1,1,9,0,102,236,47,20171121,1900,2130,10004,0,2.00,120,240.00,1,0,1511432737,1,1511432737,0,NULL,0),(188,0,2,4,1,1,1,4,0,102,237,47,20171121,1900,2130,10004,0,2.00,120,240.00,1,0,1511432738,1,1511432738,0,NULL,0),(189,0,2,3,1,1,1,3,0,102,238,47,20171121,1900,2130,10004,0,2.00,120,240.00,1,0,1511432738,1,1511432738,0,NULL,0),(190,0,2,2,1,1,1,2,0,102,239,47,20171121,1900,2130,10004,0,2.00,120,240.00,1,0,1511432739,1,1511432739,0,NULL,0),(191,0,2,1,1,1,1,1,0,102,240,47,20171121,1900,2130,10004,0,2.00,120,240.00,1,0,1511432739,1,1511432739,0,NULL,0),(192,0,2,5,1,1,1,6,0,86,241,48,20171121,1500,1700,10002,0,2.00,120,240.00,1,0,1511573588,1,1511573588,0,NULL,0),(193,0,2,6,1,1,1,7,0,86,242,48,20171121,1500,1700,10002,0,2.00,120,240.00,1,0,1511573589,1,1511573589,0,NULL,0),(194,0,2,8,1,1,1,5,0,86,244,48,20171121,1500,1700,10002,0,2.00,120,240.00,1,0,1511573589,1,1511573589,0,NULL,0),(195,0,2,9,1,1,1,9,0,86,245,48,20171121,1500,1700,10002,0,2.00,120,240.00,1,0,1511573590,1,1511573590,0,NULL,0),(196,0,2,3,1,1,1,3,0,86,247,48,20171121,1500,1700,10002,0,2.00,120,240.00,1,0,1511573591,1,1511573591,0,NULL,0),(197,0,2,2,1,1,1,2,0,86,248,48,20171121,1500,1700,10002,0,2.00,120,240.00,1,0,1511573591,1,1511573591,0,NULL,0),(198,0,2,1,1,1,1,1,0,86,249,48,20171121,1500,1700,10002,0,2.00,120,240.00,1,0,1511573592,1,1511573592,0,NULL,0),(199,0,2,33,4,0,0,42,1,0,250,0,20171125,1030,1230,1,0,1.00,60,200.00,1,0,1511582586,1,1511582586,0,NULL,0),(200,0,2,26,9,4,9,37,0,89,251,49,20171130,1900,2130,10004,0,1.00,60,200.00,1,0,1512009291,1,1512371792,1,1512371792,1),(201,0,2,27,9,4,9,38,0,89,252,49,20171130,1900,2130,10004,0,1.00,60,200.00,1,0,1512009292,1,1512371792,1,1512371792,1),(202,0,2,20,5,4,6,24,0,69,253,50,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360340,1,1512360949,1,1512360949,1),(203,0,2,1,5,4,6,29,0,69,254,50,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360341,1,1512360949,1,1512360949,1),(204,0,2,2,5,4,6,30,0,69,255,50,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360341,1,1512360949,1,1512360949,1),(205,0,2,3,5,4,6,31,0,69,256,50,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360342,1,1512360949,1,1512360949,1),(206,0,2,4,5,4,6,32,0,69,257,50,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360342,1,1512360949,1,1512360949,1),(207,0,2,8,5,4,6,33,0,69,258,50,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360343,1,1512360949,1,1512360949,1),(208,0,2,9,5,4,6,34,0,69,259,50,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360343,1,1512360949,1,1512360949,1),(209,0,2,49,5,4,6,80,0,69,260,50,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360344,1,1512360949,1,1512360949,1),(210,0,2,20,5,4,6,24,0,69,261,51,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360984,1,1512361647,1,1512361647,1),(211,0,2,1,5,4,6,29,0,69,262,51,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360984,1,1512361647,1,1512361647,1),(212,0,2,2,5,4,6,30,0,69,263,51,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360985,1,1512361647,1,1512361647,1),(213,0,2,3,5,4,6,31,0,69,264,51,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360985,1,1512361647,1,1512361647,1),(214,0,2,4,5,4,6,32,0,69,265,51,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360986,1,1512361647,1,1512361647,1),(215,0,2,8,5,4,6,33,0,69,266,51,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360986,1,1512361647,1,1512361647,1),(216,0,2,9,5,4,6,34,0,69,267,51,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360987,1,1512361647,1,1512361647,1),(217,0,2,49,5,4,6,80,0,69,268,51,20171203,800,1000,10003,0,1.00,60,200.00,1,0,1512360987,1,1512361648,1,1512361648,1),(218,0,2,20,5,4,6,24,0,69,269,52,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512361714,1,1512367642,1,1512367642,1),(219,0,2,1,5,4,6,29,0,69,270,52,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512361714,1,1512367642,1,1512367642,1),(220,0,2,2,5,4,6,30,0,69,271,52,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512361715,1,1512367642,1,1512367642,1),(221,0,2,3,5,4,6,31,0,69,272,52,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512361715,1,1512367642,1,1512367642,1),(222,0,2,4,5,4,6,32,0,69,273,52,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512361716,1,1512367642,1,1512367642,1),(223,0,2,8,5,4,6,33,0,69,274,52,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512361716,1,1512367642,1,1512367642,1),(224,0,2,9,5,4,6,34,0,69,275,52,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512361717,1,1512367642,1,1512367642,1),(225,0,2,49,5,4,6,80,0,69,276,52,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512361717,1,1512367642,1,1512367642,1),(226,0,2,20,5,4,6,24,0,69,277,53,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512367674,1,1512368050,1,1512368050,1),(227,0,2,1,5,4,6,29,0,69,278,53,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512367674,1,1512368050,1,1512368050,1),(228,0,2,2,5,4,6,30,0,69,279,53,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512367675,1,1512368050,1,1512368050,1),(229,0,2,3,5,4,6,31,0,69,280,53,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512367676,1,1512368050,1,1512368050,1),(230,0,2,4,5,4,6,32,0,69,281,53,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512367676,1,1512368050,1,1512368050,1),(231,0,2,8,5,4,6,33,0,69,282,53,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512367677,1,1512368050,1,1512368050,1),(232,0,2,9,5,4,6,34,0,69,283,53,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512367677,1,1512368050,1,1512368050,1),(233,0,2,49,5,4,6,80,0,69,284,53,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512367678,1,1512368050,1,1512368050,1),(234,0,2,20,5,4,6,24,0,69,285,54,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512368463,1,1512371341,1,1512371341,1),(235,0,2,1,5,4,6,29,0,69,286,54,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512368464,1,1512371341,1,1512371341,1),(236,0,2,2,5,4,6,30,0,69,287,54,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512368464,1,1512371341,1,1512371341,1),(237,0,2,3,5,4,6,31,0,69,288,54,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512368465,1,1512371341,1,1512371341,1),(238,0,2,4,5,4,6,32,0,69,289,54,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512368465,1,1512371341,1,1512371341,1),(239,0,2,8,5,4,6,33,0,69,290,54,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512368466,1,1512371341,1,1512371341,1),(240,0,2,9,5,4,6,34,0,69,291,54,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512368466,1,1512371341,1,1512371341,1),(241,0,2,49,5,4,6,80,0,69,292,54,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512368467,1,1512371211,1,1512371211,1),(242,0,2,20,5,4,6,24,0,69,293,55,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512371561,1,1512376421,1,1512376421,1),(243,0,2,1,5,4,6,29,0,69,294,55,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512371562,1,1512376421,1,1512376421,1),(244,0,2,2,5,4,6,30,0,69,295,55,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512371562,1,1512376421,1,1512376421,1),(245,0,2,3,5,4,6,31,0,69,296,55,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512371563,1,1512376421,1,1512376421,1),(246,0,2,4,5,4,6,32,0,69,297,55,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512371563,1,1512376421,1,1512376421,1),(247,0,2,8,5,4,6,33,0,69,298,55,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512371564,1,1512376421,1,1512376421,1),(248,0,2,9,5,4,6,34,0,69,299,55,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512371564,1,1512376421,1,1512376421,1),(249,0,2,49,5,4,6,80,0,69,300,55,20171203,800,1000,10002,0,1.00,60,200.00,1,0,1512371565,1,1512374727,1,1512374727,1),(250,0,2,20,5,4,6,24,0,69,301,56,20171203,800,1000,10015,0,1.00,60,200.00,1,0,1512376791,1,1512376890,1,1512376890,1),(251,0,2,1,5,4,6,29,0,69,302,56,20171203,800,1000,10015,0,1.00,60,200.00,1,0,1512376792,1,1512376890,1,1512376890,1),(252,0,2,2,5,4,6,30,0,69,303,56,20171203,800,1000,10015,0,1.00,60,200.00,1,0,1512376792,1,1512376890,1,1512376890,1),(253,0,2,3,5,4,6,31,0,69,304,56,20171203,800,1000,10015,0,1.00,60,200.00,1,0,1512376793,1,1512376890,1,1512376890,1),(254,0,2,4,5,4,6,32,0,69,305,56,20171203,800,1000,10015,0,1.00,60,200.00,1,0,1512376793,1,1512376890,1,1512376890,1),(255,0,2,8,5,4,6,33,0,69,306,56,20171203,800,1000,10015,0,1.00,60,200.00,1,0,1512376794,1,1512376890,1,1512376890,1),(256,0,2,9,5,4,6,34,0,69,307,56,20171203,800,1000,10015,0,1.00,60,200.00,1,0,1512376794,1,1512376890,1,1512376890,1),(257,0,2,49,5,4,6,80,0,69,308,56,20171203,800,1000,10015,0,1.00,60,200.00,1,0,1512376795,1,1512376890,1,1512376890,1),(258,0,2,20,5,4,6,24,0,69,309,57,20171203,800,1000,10009,0,1.00,60,200.00,1,0,1512377269,1,1512381337,1,1512381337,1),(259,0,2,2,5,4,6,30,0,69,311,57,20171203,800,1000,10009,0,1.00,60,200.00,1,0,1512377270,1,1512381337,1,1512381337,1),(260,0,2,3,5,4,6,31,0,69,312,57,20171203,800,1000,10009,0,1.00,60,200.00,1,0,1512377270,1,1512381337,1,1512381337,1),(261,0,2,4,5,4,6,32,0,69,313,57,20171203,800,1000,10009,0,1.00,60,200.00,1,0,1512377271,1,1512381337,1,1512381337,1),(262,0,2,8,5,4,6,33,0,69,314,57,20171203,800,1000,10009,0,1.00,60,200.00,1,0,1512377271,1,1512381337,1,1512381337,1),(263,0,2,9,5,4,6,34,0,69,315,57,20171203,800,1000,10009,0,1.00,60,200.00,1,0,1512377272,1,1512381337,1,1512381337,1),(264,0,2,49,5,4,6,80,0,69,316,57,20171203,800,1000,10009,0,1.00,60,200.00,1,0,1512377272,1,1512381337,1,1512381337,1),(265,0,2,29,9,4,11,44,0,108,317,58,20171203,1030,1230,10002,0,1.00,60,100.00,1,0,1512377419,1,1512377419,0,NULL,0),(266,0,2,30,9,4,11,43,0,108,318,58,20171203,1030,1230,10002,0,1.00,60,120.00,1,0,1512377420,1,1512377420,0,NULL,0),(267,0,2,43,9,4,11,68,0,108,319,58,20171203,1030,1230,10002,0,1.00,60,200.00,1,0,1512377420,1,1512377421,1,1512377421,1),(268,0,2,43,9,4,11,68,0,108,321,58,20171203,1030,1230,10002,0,1.00,60,200.00,1,0,1512377422,1,1512377422,0,NULL,0),(269,0,2,20,5,4,6,24,0,69,322,59,20171203,800,1000,10006,0,1.00,60,200.00,1,0,1512381375,1,1512381375,0,NULL,0),(270,0,2,1,5,4,6,29,0,69,323,59,20171203,800,1000,10006,0,1.00,60,200.00,1,0,1512381375,1,1512381375,0,NULL,0),(271,0,2,2,5,4,6,30,0,69,324,59,20171203,800,1000,10006,0,1.00,60,200.00,1,0,1512381376,1,1512381376,0,NULL,0),(272,0,2,3,5,4,6,31,0,69,325,59,20171203,800,1000,10006,0,1.00,60,200.00,1,0,1512381376,1,1512381376,0,NULL,0),(273,0,2,4,5,4,6,32,0,69,326,59,20171203,800,1000,10006,0,1.00,60,200.00,1,0,1512381377,1,1512381377,0,NULL,0),(274,0,2,8,5,4,6,33,0,69,327,59,20171203,800,1000,10006,0,1.00,60,200.00,1,0,1512381377,1,1512381377,0,NULL,0),(275,0,2,43,9,0,13,68,0,0,330,60,20171205,1900,2130,10003,10005,1.00,60,200.00,1,0,1512475491,1,1512475801,1,1512475801,1),(276,0,2,36,9,0,13,47,0,0,331,60,20171205,1900,2130,10003,10005,1.00,60,91.67,1,0,1512475491,1,1512475801,1,1512475801,1),(277,0,2,35,9,0,13,46,0,0,332,60,20171205,1900,2130,10003,10005,1.00,60,162.50,1,0,1512475491,1,1512475801,1,1512475801,1),(278,0,2,43,9,0,13,68,0,0,333,60,20171205,1900,2130,10003,10005,1.00,60,200.00,1,0,1512475801,1,1512475801,0,NULL,0),(279,0,2,36,9,0,13,47,0,0,334,60,20171205,1900,2130,10003,10005,1.00,60,91.67,1,0,1512475801,1,1512475801,0,NULL,0),(280,0,2,35,9,0,13,46,0,0,335,60,20171205,1900,2130,10003,10005,1.00,60,162.50,1,0,1512475801,1,1512475801,0,NULL,0),(281,0,2,43,9,0,13,68,0,0,336,61,20171206,1900,2130,10002,10005,1.00,60,200.00,1,0,1512527718,1,1512527744,1,1512527744,1),(282,0,2,36,9,0,13,47,0,0,337,61,20171206,1900,2130,10002,10005,1.00,60,91.67,1,0,1512527718,1,1512527744,1,1512527744,1),(283,0,2,35,9,0,13,46,0,0,338,61,20171206,1900,2130,10002,10005,1.00,60,162.50,1,0,1512527718,1,1512527744,1,1512527744,1),(284,0,2,43,9,0,13,68,0,0,339,61,20171206,1900,2130,10003,10005,1.00,60,200.00,1,0,1512527744,1,1512527744,0,NULL,0),(285,0,2,36,9,0,13,47,0,0,340,61,20171206,1900,2130,10003,10005,1.00,60,91.67,1,0,1512527744,1,1512527744,0,NULL,0),(286,0,2,35,9,0,13,46,0,0,341,61,20171206,1900,2130,10003,10005,1.00,60,162.50,1,0,1512527744,1,1512527744,0,NULL,0),(287,0,2,43,9,0,13,68,0,0,342,62,20171204,1900,2130,10003,10005,1.00,60,200.00,1,0,1512528638,1,1512701622,1,1512701622,1),(288,0,2,36,9,0,13,47,0,0,343,62,20171204,1900,2130,10003,10005,1.00,60,91.67,1,0,1512528638,1,1512701622,1,1512701622,1),(289,0,2,35,9,0,13,46,0,0,344,62,20171204,1900,2130,10003,10005,1.00,60,162.50,1,0,1512528638,1,1512701622,1,1512701622,1),(290,0,2,1,5,4,6,29,0,75,346,63,20171208,1445,1545,10004,0,1.00,60,200.00,1,0,1512716636,1,1512716636,0,NULL,0),(291,0,2,2,5,4,6,30,0,75,347,63,20171208,1445,1545,10004,0,1.00,60,200.00,1,0,1512716636,1,1512716636,0,NULL,0),(292,0,2,4,5,4,6,32,0,75,349,63,20171208,1445,1545,10004,0,1.00,60,200.00,1,0,1512716637,1,1513421737,1,1513421737,1),(293,0,2,8,5,4,6,33,0,75,350,63,20171208,1445,1545,10004,0,1.00,60,200.00,1,0,1512716638,1,1512716638,0,NULL,0),(294,0,2,70,5,4,6,116,0,75,353,63,20171208,1445,1545,10004,0,1.00,60,200.00,1,0,1512716639,1,1513421733,1,1513421733,1),(295,0,2,57,3,0,0,101,2,0,354,0,20171208,1900,2130,10003,0,2.00,120,360.00,1,0,1512725205,1,1513421314,1,1513421314,1),(296,0,2,52,3,0,0,85,2,0,355,0,20171208,1900,2130,10003,0,2.00,120,360.00,1,0,1512725205,1,1512725205,0,NULL,0),(297,0,2,1,5,4,6,29,0,82,358,64,20171213,1900,2130,0,0,1.00,60,200.00,1,0,1513168985,1,1513422394,1,1513422394,1),(301,0,2,82,1,0,7,148,0,0,366,69,20171221,1900,2130,10003,10003,2.00,120,240.00,1,0,1513821705,1,1513821705,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COMMENT='电子钱包余额变动记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_student_money_history`
--

LOCK TABLES `x360p_student_money_history` WRITE;
/*!40000 ALTER TABLE `x360p_student_money_history` DISABLE KEYS */;
INSERT INTO `x360p_student_money_history` VALUES (1,0,3,21,2,19,500.00,0.00,500.00,'',1510996786,1,1510996786,0,NULL,0),(2,0,1,1,2,23,16739.10,0.00,16739.10,'',1511161658,1,1511161658,0,NULL,0),(3,0,2,1,2,23,16739.10,16739.10,0.00,'',1511162033,1,1511162033,0,NULL,0),(4,0,1,2,2,41,2700.00,0.00,2700.00,'',1512005644,1,1512005644,0,NULL,0),(5,0,2,3,2,41,120.00,2700.00,2580.00,'',1512005689,1,1512005689,0,NULL,0),(6,0,1,3,2,37,200.00,0.00,200.00,'',1512094698,1,1512094698,0,NULL,0),(7,0,1,4,2,37,90.00,200.00,290.00,'',1512094742,1,1512094742,0,NULL,0),(8,0,1,5,2,37,102.00,290.00,392.00,'',1512094841,1,1512094841,0,NULL,0),(9,0,4,69,2,37,290.00,392.00,102.00,'',1512094995,1,1512094995,0,NULL,0),(10,0,1,6,2,37,90.00,102.00,192.00,'',1512095013,1,1512095013,0,NULL,0),(11,0,4,70,2,37,192.00,192.00,0.00,'',1512098994,1,1512098994,0,NULL,0),(12,0,1,7,2,38,180.00,0.00,180.00,'',1512109808,1,1512109808,0,NULL,0),(13,0,1,8,2,49,580.00,0.00,580.00,'',1512110495,1,1512110495,0,NULL,0),(14,0,4,75,2,49,580.00,580.00,0.00,'',1512111149,1,1512111149,0,NULL,0),(15,0,1,9,2,52,360.00,0.00,360.00,'',1512548974,1,1512548974,0,NULL,0),(16,0,2,14,2,52,360.00,360.00,0.00,'',1512550156,1,1512550156,0,NULL,0),(17,0,1,10,2,82,640.00,0.00,640.00,'',1513823167,1,1513823167,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='科目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_subject`
--

LOCK TABLES `x360p_subject` WRITE;
/*!40000 ALTER TABLE `x360p_subject` DISABLE KEYS */;
INSERT INTO `x360p_subject` VALUES (1,0,'小学数学','小学3年级-5年级奥数',1510971344,1,1510971344,0,0,NULL),(2,0,'小学英语','小学1年级-6年级英语',1510993480,1,1510993480,0,0,NULL),(3,0,'小学作文','小学1年级-6年级作文',1510993527,1,1510993527,0,0,NULL),(4,0,'音乐','电子琴钢琴教学',1510993669,1,1510993669,0,0,NULL),(5,0,'sss','sss',1511517495,1,1511517498,1,1,1511517498),(6,0,'数学拔高','数学强化',1512197520,1,1512197520,0,0,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8 COMMENT='刷卡记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_swiping_card_record`
--

LOCK TABLES `x360p_swiping_card_record` WRITE;
/*!40000 ALTER TABLE `x360p_swiping_card_record` DISABLE KEYS */;
INSERT INTO `x360p_swiping_card_record` VALUES (1,0,2,1,'0008659832',20171118,1038,1,1510972693,1,1510972694,0,NULL,0),(2,0,2,2,'0000042061',20171118,1038,1,1510972710,1,1510972710,0,NULL,0),(3,0,2,3,'0008600838',20171118,1042,0,1510972967,1,1510972967,0,NULL,0),(4,0,2,1,'0008659832',20171118,1044,0,1510973058,1,1510973058,0,NULL,0),(5,0,2,9,'0008697203',20171118,1045,0,1510973125,1,1510973125,0,NULL,0),(6,0,2,8,'0008674251',20171118,1046,0,1510973166,1,1510973166,0,NULL,0),(7,0,2,8,'0008674251',20171118,1046,0,1510973208,1,1510973208,0,NULL,0),(8,0,2,8,'0008674251',20171118,1046,0,1510973209,1,1510973209,0,NULL,0),(9,0,2,8,'0008674251',20171118,1048,0,1510973315,1,1510973315,0,NULL,0),(10,0,2,8,'0008674251',20171118,1049,0,1510973399,1,1510973399,0,NULL,0),(11,0,2,8,'0008674251',20171118,1050,0,1510973425,1,1510973425,0,NULL,0),(12,0,2,8,'0008674251',20171118,1051,1,1510973477,1,1510973477,0,NULL,0),(13,0,2,8,'0008674251',20171118,1051,0,1510973490,1,1510973490,0,NULL,0),(14,0,2,8,'0008674251',20171118,1052,0,1510973556,1,1510973556,0,NULL,0),(15,0,2,9,'0008697203',20171118,1052,1,1510973564,1,1510973565,0,NULL,0),(16,0,2,3,'0008600838',20171118,1054,1,1510973644,1,1510973644,0,NULL,0),(17,0,2,3,'0008600838',20171118,1055,0,1510973759,1,1510973759,0,NULL,0),(18,0,2,3,'0008600838',20171118,1058,0,1510973887,1,1510973887,0,NULL,0),(19,0,2,3,'0008600838',20171118,1058,0,1510973918,1,1510973918,0,NULL,0),(20,0,2,3,'0008600838',20171118,1059,0,1510973996,1,1510973996,0,NULL,0),(21,0,2,9,'0008697203',20171118,1100,0,1510974020,1,1510974020,0,NULL,0),(22,0,2,3,'0008600838',20171118,1100,0,1510974031,1,1510974031,0,NULL,0),(23,0,2,3,'0008600838',20171118,1101,0,1510974064,1,1510974064,0,NULL,0),(24,0,2,3,'0008600838',20171118,1101,0,1510974100,1,1510974100,0,NULL,0),(25,0,2,3,'0008600838',20171118,1102,0,1510974179,1,1510974179,0,NULL,0),(26,0,2,3,'0008600838',20171118,1107,0,1510974444,1,1510974444,0,NULL,0),(27,0,2,3,'0008600838',20171118,1108,0,1510974489,1,1510974489,0,NULL,0),(28,0,2,3,'0008600838',20171118,1108,0,1510974519,1,1510974519,0,NULL,0),(29,0,2,1,'0008659832',20171118,1108,0,1510974529,1,1510974529,0,NULL,0),(30,0,2,1,'0008659832',20171118,1113,0,1510974788,1,1510974788,0,NULL,0),(31,0,2,3,'0008600838',20171118,1113,0,1510974793,1,1510974793,0,NULL,0),(32,0,2,3,'0008600838',20171118,1114,0,1510974897,1,1510974897,0,NULL,0),(33,0,2,1,'0008659832',20171118,1115,0,1510974900,1,1510974900,0,NULL,0),(34,0,2,3,'0008600838',20171118,1115,0,1510974921,1,1510974921,0,NULL,0),(35,0,2,1,'0008659832',20171118,1115,0,1510974924,1,1510974924,0,NULL,0),(36,0,2,3,'0008600838',20171118,1116,0,1510975006,1,1510975006,0,NULL,0),(37,0,2,3,'0008600838',20171118,1116,0,1510975008,1,1510975008,0,NULL,0),(38,0,2,1,'0008659832',20171118,1116,0,1510975012,1,1510975012,0,NULL,0),(39,0,2,1,'0008659832',20171118,1116,0,1510975018,1,1510975018,0,NULL,0),(40,0,2,3,'0008600838',20171118,1117,0,1510975030,1,1510975030,0,NULL,0),(41,0,2,1,'0008659832',20171118,1117,0,1510975037,1,1510975037,0,NULL,0),(42,0,2,1,'0008659832',20171118,1118,0,1510975110,1,1510975110,0,NULL,0),(43,0,2,3,'0008600838',20171118,1118,0,1510975115,1,1510975115,0,NULL,0),(44,0,2,1,'0008659832',20171118,1118,0,1510975121,1,1510975121,0,NULL,0),(45,0,2,3,'0008600838',20171118,1120,0,1510975233,1,1510975233,0,NULL,0),(46,0,2,1,'0008659832',20171118,1120,0,1510975241,1,1510975241,0,NULL,0),(47,0,2,1,'0008659832',20171118,1120,0,1510975252,1,1510975252,0,NULL,0),(48,0,2,3,'0008600838',20171118,1120,0,1510975258,1,1510975258,0,NULL,0),(49,0,2,1,'0008659832',20171118,1121,0,1510975276,1,1510975276,0,NULL,0),(50,0,2,1,'0008659832',20171118,1121,0,1510975305,1,1510975305,0,NULL,0),(51,0,2,3,'0008600838',20171118,1121,0,1510975313,1,1510975313,0,NULL,0),(52,0,2,1,'0008659832',20171118,1122,0,1510975323,1,1510975323,0,NULL,0),(53,0,2,3,'0008600838',20171118,1126,0,1510975596,1,1510975596,0,NULL,0),(54,0,2,1,'0008659832',20171118,1126,0,1510975619,1,1510975619,0,NULL,0),(55,0,2,3,'0008600838',20171118,1127,0,1510975622,1,1510975622,0,NULL,0),(56,0,2,1,'0008659832',20171118,1127,0,1510975671,1,1510975671,0,NULL,0),(57,0,2,3,'0008600838',20171118,1128,0,1510975699,1,1510975699,0,NULL,0),(58,0,2,1,'0008659832',20171118,1128,0,1510975702,1,1510975702,0,NULL,0),(59,0,2,3,'0008600838',20171118,1128,0,1510975715,1,1510975715,0,NULL,0),(60,0,2,1,'0008659832',20171118,1128,0,1510975717,1,1510975717,0,NULL,0),(61,0,2,3,'0008600838',20171118,1128,0,1510975722,1,1510975722,0,NULL,0),(62,0,2,1,'0008659832',20171118,1128,0,1510975731,1,1510975731,0,NULL,0),(63,0,2,3,'0008600838',20171118,1128,0,1510975739,1,1510975739,0,NULL,0),(64,0,2,1,'0008659832',20171118,1129,0,1510975747,1,1510975747,0,NULL,0),(65,0,2,3,'0008600838',20171118,1131,0,1510975870,1,1510975870,0,NULL,0),(66,0,2,1,'0008659832',20171118,1131,0,1510975881,1,1510975881,0,NULL,0),(67,0,2,3,'0008600838',20171118,1131,0,1510975917,1,1510975917,0,NULL,0),(68,0,2,1,'0008659832',20171118,1132,0,1510975925,1,1510975925,0,NULL,0),(69,0,2,3,'0008600838',20171118,1132,0,1510975938,1,1510975938,0,NULL,0),(70,0,2,3,'0008600838',20171118,1136,0,1510976205,1,1510976205,0,NULL,0),(71,0,2,1,'0008659832',20171118,1136,0,1510976209,1,1510976209,0,NULL,0),(72,0,2,3,'0008600838',20171118,1136,0,1510976216,1,1510976216,0,NULL,0),(73,0,2,1,'0008659832',20171118,1137,0,1510976220,1,1510976220,0,NULL,0),(74,0,2,3,'0008600838',20171118,1137,0,1510976237,1,1510976237,0,NULL,0),(75,0,2,1,'0008659832',20171118,1137,0,1510976244,1,1510976244,0,NULL,0),(76,0,2,3,'0008600838',20171118,1139,0,1510976342,1,1510976342,0,NULL,0),(77,0,2,1,'0008659832',20171118,1139,0,1510976348,1,1510976348,0,NULL,0),(78,0,2,3,'0008600838',20171118,1139,0,1510976353,1,1510976353,0,NULL,0),(79,0,2,3,'0008600838',20171118,1139,0,1510976386,1,1510976386,0,NULL,0),(80,0,2,1,'0008659832',20171118,1139,0,1510976389,1,1510976389,0,NULL,0),(81,0,2,3,'0008600838',20171118,1139,0,1510976396,1,1510976396,0,NULL,0),(82,0,2,3,'0008600838',20171118,1141,0,1510976475,1,1510976475,0,NULL,0),(83,0,2,1,'0008659832',20171118,1141,0,1510976479,1,1510976479,0,NULL,0),(84,0,2,3,'0008600838',20171118,1141,0,1510976482,1,1510976482,0,NULL,0),(85,0,2,1,'0008659832',20171118,1141,0,1510976486,1,1510976486,0,NULL,0),(86,0,2,3,'0008600838',20171118,1141,0,1510976519,1,1510976519,0,NULL,0),(87,0,2,1,'0008659832',20171118,1142,0,1510976523,1,1510976523,0,NULL,0),(88,0,2,3,'0008600838',20171118,1142,0,1510976544,1,1510976544,0,NULL,0),(89,0,2,1,'0008659832',20171118,1143,0,1510976586,1,1510976586,0,NULL,0),(90,0,2,3,'0008600838',20171118,1146,0,1510976813,1,1510976813,0,NULL,0),(91,0,2,3,'0008600838',20171118,1148,0,1510976920,1,1510976920,0,NULL,0),(92,0,2,1,'0008659832',20171118,1148,0,1510976923,1,1510976923,0,NULL,0),(93,0,2,3,'0008600838',20171118,1149,0,1510976954,1,1510976954,0,NULL,0),(94,0,2,3,'0008600838',20171118,1149,0,1510976957,1,1510976957,0,NULL,0),(95,0,2,3,'0008600838',20171118,1149,0,1510976960,1,1510976960,0,NULL,0),(96,0,2,1,'0008659832',20171118,1149,0,1510976965,1,1510976965,0,NULL,0),(97,0,2,3,'0008600838',20171118,1149,0,1510976986,1,1510976986,0,NULL,0),(98,0,2,3,'0008600838',20171118,1149,0,1510976999,1,1510976999,0,NULL,0),(99,0,2,3,'0008600838',20171118,1150,0,1510977018,1,1510977018,0,NULL,0),(100,0,2,3,'0008600838',20171118,1150,0,1510977023,1,1510977023,0,NULL,0),(101,0,2,3,'0008600838',20171118,1151,0,1510977082,1,1510977082,0,NULL,0),(102,0,2,3,'0008600838',20171118,1151,0,1510977089,1,1510977089,0,NULL,0),(103,0,2,1,'0008659832',20171118,1151,0,1510977117,1,1510977117,0,NULL,0),(104,0,2,3,'0008600838',20171118,1152,0,1510977122,1,1510977122,0,NULL,0),(105,0,2,1,'0008659832',20171118,1152,0,1510977128,1,1510977128,0,NULL,0),(106,0,2,3,'0008600838',20171118,1152,0,1510977131,1,1510977131,0,NULL,0),(107,0,2,1,'0008659832',20171118,1152,0,1510977135,1,1510977135,0,NULL,0),(108,0,2,3,'0008600838',20171118,1152,0,1510977168,1,1510977168,0,NULL,0),(109,0,2,3,'0008600838',20171118,1153,0,1510977187,1,1510977187,0,NULL,0),(110,0,2,1,'0008659832',20171118,1153,0,1510977190,1,1510977190,0,NULL,0),(111,0,2,3,'0008600838',20171118,1153,0,1510977193,1,1510977193,0,NULL,0),(112,0,2,1,'0008659832',20171118,1155,0,1510977313,1,1510977313,0,NULL,0),(113,0,2,1,'0008659832',20171118,1155,0,1510977330,1,1510977330,0,NULL,0),(114,0,2,1,'0008659832',20171118,1157,0,1510977420,1,1510977420,0,NULL,0),(115,0,2,1,'0008659832',20171118,1157,0,1510977430,1,1510977430,0,NULL,0),(116,0,2,1,'0008659832',20171118,1157,0,1510977440,1,1510977440,0,NULL,0),(117,0,2,1,'0008659832',20171118,1201,0,1510977706,1,1510977706,0,NULL,0),(118,0,2,1,'0008659832',20171118,1202,0,1510977739,1,1510977739,0,NULL,0),(119,0,2,1,'0008659832',20171118,1206,0,1510977990,1,1510977990,0,NULL,0),(120,0,2,1,'0008659832',20171118,1207,0,1510978041,1,1510978041,0,NULL,0),(121,0,2,3,'0008600838',20171118,1207,0,1510978045,1,1510978045,0,NULL,0),(122,0,2,1,'0008659832',20171118,1207,0,1510978050,1,1510978050,0,NULL,0),(123,0,2,3,'0008600838',20171118,1207,0,1510978054,1,1510978054,0,NULL,0),(124,0,2,1,'0008659832',20171118,1207,0,1510978075,1,1510978075,0,NULL,0),(125,0,2,3,'0008600838',20171118,1208,0,1510978105,1,1510978105,0,NULL,0),(126,0,2,3,'0008600838',20171118,1208,0,1510978115,1,1510978115,0,NULL,0),(127,0,2,1,'0008659832',20171118,1208,0,1510978123,1,1510978123,0,NULL,0),(128,0,2,1,'0008659832',20171118,1213,0,1510978385,1,1510978385,0,NULL,0),(129,0,2,3,'0008600838',20171118,1213,0,1510978388,1,1510978388,0,NULL,0),(130,0,2,1,'0008659832',20171118,1213,0,1510978394,1,1510978394,0,NULL,0),(131,0,2,3,'0008600838',20171118,1213,0,1510978400,1,1510978400,0,NULL,0),(132,0,2,1,'0008659832',20171118,1213,0,1510978408,1,1510978408,0,NULL,0),(133,0,2,3,'0008600838',20171118,1213,0,1510978414,1,1510978414,0,NULL,0),(134,0,2,1,'0008659832',20171118,1213,0,1510978417,1,1510978417,0,NULL,0),(135,0,2,3,'0008600838',20171118,1213,0,1510978419,1,1510978419,0,NULL,0),(136,0,2,3,'0008600838',20171118,1213,0,1510978423,1,1510978423,0,NULL,0),(137,0,2,1,'0008659832',20171118,1213,0,1510978424,1,1510978424,0,NULL,0),(138,0,2,3,'0008600838',20171118,1213,0,1510978427,1,1510978427,0,NULL,0),(139,0,2,1,'0008659832',20171118,1213,0,1510978428,1,1510978428,0,NULL,0),(140,0,2,1,'0008659832',20171118,1215,0,1510978522,1,1510978522,0,NULL,0),(141,0,2,3,'0008600838',20171118,1215,0,1510978523,1,1510978523,0,NULL,0),(142,0,2,1,'0008659832',20171118,1215,0,1510978525,1,1510978525,0,NULL,0),(143,0,2,3,'0008600838',20171118,1215,0,1510978526,1,1510978526,0,NULL,0),(144,0,2,1,'0008659832',20171118,1215,0,1510978528,1,1510978528,0,NULL,0),(145,0,2,3,'0008600838',20171118,1215,0,1510978531,1,1510978531,0,NULL,0),(146,0,2,1,'0008659832',20171118,1215,0,1510978536,1,1510978536,0,NULL,0),(147,0,2,1,'0008659832',20171118,1223,0,1510979032,1,1510979032,0,NULL,0),(148,0,2,3,'0008600838',20171118,1226,0,1510979177,1,1510979177,0,NULL,0),(149,0,2,3,'0008600838',20171118,1230,0,1510979455,1,1510979455,0,NULL,0),(150,0,2,1,'0008659832',20171118,1232,0,1510979559,1,1510979559,0,NULL,0),(151,0,2,1,'0008659832',20171118,1405,0,1510985130,1,1510985130,0,NULL,0),(152,0,2,3,'0008600838',20171118,1405,0,1510985136,1,1510985136,0,NULL,0),(153,0,2,1,'0008659832',20171118,1406,0,1510985162,1,1510985162,0,NULL,0),(154,0,2,3,'0008600838',20171118,1406,0,1510985166,1,1510985166,0,NULL,0),(155,0,2,1,'0008659832',20171118,1406,0,1510985171,1,1510985171,0,NULL,0),(156,0,2,1,'0008659832',20171118,1406,0,1510985179,1,1510985179,0,NULL,0),(157,0,2,1,'0008659832',20171118,1522,0,1510989774,1,1510989774,0,NULL,0),(158,0,2,3,'0008600838',20171118,1525,0,1510989924,1,1510989924,0,NULL,0),(159,0,2,2,'0000042061',20171120,1008,0,1511143716,1,1511143716,0,NULL,0),(160,0,2,3,'0008600838',20171120,1200,0,1511150405,1,1511150405,0,NULL,0),(161,0,2,3,'0008600838',20171120,1201,0,1511150478,1,1511150478,0,NULL,0),(162,0,2,3,'0008600838',20171120,1203,0,1511150582,1,1511150582,0,NULL,0),(163,0,2,3,'0008600838',20171120,1203,0,1511150599,1,1511150599,0,NULL,0),(164,0,2,3,'0008600838',20171120,1207,0,1511150860,1,1511150860,0,NULL,0),(165,0,2,3,'0008600838',20171120,1208,0,1511150903,1,1511150903,0,NULL,0),(166,0,2,3,'0008600838',20171120,1210,0,1511151056,1,1511151056,0,NULL,0),(167,0,2,2,'0000042061',20171120,1211,0,1511151111,1,1511151111,0,NULL,0),(168,0,2,4,'0000094341',20171120,1216,0,1511151376,1,1511151376,0,NULL,0),(169,0,2,1,'0008659832',20171120,1219,0,1511151552,1,1511151552,0,NULL,0),(170,0,2,3,'0008600838',20171120,1221,0,1511151696,1,1511151696,0,NULL,0),(171,0,2,8,'0008674251',20171121,1516,0,1511248575,1,1511248575,0,NULL,0),(172,0,2,8,'0008674251',20171121,1516,0,1511248588,1,1511248588,0,NULL,0),(173,0,2,8,'0008674251',20171121,1518,0,1511248707,1,1511248707,0,NULL,0),(174,0,2,9,'0008697203',20171121,1519,0,1511248773,1,1511248773,0,NULL,0),(175,0,2,9,'0008697203',20171121,1521,0,1511248864,1,1511248864,0,NULL,0),(176,0,2,9,'0008697203',20171121,1523,0,1511249023,1,1511249023,0,NULL,0),(177,0,2,9,'0008697203',20171121,1526,0,1511249190,1,1511249190,0,NULL,0),(178,0,2,9,'0008697203',20171121,1529,0,1511249365,1,1511249365,0,NULL,0),(179,0,2,9,'0008697203',20171121,1531,1,1511249483,1,1511249484,0,NULL,0),(180,0,2,9,'0008697203',20171121,1531,0,1511249505,1,1511249505,0,NULL,0),(181,0,2,9,'0008697203',20171121,1532,0,1511249535,1,1511249535,0,NULL,0),(182,0,2,9,'0008697203',20171121,1533,0,1511249597,1,1511249597,0,NULL,0),(183,0,2,9,'0008697203',20171121,1534,0,1511249641,1,1511249641,0,NULL,0),(184,0,2,9,'0008697203',20171121,1534,0,1511249648,1,1511249648,0,NULL,0),(185,0,2,9,'0008697203',20171121,1534,0,1511249649,1,1511249649,0,NULL,0),(186,0,2,9,'0008697203',20171121,1534,0,1511249651,1,1511249651,0,NULL,0),(187,0,2,9,'0008697203',20171121,1534,0,1511249657,1,1511249657,0,NULL,0),(188,0,2,9,'0008697203',20171121,1538,1,1511249881,1,1511249881,0,NULL,0),(189,0,2,8,'0008674251',20171121,1538,1,1511249922,1,1511249922,0,NULL,0),(190,0,2,8,'0008674251',20171121,1538,0,1511249939,1,1511249939,0,NULL,0),(191,0,2,9,'0008697203',20171121,1540,1,1511250001,1,1511250001,0,NULL,0),(192,0,2,8,'0008674251',20171121,1540,1,1511250005,1,1511250005,0,NULL,0),(193,0,2,8,'0008674251',20171122,928,0,1511314106,1,1511314106,0,NULL,0),(194,0,2,9,'0008697203',20171122,928,0,1511314112,1,1511314112,0,NULL,0),(195,0,2,8,'0008674251',20171122,928,0,1511314120,1,1511314120,0,NULL,0),(196,0,2,2,'0000042061',20171122,929,0,1511314142,1,1511314142,0,NULL,0),(197,0,2,1,'0008659832',20171122,929,0,1511314145,1,1511314145,0,NULL,0),(198,0,2,4,'0000094341',20171122,929,0,1511314147,1,1511314147,0,NULL,0),(199,0,2,3,'0008600838',20171122,929,0,1511314150,1,1511314150,0,NULL,0),(200,0,2,1,'0008659832',20171213,2043,1,1513168985,1,1513168985,0,NULL,0),(201,0,2,1,'0008659832',20171213,2043,0,1513169004,1,1513169004,0,NULL,0),(202,0,2,1,'0008659832',20171213,2043,0,1513169021,1,1513169021,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8mb4 COMMENT='帐户流水表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_tally`
--

LOCK TABLES `x360p_tally` WRITE;
/*!40000 ALTER TABLE `x360p_tally` DISABLE KEYS */;
INSERT INTO `x360p_tally` VALUES (1,0,2,1,0,132,2,0,0,0,0,0,3600,'',20171123,1510971618,1,1510971618,0,NULL,0),(2,0,2,1,0,133,2,0,0,0,0,0,3600,'',20171123,1510971695,1,1510971695,0,NULL,0),(3,0,2,1,0,134,2,0,0,0,0,0,3600,'',20171123,1510971718,1,1510971718,0,NULL,0),(4,0,2,1,0,135,2,0,0,0,0,0,3600,'',20171123,1510971764,1,1510971764,0,NULL,0),(5,0,2,1,0,136,2,0,0,0,0,0,3600,'',20171123,1510971975,1,1510971975,0,NULL,0),(6,0,2,1,0,137,2,0,0,0,0,0,3600,'',20171123,1510971977,1,1510971977,0,NULL,0),(7,0,2,1,0,138,2,0,0,0,0,0,3600,'',20171123,1510972024,1,1510972024,0,NULL,0),(8,0,2,1,0,139,2,0,0,0,0,0,3600,'',20171123,1510972042,1,1510972042,0,NULL,0),(9,0,2,1,0,140,2,0,0,0,0,0,3600,'',20171123,1510972045,1,1510972045,0,NULL,0),(10,0,2,1,0,141,2,0,0,0,0,0,3600,'',20171123,1510974276,1,1510974276,0,NULL,0),(11,0,2,1,0,142,2,0,0,0,0,0,120,'',20171123,1510993877,1,1510993877,0,NULL,0),(12,0,2,1,0,143,2,0,0,0,0,0,200,'',20171123,1510994219,1,1510994219,0,NULL,0),(13,0,2,1,0,144,2,0,0,0,0,0,200,'',20171123,1510994693,1,1510994693,0,NULL,0),(14,0,2,1,0,145,2,0,0,0,0,0,200,'',20171123,1510994712,1,1510994712,0,NULL,0),(15,0,2,1,0,146,2,0,0,0,0,0,200,'',20171123,1510994740,1,1510994740,0,NULL,0),(16,0,2,1,0,147,2,0,0,0,0,0,200,'',20171123,1510994756,1,1510994756,0,NULL,0),(17,0,2,1,0,148,2,0,0,0,0,0,200,'',20171123,1510994770,1,1510994770,0,NULL,0),(18,0,2,1,0,149,2,0,0,0,0,0,6800,'',20171123,1510995734,1,1510995734,0,NULL,0),(19,0,2,1,0,150,2,0,0,0,0,0,6500,'',20171123,1510996786,1,1510996786,0,NULL,0),(20,0,2,1,0,151,2,0,0,0,0,0,3000,'',20171123,1511004143,1,1511004143,0,NULL,0),(21,0,2,1,0,152,2,0,0,0,0,0,5400,'',20171123,1511004217,1,1511004217,0,NULL,0),(22,0,2,1,0,153,2,0,0,0,0,0,24424,'',20171123,1511144986,1,1511144986,0,NULL,0),(23,0,2,1,0,154,2,0,0,0,0,0,81000,'',20171123,1511151653,1,1511151653,0,NULL,0),(24,0,2,1,0,155,2,0,0,0,0,0,9000,'',20171123,1511161354,1,1511161354,0,NULL,0),(25,0,2,2,0,1,2,0,0,0,0,0,17639,'',20171123,1511162033,1,1511162033,0,NULL,0),(26,0,2,2,0,2,2,0,0,0,0,0,3000,'',20171123,1511164074,1,1511164074,0,NULL,0),(27,0,2,1,0,156,2,0,0,0,0,0,1400,'',20171123,1511257691,1,1511257691,0,NULL,0),(28,0,2,1,0,157,2,0,0,0,0,0,1400,'',20171123,1511257709,1,1511257709,0,NULL,0),(29,0,2,1,0,158,2,0,0,0,0,0,1400,'',20171123,1511323518,1,1511323518,0,NULL,0),(30,0,2,1,0,159,2,0,0,0,0,0,1400,'',20171123,1511323616,1,1511323616,0,NULL,0),(31,0,2,1,0,160,2,0,0,0,0,0,598500,'',20171123,1511334001,1,1511334001,0,NULL,0),(32,0,2,1,1,161,2,0,0,0,0,0,1200,'',20171122,1511345233,1,1511345233,0,NULL,0),(33,0,2,1,1,162,2,0,0,0,0,0,1400,'',20171122,1511345331,1,1511345331,0,NULL,0),(34,0,2,1,1,163,2,0,0,0,0,0,200,'',20171122,1511346067,1,1511346067,0,NULL,0),(35,0,2,1,1,164,2,0,0,0,0,0,1300,'',20171122,1511354434,1,1511354434,0,NULL,0),(36,0,2,1,1,165,2,0,0,0,0,0,1100,'',20171122,1511354475,1,1511354475,0,NULL,0),(37,0,2,1,1,0,5,0,2,7,4,6,5000,'年费',20171123,1511403763,1,1511406137,0,NULL,0),(38,0,2,2,2,0,5,0,4,2,1,3,1000,'年费啊',20171123,1511404260,1,1511406094,0,NULL,0),(42,0,3,2,5,0,6,5,0,2,1,3,3000,'',20171123,1511437045,1,1511437045,0,NULL,0),(43,0,3,1,5,0,5,6,0,2,1,3,3000,'',20171123,1511437045,1,1511437045,0,NULL,0),(44,0,3,1,4,0,6,5,0,0,0,0,2000,'',20171123,1511437437,1,1511437437,0,NULL,0),(45,0,3,2,4,0,5,6,0,0,0,0,2000,'',20171123,1511437437,1,1511437437,0,NULL,0),(46,0,3,1,1,0,45,0,0,0,0,0,100,'创建帐户初始金额',20171127,1511748585,1,1511748585,0,NULL,0),(47,0,2,1,1,166,2,0,0,0,0,0,10200,'',20171129,1511943146,1,1511943146,0,NULL,0),(48,0,2,1,1,167,2,0,0,0,0,0,10200,'',20171129,1511943352,1,1511943352,0,NULL,0),(49,0,2,1,1,168,2,0,0,0,0,0,400,'',20171129,1511944947,1,1511944947,0,NULL,0),(50,0,2,1,1,169,2,0,0,0,0,0,900,'',20171129,1511944999,1,1511944999,0,NULL,0),(51,0,2,1,1,170,2,0,0,0,0,0,16120,'',20171129,1511946840,1,1511946840,0,NULL,0),(52,0,2,1,1,171,2,0,0,0,0,0,6800,'',20171129,1511946896,1,1511946896,0,NULL,0),(53,0,2,1,1,172,2,0,0,0,0,0,5400,'',20171129,1511946953,1,1511946953,0,NULL,0),(54,0,2,1,1,173,2,0,0,0,0,0,12000,'',20171129,1511947045,1,1511947045,0,NULL,0),(55,0,2,1,1,174,2,0,0,0,0,0,200,'',20171129,1511947107,1,1511947107,0,NULL,0),(56,0,2,1,1,175,2,0,0,0,0,0,2700,'',20171129,1511947296,1,1511947296,0,NULL,0),(57,0,2,1,1,176,2,0,0,0,0,0,8000,'',20171130,1512004807,1,1512004807,0,NULL,0),(58,0,2,1,1,177,2,0,0,0,0,0,9000,'',20171130,1512005529,1,1512005529,0,NULL,0),(59,0,2,1,1,178,2,0,0,0,0,0,12200,'',20171130,1512005549,1,1512005549,0,NULL,0),(60,0,2,2,2,3,2,0,0,0,0,0,2820,'',20171130,1512005689,1,1512005689,0,NULL,0),(61,0,2,1,1,179,2,0,0,0,0,0,12000,'',20171130,1512028636,1,1512028636,0,NULL,0),(62,0,2,1,1,180,2,0,0,0,0,0,32600,'',20171130,1512036356,1,1512036356,0,NULL,0),(63,0,2,1,1,181,2,0,0,0,0,0,12702,'',20171130,1512036526,1,1512036526,0,NULL,0),(64,0,2,1,1,182,2,0,0,0,0,0,1601,'',20171130,1512036599,1,1512036599,0,NULL,0),(65,0,2,1,1,183,2,0,0,0,0,0,5792,'',20171201,1512090620,1,1512090620,0,NULL,0),(66,0,2,1,1,184,2,0,0,0,0,0,5403,'',20171201,1512090733,1,1512090733,0,NULL,0),(67,0,2,1,1,185,2,0,0,0,0,0,5403,'',20171201,1512098994,1,1512098994,0,NULL,0),(68,0,2,1,1,186,2,0,0,0,0,0,12000,'',20171201,1512099589,1,1512099589,0,NULL,0),(69,0,2,1,1,187,2,0,0,0,0,0,5795,'',20171201,1512099627,1,1512099627,0,NULL,0),(70,0,2,1,1,188,2,0,0,0,0,0,203,'',20171201,1512099640,1,1512099640,0,NULL,0),(71,0,2,1,1,189,2,0,0,0,0,0,195,'',20171201,1512099651,1,1512099651,0,NULL,0),(72,0,2,2,2,4,2,0,0,0,0,0,180,'',20171201,1512110147,1,1512110147,0,NULL,0),(73,0,2,2,2,5,2,0,0,0,0,0,157,'',20171201,1512110165,1,1512110165,0,NULL,0),(74,0,2,2,2,6,2,0,0,0,0,0,180,'',20171201,1512110192,1,1512110192,0,NULL,0),(75,0,2,2,2,7,2,0,0,0,0,0,200,'',20171201,1512110444,1,1512110444,0,NULL,0),(76,0,2,1,1,190,2,0,0,0,0,0,6050,'',20171201,1512111149,1,1512111149,0,NULL,0),(77,0,2,1,1,191,2,0,0,0,0,0,2500,'',20171201,1512111302,1,1512111302,0,NULL,0),(78,0,2,1,1,192,2,0,0,0,0,0,9093,'',20171201,1512116680,1,1512116680,0,NULL,0),(79,0,2,1,1,193,2,0,0,0,0,0,6803,'',20171201,1512117268,1,1512117268,0,NULL,0),(80,0,2,1,1,194,2,0,0,0,0,0,200,'',20171201,1512119102,1,1512119102,0,NULL,0),(81,0,2,1,1,195,2,0,0,0,0,0,4590,'',20171201,1512120542,1,1512120542,0,NULL,0),(82,0,2,1,1,196,2,0,0,0,0,0,9000,'',20171201,1512122645,1,1512122645,0,NULL,0),(83,0,2,1,1,197,2,0,0,0,0,0,1651,'',20171201,1512123379,1,1512123379,0,NULL,0),(84,0,4,1,1,198,4,0,0,0,0,0,2900,'',20171204,1512350735,1,1512350735,0,NULL,0),(85,0,2,1,1,199,2,0,0,0,0,0,5693,'',20171205,1512465297,1,1512465297,0,NULL,0),(86,0,2,1,1,200,2,0,0,0,0,0,5000,'',20171205,1512469292,1,1512469292,0,NULL,0),(87,0,2,1,1,201,2,0,0,0,0,0,200,'',20171206,1512532147,1,1512532147,0,NULL,0),(88,0,2,1,1,202,2,0,0,0,0,0,1,'',20171206,1512532314,1,1512532314,0,NULL,0),(89,0,2,1,1,203,2,0,0,0,0,0,1,'',20171206,1512533285,1,1512533285,0,NULL,0),(90,0,2,1,1,204,2,0,0,0,0,0,1,'',20171206,1512533390,1,1512533390,0,NULL,0),(91,0,2,1,1,205,2,0,0,0,0,0,1,'',20171206,1512533433,1,1512533433,0,NULL,0),(92,0,2,1,1,206,2,0,0,0,0,0,5000,'',20171206,1512533556,1,1512533556,0,NULL,0),(93,0,2,1,1,207,2,0,0,0,0,0,11,'',20171206,1512533736,1,1512533736,0,NULL,0),(94,0,2,1,1,208,2,0,0,0,0,0,1681,'',20171206,1512534428,1,1512534428,0,NULL,0),(95,0,2,1,1,209,2,0,0,0,0,0,1,'',20171206,1512540608,1,1512540608,0,NULL,0),(96,0,2,1,1,210,2,0,0,0,0,0,1,'',20171206,1512540682,1,1512540682,0,NULL,0),(97,0,2,1,1,211,2,0,0,0,0,0,1,'',20171206,1512541724,1,1512541724,0,NULL,0),(98,0,2,1,1,212,2,0,0,0,0,0,5000,'',20171206,1512541852,1,1512541852,0,NULL,0),(99,0,2,1,1,213,2,0,0,0,0,0,5000,'',20171206,1512546480,1,1512546480,0,NULL,0),(100,0,2,1,1,214,1,0,0,0,0,0,490,'',20171206,1512546480,1,1512546480,0,NULL,0),(101,0,2,2,2,8,2,0,0,0,0,0,540,'',20171206,1512546836,1,1512546836,0,NULL,0),(102,0,2,1,1,215,2,0,0,0,0,0,3600,'',20171206,1512549245,1,1512549245,0,NULL,0),(103,0,2,1,1,216,2,0,0,0,0,0,3600,'',20171206,1512549247,1,1512549247,0,NULL,0),(104,0,2,2,2,9,2,0,0,0,0,0,120,'',20171206,1512549280,1,1512549280,0,NULL,0),(105,0,2,2,2,10,2,0,0,0,0,0,120,'',20171206,1512549479,1,1512549479,0,NULL,0),(106,0,2,1,1,217,2,0,0,0,0,0,3600,'',20171206,1512549483,1,1512549483,0,NULL,0),(107,0,2,1,1,218,2,0,0,0,0,0,3600,'',20171206,1512549684,1,1512549684,0,NULL,0),(108,0,2,2,2,11,2,0,0,0,0,0,120,'',20171206,1512549731,1,1512549731,0,NULL,0),(109,0,2,2,2,12,2,0,0,0,0,0,240,'',20171206,1512549837,1,1512549837,0,NULL,0),(110,0,2,2,2,13,2,0,0,0,0,0,120,'',20171206,1512550071,1,1512550071,0,NULL,0),(111,0,2,2,2,14,2,0,0,0,0,0,540,'',20171206,1512550156,1,1512550156,0,NULL,0),(112,0,2,2,2,15,2,0,0,0,0,0,120,'',20171206,1512555977,1,1512555977,0,NULL,0),(113,0,2,2,2,16,2,0,0,0,0,0,120,'',20171206,1512556049,1,1512556049,0,NULL,0),(114,0,2,2,2,17,2,0,0,0,0,0,120,'',20171206,1512556140,1,1512556140,0,NULL,0),(115,0,2,2,2,18,2,0,0,0,0,0,120,'',20171206,1512556268,1,1512556268,0,NULL,0),(116,0,2,1,1,219,2,0,0,0,0,0,10000,'',20171206,1512556368,1,1512556368,0,NULL,0),(117,0,2,1,1,220,2,0,0,0,0,0,90,'',20171207,1512611348,1,1512611348,0,NULL,0),(118,0,2,1,1,221,2,0,0,0,0,0,900,'',20171207,1512612857,1,1512612857,0,NULL,0),(119,0,2,2,2,19,2,0,0,0,0,0,4320,'',20171207,1512612953,1,1512612953,0,NULL,0),(120,0,2,1,1,222,2,0,0,0,0,0,2,'',20171207,1512613263,1,1512613263,0,NULL,0),(121,0,2,1,1,223,2,0,0,0,0,0,1098,'',20171207,1512613276,1,1512613276,0,NULL,0),(122,0,2,2,2,20,2,0,0,0,0,0,90,'',20171207,1512613804,1,1512613804,0,NULL,0),(123,0,2,1,1,224,2,0,0,0,0,0,48,'',20171207,1512614181,1,1512614181,0,NULL,0),(124,0,2,1,1,225,2,0,0,0,0,0,3600,'',20171207,1512615958,1,1512615958,0,NULL,0),(125,0,2,1,1,226,2,0,0,0,0,0,3600,'',20171207,1512615974,1,1512615974,0,NULL,0),(126,0,2,1,1,227,2,0,0,0,0,0,600,'',20171207,1512617146,1,1512617146,0,NULL,0),(127,0,2,1,1,228,2,0,0,0,0,0,400,'',20171207,1512617931,1,1512617931,0,NULL,0),(128,0,2,1,1,229,2,0,0,0,0,0,200,'',20171207,1512618093,1,1512618093,0,NULL,0),(129,0,2,1,1,230,2,0,0,0,0,0,1400,'',20171207,1512618205,1,1512618205,0,NULL,0),(130,0,2,1,1,231,2,0,0,0,0,0,900,'',20171207,1512618266,1,1512618266,0,NULL,0),(131,0,2,1,1,232,2,0,0,0,0,0,200,'',20171207,1512618505,1,1512618505,0,NULL,0),(132,0,2,1,1,233,2,0,0,0,0,0,1400,'',20171207,1512630478,1,1512630478,0,NULL,0),(133,0,2,1,1,234,2,0,0,0,0,0,200,'',20171207,1512630543,1,1512630543,0,NULL,0),(134,0,2,1,1,235,2,0,2,0,0,0,201,'',20171207,1512630769,1,1512636535,0,NULL,0),(135,0,2,1,1,236,2,0,0,0,0,0,1400,'',20171208,1512701582,1,1512701582,0,NULL,0),(136,0,2,1,1,237,2,0,0,0,0,0,6600,'订单付款流水，收据号：EVO20171208123005',20171208,1512707405,1,1512707405,0,NULL,0),(137,0,2,1,1,238,2,0,0,0,0,0,2024,'订单付款流水，收据号：WAT20171208140330',20171208,1512713010,1,1512713010,0,NULL,0),(138,0,2,1,1,239,2,0,0,0,0,0,1012,'订单付款流水，收据号：VBU20171208140620',20171208,1512713180,1,1512713180,0,NULL,0),(139,0,2,1,1,240,2,0,0,0,0,0,1012,'订单付款流水，收据号：NGY20171208140955',20171208,1512713395,1,1512713395,0,NULL,0),(140,0,2,1,1,241,2,0,0,0,0,0,200,'订单付款流水，收据号：RJS20171208141319',20171208,1512713599,1,1512713599,0,NULL,0),(141,0,2,1,1,242,2,0,0,0,0,0,506,'订单付款流水，收据号：RPS20171208141502',20171208,1512713702,1,1512713702,0,NULL,0),(142,0,2,1,1,243,2,0,0,0,0,0,3600,'订单付款流水，收据号：CLE20171208153302',20171208,1512718382,1,1512718382,0,NULL,0),(143,0,2,1,1,244,2,0,0,0,0,0,8003,'订单付款流水，收据号：EUY20171208153909',20171208,1512718749,1,1512718749,0,NULL,0),(144,0,2,1,1,245,2,0,0,0,0,0,2,'',20171212,1513065163,1,1513065163,0,NULL,0),(145,0,2,1,1,246,2,0,0,0,0,0,1200,'',20171212,1513065195,1,1513065195,0,NULL,0),(146,0,2,1,1,247,2,0,0,0,0,0,4106,'订单付款流水，收据号：FRE20171212163820',20171212,1513067900,1,1513067900,0,NULL,0),(147,0,2,1,1,248,2,0,0,0,0,0,1806,'订单付款流水，收据号：USY20171212173615',20171212,1513071375,1,1513071375,0,NULL,0),(148,0,2,1,1,249,2,0,0,0,0,0,4503,'订单付款流水，收据号：DSV20171212173859',20171212,1513071539,1,1513071539,0,NULL,0),(149,0,2,1,1,250,2,0,0,0,0,0,903,'订单付款流水，收据号：KVH20171212174309',20171212,1513071789,1,1513071789,0,NULL,0),(150,0,2,1,1,251,2,0,0,0,0,0,7200,'订单付款流水，收据号：OFT20171212174404',20171212,1513071844,1,1513071844,0,NULL,0),(151,0,2,1,1,252,2,0,0,0,0,0,4106,'订单付款流水，收据号：OQC20171212174548',20171212,1513071948,1,1513071948,0,NULL,0),(152,0,2,1,1,253,2,0,0,0,0,0,506,'订单付款流水，收据号：PRB20171212174634',20171212,1513071994,1,1513071994,0,NULL,0),(153,0,2,1,1,254,2,0,0,0,0,0,903,'订单付款流水，收据号：QKY20171212174905',20171212,1513072145,1,1513072145,0,NULL,0),(154,0,2,1,1,255,2,0,0,0,0,0,4106,'订单付款流水，收据号：FIK20171212175002',20171212,1513072202,1,1513072202,0,NULL,0),(155,0,2,1,1,256,2,0,0,0,0,0,903,'订单付款流水，收据号：JFN20171212175048',20171212,1513072248,1,1513072248,0,NULL,0),(156,0,2,1,1,257,2,0,0,0,0,0,903,'订单付款流水，收据号：TDK20171212175218',20171212,1513072338,1,1513072338,0,NULL,0),(157,0,2,1,1,258,2,0,0,0,0,0,903,'订单付款流水，收据号：ERB20171212175326',20171212,1513072406,1,1513072406,0,NULL,0),(158,0,2,1,1,259,2,0,0,0,0,0,1806,'订单付款流水，收据号：SZY20171212180118',20171212,1513072879,1,1513072879,0,NULL,0),(159,0,2,1,1,260,2,0,0,0,0,0,1806,'订单付款流水，收据号：GTU20171212181659',20171212,1513073819,1,1513073819,0,NULL,0),(160,0,2,1,1,261,2,0,0,0,0,0,2306,'订单付款流水，收据号：CUK20171212200109',20171212,1513080069,1,1513080069,0,NULL,0),(161,0,2,1,1,262,2,0,0,0,0,0,4106,'订单付款流水，收据号：CTS20171212201844',20171212,1513081124,1,1513081124,0,NULL,0),(162,0,2,1,1,263,2,0,0,0,0,0,6600,'订单付款流水，收据号：VND20171212203231',20171212,1513081951,1,1513081951,0,NULL,0),(163,0,2,1,1,264,2,0,0,0,0,0,3600,'订单付款流水，收据号：FET20171212203331',20171212,1513082011,1,1513082011,0,NULL,0),(164,0,2,1,1,265,2,0,0,0,0,0,3600,'订单付款流水，收据号：YLA20171212203355',20171212,1513082035,1,1513082035,0,NULL,0),(165,0,2,1,1,266,2,0,0,0,0,0,1409,'订单付款流水，收据号：FTE20171213094016',20171213,1513129216,1,1513129216,0,NULL,0),(166,0,2,1,1,267,2,0,0,0,0,0,4503,'订单付款流水，收据号：TQD20171213105703',20171213,1513133823,1,1513133823,0,NULL,0),(167,0,2,1,1,268,2,0,0,0,0,0,410,'订单付款流水，收据号：ENI20171214172954',20171214,1513243794,1,1513243794,0,NULL,0),(168,0,2,1,1,269,2,0,0,0,0,0,65595,'订单付款流水，收据号：KPJ20171214173126',20171214,1513243886,1,1513243886,0,NULL,0),(169,0,2,1,1,270,2,0,0,0,0,0,1600,'',20171221,1513819745,1,1513819745,0,NULL,0),(170,0,2,1,1,271,2,0,0,0,0,0,2000,'',20171221,1513819850,1,1513819850,0,NULL,0),(171,0,2,1,1,272,2,0,0,0,0,0,1600,'订单付款流水，收据号：CTS20171221104552',20171221,1513824352,1,1513824352,0,NULL,0),(172,0,2,1,1,273,2,0,0,0,0,0,1000,'',20171221,1513824931,1,1513824931,0,NULL,0),(173,0,2,1,1,274,2,0,0,0,0,0,916,'订单付款流水，收据号：UQG20171221110128',20171221,1513825288,1,1513825288,0,NULL,0),(174,0,2,1,1,275,2,0,0,0,0,0,2000,'订单付款流水，收据号：AHO20171221111557',20171221,1513826157,1,1513826157,0,NULL,0),(175,0,2,1,1,276,2,0,0,0,0,0,3600,'订单付款流水，收据号：EVD20171221111845',20171221,1513826325,1,1513826325,0,NULL,0),(176,0,2,1,1,277,2,0,0,0,0,0,10706,'',20171221,1513837503,1,1513837503,0,NULL,0);
/*!40000 ALTER TABLE `x360p_tally` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_tally_help`
--

LOCK TABLES `x360p_tally_help` WRITE;
/*!40000 ALTER TABLE `x360p_tally_help` DISABLE KEYS */;
INSERT INTO `x360p_tally_help` VALUES (1,0,'client','浪腾软件','教务管理系统',1511312962,1,NULL,0,NULL,NULL),(2,0,'item','年费','每年维护费用',1511312990,1,NULL,0,NULL,NULL),(3,0,'employee','刘子云','前端技术啊',1511313025,1,NULL,0,NULL,NULL),(4,0,'client','浪腾海风','海风技术部',1511334901,1,1511334901,0,NULL,NULL),(5,0,'client','阳光喔','客户',1511334960,1,1511488509,0,NULL,NULL),(6,0,'employee','罗燕强','PHP',1511400742,1,1511400742,0,NULL,NULL),(7,0,'item','数据维护费','一年5000',1511400815,1,1511400815,0,NULL,NULL),(8,0,'client','学而思','培训机构啊啊',1511488684,1,1511488695,1,1511488695,1);
/*!40000 ALTER TABLE `x360p_tally_help` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `x360p_tally_type`
--

LOCK TABLES `x360p_tally_type` WRITE;
/*!40000 ALTER TABLE `x360p_tally_type` DISABLE KEYS */;
INSERT INTO `x360p_tally_type` VALUES (1,0,1,0,'加盟分红','无备注',1511320218,1,NULL,0,NULL,NULL),(2,0,1,1,'罗湖分校','罗湖分校加盟商',1511321327,1,NULL,0,NULL,NULL),(3,0,2,0,'软件年费','一年50000',1511323406,1,1511330878,0,NULL,NULL),(4,0,2,3,'系统维护费','3000',1511323431,1,NULL,0,NULL,NULL),(5,0,2,3,'数据维护费','数据维护费2000',1511323475,1,NULL,0,NULL,NULL),(6,0,1,0,'投资收入','投资回报',1511323536,1,NULL,0,NULL,NULL),(7,0,1,6,'投资浪腾','收入5000000',1511324983,1,NULL,0,NULL,NULL),(8,0,2,0,'交通费','',1511487674,1,1511488495,1,1511488495,1),(9,0,2,8,'地铁费啊','一个月3000',1511487688,1,1511488490,1,1511488490,1);
/*!40000 ALTER TABLE `x360p_tally_type` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COMMENT='时间段表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_time_section`
--

LOCK TABLES `x360p_time_section` WRITE;
/*!40000 ALTER TABLE `x360p_time_section` DISABLE KEYS */;
INSERT INTO `x360p_time_section` VALUES (1,0,0,'H',7,1,800,1000,'',1510971421,1,1510971421,0,NULL,0),(2,0,0,'H',1,1,1900,2100,'',1510971442,1,1510971442,0,NULL,0),(3,0,0,'H',2,3,1900,2100,'',1510971450,1,1513673177,0,NULL,0),(4,0,0,'H',3,1,1900,2100,'',1510971450,1,1510971450,0,NULL,0),(5,0,0,'H',4,1,1900,2100,'',1510971450,1,1510971450,0,NULL,0),(6,0,0,'H',5,1,1900,2100,'',1510971450,1,1510971450,0,NULL,0),(7,0,0,'H',6,1,800,1000,'',1510971457,1,1510971457,0,NULL,0),(8,0,0,'H',7,2,1030,1230,'',1510971481,1,1510971481,0,NULL,0),(9,0,0,'H',6,2,1030,1230,'',1510971486,1,1510971486,0,NULL,0),(10,0,2,'H',7,1,800,1000,'',1510972052,1,1510972052,0,NULL,0),(11,0,2,'H',7,3,1030,1230,'',1510972066,1,1513063348,0,NULL,0),(12,0,2,'H',6,1,800,1000,'',1510972075,1,1510972075,0,NULL,0),(13,0,2,'H',6,2,1030,1230,'',1510972078,1,1510972078,0,NULL,0),(14,0,2,'H',1,2,1900,2130,'',1510972098,1,1511150504,0,NULL,0),(15,0,2,'H',5,2,1900,2130,'',1510972107,1,1511150742,0,NULL,0),(16,0,2,'H',4,1,1900,2130,'',1510972107,1,1510972107,0,NULL,0),(17,0,2,'H',3,1,1900,2130,'',1510972107,1,1510972107,0,NULL,0),(18,0,2,'H',2,1,1900,2130,'',1510972107,1,1510972107,0,NULL,0),(19,0,3,'H',1,1,1900,2130,'',1510972115,1,1510972115,0,NULL,0),(20,0,3,'H',2,1,1900,2130,'',1510972120,1,1510972120,0,NULL,0),(21,0,3,'H',3,1,1900,2130,'',1510972120,1,1510972120,0,NULL,0),(22,0,3,'H',4,1,1900,2130,'',1510972120,1,1510972120,0,NULL,0),(23,0,3,'H',5,1,1900,2130,'',1510972120,1,1510972120,0,NULL,0),(24,0,3,'H',6,1,800,1000,'',1510972131,1,1510972144,0,NULL,0),(25,0,3,'H',6,2,1030,1230,'',1510972151,1,1510972151,0,NULL,0),(26,0,3,'H',7,1,800,1000,'',1510972155,1,1510972155,0,NULL,0),(27,0,3,'H',7,2,1030,1230,'',1510972158,1,1510972158,0,NULL,0),(28,0,2,'S',6,2,1030,1045,'10:30-10:45',1510972196,1,1511149816,0,NULL,0),(29,0,2,'S',6,3,1100,1115,'11:00~10:15',1510972223,1,1511149816,0,NULL,0),(30,0,2,'S',6,4,1115,1130,'11:15~11:30',1510972262,1,1511149816,0,NULL,0),(31,0,0,'Q',7,1,800,1000,'',1510972263,1,1510972263,0,NULL,0),(32,0,0,'Q',7,2,1030,1230,'',1510972272,1,1510972272,0,NULL,0),(33,0,2,'S',6,5,1130,1145,'11:30~11:45',1510972276,1,1511149816,0,NULL,0),(34,0,0,'Q',6,1,800,1000,'',1510972276,1,1510972276,0,NULL,0),(35,0,0,'Q',6,2,1030,1230,'',1510972279,1,1510972279,0,NULL,0),(36,0,0,'Q',1,1,1900,2130,'',1510972291,1,1510972291,0,NULL,0),(37,0,0,'Q',2,1,1900,2130,'',1510972296,1,1510972296,0,NULL,0),(38,0,0,'Q',3,1,1900,2130,'',1510972296,1,1510972296,0,NULL,0),(39,0,0,'Q',4,1,1900,2130,'',1510972296,1,1510972296,0,NULL,0),(40,0,0,'Q',5,1,1900,2130,'',1510972296,1,1510972296,0,NULL,0),(41,0,2,'S',6,6,1145,1200,'11:45~12:00',1510972296,1,1511149816,0,NULL,0),(42,0,2,'S',6,7,1200,1215,'12:00~12:15',1510972317,1,1511149816,0,NULL,0),(43,0,3,'Q',7,1,800,1000,'',1510972336,1,1510972336,0,NULL,0),(44,0,3,'Q',7,2,1030,1230,'',1510972347,1,1510972347,0,NULL,0),(45,0,3,'Q',6,1,800,1000,'',1510972352,1,1510972352,0,NULL,0),(46,0,3,'Q',6,2,1030,1230,'',1510972355,1,1510972355,0,NULL,0),(47,0,3,'Q',1,1,1900,2100,'',1510972368,1,1510972368,0,NULL,0),(48,0,3,'Q',2,1,1900,2100,'',1510972375,1,1510972375,0,NULL,0),(49,0,3,'Q',3,1,1900,2100,'',1510972375,1,1510972375,0,NULL,0),(50,0,3,'Q',4,1,1900,2100,'',1510972375,1,1510972375,0,NULL,0),(51,0,3,'Q',5,1,1900,2100,'',1510972375,1,1510972375,0,NULL,0),(52,0,2,'S',7,1,1400,1445,'14:00~14:45',1510995660,1,1510995660,0,NULL,0),(53,0,2,'S',7,2,1700,1745,'17:00~17:45',1510995930,1,1510995930,0,NULL,0),(54,0,2,'S',6,1,115,200,'01:15~02:00',1511149816,1,1511149816,0,NULL,0),(55,0,2,'S',2,1,115,200,'01:15~02:00',1511149851,1,1511149851,0,NULL,0),(56,0,2,'S',4,1,445,200,'04:45~02:00',1511150237,1,1511150237,0,NULL,0),(57,0,2,'S',5,1,445,200,'04:45~02:00',1511150319,1,1511150319,0,NULL,0),(58,0,2,'H',1,1,545,815,'05:45~08:15',1511150504,1,1511150504,0,NULL,0),(59,0,2,'H',5,1,545,815,'05:45~08:15',1511150742,1,1511150742,0,NULL,0),(60,0,0,'C',1,1,1830,2030,'',1511161097,1,1511161097,0,NULL,0),(61,0,0,'C',2,1,1830,2030,'',1511161129,1,1511161129,0,NULL,0),(62,0,0,'C',3,1,1830,2030,'',1511161136,1,1511161136,0,NULL,0),(63,0,0,'S',1,2,1830,2030,'',1511161143,1,1511161159,0,NULL,0),(64,0,0,'S',1,1,900,1100,'',1511161159,1,1511161159,0,NULL,0),(65,0,0,'S',2,1,900,1100,'',1511161211,1,1511161211,0,NULL,0),(66,0,0,'S',3,1,900,1100,'',1511161219,1,1511161219,0,NULL,0),(67,0,2,'H',7,2,800,1015,'',1513063348,1,1513063348,0,NULL,0),(68,0,0,'H',2,1,100,300,'',1513666827,1,1513666827,0,NULL,0),(69,0,0,'H',2,2,300,500,'',1513673177,1,1513673177,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='试听安排记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_trial_listen_arrange`
--

LOCK TABLES `x360p_trial_listen_arrange` WRITE;
/*!40000 ALTER TABLE `x360p_trial_listen_arrange` DISABLE KEYS */;
INSERT INTO `x360p_trial_listen_arrange` VALUES (1,0,2,0,0,1,0,5,6,58,10002,20171125,800,1000,1,0,0,1510998861,1,1511247820,0,NULL,0),(2,0,2,0,0,1,0,1,1,21,10002,20171118,1500,1515,0,0,0,1510998953,1,1510998953,0,NULL,0),(3,0,2,0,0,1,0,1,1,9,10002,20171118,1000,1200,2,0,0,1510999033,1,1510999033,0,NULL,0),(4,0,2,0,0,5,0,5,6,66,0,20171201,1900,2130,0,0,0,1511337123,1,1511337123,0,NULL,0),(5,0,2,0,0,204,0,5,6,53,0,20171120,1900,2130,0,0,0,1512353740,1,1512353740,0,NULL,0),(6,0,2,1,0,0,43,5,6,69,10006,20171203,800,1000,1,1,0,1512355393,1,1512381378,0,NULL,0),(7,0,2,1,0,0,41,5,6,69,10006,20171203,800,1000,1,1,0,1512355393,1,1512381379,0,NULL,0),(8,0,2,0,0,204,0,5,6,69,10006,20171203,800,1000,1,0,0,1512355393,1,1512381379,0,NULL,0),(9,0,2,1,1,0,69,1,0,139,10004,20171208,2000,2045,0,0,0,1512727389,1,1512727389,0,NULL,0),(10,0,2,0,1,207,0,4,0,140,10003,20171208,2300,2330,0,0,0,1512728716,1,1512728716,0,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=10066 DEFAULT CHARSET=utf8mb4 COMMENT='用户表(机构用户和学生用户2类)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_user`
--

LOCK TABLES `x360p_user` WRITE;
/*!40000 ALTER TABLE `x360p_user` DISABLE KEYS */;
INSERT INTO `x360p_user` VALUES (1,0,'admin','','','管理员','1',1,'123456','5f1d7a84db00d2fce00b31a7fc73224f','http://s10.xiao360.com//x360puser_avatar/1/17/12/04/f29a6e24323727fb995602be786f4c78.jpeg','',0,1,1,0,1513837467,'192.168.3.14',218,0,1,0,1,1512006402,0,0,NULL,0,1505209347,'1c8a81376eb86f482fad1662766e6d5d'),(10001,0,'17768026489','17768026485','yaorui@qq.com','姚瑞','1',1,'LSY3En','1d05cb1d6e30b92d14227c4069b78496','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/7954068ab8b53b242c66acd05321dc1b.jpeg','',0,0,0,0,0,'',0,1510972195,0,0,0,1512627770,1,0,NULL,0,NULL,NULL),(10002,0,'18617076286','18617076286','87740070@qq.com','刘子云','1',1,'lzJr8M','b0bd76cb92de098007327bd426edcf20','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/6a4b02a7012f905927bf2fbc62ce70d0.png','',0,0,0,0,0,'',0,1510992932,1,0,0,1512376858,1,0,NULL,0,NULL,NULL),(10003,0,'17768026499','17768026499','56224589@qq.com','老师10004','1',1,'1Jt8Un','2c90121eec7f74b83fa75482d44e0dd6','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/937c1873a9d6f772694c46e9dfc44149.jpeg','',0,0,0,0,0,'',0,1511256940,1,0,0,1512440643,1,0,NULL,0,NULL,NULL),(10004,0,'admin@base','15623658950','5623@qq.com','老师10005','1',1,'dhMoyp','f9d529d6868b5f529ad55ff8ce1552e5','','',0,0,0,0,0,'',0,1511343537,1,0,0,1511495986,1,1,1511495986,1,NULL,NULL),(10005,0,'madongmei','18886865656','5555@qq.com','马冬梅','2',1,'NKlya2','f35cff11b14a23be16c4f3460c83dbf6','','',0,0,0,0,0,'',0,1511353834,1,0,0,1511353834,1,0,NULL,0,NULL,NULL),(10006,0,'huahua@base.com','13132323345','www.lantel@qq.com','李花花','2',1,'TQx9am','0ccd549475ac0ea5f96b0b3405a1bcce','','',0,0,0,0,0,'',0,1511495109,1,0,0,1511609197,1,0,NULL,0,NULL,NULL),(10007,0,'dddddd','15233434344','','dfdfs','1',1,'dAN02x','6ee4cd3b82736f7b36c251ba770aed12','','',0,0,0,0,0,'',0,1511610986,1,0,0,1511610986,1,0,NULL,0,NULL,NULL),(10008,0,'18475486545','18475486545','erd@qq.com','黄老板','1',1,'dtPtZS','fb6f7aa5522920c66a388a73c80a9932','','',0,0,0,0,0,'',0,1511831437,1,0,0,1512445783,1,0,NULL,0,NULL,NULL),(10009,0,'huahua@base.com','13124222344','4699996969@qq.com','德玛西亚','1',1,'tQYXcL','7099f9584d36969f22c16e23772877d2','','',0,0,0,0,0,'',0,1511836687,1,0,0,1511843380,1,0,NULL,0,NULL,NULL),(10010,0,'debzg','13124233444','ddddd@qq.com','德邦总管','1',1,'IEJkm2','712c4c3da9171a840328de961428fcb3','','',0,0,0,0,0,'',0,1511837092,0,0,0,1511840192,1,0,NULL,0,NULL,NULL),(10011,0,'guanliyuan','15234342429','','时光','1',1,'8aqw9n','061d7fbea058b2b256c7cc163fffa134','','',0,0,0,0,0,'',0,1511839761,1,0,0,1511839761,1,0,NULL,0,NULL,NULL),(10012,0,'haha','13125132038','yiganchagnqiangn@163.com','赵子龙','1',1,'P5gsA8','a10c52032f0fd125525b0c9202a561a2','','',0,0,0,0,1513769045,'192.168.3.111',6,1511842333,1,0,0,1513769003,1,0,NULL,0,NULL,NULL),(10013,0,'mumu','13123333334','','阿木木','1',1,'fyD1Fx','c787043f546ac94f5bdb6985d687ac04','','',0,0,0,0,0,'',0,1511845989,1,0,0,1512008473,1,0,NULL,0,NULL,NULL),(10014,0,'kesi@lantel.com','18123423333','','金克斯','1',1,'miOeLM','140068da3c5d7abe7a1963a9589a193c','','',0,0,0,0,0,'',0,1511920164,0,0,0,1512008484,1,0,NULL,0,NULL,NULL),(10015,0,'liupeihong','13006617502','','刘培红','1',1,'PgkV1b','cf5e574cf3c67b5fb86255593964af04','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/04/305f49ff244ef7d908ebe3a07848c72e.jpeg','',0,0,0,0,1513754465,'192.168.3.111',4,1512046220,1,0,0,1513760972,1,0,NULL,0,NULL,NULL),(10016,0,'18128874427','18128874427','','','0',2,'ETj39L','77cb81d5e68419430e94907e3a03bbd3','','',46,0,0,0,0,'',0,1512097253,1,0,0,1512097253,1,0,NULL,0,NULL,NULL),(10017,0,'18128874428','18128874428','','','0',2,'qcyAQj','61eb31e422c43ef3217023d39378a683','','',47,0,0,0,0,'',0,1512097366,1,0,0,1512097366,1,0,NULL,0,NULL,NULL),(10018,0,'11111111111','11111111111','','father','0',2,'PHLm0C','e037e23e2861c639fc9fa13c27b9826b','','',48,0,0,0,0,'',0,1512098199,1,0,0,1513667221,1,0,NULL,0,NULL,NULL),(10019,0,'17768026491','17768026491','','mother','0',2,'6brBhc','f8ae5151b05949f0b6d135558dda4a9a','','',48,0,0,0,0,'',0,1512098199,1,0,0,1512098199,1,0,NULL,0,NULL,NULL),(10020,0,'15132324435','15132324435','','','0',2,'Pv2Tvi','6a0596319124c12dac519afc802e96f4','','',49,0,0,0,0,'',0,1512099589,1,0,0,1512099589,1,0,NULL,0,NULL,NULL),(10021,0,'13928412238','13928412238','','','0',2,'xuFdWb','61d16f8dbdd6974f05f7340f03cc000d','','',50,0,0,0,0,'',0,1512350735,1,0,0,1512350735,1,0,NULL,0,NULL,NULL),(10022,0,'13928412281','13928412281','','','0',2,'dmP9RL','7f49aa959a4a094c391f7493532e936f','','',51,0,0,0,0,'',0,1512372280,1,0,0,1512372280,1,0,NULL,0,NULL,NULL),(10023,0,'13123422221','13123422221','','','0',2,'aKWcvW','7162337b72f7e5d70e37b619243500ff','','',52,0,0,0,0,'',0,1512465297,1,0,0,1512465297,1,0,NULL,0,NULL,NULL),(10024,0,'15134564565','15134564565','','','0',2,'8j9WqO','589f4535d80d73599595cc8662eaae20','','',53,0,0,0,0,'',0,1512529320,1,0,0,1512529320,1,0,NULL,0,NULL,NULL),(10025,0,'18878956324','18878956324','','','0',2,'XN9cKV','a8ce0655a7bb882f9e2685c9711e1280','','',54,0,0,0,0,'',0,1512548091,1,0,0,1512548091,1,0,NULL,0,NULL,NULL),(10026,0,'15532154253','15532154253','','','0',2,'o1PWaC','7ea58d279597f058e688c54b90f89c94','','',55,0,0,0,0,'',0,1512549034,1,0,0,1512549034,1,0,NULL,0,NULL,NULL),(10027,0,'15454845645','15454845645','','','0',2,'r0GBjc','5a5f6c7d4974544e3b0209367c3093d6','','',56,0,0,0,0,'',0,1512549247,1,0,0,1512549247,1,0,NULL,0,NULL,NULL),(10028,0,'18932123333','18932123333','','','0',2,'aqowjo','a35a57ab3e6a28b790fe4dd2a8d3b4db','','',57,0,0,0,0,'',0,1512549483,1,0,0,1512549483,1,0,NULL,0,NULL,NULL),(10029,0,'liuphb','15568789856','','马云','1',1,'MSvFlL','61bda201d0fad9f55475017520d73336','http://s10.xiao360.com//x360pemployee_avatar/1/17/12/14/cd216bccf99221803fec2b9ea270216a.jpeg','',0,0,0,0,0,'',0,1512549866,1,0,0,1513760961,1,0,NULL,0,NULL,NULL),(10030,0,'15054254856','15054254856','','','0',2,'knNGHs','fd6c6fedad0076780bf825cb21f4b289','','',58,0,0,0,0,'',0,1512611347,1,0,0,1512611347,1,0,NULL,0,NULL,NULL),(10031,0,'15064851234','15064851234','','','0',2,'sydgJ5','39ccca200b4e31033aab5f02bcd7c809','','',59,0,0,0,0,'',0,1512614181,1,0,0,1512614181,1,0,NULL,0,NULL,NULL),(10032,0,'15354868478','15354868478','','','0',2,'FdRjE1','8b68c78ecd18536366fb9787deda1676','','',60,0,0,0,0,'',0,1512617145,1,0,0,1512617145,1,0,NULL,0,NULL,NULL),(10033,0,'15248456263','15248456263','','','0',2,'SwlpBD','864b61a8976eb87cb87d9db353e58e4c','','',61,0,0,0,0,'',0,1512617931,1,0,0,1512617931,1,0,NULL,0,NULL,NULL),(10034,0,'18745123654','18745123654','','','0',2,'RxiRPG','dfaee72a26a49c972d55eff0ba8f29b4','','',62,0,0,0,0,'',0,1512618093,1,0,0,1512618093,1,0,NULL,0,NULL,NULL),(10035,0,'15846523154','15846523154','','','0',2,'rnPnUN','755df4255b612e1ab8765aaf37066160','','',63,0,0,0,0,'',0,1512618204,1,0,0,1512618204,1,0,NULL,0,NULL,NULL),(10036,0,'15958452156','15958452156','','','0',2,'IfCXx1','50c2f5046f866599cbff9b310823d6a4','','',64,0,0,0,0,'',0,1512618266,1,0,0,1512618266,1,0,NULL,0,NULL,NULL),(10037,0,'15451236544','15451236544','','','0',2,'Xl2sGL','c9b27be44a1ef08977d882d53368b684','','',65,0,0,0,0,'',0,1512618505,1,0,0,1512618505,1,0,NULL,0,NULL,NULL),(10038,0,'13124223123','13124223123','','','0',2,'FtfTfF','6b82ea70f35836da1d9b8aa178827649','','',66,0,0,0,0,'',0,1512713010,1,0,0,1512713010,1,0,NULL,0,NULL,NULL),(10039,0,'15334234248','15334234248','','','0',2,'PDRkSX','2efaddfe5ac4f057a03f57377c77cb8a','','',67,0,0,0,0,'',0,1512713180,1,0,0,1512713180,1,0,NULL,0,NULL,NULL),(10040,0,'13123232133','13123232133','','','0',2,'krAqck','2036aedde8fee6ae17403dc035030311','','',68,0,0,0,0,'',0,1512713395,1,0,0,1512713395,1,0,NULL,0,NULL,NULL),(10041,0,'18316229898','18316229898','','','0',2,'qdqUfA','fa31ac8d1678cddc363ea0520a77e42a','','',69,0,0,0,0,'',0,1512713598,1,0,0,1512713598,1,0,NULL,0,NULL,NULL),(10042,0,'18943423333','18943423333','','','0',2,'XCOTxB','125e8c34a71ddaa4958371c49989dfe8','','',70,0,0,0,0,'',0,1512713702,1,0,0,1512713702,1,0,NULL,0,NULL,NULL),(10043,0,'15684535123','15684535123','','','0',2,'X5QNd9','7dcc430781290b0fc80e0f813dd227be','','',71,0,0,0,0,'',0,1512730668,1,0,0,1512730668,1,0,NULL,0,NULL,NULL),(10044,0,'15123423221','15123423221','','','0',2,'lZ5NO4','005398cb6bffa62be4e748c95c46d04c','','',72,0,0,0,0,'',0,1513058791,1,0,0,1513058791,1,0,NULL,0,NULL,NULL),(10045,0,'15486875648','15486875648','','黄鹤','0',2,'Fmz9iw','cd32ef98614f7ab00321f0d953a9d987','','',73,0,0,0,0,'',0,1513063100,1,0,0,1513063100,1,0,NULL,0,NULL,NULL),(10046,0,'13123231322','13123231322','','','0',2,'JeQymn','578541ed9352be26b6790c20e3e0d429','','',74,0,0,0,0,'',0,1513072202,1,0,0,1513072202,1,0,NULL,0,NULL,NULL),(10047,0,'18790997660','18790997660','','','0',2,'MCHEtZ','2ad89a346d5c276dbe7ba993032f920a','','',75,0,0,0,0,'',0,1513072878,1,0,0,1513072878,1,0,NULL,0,NULL,NULL),(10048,0,'15234324244','15234324244','','','0',2,'lHTDEs','8de44ef12ae5ed0c8493e9d5b6b9b3f6','','',76,0,0,0,0,'',0,1513073819,1,0,0,1513073819,1,0,NULL,0,NULL,NULL),(10049,0,'13124343234','13124343234','','','0',2,'ijsGok','2a6ee4ed1f22bb7ed6fb80820819bd59','','',77,0,0,0,0,'',0,1513080069,1,0,0,1513080069,1,0,NULL,0,NULL,NULL),(10050,0,'13123232123','13123232123','','','0',2,'VPQMar','7e3effdcf373f295ec37d1668535448e','','',78,0,0,0,0,'',0,1513081124,1,0,0,1513081124,1,0,NULL,0,NULL,NULL),(10051,0,'15345435545','15345435545','','','0',2,'wvwpyI','23d584f88a6d64b7818a6f862368c137','','',79,0,0,0,1513758909,'192.168.3.171',6,1513133823,1,0,0,1513652565,1,0,NULL,0,NULL,NULL),(10052,0,'15345435542','18617076286','','','0',2,'Y4yZKe','d9cdcada8bc4957b72daaa2b49bf2c34','','',80,0,0,0,0,'',0,1513334523,1,0,0,1513651324,1,0,NULL,0,NULL,NULL),(10053,0,'sss','','','','0',1,'yxLHoB','06cb4a5ba571b3157835fee2e664a1a4','','',0,0,0,0,0,'',0,1513591455,1,0,1,1513591455,1,0,NULL,0,NULL,NULL),(10054,0,'yitong','','','','0',1,'WwOrib','d6db11526d08759ac9d32cc859fe41c6','','',0,0,0,0,0,'',0,1513591727,1,0,1,1513591727,1,0,NULL,0,NULL,NULL),(10055,0,'xiaoxydg','','','','0',1,'5Gebfb','022e448253b7a861a97d23a3df07aa4b','','',0,0,0,0,0,'',0,1513591946,1,0,1,1513591946,1,0,NULL,0,NULL,NULL),(10056,0,'xutong','','','','0',1,'HGsLRl','45456e37829c2d2300dd946e2a22a840','','',0,0,0,0,0,'',0,1513596832,1,0,1,1513596832,1,0,NULL,0,NULL,NULL),(10057,10,'sihan','','','','0',1,'r4hOIP','6f3b9ea756e92fd9dc4b73d07a0f3cbf','','',0,0,0,0,0,'',0,1513650832,0,0,1,1513665402,1,1,1513665402,1,NULL,NULL),(10058,0,'17768026499','17768026499','','','0',2,'qSr4m5','0110b83b35d38cdfe830b9b719d8ecad','','',82,0,0,0,0,'',0,1513653082,1,0,0,1513653297,1,0,NULL,0,NULL,NULL),(10059,0,'17768026498','17768026498','','father','0',2,'MQdkig','d7a153b411370ce9d460f0d2494b90cc','','',82,0,0,0,0,'',0,1513653121,1,0,0,1513653121,1,0,NULL,0,NULL,NULL),(10060,0,'22222222222','22222222222','','','0',2,'VRz1j4','1a70d0b059beac3c970762da888cb9d5','','',83,0,0,0,0,'',0,1513657215,1,0,0,1513669308,1,0,NULL,0,NULL,NULL),(10061,15,'guapicaozuo','','','','0',1,'iOTjYG','26f4e1dabc95136ecee49838f8a19e2a','','',0,0,0,0,0,'',0,1513657356,1,1,1,1513657356,1,0,NULL,0,NULL,NULL),(10062,16,'siyi','','','','0',1,'SisFNG','622cc1dcd08634b17f6829a229546580','','',0,0,0,0,0,'',0,1513657811,1,1,1,1513665391,1,1,1513665391,1,NULL,NULL),(10063,0,'17768026485','17768026485','','','0',2,'0uEJwi','cae04e01cf3d010ff8f999b67ba3ab53','','',84,0,0,0,0,'',0,1513666929,1,0,0,1513671276,1,0,NULL,0,NULL,NULL),(10064,0,'18128874425','18128874425','','','0',2,'Cf4vlO','1f03caed5996e23865082abf5f3fdc51','','',85,0,0,0,0,'',0,1513670836,1,0,0,1513670836,1,0,NULL,0,NULL,NULL),(10065,0,'18617076286','18617076286','','刘大','0',2,'u3iI9K','53a8bb0fdfdb56479f20fde0496e7d4d','','',87,0,0,0,1513821389,'192.168.3.171',4,1513732735,1,0,0,1513732735,1,0,NULL,0,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COMMENT='用户学生表(每个用户账号可以绑定1到多个学生)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x360p_user_student`
--

LOCK TABLES `x360p_user_student` WRITE;
/*!40000 ALTER TABLE `x360p_user_student` DISABLE KEYS */;
INSERT INTO `x360p_user_student` VALUES (1,0,10017,47,NULL,0,NULL,0,NULL,0),(3,0,10019,48,NULL,0,NULL,0,NULL,0),(4,0,10020,49,NULL,0,NULL,0,NULL,0),(5,0,10021,50,NULL,0,NULL,0,NULL,0),(6,0,10022,51,NULL,0,NULL,0,NULL,0),(7,0,10023,52,NULL,0,NULL,0,NULL,0),(8,0,10024,53,NULL,0,NULL,0,NULL,0),(9,0,10025,54,NULL,0,NULL,0,NULL,0),(10,0,10026,55,NULL,0,NULL,0,NULL,0),(11,0,10027,56,NULL,0,NULL,0,NULL,0),(12,0,10028,57,NULL,0,NULL,0,NULL,0),(13,0,10030,58,NULL,0,NULL,0,NULL,0),(14,0,10031,59,NULL,0,NULL,0,NULL,0),(15,0,10032,60,NULL,0,NULL,0,NULL,0),(16,0,10033,61,NULL,0,NULL,0,NULL,0),(17,0,10034,62,NULL,0,NULL,0,NULL,0),(18,0,10035,63,NULL,0,NULL,0,NULL,0),(19,0,10036,64,NULL,0,NULL,0,NULL,0),(20,0,10037,65,NULL,0,NULL,0,NULL,0),(21,0,10038,66,NULL,0,NULL,0,NULL,0),(22,0,10039,67,NULL,0,NULL,0,NULL,0),(23,0,10040,68,NULL,0,NULL,0,NULL,0),(24,0,10041,69,NULL,0,NULL,0,NULL,0),(25,0,10042,70,NULL,0,NULL,0,NULL,0),(26,0,10043,71,NULL,0,NULL,0,NULL,0),(27,0,10044,72,NULL,0,NULL,0,NULL,0),(28,0,10045,73,NULL,0,NULL,0,NULL,0),(29,0,10046,74,NULL,0,NULL,0,NULL,0),(30,0,10047,75,NULL,0,NULL,0,NULL,0),(31,0,10048,76,NULL,0,NULL,0,NULL,0),(32,0,10049,77,NULL,0,NULL,0,NULL,0),(33,0,10050,78,NULL,0,NULL,0,NULL,0),(34,0,10051,79,NULL,0,NULL,0,NULL,0),(35,0,10052,80,NULL,0,NULL,0,NULL,0),(36,0,10051,81,NULL,0,NULL,0,NULL,0),(69,0,10060,83,NULL,0,NULL,0,NULL,0),(71,0,10060,84,NULL,0,NULL,0,NULL,0),(73,0,10063,48,NULL,0,NULL,0,NULL,0),(74,0,10063,82,NULL,0,NULL,0,NULL,0),(75,0,10063,83,NULL,0,NULL,0,NULL,0),(78,0,10063,84,NULL,0,NULL,0,NULL,0),(79,0,10064,85,NULL,0,NULL,0,NULL,0),(80,0,10063,86,NULL,0,NULL,0,NULL,0),(81,0,10064,86,NULL,0,NULL,0,NULL,0),(82,0,10063,85,NULL,0,NULL,0,NULL,0),(83,0,10065,87,NULL,0,NULL,0,NULL,0);
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

--
-- Dumping data for table `x360p_wxmp_template`
--

LOCK TABLES `x360p_wxmp_template` WRITE;
/*!40000 ALTER TABLE `x360p_wxmp_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `x360p_wxmp_template` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-21 14:31:16
