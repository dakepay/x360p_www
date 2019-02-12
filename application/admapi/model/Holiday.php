<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/8
 * Time: 11:52
 */

namespace app\admapi\model;

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
	    		if($row['hid']){
	    			$r = $this->find($row['hid']);
	    			if($r && $r['name'] != $row['name']){
	    				$r->data('name',$row['name']);
	    				$r->allowField(true)->save();
	    			}
	    		}else{
	    			$this->isUpdate(false)->allowField(true)->save($row);
	    		}
	    	}

	    	if(!empty($input['delete'])){
	    		$w['hid'] = ['in',$input['delete']];
	    		$this->where($w)->delete(true);
	    	}
	    }catch(\Exception $e){
	    	return $this->user_error($e->getMessage());
	    }

    	$this->commit();
    	return true;
    }
}