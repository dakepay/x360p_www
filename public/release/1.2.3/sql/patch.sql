-- 储值卡定义表
DROP TABLE IF EXISTS `x360p_debit_card`;
CREATE TABLE `x360p_debit_card` (
  `dc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bids` varchar(255) DEFAULT '' COMMENT '校区ID',
  `dpt_ids` varchar(255) DEFAULT '' COMMENT '大区ID',
  `card_name` varchar(64) DEFAULT '' COMMENT '卡名',
  `amount` decimal(11,2) DEFAULT '0.00' COMMENT '金额',
  `discount_define` text COMMENT '折扣定义(JSON格式)',
  `expire_days` int(11) DEFAULT '365' COMMENT '有效期天数0为无限制',
  `upgrade_vip_level` int(11) DEFAULT '0' COMMENT '升级到会员级别',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`dc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 市场名单增加分配时间
ALTER TABLE `x360p_market_clue`
ADD COLUMN `assigned_time`  int(11) NOT NULL DEFAULT 0 COMMENT '分配时间' AFTER `assigned_eid`;

-- 消息表新增一些字段
ALTER TABLE `x360p_message`
ADD COLUMN `eid` int(11) NOT NULL DEFAULT '0' COMMENT 'eid' AFTER `cu_id`,
ADD COLUMN `tpl_data` text COMMENT '消息模板字段对应信息' AFTER `title`,
ADD COLUMN `error` varchar(255) DEFAULT NULL COMMENT '发送失败消息' AFTER `status`,
ADD COLUMN `mgh_id` int(11) NOT NULL DEFAULT '0' COMMENT '群发id' AFTER `error`,
MODIFY COLUMN `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发送成功:0成功，其余失败' AFTER `url`,
MODIFY COLUMN `business_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '业务ID' AFTER `business_type`
;

-- 短信群发记录表
DROP TABLE IF EXISTS `x360p_message_group_history`;
CREATE TABLE `x360p_message_group_history` (
  `mgh_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT 'og_id',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT 'bid',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1: 短信， 2：微信',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '需发送总数',
  `success_num` int(11) NOT NULL DEFAULT '0' COMMENT '成功人数',
  `content` text COMMENT '内容',
  `tpl_id` varchar(255) NOT NULL DEFAULT '0' COMMENT '短信tpl_id',
  `business_type` varchar(255) DEFAULT NULL COMMENT '微信模板',
  `tpl_data` text COMMENT '模板值',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  PRIMARY KEY (`mgh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短信群发记录';

-- 学员订单需要关联学员储值卡记录
ALTER TABLE `x360p_order`
ADD COLUMN `sdc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员储值卡记录ID' AFTER `is_demo`,
ADD COLUMN `is_debit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否储值订单,默认为0' AFTER `is_demo`
;

ALTER TABLE `x360p_order_item`
ADD COLUMN `sdc_id`  int(11) NOT NULL DEFAULT 0 COMMENT '储蓄卡id' AFTER `expire_time`,
ADD COLUMN `dc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '储值卡ID' AFTER `gid`,
MODIFY COLUMN `gtype` tinyint(1) unsigned DEFAULT '0' COMMENT '商品类型 0：课程，1：物品 2:储值卡' AFTER `is_deliver`,
MODIFY COLUMN `is_deliver` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否发货或储值，针对物品和储值' AFTER `dc_id`
;

-- 短信发送记录新增一些字段
ALTER TABLE `x360p_sms_history`
ADD COLUMN `bid` int(11) NOT NULL DEFAULT '0' COMMENT 'bid' AFTER `og_id`,
ADD COLUMN `sid` int(11) NOT NULL DEFAULT '0' COMMENT 'sid' AFTER `mobile`,
ADD COLUMN `cu_id` int(11) NOT NULL DEFAULT '0' COMMENT 'cu_id' AFTER `sid`,
ADD COLUMN `mcl_id` int(11) NOT NULL DEFAULT '0' COMMENT '市场名单' AFTER `cu_id`,
ADD COLUMN `eid` int(11) NOT NULL DEFAULT '0' COMMENT '员工id' AFTER `mcl_id`,
ADD COLUMN `mgh_id` int(11) NOT NULL DEFAULT '0' COMMENT '群发id' AFTER `tpl_data`,
ADD COLUMN `error` varchar(255) DEFAULT NULL COMMENT '发送错误消息' AFTER `status`,
ADD COLUMN `is_sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0为等待发送，1已经发送' AFTER `status`,
MODIFY COLUMN `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发送成功:0成功，其余失败' AFTER `mgh_id`
;

-- 订单相关字段支持储值卡订单记录
ALTER TABLE `x360p_order_receipt_bill`
ADD COLUMN `is_debit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否储值收据,默认为0' AFTER `is_demo`
;

-- 学员储值记录
DROP TABLE IF EXISTS `x360p_student_debit_card`;
CREATE TABLE `x360p_student_debit_card` (
  `sdc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` varchar(255) DEFAULT '' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `dc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '储值卡ID',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单条目ID',
  `remain_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '剩余金额',
  `is_used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用0未使用，1部分使用，2全部使用',
  `buy_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买日期',
  `expire_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '过期日期',
  `is_expired` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否过期',
  `upgrade_vip_level` int(11) DEFAULT '0' COMMENT '升级到会员级别',
  `create_uid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `delete_uid` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`sdc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 课耗增加edu_eid字段
ALTER TABLE `x360p_employee_lesson_hour`
ADD COLUMN `edu_eid`  int(11) UNSIGNED NULL DEFAULT 0 COMMENT '导师ID' AFTER `second_eid`;

ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `edu_eid`  int(11) UNSIGNED NULL DEFAULT 0 COMMENT '导师ID' AFTER `second_eid`;


-- 增加导入金额类型
ALTER TABLE `x360p_student_money_history`
ADD COLUMN `sdc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员储值卡记录ID' AFTER `sid`,
ADD COLUMN `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单条目ID' AFTER `sdc_id`,
MODIFY COLUMN `business_type` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '业务类型:(1:结转,2:退费,3:充值, 4:下单, 5:订单续费 ,10 导入,11:用户手动增加， 12手动减少)' AFTER `og_id`
;


CREATE TABLE IF NOT EXISTS `x360p_wechat_tpl_define`(
  `wtd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(128) DEFAULT '' COMMENT '模板名称',
  `tpl_id` varchar(64) DEFAULT '' COMMENT '模板ID',
  `tpl_define` text COMMENT '模板定义,json结构',
  `business_type` varchar(32) DEFAULT '' COMMENT '业务类型(可选无)',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`wtd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='微信消息模板配置';

-- 增加微信模板定制的字段长度
ALTER TABLE `x360p_wechat_tpl_define`
MODIFY COLUMN `tpl_id` varchar(64) DEFAULT '' COMMENT '模板ID' AFTER `name`
;

-- 学员课时导入记录新增校区及机构Id
ALTER TABLE `x360p_student_lesson_import_log`
ADD COLUMN `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID' AFTER `slil_id`,
ADD COLUMN `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID' AFTER `og_id`
;

UPDATE `x360p_student_lesson_import_log` slil left join `x360p_student` s ON slil.sid = s.sid
set slil.og_id = s.og_id,slil.bid = s.bid
;
-- 作业的精批和普批注释修改
ALTER TABLE `x360p_homework_complete`
MODIFY COLUMN `check_level` tinyint(1) unsigned DEFAULT '0' COMMENT '批改等级(0:普批，1：精批)' AFTER `check_uid`
;
-- 学员增加服务级别
ALTER TABLE `x360p_student`
ADD COLUMN `service_level` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '服务级别' AFTER `vip_level`
;

-- 更新流水日期
update x360p_tally t left join x360p_order_payment_history oph on t.relate_id=oph.oph_id
set t.int_day=from_unixtime(oph.paid_time,'%Y%m%d')
where t.cate=1 and t.relate_id>0 and t.amount>0
;

-- 学员表新增年级字段相对输入时间
ALTER TABLE `x360p_student`
ADD COLUMN `grade_update_int_ym` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '年级更新年月' AFTER `school_grade`
;

UPDATE `x360p_student`
set `grade_update_int_ym` = FROM_UNIXTIME(`update_time`,'%Y%m') where `school_grade` <> 0;
