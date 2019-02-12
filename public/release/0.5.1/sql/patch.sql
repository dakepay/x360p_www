ALTER TABLE `x360p_course_arrange`
ADD COLUMN `is_makeup`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开班补课的排课' AFTER `is_trial`;

ALTER TABLE `x360p_student_suspend`
CHANGE COLUMN `gid` `og_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '机构ID' AFTER `ss_id`;

ALTER TABLE `x360p_tally_help`
ADD COLUMN `og_id`  int NOT NULL DEFAULT 0 COMMENT '机构id' AFTER `th_id`;

ALTER TABLE `x360p_tally_type`
ADD COLUMN `og_id`  int NOT NULL DEFAULT 0 COMMENT '机构id' AFTER `tt_id`;

ALTER TABLE `x360p_org`
ADD COLUMN `parent_og_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级机构ID(属于哪个机构)' AFTER `og_id`;

ALTER TABLE `x360p_org`
ADD COLUMN `expire_day`  int(11) NOT NULL COMMENT '到期日期' AFTER `org_address`,
ADD COLUMN `account_num_limit`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '账号数限制，0为不限制' AFTER `expire_day`,
ADD COLUMN `student_num_limit`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '学员数量限制' AFTER `branch_num_limit`,
ADD COLUMN `is_frozen`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否冻结账户' AFTER `student_num_limit`;


CREATE TABLE `x360p_org_renew_log` (
  `orl_id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) NOT NULL DEFAULT '0' COMMENT '加盟商id',
  `pre_day` int(11) NOT NULL DEFAULT '0' COMMENT '延期前的过期时间',
  `new_day` int(11) NOT NULL DEFAULT '0' COMMENT '延期后的过期时间',
  `create_time` int(11) DEFAULT NULL,
  `create_uid` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `delete_uid` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`orl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COMMENT='延期记录';

ALTER TABLE `x360p_file`
ADD COLUMN `openid`  varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户openid' AFTER `delete_uid`,
ADD COLUMN `media_type`  char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '媒体文件类型（微信回调消息类型）' AFTER `openid`,
ADD COLUMN `media_id`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '微信的media_id' AFTER `media_type`;

ALTER TABLE `x360p_file`
MODIFY COLUMN `file_name`  varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' AFTER `file_type`;

ALTER TABLE `x360p_student`
ADD COLUMN `first_uid`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '首选联系人的账号uid' AFTER `first_family_rel`,
ADD COLUMN `first_openid`  varchar(255) NOT NULL DEFAULT '' COMMENT '首选联系人绑定的openid' AFTER `first_uid`,
ADD COLUMN `second_uid`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '第二联系人的账号uid' AFTER `second_tel`,
ADD COLUMN `second_openid`  varchar(255) NOT NULL DEFAULT '' COMMENT '第二联系绑定的openid' AFTER `;

DROP TABLE IF EXISTS `x360p_wechat_file`;