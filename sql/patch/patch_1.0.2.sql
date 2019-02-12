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

-- 活动报名表
DROP TABLE IF EXISTS `x360p_event_signup`;
CREATE TABLE `x360p_event_signup` (
  `es_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID,可以为0',
  `name` varchar(32) default '' COMMENT '姓名',
  `tel` varchar(32) default '' COMMENT '电话',
  `age` varchar(32) default '' COMMENT '年龄',
  `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级-3到12',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`es_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动和报名活动的学生的关联中间表';

-- 作业发表记录
DROP TABLE IF EXISTS `x360p_homework_publish`;
CREATE TABLE `x360p_homework_publish` (
  `hp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '作业发表ID',
  `bid` int(11) unsigned DEFAULT '0' COMMENT '校区ID',
  `cid` int(11) unsigned DEFAULT '0' COMMENT '班级ID',
  `lid` int(11) unsigned DEFAULT '0' COMMENT '课程ID',
  `sid` int(11) unsigned DEFAULT '0' COMMENT '学生ID',
  `hc_id` int(11) unsigned DEFAULT '0' COMMENT '作业完成ID',
  `name` varchar(255) DEFAULT '' COMMENT '作业名称',
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
  PRIMARY KEY (`hp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作业发表记录表';


INSERT INTO `x360p_dictionary` VALUES
(19,0,0,'event_type','活动类型','活动类型','1',0,1,0,0,NULL,0,0,NULL),
(180, 0, 19, '讲座','讲座','讲座','1',0,1,0,0,0,0,0,NULL),
(181, 0, 19, '期中展示','期中展示','期中展示','1',0,1,0,0,0,0,0,NULL),
(182, 0, 19, '期末展示','期末展示','期末展示','1',0,1,0,0,0,0,0,NULL),
(183, 0, 19, '优秀评比','优秀评比','优秀评比','1',0,1,0,0,0,0,0,NULL)
;