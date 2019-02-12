<?php
namespace app\index\controller;

use think\Controller;


class Index extends Base
{
    public function index()
    {
        $request = request();
        $base_domain = config('ui.domain');
        if(!$request->isSsl() && $base_domain == 'pro.xiao360.com'){
            $https_url = str_replace('http://','https://',$request->domain());
            header('Location: '.$https_url);
            exit;
        }
        return $this->send_ui();
    }
}
