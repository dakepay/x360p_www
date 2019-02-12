<?php
namespace app\admapi\model;

class ClientApplySqbCheck extends Base
{
    public function addSqbCheck($cas_id,$cid,$check_messages){

        $this->startTrans();
        try {
            $data = [
                'cas_id' => $cas_id,
                'cid' => $cid,
                'check_messages' => $check_messages,
            ];

            $rs = $this->isUpdate(false)->allowField(true)->save($data);
            if (false === $rs){
                return $this->sql_add_error('client_apply_sqb_check');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

}