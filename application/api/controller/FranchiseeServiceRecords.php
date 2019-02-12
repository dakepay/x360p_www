<?php

namespace app\api\controller;

use think\Request;
use app\api\model\FranchiseeServiceRecord;
use app\api\model\FranchiseeServiceRecordFile;
use app\api\model\FranchiseeServiceApply;
use app\api\model\Franchisee;

class FranchiseeServiceRecords extends Base
{
	public function get_list(Request $request)
	{
		$input = $request->get();
		if(!isset($input['order_field'])){
		    $input['order_field'] = 'int_day';
		    $input['order_sort']  = 'DESC';
        }
		$model = new FranchiseeServiceRecord();

		$w = [];
		$ret = $model->where($w)->getSearchResult($input);
		foreach ($ret['list'] as &$row) {
			if($row['fsa_id']){
				$model = new FranchiseeServiceApply;
				$model->skipOgId();
				$row['service_apply'] = $model->where('fsa_id',$row['fsa_id'])->find();
				$row['service_apply']['apply_eid'] = get_teacher_name($row['service_apply']['apply_eid']);
			}
		}

		return $this->sendSuccess($ret);
	}


    /**
     * 添加服务
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function post(Request $request)
	{
		$service_record = $request->post();
		$rule = [
            'content|服务内容'     => 'require',
		];
		$result = $this->validate($service_record,$rule);
		if(false === $result){
			return $this->sendError(400,$result);
		}

		if($service_record['fc_og_id'] == 0){
			$fc_id = $service_record['fc_id'];
			$franchisee = Franchisee::get($fc_id);
			$service_record['fc_og_id'] = $franchisee['fc_og_id'];
		}

		$service_record_file = isset($service_record['franchisee_service_record_file']) ? $service_record['franchisee_service_record_file'] : [];

		$model = new FranchiseeServiceRecord;

		$ret = $model->addFranchiseeServiceRecord($service_record,$service_record_file);

		if(true !== $ret){
			return $this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess();

	}

    /**
     * 编辑服务
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function put(Request $request)
	{
		$fsr_id = input('id/d');
		if(empty($fsr_id)){
			return $this->sendError(400,'param error');
		}
		$franchisee_service_record = FranchiseeServiceRecord::get($fsr_id);
		if(empty($franchisee_service_record)){
			return $this->sendError(400,'服务记录不存在，或已删除');
		}

		$put = $request->put();
		$rule = [
            'content|服务内容'     => 'require',
		];
		$result = $this->validate($put,$rule);
		if(false === $result){
			return $this->sendError(400,$result);
		}
		
		$service_record_file = isset($put['franchisee_service_record_file']) ? $put['franchisee_service_record_file'] : [];

		$ret = $franchisee_service_record->updateFranchiseeServiceRecord($put,$service_record_file,$fsr_id);

		if(false === $ret){
			return $this->sendError(400,$franchisee_service_record->getErrorMsg());
		}
		return $this->sendSuccess();


	}


    /**
     * 删除服务记录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function delete(Request $request)
	{
		$fsr_id = input('id/d');
		if(empty($fsr_id)){
			return $this->sendError(400,'param error');
		}
		$service_record = FranchiseeServiceRecord::get($fsr_id);
		if(empty($service_record)){
			return $this->sendError(400,'服务记录不存在，或已删除');
		}

		$model = new FranchiseeServiceRecord;

		$ret = $model->deleteFranchiseeServiceRecord($service_record,$fsr_id);

		if(true !== $ret){
			return $this->sendError(400,$model->getErrorMsg());
		}

		return $this->sendSuccess();

	}






}