<?php
/**
 * Author: luo
 * Time: 2018/6/4 17:16
 */

namespace app\sapi\model;


class Questionnaire extends Base
{

    public function getQtDidsAttr($value)
    {
        return $value ? explode(',', $value) : [];
    }

    public function questionnaireItem()
    {
        return $this->hasMany('QuestionnaireItem', 'qid', 'qid');
    }

}