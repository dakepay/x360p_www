<?php
/**
 * Created by sublime.
 * Author: payhon
 * Date: 2017/9/1
 * Time: 18:24
 */
namespace app\admapi\controller;

use think\Request;

class Holidays extends Base
{
	public function post(Request $request){
		$input = $request->post();

		if(isset($input['list']) && is_array($input)){
			$result = $this->m_holiday->multiSetHolidays($input);
		}else{
			$result = $this->m_holiday->singleAddHoliday($input);
		}
		
		if(!$result){
			return $this->sendError(400,$this->m_holiday->getError());
		}

		return $this->sendSuccess();
	}
}