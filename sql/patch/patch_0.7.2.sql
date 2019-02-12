ALTER TABLE `x360p_customer`
ADD COLUMN `from_sid`  int NOT NULL DEFAULT 0 COMMENT '退学学员回流，以前的学员id' AFTER `customer_status_did`;

ALTER TABLE `x360p_employee_lesson_hour` 
MODIFY COLUMN `total_lesson_amount` decimal(15, 6) UNSIGNED NOT NULL DEFAULT 0.000000 COMMENT '总计课时金额' AFTER `total_lesson_hours`;


ALTER TABLE `x360p_order_item`
ADD COLUMN `unit_lesson_hour_amount` decimal(15,6) UNSIGNED NOT NULL DEFAULT 0.000000 COMMENT '课耗单课时金额' AFTER `reduced_amount`;



ALTER TABLE `x360p_report_summary` 
MODIFY COLUMN `lesson_hour_consume` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '消耗课时数' AFTER `order_num`,
MODIFY COLUMN `lesson_hour_remain` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '剩余课时数' AFTER `lesson_hour_consume`,
MODIFY COLUMN `money_consume` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '消耗课时金额' AFTER `lesson_hour_remain`,
MODIFY COLUMN `money_remain` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '剩余课时金额' AFTER `money_consume`,
MODIFY COLUMN `lesson_hour_reward` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '教师课酬课时数' AFTER `money_remain`,
MODIFY COLUMN `money_reward` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '教师课酬金额' AFTER `lesson_hour_reward`,
MODIFY COLUMN `income_total` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '收款合计' AFTER `money_reward`,
MODIFY COLUMN `arrearage_total` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '欠款合计' AFTER `income_total`,
MODIFY COLUMN `refund_total` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '退款合计' AFTER `arrearage_total`,
MODIFY COLUMN `outlay_total` decimal(11, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '支出合计' AFTER `refund_total`;


ALTER TABLE `x360p_student_lesson_hour` 
MODIFY COLUMN `lesson_amount` decimal(15, 6) UNSIGNED NOT NULL DEFAULT 0.000000 COMMENT '课时金额' AFTER `lesson_minutes`;


ALTER TABLE `x360p_wxmp_fans`
ADD COLUMN `last_connect_time`  int NOT NULL DEFAULT 0 COMMENT '上次联系时间，主要是发送客服消息要在48小时内' AFTER `unsubscribe_time`;

