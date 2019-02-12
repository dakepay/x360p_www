<?php 

namespace app\api\controller;

use think\Request;
use app\api\model\OrderCutAmount;
use app\api\model\StudentLessonHour;

class OrderCutAmounts extends Base{

    public function get_list(Request $request)
	{
		$get = $request->get();
		$with = [];
		if(isset($get['with'])){
	        $with[] = $get['with'];
	    }
		$model = new OrderCutAmount();

		$ret = $model->getSearchResult($get,$with,true);

		foreach ($ret['list'] as &$row) {
			$row['create_employee_name'] = get_teacher_name($row['create_uid']);
		}
	        
	    return $this->sendSuccess($ret);
	}


	/**
     * 扣款转化 课消
     * method post: api/order_cut_amount/1/doconsume  
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function do_consume(Request $request)
    {
        $oca_id = input('id/d');
        $oca_info = OrderCutAmount::get($oca_id);

        $slh_id = $oca_info->slh_id;
        if($slh_id){
            return $this->sendError(400,'已经扣款转化，请勿重复操作！');
        }

        $oca_info->toArray();
        $refund_data = [
            'lesson_amount' => $oca_info['amount'],
            'change_type'   => StudentLessonHour::CHANGE_TYPE_REFUND,
            'consume_type'  => 3,
            'int_day'       => date('Ymd',time()),
        ];
        array_copy($refund_data,$oca_info,['sid','og_id','bid']);

        $res = (new StudentLessonHour)->createOneRefund($refund_data);
        if(!$res){
        	return $this->sendSuccess(400,'扣款转化操作失败！');
        }
        
        $oca_info['slh_id'] = $res->slh_id;
        $oca_info->save();

        return $this->sendSuccess();

    }


}

