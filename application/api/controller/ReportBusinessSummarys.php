<?php

namespace app\api\controller;
use think\Request;
use app\api\model\ReportBusinessSummary;


class ReportBusinessSummarys extends Base
{
    public function get_list(Request $request)
    {
        set_time_limit(0);
        ini_set("memory_limit","512M");
        $input = $request->get();
        $model = new ReportBusinessSummary();
        $result = $model->getDaySectionReport($input);
        if(!$result) {
            return $this->sendError(400, $model->getError());
        }
        return $this->sendSuccess($result);
    }

}