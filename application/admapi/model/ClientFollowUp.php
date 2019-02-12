<?php
/**
 * Author: luo
 * Time: 2017-12-09 14:19
**/

namespace app\admapi\model;

use think\Exception;

class ClientFollowUp extends Base
{

    public function setFollowDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function setNextFollowDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }


    public function client()
    {
        return $this->belongsTo('Client', 'cid', 'cid');
    }

    //添加一条跟进
    public function addOneFollowUp($data)
    {
        $rs = $this->validateData($data, 'ClientFollowUp');
        if($rs !== true) exception($this->getErrorMsg());

        $this->startTrans();
        try {
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if ($rs === false) return $this->user_error('添加跟进失败');

            $m_client = new Client();
            $m_client->where('cid', $data['cid'])->setInc('follow_times');

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

}