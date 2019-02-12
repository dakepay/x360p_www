<?php
/**
 * Author: luo
 * Time: 2018/3/22 16:16
 */

namespace app\api\controller;


use app\api\model\ActionLog;
use app\common\db\Query;
use think\Request;

class ActionLogs extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        /** @var Query $m_al */
        $m_al = new ActionLog();
        $ret = $m_al->with(['user', 'org'])->getSearchResult($get);
        
        return $this->sendSuccess($ret);
    }
}