<?php
/** 
 * Author: luo
 * Time: 2017-12-13 10:30
**/

namespace app\admapi\model;

use app\common\exception\FailResult;
use think\Exception;

class Customer extends Base
{

    public function addOneCustomer($data)
    {
        $rs = $this->validateData($data, 'Customer');
        if($rs !== true) return false;

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) exception('增加失败');

        return true;
    }

    public function updateCustomer(Customer $customer, $data)
    {
        $rs = $customer->allowField(true)->isUpdate(true)->save($data);
        if($rs === false) return $this->user_error('更新失败');

        return true;
    }

    public function deleteCustomer(Customer $customer)
    {
        $had_record = CustomerFollowUp::get(['cu_id' => $customer->cu_id]);
        if($had_record) return $this->user_error('有跟进记录不能删除');

        $rs = $customer->delete();
        if(!$rs) return $this->user_error('删除失败');

        return true;
    }

    public function updateEidOfManyCustomer($cu_ids, $eid)
    {
        $this->startTrans();
        try {
            foreach ($cu_ids as $cu_id) {
                $customer = $this->findOrFail($cu_id);
                if ($customer->eid > 0) throw new FailResult('客户已经分配给其他员工');

                $customer->eid = $eid;
                $rs = $customer->save();
                if ($rs === false) throw new Exception('分配失败');
            }
            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    /**
     * 客户实例自动转入公海
     * @return bool
     */
    public function autoInPublicSea(){
        return $this->intoPublicSea(0,1);
    }


    /**
     * @param int $cu_id
     * @param int $is_auto 是否自动转入
     * @return bool
     */
    public function intoPublicSea($cu_id = 0,$is_auto = 0){
        if($cu_id > 0){
            $cu_info = get_customer_info($cu_id);
        }else{
            $cu_info = $this->getData();
            $cu_id = $cu_info['cu_id'];
        }

        if(!$cu_info || !$cu_id){
            return $this->user_error('cu_id error!');
        }

        $name = '-';
        $now_time = time();

        if($cu_info['eid'] > 0){
            $name = get_employee_name_center($cu_info['eid']);
        }

        $note  = sprintf('由 %s => 转入公海',$name);

        if($is_auto == 1){
            $note .= '[系统自动]';
        }else{
            $note = '[操作]'.$note;
        }

        $data = [
            'cu_id' => $cu_id,
            'eid'   => $cu_info['eid'],
            'is_system' => 1,
            'system_op_type' => CustomerFollowUp::SYSTEM_OP_TYPE_IN_PS,
            'content' => $note,
        ];

        $this->startTrans();
        try {
            $m_cfu = new CustomerFollowUp();
            $result = $m_cfu->addOneFollowUp($data);

            if (!$result) {
                $this->rollback();
                return $this->user_error('跟进记录添加失败:' . $m_cfu->getError());
            }

            $update_cu = [
                'is_public' => 1,
                'in_public_time' => $now_time,
            ];

            $w_cu_update['cu_id'] = $cu_id;

            $m_cu = new self();

            $result = $m_cu->save($update_cu,$w_cu_update);

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('customer');
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 批量转入公海客户
     * @param  Customer $customer [description]
     * @return [type]             [description]
     */
    public function batIntoPublicSea($cu_ids = []){
        if (empty($cu_ids) || !is_array($cu_ids)) {
            $this->user_error('请选择转入公海客户ID');
        }
        $this->startTrans();
        try {
            foreach ($cu_ids as $cu_id) {
                $result = $this->intoPublicSea($cu_id);
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

    /**
     * 转出公海客户
     * @param $eid
     * @param int $cu_id
     * @param int $is_rob 是否抢占
     * @return bool
     */
    public function outPublicSea($eid,$cu_id = 0,$is_rob = 1){
        if(!$eid){
            return $this->user_error('请选择要转入的员工');
        }

        if($cu_id > 0){
            $cu_info = get_customer_info($cu_id);
        }else{
            $cu_info = $this->getData();
            $cu_id = $cu_info['cu_id'];
        }

        if(!$cu_info || !$cu_id){
            return $this->user_error('cu_id error!');
        }


        $name = '-';
        $now_time = time();

        if($cu_info['eid'] > 0){
            $name = get_employee_name_center($cu_info['eid']);
        }

        $note  = sprintf('由 公海 转给 => %s',$name);

        if($is_rob == 1){
            $note .= '[抢占]';
        }else{
            $note .= '[分配]';
        }

        $cfu_data = [
            'cu_id' => $cu_id,
            'eid' => $eid,
            'is_system' => 1,
            'system_op_type' => CustomerFollowUp::SYSTEM_OP_TYPE_OUT_PS,
            'content' => $note,
        ];

        $this->startTrans();
        try {

            $m_cfu = new CustomerFollowUp();
            $result = $m_cfu->addOneFollowUp($cfu_data);
            if (!$result) {
                return $this->user_error('跟进记录添加失败:'.$m_cfu->getError());
            }

            $update_cu = [
                'eid' => $eid,
                'is_public' => 0,
                'in_public_time' => 0,
            ];

            $w_cu_update['cu_id'] = $cu_id;

            $m_cu = new self();

            $result = $m_cu->save($update_cu,$w_cu_update);

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('customer');
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 批量转出公海客户
     * @param  Customer $customer [description]
     * @return [type]             [description]
     */
    public function batOutPublicSea($eid,$cu_ids = []){
        if(!$eid){
            return $this->input_param_error('eid');
        }
        if(empty($cu_ids)){
            return $this->input_param_error('cu_ids');
        }
        $this->startTrans();
        try {
            foreach ($cu_ids as $cu_id) {
                $rs = $this->outPublicSea($eid,$cu_id,0);
                if (!$rs) {
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

    /**
     * 抢占公海客户
     * @param  Customer $customer [description]
     * @return [type]             [description]
     */
    public function robPublicSea($cu_ids = []){
        if (empty($cu_ids) || !is_array($cu_ids)) {
            return $this->input_param_error('cu_ids');
        }
        $my_eid = gvar('uid');
        $cu_params = config('params.customer');

//        $w_cu['is_reg'] = 0;
        $w_cu['eid'] = $my_eid;

        $my_unreg_cu_count = $this->where($w_cu)->count();

        $this_cu_count = count($cu_ids);

        if($my_unreg_cu_count + $this_cu_count > $cu_params){
            return $this->user_error('您当前未成交的客户数量超出限制，不允许再抢占这么多公海客户!');

        }
        $this->startTrans();
        try {
            foreach ($cu_ids as $cu_id) {
                $result = $this->outPublicSea($my_eid,$cu_id);
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