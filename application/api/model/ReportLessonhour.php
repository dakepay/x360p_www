<?php 

namespace app\api\model;

use think\helper\Time;

class ReportLessonhour extends Base
{
    protected function getTimeCondition($start)
    {
    	$condition   = [];
    	$condition[] = strtotime($start);
    	$condition[] = strtotime('+1 day',strtotime($start)) - 1;
    	return $condition;
    }

	protected function makeReportOfDay($start,$og_id,$bid)
	{
		$data = [];
		$data['int_day'] = $start;
		$data['bid']     = $bid;
		$data['og_id']   = $og_id;

		// 计算字段
		$w['int_day'] = $start;
		$temp = $this->getTimeCondition($start);
		$w['create_time'] = ['between',$temp];
		$w['og_id'] = $og_id;
		$w['bid'] = $bid;
		$fields = $this->getTableFields();
		foreach ($fields as $field) {
			$field_method = 'calc_'.$field;
			if(method_exists($this,$field_method)){
				$data[$field] = $this->$field_method($w);
			}
		}

        $w_e['int_day']  = $start;
        $w_e['og_id']    = $og_id;
        $w_e['bid']      = $bid;
        $model = new self();
		$data_exist = $model->where($w_e)->find();
		if($data_exist){
			$where['id'] = $data_exist['id'];
			$model->save($data,$where);
		}else{
			$model->isUpdate(false)->save($data);
		}
		return $data;
	}


	public static function buildReport($input)
	{
		// list($start, $end) = Time::today();
		// echo $start; // 零点时间戳
		// echo $end; exit;// 23点59分59秒的时间戳
        // print_r($input);exit;
		$og_id = gvar('og_id');
		$start = $input['start'];
		$end   = $input['end'];
		$bids  = $input['bids'];
		$model = new self();
		try{
			for(; $start <= $end; ){
				foreach ($bids as $bid) {
					$model->makeReportOfDay($start,$og_id,$bid);
				}
				$start = date('Ymd',strtotime("+1 day",strtotime($start)));
			}
		}catch(\Exception $e){
            return $e->getMessage();
		}
		return true;
	}

    /**
     * 计算报名课时
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_sign_lesson_num($w)
	{
        unset($w['int_day']);
        $w['gtype'] = 0;
        return OrderItem::where($w)->sum('origin_lesson_hours');
	}
    
    /**
     * 计算报名课时金额
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_sign_lesson_amount($w)
	{
		unset($w['int_day']);
		$w['gtype'] = 0;
		return OrderItem::where($w)->sum('subtotal');
	}
    
    /**
     * 计算赠送课时
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_send_lesson_num($w)
	{
        unset($w['int_day']);
        $w['gtype'] = 0;
        return OrderItem::where($w)->sum('present_lesson_hours');
	}

    /**
     * 计算消费课时
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_consume_lesson_num($w)
	{   
		$where['create_time'] = $w['create_time'];
		$where['og_id'] = $w['og_id'];
		$where['bid'] = $w['bid'];
		$where['change_type'] = 2;
        unset($w['create_time']);
        $num1 =  StudentLessonHour::where($w)->sum('lesson_hours');
        $num2 = StudentLessonHour::where($where)->sum('lesson_hours');
        return $num1 + $num2;
	}

	/**
     * 计算消费金额
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_consume_lesson_amount($w)
	{   
		$where['create_time'] = $w['create_time'];
		$where['og_id'] = $w['og_id'];
		$where['bid'] = $w['bid'];
		$where['change_type'] = 2;
        unset($w['create_time']);
        $num1 =  StudentLessonHour::where($w)->sum('lesson_amount');
        $num2 = StudentLessonHour::where($where)->sum('lesson_amount');
        return $num1 + $num2;
	}
    
    /**
     * 计算结转课时
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_convert_lesson_num($w)
	{
		unset($w['int_day']);
		$ot_ids = OrderTransfer::where($w)->column('ot_id');
		$where['ot_id'] = ['in',$ot_ids];
		$num1 = OrderTransferItem::where($where)->sum('nums');
		$num2 = OrderTransferItem::where($where)->sum('present_nums');
		return $num1 + $num2;
	}
    
    /**
     * 计算结转金额
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_convert_lesson_amount($w)
	{
        unset($w['int_day']);
        $ot_ids = OrderTransfer::where($w)->column('ot_id');
		$where['ot_id'] = ['in',$ot_ids];
        return OrderTransferItem::where($where)->sum('amount');
	}

	/**
     * 计算退费课时
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_refund_lesson_num($w)
	{
		unset($w['int_day']);
		$or_ids = OrderRefund::where($w)->column('or_id');
		$where['or_id'] = ['in',$or_ids];
		$num1 = OrderRefundItem::where($where)->sum('nums');
		$num2 = OrderRefundItem::where($where)->sum('present_nums');
		return $num1 + $num2;
	}
    
    /**
     * 计算退费金额
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_refund_lesson_amount($w)
	{
        unset($w['int_day']);
        $or_ids = OrderRefund::where($w)->column('or_id');
		$where['or_id'] = ['in',$or_ids];
        return OrderRefundItem::where($where)->sum('amount');
	}

    /**
     * 计算期初课时
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_origin_lesson_num($w)
	{
		unset($w['create_time']);
		$prev_int_day = date('Ymd',strtotime("-1 day",strtotime($w['int_day'])));
		$w['int_day'] = $prev_int_day;
		$origin_lesson_num = ReportLessonhour::where($w)->value('remain_lesson_num');

		if($origin_lesson_num){
			return $origin_lesson_num;
		}
		return '0';
	}
    
    /**
     * 计算期初课时金额
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_origin_lesson_amount($w)
	{
        unset($w['create_time']);
        $prev_int_day = date('Ymd',strtotime("-1 day",strtotime($w['int_day'])));
		$w['int_day'] = $prev_int_day;
		$origin_lesson_amount = ReportLessonhour::where($w)->value('remain_lesson_amount');

		if($origin_lesson_amount){
			return $origin_lesson_amount;
		}
		return '0';
	}


    /**
     * 计算剩余课时
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_remain_lesson_num($w)
	{
		$where['create_time'] = $w['create_time'];
		$where['bid'] = $w['bid'];
		$where['og_id'] = $w['og_id']; 
		$where['gtype'] = 0;
		// 当天的期初课时
        unset($w['create_time']);
        $prev_int_day = date('Ymd',strtotime("-1 day",strtotime($w['int_day'])));
		$we['int_day'] = $prev_int_day;
		$we['bid'] = $w['bid'];
		$we['og_id'] = $w['og_id']; 
        $origin_lesson_num = ReportLessonhour::where($we)->value('remain_lesson_num');
        $origin_lesson_num = $origin_lesson_num ? $origin_lesson_num : '0';
        // 当天报名课时
        $sign_lesson_num = OrderItem::where($where)->sum('origin_lesson_hours');
        // 当天赠送课时
        $send_lesson_num = OrderItem::where($where)->sum('present_lesson_hours');
        unset($where['gtype']);
        // 当天结转课时
        $ot_ids = OrderTransfer::where($where)->column('ot_id');
		$wh['ot_id'] = ['in',$ot_ids];
		$num1 = OrderTransferItem::where($wh)->sum('nums');
		$num2 = OrderTransferItem::where($wh)->sum('present_nums');
        $convert_lesson_num = $num1 + $num2;
        // 当天消费课时
		$where['change_type'] = 2;
        $num1 =  StudentLessonHour::where($w)->sum('lesson_hours');
        $num2 = StudentLessonHour::where($where)->sum('lesson_hours');
        $consume_lesson_num =  $num1 + $num2;
        unset($where['change_type']);
        // 当天退费课时
		$or_ids = OrderRefund::where($where)->column('or_id');
		$whe['or_id'] = ['in',$or_ids];
		$num1 = OrderRefundItem::where($whe)->sum('nums');
		$num2 = OrderRefundItem::where($whe)->sum('present_nums');
		$refund_lesson_num =  $num1 + $num2;
        
        return $origin_lesson_num + $sign_lesson_num + $send_lesson_num - $convert_lesson_num - $consume_lesson_num - $refund_lesson_num;
	}

	/**
     * 计算剩余课时金额
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_remain_lesson_amount($w)
	{  
		$where['create_time'] = $w['create_time'];
		$where['bid'] = $w['bid'];
		$where['og_id'] = $w['og_id'];
		$where['gtype'] = 0;
		// 当天期初课时金额
        unset($w['create_time']);
        $prev_int_day = date('Ymd',strtotime("-1 day",strtotime($w['int_day'])));
		$we['int_day'] = $prev_int_day;
        $we['bid'] = $w['bid'];
		$we['og_id'] = $w['og_id'];
		$origin_lesson_amount = ReportLessonhour::where($we)->value('remain_lesson_amount');
		$origin_lesson_amount = $origin_lesson_amount ? $origin_lesson_amount : '0';
		// 当天报名课时金额
		$sign_lesson_amount =  OrderItem::where($where)->sum('subtotal');
		// 当天结转课时金额
		unset($where['gtype']);
        $ot_ids = OrderTransfer::where($where)->column('ot_id');
		$wh['ot_id'] = ['in',$ot_ids];
        $convert_lesson_amount = OrderTransferItem::where($wh)->sum('amount');
        // 当天消费课时金额
		$where['change_type'] = 2;
        $num1 =  StudentLessonHour::where($w)->sum('lesson_amount');
        $num2 = StudentLessonHour::where($where)->sum('lesson_amount');
        $consume_lesson_amount = $num1 + $num2;
        unset($where['change_type']);
        // 当天退费课时金额
        $or_ids = OrderRefund::where($where)->column('or_id');
		$whe['or_id'] = ['in',$or_ids];
        $refund_lesson_amount = OrderRefundItem::where($whe)->sum('amount');

        return $origin_lesson_amount + $sign_lesson_amount - $convert_lesson_amount - $consume_lesson_amount - $refund_lesson_amount;
	}


}