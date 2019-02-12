
-- 价格定义表
DROP TABLE IF EXISTS `x360p_lesson_price_define`;
CREATE TABLE `x360p_lesson_price_define` (
  `lpd_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `dtype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '定义类型,0按课程定价，1按课程科目定价,2按课程等级定价',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `product_level_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '产品等级字典ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `bids` varchar(255) DEFAULT '' COMMENT '校区IDS',
  `dept_ids` varchar(255) DEFAULT '' COMMENT '分公司ID',
  `sale_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '价格',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`lpd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- 订单缴费记录表新增sid关联
ALTER TABLE `x360p_order_payment_history`
ADD COLUMN `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID' AFTER `bid`
;

-- 学员课时增加2个字段
ALTER TABLE `x360p_student_lesson`
ADD COLUMN `trans_out_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '转出课时数' AFTER `transfer_lesson_hours`,
ADD COLUMN `trans_in_lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '转入课时数' AFTER `trans_out_lesson_hours`
;

-- 课消记录新增课消类型字段
ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `consume_type` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课消类型:0课时课消,1:副课时课消,2:缺课课消,3:违约课消' AFTER `lesson_amount`,
ADD COLUMN `source_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '来源类型:1:课时,2:电子钱包' AFTER `consume_type`
;

-- 新增收费项数据库表
DROP TABLE IF EXISTS `x360p_pay_item`;
CREATE TABLE `x360p_pay_item` (
  `pi_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `name` varchar(255) NOT NULL COMMENT '物品名称',
  `unit` char(4) NOT NULL COMMENT '计量单位',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  `unit_price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '单价',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态:1启用，0禁用',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`pi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='收费项目表';

-- 转让课时记录表
DROP TABLE IF EXISTS `x360p_transfer_hour_history`;
CREATE TABLE `x360p_transfer_hour_history` (
  `thh_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` varchar(255)  DEFAULT '' COMMENT '校区ID',
  `from_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转出学员ID',
  `to_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转入学员ID',
  `from_sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转出学员课时记录ID',
  `to_sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转入学员课时记录ID',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '课时数',
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_ids` varchar(255) DEFAULT '' COMMENT '可用科目',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级id',
  `remark` varchar(255) default '' COMMENT '备注',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`thh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 转让余额记录表
DROP TABLE IF EXISTS `x360p_transfer_money_history`;
CREATE TABLE `x360p_transfer_money_history` (
  `tmh_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` varchar(255)  DEFAULT '' COMMENT '校区ID',
  `from_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转出学员ID',
  `to_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转入学员ID',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT '0' COMMENT '金额',
  `remark` varchar(255) default '' COMMENT '备注',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`tmh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--  登记试听情况 新增 --
ALTER TABLE `x360p_trial_listen_arrange`
ADD COLUMN `is_arrive`  tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '0：未到 1：已到' AFTER `eid`,
ADD COLUMN `remark`  varchar(255) NULL DEFAULT NULL COMMENT '未试听原因' AFTER `is_arrive`
;



UPDATE `x360p_order_payment_history` oph left join `x360p_order` o
on oph.oid=o.oid
set oph.`sid`=o.sid where oph.`sid` = 0 and o.sid is not null
;