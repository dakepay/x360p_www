<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/8
 * Time: 11:52
 */

namespace app\api\model;

class Holiday extends Base
{
	public function getIntDayAttr($value)
    {
        return int_day_to_date_str($value);
    }


    public function setIntDayAttr($value,$data){
    	return format_int_day($value);
    }

    /**
     * 获得假期天数
     * @param  [type] $bid        [description]
     * @param  [type] $start_time [description]
     * @param  [type] $end_time   [description]
     * @return [type]             [description]
     */
    public function getHolidays($bid,$start_time,$end_time){
        $int_start_day = date('Ymd',$start_time);
        $int_end_day   = date('Ymd',$end_time);

        $w['int_day'] = ['BETWEEN',[$int_start_day,$int_end_day]];
        $w['bid']     = $bid;

        $holidays = $this->where($w)->order('int_day asc')->select();


        if(!$holidays){
            unset($w['bid']);
            $holidays = $this->where($w)->order('int_day asc')->select();
        }


        $days = [];

        if($holidays){
            foreach($holidays as $row){
                array_push($days,$row->getData('int_day'));
            }
        }

        return $days;

    }

    /**
     * 批量设置节假日
     * @param  [type] $list [description]
     * @return [type]       [description]
     */
    public function multiSetHolidays($input){
    	$list = $input['list'];

    	if(empty($list) && empty($input['delete'])){
    		$this->user_error('缺少参数list');
    		return false;
    	}

    	$this->startTrans();
    	try{
	    	foreach($list as $row){
	    	    $w = [];
	    	    $w = [
	    	        'int_day' => intval(format_int_day($row['int_day'])),
                    'year' => intval($row['year']),
                ];
	    	    $holiday = $this->where($w)->find();
	    		if(!empty($holiday)){
	    			if($holiday['name'] != $row['name']){
	    			    $update['name'] = $row['name'];
	    			    $result = $this->save($update,$holiday['hid']);
                        if (false === $result){
                            return $this->sql_delete_error('holiday');
                        }
	    			}
	    		}else{
	    			$result = $this->data([])->isUpdate(false)->allowField(true)->save($row);
                    if (false === $result){
                        return $this->sql_add_error('holiday');
                    }
	    		}
	    	}

	    	if(!empty($input['delete'])){
                $w = [];
                $w['hid'] = ['in',$input['delete']];
                $result = $this->where($w)->delete(true);
                if (false === $result){
                    return $this->sql_delete_error('holiday');
                }
	    	}
	    }catch(\Exception $e){
	    	return $this->exception_error($e);
	    }

    	$this->commit();
    	return true;
    }
}