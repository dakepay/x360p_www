<?php

namespace app\api\controller;

use think\Request;
use app\api\model\TransferHourHistory;

class TransferHourHistorys extends Base
{
	public function get_list(Request $request)
	{
		$model = new TransferHourHistory;
	    $input = $request->get();
	    $with = [];
        if(isset($input['with'])){
            $with[] = $input['with'];
        }
        $w = [];
        unset($input['with']);
	    $ret = $model->where($w)->getSearchResult($input);
	    foreach ($ret['list'] as &$row) {
	    	$row['from_student_name'] = get_student_name($row['from_sid']);
	    	$row['to_student_name'] = get_student_name($row['to_sid']);
	    }

	    if($ret !== false){
	    	return $this->sendSuccess($ret);
	    }
	}

    /**
     * 撤销转让课时
     * @param Request $request
     */
	public function delete(Request $request)
    {
        $thh_id = input('id/d');

        $mThh = new TransferHourHistory();
        $result = $mThh->delTransferHour($thh_id);
        if (false === $result){
            return $this->sendError(400,$mThh->getError());
        }

        return $this->sendSuccess();
    }

}