<?php

namespace app\api\controller;

use think\Request;
use app\api\model\Franchisee;
use app\api\model\Org;
use app\api\model\User;
use app\api\model\FranchiseeContract;
use app\api\model\FranchiseePerson;

class Franchisees extends Base
{
	public function get_list(Request $request)
	{
		$input = $request->get();
		$m_franchisee = new Franchisee;
        $w = [];
		if(isset($input['service_eid']) && strpos($input['service_eid'],',') !== false){
		    $arr_eids = explode(',',$input['service_eid']);
		    unset($input['service_eid']);
		    $w['service_eid'] = ['IN',$arr_eids];
        }


		$ret = $m_franchisee->where($w)->getSearchResult($input);

		return $this->sendSuccess($ret);

	}


	/**
     * 根据加盟商ID 获取联系人
     * get: api/franchisee_persons/1/persons
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function get_list_persons(Request $request)
	{
		$fc_id = input('id/d');
		if(empty($fc_id)){
			return $this->sendError(400,'param error');
		}
		$franchisee = Franchisee::get($fc_id);
		if(empty($franchisee)){
			return $this->sendError(400,'加盟商不存在，或已删除');
		}

		$model = new FranchiseePerson;
		$input = $request->get();
		$input['fc_id'] = $fc_id;
		$ret = $model->getSearchResult($input);

		return $this->sendSuccess($ret);

	}


	public function get_detail(Request $request,$id = 0)
	{
		$fc_id = input('id/d');
		if(empty($fc_id)){
			return $this->sendError(400,'param error');
		}

		$franchisee = Franchisee::get($fc_id);
		$franchisee['org'] = Org::get(['fc_id'=>$fc_id]);
        if(!empty($franchisee['org'])){
        	$model = new User;
			$model->skipOgId();
			$w['og_id'] = $franchisee['org']['og_id'];
			$w['is_main'] = 1;
			$w['is_admin'] = 1;
			$franchisee['org']['user'] = $model->where($w)->find();
        }
		
		if(empty($franchisee)){
			return $this->sendError(400,'加盟商不存在或已删除');
		}

		return $this->sendSuccess($franchisee);

	}
    

    /**
     * 添加加盟商
     * post Franchisees
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function post(Request $request)
	{
		$input = $request->post();

		$rule = [
            'org_name|加盟商名称' => 'require|min:4',
            'status|运营状态' => 'require',
            'org_address|机构地址' => 'require',
            'mobile|手机号'   => 'require|1[0-9]{10}',
		];
		$result = $this->validate($input,$rule);
		if(true !== $result){
			return $this->sendError(400,$result);
		}

		$model = new Franchisee;

		$fc_id = $model->addOneFranchisee($input);

		if(!$fc_id){
			return $this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess($fc_id);

	}

    /**
     * 编辑加盟商
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function put(Request $request)
	{
		$fc_id = input('id/d');
		if(empty($fc_id)){
			return $this->sendError(400,'params error');
		}
		$franchisee = Franchisee::get($fc_id);
		if(empty($franchisee)){
			return $this->sendError(400,'加盟商不存在或已删除');
		}
		
		$input = $request->put();
		$rule = [
            'org_name|加盟商名称' => 'require|min:4',
            'status|运营状态' => 'require',
            'org_address|机构地址' => 'require',
            'mobile|手机号'   => 'require|1[0-9]{10}',
		];
		$result = $this->validate($input,$rule);
		if(true !== $result){
			return $this->sendError(400,$result);
		}

		$model = new Franchisee;

		$ret = $model->updateFranchisee($input,$fc_id);

		if(false === $ret){
			return $this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess();
	}

    /**
     * 删除加盟商
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function delete(Request $request)
	{
        $fc_id = input('id/d');
		if(empty($fc_id)){
			return $this->sendError(400,'params error');
		}
		$franchisee = Franchisee::get($fc_id);
		if(empty($franchisee)){
			return $this->sendError(400,'加盟商不存在或已删除');
		}

		$model = new Franchisee;

		$ret = $model->deleteFranchisee($fc_id);
		if(true !== $ret){
			return $this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess();
	}

    /**
     * 关联已有系统
     * post api/franchisee/1/doconnect
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function do_connect(Request $request)
	{
		$fc_id = input('id/d');
		if(empty($fc_id)){
			return $this->sendError(400,'param error');
		}

		$input = $request->param();
		$og_id = $input['og_id'];
		if(empty($og_id)){
			return $this->sendError(400,'请选择关联的系统');
		}
		$org = Org::get($og_id);
		if($org['fc_id']){
			return $this->sendError(400,'系统已关联加盟商');
		}

        $model = new Franchisee;
		$ret = $model->connectSystem($fc_id,$og_id);

        if(!$ret){
        	return $this->sendError(400,$model->getErrorMsg());
        }

        return $this->sendSuccess();

	}

    /**
     * 开通校360  提交审核
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function do_create(Request $request)
	{
		$fc_id = input('id/d');
		if(empty($fc_id)){
			return $this->sendError(400,'param error');
		}
		$franchisee = Franchisee::get($fc_id);
		if(empty($franchisee)){
			return $this->sendError(400,'加盟商不存在或已删除');
		}
		$franchisee_contract = FranchiseeContract::get(['fc_id'=>$fc_id]);
		if(empty($franchisee_contract)){
			return $this->sendError(400,'加盟商未签约，不能开通校360');
		}
		$org = Org::get(['fc_id'=>$fc_id]);
		if(!empty($org)){
			return $this->sendError(400,'加盟商已开通校360');
		}

		$post = $request->post();
		// print_r($post);exit;
		$org_data = isset($post['org']) ? $post['org'] : [];
		$account_data = isset($post['user']) ? $post['user'] : [];

		$model = new Org;

		$ret = $model->createSystem($org_data,$account_data,$franchisee,$fc_id);

		if(false === $ret){
			return $this->sendError(400,$model->getError());
		}

		return $this->sendSuccess();

	}


	public function do_confirm(Request $request)
	{
		$og_id = input('id/d');
		if(empty($og_id)){
			return $this->sendError(400,'param error');
		}
		$post = $request->post();
		$org = isset($post['org']) ? $post['org'] : [];
		$account = isset($post['user']) ? $post['user'] : [];
		$data = [
            'host'               =>   $org['host'],
            'account_num_limit'  =>   $org['account_num_limit'],
            'branch_num_limit'   =>   $org['branch_num_limit'],
            'student_num_limit'  =>   $org['student_num_limit'],
            'expire_day'         =>   $org['expire_day'],
            'init_account'       =>   $account['account'],
            'init_password'      =>   $account['password'],
            'status'             =>   $account['status'],
            'is_admin'           =>   $account['is_admin'],
		];
		$m_org = new Org;
		$ret = $m_org->updateOrgData($data,$og_id,false);
		if(false === $ret){
			return $this->sendError(400,$m_org->getErrorMsg());
		}
	
		$org_data = Org::get($og_id);
		if(empty($org_data)){
			return $this->sendError(400,'机构不存在或已删除');
		}

		$franchisee = Franchisee::get(['fc_og_id'=>$og_id]);
		if(empty($franchisee)){
            return $this->sendError(400,'校360系统还未关联加盟商');
		}

		$model = new Org;

		$ret = $model->confirmSystem($org_data,$franchisee,$data);

		if(false === $ret){
			return $this->sendError(400,$model->getError());
		}

		return $this->sendSuccess();
	}


}