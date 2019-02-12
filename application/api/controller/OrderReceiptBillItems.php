<?php

namespace app\api\controller;

use app\api\model\OrderReceiptBillItem;
use think\Request;

class OrderReceiptBillItems extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();

        $mOrbi = new OrderReceiptBillItem();
        $ret = $mOrbi->getSearchResult($input);

        return $this->sendSuccess($ret);
    }


    /**
     * 修改缴费金额
     * @param Request $request
     */
    public function update_amount(Request $request)
    {
        $orbi_id = input('orbi_id/d',0);
        $amount = input('amount',0.00);

        $mOrbi = new OrderReceiptBillItem();
        $result = $mOrbi->updatAamount($orbi_id,$amount);
        if (false === $result){
            return $this->sendError(400, $mOrbi->getError());
        }
        return $this->sendSuccess($result);
    }

}