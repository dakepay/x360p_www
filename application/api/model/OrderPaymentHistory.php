<?php
/** 
 * Author: luo
 * Time: 2017-10-14 12:04
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class OrderPaymentHistory extends Base
{

    protected $hidden = [
        'update_time', 
        'is_delete', 
        'delete_time', 
        'delete_uid'
    ];

    public static $detail_fields = [
        ['type'=>'index','width'=>60,'align'=>'center'],
        ['title'=>'校区','key'=>'bid','align'=>'center'],
        ['title'=>'学员姓名','key'=>'sid','align'=>'center'],
        ['title'=>'签约员工','key'=>'eid','align'=>'center'],
        ['title'=>'签约时间','key'=>'create_time','align'=>'center'],
        ['title'=>'签约金额','key'=>'amount','align'=>'center'],
    ];

    protected function setPaidTimeAttr($value)
    {
        if(is_numeric($value)){
            return $value;
        }
        return strtotime($value);
    }

    protected function getPaidTimeAttr($value)
    {
        return $value ? date('Y-m-d', $value) : $value;
    }

    public function orderReceiptBill()
    {
        return $this->hasOne('OrderReceiptBill', 'orb_id', 'orb_id');
    }

    public function accountingAccount()
    {
        return $this->hasOne('AccountingAccount', 'aa_id', 'aa_id');
    }

    public function setIsDemoAttr($value,$data){
        if($value == 1){
            return $value;
        }
        $is_demo = 0;
        if(!empty($data['oid'])) {
            $order_info = get_order_info($data['oid']);
            $is_demo = !empty($order_info) ? $order_info['is_demo'] : 0;
        }
        return $is_demo;
    }

    //帐户收款记录
    public function createOneHistory($data) {

        $this->startTrans();
        try {
            $result = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if(!$result){
                $this->rollback();
                return $this->sql_add_error('order_payment_history');
            }
           
            $oph_id = $this->getLastInsID();

            if(isset($data['opo_id']) && $data['opo_id'] > 0) {
                $m_opo = new OrderPaymentOnline();
                $result = $m_opo->updatePayment($data['opo_id'], null, ['oph_id' => $oph_id]);
                if($result === false) {
                    $this->rollback();
                    return $this->user_error($m_opo->getErrorMsg());
                }
            }

            if($data['amount'] > 0) {
                $tally_data = $data;
                $tally_data['int_day'] = date('Ymd', $data['paid_time']);
                $tally_data['relate_id'] = $oph_id;
                $tally_data['type'] = Tally::TALLY_TYPE_INCOME;
                $m_tally = new Tally();
                $result = $m_tally->createOneTally($tally_data);
                if (!$result) {
                    $this->rollback();
                    return $this->user_error($m_tally->getError());
                }
            }
            
        }  catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return $result;
    }

    /**
     * 修改付款金额
     * @param $oid
     * @param $update_amount
     */
    public function updateAmount($orb_id,$update_amount)
    {
        $payment_history = $this->where('orb_id',$orb_id)->find();
        if (empty($payment_history)){
            return $this->user_error('付款记录不存在！');
        }

        $update['amount'] = $payment_history['amount'] + $update_amount;
        $w['orb_id'] = $orb_id;
        $result = $this->save($update,$w);
        if (false === $result){
            return $this->sql_save_error('order_payment_history');
        }

        $mTally = new Tally();
        $result = $mTally->updateAmount($payment_history,$update_amount);
        if (false === $result){
            return $this->user_error($mTally->getError());
        }

        return true;
    }

}