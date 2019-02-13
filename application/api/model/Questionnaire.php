<?php
/**
 * Author: luo
 * Time: 2018/5/23 16:13
 */

namespace app\api\model;


use app\common\exception\FailResult;
use http\Exception;

class Questionnaire extends Base
{

    public function setQtDidsAttr($value)
    {
        return !empty($value) && is_array($value) ? implode(',', array_unique($value)) : $value;
    }

    public function getQtDidsAttr($value)
    {
        return $value ? explode(',', $value) : [];
    }

    public function questionnaireItem()
    {
        return $this->hasMany('QuestionnaireItem', 'qid', 'qid');
    }

    public function addQuestionnaire($data)
    {
        try {
            $this->startTrans();

            if(empty($data['name'])) return $this->user_error('问卷标题错误');
            $rs = $this->allowField(true)->isUpdate(false)->save($data);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $qid = $this->qid;

            $item_data = isset($data['questionnaire_item']) ? $data['questionnaire_item'] : [];
            $m_qi = new QuestionnaireItem();
            foreach($item_data as $item) {
                if(isset($data['bid'])) {
                    $item['bid'] = $data['bid'];
                }
                $item['qid'] = $qid;
                $rs = $m_qi->data([])->allowField(true)->isUpdate(false)->save($item);
                if($rs === false) throw new FailResult($m_qi->getErrorMsg());
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }


    public function delQuestionnaire()
    {
        if(empty($this->getData())) return $this->user_error('问卷数据错误');

        try {
            $this->startTrans();

            $rs = $this->questionnaireItem()->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        
        return true;
    }

    public function updateQuestionnaire($update_data)
    {
        if(empty($this->getData())) return $this->user_error(400, '问卷数据为空');

        $rs = $this->allowField(true)->isUpdate(true)->save($update_data);
        if($rs === false) return false;

        return true;
    }

}