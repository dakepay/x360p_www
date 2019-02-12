<?php
/**
 * Author: luo
 * Time: 2018/7/6 16:26
 */

namespace app\sapi\model;


class Event extends Base
{
    protected $type = [
        'event_start_time' => 'timestamp',
        'event_end_time' => 'timestamp'
    ];

    protected function getBidsAttr($value)
    {
        return $value && is_string($value) ? explode(',', $value) : $value;
    }

    public function eventAttachment()
    {
        return $this->hasMany('EventAttachment', 'event_id', 'event_id');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function eventSignUp()
    {
        return $this->hasMany('EventSignUp', 'event_id', 'event_id');
    }

    public static function UpdateApplyNums($event_id)
    {
        $num = (new EventSignUp())->where('event_id', $event_id)->count();
        (new Event())->where('event_id', $event_id)->update(['apply_nums' => $num]);

        return true;
    }

}