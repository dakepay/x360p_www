<?php
/**
 * Author: luo
 * Time: 2017-11-21 18:33
**/

namespace app\api\controller;

use app\api\model\Tally;
use think\Request;
use app\api\model\TallyType as TallyTypeModel;

class TallyTypes extends Base
{


    public function get_list(Request $request)
    {
        $model = new TallyTypeModel();
        $input = $request->request();
        $input['pagesize'] = 1000;
        $result = $model->getSearchResult($input);
        return $this->sendSuccess($result);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_tt = new TallyTypeModel();
        unset($post['tt_id']);
        $rs = $m_tt->allowField(true)->save($post);
        if($rs === false) return $this->sendError(400, $m_tt->getErrorMsg());

        return $this->sendSuccess();
        //return parent::post($request);

    }

    public function put(Request $request)
    {
        $tt_id = input('id/d');
        $input = $request->only(['name', 'remark']);

        $type = TallyTypeModel::get(['tt_id' => $tt_id]);
        $rs = $type->save($input);

        if($rs === false) return $this->sendError(400, $type->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $tt_id = input('id/d');
        $is_exist = Tally::get(['tt_id' => $tt_id]);
        if($is_exist) return $this->sendError(400, '有相关记账，不能删除');

        $rs = TallyTypeModel::destroy($tt_id);
        if(!$rs) return $this->sendError(400, '删除失败');
        return $this->sendSuccess();
    }

}
