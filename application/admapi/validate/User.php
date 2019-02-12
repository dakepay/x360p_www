<?php
/**
 * Author: luo
 * Time: 2017-12-04 17:55
**/

namespace app\admapi\validate;

use think\Validate;

class User extends Validate
{
    // 验证规则
    protected $rule = [
        ['account|账号', 'require|alphaNum|unique:user'],
        ['password|密码', 'require|length:4,20'],
        ['repassword|确认密码', 'require|confirm:password'],
        //['captcha|验证码', 'require|checkCaptcha'],
        ['captcha|验证码', 'alphaNum'],
        ['email|邮箱', 'email|unique:user']
    ];

    protected $scene = [
        'post'          => ['account', 'password'],
        'signup'        => ['account', 'password', 'repassword', 'captcha' =>'require|checkSignUpCaptcha'],
        'signin'        => ['account' => 'require|length:3,20', 'password', 'captcha'],
        'msignin'       => ['account' => 'require|length:3,20', 'password'],
        'resetpwd'      => ['password'],
    ];


}