<?php
/**
 * Author: luo
 * Time: 2017-12-26 14:38
**/

namespace app\sapi\controller;


use app\sapi\model\Advice;
use think\Request;

class Advices extends Base
{

    public function get_list(Request $request)
    {
        $sid = global_sid();
        $input = $request->get();
        $m_advice = new Advice();
        $ret = $m_advice->with('adviceReply')->where('sid', $sid)->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        return parent::post($request);
    }

}