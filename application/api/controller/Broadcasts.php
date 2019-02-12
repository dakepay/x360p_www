<?php
/**
 * Author: luo
 * Time: 2017-11-23 18:13
**/

namespace app\api\controller;

use app\api\model\Broadcast;
use think\Request;

class Broadcasts extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();
        $model = new Broadcast();
        $ret = $model->with('user')->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $mBroadcast = new Broadcast();
        $result = $mBroadcast->addBroadcast($input);
        if ($result === false) return $this->sendError(400,$mBroadcast->getError());

        return $this->sendSuccess();
    }


    /**
     * 推送公告
     * @param Request $request
     */
    public function push_broadcast(Request $request)
    {
        $bc_id = input('id/d');

        $mBroadcast = new Broadcast();
        $broadcast = $mBroadcast->get($bc_id);
        if (!$broadcast){
            return $this->sendError(400,'公告不存在');
        }
        $result = $mBroadcast->pushBroadcast($broadcast);

        if (false === $result){
            return $this->sendError(400,$mBroadcast->getError());
        }

        return $this->sendSuccess();
    }

}