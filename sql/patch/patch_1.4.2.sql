
-- 员工拼音长度加长
ALTER TABLE `x360p_employee`
MODIFY COLUMN `pinyin` varchar(255) NOT NULL DEFAULT '' COMMENT '员工姓名ename的全拼' AFTER `ename`,
ADD COLUMN `com_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '分公司ID，逗号分隔' AFTER `bids`
;
-- org表新增三个字段
ALTER TABLE `x360p_org`
ADD COLUMN `init_account` varchar(32) default '' COMMENT '初始账号' AFTER `is_frozen`,
ADD COLUMN `init_password` varchar(32) default '' COMMENT '初始密码' AFTER `init_account`,
ADD COLUMN `init_status`  tinyint(1) NULL DEFAULT 0 COMMENT '1：开启 0：禁用' AFTER `init_password`,
ADD COLUMN `is_init` tinyint(1) default '1' COMMENT '是否初始化' AFTER `init_status`,
ADD COLUMN `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID(center数据库下的client表主键ID)' AFTER `og_id`
;
ALTER TABLE `x360p_franchisee`
ADD COLUMN `system_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '系统状态:0未开通1待审核2已开通' AFTER `open_int_day`,
ADD COLUMN `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID(center数据库下的client表主键ID)' AFTER `fc_og_id`
;


update `x360p_org` og
LEFT JOIN `x360p_franchisee` fc
ON og.og_id = fc.fc_og_id
set og.charge_eid = fc.service_eid
where og.charge_eid = 0 and fc.fc_id IS NOT NULL;


UPDATE `x360p_franchisee`
set system_status = 2
where fc_og_id > 0;

ALTER TABLE `x360p_market_clue`
ADD COLUMN `family_rel`  int(11) NOT NULL DEFAULT 0 COMMENT '关系' AFTER `tel`;

-- x360p_homework_complete表新增三个字段
ALTER TABLE `x360p_homework_complete`
ADD COLUMN `delete_uid` int(11) NOT NULL DEFAULT 0 COMMENT '删除用户uid' AFTER `delete_time`;

-- 排课表新增consume_lesson_amount 金额
ALTER TABLE `x360p_course_arrange`
ADD COLUMN `consume_lesson_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣课金额' AFTER `consume_source_type`
;

ALTER TABLE `x360p_course_arrange_student`
ADD COLUMN `consume_lesson_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣课金额' AFTER `consume_source_type`
;
-- 以上更新到1.4.1版本了已经


ALTER TABLE `x360p_class_attendance`
ADD COLUMN `consume_lesson_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣课金额' AFTER `consume_source_type`
;

ALTER TABLE `x360p_student_attendance`
ADD COLUMN `consume_source_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '课消来源:1:课时,2:电子钱包' AFTER `consume_lesson_hour`,
ADD COLUMN `consume_lesson_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '扣课金额' AFTER `consume_source_type`
;


-- 退费记录表新增退费日期
ALTER TABLE `x360p_order_refund`
ADD COLUMN `refund_int_day` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '退款日期' AFTER `refund_amount`
;

-- 扣费记录增加扣费日期
ALTER TABLE `x360p_order_cut_amount`
ADD COLUMN `cut_int_day` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '扣费日期' AFTER `amount`
;

-- 业绩记录新增消费类型字段
ALTER TABLE `x360p_employee_receipt`
ADD COLUMN `consume_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '收费类型：1新报，2续报，3扩科' AFTER `receipt_time`
;

UPDATE `x360p_employee_receipt` er
LEFT JOIN `x360p_order_item` oi
ON er.oid = oi.oid and oi.gtype = 0
SET er.consume_type = oi.consume_type
WHERE oi.oi_id IS NOT NULL AND er.amount > 0 AND er.consume_type = 0
;

--
SELECT er.*,oi.consume_type from `x360p_employee_receipt` er
LEFT JOIN `x360p_order_item` oi
ON er.oid = oi.oid and oi.gtype = 0
WHERE er.amount > 0 and oi.oi_id IS NOT NULL;

-- #####下面这些更新下一个版本发布的时候必须加到patch里面
-- 更细历史退费日期
UPDATE `x360p_order_refund`
set `refund_int_day` = from_unixtime(`create_time`,'%Y%m%d')
where refund_int_day = 0
;

UPDATE `x360p_order_refund_history`
set `pay_time` = `create_time`
where `pay_time` = 0;

-- 更新历史扣费记录日期
UPDATE `x360p_order_cut_amount`
set `cut_int_day` = from_unixtime(`create_time`,'%Y%m%d')
where cut_int_day = 0
;

-- 学员分配记录新增bid 已执行
ALTER TABLE `x360p_employee_student`
ADD COLUMN `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID' AFTER `og_id`
;

UPDATE `x360p_employee_student` es
LEFT JOIN `x360p_student` s
ON es.sid = s.sid
SET es.bid = s.bid
WHERE s.sid IS NOT NULL;



-- 必修添加的sql
DROP TABLE IF EXISTS `x360p_webcall_call_log`;
CREATE TABLE `x360p_webcall_call_log` (
  `wcl_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT 'og_id',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT 'bid',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '员工id',
  `token` varchar(32) DEFAULT NULL COMMENT '通话token',
  `callid` varchar(64) DEFAULT NULL COMMENT '呼叫唯一标识',
  `caller_ringtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '呼叫响铃时间',
  `caller_talkbegtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始通话时间',
  `caller_calltime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '呼叫时间',
  `caller_talkendtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '呼叫结束时间',
  `caller_phone` varchar(16) DEFAULT '' COMMENT '主叫号码',
  `caller_callcode` int(11) DEFAULT NULL COMMENT '呼叫码',
  `callee_ringtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '被叫响铃时间',
  `callee_talkbegtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '被叫接听时间',
  `callee_calltime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '呼叫时间',
  `callee_talkendtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '被叫结束时间',
  `callee_phone` varchar(16) DEFAULT '' COMMENT '被叫号码',
  `callee_callcode` int(11) DEFAULT NULL COMMENT '被叫码',
  `recordurl` varchar(255) DEFAULT '' COMMENT '原始录音文件',
  `abillsec` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '接通耗时长秒',
  `billsec` int(11) NOT NULL DEFAULT '0' COMMENT '通话时长，单位为秒',
  `cacu_minutes` int(11) NOT NULL DEFAULT '0' COMMENT '计费时长，单位为分钟',
  `reasoncode` smallint(6) NOT NULL DEFAULT '-1' COMMENT '呼叫返回码：0接通，180响铃，480被叫无应答，486被叫忙，603被叫拒绝，810主叫取消呼叫，999通信错误',
  `callee_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:市场名单， 2:客户， 3：学员',
  `mcl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '市场名单ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户名单ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '录音下载存放的file_id',
  `file_url` varchar(255) DEFAULT '' COMMENT '录音转换后的URL',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `callback_arrive_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回调到达次数',
  `relate_cmt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联沟通ID,calle_type为2时是customer_follow_up的id,为3时是student_return_visit表的id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`wcl_id`),
  KEY `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='呼叫记录';


-- 市场渠道 增加状态
ALTER TABLE `x360p_market_channel`
ADD COLUMN `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '渠道状态1开启0关闭' AFTER `deal_num`
;