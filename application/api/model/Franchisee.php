<?php

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;
use think\Hook;
use think\Log;

class Franchisee extends Base
{
    const IS_SIGN_YES = 1;   #已签约
    const IS_SIGN_NO  = 0;   # 未签约

    const SYSTEM_STATUS_NO = 0;   #未开通
    const SYSTEM_STATUS_WAIT = 1; #待开通
    const SYSTEM_STATUS_YES = 2;  #已开通

	public function setContractStartIntDayAttr($value,$data){
        return format_int_day($value);
    }

    public function setContractEndIntDayAttr($value,$data){
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

    public function getOpenIntDayAttr($value,$data){
        return $value ? int_day_to_date_str($value) : '';
    }

    
    public function franciseeContract()
    {
    	return $this->hasMany('FranchiseeContract','fc_id','fc_id');
    }

    public function franchiseePerson()
    {
        return $this->hasMany('FranchiseePerson','fc_id','fc_id');
    }


    public function franchiseeServiceApply()
    {
        return $this->hasMany('FranchiseeServiceApply','fc_id','fc_id');
    }

    public function franchiseeServiceRecord()
    {
        return $this->hasMany('FranchiseeServiceRecord','fc_id','fc_id');
    }

    /**
     * 添加一个加盟商
     * @param  [type] &$input [description]
     * @return [type]         [description]
     */
	public function addOneFranchisee(&$input)
	{
		$this->startTrans();
		try{

			$ret = $this->data([])->allowField(true)->isUpdate(false)->save($input);
			if(false === $ret){
			    $this->rollback();
				return $this->sql_add_error('franchisee');
			}
		}catch(\Exception $e){
			$this->rollback();
			return $this->exception_error($e);
		}
		$this->commit();

		return $ret;

	}

    /**
     * 编辑加盟商
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateFranchisee($data,$fc_id=0)
    {
    	$this->startTrans();
    	try{

            $where['fc_id'] = $fc_id;
            $ret = $this->allowField(true)->isUpdate(true)->save($data,$where);
            if(false === $ret){
            	return $this->sql_save_error('franchisee');
            }

    	}catch(\Exception $e){
    		$this->rollback();
    		return $this->exception_error($e);
    	}
    	$this->commit();

    	return true;

    }

    /**
     * 删除加盟商
     * @param  [type] $fc_id [description]
     * @return [type]        [description]
     */
    public function deleteFranchisee($id)
    {
        $franchisee_contract = FranchiseeContract::get(['fc_id'=>$id]);
        if(!empty($franchisee_contract)){
            return $this->user_error('加盟商存在合同，不能删除');
        }
        $franchisee_person = FranchiseePerson::get(['fc_id'=>$id]);
        if(!empty($franchisee_person)){
            return $this->user_error('加盟商存在联系人，不能删除');
        }
        $franchisee_service_apply = FranchiseeServiceApply::get(['fc_id'=>$id]);
        if(!empty($franchisee_service_apply)){
            return $this->user_error('加盟商存在服务申请，不能删除');
        }
        $franchisee_service_record = FranchiseeServiceRecord::get(['fc_id'=>$id]);
        if(!empty($franchisee_service_record)){
            return $this->user_error('加盟商存在服务记录，不能删除');
        }

        $model = new self();

        $this->startTrans();
    	try{

    		$result = $model->where('fc_id',$id)->delete();
    		if(false === $result){
    			return $this->sql_delete_error('franchisee');
    		}

    	}catch(\Exception $e){
    		$this->rollback();
    		return $this->exception_error($e);
    	}
    	$this->commit();

    	return true;

    }

    /**
     * 关联已有系统
     * @param  [type] $fc_id [description]
     * @param  [type] $og_id [description]
     * @return [type]        [description]
     */
    public function connectSystem($fc_id,$og_id)
    {
        $org = Org::get($og_id);
        if(!$org){
            return $this->input_param_error($og_id);
        }
        $franchisee = Franchisee::get($fc_id);
        if(!$franchisee){
            return $this->input_param_error($fc_id);
        }

        $this->startTrans();
        try{
            // 更新系统 加盟商ID
            $org['fc_id'] = $fc_id;
            $result = $org->save();
            if(false === $result){
                return $this->sql_save_error('org');
            }

            // 更新加盟商 系统ID

            $franchisee['fc_og_id'] = $og_id;
            $result = $franchisee->save();
            if(false === $result){
                return $this->sql_save_error('franchisee');
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;

    }





}