<?php
/**
 * Author: luo
 * Time: 2017-11-04 16:23
**/

namespace app\api\controller;

use app\api\model\ClassStudent;
use app\api\model\Order;
use app\api\model\OrderRefund;
use app\api\model\OrderItem;
use app\api\model\OrderItem as OrderItemModel;
use think\Request;

class OrderItems extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();

        $model = new OrderItemModel();
        $where = [];

        if (isset($input['order_no']) && $input['order_no'] != ''){
            $mOrder = new Order();
            $m_order = $mOrder->where('order_no',$input['order_no'])->find();
            $where['oid'] = $m_order['oid'];
        }

        $ret = $model->where($where)->getSearchResult($input);

        return $this->sendSuccess($ret);
    }


    public function put(Request $request)
    {
        $oi_id = input('id');
        $mOrderItem = new OrderItem();

        $data = $request->put();

        $order_item = OrderItem::get(['oi_id'=>$oi_id]);
        $ret = $order_item->save($data);

        if (!$ret){
            return $this->sendError(400, $mOrderItem->getError());
        }
        return $this->sendSuccess($ret);
    }



    public function get_detail(Request $request,$id=0)
    {
        $get = $request->get();

        $ret = $this->m_order_item->where('oi_id',$id)->find();
        $ret['order'] = Order::get(['oid'=>$ret['oid']]);

        return $this->sendSuccess($ret);
    }

    public function do_assign(Request $request)
    {
        $input = $request->post();
        if(empty($input) || !isset($input['data'])) return $this->sendError(400, '参数错误');

        $m_cs = new ClassStudent();
        $rs = $m_cs->assignClassByManyOrderItem($input['data']);
        if($rs === false) return $this->sendError(400, $m_cs->getErrorMsg());

        return $this->sendSuccess();
    }


    /**
     * 订单列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function branch_orders(Request $request)
    {
        $input = $request->param();

        $og_id = gvar('og_id');
        $bids = $input['bids'];

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
        $params['consume_type'] = $input['consume_type'];

        $mOrderItem = new OrderItemModel;

        $ret = $mOrderItem->alias(['x360p_order_item'=>'oi','x360p_order'=>'o'])->join('x360p_order','oi.oid = o.oid')->where(['oi.consume_type'=>$params['consume_type'],'oi.bid'=>$params['bid'],'o.pay_status'=>2,'o.paid_time'=>['between',$params['between_ts']]])->field('oi.*')->skipBid()->getSearchResult($input);


        return $this->sendSuccess($ret);
    }


    /**
     * 撤销课程升级
     * @param Request $request
     */
    public function undo_upgrade_lesson_hours(Request $request)
    {
        $oi_id = input('id/d');

        $mOrderItem = new OrderItemModel;
        $result = $mOrderItem->undoUpgradeLessonHours($oi_id);
        if (false === $result){
            return $this->sendError(400,$mOrderItem->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * 转介绍设置
     * @param Request $request
     */
    public function do_referer(Request $request)
    {
        $input = input();

        $oi_ids = $input['oi_ids'];
        $referer_sid = isset($input['referer_sid']) ? $input['referer_sid'] : 0;
        $referer_teacher_id = isset($input['referer_teacher_id']) ? $input['referer_teacher_id'] : 0;
        $referer_eid = isset($input['referer_eid']) ? $input['referer_eid'] : 0;

        $mOrderItem = new OrderItem;
        $result = $mOrderItem->patchDoReferer($oi_ids,$referer_sid,$referer_teacher_id,$referer_eid);
        if (false === $result){
            return $this->sendError(400,$mOrderItem->getError());
        }

        return $this->sendSuccess();
    }

    

}