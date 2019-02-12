<?php
/**
 * luo 20171006
 */
namespace app\api\controller;

use app\api\model\ClassStudent;
use app\api\model\CourseArrange;
use app\api\model\Employee;
use app\api\model\EmployeeStudent;
use app\api\model\EmployeeTimeSection;
use app\api\model\Lesson;
use app\api\model\Student;
use app\api\model\User;
use app\api\model\StudentLesson;
use app\common\db\Query;
use DateInterval;
use DatePeriod;
use DateTime;
use think\Request;
use app\api\model\Employee as EmployeeModel;
use app\api\model\Classes as ClassesModel;

/**
 * Class Emplyees
 * @title 员工管理接口
 * @url employees
 * @desc  员工的添加、编辑、删除
 * @version 1.0
 * @readme
 */
class Employees extends Base
{
    /**
     * @title 获取员工信息
     * @desc  根据条件获取单个员工信息
     * @url employees
     * @method GET
     */
    protected function get_detail(Request $request, $id=0){
        $id = $request->param('id');
        $employee = EmployeeModel::get($id, ['profile']);
        if (!$employee) {
            return $this->sendError(400, '该员工不存在或已被删除');
        }
        $ret = $employee->toArray();
        return $this->sendSuccess($ret);
    }

	/**
    * @desc  根据条件获取员工列表
    * @url employees
    * @method GET
    */
	protected function get_list(Request $request)
    { 
        $model = m('employee');
        $input = $request->get();
        if(isset($input['com_id'])){
            $bids = get_bids_by_dpt_id($input['com_id']);
            $where['bid'] = ['IN',$bids];
            unset($input['com_id']);
        }else {
            $x_bid = !empty($input['bid']) ? $input['bid'] : $request->header('x-bid');
            $x_bid = explode(',', $x_bid)[0];
            $where = [];
            if ($x_bid) {
                $where[] = ['exp', "find_in_set({$x_bid},bids)"];
                $input['bid'] = -1;
            }
        }
        if(isset($input['rids']) && !empty($input['rids'])) {
            $rid = intval($input['rids']);
            $where[] = ['exp', "find_in_set({$rid},rids)"];
            unset($input['rids']);
        }


        $condition = [];
        if (!empty($input['ename'])) {
            $name = $input['ename'];
            unset($input['ename']);

            $where['ename'] = ['like','%'.$name.'%'];
            if (preg_match("/^[a-z]*$/i", $name)) {/*全英文*/
                //$condition['pinyin'] = ['like','%'.$name.'%'];
                $condition['pinyin_abbr'] = $name;
            }



        }
        $result = $model->scope('bids')->where($where)->skipBid()->whereOr($condition)->with(['user','profile', 'subjects','departments'])->getSearchResult($input);
        foreach($result['list'] as $key => &$employee) {
            $employee['sj_ids'] = [];
            if(!empty($employee['subjects'])) {

                $employee['sj_ids'] = array_column($employee['subjects'], 'sj_id');
            }
            if(!empty($employee['departments'])) {
                $employee['departments'] = array_map(function($dept){
                    return array_merge($dept, $dept['pivot']);
                }, $employee['departments']);
            }

            if(!empty($employee['user'])) {
                /*在employee_list中排除admin员工*/
                //if (isset($employee['user']['is_admin']) && $employee['user']['is_admin'] === 1) {
                   // unset($result['list'][$key]);
                //} else {
                    unset($employee['user']['password']);
                //}
            }
        }
        $result['list'] = array_values($result['list']);
        return $this->sendSuccess($result);
	}

	/**
	 * @desc  创建一个员工
	 * @url employees
	 * @method  POST
	 */
	public function post(Request $request)
    {
        $input = $request->post();
        if (empty($input['employee'])) {
            return $this->sendError(400, '缺少参数[employee]或参数不合法');
        }

        $m_employee = new EmployeeModel();
        $openAccount = false;
        if (isset($input['open_account']) && $input['open_account'] == 1) {
            $openAccount = true;
        }

        $result =$m_employee->createEmployee($input, $openAccount);
        if(!$result){
            return $this->sendError(400, $m_employee->getError());
        }

        return $this->sendSuccess();
	}

	/**
	 * @desc  编辑一个员工的基本信息
	 * @url employees/:id
	 * @method  PUT
	 */
	public function put(Request $request)
    {
        /*前端发送过来的请求体json结构和post操作一样*/
        $id = $request->param('id');
        $input = $request->put();
        $m_employee = new EmployeeModel();
        $result = $m_employee->editEmployee($id, $input['employee']);

        if(!$result){
            return $this->sendError(400, $m_employee->getError());
        }
        return $this->sendSuccess('ok');
	}

	/**
	 * @desc  根据员工ID删除一个员工
	 * @url employees/:id
	 * @method  DELETE
	 */
	public function delete(Request $request)
    {
        $id = $request->param('id');
        $m_employee = new EmployeeModel();
        $result = $m_employee->deleteEmployee($id);
        if (!$result) {
            return $this->sendError(400, $m_employee->getError());
        }
        return $this->sendSuccess();
	}

    /**
     * @desc  禁用帐号
     * @author luo
     * @param $id
     * @url   /api/employees/:id/dodisable
     * @method GET
     */
	public function do_disable(Request $request, $id) {
        $employee = EmployeeModel::get($id);

        if (!$employee) {
            return $this->sendError(400, '该员工不存在或已删除');
        }

	    $result = $employee->disableAccount();

	    if(!$result){
	        return $this->sendError(400,$employee->getError());
        }

        return $this->sendSuccess();

    }

    /**
     * @desc  启用帐号
     * @author luo
     * @param $id
     * @url   /api/employees/:id/doactive
     * @method GET
     */
    public function do_active(Request $request, $id) {
        $input = $request->post();
        $employee = EmployeeModel::get($id);
        if (!$employee) {
            return $this->sendError(400, '该员工不存在或已删除');
        }

        $result = $employee->activeAccount($input);

        if(!$result){
            return $this->sendError(400,$employee->getError());
        }

        return $this->sendSuccess();
    }

    public function do_hire(Request $request)
    {
        $eid = input('eid/d');
        $employee = Employee::get($eid);
        if(empty($employee)) return $this->sendError(400, '员工不存在');

        $rs = $employee->save(['is_on_job' => 1]);
        if($rs === false) return $this->sendError(400, '操作失败');

        return $this->sendSuccess();
    }

    /**
     * 获得员工用户信息
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     * @throws \think\exception\DbException
     */
    public function get_list_user(Request $request)
    {
        $eid = input('id');
        $employee = Employee::get($eid);
        if(empty($employee)) return $this->sendError(400, '员工不存在');

        $mUser = new User();
        $m_user = $mUser->where('uid',$employee->uid)->find();

        return $this->sendSuccess($m_user);
    }

    /**
     * @desc  获取老师的排课
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list_course(Request $request)
    {
        $mode = input('mode/d', 1);
        $apn = input('apn','0,1,2');
        $date = input('date', date('Y-m-d', time()));
        $eid = input('eid');

        //获取某天一周的开始与结束
        $year = date('Y', time());
        $week = date('W', strtotime($date));
        $week_time_arr = weekday($year, $week);
        $start_day = date('Ymd', $week_time_arr['start']);
        $end_day = date('Ymd', $week_time_arr['end']);
        $start_day_obj = new DateTime($start_day);
        $end_day_obj = new DateTime(($end_day + 1));
        $interval = new DateInterval('P1D');
        $range = new DatePeriod($start_day_obj, $interval, $end_day_obj);

        $m_ca = new CourseArrange();
        $eid_arr = explode(',', $eid);
        $list = $m_ca->with('oneClass')->where('teach_eid', 'in', $eid_arr)
            ->where('int_day', 'between', [$start_day, $end_day])->select();
        $mEts = new EmployeeTimeSection();
        $time_list = $mEts->where('eid', 'in', $eid_arr)
            ->where('int_day', 'between', [$start_day, $end_day])->select();

        $data = [];
        if($mode === 1) {   #  星期为横轴
            foreach($eid_arr as $per_eid) {
                $employee = Employee::get($per_eid);
                if(empty($employee)) continue;
                $tmp = [];

                foreach($range as $dt) {
                    $day = $dt->format('Ymd');
                    $tmp['AM'][$day]['list'] = [];
                    $tmp['PM'][$day]['list'] = [];
                    $tmp['NM'][$day]['list'] = [];
                    foreach($list as $row) {
                        if($row['int_day'] == $day && $row['teach_eid'] == $per_eid) {
                            $int_start_hour = $row->getData('int_start_hour');
                            if(600 <= $int_start_hour && $int_start_hour <= 1200) {
                                $tmp['AM'][$day]['list'][] = $row;
                            } elseif(1200 < $int_start_hour && $int_start_hour <= 1800) {
                                $tmp['PM'][$day]['list'][] = $row;
                            } else {
                                $tmp['NM'][$day]['list'][] = $row;
                            }
                        }
                    }

                    $tmp['AM'][$day]['time'] = [];
                    $tmp['PM'][$day]['time'] = [];
                    $tmp['NM'][$day]['time'] = [];
                    foreach($time_list as $time) {
                        if($time['int_day'] == $day && $time['eid'] == $per_eid) {
                            $int_start_hour = $time->getData('int_start_hour');
                            if(600 <= $int_start_hour && $int_start_hour <= 1200) {
                                $tmp['AM'][$day]['time'][] = $time;
                            } elseif(1200 < $int_start_hour && $int_start_hour <= 1800) {
                                $tmp['PM'][$day]['time'][] = $time;
                            } else {
                                $tmp['NM'][$day]['time'][] = $time;
                            }
                        }
                    }

                }
                $tmp_list = [];
                $tmp_list['AM'] = array_values($tmp['AM']);
                $tmp_list['PM'] = array_values($tmp['PM']);
                $tmp_list['NM'] = array_values($tmp['NM']);
                $tmp_list['ename'] = $employee['ename'];
                $data[] = $tmp_list;
            }

        } else {    # 星期为纵轴
            foreach($eid_arr as $per_eid) {
                $tmp = [];
                $employee = Employee::get($per_eid);
                if(empty($employee)) continue;

                foreach($range as $dt) {
                    $day = $dt->format('Ymd');
                    $tmp[$day]['AM'] = [];
                    $tmp[$day]['PM'] = [];
                    $tmp[$day]['NM'] = [];
                    foreach($list as $row) {
                        if($row['int_day'] == $day && $row['teach_eid'] == $per_eid) {
                            $int_start_hour = $row->getData('int_start_hour');
                            if(600 <= $int_start_hour && $int_start_hour <= 1200) {
                                $tmp[$day]['AM'][] = $row;
                            } elseif(1200 < $int_start_hour && $int_start_hour <= 1800) {
                                $tmp[$day]['PM'][] = $row;
                            } else {
                                $tmp[$day]['NM'][] = $row;
                            }
                        }
                    }
                }
                $tmp['ename'] = $employee['ename'];
                $data[] = $tmp;
            }

        }

        return $this->sendSuccess($data);
    }

    /**
     * @desc  老师一对一、一对多学员
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list_students(Request $request)
    {
        $get = $request->get();


        $eid = input('id');

        $get = input('get.');
        $my_eid = \app\api\model\User::getEidByUid(gvar('uid'));

        if(!$request->isMobile() && isset($get['with']) && $get['with'] == 'student_lesson' ) {
           // if ($eid == $my_eid && isset($get['with']) && $get['with'] == 'student_lesson' && isset($get['pagesize']) && $get['pagesize'] == 10000) {
                return $this->get_my_students();
           // }
        }

        $m_student = new Student();

        $ret['list'] = [];

        if(!empty($get['cid']) && isset($get['cid'])) {
            if(strpos($get['cid'],',')){
                $cids = explode(',',$get['cid']);
            }else{
                $cids[0] = $get['cid'];
            }
            $arr = [];
            foreach ($cids as $cid) {
                $sids = (new ClassStudent())->where('cid', $cid)->where('status', ClassStudent::STATUS_NORMAL)
                ->column('sid');
                $m_student = new Student();
                $res = $m_student->where('sid', 'in', $sids)->field('sid')->getSearchResult([],false);
                $arr = array_merge($arr,$res['list']);
            }
            $ret['list'] = array_merge($ret['list'],$arr);
        } else {
            $arr = [];
            $sids = (new EmployeeStudent)->where('eid',$eid)->column('sid');
            $sids = array_unique($sids);
            $m_student = new Student();
            $res = $m_student->where('sid', 'in', $sids)->field('sid')->getSearchResult([],false);
            $arr = array_merge($arr,$res['list']);
            $ret['list'] = array_merge($ret['list'],$arr);
        }
        
        // 老师带的班级
        if(isset($get['class']) && $get['class'] == 1 && !isset($get['cid'])){
            $cids = (new Employee())->getOnlyClasses($eid);
            $arr = [];
            foreach ($cids as $cid) {
                $sids = (new ClassStudent())->where('cid', $cid)->where('status', ClassStudent::STATUS_NORMAL)
                ->column('sid');
                $m_student = new Student();
                $res = $m_student->where('sid', 'in', $sids)->field('sid')->getSearchResult([],false);
                $arr = array_merge($arr,$res['list']);
            }
        }

        $ret['list'] = array_merge($ret['list'],$arr);
        $this->assoc_unique($ret['list'],'sid');

        // 姓名搜索
        if(  isset($get['student_name']) && !empty($get['student_name']) ){
            $student_list = [];
            $student_name = trim($get['student_name']);
            unset($get['student_name']);
            $m_student->where(function ($query) use ($student_name) {
                $query->where('student_name',  'like', '%' . $student_name . '%');
                if (preg_match("/^[a-z]*$/i", $student_name)) {/*全英文*/
                    $query->whereOr('pinyin', 'like', '%' . $student_name . '%');
                    $query->whereOr('pinyin_abbr', $student_name);
                }
            });
            $student_ids = $m_student->column('sid');
            foreach ($ret['list'] as $k => $v) {
                if(in_array($v['sid'],$student_ids)){
                    $student_list[$k] = $v;
                }
                unset($ret['list'][$k]);
            }
            array_values($student_list);
            $ret['list'] = $student_list;
        }

        //排序
        $order_field = isset($get['order_field']) ? $get['order_field'] : 'sid';
        $order_type  = isset($get['order_type']) ? $get['order_type'] : 'desc';
        if($order_type == 'desc'){
            $sort = SORT_DESC;
        }elseif($order_type == 'asc'){
            $sort = SORT_ASC;
        }
        if(!empty($ret['list'])){
            foreach ($ret['list'] as $k => $item) {
                $sinfo = get_student_info($item['sid']);
                if($sinfo) {

                    $ret['list'][$k]['student_lesson_remain_hours'] = $sinfo['student_lesson_remain_hours'];
                    $ret['list'][$k]['student_lesson_hours'] = $sinfo['student_lesson_hours'];
                }
            }
            foreach ($ret['list'] as $row){
                $volume[]  = $row[$order_field];
            }
            array_multisort($volume, $sort, $ret['list']);
        }

        $m_sl = new StudentLesson();
        $showalltel = request()->user->hasPer('student.showalltel');
        $list = [];
        foreach($ret['list'] as $row) {
            $student = get_student_info($row['sid']);
            $student['original_first_tel'] = $student['first_tel'];
            if (!empty($per_student['second_tel'])){
                $student['original_second_tel'] = $student['second_tel'];
            }
            if (!$showalltel){
                $student['first_tel'] = substr_replace($student['first_tel'],'****',7,11);
                if (!empty($student['second_tel'])){
                    $student['original_second_tel'] = $student['second_tel'];
                    $student['second_tel'] = substr_replace($student['second_tel'],'****',7,11);
                }
            }
            $student['birth_time'] = date('Y-m-d',$student['birth_time']);
            if(empty($student)) continue;
            $cids = (new ClassStudent)->where('sid',$row['sid'])->column('cid');
            $cids = array_unique($cids);
            $student['cids'] = $cids;

            $student_lesson_list = $m_sl->where('sid',$row['sid'])->select();

            if(!$student_lesson_list){
                $student_lesson_list = [];
            }
            $student['student_lesson'] = $student_lesson_list;

            $list[] = $student;
        }

        $ret['list'] = $list;
        $ret['total'] = count($list);
        $ret['page'] = isset($get['page']) ? intval($get['page']) : 1;
        $ret['pagesize'] = isset($get['pagesize']) ? intval($get['pagesize']) : 10;

        // 分页
        if(isset($get['page']) && isset($get['pagesize'])){
            $offset = ($get['page']-1)*$get['pagesize'];
            $pagesize = $get['pagesize'];
            $ret['list'] = array_slice($ret['list'],$offset,$pagesize);
        }
        
        return $this->sendSuccess($ret);
    }

    /**
     * 获得我的学员列表
     */
    public function get_my_students(){
        $request = request();
        $get = $request->get();
        $eid = input('id');
        $with = input('with');
        $with = !empty($with) ? explode(',', $with) : [];
        $get['order_field'] = 's.sid';
        /** @var Query $m_student */
        $m_student = new Student();

        if(!empty($get['cid'])) {
            $sids = (new ClassStudent())->where('cid', $get['cid'])->where('status', ClassStudent::STATUS_NORMAL)
                ->column('sid');
            $sids = $sids ?? [-1];
            $m_student = new Student();
            $ret = $m_student->where('sid', 'in', $sids)->getSearchResult([],false);
        } else {
            $ret = $m_student->alias('s')->join('EmployeeStudent es', 'es.sid = s.sid')
                ->where('es.eid', $eid)->where('es.is_delete = 0')->distinct(true)->field('es.sid')->getSearchResult($get,false);
        }

        $m_sl = new StudentLesson();
        $list = [];
        foreach($ret['list'] as $row) {
            $student = Student::get(['sid' => $row['sid']]);
            if(empty($student)) continue;
            $student = $student->toArray();

            if(in_array('student_lesson', $with)) {
                $sl_list = $m_sl->where('lesson_status', 'lt', StudentLesson::LESSON_STATUS_DONE)
                    ->where('lesson_type', Lesson::LESSON_TYPE_ONE_TO_ONE)->where('sid', $row['sid'])
                    ->order('sl_id desc')->select();
                $student['student_lesson'] = !empty($sl_list) ? $sl_list : [];
            }

            $list[] = $student;
        }

        $ret['list'] = $list;

        if(isset($get['class']) && $get['class'] == 1){
            $ret['class'] = (new Employee())->getClasses($eid,true);
        }
        return $this->sendSuccess($ret);
    }


    /**
     * 二维数组去重
     * @param  [type] &$arr [description]
     * @param  [type] $key  [description]
     * @return [type]       [description]
     */
    public function assoc_unique(&$arr, $key) 
    { 
        $rAr=array(); 
        for($i=0;$i<count($arr);$i++) { 
            if(!isset($rAr[$arr[$i][$key]])) { 
                $rAr[$arr[$i][$key]]=$arr[$i]; 
            } 
        } 
        $arr=array_values($rAr); 
    } 

    /**
     * 获得员工所带班级
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function get_list_classes(Request $request){
        $get = $request->get();
        $eid = input('id');
        $with_students = isset($get['students']) && $get['students'] == 1;
        $include_end   = isset($get['end']) && $get['end'] == 1;

        $classes = (new Employee())->getClasses($eid,$with_students,$include_end);

        return $this->sendSuccess($classes);
    }



}