<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\Order;
use app\api\model\OrderItem;


class Orders extends Export
{
    protected $res_name = 'order';

    protected $columns = [

        ['field'=>'order_no','title'=>'订单编号','width'=>20],
        ['field'=>'create_time','title'=>'下单时间','width'=>20],
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'sid','title'=>'学员','width'=>20],
        ['field'=>'items','title'=>'项目','width'=>40],   //item_name
        ['field'=>'order_amount','title'=>'订单金额','width'=>20],
        ['field'=>'paid_amount','title'=>'实缴金额','width'=>20],
        ['field'=>'order_status','title'=>'订单状态','width'=>20],
        ['field'=>'pay_status','title'=>'付款状态','width'=>20],
        ['field'=>'ac_status','title'=>'分班状态','width'=>20],
        ['field'=>'paid_time','title'=>'报名时间','width'=>20],
        ['field'=>'aa_id','title'=>'支付方式','width'=>20],
    ];

    protected function convert_status($value)
    {
        $map = [0=>'待付款', 1=>'已付款', 10=>'已申请退款',11=>'已退款'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function convert_class($value)
    {
        $map = [0=>'未分班', 1=>'部分分班', 2=>'已分班'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function get_order_item_name($oid){
        $w_oi['oid'] = $oid;
        $oi_list = get_table_list('order_item',$w_oi);
        $ret = [];
        foreach($oi_list as $oi){
            $m_oi = new OrderItem($oi);
            array_push($ret,$m_oi->item_name);
        }
        $ret = implode(',',$ret);
        return $ret;
    }

    protected function get_title(){
        $title = '订单管理';
        return $title;
    }

    protected function convert_pay_status($value)
    {
        $map = ['未付款','部分付款','已缴清'];
        if(key_exists($value,$map)){
            return $map[$value];
        }
        return '-';
    }


    public function get_data()
    {
        $model = new Order();
        $result = $model->order('paid_time desc')->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['items'] = $this->get_order_item_name($v['oid']);
            $list[$k]['bid']       = get_branch_name($v['bid']);
            $list[$k]['order_no']  = $v['order_no'].' ';
            $list[$k]['sid']       = get_student_name($v['sid']);
            $list[$k]['order_status'] = $this->convert_status($v['order_status']);
            $list[$k]['ac_status'] = $this->convert_class($v['ac_status']);
            $list[$k]['aa_id'] = $v['order_status'] > 0 ? '前台支付' : '--';
            $list[$k]['pay_status'] = $this->convert_pay_status($v['pay_status']);
        }
   
        if (!empty($list)) {
            return collection($list)->toArray();
        }

        return [];

    }

    

}