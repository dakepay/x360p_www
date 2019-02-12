<?php
/**
 * Author: luo
 * Time: 2018/8/9 9:44
 */

namespace app\api\controller;


use app\api\model\MessageGroupHistory;
use think\Request;

class MessageGroupHistorys extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();

        $m_mgh = new MessageGroupHistory();
        $ret = $m_mgh->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_mgh = new MessageGroupHistory();
        $rs = $m_mgh->sendGroup($post);
        if($rs === false) return $this->sendError(400, $m_mgh->getErrorMsg());
        
        return $this->sendSuccess();
    }

}