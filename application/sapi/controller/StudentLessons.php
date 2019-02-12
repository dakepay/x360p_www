<?php
/**
 * Author: luo
 * Time: 2017-12-21 09:37
**/

namespace app\sapi\controller;

use app\sapi\model\StudentLesson;
use think\Request;

class StudentLessons extends Base
{

    /**
     * @desc  学生订单课程
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $sid = global_sid();
        $input = $request->get();
        $m_sl = new StudentLesson();
        $ret = $m_sl->with(['lesson', 'oneClass'])->where('sid', $sid)->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

}