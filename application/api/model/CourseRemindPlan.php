<?php

namespace app\api\model;

class CourseRemindPlan extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    /**
     * 设置自动推送课前提醒任务
     * @param $og_id
     * @param $bid
     * @param $input
     * @return bool
     */
    public function setAutoPushRemindCourseTask($og_id,$bid,$input){
        $client = gvar('client');
        $cid = $client['cid'];
        $this->startTrans();

        try {
            if (!empty($input['day0_push_int_hour'])){
                $input['day0_push_int_hour'] = format_int_hour($input['day0_push_int_hour']);
            }
            if (!empty($input['dayn_push_int_hour'])){
                $input['dayn_push_int_hour'] = format_int_hour($input['dayn_push_int_hour']);
            }
            $w['bid'] = $bid;
            $old_crp_list = $this->where($w)->select();
            if($old_crp_list){
                $m_crp = $old_crp_list[0];
                $result = $m_crp->save($input);
                if(false === $result){
                    return $this->sql_save_error('course_remind_plan');
                }
            }else{
                $m_crp = new self();
                $result = $m_crp->allowField(true)->isUpdate(false)->save($input);
                if (!$result) {
                    return $this->sql_add_error('course_remind_plan');
                }
            }

            $now_time = time();
            $now_date_str = date('Y-m-d',$now_time);
            $now_int_hour  = int_hour(time());

            if (isset($input['day1_push']) || isset($input['day2_push']) || isset($input['day3_push'])){
                $dayn_int_hour = intval($input['dayn_push_int_hour']);
                $dayn_task_excute_time = strtotime($dayn_int_hour);

                if($now_int_hour > $dayn_int_hour){
                    $dayn_task_excute_time += 86400;
                }
                $dayn_delay = $dayn_task_excute_time - $now_time;
            }

            if(isset($input['day0_push'])){
                $task_id = queue_task_id('course_remind_day0',$bid);
                queue_cancel($task_id);
                if(intval($input['day0_push']) == 1){
                    $data = [
                        'job' => "app\common\job\AutoPushCourseRemind",
                        'cid'=>$cid,
                        'og_id' => $og_id,
                        'bid' => $bid,
                        'day' => 0,
                        'is_push_teacher' =>intval($input['is_push_teacher']),
                        'task_id' => $task_id,
                    ];


                    $day0_int_hour = intval($input['day0_push_int_hour']);
                    $date_str = sprintf("%s %s",$now_date_str,int_hour_to_hour_str($day0_int_hour));

                    $task_excute_time  = strtotime($date_str);

                    if($now_int_hour > $day0_int_hour){
                        $task_excute_time += 86400;
                    }

                    $delay = $task_excute_time - $now_time;

                    queue_push('AutoPushCourseRemind', $data, 'AutoPushCourseRemind', $delay, $task_id);
                }
            }
            if(isset($input['day1_push'])){
                $task_id = queue_task_id('course_remind_day1',$bid);
                queue_cancel($task_id);
                if($input['day1_push'] == 1){
                    $data = [
                        'job' => "app\common\job\AutoPushCourseRemind",
                        'cid'   => $cid,
                        'og_id' => $og_id,
                        'bid' => $bid,
                        'day' => 1,
                        'is_push_teacher' =>intval($input['is_push_teacher']),
                        'task_id' => $task_id
                    ];

                    queue_push('AutoPushCourseRemind', $data, 'AutoPushCourseRemind', $dayn_delay, $task_id);
                }
            }
            if(isset($input['day2_push'])){
                $task_id = queue_task_id('course_remind_day2',$bid);
                queue_cancel($task_id);
                if($input['day2_push'] == 1){
                    $data = [
                        'job' => "app\common\job\AutoPushCourseRemind",
                        'cid'   => $cid,
                        'og_id' => $og_id,
                        'bid' => $bid,
                        'day' => 2,
                        'is_push_teacher' =>intval($input['is_push_teacher']),
                        'task_id' => $task_id,
                    ];

                    queue_push('AutoPushCourseRemind', $data, 'AutoPushCourseRemind', $dayn_delay, $task_id);
                }
            }
            if(isset($input['day3_push'])){
                $task_id = queue_task_id('course_remind_day3',$bid);
                queue_cancel($task_id);
                if($input['day3_push'] == 1){
                    $data = [
                        'job' => "app\common\job\AutoPushCourseRemind",
                        'cid'   => $cid,
                        'og_id' => $og_id,
                        'bid' => $bid,
                        'day' => 3,
                        'is_push_teacher' =>intval($input['is_push_teacher']),
                        'task_id' => $task_id,
                    ];

                    queue_push('AutoPushCourseRemind', $data, 'AutoPushCourseRemind', $dayn_delay, $task_id);
                }
            }

        }catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }


}