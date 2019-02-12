<?php
/**
 * Author: luo
 * Time: 2017-10-14 16:31
 * Dsc: 机构收款帐户
**/

namespace app\api\controller;

use think\Request;
use app\api\model\AccountingAccount as AccountModel;
use app\api\model\OrderPaymentHistory;

class AccountingAccounts extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();
        $model = new AccountModel();

        $input['bid'] = -1;
        $og_id = gvar('og_id') ? gvar('og_id') : 0;
        $bid = $request->bid;
        $where = "find_in_set($bid, bids) or is_public =1";
        $ret = $model->where($where)->where('og_id', $og_id)
            ->with('tally_help')->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $model = new AccountModel();
        $rs = $model->createOneAccount($input);
        if(!$rs) return $this->sendError(400, $model->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $aa_id = input('id/d');
        $input = $request->put();
        if(isset($input['bids']) && !empty($input['bids'])) {
            $input['bids'] = implode(',', $input['bids']);
        }

        $account = AccountModel::get(['aa_id' => $aa_id]);
        $rs = $account->allowField('name,type,bids,is_public,is_front,th_id,remark,is_default,cp_id')->save($input);
        if($rs === false) return $this->sendError(400, '修改失败');

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $aa_id = input('id/d');
        $account = AccountModel::get(['aa_id' => $aa_id]);
        // if((int)$account->amount != 0) return $this->sendError(400, '帐户余额不为0，不能删除');
        $m_oph = new OrderPaymentHistory;
        $aa_ids = $m_oph->column('aa_id');
        $aa_ids = array_unique($aa_ids);
        if(in_array($aa_id,$aa_ids)){
            return $this->sendError(400,'该账户有交易记录，不能删除');
        }

        $rs = $account->delete();
        if($rs === false) return $this->sendError(400, $account->getErrorMsg());

        return $this->sendSuccess();

    }

}