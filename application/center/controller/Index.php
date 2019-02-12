<?php

namespace app\center\controller;

use think\Request;


/**
 * Class Open
 * @title 公共接口
 * @url /
 * @desc  需要验证登录的公共接口
 * @version 1.0
 * @readme
 */
class Index extends Base
{
    public function index(Request $request){
        return $this->sendSuccess('ok');
    }
}
