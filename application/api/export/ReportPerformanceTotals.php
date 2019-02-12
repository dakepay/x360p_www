<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\OrderPerformance;
use app\api\model\EmployeeReceipt;

class ReportPerformanceTotals extends Export
{
	protected function get_title()
	{
		$title = '签单回款汇总表';
		return $title;
	}

    protected function get_columns(){

    }


	protected function get_order_amount($eid,$role,$input)
    {
        $model = new OrderPerformance;
        $w['eid'] = $eid;
        $w['sale_role_did'] = $role;
        $data = $model->where($w)->getSearchResult($input,[],false);
        $sum = 0;
        foreach ($data['list'] as $k => $v) {
            $sum += $v['amount'];
        }
        return $sum;
    }

    protected function get_receipt_amount($eid,$role,$input)
    {
        $model = new EmployeeReceipt;
        $w['eid'] = $eid;
        $w['sale_role_did'] = $role;
        $data = $model->where($w)->getSearchResult($input,[],false);
        $sum = 0;
        foreach ($data['list'] as $k => $v) {
            $sum += $v['amount'];
        }
        return $sum;
    }

	protected function get_data()
	{

        $model = new OrderPerformance;
        $fields = ['eid'];
        $eids = [];
        $input = $this->params;
        if(!empty($input['eid'])){
            $eids[0] = $input['eid'];
            unset($input['eid']);
        }else{
            $data = $model->field($fields)->order('eid asc')->getSearchResult($input,[],false);
            foreach ($data['list'] as $k => $v) {
                $eids[] = $v['eid'];
            }
            $eids = array_values(array_unique($eids));
        }

        $ret['list'] = [];
        foreach ($eids as $k => $eid) {
            $ret['list'][$k]['eid'] = $eid;
            $roles = $model->where('eid',$eid)->column('sale_role_did');
            $roles = array_values(array_unique($roles));
            foreach ($roles as $k1 => $role) {
                $ret['list'][$k]['infos'][$k1]['sale_role_did'] = $role;
                $ret['list'][$k]['infos'][$k1]['order_amount'] = $this->get_order_amount($eid,$role,$input);
                $ret['list'][$k]['infos'][$k1]['receipt_amount'] = $this->get_receipt_amount($eid,$role,$input);
            }
        }

        if(!empty($ret['list'])){
        	return collection($ret['list'])->toArray();
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
        $colums = ['业绩归属人','签单角色','签单金额','回款金额'];
        for ($i = 0; $i < 4; $i++) { 
        	$titleIndex = $this->getCells($i);
        	$sheet->setCellValue($titleIndex.'2',$colums[$i]);
        	$sheet->getColumnDimension($titleIndex)->setWidth(20);
        }
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1','签单回款汇总表');

        $i = 3;
        foreach ($data as $k => $v) {
        	$v['eid'] = get_teacher_name($v['eid']);
        	$sheet->setCellValue('A'.$i,$v['eid']);
        	foreach ($v['infos'] as $k1 => $v1) {
        		$j = $k1+$i;
        		$v1['sale_role_did'] = get_did_value($v1['sale_role_did']);
        		$sheet->setCellValue('B'.$j,$v1['sale_role_did'])->setCellValue('C'.$j,$v1['order_amount'])->setCellValue('D'.$j,$v1['receipt_amount']);
        		$k = $i+count($v['infos'])-1;
        		$sheet->mergeCells('A'.$i.':'.'A'.$k);
        	}
        	$i+=count($v['infos']);
        }

        // 设置单元格高度
        $sheet->getRowDimension('1')->setRowHeight(25);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $h = $i+1;
        for ($x = 3; $x < $h; $x++) { 
        	$sheet->getRowDimension($x)->setRowHeight(20);
        }
        $m = $i - 1;
        $sheet->setCellValue('A'.$i,'合计')->setCellValue('C'.$i,'=SUM(C3:C'.$m.')')->setCellValue('D'.$i,'=SUM(D3:D'.$m.')');
        // 单元格居中
        $sheet->getStyle('A1:D'.$h)->getAlignment()->applyFromArray( [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] );
        // 边框
        $sheet->getStyle('A1:D'.$i)->getBorders()->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '666666' ] ] ] );

        $sheet->setTitle($params['title']);
        return $excel->output();


	}



}