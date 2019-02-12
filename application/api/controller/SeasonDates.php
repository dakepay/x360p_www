<?php

namespace app\api\controller;

use think\Request;

class SeasonDates extends Base
{

	public function post(Request $request){
		$input = $request->post();
        

        $result = $this->m_season_date->setSeasonDate($input);

        if(false === $result){
        	return $this->sendError($this->m_season_date->getError());
        }

        return $this->sendSuccess($result);
	}
}