ALTER TABLE `x360p_org`
DROP COLUMN `province`,
DROP COLUMN `city`,
DROP COLUMN `district`,
DROP COLUMN `area_id`,
ADD COLUMN `big_area_id`  smallint(2) NOT NULL DEFAULT 0 COMMENT '大区id:配置文件big_area' AFTER `org_short_name`,
ADD COLUMN `province_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '省ID' AFTER `big_area_id`,
ADD COLUMN `city_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '城市ID' AFTER `province_id`,
ADD COLUMN `area_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '行政区ID' AFTER `city_id`,
ADD COLUMN `district_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区域ID' AFTER `area_id`;

ALTER TABLE `x360p_org`
DROP COLUMN `big_area_id`,
DROP COLUMN `area_id`;


ALTER TABLE `x360p_org`
ADD COLUMN `org_type`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '0非加盟商， 1加盟商' AFTER `org_name`,
ADD COLUMN `mobile`  char(20) NOT NULL DEFAULT '' COMMENT '联系电话' AFTER `org_type`;



ALTER TABLE `x360p_user`
ADD COLUMN `is_main`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是主帐号，加盟商主帐号' AFTER `status`;

ALTER TABLE `x360p_accounting_account`
ADD COLUMN `is_delete`  tinyint NOT NULL DEFAULT 0 AFTER `delete_uid`;

ALTER TABLE `x360p_order_receipt_bill_print_history`
CHANGE COLUMN `ob_id` `bid`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '校区ID' AFTER `og_id`;

ALTER TABLE `x360p_student_attendance`
ADD COLUMN `oi_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'order_item表主键' AFTER `sl_id`;

ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `oi_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'order_item表主键' AFTER `sl_id`;


ALTER TABLE `x360p_order_item`
ADD COLUMN `lid`  int NOT NULL DEFAULT 0 COMMENT '课程id, 主要是暂存订单、付款为0时使用' AFTER `gtype`,
ADD COLUMN `cid`  int NOT NULL DEFAULT 0 COMMENT '班级id, 主要是暂存订单、付款为0时使用' AFTER `lid`;

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