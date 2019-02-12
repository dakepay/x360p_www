<?php
/** 
 * Author: luo
 * Time: 2017-10-23 19:15
**/

namespace app\api\controller;

use app\api\model\ClassAttendance;
use app\api\model\StudentAttendance;
use think\Request;

class StudentAttendances extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $m_sa = new StudentAttendance();
        $ret = $m_sa->with('student')->getSearchResult($input);
        return $this->sendSuccess($ret);
    }


    /**
     * 撤销学生考勤的一次考勤记录
     * @param Request $request
     */
    public function delete(Request $request)
    {
        $id = $request->param('id');
        $m_att = StudentAttendance::get($id);
        if (!$m_att) {
            return $this->sendError(400, '考勤ID不存在');
        }

        $result = $m_att->cancelAttendance();

        if(!$result){
            return $this->sendError(400,$m_att->getError());
        }

        return $this->sendSuccess('ok');
    }

    /**
     * 无排课自由考勤：班课和非班课
     * @param Request $request
     */
    public function free(Request $request)
    {
        $input = $request->post();
        if (empty($input)) {
            return $this->sendError(400, '参数不能为空!');
        }
        $rule = [
            'teach_eid|教师ID'       => 'require|number',
            'int_day|上课日期'        => 'require|date',
            'lesson_period|上课时间'  => 'require|array|length:2',
            'lesson_type|课程类型'    => 'require|in:0,1,2',
            'lesson_remark|考勤备注'  => 'max:255',
            'sj_id|科目id'           => 'number',/*课时包*/
            'cid|班级id'             => 'requireIf:lesson_type,0|number',/*班课的无排课考勤*/
            'students|考勤学生'       => 'require|array', //todo 考虑停课的情况
            'is_push|是否推送给家长'   => 'boolean',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $attendance = new StudentAttendance();
        $rs = $attendance->freeAttendance($input);
        if ($rs !== true) {
            return $this->sendError(400, $attendance->getError(), 400, $attendance->attendance_fail_report);
        }
        return $this->sendSuccess($attendance->attendance_fail_report);
    }

    /**
     * 考勤打卡
     * @param Request $request
     */
    public function do_print(Request $request){
        $satt_id = $request->get('satt_id');
        $attendance = new StudentAttendance();
        $rs = $attendance->do_print($satt_id);
        if(!$rs) {
            return $this->sendError(400, $attendance->getErrorMsg());
        }
        return $this->sendSuccess($rs);
    }

}