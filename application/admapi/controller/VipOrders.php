<?php
/**
 * Author: luo
 * Time: 2017/12/6 12:22
 */

namespace app\admapi\controller;


use app\admapi\model\VipOrder;
use think\Request;

class VipOrders extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();
        $m_db = new VipOrder();
        $ret = $m_db->with(['client'])->getSearchResult($input);

        
        return $this->sendSuccess($ret);
    }
}