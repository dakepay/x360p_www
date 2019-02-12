<?php

namespace app\admapi\controller;

use think\Request;
use app\admapi\model\VipClientConsume;


class VipClientConsumes extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        $mVcc = new VipClientConsume();
        $rs = $mVcc->getSearchResult($input);

        return $this->sendSuccess($rs);
    }

    public function post(Request $request)
    {

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {

        return $this->sendSuccess();
    }

    public function delete(Request $request){

        return $this->sendSuccess();
    }

}