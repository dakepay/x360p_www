<?php
/**
 * Author: luo
 * Time: 2017-12-23 18:08
**/

namespace app\sapi\controller;


use app\sapi\model\Order;
use app\sapi\model\OrderItem;
use app\sapi\model\StudentLessonHour;
use think\Request;

class Orders extends Base
{

    /**
     * @desc  学生的订单
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $sid = global_sid();
        $input = $request->get();
        $m_order = new Order();
        $with = ['orderItems' => ['lesson','material','oneClass']];
        $ret = $m_order->where('sid', $sid)->with($with)->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  订单详情
     * @author luo
     * @param Request $request
     * @param int $id
     * @method GET
     */
    public function get_detail(Request $request, $id = 0)
    {
        $m_order = new Order();
        $with = ['orderItems' => ['lesson','material','oneClass']];
        $ret = $m_order->with($with)->find($id);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  学生课耗
     * @author luo
     * @method GET
     */
    public function get_list_consume(Request $request)
    {
        $oid = input('oid/d');
        $input = $request->get();
        $sl_ids = (new OrderItem())->where('oid', $oid)->column('sl_id');
        $sl_ids = array_filter(array_unique($sl_ids), function($val){
            if($val > 0) return true;
            return false;
        });

        $m_slh = new StudentLessonHour();
        $ret = $m_slh->with(['lesson', 'oneClass'])->where('sl_id', 'in', $sl_ids)->getSearchResult($input);
        return $this->sendSuccess($ret);

    }

}