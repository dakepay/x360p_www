<?php
namespace app\index\controller;

use think\Controller;


class M extends Base
{
    public function index()
    {
    	return $this->send_ui('m');
    }
}