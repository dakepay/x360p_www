<?php

namespace app\api\controller;


use app\api\model\Lesson;
use app\api\model\OrderItem;
use app\api\model\OrderRefundItem;
use app\api\model\OrderTransferItem;
use app\api\model\Student;
use app\api\model\StudentLesson;
use app\api\model\StudentLessonHour;
use app\api\model\ClassStudent;
use app\api\model\StudentAttendance;
use think\Request;

class FixData extends Base
{

    protected $error_message = '';

    public function split_student_lesson(Request $request){
        $m_oi = new OrderItem();
        $deal_result = [];
        $w_oi['lid'] = 0;
        $w_oi['sl_id'] = ['NEQ',0];
        $w_oi['origin_lesson_hours'] = 10;

        $oi_list = $m_oi->where($w_oi)->select();
        foreach($oi_list as $oi){
            $result = $this->do_split($oi);
            $dr = [
                'oi_id'=>$oi->oi_id,
                'sid'   => $oi->sid,
                'result'=> $result
            ];
            if($result != 1){
                $dr['message'] = $this->error_message;
            }
            array_push($deal_result,$dr);
        }

        return $this->sendSuccess($deal_result);
    }

    protected function startTrans(){
        \Think\Db::startTrans();
    }

    protected function commit(){
        \Think\Db::commit();
    }

    protected function rollback(){
        \Think\Db::rollback();
    }


    public function do_split(OrderItem $oi){
        $this->error_message = '';
        $m_oi = new OrderItem();
        $m_sl = new StudentLesson();
        $m_cs = new ClassStudent();
        $m_slh = new StudentLessonHour();
        $m_satt = new StudentAttendance();
        $m_student = new Student();

        $w_oi['sl_id'] = $oi->sl_id;

        $all_oi_list = $m_oi->where($w_oi)->select();

        if($all_oi_list && count($all_oi_list) == 1){
            $this->error_message = '不需要处理';
            return 0;//不需要处理
        }


        //开始处理
        $sid = $oi->sid;
        $w_cs['sid'] = $sid;
        $w_cs['status'] = 1;
        $all_student_cids = [];
        $cs_list = $m_cs->where($w_cs)->select();
        foreach($cs_list as $cs){
            array_push($all_student_cids,$cs->cid);
        }

        $need_fix_sl = null;
        $w_sl['sid'] = $sid;
        $sl_student_cids = [];
        $sl_list = $m_sl->where($w_sl)->select();
        foreach($sl_list as $_sl){
            array_push($sl_student_cids ,$_sl['cid']);
            if($_sl['sl_id'] == $oi['sl_id']){
                $need_fix_sl = $_sl;
            }
        }

        if(is_null($need_fix_sl)){
            $this->error_message = '找不到需要处理的student_lesson记录';
            return -1;
        }

        $new_cid = 0;
        foreach($all_student_cids as $cid){
            if(!in_array($cid,$sl_student_cids)){
                $new_cid = $cid;
                break;
            }
        }

        if($new_cid == 0){
            $this->error_message = '找不到新报名的班级记录id';
            return -2;      //出错
        }

        //获取新student_lesson应扣除的课时数
        $w_slh['cid'] = $new_cid;
        $w_slh['sid'] = $sid;

        $new_used_hours = 0;
        $new_hours = $oi['origin_lesson_hours'];
        $slh_list = $m_slh->where($w_slh)->select();

        foreach($slh_list as $slh){
            $new_used_hours += $slh['lesson_hours'];
        }



        //exit('cid'.$new_cid.',used_hours:'.$new_used_hours);

        $this->startTrans();

        try {

            //创建新的student_lesson;
            $need_fix_sl_data = $need_fix_sl->toArray();

            $new_sl = [];
            $new_sl['sid'] = $sid;
            $new_sl['cid'] = $new_cid;
            $new_sl['origin_lesson_hours'] = $new_hours;
            $new_sl['origin_lesson_times'] = $new_hours;
            $new_sl['lesson_hours'] = $new_hours;
            $new_sl['lesson_times'] = $new_hours;
            $new_sl['use_lesson_hours'] = $new_used_hours;
            $new_sl['remain_lesson_hours'] = $new_sl['lesson_hours'] - $new_used_hours;



            array_copy($new_sl, $need_fix_sl_data, ['og_id', 'bid', 'sj_ids', 'need_ac_nums', 'ac_status', 'ac_nums', 'lesson_status', 'last_attendance_time']);
            $result = $m_sl->isUpdate(false)->data([])->save($new_sl);

            if (!$result) {
                exception($m_sl->getLastSql());
            }

            $new_sl_id = $m_sl->sl_id;
            //更新orderitem
            $oi->sl_id = $new_sl_id;
            $result = $oi->save();
            if(false === $result){
               exception($oi->getLastSql());
            }
            //更新student_lesson_hours记录
            foreach($slh_list as $slh){
                $slh->sl_id = $new_sl_id;
                $result = $slh->save();
                if(false === $result){
                    exception($slh->getLastSql());
                }
            }
            //更新student_attendance记录
            $update_satt['sl_id'] = $new_sl_id;
            $w_satt_update['cid'] = $new_cid;
            $w_satt_update['sid'] = $sid;

            $result = $m_satt->save($update_satt,$w_satt_update);

            if(false === $result){
                exception($m_satt->getLastSql());
            }
            //修复老的student_lesson;
            $need_fix_sl->origin_lesson_hours = $need_fix_sl->origin_lesson_hours - $new_hours;
            $need_fix_sl->lesson_hours = $need_fix_sl->lesson_hours - $new_hours;
            $need_fix_sl->use_lesson_hours = $need_fix_sl->use_lesson_hours - $new_used_hours;
            $need_fix_sl->remain_lesson_hours = $need_fix_sl->lesson_hours - $need_fix_sl->use_lesson_hours;

            //查找最后一次考勤记录时间
            $last_slh = $m_slh->where('sl_id',$need_fix_sl->sl_id)->order('create_time DESC')->find();
            if($last_slh){
                $need_fix_sl->last_attendance_time = $last_slh->getData('create_time');
            }

            $result = $need_fix_sl->save();

            if(false === $result){
                exception($need_fix_sl->getLastSql());
            }

            //更新剩余课时
            $m_student->updateLessonHours($sid);

        }catch(Exception $e){
            $this->error_message = $e->getMessage();
            $this->rollback();
            return -9;  //数据库出错
        }

        $this->commit();

        return 1;


    }

}