-- 课标新增课程章节字段
ALTER TABLE `x360p_lesson_standard_file`
ADD COLUMN `chapter_index` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '章节序号' AFTER  `lid`
;

-- 用户表新增拓展字段
ALTER TABLE `x360p_user`
ADD COLUMN `is_ext` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是来自dss导入的员工，0：否，1：是' AFTER `is_admin`,
ADD COLUMN `ext_password` varchar(32) DEFAULT NULL COMMENT '员工在dss系统的登录密码' AFTER `is_ext`
;
