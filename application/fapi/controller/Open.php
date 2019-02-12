<?php

namespace app\fapi\controller;

/**
 * Class Open
 * @title 开放接口
 * @url /
 * @desc  不需要验证登录的公共接口
 * @version 1.0
 * @readme /md/api/api_open.md
 */
class Open extends Base
{
    public $apiAuth = false;
    public $noRest = true;

    public function _init()
    {
        $og_id = gvar('client')['og_id'];
        gvar('og_id', $og_id);
    }

}
