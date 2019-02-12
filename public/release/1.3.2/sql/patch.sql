-- 员工回款业绩增加关联学员ID
ALTER TABLE `x360p_employee_receipt`
ADD COLUMN `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联学员ID' AFTER `orb_id`
;

-- 市场渠道新增招生来源
ALTER TABLE `x360p_market_clue`
ADD COLUMN `from_did` int(11) NOT NULL DEFAULT '0' COMMENT '招生来源(招生来源字典ID)' AFTER `mc_id`,
ADD COLUMN `cu_assigned_eid` int(11) NOT NULL DEFAULT '0' COMMENT '销售分配eid' AFTER `assigned_eid`,
ADD COLUMN `cu_assigned_bid` int(11) NOT NULL DEFAULT '0' COMMENT '销售分配校区' AFTER `cu_assigned_eid`
;


-- 员工订单业绩增加关联学员ID
ALTER TABLE `x360p_order_performance`
ADD COLUMN `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联学员ID' AFTER `eid`
;


-- 服务记录增加ca_id字段
ALTER TABLE `x360p_service_record`
ADD COLUMN `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课ID' AFTER `cid`
;

-- 更新员工回款业绩
UPDATE `x360p_employee_receipt` er LEFT JOIN `x360p_order_receipt_bill` orb ON er.orb_id = orb.orb_id
set er.sid = orb.sid
WHERE orb.orb_id IS NOT NULL
;

-- 更新市场名单
UPDATE `x360p_market_clue` mc LEFT JOIN `x360p_customer` cu on mc.cu_id = cu.cu_id
set mc.cu_assigned_eid = cu.follow_eid,mc.cu_assigned_bid = cu.bid,mc.from_did=cu.from_did
WHERE mc.cu_id > 0 AND cu.cu_id IS NOT NULL
;

-- 更新订单业绩
UPDATE `x360p_order_performance` op LEFT JOIN `x360p_order` o ON op.oid = o.oid
set op.sid = o.sid
WHERE o.oid IS NOT NULL
;


-- 报表补充缺少的字段
ALTER TABLE `x360p_report_demolesson_by_teacher`
ADD COLUMN `bid`  int(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `og_id`;

ALTER TABLE `x360p_report_student_by_lesson`
ADD COLUMN `delete_uid`  int(11) UNSIGNED NULL DEFAULT 0 AFTER `is_delete`;

ALTER TABLE `x360p_report_class_by_number`
ADD COLUMN `delete_uid`  int(11) UNSIGNED NULL AFTER `is_delete`;



-- 底下加盟商账号增加字段
ALTER TABLE `x360p_org`
ADD COLUMN `is_student_limit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否限制学员数' AFTER `branch_num_limit`,
ADD COLUMN `is_account_limit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否限制账号数' AFTER `is_student_limit`,
ADD COLUMN `is_branch_limit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否限制校区数' AFTER `is_account_limit`,
ADD COLUMN `account_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '账号单价' AFTER `student_num_limit`,
ADD COLUMN `branch_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '校区单价' AFTER `account_price`,
ADD COLUMN `student_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '学员单价' AFTER `branch_price`,
ADD COLUMN `default_wxmp` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '默认公众号0：用官方1：用总部' AFTER `student_price`,
ADD COLUMN `renew_way` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '续费方式0:不允许自助续费，1:用总部，2：用官方' AFTER `default_wxmp`
;