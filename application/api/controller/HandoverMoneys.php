<?php
/**
 * Author: luo
 * Time: 2018/1/27 17:48
 */

namespace app\api\controller;


use app\api\model\HandoverMoney;
use think\Request;

class HandoverMoneys extends Base
{

    /**
     * @desc  交款记录
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_hm = new HandoverMoney();
        $ret = $m_hm->getSearchResult($get);
        
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  交班缴费
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        $m_hws = new HandoverMoney();
        $rs = $m_hws->addOneHandoverMoney($post);
        if($rs === false) return $this->sendError(400, $m_hws->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $put = $request->put();
        $hm_id = input('id');
        $handover_money = HandoverMoney::get($hm_id);
        if(empty($handover_money)) return $this->sendError(400, '不存在交款');

        $rs = $handover_money->allowField('tid')->save($put);
        if($rs === false) return $this->sendError(400, $handover_money->getErrorMsg());
        return $this->sendSuccess();
    }


    /**
     * @desc  确认缴款
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function ack(Request $request)
    {
        $hm_id = input('hm_id');
        $handover_money = HandoverMoney::get($hm_id);
        if(empty($handover_money)) return $this->sendError(400, '缴款记录不存在');

        $rs = $handover_money->ack($hm_id, $handover_money);
        if($rs === false) return $this->sendError(400, $handover_money->getErrorMsg());

        return $this->sendSuccess();
    }

}