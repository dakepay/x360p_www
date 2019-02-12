-- 维护更新教师产出与学员课耗相等
update x360p_employee_lesson_hour as elh,
(select SUM(lesson_amount) as la , catt_id from x360p_student_lesson_hour slh where catt_id <> 0 group by catt_id ) c
set elh.total_lesson_amount = c.la
where elh.catt_id = c.catt_id AND elh.total_lesson_amount <> c.la

-- 查询所有教师产出与实际学员课耗金额不相符的记录
select elh.elh_id,elh.total_lesson_amount,c.la as real_total_lesson_amount from `x360p_employee_lesson_hour` as elh left join
(select SUM(lesson_amount) as la , catt_id from x360p_student_lesson_hour slh group by catt_id) c
on elh.catt_id = c.catt_id
where elh.total_lesson_amount <> c.la and elh.catt_id <> 0

-- 更新一持共享student_lesson 分班bug

select * from `x360p_student_lesson` as sl join `x360p_class_student` as cs on sl.sl_id=cs.sl_id
where sl.lid = 0 and sl.cid=0

update x360p_student_lesson sl join x360p_class_student cs on sl.sl_id = cs.sl_id
set sl.cid = cs.cid where sl.lid =0 and sl.cid =0

-- 更新排课与班级不符合的排课
update x360p_course_arrange ca join x360p_class c on ca.cid=c.cid
set ca.sj_id = c.sj_id where ca.sj_id <> c.sj_id

select * from x360p_student_lesson sl left join x360p_class c on sl.cid=c.cid
where sl.sj_ids <> c.sj_id

update x360p_student_lesson sl left join x360p_class c on sl.cid=c.cid
set sl.sj_ids=c.sj_id where sl.sj_ids<>c.sj_id

-- 更新 学员课耗的satt_id 为0 的记录 2018年7月30日 的记录可能会受到影响，导致撤销考勤时课耗记录没有删除
update `x360p_student_lesson_hour` slh
left join `x360p_student_attendance` satt
on slh.int_day = satt.int_day
and slh.int_start_hour = satt.int_start_hour
and slh.int_end_hour = satt.int_end_hour
set slh.satt_id = satt.satt_id
where slh.satt_id = 0 and slh.change_type = 1

-- 20180820 处理斯玛特导入课时错误问题
-- 涉及校区 bid 10,11

ALTER TABLE `x360p_student_lesson_import_log`
ADD COLUMN `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID' AFTER `slil_id`,
ADD COLUMN `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID' AFTER `og_id`
;

UPDATE `x360p_student_lesson_import_log` slil left join `x360p_student` s ON slil.sid = s.sid
set slil.og_id = s.og_id,slil.bid = s.bid
;

update `x360p_student` set
`student_lesson_hours` = 0.00,
`student_lesson_remain_hours` = 0.00,
`student_lesson_times` = 0,
`student_lesson_remain_times` = 0
where bid in (10,11);

delete from `x360p_student_lesson`
where bid in (10,11);

delete from `x360p_student_lesson_import_log`
where bid in (10,11);

ALTER TABLE `x360p_student_lesson_import_log`
DROP COLUMN `og_id`,
DROP COLUMN `bid`
;

-- 删除世界之花校区的所有订单及缴费记录

delete from `x360p_order` where bid = 11;

delete from `x360p_order_item` where bid = 11;

delete from `x360p_order_receipt_bill` where bid=11;
delete from `x360p_order_receipt_bill_item` where bid =11;
delete from `x360p_order_payment_history` where bid=11;
delete from `x360p_tally` where bid = 11;

-- 更新所有的新学员用户密码为手机号后六位
update x360p_user set password=MD5(CONCAT(MD5(RIGHT(account,6)),salt)) where user_type=2 and CHAR_LENGTH(account)=11 and create_time=update_time;

select * from `x360p_user` u
left join `x360p_user_student` us on u.uid = us.uid
left join `x360p_student` s on us.sid = s.sid
where u.user_type = 2 and s.bid = 3;
-- 更新指定校区的学员用户密码为手机号后六位
update `x360p_user` u
left join `x360p_user_student` us on u.uid = us.uid
left join `x360p_student` s on us.sid = s.sid
set u.password=MD5(CONCAT(MD5(RIGHT(u.account,6)),u.salt))
where u.user_type = 2 and CHAR_LENGTH(account)=11 and s.bid = 3;


-- 更新长沙大师兄的学员课耗科目ID
update `x360p_student_lesson_hour` slh left join x360p_lesson lesson on slh.lid = lesson.lid
set slh.sj_id = lesson.sj_id where slh.sj_id < 43;

-- 更新长沙大师兄的教室课耗科目ID
update `x360p_employee_lesson_hour` elh left join x360p_lesson lesson on elh.lid = lesson.lid
set elh.sj_id = lesson.sj_id where elh.sj_id < 43;

update `x360p_student_lesson_hour` slh left join x360p_lesson lesson on slh.lid = lesson.lid
set slh.grade = lesson.fit_grade_start where slh.grade = 0 or slh.grade <> lesson.fit_grade_start;

update `x360p_employee_lesson_hour` elh left join x360p_lesson lesson on elh.lid = lesson.lid
set elh.grade = lesson.fit_grade_start where elh.grade = 0 or elh.grade <> lesson.fit_grade_start;

-- 更新排课表科目ID
update `x360p_course_arrange` ca left join `x360p_lesson` lesson on ca.lid = lesson.lid
set ca.sj_id =  CONVERT(lesson.sj_id,SIGNED) where ca.sj_id < 43 and ca.lid > 0 ;


update `x360p_course_arrange_student` cas left join `x360p_lesson` lesson on cas.lid = lesson.lid
set cas.sj_id = lesson.sj_id where cas.sj_id < 43 and cas.lid > 0;

update `x360p_course_arrange` ca left join `x360p_lesson` lesson on ca.lid = lesson.lid
set ca.grade = lesson.fit_grade_start where (ca.grade = 0 or ca.grade <> lesson.fit_grade_start) and ca.lid > 0;

update `x360p_course_arrange_student` cas left join `x360p_lesson` lesson on cas.lid = lesson.lid
set cas.grade = lesson.fit_grade_start where (cas.grade = 0 or cas.grade <> lesson.fit_grade_start) and cas.lid > 0;

-- 阳光喔数据初始化代码
delete from `x360p_student_money_history` where `og_id` = 0;
delete from `x360p_order_refund_history` where `og_id` = 0;
delete from `x360p_order_refund_item` where `og_id` = 0;
delete from `x360p_order_refund` where `og_id` = 0;
delete from `x360p_order_receipt_bill_item` where `og_id` = 0;
delete from `x360p_order_receipt_bill` where `og_id` = 0;
delete from `x360p_student_lesson` where `og_id` = 0;
delete from `x360p_order` where `og_id` = 0;
delete from `x360p_order_item` where `og_id` = 0;
delete from `x360p_order_transfer` where `og_id` = 0;
delete from `x360p_order_transfer_item` where `og_id` = 0;
delete from `x360p_order_payment_history` where `og_id` = 0;
delete from `x360p_order_payment_online` where `og_id` = 0;
delete from `x360p_tally` where `og_id` = 0;

update `x360p_accounting_account` set `amount` = 0 where `amount` > 0;
update `x360p_student` set `money` = 0 where `money` > 0;
update `x360p_student` set `student_lesson_hours` = 0,`student_lesson_remain_hours` = 0;


-- 恢复管理员密码为123456

update `x360p_user` set `salt`='Qn2eiS',`password`='59f02ce1ca2e1823341f814b6179255b' where `account`='admin';

-- 更新市场名单与客户名单之间的关联

select mcl.*,cu.* from x360p_market_clue mcl left join x360p_customer cu on mcl.og_id = cu.og_id and mcl.tel = cu.first_tel
where mcl.cu_id = 0 and cu.cu_id is NOT NULL;

update x360p_market_clue mcl left join x360p_customer cu on mcl.og_id=cu.og_id and mcl.tel=cu.first_tel
set mcl.cu_id = cu.cu_id
where mcl.og_id = 1 and mcl.cu_id = 0 and cu.cu_id is NOT NULL
;


select cu.*,mcl.mcl_id from x360p_customer cu left join x360p_market_clue mcl on cu.og_id = mcl.og_id and cu.cu_id = mcl.cu_id
where cu.mcl_id = 0 and mcl.mcl_id is NOT NULL
;

select cu.*,mcl.mcl_id from x360p_customer cu left join x360p_market_clue mcl on cu.og_id = mcl.og_id and cu.first_tel = mcl.tel
where cu.mcl_id = 0 and mcl.mcl_id is NOT NULL
;


update x360p_customer cu left join x360p_market_clue mcl on cu.og_id = mcl.og_id and cu.cu_id = mcl.cu_id
set cu.mcl_id = mcl.mcl_id,cu.mc_id = mcl.mc_id
where cu.mcl_id = 0 and mcl.mcl_id is NOT NULL
;

select mcl.* from x360p_market_clue mcl left join x360p_customer cu on mcl.tel=cu.first_tel
where cu.og_id=1 and mcl.og_id =2
;

select * from x360p_customer where cu.mc_id > 0 and cu.mcl_id = 0;

select cu.name,cu.cu_id,cu.mc_id,mc.channel_name from x360p_customer cu left join x360p_market_channel mc on cu.mc_id=mc.mc_id
where mc.channel_name IS NULL;

-- 20180919
-- 删除为0的流水记录
delete from x360p_tally where amount = 0;

select * from x360p_tally where amount > 0 and remark like '报废收据%';

-- select tly.*,oph.* from x360p_tally tly left join x360p_order_payment_history oph on tly.relate_id = oph.oph_id and tly.amount = oph.amount
-- where tly.type = 1 and oph.is_delete = 1



-- 修补市场名单已经分配给销售，但是销售看不到的bug
update `x360p_customer` cu left join `x360p_market_clue` mcl on cu.mcl_id = mcl.mcl_id
set cu.follow_eid = mcl.cu_assigned_eid
where mcl.cu_assigned_eid is not null and cu.follow_eid = 0 and mcl.cu_assigned_eid > 0
;

select cu.*,mcl.* from `x360p_customer` cu left join `x360p_market_clue` mcl on cu.mcl_id = mcl.mcl_id
where mcl.cu_assigned_eid is not null and cu.follow_eid = 0 and mcl.cu_assigned_eid > 0
;

-- 更新修复出班时间
update `x360p_class_student` set out_time = 0
where status = 1 and is_end = 0
;

-- 更新客户的试听次数
update x360p_customer c
 set c.trial_listen_times = (
  select count(*) from x360p_course_arrange_student cas
  where cas.cu_id = c.cu_id
  and is_trial = 1
  and is_in = 1
  and is_attendance = 1
  and is_delete = 0
 )
where is_delete = 0 and trial_listen_times > 0;

-- 更新customer表is_reg字段
update x360p_customer
set is_reg = 1
where is_reg = 0
and sid > 0
and is_delete = 0;

-- 添加索引
ALTER TABLE `x360p_ttt`
DROP INDEX `idx_l`,
ADD UNIQUE INDEX `idx_l`(`lid`) USING BTREE COMMENT '必须唯一';

ALTER TABLE `x360p_student_lesson_hour`
ADD UNIQUE INDEX `idx_sh`(`sid`,`ca_id`,`create_time`) USING BTREE COMMENT '同一时间必须唯一';

ALTER TABLE `x360p_student_attendance`
ADD UNIQUE INDEX `idx_sh`(`sid`,`int_day`,`int_start_hour`,`int_end_hour`,`create_time`) USING BTREE COMMENT '同一时间必须唯一';

-- 查找重复记录
select * from `x360p_employee_lesson_hour` elh
where
elh.sid = 0 AND
(elh.eid,elh.ca_id)
in (select eid,ca_id
from `x360p_employee_lesson_hour`
group by eid,ca_id having count(*) > 1
)
order by eid ASC
;

select * from `x360p_class_attendance` catt
where
ca_id > 0 AND
(catt.ca_id)
in(select ca_id
from `x360p_class_attendance`
group by ca_id having count(*) > 1
);

select * from `x360p_student_attendance` satt
where (satt.sid,satt.ca_id)
in (select sid,ca_id
from `x360p_student_attendance`
group by sid,ca_id having count(*) > 1
)
and ca_id > 0
order by sid ASC
;
-- 查询重复的学员课耗
select * from `x360p_student_lesson_hour` slh
where (slh.sid,slh.ca_id,is_delete)
in (select sid,ca_id,is_delete
from `x360p_student_lesson_hour`
where is_delete = 0 and ca_id > 0
group by sid,ca_id,is_delete having count(*) > 1
) and slh.ca_id > 0
order by sid ASC;


-- 查询重复的教师课耗
select * from `x360p_employee_lesson_hour` elh
where (elh.catt_id,elh.total_lesson_amount,total_lesson_hours)
in (
 select catt_id,total_lesson_amount,total_lesson_hours
 from `x360p_employee_lesson_hour`
 where is_delete = 0 and catt_id > 0
 group by catt_id
 having count(catt_id) > 1
) and elh.catt_id > 0
order by catt_id ASC;
-- 删除重复记录

-- 删除重复的学员课耗记录
delete from `x360p_student_lesson_hour`
where
ca_id > 0 and
slh_id NOT IN(
 select slh_id from (
  select min(slh_id) as slh_id,
  count(sid) as count
  from `x360p_student_lesson_hour`
  where ca_id > 0
  GROUP BY sid,ca_id,is_delete
  HAVING
  COUNT(sid) >= 1
 ) tmp
);
-- 删除重复的学员考勤

delete from `x360p_student_attendance`
where
ca_id > 0 and
satt_id NOT IN(
  select satt_id from (
    select min(satt_id) as satt_id,
    count(sid) as count
    from `x360p_student_attendance`
    where ca_id > 0
    GROUP BY sid,ca_id
    HAVING
    COUNT(sid) >=1
  ) tmp
);
-- 删除重复的班级考勤
delete from `x360p_class_attendance`
where
ca_id > 0 and
catt_id NOT IN(
  select catt_id from (
    select min(catt_id) as catt_id,
    count(ca_id) as count
    from `x360p_class_attendance`
    where ca_id > 0
    GROUP BY ca_id
    HAVING
    count(ca_id) >=1
  ) tmp
);
-- 删除重复的教师考勤
delete from `x360p_employee_lesson_hour`
where ca_id > 0 and
eid > 0 and
sid = 0 and
elh_id NOT IN(
  select elh_id from (
    select min(elh_id) as elh_id,
    count(eid) as count
    from `x360p_employee_lesson_hour`
    where ca_id > 0
    and eid > 0
    and sid = 0
    GROUP BY eid,ca_id
    HAVING
    count(eid) >=1
  ) tmp
);


-- 查询使用课时与消耗课时累加不相等的记录
select sl.sl_id,s.bid,b.branch_name,s.student_name,sl.sid,sl.lesson_hours,sl.use_lesson_hours,sl.remain_lesson_hours,c.use_lesson_hours as real_use_lesson_hours from `x360p_student_lesson` sl
left join (
  select sl_id,sum(lesson_hours) as use_lesson_hours from `x360p_student_lesson_hour`  where is_delete = 0 group by sl_id
) c
ON sl.sl_id = c.sl_id
left join `x360p_student` s
ON sl.sid = s.sid
left join `x360p_branch` b
on s.bid = b.bid
where sl.use_lesson_hours <> c.use_lesson_hours
order by sid ASC;

-- 更新正确的课时记录
update `x360p_student_lesson` sl
left join (
  select sl_id,sum(lesson_hours) as use_lesson_hours
  from `x360p_student_lesson_hour`
  where is_delete = 0 group by sl_id
) c
ON sl.sl_id = c.sl_id
set
sl.use_lesson_hours = c.use_lesson_hours,
sl.remain_lesson_hours = sl.lesson_hours - sl.transfer_lesson_hours - sl.refund_lesson_hours - c.use_lesson_hours
where sl.use_lesson_hours <> c.use_lesson_hours
;

-- 查询学员总剩余课时与实际累计课时不相等的记录
select s.sid,s.student_lesson_hours,s.student_lesson_remain_hours,c.lesson_hours,c.remain_lesson_hours,c.transfer_lesson_hours,c.refund_lesson_hours
from `x360p_student` s
left join (
  select sid,
        sum(lesson_hours) as lesson_hours,
        sum(transfer_lesson_hours) as transfer_lesson_hours,
        sum(refund_lesson_hours) as refund_lesson_hours,
        sum(remain_lesson_hours) as remain_lesson_hours
  from `x360p_student_lesson`
  where is_delete = 0 and lesson_status < 2
  group by sid
) c
ON s.sid = c.sid
where s.student_lesson_remain_hours <> c.remain_lesson_hours
order by sid ASC;

-- 更新正确的学员年剩余课时
update `x360p_student` s
LEFT JOIN (
  select sid,
        sum(lesson_hours) as lesson_hours,
        sum(transfer_lesson_hours) as transfer_lesson_hours,
        sum(refund_lesson_hours) as refund_lesson_hours,
        sum(remain_lesson_hours) as remain_lesson_hours
  from `x360p_student_lesson`
  where is_delete = 0 and lesson_status < 2
  group by sid
) c
ON s.sid = c.sid
set
s.student_lesson_remain_hours = c.remain_lesson_hours,
s.student_lesson_hours = c.lesson_hours - c.transfer_lesson_hours - c.refund_lesson_hours
where s.student_lesson_remain_hours <> c.remain_lesson_hours
;

-- 查询有剩余课时但是已经结课的记录
SELECT * from `x360p_student_lesson`
WHERE lesson_status = 2 AND remain_lesson_hours > 0 and is_delete = 0;


UPDATE `x360p_student_lesson`
SET `lesson_status` = 1
WHERE `lesson_status` = 2 AND remain_lesson_hours =2 and is_delete = 0;




select count(*) from x360p_customer where is_public = 1;
select count(*) from x360p_customer_follow_up where is_system=1 and system_op_type = 2;
-- 从客户公海恢复过来
update `x360p_customer`
set `is_public` = 0,`in_public_time` = 0
where `is_public` = 1 and in_public_time > 0
;
-- 删除转入公海的记录
delete from `x360p_customer_follow_up` where is_system=1 and system_op_type = 1;

-- 阳光喔的服务器转移到本地
update `pro_database_config`
set `hostname` = 'localhost',`username`='neza_root',`password`='gb$1PPPZdn'
where `host` = 'neza';

-- 阳光喔的服务器迁移到阿里云
update `pro_database_config`
set `hostname` = 'rm-2ze15z707w4t6d0b8ko.mysql.rds.aliyuncs.com',`username`='ygwo',`password`='YgwoNEza#201806'
where `host` = 'neza';


-- 查询导入超过2次的学员记录
select c.*,s.student_name,b.branch_name from (
select og_id,bid,sid,count(*) as import_nums from x360p_student_lesson_import_log
where og_id = 0
group by sid,og_id,bid having count(*) > 1
) c left join `x360p_student` s
on c.sid = s.sid
left join `x360p_branch` b
on c.bid = b.bid
;

-- 删除学员ID为0的 学员余额变动记录
delete from `x360p_student_money_history`
where `sid` = 0;

-- 查询教师课耗金额与学员课耗金额不相等的记录
SELECT elh.*,c.lesson_amount as real_amount from `x360p_employee_lesson_hour` elh
LEFT JOIN (
  select catt_id,sum(lesson_amount) as lesson_amount
  from `x360p_student_lesson_hour`
  where is_delete = 0 and catt_id > 0 and lesson_amount > 0
  group by catt_id

) c
ON elh.catt_id = c.catt_id
WHERE elh.total_lesson_amount <> c.lesson_amount;
-- 更新教师课耗金额与学员课耗金额不相等的记录
UPDATE `x360p_employee_lesson_hour` elh
LEFT JOIN (
  select catt_id,sum(lesson_amount) as lesson_amount
  from `x360p_student_lesson_hour`
  where is_delete = 0 and catt_id > 0 and lesson_amount > 0
  group by catt_id
) c
ON elh.catt_id = c.catt_id
LEFT JOIN (
  select catt_id,sum(lesson_amount) as lesson_amount
  from `x360p_student_lesson_hour`
  where is_delete = 0 and catt_id > 0 and lesson_amount > 0 and is_pay=1 and sl_id > 0
  group by catt_id
) d
ON elh.catt_id = d.catt_id
set elh.total_lesson_amount = c.lesson_amount,
    elh.payed_lesson_amount = d.lesson_amount
WHERE c.catt_id IS NOT NULL and elh.total_lesson_amount <> c.lesson_amount;

-- 查询导入记录单价与总课时数不对的记录
SELECT slh.sid,s.student_name,slh.og_id,slh.sl_id,slh.lesson_hours,slh.lesson_amount,(slh.lesson_amount / slh.lesson_hours) as unit_price,slil.unit_lesson_hour_amount from `x360p_student_lesson_hour` slh
LEFT JOIN `x360p_student_lesson_import_log` slil
ON slh.sl_id = slil.sl_id
LEFT JOIN `x360p_student` s
ON slh.sid = s.sid
where slh.og_id = 0 AND slil.sl_id IS NOT NULL AND (slh.lesson_amount / slh.lesson_hours) <> slil.unit_lesson_hour_amount;

-- 更新物品管理数据库库存数
update `x360p_material` mt
LEFT JOIN (
  select mt_id,sum(num) as num from `x360p_material_history`
  where type = 1 and is_delete = 0
  group by mt_id
) c
ON mt.mt_id = c.mt_id
LEFT JOIN (
  select mt_id,sum(num) as num from `x360p_material_history`
  where type = 2 and is_delete = 0
  group by mt_id
) d
ON mt.mt_id = d.mt_id
set mt.num = c.num-d.num
where mt.num <> c.num-d.num;

update `x360p_material_store_qty` msq
LEFT JOIN `x360p_material` mt
ON msq.mt_id = mt.mt_id
set msq.num = mt.num
where msq.num <> mt.num;

update `x360p_material` mt
LEFT JOIN(
  select parent_id as mt_id,sum(num) as num
  from `x360p_material`
  where parent_id > 0 and is_delete = 0
  group by parent_id
) c
ON mt.mt_id = c.mt_id
set mt.num = c.num
where mt.is_cate = 1 AND mt.num <> c.num

-- 查询客户跟进次数与实际跟进次数不相等的记录
select cu.cu_id,cu.follow_times,c.real_follow_times
from `x360p_customer` cu
left join (
  select cu_id,count(*) as real_follow_times from `x360p_customer_follow_up`
  where is_delete = 0 and is_system = 0
  group by cu_id
) c
on cu.cu_id = c.cu_id
where cu.is_delete = 0 and c.cu_id IS NOT NULL AND cu.follow_times <> c.real_follow_times;

-- 更新客户的实际跟进次数
update `x360p_customer` cu
LEFT JOIN (
  select cu_id,count(*) as real_follow_times from `x360p_customer_follow_up`
  where is_delete = 0 and is_system = 0
  group by cu_id
) c
on cu.cu_id = c.cu_id
set cu.follow_times = c.real_follow_times
where cu.is_delete = 0 and c.cu_id IS NOT NULL AND cu.follow_times <> c.real_follow_times;

update `x360p_customer` cu
LEFT JOIN (
  select cu_id,count(*) as real_follow_times from `x360p_customer_follow_up`
  where is_delete = 0 and is_system = 0
  group by cu_id
) c
on cu.cu_id = c.cu_id
set cu.follow_times = 0
where cu.is_delete = 0 and c.cu_id IS NULL AND cu.follow_times <> 0;


select count(*) from `x360p_custoemr_follow_up`
where is_system = 1;
delete from `x360p_customer_follow_up`
where is_system = 1;

-- 查询某个日期创建的客户名单

select * from `x360p_customer` where og_id = 1 and follow_times = 0 and create_time between unix_timestamp('2018-10-27 00:00:01') and unix_timestamp('2018-10-27 23:59:59');


delete from `x360p_customer` where og_id = 1 and follow_times = 0 and create_time between unix_timestamp('2018-10-27 00:00:01') and unix_timestamp('2018-10-27 23:59:59');



-- 更改老师，老师重名处理
update `x360p_class` set teach_eid = 10139
where bid= 14 and teach_eid = 10138;
update `x360p_course_arrange` set teach_eid = 10139
where bid = 14 and teach_eid = 10138;

update `x360p_class_attendance` set eid = 10139
where bid = 14 and eid = 10138;

update `x360p_student_lesson_hour` set eid = 10139
where bid= 14 and eid=10138;

update `x360p_employee_lesson_hour` set eid = 10139
where bid = 14 and eid =10138;

-- 更改督导负责

update `x360p_franchisee` fc
set fc.service_eid = (select eid from `x360p_employee` where ename='牟雪梅')
where fc.org_name LIKE '内蒙%' or fc.org_name LIKE '福建%' or fc.org_name LIKE '新疆%' or fc.org_name LIKE '宁夏%'
;

update `x360p_franchisee` fc
set fc.service_eid = (select eid from `x360p_employee` where ename='邓惠云')
where fc.org_name LIKE '陕西%' or fc.org_name LIKE '湖北%'
;
update `x360p_franchisee_contract` fcc
left join `x360p_franchisee` fc
ON fcc.fc_id  = fc.fc_id
set fcc.service_eid = fc.service_eid
where fcc.service_eid <> fc.service_eid
;

update `x360p_franchisee_service_record` fsr
left join `x360p_franchisee` fc
on fsr.fc_id = fc.fc_id
set fsr.eid = fc.service_eid
where fsr.eid = 0;

update `x360p_org` org
left join `x360p_franchisee` fc
on org.fc_id = fc.fc_id
set org.charge_eid = fc.service_eid
where (org.charge_eid = 0 or org.charge_eid <> fc.service_eid) and fc.fc_id IS NOT NULL;


-- 查出所有有学员课耗记录而没有教师课耗记录的数据 ygwo_NEZA
select catt.*,1 as change_type,slh.student_nums,slh.slh_amount,elh.total_lesson_amount,slh.slh_lesson_hours,elh.total_lesson_hours from x360p_class_attendance catt
left join (
  select catt_id,count(*) as student_nums,sum(lesson_amount) as slh_amount,sum(lesson_hours) as slh_lesson_hours
  from x360p_student_lesson_hour
  where is_delete = 0
  group by catt_id
) slh
ON catt.catt_id = slh.catt_id
left join x360p_employee_lesson_hour elh
ON catt.catt_id = elh.catt_id
WHERE catt.is_delete = 0 and catt.is_trial = 0 and catt.in_nums > 0 and elh.catt_id IS NULL
order by catt_id ASC;
-- 写入教师课耗记录补充
INSERT INTO x360p_employee_lesson_hour(
  og_id,
  bid,
  eid,
  second_eid,
  second_eids,
  edu_eid,
  lesson_type,
  change_type,
  lid,
  sj_id,
  grade,
  sg_id,
  cid,
  ca_id,
  catt_id,
  int_day,
  int_start_hour,
  int_end_hour,
  student_nums,
  lesson_hours,
  lesson_minutes,
  total_lesson_hours,
  total_lesson_amount,
  payed_lesson_amount,
  is_demo,
  create_time,
  create_uid,
  update_time
) SELECT * FROM (
SELECT
catt.og_id,
catt.bid,
catt.eid,
catt.second_eid,
catt.second_eids,
slh.edu_eid,
catt.lesson_type,
1 as change_type,
catt.lid,
catt.sj_id,
catt.grade,
catt.sg_id,
catt.cid,
catt.ca_id,
catt.catt_id,
catt.int_day,
catt.int_start_hour,
catt.int_end_hour,
slh.student_nums,
catt.consume_lesson_hour as lesson_hours,
catt.consume_lesson_hour * 60 as lesson_minutes,
slh.slh_lesson_hours as total_lesson_hours,
slh.slh_amount as total_lesson_amount,
slh.slh_amount as payed_lesson_amount,
catt.is_demo,
catt.create_time,
catt.create_uid,
catt.update_time
from `x360p_class_attendance` catt
left join (
  select catt_id,max(edu_eid) as edu_eid,count(*) as student_nums,sum(lesson_amount) as slh_amount,sum(lesson_hours) as slh_lesson_hours
  from `x360p_student_lesson_hour`
  where is_delete = 0
  group by catt_id
) slh
ON catt.catt_id = slh.catt_id
left join `x360p_employee_lesson_hour` elh
ON catt.catt_id = elh.catt_id
WHERE catt.is_delete = 0 and catt.is_trial = 0 and catt.in_nums > 0 and elh.catt_id IS NULL
order by catt_id ASC
) tmp;


-- 爷爷的书房，删除学员后用户的默认用户ID没有删除
select *,s1.sid,s2.sid from  `x360p_user` u
left join (
  select * from `x360p_student` where is_delete = 1
) s1
on u.default_sid = s1.sid
left join (
  select * from `x360p_student` where is_delete = 0
) s2
on u.account = s2.first_tel
where u.user_type = 2 and s1.sid IS NOT NULL AND s2.sid IS NOT NULL
;

update `x360p_user` u
left join (
  select * from `x360p_student` where is_delete = 1
) s1
on u.default_sid = s1.sid
left join (
  select * from `x360p_student` where is_delete = 0
) s2
on u.account = s2.first_tel
set u.default_sid = s2.sid
where u.user_type = 2 and s1.sid IS NOT NULL AND s2.sid IS NOT NULL;


select * from `x360p_user_student` us
left join `x360p_student` s1
on us.sid = s1.sid and s1.is_delete = 1
where us.is_delete = 0;

update `x360p_user_student` us
left join `x360p_student` s1
on us.sid = s1.sid and s1.is_delete = 1
set us.is_delete = 1,us.delete_time = 99999999
where us.is_delete = 0;

-- 查询员工业绩记录业绩时间与收据日期不相等的
SELECT * from `x360p_employee_receipt` erc
LEFT JOIN `x360p_order_receipt_bill` orb
ON erc.orb_id = orb.orb_id
WHERE erc.orb_id > 0 AND erc.oid =0 AND erc.receipt_time <> orb.paid_time;

UPDATE `x360p_employee_receipt` erc
LEFT JOIN `x360p_order_receipt_bill` orb
ON erc.orb_id = orb.orb_id
SET erc.receipt_time = orb.paid_time
WHERE erc.orb_id > 0 AND erc.oid =0 AND erc.receipt_time <> orb.paid_time;

-- 解决缺课记录与请假记录补课状态不同步问题
select * from x360p_student_absence sa
left join x360p_student_leave slv
on sa.sid = slv.sid and sa.int_day = slv.int_day and sa.int_start_hour = slv.int_start_hour and sa.int_end_hour and slv.int_end_hour
left join x360p_makeup_arrange ma
on slv.ma_id = ma.ma_id
where ma.ma_id IS NOT NULL AND sa.status = 0;

update `x360p_student_absence` sa
left join `x360p_student_leave` slv
on sa.sid = slv.sid and sa.int_day = slv.int_day and sa.int_start_hour = slv.int_start_hour and sa.int_end_hour and slv.int_end_hour
left join x360p_makeup_arrange ma
on slv.ma_id = ma.ma_id
set sa.status = 2
where ma.ma_id IS NOT NULL and sa.status = 0 and slv.satt_id > 0;

update `x360p_student_absence` sa
left join `x360p_student_leave` slv
on sa.sid = slv.sid and sa.int_day = slv.int_day and sa.int_start_hour = slv.int_start_hour and sa.int_end_hour and slv.int_end_hour
left join x360p_makeup_arrange ma
on slv.ma_id = ma.ma_id
set sa.status = 1
where ma.ma_id IS NOT NULL and sa.status = 0 and slv.satt_id = 0;

select * from x360p_employee
where com_ids = '' and locate(',',bids) > 0



-- 批量更新分公司ID


select count(*) from `x360p_market_clue` mc
where mc_id IN (
  select mc_id from `x360p_market_channel`
  where channel_name LIKE '七宝%' and bid = 2
) and bid = 2;

update `x360p_market_clue` mc
set mc.bid = 3
where mc.mc_id IN (
  select mc_id from `x360p_market_channel`
  where channel_name LIKE '七宝%' and bid = 2
) and mc.bid = 2;

-- 更新渠道的统计
select mc.mc_id,mc.total_num,c.total as real_total_num from `x360p_market_channel` mc
left join (
  select mc_id,count(*) as total from `x360p_market_clue` where is_delete = 0
  group by mc_id
) c
on mc.mc_id = c.mc_id
where c.total <> mc.total_num

update `x360p_market_channel` mc
left join (
  select mc_id,count(*) as total from `x360p_market_clue` where is_delete = 0
  group by mc_id
) c
on mc.mc_id = c.mc_id
set mc.total_num = c.total
where c.total <> mc.total_num

-- 斯玛特加盟商的主账号丢失修复
-- Qn2eiS',`password`='59f02ce1ca2e1823341f814b6179255b'
INSERT INTO `x360p_user`
( `og_id`, `account`, `mobile`, `email`, `name`, `sex`, `user_type`, `salt`, `password`, `avatar`, `openid`, `default_sid`, `is_mobile_bind`, `is_email_bind`, `is_weixin_bind`, `last_login_time`, `last_login_ip`, `login_times`, `create_time`, `status`, `is_main`, `is_admin`, `is_ext`, `ext_password`, `update_time`, `create_uid`)
VALUES
(144, '17615145146', '17615145146', '', '辽宁朝阳双塔天盛嘉苑中心', '1', 1, 'Qn2eiS', '59f02ce1ca2e1823341f814b6179255b', 'http://s1.xiao360.com/common_img/avatar.jpg', '', 0, 0, 0, 0, 0, '', 0, 1540995576, 1, 1, 1, 0, NULL, 1540995576, 10001);



-- 20181127 清除所有学员记录，适用于还没有报名的情况下，上海巧易思，咨询师需要了解学员的课时消耗情况
DELETE FROM `x360p_student` WHERE 1=1;
DELETE FROM `x360p_user_student` WHERE 1=1;
DELETE FROM `x360p_user` WHERE `user_type` = 2;
DELETE FROM `x360p_employee_student` WHERE 1=1;
DELETE FROM `x360p_student_log` WHERE 1=1;

-- 20181127 斯玛特加盟商账号的is_frozen字段不准确修复
SELECT * from `x360p_org` org
LEFT JOIN `x360p_user` u
ON org.og_id = u.og_id AND u.is_admin = 1
WHERE org.is_frozen = 1 AND u.status = 1;

UPDATE `x360p_org` org
LEFT JOIN `x360p_user` u
ON org.og_id = u.og_id AND u.is_admin =1
SET org.is_frozen = 0
WHERE org.is_frozen = 1 AND u.status = 1;

-- 20181128 市场名单获取时间为0更改为录入时间
update `x360p_market_clue`
SET `get_time` = `create_time`
WHERE `get_time` = 0;

-- 20181128 市场名单更新招生来源
SELECT `x360p_market_clue`
WHERE `from_did` = 0;

UPDATE `x360p_market_clue` mcl
LEFT JOIN `x360p_market_channel` mc
ON mcl.mc_id = mc.mc_id
SET mcl.from_did = mc.from_did
WHERE mcl.mc_id > 0 AND mcl.from_did = 0 AND mc.mc_id IS NOT NULL;

-- 20181129 更新转让课时的金额
select thh.lesson_hours,thh.lesson_amount,sl.lesson_hours as from_lesson_hours,sl.trans_out_lesson_hours,oi.price from `x360p_transfer_hour_history` thh
left join `x360p_student_lesson` sl
ON thh.from_sl_id = sl.sl_id
left join `x360p_order_item` oi
ON sl.sl_id = oi.sl_id
WHERE oi.oi_id IS NOT NULL;

update x360p_transfer_hou_history thh
left join `x360p_student_lesson` sl
ON thh.from_sl_id = sl.sl_id
left join `x360p_order_item` oi
ON sl.sl_id = oi.sl_id
set thh.lesson_amount = thh.lesson_hours * oi.price
WHERE oi.oi_id IS NOT NULL;

-- 20181130 阳光喔的储值卡 有效期不对的更新
update `x360p_student_debit_card`
set `expire_int_day` = `start_int_day` + 10000
WHERE `expire_int_day` = 19700101 and `start_int_day` > 0 and remain_amount > 10000;

update `x360p_student_debit_card`
set `expire_int_day` = `start_int_day` + 94000
WHERE `expire_int_day` = 19700101 and `start_int_day` > 0 and remain_amount < 10000;

-- 20181130 柏金瀚的 排课数据不准确
SELECT * from `x360p_course_arrange` ca
LEFT JOIN `x360p_class_attendance` catt
ON ca.ca_id = catt.ca_id
WHERE ca.cid <> catt.cid;

UPDATE `x360p_course_arrange` ca
LEFT JOIN `x360p_class_attendance` catt
ON ca.ca_id = catt.ca_id
SET ca.is_attendance = 0
WHERE ca.cid <> catt.cid AND ca.cid > 0;



SELECT * from `x360p_class_attendance` catt
LEFT JOIN `x360p_course_arrange` ca
ON catt.cid = ca.cid AND catt.int_day = ca.int_day AND catt.int_start_hour = ca.int_start_hour AND catt.int_end_hour = ca.int_end_hour AND catt.eid = ca.teach_eid
WHERE catt.ca_id <> ca.ca_id ;

UPDATE `x360p_class_attendance` catt
LEFT JOIN `x360p_course_arrange` ca
ON catt.cid = ca.cid AND catt.int_day = ca.int_day AND catt.int_start_hour = ca.int_start_hour AND catt.int_end_hour = ca.int_end_hour AND catt.eid = ca.teach_eid
SET catt.ca_id = ca.ca_id
WHERE catt.ca_id <> ca.ca_id;

SELECT satt.sid,satt.int_day,satt.int_start_hour,satt.int_end_hour,satt.cid,satt.ca_id,catt.cid as cid2,catt.ca_id as ca_id2 from `x360p_student_attendance` satt
LEFT JOIN `x360p_class_attendance` catt
ON satt.catt_id = catt.catt_id
WHERE satt.ca_id <> catt.ca_id;

SELECT slh.* from `x360p_student_lesson_hour` slh
LEFT JOIN `x360p_class_attendance` catt
ON slh.catt_id = catt.catt_id
WHERE catt.cid <> slh.cid;


-- 20181201 查询学员已考勤但是排课记录显示未考勤的记录
SELECT ca.*,c1.n1,c2.n2 from `x360p_course_arrange` ca
LEFT JOIN (
  SELECT `ca_id`,count(*) as n1  from `x360p_course_arrange_student` WHERE is_delete = 0 AND is_in > -1 group by `ca_id`
) c1
ON ca.ca_id = c1.ca_id
LEFT JOIN (
  SELECT `ca_id`,count(*) as n2 from `x360p_course_arrange_student` WHERE is_delete = 0 group by `ca_id`
) c2
ON ca.ca_id = c2.ca_id

WHERE c1.n1 IS NOT NULL AND c2.n2 IS NOT NULL AND ca.is_attendance = 0 AND c1.n1 = c2.n2;
-- 更新所有考勤过的排课记录为2
UPDATE `x360p_course_arrange` ca
LEFT JOIN (
  SELECT `ca_id`,count(*) as n1  from `x360p_course_arrange_student` WHERE is_delete = 0 AND is_in > -1 group by `ca_id`
) c1
ON ca.ca_id = c1.ca_id
LEFT JOIN (
  SELECT `ca_id`,count(*) as n2 from `x360p_course_arrange_student` WHERE is_delete = 0 group by `ca_id`
) c2
ON ca.ca_id = c2.ca_id
SET ca.is_attendance = 2
WHERE c1.n1 IS NOT NULL AND c2.n2 IS NOT NULL AND ca.is_attendance = 0 AND c1.n1 = c2.n2;

SELECT ca.*,c1.n1,c2.n2 from `x360p_course_arrange` ca
LEFT JOIN (
  SELECT `ca_id`,count(*) as n1  from `x360p_course_arrange_student` WHERE is_delete = 0 AND is_in > -1 group by `ca_id`
) c1
ON ca.ca_id = c1.ca_id
LEFT JOIN (
  SELECT `ca_id`,count(*) as n2 from `x360p_course_arrange_student` WHERE is_delete = 0 group by `ca_id`
) c2
ON ca.ca_id = c2.ca_id

WHERE c1.n1 IS NOT NULL AND c2.n2 IS NOT NULL AND ca.is_attendance = 0 AND c1.n1 < c2.n2;
-- 更新所有部分考勤的记录为1
UPDATE `x360p_course_arrange` ca
LEFT JOIN (
  SELECT `ca_id`,count(*) as n1  from `x360p_course_arrange_student` WHERE is_delete = 0 AND is_in > -1 group by `ca_id`
) c1
ON ca.ca_id = c1.ca_id
LEFT JOIN (
  SELECT `ca_id`,count(*) as n2 from `x360p_course_arrange_student` WHERE is_delete = 0 group by `ca_id`
) c2
ON ca.ca_id = c2.ca_id
SET ca.is_attendance = 1
WHERE c1.n1 IS NOT NULL AND c2.n2 IS NOT NULL AND ca.is_attendance = 0 AND c1.n1 < c2.n2;

-- 查询所有未登记考勤的排课记录显示已经考勤的
SELECT ca.*,c1.n1,c2.n2 from `x360p_course_arrange` ca
LEFT JOIN (
  SELECT `ca_id`,count(*) as n1  from `x360p_course_arrange_student` WHERE is_delete = 0 AND is_in > -1 group by `ca_id`
) c1
ON ca.ca_id = c1.ca_id
LEFT JOIN (
  SELECT `ca_id`,count(*) as n2 from `x360p_course_arrange_student` WHERE is_delete = 0 group by `ca_id`
) c2
ON ca.ca_id = c2.ca_id

WHERE c1.n1 IS NULL AND c2.n2 IS NOT NULL AND ca.is_attendance = 2;

-- 20181201 查询学员已考勤但是排课记录显示部分考勤的记录
SELECT ca.*,c1.n1,c2.n2 from `x360p_course_arrange` ca
LEFT JOIN (
  SELECT `ca_id`,count(*) as n1  from `x360p_course_arrange_student` WHERE is_delete = 0 AND is_in > -1 group by `ca_id`
) c1
ON ca.ca_id = c1.ca_id
LEFT JOIN (
  SELECT `ca_id`,count(*) as n2 from `x360p_course_arrange_student` WHERE is_delete = 0 group by `ca_id`
) c2
ON ca.ca_id = c2.ca_id

WHERE c1.n1 IS NOT NULL AND c2.n2 IS NOT NULL AND ca.is_attendance = 1 AND c1.n1 = c2.n2;

-- 更新所有已经登记完考勤的排课记录为已考勤状态
UPDATE `x360p_course_arrange` ca
LEFT JOIN (
  SELECT `ca_id`,count(*) as n1  from `x360p_course_arrange_student` WHERE is_delete = 0 AND is_in > -1 group by `ca_id`
) c1
ON ca.ca_id = c1.ca_id
LEFT JOIN (
  SELECT `ca_id`,count(*) as n2 from `x360p_course_arrange_student` WHERE is_delete = 0 group by `ca_id`
) c2
ON ca.ca_id = c2.ca_id
SET ca.is_attendance = 2
WHERE c1.n1 IS NOT NULL AND c2.n2 IS NOT NULL AND ca.is_attendance = 1 AND c1.n1 = c2.n2;


-- 20181201 更新市场名单来源id为0的
UPDATE `x360p_market_clue` mcl
LEFT JOIN `x360p_market_channel` mc
ON mcl.mc_id = mc.mc_id
SET mcl.from_did = mc.from_did
WHERE mc.mc_id IS NOT NULL AND mcl.from_did = 0;

-- 20181201 清除排课学员记录不在班级的学员
SELECT * from `x360p_course_arrange_student` cas
LEFT JOIN `x360p_class_student` cs
ON cas.sid = cs.sid AND cas.cid = cs.cid AND cs.status = 1
WHERE cas.cid > 0 AND cas.is_makeup = 0 AND cas.sid > 0 AND cas.is_trial = 0 AND cas.is_delete = 0 and cas.is_in = -1 AND cs.cs_id IS NULL;


UPDATE `x360p_course_arrange_student` cas
LEFT JOIN `x360p_class_student` cs
ON cas.sid = cs.sid AND cas.cid = cs.cid AND cs.status = 1
SET cas.is_delete = 1,cas.delete_time = 99999999
WHERE cas.cid > 0 AND cas.is_makeup = 0 AND cas.sid > 0 AND cas.is_trial = 0 AND cas.is_delete = 0 AND cas.is_in = -1 AND cs.cs_id IS NULL;



-- 20181201 查询班级实际人数与班级列表的人数不一致的数据
SELECT c.cid,c.class_name,c.student_nums,c.plan_student_nums,cc.nums from `x360p_class` c
LEFT JOIN (
  select cid,count(*) as nums from x360p_class_student where status = 1 and is_delete=0 group by cid
) cc
ON c.cid = cc.cid
WHERE c.student_nums <> cc.nums;
-- 批量更新班级人数与实际人数不相等的情况
UPDATE `x360p_class` c
LEFT JOIN (
  select cid,count(*) as nums from x360p_class_student where status = 1 group by cid
) cc
ON c.cid = cc.cid
SET c.student_nums = cc.nums
WHERE c.student_nums <> cc.nums;

-- 20181204 阳光喔出现 course_arrange_student 记录的 is_in 状态不对
SELECT * from `x360p_course_arrange_student` cas
LEFT JOIN `x360p_student_attendance` satt
ON cas.satt_id = satt.satt_id
WHERE cas.is_delete = 0 AND cas.satt_id > 0 AND cas.is_in <> -1 AND satt.satt_id IS NULL
;

UPDATE `x360p_course_arrange_student` cas
LEFT JOIN `x360p_student_attendance` satt
ON cas.satt_id = satt.satt_id
SET cas.is_in = -1,cas.satt_id = 0,cas.is_attendance = 0,cas.is_leave = 0,cas.consume_lesson_hour = 0
WHERE cas.is_delete = 0 AND cas.satt_id > 0 AND cas.is_in <> -1 AND satt.satt_id IS NULL
;

-- 查看学员课耗记录不存在考勤记录的课耗记录
SELECT * from `x360p_student_lesson_hour` slh
LEFT JOIN `x360p_student_attendance` satt
ON slh.satt_id = satt.satt_id
WHERE slh.is_delete = 0 AND slh.change_type = 1 AND satt.satt_id IS NULL;

SELECT * from `x360p_student_lesson_hour` slh
LEFT JOIN `x360p_student_attendance` satt
ON slh.sid = satt.sid AND slh.int_day = satt.int_day AND slh.int_start_hour = satt.int_start_hour AND slh.int_end_hour = satt.int_end_hour
WHERE slh.is_delete = 0 AND slh.change_type = 1 AND slh.satt_id <> satt.satt_id;

UPDATE `x360p_student_lesson_hour` slh
LEFT JOIN `x360p_student_attendance` satt
ON slh.sid = satt.sid AND slh.int_day = satt.int_day AND slh.int_start_hour = satt.int_start_hour AND slh.int_end_hour = satt.int_end_hour
SET slh.satt_id = satt.satt_id
WHERE slh.is_delete = 0 AND slh.change_type = 1 AND slh.satt_id <> satt.satt_id;




-- 更新student_lesson_hour 表的 catt_id 不准确的记录
SELECT * from `x360p_student_lesson_hour` slh
LEFT JOIN `x360p_class_attendance` catt
ON slh.ca_id = catt.ca_id
WHERE slh.is_delete = 0 AND slh.catt_id <> catt.catt_id;

UPDATE `x360p_student_lesson_hour` slh
LEFT JOIN `x360p_class_attendance` catt
ON slh.ca_id = catt.ca_id
SET slh.catt_id = catt.catt_id
WHERE slh.is_delete = 0 AND slh.catt_id <> catt.catt_id;

-- 2018-12-05 阳光喔发现 1对1 排课显示 部分考勤状态
SELECT * from `x360p_course_arrange` ca
LEFT JOIN `x360p_course_arrange_student` cas
ON ca.ca_id = cas.ca_id AND cas.is_delete = 0
WHERE ca.lesson_type = 1 AND ca.is_attendance = 1 AND cas.is_in = -1

SELECT * from `x360p_course_arrange` ca
LEFT JOIN `x360p_course_arrange_student` cas
ON ca.ca_id = cas.ca_id AND cas.is_delete = 0
WHERE ca.lesson_type = 1 AND ca.is_attendance = 2 AND (cas.is_in = -1 OR cas.cas_id IS NULL)

-- 2018-12-05 阳光喔发现 1对1 排课显示 部分考勤状态
SELECT * from `x360p_course_arrange` ca
LEFT JOIN `x360p_course_arrange_student` cas
ON ca.ca_id = cas.ca_id AND cas.is_delete = 0
WHERE ca.lesson_type = 1 AND ca.is_attendance = 0 AND cas.is_in <> -1;

UPDATE `x360p_course_arrange` ca
LEFT JOIN `x360p_course_arrange_student` cas
ON ca.ca_id = cas.ca_id AND cas.is_delete = 0
SET ca.is_attendance = 0
WHERE ca.lesson_type = 1 AND ca.is_attendance = 1 AND cas.is_in = -1;

-- 20181206 阳光喔班级删除bug
SELECT ca.cid,count(ca.ca_id) as nums from `x360p_course_arrange` ca
LEFT JOIN `x360p_class` c
ON ca.cid = c.cid
WHERE ca.lesson_type = 0 AND ca.is_delete = 0 and ca.cid > 0 AND c.cid IS NULL
GROUP BY ca.cid;

SELECT cs.cid,count(cs.cid) as nums from `x360p_class_schedule` cs
LEFT JOIN `x360p_class` c
ON cs.cid = c.cid
WHERE cs.is_delete = 0 AND c.cid IS NULL
GROUP by cs.csd_id ;

select * from  `x360p_class` c
LEFT JOIN `x360p_course_arrange` ca
ON c.cid = ca.cid
WHERE cid > 2876 and cid < 2911 AND c.sj_id <> ca.sj_id;


UPDATE `x360p_class` c
LEFT JOIN `x360p_course_arrange` ca
ON c.cid = ca.cid
SET c.teach_eid = ca.teach_eid,
c.second_eid = ca.second_eid,
c.second_eids = ca.second_eids,
c.cr_id = ca.cr_id,
c.sj_id = ca.sj_id
WHERE c.cid > 2876 and c.cid < 2911 AND c.sj_id <> ca.sj_id;


-- 2018-12-17 更新order的paid_time 为0
SELECT * from `x360p_order` o
LEFT JOIN `x360p_order_receipt_bill` orb
ON o.oid = orb.oid
WHERE o.is_delete = 0 AND orb.orb_id IS NOT NULL AND o.paid_time =0;

UPDATE `x360p_order` o
LEFT JOIN `x360p_order_receipt_bill` orb
ON o.oid = orb.oid
set o.paid_time = orb.paid_time
WHERE o.is_delete = 0 AND orb.orb_id IS NOT NULL AND o.paid_time =0;

-- 2018-12-17 批量删除指定市场渠道的客户名单
DELETE cfu FROM `x360p_customer_follow_up` cfu
LEFT JOIN `x360p_customer` cu
ON cfu.cu_id = cu.cu_id
WHERE cu.mc_id = 10;

DELETE clg FROM `x360p_customer_log` clg
LEFT JOIN `x360p_customer` cu
ON clg.cu_id = cu.cu_id
WHERE cu.mc_id = 10;

DELETE mlg FROM `x360p_market_clue_log` mlg
LEFT JOIN `x360p_market_clue` mcl
ON mlg.mcl_id = mcl.mcl_id
WHERE mcl.mc_id = 10;

DELETE FROM `x360p_customer` WHERE mc_id = 10;

DELETE FROM `x360p_market_clue` WHERE mc_id = 10;


-- 统计阳光喔数据
SELECT COUNT(*) as total_studnet from `x360p_student`
WHERE is_delete = 0;

SELECT COUNT(*) as branch_total from `x360p_branch`
WHERE is_delete = 0;

SELECT COUNT(*) as employee_total from `x360p_employee`
WHERE is_delete = 0;


-- 查询没有关联退费业绩的退费记录

SELECT * from `x360p_order_refund` orf
LEFT JOIN `x360p_employee_receipt` er
ON orf.or_id = er.or_id
WHERE er.erc_id IS NOT NULL;

-- 查找制定班级的已经结课的孩子

SELECT * from `x360p_student` s 
LEFT JOIN `x360p_class_student` cs 
ON s.sid = cs.sid
WHERE cs.cid = 455 AND cs.status = 1 and s.status = 50;

UPDATE `x360p_student` s 
LEFT JOIN `x360p_class_student` cs 
ON s.sid = cs.sid
SET s.status = 1
WHERE cs.cid = 455 AND cs.status = 1 and s.status = 50;


-- 更新学员名单in_time
update `x360p_student` s
left join (
  select * from x360p_order where is_debit = 0 order by paid_time asc
) a
on s.`sid` = a.`sid`
set s.`in_time` = a.paid_time
where a.`oid` IS NOT NULL AND a.`pay_status` = 2;




-- 更新student_lesson_hour 里面的 cr_id
update `x360p_student_lesson_hour` slh
left join `x360p_course_arrange` ca
on slh.`ca_id` = ca.`ca_id`
set slh.`cr_id` = ca.`cr_id`
where slh.`cr_id` = 0 and slh.`ca_id` > 0;

