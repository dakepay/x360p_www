<?php
/**
 * Author: luo
 * Time: 2017-11-24 18:19
**/

namespace app\api\model;

use think\Exception;

class EmployeeDimission extends Base
{

    public function setIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function addOneDimission($data)
    {
        $this->startTrans();
        try {

            if (!isset($data['eid'])) return $this->user_error('员工ID错误');
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if(!$rs) exception($this->getErrorMsg());

            $rs = (new Employee())->where('eid', $data['eid'])->update(['is_on_job' => 0]);
            if($rs === false) exception($this->getErrorMsg());

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

}