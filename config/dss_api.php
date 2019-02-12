<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/9/22
 * Time: 17:39
 */
return [
    'token'      => 'd66a19bc4b26cd68299116f07517ae0c',
    'domain'     => 'http://dss.ygwo.cn',
    'end_points' => [
        /*命令行api*/
        'schools'                 => '/api/getschools',
        'students'                => '/api/get_students',
        'employee'                => '/api/get_employees',
        'roles'                   => '/api/get_roles',
        'hours'                   => '/api/get_student_remain_money',

        /*web api*/
        'get_student_attendances' => '/api/get_student_attendances',
        'get_classid_by_name'     => '/api/get_classid_by_name',
        'get_students_by_classid' => '/api/get_students_by_classid',
        'emp_changepwd'           => '/api/emp_changepwd',
        'teacher_class'           => '/api/myclass',
    ]
];