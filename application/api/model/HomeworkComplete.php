<?php
/**
 * Author: luo
 * Time: 2018/3/21 11:21
 */

namespace app\api\model;


class HomeworkComplete extends Base
{

    public function getRejectedTimeAttr($value)
    {
        if ($value > 0){
            return date('Y-m-d H:i:s',$value);
        }else{
            return 0;
        }
    }

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('bid,sid,student_name,sex,photo_url');
    }

    public function homeworkAttachment()
    {
        return $this->hasMany('HomeworkAttachment', 'hc_id', 'hc_id');
    }

    public function homeworkReply()
    {
        return $this->hasOne('HomeworkReply', 'hc_id', 'hc_id');
    }

    public function homeworkTask()
    {
        return $this->hasOne('HomeworkTask', 'ht_id', 'ht_id');
    }

    /**
     * 作业驳回
     * @param $hc_id
     */
    public function rejectHomework($hc_id,$rejected_reason)
    {
        $homework = $this->where('hc_id',$hc_id)->find();
        if (empty($homework)){
            return $this->user_error('作业不存在');
        }

        $update['is_rejected'] = 1;
        $update['rejected_time'] = time();
        $update['rejected_reason'] = $rejected_reason;
        $update['is_check'] = 1;

        $w['hc_id'] = $hc_id;
        $result = $this->save($update,$w);
        if (false === $result){
            return $this->sql_save_error('homework_complete');
        }
        $this->rejected_Remind($homework,$rejected_reason);

        return true;
    }


    /**
     *  自动推送老师课前提醒
     * @param $data
     */
    public function rejected_Remind($data,$rejected_reason){

        $mMessage = new Message();
        try {
            $student_name = get_student_name($data['sid']);

            $task_data['hc_id'] = $data['hc_id'];
            $task_data['subject'] = '作业驳回通知';
            $task_data['student_name'] = $student_name;
            $task_data['remark'] = $rejected_reason;
            $task_data['sid'] = $data['sid'];

            $rs = $mMessage->sendTplMsg('homework_rejected',$task_data ,[],2);
            if($rs === false) return $this->user_error($mMessage->getError());
        } catch(\Exception $e) {
            log_write($e->getFile() . ' ' . $e->getLine() . ' '. $e->getMessage(), 'error');
        }

        return true;
    }

}