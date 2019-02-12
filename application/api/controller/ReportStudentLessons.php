<?php
/** 
 * Author: luo
 * Time: 2018-04-03 10:38
**/

namespace app\api\controller;

use app\api\model\ClassStudent;
use app\api\model\OrderItem;
use app\api\model\Student;
use app\api\model\StudentLesson;
use app\common\db\Query;
use think\Request;

/**
 * 学员课时统计
 * Class ReportStudents
 * @package app\api\controller
 */
class ReportStudentLessons extends Base
{
    /**
     * @desc  学员剩余课时金额
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function student(Request $request)
    {
        $get = $request->get();
        /** @var Query $m_student */
        $m_student = new Student();
        if(!empty($get['cid'])) {
            $sids = (new ClassStudent())->where('cid', $get['cid'])->where('status', ClassStudent::STATUS_NORMAL)
                ->column('sid');
            if(empty($sids)) return $this->sendSuccess(['list' => []]);
            $m_student->where('sid', 'in', $sids);
        }

        $ret = $m_student->field('sid,student_name,student_lesson_times,student_lesson_remain_times')->getSearchResult($get);
        $m_sl = new StudentLesson();
        $m_oi = new OrderItem();
        foreach($ret['list'] as &$student) {
            $sl_list = $m_sl->where('sid', $student['sid'])->where('lesson_status', 'LT',2)->select();
            $student['remain_lesson_hours'] = 0;
            $student['remain_lesson_amount'] = 0;
            $student['total_lesson_hours'] = 0;

            //学生有多个student_lesson
            foreach($sl_list as $student_lesson) {
                $student['remain_lesson_hours'] += $student_lesson['remain_lesson_hours'];
                $student['total_lesson_hours'] += ($student_lesson['lesson_hours'] - $student_lesson['refund_lesson_hours'] - $student_lesson['transfer_lesson_hours']);
                $student_lesson['remain_lesson_amount'] = 0;
                $oi_list = $m_oi->where('sl_id', $student_lesson['sl_id'])->order('oi_id desc')
                    ->field('oi_id,sl_id,gtype,subtotal,origin_lesson_hours,present_lesson_hours,unit_lesson_hour_amount')
                    ->select();

                //student_lesson 可能有多个订单
                foreach($oi_list as $item) {
                    if($student_lesson['remain_lesson_hours'] <= 0) break;
                    $item_lesson_hours = $item['origin_lesson_hours'] + $item['present_lesson_hours'];
                    $tmp_hours = $student_lesson['remain_lesson_hours'] > $item_lesson_hours ? $item_lesson_hours : $student_lesson['remain_lesson_hours'];
                    $tmp_amount = $tmp_hours * $item['unit_lesson_hour_amount'];
                    $student_lesson['remain_lesson_hours'] -= $tmp_hours;
                    $student_lesson['remain_lesson_amount'] += $tmp_amount;
                }

                $student['remain_lesson_amount'] += $student_lesson['remain_lesson_amount'];
            }
        }

        return $this->sendSuccess($ret);
    }
}