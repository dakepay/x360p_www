<?php
namespace app\index\controller;

use think\Controller;


class Student extends Base
{
    public function index()
    {
    	return $this->send_ui('student');
    }
}