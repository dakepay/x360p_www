<?php
/**
 * Author: luo
 * Time: 2017-12-22 10:29
**/

namespace app\sapi\controller;

use app\sapi\model\Employee;
use think\Request;

class Employees extends Base
{
    public function get_detail(Request $request, $id = 0)
    {
        $eid = input('id/d');
        $data = Employee::get($eid, ['profile', 'subjects']);
        //todo 老师评价
        return $this->sendSuccess($data);
    }

}