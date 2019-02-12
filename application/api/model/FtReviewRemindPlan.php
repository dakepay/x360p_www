<?php

namespace app\api\model;

class FtReviewRemindPlan extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function setEidsAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;

    }

    public function getEidsAttr($value)
    {
        $data = [];
        $eids = split_int_array($value);
        foreach ($eids as $k => $eid){
            $data[$k] = get_employee_info($eid);
        }
        return $data;
    }

    public function setIntHourAttr($value)
    {
        return format_int_hour($value);
    }

    public function getIntHourAttr($value)
    {
        return int_hour_to_hour_str($value);
    }

    /**
     * 设置自动推送课前提醒任务
     * @param $og_id
     * @param $bid
     * @param $input
     * @return bool
     */
    public function setAutoPushFtReview($og_id,$bid,$input){
        $client = gvar('client');
        $cid = $client['cid'];

        $this->startTrans();
        try {
            $w['bid'] = $bid;
            $old_plan = $this->where($w)->find();
            if(!empty($old_plan)){
                $result = $old_plan->save($input);
                if(false === $result){
                    return $this->sql_save_error('ft_review_remind_plan');
                }
            }else{
                $result = $this->allowField(true)->isUpdate(false)->save($input);
                if (false === $result) {
                    return $this->sql_add_error('ft_review_remind_plan');
                }
            }


            $now_time = time();
            $now_date_str = date('Y-m-d',$now_time);

            if(isset($input['int_hour'])){
                $task_id = queue_task_id('ft_review_remind_plan',$bid);
                queue_cancel($task_id);

                $data = [
                    'job' => "app\common\job\AutoPushFtReviewRemind",
                    'cid'=>$cid,
                    'og_id' => $og_id,
                    'bid' => $bid,
                    'task_id' => $task_id,
                ];

                $push_time = strtotime($now_date_str.$input['int_hour']);
                if($now_time > $push_time){
                    $push_time += 86400;
                }

                $delay = $push_time - $now_time;
                queue_push('AutoPushFtReviewRemind', $data, 'AutoPushFtReviewRemind', $delay, $task_id);
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }



    public function pushTodayRemindPlan()
    {
        $mFtReview = new FtReview();
        $messages = '';
        $today_statistical = $mFtReview->getReportStats();
        foreach ($today_statistical as $k => $row){
            if ($k == 'today_attendance'){
                $messages .= '今日外教共考勤'.$row.'次。';
            }
            if ($k == 'has_written'){
                $messages .= '今日外教课评报告发表数:'.$row.'次。';
            }
            if ($k == 'not_written'){
                $messages .= '今日未发表外教课评报告发表数:'.$row.'次。';
            }
            if ($k == 'has_translate'){
                $messages .= '今日以翻译外教课评报告数:'.$row.'次。';
            }
            if ($k == 'not_translate'){
                $messages .= '今日未翻译外教课评报告数:'.$row.'次。';
            }
        }

        $w['bid'] = gvar('bid');
        $remind_plan = $this->where($w)->find();
        if (empty($remind_plan)){
            return $this->user_error('该校区未设置自动推送翻译');
        }

        $mMessage = new Message();
        foreach ($remind_plan['eids'] as $eid){
            try {
                $employee_info = get_employee_info($eid);
                $task_data['ftrp_id'] = $remind_plan['ftrp_id'];
                $task_data['subject'] = '每日翻译情况汇总';
                $task_data['content'] = $employee_info['ename'].' 您好，今日翻译汇总如下： '.$messages;
                $task_data['uid'] = $employee_info['uid'];

                $rs = $mMessage->sendTplMsg('ft_review_remind',$task_data ,[],2);
                if($rs === false) return $this->user_error($mMessage->getError());
            } catch(\Exception $e) {
                log_write($e->getFile() . ' ' . $e->getLine() . ' '. $e->getMessage(), 'error');
            }
        }

        return true;
    }


}