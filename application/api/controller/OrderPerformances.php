<?php
/**
 * Author: luo
 * Time: 2018/1/4 19:34
 */

namespace app\api\controller;

use app\api\model\OrderPerformance;
use think\Request;

class OrderPerformances extends Base
{

    /**
     * @desc  签单业绩表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $input = $request->get();
        $m_op = new OrderPerformance();
        $ret = $m_op->with(['oneOrder' => ['student', 'orderItems' => ['material']]])->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

}