<?php
/**
 * Author: luo
 * Time: 2018-03-13 17:23
**/

namespace app\api\controller;

use app\api\model\OrderRefund;
use app\api\model\Order;
use think\Request;

class OrderRefunds extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();

        $mOrderRefund = new OrderRefund();

        $w = [];

        if(isset($input['refund_type'])){
            switch(intval($input['refund_type'])){
                case 1:
                    $w['need_refund_amount'] = ['GT',0];
                    $w['refund_balance_amount'] = 0;
                    break;
                case 2:
                    $w['need_refund_amount'] = 0;
                    $w['refund_balance_amount'] = ['GT',0];
                    break;
                case 3:
                    $w['need_refund_amount'] = ['GT',0];
                    $w['refund_balance_amount'] = ['GT',0];
                    break;
                case 4:
                    $w['refund_balance_amount'] = ['GT',0];
                    break;
            }
            unset($input['refund_type']);
        }

        if(isset($input['has_cut_amount']) && $input['has_cut_amount'] == 1){
            $w['cut_amount'] = ['GT',0];
            usnet($input['has_cut_amount']);
        }

        $ret = $mOrderRefund
            ->with(['employeeReceipts','orderCutAmount','order_refund_history', 'employee', 'orderRefundItem' => ['orderItem' => ['studentLesson', 'material', 'oneClass']]])
            ->withSum('refund_amount,refund_balance_amount,cut_amount')
            ->where($w)
            ->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $or_id = input('id/d');

        if($or_id <= 0) return $this->sendError(400, 'param error');

        $mOrderRefund = new OrderRefund();
        $result = $mOrderRefund->with(['employeeReceipts','order_refund_item' => ['order_item' => ['student_lesson.one_class', 'material']],
            'order_refund_history', 'employee', 'student'])->find($or_id);

        return $this->sendSuccess($result);
    }

    /**
     * 退费撤销
     * @param Request $request
     * @param int $id
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function delete(Request $request)
    {
        $or_id = input('id/d');
        $mOrderRefund = new OrderRefund();
        $result = $mOrderRefund->undoRefund($or_id);
        if (false === $result){
            return $this->sendError(400, $mOrderRefund->getError());
        }
        return $this->sendSuccess($result);
    }


    /**
     * 退费列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function branch_refund_orders(Request $request)
    {
        $input = $request->param();

        $og_id = gvar('og_id');

        if(!isset($input['start_date'])){
            $input['start_date'] = '1970-01-02';
            $input['end_date']   = '9999-12-31';
        }

        $start_ts = strtotime($input['start_date'].' 00:00:00');
        $end_ts   = strtotime($input['end_date'].' 23:59:59');
        $start_int_day = format_int_day($input['start_date']);
        $end_int_day   = format_int_day($input['end_date']);
    

        $params['between_ts'] = [$start_ts,$end_ts];
        $params['between_int_day'] = [$start_int_day,$end_int_day];
        $bids = explode(',',$input['bids']);
        $params['bid'] = ['in',$bids];
        $params['og_id'] = $og_id;

        $mOrderRefund = new OrderRefund;

        $ret = $mOrderRefund->alias(['x360p_order_refund'=>'or','x360p_order'=>'o'])->join('x360p_order','or.oid = o.oid')->where(['or.bid'=>$params['bid'],'or.refund_int_day'=>['between',$params['between_int_day']]])->field('or.*')->skipBid()->getSearchResult($input);

        foreach ($ret['list'] as &$item) {
            $item['order_refund_item'] = $this->m_order_refund_item->where('or_id',$item['or_id'])->with('order_item')->select();
        }


        return $this->sendSuccess($ret);
    }

}