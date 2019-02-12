<?php
/**
 * Author: luo
 * Time: 2018/3/9 15:08
 */

namespace app\api\model;

/**
 * Class StudentLessonHourDetail
 * @package app\api\model
 * @desc 学生课耗变化具体记录
 */
class StudentLessonHourDetail extends Base
{

    const TYPE_INC = 1; # 课耗增加
    const TYPE_DEC = 2; # 课耗减少

    /**
     * @desc  记录学生课时的变化，包括增加、减少，囊括所有变动
     * @param $sl_id int student_lesson id
     * @param $type int  变化类型 1：增加， 2：减少
     * @param $lesson_hours int  变化的课时数量
     */
    public static function recordStudentLessonVariation($sl_id, $type, $lesson_hours = 0, $oi_id = 0)
    {
        $self = new self();
        $student_lesson = StudentLesson::get($sl_id);
        if(empty($student_lesson)) exception('student_lesson不存在');

        if($type != StudentLessonHourDetail::TYPE_INC && $type != StudentLessonHourDetail::TYPE_DEC) {
            exception('变动类型不正确');
        }

        $m_oi = new OrderItem();

        $basic_data = [
            'og_id' => $student_lesson['og_id'],
            'bid' => $student_lesson['bid'],
            'sid' => $student_lesson['sid'],
            'lid' => $student_lesson['lid'],
            'sl_id' => $student_lesson['sl_id'],
            'type'  => $type,
            'int_day' => date('Ymd', time()),
        ];

        if($type == $self::TYPE_DEC) {
            //减少课时
            if($lesson_hours <= 0) exception('变动课时不能小于0');

            if($oi_id > 0) {
                //结转、退费
                $order_item = $m_oi->where('oi_id', $oi_id)->find();
                if(empty($order_item)) exception('没有相应的order_items');

                $data = [];
                $tmp_arr = [
                    'lesson_hours' => $lesson_hours,
                    'oi_id' => $oi_id,
                    'unit_lesson_hour_amount' => $order_item['unit_lesson_hour_amount'],
                    'lesson_amount' => $lesson_hours * $order_item['unit_lesson_hour_amount'],
                ];
                $data[] = array_merge($basic_data, $tmp_arr);


            } else {
                //如果没有传oi_id, 则通过sl_id查询相关item
                $had_dec_hours = $self->where('sl_id', $sl_id)->where('type', $self::TYPE_DEC)->sum('lesson_hours');
                $order_items = $m_oi->where('sl_id', $sl_id)->order('create_time asc')->select();
                if(empty($order_items)) exception('没有相应的order_items');

                $data = [];
                foreach($order_items as $per_item) {
                    if($lesson_hours <= 0) break;
                    $tmp_item_hours = $per_item['origin_lesson_hours'] + $per_item['present_lesson_hours'];
                    if($tmp_item_hours - $had_dec_hours > 0) {
                        $record_hours = ($tmp_item_hours - $had_dec_hours - $lesson_hours) > 0 ? $lesson_hours : ($tmp_item_hours - $had_dec_hours);
                        $tmp_arr = [
                            'lesson_hours' => $record_hours,
                            'oi_id' => $per_item['oi_id'],
                            'unit_lesson_hour_amount' => $per_item['unit_lesson_hour_amount'],
                            'lesson_amount' => $record_hours * $per_item['unit_lesson_hour_amount'],
                        ];
                        $data[] = array_merge($basic_data, $tmp_arr);

                        $lesson_hours -= $record_hours;
                    } else {
                        $had_dec_hours -= $tmp_item_hours;
                    }
                }
            }

        } else {
            //增加课时，目前是购买的时候会增加课时
            if($oi_id <= 0) exception('oi_id错误');
            $order_item = $m_oi->where('oi_id',  $oi_id)->find();

            if(empty($order_item) || $order_item['sl_id'] <= 0) exception('order_item不存在');

            $lesson_hours  = $lesson_hours > 0 ? $lesson_hours : $order_item['origin_lesson_hours'] + $order_item['present_lesson_hours'];
            $data = [];
            $tmp_arr = [
                'lesson_hours' => $lesson_hours,
                'oi_id' => $order_item['oi_id'],
                'unit_lesson_hour_amount' => $order_item['unit_lesson_hour_amount'],
                'lesson_amount' => $lesson_hours * $order_item['unit_lesson_hour_amount'],
            ];
            $data[] = array_merge($basic_data, $tmp_arr);
        }

        $rs = $self->saveAll($data);
        if($rs === false) exception($self->getErrorMsg());

        return true;
    }


}