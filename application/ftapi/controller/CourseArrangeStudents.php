<?php

namespace app\ftapi\controller;

use app\ftapi\model\CourseArrange;
use app\ftapi\model\CourseArrangeStudent;
use think\Request;

class CourseArrangeStudents extends Base
{

    public function get_list(Request $request)
    {
        $m_cas = new CourseArrangeStudent();
        $input = input();

        $ret = $m_cas->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function put(Request $request)
    {
        return $this->sendError(400, 'not support');
    }

    public function post(Request $request)
    {
        return $this->sendError(400, 'not support');
    }

    public function delete(Request $request)
    {
        return $this->sendError(400, 'not support');
    }

}