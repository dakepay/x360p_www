<?php
/**
 * Author: luo
 * Time: 2018/5/22 17:56
 */

namespace app\api\controller;


use app\api\model\ServiceRecord;
use think\Request;

class ServiceRecords extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_sr = new ServiceRecord();
        $ret = $m_sr->getSearchResult($get);
        
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_sr = new ServiceRecord();
        $rs = $m_sr->addServiceRecord($post);
        if($rs === false) return $this->sendError(400, $m_sr->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        return $this->sendSuccess('not support');
    }

    public function delete(Request $request)
    {
        $sr_id = input('id');
        $service_record = ServiceRecord::get($sr_id);
        if(empty($service_record)) return $this->sendSuccess();

        $rs = $service_record->delServiceRecord();
        if($rs === false) return $this->sendError(400, $service_record->getErrorMsg());
        
        return $this->sendSuccess();
    }

}