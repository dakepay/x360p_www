
ALTER TABLE `x360p_class_attendance` 
ADD COLUMN   `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型(0:班课,1:1对1,2:1对多,3:研学旅行团),创建课程的时候定义了' AFTER `lid`;


ALTER TABLE `x360p_class_log` 
CHANGE COLUMN `ct_id` `cl_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键自增ID' FIRST,
ADD COLUMN `bid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '校区ID' AFTER `og_id`,
MODIFY COLUMN `sid` int(11) unsigned DEFAULT NULL COMMENT '学生id(与班级学生操作相关）' AFTER `cid`,
MODIFY COLUMN `event_type` int(11) UNSIGNED NOT NULL COMMENT '事件类型，1：创建班级，2：编辑班级， 3：学生加入班级，4：学生退出班级，5：班级状态status更改，6：排课操作，7：考勤操作,8:升班操作，9：结课操作,10:该班级学生停课，11：该班级学生复课' AFTER `sid`
;

ALTER TABLE `x360p_class_student`
MODIFY COLUMN `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1正常,0停课,2转出  （yr:停课状态0无效，根据停课日期来动态判断）',
ADD COLUMN `stop_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '停课日期' AFTER `status`,
ADD COLUMN `stop_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '停课备注' AFTER `stop_int_day`,
ADD COLUMN `recover_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '复课日期' AFTER `stop_remark`,
ADD COLUMN `is_end` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否结课: 0:no,1:yes' AFTER `recover_int_day`
;

ALTER TABLE `x360p_customer`
MODIFY COLUMN `follow_eid` int(11) NOT NULL DEFAULT '0' COMMENT '主要跟进人（添加客户的时候选择的 主责任人，副责任人保存在customer_employee表中）' AFTER `referer_sid`
;

ALTER TABLE `x360p_student`
ADD COLUMN  `status` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '学员状态：1.正常状态，20.停课状态，30.休学状态，90.已退学' AFTER `is_lost`,
ADD COLUMN `quit_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '退学原因' AFTER `status`,
ADD COLUMN `type` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '学员类型：0.体验学员 1.正式学员 2.vip学员' AFTER `quit_reason`
;

ALTER TABLE `x360p_student_lesson`
MODIFY COLUMN `lesson_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程状态(0:未开始上课,1:上课中,2:已结课)' AFTER `ac_nums`,
MODIFY COLUMN `is_stop` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否停课(0为否，1为是)（无效字段yr20180104）' AFTER `lesson_status`,
ADD COLUMN `stop_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '停课备注，（停课操作的时候填写）' AFTER `is_stop`,
ADD COLUMN `stop_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '停课日期' AFTER `stop_remark`,
ADD COLUMN `recover_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '复课日期' AFTER `stop_int_day`
;
  
  
CREATE TABLE `x360p_advice` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT '校区id',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '学生id',
  `content` varchar(255) DEFAULT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='mobile投诉建议';

CREATE TABLE `x360p_advice_reply` (
  `ar_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '机构id',
  `bid` int(11) NOT NULL DEFAULT '0' COMMENT '校区id',
  `aid` int(11) NOT NULL DEFAULT '0' COMMENT '投诉建议id',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '回复员工',
  `content` varchar(255) DEFAULT NULL COMMENT '回复内容',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生投诉建议回复';


CREATE TABLE `x360p_customer_status_conversion` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cu_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '客户id',
  `follow_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销售责任人',
  `old_value` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '原来的客户跟进状态',
  `new_value` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新的跟进状态',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户UID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户状态转变表';

CREATE TABLE `x360p_employee_student` (
  `es_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0',
  `bid` int(11) unsigned NOT NULL DEFAULT '0',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '员工id',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学生id',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '员工与学员关系，1：一对一、一对多上课老师与学生(班课不需要记录)，2：学管师与学生',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`es_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工学员关系表';


CREATE TABLE `x360p_mobile_login_log` (
  `mll_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `sid` int(11) NOT NULL DEFAULT '0' COMMENT '家长学员',
  `ip` varchar(255) DEFAULT NULL COMMENT '登录ip',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '客户端信息',
  `login_time` int(11) DEFAULT NULL COMMENT '登录时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`mll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='家长端登录日志';



DROP TABLE IF EXISTS `x360p_report_summary`;

CREATE TABLE `x360p_report_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构id',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区id',
  `customer_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '意向客户名单数',
  `order_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '报名数(订单数)',
  `lesson_hour_consume` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消耗课时数',
  `lesson_hour_remain` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余课时数',
  `money_consume` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消耗课时金额',
  `money_remain` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余课时金额',
  `lesson_hour_reward` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教师课酬课时数',
  `money_reward` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '教师课酬金额',
  `income_total` float(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '收款合计',
  `arrearage_total` float(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '欠款合计',
  `refund_total` float(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款合计',
  `outlay_total` float(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '支出合计',
  `year` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '年份',
  `month` int(2) unsigned NOT NULL DEFAULT '0' COMMENT '月份',
  `week` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周数',
  `day` int(2) unsigned NOT NULL DEFAULT '0',
  `int_day` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '20171223',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bid_int_day` (`bid`,`int_day`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `x360p_review`;

CREATE TABLE `x360p_review` (
  `rvw_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '课评ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `rts_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点评模板配置ID',
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型:0班课，1:1对1,2:1对多，分别对应接下来的3个字段',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `sids` varchar(255) NOT NULL DEFAULT '' COMMENT '1对多学员ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `catt_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级考勤ID',
  `ca_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排课ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '日期',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课结束时间',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `content` text COMMENT '上课内容:json格式lesson_content,lesson_after_task',
  `view_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  `share_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`rvw_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课评主表';


DROP TABLE IF EXISTS `x360p_review_file`;

CREATE TABLE `x360p_review_file` (
  `rf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `rvw_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课评ID',
  `file_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件ID',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `file_type` varchar(16) NOT NULL DEFAULT 'image' COMMENT '文件类型:image,audio,video,file',
  `duration` varchar(25) NOT NULL DEFAULT '' COMMENT '当文件为mp3时该字段不为空。',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`rf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课评关联附件表';


DROP TABLE IF EXISTS `x360p_review_student`;
CREATE TABLE `x360p_review_student` (
  `rs_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `rvw_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课评ID',
  `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
  `lesson_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '课程类型:0班课，1:1对1,2:1对多，分别对应接下来的3个字段',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `lid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '日期',
  `int_start_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `int_end_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课结束时间',
  `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上课老师ID',
  `score` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分数/星星数',
  `score1` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分1',
  `score2` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分2',
  `score3` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分3',
  `score4` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分4',
  `score5` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分项5',
  `score6` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分项6',
  `score7` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分项7',
  `score8` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分项8',
  `score9` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打分项9',
  `detail` text,
  `view_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  `share_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`rs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课评学员记录表';


DROP TABLE IF EXISTS `x360p_review_tpl_define`;

CREATE TABLE `x360p_review_tpl_define` (
  `rtd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `lid` int(11) NOT NULL DEFAULT '0' COMMENT '课程ID',
  `sj_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '科目ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '班级ID',
  `rts_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课评模板配置ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`rtd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='默认课评模板定义表';


DROP TABLE IF EXISTS `x360p_review_tpl_setting`;
CREATE TABLE `x360p_review_tpl_setting` (
  `rts_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '点评模板名称',
  `setting` text NOT NULL COMMENT 'JSON结构的设置',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`rts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课评模板配置表';



DROP TABLE IF EXISTS `x360p_student_log`;
CREATE TABLE `x360p_student_log` (
  `slg_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0',
  `bid` int(11) unsigned NOT NULL DEFAULT '0',
  `sid` int(11) unsigned NOT NULL DEFAULT '0',
  `sl_id` int(11) unsigned NOT NULL DEFAULT '0',
  `op_type` int(11) unsigned NOT NULL DEFAULT '0' COMMENT ' 操作类型,详情见文档',
  `lid` int(11) unsigned NOT NULL DEFAULT '0',
  `cid` int(11) unsigned NOT NULL DEFAULT '0',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '操作描述',
  `extra_param` text COMMENT '额外的参数 json格式',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户UID',
  PRIMARY KEY (`slg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学员操作日志表';

