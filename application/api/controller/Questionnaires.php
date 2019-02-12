<?php
/**
 * Author: luo
 * Time: 2018/5/23 16:09
 */

namespace app\api\controller;


use app\api\model\Questionnaire;
use app\api\model\QuestionnaireItem;
use think\Request;

class Questionnaires extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_questionnaire = new Questionnaire();

        if(empty($get['bid'])) {
            $bids = [0, $request->bid];
            $bids = implode(',', array_unique($bids));
            $get['bid'] = "[in,{$bids}]";
        }
        $ret = $m_questionnaire->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_questionnaire = new Questionnaire();
        $rs = $m_questionnaire->addQuestionnaire($post);
        if($rs === false) return $this->sendError(400, $m_questionnaire->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $put = $request->put();
        $qid = input('id');
        $questionnaire = Questionnaire::get($qid);
        if(empty($questionnaire)) return $this->sendError(400, '问卷不存在');
        $rs = $questionnaire->updateQuestionnaire($put);
        if($rs === false) return $this->sendError(400, $questionnaire->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $qid = input('id');
        $questionnaire = Questionnaire::get($qid);
        if(empty($questionnaire)) return $this->sendSuccess();

        $rs = $questionnaire->delQuestionnaire();
        if($rs === false) return $this->sendError(400, $questionnaire->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function post_questionnaire_item(Request $request)
    {
        $qid = input('id');
        $questionnaire = Questionnaire::get($qid);
        if(empty($questionnaire)) return $this->sendError('问卷不存在');

        $post = $request->post();
        $m_qi = new QuestionnaireItem();
        $post['qid'] = $qid;
        $post['bid'] = $questionnaire['bid'];
        $rs = $m_qi->addQuestionnaireItem($post);
        if($rs === false) return $this->sendError(400, $m_qi->getErrorMsg());
        
        return $this->sendSuccess();
    }

}