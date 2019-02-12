<?php
/**
 * Author: luo
 * Time: 2017-11-23 16:42
**/

namespace app\api\controller;


use app\api\model\MaterialHistory;
use think\Request;

class MaterialHistorys extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();
        $m_mh = new MaterialHistory();
        $ret = $m_mh->with(['material_store', 'to_material_store'])->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $m_mh = new MaterialHistory();
        $rs = $m_mh->addOneHis($input);
        if(!$rs) return $this->sendError(400, $m_mh->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $mh_id = input('id/d');
        $m_mh = new MaterialHistory();
        $rs = $m_mh->delOneHis($mh_id);
        if($rs === false) return $this->sendError(400, $m_mh->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        return $this->sendError('暂不能编辑');
    }

}