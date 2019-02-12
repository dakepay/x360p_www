<?php
/**
 * Author: luo
 * Time: 2018/6/29 10:50
 */

namespace app\api\controller;


use app\api\model\ClassAttendance;
use app\api\model\CourseArrange;
use app\api\model\Customer;
use app\api\model\HomeworkTask;
use app\api\model\MarketChannel;
use app\api\model\MarketClue;
use app\api\model\Order;
use app\api\model\Review;
use app\api\model\Student;
use app\api\model\StudentArtwork;
use app\api\model\StudentAttendSchoolLog;
use app\api\model\StudentLeave;
use app\api\model\StudentLessonHour;
use app\api\model\StudentReturnVisit;
use app\api\model\Tally;

class Overview extends Base
{
    //市场名单概览
    public function market_clue()
    {
        $m_market_channel = new MarketChannel();
        $m_market_clue = new MarketClue();
        $market_channel = $m_market_channel->scope('bid')->limit(50)->select();

        $w_market_channel = [
            'bid' => request()->bid
        ];
        $w_market_clue = [
            'bid' => request()->bid
        ];
        if(!empty(input('mc_id'))) {
            $w_market_channel['mc_id'] = input('mc_id');
            $w_market_clue['mc_id'] = input('mc_id');
        }

        $total_num = $m_market_channel->where($w_market_channel)->cache(20)->sum('total_num');
        $valid_num = $m_market_channel->where($w_market_channel)->cache(20)->sum('valid_num');
        $visit_num = $m_market_channel->where($w_market_channel)->cache(20)->sum('visit_num');
        $deal_num = $m_market_channel->where($w_market_channel)->cache(20)->sum('deal_num');
        $customer_num = $m_market_clue->where($w_market_clue)->where('cu_id > 0')->cache(20)->count();

        $data = [
            'market_channel' => $market_channel,
            'total_num' => $total_num,
            'valid_num' => $valid_num,
            'visit_num' => $visit_num,
            'deal_num' => $deal_num,
            'customer_num' => $customer_num,
        ];

        return $this->sendSuccess($data);
    }

    //过去7天时间
    private function getLastSevenDays()
    {
        $today_time = time();

        $list = [];
        for($i = 0; $i <= 6; $i++) {
            $list[] = date('Ymd', ($today_time - $i * 24 * 60 *60));
        }
        sort($list);
        return $list;
    }

    //客户概览
    public function customer()
    {
        $seven_days = $this->getLastSevenDays();
        $trial_listen_num_list = [];
        $m_tla = new \app\api\model\TrialListenArrange();
        $m_cfu = new \app\api\model\CustomerFollowUp();
        foreach($seven_days as $day) {
            $trial_listen_num = $m_tla->scope('bid')->where('int_day', $day)->where('cu_id > 0')->cache(20)->count();
            $tmp_visit_num = $m_cfu->scope('bid')->where('visit_int_day', $day)->cache(20)->count();
            $trial_listen_num_list[] = [
                'day' => $day,
                'trial_listen_num' => $trial_listen_num,
                'visit_num' => $tmp_visit_num,
            ];

        }

        $customer_num_list = [];
        $m_customer = new Customer();
        foreach($seven_days as $day) {
            $day_start_time = strtotime($day . ' 00:00:00');
            $day_end_time = strtotime($day . ' 23:59:59');
            $where = [
                'create_time' => ['between', [$day_start_time, $day_end_time]],
            ];
            $tmp_customer_num = $m_customer->scope('bid')->where($where)->cache(20)->count();
            $customer_num_list[] = [
                'day' => $day,
                'customer_num' => $tmp_customer_num,
            ];
        }

        $signup_num_list = [];
        foreach($seven_days as $day) {
            $tmp_signup_num = $m_customer->scope('bid')->where('signup_int_day', $day)->cache(20)->count();
            $signup_num_list[] = [
                'day' => $day,
                'signup_num' => $tmp_signup_num,
            ];
        }

        $total_customer_num = $m_customer->scope('bid')->cache(20)->count();

        $from_did_list = $m_customer->scope('bid')->group('from_did')
            ->field('from_did, count(cu_id) as customer_num')->cache(20)->select();

        $data = [
            'trial_listen_num_list' => $trial_listen_num_list,
            'customer_num_list' => $customer_num_list,
            'signup_num_list' => $signup_num_list,
            'total_customer_num' => $total_customer_num,
            'from_did_list' => $from_did_list,
        ];

        return $this->sendSuccess($data);
    }


    public function customer_follow_up()
    {
        $m_cfu = new \app\api\model\CustomerFollowUp();
        $m_customer = new Customer();

        $total_connect_num = $m_cfu->scope('bid')->where('is_connect = 1')->cache(20)->count();
        $total_promise_num = $m_cfu->scope('bid')->where('is_promise = 1')->cache(20)->count();
        $total_visit_num = $m_cfu->scope('bid')->where('is_visit = 1')->cache(20)->count();
        $total_customer_num = $m_customer->scope('bid')->cache(20)->count();

        $seven_days = $this->getLastSevenDays();
        $follow_up_list = [];
        foreach($seven_days as $day) {
            $tmp_promise_num = $m_cfu->scope('bid')->where('is_promise = 1')->where('promise_int_day', $day)->cache(20)->count();
            $tmp_visit_num = $m_cfu->scope('bid')->where('is_visit = 1')->where('visit_int_day', $day)->cache(20)->count();
            $follow_up_list[] = [
                'day' => $day,
                'promise_num' => $tmp_promise_num,
                'visit_num' => $tmp_visit_num,
            ];
        }

        $data = [
            'connect_num' => $total_connect_num,
            'promise_num' => $total_promise_num,
            'visit_num' => $total_visit_num,
            'customer_num' => $total_customer_num,
            'follow_up_list' => $follow_up_list,
        ];
        
        return $this->sendSuccess($data);
    }

    public function trial_listen_arrange()
    {
        $m_tla = new \app\api\model\TrialListenArrange();
        $seven_days = $this->getLastSevenDays();
        $trial_listen_arrange_list = [];
        foreach($seven_days as $day) {
            $tmp_trial_listen_arrange_num = $m_tla->scope('bid')->where('int_day', $day)
                ->where('is_attendance = 1')->cache(20)->count();
            $tmp_not_attendance_listen_arrange_num = $m_tla->scope('bid')->where('int_day', $day)
                ->where('is_attendance = 0')->cache(20)->count();
            $trial_listen_arrange_list[] = [
                'day' => $day,
                'attendance_trial_listen_arrange_num' => $tmp_trial_listen_arrange_num,
                'not_attendance_listen_arrange_num' => $tmp_not_attendance_listen_arrange_num,
            ];
        }

        $listen_type_list = $m_tla->scope('bid')->group('listen_type')
            ->field('listen_type, count(tla_id) as trial_listen_arrange_num')->cache(20)->select();

        $not_attendance_listen_arrange_num = $m_tla->scope('bid')->where('is_attendance = 0')->cache(20)->count();
        $attendance_listen_arrange_num = $m_tla->scope('bid')->where('is_attendance = 1')->cache(20)->count();

        $student_listen_arrange_num = $m_tla->scope('bid')->where('sid > 0')->cache(20)->count();
        $customer_listen_arrange_num = $m_tla->scope('bid')->where('cu_id > 0')->cache(20)->count();

        $data = [
            'trial_listen_arrange_list' => $trial_listen_arrange_list,
            'listen_type_list' => $listen_type_list,
            'not_attendance_listen_arrange_num' => $not_attendance_listen_arrange_num,
            'attendance_listen_arrange_num' => $attendance_listen_arrange_num,
            'listen_object_list' => [
                ['student_listen_arrange_num' => $student_listen_arrange_num],
                ['customer_listen_arrange_num' => $customer_listen_arrange_num],
            ]
        ];

        return $this->sendSuccess($data);
    }

    //学生概览
    public function student()
    {
        $m_student = new Student();
        $seven_days = $this->getLastSevenDays();

        $student_list = [];
        foreach($seven_days as $day) {
            $day_start_time = strtotime($day . ' 00:00:00');
            $day_end_time = strtotime($day . ' 23:59:59');
            $where = [
                'create_time' => ['between', [$day_start_time, $day_end_time]],
            ];

            $tmp_student_num = $m_student->scope('bid')->where($where)->cache(20)->count();
            $student_list[] = [
                'day' => $day,
                'student_num' => $tmp_student_num,
            ];
        }

        $student_num = $m_student->scope('bid')->cache(20)->count();
        $status_list = $m_student->scope('bid')->group('status')->field('status, count(sid) as student_num')->cache(20)->select();

        $student_lesson_remain_hours = $m_student->scope('bid')->cache(20)->sum('student_lesson_remain_hours');
        $student_lesson_hours = $m_student->scope('bid')->cache(20)->sum('student_lesson_hours');

        $data = [
            'student_list' => $student_list,
            'student_num' => $student_num,
            'status_list' => $status_list,
            'student_lesso_remain_hours' => $student_lesson_remain_hours,
            'student_lesson_hours' => $student_lesson_hours
        ];

        return $this->sendSuccess($data);
    }

    //订单概览
    public function order()
    {
        $m_order = new Order();
        $seven_days = $this->getLastSevenDays();

        $paid_order_num = $m_order->scope('bid')->where('order_status', Order::ORDER_STATUS_PAID)->cache(20)->count();
        $paid_order_amount = $m_order->scope('bid')->cache(20)->sum('paid_amount');
        $unpaid_order_num = $m_order->scope('bid')->where('pay_status', Order::PAY_STATUS_NO)->cache(20)->count();
        $unpaid_order_amount = $m_order->scope('bid')->cache(20)->sum('unpaid_amount');

        $order_list = [];
        foreach($seven_days as $day) {
            $day_start_time = strtotime($day . ' 00:00:00');
            $day_end_time = strtotime($day . ' 23:59:59');
            $where = [
                'create_time' => ['between', [$day_start_time, $day_end_time]],
            ];

            $tmp_order_num = $m_order->scope('bid')->where($where)->count();
            $order_list[] = [
                'day' => $day,
                'order_num' => $tmp_order_num,
            ];
        }

        $data = [
            'paid_order_num' => $paid_order_num,
            'paid_order_amount' => $paid_order_amount,
            'unpaid_order_num' => $unpaid_order_num,
            'unpaid_order_amount' => $unpaid_order_amount,
            'order_list' => $order_list,
        ];

        return $this->sendSuccess($data);
    }

    //考勤概览
    public function attendance()
    {
        $m_ca = new ClassAttendance();
        $seven_days = $this->getLastSevenDays();
        $today = date('Ymd', time());

        $class_attendance_list = [];
        foreach($seven_days as $day) {
            $tmp_need_num = $m_ca->scope('bid')->where('int_day', $day)->cache(20)->sum('need_nums');
            $tmp_in_num = $m_ca->scope('bid')->where('int_day', $day)->cache(20)->sum('in_nums');
            $class_attendance_list[] = [
                'day' => $day,
                'need_num' => $tmp_need_num,
                'in_num' => $tmp_in_num,
            ];
        }

        $absence_num = $m_ca->scope('bid')->where('int_day', $today)->cache(20)->sum('absence_nums');
        $leave_num = $m_ca->scope('bid')->where('int_day', $today)->cache(20)->sum('leave_nums');
        $late_num = $m_ca->scope('bid')->where('int_day', $today)->cache(20)->sum('later_nums');
        $makeup_num = $m_ca->scope('bid')->where('int_day', $today)->cache(20)->sum('makeup_nums');

        $m_sl = new StudentLeave();
        $leave_type_list = $m_sl->scope('bid')->group('leave_type')->field('leave_type, count(slv_id) as leave_num')
            ->cache(20)->select();

        $data = [
            'class_attendance_list' => $class_attendance_list,
            'absence_num' => $absence_num,
            'leave_num' => $leave_num,
            'late_num' => $late_num,
            'makeup_num' => $makeup_num,
            'leave_type_list' => $leave_type_list,
        ];

        return $this->sendSuccess($data);
    }

    //课耗概览
    public function student_lesson_hour()
    {
        $m_slh = new StudentLessonHour();
        $seven_days = $this->getLastSevenDays();
        $lesson_hours_list = [];
        foreach($seven_days as $day) {
            $tmp_lesson_hours = $m_slh->scope('bid')->where('int_day', $day)->cache(20)->sum('lesson_hours');
            $lesson_hours_list[] = [
                'day' => $day,
                'lesson_hours' => $tmp_lesson_hours
            ];
        }

        $total_lesson_hours = $m_slh->scope('bid')->cache(20)->sum('lesson_hours');

        $lesson_amount_list = [];
        foreach($seven_days as $day) {
            $tmp_lesson_amount = $m_slh->scope('bid')->where('int_day', $day)->cache(20)->sum('lesson_amount');
            $lesson_amount_list[] = [
                'day' => $day,
                'lesson_amount' => $tmp_lesson_amount
            ];
        }

        $total_lesson_amount = $m_slh->scope('bid')->cache(20)->sum('lesson_amount');

        $data = [
            'lesson_hours_list' => $lesson_hours_list,
            'total_lesson_hours' => $total_lesson_hours,
            'lesson_amount_list' => $lesson_amount_list,
            'total_lesson_amount_list' => $total_lesson_amount,
        ];

        return $this->sendSuccess($data);
    }

    //学习管家概览
    public function mobile()
    {
        $bid = request()->bid;
        if(empty($bid)) return $this->sendError(400, 'bid 错误');

        $m_student = new Student();
        $student_num = $m_student->scope('bid')->cache(20)->count();
        $sql = "select count(s.sid) as num from x360p_student as s join x360p_user as u on s.first_uid = u.uid or s.second_uid = u.uid " .
            "where s.bid = {$bid} and s.is_delete = 0 and u.is_weixin_bind = 1";
        $rs = $m_student->cache(20)->query($sql);
        $bind_wechat_num = $rs[0]['num'] ?? 0;
        $bind_wechat_rate = round(($bind_wechat_num / $student_num), 3);

        $seven_days = $this->getLastSevenDays();
        $m_advice = new \app\api\model\Advice();
        $advice_num_list = [];
        foreach($seven_days as $per_day) {
            $day_start_time = strtotime($per_day . ' 00:00:00');
            $day_end_time = strtotime($per_day . ' 23:59:59');
            $where = [
                'create_time' => ['between', [$day_start_time, $day_end_time]],
            ];
            $tmp_advice_num = $m_advice->scope('bid')->where($where)->cache(20)->count();
            $advice_num_list[] = [
                'day' => $per_day,
                'advice_num' => $tmp_advice_num
            ];
        }

        $data = [
            'student_num' => $student_num,
            'bind_wechat_num' => $bind_wechat_num,
            'bind_wechat_rate' => $bind_wechat_rate,
            'advice_num_list' => $advice_num_list
        ];

        return $this->sendSuccess($data);
    }

    //课后服务概览
    public function after_class_service()
    {
        $m_review = new Review();
        $review_num = $m_review->scope('bid')->cache(20)->count();

        $m_srv = new StudentReturnVisit();
        $return_visit_num = $m_srv->scope('bid')->cache(20)->count();
        $is_connect_num = $m_srv->scope('bid')->cache(20)->count();

        $seven_days = $this->getLastSevenDays();
        $m_ht = new HomeworkTask();
        $m_sa = new StudentArtwork();
        $homework_num_list = [];
        foreach($seven_days as $day) {
            $day_start_time = strtotime($day . ' 00:00:00');
            $day_end_time = strtotime($day . ' 23:59:59');
            $where = [
                'create_time' => ['between', [$day_start_time, $day_end_time]],
            ];
            $homework_num = $m_ht->scope('bid')->where($where)->cache(20)->count();
            $artwork_num = $m_sa->scope('bi')->where($where)->cache(20)->count();
            $homework_num_list[] = [
                'day' => $day,
                'homework_num' => $homework_num,
                'artwork_num' => $artwork_num,
            ];
        }

        $data = [
            'review_num' => $review_num,
            'return_visit_num' => $return_visit_num,
            'is_connect_num' => $is_connect_num,
            'homework_num_list' => $homework_num_list
        ];

        return $this->sendSuccess($data);
    }

    public function before_class_service()
    {
        $uid = gvar('uid');

        //课前提醒
        $m_crl = new \app\api\model\CourseRemindLog();
        $my_remind_times = $m_crl->scope('bid')->where('create_uid', $uid)->group('ca_id')->cache(20)->count();
        $branch_remind_times = $m_crl->scope('bid')->group('ca_id')->cache(20)->count();
        $highest_course_remind_times_arr = $m_crl->scope('bid')->group('create_uid')
            ->field('count(distinct ca_id) as times')->order('times desc')->cache(20)->find();
        $highest_remind_times = $highest_course_remind_times_arr['times'] ?? 0;

        //$my_remind_person_num = $m_crl->scope('bid')->where('create_uid', $uid)->cache(20)->count();
        //$highest_remind_person_num_arr = $m_crl->scope('bid')->group('create_uid')
        //    ->field('count(sid) as person_num')->order('person_num desc')->cache(20)->find();
        //$highest_remind_person_num = $highest_remind_person_num_arr['person_num'];
        //$branch_remind_person_num = $m_crl->scope('bid')->cache(20)->count();

        //备课
        $m_cp = new \app\api\model\CoursePrepare();
        $my_prepare_times = $m_cp->scope('bid')->where('create_uid', $uid)->cache(20)->count();
        $highest_prepare_times_arr = $m_cp->scope('bid')->group('create_uid')
            ->field('create_uid,count(cp_id) as times')->order('times desc')->cache(20)->find();
        $highest_prepare_times = $highest_prepare_times_arr['times'] ?? 0;
        $branch_prepare_times = $m_cp->scope('bid')->cache(20)->count();

        //到离校通知
        $m_sasl = new StudentAttendSchoolLog();
        $my_attend_school_times = $m_sasl->scope('bid')->where('create_uid', $uid)->cache(20)->count();
        $highest_attend_school_times_arr = $m_sasl->scope('bid')->group('create_uid')
            ->field('create_uid,count(sasl_id) as times')->order('times desc')->cache(20)->find();
        $highest_attend_school_times = $highest_attend_school_times_arr['times'] ?? 0;
        $branch_attend_school_times = $m_sasl->scope('bid')->cache(20)->count();

        $data = [
            'course_remind' => [
                'my' => $my_remind_times,
                'highest' => $highest_remind_times,
            ],
            'branch_course_remind' => $branch_remind_times,
            'course_prepare' => [
                'my' => $my_prepare_times,
                'highest' => $highest_prepare_times
            ],
            'branch_course_prepare' => $branch_prepare_times,
            'attend_school' => [
                'my' => $my_attend_school_times,
                'highest' => $highest_attend_school_times,
            ],
            'branch_attend_school_times' => $branch_attend_school_times
        ];

        return $this->sendSuccess($data);
    }

    public function tally()
    {
        $m_tally = new Tally();
        $year_start = mktime(0,0,0,1,1,date('Y'));
        $year_end = time();
        $where_this_year = [
            'create_time' => ['between', [$year_start, $year_end]]
        ];

        $this_year_income = $m_tally->scope('bid')->where($where_this_year)->where('type', Tally::TALLY_TYPE_INCOME)
            ->cache(30)->sum('amount');
        $this_year_payout = $m_tally->scope('bid')->where($where_this_year)->where('type', Tally::TALLY_TYPE_PAYOUT)
            ->cache(30)->sum('amount');
        $total_income = $m_tally->scope('bid')->where('type', Tally::TALLY_TYPE_INCOME)
            ->cache(30)->sum('amount');
        $total_payout = $m_tally->scope('bid')->where('type', Tally::TALLY_TYPE_PAYOUT)
            ->cache(30)->sum('amount');

        $this_year_tt_id_income_list = $m_tally->scope('bid')->where($where_this_year)->where('type', Tally::TALLY_TYPE_INCOME)
            ->group('tt_id')->field('tt_id, sum(amount) total_amount')->cache(20)->select();
        $this_year_tt_id_payout_list = $m_tally->scope('bid')->where($where_this_year)->where('type', Tally::TALLY_TYPE_PAYOUT)
            ->group('tt_id')->field('tt_id, sum(amount) total_amount')->cache(20)->select();
        $total_tt_id_income_list = $m_tally->scope('bid')->where('type', Tally::TALLY_TYPE_INCOME)
            ->group('tt_id')->field('tt_id, sum(amount) total_amount')->cache(20)->select();
        $total_tt_id_payout_list = $m_tally->scope('bid')->where('type', Tally::TALLY_TYPE_PAYOUT)
            ->group('tt_id')->field('tt_id, sum(amount) total_amount')->cache(20)->select();

        $seven_days = $this->getLastSevenDays();
        $tally_day_list = [];
        foreach($seven_days as $day) {
            $day_start_time = strtotime($day . ' 00:00:00');
            $day_end_time = strtotime($day . ' 23:59:59');
            $where = [
                'create_time' => ['between', [$day_start_time, $day_end_time]],
            ];
            $income_amount = $m_tally->where($where)->where('type', Tally::TALLY_TYPE_INCOME)->cache(20)->sum('amount');
            $payout_amount = $m_tally->where($where)->where('type', Tally::TALLY_TYPE_PAYOUT)->cache(20)->sum('amount');
            $tally_day_list[] = [
                'day' => $day,
                'income_amount' => $income_amount,
                'payout_amount' => $payout_amount,
            ];
        }

        $tally_month_list = [];
        for($i = 11; $i >= 0; $i--) {
            $month_start_time = strtotime(date("Ym01 00:00:00", strtotime("-$i months")));
            $month_days = date('t', $month_start_time);
            $month_end_time = strtotime(date("Ym$month_days 23:59:59", strtotime("-$i months")));
            $where_month = [
                'create_time' => ['between', [$month_start_time, $month_end_time]]
            ];

            $income_amount = $m_tally->where($where_month)->where('type', Tally::TALLY_TYPE_INCOME)->cache(20)->sum('amount');
            $payout_amount = $m_tally->where($where_month)->where('type', Tally::TALLY_TYPE_PAYOUT)->cache(20)->sum('amount');
            $tally_month_list[] = [
                'month' => date('Ym', $month_end_time),
                'income_amount' => $income_amount,
                'payout_amount' => $payout_amount,
            ];
        }


        $data = [
            'this_year_income' => $this_year_income,
            'this_year_tt_id_income_list' => $this_year_tt_id_income_list,
            'this_year_payout' => $this_year_payout,
            'this_year_tt_id_payout_list' => $this_year_tt_id_payout_list,
            'total_income' => $total_income,
            'total_payout' => $total_payout,
            'total_tt_id_income_list' => $total_tt_id_income_list,
            'total_tt_id_payout_list' => $total_tt_id_payout_list,
            'tally_day_list' => $tally_day_list,
            'tally_month_list' => $tally_month_list,
        ];

        return $this->sendSuccess($data);
    }

    public function course_arrange()
    {
        $where_week = [
            'int_day' => ['between', [date('Ymd', strtotime('monday this week')), date('Ymd', time())]],
        ];
        $where_month = [
            'int_day' => ['between', [date('Ym01', time()), date('Ymd', time())]]
        ];
        $where_year = [
            'int_day' => ['between', [date('Y0101', time()), date('Ymd', time())]]
        ];
        $m_ca = new CourseArrange();
        $week_num = $m_ca->scope('bid')->where($where_week)->cache(20)->count();
        $month_num = $m_ca->scope('bid')->where($where_month)->cache(20)->count();
        $year_num = $m_ca->scope('bid')->where($where_year)->cache(20)->count();

        $m_slh = new StudentLessonHour();
        $week_lesson_hours = $m_slh->scope('bid')->where($where_week)->cache(20)->sum('lesson_hours');
        $month_lesson_hours = $m_slh->scope('bid')->where($where_month)->cache(20)->sum('lesson_hours');
        $year_lesson_hours = $m_slh->scope('bid')->where($where_year)->cache(20)->sum('lesson_hours');

        $week_lesson_type_list = $m_ca->scope('bid')->where($where_week)->group('lesson_type')
            ->field('lesson_type, count(ca_id) course_arrange_num')->cache(20)->select();
        $month_lesson_type_list = $m_ca->scope('bid')->where($where_month)->group('lesson_type')
            ->field('lesson_type, count(ca_id) course_arrange_num')->cache(20)->select();
        $year_lesson_type_list = $m_ca->scope('bid')->where($where_year)->group('lesson_type')
            ->field('lesson_type, count(ca_id) course_arrange_num')->cache(20)->select();

        $seven_days = $this->getLastSevenDays();
        $day_course_arrange_list = [];
        foreach($seven_days as $day) {
            $tmp_num = $m_ca->scope('bid')->where('int_day', $day)->cache(30)->count();
            $day_course_arrange_list[] = [
                'day' => $day,
                'course_arrange_num' => $tmp_num,
            ];
        }

        $data = [
            'week_course_arrange_num' => $week_num,
            'month_course_arrange_num' => $month_num,
            'year_course_arrange_num' => $year_num,
            'week_lesson_hours' => $week_lesson_hours,
            'month_lesson_hours' => $month_lesson_hours,
            'year_lesson_hours' => $year_lesson_hours,
            'week_lesson_type_list' => $week_lesson_type_list,
            'month_lesson_type_list' => $month_lesson_type_list,
            'year_lesson_type_list' => $year_lesson_type_list,
            'day_course_arrange_list' => $day_course_arrange_list
        ];

        return $this->sendSuccess($data);
    }

    public function classes()
    {
        $m_class = new \app\api\model\Classes();
        $starting_num = $m_class->scope('bid')->where('status', \app\api\model\Classes::STATUS_ING)
            ->cache(20)->count();
        $tmp_class_num = $m_class->scope('bid')->where('class_type', \app\api\model\Classes::CLASS_TYPE_TMP)
            ->cache(20)->count();
        $recruiting_num = $m_class->scope('bid')->where('status', 0)->cache('20')->count();
        $closed_num = $m_class->scope('bid')->where('status', \app\api\model\Classes::STATUS_CLOSE)
            ->cache(20)->count();

        $seven_days = $this->getLastSevenDays();
        $day_class_list = [];
        foreach($seven_days as $day) {
            $day_start_time = strtotime($day . ' 00:00:00');
            $day_end_time = strtotime($day . ' 23:59:59');
            $where_create_time = [
                'create_time' => ['between', [$day_start_time, $day_end_time]],
            ];
            $where_start_lesson_time = [
                'start_lesson_time' => ['between', [$day_start_time, $day_end_time]],
            ];
            $where_end_lesson_time = [
                'end_lesson_time' => ['between', [$day_start_time, $day_end_time]],
            ];
            $per_tmp_class_num = $m_class->scope('bid')->where('class_type', \app\api\model\Classes::CLASS_TYPE_TMP)
                ->where($where_create_time)->cache(20)->count();
            $per_recruiting_num = $m_class->scope('bid')->where('status', 0)->where($where_create_time)
                ->cache('20')->count();
            $per_starting_num = $m_class->scope('bid')
                ->where($where_start_lesson_time)->cache('20')->count();
            $per_closed_num = $m_class->scope('bid')
                ->where($where_end_lesson_time)->cache('20')->count();

            $day_class_list[] = [
                'day' => $day,
                'tmp_class_num' => $per_tmp_class_num,
                'recruiting_num' => $per_recruiting_num,
                'starting_num' => $per_starting_num,
                'closed_num' => $per_closed_num
            ];
        }

        $data = [
            'total_recruiting_num' => $recruiting_num,
            'total_starting_num' => $starting_num,
            'total_closed_num' => $closed_num,
            'total_tmp_class_num' => $tmp_class_num,
            'day_class_list' => $day_class_list
        ];

        return $this->sendSuccess($data);
    }


    /**
     * 体验课概览
     * @return [type] [description]
     */
    public function demo(){

        $total_enrolment_num = $this->m_classes->scope('bid,og_id')->where(array('is_demo'=>1,'status'=>0))->count();
        $total_started_num = $this->m_classes->scope('bid,og_id')->where(array('is_demo'=>1,'status'=>1))->count();
        $total_ended_num = $this->m_classes->scope('bid,og_id')->where(array('is_demo'=>1,'status'=>2))->count();
        $cids = $this->m_classes->scope('bid,og_id')->where(array('is_demo'=>1,'status'=>array('in',['0','1'])))->column('cid');
        $total_student_num = $this->m_class_student->scope('bid,og_id')->where(array('cid'=>array('in',$cids),'status'=>1))->count();
        $total_amount = $this->m_order->scope('bid,og_id')->where('is_demo',1)->sum('paid_amount');

        $seven_days = $this->getLastSevenDays();
        $demo_student_num_list = [];
        foreach($seven_days as $day) {
            $start = strtotime($day. '00:00:00');
            $end = strtotime($day. '23:59:59');
            $w['in_time'] = ['between',[$start,$end]];
            $w['status'] = 1;
            $w['cid'] = ['in',$cids];
            $student_num = $this->m_class_student->scope('bid,og_id')->where($w)->count();
            $demo_student_num_list[] = [
                'day'           =>  $day,
                'demo_student_num'  =>  $student_num,
            ];
        }

        $data = [
            'total_enrolment_num'   => $total_enrolment_num,
            'total_started_num'     => $total_started_num,
            'total_ended_num'       => $total_ended_num,
            'total_student_num'     => $total_student_num,
            'total_amount'          =>  $total_amount,
            'demo_student_num_list' => $demo_student_num_list,
        ];

        return $this->sendSuccess($data);
    }

}