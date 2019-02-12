<?php
/**
 * Author: luo
 * Time: 2017-12-13 16:31
**/

namespace app\admapi\controller;


use app\admapi\model\ClientService;
use think\Request;

class ClientServices extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->param();
        $m_cs = new ClientService();
        $ret = $m_cs->with(['client', 'employee'])->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $data = $request->post();
        $m_cs = new ClientService();
        $rs = $m_cs->addClientService($data);
        if(!$rs) return $this->sendError(400, $m_cs->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        return parent::put($request);
    }

    public function delete(Request $request)
    {
        return parent::delete($request);
    }

}