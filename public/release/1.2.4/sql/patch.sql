-- 市场名单新增市场名单ID，市场名单渠道ID参数
ALTER TABLE `x360p_customer`
ADD COLUMN `mc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '市场渠道ID' AFTER `next_follow_time`,
ADD COLUMN `mcl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '市场名单ID' AFTER `mc_id`
;

-- 员工表增加bid 主校区
ALTER TABLE `x360p_employee`
ADD COLUMN `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID' AFTER `og_id`
;
-- 更新员工归属校区ID
update `x360p_employee`
set `bid`=SUBSTRING_INDEX(`bids`,',',1)
where `bid` = 0 and LOCATE(',',bids)>0
;

update `x360p_employee`
set `bid`= CONVERT(`bids`,SIGNED)
where `bid` = 0 and `bids` <> '' and LOCATE(',',`bids`)=0
;


-- 学员储值卡增加 初始金额
ALTER  TABLE `x360p_student_debit_card`
ADD COLUMN `start_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0' COMMENT '初始金额' AFTER `oi_id`,
ADD COLUMN `buy_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '购买来源:0为订单购买,1为余额兑换' AFTER `dc_id`
;

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