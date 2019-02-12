<?php
/**
 * Author: luo
 * Time: 2017-12-13 10:47
**/

namespace app\admapi\model;

use think\Exception;

class CustomerFollowUp extends Base
{
    const SYSTEM_OP_TYPE_IN_PS = 1;
    const SYSTEM_OP_TYPE_OUT_PS = 2;

    public function setNextFollowDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function setFaceIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function customer()
    {
        return $this->belongsTo('Customer', 'cu_id', 'cu_id');
    }

    //添加一条跟进
    public function addOneFollowUp($data)
    {
        $rs = $this->validateData($data, 'CustomerFollowUp');
        if($rs !== true) exception($this->getErrorMsg());

        $this->startTrans();
        try {
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if ($rs === false) return $this->user_error('添加跟进失败');

            $m_customer = new Customer();
            $m_customer->where('cu_id', $data['cu_id'])->setInc('follow_times');

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

}