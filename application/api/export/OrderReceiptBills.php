<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\OrderReceiptBill;
use app\api\model\OrderItem;
use app\api\model\OrderPerformance;
use app\api\model\OrderPaymentHistory;
use app\api\model\AccountingAccount;


class OrderReceiptBills extends Export
{
    protected $res_name = 'order_receipt_bill';

    protected $columns = [
        ['field'=>'paid_time','title'=>'缴费日期','width'=>20],
        ['field'=>'orb_no','title'=>'收据编号','width'=>20],
        ['field'=>'s_no','title'=>'学号','width'=>20],
        ['field'=>'s_name','title'=>'学员','width'=>20],
        ['field'=>'amount','title'=>'缴费金额','width'=>20],
        ['field'=>'item_name','title'=>'缴费项目','width'=>40],
        ['field'=>'accounting_account','title'=>'收款账户','width'=>30],
        ['field'=>'eid','title'=>'回款业绩归属','width'=>30],
        ['field'=>'create_time','title'=>'录入时间','width'=>20],
        ['field'=>'create_uid','title'=>'经办人','width'=>20],
    ];

    protected function get_title(){
        $title = '缴费记录';
        return $title;
    }
    
    protected function get_eid($oid)
    {
        $op_info = OrderPerformance::get(['oid'=>$oid]);
        $ename = get_employee_name($op_info['eid']);
        $role = get_did_value($op_info['sale_role_did']);
        return $ename.'-'.$role;
    }

    protected function get_accounting_account($orb_id,$amount)
    {
        $oph_info = OrderPaymentHistory::get(['orb_id'=>$orb_id]);
        $w['orb_id'] = $orb_id;
        $w['amount'] = ['gt' ,0];
        $ophs = model('order_payment_history')->where($w)->field('aa_id,amount,sid')->select();

        $arr = [];
        foreach ($ophs as $k => $oph) {
            $a_info = AccountingAccount::get($oph['aa_id']);
            $arr[$k]['name'] = $a_info['name'];
            $arr[$k]['amount'] = $oph['amount'];
        }

        $string = '';
        foreach ($arr as $item) {
            $string .= $item['name'].':'.$item['amount'].' ';
        }

        return $string;
    }

    public function get_data()
    {
        $model = new OrderReceiptBill();
        $result = $model->order('create_time desc')->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $oi_info = OrderItem::get(['oid'=>$v['oid']]);
            $list[$k]['create_time'] = date('Y-m-d',strtotime($v['create_time']));
            $list[$k]['item_name'] = get_lesson_name($oi_info['lid']);
            $list[$k]['s_name'] = get_student_name($v['sid']);
            $list[$k]['s_no']   = get_student_no($v['sid']);
            $list[$k]['create_uid'] = get_employee_name($v['create_uid']);
            $list[$k]['accounting_account'] = $this->get_accounting_account($v['orb_id'],$v['amount']);
            $list[$k]['eid'] = $this->get_eid($v['oid']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}