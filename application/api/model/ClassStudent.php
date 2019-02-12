<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/4
 * Time: 18:08
 */
namespace app\api\model;

use app\common\exception\FailResult;
use think\Db;
use think\Exception;

class ClassStudent extends Base
{
    public static $detail_fields = [
        ['type'=>'index','width'=>60,'align'=>'center'],
        ['title'=>'学员姓名','key'=>'student_name','align'=>'center'],
        ['title'=>'校区','key'=>'bid','align'=>'center'],
        ['title'=>'联系电话','key'=>'first_tel','align'=>'center'],
        ['title'=>'班级','key'=>'cid','align'=>'center'],
        ['title'=>'状态','key'=>'status','align'=>'center'],
    ];

    const IS_END_YES = 1;       /*已结课*/
    const IS_END_NO  = 0;       /*未结课*/
    const STATUS_NORMAL = 1;    /*正常*/
    const STATUS_STOP   = 0;    /*停课*/
    const STATUS_CLASS_TRANSFER = 2;/*转班*/
    const STATUS_CLOSE = 9;     /*已结课*/

    const DEFAULT_RECOVER_INT_DAY = 21000101;/*默认的复课时间*/

    const IN_WAY_ORDER = 1; #订单方式
    const IN_WAY_ASSIGN = 2; #分班操作
    const IN_WAY_DSS = 3; #
    const IN_WAY_UPGRADE = 4; #升班操作
    public $in_way = ['order' => 1, 'assign' => 2, 'dss' => 3];

    protected $type = [
        'in_time'   => 'timestamp',
        'stop_time' => 'timestamp',
    ];

    protected static function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid');
    }

    public function cls()
    {
        return $this->hasOne('Classes', 'cid', 'cid');
    }

    public function student()
    {
        return $this->belongsTo('Student', 'sid', 'sid');
    }

    public function studentLesson()
    {
        return $this->belongsTo('StudentLesson', 'sl_id', 'sl_id');
    }


    //班级分配学员或客户
    public function assignStudentOrCustomer($cid, array $sids = [], array $cu_ids = [])
    {
        $class = Classes::get(['cid' => $cid]);
        if(empty($class)) return $this->user_error('班级不存在');
        if(request()->bid != $class['bid']) request()->bind('bid', $class['bid']);
        
        // 判断班级是否超员
        $class_remain_student_nums = $class->plan_student_nums - $class->student_nums;
        $assign_student_nums = count($sids);
        $assign_customer_nums = count($cu_ids);
        $assign_nums = $assign_student_nums + $assign_customer_nums;
        $exceed_stundent_nums = $assign_nums - $class_remain_student_nums;
        $error_msg = sprintf('班级学员将超出%s人，请减少%s名学员',$exceed_stundent_nums,$exceed_stundent_nums);
        if($exceed_stundent_nums > 0){
            return $this->user_error($error_msg);
        }

        $this->startTrans();
        try {
            //--1-- 循环添加班级学生
            foreach ($sids as $per_sid) {
                $rs = $this->addOneStudentToClass($class, $per_sid,false);
                if ($rs === false) throw new FailResult($this->getErrorMsg(), $this->get_error_code());
            }

            //--2-- 循环把客户添加到班级
            foreach ($cu_ids as $per_cu_id) {
                $rs = $this->addOneCustomerToClass($class, $per_cu_id,false);
                if ($rs === false) throw new FailResult($this->getErrorMsg(), $this->get_error_code());
            }
            

        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        $this->commit();

        return true;
    }

    //班级分配学员或客户
    public function assignStudentOrCustomerCopy($cid, array $sids = [], array $cu_ids = [])
    {
        $class = Classes::get(['cid' => $cid]);
        if(empty($class)) return $this->user_error('班级不存在');
        if(request()->bid != $class['bid']) request()->bind('bid', $class['bid']);

        $this->startTrans();
        try {
            //--1-- 循环添加班级学生
            foreach ($sids as $per_sid) {
                $rs = $this->addOneStudentToClass($class, $per_sid);
                if ($rs === false) throw new FailResult($this->getErrorMsg(), $this->get_error_code());
            }

            //--2-- 循环把客户添加到班级
            foreach ($cu_ids as $per_cu_id) {
                $rs = $this->addOneCustomerToClass($class, $per_cu_id);
                if ($rs === false) throw new FailResult($this->getErrorMsg(), $this->get_error_code());
            }
            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage(), $e->getCode());
        } catch(Exception $e) {
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    /*
     * 1. 班级学生表增加
     * 2. 班级学员人数
     * 3. 班级记录
     */
    public function addOneStudentToClass(Classes $class, $sid,$check_overmax = true)
    {
        $this->startTrans();
        try {
    	    if($check_overmax){
    	      	if($class->student_nums >= $class->plan_student_nums) throw new FailResult('班级已经超过计划人数,加入不了');
    	    }

            if(!self::isInClass($class->cid, $sid)) {
                //--1.1-- 班级不存在学生，则添加
                $data = [];
                $data['cid'] = $class->cid;
                $data['sid'] = $sid;
                $data['in_way'] = self::IN_WAY_ASSIGN;
                $data['in_time'] = input('in_time') ? strtotime(input('in_time')) : time();
                $rs = (new self())->isUpdate(false)->allowField(true)->save($data);
                if ($rs === false) throw new FailResult('班级学生表添加失败');

                //--2-- 班级学生人数增加
                $rs = $class->updateStudentNum($class->cid, $class);
                if ($rs === false) throw new FailResult($class->getErrorMsg());
            } else {
                //--1.2-- 存在学生刚更新
                $data = [];
                $data['cid'] = $class->cid;
                $data['in_way'] = self::IN_WAY_ORDER;
                $data['status'] = self::STATUS_NORMAL;
                $data['out_time'] = 0;
                $rs = (new self())->where('sid', $sid)->where('cid', $class->cid)->update($data);
                if ($rs === false) throw new FailResult($this->getErrorMsg());

                //--3-- 班级学生人数增加
                $rs = $class->updateStudentNum($class->cid, $class);
                if ($rs === false) throw new FailResult($class->getErrorMsg());
            }

            //更新学生的排课
            (new CourseArrange())->refreshStudentArrange($sid);

            //更新购买课程的分班状态
            $m_sl = new StudentLesson();
            $student_lesson = $m_sl->getBySattInfo($sid, $class->toArray());
            if(!empty($student_lesson) && ($student_lesson instanceof StudentLesson) && $student_lesson['ac_status'] == StudentLesson::AC_STATUS_NO) {
                $student_lesson->ac_status = StudentLesson::AC_STATUS_ALL;
                $rs = $student_lesson->allowField('ac_status')->save();
                if($rs === false) throw new FailResult($student_lesson->getError());

            }

            // 添加　学员　入班级操作日志
            ClassLog::addClassStudentInsertLog($class,$sid);

            // 创建学员与老师之间的关系
            $type = EmployeeStudent::TYPE_CLASS;
            $lid = $class->lid;
            $cid = $class->cid;
            $info = array(
                'sid' => $sid,
                'rid' => EmployeeStudent::EMPLOYEE_TEACHER,
                'eid' => $class->teach_eid
            );
            EmployeeStudent::addEmployeeStudentRelationship($info,$type,$lid,$cid);   

            // 创建学员与助教之间的关系
            if(!empty($class->second_eids)){
                foreach ($class->second_eids as $eid) {
                    $info = array(
                        'sid' => $sid,
                        'rid' => EmployeeStudent::EMPLOYEE_TA,
                        'eid' => $eid
                    );
                    EmployeeStudent::addEmployeeStudentRelationship($info,$type,$lid,$cid);
                }
            }

        } catch(Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return true;
    }


    /*
     * 1. 客户下单
     * 2. 转为学员
     * 3. 加入班级
     */
    public function addOneCustomerToClass(Classes $class, $cu_id,$check_overmax = true)
    {
        $customer_model = new Customer();
        $customer = $customer_model->find($cu_id);

        $this->startTrans();
        try {
            if($check_overmax){
                if($class->student_nums >= $class->plan_student_nums) throw new FailResult('班级已经超过计划人数,加入不了');
            }
            //--1-- 客户转为学员
            if($customer->is_reg !== 1) {
                $rs = $customer_model->changeToStudent($cu_id);
                if(!$rs) exception($customer_model->getErrorMsg());
                $customer = $customer->find($cu_id);
            }

            //--2-- 班级增加学员
            $rs = $this->addOneStudentToClass($class, $customer->sid);
            if(!$rs) exception($this->getErrorMsg());
            
        } catch(Exception $e) {
            $this->rollback();
            return $this->user_error(['msg' => $e->getMessage(), 'trace' => $e->getTrace()]);
        }

        $this->commit();

        return true;
    }

    /*
     * 1. 把学生从班级移除
     * 2. 班级人数减少
     * 3. 订单变为未分班
     * 4. 订单项目变为未分班
     */
    public function removeStudentFromClass($cs_id)
    {
        $class_student = $this->find($cs_id);
        if(empty($class_student)) return $this->user_error('班级不存在此学生');

        $this->startTrans();
        try{
            $class_student->status = self::STATUS_CLASS_TRANSFER;
            $class_student->out_time = time();
            $rs = $class_student->save();
            if($rs === false) new FailResult('转出学员失败');

            $m_class = new Classes();
            $rs = $m_class->updateStudentNum($class_student->cid);
            if($rs === false) new FailResult('班级人数减少失败');

            //更新学生的排课
            (new CourseArrange())->clearStudentAtClassArrange($class_student->sid,$class_student->cid);
            
            //添加一天学员退班日志
            ClassLog::addClassStudentDeleteLog($class_student);

            // 解除学员与老师之间的关系
            $class = get_class_info($class_student->cid);
            $type = EmployeeStudent::TYPE_CLASS;
            $lid = $class['lid'];
            $cid = $class['cid'];
            $info = array(
                'sid' => $class_student->sid,
                'rid' => EmployeeStudent::EMPLOYEE_TEACHER,
                'eid' => $class['teach_eid']
            );
            EmployeeStudent::deleteEmployeeStudentRelationship($info,$type,$lid,$cid);

            //解除学员与助教之间的关系
            if($class['second_eids']){
                $second_eids = explode(',',$class['second_eids']);
                foreach ($second_eids as $eid) {
                    $info = array(
                        'sid' => $class_student->sid,
                        'rid' => EmployeeStudent::EMPLOYEE_TA,
                        'eid' => $eid
                    );
                    EmployeeStudent::deleteEmployeeStudentRelationship($info,$type,$lid,$cid);
                }
            }

        } catch(Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    public static function countClassStudent($cid)
    {
        $num = (new self())->where('cid', $cid)->where('status != '.self::STATUS_CLASS_TRANSFER)->count();
        return $num;
    }

    //批量把订单项目分班
    public function assignClassByManyOrderItem($data)
    {
        $this->startTrans();
        try {

            foreach ($data as $row) {
                $rs = $this->assignClassByOneOrderItem($row['oi_id'], $row['cid']);
                if ($rs === false) throw new FailResult($this->getErrorMsg());
            }
            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //根据订单项目分班
    public function assignClassByOneOrderItem($oi_id, $cid)
    {
        //--1-- 购买课程信息
        $item = OrderItem::get(['oi_id' => $oi_id]);
        $student_lesson = StudentLesson::get(['sl_id' => $item['sl_id']]);
        if(empty($student_lesson)) return $this->user_error('没有student_lesson');
        if($student_lesson['cid'] > 0) return $this->user_error('购买的课程已经分过班');

        $class = Classes::get(['cid' => $cid]);
        if($student_lesson['lid'] != $class['lid']) return $this->user_error('选择的班级所属课程与购买的课程不符合');

        //--2-- 选择班级的时间表
        $class_schedules = ClassSchedule::all(['cid' => $class['cid']]);

        //--3-- 已上的班级时间表
        //$class_list = Db::name('class')->alias('c')->join('class_student cs', 'c.cid = cs.cid')
        //    ->where('sid', $item['sid'])->select();
        $m_cs = new ClassStudent();
        $cids = $m_cs->where('sid', $item['sid'])->where('status', $m_cs::STATUS_NORMAL)->column('cid');
        $cids = array_unique($cids);
        //$cids = array_column($class_list, 'cid');
        $schedule_list = (new ClassSchedule())->where('cid', 'in', $cids)->select();

        //--4-- 班级时间是否冲突
        foreach($schedule_list as $row1) {
            $week_day1 = $row1['week_day'];
            $int_start_hour1 = format_int_hour($row1['int_start_hour']);
            $int_end_hour1 = format_int_hour($row1['int_end_hour']);

            foreach($class_schedules as $row2) {
                $week_day2 = $row2['week_day'];
                $int_start_hour2 = format_int_hour($row2['int_start_hour']);
                $int_end_hour2 = format_int_hour($row2['int_end_hour']);
                if($week_day1 == $week_day2 && $int_start_hour1 >= $int_start_hour2 &&
                    $int_start_hour1 <= $int_end_hour2) {
                    return $this->user_error("选择的班级跟已经上的班级时间冲突".$row1['int_start_hour'].'-'.$row2['int_end_hour']);
                }
            }
        }

        //--5-- 分班
        $rs = $student_lesson->updateClass($student_lesson, $cid);
        if($rs === false) return $this->user_error($student_lesson->getErrorMsg());

        return true;
    }

    /**
     * 学生班课的停课操作
     */
    public function stopClassStudent(array $info)
    {
        if (!empty($info['cancel_stop'])) {
            /*撤销停课*/
            $this->data('stop_int_day', 0)->data('recover_int_day', 0)->save();
        } else {
            /*停课或编辑停课日期*/
            $sid = $this->getData('sid');
            $cid = $this->getData('cid');
            $student_attendance = (new StudentAttendance)->where('sid', $sid)
                ->where('cid', $cid)
                ->order('int_day', 'desc')
                ->limit(0, 1)
                ->find();
            if ($student_attendance) {
                //最近一次考勤日期
                $last_attendance_int_day = intval($student_attendance['int_day']);
            } else {
                //入班日期
                $in_time_int_day = intval(date('Ymd', $this->getData('in_time')));
            }
            if (!empty($last_attendance_int_day)) {
                if ($info['stop_int_day'] <= $last_attendance_int_day) {
                    throw new Exception('停课日期必须要大于学生在该班级的最近一次考勤日期！');
                }
            } elseif (!empty($in_time_int_day)) {
                if ($info['stop_int_day'] <= $in_time_int_day) {
                    throw new Exception('停课日期必须要大于学生在该班级的入班日期！');
                }
            }
            if (!empty($this->getData('stop_int_day'))) {
                $info['edit'] = true;
            }

            $data = [];
            $data['stop_int_day'] = $info['stop_int_day'];
            $default_recover_int_day = 21000101;/*给停课日期设置一个默认的复课日期方便查询*/
            if (empty($this->getData('recover_int_day'))) {
                $data['recover_int_day'] = $default_recover_int_day;
            } elseif ($info['stop_int_day'] >= $this->getData('recover_int_day')) {
                $data['recover_int_day'] = $default_recover_int_day;
            }
//            if (!empty($this->getData('recover_int_day')) && $info['stop_int_day'] >= $this->getData('recover_int_day')) {
//                $data['recover_int_day'] = 0;
//            }
            $rs = $this->allowField(true)->save($data);
            if ($rs === false) {
                throw new Exception('停课失败!');
            }
        }
        ClassLog::addStudentStopLog($this, $info);
        return $this;
    }

    /**
     * 学生班课的复课操作
     * @return boolean
     */
    public function recoverClassStudent(array $info)
    {
        if (empty($info['recover_int_day'] )) {
            $info['recover_int_day'] = intval(date('Ymd', strtotime($info['recover_date'])));
        }
        $is_end = $this->getData('is_end');
        $status = $this->getData('status');
        if ($status == self::STATUS_CLASS_TRANSFER) {
            /*转班之后无法复课：停课，转班，无法复课*/
            if (!empty($info['sl_id'])) {
                return true;
            } else {
                throw new Exception('该同学的入班记录为转班状态无法复课!');
            }
        }

        if ($is_end == self::IS_END_YES) {
            /*结课之后无法复课：停课，结课，无法复课*/
            if (!empty($info['sl_id'])) {
                return true;
            } else {
                throw new Exception('该同学的入班记录为结课状态无法复课!');
            }
        }

        $stop_int_day = $this->getData('stop_int_day');
        if (empty($stop_int_day)) {
            throw new Exception('该同学的入班记录为正常状态！');
        } elseif ($stop_int_day >= $info['recover_int_day']) {
            throw new Exception('复课日期不能小于停课日期!');
        }

        $sid = $this->getData('sid');
        $cid = $this->getData('cid');
        if (!empty($this->getData('recover_int_day')) && empty($info['sl_id'])) {
            /*修改复课日期*/
            $attendance = (new StudentAttendance())->where('sid', $sid)
                ->where('cid', $cid)
                ->where('int_day', '>', $stop_int_day)
                ->where('int_day', '<', $info['recover_int_day'])
                ->find();
            if (!empty($attendance)) {
                throw new Exception('停课期间不能存在考勤记录, 新的复课日期不合法!');
            }
            $info['edit'] = true;
        }

        $rs = $this->data('recover_int_day', $info['recover_int_day'])->save();
        if ($rs === false) {
            return false;
        }
        ClassLog::addStudentRecoverLog($this);
        return true;
    }

    /**
     * 学生班课的结课操作
     * @return bool
     */
    public function closeClassStudent()
    {
        if ($this->getData('status') == self::STATUS_CLOSE) {
            return true;
        } else {
            $rs = $this->data('status', self::STATUS_CLOSE)->data('is_end', self::IS_END_YES)->save();
            if ($rs === false) {
                return false;
            }
            
            $class_student = $this->getData();
            // 解除学员与老师之间的关系
            $class = get_class_info($class_student['cid']);
            $type = EmployeeStudent::TYPE_CLASS;
            $lid = $class['lid'];
            $cid = $class['cid'];
            $info = array(
                'sid' => $class_student['sid'],
                'rid' => EmployeeStudent::EMPLOYEE_TEACHER,
                'eid' => $class['teach_eid']
            );
            EmployeeStudent::deleteEmployeeStudentRelationship($info,$type,$lid,$cid);

            // 解除学员与助教之间的关系
            if($class['second_eids']){
                $second_eids = explode(',',$class['second_eids']);
                foreach ($second_eids as $eid) {
                    $info = array(
                        'sid' => $class_student['sid'],
                        'rid' => EmployeeStudent::EMPLOYEE_TA,
                        'eid' => $eid
                    );
                    EmployeeStudent::deleteEmployeeStudentRelationship($info,$type,$lid,$cid);
                }
            }

            return true;
        }
    }

    /**
     * 学生班课的撤销结课操作
     * @return bool
     */
    public function undoCloseClassStudent()
    {
        if ($this->getData('status') == self::STATUS_NORMAL) {
            return true;
        } else {
            $rs = $this->data('status', self::STATUS_NORMAL)->data('is_end', self::IS_END_NO)->save();
            if (false === $rs) {
                return false;
            }

            $class_student = $this->getData();
            // 创建学员与老师之间的关系
            $class = get_class_info($class_student['cid']);
            $type = EmployeeStudent::TYPE_CLASS;
            $lid = $class['lid'];
            $cid = $class['cid'];
            $info = array(
                'sid' => $class_student['sid'],
                'rid' => EmployeeStudent::EMPLOYEE_TEACHER,
                'eid' => $class['teach_eid']
            );
            EmployeeStudent::addEmployeeStudentRelationship($info,$type,$lid,$cid);

            // 创建学员与助教之间的关系
            if($class['second_eids']){
                $second_eids = explode(',',$class['second_eids']);
                foreach ($second_eids as $eid) {
                    $info = array(
                        'sid' => $class_student['sid'],
                        'rid' => EmployeeStudent::EMPLOYEE_TA,
                        'eid' => $eid
                    );
                    EmployeeStudent::addEmployeeStudentRelationship($info,$type,$lid,$cid);
                }
            }

            return true;
        }
    }

    //是否在班级中
    public static function isInClass($cid, $sid)
    {
        $w['cid'] = $cid;
        $w['sid'] = $sid;
        $w['status'] = self::STATUS_NORMAL;
        $is_in = (new self())->where($w)->count();
        return $is_in > 0 ? true : false;
    }

    //luo 编辑学生在班级的情况
    public function edit($data)
    {
        if(empty($this->getData())) return true;
        if(isset($data['in_time'])){
            $data['out_time'] = 0;
        }
        $rs = $this->allowField('in_time,out_time,in_way')->save($data);
        if($rs === false) return false;
        
        return true;
    }

    //获取班级的sids,默认是正常的
    public static function GetSidsOfClass($cid, $normal = true, $where = [])
    {
        $self = new self();
        if($normal) {
            $where = [
                'status' => self::STATUS_NORMAL,
                'is_end' => 0,
                'out_time' => ['lt', time()],
            ];
        }
        $sids = $self->where('cid', $cid)->where($where)->column('sid');
        return !empty($sids) ? array_unique($sids) : $sids;
    }

    //批量更新入班信息
    public function updateList($post)
    {
        foreach($post as $row) {
            if(empty($row['cs_id'])) return $this->user_error('cs_id 参数错误');
        }

        try {
            $this->startTrans();
            foreach($post as $row) {
                $class_student = $this->find($row['cs_id']);
                if(empty($class_student)) continue;
                $class_student->in_time = !empty($row['in_time']) ? $row['in_time'] : $class_student->in_time;
                $class_student->out_time = 0 ;
                $rs = $class_student->allowField('in_time,out_time')->save();
                if($rs === false) throw new FailResult($this->getErrorMsg());
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

}