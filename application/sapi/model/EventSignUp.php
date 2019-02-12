<?php
/**
 * Author: luo
 * Time: 2018/7/6 17:03
 */

namespace app\sapi\model;


class EventSignUp extends Base
{

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url');
    }

    public function signUp($post)
    {
        if(empty($post['event_id'])) return $this->user_error('event_id错误');
        if(empty($post['sid']) && (empty($post['name']) || empty($post['tel']))) return $this->user_error('报名对象错误');

        if(!empty($post['sid'])) {
            $exist = $this->where('event_id', $post['event_id'])->where('sid', $post['sid'])->find();
        } else {
            $exist = $this->where('event_id', $post['event_id'])->where('tel', $post['tel'])->find();
        }
        if(!empty($exist)) return true;

        $event = Event::get($post['event_id']);
        if(!$event['allow_sign_up']) return $this->user_error('活动不允许报名');
        if($event['apply_nums_limit'] > 0 && $event['apply_nums'] >= $event['apply_nums_limit']) {
            return $this->user_error('活动申请人数已经达到限定人数');
        }

        $rs = $this->allowField(true)->isUpdate(false)->save($post);
        if($rs === false) return false;

        Event::UpdateApplyNums($post['event_id']);

        return true;
    }


}