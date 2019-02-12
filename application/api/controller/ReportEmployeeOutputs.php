<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/12/23
 * Time: 17:14
 */

namespace app\api\controller;

use app\api\model\EmployeeLessonHour;
use app\api\model\StudentLessonHour;
use think\Request;

class ReportEmployeeOutputs extends Base
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
        $fields["count(slh_id)"]            = 'sum_student_nums';
        $fields["sum(lesson_amount)"]       = 'sum_lesson_amount';

        $with = [];
        if (in_array('cid', $group)) {
            $with[] = 'cls';
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
        

        $data['total_lesson_amount'] = 0;
        $data['total_student_nums']  = 0;
        $data['total_lesson_hours']  = 0;
        foreach ($data['list'] as &$item) {
            $data['total_lesson_hours']  += $item['sum_lesson_hours'];
            $data['total_student_nums']  += $item['sum_student_nums'];
            $data['total_lesson_amount'] += $item['sum_lesson_amount'];
            switch ($group[0]) {
                case 'bid':
                    $item['name'] = get_branch_name($item['bid']);
                    break;
                case 'sj_id':
                    $item['name'] = get_subject_name($item['sj_id']);
                    break;
                case 'lid':
                    $item['name'] = get_lesson_name($item['lid']);
                    break;
                case 'cid':
                    $item['name'] = get_class_name($item['cid']);
                    break;
                case 'eid':
                    $item['name'] = get_employee_name($item['eid']);
                    break;
                default:
                    break;
            }
        }
        
        return $this->sendSuccess($data);
    }

}