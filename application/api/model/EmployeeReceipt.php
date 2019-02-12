<?php
/**
 * Author: luo
 * Time: 2017-11-03 20:28
**/

namespace app\api\model;

class EmployeeReceipt extends Base
{

    const MATERAL_SALE = 4; //对外出售物品
    protected $hidden = [
        'update_time',
        'is_delete',
        'delete_time',
        'delete_uid'
    ];

    public static $detail_fields = [
        ['type'=>'index','width'=>60,'align'=>'center'],
        ['title'=>'校区','key'=>'bid','align'=>'center'],
        ['title'=>'员工姓名','key'=>'eid','align'=>'center'],
        ['title'=>'签约角色','key'=>'sale_role_did','align'=>'center'],
        ['title'=>'学员姓名','key'=>'sid','align'=>'center'],
        ['title'=>'签约时间','key'=>'receipt_time','width'=>120,'align'=>'center'],
        ['title'=>'签约金额','key'=>'amount','align'=>'center'],
    ];

    public static $detail_refund_fields = [
        ['type'=>'index','width'=>60,'align'=>'center'],
        ['title'=>'校区','key'=>'bid','align'=>'center'],
        ['title'=>'学员姓名','key'=>'sid','align'=>'center'],
        ['title'=>'退款时间','key'=>'receipt_time','width'=>120,'align'=>'center'],
        ['title'=>'退款金额','key'=>'amount','align'=>'center'],
    ];

    protected $type = [
        'receipt_time' => 'timestamp',
    ];

    public function orderReceiptBill()
    {
        return $this->hasOne('OrderReceiptBill', 'orb_id', 'orb_id');
    }

    public function orderRefund()
    {
        return $this->hasOne('OrderRefund', 'or_id', 'or_id');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid');
    }

    public function materialSale()
    {
        return $this->hasOne('MaterialSale','mts_id','mts_id');
    }

    //创建一条员工业绩
    public function createOneReceipt($data)
    {
        if(isset($data['orb_id'])){
            $mOrderReceiptBill = new OrderReceiptBill;
            $m_orb = $mOrderReceiptBill->find($data['orb_id']);
            if(!$m_orb){
                return $this->user_error('收据信息不存在!');
            }

            $data['oid'] = $m_orb['oid'];
            $data['receipt_time'] = $m_orb['paid_time'];
            $data['sid'] = $m_orb['sid'];
        }
        if(!isset($data['receipt_time']) || $data['receipt_time'] <= 0) $data['receipt_time'] = time();
        if(!isset($data['consume_type']) && isset($data['oid']) && $data['oid'] > 0){
            $consume_type  = 0;
            $w_oi['oid'] = $data['oid'];
            $w_oi['gtype'] = ['in',[0,2]];
            $oi_list = get_table_list('order_item',$w_oi);
            if($oi_list){
                foreach($oi_list as $oi){
                    if($oi['consume_type'] > 0 ){
                        $consume_type = $oi['consume_type'];
                        break;
                    }
                }
            }
            if($consume_type > 0){
                $data['consume_type'] = $consume_type;
            }
        }
        $rs = (new self())->allowField(true)->isUpdate(false)->save($data);
        if(false === $rs) return $this->user_error('创建员工回款业绩失败');

        return true;
    }

    /**
     * 添加员工退款业绩
     * @param OrderRefund $order_refund
     * @param $eid
     * @param $sale_role_did
     * @param $amount
     * @return bool
     */
    public function createOneRefund(OrderRefund $order_refund,$eid,$sale_role_did,$amount)
    {

        $data['or_id'] = $order_refund['or_id'];
        $data['oid'] = $order_refund['oid'];
        $data['receipt_time'] = $order_refund['create_time'];
        $data['sid'] = $order_refund['sid'];
        $data['eid'] = $eid;
        $data['sale_role_did'] = $sale_role_did;
        $data['amount'] = $amount;
        $data['consume_type'] = 4;

        $result = $this->allowField(true)->isUpdate(false)->save($data);
        if(false === $result) return $this->user_error('创建员工退款业绩失败');

        return true;
    }

    public function updateConsumeType($erc_id,$update){
        $w['erc_id'] = $erc_id;
        $rs = $this->save($update,$w);
        if(!$rs) return $this->user_error('修改收费类型失败');

        return true;
    }

    /**
     * 修改员销售业绩
     * @param $oid
     * @param $update_amount
     */
    public function updateReceiptAmount($oid, $update_amount)
    {
        $employee_receipt_list = $this->where('oid',$oid)->select();
        if (empty($employee_receipt_list)){
            foreach ($employee_receipt_list as $employee_receipt){
                $update['amount'] = $employee_receipt['amount'] + $update_amount;
                $w['erc_id'] = $employee_receipt['erc_id'];
                $result = $this->save($update,$w);
                if (false === $result){
                    return $this->sql_save_error('employee_receipt');
                }
            }
        }

        return true;
    }

}