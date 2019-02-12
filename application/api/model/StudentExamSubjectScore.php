<?php
/**
 * Author: luo
 * Time: 2018/4/10 17:09
 */

namespace app\api\model;


class StudentExamSubjectScore extends Base
{

    public function addSubjectScore($data)
    {
        if(empty($data['ses_id']) || empty($data['exam_subject_did']) || empty_except_zero($data['score'])) {
            return $this->user_error('登记学生科目分数缺少参数');
        }

        $score = $this->where(['ses_id' => $data['ses_id'], 'exam_subject_did' => $data['exam_subject_did']])->find();
        if(!empty($score)) {
            $score->score = $data['score'];
            $rs = $score->allowField('score')->save();
            if($rs === false) return false;

            return true;
        }

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return false;

        return true;
    }

}