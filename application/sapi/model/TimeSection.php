<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/5
 * Time: 14:20
 */
namespace app\sapi\model;

class TimeSection extends Base
{
    protected $readonly = ['bid', 'season'];
    protected static function init()
    {
        parent::init();
    }

    public function setIntStartHourAttr($value)
    {
        return format_int_hour($value);
    }

    public function setIntEndHourAttr($value)
    {
        return format_int_hour($value);
    }

    public function getIntStartHourAttr($value)
    {
        return int_hour_to_hour_str($value);
    }


    public function getIntEndHourAttr($value)
    {
        return int_hour_to_hour_str($value);
    }




}