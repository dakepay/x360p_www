<?php
/**
 * Author: luo
 * Time: 2017-12-04 17:11
**/

namespace app\api\controller;

use app\api\model\Backlog;
use think\Request;

class Backlogs extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();
        $uid = input('uid/d', gvar('uid'));
        $m_backlog = new Backlog();
        $ret = $m_backlog->where('create_uid', $uid)->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $rs = $this->validate($input, 'Backlog');
        if($rs !== true) return $this->sendError(400, $rs);
        $mBacklog = new Backlog();
        $result = $mBacklog->addBackLog($input);
        if ($result === false) return $this->sendError(400,$mBacklog->getError());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = $request->post();
        $rs = $this->validate($input, 'Backlog');
        if($rs !== true) return $this->sendError(400, $rs);
        return parent::put($request);
    }

    public function delete(Request $request)
    {
        return parent::delete($request);
    }

}