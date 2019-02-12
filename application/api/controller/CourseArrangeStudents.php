<?php
/**
 * Author: luo
 * Time: 2018/3/16 12:04
 */

namespace app\api\controller;


use app\api\model\CourseArrange;
use app\api\model\CourseArrangeStudent;
use think\Request;

class CourseArrangeStudents extends Base
{

    /**
     * @desc  所有
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $input = $request->param();
        $m_cas = new CourseArrangeStudent();
        $ret = $m_cas->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  排课添加学生
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        if(empty($post['ca_id'])) return $this->sendError(400, 'param error');

        $m_cas = new CourseArrangeStudent();
        $course = CourseArrange::get($post['ca_id']);
        if(empty($course)) return $this->sendError(400, '排课不存在');

        $rs = $m_cas->addStudents($course, $post['list']);
        if($rs === false) return $this->sendError(400, $m_cas->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        return $this->sendError(400, 'not support');
    }

    /**
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function delete_student(Request $request)
    {
        $post = $request->post();
        if(empty($post['ca_id']) || empty($post['list'])) return $this->sendError(400, 'param error');

        $m_cas = new CourseArrangeStudent();
        $rs = $m_cas->delList($post['ca_id'], $post['list']);
        if($rs === false) return $this->sendError(400, $m_cas->getErrorMsg());
        
        return $this->sendSuccess();
    }


}