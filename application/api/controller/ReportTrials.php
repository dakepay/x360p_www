<?php

namespace app\api\controller;
use think\Request;
use app\api\model\ReportTrial;
use app\api\model\TrialListenArrange;

class ReportTrials extends Base
{
     

    public function get_list(Request $request)
    {
    	$model = new ReportTrial;
    	$input = $request->get();
    	$w = [];
    	if(!empty($input['start_date'])){
    		$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
    	}
    	if(!empty($input['student_name'])){
    		$w['student_name'] = ['like','%'.$input['student_name'].'%'];
    	}

    	$data = $model->where($w)->getSearchResult($input);
    	foreach ($data['list'] as $k => $v) {
    		$data['list'][$k]['eid'] = get_teacher_name($v['eid']);
    		if($v['sign_time']){
    			$data['list'][$k]['sign_time'] = date('Y-m-d',$v['sign_time']);
    		}else{
    			$data['list'][$k]['sign_time'] = '-';
    		}
    	}
        // if data is empty
        if(empty($data['list'])){
            unset($w['student_name']);
            $m_tla = new TrialListenArrange;
            $w['og_id'] = gvar('og_id');
            $tla_ids = $m_tla->where($w)->column('tla_id');
            $ret = ReportTrial::buildReport($tla_ids);
            if($ret === false){
                return $this->sendError(400,$ret);
            }
        }

    	return $this->sendSuccess($data);
    }
    

    public function post(Request $request)
    {
    	$model = new TrialListenArrange;
    	$input = $request->post();
    	$w = [];
    	if(!empty($input['start_date'])){
    		$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
    	}

        //生成报表前，清空数据
        $m_t = new ReportTrial;
        $ids = $m_t->where($w)->column('id');
        if(!empty($ids)){
            foreach ($ids as $id) {
                $m_t->deleteTrial($id);
            }
        }

    	$w['og_id'] = gvar('og_id');
        $w['is_attendance'] = 1;
        $w['attendance_status'] = 1;
    	$tla_ids = $model->where($w)->column('tla_id');

    	$ret = ReportTrial::buildReport($tla_ids);
    	if($ret === false){
    		return $this->sendError(400,$ret);
    	}

    	return $this->sendSuccess();
    }


}