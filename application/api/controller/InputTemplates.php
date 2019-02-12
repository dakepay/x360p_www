<?php
/**
 * Author: luo
 * Time: 2017-10-31 15:16
**/

namespace app\api\controller;

use think\Request;
use app\api\model\InputTemplate;

class InputTemplates extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();

        $m_tpl = new InputTemplate();
        $where = [];
        if(gvar('uid')) $where['create_uid'] = gvar('uid');
        $ret = $m_tpl->where($where)->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();

        $m_tpl  = new InputTemplate();
        $rs = $m_tpl->createOneTemplate($input);
        if(!$rs) return $this->sendError(400, $m_tpl->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        return parent::delete($request);
    }

    public function put(Request $request)
    {
        return $this->sendError(400, '不能编辑');
    }

}