<?php
/** 
 * Author: luo
 * Time: 2017-10-26 17:18
**/


namespace app\api\controller;


use app\api\model\ClassStudent;
use app\api\model\CourseArrange;
use app\api\model\CourseArrangeStudent;
use app\api\model\Customer;
use app\api\model\CustomerEmployee;
use app\api\model\Employee;
use app\api\model\EmployeeStudent;
use app\api\model\MarketClue;
use app\api\model\Order;
use app\api\model\PublicSchool;
use app\api\model\StudentDebitCard;
use app\api\model\StudentLeave;
use app\api\model\StudentLesson;
use app\api\model\StudentAttendance;
use think\Log;
use think\Request;
use app\api\model\Student;
use app\api\model\Classes;
use think\Validate;
use util\skfrm;

class Students extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();

        if(isset($input['search_field']) && $input['search_field'] == 'student_name'){
            $input['student_name'] = $input['search_value'];
            unset($input['search_field']);
            unset($input['search_value']);
        }

        if(isset($input['search_value']) && !empty($input['search_value'])){
            $input['student_name'] = $input['search_value'];
            unset($input['search_value']);
        }

        if(isset($input['bid']) && ($input['bid'] != $request->bid) &&
            !isset($input['student_name']) && !isset($input['name_and_card'])
        ) {
            return $this->sendError(400, '选择校区同时需要输入学生名字或学员卡号');
        }


        $model = new Student();
        if (!empty($input['student_name'])) {
            $student_name = trim($input['student_name']);
            unset($input['student_name']);
            $model->where(function ($query) use ($student_name) {
                $query->where('student_name',  'like', '%' . $student_name . '%');
                if (preg_match("/^[a-z]*$/i", $student_name)) {/*全英文*/
                    $query->whereOr('pinyin', 'like', '%' . $student_name . '%');
                    $query->whereOr('pinyin_abbr', $student_name);
                }
            });
        }

        if(isset($input['school_grade']) && !empty($input['school_grade'])){
            $client = gvar('client');
            $cid    = $client['cid'];
            $now_ym = date('Ym',time());
            $cache_key = 'refresh_grade_'.$cid.'_'.gvar('og_id').'-'.$now_ym;
            $is_done = cache($cache_key);
            if(!$is_done) {
                $now_month = intval(date('n',time()));
                if ($now_month < 9) {
                    $sql = "update `x360p_student` set `school_grade`=`school_grade`+FLOOR(($now_ym-`grade_update_int_ym`)/100),`grade_update_int_ym` = $now_ym where `school_grade`<>0 and `grade_update_int_ym` > 0 and `grade_update_int_ym` < $now_ym";
                } else {
                    $sep_ym = date('Y', time()) . '09';
                    $sql = "update `x360p_student` set `school_grade`=`school_grade`+1,`grade_update_int_ym`=$now_ym where `school_grade`<>0 and `grade_update_int_ym`>0 and `grade_update_int_ym` < $sep_ym";
                }
                db()->query($sql);
                $sql = "update `x360p_student` set `school_grade`=1 where `school_grade`=0 and `grade_update_int_ym`=$now_ym";
                db()->query($sql);
                cache($cache_key,1,86400*30);
            }
        }

        if(!empty($input['name_and_card'])) {
            $name_and_card = trim($input['name_and_card']);
            $model->where('student_name|card_no','like','%'.$name_and_card.'%');
        }

        if(!isset($input['status'])) {
            $model->where('s.status', 'lt', Student::STATUS_SEAL);
        }

        if(isset($input['age_start']) && isset($input['age_end']) && $input['age_start'] == $input['age_end']){
            $months = age_to_months($input['age_start']);

            $age_time_start = strtotime("-$months months");
            $age_time_end   = strtotime("+1 month",$age_time_start) - 1;

            $model->where('birth_time','BETWEEN',[$age_time_start,$age_time_end]);
            unset($input['age_start']);
        }else{
            if(isset($input['age_start'])) {
                $months = age_to_months($input['age_start']);
                $age_start_time = strtotime("-$months months");
                $model->where('birth_time', 'elt', $age_start_time);
                unset($input['age_start']);
            }
            if(isset($input['age_end'])) {
                $months = age_to_months($input['age_end']);
                $age_end_time = strtotime("-$months months");
                $model->where('birth_time', 'egt', $age_end_time);
                unset($input['age_end']);
            }

        }

        if(isset($input['cid'])) {
            $sids = (new ClassStudent())->where('cid', $input['cid'])->where('status', ClassStudent::STATUS_NORMAL)
                ->column('sid');
            $sids = $sids ?: [-1];
            $model->where('sid', 'in', $sids);
        }

        //$model->withCount(['studying_lessons', 'stoping_lessons']);
        if(isset($input['wechat_bind'])) {
            if(isset($input['status'])){
                $model->where('s.status',$input['status']);
                unset($input['status']);
            }
            if($input['wechat_bind'] == 1) {  // 绑定了微信
                // $ret = $model->alias('s')->join('user u', 's.first_uid = u.uid')->where('u.openid', 'neq', '')->field('s.*')->getSearchResult($input);
                $ret = $model->alias(['x360p_student'=>'s','x360p_user'=>'u'])->join('x360p_user u', 's.first_uid = u.uid')->where('u.openid', 'neq', '')->field('s.*')->getSearchResult($input);
                // $ret = $model->alias('s')->join('__USER__ u ','s.first_uid= u.uid')->whrer('u.openid','neq','')->field('s.*')->getSearchResult($input);
            } else {  // 未绑定微信
                // $ret = $model->alias('s')->join('user u', 's.first_uid = u.uid')->where('u.openid', 'eq', '')->field('s.*')->getSearchResult($input);
                $ret = $model->alias(['x360p_student'=>'s','x360p_user'=>'u'])->join('x360p_user u', 's.first_uid = u.uid')->where('u.openid', 'eq', '')->field('s.*')->getSearchResult($input);
                // $ret = $model->alias('s')->join('__USER__ u ','s.first_uid= u.uid')->whrer('u.openid','eq','')->field('s.*')->getSearchResult($input);
            }
        } else {
            $ret = $model->alias(['x360p_student'=>'s'])->setNextPrevFields(['sid','student_name','nick_name','sex','photo_url','first_tel'])->getSearchResult($input);
        }

        $m_sl = new StudentLesson();
        $showalltel = request()->user->hasPer('student.showalltel');
        foreach($ret['list'] as &$per_student) {
            $per_student['referer_student_name'] = $model->getRefererStudentNameAttr(0,$per_student);
            if(!$per_student['card_no']) {$per_student['card_no'] = '';}
            if($per_student['status'] == Student::STATUS_SUSPEND){
                $per_student['stop_reason'] = $this->m_student_lesson_stop->where('sid',$per_student['sid'])->order('sls_id desc')->value('stop_remark');
            }
            $per_student['school_id_text'] = PublicSchool::getSchoolIdText($per_student['school_id']);
            $per_student['mc_name'] = get_mc_name($per_student['mc_id']);
            $per_student['studying_lessons_count'] = $m_sl->where('sid', $per_student['sid'])
                ->where('lesson_status', 'lt', $m_sl::LESSON_STATUS_DONE)->count();
            $per_student['stoping_lessons_count'] = $m_sl->where('sid', $per_student['sid'])
                ->where('is_stop = 1')
                ->where('lesson_status', 'lt', $m_sl::LESSON_STATUS_DONE)->count();

            $per_student['original_first_tel'] = $per_student['first_tel'];
            if (!empty($per_student['second_tel'])){
                $per_student['original_second_tel'] = $per_student['second_tel'];
            }
            if (!$showalltel){
                $per_student['first_tel'] = substr_replace($per_student['first_tel'],'****',7,11);
                if (!empty($per_student['second_tel'])){
                    $per_student['original_second_tel'] = $per_student['second_tel'];
                    $per_student['second_tel'] = substr_replace($per_student['second_tel'],'****',7,11);
                }
            }
        }


        return $this->sendSuccess($ret);
    }

    /**
     * 获取学员所在班级
     * api/students/431/classes
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_list_classes(Request $request)
    {
        $id = input('id/d');
        $input = $request->param();
        $student = get_student_info($id);
        if(empty($student)){
            return $this->sendError(400,'学员不存在或已删除');
        }
        $mClassStudent = new ClassStudent;
        $w = [];
        $w['is_end'] = 0;
        $w['status'] = 1;
        $w['sid'] = $id;
        $ret = $mClassStudent->where($w)->with('oneClass,oneClass.teacher')->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    /**
     * 获取未分班学员列表
     * /api/students/get_noclass_students
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_noclass_students(Request $request)
    {
        $input = $request->param();
        $model = new Student;

        $ret = $model->alias(['x360p_student'=>'s','x360p_class_student'=>'cs'])->join('x360p_class_student cs', 's.sid = cs.sid and cs.status = 1 and cs.is_end = 0','left')->where(['s.status'=>['lt',90],'cs.cs_id'=>null])->field('s.*')->getSearchResult($input);

        return $this->sendSuccess($ret);
    }
    
    /**
     * 获取已分班学员列表
     * /api/students/get_class_students
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_class_students(Request $request)
    {
        $input = $request->param();
        $model = new Student;

        $ret = $model->distinct(true)->alias(['x360p_student'=>'s','x360p_class_student'=>'cs'])->join('x360p_class_student cs', 's.sid = cs.sid and cs.status = 1 and cs.is_end = 0','inner')->where(['s.status'=>['lt',90],'cs.cs_id'=>['gt',0]])->field('s.sid,s.student_name,s.first_tel,s.status,s.birth_time,s.create_time,s.school_grade,s.money,s.student_lesson_remain_hours,s.student_lesson_hours')->getSearchResult($input);

        return $this->sendSuccess($ret);
    }
    
    /**
     * 规定时间内 未出勤学员
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_noattendance_students(Request $request)
    {
        $input = $request->param();
        $mStudentAttendance = new StudentAttendance;
        $w['is_in'] = 0;
        if (!empty($input['start_date'])) {
            $w['int_day'] = ['between', [date('Ymd',strtotime($input['start_date'])), date('Ymd',strtotime($input['end_date']))]];
        }
        $ret = $mStudentAttendance->where($w)->with('student')->getSearchResult($input);

        return $this->sendSuccess($ret);
    }


    public function get_info_by_tel(Request $request)
    {
        $input = $request->param();
        if(isset($input['tel']) && is_mobile($input['tel'])){
            $ret = [
                [
                    'type'=>'market',
                    'detail'=>$this->get_market_by_tel($input['tel']),
                ],
                [
                    'type'=>'customer',
                    'detail'=>$this->get_customer_by_tel($input['tel']),
                ],
                [
                    'type'=>'student',
                    'detail'=>$this->get_student_by_tel($input['tel']),
                ],
                [
                    'type'=>'employee',
                    'detail'=>$this->get_employee_by_tel($input['tel']),
                ],
            ];
        }
        return $this->sendSuccess($ret); 
    }

    public function get_market_by_tel($tel)
    {
        $model = new MarketClue;
        $ret = $model->where(['tel'=>$tel,'og_id'=>gvar('og_id')])->field('name,bid,cu_assigned_bid,assigned_eid,cu_assigned_eid,tel,sex,create_time')->select();
        return $ret;
    }

    public function get_customer_by_tel($tel)
    {
        $model = new Customer;
        $ret = $model->where(['first_tel'=>$tel,'og_id'=>gvar('og_id')])->field('name,bid,follow_eid,first_tel,sex,create_time')->select();
        return $ret;
    }

    public function get_student_by_tel($tel)
    {
        $model = new Student;
        $ret = $model->where(['first_tel'=>$tel,'og_id'=>gvar('og_id')])->field('student_name,bid,first_tel,sex,create_time')->select();
        return $ret;
    }

    public function get_employee_by_tel($tel)
    {
        $model = new Employee;
        $ret = $model->where(['mobile'=>$tel,'og_id'=>gvar('og_id')])->field('ename,bid,mobile,sex,create_time')->select();
        return $ret;
    }


    public function get_detail(Request $request, $id = 0)
    {
        $sid = $request->param('id/d');
        $with = $request->get('with');
        if ($with) {
            $with = explode(',',$with);
        } else {
            $with = [];
        }
        $student = Student::get($sid, $with);
        $student['school_id_text'] = PublicSchool::getSchoolIdText($student['school_id']);
        return $this->sendSuccess($student);
    }

    public function delete(Request $request)
    {
        $sid = input('id');
        $is_force = input('force', 0);
        $student = Student::get($sid);
        $rs = $student->delStudent($sid, $student, $is_force);
        if($rs === false) {
            if($student->get_error_code() == $student::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($student->getErrorMsg());
            }
            return $this->sendError(400, $student->getErrorMsg());
        }

        return $this->sendSuccess();
    }

    public function search(Request $request)
    {
        $input = $request->param();

        $model = new Student();
        if (!empty($input['student_name'])) {
            $student_name = $input['student_name'];
            unset($input['student_name']);
            $model->where(function ($query) use ($student_name) {
                $query->where('student_name',  'like', '%' . $student_name . '%');
                if (preg_match("/^[a-z]*$/i", $student_name)) {/*全英文*/
                    $query->whereOr('pinyin', 'like', '%' . $student_name . '%');
                    $query->whereOr('pinyin_abbr', $student_name);
                }
            });
        }
        $ret = $model->getSearchResult($input);

        foreach($ret['list'] as &$per_student) {
            $per_student['school_id_text'] = PublicSchool::getSchoolIdText($per_student['school_id']);
        }

        return $this->sendSuccess($ret);
    }

    public function searchByNameAndCard(Request $request){
        $input = $request->get();
        $m_student = new Student();
        if(!empty($input['name'])){
            $name = $input['name'];
            $m_student->where('student_name|card_no','like','%'.$name.'%');
            unset($input['name']);
        }
        $res = $m_student->getSearchResult($input);
        if (empty($res)){
            $this->sendError(400,'学员信息不存在');
        }

        return $this->sendSuccess($res);
    }

    /**
     * @desc 添加学员
     * @author luo
     * @method POST
     */
    public function post(Request $request)
    {
        $input = $request->post();
        $m_student = new Student();
        $rs = $m_student->createOneStudent($input);
        if($rs === false) return $this->sendError(400, $m_student->getErrorMsg());

        return $this->sendSuccess($rs);
    }

    public function put(Request $request)
    {
        $sid = input('sid/d');
        $input = $request->put();
        $student = Student::get(['sid' => $sid]);
        if(!$student){
            return $this->sendError(400,'学员信息错误，学员ID不存在或被删除!');
        }
        unset($input['create_time']);
        if(isset($input['card_no']) && !empty($input['card_no'])) {
            $ex_sinfo = (new Student())->where('sid', 'neq', $sid)->where('card_no', $input['card_no'])->find();
            if($ex_sinfo) {
                $binfo = get_branch_info($ex_sinfo['bid']);
                $msg = sprintf("卡号:%s已分配给%s校区学员:%s", $input['card_no'], $binfo['branch_name'], $ex_sinfo['student_name']);
                return $this->sendError(400, $msg);
            }
        }

        if(isset($input['bid']) && $input['bid'] != $student->bid){
            $result = $student->transferBranch($input['bid']);
            if(!$result){
                return $this->sendError(400,$student->getError());
            }

            return $this->sendSuccess();
        }


        $rs = $student->updateStudentInfo($input);
        if ($rs === false) {
            return $this->sendError(400, $student->getError());
        } else {
            return $this->sendSuccess();
        }
    }

    /**
     * @desc  学生的所有排课
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list_course_arranges(Request $request)
    {
        $sid = input('id/d');

        $where = [];

        input('is_attendance/d') && $where['is_attendance'] = input('is_attendance');

        $int_start_day = input('int_start_day');
        if($int_start_day) {
            $int_start_day = date('Ymd', strtotime($int_start_day));
            $where['int_day'] = ['>=', $int_start_day];
        }
        $int_end_day = input('int_end_day');
        if($int_end_day) {
            $int_end_day = date('Ymd', strtotime($int_end_day));
            if(isset($where['int_day'])) {
                $where['int_day'] = [$where['int_day'], ['<=', $int_end_day]];
            } else {
                $where['int_day'] = ['<=', $int_end_day];
            }
        }

        $course_list = [];

        //--1-- 所有的班课
        //$class_list = ClassStudent::all(['sid' => $sid]);
        $class_list = (new ClassStudent())->where('sid', $sid)->where('status', ClassStudent::STATUS_NORMAL)->select();
        $class_list = !empty($class_list) ? collection($class_list)->toArray() : [];
        $cids = !empty($class_list) ? array_column($class_list, 'cid') : [];
        $cids = array_unique($cids);
        foreach($cids as $cid) {
            $tmp = (new CourseArrange())->where('cid', $cid)->with('oneClass')->where($where)->select();
            $course_list = array_merge($course_list, $tmp);
        }

        $class_ca_ids = array_column($course_list, 'ca_id');

        //--2-- 所有的一对一、一对多课程
        $ca_ids = (new CourseArrangeStudent())->where('sid', $sid)->where($where)->column('ca_id');
        $ca_ids = array_diff($ca_ids, $class_ca_ids);
        foreach($ca_ids as $arr) {
            $tmp = CourseArrange::get(['ca_id' => $arr['ca_id']], ['oneClass']);
            if(empty($tmp)) continue;
            array_push($course_list, $tmp->toArray());
        }

        if(input('student_leave')) {
            foreach($course_list as &$row) {
                $student_leave = (new StudentLeave())->where('ca_id', $row['ca_id'])->where('sid', $sid)->find();
                $row['student_leave'] = $student_leave;
            }
        }

        array_multisort(array_column($course_list, 'ca_id'), SORT_ASC, $course_list);

        return $this->sendSuccess($course_list);
    }

    public function get_list_class(Request $request)
    {
        $sid = input('id/d');
        $student = Student::get(['sid' => $sid]);
        if(empty($student)) return $this->sendError(400, '学生不存在');

        $class = ClassStudent::all(['sid' => $sid, 'status' => ClassStudent::STATUS_NORMAL], ['one_class.schedules']);

        return $this->sendSuccess($class);
    }

    /**
     * 获得指定学员的停课休学记录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_list_lessonstops(Request $request)
    {
        $input = $request->param();
        $input['sid'] = input('id/d');
        if(isset($input['id'])){
            unset($input['id']);
        }
        if (!empty($input['with'])) {
            $with = explode(',', $input['with']);
            unset($input['with']);
        } else {
            $with = [];
        }
        $model = $this->m_student_lesson_stop;
        $ret = $model->getSearchResult($input,$with,false);
        return $this->sendSuccess($ret);
    }

    public function get_list_student_lessons(Request $request)
    {
        $sid = input('id/d');
        $input = $request->get();
        $input['sid'] = $sid;
        if(isset($input['id'])){
            unset($input['id']);
        }
        if (!empty($input['with'])) {
            $with = explode(',', $input['with']);
            unset($input['with']);
        } else {
            $with = [];
        }
        $ret = $this->m_student_lesson->getSearchResult($input,$with,false);

        if(isset($input['is_stop'])){
            if(!empty($ret['list'])){
                foreach($ret['list'] as $k=>$row){
                    $obj = new StudentLesson($row);
                    $obj->appendLessonStopFields($input['is_stop']);
                    $ret['list'][$k] = $obj->toArray();
                }
            }
        }
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  学生的相关销售人,作为下单默认签单人
     * @author luo
     * @method GET
     */
    public function get_list_salesman()
    {
        $sid = input('id');
        $eids = [];
        $data = [];

        $get = input('get.');

        if(isset($get['with']) && $get['with'] == 'refund'){

            $w_er['sid'] = $sid;
            $w_er['or_id'] = 0;
            $er_list = model('employee_receipt')->where($w_er)->select();

            if($er_list){
                foreach($er_list as $er){
                    array_push($eids,$er['eid']);
                }
            }


            $data['eid'] = array_unique($eids);
            return $this->sendSuccess($data);
        }


        $m_es = new EmployeeStudent();
        $eids = $m_es->where('sid', $sid)->order('es_id desc')->limit(10)->column('eid');

        $data = [];
        if(!empty($get['with']) && in_array('is_first_order', explode(',', input('with')))) {
            $order = Order::get(['sid' => $sid]);
            $data['is_first_order'] = empty($order) ? true : false;
        }

        if(empty($eids)) {
            $m_customer = new Customer();
            $customer = $m_customer->where('sid', $sid)->field('cu_id,follow_eid')->find();
            $data['eid'] = [];

            $m_ce = new CustomerEmployee();
            $eids = $m_ce->where('cu_id', $customer['cu_id'])->column('eid');
            if($customer && $customer['follow_eid'] > 0){
                $eids[] = $customer['follow_eid'];
            }
        }

        //判断市场名单的分配人
        $w_mcl['sid'] = $sid;
        $m_mcl = new MarketClue();
        $mcl_list = $m_mcl->where($w_mcl)->select();
        if($mcl_list){
            foreach($mcl_list as $mcl){
                if($mcl['assigned_eid'] > 0){
                    array_push($eids,$mcl['assigned_eid']);
                }
                if($mcl['qr_eid'] > 0){
                    array_push($eids,$mcl['qr_eid']);
                }
            }
        }

        //可以签单业绩的角色列表
        $have_ep_rids = [4,7];          //默认为咨询师和学管师

        $my = gvar('user');
        $my_rids = $my['employee']['rids'];


        foreach($my_rids as $rid){
            if(in_array($rid,$have_ep_rids)){
                array_push($eids,$my['employee']['eid']);
                break;
            }
        }

        $data['eid'] = array_unique($eids);
        return $this->sendSuccess($data);
    }

    /**
     * @desc  学员退学后回流，创建一个客户档案
     * @author luo
     * @method POST
     */
    public function post_customer()
    {
        $sid = input('id');
        $student = Student::get($sid);
        if(empty($student) || $student['status'] != Student::STATUS_QUIT) {
            return $this->sendError(400, '学生不存在或者不是退学学员');
        }

        $rs = $student->backToCustomer($sid, $student);
        if($rs === false) return $this->sendError(400, $student->getErrorMsg());

        return $this->sendSuccess();
    }

    /*停课*/
    public function do_stop(Request $request)
    {
        $sid = input('id/d');
        $student = Student::get($sid);
        if (empty($student)) {
            return $this->sendError(400, '学生不存在');
        }
        $info = $request->post('info/a');
        if (empty($info)) {
            return $this->sendError(400, '参数不合法');
        }
        $rule = [
            'sl_id|学生课程id'   => 'require|number',
            'stop_time|停课日期' => 'require',
            'stop_remark|停课备注' => 'max:255',
        ];
        $validate = new Validate($rule);
        foreach ($info as $item) {
            $right = $validate->check($item);
            if ($right !== true) {
                return $this->sendError(400, '参数错误！', 400, $validate->getError());
            }
        }
        $rs = $student->stopMultiLesson($info);
        if (!$rs) {
            return $this->sendError(400, $student->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * @desc  解封学员
     * @author luo
     * @url   /api/students/:id/
     * @method GET
     */
    public function unseal()
    {
        $sid = input('sid');
        $student = Student::get($sid);
        if(empty($student)) return $this->sendError(400, '学生不存在');

        $result = $student->unseal();
        if(!$result){
            return $this->sendError(400,$student->getError());
        }

        return $this->sendSuccess();
    }

    /*复课*/
    public function do_recover(Request $request)
    {
        $sid = input('id/d');
        $student = Student::get($sid);
        if (empty($student)) {
            return $this->sendError(400, '学生不存在');
        }
        $info = $request->post('info/a');
        if (empty($info) || count($info) < 1) {
            return $this->sendError(400, '参数错误!');
        }
        $rule = [
            'sl_id|学生课程id'      => 'require|number',
            'recover_time|复课日期' => 'require|date',
        ];
        $validate = new Validate($rule);
        foreach ($info as $item) {
            $right = $validate->check($item);
            if ($right !== true) {
                return $this->sendError(400, '参数错误！', 400, $validate->getError());
            }
        }
        $rs = $student->recoverMultiLesson($info);
        if (!$rs) {
            return $this->sendError(400, $student->getError());
        }
        return $this->sendSuccess();
    }

    /*休学，相当于批量停课,操作对象student_lesson*/
    public function do_suspend(Request $request)
    {
        $sid = input('id/d');
        $student = Student::get($sid);
        if (empty($student)) {
            return $this->sendError(400, '学生不存在');
        }
        $info = $request->post();
        $rule = [
            'suspend_date|休学日期'   => 'require|date',
            'suspend_reason|休学原因' => 'max:255',
        ];
        $validate = new Validate($rule);
        $right = $validate->check($info);
        if ($right !== true) {
            return $this->sendError(400, $validate->getError());
        }
        $rs = $student->suspend($info);
        if (!$rs) {
            return $this->sendError(400, $student->getError());
        }
        return $this->sendSuccess();
    }

    /*复学*/
    public function do_back(Request $request)
    {
        $sid = input('id/d');
        $student = Student::get($sid);
        if (empty($student)) {
            return $this->sendError(400, '学生不存在');
        }
        $info = $request->post('info/a');
        if(!$info){
            $info = [];
        }
        $rs = $student->backToSchool($info);
        if (!$rs) {
            return $this->sendError(400, $student->getError());
        }
        return $this->sendSuccess();
    }

    /*结课,这里的结课对象是student_lesson*/
    public function do_close(Request $request)
    {
        $sid = input('id/d');
        $student = Student::get($sid);
        if (empty($student)) {
            return $this->sendError(400, '学生不存在');
        }
        $input = $request->post();
        $rule = [
            'sl_id|学生课程id数组' => 'require|array|min:1',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $rs = $student->closingClass($input['sl_id']);
        if (!$rs) {
            return $this->sendError(400, $student->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 撤销结课
     * @param Request $request
     */
    public function undo_close(Request $request){

        $sid = input('sid/d');
        $student = Student::get($sid);
        if (empty($student)) {
            return $this->sendError(400, '学生不存在');
        }
        $input = $request->post();
        $rule = [
            'sl_id|学生课程id数组' => 'require|array|min:1',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $rs = $student->undoClosingClass($input['sl_id']);
        if (!$rs) {
            return $this->sendError(400, $student->getError());
        }
        return $this->sendSuccess();
    }

    /*退学, 所有课程都结课*/
    public function do_quit(Request $request)
    {
        $sid = input('id/d');
        $student = Student::get($sid);
        if (empty($student)) {
            return $this->sendError(400, '学生不存在');
        }
        $student_situation = $student->getQuitSituation();
        $force = input('force/d', 0);
        if (!empty($student_situation) && !$force) {
            return $this->sendConfirm(join('  ', $student_situation) . '是否强制退学？');
        }
        $input = $request->post();
        if(!isset($input['remark'])){
            $input['remark'] = '';
        }
        $rule = [
            'quit_reason|退学原因' => 'require|max:255',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $rs = $student->quitSchool($input);
        if (!$rs) {
            return $this->sendError(400, $student->getError());
        }
        return $this->sendSuccess();
    }

    /*入学*/
    public function do_enrol(Request $request)
    {
        $sid = input('id/d');
        $student = Student::get($sid);
        if (empty($student)) {
            return $this->sendError(400, '学生不存在');
        }
        $rs = $student->enrol();
        if (!$rs) {
            return $this->sendError(400, $student->getError());
        }
        return $this->sendSuccess();
    }

    public function do_test(Request $request)
    {
        $sid = input('id/d');
        $student = Student::get($sid);
        if (empty($student)) {
            return $this->sendError(400, '学生不存在');
        }
        $data['list'] = $student->classStudents($request->time())->select();
        $data['sql'] = $student->getLastSql();
        return $this->sendSuccess($data);
    }

    /**
     * @desc  根据手机号查询学生、客户、市场名单列表
     * @author luo
     * @method GET
     */
    public function query_by_tel()
    {
        $tel = input('tel');
        $tel = trim($tel);
        if(empty($tel)) return $this->sendError(400, '参数错误');

        $m_student = new Student();
        $student_list = $m_student->where('first_tel|second_tel', $tel)
            ->field('sid,bid,student_name,photo_url,first_tel,second_tel,sex,create_time')->select();
        $student_list = collection($student_list)->toArray();

        $m_customer = new Customer();
        $customer_list = $m_customer->where('first_tel|second_tel', $tel)
            ->field('cu_id,bid,name,first_tel,second_tel,sex,create_time')->select();
        $customer_list = collection($customer_list)->toArray();

        $m_market_clue = new MarketClue();
        $market_clue_list = $m_market_clue->where('tel', $tel)->field('mcl_id,bid,name,tel,sex,create_time')->select();
        $market_clue_list = collection($market_clue_list)->toArray();

        $list = array_merge($student_list, $customer_list, $market_clue_list);
        return $this->sendSuccess($list);
    }


    /**
     * 余额兑换储值卡
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function do_changedc(Request $request)
    {
        $input = $request->post();

        $input['sid'] = input('id');

        $m_student = Student::get($input['sid']);

        if(!$m_student){
            return $this->sendError(400,'学员信息不存在!');
        }

        $result = $m_student->changeDc($input);

        if(false === $result){
            return $this->sendError(400,$m_student->getError());
        }

        return $this->sendSuccess($result);
    }


    /**
     * @desc  充值
     * @author luo
     * @method POST
     */
    public function post_money(Request $request)
    {
        $post = $request->post();
        $post['sid'] = input('id');

        $m_student = Student::get($post['sid']);

        if(!$m_student){
            return $this->sendError(400,'学员信息不存在!');
        }

        $result = $m_student->addMoney($post);

        if(false === $result){
            return $this->sendError(400,$m_student->getError());
        }

        return $this->sendSuccess($result);
    }


    /**
     * 转让金额
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function do_transmoney(Request $request)
    {
        $sid = input('id/d');
        $student = Student::get($sid);
        $input = $request->param();
        if(empty($student)){
            return $this->sendError(400,'学员不存在，或已删除！');
        }

        if($input['amount'] > $student->money){
            return $this->sendError(400,'转让金额不得大于已有金额');
        }

        $rule = [
            'to_sid|转让学生'    => 'require|number',
            'amount|转让金额'    => 'require|float',
        ];
        $validate = new Validate($rule);
        $right = $validate->check($input);
        if ($right !== true) {
            return $this->sendError(400, '参数错误！', 400, $validate->getError());
        }

        $res = $student->transferMoney($student,$input);

        if($res === true){
            return $this->sendSuccess('操作成功');
        }

    }

    /**
     * 转让课时
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function do_transhours(Request $request)
    {
        $sid = input('id/d');

        $student = Student::get($sid);
        if(!$student){
            return $this->sendError(400,'学员不存在,或已删除!');
        }

        $input = $request->post();

        $result = $student->transferHours($input);

        if(!$result){
            return $this->sendError(400,$student->getError());
        }

        return $this->sendSuccess($result);
    }


    /**
     * 盛开人脸识别生成H5注册链接
      * @return json
     */
    public function faceqr(Request $request){
        $sid = input('sid/d');
        $student = Student::get($sid);
        if(empty($student)){
            return $this->sendError(400,'学员不存在，或已删除！');
        }
        $bid = $student->bid;
        $student_name = $student->student_name;

        $username = 'ltx360';
        $password = 'sk200508';
        $callback_url = 'http://dev.xiao360.com/api/face_notify/input';
        $skfrm = new Skfrm();
        $result = $skfrm->Faceqr($username,$password,$sid,$bid,$student_name,$callback_url);
        return $this->sendSuccess($result);
    }


    /**
     * 生成模拟登录token
     * post api/students/43/domktoken
     * https://xxx.pro.xiao360.com/student#/tklogin?token=f3671429badf3ad54595221193efbd1c
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function do_mktoken(Request $request)
    {
        $sid    = input('id/d');
        $og_id  = gvar('og_id');
        $cid    = gvar('client.cid');

        $student = get_student_info($sid);
        if(empty($student)){
            return $this->sendError(400,'学员不存在，或已删除');
        }

        $w_us['sid'] = $sid;

        $mUserStudent = new \app\api\model\UserStudent();
        $m_us = $mUserStudent->where($w_us)->find();

        if(!$m_us){
            return $this->sendError(400,'该学员的学习管家账号未创建，无法登录!');
        }

        $uid = $m_us['uid'];

        $option = [
            $cid,
            $og_id,
            $sid,
            $uid,
            request()->time(),
            request()->ip(),
            random_str(),
            'tklogin'
        ];

        $token = md5(implode('',$option));
        $cache_key = cache_key($token);
        $login_student['sid'] = $sid;
        $login_student['uid'] = $uid;
        $login_student['og_id'] = $og_id;
        $login_student['cid'] = $cid;

        $login_expire = config('api.login_expire');
        cache($cache_key,$login_student,$login_expire);

        $scheme = $request->scheme();
        $host = $_SERVER['HTTP_HOST'];

        $tokenurl = sprintf("%s://%s/student#/tklogin?token=%s",$scheme,$host,$token);

        $ret['url'] = $tokenurl;
        return $this->sendSuccess($ret);
    }
}