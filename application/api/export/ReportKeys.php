<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportKey;

class ReportKeys extends Export
{
	protected function get_title()
	{
		$input = $this->params;
		$branch_name = get_branch_name($input['bid']);
		$title = $branch_name.' '.'关键指标';
		return $title;
	}


	protected function get_columns()
	{
		
	}

	protected function get_data()
	{
		$m_rk = new ReportKey;
		$input = $this->params;
		$bid = isset($input['bid']) ? $input['bid'] : request()->bid;
		$w = [];

		if(!isset($input['start_date'])){
			$date = date("Y-m-d");
			$first = date('Y-m-01', strtotime($date));
			$last  = date('Y-m-d', strtotime("$first +1 month -1 day"));
			$input['start_date'] = $first;
			$input['end_date'] = $last;
		}

		$w['start_int_day'] = format_int_day($input['start_date']);
		$w['end_int_day'] = format_int_day($input['end_date']);
		$w['og_id'] = gvar('og_id');
		$w['bid'] = $bid;

		$ret = $m_rk->where($w)->find();

		$ret['params'] = $input;
		$ret['month'] = date('Y年m月d日',strtotime($input['start_date'])).'-'.date('Y年m月d日',strtotime($input['end_date']));
		$ret['branch_name'] = get_branch_name($bid);
        
        // 校区
        $class_avg_student_nums = $ret['class_nums'] ? round($ret['student_nums']/$ret['class_nums'],2) : $ret['student_nums'];
		$ret['branch'] = [
            'student_nums'             => $ret['student_nums'],
            'remain_lesson_hours'      => $ret['remain_lesson_hours'],
            'remain_lesson_amount'     => $ret['remain_lesson_amount'],
            'class_nums'               => $ret['class_nums'],
            'class_avg_student_nums'   => $class_avg_student_nums,
            'full_school_student_nums' => $ret['school_student_nums'],
            'differ_student_nums'      => $ret['student_nums'] - $ret['school_student_nums'],
            'expire_student_nums'      => $ret['expire_student_nums'],
		];
		// 教务
		$renew_rate = $ret['order_nums'] ? round($ret['renew_order_nums']/$ret['order_nums'],2) : 0.00;
		$refer_deal_rate = $ret['refer_nums'] ? round($ret['refer_deal_nums']/$ret['refer_nums'],2) : 0.00;
		$refund_rate = $ret['student_nums'] ? round($ret['refund_student_nums']/$ret['student_nums'],2) : 0.00;
		$cr_arrange_rate = $ret['class_room_base_nums'] ? round($ret['cr_arrange_nums']/$ret['class_room_base_nums'],2) : 0.00;
		$ret['educate'] = [
		    'new_student_nums'    => $ret['new_student_nums'],
            'renew_student_nums'  => $ret['renew_student_nums'],
            'renew_rate'          => $renew_rate,
            'refer_deal_nums'     => $ret['refer_deal_nums'],
            'refer_deal_rate'     => $refer_deal_rate,
            'refund_student_nums' => $ret['refund_student_nums'],
            'refund_rate'         => $refund_rate,
            'cr_arrange_nums'     => $ret['cr_arrange_nums'],
            'cr_arrange_rate'     => $cr_arrange_rate,
		];
		// 市场
		$mc_valid_rate = $ret['mc_student_nums'] ? round($ret['mc_valid_nums']/$ret['mc_student_nums'],2) : 0.00;
		$mc_sign_rate = $ret['mc_student_nums'] ? round($ret['mc_sign_nums']/$ret['mc_student_nums'],2) : 0.00;
		$ret['market'] = [
            'mc_student_nums'     => $ret['mc_student_nums'],
            'mc_deal_amount'      => $ret['mc_deal_amount'],
            'market_channel_nums' => $ret['market_channel_nums'],
            'mc_valid_nums'       => $ret['mc_valid_nums'],
            'mc_customer_nums'    => $ret['mc_customer_nums'],
            'mc_valid_rate'       => $mc_valid_rate,
            'mc_sign_nums'        => $ret['mc_sign_nums'],
            'mc_sign_rate'        => $mc_sign_rate,
		];
		// 顾问
		$accept_rate = $ret['valid_communicate_nums'] ? round($ret['accept_nums']/$ret['valid_communicate_nums'],2) : 0.00;
		$trial_sign_rate = $ret['trial_nums'] ? round($ret['trial_sign_nums']/$ret['trial_nums'],2) : 0.00;
		$ret['counselor'] = [
            'sale_amount'            => $ret['sale_amount'],
            'sale_lesson_hours'      => $ret['sale_lesson_hours'],
            'customer_nums'          => $ret['customer_nums'],
            'valid_communicate_nums' => $ret['valid_communicate_nums'],
            'accept_nums'            => $ret['accept_nums'],
            'accept_rate'            => $accept_rate,
            'trial_nums'             => $ret['trial_nums'],
            'trial_sign_nums'        => $ret['trial_sign_nums'],
            'trial_sign_rate'        => $trial_sign_rate,
		];
		// 教学
		$attendance_rate = $ret['should_attendance_times'] ? round($ret['attendance_times']/$ret['should_attendance_times'],2) : 0.00;
		$ret['teach'] = [
            'employee_lesson_amount'  => $ret['employee_lesson_amount'],
            'employee_lesson_hours'   => $ret['employee_lesson_hours'],
            'attendance_times'        => $ret['attendance_times'],
            'should_attendance_times' => $ret['should_attendance_times'],
            'attendance_rate'         => $attendance_rate,
            'no_arrange_student_nums' => $ret['no_arrange_student_nums'],
            'attendance_nums'         => $ret['attendance_nums'],
            'no_class_student_nums'   => $ret['no_class_student_nums'],
            'no_attendance_nums'      => $ret['no_attendance_nums'],
 		];

		if(!empty($ret)){
			return $ret;
		}

		return [];

	}

	protected function getCells($index)
	{
		$arr = range('A','Z');
		return $arr[$index];
	}


	public function customExport($data,$excel,$params)
	{
	    $sheet = $excel->getSheet();
	    // 合并标题单元格
	    $sheet->mergeCells('A1:J1');
	    $sheet->mergeCells('A4:J4');
	    // 填充标题
	    $data = $data->toArray();
        // print_r($data);exit;
		$title = $data['month'].' '.$data['branch_name'].' '.'关键指标报表';
	    $sheet->setCellValue('A1',$title);

	    $map = [2=>'校区',5=>'教务',7=>'市场',9=>'顾问',11=>'教学'];
	    foreach ($map as $key => $value) {
	    	$mkey = (int)$key + 1;
            $sheet->mergeCells('A'.$key.':A'.$mkey);
	    	$sheet->setCellValue('A'.$key,$value);
	    }

	    // 样式
	    $sheet->getColumnDimension('A')->setWidth(10);
	    $sheet->getRowDimension('1')->setRowHeight(30);
	    for($i=2;$i<=12;$i++){
	    	$sheet->getRowDimension($i)->setRowHeight(25);
	    }
	    $sheet->getRowDimension('4')->setRowHeight(10);
	    // 单元格居中
        $sheet->getStyle('A1:J12')->getAlignment()->applyFromArray( [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] );
        // 边框
        $sheet->getStyle('A1:J12')->getBorders()->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '666666' ] ] ] );
	    

        // 填充校区数据
        $branch = $data['branch'];
        $renew_rate = $data['educate']['renew_rate'];
	    $branch_title = ['B'=>'在读学员数','C'=>'剩余课时数','D'=>'剩余课时金额','E'=>'班级数量','F'=>'班级平均学员数','G'=>'满校人数','H'=>'满校差额','I'=>'合同到期学员数','J'=>'续费率'];
	    foreach ($branch_title as $key => $value) {
	    	$sheet->setCellValue($key.'2',$value);
	    }
	    $branch_number = ['B'=>$branch['student_nums'],'C'=>$branch['remain_lesson_hours'],'D'=>$branch['remain_lesson_amount'],'E'=>$branch['class_nums'],'F'=>$branch['class_avg_student_nums'],'G'=>$branch['full_school_student_nums'],'H'=>$branch['differ_student_nums'],'I'=>$branch['expire_student_nums'],'J'=>$renew_rate];
	    foreach ($branch_number as $key => $value) {
	    	$sheet->setCellValue($key.'3',$value);
	    }


        // 填充教务数据
	    $educate = $data['educate'];
	    $educate_title = ['B'=>'新签学员数','C'=>'续费学员数','D'=>'转介绍成交人数','E'=>'转介绍成交率','F'=>'退费人数','G'=>'退费率','H'=>'教室周平均排课量','I'=>'教室使用率'];
	    foreach ($educate_title as $key => $value) {
	    	$sheet->setCellValue($key.'5',$value);
	    }
	    $educate_number = ['B'=>$educate['new_student_nums'],'C'=>$educate['renew_student_nums'],'D'=>$educate['refer_deal_nums'],'E'=>$educate['refer_deal_rate'],'F'=>$educate['refund_student_nums'],'G'=>$educate['refund_rate'],'H'=>$educate['cr_arrange_nums'],'I'=>$educate['cr_arrange_rate']];
	    foreach ($educate_number as $key => $value) {
	    	$sheet->setCellValue($key.'6',$value);
	    }
	    // 填充市场数据
	    $market = $data['market'];
	    $market_title = ['B'=>'市场名单数','C'=>'市场渠道成单金额','D'=>'来源渠道数量','E'=>'市场有效名单数','F'=>'市场名单有效率','G'=>'市场名单签约数','H'=>'市场名单签约率'];
	    foreach ($market_title as $key => $value) {
	    	$sheet->setCellValue($key.'7',$value);
	    }
	    $market_number = ['B'=>$market['mc_student_nums'],'C'=>$market['mc_deal_amount'],'D'=>$market['market_channel_nums'],'E'=>$market['mc_valid_nums'],'F'=>$market['mc_valid_rate'],'G'=>$market['mc_sign_nums'],'H'=>$market['mc_sign_rate']];
	    foreach ($market_number as $key => $value) {
	    	$sheet->setCellValue($key.'8',$value);
	    }
	    // 填充顾问数据
	    $counselor = $data['counselor'];
	    $counselor_title = ['B'=>'销售金额','C'=>'销售课时数','D'=>'客户名单数','E'=>'有效沟通数','F'=>'诺到人数','G'=>'诺到率','H'=>'试听人数','I'=>'试听报名人数','J'=>'试听成单率'];
	    foreach ($counselor_title as $key => $value) {
	    	$sheet->getColumnDimension($key)->setWidth(20);
	    	$sheet->setCellValue($key.'9',$value);
	    }
	    $counselor_number = ['B'=>$counselor['sale_amount'],'C'=>$counselor['sale_lesson_hours'],'D'=>$counselor['customer_nums'],'E'=>$counselor['valid_communicate_nums'],'F'=>$counselor['accept_nums'],'G'=>$counselor['accept_rate'],'H'=>$counselor['trial_nums'],'I'=>$counselor['trial_sign_nums'],'J'=>$counselor['trial_sign_rate']];
	    foreach ($counselor_number as $key => $value) {
	    	$sheet->setCellValue($key.'10',$value);
	    }
	    // 填充教学数据
	    $teach = $data['teach'];
	    $teach_title = ['B'=>'课耗金额','C'=>'学员消耗课时数','D'=>'出勤学员人次','E'=>'应出勤学员人次','F'=>'学员出勤率','G'=>'未分班学员','H'=>'未排课学员数','I'=>'未出勤学员','J'=>'实际出勤人数'];
	    foreach ($teach_title as $key => $value) {
	    	$sheet->setCellValue($key.'11',$value);
	    }
	    $teach_number = ['B'=>$teach['employee_lesson_amount'],'C'=>$teach['employee_lesson_hours'],'D'=>$teach['attendance_times'],'E'=>$teach['should_attendance_times'],'F'=>$teach['attendance_rate'],'G'=>$teach['no_class_student_nums'],'H'=>$teach['no_arrange_student_nums'],'I'=>$teach['no_attendance_nums'],'J'=>$teach['attendance_nums']];
	    foreach ($teach_number as $key => $value) {
	    	$sheet->setCellValue($key.'12',$value);
	    }


	    $sheet->setTitle($params['title']);
        return $excel->output();


	}



}