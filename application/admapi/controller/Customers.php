<?php
/**
 * Author: luo
 * Time: 2017-12-13 10:38
**/

namespace app\admapi\controller;

use app\admapi\model\Customer;
use app\admapi\model\CustomerFollowUp;
use think\Request;

class Customers extends Base
{
    public $withoutAuthAction = ['add'];

    public function get_list(Request $request)
    {
        $input = $request->param();
        $m_customer = new customer();
        $ret = $m_customer->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $data = $request->post();
        $m_customer = new Customer();
        $rs = $m_customer->addOneCustomer($data);
        if(!$rs) return $this->sendError(400, $m_customer->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  不需要登录录入客户信息
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function add(Request $request)
    {
        $data = $request->post();
        $m_customer = new Customer();
        $data['from_did'] = 1024 ; //表单F1;
        $rs = $m_customer->addOneCustomer($data);
        if(!$rs) return $this->sendError(400, $m_customer->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = $request->put();
        $cu_id = input('id/d', 0);
        $customer = Customer::get(['cu_id' => $cu_id]);
        if(empty($customer)) return $this->sendError(400, '不存在此客户');

        $rs = $customer->updateCustomer($customer, $input);
        if($rs === false) return $this->sendError(400, $customer->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $cu_id = input('id/d');
        $customer = Customer::get(['cu_id' => $cu_id]);
        if(empty($customer)) return $this->sendError(400, '不存在此客户');

        $rs = $customer->deleteCustomer($customer);
        if($rs === false) return $this->sendError(400, $customer->getErrorMsg());

        return $this->sendSuccess();
    }

    public function post_follow_up(Request $request)
    {
        $cu_id = input('id/d');
        $input = $request->post();
        $customer = Customer::get(['cu_id' => $cu_id]);
        if(empty($customer)) return $this->sendError(400, '客户不存在');

        $input['cu_id'] = $cu_id;
        $m_cfu = new CustomerFollowUp();
        $rs = $m_cfu->addOneFollowUp($input);
        if($rs === false) return $this->sendError(400, $m_cfu->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * 转入公海客户
     * @param  Request $request [description]
     * @return [type]           [description]
     * @method POST
     */
    public function intoPublicSea(Request $request){
        $input = input();
        $c_model = new Customer();
        $rs = $c_model->batIntoPublicSea($input['cu_ids']);

        if($rs === false) return $this->sendError(400, $c_model->getErrorMsg());
        return $this->sendSuccess('转入客户公海成功');
    }

    /**
     * 转出公海客户
     * @param  Request $request [description]
     * @return [type]           [description]
     * @method POST
     */
    public function outPublicSea(Request $request){
        $input = input();
        $c_model = new Customer();
        $rs = $c_model->batOutPublicSea($input['eid'],$input['cu_ids']);

        if($rs === false) return $this->sendError(400, $c_model->getErrorMsg());

        return $this->sendSuccess('转出客户公海成功');

    }

    /**
     * 抢占公海客户
     * @param  Request $request [description]
     * @return [type]           [description]
     * @method POST
     */
    public function robPublicSea(Request $request){
        $input = input();
        $c_model = new Customer();
        $rs = $c_model->robPublicSea($input['cu_ids']);

        if($rs === false) return $this->sendError(400, $c_model->getErrorMsg());
        return $this->sendSuccess('抢占客户公海成功');

    }




}