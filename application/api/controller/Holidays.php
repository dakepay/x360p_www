<?php
/**
 * Created by sublime.
 * Author: payhon
 * Date: 2017/9/1
 * Time: 18:24
 */
namespace app\api\controller;

use app\api\model\Holiday;
use think\Request;

class Holidays extends Base
{
	public function post(Request $request){
		$input = $request->post();

		$mHoliday = new Holiday();
        $result = $mHoliday->multiSetHolidays($input);

		if(false === $result){
			return $this->sendError(400,$mHoliday->getError());
		}

		return $this->sendSuccess();
	}

	public function center_holidays(Request $request)
    {
        $year = input('year');
        $holiday_list = db('holiday', 'db_center')->where('year',$year)->select();

        $ret['list'] = $holiday_list;
        $ret['page'] = 1;
        $ret['pagesize'] = 200;

        return $this->sendSuccess($ret);
    }
}