-- 应用callcenter 记录
DROP TABLE IF EXISTS `x360p_callcenter_out_history`;
CREATE TABLE `x360p_callcenter_out_history` (
  `coh_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `call_mobile` varchar(16) DEFAULT '' COMMENT '呼叫电话',
  `recv_mobile` varchar(16) DEFAULT '' COMMENT '被叫电话',
  `call_eid` int(11) NOT NULL DEFAULT '0' COMMENT '呼叫员工ID',
  `recv_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '被叫类型:1为市场名单,2为客户名单,3为学员',
  `recv_bs_id` int(11) NOT NULL DEFAULT '0' COMMENT '被叫业务ID,1为mcu_id,2为cu_id,3为sid',
  `is_connected` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否接通，1为接通，0为未接通',
  `consume_minutes` int(11) NOT NULL DEFAULT '0' COMMENT '消费分钟数',
  `voice_record_url` varchar(255) DEFAULT '' COMMENT '录音文件URL',
  `remark` varchar(255) COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`coh_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='呼叫中心拨出历史记录';
-- 呼叫中心回调日志
DROP TABLE IF EXISTS `x360p_callcenter_callback_log`;
CREATE TABLE `x360p_callcenter_callback_log` (
  `ccl_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `call_mobile` varchar(16) DEFAULT '' COMMENT '呼叫电话',
  `recv_mobile` varchar(16) DEFAULT '' COMMENT '被叫电话',
  `rcv_params` text COMMENT '接收参数JSON存储',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ccl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT='呼叫中心回调日志';


DROP TABLE IF EXISTS `x360p_report_demolesson_by_lesson`;
CREATE TABLE `x360p_report_demolesson_by_lesson` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(10) unsigned DEFAULT '0',
  `bids` varchar(50) DEFAULT '0',
  `lid` int(11) DEFAULT '0',
  `cids` varchar(50) DEFAULT '0',
  `sids` varchar(500) DEFAULT '0',
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- OrderReceiptBill 增加oid字段
ALTER TABLE `x360p_order_receipt_bill`
ADD COLUMN `oid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单ID' AFTER `sid`,
ADD COLUMN `is_demo`tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否体验课' AFTER `oid`
;

update `x360p_order_receipt_bill` orb left join `x360p_order_receipt_bill_item` orbi
  on orb.orb_id = orbi.orb_id
  set orb.oid = orbi.oid
  where orb.oid = 0
  ;
-- student_lesson_hour 新增是否赠送
ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `is_present` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否赠送:1为赠送' AFTER `is_pay`
;

-- 加盟商增加责任人
ALTER TABLE `x360p_org`
ADD COLUMN `charge_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '责任人eid' AFTER `expire_day`
;

-- 用户新增权限配置
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
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COMMENT '用户单独权限表';

