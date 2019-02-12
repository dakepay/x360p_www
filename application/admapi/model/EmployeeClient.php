<?php
/** 
 * Author: luo
 * Time: 2017-12-16 16:34
**/

namespace app\admapi\model;

class EmployeeClient extends Base
{

    public function delOneRecord($eid, $cid)
    {
        $rs = $this->where('eid', $eid)->where('cid', $cid)->delete();
        if($rs === false) return $this->user_error('删除失败');

        return true;
    }
    
}