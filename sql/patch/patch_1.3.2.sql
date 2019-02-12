-- 市场渠道新增招生来源
ALTER TABLE `x360p_market_clue`
ADD COLUMN `from_did` int(11) NOT NULL DEFAULT '0' COMMENT '招生来源(招生来源字典ID)' AFTER `mc_id`,
ADD COLUMN `cu_assigned_eid` int(11) NOT NULL DEFAULT '0' COMMENT '销售分配eid' AFTER `assigned_eid`,
ADD COLUMN `cu_assigned_bid` int(11) NOT NULL DEFAULT '0' COMMENT '销售分配校区' AFTER `cu_assigned_eid`
;

UPDATE `x360p_market_clue` mc LEFT JOIN `x360p_customer` cu on mc.cu_id = cu.cu_id
set mc.cu_assigned_eid = cu.follow_eid,mc.cu_assigned_bid = cu.bid,mc.from_did=cu.from_did
WHERE mc.cu_id > 0 AND cu.cu_id IS NOT NULL
;

-- 员工回款业绩增加关联学员ID
ALTER TABLE `x360p_employee_receipt`
ADD COLUMN `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联学员ID' AFTER `orb_id`
;

UPDATE `x360p_employee_receipt` er LEFT JOIN `x360p_order_receipt_bill` orb ON er.orb_id = orb.orb_id
set er.sid = orb.sid
WHERE orb.orb_id IS NOT NULL
;

-- 员工订单业绩增加关联学员ID
ALTER TABLE `x360p_order_performance`
ADD COLUMN `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联学员ID' AFTER `eid`
;

UPDATE `x360p_order_performance` op LEFT JOIN `x360p_order` o ON op.oid = o.oid
set op.sid = o.sid
WHERE o.oid IS NOT NULL
;


-- 服务记录增加ca_id字段
ALTER TABLE `x360p_service_record`
ADD COLUMN `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课ID' AFTER `cid`
;
-- 报表补充缺少的字段
ALTER TABLE `x360p_report_demolesson_by_teacher`
ADD COLUMN `bid`  int(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `og_id`;

ALTER TABLE `x360p_report_student_by_lesson`
ADD COLUMN `delete_uid`  int(11) UNSIGNED NULL DEFAULT 0 AFTER `is_delete`;

ALTER TABLE `x360p_report_class_by_number`
ADD COLUMN `delete_uid`  int(11) UNSIGNED NULL AFTER `is_delete`;