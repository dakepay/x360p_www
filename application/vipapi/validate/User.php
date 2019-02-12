<?php
namespace app\vipapi\validate;

use think\Validate;
use util\Captcha;

//use think\captcha\Captcha;

class User extends Validate
{
	// 验证规则
    protected $rule = [
        ['account|账号', 'require|alphaNum|unique:user'],
        ['mobile|手机号', 'require|regex:^1\d{10}|unique:user'],
        ['password|密码', 'require|length:6,20'],
        ['repassword|确认密码', 'require|confirm:password'],
        ['captcha|验证码', 'require|checkCaptcha'],
        //['captcha|验证码', 'alphaNum'],
        ['email|邮箱', 'email|unique:user']
    ];

    protected $scene = [
    	'signin'        => ['account' => 'require|length:3,20', 'password']
    ];

    protected function checkCaptcha($code)
    {
    	$captcha = new Captcha();
        if (!$captcha->check($code,'login')) {
            return 'captcha_error';
        } else {
            return true;
        }
    }

    protected function checkSignUpCaptcha($code)
    {
        $captcha = new Captcha();
        if (!$captcha->check($code,'signup')) {
            return 'captcha_error';
        } else {
            return true;
        }
    }
}