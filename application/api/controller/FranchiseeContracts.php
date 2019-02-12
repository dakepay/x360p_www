<?php 

namespace app\api\controller;

use think\Request;
use app\api\model\FranchiseeContract;
use app\api\model\FranchiseeContractFile;

class FranchiseeContracts extends Base
{
	public function get_list(Request $request)
	{
		$input = $request->get();
		$model = new FranchiseeContract;
		$w = [];
		if(isset($input['eid'])){
		    $w['sign_eid|service_eid'] = $input['eid'];
		    unset($input['eid']);
        }
		$ret = $model->where($w)->getSearchResult($input);

		return $this->sendSuccess($ret);
	}

    /**
     * 根据合同ID 获取合同附件
     * get: api/franchisee_contracts/1/contract_files
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function get_list_contract_files(Request $request)
	{
		$fcc_id = input('id/d');
		if(empty($fcc_id)){
			return $this->sendError(400,'param error');
		}
		$franchisee_contract = Franchisee::get($fcc_id);
		if(empty($franchisee_contract)){
			return $this->sendError(400,'合同不存在，或已删除');
		}
		$model = new FranchiseeContractFile;
		$input = $request->get();
		$ret = $model->getSearchResult($input);

		return $this->sendSuccess($ret);

	}

    
    /**
     * 添加加盟商合同
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function post(Request $request)
    {
    	$franchisee_contract = $request->post();

        $rule = [
            'contract_no|合同号'              => 'require|min:4',
            'contract_start_int_day|开始日期' => 'require|date',
            'contract_end_int_day|结束日期'   => 'require|date',
            'sign_eid|签约员工'               => 'require',
        ];
        $result = $this->validate($franchisee_contract,$rule);
        if(false === $result){
            return $this->sendError(400,$result);
        }

    	$franchisee_contract_file = isset($franchisee_contract['franchisee_contract_file']) ? $franchisee_contract['franchisee_contract_file'] : [];

    	$model = new FranchiseeContract;

    	$ret = $model->addOneFranchiseeContract($franchisee_contract,$franchisee_contract_file);

    	if(false === $ret){
    		return $this->sendError(400,$model->getErrorMsg());
    	}

    	return $this->sendSuccess($ret);

    }

    /**
     * 编辑加盟商合同
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function put(Request $request)
    {
    	$fcc_id = input('id/d');
    	if(empty($fcc_id)){
    		return $this->sendError(400,'params error');
    	}
    	$franchisee_contract = FranchiseeContract::get($fcc_id);
    	if(empty($franchisee_contract)){
    		return $this->sendError(400,'合同不存在，或已删除');
    	}

    	$put = $request->put();
        $rule = [
            'contract_no|合同号'              => 'require|min:4',
            'contract_start_int_day|开始日期' => 'require|date',
            'contract_end_int_day|结束日期'   => 'require|date',
            'sign_eid|签约员工'               => 'require',
        ];
        $result = $this->validate($put,$rule);
        if(false === $result){
            return $this->sendError(400,$result);
        }

    	$franchisee_contract_file = isset($put['franchisee_contract_file']) ? $put['franchisee_contract_file'] : [];

    	$ret = $franchisee_contract->updateContract($put,$franchisee_contract_file,$fcc_id);

    	if(false === $ret){
    		return $this->sendError(400,$franchisee_contract->getErrorMsg());
    	}

    	return $this->sendSuccess();

    }

    /**
     * 删除合同
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete(Request $request)
    {
        $fcc_id = input('id/d');
        if(empty($fcc_id)){
            return $this->sendError(400,'param error');
        }
        $franchisee_contract = FranchiseeContract::get($fcc_id);
        if(empty($franchisee_contract)){
            return $this->sendError(400,'合同不存在，或已删除');
        }

        $model = new FranchiseeContract;
        $ret = $model->deleteFranchiseeContract($franchisee_contract,$fcc_id);
        if(false === $ret){
            return $this->sendError(400,$franchisee_contract->getErrorMsg());
        }

        return $this->sendSuccess();

    }


}