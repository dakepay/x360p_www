<?php
/**
 * Author: luo
 * Time: 2017-11-20 17:02
**/

namespace app\api\controller;


use app\api\model\StudentLeave;
use app\api\model\Student;
use think\Request;

class StudentLeaves extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();
        $model = new StudentLeave();
        if(!empty($input['student_name']) ) {
            $student_name = $input['student_name'];
            $sids = (new Student())->where('student_name|pinyin|pinyin_abbr', 'like', "%{$student_name}%")->column('sid');
            $sids = !empty($sids) ? array_unique($sids) : [-1];
            $model->where('sid', 'in', $sids);
        }
        $ret = $model->with(['one_class', 'course_arrange'])->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $student = Student::get(['sid' => $input['sid']]);
        $model = new StudentLeave();
        $rs = $model->addBatchLeave($input['ca_ids'], $student, $input);
        if(!$rs) return $this->sendError(400, $model->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * 删除请假
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function delete(Request $request)
    {
        $slv_id = input('id');
        /** @var StudentLessonHour $student_lesson_hour */
        $student_leave = StudentLeave::get($slv_id);

        if(empty($student_leave)) return $this->sendSuccess();

        $rs = $student_leave->delLeave();
        if($rs === false) return $this->sendError(400, $student_leave->getErrorMsg());

        return $this->sendSuccess();
    }

}