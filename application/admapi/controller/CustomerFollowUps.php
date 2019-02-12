<?php
/**
 * Author: luo
 * Time: 2017-12-09 15:43
**/

namespace app\admapi\controller;

use app\admapi\model\CustomerFollowUp;
use think\Request;

class CustomerFollowUps extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();
        $m_cuf = new CustomerFollowUp();
        $ret = $m_cuf->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        return parent::post($request);
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