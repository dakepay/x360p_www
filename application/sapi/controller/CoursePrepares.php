<?php
/**
 * Author: luo
 * Time: 2018-04-10 11:21
**/


namespace app\sapi\controller;


use app\sapi\model\ClassStudent;
use app\sapi\model\CoursePrepare;
use app\sapi\model\Student;
use think\Request;

class CoursePrepares extends Base
{

    public function get_list(Request $request)
    {
        $sid = global_sid();
        if($sid <= 0) return $this->sendError(400, 'sid error');

        $m_cp = new CoursePrepare();
        $cids = (new ClassStudent())->where('sid', $sid)->where('status', ClassStudent::STATUS_NORMAL)
            ->column('cid');
        if(!empty($cids)) {
            $cids = implode(',', array_unique($cids));
            $w_cp_string = "sid = {$sid} or find_in_set({$sid},sids) or cid in ({$cids})";
        } else {
            $w_cp_string = "sid = {$sid} or find_in_set({$sid},sids)";
        }
        $get = $request->get();
        unset($get['sid']);
        $ret = $m_cp->where($w_cp_string)->getSearchResult($get);
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


}