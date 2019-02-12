<?php 

namespace app\api\export;

use app\common\Export;
use app\api\model\ReportDemolessonByLesson;

class ReportDemolessonByLessons extends Export
{
	protected $columns = [
        ['field'=>'lid','title'=>'课程名称','width'=>20],
        ['field'=>'cids','title'=>'班级数','width'=>20],
        ['field'=>'sids','title'=>'体验人数','width'=>20],
        ['field'=>'transfered_sids','title'=>'转化人数','width'=>20],
        ['field'=>'rate','title'=>'转化率','width'=>20],
	];

	protected function get_title()
	{
		$title = '体验报表--课程';
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
        $model = new ReportDemolessonByLesson;
		$w = [];
		if(!empty($input['start_date'])){
			$w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
		}

		$data = $model->where($w)->getSearchResult($input);
        foreach ($data['list'] as $k => $v) {
        	$data['list'][$k]['lid'] = get_lesson_name($v['lid']);
        	$data['list'][$k]['rate'] = $this->get_rate($v['sids'],$v['transfered_sids']);
        }

        if(!empty($data['list'])){
        	return collection($data['list'])->toArray();
        }
        return [];
	}
}