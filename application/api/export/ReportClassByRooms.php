<?php 

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportClassByRoom;
use app\api\model\Classroom;

class ReportClassByRooms extends Export
{
	protected $columns = [
	    ['field'=>'bid','title'=>'校区名称','width'=>20],
	    ['field'=>'cr_id','title'=>'教室名称','width'=>20],
	    ['field'=>'seat_nums','title'=>'容纳人数','width'=>20],
	    ['field'=>'numbers','title'=>'排课数量','width'=>20],
	];

	protected function get_title()
	{
		$title = '教室利用统计表';
		return $title;
	}

	protected function get_branch_names($cr_id)
	{
		$bid = Classroom::where('cr_id',$cr_id)->value('bid');
		return get_branch_name($bid);
	}

	protected function get_data()
	{
		$model = new ReportClassByRoom;
		$fields = ['cr_id'];
		$group = 'cr_id';
		
		$bids = $this->params['bid']; 
        $cids = ltrim($bids,'[In,');
        $cids = rtrim($cids,']');
        $x_bids = explode(',',$cids);
        $w_cr_ids = Classroom::where('bid','in',$x_bids)->column('cr_id');
        $w['cr_id'] = ['in',$w_cr_ids];
        
        $w_c = [];
		if(!empty($this->params['start_date'])){
			$w_c['int_day'] = ['between',[date('Ymd',strtotime($this->params['start_date'])),date('Ymd',strtotime($this->params['end_date']))]];
		}

		$data = $model->where($w)->field($fields)->group($group)->order('cr_id asc')->getSearchResult($this->params,[],false);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['cr_id'] = get_class_room($v['cr_id']);
			$data['list'][$k]['bid'] = $this->get_branch_names($v['cr_id']);
			$seat_nums = Classroom::where('cr_id',$v['cr_id'])->value('seat_nums');
			$data['list'][$k]['seat_nums'] = $seat_nums;
			$w_c['cr_id'] = $v['cr_id'];
			$arrange_nums = $model->where($w_c)->sum('arrange_nums');
			$data['list'][$k]['numbers'] = $arrange_nums;
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
		

	}
}