<?php

namespace app\api\controller;

use app\api\model\EmployeeLessonHour;
use think\Request;

class EmployeeLessonHours extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        if(isset($input['year_month']) && strlen($input['year_month']) == 6) {
            $start_day = date('Ym01', strtotime($input['year_month'].'01'));
            $end_day = date('Ymt', strtotime($input['year_month'].'01'));
            $input['int_day'] = sprintf('[between,%s,%s]', $start_day, $end_day);
        }
        $model = new EmployeeLessonHour();
        $rs = $model->getSearchResult($input);

        $rs['sum_total_lesson_hours']  = $model->autoWhere($input)->sum('total_lesson_hours');
        $rs['sum_total_lesson_amount'] = $model->autoWhere($input)->sum('total_lesson_amount');
        $rs['sum_payed_lesson_amount'] = $model->autoWhere($input)->sum('payed_lesson_amount');
        $rs['sum_unpayed_lesson_amount'] = $rs['sum_total_lesson_amount'] - $rs['sum_payed_lesson_amount'];
        return $this->sendSuccess($rs);
    }
    
    
}