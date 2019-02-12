<?php
/**
 * Author: luo
 * Time: 2018/4/9 9:32
 */

namespace app\api\controller;


use app\api\model\Branch;
use app\api\model\CoursePrepare;
use app\api\model\Student;
use app\api\model\WxmpTemplate;
use app\common\db\Query;
use think\Exception;
use think\Log;
use think\Request;

class CoursePrepares extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        /** @var Query $m_cp */
        $m_cp = new CoursePrepare();
        $ret = $m_cp->with(['oneClass'])->getSearchResult($get);
        $m_student = new Student();
        foreach($ret['list'] as &$row) {
            $students = [];
            if(!empty($row['sids'])) {
                $students = $m_student->where('sid', 'in', $row['sids'])
                    ->field('sid,sex,student_name,photo_url')->select();
            }
            if(!empty($row['sid'])) {
                $students = $m_student->where('sid', 'in', $row['sid'])
                    ->field('sid,sex,student_name,photo_url')->select();
            }
            $row['students'] = empty($students) ? [] : $students;
        }

        return $this->sendSuccess($ret);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $cp_id = input('id');
        $m_cp = new CoursePrepare();
        $preparation = $m_cp->with(['coursePrepareAttachment', 'oneClass', 'student'])->find($cp_id);
        $m_student = new Student();
        if(!empty($preparation['sids'])) {
            $students = $m_student->where('sid', 'in', $preparation['sids'])
                ->field('sid,sex,student_name,photo_url')->select();
        }
        if(!empty($preparation['sid'])) {
            $students = $m_student->where('sid', 'in', $preparation['sid'])
                ->field('sid,sex,student_name,photo_url')->select();
        }
        $preparation['students'] = empty($students) ? [] : $students;
        
        return $this->sendSuccess($preparation);
    }

    /**
     * @desc  备课
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        $is_push = !empty($post['is_push']) ? $post['is_push'] : 0;
        unset($post['is_push']);

        $m_cp = new CoursePrepare();
        $cp_id = $m_cp->addPreparation($post);
        if($cp_id === false) return $this->sendError(400, $m_cp->getErrorMsg());

        if($is_push) {
            /** @var CoursePrepare $preparation */
            $preparation = $m_cp->find($cp_id);
            $sids = $preparation->getStudents();
            //if(!empty($sids)) {
            //    $rs = WxmpTemplate::wechat_tpl_notify_student('remind_before_class', $this->makeWechatTplData($preparation), $sids);
            //    if($rs === false) {
            //        return $this->sendError(400, '试听创建成功，推送学生失败，原因：' . $m_cp->getErrorMsg());
            //    }
            //}

            $tpl_data = $this->makeWechatTplData($preparation);
            foreach($sids as $sid) {
                $student = Student::get($sid);
                if(empty($student)) continue;
                $tpl_data['student_name'] = $student['student_name'];
                try {
                    $rs = WxmpTemplate::wechat_tpl_notify_student('remind_before_class', $tpl_data, [$sid]);
                    if($rs === false) throw new Exception($student['student_name'].'推送失败');

                } catch(Exception $e) {
                    Log::record($e->getMessage(), 'error');
                }
            }

            $preparation->is_push = 1;
            $preparation->push_time = time();
            $preparation->allowField('is_push,push_time')->save();
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  推送备课
     * @author luo
     * @method GET
     */
    public function wechat_notify()
    {
        $cp_id = input('cp_id');
        $m_cp = new CoursePrepare();
        /** @var CoursePrepare $preparation */
        $preparation = $m_cp->find($cp_id);
        if(empty($preparation)) return $this->sendError(400, 'cp_id error');

        $sids = $preparation->getStudents();
        if(!empty($sids)) {
            $tpl_data = $this->makeWechatTplData($preparation);
            foreach($sids as $sid) {
                $student = Student::get($sid);
                if(empty($student)) continue;
                $tpl_data['student_name'] = $student['student_name'];
                try {
                    $rs = WxmpTemplate::wechat_tpl_notify_student('remind_before_class', $tpl_data, [$sid]);
                    if($rs === false) throw new Exception($student['student_name'].'推送失败');

                } catch(Exception $e) {
                    Log::record($e->getMessage(), 'error');
                }
            }
        }

        $preparation->is_push = 1;
        $preparation->push_time = time();
        $preparation->allowField('is_push,push_time')->save();

        return $this->sendSuccess();
    }

    private function makeWechatTplData($preparation)
    {
        $branch = Branch::get($preparation['bid']);
        $data = [
            'student_name'	=> '',
            'lesson_name' => $preparation['title'],
            'school_time' => int_day_to_date_str($preparation['int_day']) . ' '
                . int_hour_to_hour_str($preparation['int_start_hour']) . '-' . int_hour_to_hour_str($preparation['int_end_hour']),
            'address'     => $branch ? $branch['branch_name'] : '',
            'mobile'      => Branch::GetTel(request()->bid, true),
            'remark'      => '该节课老师已经备课，可提前查阅',
            'ca_id'       => $preparation['ca_id'],
            'business_id'   => $preparation['cp_id']
            ];
        return $data;
    }

    public function put(Request $request)
    {
        $cp_id = input('id');
        $put = $request->put();
        $attachment_data = isset($put['course_prepare_attachment']) ? $put['course_prepare_attachment'] : [];

        /** @var CoursePrepare $preparation */
        $preparation = CoursePrepare::get($cp_id);
        if(empty($preparation)) return $this->sendError(400, '备课不存在');
        $rs = $preparation->edit($put, $attachment_data);
        if($rs === false) return $this->sendError(400, $preparation->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  删除备课
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $cp_id = input('id');
        $m_cp = new CoursePrepare();
        $rs = $m_cp->delPreparation($cp_id);
        if($rs === false) return $this->sendError(400, $m_cp->getErrorMsg());
        
        return $this->sendSuccess();
    }

}