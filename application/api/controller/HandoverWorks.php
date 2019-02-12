<?php
/**
 * Author: luo
 * Time: 2018/1/27 9:41
 */

namespace app\api\controller;


use app\api\model\HandoverWork;
use app\api\model\OrderReceiptBill;
use app\api\model\OrderRefund;
use think\Request;

class HandoverWorks extends Base
{

    /**
     * @desc  交班记录
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $get = $request->get();

        $m_hw = new HandoverWork();
        $ret = $m_hw->with(
            ['orderReceiptBill' => ['orderPaymentHistory', 'student'], 'orderRefund' => ['orderRefundHistory','student']]
        )->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  交班详情
     * @author luo
     * @param Request $request
     * @param int $id
     * @method GET
     */
    public function get_detail(Request $request, $id = 0)
    {
        $info = HandoverWork::get($id, ['orderReceiptBill','orderRefund']);

        return $this->sendSuccess($info);
    }

    /**
     * @desc  交班
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        $m_hw = new HandoverWork();
        $rs = $m_hw->addOneHandoverWork($post);
        if($rs === false) return $this->sendError(400, $m_hw->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  撤消交班
     * @author luo
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $hw_id = input('id');
        $m_hw = new HandoverWork();

        $rs = $m_hw->delHandoverWork($hw_id);
        if($rs === false) return $this->sendError(400, $m_hw->getErrorMsg());
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        return $this->sendError('not support');
    }

    /**
     * @desc  交班确认
     * @author luo
     * @method GET
     */
    public function ack()
    {
        $hw_id = input('hw_id');
        $handover_work = HandoverWork::get($hw_id);
        if(empty($handover_work)) return $this->sendError(400, '没有需要确认的交班');

        $handover_work->ack_time = time();
        $rs = $handover_work->save();
        if($rs === false) return $this->sendError(400, '确认失败');

        return $this->sendSuccess();
    }

    /**
     * @desc  未交班的相关收据
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function bills(Request $request)
    {
        $uid = input('uid', gvar('uid'));
        $get = $request->get();
        $page = input('page', 1);
        $pagesize = input('pagesize', config('default_pagesize'));
        $half_pagesize = $pagesize / 2;

        $where['create_uid'] = $uid;
        $where['hw_id'] = 0;
        $m_orb = new OrderReceiptBill();
        $m_or = new OrderRefund();

        $receipt_total = $m_orb->where($where)->autoWhere($get)->count();

        $refund_total = $m_or->where($where)->autoWhere($get)->count();

        /* 以下处理分页，丙个无关系的表，合并取值、并分页很麻烦 */
        //--1-- 数量可以分的页数
        $receipt_page_num = ceil($receipt_total / $half_pagesize);
        $refund_page_num = ceil($refund_total / $half_pagesize);
        //--2-- 取得最后一页页码，最后一页的数量，因为如果最后一页数量不够，要用另一表的去补
        if($receipt_page_num > $refund_page_num) {
            $last_page_no = $refund_page_num;
            $last_page_total = $refund_total % $half_pagesize;
        } else {
            $last_page_no = $receipt_page_num;
            $last_page_total = $receipt_total % $half_pagesize;
        }

        $offset = ($page - 1) * $half_pagesize;  # 默认偏移量
        $size = $half_pagesize; # 默认取值数量
        if($page == $last_page_no) {
            //如果当前页码是某表的最后一页，另一表可能要另外取多一部分弥补不足页面的总数量
            $size = $last_page_total == 0 ? $half_pagesize : $pagesize - $last_page_total;
        } elseif ($page > $last_page_no) {
            //如果另一表已经取完了，另一表取得数量可能就不是每页数量的一半了，可能是全部了
            $size = $pagesize;
            $need_add_size = $last_page_total == 0 ? 0 : $half_pagesize - $last_page_total;
            $offset = ($page - 1) * $half_pagesize + $need_add_size;
        }

        $receipt_list = $m_orb->with(['orderPaymentHistory' => ['accountingAccount'],'student'])
            ->where($where)->autoWhere($get)->limit($offset, $size)->select();
        $refund_list = $m_or->with(['orderRefundHistory' => ['accountingAccount'],'student'])
            ->where($where)->autoWhere($get)->limit($offset, $size)->select();

        $total_money_paid_amount = $m_orb->where($where)->autoWhere($get)->sum('money_paid_amount');
        $total_unpaid_amount = $m_orb->where($where)->autoWhere($get)->sum('unpaid_amount');
        $total_cut_amount = $m_or->where($where)->autoWhere($get)->sum('cut_amount');
        $total_refund_amount = $m_or->where($where)->autoWhere($get)->sum('refund_amount');


        $m_hw = new HandoverWork();
        $orb_ids = $m_orb->where($where)->autoWhere($get)->column('orb_id');
        $or_ids = $m_or->where($where)->autoWhere($get)->column('or_id');
        $total_cash_paid_amount = $m_hw->calCashAmountByOrbIds($orb_ids);
        $total_cash_refund_amount = $m_hw->calCashAmountByOrIds($or_ids);
        $ret['summary'] = [
            'total_money_paid_amount' => $total_money_paid_amount,
            'total_unpaid_amount' => $total_unpaid_amount,
            'total_cut_amount' => $total_cut_amount,
            'total_refund_amount' => $total_refund_amount,
            'total_cash_paid_amount' => $total_cash_paid_amount,
            'total_cash_refund_amount' => $total_cash_refund_amount,
        ];
        $ret['list'] = array_merge($receipt_list, $refund_list);
        $ret['page'] = $page;
        $ret['pagesize'] = $pagesize;
        $ret['total'] = $receipt_total + $refund_total;

        return $this->sendSuccess($ret);
    }



}