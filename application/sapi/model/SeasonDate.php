<?php


namespace app\sapi\model;

class SeasonDate extends Base
{
	/**
	 * 获得校区季节设置
	 * @param  [type] $bid    [description]
	 * @param  [type] $season [description]
	 * @return [type]         [description]
	 */
	public function getSeasonDate($bid,$season,$year = null){
		$ret = ['',''];
		$w['bid'] = intval($bid);
		if(!in_array($season,['C','S','Q','H'])){
			$season = get_current_season();
		}

		$w['season'] = $season;
		$w['year']   = intval(date('Y',time()));
		$row = $this->where($w)->find();

		if(!$row){
			$w['year'] = $w['year'] -1;
			$row = $this->where($w)->find();
		}

		if(!$row){
			$w['bid'] = 0;
			$row = $this->where($w)->find();
		}

		if($row){
			$ret[0] = $row['int_day_start'];
			$ret[1] = $row['int_day_end'];
		}

		return $ret;
	}

	public function getIntDayStartAttr($value){
		return int_day_to_date_str($value);
	}

	public function getIntDayEndAttr($value){
		return int_day_to_date_str($value);
	}

	public function setIntDayStartAttr($value,$data){
		return format_int_day($value);
	}

	public function setIntDayEndAttr($value,$data){
		return format_int_day($value);
	}

	public function getCurrentYearDayStartAttr($value,$data){
		$int_day = current_year_int_day($data['int_day_start']);
		return int_day_to_date_str($int_day);

	}

	public function getCurrentYearDayEndAttr($value,$data){
		$int_day = current_year_int_day($data['int_day_end']);
		return int_day_to_date_str($int_day);

	}

}