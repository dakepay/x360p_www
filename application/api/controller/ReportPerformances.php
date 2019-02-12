<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/12/23
 * Time: 17:14
 */

namespace app\api\controller;

use app\api\model\EmployeeReceipt;
use app\api\model\OrderPerformance;
use app\api\model\OrderItem;
use app\api\model\OrderRefund;
use app\api\model\Order;
use app\common\db\Query;
use think\Request;

class ReportPerformances extends Base
{ 
    /**
     * 获得OrderItem信息
     * @param  [type] $oid [description]
     * @return [type]      [description]
     */
    protected function get_order_item_info($id,$cache = true){
        return get_row_info($id,'order_item','oid',$cache);
    }
    

    protected function get_order_item_program($oid)
    {
        $oinfo = $this->get_order_item_info($oid);
        if($oinfo['cid']){
            return get_class_name($oinfo['cid']).'/'.get_lesson_name($oinfo['lid']);
        }else{
            return get_lesson_name($oinfo['lid']);
        }
    }

    protected function get_order_amount($eid,$role,$input)
    {
        $model = new OrderPerformance;
        $w['eid'] = $eid;
        $w['sale_role_did'] = $role;
        $data = $model->where($w)->getSearchResult($input,[],false);
        $sum = 0;
        if(!empty($data['list'])){
            foreach ($data['list'] as $k => $v) {
                $sum += $v['amount'];
            }
        }
        return $sum;
    }

    protected function get_performance_amount($input)
    {
        $model = new OrderPerformance;
        $data = $model->getSearchResult($input,[],false);
        $sum = 0;
        if(!empty($data['list'])){
            foreach ($data['list'] as $k => $v) {
                $sum += $v['amount'];
            }
        }
        return $sum;
    }

    protected function get_receipt_amount($eid,$role,$input)
    {
        $model = new EmployeeReceipt;
        $w['eid'] = $eid;
        $w['sale_role_did'] = $role;
        $data = $model->where($w)->getSearchResult($input,[],false);
        $sum = 0;
        if(!empty($data['list'])){
            foreach ($data['list'] as $k => $v) {
                $sum += $v['amount'];
            }
        }
        return $sum;
    }

    protected function get_receipt_amounts($input)
    {
        $model = new EmployeeReceipt;
        $data = $model->getSearchResult($input,[],false);
        $sum = 0;
        if(!empty($data['list'])){
            foreach ($data['list'] as $k => $v) {
                $sum += $v['amount'];
            }
        }
        return $sum;
    }

    protected function get_receipt($oid)
    {
        $model = new EmployeeReceipt;
        $w['oid'] = $oid;
        return $model->where($w)->value('amount');
    }
    
    /**
     * 签单明细
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_list(Request $request)
    {
        $input = $request->get();
        $w = [];
        if(isset($input['create_time'])){
            $w['o.paid_time'] = sql_between_intday($input['create_time']);
            unset($input['create_time']);
        }
        $model = new OrderPerformance;
        $fields = ['op.eid','op.sale_role_did','op.amount','op.oid','o.paid_time'];


        // 详情start
        $data = $model->alias('op')->join('order o','op.oid=o.oid','left')->field($fields)->where($w)->order('o.paid_time ASC')->getSearchResult($input);
        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['sale_role'] = get_did_value($v['sale_role_did']);
            $oinfo  = $this->get_order_item_info($v['oid']);
            $data['list'][$k]['sid'] = get_student_name($oinfo['sid']);
            $data['list'][$k]['program'] = $this->get_order_item_program($v['oid']);
            $data['list'][$k]['price'] = $oinfo['price'];
            $data['list'][$k]['create_time'] = date('Y-m-d',$v['paid_time']);
            // $data['list'][$k]['receipt_amount'] = $this->get_receipt($v['oid']);

            $data['list'][$k]['consume_type'] = $oinfo['consume_type'];
        }
        // 详情end

        return $this->sendSuccess($data);
    } 
    
    /**
     * 签单回款汇总
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function performance_total(Request $request)
    {
        $input = $request->get();
        $model = new OrderPerformance;
        $fields = ['eid'];
        
        $page = input('page/d');
        $pagesize = input('pagesize/d');
        $eids = [];
        if(!empty($input['eid'])){
            $eids[0] = input('eid/d');
            unset($input['eid']);
        }else{
            $w = [];
            $w['bid'] = $request->bid;
            // $w['og_id'] = gvar('og_id');
            $data = $model->field($fields)->where($w)->order('eid asc')->getSearchResult($input,[],false);
            foreach ($data['list'] as $k => $v) {
                $eids[] = $v['eid'];
            }
            $eids = array_values(array_unique($eids));
        }
        $ret['list'] = [];
        $ret['page'] = $page;
        $ret['pagesize'] = $pagesize;
        $ret['total'] = count($eids);
        foreach ($eids as $k => $eid) {
            $ret['list'][$k]['eid'] = $eid;
            $roles = $model->where('eid',$eid)->column('sale_role_did');
            $roles = array_values(array_unique($roles));
            foreach ($roles as $k1 => $role) {
                $ret['list'][$k]['infos'][$k1]['sale_role_did'] = $role;
                $ret['list'][$k]['infos'][$k1]['order_amount'] = $this->get_order_amount($eid,$role,$input);
                $ret['list'][$k]['infos'][$k1]['receipt_amount'] = $this->get_receipt_amount($eid,$role,$input);
            }
        }
        $ret['performance'] = $this->get_performance_amount($input);
        $ret['receipt'] = $this->get_receipt_amounts($input);
        return $this->sendSuccess($ret); 
    }

    
    /**
     * 回款明细
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function receipt_list(Request $request)
    {
        $input = $request->get();
        $model = new EmployeeReceipt;
        $fields = ['eid','sale_role_did','amount','receipt_time','create_time'];
        $data = $model->field($fields)->getSearchResult($input);

        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['sale_role'] = get_did_value($v['sale_role_did']);
        }

        return $this->sendSuccess($data);
    }



    

    //员工回款业绩
    public function employee_receipts(Request $request)
    {
        $get = $request->get();
        unset($get['orb_id'], $get['or_id']);
        /** @var Query $m_er */
        $m_er = new EmployeeReceipt();
        if(!empty($get['group'])) {
            $group = explode(',', $get['group']);
        } else {
            $group = [];
        }

        $fields = $group;
        $fields['sum(amount)'] = 'total_amount';
        $get['order_field'] = isset($get['order_field']) ? $get['order_field'] : 'total_amount';
        $get['order_sort'] = isset($get['order_sort']) ? $get['order_sort'] : 'desc';
        $ret = $m_er->group(join(',', $group))->field($fields)->order($get['order_field'], $get['order_sort'])
            ->getSearchResult($get);
        $receipt_amount = $m_er->where(['orb_id' => ['gt', 0], 'or_id' => 0])->autoWhere($get)->sum('amount');
        $ret['receipt_amount'] = $receipt_amount;

        $refund_amount = $m_er->where(['or_id' => ['gt', 0], 'orb_id' => 0])->autoWhere($get)->sum('amount');
        $ret['refund_amount'] = $refund_amount;

        return $this->sendSuccess($ret);
    }

    /*移动端报表*/

    /**
     * 校区业绩
     * @param  Request $request [description]
     * api/report_performances/branch_performance
     * @return [type]           [description]
     */
    public function branch_performances(Request $request)
    {
        $input = $request->param();

        if(!isset($input['start_date'])){
            $input['start_date'] = '1970-01-02';
            $input['end_date']   = '9999-12-31';
        }

        if(isset($input['start_date'])){
            $start_ts = strtotime($input['start_date'].' 00:00:00');
            $end_ts   = strtotime($input['end_date'].' 23:59:59');
            $start_int_day = format_int_day($input['start_date']);
            $end_int_day   = format_int_day($input['end_date']);
        }

        $params['between_ts'] = [$start_ts,$end_ts];
        $params['between_int_day'] = [$start_int_day,$end_int_day];
        $bids = explode(',',$input['bids']);
        $params['bid'] = ['in',$bids];
            
        // 新签订单数
        $new_order_nums = $this->get_order_nums_value($params,OrderItem::CONSUME_TYPE_NEW);
        // 新签订单金额
        $new_order_amount = $this->get_order_amount_value($params,OrderItem::CONSUME_TYPE_NEW);
        // 续费订单数
        $renew_order_nums = $this->get_order_nums_value($params,OrderItem::CONSUME_TYPE_RENEW);
        // 续费订单金额
        $renew_order_amount = $this->get_order_amount_value($params,OrderItem::CONSUME_TYPE_RENEW);
        // 退费订单数
        $refund_order_nums = $this->get_refund_nums_value($params);
        // 退费金额（包括违约）
        $refund_order_amount = $this->get_refund_amount_value($params);
        // 净业绩
        $total_amount = $new_order_amount + $renew_order_amount - $refund_order_amount;

        $ret = array(
            'new_order_nums'      => $new_order_nums,
            'new_order_amount'    => $new_order_amount,
            'renew_order_nums'    => $renew_order_nums,
            'renew_order_amount'  => $renew_order_amount,
            'refund_order_nums'   => $refund_order_nums,
            'refund_order_amount' => $refund_order_amount,
            'total_amount'        => $total_amount,
            'params'              => $input
        );
        
        return $this->sendSuccess($ret);
    }


    /**
     * 订单数
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function get_order_nums_value($params,$consume_type)
    {
        $mOrderItem = new OrderItem;
        $order_items = $mOrderItem->alias(['x360p_order_item'=>'oi','x360p_order'=>'o'])->join('x360p_order','oi.oid = o.oid')->where(['oi.consume_type'=>$consume_type,'oi.bid'=>$params['bid'],'o.pay_status'=>2,'o.paid_time'=>['between',$params['between_ts']]])->select();
        $order_items = collection($order_items)->toArray();
        /*$oids = array_column($order_items,'oid');
        $oids = array_unique($oids);*/
        return count($order_items);
    }
    
    /**
     * 订单金额
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function get_order_amount_value($params,$consume_type)
    {
        $mOrderItem = new OrderItem;
        $new_order_amount = $mOrderItem->alias(['x360p_order_item'=>'oi','x360p_order'=>'o'])->join('x360p_order','oi.oid = o.oid')->where(['oi.consume_type'=>$consume_type,'oi.bid'=>$params['bid'],'o.pay_status'=>2,'o.paid_time'=>['between',$params['between_ts']]])->sum('subtotal');
        return $new_order_amount;
    }

    /**
     * 退费订单数（order_refund 和 order_refund_history中的 oid 字段没有用起来，导致计算的订单数不准，举个栗子：一个订单可能有多笔退款记录）
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function get_refund_nums_value($params)
    {
        $mOrderRefund = new OrderRefund;
        $refund_nums = $mOrderRefund->where(['bid'=>$params['bid'],'oid'=>['gt',0],'refund_int_day'=>['between',$params['between_int_day']]])->count();
        return $refund_nums;
    }
    
    /**
     * 退费金额
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function get_refund_amount_value($params)
    {
        $mOrderRefund = new OrderRefund;
        $refund_amount = $mOrderRefund->where(['bid'=>$params['bid'],'oid'=>['gt',0],'refund_int_day'=>['between',$params['between_int_day']]])->sum('refund_amount');
        return $refund_amount;
    }












}