<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportOrgSummary;

class ReportOrgSummarys extends Export
{
    protected $report_model = null;
    protected function get_title(){
        $params = $this->params;
        $title = sprintf('加盟商运营总表(%s~%s)',$params['start_date'],$params['end_date']);
        return $title;
    }

    protected function init_report_model(){
        if(is_null($this->report_model)){
            $this->report_model = new ReportOrgSummary;
        }
    }

    protected function get_columns(){
        $this->init_report_model();
        $model = $this->report_model;

        $extra_export_fields = $model->getExtraExportFields();
        $export_fields = $model->getReportFields();

        $columns = [];

        foreach($extra_export_fields as $f=>$r){
            $columns[] = [
                'field'=>$f,
                'title'=>$r['title'],
                'width'=>20
            ];
        }

        foreach($export_fields as $f=>$r){
            $columns[] = [
                'field'=>$f,
                'title'=>$r['title'],
                'width'=>20
            ];
        }
        return $columns;

    }

    protected function get_data()
    {
        $this->init_report_model();
        $input = $this->params;
        unset($input['bid']);
        $model = $this->report_model;
        $ret = $model->getDaySectionReport($input,false);
        return $ret['list'];
    }


}