<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\StudentReturnVisit;

class visits extends Export
{

	protected $columns = [
        ['field'=>'sid','title'=>'姓名','width'=>20],
        ['field'=>'is_connect','title'=>'是否有效','width'=>20],
        ['field'=>'followup_did','title'=>'回访方式','width'=>20],
        ['field'=>'content','title'=>'回访内容','width'=>40],
        ['field'=>'int_day','title'=>'回访日期','width'=>20],
        ['field'=>'create_time','title'=>'添加时间','width'=>20],
	]; 


	protected function get_title()
	{
		$title = '学员回访';
		return $title;
	}

	protected function convert_connect($key)
	{
		$map = [0=>'无效',1=>'有效'];
		if(key_exists($key,$map)){
            return $map[$key];
		}
		return '-';
	}

	protected function get_data()
	{
		$model = new StudentReturnVisit;
		$input = $this->params;
		$ret = $model->getSearchResult($input,[],false);

		foreach ($ret['list'] as &$row) {
			$row['sid'] = get_student_name($row['sid']);
			$row['is_connect'] = $this->convert_connect($row['is_connect']);
			$row['followup_did'] = get_did_value($row['followup_did']);
			$row['int_day'] = date('Y-m-d',strtotime($row['int_day']));
			$row['create_time'] = date('Y-m-d',strtotime($row['create_time']));
		}

		if(!empty($ret['list'])){
			return collection($ret['list'])->toArray();
		}
		return [];
	}





}