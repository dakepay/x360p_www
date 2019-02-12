<?php

namespace app\api\model;

class ReportTrial extends Base
{   
	
    public static function buildReport($tla_ids)
    {
    	// print_r($tla_ids);exit;
    	foreach ($tla_ids as $tla_id) {
 
            $tla_info = get_trial_listen_arrange_info($tla_id);
            $data = [];
            $data['teach_eid'] = $tla_info['eid'];
            array_copy($data,$tla_info,['tla_id','og_id','bid','int_day']);

            if($tla_info['is_student']==0){
            	$cinfo = get_customer_info($tla_info['cu_id']);
            	$data['student_name'] = $cinfo['name'];
                $data['student_type'] = 0;

                if($cinfo['sid']){
                    $order_data = model('order')->where(['is_debit'=>0,'sid'=>$cinfo['sid']])->find();
                }

            	if($cinfo['signup_amount'] > 0 && !empty($order_data)){
            		$data['status'] = 1;
            		$data['lid'] = StudentLesson::where('sid',$cinfo['sid'])->value('lid');
            		$oid = Order::where('sid',$cinfo['sid'])->value('oid');
            		$oinfo = get_order_info($oid);
            		$data['sign_time'] = $oinfo['create_time'];
            		$data['sign_amount'] = $oinfo['order_amount'];
            		$data['receive_amount'] = $oinfo['paid_amount'];
            		$data['eid'] = OrderPerformance::where('oid',$oid)->value('eid');
            	}
            }elseif($tla_info['is_student'] == 1){
            	$sinfo = get_student_info($tla_info['sid']);
            	$data['student_name'] = $sinfo['student_name'];
            	$data['student_type'] = 1;
            	$data['lid'] = StudentLesson::where('sid',$tla_info['sid'])->value('lid');
            	if($data['lid']){
            		$data['status'] = 1;
            		$oid = Order::where('sid',$tla_info['sid'])->value('oid');
	                $oinfo = get_order_info($oid);
	                $data['sign_time'] = $oinfo['create_time'];
	        		$data['sign_amount'] = $oinfo['order_amount'];
	        		$data['receive_amount'] = $oinfo['paid_amount'];
	        		$data['eid'] = OrderPerformance::where('oid',$oid)->value('eid');
            	}
            }
            
            $model = new ReportTrial;
            $w_ex['tla_id'] = $tla_id;
            $exist_data = $model->where($w_ex)->find();
            if($exist_data){
                $where['id'] = $exist_data['id'];
                $model->save($data,$where);
            }else{
                $model->isUpdate(false)->save($data);
            }
    		
    	}
    }

    public function deleteTrial($id)
    {
        $res = (new ReportTrial)->where('id',$id)->delete();
        return $this;
    }


}