<?php
/**
 * Author: luo
 * Time: 2018/6/27 15:22
 */

namespace app\sapi\controller;


use app\sapi\model\CourseArrange;
use app\sapi\model\CourseArrangeStudent;
use think\Request;

class CourseArrangeStudents extends Base
{

    public function get_list(Request $request)
    {
        $m_cas = new CourseArrangeStudent();
        $get = $request->get();

        $ret = $m_cas->getSearchResult($get);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $post['sid'] = global_sid();
        if(empty($post['sid'])) return $this->sendError(400, 'sid错误');
        $m_ca = new CourseArrange();
        $rs = $m_ca->addOneCourse($post);
        if($rs === false) return $this->sendError(400, $m_ca->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $cas_id = input('id');
        $course_arrange_student = CourseArrangeStudent::get($cas_id);
        if(empty($course_arrange_student)) return $this->sendSuccess();

        $course_arrange = CourseArrange::get($course_arrange_student['ca_id']);
        if(empty($course_arrange)) return $this->sendSuccess();

        $rs = $course_arrange_student->delOneRow($course_arrange, $course_arrange_student['sid']);
        if($rs === false) return $this->sendError(400, $course_arrange_student->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        return $this->sendError(400, 'not support');
    }

}