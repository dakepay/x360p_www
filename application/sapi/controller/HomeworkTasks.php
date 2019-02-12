<?php
/**
 * Author: luo
 * Time: 2018/3/21 18:16
 */

namespace app\sapi\controller;

use app\sapi\model\HomeworkTask;
use app\common\db\Query;
use app\sapi\model\ClassStudent;
use app\sapi\model\HomeworkComplete;
use app\sapi\model\HomeworkView;
use think\Request;

class HomeworkTasks extends Base
{

    public function get_list(Request $request)
    {
        $sid = global_sid();
        $input = $request->get();
        if($sid <= 0) return $this->sendError(400, '学生id错误');
//        $time_end = strtotime('+30 days');
//        $time_before = strtotime('-30 days');

        $m_cs = new ClassStudent();
        $cids = $m_cs->where('sid', $sid)->where('status', ClassStudent::STATUS_NORMAL)->column('cid');

        /** @var Query $m_ht */
        $m_ht = new HomeworkTask();
        if(!empty($cids)) {
            $where = sprintf('cid in (%s) or sid = %s or find_in_set(%s, sids)', implode(',', $cids), $sid, $sid);
        } else {
            $where = sprintf('sid = %s or find_in_set(%s, sids)', $sid, $sid);
        }
//        $homework_list = $m_ht->where($where)->where('create_time', 'between', [$time_before, $time_end])
//        ->order('ht_id', 'desc')->with(['employee'])->getSearchResult($input);
        $homework_list = $m_ht->where($where)->order('ht_id', 'desc')->with(['employee'])->getSearchResult($input);

        $mHomeworkComplete = new HomeworkComplete();
        foreach($homework_list['list'] as $key => $row) {
            if($row['cid']){
                $homework_list['list'][$key]['class_name'] = get_class_name($row['cid']);
            }

            $rs = $mHomeworkComplete->where(['sid' => $sid,'ht_id' => $row['ht_id']])->find();
            if(!empty($rs)){
                unset($homework_list['list'][$key]);
            }
        }
        $homework_list['list'] =  array_values($homework_list['list']);

        $homework_list['total'] = count($homework_list['list']);

        return $this->sendSuccess($homework_list);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $sid = global_sid();
        if($sid <= 0) return $this->sendError(400,'学生身份不明');

        /** @var HomeworkTask $homework */
        $homework = HomeworkTask::get($id, ['student','oneClass','homeworkAttachment', 'employee']);
        if(!empty($homework)) {
            $m_hv = new HomeworkView();
            $view = $m_hv->where(['sid' => $sid, 'ht_id' => $homework->ht_id])->find();
            if(empty($view)) {
                $data = [
                    'sid' => $sid,
                    'ht_id' => $homework->ht_id,
                ];
                $rs = $m_hv->isUpdate(false)->save($data);
                if($rs === false) return $this->sendError(400, $m_hv->getErrorMsg());
            } else {
                $view->setInc('times');
            }
        }
        $homework['homework_complete'] = HomeworkComplete::get(['sid' => $sid, 'ht_id' => $homework->ht_id],
            ['homeworkAttachment','homeworkReply.HomeworkAttachment']);
        return $this->sendSuccess($homework);
    }

    public function post_homework_complete(Request $request)
    {
        $post = $request->post();
        $attachment_data = isset($post['homework_attachment']) ? $post['homework_attachment'] : [];
        $m_hc = new HomeworkComplete();
        $rs = $m_hc->addComplete($post, $attachment_data);
        if($rs === false) return $this->sendError(400, $m_hc->getErrorMsg());

        return $this->sendSuccess();
    }

}