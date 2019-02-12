<?php
/**
 * Author: luo
 * Time: 2018/1/20 15:10
 */

namespace app\sapi\controller;

use app\sapi\model\Broadcast;
use app\sapi\model\CourseArrange;
use app\sapi\model\Message;
use app\sapi\model\ReviewStudent;
use app\sapi\model\StudentAttendance;

class Notices extends Base
{

    //相关提示显示数量
    public function num()
    {

        $today_broadcast_num = $this->today_broadcast_num();
        $today_course_num = $this->today_course_num();
        $today_attendance_num = $this->today_attendance_num();
        $today_review_num = $this->today_review_num();
        $today_msg_num = $this->today_msg_num();

        $data = [
            'today_news_num' => $today_broadcast_num,
            'today_schedules_num' => $today_course_num,
            'today_attendances_num' => $today_attendance_num,
            'today_reviews_num' => $today_review_num,
            'today_msg_num' => $today_msg_num,
        ];

        return $this->sendSuccess($data);
    }

    private function today_broadcast_num()
    {
        $m_broadcast = new Broadcast();
        $where['create_time'] = ['between', [strtotime('today'), strtotime('tomorrow')]];
        $broadcast_num = $m_broadcast->where('type', $m_broadcast::TYPE_EXTERNAL)
            ->where($where)->count();
        return $broadcast_num;
    }

    private function today_course_num()
    {
        $sid = global_sid();
        $today = date('Ymd', time());

        $m_ca = new CourseArrange();

        $num = $m_ca->alias('ca')->join('course_arrange_student cas', 'cas.ca_id=ca.ca_id')
            ->where('ca.int_day',$today)->where('cas.sid', $sid)->count();

        return $num;
    }

    private function today_attendance_num()
    {
        $sid = global_sid();
        $m_attendance = new StudentAttendance();
        $num = $m_attendance->where('sid', $sid)->where('is_in = 1')->where('int_day', date('Ymd', time()))->count();
        return $num;
    }

    private function today_review_num()
    {
        $sid = global_sid();
        $m_review = new ReviewStudent();
        $review_num = $m_review->where('sid', $sid)->where('int_day', date('Ymd', time()))->count();
        return $review_num;
    }

    private function today_msg_num()
    {
        $sid = global_sid();
        $where['create_time'] = ['between', [strtotime('today'), strtotime('tomorrow')]];
        $m_message = new Message();
        $msg_num = $m_message->where('sid', $sid)->where($where)->count();
        return $msg_num;
    }

}