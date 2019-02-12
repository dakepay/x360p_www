<?php
/**
 * Author: luo
 * Time: 2018/6/27 9:34
 */

namespace app\sapi\controller;


use app\sapi\model\MarketClue;
use app\sapi\model\Student;
use think\Request;

class MarketClues extends Base
{

    public function get_list(Request $request)
    {
        $m_mc = new MarketClue();
        $get = $request->get();
        $ret = $m_mc->getSearchResult($get);
        
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $sid = global_sid();
        $student = Student::get($sid);
        if(empty($student)) return $this->sendError(400, '学生不存在');

        $m_mc = new MarketClue();
        $post['recommend_sid'] = $post['recommend_sid'] ?? $sid;
        $post['recommend_uid'] = $post['recommend_uid'] ?? gvar('uid');
        $post['bid'] = $student['bid'];
        $post['og_id'] = $student['og_id'];
        unset($post['sid']);
        $rs = $m_mc->addClue($post);
        if($rs === false) return $this->sendError(400, $m_mc->getErrorMsg());
        
        return $this->sendSuccess();
    }
    
    public function put(Request $request)
    {
        return $this->sendError(400, 'not support');
    }

    public function delete(Request $request)
    {
        return $this->sendError(400, 'not support');
    }

}