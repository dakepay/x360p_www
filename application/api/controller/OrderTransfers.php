<?php
/**
 * Author: luo
 * Time: 2018-03-13 17:23
**/

namespace app\api\controller;

use app\api\model\OrderTransfer;
use think\Request;

class OrderTransfers extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();

        $m_ot = new OrderTransfer();

        $ret = $m_ot->with(['orderCutAmount', 'employee', 'orderTransferItem' => ['orderItem' => ['studentLesson', 'material', 'oneClass']]])
            ->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function undo_transfer(Request $request)
    {
        $ot_id = input('ot_id/d');

        $mOrderTransfer = new OrderTransfer();
        $result = $mOrderTransfer->undoOrderTransfer($ot_id);
        if (false === $result) {
            return $this->sendError(400, $mOrderTransfer->getError());
        }

        return $this->sendSuccess();
    }

}