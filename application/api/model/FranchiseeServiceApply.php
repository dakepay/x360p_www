<?php

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;
use think\Hook;
use think\Log;

class FranchiseeServiceApply extends Base
{

	const STAY_SERVICE     = 0;  # 待服务
	const RECEIVE_SERVICE  = 1;  # 已接收
	const FINISH_SERVICE   = 2;  # 已完成

	public function franchisee()
	{
		return $this->belongsTo('Franchisee','fc_id','fc_id');
	}

	public function getReceiveTimeAttr($value,$data)
	{
		return $value ? date('Y-m-d H:i',$value) : '0';
	}

	public function setIntDayAttr($value,$data)
    {
    	return date('Ymd',strtotime($value));
    }

    public function setIntHourAttr($value,$data)
    {
    	return date('Hi',strtotime($value));
    }

    public function getIntDayAttr($value,$data)
    {
    	return $value ? int_day_to_date_str($value) : '0';
    }

    public function getIntHourAttr($value,$data)
    {
    	return $value ? int_hour_to_hour_str($value) : '0';
    }
    
    /**
     * 添加加盟商服务申请
     * @param [type] &$input [description]
     */
	public function addFranchiseeServiceApply(&$input)
	{
		$this->startTrans();
		try{

			$model = new self();
			$ret = $model->data([])->allowField(true)->isUpdate(false)->save($input);
			if(!$ret){
				$this->rollback();
				return $this->sql_add_error('franchisee_service_apply');
			}


		}catch(\Exception $e){
			$this->rollback();
			return $this->deal_exception($e->getMessage(),$e);
		}
		$this->commit();

		return true;

	}

    /**
     * 编辑加盟商服务申请
     * @param  [type] $put    [description]
     * @param  [type] $fsa_id [description]
     * @return [type]         [description]
     */
	public function updateFranchiseeServiceApply($put,$fsa_id=0)
	{
		$this->startTrans();
		try{

			$model = new self();
			$where['fsa_id'] = $fsa_id;
			$ret = $model->data([])->allowField(true)->isUpdate(true)->save($put,$where);
			if(!$ret){
				$this->rollback();
				return $this->sql_save_error('franchisee_service_apply');
			}

		}catch(\Exception $e){
			$this->rollback();
			return $this->exception_error($e);
		}
		$this->commit();

		return true;


	}

    /**
     * 删除服务申请
     * @param  [type] $franchisee_service_apply [description]
     * @return [type]                           [description]
     */
	public function deleteFranchiseeServiceApply($franchisee_service_apply)
	{
		$this->startTrans();
		try{

			$ret = $franchisee_service_apply->delete();
			if(false === $ret){
				$this->rollback();
				return $this->sql_delete_error('franchisee_service_apply');
			}

		}catch(\Exception $e){
			$this->rollback();
			return $this->exception_error($e);
		}
		$this->commit();

		return true;

	}


    /**
     * 接受服务申请
     * @param  [type] $fsa_id [description]
     * @return [type]         [description]
     */
	public function receiveApply($service_apply)
	{
		$this->startTrans();
		try{

			$service_apply['status'] = FranchiseeServiceApply::RECEIVE_SERVICE;
			$service_apply['receive_time'] = time();
			$ret = $service_apply->save();
			if(!$ret){
            	$this->rollback();
            	return $this->sql_save_error('franchisee_service_apply');
            }

		}catch(\Exception $e){
			$this->rollback();
			return $this->exception_error($e);
		}
		$this->commit();

		return true;

	}



}