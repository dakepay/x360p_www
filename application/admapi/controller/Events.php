<?php
/**
 * Author: luo
 * Time: 2017-12-18 11:43
**/

namespace app\admapi\controller;


use think\Request;

class Events extends Base
{

    public function get_list(Request $request)
    {
        return parent::get_list($request);
    }

    public function post(Request $request)
    {
        return parent::post($request);
    }

    public function put(Request $request)
    {
        return parent::put($request);
    }



}