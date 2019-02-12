<?php
namespace app\api\export;

use app\common\Export;
use app\api\model\StudentAttendance;

class ReportAttendances extends Export
{
	protected $columns = [
        ['field'=>'name','title'=>'校区','width'=>20],
        ['field'=>'total_class_attendance','title'=>'上课次数','width'=>20],
        ['field'=>'total_student_attendance','title'=>'应到人次数','width'=>20],
        ['field'=>'in_consume_num','title'=>'出勤计费次数','width'=>20],
        ['field'=>'in_unconsume_num','title'=>'出勤未计费次数','width'=>20],
        ['field'=>'in_rate','title'=>'出勤率','width'=>20],      
        ['field'=>'out_consume_num','title'=>'缺勤计费次数','width'=>20],
        ['field'=>'out_unconsume_num','title'=>'缺勤未计费次数','width'=>20],
        ['field'=>'out_rate','title'=>'缺勤率','width'=>20]
	];

	protected function convert_name($value)
	{
		$map = ['bid'=>'校区','lid'=>'课程','sj_id'=>'科目','cid'=>'班级','eid'=>'老师','att_way'=>'考勤方式'];
		if(key_exists($value,$map)){
			return $map[$value];
		}
		return '-';
	}

	protected function get_title()
	{
	    $input = $this->params;
	    $name = $this->convert_name($input['group']);
	    $title = '考勤统计（按'.$name.'）';
        return $title;
	}

	protected function get_columns()
    {
        $columns = $this->columns;
        $input = $this->params;
        switch ($input['group']) {
            case 'bid':
                $columns[0]['title'] = '校区';
                break;
            case 'lid':
                $columns[0]['title'] = '课程';
                break;
            case 'sj_id':
                $columns[0]['title'] = '科目';
                break;
            case 'cid':
                $columns[0]['title'] = '班级';
                break;
            case 'eid':
                $columns[0]['title'] = '老师';
                break;
            case 'att_way':
                $columns[0]['title'] = '考勤方式';
                break;
            default:
                # code...
                break;
        }
        return $columns;
    }

    protected function convert_att_way($value)
    {
        $map = ['登记考勤','刷卡考勤','点名考勤','自由登记考勤'];
        if(key_exists($value,$map)){
            return $map[$value];
        }
        return '-';
    }

    protected function get_data()
    {
        $input = $this->params;
        $model = new StudentAttendance();
        $w = [];
        $w['og_id'] = gvar('og_id');

        if (!empty($input['group'])) {
            $group = explode(',', $input['group']);
        } else {
            $group = [];
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
            ->field($fields);
        if (!empty($input['order_field'])) {
            $model->order($input['order_field'], $input['order_sort']);
        } else {
            $input['order_field'] = 'total_student_attendance';
            $input['order_sort']  = 'desc';
        }

        $data = $model->getSearchResult($input);
        foreach($data['list'] as &$item){
            switch($input['group']){
                case 'bid':
                    $item['name'] = get_branch_name($item['bid']);
                    break;
                case 'lid':
                    $item['name'] = get_lesson_name($item['lid']);
                    break;
                case 'cid':
                    $item['name'] = get_class_name($item['cid']);
                    break;
                case 'sj_id':
                    $item['name'] = get_subject_name($item['sj_id']);
                    break;
                case 'eid':
                    $item['name'] = get_employee_name($item['eid']);
                    break;
                case 'att_way':
                    $item['name'] = $this->convert_att_way($item['att_way']);
                    break;
                default:
                    break;
            }
        }

        if(!empty($data['list'])){
            return collection($data['list'])->toArray();
        }

        return [];
    }


}
