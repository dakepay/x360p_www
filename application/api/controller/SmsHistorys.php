<?php
/**
 * Author: luo
 * Time: 2018/8/14 16:53
 */

namespace app\api\controller;


use app\api\model\SmsHistory;
use think\Request;

class SmsHistorys extends Base
{

    public function get_list(Request $request)
    {
        $m_sh = new SmsHistory();
        $get = $request->get();
        $ret = $m_sh->getSearchResult($get);
        return $this->sendSuccess($ret);
    }


}