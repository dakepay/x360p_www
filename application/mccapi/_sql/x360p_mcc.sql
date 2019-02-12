/*
 Navicat Premium Data Transfer

 Source Server         : root@119.23.163.198
 Source Server Type    : MySQL
 Source Server Version : 50719
 Source Host           : 119.23.163.198:3306
 Source Schema         : x360p_mcc

 Target Server Type    : MySQL
 Target Server Version : 50719
 File Encoding         : 65001

 Date: 28/03/2018 17:12:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for x360p_cc_class
-- ----------------------------
DROP TABLE IF EXISTS `x360p_cc_class`;
CREATE TABLE `x360p_cc_class` (
  `cc_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cu_id` int(11) NOT NULL COMMENT '创建者ID',
  `cid` int(11) DEFAULT NULL COMMENT '校360客户ID',
  `og_id` int(11) DEFAULT NULL COMMENT '校360机构ID',
  `class_name` varchar(64) NOT NULL COMMENT '班级名称',
  `my_name` varchar(64) DEFAULT NULL COMMENT '创建者名称',
  `my_contact` varchar(16) DEFAULT NULL COMMENT '创建者联系方式',
  `class_img` varchar(255) DEFAULT NULL COMMENT '班级形象图',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for x360p_cc_class_feed
-- ----------------------------
DROP TABLE IF EXISTS `x360p_cc_class_feed`;
CREATE TABLE `x360p_cc_class_feed` (
  `ccf_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cu_id` int(10) unsigned NOT NULL COMMENT '发布者ID',
  `cc_id` int(10) unsigned NOT NULL COMMENT '班级ID',
  `feed_type` tinyint(3) unsigned NOT NULL COMMENT '1：动态 2：公告 3：作业',
  `feed_content` text COMMENT '内容',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ccf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for x360p_cc_class_feed_attachment
-- ----------------------------
DROP TABLE IF EXISTS `x360p_cc_class_feed_attachment`;
CREATE TABLE `x360p_cc_class_feed_attachment` (
  `ccfa_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ccf_id` int(10) unsigned NOT NULL COMMENT '文章ID',
  `file_type` varchar(255) DEFAULT NULL COMMENT '文件类型',
  `file_url` varchar(255) DEFAULT NULL COMMENT '文件地址',
  `file_size` bigint(20) DEFAULT NULL COMMENT '文件大小',
  `meta_width` int(11) DEFAULT NULL COMMENT '图片宽度',
  `meta_height` int(11) DEFAULT NULL COMMENT '图像高度',
  `meta_seconds` int(5) DEFAULT NULL COMMENT '媒体秒数',
  `meta_cover_url` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ccfa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for x360p_cc_class_feed_reply
-- ----------------------------
DROP TABLE IF EXISTS `x360p_cc_class_feed_reply`;
CREATE TABLE `x360p_cc_class_feed_reply` (
  `ccfr_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ccf_id` int(10) unsigned NOT NULL COMMENT '文章ID',
  `cu_id` int(10) unsigned NOT NULL COMMENT '回复者ID',
  `content` text COMMENT '回复内容',
  `creat_time` int(11) NOT NULL COMMENT '回复时间',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ccfr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for x360p_cc_class_feed_reply_attachment
-- ----------------------------
DROP TABLE IF EXISTS `x360p_cc_class_feed_reply_attachment`;
CREATE TABLE `x360p_cc_class_feed_reply_attachment` (
  `ccfra_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ccfr_id` int(11) NOT NULL COMMENT '回复ID',
  `file_type` varchar(64) DEFAULT NULL COMMENT '附件类型',
  `file_url` varchar(255) DEFAULT NULL COMMENT '附件地址',
  `file_size` bigint(20) DEFAULT NULL COMMENT '附件大小',
  `meta_width` int(11) DEFAULT NULL COMMENT '附件宽度',
  `meta_height` int(11) DEFAULT NULL COMMENT '附件高度',
  `meta_seconds` int(11) DEFAULT NULL COMMENT '附件秒数',
  `meta_cover_url` varchar(255) DEFAULT NULL COMMENT '附件封面地址',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ccfra_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for x360p_cc_class_feed_view
-- ----------------------------
DROP TABLE IF EXISTS `x360p_cc_class_feed_view`;
CREATE TABLE `x360p_cc_class_feed_view` (
  `ccfv_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ccf_id` int(11) NOT NULL COMMENT '文章ID',
  `cu_id` int(10) unsigned NOT NULL COMMENT '浏览者ID',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ccfv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for x360p_cc_class_student
-- ----------------------------
DROP TABLE IF EXISTS `x360p_cc_class_student`;
CREATE TABLE `x360p_cc_class_student` (
  `ccs_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cu_id` int(11) NOT NULL COMMENT '学生ID',
  `cc_id` int(11) DEFAULT NULL COMMENT '班级ID',
  `cid` int(11) DEFAULT NULL COMMENT '校360客户ID',
  `og_id` int(11) DEFAULT NULL COMMENT '校360机构ID',
  `student_name` varchar(64) DEFAULT NULL COMMENT '学生名称',
  `student_avatar` varchar(255) DEFAULT NULL COMMENT '学生头像',
  `join_time` int(11) DEFAULT NULL COMMENT '加入时间',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ccs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for x360p_cc_class_student_apply
-- ----------------------------
DROP TABLE IF EXISTS `x360p_cc_class_student_apply`;
CREATE TABLE `x360p_cc_class_student_apply` (
  `ccsa_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cc_id` int(11) NOT NULL COMMENT '班级ID',
  `cu_id` int(11) NOT NULL COMMENT '申请人ID',
  `student_name` varchar(45) DEFAULT NULL COMMENT '申请人姓名',
  `mobile` varchar(16) DEFAULT NULL COMMENT '申请人联系方式',
  `apply_time` int(11) DEFAULT NULL COMMENT '申请时间',
  `approve_time` int(11) DEFAULT NULL COMMENT '申请通过时间',
  `state` tinyint(4) DEFAULT NULL COMMENT '1：同意 2：拒绝',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ccsa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for x360p_cc_user
-- ----------------------------
DROP TABLE IF EXISTS `x360p_cc_user`;
CREATE TABLE `x360p_cc_user` (
  `cu_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '校360客户ID',
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '校360机构ID',
  `user_type` tinyint(4) DEFAULT NULL COMMENT '1：老师 2：家长',
  `wxapp_open_id` varchar(64) DEFAULT NULL COMMENT '小程序openid',
  `nickname` varchar(64) DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像地址',
  `sex` tinyint(1) DEFAULT 0 COMMENT '性别',
  `province` varchar(64) DEFAULT NULL COMMENT '省份',
  `city` varchar(64) DEFAULT NULL COMMENT '城市',
  `country` varchar(64) DEFAULT NULL COMMENT '国家',
  `language` varchar(64) DEFAULT NULL COMMENT '语言',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机号',
  `last_login_time` int(11) DEFAULT NULL COMMENT '上次登录时间',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
