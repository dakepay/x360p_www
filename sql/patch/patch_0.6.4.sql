ALTER TABLE `x360p_org`
ADD COLUMN `host` char(20) NOT NULL default '' COMMENT '三级域名' AFTER `org_name`
;


DROP TABLE IF EXISTS `x360p_wxmp_fans_message`;
CREATE TABLE `x360p_wxmp_fans_message` (
  `wfm_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `wxmp_id` int(11) NOT NULL DEFAULT '0',
  `appid` varchar(255) NOT NULL DEFAULT '',
  `request_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '公众号粉丝的uid（如果绑定了账号的情况有值）',
  `fans_id` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(255) NOT NULL DEFAULT '',
  `response_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回复粉丝信息的员工uid',
  `msg_type` char(30) NOT NULL DEFAULT '' COMMENT '消息类型',
  `event` varchar(30) DEFAULT '' COMMENT '当msg_type=event时的事件类型',
  `data_json` text NOT NULL COMMENT '微信的完整请求数据，json结构',
  `files_info` varchar(500) NOT NULL DEFAULT '' COMMENT '用户上传的媒体文件处理之后的文件信息,json结构',
  `msg_id` varchar(64) NOT NULL DEFAULT '' COMMENT '微信的消息id，64位整型',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '消息创建时间 （整型）',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '最后更新时间',
  `is_delete` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`wfm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公众号与粉丝的聊天记录';

ALTER TABLE `x360p_wxmp_fans_tag`
ADD COLUMN `appid`  varchar(255) NOT NULL DEFAULT '' AFTER `tag_id`;