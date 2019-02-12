DROP TABLE IF EXISTS `x360p_tally_file`;
CREATE TABLE `x360p_tally_file` (
  `tf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '校区ID',
  `tid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '帐户流水表ID',
  `file_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '文件ID',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `file_type` varchar(16) NOT NULL DEFAULT 'image' COMMENT '文件类型:image,audio,video,file',
  `duration` varchar(25) NOT NULL DEFAULT '' COMMENT '当文件为mp3时该字段不为空。',
  `media_type` char(50) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户UID',
  PRIMARY KEY (`tf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='帐户流水关联附件表';

DROP TABLE IF EXISTS `x360p_material_sale`;
CREATE TABLE `x360p_material_sale` (
  `mts_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '校区ID',
  `eid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '销售人员ID',
  `aa_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '账户',
  `ms_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '仓库ID',
  `mt_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '物品ID',
  `nums` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '数量',
  `amount` decimal(11,2) unsigned NOT NULL DEFAULT 0.00 COMMENT '金额',
  `name` varchar(16) NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户UID',
  PRIMARY KEY (`mts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='物品销售记录开单记录表';


ALTER TABLE `x360p_homework_complete`
ADD COLUMN `is_rejected` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否驳回' AFTER `sart_id`,
ADD COLUMN `rejected_reason` varchar(16) NOT NULL DEFAULT '' COMMENT '驳回原因' AFTER `is_rejected`,
ADD COLUMN `rejected_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间' AFTER `rejected_reason`
;

ALTER TABLE `x360p_review_tpl_setting`
ADD COLUMN review_style int(11) unsigned NOT NULL DEFAULT '0' COMMENT '模板样式:0通用课评,1:专业课评' AFTER `name`
;

ALTER TABLE `x360p_employee_receipt`
ADD COLUMN `mts_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '对外物品销售记录' AFTER `oid`
;

-- 促销规则
DROP TABLE IF EXISTS `x360p_promotion_rule`;
CREATE TABLE `x360p_promotion_rule` (
  `pr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '促销名称',
  `promotion_type` tinyint(1) unsigned NOT NULL COMMENT '促销类型 1:打折,2:满减,3:直减,4:送课时',
  `valve` int(11) unsigned DEFAULT '0' COMMENT '限度，eg: 满3000减200, valve=3000',
  `promotion_value` decimal(11,2) unsigned DEFAULT '0.00' COMMENT '折扣值',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否适用所有校区',
  `suit_bids` varchar(255) DEFAULT '' COMMENT '促销范围(校区)',
  `suit_lids` varchar(255) DEFAULT '' COMMENT '适用课程',
  `suit_sj_ids` varchar(255) DEFAULT '' COMMENT '适用科目',
  `start_time` int(11) unsigned DEFAULT '0' COMMENT '促销开始时间',
  `end_time` int(11) unsigned DEFAULT '0' COMMENT '促销结束时间',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态 0:失效，1：正常',
  `is_fixed` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否固定优惠',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`pr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='促销规则表';



ALTER TABLE `x360p_course_arrange_student`
ADD COLUMN `is_bk` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否约课' AFTER `is_makeup`,
ADD COLUMN `bk_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '预约时间' AFTER `is_bk`,
ADD COLUMN `bk_seat` varchar(16) DEFAULT '' COMMENT '促销范围(校区)' AFTER `bk_time`
;

ALTER TABLE `x360p_course_arrange`
ADD COLUMN `is_bk_open` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否可以预约' AFTER `is_demo`
;

ALTER TABLE `x360p_order_item`
ADD COLUMN `is_referer` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否转介绍' AFTER `is_lesson_hour_end`,
ADD COLUMN `referer_sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转介绍学员' AFTER `is_referer`,
ADD COLUMN `referer_teacher_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转介绍老师' AFTER `referer_sid`,
ADD COLUMN `referer_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '转介绍学管师' AFTER `referer_teacher_id`
;

ALTER TABLE `x360p_review`
ADD COLUMN `review_style` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否专业课评' AFTER `lesson_type`
;

ALTER TABLE `x360p_review_student`
ADD COLUMN `review_style` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否专业课评' AFTER `lesson_type`
;

-- 排课记录新增关联教材及章节ID
ALTER TABLE `x360p_course_arrange`
ADD COLUMN `tb_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教材ID' AFTER `reason`,
ADD COLUMN `tbs_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教材章节ID' AFTER `tb_id`
;
-- 授课记录新增关联教材及章节ID
ALTER TABLE `x360p_class_attendance`
ADD COLUMN `tb_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教材ID' AFTER `confirm_time`,
ADD COLUMN `tbs_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教材章节ID' AFTER `tb_id`
;

ALTER TABLE `x360p_student_money_history`
ADD COLUMN `consume_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '收费类型：1新报，2续报，3扩科' AFTER `business_type`
;

ALTER TABLE `x360p_student_absence`
ADD COLUMN `ma_ca_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '补课id' AFTER `ca_id`
;