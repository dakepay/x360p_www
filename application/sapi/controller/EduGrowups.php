<?php
/**
 * Author: luo
 * Time: 2018/6/22 17:58
 */

namespace app\sapi\controller;


use app\api\model\EduGrowup;
use think\Request;

class EduGrowups extends Base
{

    public function get_list(Request $request)
    {
        $m_eg = new EduGrowup();
        $get = $request->get();

        $ret = $m_eg->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

}