-- OrderReceiptBill 增加oid字段
ALTER TABLE `x360p_order_receipt_bill`
ADD COLUMN `oid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单ID' AFTER `sid`,
ADD COLUMN `is_demo`tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `oid`
;

-- 加盟商增加责任人
ALTER TABLE `x360p_org`
ADD COLUMN `charge_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '责任人eid' AFTER `expire_day`
;

-- student_lesson_hour 新增是否赠送
ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `is_present` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否赠送:1为赠送' AFTER `is_pay`
;

DROP TABLE IF EXISTS `x360p_report_demolesson_by_lesson`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `x360p_report_demolesson_by_teacher`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 用户单独权限表
DROP TABLE IF EXISTS `x360p_user_per`;
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


update `x360p_order_receipt_bill` orb left join `x360p_order_receipt_bill_item` orbi
  on orb.orb_id = orbi.orb_id
  set orb.oid = orbi.oid
  where orb.oid = 0
  ;