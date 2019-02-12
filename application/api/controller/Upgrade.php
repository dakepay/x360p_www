<?php
/**
 * Author: luo
 * Time: 2018/5/7 11:39
 */

namespace app\api\controller;


use app\api\model\Lesson;
use app\api\model\OrderItem;
use app\api\model\OrderRefundItem;
use app\api\model\OrderTransferItem;
use app\api\model\StudentLesson;
use think\Request;

class Upgrade extends Base
{

    private $update_fields = [
        //'student_lesson_table' => '更新student_lesson表数据',
    ];

    private $redis = null;
    private $redis_key;
    private $start = 0; # 更新开始的位置
    private $offset = 30; # 更新位移

    /**
     * @desc  更新数据库冗余数据
     * @author luo
     * @param Request $request
     * @method PUT
     */
    public function put(Request $request)
    {

        if(empty($this->update_fields)) return $this->sendSuccess($this->setReturnInfo('程序没设定要更新的数据'));
        if(is_null(gvar('og_id'))) return $this->sendError($this->setReturnInfo('机构id信息错误'));

        //redis相关数据，分多次更新
        $redis_key = 'upgrade_data_og_id_'.gvar('og_id');
        $this->redis_key = $redis_key;
        $this->redis = $this->redis ? $this->redis : redis();
        $this->start = $this->redis->hGet($redis_key, 'start') ? $this->redis->hGet($redis_key, 'start') : $this->start;
        $field = $this->redis->hGet($redis_key, 'field');

        if($this->redis->hGet($redis_key, 'done')) return $this->sendSuccess($this->setReturnInfo('所有更新完成'));

        if(!empty($field)) {
            //从redis中继续上一次的更新
            $method = 'update_' . $field;
        } else {
            //如果是第一次，则从头开始
            $first_field_row = array_slice($this->update_fields,0, 1, true);
            $field = array_keys($first_field_row)[0];
            $method = 'update_' . $field;
        }

        $rs = method_exists($this, $method);
        if(!$rs) return $this->sendSuccess($this->setReturnInfo('没有需要更新的信息'));

        $rs = $this->$method();
        return $this->sendSuccess($this->setReturnInfo($rs, 0));
    }

    //返回的数据格式，status: 0前端继续，1前端终止
    private function setReturnInfo($msg, $status = 1)
    {
        $info = [
            'status' => $status,
            'msg' => $msg,
        ];
        return $info;
    }

    //设置下次更新的信息与位置
    private function set_redis_next_update($cur_field, $cur_end, $total)
    {
        $start = $cur_end;

        $done = 0;
        if($cur_end >= $total) {
            $keys = array_keys($this->update_fields);
            $i = array_search($cur_field, $keys);
            if($i >= count($keys) - 1) {
                $done = 1;
                $field = $cur_field;
            } else {
                //更新下一字段，从0开始
                $field = $keys[$i + 1];
                $start = 0;
            }
        } else {
            $field = $cur_field;
        }
        $this->redis->hSet($this->redis_key, 'field', $field);
        $this->redis->hSet($this->redis_key, 'start', $start);
        $this->redis->hSet($this->redis_key, 'done', $done);
    }

    //更新学员剩余课时，同时更新student_lesson的课时数，课次数
    private function update_student_lesson_table()
    {
        $field = str_replace('update_','', __FUNCTION__);
        $start = $this->start;
        $end = $start + $this->offset;

        $m_sl = new StudentLesson();
        $where = sprintf('sl_id >= %d and sl_id < %d', $start, $end);
        $total = $m_sl->count();
        $list = $m_sl->where($where)->select();

        $m_oi = new OrderItem();
        $m_ori = new OrderRefundItem();
        $m_oti = new OrderTransferItem();
        $m_lesson = new Lesson();

        /** @var StudentLesson $student_lesson */
        foreach ($list as $student_lesson) {
            if($student_lesson->lid <= 0) continue;
            $lesson = $m_lesson->field('lid,price_type,unit_lesson_hours')->cache(3)->where('lid', $student_lesson->lid)->find();
            if(empty($lesson) || $lesson['unit_lesson_hours'] <= 0 ||
                ($lesson['price_type'] != Lesson::PRICE_TYPE_TIMES && $lesson['price_type'] != Lesson::PRICE_TYPE_HOUR)) continue;

            $oi_ids = $m_oi->where('sl_id', $student_lesson->sl_id)->column('oi_id');
            $refund_num = $m_ori->where('oi_id', 'in', $oi_ids)->sum('nums');
            $transfer_num = $m_oti->where('oi_id', 'in', $oi_ids)->sum('nums');

            if($lesson['price_type'] == Lesson::PRICE_TYPE_TIMES) {
                //退款结转是按实际次数的
                $refund_num = $refund_num * $lesson['unit_lesson_hours'];
                $transfer_num = $transfer_num * $lesson['unit_lesson_hours'];
            }
            if($student_lesson->use_lesson_hours <= 0) continue;
            if($student_lesson->use_lesson_hours < $transfer_num + $refund_num) continue;

            $student_lesson->transfer_lesson_hours = $transfer_num;
            $student_lesson->refund_lesson_hours = $refund_num;
            $student_lesson->use_lesson_hours = $student_lesson->use_lesson_hours - $transfer_num - $refund_num;

            $student_lesson->allowField('transfer_lesson_hours,refund_lesson_hours,use_lesson_hours')->save();

        }

        $this->set_redis_next_update($field, $end, $total);
        return sprintf('%s更新成功，从第%s条到第%s条...', $this->update_fields[$field], $start, $end);
    }

}