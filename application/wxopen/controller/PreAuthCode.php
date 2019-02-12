<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/24
 * Time: 11:30
 */

namespace app\wxopen\controller;

class PreAuthCode extends OpenApp
{
    public $apiAuth = true;

    public function index()
    {
        $openPlatform = $this->openApp->open_platform;
        $pre_auth_code = $openPlatform->pre_auth->getCode();
        $data['pre_auth_code'] = $pre_auth_code;
        return $this->sendSuccess($data);
    }
}