-- 截止11月30日16：00还未运行
ALTER TABLE `x360p_student_lesson`
MODIFY COLUMN `lesson_amount` decimal(15, 6) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '课时金额' AFTER `lesson_hours`,
MODIFY COLUMN `remain_lesson_amount` decimal(15, 6) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '剩余课时金额' AFTER `remain_lesson_hours`
;

ALTER TABLE `x360p_transfer_hour_history`
ADD COLUMN `lesson_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.00' COMMENT '课时金额' AFTER `lesson_hours`
;

update x360p_transfer_hour_history thh
left join `x360p_student_lesson` sl
ON thh.from_sl_id = sl.sl_id
left join `x360p_order_item` oi
ON sl.sl_id = oi.sl_id
set thh.lesson_amount = thh.lesson_hours * oi.price
WHERE oi.oi_id IS NOT NULL AND thh.lesson_amount = 0 AND thh.is_delete = 0;