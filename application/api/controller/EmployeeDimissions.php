<?php
/**
 * Author: luo
 * Time: 2017-11-24 18:20
**/

namespace app\api\controller;

use app\api\model\EmployeeDimission;
use think\Request;

class EmployeeDimissions extends Base
{

    public function post(Request $request)
    {
        $input = $request->post();
        $m_dimission = new EmployeeDimission();
        $rs = $m_dimission->addOneDimission($input);
        if(!$rs) return $this->sendError(400, $m_dimission->getErrorMsg());

        return $this->sendSuccess();
    }

}