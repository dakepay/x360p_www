<?php
/**
 * Author: luo
 * Time: 2018/4/13 17:18
 */

namespace app\sapi\controller;


use app\sapi\model\StudentExamScore;
use think\Request;

class StudentExamScores extends Base
{

    public function get_list(Request $request)
    {
        $sid = global_sid();
        if($sid <= 0) return $this->sendError(400, 'sid error');

        $get = $request->get();

        $m_ses = new StudentExamScore();
        $ret = $m_ses->where('sid', $sid)->getSearchResult($get);

        return $this->sendSuccess($ret);
    }

}