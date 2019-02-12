<?php
/**
 * Author: luo
 * Time: 2018/5/24 12:22
 */

namespace app\api\controller;


use app\api\model\QuestionnaireItem;
use think\Request;

class QuestionnaireItems extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_qi = new QuestionnaireItem();
        if(empty($get['bid'])) {
            $bids = [0, $request->bid];
            $bids = implode(',', array_unique($bids));
            $get['bid'] = "[in,{$bids}]";
        }

        if(isset($get['qid'])) {
            $get['bid'] = -1;
        }

        $ret = $m_qi->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    public function put(Request $request)
    {
        $put = $request->put();
        $qi_id = input('id');
        $questionnaire_item = QuestionnaireItem::get($qi_id);
        if(empty($questionnaire_item)) return $this->sendError(400, '问卷不存在');
        $rs = $questionnaire_item->updateQuestionnaireItem($put);
        if($rs === false) return $this->sendError(400, $questionnaire_item->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $qi_id = input('id');
        $m_qi = new QuestionnaireItem();
        $rs = $m_qi->where('qi_id', $qi_id)->delete();
        if($rs === false) return $this->sendError(400, $m_qi->getErrorMsg());
        
        return $this->sendSuccess();
    }

}