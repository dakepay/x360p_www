
INSERT INTO `x360p_dictionary` VALUES
(53,0,0,'invalid_followup','无效沟通类型','无效沟通类型','1',0,1,0,0,NULL,0,0, NULL),
(531, 0, 53, '无效号码','无效号码','无效号码','1',0,1,0,0,0,0,0,NULL),
(532, 0, 53, '电话忙','电话忙','电话忙','1',0,1,0,0,0,0,0,NULL),
(533, 0, 53, '对方不方便','对方不方便','对方不方便','1',0,1,0,0,0,0,0,NULL)
;

ALTER TABLE `x360p_employee_time_section`
ADD COLUMN `int_day`  int(11) NOT NULL DEFAULT 0 COMMENT '日期' AFTER `eid`;


 -- 外教端翻译情况自动推送计划
DROP TABLE IF EXISTS `x360p_ft_review_remind_plan`;
CREATE TABLE `x360p_ft_review_remind_plan` (
  `ftrp_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT 0 COMMENT 'og_id',
  `bid` int(11) NOT NULL DEFAULT 0,
  `int_hour` int(11) NOT NULL DEFAULT '0' COMMENT '当天推送时间',
  `eids` varchar(255) DEFAULT NULL COMMENT '是否推送老师',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户UID',
  PRIMARY KEY (`ftrp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='外教端翻译情况自动推送计划';


ALTER TABLE `x360p_lesson_standard_file`
ADD COLUMN `tb_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '教材ID' AFTER `lid`
;

ALTER TABLE `x360p_order_item`
ADD COLUMN `is_lesson_hour_end`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '结转状态' AFTER `consume_type`,
ADD COLUMN `from_lid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '升级来的课程id' AFTER `lid`,
;

-- 学员表 添加入学时间字段
ALTER TABLE `x360p_student`
ADD COLUMN `in_time`  int(11) NOT NULL DEFAULT 0 COMMENT '入学日期' AFTER `referer_sid`;


-- 更新学员名单in_time
update `x360p_student` s
left join `x360p_order` o
on s.`sid` = o.`sid`
set s.`in_time` = (
    select paid_time from x360p_order where sid = s.sid order by paid_time asc limit 1
)
where o.oid IS NOT NULL AND o.`pay_status` = 2 and s.`in_time` = 0;


-- 增加教室字段
ALTER TABLE `x360p_student_lesson_hour`
ADD COLUMN `cr_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '教室ID' AFTER `cid`
;

-- 更新student_lesson_hour 里面的 cr_id
update `x360p_student_lesson_hour` slh
left join `x360p_course_arrange` ca
on slh.`ca_id` = ca.`ca_id`
set slh.`cr_id` = ca.`cr_id`
where slh.`cr_id` = 0 and slh.`ca_id` > 0;



 -- 教材管理
DROP TABLE IF EXISTS `x360p_textbook`;
CREATE TABLE `x360p_textbook` (
  `tb_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '是否公共',
  `suit_bids` varchar(255) DEFAULT '' COMMENT '适用校区',
  `tb_name` varchar(255) DEFAULT NULL COMMENT '教材名',
  `tb_org_name` varchar(255) DEFAULT NULL COMMENT '出版机构',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户UID',
  PRIMARY KEY (`tb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='教材表';

 -- 教材章节管理
DROP TABLE IF EXISTS `x360p_textbook_section`;
CREATE TABLE `x360p_textbook_section` (
  `tbs_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `tb_id` int(11) NOT NULL DEFAULT 0  COMMENT '教材ID',
  `section_title` varchar(255) DEFAULT NULL COMMENT '章节名',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '章节序号',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户UID',
  PRIMARY KEY (`tbs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='教材章节表';


ALTER TABLE `x360p_order_item`
ADD COLUMN `from_sl_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '升级来的sl_id' AFTER `lid`
;

 -- 盛开人员ID长度
alter table x360p_student modify column face_id varchar(64);


alter table x360p_student_referer modify column referer_teacher_eids varchar(255) COMMENT '老师eid';