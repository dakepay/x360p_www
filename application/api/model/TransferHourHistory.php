<?php 

namespace app\api\model;

class TransferHourHistory extends Base
{
	protected $hidden = [
        'update_time', 
        'is_delete', 
        'delete_time', 
        'delete_uid'
    ];


    /**
     * 撤销转让课时
     * @param $thh_id
     */
	public function delTransferHour($thh_id)
    {
        $m_transfer_hour = $this->find(['thh_id' => $thh_id]);
        if (empty($m_transfer_hour)){
            return $this->user_error('转让课时记录不存在');
        }

        $from_sl_info = get_sl_info($m_transfer_hour['from_sl_id']);
        $to_sl_info = get_sl_info($m_transfer_hour['to_sl_id']);

        if (!$from_sl_info) {
            return $this->user_error('撤回接收学员课时不存在!');
        }
        if (!$to_sl_info) {
            return $this->user_error('撤回学员课时不存在!');
        }

        $from_student_info = get_student_info($m_transfer_hour['from_sid']);
        $to_student_info = get_student_info($m_transfer_hour['to_sid']);

        if (!$from_student_info) {
            return $this->user_error('撤回接受学员不存在!');
        }
        if (!$to_student_info) {
            return $this->user_error('撤回学员不存在!');
        }

        if ($to_sl_info['lesson_hours'] < $m_transfer_hour['lesson_hours']) {
            return $this->user_error('剩余课时不足撤回课时数!');
        }

        if ($from_sl_info['lesson_status'] == 2) {
            return $this->user_error('撤回课时已经结课，不能撤回!');
        }
        if ($to_sl_info['lesson_status'] == 2) {
            return $this->user_error('撤回接收课时已经结课，不能撤回!');
        }

        $this->startTrans();
        try {
            $mStudent = new Student();
            $mStudentLesson = new StudentLesson();

            $update_to_sl['remain_lesson_hours'] = $to_sl_info['remain_lesson_hours'] - $m_transfer_hour['lesson_hours'];
            $update_to_sl['lesson_hours'] = $to_sl_info['lesson_hours'] - $m_transfer_hour['lesson_hours'];
            $update_to_sl['trans_in_lesson_hours'] = $to_sl_info['trans_in_lesson_hours'] - $m_transfer_hour['lesson_hours'];
            $update_to_sl['lesson_amount']        = $to_sl_info['lesson_amount'] - $m_transfer_hour['lesson_amount'];
            $update_to_sl['remain_lesson_amount'] = $to_sl_info['remain_lesson_amount'] - $m_transfer_hour['lesson_amount'];

            $w_update_to_sl['sl_id'] = $to_sl_info['sl_id'];
            $result = $mStudentLesson->save($update_to_sl, $w_update_to_sl);
            if (false === $result) {
                $this->rollback();
                return $this->sql_save_error('student_lesson');
            }


            $update_from_sl['lesson_hours'] = $from_sl_info['lesson_hours'] + $m_transfer_hour['lesson_hours'];
            $update_from_sl['remain_lesson_hours'] = $from_sl_info['remain_lesson_hours'] + $m_transfer_hour['lesson_hours'];
            $update_from_sl['trans_out_lesson_hours'] = $from_sl_info['trans_out_lesson_hours'] - $m_transfer_hour['lesson_hours'];

            if($from_sl_info['lesson_amount'] > 0){
                $update_from_sl['lesson_amount']        = min_val($from_sl_info['lesson_amount'] + $m_transfer_hour['lesson_amount']);
                $update_from_sl['remain_lesson_amount'] = min_val($from_sl_info['remain_lesson_amount'] + $m_transfer_hour['lesson_amount']);
            }

            $w_update_from_sl['sl_id'] = $from_sl_info['sl_id'];

            $m_from_sl = new StudentLesson();
            $result = $m_from_sl->save($update_from_sl, $w_update_from_sl);

            if (false === $result) {
                $this->rollback();
                return $this->sql_save_error('student_lesson');
            }

            $m_transfer_hour->delete();

            $result = $mStudent->updateLessonHours($m_transfer_hour['from_sid']);
            if (false === $result) {
                $this->rollback();
                return false;
            }

            $result = $mStudent->updateLessonHours($m_transfer_hour['to_sid']);
            if (false === $result) {
                $this->rollback();
                return false;
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
        }


}