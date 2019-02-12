<?php

namespace app\api\controller;
use think\Request;
use app\api\model\Employee;
use app\api\model\ReportServiceBySystem;

class ReportServiceBySystems extends Base
{
	protected function get_sums($eid,$bid,$start,$end,$field)
	{
		$model = new ReportServiceBySystem;
		$w['int_day'] = ['between',[date('Ymd',strtotime($start)),date('Ymd',strtotime($end))]];
		$w['eid'] = $eid;
		$w['bid'] = $bid;
		$total = $model->where($w)->sum($field);
		return $total;
	}

	public function get_list(Request $request)
	{
        $input = $request->get();
        $model = new ReportServiceBySystem;
        $rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期' => 'require|date',
        ];

        // $validate = new Validate($rule);
        // $result   = $validate->check($input);
        // if(!$result){
        //     return $validate->getError();
        // }
        $rs = $this->validate($input,$rule);
        if($rs === false){
        	return $this->sendError(400,$rs);
        }

        $w = [];
        if(!empty($input['start_date'])){
        	$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
        }
        $group = 'eid';
        $bid = request()->header('x-bid');
        $sumFields = ReportServiceBySystem::getSumFields();
        $fields = ['eid'];
        $data = $model->where($w)->field($fields)->group($group)->order('eid asc')->getSearchResult($input);

        foreach ($data['list'] as $k => $v) {
        	foreach ($sumFields as $field) {
        		$data['list'][$k]['sum_'.$field] = $this->get_sums($v['eid'],$bid,$input['start_date'],$input['end_date'],$field);
                $data['list'][$k]['eid'] = get_teacher_name($v['eid']);
        	}
        }

        return $this->sendSuccess($data);

	}

    /**
     * 刷新統計
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function post(Request $request)
	{
        $model = new Employee;
        $input = $request->post();

        $rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期'   => 'require|date',
        ];
        $rs = $this->validate($input, $rule);
        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }
        $rid = 1;
        $w[] = ['exp',"find_in_set({$rid},rids)"];
        $bids = $request->header('x-bid');
        $w[] = ['exp',"find_in_set({$bids},bids)"];
        $w['og_id'] = gvar('og_id');
        $eids = $model->where($w)->column('eid');

        $wh['eid'] = ['not in',$eids];
        (new ReportServiceBySystem)->where($wh)->delete();
        
        $ret = ReportServiceBySystem::buildReport($eids,$input);

        if($ret === false){
        	return $this->sendError(400,$ret);
        }
        return $this->sendSuccess();

	}


}