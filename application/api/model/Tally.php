<?php
/**
 * Author: luo
 * Time: 2017-11-16 18:26
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class Tally extends Base
{
    const TALLY_TYPE_INCOME = 1;
    const TALLY_TYPE_PAYOUT = 2;

    const CATE_INCOME = 1;
    const CATE_PAYOUT = 2;
    const CATE_TRANSFER = 3;
    const CATE_RECEIVABLE = 4;
    const CATE_PAYABLE = 5;

    protected $insert = ['cate','int_day'];
    protected $append = ['create_eid'];

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];


    public function setIsDemoAttr($value,$data){
        if($value == 1){
            return $value;
        }
        $is_demo = 0;
        if(!empty($data['relate_id']) and $data['type'] == 1) {

            $oph_info = get_oph_info($data['relate_id']);
            $is_demo = !empty($oph_info) ? $oph_info['is_demo'] : 0;
        }
        return $is_demo;
    }

    //流水大类：1收入，2支出，3转账，4应收/应付
    public function setCateAttr($value, $data)
    {
        if(!$value && isset($data['type'])) {
            $value = $data['type'];
        }

        return $value ? $value : 0;
    }

    //业务日期
    public function setIntDayAttr($value)
    {
        if(!$value) $value = date('Ymd', time());
        return format_int_day($value);
    }

    public function getCreateEidAttr($value, $data)
    {
        if(isset($data['create_uid'])) {
            return User::getEidByUid($data['create_uid']);
        }
        return 0;
    }

    public function orderPaymentHistory()
    {
        return $this->hasOne('OrderPaymentHistory', 'oph_id', 'relate_id')
            ->field('oph_id,orb_id,aa_id,amount');
    }

    public function orderRefundHistory()
    {
        return $this->hasOne('OrderRefundHistory', 'orh_id', 'relate_id')
            ->field('orh_id,or_id,aa_id,amount');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name');
    }

    public function tallyFile()
    {
        return $this->hasMany('TallyFile','tid','tid');
    }

    /**
     * 批量创建记账
     * @param $inputs
     * @return array|bool
     */
    public function batCreateTally($inputs){
        $this->startTrans();
        try{
            foreach($inputs as $input){
                $tally_file = $input['tally_file'];
                $result = $this->createOneTally($input,$tally_file);
                if(!$result){
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

    //帐户流水，并增减帐户金额
    public function createOneTally($data, $tally_file = [])
    {
        $this->startTrans();
        try {

            $result = $this->validateData($data, 'Tally');
            if(false === $result){
                return $this->user_error($this->getError());
            }

            if ($data['type'] == self::TALLY_TYPE_INCOME) {
                (new AccountingAccount())->where('aa_id', $data['aa_id'])->setInc('amount', $data['amount']);
            } elseif ($data['type'] == self::TALLY_TYPE_PAYOUT) {
                (new AccountingAccount())->where('aa_id', $data['aa_id'])->setDec('amount', $data['amount']);
            }

            $result = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if(false === $result){
                return $this->sql_add_error('tally');
            }
            $tid = $this->getLastInsID();

            if(isset($data['to_aa_id']) && !empty($data['to_aa_id'])) {
                $result = $this->addInvolvedTally($tid);
                if(false === $result){
                    return $this->user_error($this->getError());
                }
            }

            if (!empty($tally_file)) {
                $mFile = new File();
                $mTallyFile = new TallyFile();
                foreach ($tally_file as $per_file) {
                    if(empty($per_file['file_id'])) {
                        log_write($per_file, 'error');
                        continue;
                    }
                    $file = $mFile->find($per_file['file_id']);
                    $file = $file ? $file->toArray() : [];
                    $per_file = array_merge($per_file, $file);
                    $per_file['tid'] = $tid;
                    $result = $mTallyFile->data([])->isUpdate(false)->allowField(true)->save($per_file);
                    if (false === $result){
                        return $this->sql_add_error('tall_file');
                    }
                }
            }


        } catch (\Exception $e) {
            $this->rollback();
            return $this-$this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    //创建往来帐户的流水
    public function addInvolvedTally($tid)
    {
        $tally = $this->find($tid);
        $opposite_data = $tally->toArray();

        //--1-- 往来账对方的类型相反
        $data['type'] = $opposite_data['type'] === self::TALLY_TYPE_INCOME ? self::TALLY_TYPE_PAYOUT : self::TALLY_TYPE_INCOME;
        $data['aa_id'] = $opposite_data['to_aa_id'];
        $data['to_aa_id'] = $opposite_data['aa_id'];
        $data = array_merge($opposite_data, $data);
        unset($data['tid']);

        $this->startTrans();
        try {

            //--2-- 处理账户总额
            if ($data['type'] == self::TALLY_TYPE_INCOME) {
                (new AccountingAccount())->where('aa_id', $data['aa_id'])->setInc('amount', $data['amount']);
            } elseif ($data['type'] == self::TALLY_TYPE_PAYOUT) {
                (new AccountingAccount())->where('aa_id', $data['aa_id'])->setDec('amount', $data['amount']);
            }

            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if (!$rs) exception('记录往来账失败');
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //设置自动流水的备注
    public static function setRemark($type, $data)
    {
        $remark = '';
        if($type == "payment") {
            $orb_id = isset($data['orb_id']) ? $data['orb_id'] : 0;
            $orb_no = OrderReceiptBill::getOrbNoByOrbId($orb_id);
            $remark = '订单付款流水，收据号：'.$orb_no;
        }

        if($type == 'refund') {
            $or_id = isset($data['or_id']) ? $data['or_id'] : 0;
            $bill_no = OrderRefund::getBillNoByOrId($or_id);
            $remark = '订单退款款流水，收据号：'.$bill_no;
        }

        return $remark;
    }

    /**
     * 删除业务关联的流水记录
     * @param $tid
     */
    public function delBusinessTally()
    {
        return $this->delTally(false);
    }

    /**
     * 撤销无业务关联的流水
     * @return bool
     */
    public function delTally($check_relate = true)
    {
        if(empty($this->getData())) return $this->user_error('流水账数据错误');

        $tally_data = $this->getData();
        if($check_relate) {
            if ($tally_data['relate_id'] > 0) return $this->user_error('无法撤销业务相关的流水账');
        }

        try {
            $this->startTrans();
            $m_aa = new AccountingAccount();
            if($tally_data['aa_id'] > 0) {
                if($tally_data['type'] == self::TALLY_TYPE_INCOME) {
                    $rs = $m_aa->where('aa_id', $tally_data['aa_id'])->setDec('amount', $tally_data['amount']);
                    if($rs === false) throw new FailResult($m_aa->getErrorMsg());
                }

                if($tally_data['type'] == self::TALLY_TYPE_PAYOUT) {
                    $rs = $m_aa->where('aa_id', $tally_data['aa_id'])->setInc('amount', $tally_data['amount']);
                    if($rs === false) throw new FailResult($m_aa->getErrorMsg());
                }
            }

            if($tally_data['to_aa_id'] > 0) {
                if($tally_data['type'] == self::TALLY_TYPE_INCOME) {
                    $rs = $m_aa->where('aa_id', $tally_data['to_aa_id'])->setInc('amount', $tally_data['amount']);
                    if($rs === false) throw new FailResult($m_aa->getErrorMsg());
                }

                if($tally_data['type'] == self::TALLY_TYPE_PAYOUT) {
                    $rs = $m_aa->where('aa_id', $tally_data['to_aa_id'])->setDec('amount', $tally_data['amount']);
                    if($rs === false) throw new FailResult($m_aa->getErrorMsg());
                }
            }

            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    /**
     * 编辑收据条目
     * @param $input
     */
    public function editTally($input){
        $allow_edit_fields = ['cate','tt_id','item_th_id','client_th_id','employee_th_id','remark','int_day'];

        $old_tally = $this->getData();

        //修改收款账号
        if(isset($input['aa_id']) && $input['aa_id'] != $old_tally['aa_id']){
            return $this->changeAccountingAccount($input['aa_id']);
        }

        $update = [];

        foreach($allow_edit_fields as $f){
            if(isset($input[$f]) && $input[$f] != $old_tally[$f]){
                $update[$f] = $input[$f];
            }
        }

        if (empty($update)) {
            return true;
        }

        $result = $this->isUpdate(true)->save($update);

        if (false === $result) {
            $this->rollback();
            return $this->sql_save_error('tally');
        }
        return true;
    }

    /**
     * 修改收款账号
     * @param $new_aa_id
     */
    public function changeAccountingAccount($new_aa_id){
        $old_tally = $this->getData();

        $cate = $old_tally['cate'];
        $amount = $old_tally['amount'];

        $m_old_aa = AccountingAccount::get($old_tally['aa_id']);

        if(!$m_old_aa){
            return $this->user_error('源收款账号不存在！');
        }
        $m_new_aa = AccountingAccount::get($new_aa_id);

        if(!$m_new_aa){
            return $this->user_error('新收款账号不存在!');
        }


        $this->startTrans();
        try {
            if ($cate == self::CATE_INCOME) {
                $result = $m_old_aa->setDec('amount', $amount);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('accounting_account');
                }
                $result = $m_new_aa->setInc('amount', $amount);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('accounting_account');
                }
            }elseif($cate == self::CATE_PAYOUT){
                $result = $m_old_aa->setInc('amount',$amount);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('accounting_account');
                }
                $result = $m_new_aa->setDec('amount',$amount);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('accounting_account');
                }
            }

            $this->aa_id = $new_aa_id;

            $result = $this->save();

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('tally');
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 修改帐户流水
     * @param OrderPaymentHistory $
     * @param $update_amount
     */
    public function updateAmount(OrderPaymentHistory $payment_history,$update_amount)
    {
        $w_t = [
            'type' =>   Tally::TALLY_TYPE_INCOME,
            'relate_id' => $payment_history['oph_id'],
            'int_day' =>  format_int_day($payment_history['paid_time']),
            'aa_id' => $payment_history['aa_id'],
            'sid' => $payment_history['sid']
        ];
        $tally = $this->where($w_t)->find();
        if (empty($tally)){
            return $this->user_error('流水不存在！');
        }

        $update['amount'] = $tally['amount'] + $update_amount;
        $w['tid'] = $tally['tid'];
        $result = $this->save($update,$w);
        if (false === $result){
            return $this->sql_save_error('tally');
        }
    }

}