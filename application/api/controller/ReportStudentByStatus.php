<?php

namespace app\api\controller;

use think\Request;
use app\api\model\Student;

class ReportStudentByStatus extends Base
{
	public function get_list(Request $request)
	{
		$model = new Student;

        $input = $request->param();

        $w = [];

        $group = 'bid';

        $ret = $model->where($w)->group($group)->order('bid asc')->field('bid')->getSearchResult($input);

        foreach ($ret['list'] as &$row) {
            $row['status_1'] = $model->where(['bid'=>$row['bid'],'status'=>Student::STATUS_NORMAL])->count();
            $row['status_20'] = $model->where(['bid'=>$row['bid'],'status'=>Student::STATUS_STOP])->count();
            $row['status_30'] = $model->where(['bid'=>$row['bid'],'status'=>Student::STATUS_SUSPEND])->count();
            $row['status_50'] = $model->where(['bid'=>$row['bid'],'status'=>Student::STATUS_FINISH])->count();
            $row['status_90'] = $model->where(['bid'=>$row['bid'],'status'=>Student::STATUS_QUIT])->count();
            $row['status_100'] = $model->where(['bid'=>$row['bid'],'status'=>Student::STATUS_SEAL])->count();
            $row['total'] = $model->where('bid',$row['bid'])->count();
        }

		return $this->sendSuccess($ret);
	}

    protected function convert_status($key)
    {
        $map = [1=>'正常',20=>'停课',30=>'休学',50=>'结课',90=>'退学',100=>'封存'];
        if(key_exists($key,$map)){
            return $map[$key];
        }
        return '-';
    }


    public function get_detail(Request $request,$id = 0)
    {
        $bid = input('id/d');
        $input = $request->get();

        $w['bid'] = $bid;

        $model = new Student;

        $ret = $model->where($w)->field('sid,bid,student_name,first_tel,status')->getSearchResult($input);
        foreach ($ret['list'] as &$row) {
            $row['bid'] = get_branch_name($row['bid']);
            $row['status'] = $this->convert_status($row['status']);
        }

        $ret['columns'] = Student::$detail_fields;

        return $this->sendSuccess($ret);
    }




}