<?php
/**
 * Author: luo
 * Time: 2018/5/31 9:48
 */

namespace app\api\controller;


use app\api\model\ServicePushTask;
use think\Request;

class ServicePushTasks extends Base
{
    public function get_list(Request $request)
    {
        return parent::get_list($request);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_spt = new ServicePushTask();
        $rs = $m_spt->addTask($post, !empty($post['is_push']) ? true : false);
        if($rs === false) return $this->sendError(400, $m_spt->getErrorMsg());
        
        return $this->sendSuccess();
    }
    
    public function put(Request $request)
    {
        return $this->sendSuccess('not support');
    }

    public function delete(Request $request)
    {
        $spt_id = input('id');
        $service_push_task = ServicePushTask::get($spt_id);
        $rs = $service_push_task->delTask();
        if($rs === false) return $this->sendError(400, $service_push_task->getErrorMsg());
        
        return $this->sendSuccess();
    }

}