<?php
/**
 * Author: luo
 * Time: 2018/3/20 15:50
 */

namespace app\api\controller;


use app\api\model\HomeworkComplete;
use app\api\model\HomeworkTask;
use app\api\model\HomeworkView;
use app\api\model\Student;
use app\api\model\Classes;
use app\common\db\Query;
use think\Exception;
use think\Log;
use think\Request;

class HomeworkTasks extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        $mHomeWorkTask = new HomeworkTask();

        if(isset($input['eid'])){
            $eid = $input['eid'];
            $login_user = gvar('user');
            $login_employee = $login_user['employee'];
            $login_eid = $login_employee['eid'];
            $where_str = "eid = {$eid}";
            if($login_eid == $input['eid'] ){
                $eid = $input['eid'];
                $where_cls_str = "find_in_set({$eid}, second_eids) or teach_eid = {$eid} or edu_eid = {$eid}";
                $mClass = new Classes();
                $my_cls_list = $mClass
                    ->where($where_cls_str)
                    ->where('status','LT',2)
                    ->select();
                if($my_cls_list){
                    $cids = [];
                    foreach($my_cls_list as $c){
                        array_push($cids,$c['cid']);
                    }
                    $where_str .= " or cid in (".implode(',',$cids).")";
                    //$input['cid'] = '[IN,'.implode(',',$cids).']';
                }
            }
            $mHomeWorkTask->where($where_str);
            unset($input['eid']);
        }

        $ret = $mHomeWorkTask
            ->with(['student','oneClass'])
            ->withCount('homeworkComplete')
            ->getSearchResult($input);

        $mStudent = new Student();
        foreach($ret['list'] as &$row) {
            $students = [];
            if(isset($row['sids']) && !empty($row['sids'])) {
                $sids = is_array($row['sids']) ? $row['sids'] : explode(',', $row['sids']);
                $students = $mStudent->where('sid', 'in', $sids)->field('sid,bid,student_name,sex,photo_url')->select();
            }
            $row['students'] = $students;
            $row['students_count'] = $mHomeWorkTask->getStudentsCount($row['ht_id']);
        }
        return $this->sendSuccess($ret);
    }

    public function get_detail(Request $request, $id = 0)
    {
        /** @var HomeworkTask $homework */
        $homework = HomeworkTask::get($id, ['student','oneClass','homeworkAttachment', 'homeworkComplete']);
        if(empty($homework)) return $this->sendError(400, '作业任务不存在');
        $complete_sids = !empty($homework['homework_complete']) ? array_column($homework['homework_complete'], 'sid') : [];
        $students = $homework->getStudentsOfHomework($homework->ht_id);

        $m_hv = new HomeworkView();
        foreach($students as $key => &$stu) {
            if(in_array($stu['sid'], $complete_sids)) {
                unset($students[$key]);
            }
            $view = $m_hv->where('ht_id', $homework['ht_id'])->where('sid', $stu['sid'])->find();
            $stu['homework_view'] = empty($view) ? null : $view->toArray();
        }
        $homework['incomplete_students']= array_values($students);

        return $this->sendSuccess($homework);
    }

    /**
     * @desc  作业任务完成列表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list_homework_completes(Request $request)
    {
        $ht_id = input('id');
        $get = $request->get();
        $m_hc = new HomeworkComplete();
        $with = ['student', 'homeworkAttachment', 'homeworkReply.homeworkAttachment'];
        $ret = $m_hc->where('ht_id', $ht_id)->with($with)->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  添加作业任务
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        $attachment_data = isset($post['homework_attachment']) ? $post['homework_attachment'] : [];

        $m_ht = new HomeworkTask();
        $rs = $m_ht->addOneHomework($post, $attachment_data);
        if($rs === false) return $this->sendError(400, $m_ht->getErrorMsg());

        if(isset($post['is_push']) && $post['is_push']) {
            try {
                $students = $m_ht->getStudentsOfHomework($m_ht->ht_id);
                $m_ht->wechat_tpl_notify('after_class_push', $m_ht->make_wechat_data(), $students);
            }  catch (\Exception $e) {
                Log::record('作业推送失败，ht_id:'.$m_ht->ht_id, 'wechat');
                return $this->sendError(400, '作业发布成功，推送失败，原因：' . $e->getMessage());
            }

            $m_ht->push_status = 1;
            $m_ht->allowField('push_status')->save();
        }

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $ht_id = input('id');
        $put = $request->put();
        $attachment_data = isset($put['homework_attachment']) ? $put['homework_attachment'] : [];
        /** @var HomeworkTask $homework */
        $homework = HomeworkTask::get($ht_id);
        $rs = $homework->edit($put, $attachment_data);
        if($rs === false) return $this->sendError(400, $homework->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $ht_id = input('id');
        $mHomeworkComplete = new HomeworkComplete;
        $complete = $mHomeworkComplete->where(['ht_id' => $ht_id])->select();
        if(!empty($complete)) return $this->sendError(400, '已有完成的作业，删除不了。');
        /** @var HomeworkTask $homework */
        $homework = HomeworkTask::get($ht_id);
        $rs = $homework->delHomework();
        if($rs === false) return $this->sendError(400, $homework->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * @desc  公众号推送作业
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function wechat_notify(Request $request)
    {
        $ht_id = input('ht_id');
        $homework = HomeworkTask::get($ht_id);
        if(empty($homework)) return $this->sendError(400, '作业不存在');
        $sid = input('sid/d');
        if(empty($sid)) {
            $students = $homework->getStudentsOfHomework($homework['ht_id']);
        } else {
            $student = Student::get($sid);
            if(empty($student)) return $this->sendError(400, '学生不存在');
            $students = [
                $student,
            ];
        }

        try {
            $rs = $homework->wechat_tpl_notify('after_class_push', $homework->make_wechat_data(), $students);
        } catch (\Exception $e) {
            Log::record('作业推送失败，ht_id:'.$ht_id, 'wechat');
            return $this->sendError(400, $e->getMessage());
        }

        if ($rs === false) return $this->sendError(400, $homework->getErrorMsg());

        $homework->push_status = 1;
        $homework->allowField('push_status')->save();

        return $this->sendSuccess();
    }

}