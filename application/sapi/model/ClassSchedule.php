<?php
/**
 * Author: luo
 * Time: 2018/6/30 17:12
 */

namespace app\sapi\model;


class ClassSchedule extends Base
{

    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time'];

    public function setIntStartHourAttr($value)
    {
        $value = str_replace(':', '', $value);
        $value = strlen($value) >= 4 ? $value: str_pad($value, 4, 0, STR_PAD_LEFT);
        return intval($value);
    }

    public function setIntEndHourAttr($value)
    {
        $value = str_replace(':', '', $value);
        $value = strlen($value) >= 4 ? $value: str_pad($value, 4, 0, STR_PAD_LEFT);
        return intval($value);
    }

    public function getIntStartHourAttr($value)
    {
        return $this->transformHour($value);
    }

    public function getIntEndHourAttr($value)
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

}