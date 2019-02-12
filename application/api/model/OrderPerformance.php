<?php
/**
 * Author: luo
 * Time: 2017-10-18 12:26
**/


namespace app\api\model;

class OrderPerformance extends Base
{

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function oneOrder() {
        return $this->hasOne('Order', 'oid', 'oid')
            ->field('oid,sid,order_no,order_amount,paid_amount');
    }

    public function createOnePerformance($data) {

        $rs = (new self())->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return $this->user_error('创建订单业绩记录失败');
        return $rs;
    }

    public function delPerformance($oid, array $eid)
    {
        $rs = $this->where('oid', $oid)->where('eid', 'in', $eid)->delete();
        if($rs === false) return $this->user_error('删除失败');

        return true;
    }

    /**
     * 修改订单销售业绩
     * @param $oid
     * @param $update_amount
     */
    public function updatePerformanceAmount($oid, $update_amount)
    {
        $order_performance_list = $this->where('oid',$oid)->select();
        if (empty($order_performance_list)){
            foreach ($order_performance_list as $order_performance){
                $update['amount'] = $order_performance['amount'] + $update_amount;
                $w['op_id'] = $order_performance['op_id'];
                $result = $this->save($update,$w);
                if (false === $result){
                    return $this->sql_save_error('order_performance');
                }
            }
        }

        return true;
    }

}