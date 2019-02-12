<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/12/23
 * Time: 17:14
 */

namespace app\api\controller;

use app\api\model\StudentLessonHour;
use think\Request;

class ReportStudentConsumes extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $model = new StudentLessonHour();

        $w = [];

        if(isset($input['start_date']) && !empty($input['start_date'])){
            $start_int_day = format_int_day($input['start_date']);
            $end_int_day   = format_int_day($input['end_date']);
            $w['int_day'] = ['between',[$start_int_day,$end_int_day]];
        }
        if(isset($input['bids'])){
            $bids = explode(',',$input['bids']);
            $w['bid'] = ['in',$bids];
        }

        if (!empty($input['group'])) {
            $group = explode(',', $input['group']);
        } else {
            $group = [];
        }

        $fields = $group;
        $fields["sum(lesson_hours)"]        = 'sum_lesson_hours';
        $fields["sum(lesson_amount)"]       = 'sum_total_lesson_amount';
        $fields["count(slh_id)"]            = 'sum_lesson_times';
        $with = [];
        if (in_array('sid', $group)) {
            $with['student'] = function($query) {
                $query->field(['sid', 'bid', 'sno', 'student_name']);
            };
        }

        $model->where($w)
            ->group(join(',', $group))
            ->field($fields)
            ->with($with);
        if (!empty($input['order_field'])) {
            $model->order($input['order_field'], $input['order_sort']);
        } else {
            $input['order_field'] = 'sum_lesson_hours';
            $input['order_sort']  = 'desc';
        }
        if(isset($input['bids'])){
            $data = $model->skipBid()->getSearchResult($input);
        }else{
            $data = $model->getSearchResult($input);
        }

        $data['total_lesson_hours'] = 0;
        $data['total_lesson_amount'] = 0.000000;
        $data['total_student_nums'] = 0;
        foreach ($data['list'] as &$item) {
            $data['total_lesson_hours'] += $item['sum_lesson_hours'];
            $data['total_lesson_amount'] += $item['sum_total_lesson_amount'];
            $data['total_student_nums'] ++;
            $item['name'] = get_student_name($item['sid']);
        }
        return $this->sendSuccess($data);
    }
}