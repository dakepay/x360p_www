<?php
/** 
 * Author: luo
 * Time: 2017-10-14 10:09
**/

namespace app\api\controller;

use app\api\model\InputTemplate;
use app\api\model\Order as OrderModel;
use app\api\model\Order;
use app\api\model\OrderItem;
use app\api\model\OrderPaymentOnline;
use app\api\model\OrderPerformance;
use app\api\model\OrderReceiptBill;
use app\api\model\OrderReceiptBillItem;
use app\api\model\OrderRefund;
use app\api\model\OrderRefundItem;
use app\api\model\OrderTransfer;
use app\api\model\OrderTransferItem;
use app\api\model\StudentLesson;
use think\Exception;
use think\Log;
use think\Request;
use app\api\model\Material;
use app\api\model\Classes;
use app\api\model\MarketClue;


class Orders extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        if(!isset($input['is_debit'])){
            $input['is_debit'] = 0;         //默认过滤掉储值订单
        }
        $ret = (new OrderModel())->with(['student', 'orderPerformance'])
            ->order('oid', 'desc')->getSearchResult($input);

        foreach($ret['list'] as &$order) {
            $order['order_items'] = [];
            $item_list = OrderItem::all(['oid' => $order['oid']]);

            foreach($item_list as $item) {

                /* 计算剩余次数 */
                if($item instanceof OrderItem) {
                    $item = OrderItem::getItemNumsCondition($item);
                }

                $sl_id = $item->sl_id;
                $oi_id = $item->oi_id;
                $gid = $item->gid;
                $cid = $item->cid;

                /* 获取学生课程信息 */
                $item['student_lesson'] = StudentLesson::get(function($query) use($sl_id) {
                    $query->where('sl_id', $sl_id);
                }, ['one_class', 'employee_student']);

                /* 物品 */
                $item['material'] = Material::get(['mt_id' => $gid]);

                /* 班级 */
                $item['one_class'] = Classes::get(['cid' => $cid]);

                $item['transfer_item'] = OrderTransferItem::all(function($query) use($oi_id) {
                    $query->where('oi_id', $oi_id);
                });
                $item['refund_item'] = OrderRefundItem::all(function($query) use($oi_id) {
                    $query->where('oi_id', $oi_id);
                });
                $item['receipt_bill_item'] = $item->getItemPaymentHis($item);
                array_push($order['order_items'], $item);
            }
        }

        return $this->sendSuccess($ret);
    }

    public function get_detail(Request $request, $id=0)
    {
        $get = $request->get();
        $with = !empty($get['with']) ? explode(',', $get['with']) : [];
        $with[] = 'student';
        $with[] = 'orderPerformance';
        $order = Order::get(['oid' => $id], $with);

        $item_list = OrderItem::all(['oid' => $order['oid']]);
        $list = [];

        foreach($item_list as $item) {

            /* 计算剩余次数 */
            if($item instanceof OrderItem) {
                $item = OrderItem::getItemNumsCondition($item);
            }

            $sl_id = $item->sl_id;
            $oi_id = $item->oi_id;
            $gid = $item->gid;
            $cid = $item->cid;

            /* 获取学生课程信息 */
            $item['student_lesson'] = StudentLesson::get(function($query) use($sl_id) {
                $query->where('sl_id', $sl_id)->field('lid,cid,expire_time');
            }, ['one_class']);

            /* 物品 */
            $item['material'] = Material::get(['mt_id' => $gid]);

            /* 班级 */
            $item['one_class'] = Classes::get(['cid' => $cid]);

            $item['transfer_item'] = OrderTransferItem::all(function($query) use($oi_id) {
                $query->where('oi_id', $oi_id);
            });
            $item['refund_item'] = OrderRefundItem::all(function($query) use($oi_id) {
                $query->where('oi_id', $oi_id);
            });
            $item['receipt_bill_item'] = $item->getItemPaymentHis($item);
            $item = $item->toArray();
            array_push($list, $item);
        }

        $order['order_items'] = $list;

        return $this->sendSuccess($order);

    }

    /**
     * 下单
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function post(Request $request){
        $input = $request->post();

        $mOrder = new Order();
        $result = $mOrder->createOrder($input);

        if(!$result){
            return $this->sendError(400,$this->m_order->getError());
        }

        if(isset($input['is_push']) && $input['is_push']) {
            $order_info = get_order_info($this->m_order->oid,false);
            $this->m_message->sendTplMsg('order_purchase_success',$order_info);
        }

        return $this->sendSuccess($result);
    }

    /**
     * @desc  修改收据号
     * @param Request $request
     * @param int $id
     * @method PUT
     */
    public function put(Request $request)
    {

        $oid = input('id');
        $m_o = new Order();
        $input = $request->put();

        $result = $m_o->updatContract($oid,$input);
        if (!$result){
            return $this->sendError(400, $m_o->getError());
        }
        return $this->sendSuccess($result);
    }

    /**
     * @desc  添加业绩归属人
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post_salesman(Request $request)
    {
        $post = $request->post();
        $salesman_data = $post['salesman'];
        $oid = input('id');

        $m_order = new Order();
        $rs = $m_order->addSalesman($oid, $salesman_data);
        if($rs === false) return $this->sendError(400, $m_order->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * @desc  删除订单业绩人
     * @author luo
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method DELETE
     */
    public function delete_salesman(Request $request)
    {
        $oid = input('id', 0);
        $eid = input('eid', 0);
        $eid = explode(',', $eid);

        $m_op = new OrderPerformance();
        $rs = $m_op->delPerformance($oid, $eid);
        if($rs === false) return $this->sendError(400, $m_op->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  删除订单
     * @author luo
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $oid = input('id/d');
        $order = Order::get(['oid' => $oid]);

        if(!$order){
            return $this->sendError(400,'订单不存在或已经被删除!');
        }

        $sid = $order['sid'];

        
        $result = $order->deleteOrder($order);
        if(false === $result) {
            if($order->get_error_code() == $order::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($order->getErrorMsg());
            }
            return $this->sendError(400, $order->getErrorMsg());
        }
        // 如果订单没有付款  删除订单 更新市场名单状态 同时更新市场渠道成交人数；如果删除订单学员有成交订单记录 以下代码忽略
        $w_order['sid'] = $sid;
        $w_order['pay_status'] = ['gt',0];
        $orders = (new Order())->where($w_order)->find();
        if(empty($orders) || $order['pay_status'] == 0){
            $clue = MarketClue::get(['sid'=>$order['sid']]);
            if($clue){
                $clue->is_deal = 0;
                $clue->save();
                MarketClue::UpdateNumOfChannel($clue['mc_id']);
            }
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  订单的order_items
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list_order_items(Request $request)
    {
        $oid = input('id');
        $m_order_item = new OrderItem();
        $items = $m_order_item->where('oid', $oid)->with(['studentLesson' => ['oneClass','employeeStudent']])->select();

        return $this->sendSuccess($items);
    }


    /**
     * 确认订单支付
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function do_submit(Request $request)
    {
        $input = $request->post();

        $mOrder = new Order();
        $result = $mOrder->pay($input);

        if(!$result){
            return $this->sendError(400,$mOrder->getError());
        }

        
        if(isset($input['is_push']) && $input['is_push']) {
            $order_info = get_order_info($this->m_order->oid,false);
            $this->m_message->sendTplMsg('order_purchase_success',$order_info);
        }

        return $this->sendSuccess($result);
    }

    /**
     * @desc  订单补缴费用
     * @author luo
     * @method POST
     */
    public function do_supplement(Request $request)
    {
        $input = request()->param();
        $sid = input('sid/d');
        $items = input('items/a');
        $payments = input('payment/a');
        $paid_time = isset($input['paid_time']) ? $input['paid_time'] : date('Y-m-d',time());

        $model = new OrderModel();
        $orb_id = $model->supplementPay($sid, $items, $payments, $paid_time);
        if(!$orb_id) return $this->sendError(400, $model->getErrorMsg());

        return $this->sendSuccess(['orb_id' => $orb_id]);
    }

    /**
     * @desc  订单结转
     * @author luo
     * @url  dotransfer
     * @method POST
     */
    public function do_transfer(Request $request) {
        $input = $request->post();

        $student_data = $input['student'];
        $transfer_items = $input['items'];
        $cut_items = isset($input['cut_items']) && !empty($input['cut_items']) ? $input['cut_items'] : [];

        $order_transfer_model = new OrderTransfer();
        $rs = $order_transfer_model->transfer($student_data, $transfer_items, $cut_items);
        if(!$rs) {
            return $this->sendError(400, $order_transfer_model->getErrorMsg(), 400, $order_transfer_model->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  订单退款
     * @author luo
     * @url   do_refund
     * @method POST
     */
    public function do_refund(Request $request) {
        $input = $request->post();
        $mOrderRefund = new OrderRefund();
        $student_data = $input['student'];
        $refund_items = $input['items'];
        $accounts = $input['accounts'];
        $cut_items = isset($input['cut_items']) && !empty($input['cut_items']) ? $input['cut_items'] : [];
        $refund_balance_amount = isset($input['refund_balance_amount']) ? floatval($input['refund_balance_amount']) : 0;
        $salesman = isset($input['salesman']) ? $input['salesman'] : [];
        $remark = isset($input['remark'])?safe_str($input['remark']):'';
        $refund_int_day = isset($input['refund_int_day'])?format_int_day($input['refund_int_day']):0;
        $or_id = $mOrderRefund->refund($student_data, $refund_items, $accounts, $cut_items, $refund_balance_amount, $salesman,$remark,$refund_int_day);
        if(!$or_id){
            return $this->sendError(400, $mOrderRefund->getError());
        }
        $ret['or_id'] = $or_id;
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  打印票据
     * @author luo
     * @method GET
     */
    public function do_print() {
        $input = input('get.');
        $type = input('type');
        $number = input('number');

        switch($type) {
            case 'receipt_bill':
                $bill_type = 1;
                if(isset($input['bt'])){
                    $bill_type = intval($input['bt']);
                }
                $model = new OrderReceiptBill();
                $data = $model->makePrintData($number,$bill_type);
                break;

            case 'refund_bill':
                $model = new OrderRefund();
                $data = $model->makePrintData($number);
                break;
            case 'lesson':
                $model = new Order();
                $data = $model->makeLessonPrintData($number);
                break;
            default:
                $data = [];
        }

        return $this->sendSuccess($data);
    }

    public function get_list_receipt_bill_item(Request $request)
    {
        $input = $request->param();

        $oid = input('id/d');
        $m_orb = new OrderReceiptBillItem();
        $ret = $m_orb->where('oid', $oid)
            ->with(['orderReceiptBill' => ['orderPaymentHistory','employee'],'orderItem' => ['studentLesson','material', 'oneClass']])
            ->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function get_list_refund_item(Request $request)
    {
        $input = $request->param();
        $oid = input('id/d');

        $oi_ids = (new OrderItem())->where('oid', $oid)->column('oi_id');

        if(!empty($oi_ids)) {
            $m_ori = new OrderRefundItem();
            $ret = $m_ori->where('oi_id', 'in', $oi_ids)
                ->with(['orderRefund' => ['orderRefundHistory','employee'],'orderItem' => ['studentLesson', 'material', 'oneClass']])
                ->getSearchResult($input);
        } else {
            $ret['total'] = 0;
            $ret['page'] = input('page/d', 1);
            $ret['pagesize'] = input('pagesize/d', config('default_pagesize'));
            $ret['list'] = [];
        }

        return $this->sendSuccess($ret);
    }

    public function get_list_transfer_item(Request $request)
    {
        $input = $request->param();
        $oid = input('id/d');

        $oi_ids = (new OrderItem())->where('oid', $oid)->column('oi_id');

        if(!empty($oi_ids)) {
            $m_oti = new OrderTransferItem();
            $ret = $m_oti->where('oi_id', 'in', $oi_ids)->with(['employee', 'orderItem' => ['studentLesson', 'material', 'oneClass']])
                ->getSearchResult($input);
        } else {
            $ret['total'] = 0;
            $ret['page'] = input('page/d', 1);
            $ret['pagesize'] = input('pagesize/d', config('default_pagesize'));
            $ret['list'] = [];
        }

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  订单在线付款状态
     * @author luo
     * @method GET
     */
    public function online_payment_status(Request $request)
    {
        $oid = input('oid');
        $paid_amount = input('paid_amount');
        $m_opo = new OrderPaymentOnline();
        //查询两小时内的订单相关金额的列表数据
        $order_list = $m_opo->where('create_time', 'gt', time()-120*60)->where('oid', $oid)
            ->where('paid_amount', $paid_amount)->order('create_time desc')->select();

        $info = [];
        //循环处理列表，判断是否已经支付成功
        foreach($order_list as $per_order) {
            $info =  [
                'opo_id' => $per_order['opo_id'],
                'status' => $per_order['status'],
                'oid' => $per_order['oid'],
                'paid_amount' => $per_order['paid_amount'],
            ];
            if($per_order['status'] == 1 && $per_order['oph_id'] == 0) {
                break;
            }

            if($per_order['status'] == 0) {
                $rs = $m_opo->updateByOutTradeNo($per_order['out_trade_no'], $per_order);
                if($rs == true) {
                    $info['status'] = 1;
                    break;
                }
            }

            $info = [];
        }

        if(!empty($info) && $info['status'] == 1) {
            return $this->sendSuccess($info);
        } else {
            return $this->sendSuccess(['status' => 0]);
        }
    }

    /**
     * @desc  订单的在线付款记录
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list_payment_online(Request $request)
    {
        $oid = input('id/d');
        $get = $request->get();

        $m_opo = new OrderPaymentOnline();
        $ret = $m_opo->where('oid', $oid)->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

}