<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\Student;

class ReportStudentByStatus extends Export
{

	protected $columns = [
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'status_1','title'=>'正常学员','width'=>20],
        ['field'=>'status_20','title'=>'停课学员','width'=>20],
        ['field'=>'status_30','title'=>'休学学员','width'=>20],
        ['field'=>'status_50','title'=>'结课学员','width'=>20],
        ['field'=>'status_90','title'=>'退学学员','width'=>20],
        ['field'=>'status_100','title'=>'封存学员','width'=>20],
        ['field'=>'total','title'=>'总学员','width'=>20],
	];


	protected function get_title()
	{
		$title = '学员状态分析表';
		return $title;
	}

	protected function get_data()
	{
		$model = new Student;

        $input = $this->params;

        $group = 'bid';

        $ret = $model->group($group)->order('bid asc')->field('bid')->getSearchResult($input);

        foreach ($ret['list'] as $k => $v) {
    	    $ret['list'][$k]['bid'] = get_branch_name($v['bid']);
            $ret['list'][$k]['status_1'] = $model->where(['bid'=>$v['bid'],'status'=>Student::STATUS_NORMAL])->count();
            $ret['list'][$k]['status_20'] = $model->where(['bid'=>$v['bid'],'status'=>Student::STATUS_STOP])->count();
            $ret['list'][$k]['status_30'] = $model->where(['bid'=>$v['bid'],'status'=>Student::STATUS_SUSPEND])->count();
            $ret['list'][$k]['status_50'] = $model->where(['bid'=>$v['bid'],'status'=>Student::STATUS_FINISH])->count();
            $ret['list'][$k]['status_90'] = $model->where(['bid'=>$v['bid'],'status'=>Student::STATUS_QUIT])->count();
            $ret['list'][$k]['status_100'] = $model->where(['bid'=>$v['bid'],'status'=>Student::STATUS_SEAL])->count();
            $ret['list'][$k]['total'] = $model->where('bid',$v['bid'])->count();
        }

        if(!empty($ret['list'])){
        	return collection($ret['list'])->toArray();
        }

        return [];
	}


}