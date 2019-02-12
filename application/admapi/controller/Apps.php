<?php

namespace app\admapi\controller;

use think\Request;
use app\admapi\model\App;


class Apps extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        $m_app = new App();
        $rs = $m_app->getSearchResult($input);

        return $this->sendSuccess($rs);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $m_app = new App();
        $rs = $m_app->addApp($input);
        if(!$rs) return $this->sendError(400, $m_app->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = $request->put();
        $m_app = new App();
        $rs = $m_app->updateApp($input);
        if(!$rs) return $this->sendError(400, $m_app->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request){
        $app_id = input('id/d');
        $m_app = new App();
        $rs = $m_app->delApp($app_id);
        if(!$rs) return $this->sendError(400, $m_app->getErrorMsg());

        return $this->sendSuccess();
    }

}