<?php
/**
 * {}括号里面的是变量，可以自定义
 * add_service_record('attendance', ['sid' => 10, 'eid' => 10004, 'st_did' => 2, 'time' => '2015-09-10']);
 */
return [
    'attendance'            => '{ename}给{name}做了考勤服务。{content}',  # 考勤服务
    'course_arrange_remind' => '{ename}给{name}做了课前提醒服务, 时间：{time}',  # 课前提醒服务
    'attend_school'         => '{ename}给{name}做了到离校通知服务, 时间：{time}',  # 到离校服务
    'review'                => '{ename}给{name}做了课评服务, 时间：{time}',  # 课评服务
    'homework'              => '{ename}给{name}做了布置作业服务, 时间：{time}',  # 作业服务
    'artwork'               => '{ename}给{name}做了作品服务, 时间：{time}',  # 作品服务
    'return_visit'          => '{ename}给{name}做了回访服务, 时间：{time}',  # 回访服务
    'study_situation'       => '{ename}给{name}做了学情服务, 时间：{time}',  # 学情服务
];