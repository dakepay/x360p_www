<?php 

namespace app\api\export;

use app\common\Export;
use app\api\model\OrderItem;

class OrderItems extends Export
{
	protected $columns = [
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'sid','title'=>'学员','width'=>20],
        ['field'=>'item_name','title'=>'项目','width'=>20],
        ['field'=>'consume_type','title'=>'类型','width'=>20],
        ['field'=>'order_no','title'=>'订单号','width'=>20],
        ['field'=>'create_time','title'=>'下单时间','width'=>20],
        ['field'=>'expire_time','title'=>'有效期','width'=>20],
        ['field'=>'nums','title'=>'数量','width'=>20],
        ['field'=>'origin_price','title'=>'原单价','width'=>20],
        ['field'=>'price','title'=>'折扣单价','width'=>20],
        ['field'=>'reduced_amount','title'=>'优惠','width'=>20],
        ['field'=>'present_lesson_hours','title'=>'赠送','width'=>20],
        ['field'=>'subtotal','title'=>'小计金额','width'=>20],
        ['field'=>'paid_amount','title'=>'实缴金额','width'=>20],
        ['field'=>'referer_sid','title'=>'转介绍学员','width'=>20],
        ['field'=>'referer_teacher_id','title'=>'转介绍老师','width'=>20],
        ['field'=>'referer_eid','title'=>'转介绍学管师','width'=>20],

	];

	protected function get_title()
	{
		$title = '报名项目';
		return $title;
	}

	protected function convert_consume_type($value)
	{
		$map = [1=>'新报',2=>'续报',3=>'扩科'];
		if(key_exists($value,$map)){
			return $map[$value];
		}
		return '-';
	}


	protected function get_data()
	{
		$input = $this->params;
        $pagenation = $this->pagenation;
		$mOrderItem = new OrderItem;
        $ret = $mOrderItem->getSearchResult($input,$pagenation);

        foreach ($ret['list'] as &$item) {
        	$item['sid'] = get_student_name($item['sid']);
        	$item['bid'] = get_branch_name($item['bid']);
        	$item['order_no'] = ' '.$item['join_order']['order_no'];
        	$item['consume_type'] = $this->convert_consume_type($item['consume_type']);
            $item['referer_sid'] = get_student_name($item['referer_sid']);
            $item['referer_teacher_id'] = get_employee_name($item['referer_teacher_id']);
            $item['referer_eid'] = get_employee_name($item['referer_eid']);
        }
        if($pagenation){
            return $ret;
        }

        if(!empty($ret['list'])){
        	return collection($ret['list'])->toArray();
        }
        return [];
	}

}