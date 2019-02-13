<?php

namespace app\api\model;

use app\common\Report;

class ReportKey extends Report
{
	protected $report_name = '关键指标报表';
	protected $report_table_name = 'report_key';

	protected $bid_row_field_value = [];

	protected $report_fields = [
	    // 校区
	    'student_nums' => ['title'=>'在读学员数','type'=>Report::FTYPE_INT],
	    'remain_lesson_hours' => ['title'=>'剩余课时','type'=>Report::FTYPE_DECIMAL132],
	    'remain_lesson_amount' => ['title'=>'剩余课时金额','type'=>Report::FTYPE_DECIMAL156],
	    'class_nums' => ['title'=>'班级数量','type'=>Report::FTYPE_INT],
	    'school_student_nums' => ['title'=>'满校数量','type'=>Report::FTYPE_INT],
	    'expire_student_nums' => ['title'=>'合同到期学员数','type'=>Report::FTYPE_INT],
        // 教务
        'new_student_nums'   => ['title'=>'新签学员数','type'=>Report::FTYPE_INT],
	    'renew_student_nums' => ['title'=>'续费学员数','type'=>Report::FTYPE_INT],
	    'renew_order_nums' => ['title'=>'续费订单数','type'=>Report::FTYPE_INT],
	    'order_nums' => ['title'=>'订单数','type'=>Report::FTYPE_INT],
	    'refer_nums' => ['title'=>'转介绍人数','type'=>Report::FTYPE_INT],
	    'refer_deal_nums' => ['title'=>'转介绍成交人数','type'=>Report::FTYPE_INT],
	    'refund_student_nums' => ['title'=>'退费学员数','type'=>Report::FTYPE_INT],
	    'cr_arrange_nums' => ['title'=>'教室周平均排课量','type'=>Report::FTYPE_INT],
	    'class_room_base_nums' => ['title'=>'校区每间教室每周上课基数','type'=>Report::FTYPE_INT],
        // 市场
	    'mc_student_nums' => ['title'=>'市场名单数','type'=>Report::FTYPE_INT],
	    'mc_deal_amount' => ['title'=>'市场渠道成单金额','type'=>Report::FTYPE_DECIMAL156],
	    'market_channel_nums' => ['title'=>'来源渠道数量','type'=>Report::FTYPE_INT],
	    'mc_customer_nums' => ['title'=>'（市场转）客户名单数','type'=>Report::FTYPE_INT],
	    'mc_valid_nums' => ['title'=>'市场有效名单数','type'=>Report::FTYPE_INT],
	    'mc_sign_nums' => ['title'=>'市场名单签约数','type'=>Report::FTYPE_INT],
	    // 顾问
	    'sale_amount' => ['title'=>'销售金额','type'=>Report::FTYPE_DECIMAL156],
	    'sale_lesson_hours' => ['title'=>'销售课时数','type'=>Report::FTYPE_DECIMAL132],
	    'customer_nums' => ['title'=>'客户名单数','type'=>Report::FTYPE_INT],
	    'valid_communicate_nums' => ['title'=>'有效沟通数','type'=>Report::FTYPE_INT],
	    'accept_nums' => ['title'=>'诺到人数','type'=>Report::FTYPE_INT],
	    'trial_nums' => ['title'=>'试听人数','type'=>Report::FTYPE_INT],
	    'trial_sign_nums' => ['title'=>'试听报名人数','type'=>Report::FTYPE_INT],
	    // 教学
	    'employee_lesson_amount' => ['title'=>'课耗金额','type'=>Report::FTYPE_DECIMAL156],
	    'employee_lesson_hours' => ['title'=>'学员课耗课时数','type'=>Report::FTYPE_DECIMAL132],
	    'attendance_times' => ['title'=>'出勤学员人次','type'=>Report::FTYPE_INT],
	    'should_attendance_times' => ['title'=>'应出勤学员人次','type'=>Report::FTYPE_INT],
	    'no_class_student_nums' => ['title'=>'未分班学员数','type'=>Report::FTYPE_INT],
	    'no_arrange_student_nums' => ['title'=>'未排课学员数','type'=>Report::FTYPE_INT],
	    'no_attendance_nums' => ['title'=>'未出勤学员数','type'=>Report::FTYPE_INT],
	    'attendance_nums' => ['title'=>'实际出勤人数','type'=>Report::FTYPE_INT],
	];

	public function getReportFields()
	{
		return $this->report_fields;
	}


	public function getMonthSectionReport($input,$pagenation = false)
	{
		$bid = isset($input['bid']) ? $input['bid'] : request()->bid;

		if(!isset($input['start_date'])){
			$date = date("Y-m-d");
			$first = date('Y-m-01', strtotime($date));
			$last  = date('Y-m-d', strtotime("$first +1 month -1 day"));
			$input['start_date'] = $first;
			$input['end_date'] = $last;
		}

		$og_id = gvar('og_id');

		$w['start_int_day'] = format_int_day($input['start_date']);
		$w['end_int_day']   = format_int_day($input['end_date']);
		$w['og_id'] = $og_id;
		$w['bid'] = $bid;

		$day_diff = int_day_diff($w['start_int_day'],$w['end_int_day']);
        if($day_diff > 31){
            return $this->user_error('查询时间间隔不可超过1个月');
        }
        
        $model = new self();
		$result = $model->where($w)->find();

		if(empty($result) || (isset($input['refresh']) && $input['refresh'] == 1)){
			$result = $this->buildMonthSectionReport($input['start_date'],$input['end_date'],$bid);
		}

		$result['params'] = $input;
		$result['month'] = date('Y年m月d日',strtotime($input['start_date'])).'-'.date('Y年m月d日',strtotime($input['end_date']));
		$result['branch_name'] = get_branch_name($bid);


        // 校区
        $student_nums           = isset($result['student_nums']) ? $result['student_nums'] : 0;
        $remain_lesson_amount           = isset($result['remain_lesson_amount']) ? $result['remain_lesson_amount'] : 0;
        $remain_lesson_hours    = isset($result['remain_lesson_hours']) ? $result['remain_lesson_hours'] : 0.00;
        $class_nums             = isset($result['class_nums']) ? $result['class_nums'] : 0;
        $class_avg_student_nums = $class_nums ? round($student_nums/$class_nums,2) : 0.00;
        $full_school_student_nums = isset($result['school_student_nums']) ? $result['school_student_nums'] : 0;
        $differ_student_nums    = $student_nums - $full_school_student_nums;
        $expire_student_nums  = isset($result['expire_student_nums']) ? $result['expire_student_nums'] : 0;
		$result['branch'] = [
            'student_nums'             => $student_nums,
            'remain_lesson_hours'      => $remain_lesson_hours,
            'remain_lesson_amount'     => $remain_lesson_amount,
            'class_nums'               => $class_nums,
            'class_avg_student_nums'   => $class_avg_student_nums,
            'full_school_student_nums' => $full_school_student_nums,
            'differ_student_nums'      => $differ_student_nums,
            'expire_student_nums'      => $expire_student_nums,
		];
        // 教务
        $new_student_nums  = isset($result['new_student_nums']) ? $result['new_student_nums'] : 0;
        $renew_student_nums  = isset($result['renew_student_nums']) ? $result['renew_student_nums'] : 0;
        $renew_order_nums = isset($result['renew_order_nums']) ? $result['renew_order_nums'] : 0;
        $order_nums = isset($result['order_nums']) ? $result['order_nums'] : 0;
        $renew_rate = $order_nums ? round($renew_order_nums/$order_nums,2) : 0;
        $refer_deal_nums     = isset($result['refer_deal_nums']) ? $result['refer_deal_nums'] : 0;
        $refer_nums          = isset($result['refer_nums']) ? $result['refer_nums'] : 0;
        $refer_deal_rate     = $refer_nums ? round($refer_deal_nums/$refer_nums,6) : 0.00;
        $refund_student_nums = isset($result['refund_student_nums']) ? $result['refund_student_nums'] : 0;
        $refund_rate         = $student_nums ? round($refund_student_nums/$student_nums,2) : 0.00;
        $cr_arrange_nums     = isset($result['cr_arrange_nums']) ? $result['cr_arrange_nums'] : 0;
        $class_room_base_nums = isset($result['class_room_base_nums']) ? $result['class_room_base_nums'] : 0;
        $cr_arrange_rate     = $class_room_base_nums ? round($cr_arrange_nums/$class_room_base_nums,2) : 0.00;
		$result['educate'] = [
		    'new_student_nums'    => $new_student_nums,
            'renew_student_nums'  => $renew_student_nums,
            'renew_rate'          => $renew_rate,
            'refer_deal_nums'     => $refer_deal_nums,
            'refer_deal_rate'     => $refer_deal_rate,
            'refund_student_nums' => $refund_student_nums,
            'refund_rate'         => $refund_rate,
            'cr_arrange_nums'     => $cr_arrange_nums,
            'cr_arrange_rate'     => $cr_arrange_rate,
		];
        // 市场
        $mc_student_nums     = isset($result['mc_student_nums']) ? $result['mc_student_nums'] : 0;
        $mc_deal_amount      = isset($result['mc_deal_amount']) ? $result['mc_deal_amount'] : 0.000000;
        $market_channel_nums = isset($result['market_channel_nums']) ? $result['market_channel_nums'] : 0;
        $mc_customer_nums    = isset($result['mc_customer_nums']) ? $result['mc_customer_nums'] : 0;
        $mc_valid_nums       = isset($result['mc_valid_nums']) ? $result['mc_valid_nums'] : 0;
        $mc_valid_rate       = $mc_customer_nums ? round($mc_valid_nums/$mc_customer_nums,4) : 0.00;
        $mc_sign_nums        = isset($result['mc_sign_nums']) ? $result['mc_sign_nums'] : 0;
        $mc_sign_rate        = $mc_customer_nums ? round($mc_sign_nums/$mc_customer_nums,4) : 0.0000;
		$result['market'] = [
            'mc_student_nums'     => $mc_student_nums,
            'mc_deal_amount'      => $mc_deal_amount,
            'market_channel_nums' => $market_channel_nums,
            'mc_valid_nums'       => $mc_valid_nums,
            'mc_valid_rate'       => $mc_valid_rate,
            'mc_sign_nums'        => $mc_sign_nums,
            'mc_sign_rate'        => $mc_sign_rate,
		];
        // 顾问
        $sale_amount            = isset($result['sale_amount']) ? $result['sale_amount'] : 0.000000;
        $sale_lesson_hours      = isset($result['sale_lesson_hours']) ? $result['sale_lesson_hours'] : 0.00;
        $customer_nums          = isset($result['customer_nums']) ? $result['customer_nums'] : 0;
        $valid_communicate_nums = isset($result['valid_communicate_nums']) ? $result['valid_communicate_nums'] : 0;
        $accept_nums            = isset($result['accept_nums']) ? $result['accept_nums'] : 0;
        $accept_rate            = $valid_communicate_nums ? round($accept_nums/$valid_communicate_nums,2) : 0.00;
        $trial_nums             = isset($result['trial_nums']) ? $result['trial_nums'] : 0;
        $trial_sign_nums        = isset($result['trial_sign_nums']) ? $result['trial_sign_nums'] : 0;
        $trial_sign_rate        = $trial_nums ? round($trial_sign_nums/$trial_nums,2) : 0.00;
		$result['counselor'] = [
            'sale_amount'            => $sale_amount,
            'sale_lesson_hours'      => $sale_lesson_hours,
            'customer_nums'          => $customer_nums,
            'valid_communicate_nums' => $valid_communicate_nums,
            'accept_nums'            => $accept_nums,
            'accept_rate'            => $accept_rate,
            'trial_nums'             => $trial_nums,
            'trial_sign_nums'        => $trial_sign_nums,
            'trial_sign_rate'        => $trial_sign_rate,
		];
        // 教学
        $employee_lesson_amount  = isset($result['employee_lesson_amount']) ? $result['employee_lesson_amount'] : 0.000000;
        $employee_lesson_hours   = isset($result['employee_lesson_hours']) ? $result['employee_lesson_hours'] : 0.00;
        $attendance_times        = isset($result['attendance_times']) ? $result['attendance_times'] : 0;
        $should_attendance_times = isset($result['should_attendance_times']) ? $result['should_attendance_times'] : 0;
        $attendance_rate         = $attendance_times ? round($attendance_times/$should_attendance_times,2) : 0.00;
        $no_arrange_student_nums = isset($result['no_arrange_student_nums']) ? $result['no_arrange_student_nums'] : 0;
        $attendance_nums         = isset($result['attendance_nums']) ? $result['attendance_nums'] : 0;
        $no_class_student_nums   = isset($result['no_class_student_nums']) ? $result['no_class_student_nums'] : 0;
        $no_attendance_nums   = isset($result['no_attendance_nums']) ? $result['no_attendance_nums'] : 0;
		$result['teach'] = [
            'employee_lesson_amount'  => $employee_lesson_amount,
            'employee_lesson_hours'   => $employee_lesson_hours,
            'attendance_times'        => $attendance_times,
            'should_attendance_times' => $should_attendance_times,
            'attendance_rate'         => $attendance_rate,
            'no_arrange_student_nums' => $no_arrange_student_nums,
            'attendance_nums'         => $attendance_nums,
            'no_class_student_nums'   => $no_class_student_nums,
            'no_attendance_nums'      => $no_attendance_nums,
		];

		return $result;
	}


	public function buildMonthSectionReport($start,$end,$bid)
	{
		$start_ts = strtotime($start.' 00:00:00');
		$end_ts   = strtotime($end.' 23:59:59');

		$start_int_day = format_int_day($start);
		$end_int_day   = format_int_day($end);

		$params['between_ts'] = [$start_ts,$end_ts];
		$params['between_int_day'] = [$start_int_day,$end_int_day];
		$params['bid'] = $bid;
		$params['og_id'] = gvar('og_id');

		$this->init_bid_row_field($params);
		$this->build_month_section_report_before($params);
		$this->build_month_section_report_center($params);
        $this->build_month_section_report_after($params);

		return $this->save_month_section_report($params);

	}


	protected function init_bid_row_field(&$params)
	{
		$this->bid_row_field_value['og_id'] = $params['og_id'];
		$this->bid_row_field_value['bid'] = $params['bid'];
		$this->bid_row_field_value['start_int_day'] = $params['between_int_day'][0];
		$this->bid_row_field_value['end_int_day'] = $params['between_int_day'][1];
		return $this;
	}

	protected function build_month_section_report_before(&$params)
	{
        $this->count_student($params);
        $this->count_class($params);
        $this->count_employee_lesson_hour($params);
	}


	protected function build_month_section_report_center(&$params)
	{
        foreach ($this->report_fields as $field => $row) {
        	if(isset($this->bid_row_field_value[$field])){
        		continue;
        	}
        	$func = 'get_'.$field.'_value';
        	if(method_exists($this,$func)){
        		$this->bid_row_field_value[$field] = $this->$func($params);
        	}
        }
        return $this;
	}


	protected function build_month_section_report_after(&$params)
	{

	}


	protected function save_month_section_report(&$params)
	{
		if(!$this->save_to_table){
			return array_merge($this->bid_row_field_value,['id'=>0]);
		}

		$model = new static();
		$w['start_int_day'] = $params['between_int_day'][0];
		$w['end_int_day']   = $params['between_int_day'][1];
		$w['bid']           = $params['bid'];

		$data = $model->where($w)->find();
		if(!empty($data)){
			foreach ($this->report_fields as $field => $row) {
				if(isset($this->bid_row_field_value[$field])){
					$data[$field] = $this->bid_row_field_value[$field];
				}
				$ret = $data->save();
				$ret = $data->toArray();
			}
		}else{
            $ret = $model->save($this->bid_row_field_value);
            if(!$ret){
            	return [];
            }
            $ret = $model->toArray();
		}    

        return $ret;

	}

	protected function count_student($params)
	{
		$w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        //$w['in_time'] = ['gt',0];
        $w['status'] = ['in',['1','30']];

        $student_nums = 0;
        $remain_lesson_hours = 0.00;

        foreach(get_all_rows('student',$w) as $stu){
            $student_nums++;
            $remain_lesson_hours += $stu['student_lesson_remain_hours'];
        }

        if($remain_lesson_hours <= 0){
        	$remain_lesson_hours = '-';
        }

        $this->bid_row_field_value['student_nums'] = $student_nums;
        $this->bid_row_field_value['remain_lesson_hours'] = $remain_lesson_hours;
	}
    
    // 剩余课时金额
	protected function get_remain_lesson_amount_value($params)
	{
		$w['bid'] = $params['bid'];
		$w['og_id'] = $params['og_id'];
        $w['lesson_status'] = ['in',['0','1']];

        $remain_lesson_amount = 0.000000;
		foreach(get_all_rows('student_lesson',$w) as $stu){
            $remain_lesson_amount += $stu['remain_lesson_amount'];
        }
        return $remain_lesson_amount;
	}
    
    /**
     * 班级数量包括 正常班级和临时班级
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
	protected function count_class($params)
	{
		$w['bid'] = $params['bid'];
		$w['og_id'] = $params['og_id'];
		$w['status'] = ['not in',Classes::STATUS_CLOSE];

		$class_nums = 0;

		foreach (get_all_rows('class',$w) as $cls) {
			$class_nums++;
		}

		$this->bid_row_field_value['class_nums'] = $class_nums;
	}

    // 满校人数
    protected function get_school_student_nums_value($params)
    {
        $config = user_branch_config('ki',$params['bid']);
		$cfg = $config['full_school_student_nums'];
		$week_ts_nums = $cfg['week_ts_nums'];
		$classroom_per_nums = $cfg['classroom_per_nums'];
		$room_nums = $cfg['room_nums'];
	    $nums = $week_ts_nums*$classroom_per_nums*$room_nums;
	    return $nums;
    }

    // 合同到期学员数
    protected function get_expire_student_nums_value($params)
    {
    	$w['bid'] = $params['bid'];
    	$w['expire_time'] = ['between',$params['between_ts']];
    	$sids = $this->m_student_lesson->where($w)->column('sid');
    	$sids = array_unique($sids);
    	return count($sids);
    }

    //续费学员仅统计 符合条件 的学费学员（一个月内的续费学员）
	protected function get_renew_student_nums_value($params)
	{
        $order_items = model('order_item')->alias(['x360p_order_item'=>'oi','x360p_order'=>'o'])->join('x360p_order','oi.oid = o.oid')->where(['oi.consume_type'=>['gt',1],'oi.bid'=>$params['bid'],'o.pay_status'=>2,'o.paid_time'=>['between',$params['between_ts']]])->select();
        $order_items = collection($order_items)->toArray();
        $sids = array_column($order_items,'sid');

        return count($sids);
	}

    // 新报学员仅统计 符合条件 的学费学员
	protected function get_new_student_nums_value($params)
	{
		$order_items = model('order_item')->alias(['x360p_order_item'=>'oi','x360p_order'=>'o'])->join('x360p_order','oi.oid = o.oid')->where(['oi.consume_type'=>1,'oi.bid'=>$params['bid'],'o.pay_status'=>2,'o.paid_time'=>['between',$params['between_ts']]])->select();
		$order_items = collection($order_items)->toArray();
        $sids = array_column($order_items,'sid');
        
        return count($sids);
	}
    
    // 续费订单数
	protected function get_renew_order_nums_value($params)
	{
		$order_items = model('order_item')->alias(['x360p_order_item'=>'oi','x360p_order'=>'o'])->join('x360p_order','oi.oid = o.oid')->where(['oi.consume_type'=>['gt',1],'oi.bid'=>$params['bid'],'o.pay_status'=>2,'o.paid_time'=>['between',$params['between_ts']]])->select();

        $order_items = collection($order_items)->toArray();
        $sids = array_column($order_items,'sid');
        
        return count($sids);
	}
    
    // 订单数
	protected function get_order_nums_value($params)
	{
		/*$w['bid'] = $params['bid'];
		$w['create_time'] = ['between',$params['between_ts']];
		$count = $this->m_order->where($w)->count();
		return $count;*/

		$w_s['bid'] = $params['bid'];
		$w_s['student_lesson_remain_hours'] = ['elt',40];
		$count = $this->m_student->where($w_s)->count();

		return $count;
	}

    // 转介绍学员数
	protected function get_refer_nums_value($params)
	{
        $config = user_branch_config('ki',$params['bid']);
		$cfg = $config['transfer_student_nums'];
		$w['bid'] = $params['bid'];
		
	    $w['get_time'] = ['between',$params['between_ts']];
	    $w['from_did'] = ['in',$cfg['from_dids']];

	    //市场名单
	    $count1 = model('market_clue')->where($w)->count();
        
        //客户名单
        $w['mcl_id'] = ['eq',0];
        $count2 = model('customer')->where($w)->count();

        //学员名单
        $w['mc_id'] = ['eq',0];
        unset($w['get_time']);
        unset($w['mcl_id']);
        $w['create_time'] = ['between',$params['between_ts']];
        $count3 = model('student')->where($w)->count();

        $count = $count1 + $count2 + $count3;
		
		return $count;
	}

	// 转介绍成交学员数
	protected function get_refer_deal_nums_value($params)
	{
        $config = user_branch_config('ki',$params['bid']);
		$cfg = $config['transfer_student_nums'];
		$w['bid'] = $params['bid'];
		$w['sid'] = ['gt',0];
       
		$w['create_time'] = ['between',$params['between_ts']];
		$w['from_did'] = ['in',$cfg['from_dids']];
		$sids = model('student')->where($w)->column('sid');

		unset($w['from_did']);
	    unset($w['create_time']);

		//$w['int_day'] = ['between',$params['between_int_day']];
		$w['sid'] = ['in',$sids];

		$w['paid_time'] = ['between',$params['between_ts']];
		$w['pay_status'] = 2;
		$sids = model('order')->where($w)->column('sid');

		//$sids = model('tally')->where($w)->where('type',Tally::TALLY_TYPE_INCOME)->column('sid');
        $sids = array_unique($sids);

		return count($sids);
	}

	// 退费学员数
	protected function get_refund_student_nums_value($params)
	{
		$w['bid'] = $params['bid'];
		$w['refund_int_day'] = ['between',$params['between_int_day']];

		$sids = model('order_refund')->where($w)->column('sid');
		$sids = array_unique($sids);

		return count($sids);
	}

    // 教室周平均排课量
	protected function get_cr_arrange_nums_value($params)
	{
		$days = $params['between_ts'];
		$diff = day_diff($days[0],$days[1]);

		$w['bid'] = $params['bid'];
		$w['int_day'] = ['between',$params['between_int_day']];
		$nums = $this->m_course_arrange->where($w)->count();
		$nums = $nums*7/$diff;

        $config = user_branch_config('ki',$params['bid']);
		$cfg = $config['full_school_student_nums'];
		$room_nums = $cfg['room_nums'];

        $count = $room_nums ? intval($nums/$room_nums) : 0;

		return $count;
	}


	protected function get_class_room_base_nums_value($params)
	{
		$config = user_branch_config('ki',$params['bid']);
		$cfg = $config['cr_arrange_rate'];
		$class_room_base_nums = $cfg['class_room_base_nums'];
		return $class_room_base_nums;
	}


	// 市场名单数 仅统计指定时间内新增的市场名单数
	protected function get_mc_student_nums_value($params)
	{
		$config = user_branch_config('ki',$params['bid']);
		$cfg = $config['mc_student_nums'];
		$w['bid'] = $params['bid'];
		
		//市场名单
	    $w['get_time'] = ['between',$params['between_ts']];
	    $w['from_did'] = ['in',$cfg['from_dids']];
	    $count1 = model('market_clue')->where($w)->count();
        
        //客户名单
        $w['mcl_id'] = ['eq',0];
        $count2 = model('customer')->where($w)->count();

        //学员名单
        $w['mc_id'] = ['eq',0];
        unset($w['get_time']);
        unset($w['mcl_id']);
        $w['create_time'] = ['between',$params['between_ts']];
        $count3 = model('student')->where($w)->count();

        $count = $count1 + $count2 + $count3;
		
		return $count;
	}

	// 市场名单总数(转成客户的)
	protected function get_mc_customer_nums_value($params)
	{
		$config = user_branch_config('ki',$params['bid']);
		$cfg = $config['mc_student_nums'];
		$from_dids = $cfg['from_dids'];
        $w['bid'] = $params['bid'];
        // $w['customer_status_did'] = ['neq',1025];
        $w['intention_level'] = ['gt',0];
        $w['get_time'] = ['between',$params['between_ts']];
        $w['from_did'] = ['in',$from_dids];
        
        $count = model('customer')->where($w)->count();

		return $count;
	}

	// 市场名单有效数
	protected function get_mc_valid_nums_value($params)
	{
		$config = user_branch_config('ki',$params['bid']);
		$cfg = $config['mc_student_nums'];
		$from_dids = $cfg['from_dids'];
        $w['bid'] = $params['bid'];
        $w['get_time'] = ['between',$params['between_ts']];
        $w['intention_level'] = ['gt',2];
        // $w['customer_status_did'] = ['neq',1025];
        $w['from_did'] = ['in',$from_dids];
        
        $count = model('customer')->where($w)->count();

		return $count;
	}
    
    // 市场名单签约数
	protected function get_mc_sign_nums_value($params)
	{
		$config = user_branch_config('ki',$params['bid']);
		$cfg = $config['mc_student_nums'];
		$w['bid'] = $params['bid'];
		$w['sid'] = ['gt',0];
       
		$w['from_did'] = ['in',$cfg['from_dids']];
		$sids = model('student')->where($w)->column('sid');

		$order_items = model('order_item')->alias(['x360p_order_item'=>'oi','x360p_order'=>'o'])->join('x360p_order','oi.oid = o.oid')->where(['oi.sid'=>['in',$sids],'oi.consume_type'=>['eq',1],'o.paid_time'=>['between',$params['between_ts']],'o.pay_status'=>2])->select();

		$order_items = collection($order_items)->toArray();
		$order_sids = array_column($order_items,'sid');
		$order_sids = array_unique($order_sids);

		return count($order_sids);

	}

	// 市场渠道成单金额
	protected function get_mc_deal_amount_value($params)
	{
        $config = user_branch_config('ki',$params['bid']);
		$cfg = $config['mc_student_nums'];
		$w['bid'] = $params['bid'];
		$w['sid'] = ['gt',0];
       
		$w['from_did'] = ['in',$cfg['from_dids']];
		$sids = model('student')->where($w)->column('sid');

		$paid_amount = model('order_item')->alias(['x360p_order_item'=>'oi','x360p_order'=>'o'])->join('x360p_order','oi.oid = o.oid')->where(['oi.sid'=>['in',$sids],'oi.consume_type'=>['eq',1],'o.paid_time'=>['between',$params['between_ts']],'o.pay_status'=>2])->sum('oi.paid_amount');

		return $paid_amount;
	}


	// 市场渠道数量
	protected function get_market_channel_nums_value($params)
	{
		$w['bid'] = $params['bid'];
		$w['create_time'] = ['between',$params['between_ts']];
		$count = model('market_channel')->where($w)->count();
		return $count;
	}

    // 销售金额
	protected function get_sale_amount_value($params)
	{
		$w['bid'] = $params['bid'];
		$w['paid_time'] = ['between',$params['between_ts']];
		$w['pay_status'] = 2;
		$w['is_debit'] = 0;

		$sum = model('order')->where($w)->sum('order_amount');

		return $sum;
	}


	// 销售课时数 报名课时+赠送课时
	protected function get_sale_lesson_hours_value($params)
    {
		$origin_lesson_hours = model('order_item')
            ->alias('or')
            ->join('order o','or.oid = o.oid')
            ->where(['o.bid'=>$params['bid'],'o.pay_status'=>2,'o.paid_time'=>['between',$params['between_ts']]])
            ->sum('or.origin_lesson_hours');
		$present_lesson_hours = model('order_item')
            ->alias('or')
            ->join('order o','or.oid = o.oid')
            ->where(['o.bid'=>$params['bid'],'o.pay_status'=>2,'o.paid_time'=>['between',$params['between_ts']]])
            ->sum('or.present_lesson_hours');

        $total_lesson_hours = $origin_lesson_hours + $present_lesson_hours;

		if($total_lesson_hours <= 0){
			return '-';
		}

		return $total_lesson_hours;
	}
    
    // 客户名单数
	protected function get_customer_nums_value($params)
	{
        $w['bid'] = $params['bid'];
        $w['get_time'] = ['between',$params['between_ts']];
        $count = $this->m_customer->where($w)->count();
        return $count;
	}


	// 有效沟通数
	protected function get_valid_communicate_nums_value($params)
	{
		$w['bid'] = $params['bid'];
		$w['is_connect'] = ['gt',0];
		$w['is_system'] = ['eq',0];
		$w['intention_level'] = ['gt',2];
		$w['create_time'] = ['between',$params['between_ts']];
		$count = model('customer_follow_up')->where($w)->count();
		return $count;
	}


	// 诺到人数
	protected function get_accept_nums_value($params)
	{

		$w['bid'] = $params['bid'];
		$w['is_connect'] = ['gt',0];
		$w['is_promise'] = ['gt',0];
		$w['is_system'] = ['eq',0];
		$w['promise_int_day'] = ['between',$params['between_int_day']];
		$count = model('customer_follow_up')->where($w)->count();
		return $count;
	}


	// 试听人数
	protected function get_trial_nums_value($params)
	{
		$w['bid'] = $params['bid'];
		$w['int_day'] = ['between',$params['between_int_day']];
		$w['is_attendance'] = 1;
		$w['attendance_status'] = 1;
        // 客户试听人数统计
        $w['cu_id'] = ['gt',0];
		$cu_ids = model('trial_listen_arrange')->where($w)->column('cu_id');
		$cu_ids = array_unique($cu_ids);
		$customer_nums = count($cu_ids);
        // 学员试听人数统计
        $w['sid'] = ['gt',0];
        unset($w['cu_id']);
		$sids = model('trial_listen_arrange')->where($w)->column('sid');
		$sids = array_unique($sids);
		$student_nums = count($sids);

		return $customer_nums + $student_nums;
	}


	// 试听报名人数  
	protected function get_trial_sign_nums_value($params)
	{
		$w['bid'] = $params['bid'];
		// $w['int_day'] = ['between',$params['between_int_day']];
		$w['is_attendance'] = 1;
		$w['attendance_status'] = 1;
        
        $w['cu_id'] = ['gt',0];
		$cu_ids = model('trial_listen_arrange')->where($w)->column('cu_id');
		$customer_sids = model('customer')->where('cu_id','in',$cu_ids)->column('sid');

        $w['sid'] = ['gt',0];
        unset($w['cu_id']);
		$student_sids = model('trial_listen_arrange')->where($w)->column('sid');
		$student_sids = array_unique($student_sids);

		$sids = array_merge($customer_sids,$student_sids);

		$order_items = model('order_item')->alias(['x360p_order_item'=>'oi','x360p_order'=>'o'])->join('x360p_order','oi.oid = o.oid')->where(['oi.gtype'=>OrderItem::GTYPE_LESSON,'oi.sid'=>['in',$sids],'o.paid_time'=>['between',$params['between_ts']],'o.pay_status'=>2])->select();
		$order_items = collection($order_items)->toArray();
		$order_sids = array_column($order_items,'sid');
		$order_sids = array_unique($order_sids);

		return count($order_sids);
	}

	protected function count_employee_lesson_hour($params)
	{
		$w['bid'] = $params['bid'];
		$w['int_day'] = ['between',$params['between_int_day']];

		$lesson_amount = 0.000000;
		$lesson_hours  = 0.00;

		foreach (get_all_rows('student_lesson_hour',$w) as $slh) {
			$lesson_amount += $slh['lesson_amount'];
			$lesson_hours += $slh['lesson_hours'];
		}

		if($lesson_hours <= 0){
			$lesson_hours = '-';
		}

		$this->bid_row_field_value['employee_lesson_amount'] = $lesson_amount;
		$this->bid_row_field_value['employee_lesson_hours'] = $lesson_hours;
	}


	// 出勤学员人次数
	protected function get_attendance_times_value($params)
	{
		$w['bid'] = $params['bid'];
		$w['int_day'] = ['between',$params['between_int_day']];
		$w['is_in'] = 1;
		$count = model('student_attendance')->where($w)->count();
		return $count;
	}

	// 应出勤学员人次数
	protected function get_should_attendance_times_value($params)
	{
		$w['bid'] = $params['bid'];
		$w['int_day'] = ['between',$params['between_int_day']];
		$count = model('student_attendance')->where($w)->count();
		return $count;
	}

	// 未分班学员数
	protected function get_no_class_student_nums_value($params)
	{
        $w['bid'] = $params['bid'];
        $w['status'] = 1;
        $sids = model('class_student')->where($w)->column('sid');
        
        $w['status'] = ['lt',90];
        $w['sid'] = ['not in',$sids];
        $count = model('student')->where($w)->count();
        return $count;
	}

    // 未出勤学员数
	protected function get_no_attendance_nums_value($params)
	{
		$w['bid'] = $params['bid'];
		$w['int_day'] = ['between',$params['between_int_day']];
		$w['is_in'] = 0;
		$sids = model('student_attendance')->where($w)->column('sid');
		$sids = array_unique($sids);
		return count($sids);
	}

	//未排课学员数
	protected function get_no_arrange_student_nums_value($params)
	{
		$w['bid'] = $params['bid'];
        $w['status'] = Student::STATUS_NORMAL;

        $all_sids = model('student')->where($w)->column('sid');
        if(!empty($all_sids)){
        	$arrange_sids = model('course_arrange_student')->where('sid','in',$all_sids)->column('sid');
        }else{
        	$arrange_sids = [];
        }
        $all_student_nums = count($all_sids);
        $all_arrange_nums = count(array_unique($arrange_sids));
        
        return $all_student_nums - $all_arrange_nums;
	}
    
    // 实际出勤人数
	protected function get_attendance_nums_value($params)
	{
		$w['bid'] = $params['bid'];
		$w['int_day'] = ['between',$params['between_int_day']];
		$w['is_in'] = 1;
		$sids = model('student_attendance')->where($w)->column('sid');
		$sids = array_unique($sids);
		return count($sids);
	}








}