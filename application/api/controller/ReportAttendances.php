<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/12/23
 * Time: 17:14
 */

namespace app\api\controller;

use app\api\model\StudentAttendance;
use think\Request;

class ReportAttendances extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $model = new StudentAttendance();
        $w = [];
        $w['og_id'] = gvar('og_id');

        if (!empty($input['group'])) {
            $group = explode(',', $input['group']);
        } else {
            $group = [];
        }
//        unset($input['group']);

        $table_fields = $model->getTableFields();
        foreach ($input as $key => $value) {
            if (in_array($key, $table_fields)) {
                $w[$key] = $value; //todo sql语法处理
            }
        }
        $with = [];
        if (in_array('bid', $group)) {
            $with['branch'] = function ($query) {
                $query->field(['bid', 'branch_name']);
            };
        }
        if (in_array('lid', $group)) {
            $with['lesson'] = function ($query) {
                $query->field(['lid', 'lesson_name', 'lesson_no']);
            };
        }
        if (in_array('sj_id', $group)) {
            $with['subject'] = function ($query) {
                $query->field(['sj_id', 'subject_name']);
            };
        }
        if (in_array('cid', $group)) {
            $with['cls']['teacher'] = function($query) {
                $query->field(['eid', 'ename']);
            };
            $with['cls']['assistant'] = function($query) {
                $query->field(['eid', 'ename']);
            };
        }
        if (in_array('eid', $group)) {
            $with[] = 'employee';
        }
        if (in_array('sid', $group)) {
            $with[] = 'student';
        }
        $fields = $group;
        $fields['count(satt_id)'] = 'total_student_attendance';
        $fields['count(distinct catt_id)'] = 'total_class_attendance';
        $fields['sum(is_in)']    = 'total_in';
        $fields['(sum(is_in)/count(satt_id))'] = 'in_rate';
        $fields['(sum(case when is_in=0 then 1 else 0 end)/count(satt_id))'] = 'out_rate';
        $fields['sum(case WHEN is_in=0 then 1 else 0 end)'] = 'total_out';
        $fields['sum(is_leave)'] = 'total_leave';
        $fields['sum(is_late)']  = 'total_late';
        $fields['sum(case WHEN is_in=1 and is_consume = 1 then 1 else 0 end)'] = 'in_consume_num';
        $fields['sum(case WHEN is_in=1 and is_consume = 0 then 1 else 0 end)'] = 'in_unconsume_num';
        $fields['sum(case WHEN is_in=0 and is_consume = 1 then 1 else 0 end)'] = 'out_consume_num';
        $fields['sum(case WHEN is_in=0 and is_consume = 0 then 1 else 0 end)'] = 'out_unconsume_num';
        $model->where([])
            ->group(join(',', $group))
            ->field($fields)
            ->with($with);
        if (!empty($input['order_field'])) {
            $model->order($input['order_field'], $input['order_sort']);
        } else {
            $input['order_field'] = 'total_student_attendance';
            $input['order_sort']  = 'desc';
        }

        $data = $model->getSearchResult($input);
        return $this->sendSuccess($data);
    }

    
    /**
     * 移动端 新 考勤汇总 按校区汇总
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function branch_attendances(Request $request)
    {
        $input = $request->param();

        if(!isset($input['start_date'])){
            $input['start_date'] = '1970-01-02';
            $input['end_date']   = '9999-12-31';
        }

        $start_int_day = format_int_day($input['start_date']);
        $end_int_day   = format_int_day($input['end_date']);

        $params['int_day'] = ['between',[$start_int_day,$end_int_day]];

        $bids = isset($input['bids']) ? explode(',',$input['bids']) : [];
        $params['bid'] = ['in',$bids];

        $mStudentAttendance = new StudentAttendance;
        $group = isset($input['group']) ? $input['group'] : 'bid';

        $data = $mStudentAttendance->where($params)->field($group)->group($group)->skipBid()->getSearchResult($input);
        
        $data['total_course_arrange_nums'] = 0;
        $data['total_lesson_amount'] = 0.000000;

        foreach ($data['list'] as &$item) {

            $item['course_arrange_nums'] = $this->get_course_arrange_nums_value($params,$group,$item[$group]);
            $item['lesson_amount'] = $this->get_lesson_amount_value($params,$group,$item[$group]);

            $item['real_attendance_times'] = $this->get_attendance_times_value($params,$group,$item[$group]);
            $item['should_attendance_times'] = $this->get_should_attendance_times_value($params,$group,$item[$group]);


            $item['attendance_rate'] = $item['should_attendance_times'] ? round($item['real_attendance_times']/$item['should_attendance_times'],2) : 0;

            switch ($group) {
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
                case 'sid':
                    $item['name'] = get_student_name($item['sid']);
                    break;
                default:
                    break;
            }

            $data['total_course_arrange_nums'] += $item['course_arrange_nums'];
            $data['total_lesson_amount'] += $item['lesson_amount'];
        }


        return $this->sendSuccess($data);


    }

    protected function get_course_arrange_nums_value($params,$group,$value)
    {
        $mStudentAttendance = new StudentAttendance;

        $ca_ids = $mStudentAttendance->where(['int_day'=>$params['int_day'],$group=>$value])->column('ca_id');
        $count = count(array_unique($ca_ids));

        return $count;
    }

    protected function get_lesson_amount_value($params,$group,$value)
    {
        $mStudentAttendance = new StudentAttendance;

        $ca_ids = $mStudentAttendance->where(['int_day'=>$params['int_day'],$group=>$value])->column('ca_id');
        $ca_ids = array_unique($ca_ids);
        $total_lesson_amount = 0.000000;
        foreach ($ca_ids as $ca_id) {
            $lesson_amount = $this->m_student_lesson_hour->where(['ca_id'=>$ca_id,$group=>$value])->sum('lesson_amount');
            $total_lesson_amount += $lesson_amount;
        }

        return $total_lesson_amount;
    }


    protected function get_attendance_times_value($params,$group,$value)
    {
        $mStudentAttendance = new StudentAttendance;

        $w[$group] = $value;
        $w['int_day'] = $params['int_day'];
        $w['is_in'] = 1;
        $count = $mStudentAttendance->where($w)->count();
        return $count;
    }

    protected function get_should_attendance_times_value($params,$group,$value)
    {
        $mStudentAttendance = new StudentAttendance;

        $w[$group] = $value;
        $w['int_day'] = $params['int_day'];
        $count = $mStudentAttendance->where($w)->count();
        return $count;
    }


}