<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/12/23
 * Time: 17:14
 */

namespace app\api\controller;

use app\api\model\Classes;
use app\api\model\ClassStudent;
use app\api\model\Employee;
use app\api\model\EmployeeStudent;
use app\api\model\Role;
use app\api\model\Student;
use app\api\model\StudentLesson;
use think\Request;

/**
 * 在读学员统计表
 * Class ReportStudents
 * @package app\api\controller
 */
class ReportStudents extends Base
{
    //处理where的开始、结束时间
    private function betweenDay($where = [])
    {
        $day = input('day');
        $day = trim($day, '[]');
        $day_arr = explode(',', $day);
        if(count($day_arr) != 3 || strtolower($day_arr[0]) != 'between') return $where;

        $start_day = format_int_day($day_arr[1]);
        $end_day = format_int_day($day_arr[2]);

        $where['start_int_day'] = ['between', [$start_day, $end_day]];
        $where['end_int_day'] = ['elt', $end_day];
        return $where;
    }

    //根据学校的在读学生统计
    public function school(Request $request)
    {
        $page = input('page/d', 0);
        $pagesize = input('pagesize/d', config('default_pagesize'));
        $get = $request->get();

        $model = new Student(); 
        $w = [];

        $group = 'school_id';
        $field = ['school_id', 'count(distinct sid)' => 'subtotal'];
        $list  = $model->field($field)->where($w)->group($group)->autoWhere($get)->page($page,$pagesize)->select();
        $total = $model->field($field)->where($w)->group($group)->autoWhere($get)->count();
        $data['list'] = $list;
        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['school_id'] = get_school_name($v['school_id']);
        }
        $data['total'] = $total;
        $data['page'] = $page;
        $data['pagesize'] = $pagesize;
        
        
        // $data = $model->getSearchResult();



        return $this->sendSuccess($data);

    }

    protected function get_student_nums($bid)
    {
        $model = new Student;
        $w['bid'] = $bid;
        $w['status'] = 1;
        return $model->where($w)->count();
    }

    //根据校区的在读学生统计
    public function branch(Request $request)
    {
        
        $input = $request->get();
        $fields = ['bid'];
        $group = 'bid';
        $w = [];
        $w['status'] = 1;
        if(!empty($input['bid'])){
            $w['bid'] = $input['bid'];
        }
        $model = new Student;
        $data = $model->where($w)->field($fields)->group($group)->order('bid asc')->getSearchResult(); 
        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['subtotal'] = $this->get_student_nums($v['bid']);
        }
        return $this->sendSuccess($data);
    }

    //根据课程的在读学生统计
    public function lesson(Request $request)
    {
        $page = input('page/d', 0);
        $pagesize = input('pagesize/d', config('default_pagesize'));
        $get = $request->get();

        $model = new StudentLesson(); //select lid, count(DISTINCT sid) from x360p_student_lesson where is_stop = 0 group by lid;
        $w = [];
        $w = $this->betweenDay($w);
        $group = 'lid';
        $field = ['lid', 'count(distinct sid)' => 'subtotal'];
        $list  = $model->field($field)->where($w)->group($group)->autoWhere($get)->page($page, $pagesize)->select();
        $total  = $model->field($field)->where($w)->group($group)->autoWhere($get)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $data['page'] = $page;
        $data['pagesize'] = $pagesize;
        return $this->sendSuccess($data);
    }

    //根据班级的在读学生统计
    public function class(Request $request)
    {
        $page = input('page/d', 0);
        $pagesize = input('pagesize/d', config('default_pagesize'));
        $get = $request->get();

        $model = new StudentLesson(); //select cid, count(DISTINCT sid) from x360p_student_lesson where is_stop = 0 group by lid;
        $w = [];
        $w = $this->betweenDay($w);
        $group = 'cid';
        $field = ['cid', 'count(distinct sid)' => 'subtotal'];
        $list  = $model->field($field)->where($w)->group($group)->autoWhere($get)->page($page, $pagesize)->select();
        $m_class = new Classes();
        foreach($list as &$row) {
            $row['one_class'] = $m_class->find($row['cid']);
        }
        $total  = $model->field($field)->where($w)->group($group)->autoWhere($get)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $data['page'] = $page;
        $data['pagesize'] = $pagesize;
        return $this->sendSuccess($data);
    }

    //根据老师的在读学生统计
    public function employee(Request $request)
    {
        $page = input('page/d', 0);
        $pagesize = input('pagesize/d', config('default_pagesize'));
        $get = $request->get();
        $day = input('day');
        $day_arr = explode(',', trim($day, '[]'));

        //--1-- 处理时间条件
        $class_where = [];
        $es_where = [];
        if(count($day_arr) == 3 || strtolower($day_arr[0]) == 'between') {
            $start_time = str_to_time($day_arr[1]);
            $end_time = str_to_time($day_arr[2], true);
            $class_where['start_lesson_time'] = ['egt', $start_time];
            $class_where['end_lesson_time'] = ['elt', $end_time];
            $es_where['create_time'] = ['between', [$start_time, $end_time]];
        }

        $employee_where = isset($get['eid']) ? $employee_where = 'e.eid='.$get['eid'] : '';

        //--2-- 先取得老师
        $m_employee = new Employee();
        $list = $m_employee->alias('e')->join('employee_role r', 'e.eid = r.eid')
            ->where('r.rid = 1')->where($employee_where)->page($page, $pagesize)->select();
        $total = $m_employee->alias('e')->join('employee_role r', 'e.eid = r.eid')
            ->where('r.rid = 1')->where($employee_where)->count();

        //--3-- 根据老师教的班级、或者带的一对一、一对多课程所有的学生，统计学生人数
        $m_es = new EmployeeStudent();
        $m_class = new Classes();
        $m_cs = new ClassStudent();
        foreach ($list as &$employee) {
            $eid = $employee['eid'];
            //--3.1-- 一对一、一对多学生
            $sids1 = $m_es->where($es_where)->where('eid', $eid)->column('sid');

            //--3.2-- 班级学生
            $cids = $m_class->where($class_where)->where('teach_eid', $eid)->column('cid');
            $sids2 = $m_cs->where('cid', 'in', $cids)->where('status', $m_cs::STATUS_NORMAL)->column('sid');

            //--3.3-- 总的学生人数
            $student_num = count(array_unique(array_merge($sids1, $sids2)));
            $employee['subtotal'] = $student_num;
        }

        $data['list'] = $list;
        $data['total'] = $total;
        $data['page'] = $page;
        $data['pagesize'] = $pagesize;
        return $this->sendSuccess($data);
    }

    //未分班的在读学生
    public function unassign(Request $request)
    {
        $page = input('page/d', 0);
        $pagesize = input('pagesize/d', config('default_pagesize'));
        $get = $request->get();

        $w = [];
        $w = $this->betweenDay($w);
        $w['ac_status'] = 0;
        $model = new StudentLesson();
        $data['list'] = $model->where($w)->autoWhere($get)->page($page, $pagesize)->select();
        $total = $model->where($w)->autoWhere($get)->count();

        foreach($data['list'] as &$row) {
            $row['student'] = Student::get($row['sid']);
        }
        $data['total'] = $total;
        $data['page'] = $page;
        $data['pagesize'] = $pagesize;
        return $this->sendSuccess($data);
    }


    /**
     * [student_summaries description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function student_summaries(Request $request)
    {
        $input = $request->param();

        if(!isset($input['start_date'])){
            $input['start_date'] = '1970-01-02';
            $input['end_date']   = '9999-12-31';
        }

        $start_ts = strtotime($input['start_date'].' 08:00:01');
        $end_ts   = strtotime($input['end_date'].' 23:59:59');

        $params['create_time'] = ['between',[$start_ts,$end_ts]];

        $bids = isset($input['bids']) ? explode(',',$input['bids']) : [];
        $params['bid'] = ['in',$bids];

        $mStudent = new Student;
        
        $field = 'bid';

        $data = $mStudent->where(['bid'=>$params['bid']])->field($field)->group($field)->skipBid()->getSearchResult($input);
        
        $data['total_student_nums'] = 0;
        $data['total_new_student_nums'] = 0;
        foreach ($data['list'] as &$item) {
            $item['name'] = get_branch_name($item['bid']);
            $w['bid'] = $item['bid'];
            $w['status'] = ['in',[1,30]];
            $w['create_time'] = ['gt',0];
            $item['student_nums'] = $mStudent->where($w)->count();

            $w['create_time'] = $params['create_time'];
            $item['new_student_nums'] = $mStudent->where($w)->count();

            $data['total_student_nums'] += $item['student_nums'];
            $data['total_new_student_nums'] += $item['new_student_nums'];
        }

        return $this->sendSuccess($data);

    }

    /**
     * 按课程统计学员
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function student_summaries_by_lesson(Request $request)
    {
        $input = $request->param();

        if(!isset($input['start_date'])){
            $input['start_date'] = '1970-01-02';
            $input['end_date']   = '9999-12-31';
        }

        $start_int_day = format_int_day($input['start_date']);
        $end_int_day = format_int_day($input['end_date']);

        $mStudentLesson = new StudentLesson;

        $bids = isset($input['bids']) ? explode(',',$input['bids']) : [];

        $group = 'lid';
        $field = 'lid';

        $w['bid'] = ['in',$bids];
        $w['lesson_status'] = ['in',[0,1]];
        $w['is_stop'] = 0;
        $w['lid'] = ['gt',0];

        $data = $mStudentLesson->where($w)->group($group)->field($field)->skipBid()->getSearchResult();
        $data['total_student_nums'] = 0;
        $data['total_new_student_nums'] = 0;
        foreach ($data['list'] as &$item) {
            $item['name'] = get_lesson_name($item['lid']);
            $w['lid'] = $item['lid'];
            $item['student_nums'] = $mStudentLesson->where($w)->count();
            $data['total_student_nums'] += $item['student_nums'];

            $item['new_student_nums'] = $mStudentLesson->where($w)->where('start_int_day','between',[$start_int_day,$end_int_day])->count();
            $data['total_new_student_nums'] += $item['new_student_nums'];
        }

        return $this->sendSuccess($data);

    }

    /**
     * 按班级统计学员
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function student_summaries_by_class(Request $request)
    {
        $input = $request->param();

        if(!isset($input['start_date'])){
            $input['start_date'] = '1970-01-02';
            $input['end_date']   = '9999-12-31';
        }

        $start_ts = strtotime($input['start_date'].' 00:00:00');
        $end_ts = strtotime($input['end_date'].' 23:59:59');

        $bids = isset($input['bids']) ? explode(',',$input['bids']) : [];

        $mClassStudent = new ClassStudent;

        $group = 'cid';
        $field = 'cid';

        $w['bid'] = ['in',$bids];
        $w['status'] = 1;

        $data = $mClassStudent->where($w)->group($group)->field($field)->skipBid()->getSearchResult();
        $data['total_student_nums'] = 0;
        $data['total_new_student_nums'] = 0;
        foreach ($data['list'] as &$item) {
            $item['name'] = get_class_name($item['cid']);
            $w['cid'] = $item['cid'];
            $item['student_nums'] = $mClassStudent->where($w)->count();
            $data['total_student_nums'] += $item['student_nums'];

            $item['new_student_nums'] = $mClassStudent->where($w)->where('in_time','between',[$start_ts,$end_ts])->count();
            $data['total_new_student_nums'] += $item['new_student_nums'];
        }

        return $this->sendSuccess($data);
    }


    /**
     * 根据老师统计学员
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function student_summaries_by_teacher(Request $request)
    {
        $input = $request->param();

        if(!isset($input['start_date'])){
            $input['start_date'] = '1970-01-02';
            $input['end_date']   = '9999-12-31';
        }

        $start_ts = strtotime($input['start_date'].' 00:00:00');
        $end_ts = strtotime($input['end_date'].' 23:59:59');

        $bids = isset($input['bids']) ? explode(',',$input['bids']) : [];

        $mEmpolyeeStudent = new EmployeeStudent;

        $group = 'eid';
        $field = 'eid';

        $w['bid'] = ['in',$bids];
        $w['rid'] = 1;

        $data = $mEmpolyeeStudent->where($w)->group($group)->field($field)->skipBid()->getSearchResult();
        $data['total_student_nums'] = 0;
        $data['total_new_student_nums'] = 0;
        foreach ($data['list'] as &$item) {
            $item['name'] = get_employee_name($item['eid']);
            $w['eid'] = $item['eid'];
            $item['student_nums'] = $mEmpolyeeStudent->where($w)->count();
            $data['total_student_nums'] += $item['student_nums'];

            $item['new_student_nums'] = $mEmpolyeeStudent->where($w)->where('create_time','between',[$start_ts,$end_ts])->count();
            $data['total_new_student_nums'] += $item['new_student_nums'];
        }

        return $this->sendSuccess($data);
    }

    
    /**
     * 按学校统计学员
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function student_summaries_by_school(Request $request)
    {
        $input = $request->param();

        if(!isset($input['start_date'])){
            $input['start_date'] = '1970-01-02';
            $input['end_date']   = '9999-12-31';
        }

        $start_ts = strtotime($input['start_date'].' 00:00:00');
        $end_ts = strtotime($input['end_date'].' 23:59:59');

        $bids = isset($input['bids']) ? explode(',',$input['bids']) : [];
        
        $mStudent = new Student;

        $group = 'school_id';
        $field = 'school_id';

        $w['bid'] = ['in',$bids];
        $w['in_time'] = ['gt',0];
        $w['status'] = ['in',[1,30]];

        $data = $mStudent->where($w)->group($group)->field($field)->skipBid()->getSearchResult();
        $data['total_student_nums'] = 0;
        $data['total_new_student_nums'] = 0;
        foreach ($data['list'] as &$item) {
            $item['name'] = get_school_name($item['school_id']);
            $w['school_id'] = $item['school_id'];
            $item['student_nums'] = $mStudent->where($w)->count();
            $data['total_student_nums'] += $item['student_nums'];

            $item['new_student_nums'] = $mStudent->where($w)->where('create_time','between',[$start_ts,$end_ts])->count();
            $data['total_new_student_nums'] += $item['new_student_nums'];
        }

        return $this->sendSuccess($data);
    }


    /**
     * 未分班学员
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function student_summaries_by_unassign(Request $request)
    {
        $input = $request->param();

        if(!isset($input['start_date'])){
            $input['start_date'] = '1970-01-02';
            $input['end_date']   = '9999-12-31';
        }

        $start_int_day = format_int_day($input['start_date']);
        $end_int_day = format_int_day($input['end_date']);

        $bids = isset($input['bids']) ? explode(',',$input['bids']) : [];

        $mStudentLesson = new StudentLesson;

        $field = 'sid,remain_lesson_hours,lid,remain_arrange_hours,remain_arrange_times,start_int_day,end_int_day';

        $w['bid'] = ['in',$bids];
        $w['start_int_day'] = ['between',[$start_int_day,$end_int_day]];
        $w['ac_status'] = 0;
        $w['lesson_type'] = 0;
        $w['lesson_status'] = ['in',['0','1']];
        $w['is_stop'] = 0;

        $data = $mStudentLesson->where($w)->field($field)->with('student')->skipBid()->getSearchResult();
        $data['total_student_nums'] = 0;
        foreach ($data['list'] as &$item) {
            unset($item['total_lesson_hours']);
            unset($item['expire_time_text']);
            if($item['end_int_day']){
                $item['remain_days'] = int_day_diff($item['start_int_day'],$item['end_int_day']);
            }
            $item['lesson_name'] = get_lesson_name($item['lid']);
            $data['total_student_nums'] += 1;
        }
        
        return $this->sendSuccess($data);
    }



}