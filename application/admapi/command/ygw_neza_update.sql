-- 更新角色对应的roleid
-- 老师
update `x360p_role`
set `ext_id`=8 where rid=1;
-- 助教
update `x360p_role`
set `ext_id`=-1 where rid=2;
-- 校长
update `x360p_role`
set `ext_id`=5 where rid=3;
-- 导师
update `x360p_role`
set `ext_id`=4 where rid=4;
-- 前台
update `x360p_role`
set `ext_id`=3 where rid=5;
-- 财务
update `x360p_role`
set `ext_id`=24 where rid=6;
-- 咨询师CC
update `x360p_role`
set `ext_id`=15 where rid=7;
-- 市场专员
update `x360p_role`
set `ext_id`=-2 where rid=8;
-- 系统管理员
update `x360p_role`
set `ext_id`=26 where rid=10;

-- 用户表新增拓展字段
ALTER TABLE `x360p_user`
ADD COLUMN `is_ext` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是来自dss导入的员工，0：否，1：是' AFTER `is_admin`,
ADD COLUMN `ext_password` varchar(32) DEFAULT NULL COMMENT '员工在dss系统的登录密码' AFTER `is_ext`
;

-- 删除接口配置
delete from `x360p_config` where `cfg_name`='org_api';
-- 第二步：dss sync role ,dss sync branchs ,dss sync student


