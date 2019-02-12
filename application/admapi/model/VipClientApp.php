<?php

namespace app\admapi\model;

use think\Exception;


class VipClientApp extends Base
{
    const VIP_CLIENT_APP_OFF = 0;
    const VIP_CLIENT_APP_ON = 1;


    /**
     * 添加一条客户购买APP记录
     * @return bool
     */
    public function addOneClientApp($data){
        $this->startTrans();
        try {
            $result = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if(!$result){
                $this->rollback();
                return $this->sql_add_error('vip_client_app');
            }

        }catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 更新客户APP记录
     * @return bool
     */
    public function updateClientApp($vca_id,$data){
        $w_vca['vca_id'] = $vca_id;
        $this->startTrans();
        try {
            $result = $this->save($data,$w_vca);
            if(!$result){
                $this->rollback();
                return $this->sql_add_error('vip_client_app');
            }

        }catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 删除客户APP记录
     * @return bool
     */
    public function delClientApp($vca_id){
        if(empty($vca_id)){
            return $this->user_error('vca_id empty!');
        }
        $w['area_id'] = $vca_id;
        $rs = $this->where($w)->delete(true);
        if(false === $rs){
            return $this->sql_save_error('vip_client_app');
        }
        return true;
    }

}
