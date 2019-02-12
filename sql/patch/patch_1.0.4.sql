CREATE TABLE `x360p_wechat_tpl_define` (
  `wtd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(128) DEFAULT '' COMMENT '模板名称',
  `tpl_id` varchar(32) DEFAULT '' COMMENT '模板ID',
  `tpl_define` text COMMENT '模板定义,json结构',
  `business_type` varchar(32) DEFAULT '' COMMENT '业务类型(可选无)',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`wtd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信消息模板配置';

-- 学情服务增加几个字段
ALTER TABLE `x360p_study_situation`
ADD COLUMN `is_push` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推送,1:推送短信,2:推送微信,3:短信、微信都推送' AFTER `lbs_id`,
ADD COLUMN `push_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推送时间' AFTER `is_push`,
ADD COLUMN `is_query` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否查询,0为未查询，1为1查询' AFTER `push_time`,
ADD COLUMN `query_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查询时间' AFTER `is_query`,
ADD COLUMN `query_openid` varchar(32) DEFAULT '' COMMENT '查询openid' AFTER `query_time`,
ADD COLUMN `is_view` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否查看' AFTER `query_openid`,
ADD COLUMN `view_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查看时间' AFTER `is_view`
;


-- 排课学员表增加一个是否取消排课字段
ALTER TABLE `x360p_course_arrange_student`
ADD COLUMN `is_cancel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否取消' AFTER `is_attendance`
;

-- 所有年级字段都更改为允许负数
ALTER TABLE `x360p_lesson`
MODIFY COLUMN `fit_grade_start` smallint(11) NOT NULL DEFAULT 0 COMMENT '适合年级开始' AFTER `fit_age_end`,
MODIFY COLUMN `fit_grade_end` smallint(11) NOT NULL DEFAULT 0 COMMENT '适合年级结束' AFTER `fit_grade_start`
;

ALTER TABLE `x360p_class`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT 0 COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_class_attendance`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_course_arrange`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_course_arrange_student`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_employee_lesson_hour`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;


ALTER TABLE `x360p_student_attendance`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;


ALTER TABLE `x360p_student_absence`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;


ALTER TABLE `x360p_student_lesson`
MODIFY COLUMN `fit_grade_start` int(11) NOT NULL DEFAULT '0' COMMENT '适应年级start' AFTER `sj_ids`,
MODIFY COLUMN `fit_grade_end` int(11) NOT NULL DEFAULT '0' COMMENT '适应年级end' AFTER `fit_grade_start`
;

ALTER TABLE `x360p_student_lesson_hour`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;


ALTER TABLE `x360p_student_leave`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

ALTER TABLE `x360p_makeup_arrange`
MODIFY COLUMN `grade` int(11) NOT NULL DEFAULT '0' COMMENT '年级' AFTER `sj_id`
;

