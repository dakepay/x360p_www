<?php
/**
 * Author: luo
 * Time: 2018/7/10 19:48
 */

namespace app\api\controller;


use app\api\model\Event;
use app\api\model\EventSignUp;
use app\api\model\Student;
use think\Request;

class EventSignUps extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_esu = new EventSignUp();
        $m_event = new Event();
        $bid = $request->bid;
        $event_ids = $m_event->where("find_in_set({$bid}, bids) or scope = 'global'")->column('event_id');
        $m_event->where('event_id', 'in', $event_ids);
        $get['bid'] = -1;

        if(!empty($get['name'])) {
            $m_student = new Student();
            $name = $get['name'];
            $sids = $m_student->where('student_name', 'like', "%{$name}%")->column('sid');
            $sids = implode(',', array_unique($sids));
            if(!empty($sids)) {
                $m_esu->where("(name like '%{$name}%' or sid in ({$sids}))");
            } else {
                $m_esu->where("name like %{$name}%");
            }
            unset($get['name']);
        }

        if(!empty($get['tel'])) {
            $m_student = new Student();
            $tel = $get['tel'];
            $sids = $m_student->where('first_tel', $tel)->column('sid');
            $sids = implode(',', array_unique($sids));
            if(!empty($sids)) {
                $m_esu->where("(tel = {$tel} or sid in ({$sids}))");
            } else {
                $m_esu->where("tel = {$tel}");
            }
            unset($get['tel']);
        }



        $ret = $m_esu->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    public function put(Request $request)
    {
        $put = $request->put();
        $esu_id = input('id');
        $event_sign_up = EventSignUp::get($esu_id);
        if(empty($event_sign_up)) return $this->sendError(400, '没有报名信息');

        if(empty($event_sign_up['is_attend']) && !empty($put['is_attend'])) {
            $put['attend_time'] = time();
        }

        $rs = $event_sign_up->allowField('is_attend,attend_time,name,tel,remark')->save($put);
        if($rs === false) return $this->sendError(400, $event_sign_up->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function post_market_clue(Request $request)
    {
        $esu_id = input('id');
        $event_sign_up = EventSignUp::get($esu_id);
        if(empty($event_sign_up)) return $this->sendError(400, '没有报名信息');
        if($event_sign_up['sid'] > 0) return $this->sendError(400, '学生无法转化');

        $rs = $event_sign_up->changeToMarketClue();
        if($rs === false) return $this->sendError(400, $event_sign_up->getErrorMsg());

        return $this->sendSuccess();
    }


}