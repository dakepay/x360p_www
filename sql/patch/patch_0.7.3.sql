ALTER TABLE `x360p_wxmp_fans`
MODIFY COLUMN `uid`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id,家长uid' AFTER `bid`,
ADD COLUMN `employee_uid`  int NULL DEFAULT 0 COMMENT '机构员工uid，微信绑定既可以家长，也同时可以员工' AFTER `uid`;

ALTER TABLE `x360p_wxmp_menu`
ADD COLUMN `matchrule`  text NULL COMMENT '菜单对应的用户分组规划，即特定用户的显示菜单' AFTER `buttons`;

ALTER TABLE `x360p_wxmp_menu`
ADD COLUMN `menuid`  int NOT NULL DEFAULT 0 COMMENT '微信菜单id' AFTER `appid`;

ALTER TABLE `x360p_accounting_account`
ADD COLUMN `cp_id`  int NOT NULL DEFAULT 0 COMMENT '支付配置id' AFTER `is_default`;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='支付配置表';


CREATE TABLE `x360p_order_payment_online` (
  `opo_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `oid` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `aa_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收款账号ID',
  `paid_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '付款金额',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0：待支付，1：已支付',
  `out_trade_no` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4;


ALTER TABLE `x360p_classroom`
ADD COLUMN `area`  decimal(11,2) NOT NULL DEFAULT 0.00 COMMENT '教室面积' AFTER `seat_config`;






