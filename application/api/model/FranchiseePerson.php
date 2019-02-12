<?php 

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;
use think\Hook;
use think\Log;

class FranchiseePerson extends Base
{
	/**
	 * 添加加盟商联系人
	 * @param [type] &$data [description]
	 */
	public function addFranchiseePerson($data)
	{
		// print_r($data);exit;
		$this->startTrans();
		try{
            $model = new self();
			$ret = $model->data($data)->allowField(true)->isUpdate(false)->save();

			if(false === $ret){
				$this->rollback();
				return $this->sql_add_error('franchisee_person');
			}

		}catch(\Exception $e){
			$this->rollback();
			return $this->exception_error($e);
		}
		$this->commit();

		return $ret;

	}
  
    /**
     * 编辑加盟商联系人
     * @param  [type]  $put    [description]
     * @param  integer $fcp_id [description]
     * @return [type]          [description]
     */
	public function updateFranchiseePerson($put,$fcp_id=0)
	{
		$this->startTrans();
		try{
            
            $model = new self();
			$where['fcp_id'] = $fcp_id;
			$ret = $model->allowField(true)->isUpdate(true)->save($put,$where);

			if(false === $ret){
				$this->rollback();
				return $this->sql_save_error('franchisee_person');
			}

		}catch(\Exception $e){
			$this->rollback();
			return $this->exception_error($e);
		}
		$this->commit();

		return true;
	}

    /**
     * 删除联系人
     * @param  [type] $fcp_id [description]
     * @return [type]         [description]
     */
	public function deleteFranchiseePerson($fcp_id)
	{
		$this->startTrans();
		try{

			$franchisee_person = FranchiseePerson::get($fcp_id);
			$ret = $franchisee_person->delete();

			if(false === $ret){
				$this->rollback();
				return $this->sql_delete_error('franchisee_person');
			}

		}catch(\Exception $e){
			$this->rollback();
			return $this->exception_error($e);
		}
		$this->commit();

		return true;

	}


}