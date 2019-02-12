<?php

namespace app\api\controller;

use think\Request;
use app\api\model\StudentLesson;
use app\api\model\ReportStudentByRemainlessonhour;

class ReportStudentByRemainlessonhours extends Base
{
	public function get_list(Request $request)
	{
		$input = $request->get();
		$model = new ReportStudentByRemainlessonhour;

		$w = [];
		$w['status'] = 1;
		if(!empty($input['cid'])){
			$w[] = ['exp',"find_in_set({$input['cid']},cids)"];
		}
		if(!empty($input['lid'])){
			$w[] = ['exp',"find_in_set({$input['lid']},lids)"];
		}

		$data = $model->where($w)->order('sid asc')->getSearchResult($input);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['sid'] = get_student_name($v['sid']);
		}

		return $this->sendSuccess($data);

	}

	public function post(Request $request)
	{
        $model = new StudentLesson;
        $bids = $request->header('x-bid');

        $w['lesson_status'] = ['in',['0','1']];
        $w['og_id'] = gvar('og_id');
        $w['bid'] = ['in',$bids];
        $sids = $model->where($w)->order('sid asc')->column('sid');
        $sids = array_unique($sids);

        $ret = ReportStudentByRemainlessonhour::buildReport($sids);

        if ($ret === false) {
            return $this->sendError(400, $ret);
        }
        return $this->sendSuccess();

	}
}