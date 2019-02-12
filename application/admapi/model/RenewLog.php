<?php
/**
 * Author: luo
 * Time: 2017-12-04 15:00
**/

namespace app\admapi\model;

class RenewLog extends Base
{

    public function setPreDayAttr($value) {
        return $value ? format_int_day($value) : $value;
    }

    public function setNewDayAttr($value) {
        return $value ? format_int_day($value) : $value;
    }

    public function addOneLog($data)
    {
        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) $this->user_error("添加延期记录失败");

        return true;
    }

}