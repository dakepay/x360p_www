<?php

namespace app\ftapi\model;

use app\common\exception\FailResult;
use think\Db;
use think\Exception;

class Classes extends Base
{
    protected $name = 'class';

    const CLASS_TYPE_NORMAL = 0; # 标准班级类型
    const CLASS_TYPE_TMP = 1; # 临时班级类型
    const CLASS_TYPE_ACTIVITY = 2;  # 活动班级

    public function getIntStartHourAttr($value)
    {
        return $this->transformHour($value);
    }

    protected function transformHour($hour)
    {
        $hour = (string)$hour;
        if (strlen($hour) == 3) {
            $hour = '0' . $hour;
        }
        $temp = str_split($hour, 2);
        return implode(':', $temp);
    }

    public function getIntEndHourAttr($value)
    {
        return $this->transformHour($value);
    }

    public function getStartLessonTimeAttr($value)
    {
        return $value ? date('Y-m-d', $value) : $value;
    }

    public function getEndLessonTimeAttr($value)
    {
        return $value ? date('Y-m-d', $value) : $value;
    }

    public function branch()
    {
        return $this->belongsTo('Branch', 'bid', 'bid');
    }

    public function teacher()
    {
        return $this->belongsTo('Employee', 'teach_eid', 'eid');
    }
    public function assistant()
    {
        return $this->belongsTo('Employee', 'second_eid', 'eid');
    }

    public function students()
    {
        return $this->belongsToMany('Student', 'ClassStudent', 'sid', 'cid');
    }

    public function courseArranges()
    {
        return $this->hasMany('CourseArrange', 'cid', 'cid');
    }

    public function lesson()
    {
        return $this->belongsTo('Lesson', 'lid', 'lid');
    }

    public function classroom()
    {
        return $this->hasOne('Classroom', 'cr_id', 'cr_id');
    }

    public function schedule()
    {
        return $this->hasMany('ClassSchedule', 'cid', 'cid');
    }

    public function studentArtwork()
    {
        return $this->hasMany('StudentArtwork', 'cid', 'cid');
    }






}
