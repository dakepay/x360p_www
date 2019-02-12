
ALTER TABLE `x360p_customer_follow_up` 
MODIFY COLUMN `customer_status_did` int(11) NOT NULL DEFAULT 0 COMMENT '客户状态字典ID' AFTER `intetion_level`
;

ALTER TABLE `x360p_tally`
MODIFY COLUMN `amount`  decimal(11,2) NOT NULL DEFAULT 0 COMMENT '金额' AFTER `employee_th_id`;

ALTER TABLE `x360p_employee_receipt`
ADD COLUMN `sale_role_did`  int NOT NULL DEFAULT 0 COMMENT '销售角色ID' AFTER `eid`;

ALTER TABLE `x360p_order_cut_amount`
ADD COLUMN `sid`  int NULL DEFAULT 0 COMMENT '学生id' AFTER `bid`;

INSERT INTO `x360p_season_date`  VALUES 
(5,0,0,0,'A',99990101,99991231,0,0,NULL,0,0,0)
;

INSERT INTO `x360p_dictionary` VALUES 
(159,0,8,'已到访','已到访','已到访','1','0','1',1508918674,18,1508919955,0,0,NULL),
(160,0,12,'A','全年','A','1',0,1,1508919955,18,1508919955,0,0,NULL)
;



ALTER TABLE `x360p_student` 
CHANGE COLUMN `type` `student_type` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT '学员类型：0.体验学员 1.正式学员 2.vip学员' AFTER `quit_reason`
;




ALTER TABLE `x360p_class`
MODIFY COLUMN `season` char(1) NOT NULL DEFAULT 'A' COMMENT '季节' AFTER `year`
;
ALTER TABLE `x360p_class_schedule`
MODIFY COLUMN `season` char(1) NOT NULL DEFAULT 'A' COMMENT '季度' AFTER `year`
;

ALTER TABLE `x360p_course_arrange`
MODIFY COLUMN `season` char(1) NOT NULL DEFAULT 'A' COMMENT '季度' AFTER `chapter_index`
;

ALTER TABLE `x360p_lesson`
MODIFY COLUMN `season` char(1) NOT NULL DEFAULT 'A' COMMENT '学期季节' AFTER `year`
;

ALTER TABLE `x360p_time_section`
MODIFY COLUMN `season` char(1) NOT NULL DEFAULT 'A' COMMENT '季节' AFTER `bid`
;
ALTER TABLE `x360p_student_absence`
MODIFY COLUMN `catt_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'class_attendance考勤记录ID' AFTER `ca_id`,
MODIFY COLUMN `satt_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '学生的考勤id（student_lesson）' AFTER `catt_id`,
COMMENT='缺课记录表';

ALTER TABLE `x360p_makeup_arrange`
CHANGE COLUMN `is_attendance` `catt_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'class_attendance表主键' AFTER `int_end_hour`,
CHANGE COLUMN `attendance_status` `satt_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'student_attendance表主键' AFTER `catt_id`;

