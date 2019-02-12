<?php
/**
 * Author: luo
 * Time: 2018/7/6 16:25
 */

namespace app\sapi\controller;


use app\api\model\ClassStudent;
use app\sapi\model\Event;
use think\Request;

class Events extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_event = new Event();

        if(!empty($get['bid'])) {
            $bid = $get['bid'];
            $m_event->where("find_in_set({$bid}, bids) or scope = 'global'");
            unset($get['bid']);
        }

        if(!empty($get['event_type_did']) && $get['event_type_did'] == 181) {
            $sid = global_sid();
            if($sid <= 0) return $this->sendError(400, 'sid错误');

            $cids = (new ClassStudent())->where('status', ClassStudent::STATUS_NORMAL)->where('sid', $sid)
                ->column('cid');
            $cids = array_unique($cids);
            if(empty($cids)) {
                return $this->sendSuccess(['list' => []]);
            }

            $m_event->where('cid', 'in', $cids);
        }


        $ret = $m_event->getSearchResult($get);
        return $this->sendSuccess($ret);
    }



}