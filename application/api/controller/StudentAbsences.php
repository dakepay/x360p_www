<?php
/**
 * Author: luo
 * Time: 2017-11-21 10:15
**/

namespace app\api\controller;


use app\api\model\Student;
use app\api\model\StudentAbsence;
use think\Request;

class StudentAbsences extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_sa = new StudentAbsence();
        if(!empty($get['student_name']) ) {
            $student_name = $get['student_name'];
            $sids = (new Student())->where('student_name|pinyin|pinyin_abbr', 'like', "%{$student_name}%")->column('sid');
            $sids = !empty($sids) ? array_unique($sids) : [-1];
            $m_sa->where('sid', 'in', $sids);
        }

        $ret = $m_sa->getSearchResult($get);;
        
        return $this->sendSuccess($ret);
    }

    /*撤销补课(为了照顾前端撤销补课的接口在缺课对象上进行)*/
    public function do_cancel(Request $request)
    {
        $sa_id = $request->param('id/d');
        $absence = StudentAbsence::get($sa_id);
        if (empty($absence)) {
            return $this->sendError(400, 'resource not found!');
        }
        $rs = $absence->cancelMakeup();
        if (!$rs) {
            return $this->sendError(400, $absence->getError());
        }
        return $this->sendSuccess();
    }

}