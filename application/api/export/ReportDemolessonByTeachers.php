<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportDemolessonByTeacher;

class ReportDemolessonByTeachers extends Export
{
    protected $columns = [
        ['field'=>'eid','title'=>'姓名','width'=>20],
        ['field'=>'lids','title'=>'课程数','width'=>20],
        ['field'=>'cids','title'=>'班级数','width'=>20],
        ['field'=>'sids','title'=>'体验人数','width'=>20],
        ['field'=>'transfered_sids','title'=>'转化人数','width'=>20],
        ['field'=>'rate','title'=>'转化率','width'=>20]
    ];

    protected function get_title()
    {
    	$title = '体验报名--老师';
    	return $title;
    }

    protected function get_rate($sids,$transfered_sids)
	{
		$rates = '';
		if($sids==0){
			return '0%';
		}else{
			$rates = round($transfered_sids/$sids*100,2).'%';
		}
		return $rates;
	}

    protected function get_data()
    {
    	$input = $this->params;
    	$model = new ReportDemolessonByTeacher;

		$w = [];
		if(!empty($input['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		}
		$fields = ['eid'];
		$group = 'eid';

		$data = $model->where($w)->field($fields)->group($group)->order('eid asc')->getSearchResult($input);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['eid'] = get_teacher_name($v['eid']);
			$data['list'][$k]['cids'] = count($model->where('eid',$v['eid'])->column('cid'));
			$data['list'][$k]['lids'] = count(array_unique($model->where('eid',$v['eid'])->column('lid')));
			$data['list'][$k]['sids'] = $model->where('eid',$v['eid'])->sum('sids');
			$data['list'][$k]['transfered_sids'] = $model->where('eid',$v['eid'])->sum('transfered_sids');
			$data['list'][$k]['rate'] = $this->get_rate($data['list'][$k]['sids'],$data['list'][$k]['transfered_sids']);
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
    }

}