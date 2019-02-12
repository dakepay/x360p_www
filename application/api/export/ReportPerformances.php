<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\OrderPerformance;

class ReportPerformances extends Export
{
	protected $columns = [
        ['field'=>'eid','title'=>'业绩归属人','width'=>20],
        ['field'=>'sale_role_did','title'=>'签单角色','width'=>20],
        ['field'=>'program','title'=>'项目','width'=>30],
        ['field'=>'sid','title'=>'学员','width'=>20],
        ['field'=>'price','title'=>'单价','width'=>20],
        ['field'=>'amount','title'=>'签单金额','width'=>20],
        ['field'=>'create_time','title'=>'签单日期','width'=>20],
        ['field'=>'consume_type','title'=>'收费类型','width'=>20],

	];

	protected function get_title()
	{
		$title = '签单业绩汇总表';
		return $title;
	}

	protected function get_order_item_info($id,$cache = true){
        return get_row_info($id,'order_item','oid',$cache);
    }

    protected function get_order_item_program($oid)
    {
        $oinfo = $this->get_order_item_info($oid);
        if($oinfo['cid']){
            return get_class_name($oinfo['cid']).'/'.get_lesson_name($oinfo['lid']);
        }else{
            return get_lesson_name($oinfo['lid']);
        }
    }

    protected function convert_consume_type($key)
    {
    	$map = ['1'=>'新报','2'=>'续保','3'=>'扩科'];
    	if(key_exists($key,$map)){
    		return $map[$key];
    	}
    	return '';
    }

	protected function get_data()
	{
		$model = new OrderPerformance;
        $fields = ['eid','sale_role_did','amount','oid','create_time'];
		$data = $model->order('create_time desc')->field($fields)->getSearchResult($this->params,[],false);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['eid'] = get_teacher_name($v['eid']);
			$data['list'][$k]['sale_role_did'] = get_did_value($v['sale_role_did']);
			$data['list'][$k]['create_time'] = date('Y-m-d',strtotime($v['create_time']));
			$data['list'][$k]['program'] = $this->get_order_item_program($v['oid']);
			$oinfo = $this->get_order_item_info($v['oid']);
			$data['list'][$k]['sid'] = get_student_name($oinfo['sid']);
			$data['list'][$k]['price'] = $oinfo['price'];
			$data['list'][$k]['consume_type'] = $this->convert_consume_type($oinfo['consume_type']);
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
	}
}