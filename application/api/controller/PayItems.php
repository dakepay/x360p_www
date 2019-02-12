<?php


namespace app\api\controller;


use app\api\model\PayItem;
use app\api\model\User;
use think\Request;

class PayItems extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->param();
        $model = new PayItem();
        $ret = $model->getSearchResult($input);

        return $this->sendSuccess($ret);
    }
}