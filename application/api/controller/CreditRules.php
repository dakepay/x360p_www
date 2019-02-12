<?php
/**
 * Author: luo
 * Time: 2018/6/14 11:06
 */

namespace app\api\controller;


use app\api\model\CreditRule;
use think\Request;

class CreditRules extends Base
{
    public function get_list(Request $request)
    {
        $m_cr = new CreditRule();
        $get = $request->get();

        $bid = !empty($get['bid']) ? $get['bid'] : $request->bid;
        $m_cr->where('bid', 'in', [0, $bid]);
        $get['bid'] = -1;
        $ret = $m_cr->getSearchResult($get);
        
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $m_cr = new CreditRule();
        $post = $request->post();
        $rs = $m_cr->addCreditRule($post);
        if($rs === false) return $this->sendError(400, $m_cr->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $cru_id = input('id');
        $credit_rule = CreditRule::get($cru_id);
        if(empty($credit_rule)) return $this->sendError(400, '积分规划不存在');

        $put = $request->put();
        $rs = $credit_rule->updateCreditRule($put);
        if($rs === false) return $this->sendError(400, $credit_rule->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $cru_id = input('id');
        $credit_rule = CreditRule::get($cru_id);
        if(empty($credit_rule)) return $this->sendSuccess();

        $rs = $credit_rule->delCreditRule();
        if($rs === false) return $this->sendError(400, $credit_rule->getErrorMsg());
        
        return $this->sendSuccess();
    }


}