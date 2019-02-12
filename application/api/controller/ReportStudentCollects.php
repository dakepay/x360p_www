<?php

namespace app\api\controller;
use think\Request;

use app\api\model\ReportStudentCollect;

class ReportStudentCollects extends Base
{
	public function get_list(Request $request)
	{
		// echo 'success';exit;
		$input = $request->get();
		$input['bid'] = $request->header('x-bid');
		$model = new ReportStudentCollect;
		$ret = $model->getDaySectionReport($input);
		if(!$ret){
			return $this->sendError(400,$model->getError());
		}

		return $this->sendSuccess($ret);

	}
}