<?php

namespace app\ftapi\controller;

use think\Request;
use app\ftapi\model\ClassAttendance;

class ClassAttendances extends Base
{
    public function get_list(Request $request)
    {

        $input = $request->get();
        $mClassAttendance = new ClassAttendance();

        $eid = global_eid();
        $employee_info = get_employee_info($eid);
        $w_ca['eid'] = $employee_info['eid'];

        $ret = $mClassAttendance->where($w_ca)->with(['students' => ['student']])->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $catt_id = input('id/d');
        $class_attendance = ClassAttendance::get($catt_id, ['ft_review','students' => ['student']]);
        return $this->sendSuccess($class_attendance);
    }


}