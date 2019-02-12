<?php 

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportClassByTeacher;

class ReportClassByTeachers extends Export
{
	protected $columns = [
        ['field'=>'teach_eid','title'=>'姓名','width'=>20],
        ['field'=>'class_nums','title'=>'带班数量','width'=>20],
        ['field'=>'sum_total_arrange_nums','title'=>'总排课数','width'=>20],
        ['field'=>'sum_on_arrange_nums','title'=>'已上课数','width'=>20],
	];

	protected function get_title()
	{
		$title = '带班统计表';
		return $title;
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

	protected function get_data()
	{
		$model = new ReportClassByTeacher;

		$w = [];
		if(!empty($this->params['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($this->params['start_date'])),date('Ymd',strtotime($this->params['end_date']))]];
		}

		$w['og_id'] = gvar('og_id');
    	$fields = ReportClassByTeacher::getSumFields();
    	array_unshift($fields,'teach_eid');
    	$group = 'teach_eid';

    	$data = $model->where($w)->group($group)->field($fields)->order('teach_eid asc')->getSearchResult($this->params,[],false);

    	foreach ($data['list'] as $k => $v) {
            $cids = $this->get_cids($v['teach_eid'],$this->params['start_date'],$this->params['end_date']);
    		if(empty($cids)){
    			unset($data['list'][$k]);
    		}else{
                $data['list'][$k]['cids'] = explode(',',$cids);
                $data['list'][$k]['class_nums'] = count($data['list'][$k]['cids']);
    		}
    		$data['list'][$k]['teach_eid'] = get_teacher_name($v['teach_eid']);
    	}

    	if(!empty($data['list'])){
    		return collection($data['list'])->toArray();
    	}

    	return [];
	}
}