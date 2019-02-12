-- 2018-01-29
-- 客户表新增3个字段，一个父客户ID，一个机构ID，一个客户类型字段 用于解决加盟商的独立域名访问问题
ALTER TABLE `pro_client` 
ADD COLUMN `parent_cid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所在客户ID' AFTER `cid`,
ADD COLUMN `og_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '机构ID' AFTER `parent_cid`,
ADD COLUMN `client_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户类型（0为客户，1为代理商)' AFTER `og_id`
;


-- 2018-02-02
-- 客户用户表新增1个机构ID字段，为后面同意一数据库多机构做准备
ALTER TABLE `pro_client_user` 
ADD COLUMN `og_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '机构ID' AFTER `cid`;


-- 2018-02-25 新增学员单价
ALTER TABLE `pro_client` 
ADD COLUMN `student_price` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '学员价格' AFTER `account_price`;


-- 2018-06-02 新增客户冻结功能
ALTER TABLE `pro_client`
ADD COLUMN `frozen_int_day` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '冻结日期';

INSERT INTO `pro_dictionary`(`did`, `pid`, `name`, `title`, `desc`, `is_system`, `sort`, `display`, `create_time`, `create_uid`, `update_time`, `is_delete`, `delete_uid`, `delete_time`) VALUES (301, 3, '系统冻结', '系统冻结', '系统冻结', b'0', 0, 1, 0, 0, NULL, 0, 0, NULL);
INSERT INTO `pro_dictionary`(`did`, `pid`, `name`, `title`, `desc`, `is_system`, `sort`, `display`, `create_time`, `create_uid`, `update_time`, `is_delete`, `delete_uid`, `delete_time`) VALUES (302, 3, '系统解冻', '系统解冻', '系统解冻', b'0', 0, 1, 0, 0, NULL, 0, 0, NULL);




-- 2018-08-01 新增应用中心
DROP TABLE IF EXISTS `pro_vip_client_app`;
CREATE TABLE `pro_vip_client_app` (
  `vca_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '客户APP记录ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `app_name` varchar(32) default '' COMMENT '应用名称',
  `expire_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  `minutes` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余分钟数，对于callcenter有效',
  `buy_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买时间',
  `og_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买用户ID',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vca_id`),
  KEY `idx_app_name` (`app_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='客户APP记录';

-- 呼叫中心分钟历史记录数
DROP TABLE IF EXISTS `pro_app_callcenter_minutes_history`;
CREATE TABLE `pro_app_callcenter_minutes_history` (
  `acmh_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'callcenter消费ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `og_uid` int(11) unsigned NOT NULL default 0 COMMENT '机构的用户ID',
  `op_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0为消费，1为充值',
  `minutes` int(11) NOT NULL DEFAULT '0' COMMENT '消费或充值分钟数',
  `remark` varchar(255) COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`acmh_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='呼叫中心分钟历史记录';
-- 呼叫中心手机注册
DROP TABLE IF EXISTS `pro_app_callcenter_mobile_reg`;
CREATE TABLE `pro_app_callcenter_mobile_reg` (
  `acmr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'callcenter消费ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `mobile` varchar(16) DEFAULT '' COMMENT '手机号',
  `og_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构的用户ID',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`acmr_id`),
  key `idx_mobile` (`mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='呼叫中心手机注册';


-- 20180804 客户新增备注字段
ALTER TABLE `pro_client`
ADD COLUMN `remark` varchar(255) DEFAULT '' COMMENT '备注' AFTER `eid`
;

-- 20180804 新增授权登录日志
DROP TABLE IF EXISTS `pro_auth_login_log`;
CREATE TABLE `pro_auth_login_log` (
  `all_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `og_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `login_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录用户ID',
  `is_auth` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否授权',
  `login_ip` varchar(16) DEFAULT '' COMMENT '登录IP地址',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`all_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='授权登录日志';

DROP TABLE IF EXISTS `pro_vip_client_app`;
CREATE TABLE `pro_vip_client_app` (
  `vca_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '客户APP记录ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `app_name` varchar(32) DEFAULT '' COMMENT '应用名称',
  `expire_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  `seconds` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余秒数，对于webcall有效',
  `buy_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买时间',
  `og_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买用户ID',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vca_id`),
  KEY `idx_app_name` (`app_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='客户APP记录';




DROP TABLE IF EXISTS `pro_webcall_call_log`;
CREATE TABLE `pro_webcall_call_log` (
  `wcl_id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT 'cid',
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT 'og_id',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT 'bid',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '员工id',
  `token` varchar(255) DEFAULT NULL COMMENT '通话token',
  `callid` varchar(255) DEFAULT NULL COMMENT '呼叫唯一标识',
  `billsec` int(11) NOT NULL DEFAULT '0' COMMENT '通话时长，单位为秒',
  `recordurl` varchar(255) DEFAULT NULL COMMENT '录音地址',
  `reasoncode` smallint(6) NOT NULL DEFAULT '-1' COMMENT '呼叫返回码：0接通，180响铃，480被叫无应答，486被叫忙，603被叫拒绝，810主叫取消呼叫，999通信错误',
  `caller_phone` varchar(255) DEFAULT NULL COMMENT '主叫电话',
  `calltime` int(11) NOT NULL DEFAULT '0' COMMENT '呼叫时间',
  `ringtime` int(11) NOT NULL DEFAULT '0' COMMENT '主叫响铃时间',
  `talkbegtime` int(11) NOT NULL DEFAULT '0' COMMENT '接通时间',
  `talkendtime` int(11) NOT NULL DEFAULT '0' COMMENT '挂断时间',
  `callee_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:市场名单， 2:客户， 3：学员',
  `callee_phone` varchar(255) DEFAULT NULL COMMENT '被叫电话',
  `dialback_data` text COMMENT '呼叫数据',
  `callback_data` text COMMENT '通话回调数据',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '录音下载存放的file_id',
  `remark` varchar(255) DEFAULT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`wcl_id`),
  KEY `idx_token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COMMENT='呼叫记录';


-- 20180824 新增客户的按学员数、校区数、账号数限制字段
ALTER TABLE `pro_client`
ADD COLUMN `is_student_limit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否限制学员数' AFTER `branch_num_limit`,
ADD COLUMN `is_account_limit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否限制账号数' AFTER `is_student_limit`,
ADD COLUMN `is_branch_limit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否限制校区数' AFTER `is_account_limit`,
ADD COLUMN `branch_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '校区单价' AFTER `student_price`
;


UPDATE `pro_client` set `is_student_limit` = 1;

-- 20180905 给客户新增负责任客服ID
ALTER TABLE `pro_client`
ADD COLUMN `service_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客服ID' AFTER `eid`
;

-- 20180910 客户新增培训费用
ALTER TABLE `pro_client`
ADD COLUMN `edu_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '培训费用' AFTER `add_renew_amount`
;

-- 20181022 收钱吧入网资料
DROP TABLE IF EXISTS `pro_client_apply_sqb`;
CREATE TABLE `pro_client_apply_sqb` (
  `cas_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '收钱吧申请ID',
  `cid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '专业版客户ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '商户名',
  `business_name` varchar(32) DEFAULT NULL COMMENT '商户经营名称',
  `contact_name` varchar(16) NOT NULL DEFAULT '' COMMENT '联系人',
  `contact_cellphone` varchar(32) NOT NULL DEFAULT '' COMMENT '联系电话',
  `industry` varchar(36) DEFAULT NULL COMMENT '行业',
  `area` varchar(6) NOT NULL DEFAULT '' COMMENT '地区',
  `area_arr` varchar(255) NOT NULL DEFAULT '' COMMENT '地区',
  `street_address` varchar(128) NOT NULL DEFAULT '' COMMENT '详细地址',
  `account_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '账户类型：1个人账户，0企业账户',
  `bank_card` varchar(45) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `bank_card_image` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡照片',
  `bank_name` varchar(45) NOT NULL DEFAULT '' COMMENT '开户银行',
  `bank_area` varchar(6) NOT NULL DEFAULT '' COMMENT '开户地区',
  `bank_area_arr` varchar(255) NOT NULL DEFAULT '' COMMENT '开户地区',
  `branch_name` varchar(45) NOT NULL DEFAULT '' COMMENT '开户支行',
  `holder` varchar(45) NOT NULL DEFAULT '' COMMENT '开户姓名',
  `bank_cellphone` varchar(32) DEFAULT NULL COMMENT '预留手机号',
  `legal_person_name` varchar(100) DEFAULT NULL COMMENT '法人姓名	',
  `business_license_photo` varchar(255) DEFAULT NULL COMMENT '营业执照	',
  `tax_payer_id` varchar(45) DEFAULT NULL COMMENT '工商注册号	',
  `id_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '证件类型：1 身份证; 2 护照; 3 台胞证; 4 港澳通行证;',
  `identity` varchar(18) NOT NULL DEFAULT '' COMMENT '身份证号',
  `holder_id_front_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证正面照',
  `holder_id_back_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证反面照',
  `brand_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '门头照片',
  `indoor_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '室内照片',
  `outdoor_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '室外照片',
  `other_photos` varchar(1024) DEFAULT NULL COMMENT '其他照片',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `longitude` double DEFAULT 0 COMMENT '经度',
  `latitude` double DEFAULT 0 COMMENT '纬度',
  `extra` varchar(255) DEFAULT NULL COMMENT '其他字段(JSON)',
  `vendor_app_id` varchar(36) NOT NULL DEFAULT '' COMMENT '服务商appid',
  `vendor_sn` varchar(32) NOT NULL DEFAULT '' COMMENT '服务商sn',
  `organization_id` varchar(32) DEFAULT NULL COMMENT '组织',
  `user_id` varchar(32) DEFAULT NULL COMMENT '推广者',
  `client_sn` varchar(50) DEFAULT NULL COMMENT '外部商户号',
  `create_account` tinyint(1) DEFAULT 1 COMMENT '是否创建商户账号',
  `is_audit` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态：0未审核，1系统审核通过，2收钱吧审核中，4审核通过',
  `config` text DEFAULT NULL COMMENT '入网返回资料，json格式',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
  PRIMARY KEY (`cas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收钱吧入网资料';

-- 20181030 新增客户公海
ALTER TABLE `pro_customer`
ADD COLUMN `is_public` int(1) unsigned DEFAULT 0 COMMENT '是否为公海客户(1是0否)' AFTER `area_id`,
ADD COLUMN `in_public_time` int(11) unsigned DEFAULT 0 COMMENT '转入公海客户时间' AFTER `is_public`
;

ALTER TABLE `pro_customer_follow_up`
ADD COLUMN `is_system` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否系统操作 ' AFTER `eid`,
ADD COLUMN `system_op_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '系统操作类型：1转入公海2从公海转出' AFTER `eid`
;

ALTER TABLE `pro_employee`
ADD COLUMN `rids` varchar(255) NOT NULL DEFAULT '' COMMENT '角色ID,逗号分隔' AFTER `uid`
;


ALTER TABLE `pro_vip_order`
ADD COLUMN `creat_from` tinyint(1) unsigned DEFAULT 0 COMMENT '订单由谁产生(1后台操作0客户)' AFTER `status`,
ADD COLUMN `creat_id` int(11) unsigned DEFAULT 0 COMMENT '后台操作id' AFTER `creat_from`,
ADD COLUMN `remark` varchar(255) DEFAULT '' COMMENT '备注介绍' AFTER `creat_id`
;


-- APP定义表
DROP TABLE IF EXISTS `pro_app`;
CREATE TABLE `pro_app` (
  `app_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'app_id自增',
  `app_ename` varchar(16) NOT NULL DEFAULT '' COMMENT 'app英文名称',
  `app_name` varchar(32) NOT NULL DEFAULT '' COMMENT 'APP中文名称',
  `app_uri` varchar(255) DEFAULT '' COMMENT 'APP的应用地址',
  `app_icon_uri` varchar(255) DEFAULT '' COMMENT 'APP的图标地址',
  `app_desc` varchar(255) DEFAULT '' COMMENT 'APP描述',
  `price_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '计费规则1：按年计费，2：按量计费，3：按年按量计费',
  `year_price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '年费价格',
  `volume_price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '按量付费价格',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
  PRIMARY KEY (`app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1001 DEFAULT CHARSET=utf8 COMMENT='APP定义表';

-- 客户APP记录表
DROP TABLE IF EXISTS `pro_vip_client_app`;
CREATE TABLE `pro_vip_client_app` (
  `vca_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '客户APP记录ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `app_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '应用ID',
  `app_ename` varchar(16) NOT NULL DEFAULT '' COMMENT '应用英文名称',
  `expire_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  `volume_limit` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '容量限制,对于callcenter,则为分钟数',
  `volume_used` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '容量使用',
  `buy_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买时间',
  `og_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买用户ID',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态0为禁用1为启用',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vca_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户APP记录';

-- 客户消费记录表
DROP TABLE IF EXISTS `pro_vip_client_consume`;
CREATE TABLE `pro_vip_client_consume` (
  `vcc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '客户消费记录ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `consume_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '消费类型：1初始开通系统,2续费,3扩容,4购买物品,5购买增值服务,6初次购买APP,7,APP续费',
  `app_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '应用ID,consume_type为6有效',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0' COMMENT '金额',
  `vo_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `extra_params` text COMMENT '额外参数JSON结构',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vcc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户APP消费记录表';


-- 员工业绩表
DROP TABLE IF EXISTS `pro_employee_performance`;
CREATE TABLE `pro_employee_performance` (
  `ep_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '绩效id',
  `cid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '客户ID',
  `eid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '员工ID',
  `vo_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'VIP订单ID',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT 0.00 COMMENT '成交金额',
  `vcc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '对应消费记录ID',
  `consume_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '消费类型：1初始开通系统,2续费,3扩容,4购买物品,5购买增值服务,6初次购买APP,7,APP续费',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
  PRIMARY KEY (`ep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='员工业绩表';


-- VIP客户操作日志
DROP TABLE IF EXISTS `pro_vip_action_log`;
CREATE TABLE `pro_vip_action_log` (
  `val_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '操作日志ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `uri` varchar(256) NOT NULL,
  `log_params` text NOT NULL COMMENT '日志参数(json格式)',
  `log_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '日志描述用户描述',
  `ip` varchar(32) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`val_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='系统操作日志表(记录系统操作日志)';


-- 系统配置表
DROP TABLE IF EXISTS `pro_config`;
CREATE TABLE `pro_config` (
  `cfg_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `cfg_name` varchar(32) NOT NULL DEFAULT '' COMMENT '配置名称',
  `cfg_value` text NOT NULL COMMENT '配置值',
  `format` enum('int','string','json') NOT NULL DEFAULT 'string',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
  PRIMARY KEY (`cfg_id`),
  UNIQUE KEY `idx_cfg_name` (`cfg_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表(KV结构)';

-- 必修添加的sql
DROP TABLE IF EXISTS `pro_webcall_call_log`;
CREATE TABLE `pro_webcall_call_log` (
  `wcl_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT 'og_id',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT 'cid',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT 'bid',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '员工id',
  `token` varchar(32) DEFAULT NULL COMMENT '通话token',
  `callid` varchar(64) DEFAULT NULL COMMENT '呼叫唯一标识',
  `caller_ringtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '呼叫响铃时间',
  `caller_talkbegtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始通话时间',
  `caller_calltime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '呼叫时间',
  `caller_talkendtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '呼叫结束时间',
  `caller_phone` varchar(16) DEFAULT '' COMMENT '主叫号码',
  `caller_callcode` int(11) DEFAULT NULL COMMENT '呼叫码',
  `callee_ringtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '被叫响铃时间',
  `callee_talkbegtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '被叫接听时间',
  `callee_calltime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '呼叫时间',
  `callee_talkendtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '被叫结束时间',
  `callee_phone` varchar(16) DEFAULT '' COMMENT '被叫号码',
  `callee_callcode` int(11) DEFAULT NULL COMMENT '被叫码',
  `recordurl` varchar(255) DEFAULT '' COMMENT '原始录音文件',
  `abillsec` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '接通耗时长秒',
  `billsec` int(11) NOT NULL DEFAULT '0' COMMENT '通话时长，单位为秒',
  `cacu_minutes` int(11) NOT NULL DEFAULT '0' COMMENT '计费时长，单位为分钟',
  `reasoncode` smallint(6) NOT NULL DEFAULT '-1' COMMENT '呼叫返回码：0接通，180响铃，480被叫无应答，486被叫忙，603被叫拒绝，810主叫取消呼叫，999通信错误',
  `callee_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:市场名单， 2:客户， 3：学员',
  `mcl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '市场名单ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户名单ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '录音下载存放的file_id',
  `file_url` varchar(255) DEFAULT '' COMMENT '录音转换后的URL',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `callback_arrive_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回调到达次数',
  `relate_cmt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联沟通ID,calle_type为2时是customer_follow_up的id,为3时是student_return_visit表的id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`wcl_id`),
  KEY `idx_token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COMMENT='呼叫记录';


-- 2018-11-15 新增job 表字段 task_id
ALTER TABLE `pro_jobs`
ADD COLUMN `task_id` varchar(32) DEFAULT '' COMMENT '任务ID' AFTER `queue`
;


