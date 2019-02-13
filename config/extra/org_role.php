<?php
/**
 * 默认角色数据库对应记录
INSERT INTO `x360p_role` VALUES 
(1,0,'老师','教师老师','','',1,1498095552,1,1504668175,NULL,0,0,NULL,NULL),
(2,0,'助教','助教','','',1,1498095552,1,1504668175,NULL,0,0,NULL,NULL),
(3,0,'校长','校长','','',1,1498098725,1,1504668445,NULL,0,0,NULL,NULL),
(4,0,'学管师','学管师','',NULL,1,1498290947,1,1498290947,NULL,0,0,NULL,NULL),
(5,0,'前台','前台','',NULL,0,1498290965,1,1498290965,NULL,0,0,NULL,NULL),
(6,0,'财务','财务','',NULL,0,1498290995,1,1498290995,NULL,0,0,NULL,NULL),
(7,0,'招生主管','招生主管','',NULL,0,1498291022,1,1498291022,NULL,0,0,NULL,NULL),
(7,0,'招生专员','招生专员','',NULL,0,1498291022,1,1498291022,NULL,0,0,NULL,NULL),
(9,0,'市场','市场专员','',NULL,0,1498291051,1,1498291051,NULL,0,0,NULL,NULL),
(10,0,'系统管理员','系统管理员拥有最高权限','',NULL,0,1498291051,1,1498291051,NULL,0,0,NULL,NULL)
 */
//特殊角色ID
//教师rid 为1  系统管理员rid 为10
return [

	[
		'rid'			=> 1,
		'role_name'		=> '老师',
		'role_desc'		=> '老师',
        'pers'          => 'dashboard,dashboard,dashboard.attendance,dashboard.lessonwarn,dashboard.timeswarn,dashboard.lostwarn,dashboard.todaytrial,dashboard.birthday,dashboard.studentstats,dashboard.broadcast,dashboard.broadcast,dashboard.backlogs,dashboard.backlogs,backlogs.add,backlogs.edit,backlogs.delete,backlogs.finished,backlogs.unfinished,backlogs.addline,backlogs.cancelline,dashboard.side_attendance_panel,business,business.student,student.archive,student.archive,student.add,student.addrecord,student.addtask,student.assign,student.assign,assign.cancel,assign.batch,business.arrange,arrange.lists,arrange.lists,arrange.schedule,arrange.schedule,business.attendance,attendance.teach,attendance.teach,attendance.review,service,service.bclass,bclass.remind,bclass.remind,remind.push,bclass.prepare,bclass.prepare,prepare.add,prepare.edit,service.aclass,aclass.comments,aclass.comments,comments.add,comments.send,aclass.homework,aclass.homework,homework.add,homework.push,aclass.artwork,aclass.artwork,artwork.add,aclass.visit,aclass.visit,visit.add,arrange.checkAll,app.faceatt,faceatt.manage,app.ft,ft.manage,app.webcall,webcall.manage',
		'mobile_pers'	=> ''
	],
	[
		'rid'			=> 2,
		'role_name'		=> '助教',
		'role_desc'		=> '助教',
		'pers'			=> 'dashboard,dashboard,dashboard.addcustomer,dashboard.lessonwarn,dashboard.timeswarn,dashboard.lostwarn,dashboard.todaytrial,dashboard.birthday,dashboard.broadcast,dashboard.broadcast,dashboard.backlogs,dashboard.backlogs,backlogs.add,backlogs.edit,backlogs.delete,backlogs.finished,backlogs.unfinished,backlogs.addline,backlogs.cancelline,dashboard.side_attendance_panel,business,business.student,student.archive,student.archive,student.add,student.transfer,student.pay,student.refund,student.transferclass,student.transferschool,student.stopLesson,student.resumeLesson,student.leaveSchool,student.backSchool,student.dropOut,student.admission,student.delete,student.leave,student.addrecord,student.addtask,student.showalltel,business.class,class.list,class.list,business.arrange,arrange.lists,arrange.lists,arrange.edit,arrange.add,arrange.cancel,arrange.delete,arrange.schedule,arrange.schedule,schedule.viewreview,schedule.addreview,business.attendance,attendance.teach,attendance.teach,attendance.review,attendance.cancel,attendance.student,attendance.student,attendance.swipe,attendance.swipe,attendance.leave,attendance.leave,attendance.absence,attendance.absence,attendance.makeup,attendance.makeup,service,service.bclass,bclass.remind,bclass.remind,remind.push,bclass.prepare,bclass.prepare,prepare.add,prepare.edit,bclass.notice,service.aclass,aclass.comments,aclass.comments,comments.add,comments.send,aclass.homework,aclass.homework,homework.add,homework.push,aclass.artwork,aclass.artwork,artwork.add,aclass.visit,aclass.visit,visit.add,arrange.checkAll',
		'mobile_pers'	=> ''
	],
	[
		'rid'			=> 3,
		'role_name'		=> '校长',
		'role_desc'		=> '校长',
		'pers'			=> 'dashboard,dashboard.view,dashboard.dosignup,dashboard.signupconfirm,dashboard.saveorder,'.
		'dashboard.chargeconfirm,dashboard.dosettle,dashboard.chargemakeup,dashboard.dotransfer,dashboard.dorefund,'.
		'dashboard.toformal,dashboard.broadcast,broadcast.add,broadcast.edit,broadcast.delete,dashboard.backlogs,'.
		'backlogs.add,backlogs.edit,backlogs.delete,backlogs.finished,backlogs.unfinished,backlogs.addline,'.
		'backlogs.cancelline,recruiting,recruiting.list,recruiting.add,recruiting.edit,recruiting.delete,'.
		'recruiting.following,following.add,following.edit,recruiting.audition,audition.add,audition.delete,business,'.
		'business.student,student.add,student.edit,student.export,student.stop,student.transfer,student.transferclass,'.
		'student.transferschool,student.stopLesson,student.resumeLesson,student.leaveSchool,student.backSchool,'.
		'student.dropOut,student.admission,student.delete,business.order,order.pay,order.refund,business.class,'.
		'class.add,class.edit,class.arrange,class.students,business.arrange,arrange.add,arrange.visual,arrange.delete,'.
		'business.attendance,attendance.cancel,business.hour,business.iae,service,service.study,service.visit,'.
		'service.remind,service.comments,service.homeworks,reports,reports.overview,reports.customer,reports.on,'.
		'reports.income,reports.attendance,reports.performance,reports.incomeandexpend,reports.service,reports.export,'.
		'basic,basic.lesson,lesson.new,lesson.bindmaterial,lesson.delete,lesson.edit,basic.subject,subject.add,'.
		'subject.edit,subject.delete,basic.teachers,teachers.add,teachers.edit,teachers.delete,account.add,account.lock,'.
		'account.reset,basic.classrooms,classrooms.add,classrooms.edit,classrooms.delete,basic.time,time.add,time.edit,'.
		'time.delete,basic.holiday,basic.materials,materials.add,materials.edit,materials.delete,materials.in,'.
		'materials.out,materials.transfer,materials.store,basic.schools,schools.add,schools.edit,schools.delete',
		'mobile_pers'	=> ''
	],
	[
		'rid'			=> 4,
		'role_name'		=> '学管师',
		'role_desc'		=> '学管师',
		'pers'			=> 'dashboard,dashboard.view,dashboard.dosignup,dashboard.signupconfirm,dashboard.saveorder,'.
		'dashboard.chargeconfirm,dashboard.dosettle,dashboard.chargemakeup,dashboard.dotransfer,dashboard.dorefund,'.
		'dashboard.toformal,dashboard.broadcast,broadcast.add,broadcast.edit,broadcast.delete,dashboard.backlogs,'.
		'backlogs.add,backlogs.edit,backlogs.delete,backlogs.finished,backlogs.unfinished,backlogs.addline,'.
		'backlogs.cancelline,recruiting,recruiting.list,recruiting.add,recruiting.edit,recruiting.delete,'.
		'recruiting.following,following.add,following.edit,recruiting.audition,audition.add,audition.delete,'.
		'business.student,student.add,student.edit,student.export,student.stop,student.transfer,student.transferclass,'.
		'student.transferschool,student.stopLesson,student.resumeLesson,student.leaveSchool,student.backSchool,'.
		'student.dropOut,student.admission,student.delete,business.order,order.pay,order.refund,business.class,'.
		'class.add,class.edit,class.arrange,class.students,business.attendance,attendance.cancel,business.hour,'.
		'business.iae,service,service.study,service.visit,service.remind,service.comments,service.homeworks',
		'mobile_pers'	=> ''
	],
	[
		'rid'			=> 5,
		'role_name'		=> '前台',
		'role_desc'		=> '前台',
		'pers'			=> 'dashboard,dashboard.view,dashboard.dosignup,dashboard.signupconfirm,dashboard.saveorder,'.
		'dashboard.chargeconfirm,dashboard.dosettle,dashboard.chargemakeup,dashboard.dotransfer,dashboard.dorefund,'.
		'dashboard.toformal,dashboard.broadcast,broadcast.add,broadcast.edit,broadcast.delete,dashboard.backlogs,'.
		'backlogs.add,backlogs.edit,backlogs.delete,backlogs.finished,backlogs.unfinished,backlogs.addline,'.
		'backlogs.cancelline,recruiting,recruiting.list,recruiting.add,recruiting.edit,recruiting.delete,'.
		'recruiting.following,following.add,following.edit,recruiting.audition,audition.add,audition.delete,'.
		'business.student,student.add,student.edit,student.export,student.stop,student.transfer,student.transferclass,'.
		'student.transferschool,student.stopLesson,student.resumeLesson,student.leaveSchool,student.backSchool,'.
		'student.dropOut,student.admission,student.delete,business.order,order.pay,order.refund,business.attendance,attendance.cancel',
		'mobile_pers'	=> ''
	],
	[
		'rid'			=> 6,
		'role_name'		=> '财务',
		'role_desc'		=> '财务',
		'pers'			=> 'dashboard,dashboard.backlogs,backlogs.add,backlogs.edit,backlogs.delete,backlogs.finished,backlogs.unfinished,backlogs.addline,backlogs.cancelline,business,business.hour,business.iae,iae.tally,tally.add,iae.bills,bills.invalid,iae.hand,hand.ack,hand.transfer,iae.type,type.add,iae.help,help.add,reports,reports.overview,reports.customer,reports.on,reports.income,reports.attendance,reports.performance,reports.incomeandexpend,reports.export',
		'mobile_pers'	=> ''
	],
	[
		'rid'			=> 7,
		'role_name'		=> '咨询师',
		'role_desc'		=> '咨询师',
		'pers'			=> 'dashboard,dashboard.view,dashboard.dosignup,dashboard.signupconfirm,dashboard.saveorder,'.
		'dashboard.chargeconfirm,dashboard.dosettle,dashboard.chargemakeup,dashboard.dotransfer,dashboard.dorefund,'.
		'dashboard.toformal,dashboard.broadcast,broadcast.add,broadcast.edit,broadcast.delete,dashboard.backlogs,'.
		'backlogs.add,backlogs.edit,backlogs.delete,backlogs.finished,backlogs.unfinished,backlogs.addline,'.
		'backlogs.cancelline,recruiting.list,recruiting.add,recruiting.edit,recruiting.delete,recruiting.following,'.
		'following.add,following.edit,business.order,order.pay,order.refund,service.visit,reports.overview,reports.customer',
		'mobile_pers'	=> ''
	],
	[
		'rid'			=> 8,
		'role_name'		=> '市场专员',
		'role_desc'		=> '市场专员',
		'pers'			=> 'system,system.configs,configs.params,configs.ui,configs.print,configs.wxmp,wxmp.list,wxmp.basic,wxmp.tplmsg,wxmp.menu,wxmp.material,wxmp.reply,configs.template,configs.storage,configs.reviews_tpl,system.departments,departments.add,departments.edit,departments.delete,branchs.edit,system.employees,employees.add,employees.edit,employees.delete,employees.leave,employees.restore,system.roles,roles.add,roles.edit,roles.delete,roles.per,system.dicts,system.orgs,orgs.add,orgs.edit,orgs.lock,orgs.renew,orgs.delete',
		'mobile_pers'	=> ''
	],
    [
        'rid'			=> 10,
        'role_name'		=> '系统管理员',
        'role_desc'		=> '系统管理员',
        'pers'			=> 'dashboard,dashboard,dashboard.addcustomer,dashboard.follow,dashboard.signup,dashboard.payment,order.save,order.submit,dashboard.attendance,dashboard.transfer,dashboard.refund,dashboard.leave,dashboard.sendsms,dashboard.sendwx,dashboard.lessonwarn,dashboard.timeswarn,dashboard.lostwarn,dashboard.todaytrial,dashboard.birthday,dashboard.studentstats,dashboard.broadcast,dashboard.broadcast,broadcast.add,broadcast.edit,broadcast.delete,dashboard.backlogs,dashboard.backlogs,backlogs.add,backlogs.edit,backlogs.delete,backlogs.finished,backlogs.unfinished,backlogs.addline,backlogs.cancelline,dashboard.side_attendance_panel,recruiting,recruiting.market,market.overview,market.channel,market.channel,channel.add,channel.edit,channel.delete,channel.addclue,channel.importclue,channel.scannerclue,market.clue,market.clue,clue.add,clue.edit,clue.delete,clue.import,clue.export,clue.distribute,clue.transfer,clue.batch,market.recommend,recruiting.list,list.overview,list.customer,list.customer,customer.add,customer.import,customer.export,customer.batch,customer.signup,customer.follow,customer.edit,customer.delete,customer.distribution,list.lost,list.lostwarn,recruiting.following,following.overview,following.flist,following.flist,flist.add,flist.delete,flist.edit,flist.export,following.summary,recruiting.audition,audition.overview,audition.aslist,audition.aslist,aslist.add,aslist.delete,aslist.complete,audition.aclist,audition.aclist,aclist.add,aclist.edit,aclist.delete,aclist.student,audition.assess,audition.assess,assess.add,assess.edit,assess.delete,recruiting.demo,demo.overview,demo.lesson,demo.lesson,demolesson.add,demolesson.edit,demolesson.delete,demo.signup,demo.signup,demoorder.asclass,demoorder.makeup,demoorder.transfer,demoorder.delete,demoorder.pay,demoorder.refund,demo.class,demo.class,democlass.add,democlass.edit,democlass.arrange,democlass.students,democlass.delete,democlass.close,business,business.student,student.overview,student.archive,student.archive,student.add,student.issueCard,student.edit,student.export,student.closeLesson,student.transfer,student.transmoney,student.transhour,student.debit,student.changedc,student.pay,student.refund,student.transferclass,student.transferschool,student.stopLesson,student.resumeLesson,student.leaveSchool,student.backSchool,student.dropOut,student.admission,student.delete,student.leave,student.unarchive,student.addrecord,student.addtask,student.integral,student.showalltel,student.assign,student.assign,assign.add,assign.cancel,assign.batch,student.integral,student.integral,integral.batch,integral.ruleadd,student.class,business.order,order.overview,order.offline,order.offline,offline.asclass,offline.makeup,offline.transfer,offline.print,offline.delete,offline.pay,offline.refund,offline.edit,offline.export,order.items,order.items,orderitems.undo,orderitems.edit,order.hours,order.hours,hours.import,hours.edit,hours.present,hours.transfer,transfer.undo,transferhours.undo,transfermoney.undo,presenthours.undo,order.referer,business.class,class.overview,class.list,class.list,class.add,class.edit,class.arrange,class.students,class.delete,class.up,class.close,class.undo,class.templist,class.templist,temp.edit,temp.scanner,temp.delete,class.students,business.arrange,arrange.overview,arrange.lists,arrange.lists,arrange.edit,arrange.add,arrange.visual,arrange.cancel,arrange.delete,arrange.batch,arrange.export,arrange.schedule,arrange.schedule,schedule.viewreview,schedule.addreview,schedule.deletearrange,business.attendance,attendance.overview,attendance.teach,attendance.teach,attendance.review,attendance.cancel,attendance.confirm,attendance.cancel_confirm,class_attendance.export,attendance.student,attendance.student,student_attendance.export,attendance.swipe,attendance.swipe,swipe.export,attendance.leave,attendance.leave,leave.export,attendance.absence,attendance.absence,absence.export,attendance.makeup,attendance.makeup,makeup.export,business.hour,hour.overview,hour.student,hour.student,hour.reg,hour.deduct,hour.delete,hour.lesson,hour.employee,hour.teacher,business.iae,iae.overview,iae.tally,iae.tally,tally.add,tally.delete,tally.edit,iae.bills,iae.bills,bills.invalid,bills.edit,iae.refund,iae.refund,refund.undo,refund.print,iae.receipt,iae.receipt,receipt.edit,receipt.delete,iae.hand,iae.hand,hand.ack,hand.transfer,iae.asset,iae.summary,iae.type,iae.type,type.add,iae.help,iae.help,help.add,service,service.bclass,bclass.overview,bclass.remind,bclass.remind,remind.push,remind.plan,bclass.prepare,bclass.prepare,prepare.add,prepare.edit,prepare.delete,prepare.export,bclass.notice,service.aclass,aclass.overview,aclass.comments,aclass.comments,comments.add,comments.delete,comments.send,comments.export,aclass.homework,aclass.homework,homework.add,homework.push,homework.delete,homework.export,aclass.artwork,aclass.artwork,artwork.add,artwork.delete,artwork.export,aclass.visit,aclass.visit,visit.add,visit.delete,visit.export,service.study,study.overview,study.student,study.log,study.reply,study.complaint,service.situation,service.situation,situation.add,situation.edit,situation.delete,service.lesson_buy_suit,service.lesson_buy_suit,lesson_buy_suit.add,lesson_buy_suit.edit,lesson_buy_suit.delete,app,app.books,app.materials,app.materials,materials.add,materials.edit,materials.delete,materials.in,materials.out,materials.transfer,materials.store,app.achievement,achievement.exam,achievement.exam,exam.add,exam.edit,exam.delete,achievement.score,achievement.score,score.entry,score.import,score.export,score.delete,score.edit,app.knowledge,app.knowledge,knowledge.add,knowledge.edit,knowledge.delete,app.fans,app.event,event.list,event.list,event.add,event.edit,event.delete,event.signup,event.signup,app.franchisees,franchisees.archive,franchisees.archive,francharchive.edit,francharchive.delete,francharchive.contract,francharchive.contact,francharchive.open,francharchive.link,francharchive.service,contact.add,contact.edit,contact.delete,franchisees.contract,franchisees.contract,franchcontract.edit,franchcontract.delete,franchisees.service,franchisees.service,apply.edit,apply.delete,franchisees.system,franchisees.system,franchsystem.add,franchsystem.edit,franchsystem.lock,franchsystem.unlock,franchsystem.renew,franchsystem.delete,franchsystem.config,franchsystem.reset,franchsystem.login,franchsystem.review,franchisees.report,app.center,reports,reports.ki,reports.overview,reports.customer,reports.trial,reports.demolesson,reports.on,reports.class,reports.income,reports.attendance,reports.service,reports.performance,performance.employee,performance.branch,performance.stats,performance.teacher,reports.incomeandexpend,reports.export,system,system.configs,configs.params,configs.ui,configs.print,configs.wxmp,configs.wxmp,wxmp.list,wxmp.basic,wxmp.tplmsg,wxmp.menu,wxmp.material,wxmp.reply,configs.account,configs.account,payaccount.add,payaccount.edit,payaccount.delete,configs.wechat,configs.sms,configs.sms,sms.gateway,sms.tpls,configs.template,configs.payment,configs.payment,sqb.applyedit,sqb.applydelete,sqb.configactive,sqb.configedit,sqb.configdelete,configs.storage,configs.service_standard,configs.reviews_tpl,configs.business_code,configs.recommend,configs.qrsign,configs.customer_fields,configs.api,configs.maintenance,system.basic,basic.lesson,basic.lesson,lesson.new,lesson.bindmaterial,lesson.delete,lesson.edit,lesson.define,lesson.promotion,basic.course_standard_file,basic.course_standard_file,course_standard_file.add,course_standard_file.edit,course_standard_file.delete,basic.subject,basic.subject,subject.add,subject.edit,subject.delete,subject.grademanage,basic.teachers,basic.teachers,teachers.add,teachers.edit,teachers.delete,account.add,account.lock,account.reset,basic.classrooms,basic.classrooms,classrooms.add,classrooms.edit,classrooms.delete,basic.time,basic.time,time.add,time.edit,time.delete,basic.holiday,basic.fees,basic.fees,fees.add,fees.edit,fees.delete,basic.schools,basic.schools,schools.add,schools.edit,schools.delete,basic.mobile_page,basic.mobile_page,mobile_page.add,mobile_page.edit,mobile_page.delete,basic.questionnaire,basic.questionnaire,ques.add,ques.edit,ques.delete,quesitem.add,quesitem.edit,quesitem.delete,basic.lesson_suit_define,basic.lesson_suit_define,lesson_suit_define.add,lesson_suit_define.edit,lesson_suit_define.delete,basic.debit,basic.debit,debit_card.add,debit_card.edit,debit_card.delete,debit_card_history.print,debit_card_history.edit,debit_card_history.delete,system.staff,staff.departments,staff.departments,departments.add,departments.edit,departments.delete,branchs.edit,staff.employees,staff.employees,employees.add,employees.edit,employees.delete,employees.leave,employees.restore,employees.extraper,staff.roles,staff.roles,roles.add,roles.edit,roles.delete,roles.per,system.dicts,system.orgs,system.orgs,orgs.add,orgs.edit,orgs.lock,orgs.unlock,orgs.renew,orgs.delete,orgs.config,orgs.reset,system.logs,student.checkAll,class.checkAll,arrange.checkAll,prepare.all,comments.all,artwork.all,homework.all,visit.all',
        'mobile_pers'	=> ''
    ],

];