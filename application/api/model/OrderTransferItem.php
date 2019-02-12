<?php
/** 
 * Author: luo
 * Time: 2017-10-18 18:26
**/


namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class OrderTransferItem extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function orderItem()
    {
        return $this->hasOne('OrderItem', 'oi_id', 'oi_id');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'uid', 'create_uid')->field('eid,uid,ename');
    }

    public function createTransferItem($data) {
        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return $this->user_error('添加结转项目失败');

        return true;
    }

    public static function getTransferNumByOiId($oi_id)
    {
        $nums = (new self())->where('oi_id', $oi_id)->sum('nums');
        $present_nums = (new self())->where('oi_id', $oi_id)->sum('present_nums');
        return $nums + $present_nums;
    }

    public static function getTransferAmountByOiId($oi_id)
    {
        $amount = (new self())->where('oi_id', $oi_id)->sum('amount');
        return $amount ? $amount : 0;
    }

    /*
     * 删除订单的结转，主要是在删除订单时使用
     * 一次结转可能涉及多个订单
     * 一个订单可能有多次结转
     */
    public function delOrderTransferItem($oi_id)
    {
        try {
            $this->startTrans();

            $transfer_item_list = $this->where('oi_id', $oi_id)->order('ot_id asc')->select();
            $m_order_item = new OrderItem();
            $m_student_lesson = new StudentLesson();
            $m_order_transfer = new OrderTransfer();
            foreach($transfer_item_list as $tmp_transfer_item) {
                //如果是一次结转涉及多个订单，无法处理，因为结转金额、电子余额、扣款合并无法拆分计算
                $oi_ids = $this->where('ot_id', $tmp_transfer_item['ot_id'])->column('oi_id');
                if(empty($oi_ids)) continue;
                $oids = $m_order_item->where('oi_id', 'in', $oi_ids)->column('oid');
                if(count(array_unique($oids)) > 1) throw new FailResult('结转涉及多个订单，无法删除');

                //处理每个结转项目涉及的课时、金额
                $order_items = $m_order_item->where('oi_id', 'in', $tmp_transfer_item['oi_id'])->select();
                foreach($order_items as $tmp_order_item) {
                    if($tmp_order_item['gtype'] == OrderItem::GTYPE_GOODS) { // 处理物品
                        $mh_data = [
                            'mt_id' => $tmp_order_item['gid'],
                            'ms_id' => Branch::getMsId($tmp_order_item['bid']),
                            'int_day' => date('Ymd', time()),
                            'num' => $tmp_transfer_item['nums'] + $tmp_transfer_item['present_nums'],
                            'type' => MaterialHistory::TYPE_OUT,
                            'remark' => '取消结转'.Order::getOrderNo(isset($tmp_order_item['oid']) ? $tmp_order_item['oid'] : 0)
                        ];
                        $m_mh = new MaterialHistory();
                        $rs = $m_mh->addOneHis($mh_data);
                        if($rs === false) throw new FailResult($m_mh->getErrorMsg());
                    } else {
                        if($tmp_order_item['sl_id'] > 0) { //删除结转，处理课时，减少相应课时，减少结转课时
                            $handle_student_lesson_data = [
                                'present_lesson_hours' => $tmp_transfer_item['present_nums']
                            ];
                            $m_student_lesson::calcLessonTimesAndHour(Lesson::get($tmp_order_item['lid']),
                                $handle_student_lesson_data, $tmp_transfer_item['nums'], null, $tmp_order_item['cid']);
                            $student_lesson = $m_student_lesson->get($tmp_order_item['sl_id']);
                            if(empty($student_lesson)) throw new FailResult('结转相关课时不存在');
                            $handle_student_lesson_data['lesson_amount'] = $tmp_transfer_item['amount'];
                            $rs = $m_student_lesson->handleStudentLessonHours($student_lesson, $handle_student_lesson_data,
                                StudentLesson::HANDLE_STUDENT_LESSON_HOURS_DELETE_TRANSFER);
                            if($rs === false) throw new FailResult($m_student_lesson->getErrorMsg());
                        }
                    }
                }

                $rs = $m_order_transfer->delOrderTransfer($tmp_transfer_item['ot_id']);
                if($rs === false) throw new FailResult($m_order_transfer->getErrorMsg());

                $rs = $tmp_transfer_item->delete();
                if($rs === false) throw new FailResult($tmp_transfer_item->getError());

            }

            $this->commit();
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        return true;
    }

    /**
     * 结转项目撤销
     * @param OrderTransferItem $order_iransfer_item
     */
    public function undoOrderTransferItem($ot_id)
    {
        $transfer_item_list = $this->where('ot_id', $ot_id)->select();
        if (!empty($transfer_item_list)){
            $mOrder_item = new OrderItem();
            $mStudent = new Student();
            $mStudent_lesson = new StudentLesson();
            $this->startTrans();
            try {
                foreach ($transfer_item_list as $transfer_item){
                    $order_item_info = $mOrder_item->get($transfer_item['oi_id']);
                    $student_lesson_info = $mStudent_lesson->get($order_item_info['sl_id']);
                    if (empty($student_lesson_info)){
                        $this->rollback();
                        return $this->user_error('student_lesson empty');
                    }

                    $result = $mStudent_lesson->undoTransferLesson($student_lesson_info, $transfer_item['nums'], $transfer_item['amount'], $transfer_item['present_nums']);
                    if (false === $result){
                        $this->rollback();
                        return $this->user_error($mStudent_lesson->getError());
                    }

                    $student_info = $mStudent->get($order_item_info['sid']);
                    $result = $mStudent->undoTransferLesson($student_info, $transfer_item['nums'],$transfer_item['present_nums']);
                    if (false === $result){
                        $this->rollback();
                        return $this->user_error($mStudent->getError());
                    }


                    $result = $mOrder_item->undoDeductPresentLessonHours($order_item_info, $transfer_item['present_nums']);
                    if (false === $result){
                        $this->rollback();
                        return $this->user_error($mOrder_item->getError());
                    }

                    $operate_data = [
                        'oid'           => $order_item_info['oid'],
                        'oi_id'         => $order_item_info['oi_id'],
                        'unit_price'    => $transfer_item['unit_price'],
                        'lesson_amount' => $transfer_item['amount'],
                        'sl_id'         => $student_lesson_info['sl_id'],
                        'lesson_hours'  => $transfer_item['nums'],
                        'op_type'       => StudentLessonOperate::OP_TYPE_UNTRANSFER,
                        'remark'        => '撤销结转'
                    ];

                    $mSlo = new StudentLessonOperate();
                    $result = $mSlo->addOperation($operate_data);
                    if(false === $result){
                        $this->rollback();
                        return $this->user_error($mSlo->getError());
                    }

                    $transfer_item->delete();
                }
            } catch(\Exception $e) {
                $this->rollback();
                return $this->exception_error($e);
            }
        }

        $this->commit();
        return true;
    }

}