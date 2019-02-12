<?php
namespace app\index\controller;


class Ft extends Base
{
    public function index()
    {
    	return $this->send_ui('ft');
    }
}