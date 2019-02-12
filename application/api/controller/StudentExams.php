<?php
/**
 * Author: luo
 * Time: 2018/4/8 19:06
 */

namespace app\api\controller;


use app\api\model\StudentExam;
use app\api\model\StudentExamScore;
use think\Request;

class StudentExams extends Base
{
    
    public function get_list(Request $request)
    {
        return parent::get_list($request);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_se = new StudentExam();
        $rs = $m_se->addExam($post);
        if($rs === false) return $this->sendError(400, $m_se->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $se_id = input('id');
        $put = $request->put();

        $score = StudentExamScore::get(['se_id' => $se_id]);
        if(!empty($score)) return $this->sendError(400, '有学生成绩，修改不了');

        $exam = StudentExam::get($se_id);
        $rs = $exam->editExam($put);
        if($rs === false) return $this->sendError(400, $exam->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $se_id = input('id');
        $exam = StudentExam::get($se_id);
        if(empty($exam)) return $this->sendSuccess();

        $score = StudentExamScore::get(['se_id' => $se_id]);
        if(!empty($score)) return $this->sendError(400, '有学生成绩，删除不了');

        $rs = $exam->delete();
        if($rs === false) return $this->sendError(400, $exam->getErrorMsg());

        return true;
    }

}