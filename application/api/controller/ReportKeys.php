<?php

namespace app\api\controller;

use think\Request;
use app\api\model\ReportKey;

class ReportKeys extends Base
{
	public function get_list(Request $request)
	{
		$input = $request->get();
		$model = new ReportKey;

		$ret = $model->getMonthSectionReport($input,true);
		if(!$ret){
			return $this->sendError(400,$model->getError());
		}

		return $this->sendSuccess($ret);

	}

}