<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\EmployeeLessonHour;
use app\api\model\CourseArrange;
use app\api\model\CourseArrangeStudent;

class ReportEmployeeLessonHours extends Export
{

	protected function get_title(){
		$title = '教师授课统计表';
		return $title;
	}

    protected function get_columns(){

    }


	protected function get_lesson_num($start,$end,$eid)
    {
        $model = new EmployeeLessonHour;
        $w['int_day'] = ['between',[date('Ymd',strtotime($start)),date('Ymd',strtotime($end))]];
        $w['eid'] = $eid;
        // $w['bid'] = $bid;
        return  $model->where($w)->sum('total_lesson_hours');
    }

    protected function get_trial_num($start,$end,$eid)
    {
        $model = new CourseArrange;
        $w['teach_eid'] = $eid;
        // $w['bid'] = $bid;
        $w['int_day'] = ['between',[date('Ymd',strtotime($start)),date('Ymd',strtotime($end))]];
        $ca_ids = $model->where($w)->column('ca_id');

        unset($w['bid']);
        unset($w['teach_eid']);
        $w['ca_id'] = ['in',$ca_ids];
        $w['is_trial'] = 1; 
        $w['is_attendance'] = 1;
        
        return (new CourseArrangeStudent)->where($w)->count();
    }

	protected function get_data()
	{
		$model = new EmployeeLessonHour;
        $input = $this->params;

		// $bid = $input['bid'];
		$w = [];
        $w['og_id'] = gvar('og_id');
		if(!empty($input['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		}

		$group = 'eid';
		$fields = ['eid'];
		$data = $model->where($w)->field($fields)->group($group)->order('eid asc')->getSearchResult($input,[],false);

		$start = strtotime($input['start_date']);
        $end = strtotime($input['end_date']);
        $mode = 0;
        $week_section = get_week_section($start,$end,$mode);

        $data['week_section'] = $week_section;


        foreach ($data['list'] as $k => $v) {
        	$data['list'][$k]['lesson_nums'] = $this->get_lesson_num($input['start_date'],$input['end_date'],$v['eid']);
            $data['list'][$k]['trial_nums'] = $this->get_trial_num($input['start_date'],$input['end_date'],$v['eid']);
            $data['list'][$k]['total_nums'] = $data['list'][$k]['lesson_nums']+$data['list'][$k]['trial_nums'];
            foreach ($week_section as $k1 => $v1) {
                $data['list'][$k]['weeks'][$k1]['lesson_num'] = $this->get_lesson_num($v1['start'],$v1['end'],$v['eid']);
                $data['list'][$k]['weeks'][$k1]['trial_num'] = $this->get_trial_num($v1['start'],$v1['end'],$v['eid']);
            }
        }

		if(!empty($data)){
			return collection($data)->toArray();
		}
		return [];
	}
    
    /**
     * 获取星期数组 ['第一周','第二周','第三周','第四周'];
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
	protected function get_section($weeks)
	{
        $section = [];
        $arr = ['一','二','三','四','五','六'];
        foreach ($weeks as $k => $v) {
        	$section[] = "第".$arr[$k]."周\r\n(".date('m-d',strtotime($v['start']))."~".date('m-d',strtotime($v['end'])).")";
        }
        // $len = count($weeks,0);
        // for ($i = 0; $i < $len ; $i++) { 
        // 	$section[] = '第'.$arr[$i].'周';
        // }
        return $section;
	}

	protected function getCells($index)
	{
		$arr = range('A','Z');
		return $arr[$index];
	}



    /**
     * 自定义导出方法
     * @param  [type] $list   [description]
     * @param  [type] $excel  [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
	public function customExport($data,$excel,$params)
	{
        $sheet = $excel->getSheet();
        // 授课类型
        $lesson_type = ['课时','试听'];
        // 授课星期
        $week_section = $this->get_section($data['week_section']);
        // 授课数据
        $list = $data['list'];

        // 合并标题单元格
        $i = 2*count($week_section)+6;
        $xIndex = $this->getCells($i);
        $sheet->mergeCells('A1:'.$xIndex.'1');  
        $l = count($list,0);
        $yIndex = $l+5;
        $sheet->setCellValue('A'.$yIndex,'合计');
        $zIndex = $yIndex - 1;
        $f = 5;

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getRowDimension('1')->setRowHeight(25);
        for ($m=2; $m <= $yIndex  ; $m++) { 
        	$sheet->getRowDimension($m)->setRowHeight(20);
        }
        $sheet->getRowDimension('3')->setRowHeight(40);

        // 单元格居中
        $sheet->getStyle('A1:'.$xIndex.$yIndex)->getAlignment()->applyFromArray( [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] );
        // 边框
        $sheet->getStyle('A3:'.$xIndex.$yIndex)->getBorders()->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '666666' ] ] ] );

        // $sheet->getStyle('B2')->getFill()->applyFromArray( [ 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR, 'rotation' => 0, 'startColor' => [ 'rgb' => '333333' ], 'endColor' => [ 'argb' => '66666666' ] ] );
        
        // 循环星期
        foreach ($week_section as $k => $v){
        	$weekIndex = $this->getCells($k*2+1);
        	$nextIndex = $this->getCells($k*2+2);
        	$sheet->mergeCells($weekIndex.'3:'.$nextIndex.'3');
        	$sheet->setCellValue($weekIndex.'3',$v);
        	$sheet->getStyle($weekIndex.'3')->getAlignment()->setWrapText(true);
        }
     
        $date = $data['week_section'][0]['start'];
        $year = date('Y',strtotime($date));
        $month = date('m',strtotime($date));
        
        $sheet->mergeCells('A3:A4');
        $sheet->setCellValue('A1','教师授课统计表')->setCellValue('A2','年份：')->setCellValue('B2',$year)->setCellValue('D2','月份')->setCellValue('E2',$month);
        
        $x = 2*count($week_section)+1;
        $monthIndexStart = $this->getCells($x);
        $monthIndexMid = $this->getCells($x+1);
        $monthIndexEnd = $this->getCells($x+2);
        $salaryIndexStart = $this->getCells($x+3);
        $salaryIndexMid = $this->getCells($x+4);
        $salaryIndexEnd = $this->getCells($x+5);

        $j = 5;
        foreach ($list as $k => $v) {
        	$v['eid'] = get_teacher_name($v['eid']);
        	$sheet->setCellValue('A3','老师姓名');
        	$sheet->setCellValue('A'.$j,$v['eid']);
        	$sheet->setCellValue($monthIndexStart.$j,$v['lesson_nums']);
        	$sheet->setCellValue($monthIndexMid.$j,$v['trial_nums']);
        	$sheet->setCellValue($monthIndexEnd.$j,$v['total_nums']);

        	$sheet->setCellValue($salaryIndexEnd.$j,'=SUM('.$salaryIndexStart.$j.':'.$salaryIndexMid.$j.')');

        	foreach ($v['weeks'] as $k1 => $v1) {
        		$lessonIndex = $this->getCells($k1*2+1);
        		$trialIndex = $this->getCells($k1*2+2);
        		$sheet->setCellValue($lessonIndex.'4','课时')->setCellValue($trialIndex.'4','试听');
        		$sheet->setCellValue($lessonIndex.$j,$v1['lesson_num'])->setCellValue($trialIndex.$j,$v1['trial_num']);
        		$sheet->setCellValue($lessonIndex.$yIndex,'=SUM('.$lessonIndex.$f.':'.$lessonIndex.$zIndex.')')->setCellValue($trialIndex.$yIndex,'=SUM('.$trialIndex.$f.':'.$trialIndex.$zIndex.')');
        	}
        	$j ++;
        }
  
        $sheet->setCellValue($monthIndexStart.'3','月统计');
        $sheet->mergeCells($monthIndexStart.'3:'.$monthIndexEnd.'3');
        $sheet->setCellValue($monthIndexStart.'4','课时');
        $sheet->setCellValue($monthIndexMid.'4','试听');
        $sheet->setCellValue($monthIndexEnd.'4','合计');
        $sheet->setCellValue($monthIndexStart.$yIndex,'=SUM('.$monthIndexStart.$f.':'.$monthIndexStart.$zIndex.')');
        $sheet->setCellValue($monthIndexMid.$yIndex,'=SUM('.$monthIndexMid.$f.':'.$monthIndexMid.$zIndex.')');
        $sheet->setCellValue($monthIndexEnd.$yIndex,'=SUM('.$monthIndexEnd.$f.':'.$monthIndexEnd.$zIndex.')');

        
        $sheet->setCellValue($salaryIndexStart.'3','工资统计');
        $sheet->mergeCells($salaryIndexStart.'3:'.$salaryIndexEnd.'3');
        $sheet->setCellValue($salaryIndexStart.'4','基本工资');
        $sheet->setCellValue($salaryIndexMid.'4','课时费');
        $sheet->setCellValue($salaryIndexEnd.'4','合计');
        $sheet->setCellValue($salaryIndexStart.$yIndex,'=SUM('.$salaryIndexStart.$f.':'.$salaryIndexStart.$zIndex.')');
        $sheet->setCellValue($salaryIndexMid.$yIndex,'=SUM('.$salaryIndexMid.$f.':'.$salaryIndexMid.$zIndex.')');
        $sheet->setCellValue($salaryIndexEnd.$yIndex,'=SUM('.$salaryIndexEnd.$f.':'.$salaryIndexEnd.$zIndex.')');

        $sheet->setTitle($params['title']);
        return $excel->output();

	}



}