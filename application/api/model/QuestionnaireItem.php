<?php
/**
 * Author: luo
 * Time: 2018/5/23 16:14
 */

namespace app\api\model;


class QuestionnaireItem extends Base
{
    public $type = [
        'choices' => 'json',
    ];

    public function addQuestionnaireItem($data)
    {
        $rs = $this->allowField(true)->data([])->isUpdate(false)->save($data);
        if($rs === false) return false;

        return true;
    }

    public function updateQuestionnaireItem($update_data)
    {
        if(empty($this->getData())) return $this->user_error('问卷条目数据错误');

        $rs = $this->allowField(true)->isUpdate(true)->save($update_data);
        if($rs === false) return false;

        return true;
    }


}