-- 活动表
DROP TABLE IF EXISTS `x360p_event`;
CREATE TABLE `x360p_event` (
  `event_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动ID',
  `event_name` varchar(255) NOT NULL DEFAULT '' COMMENT '活动名称',
  `is_event_online` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否线上活动',
  `bids` varchar(255) NOT NULL DEFAULT '' COMMENT '活动执行校区id用逗号分隔',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'scope位class的时候cid有效',
  `scope` enum('class','branch','global') NOT NULL DEFAULT 'global' COMMENT '活动范围',
  `event_type_did` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '活动类型字典ID(180:讲座,181:期中展示,182:期末展示,183:优秀评比)',
  `event_start_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动开始时间',
  `event_end_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动结束时间',
  `share_title` varchar(255) NOT NULL DEFAULT '' COMMENT '宣传分享标题',
  `share_image_url` varchar(255) NOT NULL DEFAULT '' COMMENT '分享图片路径(300*300)',
  `event_image_url` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图片(600px * 300px)',
  `event_meta` text COMMENT '活动结构化描述',
  `event_content` text NOT NULL COMMENT '活动内容介绍',
  `link_url` varchar(255) DEFAULT NULL COMMENT '链接URL',
  `status` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态（0:已禁用,1:正常,2:已结束,3:已取消)',
  `apply_nums_limit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '报名人数限制',
  `view_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览人数',
  `apply_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '报名人数',
  `summary` text COMMENT '活动总结',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动表';

-- 活动附件表
DROP TABLE IF EXISTS `x360p_event_attachment`;
CREATE TABLE `x360p_event_attachment` (
  `ea_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动附件ID',
  `event_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `file_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件ID',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件路径',
  `file_size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '附件大小(字节)',
  `file_ext` char(8) NOT NULL DEFAULT '' COMMENT '附件拓展名',
  `create_time` int(11) NOT NULL,
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`ea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动对应的附件表';

-- 作业发表记录表
DROP TABLE IF EXISTS `x360p_homework_publish`;
CREATE TABLE `x360p_homework_publish` (
  `hp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '作业发表ID',
  `bid` int(11) unsigned DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned DEFAULT '0' COMMENT '班级ID',
  `lid` int(11) unsigned DEFAULT '0' COMMENT '课程ID',
  `sid` int(11) unsigned DEFAULT '0' COMMENT '学生ID',
  `hc_id` int(11) unsigned DEFAULT '0' COMMENT '作业完成ID',
  `media_type` tinyint(1) unsigned DEFAULT '0' COMMENT '发表媒体类型(0:纸媒,1:线上媒体)',
  `media_name` varchar(255) DEFAULT '' COMMENT '媒体名称',
  `link_url` varchar(255) DEFAULT NULL COMMENT '可查看链接',
  `description` varchar(255) DEFAULT '' COMMENT '发表说明',
  `publish_time` int(11) unsigned DEFAULT NULL COMMENT '发表日期',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`hp_id`,`delete_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业发表记录表';

-- 学员课时赠送记录
DROP TABLE IF EXISTS `x360p_student_lesson_present`;
CREATE TABLE `x360p_student_lesson_present` (
  `slp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID,可以为0',
  `oi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单条目ID，可以为0',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员课时记录ID',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '赠送课时数',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`slp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员课时赠送记录';

-- 学员课时结转记录
DROP TABLE IF EXISTS `x360p_student_lesson_transfer`;
CREATE TABLE `x360p_student_lesson_transfer` (
  `slt_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员课时记录ID',
  `lesson_hours` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结转课时数',
  `unit_price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结转单价',
  `lesson_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '结转金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`slt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员课时结转记录';

-- 短信模板配置
DROP TABLE IF EXISTS `x360p_sms_tpl_define`;
CREATE TABLE `x360p_sms_tpl_define` (
  `std_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(128) default '' COMMENT '模板名称',
  `service_name` varchar(64) default '' COMMENT '短信服务商ID',
  `tpl_id` varchar(32) default '' COMMENT '模板ID',
  `tpl_define` text COMMENT '模板定义,json结构',
  `apply_tpl` varchar(512) default '' COMMENT '运营商短信模板',
  `business_type` varchar(32) default '' COMMENT '业务类型',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`std_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短信模板配置';


-- 学情问卷主表新增问卷题目类型
ALTER TABLE `x360p_questionnaire`
ADD COLUMN   `qt_dids` varchar(255) DEFAULT '' COMMENT '问卷类型字典ID多选,逗号分隔' AFTER `name`
;

ALTER TABLE `x360p_file_package`
ADD COLUMN `short_id` char(16) NOT NULL DEFAULT '' COMMENT '短ID唯一' AFTER `fp_id`
;

ALTER TABLE `x360p_study_situation`
ADD COLUMN `short_id` char(16) NOT NULL DEFAULT '' COMMENT '短ID唯一' AFTER `ss_id`,
ADD COLUMN `lbs_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学习方案ID' AFTER `remark`
;

ALTER TABLE `x360p_knowledge_item`
CHANGE COLUMN `ktype` `ktype_did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '知识类型:211系统帮助,212工作指引,213沟通话术' AFTER `bid`
;
-- 消息发送增加了客户ID字段
ALTER TABLE `x360p_message`
ADD COLUMN `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID' AFTER `sid`
;
-- 客户表增加了openid字段
ALTER TABLE `x360p_customer`
ADD COLUMN `openid` varchar(64) NOT NULL DEFAULT '' COMMENT '微信openid' AFTER `second_tel`
;

-- 新增字典表
INSERT INTO `x360p_dictionary` VALUES
(19,0,0,'event_type','活动类型','活动类型','1',0,1,0,0,NULL,0,0,NULL),
(20,0,0,'questionnaire_type','学情问卷题目分类','学情问卷题目分类','1',0,1,0,0,NULL,0,0,NULL),
(21,0,0,'knowlege_type','知识类型','知识类型','1',0,1,0,0,NULL,0,0,NULL),
(22,0,0,'class_service_type','班级服务类型','班级服务类型','1',0,1,0,0,NULL,0,0,NULL),
(23,0,0,'student_service_type','个性服务类型','个性服务类型','1',0,1,0,0,NULL,0,0,NULL),
(180, 0, 19, '讲座','讲座','讲座','1',0,1,0,0,0,0,0,NULL),
(181, 0, 19, '期中展示','期中展示','期中展示','1',0,1,0,0,0,0,0,NULL),
(182, 0, 19, '期末展示','期末展示','期末展示','1',0,1,0,0,0,0,0,NULL),
(183, 0, 19, '优秀评比','优秀评比','优秀评比','1',0,1,0,0,0,0,0,NULL),
(190, 0, 20, '倾听力','倾听力','倾听力','1',0,1,0,0,0,0,0,NULL),
(191, 0, 20, '阅读力','阅读力','阅读力','1',0,1,0,0,0,0,0,NULL),
(192, 0, 20, '研学力','研学力','研学力','1',0,1,0,0,0,0,0,NULL),
(193, 0, 20, '思维力','思维力','思维力','1',0,1,0,0,0,0,0,NULL),
(194, 0, 20, '口语力','口语力','口语力','1',0,1,0,0,0,0,0,NULL),
(195, 0, 20, '写作文笔','写作文笔','写作文笔','1',0,1,0,0,0,0,0,NULL),
(196, 0, 20, '写作结构','写作结构','写作结构','1',0,1,0,0,0,0,0,NULL),
(197, 0, 20, '写作构思','写作构思','写作构思','1',0,1,0,0,0,0,0,NULL),
(198, 0, 20, '文学创作','文学创作','文学创作','1',0,1,0,0,0,0,0,NULL),
(199, 0, 20, '表演力','表演力','表演力','1',0,1,0,0,0,0,0,NULL),
(200, 0, 20, '考试力','考试力','考试力','1',0,1,0,0,0,0,0,NULL),
(201, 0, 20, '学习动机','学习动机','学习动机','1',0,1,0,0,0,0,0,NULL),
(202, 0, 20, '学习习惯','学习习惯','学习习惯','1',0,1,0,0,0,0,0,NULL),
(203, 0, 20, '学习方法','学习方法','学习方法','1',0,1,0,0,0,0,0,NULL),
(211, 0, 21, '系统帮助','系统帮助','系统帮助','1',0,1,0,0,0,0,0,NULL),
(212, 0, 21, '工作指引','工作指引','工作指引','1',0,1,0,0,0,0,0,NULL),
(213, 0, 21, '沟通话术','沟通话术','沟通话术','1',0,1,0,0,0,0,0,NULL)
;