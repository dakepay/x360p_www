<?php

namespace app\api\controller;

use think\Request;
use app\api\model\FranchiseePerson;
use app\api\model\Franchisee;

class FranchiseePersons extends Base
{
	public function get_list(Request $request)
	{
		$input = $request->get();
		$model = new FranchiseePerson;

		$w = [];
		$ret = $model->where($w)->getSearchResult($input);

		return $this->sendSuccess($ret);
	}
    
    

    /**
     * 添加加盟商联系人
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function post(Request $request)
	{
		$input = $request->post();
		$rule = [
            'name|姓名'       => 'require|min:2',
            'mobile|手机号'   => 'require|1[0-9]{10}',
            'email|邮箱'      => 'email',
		];
		$result = $this->validate($input,$rule);
		if(true !== $result){
			return $this->sendError(400,$result);
		}

		$model = new FranchiseePerson;

		$fcp_id = $model->addFranchiseePerson($input);

		if(!$fcp_id){
			return $this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess($fcp_id);

	}
    
    /**
     * 编辑加盟商联系人
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function put(Request $request)
	{
		$fcp_id = input('id/d');
		if(empty($fcp_id)){
			return $this->sendError(400,'param error');
		}
		$franchisee_person = FranchiseePerson::get($fcp_id);
		if(empty($franchisee_person)){
			return $this->sendError(400,'联系人不存在，或已删除');
		}

		$input = $request->put();
		$rule = [
            'name|姓名'       => 'require|min:2',
            'mobile|手机号'   => 'require|1[0-9]{10}',
            'email|邮箱'      => 'email',
		];
		$result = $this->validate($input,$rule);
		if(true !== $result){
			return $this->sendError(400,$result);
		}
		
		$model = new FranchiseePerson;

		$ret = $model->updateFranchiseePerson($input,$fcp_id);

		if(false === $ret){
			return $this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess();

	}

    /**
     * 删除加盟商联系人
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function delete(Request $request)
	{
		$fcp_id = input('id/d');
		if(empty($fcp_id)){
			return $this->sendError(400,'param error');
		}
		$franchisee_person = FranchiseePerson::get($fcp_id);
		if(empty($franchisee_person)){
			return $this->sendError(400,'联系人不存在，或已删除');
		}

		$model = new FranchiseePerson;

		$ret = $model->deleteFranchiseePerson($fcp_id);
		if(true !== $ret){
			return $this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess();

	}

}