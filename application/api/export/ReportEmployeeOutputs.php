<?php

namespace app\api\export;

use app\api\model\StudentLessonHour;
use app\common\Export;

class ReportEmployeeOutputs extends Export
{
	protected $columns = [
	    ['field'=>'bid','title'=>'校区','width'=>20],
	    ['field'=>'sum_lesson_hours','title'=>'课时数','width'=>20],
	    ['field'=>'sum_student_nums','title'=>'学员数','width'=>20],
	    ['field'=>'sum_total_lesson_amount','title'=>'课时金额','width'=>20],
	];


	protected function get_title()
	{
		$input = $this->params;
		$group = $input['group'];
	    switch ($group) {
	    	case 'bid':
	    		$title = '校区';
	    		break;
	        case 'lid':
	            $title = '课程';
	            break;
	        case 'sj_id':
	            $title = '科目';
	            break;
	        case 'cid':
	            $title = '班级';
	            break;
	        case 'eid':
	            $title = '老师';
	            break;
	    	default:
	    		$title = '校区';
	    		break;
	    }
	    $time = sprintf('%s_%s',format_int_day($input['start_date']),format_int_day($input['end_date']));
	    $title = $time.'课耗确收表(按'.$title.')';
	    return $title;
	}


	protected function get_columns()
	{
		$input = $this->params;
	    $group = $input['group'];
	    switch ($group) {
	    	case 'bid':
	    		$name = '校区';
	    		break;
	    	case 'lid':
	    	    $name = '课程';
	    	    break;
	    	case 'sj_id':
	    	    $name = '科目';
	    	    break;
	    	case 'cid':
	    	    $name = '班级';
	    	    break;
	    	case 'eid':
	    	    $name = '老师';
	    	    break;
	    	default:
	    		$name = '校区';
	    		break;
	    }
	    $col = $this->columns;
	    $col[0]['field'] = $group;
	    $col[0]['title'] = $name;
		return $col;
	}

	protected function get_data()
	{
		$input = $this->params;
        $model = new StudentLessonHour();
        $w = [];

        if(isset($input['start_date']) && !empty($input['start_date'])){
            $w['int_day'] = ['between',[format_int_day($input['start_date']),format_int_day($input['end_date'])]];
        }
        if (!empty($input['group'])) {
            $group = explode(',', $input['group']);
        } else {
            $group = [];
        }

        $fields = $group;
        $fields["sum(lesson_hours)"]        = 'sum_lesson_hours';
        $fields["sum(lesson_amount)"] = 'sum_total_lesson_amount';
        $fields["count(slh_id)"]        = 'sum_student_nums';

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

        $ret = $model->getSearchResult($input);
        foreach ($ret['list'] as &$item) {

        	switch ($input['group']) {
        		case 'bid':
        			$item['bid'] = get_branch_name($item['bid']);
        			break;
        		case 'lid':
        		    $item['lid'] = get_lesson_name($item['lid']);
        		    break;
        		case 'sj_id':
        		    $item['sj_id'] = get_subject_name($item['sj_id']);
        		    break;
        		case 'cid':
        		    $item['cid'] = get_class_name($item['cid']);
        		    break;
        		case 'eid':
        		    $item['eid'] = get_teacher_name($item['eid']);
        		    break;
        		default:
        		    $item['bid'] = get_branch_name($item['bid']);
        			break;
        	}
        }

        if(!empty($ret['list'])){
        	return collection($ret['list'])->toArray();
        }

        return [];


	}


}