-- 课程添加是否适用所有校区字段
ALTER TABLE `x360p_lesson`
ADD COLUMN `is_public` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否适用所有校区' AFTER `is_demo`
;


-- 客户跟踪记录表新增字段
ALTER TABLE `x360p_customer_follow_up`
ADD COLUMN `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否系统操作' AFTER `eid`,
ADD COLUMN `system_op_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '系统操作类型：1转入公海2从公海转出' AFTER `is_system`
;
-- 查询有跟踪记录但是最后跟踪时间为0的
-- SELECT * from `x360p_customer` where follow_times > 0 and last_follow_time = 0
;
-- 更新客户最后跟踪时间 todo
UPDATE `x360p_customer` cu LEFT JOIN `x360p_customer_follow_up` cfu
ON cu.cu_id = cfu.cu_id
set cu.last_follow_time = cfu.create_time
where cu.follow_times > 0 and cu.last_follow_time = 0 and cfu.create_time IS NOT NULL;


-- 图书管理应用
-- 图书表
-- post /api/bookds
-- /api/books/12/doin
-- /api/books/1/dolending
-- /api/bookds/1/doreturn
-- get /api/books
DROP TABLE IF EXISTS `x360p_book`;
CREATE TABLE `x360p_book` (
  `bk_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '图书ID',
  `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
  `bid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '校区ID',
  `isbn` varchar(16) DEFAULT '' COMMENT 'ISBN号',
  `barcode` varchar(16) DEFAULT '' COMMENT '内部条码',
  `name` varchar(64) DEFAULT '' COMMENT '书名',
  `author` varchar(32) DEFAULT '' COMMENT '作者',
  `book_cate_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '书本分类字典ID',
  `book_pub_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出版社字典ID',
  `book_brand_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出品方字典ID',
  `pages` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '页数',
  `cover_image_url` varchar(255) DEFAULT '' COMMENT '封面图片',
  `pub_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '出班年月',
  `price` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '定价',
  `book_package_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '装帧字典ID',
  `purchase_url` varchar(255) default '' COMMENT '采购网址',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否公共',
  `suit_bids` varchar(255) DEFAULT '' COMMENT '适用校区',
  `qty` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '库存本数',
  `remark` varchar(255) DEFAULT '' COMMENT '备注介绍',
  `lending_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '借出次数',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '书籍状态1启用0禁用',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
  PRIMARY KEY (`bk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='书籍资料表';
-- 书籍库存表
DROP TABLE IF EXISTS `x360p_book_store`;
CREATE TABLE `x360p_book_store`(
    `bks_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
    `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
    `bk_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '书籍ID',
    `total_qty` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总数量',
    `qty` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
    `place_no` varchar(16) DEFAULT '' COMMENT '存放区域编号',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`bks_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='书籍库存表';

-- 书籍库存变动表
DROP TABLE IF EXISTS `x360p_book_qty_history`;
CREATE TABLE `x360p_book_qty_history`(
    `bqh_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
    `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
    `bk_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '书籍ID',
    `op_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1:增加0：减少',
    `qty` int(11) NOT NULL DEFAULT '0' COMMENT '变动数量',
    `op_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '入库日期',
    `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联学员ID',
    `remark` varchar(255) DEFAULT '' COMMENT '备注介绍',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`bqh_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='书籍库存变动表';

-- 书籍借出记录
DROP TABLE IF EXISTS `x360p_book_lending`;
CREATE TABLE `x360p_book_lending`(
    `bkl_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
    `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
    `bk_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '书籍ID',
    `qty` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '借出数量',
    `apply_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '申请借出日期',
    `apply_way` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '申请方式0:线下,1:线上',
    `lending_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '借出日期',
    `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '借出学员',
    `lending_days` int(11) unsigned NOT NULL DEFAULT '30' COMMENT '预备借出天数',
    `back_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '还书日期',
    `over_days` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '超出还书天数',
    `back_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '书籍状态0未还1已还2报损',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`bkl_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='书籍借阅表';

-- 流程定义表
DROP TABLE IF EXISTS `x360p_workflow_define`;
CREATE TABLE `x360p_workflow_define`(
    `wfd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '工作流程ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
    `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
    `workflow_type_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '流程类型ID',
    `is_system` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否系统内置流程',
    `define` text COMMENT '定义,JSON结构',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`bkl_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='流程定义表';

-- 流程表
DROP TABLE IF EXISTS `x360p_workflow`;
CREATE TABLE `x360p_workflow`(
    `wf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '工作流程ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
    `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
    `workflow_type_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '流程类型ID',
    `from_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发起员工ID', 
    `business_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '业务ID',
    `business_data` text COMMENT '业务数据,JSON结构',
    `sid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '学员ID',
    `current_step` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '当前步骤',
    `is_finish` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否结束',
    `finish_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`bkl_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='流程表';
-- 流程步骤表
DROP TABLE IF EXISTS `x360p_workflow_step`;
CREATE TABLE `x360p_workflow_step`(
    `wfs_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '工作流程ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
    `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
    `wf_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
    `step` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '步骤',
    `data` text COMMENT '数据,JSON结构',
    `apply_remark` varchar(255) default '' COMMENT '申请备注',
    `from_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发起员工ID',
    `last_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上一步员工ID',
    `to_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '到达员工ID',
    `to_dept_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '到达部门ID',
    `to_jobtitle_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '到达职位ID',
    `to_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '到达类型:0部门，1具体到人',
    `is_deal` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否处理',
    `is_pass` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '是否通过:0未通过，1:通过',
    `deal_remark` varchar(255) default '0' COMMENT '处理备注',
    `from_wfs_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '来源步骤ID',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`bkl_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='流程步骤表';
-- 流程步骤附件表
DROP TABLE IF EXISTS `x360p_workflow_step_file`;
CREATE TABLE `x360p_workflow_step_file`(
    `wfsf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '工作流程ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
    `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
    `wf_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
    `wfs_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '流程步骤ID',
    `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '文件id',
    `file_url` varchar(255) DEFAULT '' COMMENT '文件URL',
    `file_type` varchar(16) DEFAULT '' COMMENT '文件类型',
    `file_size` bigint(20) unsigned DEFAULT '0' COMMENT '文件大小',
    `file_name` varchar(64) DEFAULT '' COMMENT '文件名',
    `media_type` char(50) DEFAULT NULL COMMENT '媒体类型',
    `duration` varchar(255) DEFAULT NULL COMMENT '音频时长',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`bkl_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='流程步骤附件表';

-- 机构表新增与加盟商表之间的关联
ALTER TABLE `x360p_org`
ADD COLUMN `fc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加盟商ID' AFTER `og_id`
;

-- 加盟商
DROP TABLE IF EXISTS `x360p_franchisee`;
CREATE TABLE `x360p_franchisee`(
    `fc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '加盟商ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
    `org_name` varchar(64) default '' COMMENT '加盟商名称',
    `province_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '省ID',
    `city_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '城市ID',
    `district_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '区域ID',
    `org_address` varchar(255) NOT NULL DEFAULT '' COMMENT '机构地址',
    `status` tinyint(2) NOT NULL DEFAULT '-1' COMMENT '校区运营状态(0:未选址1:筹备期2:预售期3:正常营业4:停业5:已解约',
    `address_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '店面性质:商场,社区,写字楼,商圈,未选址',
    `decorate_fee` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '装修费用',
    `is_head_decorate` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否总部装修',
    `is_owner_change` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '主体变更 是否完成',
    `business_license` varchar(32) DEFAULT '' COMMENT '营业执照号',
    `is_authorize_dispatch` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '授权铜牌是否下发',
    `fc_og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校360系统机构ID',
    `org_email` varchar(32) DEFAULT '' COMMENT '企业邮箱',
    `sale_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '销售员工ID',
    `service_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '督导员工ID',
    `is_sign` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否签约',
    `contract_start_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '合同开始日期',
    `contract_end_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '合同结束日期',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`fc_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='加盟商';

-- 加盟商合同管理
DROP TABLE IF EXISTS `x360p_franchisee_contract`;
CREATE TABLE `x360p_franchisee_contract`(
    `fcc_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '合同ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
    `fc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加盟商ID',
    `contract_no` varchar(32) DEFAULT '' COMMENT '合同号',
    `contract_start_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加盟合同开始日期',
    `contract_end_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加盟合同结束日期',
    `region_level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '区域性质一类二类三类四类五类',
    `join_fee1` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '特许经营费',
    `join_fee2` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '履约保证金',
    `join_fee3` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '年度使用费',
    `join_fee4` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '教育商品款',
    `contract_amount` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '合同总金额',
    `all_pay_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '全款到账日期',
    `content` text COMMENT '合同中的特殊约定，文本复制',
    `sign_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '签约员工ID',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`fcc_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='加盟商合同表';
-- 加盟商合同附件表
DROP TABLE IF EXISTS `x360p_franchisee_contract_file`;
CREATE TABLE `x360p_franchisee_contract_file` (
  `fccf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `fcc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '合同ID',
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
  PRIMARY KEY (`fccf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='加盟商合同附件表';

-- 加盟商联系人
DROP TABLE IF EXISTS `x360p_franchisee_person`;
CREATE TABLE `x360p_franchisee_person`(
    `fcp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '合同联系人ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '机构ID',
    `fc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加盟商ID',
    `name` varchar(32) NOT NULL DEFAULT '' COMMENT '姓名',
    `sex` enum('2','1','0') NOT NULL DEFAULT '0' COMMENT '性别(0:未确定,1:男,2:女)',
    `mobile` varchar(16) NOT NULL COMMENT '手机号码',
    `email` varchar(64) NOT NULL DEFAULT '' COMMENT 'Email地址',
    `id_card_no` varchar(20) NOT NULL DEFAULT '' COMMENT '身份证号',
    `address` varchar(32) DEFAULT '' COMMENT '个人通讯地址',
    `is_partner` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否合伙人',
    `partner_percent` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '股份占比',
    `is_contract_sign` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否合同签约人',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`fcp_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='加盟商联系人表';

-- 加盟商服务记录
DROP TABLE IF EXISTS `x360p_franchisee_service_record`;
CREATE TABLE `x360p_franchisee_service_record`(
    `fsr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '合同ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
    `fc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加盟商ID',
    `fsa_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '申请ID',
    `fc_service_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '服务类型字典ID',
    `int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成日期',
    `int_hour` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成时间',
    `content` text COMMENT '服务内容',
    `eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '员工ID',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT 0,
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '删除用户ID',
    PRIMARY KEY (`fsr_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='加盟商服务记录';
-- 加盟商服务记录附件表
DROP TABLE IF EXISTS `x360p_franchisee_service_record_file`;
CREATE TABLE `x360p_franchisee_service_record_file` (
  `fsrf_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `fsr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '服务记录ID',
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
  PRIMARY KEY (`fsrf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='加盟商服务记录文件表';

-- 加盟商服务申请
DROP TABLE IF EXISTS `x360p_franchisee_service_apply`;
CREATE TABLE `x360p_franchisee_service_apply`(
    `fsa_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '合同ID',
    `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
    `fc_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加盟商ID',
    `fc_og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '申请加盟商机构ID',
    `apply_eid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '申请员工ID',
    `fc_service_did` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '加盟商服务字典ID(开业指导，到店督导、专家讲座)',
    `title` varchar(128) default '' COMMENT '标题',
    `remark` varchar(255) default '' COMMENT '备注描述',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态:0待服务1已接受2已完成',
    `service_fee` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '服务费用',
    `receive_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '接受时间', 
    `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
    `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
    `is_delete` tinyint(1) NOT NULL DEFAULT '0',
    `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
    `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
    PRIMARY KEY (`fsa_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='加盟商服务申请';

-- 手机登录日志
ALTER TABLE `x360p_mobile_login_log`
ADD COLUMN `og_id` int(11) NOT NULL DEFAULT '0' COMMENT 'og_id' AFTER `mll_id`,
ADD COLUMN `bid` int(11) NOT NULL DEFAULT '0' COMMENT 'bid' AFTER `og_id`
;

-- 字典项目
INSERT INTO `x360p_dictionary` VALUES
(41,0,0,'book_cate','图书分类','图书分类','1',0,1,0,0,NULL,0,0,NULL),
(42,0,0,'book_pub','图书出版社','图书出版社','1',0,1,0,0,NULL,0,0,NULL),
(43,0,0,'book_brand','图书出品方','图书出品方','1',0,1,0,0,NULL,0,0,NULL),
(44,0,0,'book_package','图书封装方式','图书封装方式','1',0,1,0,0,NULL,0,0,NULL),
(51,0,0,'address','店面性质','店面性质','1',0,1,0,0,NULL,0,0,NULL),
(52,0,0,'fc_service','加盟商服务类型','加盟商服务类型','1',0,1,0,0,NULL,0,0,NULL),
(440, 0, 44, '精装','精装','精装','1',0,1,0,0,0,0,0,NULL),
(441, 0, 44, '平装','平装','平装','1',0,1,0,0,0,0,0,NULL),
(442, 0, 44, '简装','简装','简装','1',0,1,0,0,0,0,0,NULL),
(510, 0, 51, '商场','商场','商场','1',0,1,0,0,0,0,0,NULL),
(511, 0, 51, '社区','社区','社区','1',0,1,0,0,0,0,0,NULL),
(512, 0, 51, '写字楼','写字楼','写字楼','1',0,1,0,0,0,0,0,NULL),
(513, 0, 51, '商圈','商圈','商圈','1',0,1,0,0,0,0,0,NULL),
(520, 0, 52, '开业指导','开业指导','开业指导','1',0,1,0,0,0,0,0,NULL),
(521, 0, 52, '到店督导','到店督导','到店督导','1',0,1,0,0,0,0,0,NULL),
(522, 0, 52, '专家讲座','专家讲座','专家讲座','1',0,1,0,0,0,0,0,NULL)
;

-- 新增几个加盟商相关的系统角色
INSERT INTO `x360p_role` VALUES
(101,0,'渠道销售','渠道销售','','',1,1498095552,1,1504668175,NULL,0,0,NULL,NULL),
(102,0,'督导','加盟商督导','','',1,1498095552,1,1504668175,NULL,0,0,NULL,NULL)
;
-- 设置新增的角色ID为1001
ALTER TABLE `x360p_role`
AUTO_INCREMENT = 1001;




ALTER TABLE `x360p_franchisee_service_record`
ADD COLUMN `fc_og_id`  int(11) NOT NULL DEFAULT 0 AFTER `fc_id`;

ALTER TABLE `x360p_franchisee_service_apply`
ADD COLUMN `finish_time`  int(11) NOT NULL DEFAULT 0 COMMENT '完成时间' AFTER `receive_time`;

ALTER TABLE `x360p_franchisee_service_apply`
ADD COLUMN `int_day`  int(11) NOT NULL DEFAULT 0 COMMENT '完成日期' AFTER `finish_time`,
ADD COLUMN `int_hour`  int(11) NOT NULL DEFAULT 0 COMMENT '完成时间' AFTER `int_day`;

ALTER TABLE `x360p_franchisee`
ADD COLUMN `mobile`  varchar(20) NOT NULL COMMENT '联系电话' AFTER `org_name`;


ALTER TABLE `x360p_franchisee_contract`
ADD COLUMN `open_int_day`  int(11) NOT NULL DEFAULT 0 COMMENT '开业时间' AFTER `contract_end_int_day`;

ALTER TABLE `x360p_franchisee`
ADD COLUMN `open_int_day`  int(11) NOT NULL DEFAULT 0 COMMENT '开业时间' AFTER `contract_end_int_day`;


ALTER TABLE `x360p_franchisee_service_apply`
ADD COLUMN `service_eid`  int(11) NOT NULL DEFAULT 0 COMMENT '督导ID' AFTER `apply_eid`;