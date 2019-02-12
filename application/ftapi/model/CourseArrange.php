<?php
namespace app\ftapi\model;

class CourseArrange extends Base
{
    protected $append = ['course_name'];


    public function setIntDayAttr($value, $data)
    {
        return format_int_day($value);
    }

    protected function setIntStartHourAttr($value, $data)
    {
        return format_int_hour($value);
    }

    protected function setIntEndHourAttr($value, $data)
    {
        return format_int_hour($value);
    }

    public function lesson()
    {
        return $this->hasOne('Lesson', 'lid', 'lid');
    }

    public function oneClass()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }

    public function students()
    {
        return $this->belongsToMany('Student', 'course_arrange_student', 'sid', 'ca_id');
    }

    public function teacher()
    {
        return $this->hasOne('Employee', 'eid', 'teach_eid');
    }

    public function ftReview(){
        return $this->hasOne('FtReview','ca_id','ca_id');
    }

    protected function init_cainfo($ca_id){
        if($ca_id == 0){
            $ca_info = $this->getData();
            if(!isset($ca_info['ca_id'])){
                return $this->user_error('模型未实例化!');
            }
            $ca_id = $ca_info['ca_id'];
        }else{
            $ca_info = get_ca_info($ca_id);
            if(!$ca_info){
                return $this->user_error('排课ID不存在!');
            }
            $this->data($ca_info);
        }
        return $ca_info;
    }

    public function getCourseNameAttr($value,$data){
        $course_name = get_course_name_by_row($data);

        return $course_name;
    }

    /**
     * @desc 获取考勤对象
     * @param int $ca_id
     * @param bollean $refresh
     */
    public function getAttObjects($ca_id = 0,$refresh = false,$get_sl = true){
        $ca_info = $this->init_cainfo($ca_id);

        if(!$ca_info){
            return false;
        }

        $ca_id = $ca_info['ca_id'];

        $is_class_ca = $ca_info['lesson_type'] == Lesson::LESSON_TYPE_CLASS;


        if($ca_info['is_trial'] == 1) {
            $this->createTrialArrangeStudents();
        }elseif($ca_info['is_makeup'] == 1){
            $this->createMakeupArrangeStudents();
        }else{
            if($is_class_ca && $refresh){
                $this->createClassArrangeStudents($refresh);
            }
        }



        $w_cas['ca_id'] = $ca_id;

        $cas_list = get_table_list('course_arrange_student',$w_cas);

        if(!$cas_list){
            $cas_list = [];
        }

        //判断是否有试听或补课
        $has_makeup = 0;
        $has_trial  = 0;
        $student_fields = ['sid','student_name','nickname','sex','photo_url','status','money'];
        $customer_fields = ['cu_id','name','nick_name','sex'];

        $sid_maps = [];

        foreach($cas_list as $k=>$cas){
            if($cas['sid'] == 0 && $cas['cu_id'] == 0){
                //删除无效记录
                db('course_arrange_student')->where('cas_id',$cas['cas_id'])->delete();
                unset($cas_list[$k]);
                continue;
            }
            if($cas['sid'] > 0 && isset($sid_maps[$cas['sid']])){
                //删除重复学员
                db('course_arrange_student')->where('cas_id',$cas['cas_id'])->delete();
                unset($cas_list[$k]);
                continue;
            }
            if($cas['is_makeup'] == 1){
                $has_makeup++;
            }
            if($cas['is_trial'] == 1) {
                $has_trial++;
            }
            if($cas['sid'] > 0){
                $sid_maps[$cas['sid']] = $cas;
            }

        }

        if(!$has_trial && $is_class_ca && $refresh){
            $trial_cas_list = $this->refreshTrialStudents();
            if(!empty($trial_cas_list)){
                foreach($trial_cas_list as $cas){
                    array_push($cas_list,$cas);
                }
            }
        }

        if(!$has_makeup && $is_class_ca && $refresh){
            $makeup_cas_list = $this->refreshMakeupStudents();
            if(!empty($makeup_cas_list)){
                foreach($makeup_cas_list as $cas){
                    array_push($cas_list,$cas);
                }
            }
        }

        foreach($cas_list as $k=>$cas){
            $student  = [];
            $customer = [];
            $attendance = null;
            $leave = null;

            if($cas['sid'] > 0){
                $s_info = get_student_info($cas['sid']);
                array_copy($student,$s_info,$student_fields);
                if($get_sl) {
                    $student['student_lesson'] = StudentLesson::GetStudentLessonByCa($cas['sid'], $ca_info);
                }
            }
            if($cas['cu_id'] > 0){
                $cu_info = get_customer_info($cas['cu_id']);
                array_copy($customer,$cu_info,$customer_fields);
            }
            if($cas['satt_id'] > 0){
                $attendance = get_satt_info($cas['satt_id']);
                $attendance['in_time'] = date('Y-m-d H:i',$attendance['in_time']);
            }
            if($cas['is_leave'] == 1){
                $w_slv['ca_id'] = $ca_id;
                $w_slv['sid']   = $cas['sid'];
                $leave = get_slv_info($w_slv);
                if($leave){
                    if(empty($leave['reason'])){
                        $leave['reason'] = get_did_value($leave['leave_type']);
                    }
                    $cas_list[$k]['remark'] = $leave['reason'];

                }
                $cas_list[$k]['is_in']  = 0;
            }

            $cas_list[$k]['student']  = $student;
            $cas_list[$k]['customer'] = $customer;
            $cas_list[$k]['attendance'] = $attendance;
            $cas_list[$k]['leave'] = $leave;
        }

        return $cas_list;
    }

    /**
     * 创建试听排课的学员记录
     * @return bool
     */
    protected function createTrialArrangeStudents(){
        $ca_info = $this->getData();
        $ca_id   = $ca_info['ca_id'];
        $w_cas['ca_id'] = $ca_id;
        $ex_cas = $this->m_course_arrange_student->where($w_cas)->find();

        //如果班级已经存在排课学生，就不创建了
        if($ex_cas){
            $now_int_day = int_day(time());
            if($now_int_day > $ca_info['int_day']){
                $w_cas['is_in'] = ['>',-1];
                $ex_cas = $this->m_course_arrange_student->where($w_cas)->find();

                if($ex_cas){
                    return true;
                }
            }else{
                return true;
            }
        }

        $w_tla['ca_id'] = $ca_id;

        $tla_list = get_table_list('trial_listen_arrange',$w_tla);

        if($tla_list){
            $cas_fields = ['og_id','ca_id', 'lid', 'cid','sj_id','sg_id','int_day', 'int_start_hour', 'int_end_hour'];
            $tla_fields = ['cu_id','sid'];
            $this->startTrans();
            try{
                foreach($tla_list as $tla){
                    $w_ex_cas = [];
                    $w_ex_cas['ca_id'] = $tla['ca_id'];
                    if($tla['is_student'] == 1){
                        $w_ex_cas['sid'] = $tla['sid'];
                    }else{
                        $w_ex_cas['cu_id'] = $tla['cu_id'];
                    }

                    $ex_cas = get_cas_info($w_ex_cas,false);
                    if ($ex_cas) {
                        continue;       //如果已经存在排课学员，跳过
                    }
                    $cas = [];
                    $cas['is_trial'] = 1;
                    array_copy($cas, $ca_info, $cas_fields);
                    array_copy($cas,$tla,$tla_fields);

                    if($tla['is_attendance'] == 1){
                        $cas['is_in'] = $tla['attendance_status'];
                        $cas['is_attendance'] = 1;

                        if($tla['sid'] > 0){
                            $w_satt['sid'] = $tla['sid'];
                            $w_satt['ca_id'] = $tla['ca_id'];

                            $satt_info = get_satt_info($w_satt);

                            if($satt_info){
                                $cas['satt_id'] = $satt_info['satt_id'];
                            }
                        }
                    }



                    $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                    if (!$result) {
                        $this->rollback();
                        return $this->sql_add_error('course_arrange_student');
                    }


                }
            }catch(Exception $e){
                $this->rollback();
                return $this->exception_error($e);
            }
            $this->commit();
        }
        return true;
    }

    /**
     * 创建补课学员记录
     * @return bool
     * @throws Exception
     */
    protected function createMakeupArrangeStudents(){
        $ca_info = $this->getData();
        $ca_id   = $ca_info['ca_id'];
        $w_cas['ca_id'] = $ca_id;
        $ex_cas = $this->m_course_arrange_student->where($w_cas)->find();

        //如果班级已经存在排课学生，就不创建了
        if($ex_cas){
            $now_int_day = int_day(time());
            if($now_int_day > $ca_info['int_day']){
                $w_cas['is_in'] = ['>',-1];
                $ex_cas = $this->m_course_arrange_student->where($w_cas)->find();

                if($ex_cas){
                    return true;
                }
            }else{
                return true;
            }
        }

        if($ca_info['is_attendance'] == 2){     //如果已经登记过考勤的，直接从Student_attendance表获取记录
            $w_satt = [];
            $w_satt['ca_id'] = $ca_id;
            $satt_list = get_table_list('student_attendance',$w_satt);
            if($satt_list){
                $cas_fields = ['og_id','sid','is_in','is_consume','ca_id', 'lid', 'cid','sj_id','sg_id','int_day', 'int_start_hour', 'int_end_hour'];
                $this->startTrans();
                try{
                    foreach($satt_list as $satt_info){
                        $w_ex_cas = [];
                        $w_ex_cas['ca_id'] = $satt_info['ca_id'];

                        $w_ex_cas['sid'] = $satt_info['sid'];


                        $ex_cas = get_cas_info($w_ex_cas,false);
                        if ($ex_cas) {
                            continue;       //如果已经存在排课学员，跳过
                        }

                        $cas = [];

                        array_copy($cas, $satt_info, $cas_fields);
                        $cas['is_attendance'] = 1;
                        $cas['is_makeup'] = 1;


                        $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                        if (!$result) {
                            $this->rollback();
                            return $this->sql_add_error('course_arrange_student');
                        }

                    }
                }catch(Exception $e){
                    $this->rollback();
                    return $this->exception_error($e);
                }

                $this->commit();
            }
        }else{
            $w_ma['ca_id'] = $ca_id;

            $ma_list = get_table_list('makeup_arrange',$w_ma);

            if($ma_list){
                $cas_fields = ['og_id','ca_id', 'lid', 'cid','sj_id','sg_id','int_day', 'int_start_hour', 'int_end_hour'];
                $ma_fields = ['sid','satt_id'];
                $this->startTrans();
                try{
                    foreach($ma_list as $ma){
                        $w_ex_cas = [];
                        $w_ex_cas['ca_id'] = $ma['ca_id'];

                        $w_ex_cas['sid'] = $ma['sid'];


                        $ex_cas = get_cas_info($w_ex_cas,false);
                        if ($ex_cas) {
                            continue;       //如果已经存在排课学员，跳过
                        }
                        $cas = [];
                        $cas['is_makeup'] = 1;
                        array_copy($cas, $ca_info, $cas_fields);
                        array_copy($cas,$ma,$ma_fields);

                        if($ma['satt_id'] > 0){
                            $satt_info = get_satt_info($ma['satt_id']);
                            if($satt_info) {
                                $cas['is_in'] = $satt_info['is_in'];
                                $cas['is_consume'] = $satt_info['is_consume'];
                            }
                            $cas['is_attendance'] = 1;
                        }



                        $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                        if (!$result) {
                            $this->rollback();
                            return $this->sql_add_error('course_arrange_student');
                        }


                    }
                }catch(Exception $e){
                    $this->rollback();
                    return $this->exception_error($e);
                }
                $this->commit();
            }
        }


        return true;
    }

    /**
     * 创建班级排课的学员记录
     * @return [type] [description]
     */
    protected function createClassArrangeStudents($refresh = false){
        $ca_info = $this->getData();
        $ca_id   = $ca_info['ca_id'];
        if(!$refresh) {
            $w_cas['ca_id'] = $ca_id;
            $ex_cas = $this->m_course_arrange_student->where($w_cas)->find();
            //如果班级已经存在排课学生，就不创建了
            if ($ex_cas) {
                $now_int_day = int_day(time());
                if ($now_int_day > $ca_info['int_day']) {
                    $w_cas['is_in'] = ['>', -1];
                    $ex_cas = $this->m_course_arrange_student->where($w_cas)->find();

                    if ($ex_cas) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
            return true;
        }
        $course_arrange_int_day = $ca_info['int_day'];
        $now_int_day = int_day(time());


        $course_arrange_time = strtotime(int_day_to_date_str($course_arrange_int_day).' 23:59:59');

        $w_cs['cid']     = $ca_info['cid'];
        $w_cs['is_end']  = 0;
        $w_cs['status']  = ['LT',2];
        $w_cs['in_time'] = ['LT',$course_arrange_time];


        $cs_list = get_table_list('class_student',$w_cs);



        if($cs_list){
            $cas_fields = ['og_id','bid','ca_id', 'lid', 'cid','sj_id','sg_id','grade','int_day', 'int_start_hour', 'int_end_hour',
                'consume_source_type','consume_lesson_amount'];
            $satt_fields = ['sid','satt_id','is_in','is_leave','is_makeup','is_consume'];
            $day_fields = ['int_day','int_start_hour','int_end_hour'];
            //获得当天的请假记录
            $slv_map = $this->get_student_leave_map($ca_id);

            $this->startTrans();
            try {
                foreach ($cs_list as $cs) {
                    //先判断是否已经考过勤的
                    if($now_int_day > $course_arrange_int_day){
                        $w_satt = [];
                        $w_satt['sid'] = $cs['sid'];
                        array_copy($w_satt,$ca_info,$day_fields);

                        $satt_info = get_satt_info($w_satt,false);


                        if($satt_info){
                            $cas = [];
                            array_copy($cas,$ca_info,$cas_fields);
                            array_copy($cas,$satt_info,$satt_fields);

                            $cas['is_attendance'] = 1;

                            $w_cas_ex = [];
                            $w_cas_ex['sid'] = $cs['sid'];
                            array_copy($w_cas_ex,$cas,$day_fields);

                            $m_course_arrange_student = new CourseArrangeStudent();

                            $ex_cas = $m_course_arrange_student->where($w_cas_ex)->find();
                            if($ex_cas){
                                $update_cas = [];
                                $update_cas['is_attendance'] = 1;
                                array_copy($update_cas,$cas,$satt_fields);
                                $w_cas_update['cas_id'] = $ex_cas['cas_id'];

                                $result = $m_course_arrange_student->save($update_cas,$w_cas_update);
                                if (false === $result) {
                                    $this->rollback();
                                    return $this->sql_save_error('course_arrange_student');
                                }
                            }else{
                                $result = $m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                                if (!$result) {
                                    $this->rollback();
                                    return $this->sql_add_error('course_arrange_student');
                                }
                            }
                            continue;
                        }
                    }
                    /*
                    if ($cs['out_time'] > 0 && $cs['out_time'] > $cs['in_time'] && $cs['out_time'] < $course_arrange_time) {      //排除掉在排课日期退班的学员

                        continue;
                    }
                    */
                    $cas = [];
                    array_copy($cas, $ca_info, $cas_fields);

                    $cas['sid'] = $cs['sid'];

                    $w_ex_cas = [];
                    $w_ex_cas['ca_id'] = $cas['ca_id'];
                    $w_ex_cas['sid'] = $cs['sid'];

                    $ex_cas = get_cas_info($w_ex_cas,false,false);
                    if ($ex_cas) {

                        continue;       //如果已经存在排课学员，跳过
                    }


                    if (isset($slv_map[$cs['sid']])) {
                        $cas['is_leave'] = 1;
                        $cas['remark'] = $slv_map[$cs['sid']]['reason'];
                    }

                    $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                    if (!$result) {
                        $this->rollback();
                        return $this->sql_add_error('course_arrange_student');
                    }
                }
            }catch(\Exception $e){
                $this->rollback();
                return $this->exception_error($e);
            }
            $this->commit();
        }

        return true;
    }

    /**
     * 更新试听学员列表
     */
    public function refreshTrialStudents($ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return false;
        }
        $cas_list = [];
        $w_tla['ca_id'] = $ca_id;

        $tla_list = get_table_list('trial_listen_arrange',$w_tla);

        if(!$tla_list){
            return $cas_list;
        }

        $cas_fields = ['og_id','ca_id', 'lid', 'cid','sj_id','sg_id','int_day', 'int_start_hour', 'int_end_hour'];

        $this->startTrans();
        try {
            foreach ($tla_list as $tla) {
                $w_ex_cas = [];
                $w_ex_cas['ca_id'] = $tla['ca_id'];

                $cas = [];
                $cas['is_trial'] = 1;

                if ($tla['is_student']) {
                    $cas['sid'] = $tla['sid'];
                    $w_ex_cas['sid'] = $tla['sid'];
                } else {
                    $cas['cu_id'] = $tla['cu_id'];
                    $w_ex_cas['cu_id'] = $tla['cu_id'];
                }
                array_copy($cas, $ca_info, $cas_fields);

                $ex_cas = get_cas_info($w_ex_cas,false);
                if ($ex_cas) {
                    array_push($cas_list,$ex_cas);
                    continue;       //如果已经存在排课学员，跳过
                }

                $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                if (!$result) {
                    $this->rollback();
                    return $this->sql_add_error('course_arrange_student');
                }
                $cas_id = $this->m_course_arrange_student->cas_id;

                $cas_info = get_cas_info($cas_id);

                array_push($cas_list,$cas_info);
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return $cas_list;
    }

    /**
     * @param $ca_id
     */
    public function refreshMakeupStudents($ca_id = 0){
        $ca_info = $this->init_cainfo($ca_id);
        if(!$ca_info){
            return false;
        }
        $cas_list = [];
        $w_ma['ca_id'] = $ca_id;

        $ma_list = get_table_list('makeup_arrange',$w_ma);

        if(!$ma_list){
            return $ma_list;
        }

        $cas_fields = ['og_id','ca_id', 'lid', 'cid','sj_id','sg_id','int_day', 'int_start_hour', 'int_end_hour'];

        $this->startTrans();
        try {
            foreach ($ma_list as $ma) {
                $w_ex_cas = [];
                $w_ex_cas['ca_id'] = $ma['ca_id'];

                $cas = [];
                $cas['is_makeup'] = 1;
                $cas['sid'] = $ma['sid'];
                array_copy($cas, $ca_info, $cas_fields);

                $ex_cas = get_cas_info($w_ex_cas,false);
                if ($ex_cas) {
                    array_push($cas_list,$ex_cas);
                    continue;       //如果已经存在排课学员，跳过
                }

                $result = $this->m_course_arrange_student->data([])->isUpdate(false)->save($cas);
                if (!$result) {
                    $this->rollback();
                    return $this->sql_add_error('course_arrange_student');
                }
                $cas_id = $this->m_course_arrange_student->cas_id;

                $cas_info = get_cas_info($cas_id);

                array_push($cas_list,$cas_info);
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return $cas_list;
    }


}