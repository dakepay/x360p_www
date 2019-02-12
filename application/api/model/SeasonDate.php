<?php


namespace app\api\model;

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

	/**
	 * 设置季节开课时间段
	 * @param [type] $input [description]
	 */
	public function setSeasonDate($input){
		$need_fields = ['bid','year','season'];

		if(!$this->checkInputParam($input,$need_fields)){
			return false;
		}

		$w['bid']    = $input['bid'];
		$w['year']   = $input['year'];
		$w['season'] = $input['season'];

		$rs = $this->where($w)->find();

		$ret = false;

		if(!$rs){
			$allow_fields = ['bid','year','season','int_day_start','int_day_end'];
			$sd_id = $this->allowField($allow_fields)->save($input);
			if(!$sd_id){
				return $this->sql_add_error('season_date');
			}
			$input['sd_id'] = $sd_id;
			$ret = $input;
		}else{
			$rs->int_day_start = $input['int_day_start'];
			$rs->int_day_end   = $input['int_day_end'];
			$result = $rs->save();
			if(false === $result){
				return $this->sql_save_error('season_date');
			}
			$ret = $rs;
		}

		return $ret;
	}


	/**
	 * 设置季节开课日期
	 * @param [type] $int_day_start [description]
	 * @param [type] $int_day_end   [description]
	 * @param [type] $bid           [description]
	 * @param [type] $season        [description]
	 */
	public function setSeasonDateAttr($int_day_start,$int_day_end,$bid,$season){
		$reg_date = '/^\d{4}-\d{2}-\d{2}$/';
		if(!preg_match($reg_date,$int_day_start)){
			$this->user_error('开始日期格式不正确');
			return false;
		}
		if(!preg_match($reg_date,$int_day_start)){
			$this->user_error('结束日期格式不正确');
			return false;
		}
		$cur_year = date('Y',time());

		$w['bid'] = $bid;
		$w['season'] = $season;
		$w['year'] = $cur_year;

		$record = $this->where($w)->find();

		if(!$record){
			$this->int_day_start = $int_day_start;
			$this->int_day_end   = $int_day_end;
			$this->bid           = $bid;
			$this->year          = $cur_year;
			$this->season        = $season;
			return $this->save();
		}

		$record->int_day_start = $int_day_start;
		$record->int_day_end   = $int_day_end;

		return $record->save();

		
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