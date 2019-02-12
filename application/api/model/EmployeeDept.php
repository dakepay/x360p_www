<?php
/**
 * Author: luo
 * Time: 2017-11-28 15:42
**/

namespace app\api\model;

class EmployeeDept extends Base
{

    public function employee()
    {
        return $this->hasMany('Employee', 'eid', 'eid');
    }

}