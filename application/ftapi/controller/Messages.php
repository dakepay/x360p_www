<?php

namespace app\ftapi\controller;

use think\Request;
use app\ftapi\model\Message as MessageModel;

class Messages extends Base
{

    public function get_list(Request $request)
    {
        $eid = global_eid();
        $employee_info = get_employee_info($eid);
        $input = $request->get();
        $m_message = new MessageModel();
        $ret = $m_message->where('eid', $employee_info['eid'])->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

}