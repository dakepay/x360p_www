<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\FranchiseeServiceRecord;

class FranchiseeServiceRecords extends Export
{
	protected $columns = [
        ['title'=>'加盟商名称','field'=>'org_name','width'=>20],
        ['title'=>'服务类型','field'=>'fc_service_did','width'=>20],
        ['title'=>'完成时间','field'=>'finish_time','width'=>20],
        ['title'=>'员工','field'=>'eid','width'=>20],
        ['title'=>'服务内容','field'=>'content','width'=>100],
	];

	protected function get_title()
	{
		$title = '服务记录表';
		return $title;
	}

	protected function get_data()
	{
		$input = $this->params;
		$model = new FranchiseeServiceRecord;

		$w = [];
		$ret = $model->where($w)->getSearchResult($input,[],false);
		foreach ($ret['list'] as &$row) {
			$row['org_name'] = get_franchisee_name($row['fc_id']);
			$row['fc_service_did'] = get_did_value($row['fc_service_did']);
			$row['eid'] = get_teacher_name($row['eid']);
			$row['finish_time'] = $row['int_day'].' '.$row['int_hour'];
		}

		if(!empty($ret['list'])){
			return collection($ret['list'])->toArray();
		}

		return [];
	}
}