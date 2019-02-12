<?php
/**
 * Author: luo
 * Time: 2018/7/6 11:15
 */

namespace app\api\controller;


use app\api\model\Event;
use think\Request;

class Events extends Base
{

    public function get_list(Request $request)
    {
        $m_event = new Event();
        $get = $request->get();
        if(empty($get['bids'])) {
            $bid = $request->bid;
            $where = "find_in_set({$bid}, bids) or scope = 'global'";
            $m_event->where($where);
            $get['bid'] = -1;
        }
        $ret = $m_event->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();

        $m_event = new Event();
        $rs = $m_event->addEvent($post);
        if($rs === false) return $this->sendError(400, $m_event->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $event_id = input('id');
        $put = $request->put();
        $event = Event::get($event_id);
        if(empty($event)) return $this->sendError(400, '活动不存在');
        $rs = $event->updateEvent($put);
        if($rs === false) return $this->sendError(400, $event->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $event_id = input('id');
        $event = Event::get($event_id);
        if(empty($event)) $this->sendSuccess();

        $rs = $event->delEvent(input('force', 0));
        if($rs === false) {
            if($event->get_error_code() == $event::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($event->getErrorMsg());
            }
            return $this->sendError(400, $event->getErrorMsg());
        }

        return $this->sendSuccess();
    }

}