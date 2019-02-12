<?php
/**
 * Author: luo
 * Time: 2018/1/9 14:56
 */

namespace app\api\controller;

use app\api\model\Student;
use app\api\model\StudentAttendSchoolLog;
use app\common\db\Query;
use think\Exception;
use think\Log;
use think\Request;

class StudentAttendSchoolLogs extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        /** @var Query $m_sasl */
        $m_sasl = new StudentAttendSchoolLog();
        $ret = $m_sasl->getSearchResult($get);

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  添加记录
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();

        if(isset($post['sid'])) {
            $m_attend_log = new StudentAttendSchoolLog();

            $result = $m_attend_log->addOneLog($post);

            if(!$result){
                return $this->sendError($m_attend_log->getError(),400);
            }
        } elseif(isset($post['sids'])) {
            $m_sasl = new StudentAttendSchoolLog();

            is_string($post['sids']) && $post['sids'] = explode(',', $post['sids']);
            $result = $m_sasl->addLogs($post, $post['sids']);
            if($result === false) return $this->sendError(400, $m_sasl->getErrorMsg());
        } elseif(isset($post['card_no'])){
            $sid = (new Student())->where('card_no', $post['card_no'])->value('sid');
            if(empty($sid)) return $this->sendError('卡号没找到相应学员');

            $post['sid'] = $sid;
            $m_attend_log = new StudentAttendSchoolLog();
            $result = $m_attend_log->addOneLog($post);
            if(!$result){
                return $this->sendError(400, $m_attend_log->getError());
            }

        } else {
            return $this->sendError(400, 'param error');
        }

        return $this->sendSuccess($result);

        //添加到离校记录
        /*
        $rs = $m_attend_log->addOneLog($post);
        if($rs === false) return $this->sendError(400, $m_attend_log->getErrorMsg());

        try {
            if (isset($post['is_attend']) && $post['is_attend'] == 1) {
                $action_name = '已到校';
            }
            if (isset($post['is_leave']) && $post['is_leave'] == 1) {
                $action_name = '已离校';
            }
            if (isset($action_name)) {
                $m_attend_log->wechat_tpl_notify($post['sid'], $action_name);
            }
        } catch (Exception $e) {
            Log::record('到校通知发送失败，sid:',$post['sid']);
        }

        return $this->sendSuccess();
        */
    }

    /**
     * @desc  刷卡到校离校
     * @author luo
     * @method POST
     */
    public function swipe_card()
    {
        $card_no = input('card_no');
        if(empty($card_no)) return $this->sendError(400, 'card_no error');

        $student = (new Student())->where('card_no', $card_no)->find();
        if(empty($student)) return $this->sendError(400, '不存在此学生');

        $log_data = [
            'sid' => $student['sid'],
            'int_day' => date('Ymd', time()),
        ];

        $m_attend_log = new StudentAttendSchoolLog();
        $log = $m_attend_log->where('sid', $log_data['sid'])->where('int_day', $log_data['int_day'])->find();

        if(empty($log) || $log['is_attend'] == 0) {
            $log_data['is_attend'] = 1;
        } else {
            $log_data['is_leave'] = 1;
        }

        $rs = $m_attend_log->addOneLog($log_data);
        if($rs === false) return $this->sendError(400, $m_attend_log->getErrorMsg());

        return $this->sendSuccess();
    }

}