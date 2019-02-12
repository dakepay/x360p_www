<?php
/**
 * Author: luo
 * Time: 2017/12/6 12:22
 */

namespace app\admapi\controller;


use app\admapi\model\Client;
use app\admapi\model\Customer;
use app\admapi\model\Employee;
use app\admapi\model\EmployeeClient;
use think\Request;

class Employees extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();
        $m_employee = new Employee();
        $ret = $m_employee->with(['user'])->getSearchResult($input);

        $m_ec = new Client();
        $m_customer = new Customer();
        foreach ($ret['list'] as &$row) {
            $row['client_num'] = $m_ec->where('eid', $row['eid'])->count();
            $row['customer_num'] = $m_customer->where('eid', $row['eid'])->where('is_buy = 0')->count();
            $rids = explode(',',$row['rids']);
            if (isset($rids[0]) && $rids[0] != ''){
                $row['rids'] = array_intval($rids);
            }else{
                $row['rids'] = [];
            }
        }
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $is_open_account = isset($input['open_account']) ? (int)$input['open_account'] : 0;
        $m_employee = new Employee();
        $rs = $m_employee->addOneEmployee($input, $is_open_account);
        if($rs === false) return $this->sendError(400, $m_employee->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = $request->put();
        $m_employee = new Employee();
        $eid = $request->put('eid/d');
        $employee = $m_employee::get(['eid' => $eid]);
        if(empty($employee)) return $this->sendError(400, "员工不存在");
        unset($input['create_time']);
        $rs = $m_employee->updateEmployee($eid,$input);
        if($rs === false) return $this->sendError(400, $m_employee->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $eid = input('id/d');
        $employee = Employee::get(['eid' => $eid]);
        if(empty($employee)) return $this->sendError(400, "员工不存在");

        $rs = $employee->delOneEmployee($eid, $employee);
        if($rs === false) return $this->sendError(400, $employee->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  员工的正式客户
     * @author luo
     */
    public function get_list_clients(Request $request)
    {
        $eid = input('id/d');
        $input = $request->param();

        $m_c = new Client();
        $ret = $m_c->where('eid', $eid)->getSearchResult($input);
       
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  员工的意向客户
     * @author luo
     * @method GET
     */
    public function get_list_customers(Request $request)
    {
        $eid = input('id/d');
        $input = $request->param();

        $m_customer = new Customer();
        $ret = $m_customer->where('eid', $eid)->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    //员工增加客户
    public function post_clients(Request $request)
    {
        $eid = input('id/d');
        $input = $request->post();
        if(!isset($input['cids']) || !is_array($input['cids'])) return $this->sendError(400, '参数错误');

        $employee = Employee::get(['eid' => $eid]);
        $rs = $employee->addBatchClient($input['cids'],$eid, $employee);
        if($rs === false) return $this->sendError(400,$employee->getErrorMsg());

        return $this->sendSuccess();
    }

    //移除员工客户
    public function delete_client(Request $request)
    {
        $eid = input('id/d');
        $cid = input('subid/d');
        $m_ec = new EmployeeClient();
        $rs = $m_ec->delOneRecord($eid, $cid);
        if($rs === false) return $this->sendError(400, $m_ec->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  员工批量分配意向客户
     * @author luo
     * @method POST
     */
    public function post_customers(Request $request)
    {
        $eid = input('id/d');
        $input = $request->post();
        if(!isset($input['cu_ids']) || !is_array($input['cu_ids'])) return $this->sendError(400, '参数错误');

        $m_customer = new Customer();
        $rs = $m_customer->updateEidOfManyCustomer($input['cu_ids'], $eid);
        if($rs === false) return $this->sendError(400,$m_customer->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  员工移除一个意向客户
     * @author luo
     * @method POST
     */
    public function delete_customer(Request $request)
    {
        $eid = input('id/d');
        $cu_id = input('subid/d');
        $cu_ids = [$cu_id];

        $m_customer = new Customer();
        $rs = $m_customer->updateEidOfManyCustomer($cu_ids, 0);
        if($rs === false) return $this->sendError(400,$m_customer->getErrorMsg());

        return $this->sendSuccess();
    }

}