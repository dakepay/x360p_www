<?php

namespace app\api\controller;

use think\Request;
use app\api\model\CourseArrange;
use app\api\model\ReportClassByTeacher;


class ReportClassByTeachers extends Base
{

    protected function get_total_arrange_nums($eid,$start,$end)
    {
    	$model = new ReportClassByTeacher;
        $w['teach_eid'] = $eid;
        $w['int_day'] = ['between',[date('Ymd',strtotime($start)),date('Ymd',strtotime($end))]];
        return $model->where($w)->sum('total_arrange_nums');
    }

    protected function get_cids($eid,$start,$end)
    {
    	$model = new ReportClassByTeacher;
    	$w['int_day'] = ['between',[date('Ymd',strtotime($start)),date('Ymd',strtotime($end))]];
    	$w['teach_eid'] = $eid;
    	$w['total_arrange_nums'] = ['gt','0'];
    	$cids = $model->where($w)->column('cid');
    	$cids = array_unique($cids);
    	return implode(',',$cids);
    }

    protected function get_total_arranges($eid,$start,$end)
    {
    	$model = new ReportClassByTeacher;
    	$w['int_day'] = ['between',[date('Ymd',strtotime($start)),date('Ymd',strtotime($end))]];
    	$w['teach_eid'] = $eid;
    	$w['total_arrange_nums'] = ['gt','0'];
    	$ca_ids = $model->where($w)->column('ca_ids');
    	return implode(',',$ca_ids);
    }

    protected function get_on_arranges($eid,$start,$end)
    {
    	$model = new ReportClassByTeacher;
    	$w['int_day'] = ['between',[date('Ymd',strtotime($start)),date('Ymd',strtotime($end))]];
    	$w['teach_eid'] = $eid;
    	$w['total_arrange_nums'] = ['gt','0'];
    	$w['on_arrange_nums'] = ['gt','0'];
    	$on_ca_ids = $model->where($w)->column('on_ca_ids');
    	return implode(',',$on_ca_ids);
    }

    public function get_list(Request $request)
    {
    	$model = new ReportClassByTeacher;
    	$input = $request->get();

        $rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期' => 'require|date',
        ];
        $ret = $this->validate($input,$rule);
        if($ret !== true){
            return $this->sendError(400,$ret);
        }

    	$w = [];

    	if(!empty($input['start_date'])){	
    		$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
    	}

    	$fields = ReportClassByTeacher::getSumFields();
    	array_unshift($fields,'teach_eid');
    	$group = 'teach_eid';
    	
    	$data = $model->where($w)->group($group)->field($fields)->order('teach_eid asc')->getSearchResult($input);

    	foreach ($data['list'] as $k => $v) {
            // 排课详情、已上课详情，暂时注释
    		// $total_arranges = $this->get_total_arranges($v['teach_eid'],$input['start_date'],$input['end_date']);
    		// $data['list'][$k]['total_arranges'] = explode(',',$total_arranges);
    		// $on_arranges = $this->get_on_arranges($v['teach_eid'],$input['start_date'],$input['end_date']);
    		// $data['list'][$k]['on_arranges'] = explode(',',$on_arranges);
            
            $cids = $this->get_cids($v['teach_eid'],$input['start_date'],$input['end_date']);
    		if(empty($cids)){
    			unset($data['list'][$k]);
    		}else{
                $data['list'][$k]['cids'] = explode(',',$cids);
    		}
    		
    	}
    	return $this->sendSuccess($data);

    }


	public function post(Request $request)
	{
		$model = new CourseArrange;
		$input = $request->post();
		$rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期' => 'require|date',
		];
		unset($input['lid']);
		unset($input['cid']);
		$ret = $this->validate($input,$rule);
		if($ret === false){
			return $this->sendError(400,$ret);
		}
		$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		$w['lesson_type'] = 0;
        $w['og_id'] = gvar('og_id');
		$eids = $model->where($w)->column('teach_eid');
		$input['eids'] = array_unique($eids);
		// print_r($eids);exit;
		$res = ReportClassByTeacher::buildReport($input);
		if($res === false){
			return $this->sendError(400,$res);
		}
		return $this->sendSuccess();

	}
}