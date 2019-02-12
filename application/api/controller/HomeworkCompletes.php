<?php
/**
 * Author: luo
 * Time: 2018/3/27 11:39
 */

namespace app\api\controller;


use app\api\model\ClassStudent;
use app\api\model\HomeworkComplete;
use app\api\model\HomeworkReply;
use app\api\model\HomeworkTask;
use app\api\model\HomeworkPublish;
use think\Request;

class HomeworkCompletes extends Base
{

    public function get_detail(Request $request, $id = 0)
    {
        $hc_id = input('id');
        $m_hc = new HomeworkComplete();
        $get = $request->get();
        $with = empty($get['with']) ? [] : $get['with'];
        $homework_complete = $m_hc->with($with)->where('hc_id', $hc_id)->find();
        return $this->sendSuccess($homework_complete);
    }

    public function student_status()
    {
        $sid = input('sid', 0);
        if($sid <= 0) return $this->sendError(400, '学生id错误');
        $input_start_day = input('start_day', 'monday this week');
        $input_end_day = input('end_day', 'sunday this week');
        $time_start = strtotime($input_start_day);
        $time_end = strtotime($input_end_day);

        $m_cs = new ClassStudent();
        $cids = $m_cs->where('sid', $sid)->where('status', ClassStudent::STATUS_NORMAL)->column('cid');

        $m_ht = new HomeworkTask();
        if(!empty($cids)) {
            $where = sprintf('cid in (%s) or sid = %s or find_in_set(%s, sids)', implode(',', $cids), $sid, $sid);
        } else {
            $where = sprintf('sid = %s or find_in_set(%s, sids)', $sid, $sid);
        }

        $homework_num = $m_ht->where($where)->where('create_time', 'between', [$time_start, $time_end])
            ->count();

        $m_hc = new HomeworkComplete();
        $complete_num = $m_hc->where('sid', $sid)->where('create_time', 'between', [$time_start, $time_end])->count();

        $m_hp = new HomeworkPublish();
        $publish_num = $m_hp->where('sid', $sid)->where('create_time', 'between', [$time_start, $time_end])->count();

        $complete_list = $m_hc->where('sid', $sid)->where('create_time', 'between', [$time_start, $time_end])
            ->order('hc_id', 'asc')->field('bid,lid,sid,star,is_check,check_level,result_level')->select();

        $data = [
            'homework_num' => $homework_num,
            'complete_num' => $complete_num,
            'publish_num' => $publish_num,
            'complete_list' => $complete_list
        ];

        return $this->sendSuccess($data);
    }

    /**
     * @desc  作业回复
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post_homework_reply(Request $request)
    {
        $post = $request->post();
        $post['hc_id'] = input('id');
        $attachment_data = isset($post['homework_attachment']) ? $post['homework_attachment'] : [];
        $m_hc = new HomeworkReply();
        $rs = $m_hc->addReply($post, $attachment_data);
        if($rs === false) return $this->sendError(400, $m_hc->getErrorMsg());

        return $this->sendSuccess();

    }

    /**
     * 作业驳回
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function reject_homework(Request $request)
    {
        $input = input();

        $hc_id = $input['hc_id'];
        $check_content = isset($input['rejected_reason']) ? $input['rejected_reason'] : '';
        $mHomeworkComplete = new HomeworkComplete();

        $result = $mHomeworkComplete->rejectHomework($hc_id,$check_content);
        if (false === $result){
            return $this->sendError(400,$mHomeworkComplete->getError());
        }

        return $this->sendSuccess();
    }


}