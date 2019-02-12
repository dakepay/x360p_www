<?php
/**
 * Author: luo
 * Time: 2018/6/22 10:25
 */

namespace app\api\controller;


use app\api\model\WechatTplDefine;
use think\Request;

class WechatTplDefines extends Base
{

    public function get_list(Request $request)
    {
        $m_wtd = new WechatTplDefine();
        $get = $request->get();
        $ret = $m_wtd->getSearchResult($get);
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