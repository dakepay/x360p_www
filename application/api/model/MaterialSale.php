<?php

namespace app\api\model;


class MaterialSale extends Base
{

    public function Material()
    {
        return $this->hasOne('Material','mt_id','mt_id');
    }

    /**
     * 添加物品销售记录
     * @param $data
     * @return bool
     */
    public function addMaterialSale($input)
    {
        $need_fields = ['bid','nums','amount','eid','aa_id','mt_id','ms_id','name','mobile'];
        if (!$this->checkInputParam($input,$need_fields)){
            return false;
        }

        $mMaterial = new Material();
        $material = $mMaterial->where('mt_id',$input['mt_id'])->find();
        if (empty($material)){
            return $this->user_error('购买物品不存在！');
        }

        $mAccountingAccount = new AccountingAccount();
        $account = $mAccountingAccount->where('aa_id',$input['aa_id'])->find();
        if (empty($account)){
            return $this->user_error('会计账户不存在！');
        }

        $this->startTrans();
        try{

            $result = $this->isUpdate(false)->allowField(true)->save($input);
            if (false === $result){
                return $this->sql_add_error('material_sale');
            }

            $result = $this->doMaterial($input['mt_id'],$input['ms_id'],$input['nums']);
            if ($result === false){
                return $this->user_error($this->getError());
            }

            $mts_id = $this->getAttr('mts_id');

            $mEmployeeReceipt = new EmployeeReceipt();
            $receipt_data = [
                'bid' => $input['bid'],
                'eid' => $input['eid'],
                'mts_id' => $mts_id,
                'amount' => $input['amount'],
                'consume_type' => EmployeeReceipt::MATERAL_SALE
            ];
            $result = $mEmployeeReceipt->createOneReceipt($receipt_data);
            if(false === $result){
                return $this->user_error($mEmployeeReceipt->getError());
            }

            $mTally = new Tally();

            $tally_data = [
                'bid' => $input['bid'],
                'aa_id' => $input['aa_id'],
                'amount' => $input['amount'],
                'type' => Tally::TALLY_TYPE_INCOME,
                'relate_id' => $mts_id,
                'employee_th_id' => $input['eid'],
                'remark' => '对外物品销售。购买人：'.$input['name'].' 电话：'.$input['mobile']
            ];
            $result = $mTally->createOneTally($tally_data);
            if (false === $result){
                return $this->user_error($mTally->getError());
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    public function doMaterial($mt_id,$ms_id,$nums)
    {
        $this->startTrans();
        try {
//            $mMsq = new MaterialStoreQty();
//            $is_enough = $mMsq->isEnoughMaterial($ms_id, $mt_id, $nums);
//            if ($is_enough === false){
//                return $this->user_error('库存不足');
//            }

            $material_history_data = [
                'mt_id' => $mt_id,
                'ms_id' => $ms_id,
                'num'   => $nums,
                'int_day' => date('Ymd', time()),
                'type'  => MaterialHistory::TYPE_OUT,
                'cate'  => MaterialHistory::OUT_SALE,
                'remark' => '对外物品销售记录'
            ];

            $mMaterialHistory = new MaterialHistory();
            $result = $mMaterialHistory->addOneHis($material_history_data);
            if (false === $result){
                return $this->user_error($mMaterialHistory->getError());
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 删除物品销售记录开单记录
     * @param $ms_id
     */
    public function delMaterialSale($mts_id)
    {
        $m_material_sale = $this->where('mts_id',$mts_id)->find();
        if (empty($m_material_sale)){
            return $this->user_error('记录不存在');
        }

        $this->startTrans();
        try {
            $material_history_data = [
                'mt_id' => $m_material_sale['mt_id'],
                'ms_id' => $m_material_sale['ms_id'],
                'num'   => $m_material_sale['nums'],
                'int_day' => date('Ymd', time()),
                'type'  => MaterialHistory::TYPE_OUT,
                'cate'  => MaterialHistory::OUT_SALE,
                'remark' => '对外物品销售记录'
            ];
            $mMaterialHistory = new MaterialHistory();
            $result = $mMaterialHistory->addOneHis($material_history_data);
            if (false === $result){
                return $this->user_error($mMaterialHistory->getError());
            }

            $mTally = new Tally();
            $tally_data = [
                'bid' => $m_material_sale['bid'],
                'aa_id' => $m_material_sale['aa_id'],
                'amount' => $m_material_sale['amount'],
                'type' => Tally::TALLY_TYPE_PAYOUT,
                'relate_id' => $mts_id,
                'employee_th_id' => $m_material_sale['eid'],
                'remark' => '撤销对外物品销售'
            ];
            $result = $mTally->createOneTally($tally_data);
            if (false === $result){
                return $this->user_error($mTally->getError());
            }

            $mEmployeeReceipt = new EmployeeReceipt();
            $w_er['mts_id'] = $mts_id;
            $m_employee_receiptt = $mEmployeeReceipt->where($w_er)->find();
            if (empty($m_employee_receiptt)){
                return $this->user_error('业绩记录不存在！');
            }
            $result = $m_employee_receiptt->delete();
            if (false === $result){
                return $this->sql_delete_error('employee_receipt');
            }

            $result = $m_material_sale->delete();
            if (false === $result){
                return $this->sql_delete_error('material_sale');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 修改开单信息
     * @param $mts_id
     * @param $put
     */
    public function updateMaterialSale($mts_id,$put)
    {
        $w['mts_id'] = $mts_id;
        $result = $this->allowField(true)->save($put,$mts_id);

        if (false === $result){
            return $this->sql_save_error('material_sale');
        }

        return true;
    }

}