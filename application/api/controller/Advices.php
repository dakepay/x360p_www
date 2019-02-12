<?php
/**
 * Author: luo
 * Time: 2018/1/15 12:29
 */

namespace app\api\controller;

use app\api\model\Advice;
use think\Request;

class Advices extends Base
{

    /**
     * @desc  投诉建议列表
     * @author luo
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method GET
     */
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_advice = new Advice();
        $list = $m_advice->with('student')->getSearchResult($get);
        return $this->sendSuccess($list);
    }

    public function put(Request $request)
    {
        return $this->sendError(400, 'Cannot put');
    }

}