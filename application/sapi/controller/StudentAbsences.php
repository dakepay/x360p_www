<?php
/**
 * Author: luo
 * Time: 2018/6/30 10:54
 */

namespace app\sapi\controller;


use app\sapi\model\StudentAbsence;
use think\Request;

class StudentAbsences extends Base
{

    public function get_list(Request $request)
    {
        $m_sa = new StudentAbsence();
        $get = $request->get();
        $ret = $m_sa->getSearchResult($get);
        return $this->sendSuccess($ret);
    }


}