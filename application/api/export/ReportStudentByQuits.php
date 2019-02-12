<?php

namespace app\api\export;
use app\common\Export;
use app\api\model\ReportStudentByQuit;
use app\api\model\Student;

class ReportStudentByQuits extends Export
{
	protected $columns = [
        ['field'=>'bid','title'=>'校区名称','width'=>20],
        ['field'=>'eid','title'=>'学管师','width'=>20],
        ['field'=>'sid','title'=>'姓名','width'=>20],
        ['field'=>'first_tel','title'=>'电话','width'=>20],
        ['field'=>'quit_time','title'=>'流失时间','width'=>20],
        ['field'=>'quit_reason','title'=>'流失原因','width'=>20],
	];

	protected function get_title()
	{
		$title = '学员流失表';
		return $title;
	}

    protected function get_teach_name($sid)
    {
    	$eid =  Student::where('sid',$sid)->value('eid');
    	return get_teacher_name($eid);
    }

    protected function get_first_tel($sid)
    {
    	return Student::where('sid',$sid)->value('first_tel');
    }

	protected function get_data()
	{
		$model = new ReportStudentByQuit;

		$w = [];
		if(!empty($this->params['cid'])){
			$w[] = ['exp',"find_in_set({$this->params['cid']},cids)"];
		}
		if(!empty($this->params['lid'])){
			$w[] = ['exp',"find_in_set({$this->params['lid']},lids)"];
		}
		if(!empty($this->params['start_date'])){
			$w['quit_time'] = ['between',[strtotime($this->params['start_date']),strtotime($this->params['end_date'])]];
		}

		$data = $model->where($w)->order('quit_time desc')->getSearchResult($this->params,[],false);

		foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['bid'] = get_branch_name($v['bid']);
			$data['list'][$k]['eid'] = $this->get_teach_name($v['sid']);
			$data['list'][$k]['sid'] = get_student_name($v['sid']);
			$data['list'][$k]['first_tel'] = $this->get_first_tel($v['sid']);
			$data['list'][$k]['quit_reason'] = date('Y-m-d',$v['quit_time']);
			$data['list'][$k]['quit_reason'] = get_did_value($v['quit_reason']);
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
	}
}