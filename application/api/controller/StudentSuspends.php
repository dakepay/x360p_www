<?php 

namespace app\api\controller;

use think\Request;
use app\api\model\StudentSuspend;
use app\api\model\Student;

class StudentSuspends extends Base
{
    public function get_list(Request $request)
    {
    	$model = new StudentSuspend;
        $input = $request->param();

        $order_field = isset($input['order_field']) ? $input['order_field'] : 'ss_id';
        $order_sort  = isset($input['order_sort']) ? $input['order_sort'] : 'desc';

    	$ret = $model->order($order_field,$order_sort)->getSearchResult($input);

    	$m_st = new Student;

    	foreach ($ret['list'] as $k => $v) {
    		$ret['list'][$k]['student'] = $m_st->where('sid',$v['sid'])->field('sid,student_name')->find();
    		$ret['list'][$k]['begin_time'] = date('Y-m-d',$v['begin_time']);
    		$ret['list'][$k]['end_time'] = date('Y-m-d',$v['end_time']);
    		$ret['list'][$k]['create_employee_name'] = get_teacher_name($v['create_uid']);
    	}

    	return $this->sendSuccess($ret);

    }




}