<?php

namespace app\api\controller;

use think\Request;
use app\api\model\ReportLessonhour;
use app\api\model\Branch;

class ReportLessonhours extends Base
{
	public function get_list(Request $request)
	{
		$model = new ReportLessonhour;
		$input = $request->get();
	    $w = [];
	    if(!empty($input['today'])){
	    	$w['int_day'] = date('Ymd',strtotime($input['today']));
	    }else{
	    	$w['int_day'] = date('Ymd',time());
	    }
	    $data = $model->where($w)->getSearchResult();
	    return $this->sendSuccess($data);
	}


	public function post(Request $request)
	{
		$bids = (new Branch)->where('og_id',gvar('og_id'))->column('bid');
		$input['bids'] = $bids;
		$input['end']   = date('Ymd',time());
		$model = new ReportLessonhour;
		$data_exist = $model->select();

		if(empty($data_exist)){

			$start = model('order_item')->where([
	            'og_id' => gvar('og_id'),
	            'gtype' => 0,
			])->order('create_time asc')->value('create_time');
			$input['start'] = date('Ymd',$start);
			$ret = ReportLessonhour::buildReport($input);

		}else{
            $last_day = $model->where('og_id',gvar('og_id'))->order('id desc')->value('int_day');
            $now = date('Ymd',time());
            if($last_day == $now){
            	$input['start'] = $now;
            }else{
            	$input['start'] = date('Ymd',strtotime("+1 day",strtotime($last_day)));
            }
            $ret = ReportLessonhour::buildReport($input);
		}

		if($ret === false){
			return $this->sendError(400,$ret);
		}

		return $this->sendSuccess();

	}


}