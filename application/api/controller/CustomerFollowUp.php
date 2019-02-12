<?php
/** 
 * Author: luo
 * Time: 2017-10-27 16:03
**/

namespace app\api\controller;

use app\api\model\Customer;
use think\Request;
use app\api\model\MarketChannel;
use app\api\model\MarketClue;
use app\api\model\CustomerFollowUp as FollowUpModel;

class CustomerFollowUp extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();
        $input['with'] = 'customer';
        if(!isset($input['is_system'])){
            $input['is_system'] = 0;
        }
        $model = new FollowUpModel();
        if(isset($input['name'])) {
            $name = $input['name'];
            unset($input['name']);
            $cu_ids = (new Customer())->where('name', 'like', "%$name%")->column('cu_id');
            if(!empty($cu_ids)) $model->where('cu_id', 'in', $cu_ids);
        }
        $ret = $model->order('cfu_id', 'desc')->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    public function put(Request $request)
    {
        $cfu_id = input('id/d');
        $put = $request->put();
        if(isset($put['intention_level']) && empty($put['intention_level'])){
            return $this->sendError(400,'请选择意向级别!');
        }
        $follow_up = FollowUpModel::get($cfu_id);
        if(empty($follow_up)) $this->sendError(400, '跟进记录不存在');
        
        // 客户跟单 到访登记 更新 相对应市场渠道 到访人数
        $cinfo = get_customer_info($follow_up['cu_id']);
        $mc_id = $cinfo['mc_id'];
        if($mc_id && $put['is_visit'] == 1){
            (new MarketChannel)->where('mc_id',$mc_id)->setInc('visit_num');
        }elseif($mc_id && $put['is_visit'] == 0){  
            (new MarketChannel)->where('mc_id',$mc_id)->setDec('visit_num');
        }
        MarketClue::UpdateNumOfChannel($mc_id);

        $rs = $follow_up->updateFollowUp($put, $cfu_id, $follow_up);
        if($rs === false) return $this->sendError(400, $follow_up->getErrorMsg());

        return $this->sendSuccess();
    }


    public function delete(Request $request)
    {
        $cfu_id = input('id/d');
        if(empty($cfu_id)){
            return $this->sendError(400,'参数错误');
        }
        $customer_follow_up = FollowUpModel::get($cfu_id);
        if(empty($customer_follow_up)){
            return $this->sendError(400,'跟进记录不存在或已删除');
        }

        $res = $customer_follow_up->deleteFollowUp($cfu_id);
        if(false === $res){
            return $this->sendError(400,$customer_follow_up->getError());
        }

        return $this->sendSuccess();
    }



}