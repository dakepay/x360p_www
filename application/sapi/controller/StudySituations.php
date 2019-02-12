<?php
/**
 * Author: luo
 * Time: 2018/6/4 17:11
 */

namespace app\sapi\controller;


use app\sapi\model\StudySituation;
use think\Request;

class StudySituations extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_ss = new StudySituation();
        $ret = $m_ss->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

}