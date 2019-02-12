<?php
/** 
 * Author: luo
 * Time: 2017-10-19 09:30
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class OrderRefundHistory extends Base
{

    public function orderRefund()
    {
        return $this->hasOne('OrderRefund', 'or_id', 'or_id')
            ->field('sid,or_id,bill_no,need_refund_amount,refund_balance_amount,cut_amount,refund_amount');
    }

    public function accountingAccount()
    {
        return $this->hasOne('AccountingAccount', 'aa_id', 'aa_id');
    }

    public function createRefundHistory($data) {

        $this->startTrans();

        try {
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if (!$rs) return $this->user_error('退款记录失败');

            $orh_id = $this->getLastInsID();

            $tally_data = $data;
            if(isset($data['pay_time']) && $data['pay_time'] > 0){
                $tally_data['int_day'] = int_day($data['pay_time']);
            }
            $tally_data['type'] = Tally::TALLY_TYPE_PAYOUT;
            $tally_data['relate_id'] = $orh_id;

            $or_id = isset($data['or_id']) ? $data['or_id'] : 0;
            $tally_data['remark'] = Tally::setRemark("refund", ['or_id' => $or_id]);
            $rs = (new Tally())->createOneTally($tally_data);
            if (!$rs) return $this->user_error('流水记录失败');
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

}