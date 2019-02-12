<?php
/**
 * Author: luo
 * Time: 2018/7/6 17:41
 */

namespace app\sapi\controller;


use think\Request;

class Pages extends Base
{

    public function get_list(Request $request)
    {
        return parent::get_list($request);
    }

}