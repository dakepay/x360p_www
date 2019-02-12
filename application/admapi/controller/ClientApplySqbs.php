<?php
namespace app\admapi\controller;

use think\Request;
use util\Sqb;
use app\admapi\model\ClientApplySqb;

class ClientApplySqbs extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        $mCas = new ClientApplySqb();
        $result = $mCas->getSearchResult($input);

        return $this->sendSuccess($result);
    }

    public function put(Request $request)
    {
        $input = $request->put();
        $mCas = new ClientApplySqb();
        $result = $mCas->updateInfo($input);
        if(false === $result) {
            return $this->sendError(400, $mCas->getError());
        }

        return $this->sendSuccess();
    }

    public function do_audit(Request $request){
        $cas_id = input('cas_id/d');
        $input = input();

        $is_audit = isset($input['is_audit']) ? intval($input['is_audit']) : 0;
        $is_check = isset($input['is_check']) ? intval($input['is_check']) : 0;
        $check_messages = isset($input['check_messages']) ? $input['check_messages'] : '';
        $mCas = new ClientApplySqb();
        $result = $mCas->do_audit($cas_id,$is_audit,$is_check,$check_messages);
        if(false === $result) {
            return $this->sendError(400, $mCas->getError());
        }
        return $this->sendSuccess();
    }


    /**
     * @desc  商户入网
     * @param Request $request
     */
    public function do_create(Request $request){
        $cas_id = input('cas_id/d');
        $mCas = new ClientApplySqb();
        $result = $mCas->do_create($cas_id);
        if(false === $result) {
            return $this->sendError(400, $mCas->getError());
        }
        return $this->sendSuccess();
    }


    public function get_area(Request $request){
        $url = 'http://sales-web.shouqianba.com/api/v1/allprovinces';
        $area = http_request($url);
        $area = json_decode($area,true);
        return $this->sendSuccess($area['data']);
    }


}