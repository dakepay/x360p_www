<?php

namespace app\api\model;

use app\common\Report;

class ReportEmployeePerformanceSummary extends Report
{
	protected $report_name  = '员工业绩表';
    protected $report_table_name     = 'report_employee_performance_summary';

    protected $eid_row_field_value = [];

    protected $report_fields = [
        'eid'          =>   ['title'=>'员工姓名','type'=>Report::FTYPE_INT],
        // 'performance_type'  =>   ['title'=>'类型','type'=>Report::FTYPE_INT],  // 1签单 2确收
        'performance_amount'       =>   ['title'=>'净业绩','type'=>Report::FTYPE_SIGNED_DECIMAL156],
        'performance_amount_a'      =>  ['title'=>'签单金额','type'=>Report::FTYPE_SIGNED_DECIMAL156],
        'performance_nums' => ['title'=>'签单数','type'=>Report::FTYPE_INT],
        'refund_nums' => ['title'=>'退单数','type'=>Report::FTYPE_INT],
        'refund_amount' => ['title'=>'退单金额','type'=>Report::FTYPE_DECIMAL156],
        'teach_lesson_amount' => ['title'=>'老师确收金额','type'=>Report::FTYPE_DECIMAL156],
        'teach_lesson_hours'  => ['title'=>'老师确收课时','type'=>Report::FTYPE_DECIMAL132],
        'second_lesson_amount' => ['title'=>'助教确收金额','type'=>Report::FTYPE_DECIMAL156],
        'second_lesson_hours'  => ['title'=>'助教确收课时','type'=>Report::FTYPE_DECIMAL132],
        'edu_lesson_amount' => ['title'=>'班主任确收金额','type'=>Report::FTYPE_DECIMAL156],
        'edu_lesson_hours'  => ['title'=>'班主任确收课时','type'=>Report::FTYPE_DECIMAL132],
    ];

    public function getReportFields()
    {
    	return $this->report_fields;
    }


    /**
     * 查询报表
     * @param $input
     * @param bool $pagenation
     * @return false|mixed|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDaySectionReport($input,$pagenation = false){
        if(isset($input['bid']) && $input['bid'] == -1){
            unset($input['bid']);
        }
        if(!isset($input['start_date'])){
            $ds = current_week_ds();
            $input['start_date'] = $ds[0];
            $input['end_date']   = $ds[1];
        }
        $og_id = gvar('og_id');
        $w['start_int_day'] = format_int_day($input['start_date']);
        $w['end_int_day']   = format_int_day($input['end_date']);
        $w['og_id'] = $og_id;

        if(isset($input['bid'])){
            $w['bid'] = $input['bid'];
        }else{
            $user = gvar('user');
            $w['bid'] = ['IN',$user['employee']['bids']];
        }

        $day_diff = int_day_diff($w['start_int_day'],$w['end_int_day']);

        if($day_diff > 31){
            return $this->user_error('查询时间间隔不可超过1个月');
        }

        $mEmployee = new Employee();
        $w_em['og_id'] = $og_id;
        if(isset($w['bid'])){
            $w_em['bid'] = $w['bid'];
        }
        $eids = $mEmployee->where($w_em)->column('eid');

        $db = db($this->report_table_name);
        $result = $db->where($w)->select();
        if(!$result || isset($input['refresh']) && $input['refresh'] == 1){
            $build_eids = $eids;
        }else{
            $result_eids = array_column($result,'eid');
            $build_eids = array_values(array_diff($eids,$result_eids));
        }

        if(!empty($build_eids)){
        	foreach ($build_eids as $eid) {
        		$this->buildDaySectionReports($input['start_date'],$input['end_date'],$eid);
        	}
        }
        
        $model = new self();
        $page['page'] = $input['page'];
        $page['pagesize'] = $input['pagesize'];
        $result = $model->where($w)->getSearchResult($page,[],$pagenation);

        $result['params'] = $input;

        return $result;

    }

    /**
     * 创建区间报表
     * @param  [type] $start [description]
     * @param  [type] $end   [description]
     * @param  [type] $eid   [description]
     * @param  [type] $bid   [description]
     * @return [type]        [description]
     */
    public function buildDaySectionReports($start,$end,$eid)
    {
    	$start_ts = strtotime($start.' 00:00:00');
    	$end_ts   = strtotime($end.' 23:59:59');

    	$start_int_day = format_int_day($start);
    	$end_int_day = format_int_day($end);

    	$params['between_ts'] = [$start_ts,$end_ts];
    	$params['between_int_day'] = [$start_int_day,$end_int_day];

    	$params['eid']   = $eid;
    	$params['og_id'] = gvar('og_id');

        $this->init_eid_row_field($params);
    	$this->build_day_section_report_before($params);
    	$this->build_day_section_report_center($params);
    	$this->build_day_section_report_after($params);

        return $this->save_day_section_reports($params);
    }
    
    /**
     * 初始化一行员工数据
     * @param  [type] &$params [description]
     * @return [type]          [description]
     */
    protected function init_eid_row_field(&$params)
    {
        $this->eid_row_field_value['og_id'] = $params['og_id'];
        $this->eid_row_field_value['start_int_day'] = $params['between_int_day'][0];
        $this->eid_row_field_value['end_int_day'] = $params['between_int_day'][1];
        return $this;
    }

    
    /**
     * 生成报表前段
     * @param  [type] &$params [description]
     * @return [type]          [description]
     */
    protected function build_day_section_report_before(&$params){
    	$this->count_performance($params);
        $this->count_refund($params);
        $this->count_teach_lesson($params);
        $this->count_second_lesson($params);
        $this->count_edu_lesson($params);
    }


    /**
     * 生成报表中段
     * @param $params
     */
    protected function build_day_section_report_center(&$params){
        foreach($this->report_fields as $field=>$row){
            if(isset($this->eid_row_field_value[$field])){
                continue;
            }
            $func = 'get_'.$field.'_value';
            if(method_exists($this,$func)){
                $this->eid_row_field_value[$field] = $this->$func($params);
            }
        }
        return $this;
    }


    protected function save_day_section_reports(&$params)
    {
    	if(!$this->save_to_table){
    		return array_merge($this->eid_row_field_value,['id'=>0]);
    	}

    	$model = new static();
    	$w_ex['start_int_day'] = $params['between_int_day'][0];
    	$w_ex['end_int_day']   = $params['between_int_day'][1];
    	$w_ex['eid']           = $params['eid'];

		$exist_data = $model->where($w_ex)->find();

		if($exist_data){
            /*$exist_data['amount'] = $this->eid_row_field_value['amount'];
            $exist_data['lesson_hours'] = $this->eid_row_field_value['lesson_hours'];
            $exist_data['nums'] = $this->eid_row_field_value['nums'];   */
            foreach($this->report_fields as $field=>$r){
                if(isset($this->eid_row_field_value[$field])){
                    $exist_data[$field] = $this->eid_row_field_value[$field];
                }
            }
            $result = $exist_data->save();
            $result = $exist_data->toArray();
		}else{
			$result = $model->save($this->eid_row_field_value);
        	if(!$result){
        		return [];
        	}
        	$result = $model->toArray();
		}
  
    	return $result;
    }

    /**
     * 统计签单金额  签单数
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function count_performance($params)
    {
        // print_r($params);exit;
        $performance_amount       = 0.000000;
        $performance_nums         = 0.00;
        $performance_amount_a     = 0.000000;

        $einfo = get_employee_info($params['eid']);
        $this->eid_row_field_value['eid'] = $params['eid'];
        $this->eid_row_field_value['bid'] = $einfo['bid'];
        $this->eid_row_field_value['dept_id'] = get_dept_id_by_bid($einfo['bid']);

        $w_op['eid'] = $params['eid'];
        $w_op['receipt_time'] = ['between',$params['between_ts']];
        // $w_op['amount'] = ['gt',0];
        $w_op['bid'] = request()->bid;
        foreach (get_all_rows('employee_receipt',$w_op) as $op) {
            $performance_nums ++;
            $performance_amount += $op['amount'];
            if($op['amount'] > 0){
                $performance_amount_a += $op['amount'];
            }
        }
        $this->eid_row_field_value['performance_amount'] = $performance_amount;
        $this->eid_row_field_value['performance_nums']   = $performance_nums;
        $this->eid_row_field_value['performance_amount_a'] = $performance_amount_a;

    }

    protected function count_refund($params)
    {
        $refund_amount       = 0.000000;
        $refund_nums         = 0.00;

        $w_op['eid'] = $params['eid'];
        $w_op['receipt_time'] = ['between',$params['between_ts']];
        $w_op['amount'] = ['lt',0];
        $w_op['bid'] = request()->bid;
        foreach (get_all_rows('employee_receipt',$w_op) as $op) {
            $refund_nums ++;
            $refund_amount += abs($op['amount']);
        }
        $this->eid_row_field_value['refund_amount'] = $refund_amount;
        $this->eid_row_field_value['refund_nums']   = $refund_nums;
    }

    /**
     * 统计老师确收金额 确收课时
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function count_teach_lesson($params)
    {
        $teach_lesson_amount   =  0.000000;
        $teach_lesson_hours    =  0.00;

        $w_elh['eid'] = $params['eid'];
        $w_elh['int_day'] = ['between',$params['between_int_day']];
        foreach (get_all_rows('employee_lesson_hour',$w_elh) as $elh) {
            $teach_lesson_hours += $elh['total_lesson_hours'];
            $teach_lesson_amount += $elh['total_lesson_amount'];
        }  
        $this->eid_row_field_value['teach_lesson_amount'] = $teach_lesson_amount;
        $this->eid_row_field_value['teach_lesson_hours'] = $teach_lesson_hours;
    }

    
    /**
     * 统计助教确收金额  确收课时
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function count_second_lesson($params)
    {
        $second_lesson_amount = 0.000000;
        $second_lesson_hours   = 0.00;

        $w_elh[] = ['exp',"find_in_set({$params['eid']},second_eids)"];
        $w_elh['int_day'] = ['between',$params['between_int_day']];
        foreach (get_all_rows('employee_lesson_hour',$w_elh) as $elh) {
            $second_lesson_hours += $elh['total_lesson_hours'];
            $second_lesson_amount += $elh['total_lesson_amount'];
        }  
        $this->eid_row_field_value['second_lesson_amount'] = $second_lesson_amount;
        $this->eid_row_field_value['second_lesson_hours'] = $second_lesson_hours;

    }


    /**
     * 统计班主任确收金额 确收课时
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function count_edu_lesson($params)
    {
        $edu_lesson_amount   =  0.000000;
        $edu_lesson_hours    =  0.00;

        $w_elh['edu_eid'] = $params['eid'];
        $w_elh['int_day'] = ['between',$params['between_int_day']];
        foreach (get_all_rows('employee_lesson_hour',$w_elh) as $elh) {
            $edu_lesson_hours += $elh['total_lesson_hours'];
            $edu_lesson_amount += $elh['total_lesson_amount'];
        }  
        $this->eid_row_field_value['edu_lesson_amount'] = $edu_lesson_amount;
        $this->eid_row_field_value['edu_lesson_hours'] = $edu_lesson_hours;
    }









}