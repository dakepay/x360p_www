<?php

namespace app\ftapi\model;

use think\Exception;

class StudentLesson extends Base
{

    const LESSON_STATUS_DONE = 2; //已经结课


    public $type = [
        'last_attendance_time' => 'timestamp',
    ];

    public function setExpireTimeAttr($value)
    {
        return $value ? strtotime($value) : 0;
    }

    public function getExpireTimeAttr($value)
    {
        return $value ? date('Y-m-d', $value) : 0;
    }

    public function getLastAttendanceTimeAttr($value)
    {
        return $value && is_numeric($value) ? date('Y-md H:i', $value) : $value;
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

    public function student()
    {
        return $this->hasOne('Student','sid','sid')
            ->field('sid,bid,student_name,nick_name,sex,photo_url,birth_time,first_family_name,card_no,sno');
    }

    public function lesson()
    {
        return $this->hasOne('Lesson', 'lid', 'lid')->field('lid,price_type,lesson_name,unit_lesson_hours,lesson_cover_picture');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid');
    }

    public function orderItems()
    {
        return $this->hasMany('OrderItem', 'sl_id', 'sl_id')->order('create_time', 'asc');
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
            return $student_lesson;
        }

        return $empty_student_lesson;
    }



}