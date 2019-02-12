<?php

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class FranchiseeServiceRecord extends Base
{
	public function franchiseeServiceRecordFile()
	{
		return $this->hasMany('FranchiseeServiceRecordFile','fsr_id','fsr_id');
	}

	public function franchisee()
    {
    	return $this->belongsTo('Franchisee','fc_id','fc_id');
    }

    public function franchiseeServiceApply()
    {
    	return $this->belongsTo('FranchiseeServiceApply','fsa_id','fsa_id');
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
    	return $value ? int_day_to_date_str($value) : '';
    }

    public function getIntHourAttr($value,$data)
    {
    	return $value ? int_hour_to_hour_str($value) : '';
    }


    /**
     * 添加服务
     * @param [type] $service_record      [description]
     * @param [type] $service_record_file [description]
     */
    public function addFranchiseeServiceRecord($service_record,$service_record_file = [])
    {
    	$this->startTrans();
    	try{

    		// 1、添加服务
            $ret = $this->data([])->allowField(true)->isUpdate(false)->save($service_record);
            if(false === $ret){
                $this->rollback();
                return $this->sql_add_error('franchisee_service_record');
            }
            $fsr_id = $this->getAttr('fsr_id');
            $fsa_id = $this->getAttr('fsa_id');

            // 2、添加服务附件
            if(!empty($service_record_file)){
            	$m_file = new File;
            	$m_fsrf = new FranchiseeServiceRecordFile;

            	foreach ($service_record_file as $per_file) {
            		if(empty($per_file['file_id'])){
            			log_write($per_file,'error');
            			continue;
            		}
            		$file = $m_file->find($per_file['file_id']);
            		$file = $file ? $file->toArray() : [];

            		$per_file = array_merge($per_file,$file);
            		$per_file['fsr_id'] = $fsr_id;

            		$ret = $m_fsrf->data([])->allowField(true)->isUpdate(false)->save($per_file);

            		if(false === $ret){
                        $this->rollback();
                        return $this->sql_add_error('franchisee_service_record_file');
            		}
            	}
            }

            // 3、更新服务申请
            if($fsa_id){
	            $model = new FranchiseeServiceApply;
	            $model->skipOgId();
			    $service_apply = $model->where('fsa_id',$fsa_id)->find();
	            // $service_apply = FranchiseeServiceApply::get(['fsa_id'=>$fsa_id]);
	            $service_apply['status'] = FranchiseeServiceApply::FINISH_SERVICE;
	            $service_apply['int_day'] = $service_record['int_day'];
	            $service_apply['int_hour'] = $service_record['int_hour'];
	            $ret = $service_apply->save();
	            if(!$ret){
                    $this->rollback();
                    return $this->sql_save_error('franchisee_service_apply');
	            }
            }

    	}catch(\Exception $e){
			$this->rollback();
			return $this->exception_error($e);
		}
		$this->commit();

		return true;

    }


    /**
     * 编辑服务
     * @param  [type]  $record_data [description]
     * @param  [type]  $record_file [description]
     * @param  integer $fsr_id      [description]
     * @return [type]               [description]
     */
    public function updateFranchiseeServiceRecord($record_data,$record_file = [],$fsr_id = 0)
    {
    	$this->startTrans();
    	try{

    		$where['fsr_id'] = $fsr_id;
    		// 更新服务
    		$model = new self();
    		$ret = $model->allowField(true)->isUpdate(true)->save($record_data,$where);
    		if(false === $ret){
                $this->rollback();
                return $this->sql_save_error('franchisee_service_record');
    		}

    		// 更新服务附件
    		$old_file_ids = $this->franchiseeServiceRecordFile()->column('file_id');
    		$new_file_ids = array_column($record_file,'file_id');

    		$del_file_ids = array_diff($old_file_ids,$new_file_ids);
    		$add_file_ids = array_diff($new_file_ids,$old_file_ids);

    		// 删除服务附件
    		if($del_file_ids){
    			$ret = $this->franchiseeServiceRecordFile()->where('file_id','in',$del_file_ids)->delete();
    		}
    		if(false === $ret){
                $this->rollback();
                return $this->sql_delete_error('franchisee_service_record_file');
    		}

    		// 添加服务附件
    		if(!empty($add_file_ids)){
    			$m_file = new File;
    			$m_fsrf = new FranchiseeServiceRecordFile;

    			foreach ($add_file_ids as $per_file_id) {
    				$file = $m_file->find($per_file_id);
    				if(empty($file)){
    					return $this->user_error('服务附件不存在');
    				}
    				$file = $file ? $file->toArray() : [];
    				$per_file['fsr_id'] = $fsr_id;
    				$per_file = array_merge($per_file,$file);

    				$ret = $m_fsrf->data([])->allowField(true)->isUpdate(false)->save($per_file);
    				if(false === $ret){
                        $this->rollback();
                        return $this->sql_add_error('franchisee_service_record_file');
    				}

    			}
    		}

    	}catch(\Exception $e){
			$this->rollback();
			return $this->exception_error($e);
		}
		$this->commit();

		return true;

    }


    /**
     * 删除服务记录
     * @param  [type]  $service_record [description]
     * @param  integer $fsr_id         [description]
     * @return [type]                  [description]
     */
    public function deleteFranchiseeServiceRecord($service_record,$fsr_id = 0)
    {
    	$this->startTrans();
    	try{

    		// 删除服务
    		$ret = $service_record->delete();
    		if(false === $ret){
                $this->rollback();
    			return $this->sql_delete_error('franchisee_service_record');
    		}

    		// 删除服务记录附件
    		$m_fsrf = new FranchiseeServiceRecordFile();
    		$franchisee_service_record_file = $m_fsrf->where('fsr_id',$fsr_id)->select();
    		if($franchisee_service_record_file){
    			$ret = $m_fsrf->where('fsr_id','in',array_column($franchisee_service_record_file,'fsr_id'))->delete();
    		}
    		if(false === $ret){
                $this->rollback();
    			return $this->sql_delete_error('franchisee_service_record_file');
    		}

    	}catch(\Exception $e){
			$this->rollback();
			return $this->deal_exception($e->getMessage(),$e);
		}
		$this->commit();

		return true;

    }








}