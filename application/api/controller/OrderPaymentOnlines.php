<?php
/**
 * Author: luo
 * Time: 2018/3/6 16:46
 */

namespace app\api\controller;


use app\api\model\OrderPaymentOnline;
use think\Request;

class OrderPaymentOnlines extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_opo = new OrderPaymentOnline();
        $ret = $m_opo->with(['orderPaymentHistory' => ['orderReceiptBill']])->getSearchResult($get);
        return $this->sendSuccess($ret);
    }


}