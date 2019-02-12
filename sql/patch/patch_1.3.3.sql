
INSERT INTO `x360p_dictionary` VALUES
(25,0,0,'extra_consume','额外课消项目','额外课消项目','1',0,1,0,0,NULL,0,0,NULL),
(252, 0, 25, '妈妈课堂','妈妈课堂','妈妈课堂','1',0,1,0,0,0,0,0,NULL)
;
-- 订单添加用户定义收据号
ALTER TABLE `x360p_order`
ADD COLUMN `user_contract_no` varchar(32) default '' COMMENT '用户定义合同号' AFTER `order_no`
;
-- 收据添加用户自定义收据编号
ALTER TABLE `x360p_order_receipt_bill`
ADD COLUMN `user_receipt_no` varchar(32) default '' COMMENT '用户定义收据号' AFTER `orb_no`
;

-- 市场名单增加是否试听
ALTER TABLE `x360p_market_clue`
ADD COLUMN `is_trial` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否试听' AFTER `is_valid`
;
-- 市场名单增加成交金额字段
ALTER TABLE `x360p_market_clue`
ADD COLUMN `deal_amount` decimal(13,2) NOT NULL DEFAULT '0.00' COMMENT '成交金额' AFTER `is_deal`
;

-- 客户名单增加是否试听
ALTER TABLE `x360p_customer`
ADD COLUMN `is_trial` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否试听' AFTER `from_sid`
;


-- 新增收费项数据库表
DROP TABLE IF EXISTS `x360p_pay_item`;
CREATE TABLE `x360p_pay_item` (
  `pi_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `name` varchar(255) NOT NULL COMMENT '杂费项目名称',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `unit_price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '单价',
  `is_performance` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否算业绩默认不算',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1启用，0禁用',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`pi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='杂费项目表';

-- 订单扣款记录增加课消ID
ALTER TABLE `x360p_order_cut_amount`
ADD COLUMN `slh_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课消ID' AFTER `amount`
;
-- 学员课时记录增加扣款转化类型
ALTER TABLE `x360p_student_lesson_hour`
MODIFY COLUMN `change_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '变化类型：1考勤，2自由登记课耗，3扣款转化' AFTER `slil_id`;

-- 修改结转单价为6位
ALTER TABLE `x360p_order_transfer_item`
MODIFY COLUMN `unit_price` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '结转单价' AFTER `present_nums`
;

-- 修改退费单价为6位
ALTER TABLE `x360p_order_refund_item`
MODIFY COLUMN `unit_price` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '退费单价' AFTER `present_nums`
;

-- 修改学员剩余金额为6位
ALTER TABLE `x360p_student`
MODIFY COLUMN `money` decimal(15,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT '剩余余额' AFTER `card_no`
;

-- 修改学员余额变化金额为6位
ALTER TABLE `x360p_student_money_history`
MODIFY COLUMN `amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.00' COMMENT '金额' AFTER `oi_id`,
MODIFY COLUMN `before_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.00' COMMENT '操作前余额' AFTER `amount`,
MODIFY COLUMN `after_amount` decimal(15,6) unsigned NOT NULL DEFAULT '0.00' COMMENT '操作后余额' AFTER `before_amount`
;

-- 收据表新增缴费日期
ALTER TABLE `x360p_order_receipt_bill`
ADD COLUMN `paid_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '付款时间' AFTER `hw_id`
;

UPDATE `x360p_order_receipt_bill` orb LEFT JOIN `x360p_order_payment_history` oph on orb.orb_id = oph.orb_id
set orb.paid_time = oph.paid_time
where oph.paid_time IS NOT NULL
;

-- 知识库附件表
DROP TABLE IF EXISTS `x360p_knowledge_item_file`;
CREATE TABLE `x360p_knowledge_item_file` (
  `kif_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `ki_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '知识库条目ID',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '文件id',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件URL',
  `file_type` varchar(16) DEFAULT '' COMMENT '文件类型',
  `file_size` bigint(20) unsigned DEFAULT '0' COMMENT '文件大小',
  `file_name` varchar(64) DEFAULT '' COMMENT '文件名',
  `media_type` char(50) DEFAULT NULL COMMENT '媒体类型',
  `duration` varchar(255) DEFAULT NULL COMMENT '音频时长',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`kif_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='知识库条目文件表';

-- 班级增加多个助教
ALTER TABLE `x360p_class`
ADD COLUMN `second_eids` varchar(255) default '' COMMENT '助教IDS' AFTER `second_eid`
;

update `x360p_class`
set `second_eids`=`second_eid` where `second_eid` > 0
;

-- 加盟商表增加加盟时间、开业时间
ALTER TABLE `x360p_org`
ADD COLUMN `join_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加盟时间' AFTER `org_address`,
ADD COLUMN `open_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开业时间' AFTER `join_int_day`
;

-- 客户名单增加相同家庭客户ID
ALTER TABLE `x360p_customer`
ADD COLUMN `family_cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '同一家庭客户ID' AFTER `cu_id`
;

-- 客户名单新增是否公海字段,进入公海时间
ALTER TABLE `x360p_customer`
ADD COLUMN `is_public` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否公海' AFTER `mcl_id`,
ADD COLUMN `in_public_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转入公海时间' AFTER `is_public`
;

-- 排课增加多个助教
ALTER TABLE `x360p_course_arrange`
ADD COLUMN `second_eids`  varchar(255) NULL DEFAULT '' COMMENT '多助教ID' AFTER `second_eid`;

-- 班级考勤增加多个助教
ALTER TABLE `x360p_class_attendance`
ADD COLUMN `second_eids`  varchar(255) NULL DEFAULT '' COMMENT '多助教ID' AFTER `second_eid`;

-- 学员考勤增加多个助教
ALTER TABLE `x360p_student_attendance`
ADD COLUMN  `second_eids` varchar(255) NULL DEFAULT '' COMMENT '多助教ID' AFTER `second_eid`;

-- 学生课耗增加多个助教
ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `second_eids`  varchar(255) NULL DEFAULT '' COMMENT '多助教ID' AFTER `second_eid`;

-- 教师课耗增加多个助教
ALTER TABLE `x360p_employee_lesson_hour`
ADD COLUMN `second_eids`  varchar(255) NULL DEFAULT '' COMMENT '多助教ID' AFTER `second_eid`;

update `x360p_student_attendance`
set `second_eids`=`second_eid` where `second_eid` > 0
;

update `x360p_course_arrange`
set `second_eids`=`second_eid` where `second_eid` > 0
;

update `x360p_class_attendance`
set `second_eids`=`second_eid` where `second_eid` > 0
;

update `x360p_student_lesson_hour`
set `second_eids`=`second_eid` where `second_eid` > 0
;

update `x360p_employee_lesson_hour`
set `second_eids`=`second_eid` where `second_eid` > 0
;


