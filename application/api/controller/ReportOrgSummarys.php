<?php

namespace app\api\controller;
use think\Request;
use app\api\model\ReportOrgSummary;


class ReportOrgSummarys extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $model = new ReportOrgSummary();
        $result = $model->getDaySectionReport($input,true);
        if(!$result) {
            return $this->sendError(400, $model->getError());
        }
        return $this->sendSuccess($result);
    }

}