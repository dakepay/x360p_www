<?php

namespace app\admapi\controller;

use think\Request;
use app\admapi\model\Broadcast;
class Broadcasts extends Base
{

    public function get_list(Request $request)
    {
        $input = input();
        $model = new Broadcast();
        $ret = $model->with('user')->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    public function put(Request $request)
    {
        $input = $request->put();
        $mBroadcast = new Broadcast();
        $rs = $mBroadcast->updateBroadcast($input);
        if(!$rs) return $this->sendError(400, $mBroadcast->getErrorMsg());

        return $this->sendSuccess();
    }

}