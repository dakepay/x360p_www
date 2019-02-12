<?php

namespace app\admapi\model;

class EmployeePerformance extends Base
{


    /**
     * 添加绩效
     * @param  Customer $customer [description]
     * @return [type]             [description]
     */
    public function addOneEmployeePerformance($data){

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
        return true;
    }

    /**
     * 批量添加绩效
     * @param  Customer $customer [description]
     * @return [type]             [description]
     */
    public function batAddEmployeePerformance($eids,$data){
        if (empty($eids) || !is_array($eids)) {
            $this->user_error('请选择eid');
        }
        $this->startTrans();
        try {
            foreach ($eids as $eid) {
                $data['eid'] = $eid;
                $result = $this->addOneEmployeePerformance($data);
                if (!$result) {
                    $this->rollback();
                    return false;
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

}
