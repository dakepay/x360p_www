<?php

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;
use think\Hook;
use think\Log;

class FranchiseeContract extends Base
{

	public function setContractStartIntDayAttr($value,$data){
        return format_int_day($value);
    }

    public function setContractEndIntDayAttr($value,$data){
        return format_int_day($value);
    }

    public function setAllPayIntDayAttr($value,$data){
        return format_int_day($value);
    }

    public function setOpenIntDayAttr($value,$data){
        return format_int_day($value);
    }

    public function getContractStartIntDayAttr($value,$data){
        return $value ? int_day_to_date_str($value) : '';
    }

    public function getContractEndIntDayAttr($value,$data){
        return $value ? int_day_to_date_str($value) : '';
    }

    public function getAllPayIntDayAttr($value,$data){
        return $value ? int_day_to_date_str($value) : '';
    }

    public function getOpenIntDayAttr($value,$data){
        return $value ? int_day_to_date_str($value) : '';
    }

	public function franchiseeContractFile()
	{
		return $this->hasMany('FranchiseeContractFile','fcc_id','fcc_id');
	}

    public function franchisee()
    {
    	return $this->belongsTo('Franchisee','fc_id','fc_id');
    }

    /**
     * 添加一个加盟商合同
     * @param [type] $franchisee_contract      [description]
     * @param [type] $franchisee_contract_file [description]
     */
	public function addOneFranchiseeContract($franchisee_contract,$franchisee_contract_file = [])
	{

		$this->startTrans();
		try{

			// 添加合同
			$ret = $this->data([])->allowField(true)->isUpdate(false)->save($franchisee_contract);

			if(false === $ret){
				$this->rollback();
				return $this->sql_add_error('franchisee_contract');
			}
			$fcc_id = $this->getAttr('fcc_id');

			// 更新 加盟商合同日期
			$franchisee = Franchisee::get($franchisee_contract['fc_id']);
			$franchisee['contract_start_int_day'] = $franchisee_contract['contract_start_int_day'];
			$franchisee['contract_end_int_day']   = $franchisee_contract['contract_end_int_day'];
			$franchisee['open_int_day']           = $franchisee_contract['open_int_day'];
			$franchisee['is_sign'] = Franchisee::IS_SIGN_YES;
			$ret = $franchisee->save();
			if(false === $ret){
				$this->rollback();
				return $this->sql_save_error('franchisee');
			}

			// 添加合同附件
			if(!empty($franchisee_contract_file)){
				$m_file = new File;
				$m_fcf = new FranchiseeContractFile;
				foreach ($franchisee_contract_file as $per_file) {
					if(empty($per_file['file_id'])){
						log_write($per_file,'error');
						continue;
					}
					$file = $m_file->find($per_file['file_id']);
					$file = $file ? $file->toArray() : [];
					$per_file = array_merge($per_file,$file);
					$per_file['fcc_id'] = $fcc_id;

					$ret = $m_fcf->data([])->allowField(true)->isUpdate(false)->save($per_file);
					if(false === $ret){
						$this->rollback();
						return $this->sql_add_error('franchisee_contract_file');
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
     * 编辑加盟商合同
     * @param  [type]  $contract_data [description]
     * @param  [type]  $contract_file [description]
     * @param  integer $fcc_id        [description]
     * @return [type]                 [description]
     */
	public function updateContract($contract_data,$contract_file=[],$fcc_id=0)
	{
		$this->startTrans();
		try{

			$franchisee = Franchisee::get($contract_data['fc_id']);
			$franchisee = $franchisee ? $franchisee : $contract_data['franchisee'];
			$contract_data['fc_id'] = $contract_data['fc_id'] ? $contract_data['fc_id'] : $franchisee['fc_id'];

			$where['fcc_id'] = $fcc_id;

			// 更新 加盟商合同
			$ret = $this->allowField(true)->isUpdate(true)->save($contract_data,$where);
			if(false === $ret){
				$this->rollback();
				return $this->sql_save_error('franchisee_contract');
			}

			// 更新 加盟商合同日期
			$data['contract_start_int_day'] = $contract_data['contract_start_int_day'];
			$data['contract_end_int_day']   = $contract_data['contract_end_int_day'];
			$data['open_int_day']   = $contract_data['open_int_day'];
			$w['fc_id'] = $franchisee['fc_id'];
			$ret = (new Franchisee)->save($data,$w);
			if(!$ret){
				$this->rollback();
				return $this->sql_save_error('franchisee');
			}

			// 更新加盟商合同附件
			$old_file_ids = $this->franchiseeContractFile()->column('file_id');
			$new_file_ids = array_column($contract_file,'file_id');
            $del_file_ids = array_diff($old_file_ids,$new_file_ids);
            $add_file_ids = array_diff($new_file_ids,$old_file_ids);
            $ret = $this->franchiseeContractFile()->where('file_id','in',$del_file_ids)->delete();
            if(false === $ret){
            	$this->rollback();
            	return $this->sql_delete_error('franchisee_contract_file');
            }

            $m_file = new File();
            $m_fcf = new FranchiseeContractFile();

            foreach ($add_file_ids as $per_file_id) {
            	$file = $m_file->find($per_file_id);
            	if(empty($file)){
            		return $this->user_error('合同附件不存在');
            	}
            	$file = $file ? $file->toArray() : [];
                
                $per_file['fcc_id'] = $fcc_id;
            	$per_file = array_merge($per_file,$file);
            	// print_r($per_file);exit;
				$ret = $m_fcf->data([])->allowField(true)->isUpdate(false)->save($per_file);

				if(false === $ret){
					$this->rollback();
					return $this->sql_add_error('franchisee_contract_file');
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
     * 删除合同
     * @param  [type] $frachisee_contract [description]
     * @return [type]                     [description]
     */
	public function deleteFranchiseeContract($franchisee_contract,$fcc_id=0)
	{
		$this->startTrans();
		try{
            
            // 删除合同
			$ret = $franchisee_contract->delete();
			if(false === $ret){
				$this->rollback();
				return $this->sql_delete_error('franchisee_contract');
			}

			// 删除合同附件
			$model = new FranchiseeContractFile;
    		$franchisee_contract_file = $model->where('fcc_id',$fcc_id)->select();
    		if($franchisee_contract_file){
    			$ret = $model->where('fcc_id','in',array_column($franchisee_contract_file,'fcc_id'))->delete();
    		}
			if(false === $ret){
				$this->rollback();
				return $this->sql_delete_error('franchisee_contract_file');
			}

			// 如果加盟商没有合同 更新加盟商签约状态
			$fc_id = $franchisee_contract['fc_id'];
			$contract = (new FranchiseeContract)->where('fc_id',$fc_id)->select();
			if(empty($contract)){
				$franchisee = Franchisee::get($fc_id);
				$franchisee['is_sign'] = Franchisee::IS_SIGN_NO;
				$ret = $franchisee->save();
				if(!$ret){
					$this->rollback();
					return $this->sql_save_error('franchisee');
				}
			}

		}catch(\Exception $e){
			$this->rollback();
			return $this->exception_error($e);
		}
		$this->commit();
		return true;

	}


}