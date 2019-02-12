<?php
/**
 * Author: luo
 * Time: 2017-10-26 10:11
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;
use think\Validate;

class StudentLessonHour extends Base
{

    public static $detail_fields = [
        ['type'=>'index','width'=>60,'align'=>'center'],
        ['title'=>'校区','key'=>'bid','align'=>'center'],
        ['title'=>'学员姓名','key'=>'sid','align'=>'center'],
        ['title'=>'课时数','key'=>'lesson_hours','align'=>'center'],
        ['title'=>'课时金额','key'=>'lesson_amount','align'=>'center'],
    ];

    public static $detail_cut_fields = [
        ['type'=>'index','width'=>60,'align'=>'center'],
        ['title'=>'校区','key'=>'bid','align'=>'center'],
        ['title'=>'学员姓名','key'=>'sid','align'=>'center'],
        ['title'=>'扣款金额','key'=>'lesson_amount','align'=>'center'],
        ['title'=>'扣款时间','key'=>'int_day','align'=>'center'],
    ];

    const CHANGE_TYPE_ATTENDANCE = 1; # 考勤
    const CHANGE_TYPE_FREE = 2; # 自由登记课耗
    const CHANGE_TYPE_REFUND = 3; # 退款转化

    protected $append = ['course_name'];

    public function getCourseNameAttr($value,$data){
        $course_name = get_course_name_by_row($data);

        return $course_name;
    }

    public function setIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function setIntStartHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    public function setIntEndHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    public function student()
    {
        return $this->belongsTo('Student', 'sid', 'sid');
    }

    public function cls()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }


    public function createOneRefund($data)
    {
        $res = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if(!$res) return $this->user_error('添加扣款转化失败');

        return $this;
    }

    /*根据学生的一条考勤记录创建对应的课消记录*/
    public static function createConsumeRecordByAttendance(StudentAttendance $attendance)
    {
        if (empty($attendance['is_consume'])) {
            //不计课时不添加记录
            return false;
        }
        $w = [];
//        $w['sid']     = $attendance['sid'];
        $w['satt_id'] = $attendance['satt_id'];
        $consume_record = StudentLessonHour::get($w);
        if ($consume_record) {
            throw new Exception('该次考勤已经存在课消记录了!');
        }

//
//        $w = [];
//        $w['sid']          = $attendance['sid'];
//        $w['sl_id']        = $attendance['sl_id'];
//        $w['int_day']      = $attendance['int_day'];
//        $w['int_end_hour'] = $attendance['int_end_hour'];
//        $w['int_start_hour'] = $attendance['int_start_hour'];
//        $consume_record = StudentLessonHour::get($w);
//        if ($consume_record) {
//            throw new Exception('该次考勤已经存在课消记录了!');
//        }
        $data = [];
        $table_field = self::getTableInfo()['fields'];
        $omit_field  = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];
        foreach ($table_field as $key => $value) {
            if (in_array($value, $omit_field)) {
                unset($table_field[$key]);
            }
        }
        foreach ($table_field as $field) {
            if (isset($attendance[$field])) {
                $data[$field] = $attendance[$field];
            }
        }

        /*单次课的课时*/
        $data['lesson_hours'] = $attendance['lesson']['unit_lesson_hours'];

        /*单次课总时间长度（单位：分钟）*/
        $data['lesson_minutes'] = $attendance['lesson']['unit_lesson_minutes'];

        /*课耗money*/
        $data['lesson_amount'] = $attendance['consume'];
        $data['oi_id'] = $attendance['oi_id'];
        $model = new self();
        $model->allowField(true)->isUpdate(false)->save($data);
        return $model;
    }

    public static function rollbackConsume(StudentAttendance $attendance)
    {
        if (empty($attendance['is_consume'])) {
            return;
        }
        $w = [];
//        $w['sid']     = $attendance['sid'];
        $w['satt_id'] = $attendance['satt_id'];
//        $w['sl_id'] = $attendance['sl_id'];
//        $w['ca_id'] = $attendance['ca_id'];
//        $w['int_day'] = $attendance['int_day'];
//        $w['int_start_hour'] = $attendance['int_start_hour'];
//        $w['int_end_hour']   = $attendance['int_end_hour'];
        $model = self::get($w);
        if (empty($model)) {
            throw new Exception('resource[student_lesson_hour] not found');
        }
        $model->delete();
    }    
        
    public function countConsumeOfDay($day) 
    {
        $day = format_int_day($day);
        $num = $this->scope('bid')->where('int_day', $day)->count();
        return $num;
    }

    /**
     * 按班级批量登记学员课消
     * @param array $post
     * @return bool
     */
    public function addMultiConsume(array $post)
    {
        try {
            $this->startTrans();
            foreach ($post as $row) {
                if(empty($row['lesson_hours']) || $row['lesson_hours'] <= 0) continue;
                $row['sl_id'] = strval($row['sl_id']);
                if(strpos($row['sl_id'],',') !== false){
                    $sl_ids = explode(',',$row['sl_id']);
                    $total_lesson_hours = $row['lesson_hours'];
                    $input_row = $row;
                    foreach($sl_ids as $sl_id){
                        if($total_lesson_hours <= 0){
                            break;
                        }
                        $sl_info = get_sl_info($sl_id);
                        if(!$sl_info){
                            throw new FailResult('课时ID:'.$sl_id.'不存在!');
                        }
                        $input_row['sl_id'] = $sl_id;
                        if($sl_info['remain_lesson_hours'] < $total_lesson_hours){
                            $input_row['lesson_hours'] = $sl_info['remain_lesson_hours'];
                        }else{
                            $input_row['lesson_hours'] = $total_lesson_hours;
                        }

                        $result = $this->addConsume($input_row);
                        if(false === $result){
                            throw new FailResult($this->getErrorMsg());
                        }

                        $total_lesson_hours -= $input_row['lesson_hours'];
                    }
                }else{
                    $rs = $this->addConsume($row);
                    if($rs === false) throw new FailResult($this->getErrorMsg());
                }

            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

    /**
     * 从钱包产生违约课消
     * @param $input
     * @return bool
     */
    public function createConsumeFromMoney($input){
        $need_fields = ['sid','lesson_amount','remark'];

        if(!$this->checkInputParam($input,$need_fields)){
            return false;
        }

        if(empty($input['int_day']) ){
            $input['int_day'] = int_day(time());
        }

        if($input['lesson_amount'] <= 0 ){
            return $this->user_error('课消金额不能为0');
        }

        $sid = $input['sid'];
        $lesson_amount = floatval($input['lesson_amount']);

        $m_student = Student::get($sid);

        if(!$m_student){
            return $this->user_error('学员ID不存在!');
        }

        if($m_student->money < $lesson_amount){
            return $this->user_error('学员电子钱包余额小于课消金额!');
        }

        $this->startTrans();
        try {
            //产生课消记录
            $slh_info = [];
            $fixed_info = [
                'consume_type'  =>  3,
                'sl_id'         =>  0,
                'source_type'   =>  2,
                'eid'           =>  0,
                'second_eid'    =>  0,
                'edu_eid'       =>  0,
            ];
            $slh_info = array_merge($input,$fixed_info);

            $slh_info['change_type'] = self::CHANGE_TYPE_FREE;
            $slh_info['is_pay'] = 1;

            $result = $this->data([])->allowField(true)->isUpdate(false)->save($slh_info);

            if(!$result){
                $this->rollback();
                return $this->sql_add_error('student_lesson_hour');
            }

            $slh_info['slh_id'] = $this->slh_id;
            //电子钱包余额变动
            $update_student['money'] = $m_student->money - $lesson_amount;

            $smh = [];
            $smh['sid'] = $sid;
            $smh['business_type'] = StudentMoneyHistory::BUSINESS_TYPE_CONSUME;
            $smh['business_id']   = $slh_info['slh_id'];
            $smh['before_amount'] = $m_student->money;
            $smh['after_amount']  = $update_student['money'];
            $smh['amount']        = $lesson_amount;
            $smh['remark']        = '违约课消:'.$input['remark'];



            $m_smh = new StudentMoneyHistory();

            $result = $m_smh->save($smh);

            if(!$result){
                $this->rollback();
                return $this->sql_add_error('student_money_history');
            }

            //更改学员剩余课时数
            $m_student->money = $update_student['money'];
            $result = $m_student->save();

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('student');
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return true;
    }

    /**
     * 登记学员课消
     * @param $post
     * @return bool
     */
    public function addStudentConsume($input){
        $need_fields = ['sid','sl_id','lesson_hours','int_day','lesson_amount','remark'];

        if(!$this->checkInputParam($input,$need_fields)){
            return false;
        }
        $input['int_day'] = format_int_day($input['int_day']);

        $input['source_type'] = isset($input['source_type'])?intval($input['source_type']):1;        //默认扣课时
        $input['consume_type'] = isset($input['consume_type'])?intval($input['consume_type']):0;     //默认课时课消

        $w_slh['int_day'] = $input['int_day'];
        $w_slh['sid'] = $input['sid'];
        $w_slh['consume_type'] = $input['consume_type'];
        $w_slh['change_type'] = self::CHANGE_TYPE_FREE;

        if(!empty($input['int_start_hour'])){
            $w_slh['int_start_hour'] = format_int_hour($input['int_start_hour']);
        }

        $model = new self();
        $ex_slh = $model->where($w_slh)->find();
        if($ex_slh){
            if(!empty($input['int_start_hour'])){
                return $this->user_error('同一学员在同一时段已存在登记课消记录!');
            }
            return $this->user_error('同一学员在同一日期已存在登记课消记录,如果需要登记同一日期的课消记录，可进一步选择课消时段!');
        }

        if($input['consume_type'] == 3 && $input['source_type'] == 2){
            //从电子钱包产生违约课消
            return $this->createConsumeFromMoney($input);
        }else{

            $this->startTrans();
            try {
                $result = $this->addConsume($input);
                if (false === $result) {
                    throw new FailResult($this->getErrorMsg());
                }
            }catch(\Exception $e){
                $this->rollback();
                return $this->exception_error($e);
            }
            $this->commit();
        }

        return true;
    }

    //自由登记学生课耗  备份
    public function addConsume_bakup($post)
    {
        //--1-- 验证数据
        $rule = [
            'sid' => 'require',
            'sl_id' => 'require',
            'lesson_hours' => 'require|number',
        ];
        $validate = new Validate();
        $rs = $validate->check($post, $rule);
        if($rs !== true) return $this->user_error($validate->getError());

        //--2-- 学生课时数据
        /** @var StudentLesson $student_lesson */
        $student_lesson = StudentLesson::get($post['sl_id']);
        if(empty($student_lesson)) return $this->user_error('student_lesson不存在');
        if($post['lesson_hours'] > $student_lesson['remain_lesson_hours']) return $this->user_error('扣减课时不能大于剩余课时');

        //--3-- 课时相关的订单
        $m_oi = new OrderItem();
        $oi_list = $m_oi->where('sl_id', $student_lesson['sl_id'])->order('oi_id asc')
            ->field('oid,oi_id,sl_id,gtype,subtotal,origin_lesson_hours,paid_amount,present_lesson_hours,unit_lesson_hour_amount')
            ->select();

        $m_order = new Order();
        foreach($oi_list as $key => $row) {
            $tmp_order_num = $m_order->where('oid', $row['oid'])->where('order_status < 10')->count();
            if($tmp_order_num <= 0) unset($oi_list[$key]);
        }

        $oi_list = empty($oi_list) ? [] : collection($oi_list)->toArray();

        $m_slil = new StudentLessonImportLog();
        $student_lesson_import_log = $m_slil->where('sl_id', $student_lesson['sl_id'])->select();
        $price_list = array_merge($oi_list, $student_lesson_import_log);

        $student_hour_data = array_merge($student_lesson->toArray(), $post);
        $student_hour_data['change_type'] = self::CHANGE_TYPE_FREE;

        try {
            $this->startTrans();

            $to_calc_hours = $post['lesson_hours'];
            $use_lesson_hours = $student_lesson['use_lesson_hours'];
            $m_elh = new EmployeeLessonHour();

            //student_lesson 可能有多个订单
            foreach ($price_list as $item) {
                //如果需要记录的课时小于0，直接跳出
                if ($to_calc_hours <= 0) break;

                if(isset($item['oi_id'])) {
                    $item_lesson_hours = $item['origin_lesson_hours'] + $item['present_lesson_hours'];
                } else {
                    $item_lesson_hours = $item['lesson_hours'];
                }
                //如果已经消耗的课时已经大于item的课时，则跳过
                if ($item_lesson_hours <= $use_lesson_hours) {
                    $use_lesson_hours -= $item_lesson_hours;
                    continue;
                }

                //item课时减掉已经消耗的为剩下可用于计算的
                $item_lesson_hours -= $use_lesson_hours;
                //手动记录一个课耗，可能数量分布在两个item，则有两个课耗记录
                $tmp_calc_hours = $to_calc_hours >= $item_lesson_hours ? $item_lesson_hours : $to_calc_hours;
                $student_hour_data['lesson_hours'] = $tmp_calc_hours;
                $student_hour_data['lesson_amount'] = $student_hour_data['lesson_hours'] * $item['unit_lesson_hour_amount'];
                $student_hour_data['oi_id'] = isset($item['oi_id']) ? $item['oi_id'] : 0;
                $student_hour_data['slil_id'] = isset($item['slil_id']) ? $item['slil_id'] : 0;
                $rs = $this->data([])->allowField(true)->isUpdate(false)->save($student_hour_data);
                if ($rs === false) return false;

                $employee_hour_data = $student_hour_data;
                $employee_hour_data['student_nums'] = 1;
                $employee_hour_data['total_lesson_hours'] = $student_hour_data['lesson_hours'];
                $employee_hour_data['total_lesson_amount'] = $student_hour_data['lesson_amount'];
                $employee_hour_data['payed_lesson_amount'] = isset($item['slil_id']) || $item['paid_amount'] > 0
                    ? $student_hour_data['lesson_amount'] : 0;
                $rs = $m_elh->allowField(true)->data([])->isUpdate(false)->save($employee_hour_data);
                if($rs === false) return $this->user_error($m_elh->getErrorMsg());

                $use_lesson_hours -= $student_hour_data['lesson_hours'];
                $to_calc_hours -= $tmp_calc_hours;
            }

            $lesson = Lesson::get($student_lesson['lid']);
            $lesson_times = !empty($lesson) ? ($lesson['unit_lesson_hours'] > 0 ? $post['lesson_hours'] / $lesson['unit_lesson_hours'] : 0) : 0;

            //更新student_lesson的使用课时与剩余课时
            $student_lesson->use_times = $student_lesson->use_times + $lesson_times;
            $student_lesson->remain_times = min_val($student_lesson->remain_times - $lesson_times);
            $student_lesson->use_lesson_hours = $student_lesson->use_lesson_hours + $post['lesson_hours'];
            $student_lesson->remain_lesson_hours = $student_lesson->remain_lesson_hours - $post['lesson_hours'];

            $rs = $student_lesson->allowField('use_times,remain_times,use_lesson_hours,remain_lesson_hours,remain_arrange_times')
                ->save();
            if ($rs === false) throw new FailResult('更新相关剩余课次数失败');

            (new Student())->updateLessonHours($post['sid']);

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //自由登记学生课耗
    public function addConsume($post)
    {
        //--1-- 验证数据
        $rule = [
            'sid' => 'require',
            'sl_id' => 'require',
            'lesson_hours' => 'require|number',
        ];
        $validate = new Validate();
        $rs = $validate->check($post, $rule);
        if($rs !== true) return $this->user_error($validate->getError());

        $post['sl_id'] = intval($post['sl_id']);
        if($post['lesson_hours'] == 0 || $post['sl_id'] == 0){
            return true;
        }
        //--2-- 学生课时数据
        /** @var StudentLesson $student_lesson */
        $student_lesson = StudentLesson::get($post['sl_id']);
        if(empty($student_lesson)) return $this->user_error('student_lesson不存在');
        if($post['lesson_hours'] > $student_lesson['remain_lesson_hours']) return $this->user_error('扣减课时不能大于剩余课时');
        
        $cid = $student_lesson['cid'];
        $cinfo = get_class_info($cid);

        if(empty($post['int_day']) ){
            $post['int_day'] = int_day(time());
        }

        $post['remark'] = isset($post['remark'])?safe_str($post['remark']):'';

        try {
            $this->startTrans();

            $consume_lesson_hour = $post['lesson_hours'];
            $consume_lesson_amount = $student_lesson->getConsumeLessonAmount($consume_lesson_hour);
            $student_hour_data = array_merge($student_lesson->toArray(), $post);

	        if($cinfo){
	            $student_hour_data['eid'] = $cinfo['teach_eid'];
	            $student_hour_data['second_eid'] = $cinfo['second_eid'];
	            $student_hour_data['second_eids'] = explode(',',$cinfo['second_eids']);
	            $student_hour_data['edu_eid'] = $cinfo['edu_eid'];
	        }

            $student_hour_data['change_type'] = self::CHANGE_TYPE_FREE;
            $student_hour_data['lesson_hours'] = $consume_lesson_hour;
            $student_hour_data['lesson_amount'] = $consume_lesson_amount;
            $student_hour_data['oi_id'] = $student_lesson->consume_oi_id;
            $student_hour_data['is_pay'] = 1;

            $student_hour_data['source_type'] = isset($post['source_type'])?intval($post['source_type']):1;        //默认扣课时
            $student_hour_data['consume_type'] = isset($post['consume_type'])?intval($post['consume_type']):0;     //默认课时课消


            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($student_hour_data);
            if ($rs === false) return false;

            if($post['eid'] > 0) {
                $m_elh = new EmployeeLessonHour();
                $employee_hour_data = $student_hour_data;
		
		        if($cinfo){

	                $employ_hour_data['eid'] = $cinfo['teach_eid'];
	                $employ_hour_data['second_eid'] = $cinfo['second_eid'];
	                $employ_hour_data['second_eids'] = explode(',',$cinfo['second_eids']);
	                $employ_hour_data['edu_eid'] = $cinfo['edu_eid'];
		        }

                $employee_hour_data['slh_id'] = $this->slh_id;
                $employee_hour_data['student_nums'] = 1;
                $employee_hour_data['total_lesson_hours'] = $student_hour_data['lesson_hours'];
                $employee_hour_data['total_lesson_amount'] = $student_hour_data['lesson_amount'];
                $employee_hour_data['payed_lesson_amount'] = $student_hour_data['lesson_amount'];
                $rs = $m_elh->allowField(true)->data([])->isUpdate(false)->save($employee_hour_data);
                if ($rs === false) return $this->user_error($m_elh->getErrorMsg());

            }

            //更新student_lesson的使用课时与剩余课时
            $student_lesson->use_lesson_hours = $student_lesson->use_lesson_hours + $consume_lesson_hour;
            $student_lesson->remain_lesson_hours = $student_lesson->remain_lesson_hours - $consume_lesson_hour;
            $student_lesson->remain_lesson_amount = min_val($student_lesson->remain_lesson_amount - $consume_lesson_amount);

            $rs = $student_lesson->allowField('use_lesson_hours,remain_lesson_hours,remain_lesson_amount')
                ->save();
            if ($rs === false) throw new FailResult('更新相关剩余课次数失败');

            (new Student())->updateLessonHours($post['sid']);

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function delStudentLessonHour()
    {
        $slh_info = $this->getData();
        if(empty($slh_info)) return $this->user_error('课耗数据为空');
        $this->startTrans();

        try {

            //更新student_lesson的使用课时与剩余课时
            if($slh_info['sl_id'] > 0) {
                /** @var StudentLesson $student_lesson */
                $student_lesson = StudentLesson::get($this->getData('sl_id'));

                $lesson_hours = $this->getData('lesson_hours');
                $lesson_amount = $this->getData('lesson_amount');

                $student_lesson->use_lesson_hours    = $student_lesson->use_lesson_hours - $lesson_hours;
                $student_lesson->remain_lesson_hours +=  $lesson_hours;
                $student_lesson->remain_lesson_amount += $lesson_amount;
                $rs = $student_lesson->allowField('use_times,remain_times,use_lesson_hours,remain_lesson_hours,remain_arrange_times,remain_lesson_amount')
                    ->save();
                if ($rs === false) throw new FailResult('更新相关剩余课次数失败');

                (new Student())->updateLessonHours($student_lesson['sid']);

            }

            $source_type = $this->getData('source_type');
            if($source_type == 2){
                //扣除钱包余额
                $w_smh['business_id'] = $this->getData('slh_id');
                $w_smh['business_type'] = StudentMoneyHistory::BUSINESS_TYPE_CONSUME;
                $w_smh['sid'] = $slh_info['sid'];

                $m_smh = new StudentMoneyHistory();
                $smh   = $m_smh->where($w_smh)->find();

                if($smh){
                    $result = $smh->rollbackHistory();
                    if(!$result){
                        $this->rollback();
                        return $this->user_error($smh->getError());
                    }
                }
            }

            //需要关联删除教师课耗

            $w_elh['slh_id'] = $slh_info['slh_id'];
            $result = $this->m_employee_lesson_hour->where($w_elh)->delete();
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('employee_lesson_hour');
            }
            $result = $this->delete();
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('student_lesson_hour');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 取消课时消耗
     * @param $satt_id
     * @return bool
     */
    public function rollbackLessonHour($satt_id)
    {
        $w['satt_id'] = $satt_id;
        $slh_list = get_table_list('student_lesson_hour',$w);
        if(!$slh_list){
            return true;
        }
        $this->startTrans();
        try {
            $rollback_money = 0.00;
            $sid = 0;
            $int_start_hour = 0;
            $int_end_hour = 0;
            $int_day = 0;
            foreach($slh_list as $slh_info) {
                $sid = $slh_info['sid'];
                $int_day = $slh_info['int_day'];
                $int_start_hour = $slh_info['int_start_hour'];
                $int_end_hour = $slh_info['int_end_hour'];
                if(isset($slh_info['source_type']) && $slh_info['source_type'] == 2){
                    $rollback_money += $slh_info['lesson_amount'];
                }

                $sl_id = $slh_info['sl_id'];
                if ($sl_id > 0) {
                    $m_sl = StudentLesson::get($sl_id);
                    if ($m_sl) {
                        $result = $m_sl->rollbackLessonHour($slh_info['lesson_hours'],$slh_info['lesson_amount']);
                        if (!$result) {
                            exception($m_sl->getError());
                        }
                    }
                }

                $w_slh['slh_id'] = $slh_info['slh_id'];
                $result = $this->where($w_slh)->delete(true);
                if (false === $result) {
                    $this->rollback();
                    return $this->sql_delete_error('student_lesson_hour');
                }
            }

            if($rollback_money > 0){
                //返还钱包余额
                $m_student = Student::get($sid);
                $remark = sprintf("撤销考勤返回余额:%s(%s %s~%s)",$rollback_money,$int_day,$int_start_hour,$int_end_hour);
                $result = $m_student->rollbackConsumeMoney($rollback_money,$remark);
                if(!$result){
                    $this->rollback();
                    return $this->user_error($m_student->getError());
                }
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 付款课时消耗记录
     * @param $sl_id
     * @param bool $refresh 是否刷新student_lesson
     * @param int $slh_id
     * @return bool
     */
    public function pay($sl_id,$refresh = true,$slh_id = 0){
        $pay_lesson_hour = 0.00;
        if($slh_id == 0){
            $slh_info = $this->getData();
        }else{
            $slh_info = get_slh_info($slh_id);
            $this->data($slh_info);
        }

        $sl_info = get_sl_info($sl_id);

        if(!$sl_info){
            return $this->user_error('sl_id不存在!');
        }

        /*
         * 多余，指定的课时付款，不需要判断是否符合

        if(!is_sl_matched($sl_info,$slh_info)){
            return $pay_lesson_hour;
        }
        */


        if($refresh && $sl_info['remain_lesson_hours'] < $slh_info['lesson_hours']){
            return $pay_lesson_hour;
        }
        $m_student_lesson = new StudentLesson();
        $this->startTrans();
        try {
            $slh_info['lesson_amount'] = $m_student_lesson->getUnitLessonHourAmount($sl_id);
            $this->is_pay = 1;
            $this->sl_id  = $sl_id;
            $this->lesson_amount = $slh_info['lesson_amount'];

            $result = $this->save();

            if (false === $result) {
                $this->rollback();
                return $this->sql_save_error('student_lesson_hour');
            }

            //更新employee_lesson_hour
            $w_elh_update['catt_id'] = $slh_info['catt_id'];
            $m_elh = new EmployeeLessonHour();
            $result = $m_elh->where($w_elh_update)->setInc('payed_lesson_amount',$slh_info['lesson_amount']);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('employee_lesson_hour');
            }

            if($refresh){
                $update_sl['remain_lesson_hours'] = $sl_info['remain_lesson_hours'] - $slh_info['lesson_hours'];
                $update_sl['use_lesson_hours'] = $sl_info['use_lesson_hours'] + $slh_info['lesson_hours'];

                $w_sl['sl_id'] = $sl_id;

                $m_sl = new StudentLesson();
                $result = $m_sl->save($update_sl,$w_sl);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('student_lesson');
                }

                (new Student())->updateLessonHours($this->sid);
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return $slh_info['lesson_hours'];
    }

    //编辑课耗
    public function updateStudentLessonHour($update_data)
    {
        if(empty($this->getData())) return $this->user_error('模型数据错误');

        $sl_id = $this->getData('sl_id');
        if(empty($sl_id)) return $this->user_error('sl_id错误');

        $old_lesson_hours = $this->getData('lesson_hours');

        $student_lesson = StudentLesson::get($sl_id);
        $change_lesson_hours = $update_data['lesson_hours'] - $old_lesson_hours;

        //如果是增加课时，判断课时是否够扣
        if($change_lesson_hours > 0) {
             if(empty($student_lesson) || $student_lesson['remain_lesson_hours'] < $change_lesson_hours) {
                 return $this->user_error('课时不存在或者剩余课时不够');
             }
        }

        try {
            $this->startTrans();

            if($change_lesson_hours != 0) {
                $update_student_lesson_data = [
                    'use_lesson_hours'     => $student_lesson->use_lesson_hours + $change_lesson_hours,
                    'remain_lesson_hours'  => $student_lesson->remain_lesson_hours - $change_lesson_hours,
                    'remain_arrange_hours' => $student_lesson->remain_arrange_hours - $change_lesson_hours,
                ];
                $rs = $student_lesson->save($update_student_lesson_data);
                if($rs === false) throw new FailResult($student_lesson->getErrorMsg());
            }

            $rs = $this->allowField('lesson_hours,lesson_amount')->isUpdate(true)->save($update_data);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function updateLessonAmount($sl_id,$oi_id)
    {
        $w = [
            'sl_id' => $sl_id,
            'oi_id' => $oi_id
        ];
        $lesson_hour_list = $this->where($w)->select();
        if ($lesson_hour_list){
            $mStudentLesson = new StudentLesson();
            $unit_lesson_hour_amount = $mStudentLesson->getUnitLessonHourAmount($sl_id,$oi_id);
            foreach ($lesson_hour_list as $lesson_hour){
                $update['lesson_amount'] = $unit_lesson_hour_amount * $lesson_hour['lesson_hours'];
                $w_slh['slh_id'] = $lesson_hour['slh_id'];
                $result = $this->save($update,$w_slh);
                if (false === $result){
                    return $this->sql_save_error('student_lesson_hour');
                }
            }
        }

        return true;
    }

}
