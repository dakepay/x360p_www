<?php
/**
 * Author: luo
 * Time: 2018-01-06 11:02
**/

namespace app\api\controller;

use app\api\model\EmployeeReceipt;
use app\api\model\OrderReceiptBill;
use app\api\model\OrderRefund;
use app\common\db\Query;
use think\Request;

class EmployeeReceipts extends Base
{
    /**
     * @desc  回款业绩
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $input = $request->get();
        /** @var Query $m_er */
        $mEmployeeReceiptr = new EmployeeReceipt();

        $where = 'er.amount != 0';

        if (isset($input['sid']) && $input['sid'] != ''){
            $where .= ' and er.sid = '.$input['sid'];
        }
        if (isset($input['eid']) && $input['eid'] != ''){
            $where .= ' and er.eid = '.$input['eid'];
        }
        if (isset($input['sale_role_did']) && $input['sale_role_did'] != ''){
            $where .= ' and er.sale_role_did = '.$input['sale_role_did'];
        }
        if (isset($input['consume_type']) && $input['consume_type'] != ''){
            $where .= ' and er.consume_type = '.$input['consume_type'];
        }
        if (isset($input['consume_type']) && $input['consume_type'] != ''){
            $where .= ' and er.consume_type = '.$input['consume_type'];
        }
        if (isset($input['receipt_time']) && $input['receipt_time'] != ''){
            $receipt_time = explode(',',$input['receipt_time']);
            $where .= ' and er.receipt_time between '.strtotime($receipt_time[0]).' and '.strtotime($receipt_time[1]);
        }
        if (isset($input['channel']) && $input['channel'] != ''){
            $where .= ' and s.mc_id = '.$input['channel'];
        }
        if (isset($input['from']) && $input['from'] != ''){
            $where .= ' and s.from_did = '.$input['from'];
        }

        $with = [
            'student','orderReceiptBill' => ['orderReceiptBillItem' => ['orderItem' => ['material']]],
            'orderRefund' => ['orderRefundItem' => ['orderItem' => ['material']]],
            'materialSale' => ['Material']
        ];
        $ret = $mEmployeeReceiptr->alias('er')
            ->join('student s','er.sid = s.sid','left')
            ->field('er.*')
            ->where($where)->with($with)->getSearchResult();

        foreach ($ret['list'] as $k => $v){
            $ret['list'][$k]['channel'] = get_channel_name($v['student']['mc_id']);
        }
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  添加回款人
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        if(!isset($post['orb_id']) || !isset($post['eid'])) return $this->sendError(400, 'param error');

        $bill = OrderReceiptBill::get(['orb_id' => $post['orb_id']]);
        if($post['amount'] > $bill['amount'] || $post['amount'] <= 0) return $this->sendError(400, '金额不符合收据');

        $m_er = new EmployeeReceipt();
        $is_exist = $m_er->where('eid', $post['eid'])->where('orb_id', $post['orb_id'])->find();
        if(!empty($is_exist)) return $this->sendError(400, '该笔业绩记录已经存在!');

        $rs = $m_er->createOneReceipt($post);
        if($rs === false) return $this->sendError(400, $m_er->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  添加退费回款人
     * @param Request $request
     * @method POST
     */
    public function refund_receipt(Request $request)
    {
        $post = $request->post();
        if(!isset($post['or_id']) || !isset($post['eid'])) return $this->sendError(400, 'param error');

        $order_refund = OrderRefund::get($post['or_id']);
        if (empty($order_refund)) return $this->sendError(400, '退费记录不存在');
        if($post['amount'] < -$order_refund['refund_amount'] || $post['amount'] >= 0) return $this->sendError(400, '金额不符合收据');

        $mEmployeeReceipt = new EmployeeReceipt();
        $rs = $mEmployeeReceipt->createOneRefund($order_refund,$post['eid'],$post['sale_role_did'],$post['amount']);
        if($rs === false) return $this->sendError(400, $mEmployeeReceipt->getError());

        return $this->sendSuccess();
    }

    public function put(Request $request){
        $erc_id = input('id/d');
        $EmployeeReceipt = EmployeeReceipt::get($erc_id);
        $input = $request->put();
        if(empty($EmployeeReceipt)) return $this->sendError(400, '记录不存在');

        $rs = $EmployeeReceipt->updateConsumeType($erc_id,$input);
        if($rs === false) return $this->sendError(400, $EmployeeReceipt->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  删除回款
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $erc_id = input('id');
        $rs = EmployeeReceipt::destroy($erc_id, true);
        if($rs === false) return $this->sendError(400, '删除员工回款失败');

        return $this->sendSuccess();
    }

}