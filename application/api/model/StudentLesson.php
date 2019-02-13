<?php
/**
 * Author: luo
 * Time: 2017-11-06 19:24
**/

namespace app\api\model;

use app\api\controller\StudentLogs;
use app\common\exception\FailResult;
use think\Exception;
use think\Log;

class StudentLesson extends Base
{
    const AC_STATUS_NO   = 0; //未分班
    const AC_STATUS_SOME = 1; //部分分班
    const AC_STATUS_ALL  = 2; //完成分班

    const LESSON_STATUS_NO   = 0; //未开始上课
    const LESSON_STATUS_ING  = 1; //上课中
    const LESSON_STATUS_DONE = 2; //已经结课

    const HANDLE_STUDENT_LESSON_HOURS_REFUND = 1;  # 退款处理学生课时
    const HANDLE_STUDENT_LESSON_HOURS_TRANSFER = 2;  # 结转处理学生课时
    const HANDLE_STUDENT_LESSON_HOURS_DELETE_BILL = 3;  # 报废收据减少总的课时
    const HANDLE_STUDENT_LESSON_HOURS_OTHER = 4;  # 其他处理学生课时，如删除order_item,考勤
    const HANDLE_STUDENT_LESSON_HOURS_DELETE_REFUND = 5;  # 取消退款处理课时
    const HANDLE_STUDENT_LESSON_HOURS_DELETE_TRANSFER = 6;  # 取消退款处理课时

    protected $append = ['expire_time_text','total_lesson_hours'];

    public $type = [
        'last_attendance_time' => 'timestamp',
    ];

    public $consume_oi_id = 0;          //消耗的order_item id ,默认为0，getConsumeLessonAmount函数用
    public $is_present = false;         //是否赠送，获得课消金额时用到
    public $is_found_oi = false;        //是否找到对应的order_item,获得课消金额时用到

    protected static function init()
    {
        parent::init();
    }

    /**
     * 根据排课记录获得学员课时
     * @param $sid
     * @param $ca_info
     */
    public static function GetStudentLessonByCa($sid,$ca_info){
        $w['lesson_status'] = ['LT',self::LESSON_STATUS_DONE];
        $w['sid'] = $sid;

        $empty_student_lesson = [
            'sid'=>$sid,
            'sl_id'=>0,
            'lid'=>0,
            'cid'=>0,
            'sj_ids'=>0,
            'fit_grade_start'=>0,
            'fit_grade_end'=>0,
            'price_type'=>2,            //默认按课时收费，3为按时间收费
            'lesson_hours'=>0.00,
            'lesson_amount' => 0.00,
            'use_lesson_hours'=>0.00,
            'remain_lesson_hours'=>0.00,
            'remain_lesson_amount'=>0.00,
            'refund_lesson_hours'=>0.00,
            'transfer_lesson_hour'=>0.00,
            'present_lesson_hours'=>0.00,
            'import_lesson_hours'=>0.00,
            'trans_in_lesson_hours'=>0.00,
            'trans_out_lesson_hours'=>0.00,
            'expire_time'=>0,
            'is_expired' => 0,//是否过期，1为过期，0为未到期
            'lesson_name'=>'',//课程名称
        ];

        $sl_list = get_table_list('student_lesson',$w,[],'create_time ASC');

        if(!$sl_list){
            return $empty_student_lesson;
        }

        $is_makeup_class_ca   = false;
        $found_student_lesson = false;
        $student_lesson = [];
        $sl_fields = [
            'sid',
            'sl_id',
            'lid',
            'cid',
            'sj_ids',
            'fit_grade_start',
            'fit_grade_end',
            'price_type',
            'lesson_hours',
            'lesson_amount',
            'use_lesson_hours',
            'remain_lesson_hours',
            'remain_lesson_amount',
            'refund_lesson_hours',
            'transfer_lesson_hours',
            'import_lesson_hours',
            'present_lesson_hours',
            'trans_in_lesson_hours',
            'trans_out_lesson_hours',
            'expire_time'
        ];

        if($ca_info['cid'] > 0){

            $class_info = get_class_info($ca_info['cid']);
            if($ca_info['sj_id'] == 0){
                $ca_info['sj_id'] = $class_info['sj_id'];
            }
            if($class_info['class_type'] == 1){
                $is_makeup_class_ca = true;
            }
            foreach($sl_list as $sl){
                if($sl['cid'] > 0 && $sl['cid'] == $ca_info['cid']){
                    array_copy($student_lesson,$sl,$sl_fields);
                    break;
                }
            }

            if(empty($student_lesson)){

                if($class_info['class_type'] == 1){ //如果是补课班级，那么只要年级段匹配即可
                    $grade = $class_info['grade'];
                    foreach($sl_list as $sl){
                        $arr_sl_grade = get_student_lesson_grade($sl);
                        if($grade < 20){        //如果是默认的年级 1-12
                            if($grade >= $arr_sl_grade['fit_grade_start'] && $grade <= $arr_sl_grade['fit_grade_end']){
                                array_copy($student_lesson,$sl,$sl_fields);
                                break;
                            }
                        }else{
                            if($grade == $arr_sl_grade['fit_grade_start']){
                                array_copy($student_lesson,$sl,$sl_fields);
                                break;
                            }
                        }
                    }
                }
            }

            if(empty($student_lesson)){
                $catt_config = user_config('params.class_attendance');
                $sl_bcu_subject = $catt_config['sl_bcu_subject'];
                if($sl_bcu_subject == 1){
                    foreach($sl_list as $sl){
                        $matched = is_sj_id_matched($sl,$class_info);
                        if($sl['remain_lesson_hours'] > 0 && $matched){
                            array_copy($student_lesson,$sl,$sl_fields);
                            break;
                        }
                    }
                }
            }

            if(!empty($student_lesson)){
                $found_student_lesson = true;
            }

        }

        if(!$found_student_lesson && $ca_info['lid'] > 0){
            foreach($sl_list as $sl){
                if(($sl['price_type'] == 3 || $sl['remain_lesson_hours'] > 0) && $sl['lid'] == $ca_info['lid']){
                    array_copy($student_lesson,$sl,$sl_fields);
                    break;
                }
            }

            if(!empty($student_lesson)){
                $found_student_lesson = true;
            }
        }
        if(!$found_student_lesson && $ca_info['sj_id'] > 0){
            foreach($sl_list as $sl){
                $matched = is_sl_matched($sl,$ca_info);
                if($matched){
                    array_copy($student_lesson,$sl,$sl_fields);
                    break;
                }
            }
            if(!empty($student_lesson)){
                $found_student_lesson = true;
            }
        }

        if($found_student_lesson){

            if($student_lesson['expire_time'] >0){
                $now_time = time();
                if($student_lesson['expire_time'] < $now_time){
                    $student_lesson['is_expired'] = 1;
                    $student_lesson['remain_days'] = day_diff($student_lesson['expire_time'],$now_time);
                }else{
                    $student_lesson['is_expired'] = 0;
                    $student_lesson['remain_days'] = day_diff($now_time,$student_lesson['expire_time']);
                }
            }

            if($is_makeup_class_ca) {
                $student_lesson['lesson_name'] = get_student_lesson_lesson_name($student_lesson);
            }
            //如果剩余课时为0，查一下是否有同一课程的课时
            if($student_lesson['remain_lesson_hours'] == 0){
                foreach($sl_list as $sl){
                    if($sl['sl_id'] != $student_lesson['sl_id'] && $sl['remain_lesson_hours'] >0  && $sl['lid'] == $ca_info['lid']){
                        array_copy($student_lesson,$sl,$sl_fields);
                        break;
                    }
                }
            }
            return $student_lesson;
        }

        return $empty_student_lesson;
    }

    /**
     * 根据学员考勤记录获得学员课时
     * @param $sid
     * @param $satt_info
     */
    public function getBySattInfo($sid,$satt_info){
        $w['lesson_status'] = ['LT',self::LESSON_STATUS_DONE];
        $w['sid'] = $sid;

        if(isset($satt_info['ca_id']) && $satt_info['ca_id'] > 0){
            $ca_info = get_ca_info($satt_info['ca_id']);
            $sl_info = self::GetStudentLessonByCa($sid,$ca_info);
            if($sl_info['sl_id'] == 0){
                return false;
            }
            return new StudentLesson($sl_info);
        }


        $sl_list = $this->where($w)->order('create_time ASC')->select();

        if(!$sl_list){
            return false;
        }

        if($satt_info['cid'] > 0){
            foreach($sl_list as $sl){
                if($sl['cid'] > 0 && $sl['cid'] == $satt_info['cid']){
                    return $sl;
                }
            }
            $class_info = get_class_info($satt_info['cid']);
            if($class_info['class_type'] == 1){ //如果是补课班级，那么只要年级段匹配即可
                $grade = $class_info['grade'];
                foreach($sl_list as $sl){
                    $arr_sl_grade = get_student_lesson_grade($sl);
                    if($grade < 20){        //如果是默认的年级 1-12
                        if($grade >= $arr_sl_grade['fit_grade_start'] && $grade <= $arr_sl_grade['fit_grade_end']){
                            return $sl;
                        }
                    }else{
                        if($grade == $arr_sl_grade['fit_grade_start']){
                            return $sl;
                        }
                    }
                }
            }


            $catt_config = user_config('params.class_attendance');
            $sl_bcu_subject = $catt_config['sl_bcu_subject'];
            if($sl_bcu_subject == 1){
                foreach($sl_list as $sl){
                    $matched = is_sl_matched($sl,$class_info);
                    if($sl['remain_lesson_hours'] > 0 && $matched){
                        return $sl;
                    }
                }
            }

        }

        if($satt_info['lid'] > 0){
            foreach($sl_list as $sl){
                if($sl['lid'] == $satt_info['lid'] && ($sl['price_type'] == 3 || $sl['remain_lesson_hours'] > 0) ){
                    return $sl;
                }
            }

        }

        if($satt_info['sj_id'] > 0){
            foreach($sl_list as $sl) {
                $matched = is_sl_matched($sl,$satt_info);
                if($matched){
                    return $sl;
                }
            }
        }

        return false;
    }



    public function setSjIdsAttr($value,$data)
    {
        $sj_ids = is_array($value) ? implode(',', $value) : $value;
        
        return $sj_ids;
    }

    public function getSjIdsAttr($value,$data)
    {
        if(empty($value)){
            return [];
        }

        if(is_array($value)) return $value;

        $value = explode(',', $value);
        $value = array_map(function($id){
            return intval($id);
        }, $value);

        return $value;
    }

    public function setExpireTimeAttr($value)
    {
        if(preg_match('/^\d{4}-\d{2}-\d{2}$/',$value)){
            return strtotime($value);
        }
        return $value ? intval($value) : 0;
    }

    public function setStartIntDayAttr($value)
    {
        return $value ? format_int_day($value) : date('Ymd', time());
    }

    public function getExpireTimeAttr($value)
    {
        return $value && is_numeric($value) ? date('Y-m-d', $value) : $value;
    }

    public function getTotalLessonHoursAttr($value,$data){
        $total_lesson_hours = 0.00;
        if(isset($data['lesson_hours'])){
            $total_lesson_hours = $data['lesson_hours'] - $data['refund_lesson_hours'] - $data['transfer_lesson_hours'];
        }
        
        return $total_lesson_hours;
    }

    public function getExpireTimeTextAttr($value,$data)
    {
        $text = '无限制';
        $now_time = time();
        if(isset($data['expire_time']) && intval($data['expire_time']) > 0){
            $int_day_expire_time = intval(date('Ymd',intval($data['expire_time'])));
            $int_day_now_time    = intval(date('Ymd',$now_time));
            if($int_day_expire_time < $int_day_now_time){
                $text = '已过期';
            }else{
                $text = '正常';
            }
        }
        return $text;
    }

    public function getLastAttendanceTimeAttr($value)
    {
        return $value && is_numeric($value) ? date('Y-m-d H:i', $value) : $value;
    }

    public function student()
    {
        return $this->hasOne('Student','sid','sid')
            ->field('sid,bid,student_name,nick_name,sex,photo_url,birth_time,first_family_name,card_no,sno,status');
    }

    public function lesson()
    {
        return $this->hasOne('Lesson', 'lid', 'lid');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid');
    }

    public function orderItems()
    {
        return $this->hasMany('OrderItem', 'sl_id', 'sl_id')->order('create_time', 'asc');
    }

    public function classStudents()
    {
        return $this->hasMany('ClassStudent', 'sl_id', 'sl_id')->where('status', '<', 2);
    }

    public function employeeStudent()
    {
        return $this->hasOne('EmployeeStudent', 'sl_id', 'sl_id');
    }
    
    /**
     * @desc  计算购买的课程的课次与课时
     * @author luo
     * @param Lesson $lesson
     * @param $data
     * @param $nums
     * @param null $price_type  用于处理order_item的nums_unit与课程的price_type不一致的情况
     */
    public static function calcLessonTimesAndHour(Lesson $lesson = null, &$data, $nums, $price_type = null, $cid = 0)
    {
        if(empty($lesson) && empty($cid)) throw new FailResult('课程、班级参数都不存在');

	    $unit_lesson_hours = 0;
        if(!empty($lesson)) {
            $price_type = $price_type ? $price_type : $lesson->price_type;
            $unit_lesson_hours = $lesson->unit_lesson_hours;
        }


        if(empty($lesson) && $cid >  0) {
            $class = m("Classes")::withTrashed()->where('cid', $cid)->cache(1)->find();
            if(empty($class)) throw new FailResult('班级不存在');
            $price_type = Lesson::PRICE_TYPE_HOUR;
            $unit_lesson_hours = $class['consume_lesson_hour'];
        }

        if(floatval($unit_lesson_hours ) == 0){
            $unit_lesson_hours = 1;
        }

        if($price_type == Lesson::PRICE_TYPE_TIMES) {
            $data['origin_lesson_times'] = $nums;
            $data['present_lesson_times'] = isset($data['present_lesson_times']) ? $data['present_lesson_times'] : 0;
            $data['lesson_times'] = $data['origin_lesson_times'] + $data['present_lesson_times'];

            $data['origin_lesson_hours'] = $unit_lesson_hours * $data['origin_lesson_times'];
            $data['present_lesson_hours'] = $unit_lesson_hours * $data['present_lesson_times'];
            $data['lesson_hours'] = $data['origin_lesson_hours'] + $data['present_lesson_hours'];
        } elseif($price_type == Lesson::PRICE_TYPE_HOUR) {
            $data['origin_lesson_hours'] = $nums;
            $data['present_lesson_hours'] = isset($data['present_lesson_hours']) ? $data['present_lesson_hours'] : 0;
            $data['lesson_hours'] = $data['origin_lesson_hours'] + $data['present_lesson_hours'];

            $data['origin_lesson_times'] = floor($data['origin_lesson_hours'] / $unit_lesson_hours);
            $data['present_lesson_times'] = floor($data['present_lesson_hours'] / $unit_lesson_hours);
            $data['lesson_times'] = $data['origin_lesson_times'] + $data['present_lesson_times'];
        } else {
            $data['origin_lesson_hours'] = 0;
            $data['present_lesson_hours'] = isset($data['present_lesson_hours']) ? $data['present_lesson_hours'] : 0;
            $data['lesson_hours'] = $data['origin_lesson_hours'] + $data['present_lesson_hours'];

            $data['origin_lesson_times'] = $nums;
            $data['present_lesson_times'] = isset($data['present_lesson_times']) ? $data['present_lesson_times'] : 0;
            $data['lesson_times'] = $data['origin_lesson_times'] + $data['present_lesson_times'];
        }

        $data['remain_times'] = $data['lesson_times'];
        $data['remain_lesson_hours'] = $data['lesson_hours'];
        $data['remain_arrange_times'] = $data['lesson_times'];
        $data['remain_arrange_hours'] = $data['lesson_hours'];

        return $data;
    }

    //分班数量
    public function getNeedAcNums(Lesson $lesson, $data)
    {
        $data['need_ac_nums'] = $lesson->ac_class_nums;
        return $data;
    }

    //购买的课程相关数据处理
    public function makeLessonData(Lesson $lesson, $data)
    {
        $data = self::calcLessonTimesAndHour($lesson, $data, $data['nums']);
        $data = self::getNeedAcNums($lesson, $data);
        return $data;
    }

    //获取总的购买课次数
    public function getTotalOriginNums(StudentLesson $student_lesson)
    {
        return $student_lesson->origin_lesson_times;
    }

    //获取总的使用次数
    public function getTotalUseNums(StudentLesson $student_lesson)
    {
        return $student_lesson->use_lesson_hours;
    }

    public function createOneItem($data)
    {
        $this->startTrans();
        try {
            $data['start_int_day'] = isset($data['start_int_day']) ? $data['start_int_day'] : date('Ymd', time());

            if (isset($data['cid']) && $data['cid'] > 0) $data['ac_nums'] = 1;
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if ($rs === false) return $this->user_error('添加学生课程失败');
            $sl_id = $this->getAttr('sl_id');

            if (isset($data['cid']) && $data['cid'] > 0) {
                $class = Classes::get(['cid' => $data['cid']]);
                $m_cs = new ClassStudent();
                $rs = $m_cs->addOneStudentToClass($class, $data['sid']);
                if ($rs === false) throw new FailResult($m_cs->getErrorMsg());
            }
            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $sl_id;
    }

    public function updateClass(StudentLesson $student_lesson, $cid)
    {
        $this->startTrans();
        try {
            $rs = $this->where('sl_id', $student_lesson->sl_id)->update(['cid' => $cid, 'ac_nums' => 1]);
            if ($rs === false) return $this->user_error('更新学生课程班级失败');

            $class = Classes::get(['cid' => $cid]);
            $m_cs = new ClassStudent();
            $rs = ($m_cs)->addOneStudentToClass($class, $student_lesson->sid);
            if(!$rs) exception($m_cs->getErrorMsg());
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $student_lesson->sl_id;
    }

    //编辑student_lesson的科目、过期时间
    public function editStudentLesson(array $update_data)
    {
        if(empty($update_data)) return true;

        $sl_info = $this->getData();
        if(empty($sl_info) && !isset($update_data['sl_id'])) return $this->user_error('student_lesson数据错误');
        if(empty($sl_info)) {
            $this->find($update_data['sl_id']);
        }

        $rs = $this->allowField('sj_ids,lid,cid,expire_time,start_int_day,end_int_day')->isUpdate(true)->save($update_data);
        if($rs === false) return false;
        
        return true;
    }

    //课程增加课次、课时
    public function addTimes(StudentLesson $student_lesson, $add_data) {
        $this->startTrans();
        try {
            $update_data = [
                'origin_lesson_times' => $student_lesson->origin_lesson_times + $add_data['origin_lesson_times'],
                'present_lesson_times' => $student_lesson->present_lesson_times + $add_data['present_lesson_times'],
                'lesson_times' => $student_lesson->lesson_times + $add_data['lesson_times'],
                'origin_lesson_hours' => $student_lesson->origin_lesson_hours + $add_data['origin_lesson_hours'],
                'present_lesson_hours' => $student_lesson->present_lesson_hours + $add_data['present_lesson_hours'],
                'lesson_hours' => $student_lesson->lesson_hours + $add_data['lesson_hours'],
                'remain_times' => $student_lesson->remain_times + $add_data['remain_times'],
                'remain_lesson_hours' => $student_lesson->remain_lesson_hours + $add_data['remain_lesson_hours'],
                'remain_arrange_times' => $student_lesson->remain_arrange_times + $add_data['remain_arrange_times'],
                'remain_arrange_hours' => $student_lesson->remain_arrange_hours + $add_data['remain_arrange_hours'],
            ];

            $student_lesson->isUpdate(true)->save($update_data);

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->user_error(['msg' => '学生课程增加课次失败', 'trace' => $e->getTrace()]);
        }

        return true;
    }

    //课程减少课次、课时，增加课耗, 20180507后废弃
    public function decTimes(StudentLesson $student_lesson, $dec_data) {
        $this->startTrans();
        try {
            $use_times = $dec_data['remain_times'];
            $use_lesson_hours = $dec_data['remain_lesson_hours'];

            $update_data['remain_times'] = $student_lesson->remain_times - $dec_data['remain_times'];
            $update_data['remain_lesson_hours'] = $student_lesson->remain_lesson_hours - $dec_data['remain_lesson_hours'];
            $update_data['remain_arrange_times'] = $student_lesson->remain_arrange_times - $dec_data['remain_arrange_times'];
            $update_data['use_times'] = $student_lesson->use_times + $use_times;
            $update_data['use_lesson_hours'] = $student_lesson->use_lesson_hours + $use_lesson_hours;

            $rs = $student_lesson->allowField(['remain_times','remain_lesson_hours','remain_arrange_times',
                'use_times','use_lesson_hours'])->save($update_data);
            if($rs === false) exception($student_lesson->getErrorMsg());

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //计算student_lesson的单位课时金额
    public function getUnitLessonHourAmount($sl_id, $oi_id = 0)
    {
        $m_oi = new OrderItem();
        $unit_lesson_hour_amount = 0;
        if($oi_id > 0) {
            $order_item = $m_oi->where('oi_id', $oi_id)->find();
            if(empty($order_item)) return 0;

            return $order_item['unit_lesson_hour_amount'];
        }

        if($sl_id <= 0) return $this->user_error(400, 'sl_id小于等于0');
        $order_item_list = $m_oi->where('sl_id', $sl_id)->order('oi_id asc')->select();
        $student_lesson = $this->where('sl_id', $sl_id)->find();
        $total_used_hours = $student_lesson->refund_lesson_hours + $student_lesson->transfer_lesson_hours + $student_lesson->use_lesson_hours;
        //如果student_lesson有多个item,则按时间顺序减除已经使用的课时，计算目前可能使用的课时单位金额
        foreach($order_item_list as $item) {
            if($item->origin_lesson_hours + $item->present_lesson_hours >= $total_used_hours) {
                $unit_lesson_hour_amount = $item->unit_lesson_hour_amount;
                break;
            }

            $total_used_hours = $total_used_hours - $item->origin_lesson_hours - $item->present_lesson_hours;
        }

        return $unit_lesson_hour_amount;
    }

    /**
     * 根据消耗课时数获取消耗课时金额
     * @param int $consume_lesson_hour
     * @param int $consume_time
     * @param int $is_trans
     * @param int $use_trans_out_hours
     * @return float|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getConsumeLessonAmount($consume_lesson_hour = 1,$consume_time = 0,$is_trans = 0,$use_trans_out_hours = 0){
        $this->consume_oi_id = 0;//清零
        $this->is_present = false;
        $this->is_found_oi = false;
        $plcm_config = user_config('params.present_lesson_consume_method');//1先消耗正常课时，再消耗赠送 2 平均单价
        $sl_info = $this->getData();
        $lesson_amount  = 0.00;
        $consume_trans_in_lesson_amount = 0.00;


        if($is_trans == 0 && $sl_info['trans_in_lesson_hours'] > 0 && $sl_info['use_lesson_hours'] < $sl_info['trans_in_lesson_hours']){
            //转入课时的处理
            $w_thh['to_sl_id'] = $sl_info['sl_id'];
            $m_thh = new TransferHourHistory();
            $thh = $m_thh->where($w_thh)->find();
            if(!$thh){
                return $lesson_amount;
            }

            $from_sl = self::get($thh['from_sl_id']);
            if(!$from_sl){
                return $lesson_amount;
            }

            $use_trans_out_hours = $sl_info['use_lesson_hours'];

            $remain_trans_in_lesson_hours = $sl_info['trans_in_lesson_hours'] - $sl_info['use_lesson_hours'];
            if($remain_trans_in_lesson_hours < $consume_lesson_hour){
                $consume_trans_in_lesson_hours = $remain_trans_in_lesson_hours;
                $consume_lesson_hour = $consume_lesson_hour - $remain_trans_in_lesson_hours;
            }else{
                $consume_trans_in_lesson_hours = $consume_lesson_hour;
            }

            $consume_trans_in_lesson_amount = $from_sl->getConsumeLessonAmount($consume_trans_in_lesson_hours,0,1,$use_trans_out_hours);
        }

        if($consume_lesson_hour <= 0){      //如果消耗的课时数在赠送课时数范围内
            return $consume_trans_in_lesson_amount;
        }

        $sl_add_history = [];

        // 规则 先消耗导入课时，再消耗导出课时，最后消耗购买课时
        $sl_id                      = $sl_info['sl_id'];
        $import_lesson_hours        = $sl_info['import_lesson_hours'];
        $lesson_hours               = $sl_info['lesson_hours'];
        $remain_lesson_hours        = $sl_info['remain_lesson_hours'];
        $use_lesson_hours           = $sl_info['use_lesson_hours'];
        $present_lesson_hours       = $sl_info['present_lesson_hours'];
        $trans_out_lesson_hours     = $sl_info['trans_out_lesson_hours'];

        if($is_trans == 1){
            $remain_lesson_hours += $trans_out_lesson_hours;
            $remain_lesson_hours -= $use_trans_out_hours;
        }

        if($remain_lesson_hours <= 0){
            return $lesson_amount;
        }

        $need_query_order_item = true;      //是否需要查询订单条目
        $need_query_present_history = false;//是否需要查询赠送记录

        $has_import = false;
        $has_order_item = false;
        $has_order_present = false;
        $has_present = false;

        $is_mix = false;    //是否混合课时记录

        $has_multi_order_item = false;      //有多条订单条目
        $has_multi_import = false;  //有多条导入记录
        $has_multi_present = false; //有多条赠送记录
        $has_multi_order_present = false;   //有多条订单赠送记录

        if($import_lesson_hours > 0){
            $mSlil = new StudentLessonImportLog();
            //获取导入记录列表
            $w_slil['sl_id'] = $sl_info['sl_id'];
            $slil_list = $mSlil->where($w_slil)->order('create_time ASC')->select();
            if(!$slil_list){
                $w_slil = [];
                $w_slil['sid'] = $sl_info['sid'];
                $w_slil['lid'] = $sl_info['lid'];
                $slil_list = $mSlil->where($w_slil)->select();
            }
            if($slil_list){
                $has_import = true;
                $slil_fields = ['lesson_hours','unit_lesson_hour_amount','create_time'];
                foreach($slil_list as $slil){
                    $sah = [];
                    $sah['type'] = 1;       //1为导入
                    array_copy($sah,$slil->toArray(),$slil_fields);

                    array_push($sl_add_history,$sah);
                }
                if(count($slil_list) > 1){
                    $has_multi_import = true;
                }
            }
            if($import_lesson_hours == $lesson_hours){
                $need_query_order_item = false;
            }
        }

        if($need_query_order_item){
            $m_oi = new OrderItem();
            $w_oi['sl_id'] = $sl_id;
            $oi_list = $m_oi->where($w_oi)->order('create_time ASC')->select();
            if(!$oi_list){
                $w_oi = [];
                $w_oi['sid'] = $sl_info['sid'];
                $w_oi['lid'] = $sl_info['lid'];
                $oi_list = $m_oi->where($w_oi)->order('create_time ASC')->select();
                // 修复bug20180920
                if($oi_list && count($oi_list) == 1 && $oi_list[0]['sl_id'] == 0){
                    $oi_list[0]->sl_id = $sl_id;
                    $oi_list[0]->save();
                }
            }
            if($oi_list){
                $has_order_item = true;
                $order_present_hours = 0;
                $order_present_times = 0;
                $oi_fields = ['oi_id','price','reduced_amount','subtotal','create_time','origin_lesson_hours','present_lesson_hours','deduct_present_lesson_hours'];
                foreach($oi_list as $oi){
                    $sah = [];
                    $sah['type'] = 0;   //0为购买
                    array_copy($sah,$oi->toArray(),$oi_fields);

                    $order_info = get_order_info($oi['oid']);
                    $sah['create_time'] = $order_info['paid_time'];     //实际以报名时间为准
                    $sah['present_lesson_hours'] = $sah['present_lesson_hours'] - $sah['deduct_present_lesson_hours'];
                    $sah['lesson_hours'] = $sah['origin_lesson_hours'] + $sah['present_lesson_hours'];



                    array_push($sl_add_history,$sah);
                    $order_present_hours +=  $sah['present_lesson_hours'];
                    if($sah['present_lesson_hours'] > 0){
                        $order_present_times ++;
                    }
                }

                if($order_present_hours < $present_lesson_hours){
                    $need_query_present_history = true;
                }

                if(count($oi_list) > 1){
                    $has_multi_order_item = true;
                }

                if($order_present_hours > 0){
                    $has_order_present = true;
                    if($order_present_times > 1){
                        $has_multi_order_present = true;
                    }
                }

                if($has_import){
                    $is_mix = true;
                }
            }
        }

        if($need_query_present_history){
            $m_slo = new StudentLessonOperate();
            $w_slo['op_type'] = 4;
            $w_slo['sl_id'] = $sl_id;
            $w_slo['oid'] = 0;

            $slo_list = $m_slo->where($w_slo)->order('create_time ASC')->select();

            if($slo_list){
                $has_present = true;
                foreach($slo_list as $slo){

                    $sah = [];
                    $sah['type'] = 2;   //2为赠送
                    $sah['lesson_hours'] = $slo['lesson_hours'];
                    $sah['create_time']  = $slo['create_time'];
                    array_push($sl_add_history,$sah);
                }
                if(count($slo_list) > 1){
                    $has_multi_present = true;
                }
            }
        }

        $sl_add_history = list_sort_by($sl_add_history,'create_time');            //课时增加记录
        $need_query_slh = false;
        $found_result = false;


        if(!$is_mix){
            if($has_order_item){        //最简单的处理情况
                if(!$has_multi_order_item && !$has_order_present) {
                    if($sl_add_history[0]['reduced_amount'] > 0){
                        $sl_add_history[0]['price'] = $sah['subtotal'] / $sah['lesson_hours'];
                    }
                    $lesson_amount = $sl_add_history[0]['price'] * $consume_lesson_hour;
                    $found_result = true;
                }else{
                    $need_query_slh = true;
                }
            }elseif($has_import){
                if(!$has_multi_import){
                    $lesson_amount = $sl_add_history[0]['unit_lesson_hour_amount'] * $consume_lesson_hour;
                    $found_result = true;
                }else{
                    $need_query_slh = true;
                }
            }elseif($has_present){
                $this->is_present = true;
                $found_result = true;
            }
        }

        if($found_result){      //到这里能解决80%的问题
            $this->is_found_oi = true;
            return $lesson_amount + $consume_trans_in_lesson_amount;
        }

        $cacu_consume_lesson_hour = $consume_lesson_hour;   //用于计算的课时数
        $out_of_range_lesson_hour = 0;                      //超出课时数的课时数
        if($consume_lesson_hour > $remain_lesson_hours){
            $cacu_consume_lesson_hour = $remain_lesson_hours;
            $out_of_range_lesson_hour = $consume_lesson_hour - $remain_lesson_hours;
        }


        $loop_use_lesson_hours = 0;
        $sah_index = -1;
        foreach($sl_add_history as $i=>$sah){
            $sah_index++;
            $loop_use_lesson_hours += $sah['lesson_hours'];
            if($loop_use_lesson_hours > $use_lesson_hours){
                $sl_add_history[$i]['use_lesson_hours'] = $sah['lesson_hours'] - ($loop_use_lesson_hours - $use_lesson_hours );
                $sah_index = $i;
                break;
            }else{
                $sl_add_history[$i]['use_lesson_hours'] = $sah['lesson_hours'];
            }
        }

        if($sah_index == -1){
            return $lesson_amount + $consume_trans_in_lesson_amount;
        }

        $sah = $sl_add_history[$sah_index];

        if($sah['type'] == 2){
            $found_result = true;
            $this->is_present = true;
        }elseif($sah['type'] == 1){
            $lesson_amount = $sah['unit_lesson_hour_amount'] * $cacu_consume_lesson_hour;
            $found_result = true;
        }else{
            $this->consume_oi_id = $sah['oi_id'];
            if($sah['present_lesson_hours'] == 0){
                $lesson_amount = $sah['price'] * $cacu_consume_lesson_hour;
                $found_result = true;
            }else{
                //最后处理有赠送的情况
                if($plcm_config == 2){//按平均单价计算
                    $unit_lesson_hour_price = $sah['subtotal'] / ($sah['origin_lesson_hours'] + $sah['present_lesson_hours']);
                    $lesson_amount = $unit_lesson_hour_price * $cacu_consume_lesson_hour;
                    $found_result = true;
                }elseif($plcm_config == 3){//先消耗赠送课时
                    if(($sah['use_lesson_hours'] + $cacu_consume_lesson_hour) > $sah['present_lesson_hours']){
                       $lesson_amount = $sah['price'] * $cacu_consume_lesson_hour;
                    }else{
                        $this->is_present = true;
                    }
                    $found_result = true;
                }else{//先消耗正常课时
                    if(($sah['use_lesson_hours'] + $cacu_consume_lesson_hour) <= $sah['origin_lesson_hours']){
                        if($sah['reduced_amount'] > 0){
                            $sah['price'] = $sah['subtotal'] / $sah['origin_lesson_hours'];
                        }
                        $lesson_amount = $sah['price'] * $cacu_consume_lesson_hour;
                    }else{
                        $this->is_present = true;
                    }
                    $found_result = true;
                }
            }
        }

        if($found_result){
            $this->is_found_oi = true;
        }

        return $lesson_amount + $consume_trans_in_lesson_amount;
    }


    /**
     * @desc  课程减少课次、课时，增加课耗
     * @author luo
     * @param StudentLesson $student_lesson
     * @param $dec_data
     * @param $type int 1:退款，2结转，3其他
     */
    public function handleStudentLessonHours(StudentLesson $student_lesson, $handle_data, $type = self::HANDLE_STUDENT_LESSON_HOURS_OTHER) {


        if($type == self::HANDLE_STUDENT_LESSON_HOURS_REFUND) {
            $update_data['remain_lesson_hours'] = $student_lesson->remain_lesson_hours - $handle_data['remain_lesson_hours'];
            $update_data['remain_arrange_hours'] = min_val($student_lesson->remain_arrange_hours - $handle_data['lesson_hours']);
            $update_data['refund_lesson_hours'] = $student_lesson->refund_lesson_hours + $handle_data['lesson_hours'];
            if(isset($handle_data['lesson_amount']) && $handle_data['lesson_amount'] > 0) {
                $update_data['remain_lesson_amount'] = min_val($student_lesson->remain_lesson_amount - $handle_data['lesson_amount']);
            }

        } elseif($type == self::HANDLE_STUDENT_LESSON_HOURS_TRANSFER) {
            $update_data['remain_lesson_hours'] = $student_lesson->remain_lesson_hours - $handle_data['remain_lesson_hours'];
            $update_data['remain_arrange_hours'] = min_val($student_lesson->remain_arrange_hours - $handle_data['lesson_hours']);
            $update_data['transfer_lesson_hours'] = $student_lesson->transfer_lesson_hours + $handle_data['lesson_hours'];
            if(isset($handle_data['lesson_amount']) && $handle_data['lesson_amount'] > 0) {
                $update_data['remain_lesson_amount'] = min_val($student_lesson->remain_lesson_amount - $handle_data['lesson_amount']);
            }

        } elseif($type == self::HANDLE_STUDENT_LESSON_HOURS_DELETE_BILL) {
            $handle_lesson_hours = $handle_data['origin_lesson_hours'] + $handle_data['present_lesson_hours'];
            $update_data = [
                'origin_lesson_hours'  => $student_lesson->origin_lesson_hours - $handle_data['origin_lesson_hours'],
                'present_lesson_hours' => $student_lesson->present_lesson_hours - $handle_data['present_lesson_hours'],
                'lesson_hours'         => $student_lesson->lesson_hours - $handle_lesson_hours,
                'remain_lesson_hours'  => $student_lesson->remain_lesson_hours - $handle_lesson_hours,
                'remain_arrange_hours' => $student_lesson->remain_arrange_hours - $handle_lesson_hours,
            ];
            if(isset($handle_data['lesson_amount']) && $handle_data['lesson_amount'] > 0) {
                $update_data['lesson_amount'] = min_val($student_lesson->lesson_amount - $handle_data['lesson_amount']);
                $update_data['remain_lesson_amount'] = min_val($student_lesson->remain_lesson_amount - $handle_data['lesson_amount']);
            }

        } elseif($type == self::HANDLE_STUDENT_LESSON_HOURS_DELETE_REFUND) {
            $update_data['remain_lesson_hours'] = $student_lesson->remain_lesson_hours + $handle_data['remain_lesson_hours'];
            $update_data['remain_arrange_hours'] = min_val($student_lesson->remain_arrange_hours + $handle_data['lesson_hours']);
            $update_data['refund_lesson_hours'] = $student_lesson->refund_lesson_hours - $handle_data['lesson_hours'];
            if(isset($handle_data['lesson_amount']) && $handle_data['lesson_amount'] > 0) {
                $update_data['remain_lesson_amount'] = $student_lesson->remain_lesson_amount + $handle_data['lesson_amount'];
            }
        } elseif($type == self::HANDLE_STUDENT_LESSON_HOURS_DELETE_TRANSFER) {
            $update_data['remain_lesson_hours'] = $student_lesson->remain_lesson_hours + $handle_data['remain_lesson_hours'];
            $update_data['remain_arrange_hours'] = min_val($student_lesson->remain_arrange_hours + $handle_data['lesson_hours']);
            $update_data['transfer_lesson_hours'] = $student_lesson->transfer_lesson_hours - $handle_data['lesson_hours'];

            if(isset($handle_data['lesson_amount']) && $handle_data['lesson_amount'] > 0) {
                $update_data['remain_lesson_amount'] = $student_lesson->remain_lesson_amount + $handle_data['lesson_amount'];
            }
        } else {
            $update_data['use_lesson_hours'] = $student_lesson->use_lesson_hours + $handle_data['lesson_hours'];

        }

        if(isset($update_data['remain_lesson_hours']) && $update_data['remain_lesson_hours'] < 0) {
            return $this->user_error('student_lesson剩余课时小于0,出现异常');
        }
        $this->startTrans();
        try {
            $update_fields = [
                'origin_lesson_hours',
                'present_lesson_hours',
                'lesson_hours',
                'remain_lesson_hours',
                'remain_arrange_hours',
                'refund_lesson_hours',
                'transfer_lesson_hours',
                'use_lesson_hours',
                'lesson_amount',
                'remain_lesson_amount'
            ];
            $result = $student_lesson->allowField($update_fields)->save($update_data);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('student_lesson');
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error( $e);
        }
        $this->commit();

        return true;
    }

    //当学员的剩余课次为0，则退出所在的班级
    public function exitClassWhenBuyLessonEqualZero($sl_id, $student_lesson = null)
    {
        if(is_null($student_lesson)) {
            $student_lesson = $this->find($sl_id);
        }

        if(empty($student_lesson)) return true;

        if($student_lesson->remain_lesson_hours > 0) {
            return true;
        }

        try {
            $this->startTrans();
            //更新为已结课状态
            $student_lesson->lesson_status = StudentLesson::LESSON_STATUS_DONE;
            $rs = $student_lesson->save();
            if ($rs === false) throw new FailResult('更新购买课程状态失败');

            //把学生从班级移除
            if ($student_lesson->cid > 0) {
                $m_cs = new ClassStudent();
                if($m_cs::isInClass($student_lesson->cid, $student_lesson->sid)) {
                    $class_student = $m_cs->where('cid', $student_lesson->cid)->where('sid', $student_lesson->sid)
                        ->where('status != '.$m_cs::STATUS_CLASS_TRANSFER)->find();
                    $rs = $m_cs->removeStudentFromClass($class_student->cs_id);
                    if($rs === false) throw new FailResult($m_cs->getErrorMsg());
                }
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

    //获取学生购买的某个课程，默认为没结课的
    public static function getInfoBySidAndLid($sid, $lid, $lesson_status = ['neq', self::LESSON_STATUS_DONE])
    {
        $where = ['lesson_status' => $lesson_status];
        $info = (new self())->where('sid', $sid)->where('lid', $lid)->where($where)->find();
        return $info;
    }

    /**
     * 根据学员ID和科目ID获得可用StudentLesson列表
     * @param [type] $sid   [description]
     * @param [type] $sj_id [description]
     */
    public static function GetBySidAndSjId($sid,$sj_id)
    {
        $w['lesson_status'] = ['LT',self::LESSON_STATUS_DONE];
        $w['sid'] = $sid;

        $m_sl = new self();

        $sl_list = $m_sl->where($w)->where("find_in_set($sj_id,sj_ids)")->select();

        if(!$sl_list){
            $sl_list = [];
        }

        return $sl_list;

    }

    /**
     * 获取StudentLessonInfo
     * @param  [type]  $sid   [description]
     * @param  [type]  $sj_id [description]
     * @param  integer $sg_id [description]
     * @param  integer $cid [description]
     * @return [type]         [description]
     */
    public function getStudentLessonInfo($sid,$sj_id,$sg_id = 0,$cid = 0){
        $ret = [];
        $ret['sid'] = $sid;
        $ret['remain_lesson_hours'] = 0;
        $ret['lesson_hours'] = 0;
        $ret['items'] = [];

        $w['lesson_status'] = ['LT',self::LESSON_STATUS_DONE];
        $w['sid'] = $sid;

        $m_sl = new self();

        if($cid > 0){
            $w['cid'] = $cid;
        }

        $sl_list = $m_sl->where($w)->where("find_in_set($sj_id,sj_ids)")->select();

        if($sl_list){
            foreach($sl_list as $sl){
                $ret['remain_lesson_hours'] += $sl['remain_lesson_hours'];
                $ret['lesson_hours'] += $sl['lesson_hours']-$sl['refund_lesson_hours']-$sl['transfer_lesson_hours'];
                array_push($ret['items'],$sl->getData());
            } 
        }else{
            if($cid > 0){
                unset($w['cid']);
                $sl_list = $m_sl->where($w)->where("find_in_set($sj_id,sj_ids)")->select();
                if($sl_list){
                    foreach($sl_list as $sl){
                        $ret['remain_lesson_hours'] += $sl['remain_lesson_hours'];
                        $ret['lesson_hours'] += $sl['lesson_hours']-$sl['refund_lesson_hours']-$sl['transfer_lesson_hours'];
                        array_push($ret['items'],$sl->getData());
                    }
                }
            }
        }

        if(!empty($ret['items'])){
            $ret['sl_id'] = implode(',',array_column($ret['items'],'sl_id'));
        }else{
            $ret['sl_id'] = 0;
        }

        return $ret;
        
    }

    //判断是否需要新增student_lesson
    public function canAddStudentLesson($sid, $lid, $cid = 0)
    {
        //--1-- 相应课程、班级的student_lesson是否存在
        $w = [
            'sid' => $sid,
            'lid' => $lid,
            'cid' => $cid,
            'lesson_status' => ['neq', self::LESSON_STATUS_DONE],
        ];
        $student_lesson = $this->where($w)->find();
        if(!empty($student_lesson)) return $student_lesson;

        //--2-- 如果班级id大于0,但是没有相应的student_lesson, 是否有没分班的student_lesson
        if($cid > 0) {
            $w = [
                'sid' => $sid,
                'lid' => $lid,
                'cid' => 0,
                'lesson_status' => ['neq', self::LESSON_STATUS_DONE],
            ];
            $student_lesson = $this->where($w)->find();
            if(!empty($student_lesson)) return $student_lesson;
        }

        return true;
    }

    public static function getInfoBySidAndCid($sid, $cid)
    {
        $info = StudentLesson::get(['sid' => $sid, 'cid' => $cid]);
        return $info;
    }

    //根据订单项目，更新订单分班状态
    public static function updateAcStatusToAssigned($sj_id, $sid)
    {
        if($sj_id <= 0) return '科目不正确';
        if($sid <= 0) return '学生id不正确';

        $rs = (new self())->where('sid', $sid)->where("find_in_set($sj_id, sj_ids)")
            ->update(['ac_status' => self::AC_STATUS_ALL]);

        if($rs === false) return '更新购买课程分班状态失败';

        return true;
    }

    //更新订单相关的班级状态
    public static function updateAcStatus($sj_id, $sid, $cid, $status)
    {
        if($sj_id <= 0 || $sid <= 0) return true;

        try {
            $model = new self();
            $model->startTrans();

            if($status == self::AC_STATUS_NO) {  // 取消分班
                //--1--更新student_lesson
                $rs = $model->where('sj_id', $sj_id)->where('sid', $sid)
                    ->update(['cid' => 0, 'ac_status' => $status]);
                if ($rs === false) return $model->user_error('更新订单分班状态失败');

                $sl_ids = $model->where('sj_id', $sj_id)->where('sid', $sid)->column('sl_id');

                $m_oi = new OrderItem();
                $rs = $m_oi->where('sl_id', 'in', $sl_ids)->where('sid', $sid)->update(['cid' => 0]);
                if($rs === false) return $model->user_error($m_oi->getErrorMsg());
            } else {
                //--1--更新student_lesson
                $rs = $model->where("find_in_set($sj_id, sj_ids)")->where('sid', $sid)
                    ->update(['cid' => $cid, 'ac_status' => $status]);
                if ($rs === false) return $model->user_error('更新订单分班状态失败');

                $sl_ids = $model->where("find_in_set($sj_id, sj_ids)")->where('sid', $sid)->column('sl_id');

                $m_oi = new OrderItem();
                $rs = $m_oi->where('sl_id', 'in', $sl_ids)->where('sid', $sid)->update(['cid' => $cid]);
                if($rs === false) return $model->user_error($m_oi->getErrorMsg());
            }

            //--2-- 更新order
            $oids = $m_oi->where('sl_id', 'in', $sl_ids)->column('oid');
            $m_order = new Order();
            $order_list = $m_order->where('oid', 'in', $oids)->select();
            foreach ($order_list as $order) {
                $not_assign_num = $m_oi->where('oid', $order->oid)->where('gtype', OrderItem::GTYPE_LESSON)
                    ->where('cid', 0)->where('sid', $sid)->count();
                if ($not_assign_num == 0) {
                    $order->ac_status = Order::AC_STATUS_ALL;
                } else {
                    $order->ac_status = Order::AC_STATUS_SOME;
                }
                $order->save();
            }
            $model->commit();
        } catch (Exception $e) {
            $model->rollback();
            return $model->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //计算某天的课程购买情况
    public function countNumOfDay($day)
    {
        $start_time = strtotime($day);
        $end_time = $start_time + 24*3600;
        $num = $this->scope('bid')->where('create_time', '>=', $start_time)->where('create_time', '<=', $end_time)
            ->count();
        return $num;
    }

    /**
     * @param StudentAttendance $attendance
     * @return bool
     */
    public static function rollbackConsume(StudentAttendance $attendance)
    {
        if (empty($attendance['is_consume'])) {
            return false;
        }
        $model = $attendance['student_lesson'];

        if($model){
            $data = [];
            $use_times = $model['use_times'];
            $data['use_times'] = $use_times - 1;

            $remain_times = $model['remain_times'];
            $data['remain_times'] = $remain_times + 1;

            $m_slh = model('student_lesson_hour')->where('satt_id',$attendance['satt_id'])->find();

            if($m_slh){
                $unit_lesson_hours = $m_slh->lesson_hours;
            }else{
                $lesson = $model['lesson'];
                $unit_lesson_hours = $lesson['unit_lesson_hours'];/*单次课扣多少课时*/
            }

            $use_lesson_hours  = $model['use_lesson_hours'];
            $data['use_lesson_hours'] = $use_lesson_hours - $unit_lesson_hours;

            $remain_lesson_hours = $model['remain_lesson_hours'];
            $data['remain_lesson_hours'] = $remain_lesson_hours + $unit_lesson_hours;

            $model->allowField(true)->isUpdate(true)->save($data);
        }
    }

    public function checkActive()
    {
        //todo
        return true;
    }

    /**
     * 根据新考勤，更新一次考勤对应的学生的课程的使用情况
     * @param StudentAttendance $attendance
     * @throws Exception
     */
    public function attendanceConsume(StudentAttendance $attendance)
    {
        if ($this->getData('lesson_status') == self::LESSON_STATUS_NO) {
            $this->data('lesson_status', self::LESSON_STATUS_ING)->save();
        }
        /*登记考勤的时候选择不计算课消*/
        if (empty($attendance['is_consume'])) {
            return ;
        }

        /*扣除课消*/
        $data = [];
        $use_times = $this->getData('use_times');
        $data['use_times'] = $use_times + 1;

        $remain_times = $this->getData('remain_times');
        if ($remain_times <= 0) {
            throw new Exception('剩余课次不足！');
        }
        $data['remain_times'] = $remain_times - 1;

        $unit_lesson_hours = $attendance['lesson']['unit_lesson_hours'];/*单次课扣多少课时*/
        $use_lesson_hours = $this->getData('use_lesson_hours');
        $data['use_lesson_hours'] = $use_lesson_hours + $unit_lesson_hours;

        $remain_lesson_hours = $this->getData('remain_lesson_hours');
        $data['remain_lesson_hours'] = $remain_lesson_hours - $unit_lesson_hours;
        //todo 如果剩余课时少于配置的数量,可以做一些通知。
        $this->allowField(true)->isUpdate(true)->save($data);
    }

    public static function getRecordByAtdInfo(array $atd_info)
    {
        $w = [];
        $w['lesson_status'] = ['LT',2];//必须是未结课的
        $w['sid'] = $atd_info['sid'];
        if (!empty($atd_info['sl_id'])) {
            $w = $atd_info['sl_id'];
        } elseif (!empty($atd_info['target_ca'])) {
            $w['lid'] = $atd_info['target_ca']['lid'];
        } elseif (!empty($atd_info['cid'])) {
            $cls = Classes::get($atd_info['cid']);
            $w['lid'] = $cls['lid'];
        } elseif (!empty($atd_info['lid'])) {
            $w['lid'] = $atd_info['lid'];
        } else {
            throw new Exception('无法确定学生课程！');
        }
        return self::get($w);
    }

    /**
     * 停课操作
     * @param  array   $info    [description]
     * @param  Student $student [description]
     * @return [type]           [description]
     */
    public function stop(array $info,Student $student)
    {
        //第一步判断是否有未复课的停课操作
        //第一步写入停课操作
        $sid = $student->sid;
        $bid = request()->bid;
        $is_today_stop = false;
        $now_int_day  = intval(date('Ymd',time()));
        $stop_int_day = intval(format_int_day($info['stop_time']));
        $recover_int_day = isset($info['recover_time'])?intval(format_int_day($info['recover_time'])):0;
        $stop_remark = safe_str($info['stop_remark']);
        $cid = 0;
        if(isset($info['cid'])){
            $cid = intval($info['cid']);
        }
        if($recover_int_day > 0 && $recover_int_day <= $stop_int_day){
            return $this->user_error('复课日期不能小于等于停课日期');
        }

        $lesson_type = $this->lesson_type;

        $w_sls['sid'] = $student->sid;
        $w_sls['bid'] = $bid;
        $w_sls['stop_type'] = 1;
        $w_sls['expired_time'] = 0;

        $exists_sls = StudentLessonStop::get($w_sls);

        if($exists_sls){
            $msg = '该学生已经办理了休学，无需且无法进行停课操作!';
            return $this->user_error($msg);
        }

        if($stop_int_day <= $now_int_day && ($recover_int_day == 0 || $recover_int_day > $now_int_day)){
            $is_today_stop = true;
        } 

        //检查是否更新sls记录
        $is_new_stop = true;

        $w_sls['stop_type'] = 0;
        $w_sls['lid']       = $this->lid;

        $sls = StudentLessonStop::get($w_sls);

        $this->startTrans();

        if($sls){
            $sls->stop_int_day = $stop_int_day;
            $sls->recover_int_day = $recover_int_day;
            $sls->stop_remark = $stop_remark;

            $result = $sls->save();

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('student_lesson_stop');
            }

            $is_new_stop = false;
        }
        
        if($is_new_stop){

            $sls['sid'] = $student->sid;
            $sls['stop_type'] = 0;
            $sls['og_id'] = $student->og_id;
            $sls['bid']   = request()->bid;
            $sls['stop_int_day'] = $stop_int_day;
            $sls['recover_int_day'] = $recover_int_day;
            $sls['stop_remark']     = $stop_remark;
            $sls['lid'] = $this->lid;
            $sls['cid'] = $cid;

            if($is_today_stop){
                $sls['stop_time'] = time();
            }


            $sls_id = $this->m_student_lesson_stop->isUpdate(false)->save($sls, []);

            if(!$sls_id){
                $this->rollback();
                return $this->sql_save_error('student_lesson_stop');
            }
        }

        //如果是当天停课
        if($is_today_stop){
            $this->is_stop = 1;

            $result = $this->save();

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('student_lesson');
            }

            if($lesson_type == 0 && $cid > 0){
            //班课
                $w_cs['sid'] = $sid;
                $w_cs['cid'] = $cid;
                $w_cs['status'] = 1;
                $w_cs['out_time'] = 0;

                $update_cs = [];
                $update_cs['status'] = 0;
                $result = $this->m_class_student->save($update_cs,$w_cs);

                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('class_student');
                }
            }

            $student->status = 20;      //停课状态学生

            $result = $student->save();
            
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('student');
            }
        }
        //添加学员操作日志
        $result = StudentLog::addStopLog($this,$info);
        if(!$result){
            $this->rollback();
            return $this->sql_add_error('student_log');
        }

        $this->commit();
        return true;
    }

    /**
     * 复课操作
     * @param  array   $info    [description]
     * @param  Student $student [description]
     * @return [type]           [description]
     */
    public function recoverLesson(array $info,Student $student)
    {
        $sid    = $student->sid;
        $sl_id  = $this->getData('sl_id');
        $now_int_day = intval(date('Ymd',time()));

        //$bid    = request()->bid;
        $recover_int_day = format_int_day($info['recover_time']);

        $w_sls['sid']   = $sid;
        //$w_sls['bid']   = $bid;
        //$w_sls['sl_id'] = $sl_id;
        $w_sls['lid'] = $this->getData('lid');
        //$w_sls['recover_int_day'] = 0;

        if(isset($info['cid'])){
            $w_sls['cid'] = $info['cid'];
        }

        $mSls = new StudentLessonStop();

        $m_sls = $mSls->skipBid()->where($w_sls)->find();

        if(!$m_sls){
            return $this->user_error('该课程没有停课记录,无需复课!');
        }

        if($recover_int_day < $m_sls['stop_int_day']){
            return $this->user_error('复课日期不能小于停课日期');
        }

        $this->startTrans();

        try {

            if ($recover_int_day <= $now_int_day) {
                //立即复课操作
                $m_sls->recover_int_day = $recover_int_day;
                $m_sls->expired_time = time();

                $result = $m_sls->save();
                if (false === $result) {
                    $this->rollback();
                    return $this->sql_save_error('student_lesson_stop');
                }

                if (isset($info['cid'])) {
                    $w_cs = [];
                    $w_cs['sid'] = $sid;
                    $w_cs['sl_id'] = $sl_id;
                    $w_cs['cid'] = $info['cid'];
                    $w_cs['out_time'] = 0;
                    $w_cs['status'] = 0;

                    $update_cs = [];
                    $update_cs['status'] = 1;

                    $mClassStudent = new ClassStudent();
                    $result = $mClassStudent->save($update_cs, $w_cs);

                    if (false === $result) {
                        $this->rollback();
                        return $this->sql_save_error('class_student');
                    }
                }

                //studentSuspend更新
                $w_ss = [];
                $w_ss['sid'] = $sid;
                $w_ss['sl_id'] = $sl_id;
                $w_ss['end_time'] = 0;

                $update_ss = [];
                $update_ss['end_time'] = time();

                $mStudentSuspend = new StudentSuspend();
                $result = $mStudentSuspend->save($update_ss,$w_ss);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('student_suspend');
                }


                if ($student->status >= 20 && $student->status < 30) {
                    $student->status = 1;
                    $result = $student->save();

                    if (false === $result) {
                        $this->rollback();
                        return $this->sql_save_error('student');
                    }
                }

            } else {
                $m_sls->recover_int_day = $recover_int_day;
                $result = $m_sls->save();
                if (false === $result) {
                    $this->rollback();
                    return $this->sql_save_error('student_lesson_stop');
                }
            }

            $this->is_stop = 0;
            $this->allowField('is_stop')->save();
            
            //添加一条 学员复课 操作日志
            StudentLog::addRecoverLog($this,$recover_int_day);

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;

    }


    /**
     * 复课操作
     */
    public function recoverStudentLesson(array $info,Student $student)
    {
        $sid = $this->getData('sid');
        $sl_id = $this->getData('sl_id');
        $stop_int_day = $info['stop_int_day'];
        $info['recover_int_day'] = intval(format_int_day($info['recover_time']));
        $sl_info = $this->getData();
        
        if ($stop_int_day == 0) {
            throw new Exception('该学生的课程没有设置停课日期，无需复课！');
        }
        if ($info['recover_int_day'] <= $stop_int_day) {
            throw new Exception('复课日期不能小于停课日期!');
        }
        if (!empty($sl_info['recover_int_day'])) {
            /*修改复课日期*/
            $attendance = (new StudentAttendance())->where('sid', $sid)
                ->where('sl_id', $sl_id)
                ->where('int_day', '>', $stop_int_day)
                ->where('int_day', '<', $info['recover_int_day'])
                ->find();
            if (!empty($attendance)) {
                throw new Exception('停课期间不能存在考勤记录, 新的复课日期不合法!');
            }
            $info['edit'] = true;
        }
        $w = [];
        $w['sl_id'] = $this->getData('sl_id');
        $w['stop_int_day'] = ['>', 0];/*状态为停课*/
//        $w['is_end'] = ClassStudent::IS_END_NO;/*未结课*/
        $w['status'] = ['>', ClassStudent::STATUS_CLASS_TRANSFER]; /*未转班*/
        $class_students = ClassStudent::all($w);
        foreach ($class_students as $item) {
            if (!$item instanceof ClassStudent) {
                throw new Exception('pass');
            }
            $res = $item->recoverClassStudent($info);
            if ($res === false) {
                throw new Exception($item->getError());
            }
        }

        $rs = $this->data('recover_int_day', $info['recover_int_day'])->save();
        if ($rs === false) {
            return false;
        }
        StudentLog::addRecoverLog($this);
        return true;
    }

    /**
     * 结课操作
     */
    public function close()
    {
        $w = [];
        $w['sl_id']  = $this->getData('sl_id');
        /*获取所有的班级信息*/
        $class_students = ClassStudent::all($w);
        foreach ($class_students as $item) {
            $rs = $item->closeClassStudent();
            if ($rs === false) {
                $this->error = $item->getError();
                return false;
            }
        }
        $this->data('lesson_status', StudentLesson::LESSON_STATUS_DONE)->save();
        StudentLog::addCloseLog($this);
        return true;
    }

    public function undoClose()
    {
        $w = [];
        $w['sl_id']  = $this->getData('sl_id');
        $w['status']  = ClassStudent::STATUS_CLOSE;

        /*获取已结课所有的班级信息*/
        $mClassStudent = new ClassStudent();
        $class_students = $mClassStudent->where($w)->select();

        foreach ($class_students as $student) {
            $result = $student->undoCloseClassStudent();
            if (false === $result) {
                return $this->user_error($student->getError());
            }
        }
        $this->data('lesson_status', StudentLesson::LESSON_STATUS_ING)->save();
        StudentLog::addCloseLog($this);
        return true;
    }

    /**
     * 附加上停课字段
     * @param  integer $is_stop [description]
     * @return [type]           [description]
     */
    public function appendLessonStopFields($is_stop = 0){
        $w_sls['sid'] = $this->getData('sid');
        $w_sls['stop_type'] = 0;
        $w_sls['expired_time'] = 0;
        $w_sls['lid'] = $this->getData('lid');
        if($is_stop == 0){
            $w_sls['stop_time'] = 0;
        }else{
            $w_sls['stop_time'] = ['GT',0];
        }

        $sls = StudentLessonStop::get($w_sls);

        if($sls){
            $this->stop_int_day = $sls->stop_int_day;
            $this->recover_int_day = $sls->recover_int_day;
            $this->stop_remark = $sls->stop_remark;
        }else{
            $this->stop_int_day = 0;
            $this->recover_int_day = 0;
            $this->stop_remark = '';
        }
    }

    /**
     * 获取剩余课时金额
     * @return [type] [description]
     */
    public function getRemainLessonAmount(){
        $expire_time = $this->getData('expire_time');
        if($expire_time > 0 && $expire_time < time()){
            return 0.00;
        }
        $lesson_status = $this->getData('lesson_status');
        if($lesson_status == 2){
            return 0.00;
        }
        $remain_lesson_hours = $this->getData('remain_lesson_hours');
        if($remain_lesson_hours <= 0){
            return 0.00;
        }
        $w_oi['sl_id'] = $this->getData('sl_id');
        $oi_list = $this->m_order_item->where($w_oi)->order('create_time DESC')->select();
        $lesson_amount = 0.00;

        $present_lesson_consume_method = user_config('params.present_lesson_consume_method');

        if($oi_list){
            
            foreach($oi_list as $oi){
                $oi_origin_lesson_hours  = $oi['origin_lesson_hours'];
                $oi_present_lesson_hours = $oi['present_lesson_hours'];
                $reduced_amount          = $oi['reduced_amount'];

                $oi_lesson_hours = $oi_origin_lesson_hours + $oi_present_lesson_hours;

                $unit_lesson_hour_amount = $oi['unit_lesson_hour_amount'];

                if($unit_lesson_hour_amount == 0 && $oi_lesson_hours > 0){
                    $unit_lesson_hour_amount = round(($oi['subtotal'] / $oi_lesson_hours), 6);
                }

                $oi_lesson_amount = $unit_lesson_hour_amount * $remain_lesson_hours;


                if($oi_present_lesson_hours > 0){
                    if($oi['nums_unit'] == 1){
                        $onetime_lesson_hours       =  Lesson::getUnitLessonHoursByLid($oi['lid']);
                        $unit_hours_price           = $oi['price'] / $onetime_lesson_hours;
                    }else{
                        $unit_hours_price           = $oi['price'];
                    }

                    if($reduced_amount > 0){    //如果有直减金额
                        $unit_hours_price = $unit_hours_price - round($reduced_amount / $oi_origin_lesson_hours,6); 
                    }

                    //如果有赠送课次，那么还要特殊处理
                    if($present_lesson_consume_method == 1){//先消耗正常课次，再消耗剩余课次
                        $oi_lesson_amount = ($remain_lesson_hours - $oi_present_lesson_hours) * $unit_hours_price;
                        if($oi_lesson_amount < 0){
                            $oi_lesson_amount = 0;
                        }
                    }elseif($present_lesson_consume_method == 3){
                        if($remain_lesson_hours > $oi_origin_lesson_hours){
                            $oi_lesson_amount = $oi_origin_lesson_hours * $unit_hours_price;
                        }else{
                            $oi_lesson_amount = $remain_lesson_hours * $unit_hours_price;
                        }
                    }
                }
               
                $lesson_amount += $oi_lesson_amount;
                $remain_lesson_hours = $remain_lesson_hours - $oi_lesson_hours;

                if($remain_lesson_hours <= 0){
                    break;
                }
            }
        }


        return $lesson_amount;
    }

    /**
     * 根据学员ID和科目ID获得学员的课时模型
     * @param  [type] $sid   [description]
     * @param  [type] $sj_id [description]
     * @return [type]        [description]
     */
    public function getByStudentSjId($sid,$sj_id){
        $w_sl['sid'] = $sid;
        $w_sl['lesson_status'] = ['LT',2];
        $w_sl['remain_lesson_hours'] = ['GT',0];
        
        $w_sjids_str = "find_in_set($sj_id,sj_ids)";

        $sl_list = $this->where($w_sl)->where($w_sjids_str)->order('create_time ASC')->select();

        if($sl_list){
            return $sl_list[0];
        }

        return false;
    }



    public function fixStudentLessonSjIds($sid){
        $w_sl['sid'] = $sid;
        $w_sl['sj_ids'] = '';
        $w_sl['lid'] = ['neq',0];

        $sl_list = $this->where($w_sl)->select();

        if($sl_list){
            foreach($sl_list as $sl){
                $lesson = $this->m_lesson->where('lid',$sl->lid)->cache(1)->find();
                if($lesson){
                    if(!empty($lesson->sj_ids)){
                        $sl->sj_ids = $lesson->sj_ids;
                    }else{
                        $sl->sj_ids = [$lesson->sj_id];
                    }

                    $sl->save();
                }
            }
        }

    }

    /**
     * 扣除课时
     * @param  [type] $lesson_hour [description]
     * @return [type]              [description]
     */
    public function reduceLessonHour($lesson_hours,$attendance_time,$lesson_amount = 0){
        $update = [];
        $update['remain_lesson_hours'] = $this->getData('remain_lesson_hours') - $lesson_hours;
        $update['use_lesson_hours']    = $this->getData('use_lesson_hours') + $lesson_hours;
        $update['last_attendance_time'] = $attendance_time;

        if($lesson_amount > 0){
            $update['remain_lesson_amount'] = min_val($this->getData('remain_lesson_amount') - $lesson_amount);
        }

        $sl_info = $this->getData();

        $w = [];
        $w['sl_id'] = $sl_info['sl_id'];

        $this->startTrans();
        try{
            $result = $this->save($update,$w);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('student_lesson');
            }
            $m_student = new Student();
            $result = $m_student->updateLessonHours($sl_info['sid']);
            if(!$result){
                exception($m_student->getError());
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 增加课时
     * @param $lesson_hour
     */
    public function rollbackLessonHour($lesson_hours,$lesson_amount = 0){
        $sl_info = $this->getData();
        $update = [];
        $update['remain_lesson_hours'] = $sl_info['remain_lesson_hours'] + $lesson_hours;
        $update['use_lesson_hours']    = $sl_info['use_lesson_hours'] - $lesson_hours;

        if($lesson_amount > 0){
            $update['remain_lesson_amount'] = $sl_info['remain_lesson_amount'] + $lesson_amount;
        }

        if($update['use_lesson_hours'] < 0){
            $update['use_lesson_hours'] = 0;
            $update['remain_lesson_hours'] = $sl_info['remain_lesson_hours'];
        }

        $w = [];
        $w['sl_id'] = $sl_info['sl_id'];

        $this->startTrans();
        try {
            $result = $this->save($update, $w);
            if (false === $result) {
                $this->rollback();
                return $this->sql_save_error('student_lesson');
            }

            $m_student = new Student();
            $result = $m_student->updateLessonHours($sl_info['sid']);
            if(!$result){
                $this->rollback();
                return $this->user_error($m_student->getError());
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * @desc 导入课时数
     * @author luo
     */
    public function importStudentLesson($import_data)
    {
        $rule = [
            'sid' => 'require',
            'lesson_hours' => 'require|float|gt:0',
            'sj_ids' => 'require',
        ];
        $rs = $this->validate($rule)->validateData($import_data);
        if($rs === false) return false;

        $import_data['lid'] = isset($import_data['lid']) ? $import_data['lid'] : 0;
        $import_data['unit_lesson_hour_amount'] = isset($import_data['unit_lesson_hour_amount'])?floatval($import_data['unit_lesson_hour_amount']):0;

        $import_lesson_amount = $import_data['unit_lesson_hour_amount'] * $import_data['lesson_hours'];

        $this->startTrans();
        try {

            $w_sl['sid']    = $import_data['sid'];
            $w_sl['sj_ids'] = $import_data['sj_ids'];
            $m_sl = $this->where($w_sl)->find();

            if($m_sl) {
                $m_sl->import_lesson_hours      += $import_data['lesson_hours'];
                $m_sl->lesson_hours             += $import_data['lesson_hours'];
                $m_sl->remain_lesson_hours      += $import_data['lesson_hours'];
                $m_sl->remain_arrange_hours     += $import_data['lesson_hours'];
                $m_sl->lesson_amount            += $import_lesson_amount;
                $m_sl->remain_lesson_amount     += $import_lesson_amount;
                $result = $m_sl->allowField(true)->save();
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('student_lesson');
                }
                $sl_id = $m_sl->sl_id;
            } else {
                if(!empty($import_data['lid'])) {
                    $lesson = Lesson::get($import_data['lid']);
                }
                $sl = [
                    'sid'                  => $import_data['sid'],
                    'lid'                  => isset($import_data['lid']) ? $import_data['lid'] : 0,
                    'sj_ids'               => $import_data['sj_ids'],
                    'lesson_type'          => isset($lesson) ? $lesson['lesson_type'] : 0,
                    'lesson_hours'         => $import_data['lesson_hours'],
                    'import_lesson_hours'  => $import_data['lesson_hours'],
                    'remain_lesson_hours'  => $import_data['lesson_hours'],
                    'remain_arrange_hours' => $import_data['lesson_hours'],
                    'lesson_amount'        => $import_lesson_amount,
                    'remain_lesson_amount'  => $import_lesson_amount
                ];
                $result = $this->data([])->allowField(true)->isUpdate(false)->save($sl);
                if(!$result){
                    $this->rollback();
                    return $this->sql_add_error('student_lesson');
                }
                $sl_id = $this->sl_id;
            }

            $mSlil = new StudentLessonImportLog();
            $import_data['sl_id'] = $sl_id;
            $result = $mSlil->addLog($import_data);
            if(!$result){
                $this->rollback();
                return $this->user_error($mSlil->getError());
            }


            (new Student())->updateLessonHours($import_data['sid']);

        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;
    }

    /**
     * 获得课时情况
     * @return [type] [description]
     */
    public function getLessonInfo(){
        $sl_info = $this->getData();
        $ret['lesson_name'] = '';
        $ret['subject'] = '';
        $lid = $this->getData('lid');

        if($lid > 0){
            $lesson_info = get_lesson_info($lid);
            $ret['lesson_name'] = $lesson_info['lesson_name'];
        }

        $sj_ids = $this->getData('sj_ids');
        $arr_sj_ids = explode(',',$sj_ids);

        if(!empty($arr_sj_ids)){
            $subject_name = [];
            foreach($arr_sj_ids as $sj_id){
                $subject = get_subject_info($sj_id);
                array_push($subject_name,$subject['subject_name']);
            }
            $ret['subject'] = implode(',',$subject_name);
        }

        $ret['expire_time'] = date('Y-m-d',$this->getData('expire_time'));
        $ret = array_merge($sl_info,$ret);
        return $ret;
    }

    /**
     * 根据订单项目创建或学员课时记录
     * @param [type] &$oi_info [description]
     */
    public function addByOrderItem(&$oi_info){

        $w_sl = [];
        $w_sl['sid'] = $oi_info['sid'];
        $w_sl['lesson_status'] = ['LT',2];

        $sl_info = [];

        //课时金额
        $sl_lesson_amount = $oi_info['subtotal'];

        if($oi_info['lid'] > 0){
            $w_sl['lid'] = $oi_info['lid'];
            //20190128添加，加下面这3行代码后，报不同的班级会产生多条student_lesson记录，更合理。
            if($oi_info['cid'] > 0){
                $w_sl['cid'] = $oi_info['cid'];
            }

            $lesson_info = get_lesson_info($oi_info['lid']);

            if(empty($lesson_info['sj_ids'])){
                $lesson_info['sj_ids'] = $lesson_info['sj_id'];
            }

            $lesson_type = $lesson_info['lesson_type'];

            $sl_info['lid']    = $oi_info['lid'];
            $sl_info['lesson_type'] = $lesson_type;

            array_copy($sl_info,$lesson_info,['fit_grade_start','fit_grade_end','sj_ids','is_package']);

            if($lesson_type == 0){
                $sl_info['ac_status']    = 0;
                $sl_info['need_ac_nums'] = $lesson_info['is_multi_class'] == 1 ? $lesson_info['ac_class_nums']:1;
                $sl_info['ac_nums'] = 0;

                if($oi_info['cid'] > 0){
                    $sl_info['ac_status'] = 2;
                    $sl_info['ac_nums'] = 1;
                    $sl_info['cid'] = $oi_info['cid'];
                }
            }

            $sl_info['price_type'] = $oi_info['nums_unit'];     //计费模式 1，2，3

            if($oi_info['nums_unit'] == 1){
                //将课次转换为课时
                $unit_lesson_hours = $lesson_info['unit_lesson_hours'];
                $sl_info['origin_lesson_times'] = $oi_info['origin_lesson_times'];
                $sl_info['present_lesson_times'] = $oi_info['present_lesson_times'];
                $sl_info['lesson_times'] = $sl_info['origin_lesson_times'] + $sl_info['present_lesson_times'];

                $sl_info['origin_lesson_hours'] = $unit_lesson_hours * $oi_info['origin_lesson_times'];
                $sl_info['present_lesson_hours'] = $unit_lesson_hours * $oi_info['present_lesson_times'];
                $sl_info['lesson_hours'] = $unit_lesson_hours * $sl_info['lesson_times'];
            }elseif($oi_info['nums_unit'] == 2){
                //将课时转换为课次
                $unit_lesson_hours = $lesson_info['unit_lesson_hours'];
                $sl_info['origin_lesson_hours'] = $oi_info['nums'];
                $sl_info['present_lesson_hours'] = $oi_info['present_lesson_hours'];
                $sl_info['lesson_hours'] = $sl_info['origin_lesson_hours'] + $sl_info['present_lesson_hours'];

                if($unit_lesson_hours > 0) {
                    $sl_info['origin_lesson_times'] = $oi_info['origin_lesson_hours'] / $unit_lesson_hours;
                    $sl_info['present_lesson_times'] = $oi_info['present_lesson_hours'] / $unit_lesson_hours;
                    $sl_info['lesson_times'] = $sl_info['lesson_hours'] / $unit_lesson_hours;
                }
            }else{
                //$sl_info['start_int_day'] = $oi_info['start_int_day'];
                $sl_info['expire_time']   = $oi_info['expire_time'];
                $sl_info['end_int_day']   = int_day($oi_info['expire_time']);
                $sl_info['origin_lesson_hours'] = 0;
                $sl_info['present_lesson_hours'] = 0;
                $sl_info['lesson_hours'] = 0;

                $sl_info['origin_lesson_times'] = 0;
                $sl_info['present_lesson_times'] = 0;
                $sl_info['lesson_times'] = 0;
            }


        }elseif($oi_info['cid'] > 0){
            $class_info = get_class_info($oi_info['cid']);

            $w_sl['sj_ids'] = $class_info['sj_id'];
            $w_sl['cid'] = $class_info['cid'];              //报班级续报

            $sl_info['lid'] = 0;
            $sl_info['sj_ids'] = $class_info['sj_id'];
            $sl_info['lesson_type'] = 0;
            $sl_info['cid'] = $class_info['cid'];
            $sl_info['ac_status'] = 2;
            $sl_info['need_ac_nums'] = 1;
            $sl_info['ac_nums'] = 1;
            $sl_info['fit_grade_start'] = $sl_info['fit_grade_end'] = $class_info['grade'];
            $sl_info['origin_lesson_hours']  = $oi_info['origin_lesson_hours'];
            $sl_info['present_lesson_hours'] = $oi_info['present_lesson_hours'];
            $sl_info['lesson_hours'] = $sl_info['origin_lesson_hours'] + $sl_info['present_lesson_hours'];
        }
        $sl_id = 0;
        $this->startTrans();
        try{
            $mStudentLesson = new self();
            $ex_sl = $mStudentLesson->where($w_sl)->find();

            if(!$ex_sl){
                //创建新的student_lesson
                array_copy($sl_info,$oi_info,['og_id','bid','sid','expire_time']);

                $sl_info['remain_lesson_hours'] = $sl_info['lesson_hours'];
                $order = get_order_info($oi_info['oid']);
                $sl_info['start_int_day'] = !empty($order) ? date('Ymd', $order['paid_time']) : date('Ymd', time());
                $sl_info['remain_arrange_hours'] = $sl_info['remain_lesson_hours'];

                $sl_info['lesson_amount'] = $sl_lesson_amount;
                $sl_info['remain_lesson_amount'] = $sl_lesson_amount;

                $result = $mStudentLesson->data([])->isUpdate(false)->save($sl_info);

                if(!$result){
                    $this->rollback();
                    return $this->sql_add_error('student_lesson');
                }

                $sl_id = $mStudentLesson->sl_id;

                $sl_info['sl_id'] = $sl_id;

            }else{
                //增加课时
                $update_sl = [];
                $update_sl['origin_lesson_hours'] = $ex_sl['origin_lesson_hours'] + $sl_info['origin_lesson_hours'];
                $update_sl['present_lesson_hours'] = $ex_sl['present_lesson_hours'] + $sl_info['present_lesson_hours'];
                $update_sl['lesson_hours'] = $ex_sl['lesson_hours'] + $sl_info['lesson_hours'];
                $update_sl['remain_lesson_hours'] = $ex_sl['remain_lesson_hours'] + $sl_info['lesson_hours'];
                $update_sl['remain_arrange_hours'] = $ex_sl['remain_arrange_hours'] + $sl_info['lesson_hours'];
                $update_sl['lesson_amount'] = $ex_sl['lesson_amount'] + $sl_lesson_amount;
                $update_sl['remain_lesson_amount'] = $ex_sl['remain_lesson_amount'] + $sl_lesson_amount;

                $w_sl_update = [];
                $w_sl_update['sl_id'] = $ex_sl['sl_id'];

                $result = (new StudentLesson)->save($update_sl,$w_sl_update);

                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('student_lesson');
                }

                $sl_id = $ex_sl['sl_id'];
            }

            if($sl_info['present_lesson_hours'] > 0) {

                $operate_data = [
                    'sl_id' => $sl_id,
                    'lesson_hours' => $sl_info['present_lesson_hours'],
                    'op_type' => StudentLessonOperate::OP_TYPE_ORDER,
                    'oid' => $oi_info['oid'],
                    'oi_id' => $oi_info['oi_id'],
                    'unit_price' => $oi_info['unit_lesson_hour_amount']
                ];
                $m_slo = new StudentLessonOperate();
                $rs = $m_slo->addOperation($operate_data);
                if(false === $rs){
                    $this->rollback();
                    return $this->user_error($m_slo->getErrorMsg());
                }
            }


            if($oi_info['cid'] > 0){
                $order_info = get_order_info($oi_info['oid']);
                $mClasses = new Classes();
                $result = $mClasses->addStudent($oi_info['sid'],1,$sl_id,$oi_info['cid'],$order_info['paid_time']);
                if(!$result){
                    $this->rollback();
                    return false;
                }
            }

            $update_oi = [];
            $update_oi['sl_id'] = $sl_id;

            $w_oi_update = [];
            $w_oi_update['oi_id'] = $oi_info['oi_id'];

            $mOrderItem = new OrderItem();
            $result = $mOrderItem->save($update_oi,$w_oi_update);

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('order_item');
            }

            //自动付款课时记录
            $result = $this->autoPayStudentLessonHour($sl_id);
            if(false === $result){
                $this->rollback();
                return false;
            }


        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return $sl_id;
    }

    /**
     * 学员课程升级
     * @param $oi_info
     * @return bool
     */
    public function upgradeLessonHours(&$oi_info)
    {
        $mStudentLesson = new self();
        $old_sl = $mStudentLesson->where('sl_id',$oi_info['from_sl_id'])->find();
        if (empty($old_sl)){
            return $this->user_error('学员课程信息不存在');
        }

        $sl_info = [];
        $w_sl['lid'] = $oi_info['lid'];
        $lesson_info = get_lesson_info($oi_info['lid']);

        if(empty($lesson_info['sj_ids'])){
            $lesson_info['sj_ids'] = $lesson_info['sj_id'];
        }

        $lesson_type = $lesson_info['lesson_type'];

        $sl_info['lid']    = $oi_info['lid'];
        $sl_info['lesson_type'] = $lesson_type;

        array_copy($sl_info,$lesson_info,['fit_grade_start','fit_grade_end','sj_ids','is_package']);

        if($lesson_type == 0){
            $sl_info['ac_status']    = 0;
            $sl_info['need_ac_nums'] = $lesson_info['is_multi_class'] == 1 ? $lesson_info['ac_class_nums']:1;
            $sl_info['ac_nums'] = 0;

            if($oi_info['cid'] > 0){
                $sl_info['ac_status'] = 2;
                $sl_info['ac_nums'] = 1;
                $sl_info['cid'] = $oi_info['cid'];
            }
        }

        $sl_info['price_type'] = $oi_info['nums_unit'];     //计费模式 1，2，3

        if($oi_info['nums_unit'] == 1){
            //将课次转换为课时
            $unit_lesson_hours = $lesson_info['unit_lesson_hours'];
            $sl_info['origin_lesson_times'] = $oi_info['origin_lesson_times'];
            $sl_info['present_lesson_times'] = $oi_info['present_lesson_times'];
            $sl_info['lesson_times'] = $sl_info['origin_lesson_times'] + $sl_info['present_lesson_times'];

            $sl_info['origin_lesson_hours'] = $unit_lesson_hours * $oi_info['origin_lesson_times'];
            $sl_info['present_lesson_hours'] = $unit_lesson_hours * $oi_info['present_lesson_times'];
            $sl_info['lesson_hours'] = $unit_lesson_hours * $sl_info['lesson_times'];
        }elseif($oi_info['nums_unit'] == 2) {
            //将课时转换为课次
            $unit_lesson_hours = $lesson_info['unit_lesson_hours'];
            $sl_info['origin_lesson_hours'] = $oi_info['nums'];
            $sl_info['present_lesson_hours'] = $oi_info['present_lesson_hours'];
            $sl_info['lesson_hours'] = $sl_info['origin_lesson_hours'] + $sl_info['present_lesson_hours'];

            if ($unit_lesson_hours > 0) {
                $sl_info['origin_lesson_times'] = $oi_info['origin_lesson_hours'] / $unit_lesson_hours;
                $sl_info['present_lesson_times'] = $oi_info['present_lesson_hours'] / $unit_lesson_hours;
                $sl_info['lesson_times'] = $sl_info['lesson_hours'] / $unit_lesson_hours;
            }
        }

        //课时金额
        $sl_lesson_amount = $oi_info['subtotal'];
        $this->startTrans();
        try{
            //创建新的student_lesson
            array_copy($sl_info,$oi_info,['og_id','bid','sid','expire_time']);

            $sl_info['origin_lesson_hours'] = $old_sl['origin_lesson_hours'] + $oi_info['origin_lesson_hours'];
            $sl_info['present_lesson_hours'] = $old_sl['present_lesson_hours'] + $oi_info['present_lesson_hours'];
            $sl_info['lesson_hours'] = $old_sl['remain_lesson_hours'] + $oi_info['nums'];
            $sl_info['remain_lesson_hours'] = $old_sl['remain_lesson_hours'] + $oi_info['nums'];
            $sl_info['lesson_amount'] = $old_sl['lesson_amount'] + $sl_lesson_amount;
            $sl_info['remain_lesson_amount'] = $old_sl['remain_lesson_amount'] + $sl_lesson_amount;
            $order = get_order_info($oi_info['oid']);
            $sl_info['start_int_day'] = !empty($order) ? date('Ymd', $order['paid_time']) : date('Ymd', time());

            $result = $mStudentLesson->data([])->isUpdate(false)->save($sl_info);

            if(!$result){
                $this->rollback();
                return $this->sql_add_error('student_lesson');
            }

            $sl_id = $mStudentLesson->sl_id;

            $sl_info['sl_id'] = $sl_id;

            if($sl_info['present_lesson_hours'] > 0) {

                $operate_data = [
                    'sl_id' => $sl_id,
                    'lesson_hours' => $sl_info['present_lesson_hours'],
                    'op_type' => StudentLessonOperate::OP_TYPE_UPGRADE,
                    'oid' => $oi_info['oid'],
                    'oi_id' => $oi_info['oi_id'],
                    'unit_price' => $oi_info['unit_lesson_hour_amount']
                ];
                $m_slo = new StudentLessonOperate();
                $rs = $m_slo->addOperation($operate_data);
                if(false === $rs){
                    $this->rollback();
                    return $this->user_error($m_slo->getError());
                }
            }

            $update_oi = [];
            $update_oi['sl_id'] = $sl_id;
            $w_oi_update = [];
            $w_oi_update['oi_id'] = $oi_info['oi_id'];
            $mOrderItem = new OrderItem();
            $result = $mOrderItem->save($update_oi,$w_oi_update);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('order_item');
            }

            //  更改升级前shudent_lesson
            $update_old_sl = [];
            $update_old_sl['remain_lesson_hours'] = 0;
            $update_old_sl['lesson_status'] = 2;

            $update_old_w = [];
            $update_old_w['sl_id'] = $oi_info['from_sl_id'];
            $result = $this->save($update_old_sl,$update_old_w);
            if (false === $result){
                $this->rollback();
                return $this->sql_save_error('student_lesson');
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 撤销课程升级
     * @param $sl_id
     */
    public function undoUpgradeLessonHours($oi_id,$sl_id)
    {
        $mStudent_lesson = $this->find($sl_id);
        if (empty($mStudent_lesson)){
            return $this->user_error('课程记录不存在');
        }

        if ($mStudent_lesson['use_lesson_hours'] > 0){
            return $this->user_error('该课程已消耗课时,不能撤销');
        }

        $mOrderItem = new OrderItem();
        $mOrder_item = $mOrderItem->find($oi_id);
        $old_sl = $this->where('sl_id',$mOrder_item['from_sl_id'])->find();

        $this->startTrans();
        try{

            $update_old_sl = [];
            $update_old_sl['remain_lesson_hours'] = $mStudent_lesson['remain_lesson_hours'] - $mOrder_item['nums'];
            $update_old_sl['lesson_status'] = 1;

            $update_old_w = [];
            $update_old_w['sl_id'] = $mOrder_item['from_sl_id'];
            $result = $this->save($update_old_sl,$update_old_w);
            if (false === $result){
                $this->rollback();
                return $this->sql_save_error('student_lesson');
            }

            $mOrder = Order::get($mOrder_item['oid']);
            $result = $mOrder->deleteOrder($mOrder,true);
            if (false === $result){
                $this->rollback();
                return $this->user_error($mOrder->getError());
            }

            $result = $mStudent_lesson->delete();
            if (false === $result){
                $this->rollback();
                return $this->sql_delete_error('student_lesson');
            }

            $result = $mOrderItem->delOneItem($mOrder_item['oi_id']);
            if (false === $result){
                $this->rollback();
                return $this->user_error($mOrderItem->getError());
            }

            $mStudent = new Student();
            $result = $mStudent->updateLessonHours($old_sl['sid']);
            if(false === $result){
                return $this->user_error($mStudent->getError());
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;

    }



    /**
     * 自动支付课时消耗记录
     * @param int $sl_id
     * @return bool
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function autoPayStudentLessonHour($sl_id = 0){
        if($sl_id == 0){
            $sl_info = $this->getData();
        }else{
            $sl_info = get_sl_info($sl_id);
            $this->data($sl_info);
        }
        $sl_id = $sl_info['sl_id'];
        $sid   = $sl_info['sid'];
        $bid   = $sl_info['bid'];

        $w_slh['sl_id'] = 0;
        $w_slh['is_pay'] = 0;
        $w_slh['sid'] = $sid;
        $w_slh['bid'] = $bid;

        $mSlh = new StudentLessonHour();
        $m_unpay_slh_list = $mSlh->where($w_slh)->select();

        if(!$m_unpay_slh_list){
            return true;
        }

        $this->startTrans();
        try{
            $remain_lesson_hours = $sl_info['remain_lesson_hours'];
            $use_lesson_hours    = $sl_info['use_lesson_hours'];
            $remain_lesson_amount = $sl_info['remain_lesson_amount'];
            foreach($m_unpay_slh_list as $slh){
                if($remain_lesson_hours < $slh['lesson_hours']){
                    break;
                }
                $result = $slh->pay($sl_id,false);
                if(false === $result){
                    $this->rollback();
                    return $this->user_error($slh->getError());
                }
                $pay_lesson_hour = $result;
                $remain_lesson_hours -= $pay_lesson_hour;
                $use_lesson_hours    += $pay_lesson_hour;
                $remain_lesson_amount -= $slh['lesson_amount'];
            }

            $update_sl['remain_lesson_hours'] = $remain_lesson_hours;
            $update_sl['use_lesson_hours'] = $use_lesson_hours;

            $w_sl_update['sl_id'] = $sl_id;
            $result = $this->save($update_sl,$w_sl_update);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('student_lesson');
            }


        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;

    }

    /**
     * 更新排课次数
     * @param  [type] $sid [description]
     * @param  [type] $lid [description]
     * @return [type]      [description]
     */
    public function updateArrange($sid,$lid){
        $w_sl['sid'] = $sid;
        $w_sl['lid'] = $lid;
        $w_sl['lesson_status'] = ['LT',2];

        $m_sl = $this->where($w_sl)->find();

        if(!$m_sl){
            return true;
        }

        $w_cas = [];
        $w_cas['is_attendance'] = 0;
        array_copy($w_cas,$w_sl,['sid','lid']);

        $arranged_hours = 0.00;

        $mCas = new CourseArrangeStudent();

        $cas_list = $mCas->where($w_cas)->select();

        foreach($cas_list as $cas){
            $arranged_hours += $cas->lesson_hour;
        }

        $remain_arrange_hours = min_val($m_sl->remain_lesson_hours - $arranged_hours);

        $m_sl->remain_arrange_hours = $remain_arrange_hours;


        $result = $m_sl->save();

        if(false === $result){
            return $this->sql_save_error('student_lesson');
        }

        return true;


    }

    /**
     * 删除学员课时记录
     * @param int $sl_id
     * @return bool
     */
    public function remove($sl_id = 0){
        if($sl_id == 0){
            $sl_info = $this->getData();

        }else{
            $sl_info = get_sl_info($sl_id);
            $this->data($sl_info);
        }
        $sl_id = $sl_info['sl_id'];
        $w_slh['sl_id'] = $sl_id;
        $m_slh = new StudentLessonHour();
        $slh_count = $m_slh->where($w_slh)->count();
        if($slh_count > 0){
            return $this->user_error('删除课时记录失败，请先撤销相关考勤记录!');
        }

        $result = $this->delete(true);
        if(false === $result){
            return $this->sql_delete_error('student_lesson');
        }
        return true;
    }


    // 撤回退费课时返回
    public function backLesson(StudentLesson $studentlesson,$num){

        if(!isset($num) || abs(floatval($num) == 0)) {
            return $this->user_error('缺少返还课程数');
        }
        $data = [
            'lesson_hours' => $studentlesson['lesson_hours'] + $num,
            'remain_lesson_hours' => $studentlesson['remain_lesson_hours'] + $num,
        ];
        $w = ['sl_id' => $studentlesson['sl_id']];
        $re = $this->where($w)->update($data);
        if (!$re){
            return $this->user_error('返还课程数失败');
        }
        return true;
    }

    public function undoTransferLesson(StudentLesson $student_lesson, $nums, $amount, $present_nums = 0)
    {
        if(!isset($nums) || abs(floatval($nums) == 0)) {
            return $this->user_error('缺少撤销结转返还课程数');
        }

        $transfer_lesson_hours = min_val($student_lesson['transfer_lesson_hours'] - $nums );

        $data = [
            'remain_arrange_hours' => $student_lesson['remain_arrange_hours'] + $nums + $present_nums,
            'remain_lesson_amount' => $student_lesson['remain_lesson_amount'] + $amount,
            'remain_lesson_hours' => $student_lesson['remain_lesson_hours'] + $nums + $present_nums,
            'transfer_lesson_hours' => $transfer_lesson_hours,
        ];


        if ($student_lesson['lesson_status'] == 2){
            $data['lesson_status'] = 1;
        }

        $w['sl_id'] = $student_lesson['sl_id'];

        $result = $this->save($data, $w);
        if (false === $result){
            return $this->sql_save_error('student_lesson');
        }

        return true;
    }

    /**
     * 更新课程金额
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function updateLessonAmount(){
        $sl_info = $this->getData();
        $sl_id = $sl_info['sl_id'];

        //正常购买
        $oi_lesson_hours = 0;
        $oi_lesson_amount = 0;
        $refund_lesson_hours = 0;
        $refund_lesson_amount = 0;
        //导入课时
        $import_lesson_hours = 0;
        $import_lesson_amount = 0;
        //转入课时
        $trans_in_lesson_hours = 0;
        $trans_in_lesson_amount = 0;
        //转出课时
        $trans_out_lesson_hours = 0;
        $trans_out_lesson_amount = 0;

        //先统计正常购买的课时数
        $w_oi['sl_id'] = $sl_id;
        $mOrderItem = new OrderItem();

        $oi_list = $mOrderItem->where($w_oi)->select();
        if($oi_list){
            foreach($oi_list as $oi){
                $oi_lesson_amount += $oi['subtotal'];
                $oi_lesson_hours += ($oi['origin_lesson_hours']  + $oi['present_lesson_hours'] - $oi['deduct_present_lesson_hours']);
                if($oi['order_refund_item']){
                    foreach($oi['order_refund_item'] as $ori){
                        $refund_lesson_hours += $ori['nums'];
                        $oi_lesson_hours -= $ori['nums'];
                        $oi_lesson_amount -= $ori['amount'];
                        $refund_lesson_amount += $ori['amount'];
                    }
                }
            }
        }
        //导入
        if($sl_info['import_lesson_hours'] > 0){
            $mSlil = new StudentLessonImportLog();
            $w_slil['sl_id'] = $sl_id;
            $slil_list = $mSlil->where($w_slil)->select();
            if($slil_list){
                foreach($slil_list as $slil){
                    $import_lesson_hours += $slil['lesson_hours'];
                    $import_lesson_amount += ($slil['lesson_hours'] * $slil['unit_lesson_hour_amount']);
                }
            }
        }
        //转入
        if($sl_info['trans_in_lesson_hours'] > 0){
            $mThh = new TransferHourHistory();
            $w_thh['to_sl_id'] = $sl_id;
            $thh_list = $mThh->where($w_thh)->select();
            if($thh_list){
                foreach($thh_list as $thh){
                    $trans_in_lesson_hours += $thh['lesson_hours'];
                    $trans_in_lesson_amount += $thh['lesson_amount'];
                }
            }
        }

        //转出
        if($sl_info['trans_out_lesson_hours'] > 0){
            $mThh = new TransferHourHistory();
            $w_thh['from_sl_id'] = $sl_id;
            $thh_list = $mThh->where($w_thh)->select();
            if($thh_list){
                foreach($thh_list as $thh){
                    $trans_out_lesson_hours += $thh['lesson_hours'];
                    $trans_out_lesson_amount += $thh['lesson_amount'];
                }
            }
        }

        $total_lesson_amount = min_val($oi_lesson_amount - $refund_lesson_amount + $import_lesson_amount + $trans_in_lesson_amount - $trans_out_lesson_amount);

        $this->lesson_amount = $total_lesson_amount;

        //计算已经使用的课时金额
        $mSlh = new StudentLessonHour();

        $w_slh['sl_id'] = $sl_id;

        $use_lesson_amount = $mSlh->where($w_slh)->sum('lesson_amount');

        if(!$use_lesson_amount){
            $use_lesson_amount = 0;
        }

        $remain_lesson_amount = min_val($total_lesson_amount - $use_lesson_amount);

        $this->remain_lesson_amount = $remain_lesson_amount;


        try {
            $result = $this->isUpdate(true)->save();
        }catch(\Exception $e){
            $this->user_error($e->getMessage());
            return false;
        }
        if(false === $result){
            return $this->sql_save_error('student_lesson');
        }

        return true;
    }

    /**
     * 减少导入课时数
     * @param $lesson_hours
     * @param $unit_lesson_hour_amount
     * @return bool
     */
    public function reduceImportLessonHours($lesson_hours,$unit_lesson_hour_amount)
    {
        $lesson_amount = $unit_lesson_hour_amount * $lesson_hours;
        $this->import_lesson_hours = min_val($this->import_lesson_hours - $lesson_hours);
        $this->lesson_hours = min_val($this->lesson_hours - $lesson_hours);
        $this->remain_lesson_hours = min_val($this->remain_lesson_hours - $lesson_hours);
        $this->remain_lesson_amount = min_val($this->remain_lesson_amount - $lesson_amount);

        $this->startTrans();
        try {
            if($this->lesson_hours == 0){       //如果只有导入课时，那么删除课时记录
                $result = $this->delete();
                if (false === $result) {
                    return $this->sql_delete_error('student_lesson');
                }
            }else{
                $result = $this->save();
                if (false === $result) {
                    return $this->sql_save_error('student_lesson');
                }
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }


}