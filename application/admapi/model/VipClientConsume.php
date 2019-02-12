<?php

namespace app\admapi\model;


class VipClientConsume extends Base
{

    public function app(){
        return $this->hasOne('App', 'app_id', 'app_id');
    }
    public function client(){
        return $this->hasOne('Client', 'cid', 'cid');
    }

    public function addOneClientConsume($data){
        $this->startTrans();
        try {
            $result = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if(!$result){
                $this->rollback();
                return $this->sql_add_error('vip_client_consume');
            }

        }catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        $vcc_id = $this->getAttr('vcc_id');
        return $vcc_id;
    }


}
