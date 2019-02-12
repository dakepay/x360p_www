<?php
/**
 * Author: luo
 * Time: 2017-12-20 11:59
**/

namespace app\sapi\controller;

use app\sapi\model\Broadcast;
use think\Request;

class Broadcasts extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        $m_broadcast = new Broadcast();
        $ret = $m_broadcast->where('type', $m_broadcast::TYPE_EXTERNAL)->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  通知数量
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function count(Request $request)
    {
        $input = $request->get();
        $m_broadcast = new Broadcast();
        $num = $m_broadcast->where('type', $m_broadcast::TYPE_EXTERNAL)->count();
        return $this->sendSuccess($num);
    }

}