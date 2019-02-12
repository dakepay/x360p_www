<?php
namespace app\api\export;

use app\common\Export;
use app\api\model\ReportServiceBySystem;

class ReportServiceBySystems extends Export
{
	protected function get_title()
	{
		$month = date('m',strtotime($this->params['start_date']));
		$title = '系统服务('.$month.'月)统计表';
		return $title;
	}

	protected function get_columns(){

    }

	protected function getSum($eid,$bid,$start,$end,$field)
	{
		$model = new ReportServiceBySystem;
		$w['int_day'] = ['between',[date('Ymd',strtotime($start)),date('Ymd',strtotime($end))]];
		$w['eid'] = $eid;
		$w['bid'] = $bid;
		$total = $model->where($w)->sum($field);
		return $total;
	}


	protected function get_data()
	{
		$model = new ReportServiceBySystem;
		$input = $this->params;
		$group = 'eid';
		$fields = 'eid';
		$sumFields = ReportServiceBySystem::getSumFields();
		$w = [];
		if(!empty($input['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		}
		$data = $model->where($w)->field($fields)->group($group)->order('eid asc')->getSearchResult($input,[],false);
		
		foreach ($data['list'] as $k => $v) {
			foreach ($sumFields as $field) {
				$data['list'][$k]['sum_'.$field] = $this->getSum($v['eid'],$input['bid'],$input['start_date'],$input['end_date'],$field);
				$data['list'][$k]['eid'] = get_teacher_name($v['eid']);
			}
		}

		if($data['list']){
			return collection($data['list'])->toArray();
		}
		return [];
	}


	/**
     * 获取列单元格
     * @param  [type] $index [description]
     * @return [type]        [description]
     */
	protected function getCells($index)
	{
		$arr = range('A','Z');
		return $arr[$index];
	}

	/**
     * 自定义导出方法
     * @param  [type] $data   [description]
     * @param  [type] $excel  [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
	public function customExport($data,$excel,$params)
	{
		$sheet = $excel->getSheet();
        
        $arr = ['老师姓名','排课次数','考勤次数'];
        foreach ($arr as $k => $val) {
        	$cIndex = $this->getCells($k);
        	$sheet->setCellValue($cIndex.'1',$val);
        	$sheet->mergeCells($cIndex.'1:'.$cIndex.'2');
        }

		$service_column = ['课前提醒','备课服务','课评服务','作业服务','作品服务','学员回访'];
		$type = ['次数','人数'];
		foreach ($service_column as $key => $value) {
			$i = 2*$key + 3; 
			$j = 2*$key + 4;
			$columnIndex1 = $this->getCells($i);
			$columnIndex2 = $this->getCells($j);
			$sheet->setCellValue($columnIndex1.'1',$value);
			$sheet->mergeCells($columnIndex1.'1:'.$columnIndex2.'1');
			$sheet->setCellValue($columnIndex1.'2',$type[0]);
			$sheet->setCellValue($columnIndex2.'2',$type[1]);
		}
        $fields = ['eid','sum_arrange_times','sum_attendance_times','sum_s1_times','sum_s1_nums','sum_s2_times','sum_s2_nums','sum_s4_times','sum_s4_nums','sum_s5_times','sum_s5_nums','sum_s6_times','sum_s6_nums','sum_s7_times','sum_s7_nums',];
		$start = 3;

		$x = count($service_column)*2 + 2;
		$y = count($data) +3;
		$z = $y - 1;
		$xIndex = $this->getCells($x);

		for ($i = 1; $i <= $x; $i++) { 
			$totalIndex = $this->getCells($i);
			$sheet->setCellValue($totalIndex.$y,'=SUM('.$totalIndex.$start.':'.$totalIndex.$z.')');
		}
		
		$sheet->setCellValue('A'.$y,'合计');
		foreach ($data as $k => $v) {
            foreach ($fields as $key => $value) {
            	$dataIndex = $this->getCells($key);
            	$sheet->setCellValue($dataIndex.$start,$v[$value]);
            }
			$start++;
		}
		
        // 宽高
        $sheet->getColumnDimension('A')->setWidth(15);
		for ($m=1; $m <= $y  ; $m++) { 
        	$sheet->getRowDimension($m)->setRowHeight(20);
        }
		// 居中
		$sheet->getStyle('A1:'.$xIndex.$y)->getAlignment()->applyFromArray( [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] );
		// 边框
        $sheet->getStyle('A1:'.$xIndex.$y)->getBorders()->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '999999' ] ] ] );
		


		$sheet->setTitle($params['title']);
        return $excel->output();

	}
}