<?php
/** 
 * Author: luo
 * Time: 2017-11-15 17:35
**/

namespace app\api\controller;


use app\api\model\OrderReceiptBill;
use think\Request;

class OrderReceiptBills extends Base
{

    /**
     * @desc  收据列表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $input = $request->get();
        if(isset($input['pay_type'])){
            switch($input['pay_type']){
                case 1:
                    $input['balance_paid_amount'] = ['GT',0];
                    $input['money_paid_amount'] = 0;
                    break;
                case 2:
                    $input['balance_paid_amount'] = 0;
                    $input['money_paid_amount'] = ['GT',0];
                    break;
                case 3:
                    $input['balance_paid_amount'] = ['GT',0];
                    $input['money_paid_amount'] = ['GT',0];
                    break;

            }
            unset($input['pay_type']);
        }
        $model = new OrderReceiptBill();
        $ret = $model->with(['order_receipt_bill_item' => ['order_item' => ['student_lesson', 'material']],
            'order_payment_history', 'employee'])
            ->order('create_time desc')->withSum('amount,money_paid_amount,balance_paid_amount')->getSearchResult($input);


        return $this->sendSuccess($ret);
    }

    /**
     * @desc  收据详情
     * @author luo
     * @param Request $request
     * @param int $id
     * @method GET
     */
    public function get_detail(Request $request, $id = 0)
    {
        $orb_id = $id;
        if($orb_id <= 0) return $this->sendError(400, 'param error');

        $m_orb = new OrderReceiptBill();
        $bill = $m_orb->with(['employeeReceipt','order_receipt_bill_item' => ['order_item' => ['student_lesson.one_class', 'material']],
            'order_payment_history', 'employee', 'student'])->find($orb_id);
        return $this->sendSuccess($bill);
    }

    /**
     * @desc  修改收据号
     * @param Request $request
     * @param int $id
     * @method PUT
     */
    public function put(Request $request)
    {
        $orb_id = input('id');
        $m_orb = new OrderReceiptBill();
        $input = $request->put();

        $result = $m_orb->updateReceipt($orb_id,$input);
        if (!$result){
            return $this->sendError(400, $m_orb->getError());
        }
        return $this->sendSuccess($result);
    }

    /**
     * @desc  报废缴款
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {

        $orb_id = input('id');
        $m_orb = new OrderReceiptBill();
        $rs = $m_orb->delOneBill($orb_id);
        if($rs === false) {
            if($m_orb->get_error_code() == $m_orb::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($m_orb->getErrorMsg());
            }
            return $this->sendError(400, $m_orb->getErrorMsg());
        }

        return $this->sendSuccess();
    }




}