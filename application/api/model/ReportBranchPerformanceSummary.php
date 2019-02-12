<?php

namespace app\api\model;

use app\common\Report;

class ReportBranchPerformanceSummary extends Report
{
	protected $report_name  = '校区业绩表';
	protected $report_table_name     = 'report_branch_performance_summary';

    protected $bid_row_field_value = [];

    protected $report_fields = [
        'amount'       =>   ['title'=>'金额','type'=>Report::FTYPE_DECIMAL156],
        'nums'         =>   ['title'=>'签单数','type'=>Report::FTYPE_INT],
        'lesson_hours' =>   ['title'=>'确收课时','type'=>Report::FTYPE_DECIMAL132],
        'lesson_amount'=>   ['title'=>'确收金额','type'=>Report::FTYPE_DECIMAL156],
        'refund_nums'  =>   ['title'=>'退单数','type'=>Report::FTYPE_INT],
        'refund_amount'=>   ['title'=>'退单金额','type'=>Report::FTYPE_DECIMAL156],
        'cut_amount'   =>   ['title'=>'违约课消金额','type'=>Report::FTYPE_DECIMAL156],
    ];

    /**
     * 获取查询报表
     * @param $input
     */
    public function getDaySectionReport($input,$pagenation = false){
        if(!isset($input['start_date'])){
            $ds = current_week_ds();
            $input['start_date'] = $ds[0];
            $input['end_date']   = $ds[1];
        }
        $w['start_int_day'] = format_int_day($input['start_date']);
        $w['end_int_day']   = format_int_day($input['end_date']);

        $day_diff = int_day_diff($w['start_int_day'],$w['end_int_day']);

        if($day_diff > 31){
            return $this->user_error('查询时间间隔不可超过1个月');
        }
        $og_id = gvar('og_id');
        $user  = gvar('user');

        $request_bids = isset($user['employee'])?$user['employee']['bids']:[];

        $query_bids = [];
        $build_bids = [];
        if(!$request_bids){
            $request_bids = [];
        }

        $query_bids = $request_bids;

        if(isset($input['bid'])){
            if($input['bid'] == -1){
                $query_bids = [];
            }else{
                $query_bids = explode(',',$input['bid']);
            }
        }

        if(empty($query_bids)){
            $w_branch['og_id'] = $og_id;
            $branch_list = get_table_list('branch',$w_branch);
            $query_bids  = array_column($branch_list,'bid');
        }

        $w['bid'] = ['in',$query_bids];



        $db = db($this->report_table_name);

        $input['order_field'] = isset($input['order_field']) ? $input['order_field'] : '';
        $input['order_sort'] = isset($input['order_sort']) ? $input['order_sort'] : '';

        $result = $db->where($w)->order($input['order_field'],$input['order_sort'])->select();

        $result_bids = [];

        if(!$result || isset($input['refresh']) && $input['refresh'] == 1){
            $result = [];
            $build_bids = $query_bids;
        }else{
            if(count($result) < count($query_bids)){
                $result_bids = array_column($result,'bid');
                $build_bids = array_values(array_diff($query_bids,$result_bids));
            }
        }

        if(!empty($build_bids)){
            foreach($build_bids as $bid){
               $result[] = $this->buildDaySectionReport($input['start_date'],$input['end_date'],$bid);
            }
        }

        foreach ($result as &$item) {
            $item['only_amount'] = round($item['amount'] - abs($item['refund_amount']),6);
        }

        $ret['list'] = $result;
        $ret['params'] = $input;
        $ret['total'] = count($result);

        $enable_company = user_config('params.enable_company');
        if($enable_company){
            $ret['list1'] = $this->getCompanyList($result);
            foreach($ret['list1'] as $k=>$v){
                $ret['list1'][$k]['only_amount'] = $v['amount'] - abs($v['refund_amount']);
            }
        }else{
            $ret['list1'] = [];
        }

        return $ret;

    }


    /**
     * 生成报表前段
     * @param  [type] &$params [description]
     * @return [type]          [description]
     */
    protected function build_day_section_report_before(&$params){
    	$this->count_sum($params);
        $this->count_refund($params);
        $this->count_student_lesson_hour($params);
    }



    protected function count_sum($params)
    {
	    $w_oph['bid'] = $params['bid'];
		$w_oph['paid_time'] = ['between',$params['between_ts']];
		$w_oph['amount'] =  ['gt',0];
		$w_oph['is_demo'] = 0;

		$mOph = new OrderPaymentHistory();
		$amount = $mOph->where($w_oph)->sum('amount');
		$nums   = $mOph->where($w_oph)->count();



		$this->bid_row_field_value['amount'] = floatval($amount);
		$this->bid_row_field_value['nums']   = $nums;

    }

    protected function count_refund($params)
    {

        $w_op['bid'] = $params['bid'];
        $w_op['refund_int_day'] = ['between',$params['between_int_day']];


        $mOrderRefund = new OrderRefund();

        $refund_amount = $mOrderRefund->where($w_op)->sum('refund_amount');
        $refund_nums = $mOrderRefund->where($w_op)->count();


        $this->bid_row_field_value['refund_amount'] = floatval($refund_amount);
        $this->bid_row_field_value['refund_nums']   = $refund_nums;
    }
    
    // 违约课消金额
    protected function get_cut_amount_value($params)
    {
        $w['bid'] = $params['bid'];
        $w['consume_type'] = 3;
        $w['int_day'] = ['between',$params['between_int_day']];
        $count = model('student_lesson_hour')->where($w)->sum('lesson_amount');
        return $count;
    }


    protected function count_student_lesson_hour($params)
    {

        $lesson_hours = 0.00;
        $lesson_amount = 0.000000;

        $w_slh['bid'] = $params['bid'];
        $w_slh['int_day'] = ['between',$params['between_int_day']];

        foreach (get_all_rows('student_lesson_hour',$w_slh) as $slh) {
            $lesson_hours += $slh['lesson_hours'];
            $lesson_amount += $slh['lesson_amount'];
        }

        $this->bid_row_field_value['lesson_hours'] = $lesson_hours;
        $this->bid_row_field_value['lesson_amount'] = $lesson_amount;
    }







}