<?php
/**
 * Author: luo
 * Time: 2018/4/9 19:39
 */

namespace app\sapi\model;


class StudentExam extends Base
{


    protected function getExamSubjectDidsAttr($value)
    {
        return is_string($value) ? explode(',', $value) : $value;
    }



}