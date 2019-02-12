ALTER TABLE `x360p_backlog` 
ADD COLUMN  `url` varchar(255) DEFAULT NULL COMMENT '待办跳转地址' AFTER `status`
;

ALTER TABLE `x360p_customer_follow_up`
CHANGE COLUMN `intetion_level` `intention_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '意向级别' AFTER `next_follow_time`
;

ALTER TABLE `x360p_order_receipt_bill`
ADD COLUMN `student_money` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '超额预存部分' AFTER `unpaid_amount`,
ADD COLUMN `hw_id` int(11) NOT NULL DEFAULT '0' COMMENT '收据交班id' AFTER `student_money`
;

ALTER TABLE `x360p_order_receipt_bill_item`
ADD COLUMN `balance_paid_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '电子钱包支付' AFTER `paid_amount`,
ADD COLUMN `money_paid_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '非电子钱包支付' AFTER `balance_paid_amount`
;

ALTER TABLE `x360p_order_refund`
ADD COLUMN  `hw_id` int(11) NOT NULL DEFAULT '0' COMMENT '交班id' AFTER `remark`
;

ALTER TABLE `x360p_tally`
ADD COLUMN `sid` int(11) NOT NULL DEFAULT '0' COMMENT '相关的学生id' AFTER `int_day`
;




DROP TABLE IF EXISTS `x360p_handover_money`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='交班后统一缴费';

DROP TABLE IF EXISTS `x360p_handover_work`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='交班记录';
