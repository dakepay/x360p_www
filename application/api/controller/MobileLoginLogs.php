<?php
/**
 * Author: luo
 * Time: 2018/1/8 18:27
 */

namespace app\api\controller;


use app\api\model\MobileLoginLog;
use think\Request;

class MobileLoginLogs extends Base
{

    /**
     * @desc  手机端登录日志
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $input = $request->get();
        $m_mll = new MobileLoginLog();
        $ret = $m_mll->with(['user','student'])->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

}