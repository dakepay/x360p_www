<?php
namespace app\api\Export;

use app\api\model\StudentAttendance;
use app\common\Export;
class ReportAttendanceStudents extends Export
{
    protected $columns = [
        ['field'=>'student_name','title'=>'学员','width'=>20],
        ['field'=>'sno','title'=>'学号','width'=>20],
        ['field'=>'branch_name','title'=>'所属校区','width'=>20],
        ['field'=>'total_class_attendance','title'=>'上课次数','width'=>20],
        ['field'=>'total_student_attendance','title'=>'应到人次数','width'=>20],
        ['field'=>'in_consume_num','title'=>'出勤计费次数','width'=>20],
        ['field'=>'in_unconsume_num','title'=>'出勤未计费次数','width'=>20],
        ['field'=>'in_rate','title'=>'出勤率','width'=>20],
        ['field'=>'out_consume_num','title'=>'缺勤计费次数','width'=>20],
        ['field'=>'out_unconsume_num','title'=>'缺勤未计费次数','width'=>20],
        ['field'=>'out_rate','title'=>'缺勤率','width'=>20]
    ];

    /**
     * @return string
     */
    protected function get_title()
    {
        $input = $this->params;
        $title = '考勤统计（按学员）';
        return $title;
    }

    public function get_data()
    {
        $input = $this->params;
        $input['group'] = 'sid';
        $model = new StudentAttendance();


        $w = [];
        $w['og_id'] = gvar('og_id');

        $group = explode(',', $input['group']);

        $with = [];
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
        foreach($data['list'] as &$item){
            $item['student_name'] = $item['student']['student_name'];
            $item['sno'] = $item['student']['sno'];
            $item['branch_name'] = get_branch_name($item['student']['bid']);
        }

        if(!empty($data)){
            return collection($data['list'])->toArray();
        }
        return [];
    }




}