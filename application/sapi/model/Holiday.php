<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/8
 * Time: 11:52
 */

namespace app\sapi\model;

class Holiday extends Base
{
	public function getIntDayAttr($value)
    {
        return int_day_to_date_str($value);
    }


    public function setIntDayAttr($value,$data){
    	return format_int_day($value);
    }


}