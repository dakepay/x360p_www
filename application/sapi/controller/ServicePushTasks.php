<?php
/**
 * Author: luo
 * Time: 2018/6/23 9:52
 */

namespace app\sapi\controller;


use app\sapi\model\ServicePushTask;
use think\Request;

class ServicePushTasks extends Base
{
    public function get_list(Request $request)
    {
        $m_spt = new ServicePushTask();
        $get = $request->get();
        $ret = $m_spt->getSearchResult($get);
        return $this->sendSuccess($ret);
    }


}