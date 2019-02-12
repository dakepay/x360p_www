<?php 

namespace app\api\controller;

use think\Request;
use app\api\model\Employee;
use app\api\model\Classes;
use app\api\model\Student;
use app\api\model\ClassStudent;
use app\api\model\CourseArrange;
use app\api\model\CourseArrangeStudent;
use app\api\model\ReportStudentByTeacher;


class ReportStudentByTeachers extends Base
{
	public function get_list(Request $request)
	{
		$model = new ReportStudentByTeacher;
		$input = $request->get();
        $w['is_on_job'] = 1;
        $w['bids'] = $request->header('x-bid');

		$data = $model->order('teach_eid asc')->where($w)->getSearchResult($input);

		return $this->sendSuccess($data);
	}

    protected function convert_class_type($key)
    {
        $map = [0=>'标准班级',1=>'临时班级',2=>'活动班级'];
        if(key_exists($key,$map)){
            return $map[$key];
        }
        return '-';
    }

    protected function convert_status($key)
    {
        $map = [1=>'正常',20=>'停课',30=>'休学',90=>'退学',100=>'封存'];
        if(key_exists($key,$map)){
            return $map[$key];
        }
        return '-';
    }

    protected function convert_student_status($key)
    {
        $map = [0=>'停课',1=>'正常',2=>'转出',9=>'结课'];
        if(key_exists($key,$map)){
            return $map[$key];
        }
        return '-';
    }

    protected function convert_attendance($key)
    {
        $map = [0=>'未考勤',1=>'部分考勤',2=>'全部考勤'];
        if(key_exists($key,$map)){
            return $map[$key];
        }
        return '-';
    }

    public function get_detail(Request $request,$id=0)
    {
        $eid = input('id/d');
        $input = $request->get();

        $m_classes = new Classes;
        $m_student = new Student;
        $m_cs = new ClassStudent;
        $m_ca = new CourseArrange;
        $m_cas = new CourseArrangeStudent;

        switch ($input['type']) {
            case 'class_nums':
                $w['teach_eid'] = $eid;
                $w['status'] = ['in',['0','1']];
                $ret = $m_classes->where($w)->getSearchResult($input);
                foreach ($ret['list'] as &$row) {
                    $row['bid'] = get_branch_name($row['bid']);
                    $row['teach_eid'] = get_teacher_name($row['teach_eid']);
                    $row['class_type'] = $this->convert_class_type($row['class_type']);
                }
                $ret['columns'] = Classes::$detail_fields;
                break;
            case 'class_student_nums':
                $w['teach_eid'] = $eid;
                $w['status'] = ['in',['0','1']];
                $w['bid'] = $request->header('x-bid');
                
                $cids = $m_classes->where($w)->column('cid');
                $w_cs['cid'] = ['in',$cids];
                $w_cs['status'] = 1;

                $ret = $m_cs->where($w_cs)->getSearchResult($input);
                foreach ($ret['list'] as &$row) {
                    $sinfo = get_student_info($row['sid']);
                    $row['student_name'] = $sinfo['student_name'];
                    $row['bid'] = get_branch_name($row['bid']);
                    $row['first_tel'] = $sinfo['first_tel'];
                    $row['cid'] = get_class_name($row['cid']);
                    $row['status'] = $this->convert_status($row['status']);
                }
                $ret['columns'] = ClassStudent::$detail_fields;
                break;
            case 'onetoone_student_nums':
                $w['teach_eid'] = $eid;
                $w['lesson_type'] = 1;
                $w['is_trial'] = 0;
                $w['is_cancel'] = 0;
                $w['bid'] = $request->header('x-bid');

                $ca_ids = $m_ca->where($w)->column('ca_id');

                $w_cas['ca_id'] = ['in',$ca_ids];

                $sids = $m_cas->where($w_cas)->column('sid');
                $w_s['sid'] = ['in',$sids];

                $ret = $m_student->where($w_s)->getSearchResult($input);

                foreach ($ret['list'] as &$row) {
                    $row['bid'] = get_branch_name($row['bid']);
                    $row['status'] = $this->convert_student_status($row['status']);
                }
                $ret['columns'] = Student::$detail_fields;
                break;
            case 'onetomore_student_nums':
                $w['teach_eid'] = $eid;
                $w['lesson_type'] = 2;
                $w['is_trial'] = 0;
                $w['is_cancel'] = 0;
                $w['bid'] = $request->header('x-bid');

                $ca_ids = $m_ca->where($w)->column('ca_id');
                $w_cas['ca_id'] = ['in',$ca_ids];

                $sids = $m_cas->where($w_cas)->column('sid');

                $w_s['sid'] = ['in',$sids];
                $ret = $m_student->where($w_s)->getSearchResult($input);

                foreach ($ret['list'] as &$row) {
                    $row['bid'] = get_branch_name($row['bid']);
                    $row['status'] = $this->convert_student_status($row['status']);
                }
                $ret['columns'] = Student::$detail_fields;
                break;
            
            default:
                # code...
                break;
        }


        return $this->sendSuccess($ret);



    }


	public function post(Request $request)
	{
        $model = new Employee;

        $rid = 1;
        $w[] = ['exp', "find_in_set({$rid},rids)"];
        $w['og_id'] = gvar('og_id');
        // $bid = $request->header('x-bid');
        // $w[] = ['exp',"find_in_set({$bid},bids)"];
        $eids = $model->where($w)->column('eid');

        // delete data
        $m_rsbt = new ReportStudentByTeacher;
        $data = $m_rsbt->select();
        foreach ($data as $teacher) {
            if(!empty($teacher)){
                $m_rsbt->deleteOldData($teacher);
            }
        }

        $ret = ReportStudentByTeacher::buildReport($eids);

        if ($ret === false) {
            return $this->sendError(400, $ret);
        }
        
        return $this->sendSuccess();


	}
}