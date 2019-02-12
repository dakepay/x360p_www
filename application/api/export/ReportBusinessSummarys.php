<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportBusinessSummary;

class ReportBusinessSummarys extends Export
{
    protected $report_model = null;

    public function __init(){
        set_time_limit(0);
        ini_set("memory_limit","516M");
    }
    protected function get_title(){
        $params = $this->params;
        $title = sprintf('(%s~%s)',$params['start_date'],$params['end_date']);
        return $title;
    }

    protected function get_columns(){

    }


    protected function get_data()
    {
        $model = new ReportBusinessSummary;

        $this->report_model = $model;

        $input = $this->params;

        unset($input['bid']);

        $ret = $model->getDaySectionReport($input);

        return $ret;
    }


    /**
     * 10进制int（1-10）转26进制A-Z，服务于excel列数转换
     * @param int $intnum
     * @return string
     */
    public function intToColumn($intnum){
        $result_num = "";
        if(empty($intnum) || !is_numeric($intnum)) return $result_num;
        $remainder = 0;
        $int_last = doubleval($intnum);
        while($int_last>0){
            $remainder = fmod($int_last-1, 26) + 1; // 太大的数，使用%会导致溢出；由于26进制，应该是0-25，所以这里要减掉当前位置上多出的1
            $int_last = doubleval(floor(($int_last-1) / 26));
            $result_num = chr($remainder+64) . $result_num;
        }
        return $result_num;
    }

    protected function getCells($index)
    {
        return $this->intToColumn($index);
    }

    protected function align_center($sheet,$col_row){

        $sheet->getStyle($col_row)->getAlignment()->applyFromArray(
            [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        );

    }

    protected function set_export_extra_title($extra_title,$report_fields,$sheet,$is_company = false){

        $col = 0;
        if(!$is_company) {
            $sheet->setCellValue('A1', '校区');
            $sheet->mergeCells('A1:A3');
            $this->align_center($sheet,'A1:A3');
            $col++;

            $enable_company = user_config('params.enable_company');
            if($enable_company){
                $sheet->setCellValue('B1','分公司');
                $sheet->mergeCells('B1:B3');
                $this->align_center($sheet,'B1:B3');
                $col++;
            }
        }else{
            $sheet->setCellValue('A1','分公司');
            $sheet->mergeCells('A1:A3');
            $this->align_center($sheet,'A1:A3');
            $col++;
        }



        $field_index = [];
        $index = 0;
        foreach($report_fields as $f=>$r){
            $field_index[$f] = $index;
            $index++;
        }


        $c_r = 1;
        $col++;
        foreach($extra_title as $row){
            foreach($row as $r){
                $col_start_index = $field_index[$r['start_field']] + $col;
                $col_end_index   = $field_index[$r['end_field']] + $col;
                $c_s_c = $this->getCells($col_start_index);
                $c_e_c = $this->getCells($col_end_index);
                $sheet->setCellValue($c_s_c.$c_r,$r['title']);
                $sheet->mergeCells($c_s_c.$c_r.':'.$c_e_c.$c_r);
                $this->align_center($sheet,$c_s_c.$c_r);
            }
            $c_r++;
        }

        $c_c_i = $col;
        foreach($report_fields as $f=>$r){
            $c_c_c = $this->getCells($c_c_i);
            $sheet->setCellValue($c_c_c.$c_r,$r['title']);
            $c_c_i++;
        }

        $c_r++;

        $sheet->getRowDimension('1')->setRowHeight(25);
        $sheet->getRowDimension('2')->setRowHeight(25);
        $sheet->getRowDimension('3')->setRowHeight(25);

        return [$col,$c_r];
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

        $model = $this->report_model;

        $report_fields = $model->getReportFields();

        $extra_title = $model->getExtraTitle();

        $enable_company = user_config('params.enable_company');

        list($start_col,$start_row) = $this->set_export_extra_title($extra_title,$report_fields,$sheet);

        // 授课数据
        $list = $data['list'];

        $row_index = $start_row;
        foreach($list as $k=>$row){
            $sheet->setCellValue('A'.$row_index,get_branch_name($row['bid']));
            if($enable_company) {
                $sheet->setCellValue('B'.$row_index,get_dept_name($row['dept_id']));
            }


            $col = $start_col;
            foreach($report_fields as $f=>$fi){
                $c_c = $this->getCells($col);
                $value = isset($row[$f])?$row[$f]:'-';
                $sheet->setCellValue($c_c.$row_index,$value);
                $col++;
            }
            $row_index++;
        }

        $sheet->setTitle($params['title'].'- 按校区汇总');

        if($enable_company){
            $sheet = $excel->createSheet();
            list($start_col,$start_row) = $this->set_export_extra_title($extra_title,$report_fields,$sheet,true);

            $list = $data['list1'];

            $row_index = $start_row;

            foreach($list as $k=>$row){
                $sheet->setCellValue('A'.$row_index,get_dept_name($row['dept_id']));

                $col = $start_col;

                foreach($report_fields as $f=>$fi){
                    $c_c = $this->getCells($col);
                    $value = isset($row[$f])?$row[$f]:'-';
                    $sheet->setCellValue($c_c.$row_index,$value);
                    $col++;
                }
                $row_index++;
            }
            $sheet->setTitle($params['title'].'- 按分公司汇总');
        }


        return $excel->output();

    }
}