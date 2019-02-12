<?php
/**
 * Author: luo
 * Time: 2017-12-20 09:15
**/

namespace app\sapi\controller;

use app\sapi\model\CourseArrange;
use app\sapi\model\Student;
use app\sapi\model\StudentLeave;
use think\Request;

class StudentLeaves extends Base
{

    /**
     * @desc  请假
     * @author luo
     * @method POST
     */
    public function post(Request $request)
    {
        $input = $request->post();
        if(!isset($input['sid']) || !isset($input['ca_id'])) return $this->sendError(400, '缺少参数');

        $student = Student::get($input['sid']);
        $course = CourseArrange::get($input['ca_id']);

        $m_sl = new StudentLeave();
        $rs = $m_sl->createOneLeave($course, $student, $input);
        if(!$rs) return $this->sendError(400, $m_sl->getErrorMsg());

        return $this->sendSuccess();
    }

}