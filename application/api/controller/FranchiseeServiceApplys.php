<?php

namespace app\api\controller;

use think\Request;
use app\api\model\FranchiseeServiceApply;
use app\api\model\Franchisee;

class FranchiseeServiceApplys extends Base
{
	/**
	 * 加盟商获取自己的申请列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function get_list(Request $request)
	{
		$input = $request->get();
		$model = new FranchiseeServiceApply;

		if(isset($input['og_id'])) {
            gvar('og_id', $input['og_id']);
        }

		$w = [];
		$ret = $model->where($w)->getSearchResult($input);

		gvar('og_id', gvar('client.og_id'));

		return $this->sendSuccess($ret);
	}

    /**
     * 总部获取所有加盟商的申请列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function get_all_applys(Request $request)
	{
		$input = $request->get();
		$model = new FranchiseeServiceApply();
		$model->skipOgId();
		$w = [];
		if(isset($input['fc_og_id'])){
			$w['fc_og_id'] = $input['fc_og_id'];
		}
		$ret = $model->where($w)->getSearchResult($input);
		foreach ($ret['list'] as &$row) {
			$row['apply_eid'] = get_teacher_name($row['apply_eid']);
	    }
		return $this->sendSuccess($ret);

	}

	/**
     * 获取加盟商新提交的服务申请数量
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function get_new_apply_nums(Request $request)
	{
		$input = $request->get();
		$model = new FranchiseeServiceApply();
		$model->skipOgId();
		// $w['status'] = FranchiseeServiceApply::STAY_SERVICE;
		$w['status'] = ['in',['0','1']];
		$user = gvar('user');
		$w['service_eid'] = $user['employee']['eid'];

		$ret = $model->where($w)->getSearchResult($input);

		return $ret['total'];

	}

    

    /**
     * 加盟商提交服务申请
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function post(Request $request)
	{
		$input = $request->post();
		$m_franchisee = new Franchisee;
		$m_franchisee->skipOgId();
		$franchisee = $m_franchisee->where('fc_id',$input['fc_id'])->find();
		$input['service_eid'] = $franchisee['service_eid'] ? $franchisee['service_eid'] : 0;
		$rule = [
            'title|标题'               =>  'require|min:4',
            'fc_service_did|服务类型'  =>   'require',
            'apply_eid|申请员工'       =>   'require',
		];
		$result = $this->validate($input,$rule);
		if(false === $result){
			return $this->sendError(400,$result);
		}

		$model = new FranchiseeServiceApply();

		$ret = $model->addFranchiseeServiceApply($input);

		if($ret === false){
			return $this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess();

	}

    /**
     * 接受服务申请
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function do_receive(Request $request)
	{
		$fsa_id = input('id/d');
		if(empty($fsa_id)){
			return $this->sendError(400,'param error');
		}
		/*$franchisee_service_apply = FranchiseeServiceApply::get($fsa_id);
		if(empty($franchisee_service_apply)){
			return $this->sendError(400,'服务申请不存在，或已删除');
		}*/
		$model = new FranchiseeServiceApply();
		$model->skipOgId();
		$franchisee_service_apply = $model->where('fsa_id',$fsa_id)->find();
		if(empty($franchisee_service_apply)){
			return $this->sendError(400,'服务申请不存在，或已删除');
		}

		$ret = $model->receiveApply($franchisee_service_apply);

		if(false === $ret){
			return $this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess();

	}
    

    /**
     * 编辑服务申请
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function put(Request $request)
	{
		$fsa_id = input('id/d');
		if(empty($fsa_id)){
			return $this->sendError(400,'param error');
		}
		$franchisee_service_apply = FranchiseeServiceApply::get($fsa_id);
		if(empty($franchisee_service_apply)){
			return $this->sendError(400,'服务申请不存在，或已删除');
		}

		$put = $request->put();
		$rule = [
            'title|标题'               =>  'require|min:4',
            'fc_service_did|服务类型'  =>   'require',
            'apply_eid|申请员工'       =>   'require',
		];
		$result = $this->validate($put,$rule);
		if(false === $result){
			return $this->sendError(400,$result);
		}

        $model = new FranchiseeServiceApply();
		$ret = $model->updateFranchiseeServiceApply($put,$fsa_id);

		if(false === $ret){
			$this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess();

	}

    /**
     * 删除服务申请
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function delete(Request $request)
	{
		$fsa_id = input('id/d');
		if(empty($fsa_id)){
			return $this->sendError(400,'param error');
		}
		$franchisee_service_apply = FranchiseeServiceApply::get($fsa_id);
		if(empty($franchisee_service_apply)){
			return $this->sendError(400,'服务申请不存在，或已删除');
		}
        
        $model = new FranchiseeServiceApply();
        $ret = $model->deleteFranchiseeServiceApply($franchisee_service_apply);

        if(false === $ret){
        	return $this->sendError(400,$model->getErrorMsg());
        }

        return $this->sendSuccess();
        
	}


}