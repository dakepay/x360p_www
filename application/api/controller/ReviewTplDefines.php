<?php
/**
 * Author: luo
 * Time: 2017-12-28 20:34
**/

namespace app\api\controller;

use app\api\model\ReviewTplDefine;
use think\Request;

class ReviewTplDefines extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();

        $where = [];
        if(isset($input['type']) && $input['type'] == 'lesson') {
            $where['lid'] = $input['lid'];
            $where['cid'] = 0;
            $where['sj_id'] = 0;
        }

        if(isset($input['type']) && $input['type'] == 'subject') {
            $where['lid'] = 0;
            $where['cid'] = 0;
            $where['sj_id'] = $input['sj_id'];
        }

        if(isset($input['type']) && $input['type'] == 'class') {
            $where['lid'] = 0;
            $where['cid'] = $input['cid'];
            $where['sj_id'] = 0;
        }

        $m_rtd = new ReviewTplDefine();
        $ret = $m_rtd->where($where)->with('reviewTplSetting')->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        if(!isset($input['rts_id'])) return $this->sendError(400, 'invalid param');
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