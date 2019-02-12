<?php
/**
 * Author: luo
 * Time: 2017/12/26 19:46
 */

namespace app\sapi\controller;

use think\Request;
use app\sapi\model\Message as MessageModel;

class Messages extends Base
{

    public function get_list(Request $request)
    {
        $sid = global_sid();
        $input = $request->get();
        $m_message = new MessageModel();
        $ret = $m_message->where('sid', $sid)->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

}