<?php
/**
 * Author: luo
 * Time: 2017-12-20 10:15
**/

namespace app\sapi\controller;

use app\sapi\model\StudentAttendance;
use think\Request;

class StudentAttendances extends Base
{

    public function get_list(Request $request)
    {
        $sid = global_sid();
        $input = $request->get();

        $m_sa = new StudentAttendance();
        $ret = $m_sa->with(['courseArrange' => ['lesson', 'oneClass']])->where('sid', $sid)->order('int_day', 'DESC')->getSearchResult($input);
        $list = [];
        foreach ($ret['list'] as $row) {
            $month = date('Y-m', strtotime($row['int_day']));
            $list[$month][] = $row;
        }


        $ret['list'] = $list;
        return $this->sendSuccess($ret);
    }


}