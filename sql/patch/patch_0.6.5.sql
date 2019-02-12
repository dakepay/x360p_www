ALTER TABLE `x360p_wxmp_fans`
MODIFY COLUMN `sex`  smallint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别' AFTER `headimgurl`;

ALTER TABLE `x360p_tally`
ADD COLUMN `sid`  int NOT NULL DEFAULT 0 COMMENT '相关的学生id' AFTER `int_day`;

ALTER TABLE `x360p_handover_work`
DROP COLUMN `handover_cash`,
MODIFY COLUMN `money_dec_amount`  decimal(11,2) NOT NULL DEFAULT 0.00 COMMENT '值班期间减少的金额，
退费' AFTER `money_inc_amount`,
MODIFY COLUMN `cash_inc_amount`  decimal(11,2) NOT NULL DEFAULT 0 COMMENT '值班期间增加的现金' AFTER
`money_dec_amount`,
MODIFY COLUMN `cash_dec_amount`  decimal(11,2) NOT NULL DEFAULT 0 COMMENT '值班期间减少的现金' AFTER
`cash_inc_amount`;

ALTER TABLE `x360p_handover_money`
MODIFY COLUMN `amount`  decimal(11,2) NOT NULL COMMENT '缴费总额(包括现金）' AFTER `eid`,
MODIFY COLUMN `cash_amount`  decimal(11,2) NOT NULL DEFAULT 0 COMMENT '现金部分总额' AFTER `amount`;

ALTER TABLE `x360p_handover_work`
ADD COLUMN `start_time`  int NOT NULL DEFAULT 0 COMMENT '开始时间' AFTER `ack_time`,
ADD COLUMN `end_time`  int NOT NULL DEFAULT 0 COMMENT '结束时间' AFTER `start_time`;

ALTER TABLE `x360p_order_receipt_bill_item`
MODIFY COLUMN `balance_paid_amount`  decimal(11,2) NOT NULL DEFAULT 0 COMMENT '电子钱包支付' AFTER `paid_amount`,
MODIFY COLUMN `money_paid_amount`  decimal(11,2) NOT NULL DEFAULT 0 COMMENT '非电子钱包支付' AFTER `balance_paid_amount`;

