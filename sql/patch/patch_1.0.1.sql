
-- 服务任务也叫服务日程
DROP TABLE IF EXISTS `x360p_service_task`;
CREATE TABLE `x360p_service_task` (
  `st_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `object_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '对象类型:0客户，1学员,2班级',
  `st_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '服务类型:任务操作字典ID:service_type = st',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务完成日期截止',
  `int_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务完成时间截止',
  `own_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务完成人员工ID',
  `remark` text NOT NULL COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0待办，1完成，-1取消',
  `create_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建人员工ID，0表示系统创建',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`st_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务日程表';

-- 服务记录也叫服务过程记录
DROP TABLE IF EXISTS `x360p_service_record`;
CREATE TABLE `x360p_service_record` (
  `sr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `object_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '对象类型:0客户，1学员,2班级',
  `st_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '服务类型:任务操作字典ID:service_type = st',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成日期',
  `int_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成时间',
  `url` varchar(255) default '' COMMENT '关联URL',
  `is_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推送',
  `rel_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联记录ID',
  `content` text NOT NULL COMMENT '服务于内容',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '员工ID',
  `student_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '覆盖学生人数',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`sr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务日程表';

-- 服务记录关联附件
DROP TABLE IF EXISTS `x360p_service_record_file`;
CREATE TABLE `x360p_service_record_file` (
  `srf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `sr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课标ID',
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
  PRIMARY KEY (`srf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务记录文件表';


-- 服务推送任务表
DROP TABLE IF EXISTS `x360p_service_push_task`;
CREATE TABLE `x360p_service_push_task` (
  `spt_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `object_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '对象类型:0客户，1学员,2班级',
  `app_id` varchar(64) default '' COMMENT '公众号AppId',
  `openid` varchar(64) default '' COMMENT '粉丝openid',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `remark` varchar(255) default '' COMMENT '推送备注',
  `url` varchar(255) default '' COMMENT '推送的URL地址',
  `content_type` varchar(32) default '' COMMENT '内容类型 file_package,link,page 三种类型',
  `rel_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联内容ID',
  `is_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推送',
  `push_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送时间',
  `push_success_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送数量',
  `push_failure_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送失败数量',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`spt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务记录文件表';

-- 文件包表
DROP TABLE IF EXISTS `x360p_file_package`;
CREATE TABLE `x360p_file_package` (
  `fp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `short_id` char(16) NOT NULL DEFAULT '' COMMENT '短ID唯一',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `title` varchar(255) DEFAULT '' COMMENT '文件包说明',
  `files_package_id` char(32) NOT NULL DEFAULT '' COMMENT '包所有文件id的md5值',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`fp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件包表';

-- 文件包关联附件
DROP TABLE IF EXISTS `x360p_file_package_file`;
CREATE TABLE `x360p_file_package_file` (
  `fpf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `fp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课标ID',
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
  PRIMARY KEY (`fpf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件包附件表';

-- 文件包浏览记录表（谁看了)
DROP TABLE IF EXISTS `x360p_file_package_view`;
CREATE TABLE `x360p_file_package_view` (
  `fpv_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `openid` varchar(64) default '' COMMENT '粉丝OPENID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`fpv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件包浏览记录表';

-- 移动端页面管理
DROP TABLE IF EXISTS `x360p_page`;
CREATE TABLE `x360p_page` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `is_cate` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否分类',
  `parent_pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级页面ID,分类ID',
  `thumb_url` varchar(255) default '' COMMENT '图片URL',
  `title` varchar(255) default '' COMMENT '标题',
  `content` text COMMENT '内容',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='移动端页面';

-- 学情问卷
DROP TABLE IF EXISTS `x360p_questionnaire`;
CREATE TABLE `x360p_questionnaire` (
  `qid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(255) default '' COMMENT '标题',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`qid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='问卷';


-- 学情问卷题目
DROP TABLE IF EXISTS `x360p_questionnaire_item`;
CREATE TABLE `x360p_questionnaire_item` (
  `qi_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `qid` int(11) DEFAULT '0' COMMENT '问卷id',
  `qt_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '问卷类型字典ID',
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `choices` text COMMENT '选项,JSON格式',
  `is_multi` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否多选',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`qi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='问卷题目';



-- 学情服务
DROP TABLE IF EXISTS `x360p_study_situation`;
CREATE TABLE `x360p_study_situation` (
  `ss_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `short_id` char(16) NOT NULL DEFAULT '' COMMENT '短ID唯一',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `qid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '问卷ID',
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `content` text COMMENT 'json数据',
  `create_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建人eid',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成日期',
  `int_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '评语',
  `lbs_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学习方案ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ss_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学情记录表';

-- 学情服务条目
DROP TABLE IF EXISTS `x360p_study_situation_item`;
CREATE TABLE `x360p_study_situation_item` (
  `ssi_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `ss_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学情服务ID',
  `qid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '问卷ID',
  `qi_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '问卷题目ID',
  `answer` text COMMENT '答案JSON结构',
  `score` decimal(11,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '得分',
  `is_unknown` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否位未知',
  `next_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下次约定了解日期',
  `is_parent_focus` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否家长关注项',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ssi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='移动端页面';

-- 学习套餐
DROP TABLE IF EXISTS `x360p_lesson_suit_define`;
CREATE TABLE `x360p_lesson_suit_define` (
  `lsd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '套餐名',
  `define` text COMMENT '定义JSON结构:[{product_level_did:nums}]',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`lsd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学习套餐定义表';
-- 学习方案
DROP TABLE IF EXISTS `x360p_lesson_buy_suit`;
CREATE TABLE `x360p_lesson_buy_suit` (
  `lbs_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `title` varchar(255) default '' COMMENT '方案标题',
  `define` text COMMENT '定义JSON结构:[{lid:nums}]',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`lbs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学习套餐定义表';

-- 知识管理
DROP TABLE IF EXISTS `x360p_knowledge_item`;
CREATE TABLE `x360p_knowledge_item` (
  `ki_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `ktype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '知识类型:1系统帮助,2工作指引,3沟通话术',
  `router_uri` varchar(255) default '' COMMENT '路由URI',
  `system_uri` varchar(255) default '' COMMENT '系统内置URI',
  `title` varchar(255) default '' COMMENT '方案标题',
  `keywords` varchar(255) default '' COMMENT '关键词,逗号分隔',
  `content` text COMMENT '内容',
  `stars` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '星星数',
  `create_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建员工ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ki_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='知识条目表';


ALTER TABLE `x360p_base`.`x360p_material`
ADD COLUMN `parent_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父ID' AFTER `og_id`;

ALTER TABLE `x360p_material`
ADD COLUMN `is_cate`  tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否是分类栏目' AFTER `sale_price`;

ALTER TABLE `x360p_student_lesson`
ADD COLUMN `import_lesson_hours`  decimal(11,2) NOT NULL DEFAULT 0 COMMENT '导入课时数' AFTER `lesson_hours`;

DROP TABLE IF EXISTS `x360p_student_lesson_import_log`;
CREATE TABLE `x360p_student_lesson_import_log` (
  `slil_id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生id',
  `sj_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '适用科目',
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '科目id',
  `lesson_hours` double(11,2) NOT NULL DEFAULT '0.00' COMMENT '导入课时数量',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  PRIMARY KEY (`slil_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生课时导入记录';


ALTER TABLE `x360p_file`
MODIFY COLUMN `file_name`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `file_type`;

