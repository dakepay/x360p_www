<?php

namespace app\api\controller;

use app\api\model\Branch;
use app\api\model\ClassAttendance;
use app\api\model\Customer;
use app\api\model\MarketClue;
use app\api\model\OrderReceiptBill;
use app\api\model\Order;
use app\api\model\OrderRefund;
use app\api\model\ReportSummary;
use app\api\model\ReportTotal;
use app\api\model\Student;
use app\api\model\StudentAttendance;
use app\api\model\StudentLessonHour;
use app\api\model\TrialListenArrange as TrialModel;
use think\Request;

class ReportSummaries extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->only(['start_date', 'end_date']);
        $rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期'   => 'require|date',
        ];
        $rs = $this->validate($input, $rule);
        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }
        $w = [];
        if (!empty($input['start_date'])) {
            $w['int_day'] = ['between', [date('Ymd', strtotime($input['start_date'])), date('Ymd', strtotime($input['end_date']))]];
        }
        $w['og_id'] = gvar('og_id');
        $bids = (new Branch())->column('bid');
        $bids = array_unique($bids);
        $w['bid'] = ['in', $bids];
        $fields = ReportSummary::getSumFields();
        array_unshift($fields, 'bid');
        if (!isset($input['order_field']) || !in_array($input['order_field'], $fields)) {
            $input['order_field'] = 'bid';
            $input['order_sort']  = 'asc';
        }
        
        $data = ReportSummary::where($w)->field($fields)->group('bid')->getSearchResult($input,[],false);

        // echo '123';exit;
        
        if(empty($data)){
            $rs = ReportSummary::buildReport($input);
            if ($rs !== true) {
                return $this->sendError(400, $rs);
            }
            $data = ReportSummary::where($w)->field($fields)->group('bid')->getSearchResult($input,[],false);
        }
        return $this->sendSuccess($data);
    }

    public function post(Request $request)
    {
        $input = $request->post();

        // print_r($input);exit;

        $rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期'   => 'require|date',
        ];
        $rs = $this->validate($input, $rule);
        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }
        $rs = ReportSummary::buildReport($input);
        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }
        return $this->sendSuccess();
    }

    /**
     * @desc  手机机构端周报，月报
     * @author luo
     * @method GET
     */
    public function overview()
    {
        $start_day = input('start_day');
        $end_day = input('end_day');
        if(empty($start_day) || empty($end_day)) return $this->sendError(400,'param error');
        $redis = redis();
        $redis_key = sprintf('report_summaries_overview_%s_%s', $start_day, $end_day);
        $ret = $redis->get($redis_key);
        if(!empty($ret)) return $this->sendSuccess($ret);

        $student_data = $this->getStudentData($start_day, $end_day);
        $order_data = $this->getOrderData($start_day, $end_day);
        $attendance_data = $this->getAttendanceData($start_day, $end_day);
        $ret = [
            'student_data' => $student_data,
            'order_data' => $order_data,
            'attendance_data' => $attendance_data,
        ];
        $redis->set($redis_key, $ret);

        return $this->sendSuccess($ret);
    }

    //报生相关的报表
    private function getStudentData($start_day, $end_day)
    {
        $start_time = strtotime($start_day);
        $end_time = strtotime($end_day);
        $data = [
            'student_inc_num' => 0,
            'customer_inc_num' => 0,
            'transfer_rate' => 0,
            'trial_listen_num' => 0,
        ];

        $where = [];
        $where['create_time'] = ['between', [$start_time, $end_time]];
        $group = 'bid';

        //增加的学生
        $m_student = new Student();
        $field = 'bid, count(sid) as student_inc_num';
        $data['student_inc_num'] = $m_student->where($where)->group($group)->field($field)->select();

        //增加的客户
        $m_customer = new Customer();
        $data['customer_inc_num'] = $m_customer->where($where)->count();
        $field = 'bid, count("cu_id") as customer_inc_num';
        $data['customer_inc_num'] = $m_customer->where($where)->group($group)->field($field)->select();

        //转化率
        $t_where = $where;
        $t_where['is_reg'] = 1;
        $field = 'bid, count("cu_id") as is_reg_num';
        $is_reg_num_arr = $m_customer->where($t_where)->group($group)->field($field)->select();
        $data['is_reg_num'] = $is_reg_num_arr;
        $data['transfer_rate'] = [];
        foreach($is_reg_num_arr as $row) {
            $key = array_search($row['bid'], array_column($data['customer_inc_num'], 'bid'));
            $tmp = [
                'bid' => $row['bid'],
                'transfer_rate' => $key !== false ? round(($row['is_reg_num'] / $data['customer_inc_num'][$key]['customer_inc_num']),2) : 0,
            ];
            $data['transfer_rate'][] = $tmp;
        }

        //试听次数
        $m_tla = new TrialModel();
        $field = 'bid, count("tla_id") as trial_listen_num';
        $data['trial_listen_num'] = $m_tla->where($where)->group($group)->field($field)->select();

        return $data;
    }

    //订单相关的报表
    private function getOrderData($start_day, $end_day)
    {
        $start_time = strtotime($start_day);
        $end_time = strtotime($end_day);
        $data = [
            'receipt_bill_num' => 0,
            'receipt_bill_amount' => 0,
            'refund_num' => 0,
            'refund_amount' => 0,
        ];

        $where = [];
        $where['create_time'] = ['between', [$start_time, $end_time]];
        $group = 'bid';

        $m_orb = new OrderReceiptBill();
        $field = 'bid, count("orb_id") as receipt_bill_num';
        $data['receipt_bill_num'] = $m_orb->where($where)->group($group)->field($field)->select();
        $field = 'bid, sum(money_paid_amount) as receipt_bill_amount';
        $data['receipt_bill_amount'] = $m_orb->where($where)->group($group)->field($field)->select();

        $m_or = new OrderRefund();
        $field = 'bid, count("or_id") as refund_num';
        $data['refund_num'] = $m_or->where($where)->group($group)->field($field)->select();
        $field = 'bid, sum(refund_amount) as refund_amount';
        $data['refund_amount'] = $m_or->where($where)->group($group)->field($field)->select();

        return $data;
    }

    //考勤相关的数据
    private function getAttendanceData($start_day, $end_day)
    {
        $data = [
            'class_attendance_num' => 0,
            'student_attendance_num' => 0,
            'lesson_consume_amount' => 0,
            'lesson_consume_hours' => 0,
        ];

        $where = [];
        $where['int_day'] = ['between', [$start_day, $end_day]];
        $group = 'bid';

        //班级考勤次数
        $m_ca = new ClassAttendance();
        $field = 'bid, count(catt_id) as class_attendance_num';
        $data['class_attendance_num'] = $m_ca->where($where)->group($group)->field($field)->select();

        //学生考勤次数
        $m_sa = new StudentAttendance();
        $field = 'bid, count(satt_id) as student_attendance_num';
        $data['student_attendance_num'] = $m_sa->where($where)->group($group)->field($field)->select();

        //课消相关数量
        $m_slh = new StudentLessonHour();
        $field = 'bid, sum(lesson_amount) as lesson_consume_amount';
        $data['lesson_consume_amount'] = $m_slh->where($where)->group($group)->field($field)->select();

        $field = 'bid, sum(lesson_hours) as lesson_consume_hours';
        $data['lesson_consume_hours'] = $m_slh->where($where)->group($group)->field($field)->select();

        return $data;
    }



    /**
     * 手机端  校区运营总部  新
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function branch_summaries(Request $request)
    {
        $input = $request->param();

        if(!isset($input['start_date'])){
            /*$date = date("Y-m-d");
            $first = date('Y-m-01', strtotime($date));
            $last  = date('Y-m-d', strtotime("$first +1 month -1 day"));*/
            $first = '1970-01-02';
            $last  = '9999-12-31';
            $input['start_date'] = $first;
            $input['end_date'] = $last;
        }

        $params['bid'] = isset($input['bids']) ? explode(',',$input['bids']) : [];

        $start_ts = strtotime($input['start_date'].' 08:00:01');
        $end_ts = strtotime($input['end_date'].' 23:59:59');
        $start_int_day = format_int_day($input['start_date']);
        $end_int_day = format_int_day($input['end_date']);

        $params['between_ts'] = [$start_ts,$end_ts];
        $params['between_int_day'] = [$start_int_day,$end_int_day];

        /*$redis = redis();
        $redis_key = sprintf('branch_summaries_%s_%s', $input['start_date'], $input['end_date']);
        $data = $redis->get($redis_key);
        if(!empty($data)) return $this->sendSuccess($data);*/

        $student_data = $this->get_student_data_value($params);
        $order_data = $this->get_order_data_value($params);
        $attendance_data = $this->get_attendance_data_value($params);
        $data = array(
            'student_data'    => $student_data,
            'order_data'       => $order_data,
            'attendance_data' => $attendance_data
        );

        // $redis->set($redis_key, $data);
        
        return $this->sendSuccess($data);

    }


    protected function get_student_data_value($params)
    {
        $data = array(
            'new_student_nums'  => 0,   //新增学员
            'new_customer_nums' => 0,   //新增客户
            'market_clue_nums'  => 0,   //新增市场名单
            'trial_nums'        => 0,   //新增试听
        );

        $group = 'bid';

        // 新增学员数
        $mStudent = new Student;
        $field = 'bid, count(sid) as new_student_nums';
        $data['new_student_nums'] = $mStudent->where(['bid'=>['in',$params['bid']],'in_time'=>['between',$params['between_ts']]])->group($group)->field($field)->select();
        $data['total_new_student_nums'] = $mStudent->where(['bid'=>['in',$params['bid']],'in_time'=>['between',$params['between_ts']]])->count();

        // 新增客户数
        $mCustomer = new Customer;
        $field = 'bid, count(cu_id) as new_customer_nums';
        $data['new_customer_nums'] = $mCustomer->where(['bid'=>['in',$params['bid']],'get_time'=>['between',$params['between_ts']]])->group($group)->field($field)->select();
        $data['total_new_customer_nums'] = $mCustomer->where(['bid'=>['in',$params['bid']],'get_time'=>['between',$params['between_ts']]])->count();


        //新增市场名单数
        $mMarketClue = new MarketClue;
        $field = 'bid, count(mcl_id) as new_market_clue_nums';
        $data['new_market_clue_nums'] = $mMarketClue->where(['bid'=>['in',$params['bid']],'get_time'=>['between',$params['between_ts']]])->group($group)->field($field)->select();
        $data['total_new_market_clue_nums'] = $mMarketClue->where(['bid'=>['in',$params['bid']],'get_time'=>['between',$params['between_ts']]])->count();

        //新增试听人次数
        $mTrial = new TrialModel;
        $field = 'bid, count(tla_id) as new_trial_nums';
        $data['new_trial_nums'] = $mTrial->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']]])->group($group)->field($field)->select();
        $data['total_new_trial_nums'] = $mTrial->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']]])->count();


        unset($data['market_clue_nums']);
        unset($data['trial_nums']);

        return $data;
    }


    protected function get_order_data_value($params)
    {
        $data = array(
            'new_order_nums'      => 0,   //新增订单数
            'new_order_amount'    => 0.000000,  // 新增订单金额
            'refund_order_nums'   => 0, //退费订单数
            'refund_order_amount' => 0.000000  //退费金额
        );

        $group = 'bid';

        //新增订单数 订单金额
        $mOrder = new Order;
        $field = 'bid, count(oid) as new_order_nums';
        $data['new_order_nums'] = $mOrder->where(['bid'=>['in',$params['bid']],'paid_time'=>['between',$params['between_ts']],'pay_status'=>2,'is_debit'=>0])->group($group)->field($field)->select();
        $data['total_new_order_nums'] = $mOrder->where(['bid'=>['in',$params['bid']],'paid_time'=>['between',$params['between_ts']],'pay_status'=>2,'is_debit'=>0])->count();

        $field = 'bid, sum(order_amount) as new_order_amount';
        $data['new_order_amount'] = $mOrder->where(['bid'=>['in',$params['bid']],'paid_time'=>['between',$params['between_ts']],'pay_status'=>2,'is_debit'=>0])->group($group)->field($field)->select();
        $data['total_new_order_amount'] = $mOrder->where(['bid'=>['in',$params['bid']],'paid_time'=>['between',$params['between_ts']],'pay_status'=>2,'is_debit'=>0])->sum('order_amount');

        // 新增退费数 退费金额
        $mOrderRefund = new OrderRefund;
        $field = 'bid, count(or_id) as refund_order_nums';
        $data['refund_order_nums'] = $mOrderRefund->where(['bid'=>['in',$params['bid']],'refund_int_day'=>['between',$params['between_int_day']],'oid'=>['gt',0]])->group($group)->field($field)->select();
        $data['total_refund_order_nums'] = $mOrderRefund->where(['bid'=>['in',$params['bid']],'refund_int_day'=>['between',$params['between_int_day']],'oid'=>['gt',0]])->count();

        $field = 'bid, sum(refund_amount) as refund_order_amount';
        $data['refund_order_amount'] = $mOrderRefund->where(['bid'=>['in',$params['bid']],'refund_int_day'=>['between',$params['between_int_day']],'oid'=>['gt',0]])->group($group)->field($field)->select();
        $data['total_refund_order_amount'] = $mOrderRefund->where(['bid'=>['in',$params['bid']],'refund_int_day'=>['between',$params['between_int_day']],'oid'=>['gt',0]])->sum('refund_amount');

        return $data;
    }


    protected function get_attendance_data_value($params)
    {
        $data = array(
            'attendance_times'         => 0,
            'attendance_student_times' => 0,
            'attendance_lesson_hours'  => 0.00,
            'attendance_lesson_amount' => 0.000000
        );

        $group = 'bid';
        // 考勤次数  考勤人次数
        $mStudentAttendance = new StudentAttendance;
        $field = 'bid, count(distinct ca_id) as attendance_times';
        $data['attendance_times'] = $mStudentAttendance->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']]])->group($group)->field($field)->select();
        $ca_ids = $mStudentAttendance->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']]])->column('ca_id');
        $data['total_attendance_times'] = count(array_unique($ca_ids));

        $field = 'bid, count(satt_id) as attendance_student_times';
        $data['attendance_student_times'] = $mStudentAttendance->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']],'is_in'=>1])->field($field)->group($group)->select();
        $data['total_attendance_student_times'] = $mStudentAttendance->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']],'is_in'=>1])->count();


        // 考勤课消课时   考勤课消金额
        $mStudentLessonHour = new StudentLessonHour;
        $consume_type = ['0','1','2','3'];
        $field = 'bid, sum(lesson_hours) as attendance_lesson_hours';
        $data['attendance_lesson_hours'] = $mStudentLessonHour->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']],'consume_type'=>['in',$consume_type]])->group($group)->field($field)->select();
        $data['total_attendance_lesson_hours'] = $mStudentLessonHour->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']],'consume_type'=>['in',$consume_type]])->sum('lesson_hours');

        $field = 'bid, sum(lesson_amount) as attendance_lesson_amount';
        $data['attendance_lesson_amount'] = $mStudentLessonHour->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']],'consume_type'=>['in',$consume_type]])->group($group)->field($field)->select();
        $data['total_attendance_lesson_amount'] = $mStudentLessonHour->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']],'consume_type'=>['in',$consume_type]])->sum('lesson_amount');
   

        return $data;
    }











}