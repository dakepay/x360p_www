<?php
/**
 * 20170901 luo 排班model
 */

namespace app\api\model;

use think\Exception;

class ClassSchedule extends Base
{

    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time'];

    public function setIntStartHourAttr($value)
    {
        $value = str_replace(':', '', $value);
        $value = strlen($value) >= 4 ? $value: str_pad($value, 4, 0, STR_PAD_LEFT);
        return intval($value);
    }

    public function setIntEndHourAttr($value)
    {
        $value = str_replace(':', '', $value);
        $value = strlen($value) >= 4 ? $value: str_pad($value, 4, 0, STR_PAD_LEFT);
        return intval($value);
    }

    public function getIntStartHourAttr($value)
    {
        return $this->transformHour($value);
    }

    public function getIntEndHourAttr($value)
    {
        return $this->transformHour($value);
    }

    protected function transformHour($hour)
    {
        $hour = (string)$hour;
        if (strlen($hour) == 3) {
            $hour = '0' . $hour;
        }
        $temp = str_split($hour, 2);
        return implode(':', $temp);
    }

    public function classes()
    {
        return $this->belongsTo('Classes', 'cid');
    }

    /**
     * 创建一个班级排班计划
     * @param  [type] $input [description]
     * @param  [type] $cid   [description]
     * @return [type]        [description]
     */
    public function createOneClassSchedule($input,$cid){
        if(!$cid){
            return $this->input_param_error('cid',1);
        }
        if(!$this->checkInputParam($input,['week_day','int_start_hour','int_end_hour'])){
            return false;
        }

        $data = [];
        $class_data = get_class_info($cid);

        if(!$class_data){
            return $this->user_error('班级ID不存在:'.$cid);
        }

        $parent_cid = $class_data['parent_cid'];
        array_copy($data,$class_data,['bid','cid','teach_eid'=>'eid','lid','year']);

        $data = array_merge($data, $input);

        $data['cid']       = $cid;
        $data['week_day']       = intval($input['week_day']);
        $data['int_start_hour'] = format_int_hour($input['int_start_hour']);
        $data['int_end_hour']   = format_int_hour($input['int_end_hour']);
        $data['cr_id']          = isset($input['cr_id'])?intval($input['cr_id']):$class_data['cr_id'];

        //排除存在的排班计划
        $w_ex['cid']        = $data['cid'];
        $w_ex['week_day']   = $data['week_day'];
        $w_ex['int_start_hour'] = $data['int_start_hour'];
        $w_ex['int_end_hour']   = $data['int_end_hour'];

        $ex_schedule = $this->where($w_ex)->find();

        if($ex_schedule){
            return $this->user_error(sprintf('该班级这个时间段(周%s %s ~ %s)已经有排课计划存在！',$data['week_day'],$data['int_start_hour'],$data['int_end_hour']));
        }

        //排除老师的重复
        unset($w_ex['cid']);

        if($parent_cid > 0){        //应该排除原来升班过来的班级的老师的排课计划
            $w_ex['cid'] = ['NEQ',$parent_cid];
        }
        
        if($data['eid'] > 0){
            $w_ex['eid'] = $data['eid'];

            $ex_schedule = $this->where($w_ex)->find();
            if($ex_schedule){
                $ex_class = get_class_info($ex_schedule['cid']);
                if($ex_class['status'] != 2) {  //排除结课班级

                    $error_msg = sprintf('班级排课计划老师时间冲突，<br/>在同时段,老师:%s 有其他的班级排课计划存在!<br/>班级名称是:%s,时段是:周%s %s ~ %s',
                        get_employee_name($data['eid']),
                        $ex_class['class_name'],
                        $data['week_day'],
                        $data['int_start_hour'],
                        $data['int_end_hour']
                    );

                    return $this->user_error($error_msg);
                }
            }
        }

        //排除教室的重复
        if($data['cr_id'] > 0){
            if(isset($w_ex['eid'])){
                unset($w_ex['eid']);
            }
            $w_ex['cr_id'] = $data['cr_id'];

            $ex_schedule = $this->where($w_ex)->find();

            if($ex_schedule){
                $ex_class = get_class_info($ex_schedule['cid']);
                if($ex_class['status'] != 2) {//排除结课班级
                    $classroom = get_classroom_info($data['cr_id']);
                    $error_msg = sprintf('班级排课计划教室时间冲突，<br/>在同时段,教室:%s 有其他的班级排课计划存在!<br/>班级名称是:%s,时段是:周%s %s ~ %s',
                        $classroom['room_name'],
                        $ex_class['class_name'],
                        $data['week_day'],
                        $data['int_start_hour'],
                        $data['int_end_hour']
                    );
                    return $this->user_error($error_msg);
                }
            }
        }


        $this->startTrans();

        try{
            $result = $this->isUpdate(false)->allowField(true)->save($data);

            if(false === $result){
                $this->rollback();
                return $this->sql_add_error('class_schedule');
            }

            // 添加班级排课计划日志
            $res = ClassLog::addClassScheduleLog($input,$cid);
            if($res === false){
                return $this->sql_add_error('class_log');
            }
            

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return $this->csd_id;

    }

    //luo 增加排班
    public function addSchedule($class, $input)
    {
        $data = [];
        if (!empty($input['second_eids'])){
            $input['second_eids'] = implode(',',$input['second_eids']);
        }

        $data['bid'] = $class->bid;
        $data['cid'] = $class->cid;
        $data['eid'] = $class->teach_eid;
        $data['lid'] = $class->lid;
        $data['year'] = $class->year;
        $data['week_day'] = $input['week_day'];
        $data['int_start_hour'] = $input['int_start_hour'];
        $data['int_end_hour'] = $input['int_end_hour'];
        $data['cr_id'] = isset($input['cr_id']) ? $input['cr_id'] : $class->cr_id;
        $data = array_merge($data, $input);

        $had_schedule = $this->where('cid', $data['cid'])->where('week_day', $data['week_day'])
                        ->where('int_start_hour', format_int_day($data['int_start_hour']))->count();
        if($had_schedule) {
            $this->error = '这个时间已经有排班';
            return false;
        }

        return $this->data([])->isUpdate(false)->allowField(true)->save($data);
    }

    public function batAddSchedule($class,$schedules){

        if(empty($schedules || !is_array($schedules))){
            return $this->input_param_error('schedules');
        }

        $this->startTrans();
        try {
            foreach ($schedules as $schedule) {
                $rs = $this->addSchedule($class,$schedule);
                if (!$rs) {
                    return false;
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    //20170901 luo 增加班级，排班
    public function addClassAndSchedule($input)
    {
        $lesson = Lesson::get($input['lid']);
        if(empty($lesson)) {
            $this->user_error('不存在这个课程');
            return false;
        }

        $input['year'] = date('Y', str_to_time($input['start_lesson_time']));

        $class = new Classes();

        $this->startTrans();
        $result = $class->allowField(true)->save($input);
        if (!$result) {
            $this->rollback();
            $this->user_error($class->getError());
            return false;
        }

        //增加一个排班记录
        $schedule = new ClassSchedule();
        $add_schedule_rs = $schedule->addSchedule($class, $input);
        if(!$add_schedule_rs) {
            $this->rollback();
            $this->user_error($schedule->getError());
            return false;
        }

        $this->commit();
        return true;
    }

    //luo 更新排班信息
    public function updateSchedule($input) {
        //$class = Classes::get($input['cid']);
        //if(empty($class)) {
        //    $this->user_error('班级信息不存在');
        //    return false;
        //}
        //
        //if(isset($input['start_lesson_time'])) {
        //    $input['year'] = date('Y', strtotime($input['start_lesson_time']));
        //}
        //
        ////修改班级信息
        //$update_class_rs = $class->allowField(true)->save($input, ['cid' => $class->cid]);
        //if (!$update_class_rs) {
        //    $this->user_error($class->getError());
        //    return false;
        //}
        $schedule = $this->where('csd_id', $input['csd_id'])->find();
        if(empty($schedule)) return $this->user_error('排班不存在');

        $rs = $schedule->allowField(true)->save($input);
        if($rs === false) return $this->user_error($schedule->getError());

        return true;
    }

    public function deleteSchedule($input) {
        $schedule = self::get($input['csd_id']);
        if(empty($schedule)) return true;
        /*
        多余，不需要判断排课是否存在
        $schedule_int_start_hour = $schedule->getData('int_start_hour');
        $schedule_int_end_hour = $schedule->getData('int_end_hour');
        $schedule_week_day = $schedule->getData('week_day');

        if($schedule['cid'] > 0) {
            $m_ca = new CourseArrange();
            $course_list = $m_ca->where('cid', $schedule['cid'])->select();
            foreach($course_list as $per_course) {
                $tmp_int_day = $per_course->getData('int_day');
                $tmp_int_start_hour = $per_course->getData('int_start_hour');
                $tmp_int_end_hour = $per_course->getData('int_end_hour');
                if(empty($tmp_int_day) || empty($tmp_int_start_hour) || empty($tmp_int_end_hour)) continue;

                $tmp_week_day = date('w', strtotime($tmp_int_day));
                $tmp_week_day = $tmp_week_day == 0 ? 7 : $tmp_week_day;

                if($tmp_week_day == $schedule_week_day && $tmp_int_start_hour == $schedule_int_start_hour
                    && $tmp_int_end_hour == $schedule_int_end_hour) {
                    return $this->user_error('星期' . number2chinese($schedule_week_day) . '有排课，无法删除');
                }
            }
        }
        */
        //todo:这里需要增加一个日志
        $result = $schedule->delete();
        if(false === $result){
            return $this->sql_delete_error('class_schedule');
        }

        return true;
    }

    //更新班级排班
    public function updateScheduleOfClass($cid, $new_schedules)
    {
        $class_info = Classes::get(['cid' => $cid]);
        if(empty($class_info)) return $this->user_error('班级不存在');

        $schedule_model = new ClassSchedule();
        $old_schedules = $schedule_model->where('cid', $cid)->select();

        $data = [
            'cid'            => $class_info['cid'],
            'eid'            => $class_info['teach_eid'],
            'cr_id'          => $class_info['cr_id'],
            'year'           => $class_info['year'],
            'season'         => $class_info['season'],
            'int_start_hour' => $class_info['int_start_hour'],
            'int_end_hour'   => $class_info['int_end_hour'],
        ];

        $this->startTrans();
        try {
            //--1-- 更新排班
            foreach($new_schedules as $per_schedule) {
                $per_schedule['int_start_hour'] = format_int_hour($per_schedule['int_start_hour']);
                $per_schedule['int_end_hour'] = format_int_hour($per_schedule['int_end_hour']);

                foreach($old_schedules as $per_old_schedule) {
                    if($per_old_schedule['week_day'] == $per_schedule['week_day']
                        && $per_old_schedule->getData('int_start_hour') == $per_schedule['int_start_hour']
                        && $per_old_schedule->getData('int_end_hour') == $per_schedule['int_end_hour']) {


                        $update_cs = [];
                        $update_cs['cr_id'] = $per_schedule['cr_id'];
                        $update_cs['consume_lesson_hour'] = $per_schedule['consume_lesson_hour']?$per_old_schedule['consume_lesson_hour']:0;
                        $update_cs['eid'] = $per_schedule['eid'];


                        $schedule_model->where('csd_id', $per_old_schedule['csd_id'])
                            ->update($update_cs);

                        break;
                    }

                }
            }

            //--2-- 增加排班
            foreach($new_schedules as $per_schedule) {
                $per_schedule['int_start_hour'] = format_int_hour($per_schedule['int_start_hour']);
                $per_schedule['int_end_hour'] = format_int_hour($per_schedule['int_end_hour']);

                $flag = true;
                foreach($old_schedules as $per_old_schedule) {
                    if($per_old_schedule['week_day'] == $per_old_schedule['week_day']
                        && $per_old_schedule->getData('int_start_hour') == $per_old_schedule['int_start_hour']
                        && $per_old_schedule->getData('int_end_hour') == $per_old_schedule['int_end_hour']) {
                        $flag = false;
                        break;
                    }
                }

                if($flag) {
                    $data['int_start_hour'] = $per_schedule['int_start_hour'];
                    $data['int_end_hour'] = $per_schedule['int_end_hour'];
                    $data['cr_id'] = $per_schedule['cr_id'];
                    $data['week_day'] = $per_schedule['week_day'];
                    $data['eid'] = $per_schedule['eid'];

                    $is_existed = $schedule_model->where('int_start_hour', format_int_hour($data['int_start_hour']))
                        ->where('int_end_hour', format_int_hour($data['int_end_hour']))
                        ->where('cr_id', $data['cr_id'])->where('week_day', $data['week_day'])
                        ->where('cid', $cid)->count();
                    if(!$is_existed ) {
                        $data['consume_lesson_hour'] = $per_schedule['consume_lesson_hour'] ?? 0;
                        $schedule_model->data([])->isUpdate(false)->save($data);

                        // 添加一条 增加班级排课日志
                        $res = ClassLog::addClassScheduleLog($per_schedule,$cid);
                        if($res === false){
                            return $this->add_sql_error('class_log');
                        }
                    }
                }
            }

            //--3-- 删除排班
            foreach($old_schedules as $per_old_schedule) {

                $del_flag = true;
                foreach($new_schedules as $per_schedule) {
                    $per_schedule['int_start_hour'] = format_int_hour($per_schedule['int_start_hour']);
                    $per_schedule['int_end_hour'] = format_int_hour($per_schedule['int_end_hour']);

                    if($per_schedule['week_day'] == $per_old_schedule['week_day']
                        && $per_schedule['int_start_hour'] == $per_old_schedule->getData('int_start_hour')
                        && $per_schedule['int_end_hour'] == $per_old_schedule->getData('int_end_hour')) {
                        $del_flag = false;
                        break;
                    }
                }

                if($del_flag) {
                    $schedule_model->where('csd_id', $per_old_schedule['csd_id'])->delete();

                    // 添加一条 删除班级排课日志
                    $res = ClassLog::deleteClassScheduleLog($per_old_schedule,$cid);
                    if($res === false){
                        return $this->delete_sql_error('class_log');
                    }
                }

            }

        } catch(\Exception $e) {
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

}
