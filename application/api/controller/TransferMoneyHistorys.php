<?php

namespace app\api\controller;

use think\Request;
use app\api\model\TransferMoneyHistory;

class TransferMoneyHistorys extends Base
{
	public function get_list(Request $request)
	{
        $mTmh = new TransferMoneyHistory;
	    $input = $request->get();
        $w = [];
        unset($input['with']);
	    $ret = $mTmh->where($w)->getSearchResult($input);
	    foreach ($ret['list'] as &$row) {
	    	$row['from_student_name'] = get_student_name($row['from_sid']);
	    	$row['to_student_name'] = get_student_name($row['to_sid']);
	    }
	    if($ret !== false){
	    	return $this->sendSuccess($ret);
	    }
	}


    /**
     * 撤销转让余额
     * @param Request $request
     */
	public function delete(Request $request)
    {
        $tmh_id = input('id/d');

        $mTmh = new TransferMoneyHistory();
        $result = $mTmh->delTransferMoney($tmh_id);
        if (false === $result){
            return $this->sendError($mTmh->getError());
        }

        return $this->sendSuccess();
    }
	
}