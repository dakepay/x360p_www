<?php

namespace app\admapi\controller;

use think\Request;
use app\admapi\model\VipClientApp;


class VipClientApps extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        $mVca = new VipClientApp();
        $rs = $mVca->getSearchResult($input);
        foreach ($rs['list'] as $k => $v){
            $rs['list'][$k]['client_name'] = get_client_name($v['cid']);
            $app_info = get_app_info($v['app_id']);
            $rs['list'][$k]['app_name'] = $app_info['app_name'];
            $rs['list'][$k]['price_type'] = $app_info['price_type'];
        }
        return $this->sendSuccess($rs);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $mVca = new VipClientApp();
        $rs = $mVca->addOneClientApp($input);
        if(!$rs) return $this->sendError(400, $mVca->getErrorMsg());
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = $request->put();
        $mVca = new VipClientApp();
        $vca_id = $input['vca_id'];
        $clientApp = $mVca->get($vca_id);
        if(empty($clientApp)) {
            return $this->sendError(400, "App购买记录不存在");
        }
        unset($input['vca_id']);
        $rs = $mVca->updateClientApp($vca_id,$input);
        if(!$rs) return $this->sendError(400, $mVca->getErrorMsg());
        return $this->sendSuccess();
    }

    public function delete(Request $request){
        $vca_id = input('id/d');
        $mVca = new VipClientApp();
        $rs = $mVca->delClientApp($vca_id);
        if(!$rs) return $this->sendError(400, $mVca->getErrorMsg());

        return $this->sendSuccess();
    }

}