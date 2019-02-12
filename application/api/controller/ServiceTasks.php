<?php
/**
 * Author: luo
 * Time: 2018/5/22 16:49
 */

namespace app\api\controller;


use app\api\model\ServiceTask;
use think\Request;

class ServiceTasks extends Base
{
    /**
     * @desc  服务任务列表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $m_st = new ServiceTask();
        $get = $request->get();
        $ret = $m_st->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_st = new ServiceTask();
        $rs = $m_st->addServiceTask($post);
        if($rs === false) return $this->sendError(400, $m_st->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $st_id = input('id');
        $service_task = ServiceTask::get($st_id);
        if(empty($service_task)) return $this->sendError(400, '服务不存在');

        $put = $request->put();
        $rs = $service_task->updateServiceTask($put);
        if($rs === false) return $this->sendError(400, $service_task->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        return parent::delete($request);
    }

}