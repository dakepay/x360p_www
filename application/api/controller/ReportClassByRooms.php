<?php 

namespace app\api\controller;

use think\Request;
use app\api\model\ReportClassByRoom;
use app\api\model\CourseArrange;
use app\api\model\Classroom;

class ReportClassByRooms extends Base
{


	public function get_list(Request $request)
	{
		$model = new ReportClassByRoom;
		$input = $request->get();

		$rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期'   => 'require|date',
        ];
        $ret = $this->validate($input, $rule);
        if ($ret === false) {
            return $this->sendError(400, $rs);
        }

        $w = [];
        if(!empty($input['start_date'])){
            $w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
        }

        $w['og_id'] = gvar('og_id');
        
        $fields = ReportClassByRoom::getSumFields();
        array_unshift($fields,'cr_id');

        $x_bids = request()->header('x-bid');
        $w_cr_ids = Classroom::where('bid','in',$x_bids)->column('cr_id');
        $w['cr_id'] = ['in',$w_cr_ids];

        $data = $model->where($w)->group('cr_id')->field($fields)->order('cr_id asc')->getSearchResult($input);
        foreach ($data['list'] as $k => $v) {
        	$crinfo = get_classroom_info($v['cr_id']);
        	$data['list'][$k]['bid'] = $crinfo['bid'];
        	$data['list'][$k]['seat_nums'] = $crinfo['seat_nums'];
        	
            $w_c['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
            $w_c['cr_id'] = $v['cr_id'];
        	$course_arrange = CourseArrange::where($w_c)->column('ca_id');
        	$data['list'][$k]['course_arrange'] = $course_arrange;

        	if($data['list'][$k]['sum_arrange_nums']==0){
        		unset($data['list'][$k]);
        	}

        }


        return $this->sendSuccess($data);

	}


	public function post(Request $request)
	{
		$input = $request->post();
		$rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期'   => 'require|date',
		];
		$rs = $this->validate($input,$rule);
		if($rs ===false){
			return $this->sendError(400,$rs);
		}

		$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
        $w['og_id'] = gvar('og_id');
        $bids = $request->header('x-bid');
        $bids = explode(',',$bids);
        $w['bid'] = ['in',$bids];
		$cr_ids = CourseArrange::where($w)->column('cr_id');
		$input['cr_id'] = array_unique($cr_ids);
		foreach ($input['cr_id'] as $key=>$value)
		{
		    if ($value === 0)
		    unset($input['cr_id'][$key]);
		}

	    $ret = ReportClassByRoom::buildReport($input);
	    if($ret === false){
	    	return $this->sendError(400,$ret);
	    }

	    return $this->sendSuccess();

	}




}