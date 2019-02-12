<?php
namespace app\index\controller;

use think\Controller;


class School extends Base
{
    public function index()
    {
    	return $this->send_ui('school');
    }
}