<?php
/** 
 * Author: luo
 * Time: 2017-10-18 19:39
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class OrderRefundItem extends Base
{

    public function orderItem()
    {
        return $this->hasOne('OrderItem', 'oi_id', 'oi_id');
    }

    public function orderRefund()
    {
        return $this->hasOne('OrderRefund', 'or_id', 'or_id');
    }

    public function createRefundItem($data) {
        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return false;

        return true;
    }

    public static function getRefundNumByOiId($oi_id)
    {
        $w_ori['oi_id'] = $oi_id;
        $ori_list = get_table_list('order_refund_item',$w_ori);
        $nums = 0;
        if($ori_list) {
            foreach ($ori_list as $ori) {
                $nums += ($ori['nums'] + $ori['present_nums']);
            }
        }
        return $nums;
    }

    public static function getRefundAmountByOiId($oi_id)
    {
        $amount = (new self())->where('oi_id', $oi_id)->sum('amount');
        return $amount ? $amount : 0;
    }

    /*
     * 删除退款项目，此处主要是用于删除订单，当订单有退款情况的时候
     * 一个订单可能有多次退款，要把多次退款都还原，方便后续删除订单处理相关金额与课时
     * 一次退款还可能涉及多个不同的订单
     */
    public function delOrderRefundItem($oi_id)
    {
        try {
            $this->startTrans();

            $refund_item_list = $this->where('oi_id', $oi_id)->order('or_id asc')->select();
            $m_order_item = new OrderItem();
            $m_student_lesson = new StudentLesson();
            $m_order_refund = new OrderRefund();
            foreach($refund_item_list as $tmp_refund_item) {
                //如果是一次退款涉及多个订单，无法处理，因为退款金额、电子余额、扣款合并无法拆分计算
                $oi_ids = $this->where('or_id', $tmp_refund_item['or_id'])->column('oi_id');
                if(empty($oi_ids)) continue;
                $oids = $m_order_item->where('oi_id', 'in', $oi_ids)->column('oid');
                if(count(array_unique($oids)) > 1) throw new FailResult('退款涉及多个订单，无法删除');

                //处理每个退款项目涉及的课时、金额
                $order_items = $m_order_item->where('oi_id', 'in', $tmp_refund_item['oi_id'])->select();
                foreach($order_items as $tmp_order_item) {
                    if($tmp_order_item['gtype'] == OrderItem::GTYPE_GOODS) { // 处理物品
                        $mh_data = [
                            'mt_id' => $tmp_order_item['gid'],
                            'ms_id' => Branch::getMsId($tmp_order_item['bid']),
                            'int_day' => date('Ymd', time()),
                            'num' => $tmp_refund_item['nums'] + $tmp_refund_item['present_nums'],
                            'type' => MaterialHistory::TYPE_OUT,
                            'remark' => '取消退款'.Order::getOrderNo(isset($tmp_order_item['oid']) ? $tmp_order_item['oid'] : 0)
                        ];
                        $m_mh = new MaterialHistory();
                        $rs = $m_mh->addOneHis($mh_data);
                        if($rs === false) throw new FailResult($m_mh->getErrorMsg());
                    } else {
                        if($tmp_order_item['sl_id'] > 0) { //删除退款，处理课时，减少相应课时，减少退款课时
                            $handle_student_lesson_data = [
                                'present_lesson_hours' => $tmp_refund_item['present_nums']
                            ];
                            $m_student_lesson::calcLessonTimesAndHour(Lesson::get($tmp_order_item['lid']),
                                $handle_student_lesson_data, $tmp_refund_item['nums'], null, $tmp_order_item['cid']);

                            $handle_student_lesson_data['lesson_amount'] = $tmp_refund_item['amount'];
                            $student_lesson = $m_student_lesson->get($tmp_order_item['sl_id']);
                            if(empty($student_lesson)) throw new FailResult('退款相关课时不存在');
                            $rs = $m_student_lesson->handleStudentLessonHours($student_lesson, $handle_student_lesson_data,
                                StudentLesson::HANDLE_STUDENT_LESSON_HOURS_DELETE_REFUND);
                            if($rs === false) throw new FailResult($m_student_lesson->getErrorMsg());
                        }
                    }
                }

                $rs = $m_order_refund->delOrderRefund($tmp_refund_item['or_id']);
                if($rs === false) throw new FailResult($m_order_refund->getErrorMsg());

                $rs = $tmp_refund_item->delete();
                if($rs === false) throw new FailResult($tmp_refund_item->getError());

            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        return true;
    }

}