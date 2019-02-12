<?php
/**
 * Author: luo
 * Time: 2018/4/9 19:39
 */

namespace app\api\model;


use think\Validate;

class StudentExam extends Base
{
    protected function setExamSubjectDidsAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    protected function setExamIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    protected function setScoreReleaseIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    protected function getExamSubjectDidsAttr($value)
    {
        return is_string($value) ? explode(',', $value) : $value;
    }

    public function addExam($post)
    {
        $rule = [
            'exam_name' => 'require',
            'exam_int_day' => 'require',
        ];

        $validate = new Validate();
        $rs = $validate->check($post, $rule);
        if($rs === false) return $this->user_error($validate->getError());

        $rs = $this->allowField(true)->save($post);
        if($rs === false) return $this->user_error($this->getError());

        return true;
    }

    public function editExam($put)
    {
        if(empty($this->getData())) return $this->user_error('考试数据为空');

        $rs = $this->validate()->validateData($put);
        if($rs !== true) return false;

        $rs = $this->allowField(true)->save($put);
        if($rs === false) return false;

        return true;
    }

}