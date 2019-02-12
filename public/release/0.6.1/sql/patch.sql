ALTER TABLE `x360p_class_attendance`
MODIFY COLUMN `class_student_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '非补课和试听的应到人数',
MODIFY COLUMN `need_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总的应到人数：包括正常学员，补课学员，试听学员',
MODIFY COLUMN `makeup_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '应到补课人数',
MODIFY COLUMN `trial_nums` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '应到试听人数'
;

ALTER TABLE `x360p_dictionary`
MODIFY COLUMN `is_system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否系统默认'
;

INSERT INTO `x360p_dictionary` VALUES 
(15,0,0,'drop_out_reason','退学原因','退学原因','1',0,1,0,0,NULL,0,0,NULL),
(157,0,15,'','无理由退学','无理由退学','1',0,1,0,0,NULL,0,0,NULL),
(158,0,15,'','服务不满意','服务不满意','1',0,1,0,0,NULL,0,0,NULL)
;

ALTER TABLE `x360p_student`
ADD COLUMN `student_lesson_times` int(11) NOT NULL DEFAULT '0' COMMENT '购买的总课次' AFTER `type`,
ADD COLUMN `student_lesson_remain_times` int(11) NOT NULL DEFAULT '0' COMMENT '学生购买的课程总剩余次数' AFTER `student_lesson_times`
;

ALTER TABLE `x360p_student_attendance`
MODIFY COLUMN  `is_late` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否迟到(0:未迟到,1:迟到),只有刷卡考勤才会有这个字段'
;

ALTER TABLE `x360p_trial_listen_arrange`
ADD COLUMN `catt_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'class_attendance的主键' AFTER `ca_id`
;
